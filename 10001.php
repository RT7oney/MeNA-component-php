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
 * 参数1 phone (必须)
 * 参数2 login_type (必须)
 * 参数3 pwd (必须 6<=长度<=20) authentication_token
 * 参数4 login_ip (必须)
 */

$tcp_worker = new Worker("tcp://0.0.0.0:10001");
// 启动4个进程对外提供服务
$tcp_worker->count = 4;

// 当客户端发来数据时
$tcp_worker->onMessage = function ($connection, $data) {
	global $_SG, $_SC;
	$data = json_decode($data, true);
	if ($data) {
		if (isset($data['phone']) && is_mobile($data['phone'])) {
			if (isset($data['login_ip'])) {
				if (isset($data['login_type']) && in_array($data['login_type'], $login_types)) {
					common_db_mysql_connect();
					switch ($data['login_type']) {
					case 'standard': //使用手机号密码登录
						if (isset($data['pwd']) && (strlen($data['pwd']) >= 4) && (strlen($data['pwd']) <= 20)) {
							$suffix = substr($data['phone'], 0, 4);
							$users_table_name = 'users_' . $suffix;
							$users_profiles_table_name = 'user_profiles_' . $suffix;

							$sql = "select * from `$users_table_name` where phone = '" . $data['phone'] . "' limit 1";
							$query = $_SG['db']->query($sql);
							$row = $_SG['db']->fetch_array($query);

							if (!isset($row['encrypted_password']) || empty($row['encrypted_password'])) {
								$row['is_pwd_set'] = '0';
							} else {
								$row['is_pwd_set'] = '1';
							}
							if (count($row) >= 1 && is_array($row)) {
								//判断是否存在uuid
								if (empty($row['uuid'])) {
									//没有uuid
									//生成uuid
									$uuid = md5(strtotime($row['created_at']) . $row['phone'] . rand(1000, 9999));
									$row['uuid'] = $uuid;
									//写入mysql
									$sql = "update " . $users_table_name . " set uuid = '" . $row['uuid'] . "' where phone='" . $row['phone'] . "'";
									$update_user_uuid_query = $_SG['db']->query($sql);
									$sql1 = "update " . $users_profiles_table_name . " set uuid = '" . $row['uuid'] . "' where  phone='" . $row['phone'] . "'";
									$update_user_profiles_uuid_query = $_SG['db']->query($sql1);
									if ($update_user_uuid_query && $update_user_profiles_uuid_query) {
										add_user_uuid($row['uuid'], $row['phone']);
									}
								} else {
									//查找redis里有没有
									if (get_phone($row['uuid']) == false) {
										add_user_uuid($row['uuid'], $row['phone']);
									}
								}
								if (password_verify($data['pwd'], $row['encrypted_password'])) {
									$sql = "select `user_brithday`,IFNULL(`sex`,0) as sex,`identity_code`,`name`,IFNULL(`identity_state`,0) as identity_state,`id`,`nick_name`,`area`,IFNULL(`coin_total`,0) as coin_total,IFNULL(`is_auto_pensions`,0) as is_auto_pensions,image_file_name,identity_code,total_income,frozen_coin,total_conversions,account_state,registration_id,is_system_notice,is_coin_notice,is_pension_notice  from `$users_profiles_table_name` where uuid = '" . $row['uuid'] . "'";
									$query = $_SG['db']->query($sql);
									$user_info = $_SG['db']->fetch_array($query);

									$check = true;
									if (!empty($user_info['identity_code']) && !empty($user_info['name'])) {
										$check = check_identity($user_info['identity_code'], $user_info['name'], $row['uuid'], $users_profiles_table_name);
									}
									if (!isset($user_info['image_file_name']) || empty($user_info['image_file_name'])) { //是否设置过头像
										$row['is_head_portrait'] = '0'; //没有设置头像
									} else {
										$row['is_head_portrait'] = '1'; //有设置头像
									}
									if (isset($user_info['identity_code']) && !empty($user_info['identity_code'])) {
//身份证号
										$mosaic = (isset($stars[(strlen($user_info['identity_code']) - 2)])) ? $stars[(strlen($user_info['identity_code']) - 2)] : '****************';
										$user_info['identity_code'] = substr($user_info['identity_code'], 0, 1) . $mosaic . substr($user_info['identity_code'], -1, 1); //截取身份证号
									}
									$houcui = (isset($stars[(mb_strlen($user_info['name'], 'utf-8') - 1)])) ? $stars[(mb_strlen($user_info['name'], 'utf-8') - 1)] : '';
									$user_info['name'] = mb_substr($user_info['name'], 0, 1, 'utf-8') . $houcui;
									//判断有没有关联过会员卡 有没有参与过全民行动 有没有添加过心愿单
									$user_info['is_associated_members_card'] = get_activity_state($row['uuid'], 'associated_members_card'); //是否关联过会员卡.0未关联,1关联
									$user_info['is_wish'] = get_activity_state($row['uuid'], 'wish_list'); //是否添加过心愿单.0未添加,1有添加
									$user_info['is_merchant_info_cursor'] = get_activity_state($row['uuid'], 'merchant_info_cursor', 'id', ' '); //是否参与过全民行动录入商户.0未参与,1有参与
									//判断是否绑定过银行卡
									$user_info['is_bind_bank_card'] = get_activity_state($row['uuid'], 'bank_card_verify_infos'); //是否绑定过银行卡.0=未绑定,1有绑定

									$result = array_merge($row, $user_info);

									if ($check) {
										$msg = array('code' => 1007201, 'msg' => '登陆成功', 'data' => $result);
									} else {
										$msg = array('code' => 1007202, 'msg' => '系统错误');
									}
									if (empty($row['first_sign_in_at'])) {
										$sql = "update `$users_table_name` set first_sign_in_at = now() where  id = '" . $row['id'] . "'";
										$_SG['db']->query($sql);
									}
									$sql = "update `$users_table_name` set last_sign_in_at = current_sign_in_at ,last_sign_in_ip = current_sign_in_ip where id = '" . $row['id'] . "'";
									$_SG['db']->query($sql);
									$sql = "update `$users_table_name` set sign_in_count = (sign_in_count + 1),
                                          current_sign_in_ip = '" . $data['login_ip'] . "',current_sign_in_at = now() where id = '" . $row['id'] . "'";
									$_SG['db']->query($sql);

								} else {
									//密码错误
									$msg = array('code' => 1007203, 'msg' => '密码错误');
								}
							} else {
								//用户不存在
								$msg = array('code' => 1007204, 'msg' => '用户不存在');
							}
						} else {
							//用户密码非法
							$msg = array('code' => 1007205, 'msg' => '用户密码格式错误');
						}
						break;
					}

					$_SG['db']->close();
				} else {
					//登录类型非法
					$msg = array('code' => 1007210, 'msg' => '登录类型错误');
				}
			} else {
				//登录id非法
				$msg = array('code' => 1007211, 'msg' => '登录IP错误');
			}
		} else {
			//用户手机号非法
			$msg = array('code' => 1007212, 'msg' => '用户手机错误');

		}

	} else {
		$msg = array('code' => 50072010, 'msg' => '参数不合法');
	}
	$connection->send(json_encode($msg));
	$connection->close();
};
Worker::$stdoutFile = '/var/log/workerman/10072-' . date('Ym') . '.log';
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
	$headers = array();
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
