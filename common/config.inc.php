<?php
$_SC['pconnect'] = 0;
$_SC['dbcharset'] = 'utf8';
//$_SC['api_host'] = 'http://121.40.62.252/api/v1';
$_SC['api_host'] = 'http://10.251.241.84/api/v1';
$_SC['max_avatar_size'] = 5242880;
/*
 *  测试服务器数据库配置
*/
$_SC['dbhost'] = 'ccpptestdb.mysql.rds.aliyuncs.com';
$_SC['dbuser'] = 'denong';
$_SC['dbpw'] = 'JsXMlEyPHmd6UqqQ';
$_SC['dbname'] = 'denong_api_production';



/*
 * MEMCACHED配置
 */
$_SC['ocsHost'] = '0f0243a476304067.m.cnhzaliqshpub001.ocs.aliyuncs.com';
$_SC['ocsUser'] = '0f0243a476304067';
$_SC['ocsPw'] = 'fFUEs85tYxqL34z2';
$_SC['ocsPort'] = 11211;


/*
 * Redis配置
 */
$_SC['redisHost'] = '341b3506a0e343f2.m.cnhza.kvstore.aliyuncs.com';
$_SC['redisUser'] = '341b3506a0e343f2';
$_SC['redisPw'] = 'aMdhTKEjycLa2DdB';
$_SC['redisPort'] = 6379;



define('AES_KEY','fgewcbxc2lso9slc');
define('AES_IV','ps0xmc92jssaqsxp');

$_SC['accessKeyId'] = "OefEPxsAIZzDhTfm";
$_SC['accessKeySecret'] = "uyvCjkC3yh210aJXLOzNZDM2o8BvZx";
$_SC['endpoint'] = "oss-cn-hangzhou-internal.aliyuncs.com";
/*
 * 二维码图片存放根目录
 */
define('QRCODE_ROOT','test/');
define('CODE_TO_COIN_URL','http://subs.wechat.test.denong.com/index.php');
/*
 * 测试存储空间名
 */
$_SC['bucket'] = "testdenong";
$_SC['user_bucket'] = "testdenonguser";
define('USER_FILE_ROOT','test/');//用户文件存放跟目录
$_SC['merchant_bucket'] = "testdenongmerchant";
/*
 * 银行卡验证接口key
 */
$_SC['bank_card_api_key'] = 'f3863416a7fe421e75b11d4e60d10a3b';
$_SC['identity_key']='denong';
$_SC['identity_secret']='dengnong-13800138000-dn';
/*
 * JPush极光推送接口config
 */
$_SC['JPush_key'] = '8e3138b19bef6e12a978b16c';
$_SC['JPush_secret']='de59fba883d2c092fbaedc40';
$_SC['JPush_log_path']='/var/log/JPush/'.date('Ym').'.log';
$_SC['JPush_apns_production']=false;//是否生成环境
$_SC['JPush_time_to_live'] = 863999;//离线消息保留时长
?>
