<?php
/**
 * By:Ryan
 * Date: 16/8/11
 * Time: 下午5:05
 */
include_once './common/config.inc.php';

/**
 * des解密接口发送回来的消息
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
function common_decode($data) {
	global $_CONF;
	include_once './common/DES.php';
	$encrypter = new DesCrypter($_CONF['Des-Key'], MCRYPT_3DES);
	$result = $encrypter->decrypt(base64_decode($data));
	$encrypter->close();
	return json_decode($result, true);
}

// 是否是邮箱 by Ryan
function is_email($email) {
	if (preg_match('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $email)) {
		return true;
	} else {
		return false;
	}
}
//验证手机号合法
function is_mobile($value) {
	if (preg_match('/^1[34578][0-9]{9}$/', $value)) {
		return true;
	} else {
		return false;
	}
}
//mysql连接
function common_db_mysql_connect() {
	global $_OBJ, $_CONF;
	include_once './common/Mysql.php';
	$_OBJ['db'] = new MySQL_DB;
	$_OBJ['db']->connect($_CONF['dbhost'], $_CONF['dbuser'], $_CONF['dbpw'], $_CONF['dbname']);
}
//mysqli连接
function common_db_mysqli_connect() {
	global $_OBJ, $_CONF;
	include_once './common/Mysqli.php';
	$_OBJ['db'] = new MySQLi_DB($_CONF['dbhost'], $_CONF['dbuser'], $_CONF['dbpw'], $_CONF['dbname'], $_CONF['dbport']);
}

//创建分表
function create_table($table_name, $new_table_name) {
	global $_OBJ;
	$sql = "create table `$new_table_name` like `$table_name`";
	$query = $_OBJ['db']->query($sql);
	if ($query) {
		return true;
	} else {
		return false;
	}
}

//判断表是否存在
function table_exist($table_name) {
	global $_OBJ, $_CONF;
	$sql = "show tables like '" . $table_name . "' ";
	$query = $_OBJ['db']->query($sql);
	$row = $_OBJ['db']->fetch_array($query);
	if (count($row) > 0) {
		return true;
	} else {
		return false;
	}
}

function common_encrypt($data) {
	return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, AES_KEY, json_encode($data), MCRYPT_MODE_CBC, AES_IV));
}

function common_decrypt($data) {
	return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, AES_KEY, base64_decode($data), MCRYPT_MODE_CBC, AES_IV);
}

//精确毫秒时间戳
function common_microtime_float() {

	list($usec, $sec) = explode(" ", microtime());

	return ((float) $usec + (float) $sec);

}
//随即产生字符串
function common_get_randChar($length) {
	$str = null;
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
	$max = strlen($strPol) - 1;

	for ($i = 0; $i < $length; $i++) {
		$str .= $strPol[rand(0, $max)];
	}
	return $str;
}

/**
 * curl方法 by Ryan
 */
function common_curl_post($url, $data, $header_arr) {
	$header = array('Content-Type: application/json', 'Accept: application/json');
	if ($header_arr) {
		$header = array_merge($header, $header_arr);
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$info = curl_exec($ch);
	curl_close($ch);
	$info = json_decode($info, true);
	return $info;
}

function common_curl_get($url, $data, $header_arr) {
	$header = array('Content-Type: application/json', 'Accept: application/json');
	if ($header_arr) {
		$header = array_merge($header, $header_arr);
	}
	if ($data) {
		foreach ($data as $key => $value) {
			$url .= "&$key=" . rawurlencode($value);
		}
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$info = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error ' . curl_error($ch);
		//捕抓异常

	}
	curl_close($ch);
	$info = json_decode($info, true);
	return $info;
}

/**
 * 分离上传的参数的参数名称并且拼接为一个数组
 */
function common_check_parameter($data, $parameter) {
	$arr = array();
	foreach ($data as $key => $value) {
		array_push($arr, $key);
	}
	$ret = array_diff($arr, $parameter);
	if (count($ret) == 0) {
		return ture;
	}
	return false;
}

/**
 * 统一response方法
 */
function common_response($code, $msg, $data) {
	return array('code' => (string) $code, 'msg' => $msg, 'data' => $data);
}

/**
 * 发送邮件的方法
 */
function common_sendmail($to, $key) {
	include_once './common/Email.php';
	$mail = new MySendMail();
	$mail->setServer("smtp.163.com", "xiaoquexing163@163.com", "123qweasdzxc");
	$mail->setFrom("MeNA");
	$mail->setReceiver($to);
	//$mail->setReceiver("XXXXX@XXXXX");
	// $mail->setCc("XXXXX@XXXXX");
	// $mail->setCc("XXXXX@XXXXX");
	// $mail->setBcc("XXXXX@XXXXX");
	// $mail->setBcc("XXXXX@XXXXX");
	// $mail->setBcc("XXXXX@XXXXX");
	$mail->setMailInfo("MeNA忘记密码", email_tpl($to, $key));
	$check = $mail->sendMail();
	return $check;
}

/**
 * 记录日志
 */
function common_log($log_data) {
	$fd = fopen('../log/' . date('Ym') . '.log', "a");
	$str = "【" . date("Y/m/d h:i:s", time()) . "】" . $log_data;
	fwrite($fd, $str . "\n");
	fclose($fd);
}

/**
 * email 发送模板
 */
function email_tpl($email, $key) {
	return '<div style="margin:0 auto;width:80%;height:100%;"><div style="margin:3% auto;width:80%;height:80%;border:10px solid #00bcd4;border-style:outset;background-image:url(http://img1.3lian.com/2015/w2/34/d/64.jpg)"><div style="margin:10% auto;width:80%;height:80%;background-color:#fff9c4;filter:Alpha(Opacity=10);-moz-opacity:0.5;opacity:0.5;"><div style="padding: 10%;width:80%;height:80%;font:30px;font-weight:bold;line-height:1.5;">你好，您的MeNA账号' . $email . '正在修改密码，请点击以下链接修改密码（有效期3天）<p style="text-align:center"><a href="https://www.baidu.com/s?wd=' . $key . '">修改密码</a></p>如果不是您本人操作，请忽略此邮件</div></div></div></div>';
}
