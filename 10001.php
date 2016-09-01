<?php
/**
 * Date: 16/8/11
 * Time: pm 5:16
 */
use Workerman\Worker;

require_once './Workerman/Autoloader.php';
require_once './common/common.php';

//用户登录

/*
 * 运行流程
 * 参数1 email (必须)
 * 参数2 password (必须 6<=长度<=20)
 */

$tcp_worker = new Worker("tcp://0.0.0.0:10001");
// 启动4个进程对外提供服务
$tcp_worker->count = 4;

// 当客户端发来数据时
$tcp_worker->onMessage = function ($connection, $data) {
	error_reporting(E_ALL & ~E_NOTICE);
	global $_OBJ, $_CONF;
	// 参数列表
	$parameter = array('email', 'password');
	// 参数检查
	$data = json_decode($data, true);
	if ($data) {
		if (common_check_parameter($data, $parameter)) {
			common_db_mysqli_connect();
			$sql = "select * from users where email = '" . $data['email'] . "' limit 1";
			$row = $_OBJ['db']->get_row($sql);
			if (count($row) <= 0) {
				$msg = common_response(10001.403, '该邮箱未注册', null);
			} else {
				// $password = password_hash($data['password'], PASSWORD_DEFAULT);
				$password = md5($data['password']);
				// print_r($password);
				if ($password !== $row['password']) {
					$msg = common_response(10001.404, '密码不正确', null);
				} else {
					$msg = common_response(10001.201, '成功', array('api_token' => $row['the_id']));
				}
			}
		} else {
			$msg = common_response(10001.402, '请求失败，参数不符', null);
		}
	} else {
		$msg = common_response(10001.401, '请求失败，没有参数', null);
	}
	$connection->send(json_encode($msg, JSON_UNESCAPED_UNICODE));
	$connection->close();
};
Worker::$stdoutFile = 'log/10001-' . date('Ym') . '.log';
// 运行worker
Worker::runAll();

//登录后 查看用户身份证信息是否在reids中
function check_identity($identity_code, $name, $uuid, $table_name) {
	global $_SC, $_SG;
	$redis = new Redis();
	// 阿里云配置
	if ($redis->connect($_SC['redisHost'], $_SC['redisPort']) == false) {
		return false;
	}
	if ($redis->auth($_SC['redisUser'] . ":" . $_SC['redisPw']) == false) {
		return false;
	}

	// 本地配置
	//    if($redis->connect('127.0.0.1', 6379) == false){
	//        return false;
	//    }
	$redis->select(6);
	$key = (string) $identity_code;
	if ($redis->exists($key)) {
		$arrRedis = json_decode($redis->zRange($key, 0, -1)[0], true);
		if ($arrRedis['name'] != $name) {
			$sql = "update `$table_name` set  name='" . $arrRedis['name'] . "',identity_state=2  where uuid='" . $uuid . "'";
			$_SG['db']->query($sql);
		} else {
			$sql = "update `$table_name` set  identity_state=2  where uuid='" . $uuid . "'";
			$_SG['db']->query($sql);
		}
	} else {
		$result = json_decode(identity_curl_post(trim($name), trim($identity_code)), true);
		$recode = $result['response']['status']['code'];
		if ($recode == 0 || substr($recode, 0, 1) == 2) {
			if ($recode == 0) {
				set_user_check_sources(trim($identity_code), trim($name), $uuid, $result);
				$sql = "update `$table_name` set  identity_state=2  where uuid='" . $uuid . "'";
				$_SG['db']->query($sql);
			} else {
				set_user_check_fail(trim($identity_code), trim($name));
				$sql = "update `$table_name` set identity_code='',name='',identity_state=0  where uuid='" . $uuid . "'";
				$_SG['db']->query($sql);
			}
		}

	}
	return true;
}

function identity_curl_post($name, $id) {
	global $_SC;
	$url = 'https://45.120.243.194/id-verification/service/check'; //测试接口地址
	$app_key = $_SC['identity_key'];
	$app_secret = $_SC['identity_secret'];
	$time = date('Y-m-d H:i:s', time());
	$data = array(
		'name' => $name,
		'id' => $id,
		'app_key' => $app_key,
		'time' => $time,
		'sign' => md5($app_key . $id . $time . $app_secret),
		'image' => '',
	);
	$ch = curl_init();
	$headers = null;
	$headers[] = 'Content-Type: application/x-www-form-urlencoded;';
	$fields_string = http_build_query($data, '&');
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //timeout on connect
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout on response
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
	$return = curl_exec($ch);
	curl_close($ch);
	return $return;
}

//用户身份证成功写入
function set_user_check_sources($identity_code, $name, $uuid, $result) {
	global $_SC;
	$redis = new Redis();
	// 阿里云配置

	if ($redis->connect($_SC['redisHost'], $_SC['redisPort']) == false) {
		return false;
	}
	if ($redis->auth($_SC['redisUser'] . ":" . $_SC['redisPw']) == false) {
		return false;
	}
	// 本地配置
	//    if($redis->connect('127.0.0.1', 6379) == false){
	//        return false;
	//    }
	$redis->select(6);
	$key = (string) $identity_code;
	$data = array('name' => $name, 'uuid' => $uuid, 'info' => $result);
	if ($redis->zAdd($key, time(), json_encode($data)) == false) {
		return false;
	} else {
		$redis->select(7);
		$redis->del($key);
		return true;
	}
}

//用户身份证认证失败写入
function set_user_check_fail($identity_code, $name) {
	global $_SC;
	$redis = new Redis();
	// 阿里云配置

	if ($redis->connect($_SC['redisHost'], $_SC['redisPort']) == false) {
		return false;
	}
	if ($redis->auth($_SC['redisUser'] . ":" . $_SC['redisPw']) == false) {
		return false;
	}

	// 本地配置
	//    if($redis->connect('127.0.0.1', 6379) == false){
	//        return false;
	//    }
	$redis->select(7);
	$key = (string) $identity_code;
	$value = (string) $name;
	$redis->zAdd($key, time(), md5($value));
	return true;
}

//判断短信验证码是否存在,有效 by lichao
function user_issms_exist($code, $phone, $action) {
	global $_SG;
	$time = date('Ym', time());
	$time2 = date('Ym', time() - 7200);
	if ($time == $time2) {
		//当前时间年月和2小时之前的时间年月一样
		$table_name = 'sms_code_' . $time;
		if (table_exist($table_name)) {
			$sql = "select * from `$table_name` where code = '" . $code . "' and phone='" . $phone . "' and action='" . $action . "' and status=1 and send_status=1 and overdue_time>now() limit 1";
			$query = $_SG['db']->query($sql);
			$row = $_SG['db']->fetch_array($query);
			if (count($row) > 0 && is_array($row)) {
				$row['table_name'] = $table_name;
				return $row;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else {
		//当前时间年月和2小时之前的时间年月不一样
		$table_name = 'sms_code_' . $time;
		if (table_exist($table_name)) {
			$sql = "select * from `$table_name` where code = '" . $code . "' and phone='" . $phone . "' and action='" . $action . "' and status=1 and send_status=1  and overdue_time>now() limit 1";
			$query = $_SG['db']->query($sql);
			$row = $_SG['db']->fetch_array($query);
			if (count($row) > 0 && is_array($row)) {
				$row['table_name'] = $table_name;
				return $row;
			} else {
				$table_name2 = 'sms_code_' . $time2;
				$sql2 = "select * from `$table_name2` where code = '" . $code . "' and phone='" . $phone . "' and action='" . $action . "' and status=1 and send_status=1 and overdue_time>now() limit 1";
				$query2 = $_SG['db']->query($sql2);
				$row2 = $_SG['db']->fetch_array($query2);
				if (count($row2) > 0 && is_array($row2)) {
					$row2['table_name'] = $table_name2;
					return $row2;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}

	}
}
//增加uuid
function add_user_uuid($uuid, $phone) {
	global $_SC;
	/**
	 * 在此验证数据合法性
	 */
	$redis = new Redis();
	if ($redis->connect($_SC['redisHost'], $_SC['redisPort']) == false) {
		return false;
	}
	if ($redis->auth($_SC['redisUser'] . ":" . $_SC['redisPw']) == false) {
		return false;
	}
	$redis->select(9);
	$key = (string) $uuid;
	$value = (string) $phone;
	$add = $redis->set($key, $value);
	if ($add) {
		return true;
	} else {
		return false;
	}
}
//判断uuid对应的手机号是否存在
function get_phone($uuid) {
	global $_SC;
	$redis = new Redis();
	if ($redis->connect($_SC['redisHost'], $_SC['redisPort']) == false) {
		return false;
	}
	if ($redis->auth($_SC['redisUser'] . ":" . $_SC['redisPw']) == false) {
		return false;
	}
	$redis->select(9);
	if ($phone = $redis->get($uuid)) {
		return $phone;
	} else {
		return false;
	}
}
//判断活动状态
function get_activity_state($uuid, $table, $id = 'id', $where = 'and is_delete = 0') {
	global $_SG;
	$sql = "select " . $id . " from " . $table . " where uuid = '" . $uuid . "' $where limit 1 ";
	$query = $_SG['db']->query($sql);
	$row = $_SG['db']->fetch_array($query);
	if (count($row) > 0 && is_array($row)) {
		return '1';
	} else {
		return '0';
	}
}
