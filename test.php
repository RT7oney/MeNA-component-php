<?php
require_once './common/common.php';

print_r(common_decode("DJ/MmSOu4nTCDddQaJ+DhiSn0fkNG+kV"));
die;
//function microtime_float(){
//
//   list($usec, $sec) = explode(" ", microtime());
//
//   return ((float)$usec + (float)$sec);
//
//}
//echo strlen(substr(md5( microtime_float().md5(1)),0,24));
//echo                                 $daytime=date('YmdHis');
//echo md5('leeeee',true);
//echo strlen(md5(microtime_float()));
//echo date('Y-m-d H:i:s',time());
//echo date('Y-m-d H:i:s',microtime_float());

//function common_get_randChar($length){
//    $str = null;
//    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
//    $max = strlen($strPol)-1;
//
//    for($i=0;$i<$length;$i++){
//        $str.=$strPol[rand(0,$max)];
//    }
//    return $str;
//}
//
//
////php生成GUID
//function getGuid() {
//    $charid = strtoupper(md5(uniqid(mt_rand(), true)));
//
//    $hyphen ="";// "-"
//    $uuid = substr($charid, 0, 8).$hyphen
//        .substr($charid, 8, 4).$hyphen
//        .substr($charid,12, 4).$hyphen
//        .substr($charid,16, 4).$hyphen
//        .substr($charid,20,12);
//    return $uuid;
//}
//
//echo strlen(getGuid());
//$overdue_time="";
//if(isset($data['overdue_time'])){
//    $overdue_time=$data['overdue_time'];
//}else{
//    $overdue_time="";
//}
//if(floor($overdue_time) == $overdue_time && $overdue_time > 0){
//echo 123;
//}
//$overdue_time="'".date('Y-m-d H:i:s',time())."'";
//
////echo $overdue_time;
//$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//$conn = socket_connect($socket,'121.41.5.21',10081);
//$data = array('phone'=>'15527248393','user'=>'lichao222','password'=>'123456','agents_id'=>'2');
//$data = array('page'=>'123'); //10028
//$data = array('phone'=>'15527248393','user'=>'lichao123','password'=>'123456','code'=>'466084'); //10030
//$data = array('merchant_id'=>'277','num'=>'5','coin'=>'15','px'=>'50','overdue_time'=>''); //10031
//$data = array('name'=>'hetao','password'=>'123456','phone'=>'18817393014');  //10032
//$data = array('id'=>'2','name'=>'123','fax'=>'123444'); //10033
//$data = array('phone'=>'18817393014','password'=>'123456'); //10034
//$data = array('phone'=>'15527248393','code'=>'731948','password'=>'123456'); //10035

//$data = array('code'=>'yyENtGwl3TB5jqcTplTPi81KqeR5lDwzBIzmjOfyzUg=864382703ec21f7b13G2mkrI','phone'=>'15001888642'); //10040
//$data = array('phone'=>'15527248393','sms_type'=>'1','action'=>'user_update_password'); //10041
//$data = array('merchant_infos_id'=>'277'); //10042
//$data = array('id'=>'1'); //10043
//$data = array('invit'=>'abcd.12'); //10044
//$data = array('merchant_id'=>'277','start_date'=>'20160101','end_date'=>'20160501'); //10038
//$data = array('merchant_id'=>'277','page'=>'1','limit'=>'2'); //10051
//$data = array('merchant_id'=>'277','pay_type'=>'1','trade_no'=>'123213124124','payment_no'=>'5523423411','volume'=>'123'); //10052
//$data = array('merchant_info_id'=>'1','phone'=>'18629320138','password'=>'20202'); //10045
//$data = array('merchant_infos_id'=>'277','ids'=>'123fsaa123124','merchants_id'=>'277'); //10049
//$data = array('merchant_infos_id'=>'277'); //10050
//$data = array('merchant_id'=>'277','start_date'=>'1456824367','end_date'=>'1460457087');
//$data = array('merchant_id'=>'277','coin'=>'100','phone'=>'15527248393');
//$data = array('agents_id'=>'3'); //10056
//$data = array('agents_id'=>'3'); //10057
//$data = array('id'=>'277','numerator'=>'2','denominator'=>'1000'); //10063
//$data = array('id'=>'277'); //10064
//$data = array('id'=>array(1),'type'=>'0','merchant_id'=>'278'); //10061
//$data = array('user_id'=>'420106199401050036','name'=>'王武','uuid'=>'41852298f1fb101687ff7c808597e18e'); //10055
//////echo $data['user_id'].':'.md5($data['name']);
////$data = array('agents_id'=>'2'); //10073
////////
//$data = array('phone'=>'18521531024','pwd'=>'111111','login_type'=>'standard','login_ip'=>'1.1.1.1'); //10073
//$data = array('uuid'=>'498eae8d2aa4a5e752e3604629307713','nick_name'=>'sada'); //10077
//$data = array('search_time'=>'1461418129'); //10078
//$data = array('uuid'=>'41852298f1fb101687ff7c808597e18e','search_time'=>'1449732386'); //10077

//$in =  json_encode($data);
//socket_write($socket,$in,strlen($in));
//$out = socket_read($socket, 2041);
//socket_close($socket);
//echo $out;
//echo date('Y-m-d', strtotime('-16 month'));
//echo date('Ymd','-1704182400');

// $arra=array(
//     'bus'=>array('data'=>111,'name'=>'11.jpg'),
//     'org'=>array('data'=>111,'name'=>'11.jpg'),
//     'taz'=>array('data'=>111,'name'=>'11.jpg'),

// );
// echo "<pre>";
// echo json_encode($arra);
// print_r(json_decode(json_encode($arra),true));
// $aa='asdasdasd.jpg';
// echo explode('.',$aa)[1];
// //echo SK('121.41.5.21','10072',$data);
// //////socket请求
// function SK($host,$port,$data,$size=8192){
//     $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//     socket_connect($socket,$host,$port);
//     $in =  json_encode($data);
//     socket_write($socket,$in,strlen($in));
//     $out = '';
//     while ($tmp = socket_read($socket, $size)) {
//         if(strlen($tmp) == 0){
//             break;
//         }else{
//             $out .= $tmp;
//         }
//     }
//     socket_close($socket);
//     return $out;
// }
//$aa="923812123021312";
//substr($aa,0,-4);
//echo str_replace(substr($aa,0,-4),"**** **** **** ",$aa);
//isChineseName($name);
//判断用户名字合法性
//function isChineseName($name){
//  if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name)) {
//        return true;
//    } else {
//        return false;
//    }
//}
//$a=1;
//if($a){
//    echo "123";
//}else{
//    echo "12asd3";
//}
//echo date('Y-m-d H:i:s');
//$time=date('Ym',time());
//$time2=date('Ym',time()-7200);
//if($time==$time2){
//    echo 123;
//}
//echo $time;
//echo $time2;
//echo count(array('1','2','3'));
//$aaa=array('1','2');
//echo count($aaa);
//精确毫秒时间戳
//function microtime_float(){
//
//    list($usec, $sec) = explode(" ", microtime());
//
//    return ((float)$usec + (float)$sec);
//
//}
//echo microtime_float();

//$pwd = "123456";
//$data['user']="123456";
//echo count($data['user']);
//exit;
//if(strlen($data['user'])>=4){
//
//}
//$pwd=123456;
//$hash = password_hash($pwd);
//echo $hash;
//if (password_verify($pwd,'$2y$10$DVPXy66TwDtzWIKkodVlqO0gMFLCroVKONe1yuRC3rRvz3Sd2UOTa')) {
//    echo "密码正确";
//} else {
//    echo "密码错误";
//}
//echo $hash;
//$arrdata=array();
//$arrdata['asd']="name='asd'";
//$arrdata['ssdc']="sss='aawwww'";
//echo implode(",",$arrdata);
//print_r($arrdata);
//
//define('AES_KEY','fgewcbxc2lso9slc');
//define('AES_IV','ps0xmc92jssaqsxp');
//function my_encrypt($data){
//    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128,AES_KEY,addPkcs7Padding(json_encode($data)),MCRYPT_MODE_CBC,AES_IV));
//}
//
//function my_decrypt($data){
//    return stripPkcs7Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_128,AES_KEY,base64_decode($data),MCRYPT_MODE_CBC,AES_IV));
//}
//
//function addPkcs7Padding($string=null, $blocksize = 32) {
//    $len = strlen($string); //取得字符串长度
//    $pad = $blocksize - ($len % $blocksize); //取得补码的长度
//    $string .= str_repeat(chr($pad), $pad); //用ASCII码为补码长度的字符， 补足最后一段
//    return $string;
//}
//
//function stripPkcs7Padding($string){
//    $slast = ord(substr($string, -1));
//    $slastc = chr($slast);
//    $pcheck = substr($string, -$slast);
//    if(preg_match("/$slastc{".$slast."}/", $string)){
//        $string = substr($string, 0, strlen($string)-$slast);
//        return $string;
//    } else {
//        return false;
//    }
//}
//$w=201610;
//echo my_encrypt($w);
//$s=my_encrypt($w);
//$code="q0i1yPuTdKRwbPZugluHqeU71D0raaO3reNViyQAg/4=739930f97be9899e95SqKzMK";
//$s=substr($a,0,44);
//$s=my_decrypt($s);
//
//$b=str_replace('"', '', $s);;
//echo $b;
//echo "<br>";
//echo strlen(my_encrypt(201723));
//echo "<br>";
//echo strlen(my_encrypt(239211));
//echo "<br>";
//echo strlen(my_encrypt(231223));
//echo "<br>";
//echo strlen(my_encrypt(234983));
//echo "<br>";
//echo strlen(my_encrypt(210283));
//echo "<br>";
//echo strlen(my_encrypt(098321));

//精确毫秒时间戳
//$jmstring=substr($code,0,44);
//$ym=str_replace('"', '', my_decrypt($jmstring));
//$table_name = "coin_code_".$ym;
//echo $table_name;
//$jmstring="q0i1yPuTdKRwbPZugluHqeU71D0raaO3reNViyQAg/4=739930f97be9899e95SqKzMK";
//$jmstring=substr($jmstring,0,44);
//
//$ym=str_replace('"', '', my_decrypt($jmstring));
//if($ym){
//    echo 123;
//}else{
//    echo 222;
//}
//echo $ym;
//$sql_data=array('lsss','spspss','opwmskww');
//$sql = implode(' union ', $sql_data);

//echo $sql;
//var_dump(explode('_',__CLASS__)[1]);

//socket请求
//function SK($host,$port,$data,$size=8192){
//    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//    socket_connect($socket,$host,$port);
//    $in =  json_encode($data);
//    socket_write($socket,$in,strlen($in));
//    $out = '';
//    while ($tmp = socket_read($socket, $size)) {
//        if(strlen($tmp) == 0){
//            break;
//        }else{
//            $out .= $tmp;
//        }
//    }
//    socket_close($socket);
//    return $out;
//}
//
//cs10038();
//function cs10038(){
//    $data['merchant_id'] = '1';
//    $data['start_date'] = '20160201';
//    $data['end_date'] = '20160301';
//    $data['page'] = '3';
//    $data['limit'] = '30';
//    $arr = SK('121.41.5.21',10038,$data);
//    echo $arr;
//}
//$date1 ='2016-03-01';
//$date2 ='2017-02-01';
//echo  getMonthNum($date1,$date2);
//$start_year_mon='2016-03-01';
//$end_year_mon='2018-02-01';
//$mi = getMonthNum($start_year_mon,$end_year_mon);
//echo $mi;
//$k = 0;
//$start_year_mon=201603;
//for ($i = 0; $i <=$mi; $i++) {
//    $sql_data[$k]=$start_year_mon;
//    $n_table_name = "coin_code_" . $start_year_mon;
////    if (table_exist($n_table_name)) {
//        $sql_data[$k] = "select * from ".$n_table_name." where merchant_id='" . 1 . "' and create_time >= '" . 222 . "' and create_time <= '" . 2 . "'";
//        $k++;
////    }
//    if(substr($start_year_mon,4,2)==12){
//        $start_year_mon=substr($start_year_mon,0,4)+1;
//        $start_year_mon=$start_year_mon."01";
//    }else{
//        $start_year_mon= $start_year_mon+1;
//    }
//
//
//}
//echo "<pre>";
//print_r($sql_data);

//function getMonthNum($date1,$date2){
//    $date1_stamp=strtotime($date1);
//    $date2_stamp=strtotime($date2);
//    list($date_1['y'],$date_1['m'])=explode("-",date('Y-m',$date1_stamp));
//    list($date_2['y'],$date_2['m'])=explode("-",date('Y-m',$date2_stamp));
//    return abs(($date_2['y']-$date_1['y'])*12 +$date_2['m']-$date_1['m']);
//}

//$user_id="12345603920302021123";
//if(shenhe($user_id)){
//    echo "123";
//}else{
//    echo "234";
//}

//$str="阿萨德asdf想";
//
//
//
//
//function isChineseName($name){
//    if(!eregi("[^\x80-\xff]","$name")){
//        echo "全是中文";
//    }else{
//        echo "不是";
//    }
//}
//
//function shenhe($user_id){
//    if(strlen($user_id)==15){
//        if(preg_match("/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/", $user_id)){
//            return true;
//        }else{
//            return false;
//        }
//    }else if(strlen($user_id)==18){
//        if(preg_match("/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/", $user_id)){
//            return true;
//        }else{
//            return false;
//        }
//    }else{
//        return false;
//    }
//
//}
//API接口账号密码app&secret方法
//$str = '代理商后台';
//$app = getApiApp($str);
//$secret = getApiSecret($str);
//echo '用户名---'.$str.'<br>';
//echo '这是app---'.$app.'<br>';
//echo '这是secret---'.$secret.'<br>';
//function getApiApp($str){
//    $str = $str.'APIAPP'.common_get_randChar(16);
//    return md5(md5($str));
//}
//function getApiSecret($str){
//    $str = $str.'APISECRET'.common_get_randChar(16);
//    return md5(md5($str));
//}
////随即产生字符串
//function common_get_randChar($length){
//    $str = null;
//    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
//    $max = strlen($strPol)-1;
//
//    for($i=0;$i<$length;$i++){
//        $str.=$strPol[rand(0,$max)];
//    }
//    return $str;
//}

//$arr=array('id'=>1,'sd'=>'sdc','name'=>'jack');
//echo count($arr);
//echo implode(",",$arr);

//function addFileToZip($path,$zip){
//    $handler=opendir($path); //打开当前文件夹由$path指定。
//    while(($filename=readdir($handler))!==false){
//        if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
//            if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
//                addFileToZip($path."/".$filename, $zip);
//            }else{ //将文件加入zip对象
//                $zip->addFile($path."/".$filename);
//            }
//        }
//    }
//    @closedir($path);
//}
//
//
//$zip=new ZipArchive();
//if($zip->open('images.zip', ZipArchive::OVERWRITE)=== TRUE){
//    addFileToZip('images/', $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
//    $zip->close(); //关闭处理的zip文件
//}

//$arr=array(
//    'a'=>array('asd'),
//    'b'=>array('asd'),
//    'c'=>array('xxx')
//);
//foreach($arr as $k=>$v){
//    $arr1[]=$k;
//}
//print_r($arr1);

// echo strtotime("null");

// //用户身份证成功写入
// //echo "123";
// //select(421081198708210032);
// function select($identity_code) {
// 	$redis = new Redis();
// 	// 阿里云配置
// 	/*
// 	    if ($redis->connect($_SC['redisHost'], $_SC['redisPort']) == false) {
// 	        return false;
// 	    }
// 	    if ($redis->auth($_SC['redisUser'] . ":" . $_SC['redisPw']) == false) {
// 	        return false;
// */
// 	// 本地配置
// 	if ($redis->connect('127.0.0.1', 6379) == false) {
// 		return false;
// 	}
// 	$key = (string) $identity_code;
// 	$redis->select(6);
// 	$arr = $redis->zRange($key, 0, -1);
// 	var_dump($arr);
// }

#######################################################
#######################################################
######################比较吊############################
#######################################################
#######################################################
#######################################################
#######################################################

/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 16/3/27
 * Time: 下午2:18
 */

//socket请求

$host = '121.41.5.21';
$port = '10020';
$data = array(
	'uuid' => '8a40f07c81deac67498734931f48b847',
	'bank_card_info_id' => '5218995118881018',

//    'bank_card_info_id'=>'6212261001030537333',
);
echo SK($host, $port, $data);

$host = '121.41.5.21';
$port = '1002';
$data = array(
	'uuid' => 'ef4a586ad69d7bdda02fc3557fa4fb0b',
	'action_type' => 'present',
	'merchant_id' => '277',
	'coin' => '100',
);
echo SK($host, $port, $data);

$host = '121.41.5.21';
$port = '10072';
$data = array(
	'phone' => '18621541350',
	'login_type' => 'standard',
	'login_ip' => '1.1.1.1',
	'pwd' => '123456',
);
echo SK($host, $port, $data);

$host = '121.41.5.21';
$port = '10041';
$data = array(
	'phone' => '18672792565',
	'sms_type' => '1',
	'action' => 'user_registe',
);
echo SK($host, $port, $data);

$host = '121.41.5.21';
$port = '10071';
$data = array(
	'phone' => '18621541358',
	'pwd' => 'mimamimam',
	'code' => '1234',
	'channel' => 'Wechat',
);
echo SK($host, $port, $data);

function SK($host, $port, $data, $size = 8192) {
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	socket_connect($socket, $host, $port);
	$in = json_encode($data);
	socket_write($socket, $in, strlen($in));
	$out = '';
	while ($tmp = socket_read($socket, $size)) {
		if (strlen($tmp) == 0) {
			break;
		} else {
			$out .= $tmp;
		}
	}
	socket_close($socket);
	return $out;
}

exit;
use OSS\Core\OssException;

require_once './Workerman/Autoloader.php';
require_once './common/common.php';

use OSS\OssClient;
use Workerman\Worker;

require_once './common/aliyun-oss/autoload.php';

$tcp_worker = new Worker("tcp://0.0.0.0:10048");

// 启动4个进程对外提供服务
$tcp_worker->count = 4;

// 当客户端发来数据时
$tcp_worker->onMessage = function ($connection, $data) {
	global $_SG, $_SC;
	//配置文件 上传文件
	//    $file_path = './1.png';
	//    $file = file_get_contents($file_path);
	$object = 'testoss/1/1.png';
	$ossClient = new OssClient($_SC['accessKeyId'], $_SC['accessKeySecret'], $_SC['endpoint'], false);
//    oss_upload($ossClient,$_SC['bucket'],$object,$file);
	$url = getSignedUrlForGettingObject($ossClient, $_SC['bucket'], $object);
	print_r($url);

	$connection->close();
};
Worker::$stdoutFile = '/var/log/workerman/10048-' . date('Ym') . '.log';
// 运行worker
Worker::runAll();

//删除文件
function deleteObject($ossClient, $bucket, $object) {
	try {
		$ossClient->deleteObject($bucket, $object);
		return true;
	} catch (OssException $e) {
		return false;
		return;
	}

}
//获取访问资源 内网地址
function getSignedUrlForGettingObject($ossClient, $bucket, $object, $timeout = 3600) {
	try {
		$signedUrl = $ossClient->signUrl($bucket, $object, $timeout);
		return $signedUrl;
	} catch (OssException $e) {

		return false;
	}
}
//上传文件
function oss_upload($ossClient, $bucket, $object, $file) {
	global $_SC;
	$doesExist = $ossClient->doesBucketExist($bucket);
	if (!$doesExist) {
		$client = $ossClient->createBucket($_SC['bucket']);
	}
	try {
		$ossClient->putObject($_SC['bucket'], $object, $file);
		return true;
	} catch (OssException $e) {
		return false;
	}
}
