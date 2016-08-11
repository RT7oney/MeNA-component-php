<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 16/3/21
 * Time: 下午5:05
 */
include_once './common/config.inc.php';

// 是否是邮箱 by Ryan
function is_email($email){
  if (preg_match('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $email)) {
      return true;
  }else{
      return false;
  }
}
//验证手机号合法
function is_mobile($value){
    if (preg_match('/^1[34578][0-9]{9}$/', $value)) {
        return true;
    }else{
        return false;
    }
}
//mysql连接
function common_db_mysql_connect() {
    global $_SG,$_SC;
    include_once('./common/Mysql.php');
    $_SG['db'] = new dbstuff;
    $_SG['db']->connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw'], $_SC['dbname'], $_SC['pconnect']);
}

//创建分表
function create_table($table_name,$new_table_name){
    global $_SG;
    $sql = "create table `$new_table_name` like `$table_name`";
    $query = $_SG['db']->query($sql);
    if($query){
        return true;
    }else{
        return false;
    }
}

//判断表是否存在
function table_exist($table_name){
    global $_SG,$_SC;
    $sql = "show tables like '".$table_name."' ";
    $query = $_SG['db']->query($sql);
    $row = $_SG['db']->fetch_array($query);
    if(count($row) > 0){
        return true;
    }else{
        return false;
    }
}

//判断短信验证码是否存在,有效 by lichao
function issms_exist($code,$phone,$action){
    global $_SG;
    $time=date('Ym',time());
    $time2=date('Ym',time()-7200);
    if($time == $time2){    //当前时间年月和2小时之前的时间年月一样
        $table_name='sms_code_'.$time;
        if(table_exist($table_name)){
            $sql = "select * from `$table_name` where code = '".$code."' and phone='".$phone."' and action='".$action."' and status=1 and send_status=1 and overdue_time>now() limit 1";
            $query = $_SG['db']->query($sql);
            $row = $_SG['db']->fetch_array($query);
            if(count($row) > 0 && is_array($row)){
                $row['table_name']=$table_name;
                return $row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }else{                  //当前时间年月和2小时之前的时间年月不一样
        $table_name='sms_code_'.$time;
        if(table_exist($table_name)){
            $sql = "select * from `$table_name` where code = '".$code."' and phone='".$phone."' and action='".$action."' and status=1 and send_status=1  and overdue_time>now() limit 1";
            $query = $_SG['db']->query($sql);
            $row = $_SG['db']->fetch_array($query);
            if(count($row) > 0 && is_array($row)){
                $row['table_name']=$table_name;
                return $row;
            }else{
                $table_name2='sms_code_'.$time2;
                $sql2 = "select * from `$table_name2` where code = '".$code."' and phone='".$phone."' and action='".$action."' and status=1 and send_status=1 and overdue_time>now() limit 1";
                $query2 = $_SG['db']->query($sql2);
                $row2 = $_SG['db']->fetch_array($query2);
                if(count($row2) > 0 && is_array($row2)){
                    $row2['table_name']=$table_name2;
                    return $row2;
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }

    }

}


function my_encrypt($data){
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128,AES_KEY,addPkcs7Padding(json_encode($data)),MCRYPT_MODE_CBC,AES_IV));
}

function my_decrypt($data){
    return stripPkcs7Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_128,AES_KEY,base64_decode($data),MCRYPT_MODE_CBC,AES_IV));
}

function addPkcs7Padding($string=null, $blocksize = 32) {
    $len = strlen($string); //取得字符串长度
    $pad = $blocksize - ($len % $blocksize); //取得补码的长度
    $string .= str_repeat(chr($pad), $pad); //用ASCII码为补码长度的字符， 补足最后一段
    return $string;
}

function stripPkcs7Padding($string){
    $slast = ord(substr($string, -1));
    $slastc = chr($slast);
    $pcheck = substr($string, -$slast);
    if(preg_match("/$slastc{".$slast."}/", $string)){
        $string = substr($string, 0, strlen($string)-$slast);
        return $string;
    } else {
        return false;
    }
}


//精确毫秒时间戳
function microtime_float(){

    list($usec, $sec) = explode(" ", microtime());

    return ((float)$usec + (float)$sec);

}
//随即产生字符串
function common_get_randChar($length){
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;

    for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];
    }
    return $str;
}

/**
 * ---------------------
 * 调用德浓ROR接口函数封装
 * ---------------------
 *   by  Ryan
 */


 /**
  * 拼装请求报头 by Ryan
  * @param array $data
  */
function dnHeader($data)
  {
     return array(
       'X-User-Token:'.$data['token'],
       'X-User-Phone:'.$data['phone']
     );
  }


  /**
   * curl方法 by Ryan
   */
function sub_curl_post($url, $data, $header_arr) {
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

function sub_curl_get($url, $data, $header_arr) {
      $header = array('Content-Type: application/json', 'Accept: application/json');
      if ($header_arr) {
          $header = array_merge($header, $header_arr);
      }
      if ($data) {
          foreach ($data as $key => $value) {
              $url.= "&$key=" . rawurlencode($value);
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

function sub_curl_patch($url, $data, $header_arr){

    $header = array('Content-Type: application/json', 'Accept: application/json');
    if ($header_arr) {
        $header = array_merge($header, $header_arr);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    // curl_setopt($ch, CURLOPT_POST, true);
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

function curl_img($url, $data, $header_arr){

    $header = array('Content-Type: application/json', 'Accept: application/json');
    if ($header_arr) {
        $header = array_merge($header, $header_arr);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
    $res=curl_exec( $ch );
    curl_close ( $ch );

    return json_decode($res, true);
  }


