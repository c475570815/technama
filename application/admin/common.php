<?php
/**
 * 如果需要给当前应用添加函数，只需要在应用的公共文件（application/common.php）中定义需要的函数即可，系统会自动加载
 * 如果你需要增加新的函数文件，例如需要增加一个sys.php，那么就需要和上面一样设置extra_file_list配置：
 * 'extra_file_list'        => [ APP_PATH . 'helper.php', THINK_PATH . 'helper.php', APP_PATH . 'sys.php'],
 */
/**
 * 根据字符串返回数组
 * @param $str  如1,2-6,8-10
 * @return array  [1,2,3,4,5,6,8,9,10]
 */
function getWeeksByString($str){
    $arr=array();
    $sections=explode(",", $str);

    foreach ($sections as $sec){
        $pos=strpos($sec, '-');
        if( $pos==false && is_integer(intval($sec))){
            $arr[$sec]=$sec;
        }else{
            $a2b=explode("-", $sec);
            $a=intval($a2b[0]);
            $b=intval($a2b[1]);
            for($a;$a<=$b;$a++){
                $arr[$a]=$a;
            }
        }
    }
    return $arr;
}


/**
 * 获取用户真实IP
 * @return string
 */
function get_client_ip() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"),
            "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']
        && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return ($ip);
}


/**
 * 根据ip地址获取地址信息
 * @param $ip
 * @return mixed
 */
function getAddressFromIp($ip){
    $urlTaobao = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
    $urlSina = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$ip;
    $json = file_get_contents($urlTaobao);
    $jsonDecode = json_decode($json);
    if($jsonDecode->code==0){//如果取不到就去取新浪的
        $data['country'] = $jsonDecode->data->country;
        $data['province'] = $jsonDecode->data->region;
        $data['city'] = $jsonDecode->data->city;
        $data['isp'] = $jsonDecode->data->isp;
        return $data;
    }else{
        $json = file_get_contents($urlSina);
        $jsonDecode = json_decode($json);
        $data['country'] = $jsonDecode->country;
        $data['province'] = $jsonDecode->province;
        $data['city'] = $jsonDecode->city;
        $data['isp'] = $jsonDecode->isp;
        $data['district'] = $jsonDecode->district;
        return $data;
    }
}

/**
 * LDAP验证
 * @param $userid    用户名
 * @param $pass     密码
 */
function ldapValid($userid,$pass){
    $ldap_server_ip = '172.16.0.3';
    $ldap_server_port = '389';
    $user =$userid.'@scetc.local';//用户名后必须带后缀
    $basedn = 'DC=scetc,DC=local';
    //建立到ldap服务器的连接
    $ldapConnect = ldap_connect($ldap_server_ip, $ldap_server_port);
    // 设置Active Driectory服务器选项
    ldap_set_option( $ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3 );
    ldap_set_option($ldapConnect,LDAP_OPT_REFERRALS, 0 );
    //验证帐号密码
    $bind = @ldap_bind($ldapConnect, $user, $pass);
    if ($bind) {
        ldap_close($ldapConnect);
        return true;
    } else {
        ldap_close($ldapConnect);
        return false;
    }
}

/**
 * 发送邮件
 * @param $to       收件人，可以是字符串或数组
 * @param $subject  主题
 * @param $body     内容
 * @return bool
 */
function sendmail($to, $subject, $body)
{
    vendor("PHPMailer.PHPMailerAutoload");
    $mail = new \PHPMailer(true);
    $mail->IsSMTP();
    //$mail->SMTPDebug = 3;
    $mail->CharSet = 'UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
    $mail->SMTPAuth = true; //开启认证
    $mail->SMTPSecure = 'ssl';      // 使用TLS加密，也支持ssl
    $mail->Priority = 3;   // 设置邮件优先级 1高, 3正常（默认）, 5低 
    $mail->Port = config('THINK_EMAIL.SMTP_PORT');                  // TCP 端口
    $mail->Host = config('THINK_EMAIL.SMTP_HOST');
    $mail->Username = config('THINK_EMAIL.SMTP_USER');;
    $mail->Password = config('THINK_EMAIL.SMTP_PASS');;
    $mail->AddReplyTo(config('THINK_EMAIL.REPLY_EMAIL'), config('THINK_EMAIL.REPLY_NAME'));//回复地址
    $mail->From = config('THINK_EMAIL.FROM_EMAIL');
    $mail->FromName = config('THINK_EMAIL.FROM_NAME');
    if(is_array($to) ){
        foreach($to as $addr){
            $mail->AddAddress($addr);
        }
    }else{
        $mail->AddAddress($to);
    }
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->WordWrap = 80; // 设置每行字符串的长度
    //$mail->AddAttachment("f:/test.png"); //可以添加附件
    $mail->IsHTML(true);
    if ($mail->Send()) {
        return true;
    } else {
        return false;
    }
}
/**
 * 网易云信发送短信
 * http://dev.netease.im/docs?doc=server&#发送模板短信
 * @param string $mobile
 * @param string $params
 * @param string $templateid
 * @return array
 */
function netease_sms($mobile='',$params='',$templateid=''){
    header("Content-Type:text/html; charset=utf-8");
    $AppKey =config('neteaseim.appkey');
    $AppSecret = config('neteaseim.appsecret');
    $Nonce = rand(100000,999999);
    $CurTime = time();
    $CheckSum = strtolower(sha1($AppSecret.$Nonce.$CurTime));
    $url =  config('neteaseim.sms_url');
    $head_arr = array();
    $head_arr[] = 'Content-Type: application/x-www-form-urlencoded';
    $head_arr[] = 'charset: utf-8';
    $head_arr[] = 'AppKey:'.$AppKey;
    $head_arr[] = 'Nonce:'.$Nonce;
    $head_arr[] = 'CurTime:'.$CurTime;
    $head_arr[] = 'CheckSum:'.$CheckSum;
    $data = array();
    $data['templateid'] = $templateid;
    $data['mobiles'] = $mobile;
    $data['params'] = $params;
    //var_dump($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head_arr);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $result = curl_exec($ch);
    curl_close($ch);
    $resArr = (array) json_decode($result);
    //$resArr = (array) json_decode('{"code":200,"msg":"sendid","obj":1}');
    //var_dump($resArr);
    //echo $resArr['code'];
    return $resArr;
}
/**
 * yuntongxun.com发送模板短信
 * @param to 手机号码集合,用英文逗号分开
 * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
 * @param $tempId 模板Id
 */
function yuntongxun_sms($to, $datas, $tempId)
{
    // 读取配置信息
    $accountSid = config('yuntongxun.ACCOUNT_SID');
    $accountToken = config('yuntongxun.AUTH_TOKEN');
    $appId = config('yuntongxun.APPID');
    $serverIP =config('yuntongxun.REST_URL');
    $serverPort = config('yuntongxun.PORT');
    $softVersion = config('yuntongxun.VERSION');
    // 初始化REST SDK
    vendor("yuntongxun.CCPRestSDK");
    $rest = new \REST($serverIP, $serverPort, $softVersion);
    $rest->setAccount($accountSid, $accountToken);
    $rest->setAppId($appId);
    // 发送模板短信
    $result = $rest->sendTemplateSMS($to, $datas, $tempId);
    if ($result == NULL) {
        $ret = ['success' => false, 'message' => '错误'];
    }
    if ($result->statusCode != 0) {
        $ret = ['success' => false, 'message' => $result->statusMsg];
    } else {
        //echo "Sendind TemplateSMS success!<br/>";
        // 获取返回信息
        $smsmessage = $result->TemplateSMS;
        $ret = ['success' => true, 'message' => $smsmessage->dateCreated.' 消息发送成功！'];

    }
    return json($ret);
}
?>