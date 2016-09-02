<?php
/**
 * Date: 16/8/11
 * Time: pm 5:16
 */
use Workerman\Worker;

require_once './Workerman/Autoloader.php';
require_once './common/common.php';

//忘记密码（发送邮件）

/*
 * 运行流程
 * 参数1 email (必须)
 */

$tcp_worker = new Worker("tcp://0.0.0.0:10004");
// 启动4个进程对外提供服务
$tcp_worker->count = 4;

// 当客户端发来数据时
$tcp_worker->onMessage = function ($connection, $data) {
	error_reporting(E_ALL & ~E_NOTICE);
	global $_OBJ, $_CONF;
	// 参数列表
	$parameter = array('email');
	// 参数检查
	$data = json_decode($data, true);
	if ($data) {
		// echo 'hi';
		if (common_check_parameter($data, $parameter)) {
			common_db_mysqli_connect();
			$sql = "select * from users where email = '" . $data['email'] . "' limit 1";
			$row = $_OBJ['db']->get_row($sql);
			if (count($row) > 0) {
				//1.生成一个修改密码的参数(用‘邮箱’+‘时间戳’去sha1)，三天过期(+259200)
				$str = sha1($data['email'] . (string) time());
				$make_time = (string) time();
				$expire_time = (string) (time() + 259200);
				//2.存数据库并且存redis生成参数的时间戳，过期时间戳
				$check_sql = "select * from forget_password where email = '" . $data['email'] . "' limit 1";
				$check_row = $_OBJ['db']->get_row($check_sql);
				if ($check_row > 0) {
					$update_sql = "update forget_password set make_time = '" . $make_time . "' , expire_time = '" . $expire_time . "' where email = '" . $data["email"] . "'";
					$query = $_OBJ['db']->query($update_sql);
				} else {
					$insert_sql = "insert into forget_password (`email`,`make_time`,`expire_time`)  value ('" . $data['email'] . "','" . $make_time . "','" . $expire_time . "')";
					$query = $_OBJ['db']->query($insert_sql);
				}
				if ($query) {
					//插入数据库成功，去插入redis
					$redis_check = setRedis_forget_password($data['email'], $make_time, $expire_time);
					// var_dump($redis_check);
					if ($redis_check) {
						//3.发送邮件，当用户点击链接之后（两个页面），取到参数，然后去数据库或者redis比对，如果参数过期，出现已过期的(第一个)页面提示，如果参数没有过期出现(第二个)修改密码的页面
						$mail_check = common_sendmail($data['email'], $str);
						if ($mail_check) {
							$msg = common_response(10004.201, '发送成功', null);
						} else {
							$msg = common_response(10004.503, '发送失败', null);
						}
					} else {
						$msg = common_response(10004.502, 'redis中存在该值', null);
					}
				} else {
					$msg = common_response(10004.501, '插入数据库出错', null);
				}
			} else {
				$msg = common_response(10004.403, '该邮箱未注册', null);
			}
		} else {
			$msg = common_response(10004.402, '请求失败，参数不符', null);
		}
	} else {
		$msg = common_response(10004.401, '请求失败，没有参数', null);
	}
	$connection->send(json_encode($msg, JSON_UNESCAPED_UNICODE));
	$connection->close();
};
Worker::$stdoutFile = 'log/10004-' . date('Ym') . '.log';
// 运行worker
Worker::runAll();

function setRedis_forget_password($email, $make_time, $expire_time) {
	global $_CONF;
	$redis = new Redis();
	// redis 连接
	if ($redis->connect($_CONF['redisHost'], $_CONF['redisPort']) == false) {
		return false;
	}
	// redis 设置密码
	// if ($redis->auth($_CONF['redisUser'] . ":" . $_CONF['redisPw']) == false) {
	// 	return false;
	// }
	$redis->select(1);
	$key = $email . ':forget_password';
	// $data = json_encode(array('make_time' => $make_time, 'expire_time' => $expire_time));
	$add = $redis->hMset($key, array('make_time' => $make_time, 'expire_time' => $expire_time));
	return $add;
}
