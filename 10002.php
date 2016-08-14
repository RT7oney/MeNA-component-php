<?php
/**
 * By:Ryan
 * Date: 16/8/11
 * Time: pm 5:16
 */
use Workerman\Worker;

require_once './Workerman/Autoloader.php';
require_once './common/common.php';

//用户注册

/*
 * 运行流程
 * 参数1 email (必须)
 * 参数2 password (必须)
 */

$tcp_worker = new Worker("tcp://0.0.0.0:10002");
// 启动4个进程对外提供服务
$tcp_worker->count = 4;

// 当客户端发来数据时
$tcp_worker->onMessage = function ($connection, $data) {
	error_reporting(E_ALL & ~E_NOTICE);
	global $_OBJ, $_CONF;
	// 参数列表
	$parameter = array('email', 'password', 'name');
	// 参数检查
	$data = json_decode($data, true);
	if ($data) {
		if (common_check_parameter($data, $parameter)) {
			common_db_mysqli_connect();
			$sql = "select * from users where email = '" . $data['email'] . "' limit 1";
			$row = $_OBJ['db']->get_row($sql);
			if (count($row) > 0) {
				$msg = common_response(10002.403, '该邮箱已被注册');
			} else {
				$password = password_hash($data['password'], PASSWORD_DEFAULT);
				$the_id = md5(uniqid($password . time()));
				//事务处理
				$_OBJ['db']->query('BEGIN');
				$_OBJ['db']->query('SET AUTOCOMMIT=0');
				try {
					$users_sql = "insert into users (`email`,`password`,`the_id`)  value ('" . $data['email'] . "','" . $password . "','" . $the_id . "')";
					$users_query = $_OBJ['db']->query($users_sql);
					$user_profile_sql = "insert into user_profile (`the_id`,`name`)  value ('" . $the_id . "','" . $data['name'] . "')";
					$user_profile_query = $_OBJ['db']->query($user_profile_sql);
					if ($users_query && $user_profile_query) {
						$_OBJ['db']->query('COMMIT');
						$msg = common_response(10002.201, array('token' => $the_id));
					} else {
						$msg = common_response(10002.502, '插入数据有误');
						$_OBJ['db']->query('ROLLBACK');
					}
				} catch (Exception $e) {
					$msg = common_response(10002.501, '系统错误');
					$_OBJ['db']->query('ROLLBACK');
				}
			}
		} else {
			$msg = common_response(10002.402, '请求失败，参数不符');
		}
	} else {
		$msg = common_response(10002.401, '请求失败，没有参数');
	}
	$connection->send(json_encode($msg, JSON_UNESCAPED_UNICODE));
	$connection->close();
};
Worker::$stdoutFile = 'log/10002-' . date('Ym') . '.log';
// 运行worker
Worker::runAll();
