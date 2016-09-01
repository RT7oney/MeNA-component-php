<?php
/**
 * Date: 16/8/11
 * Time: pm 5:16
 */
use Workerman\Worker;

require_once './Workerman/Autoloader.php';
require_once './common/common.php';

//开发者登录

/*
 * 运行流程
 * 参数1 the_id (必须)
 * 参数2 password (必须 6<=长度<=20)
 */

$tcp_worker = new Worker("tcp://0.0.0.0:10003");
// 启动4个进程对外提供服务
$tcp_worker->count = 4;

// 当客户端发来数据时
$tcp_worker->onMessage = function ($connection, $data) {
	error_reporting(E_ALL);
	global $_OBJ, $_CONF;
	// 参数列表
	$parameter = array('the_id', 'password');
	// 参数检查
	$data = json_decode($data, true);
	if ($data) {
		if (common_check_parameter($data, $parameter)) {
			common_db_mysqli_connect();
			$sql = "select * from users where the_id = '" . $data['the_id'] . "' limit 1";
			$row = $_OBJ['db']->get_row($sql);
			if (count($row) <= 0) {
				$msg = common_response(10003.403, '未查询到相关信息');
			} else {
				// $password = password_hash($data['password'], PASSWORD_DEFAULT);
				$password = md5($data['password']);
				// print_r($password);
				if ($password !== $row['password']) {
					$msg = common_response(10003.404, '密码不正确');
				} else {
					if ($row['isdev'] == 1) {
						$msg = common_response(10003.405, '已经是开发者了');
					} else {
						$update_sql = 'update users set isdev = 1 where the_id = ' . $data['the_id'];
						$query = $_OBJ['db']->query($update_sql);
						if ($query) {
							$name_sql = 'select * from user_profile where the_id = ' . $data['the_id'];
							$name_row = $_OBJ['db']->get_row($name_sql);
							$msg = common_response(10003.201, array('data' => array('api_token' => $row['the_id'], 'dev_name' => $name_row['name'])));
						} else {
							$msg = common_response(10003.501, '服务器内部错误');
						}
					}
				}
			}
		} else {
			$msg = common_response(10003.402, '请求失败，参数不符');
		}
	} else {
		$msg = common_response(10003.401, '请求失败，没有参数');
	}
	$connection->send(json_encode($msg));
	$connection->close();
};
Worker::$stdoutFile = 'log/10003-' . date('Ym') . '.log';
// 运行worker
Worker::runAll();
