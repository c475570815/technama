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
 * @param $userid    用户名，后必须带后缀
 * @param $pass
 */
function ldapValid($userid,$pass){
    $ldap_server_ip = '172.16.0.3';
    $ldap_server_port = '389';
    $user =$userid.'@scetc.local';
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
?>