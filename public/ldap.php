<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/29
 * Time: 14:48
 */

$ldap_server_ip = '172.16.0.3';
$ldap_server_port = '389';
$user = 'SCETC\admodi';
$pass = 'ilovead';
// 用户名后必须带后缀
$user = '201512052598@scetc.local';
$pass = '916038';
// scetc.local
$basedn = 'DC=scetc,DC=local'; //

echo "$user =" . $pass;
//建立到ldap服务器的连接
$ldapConnect = ldap_connect($ldap_server_ip, $ldap_server_port);
// 设置Active Driectory服务器选项
ldap_set_option( $ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3 );
ldap_set_option($ldapConnect,LDAP_OPT_REFERRALS, 0 );
//验证帐号密码，ldap_bind第一个为绑定的连接，第二个为用户名(注意是否有后缀)，第三个为密码。
$bind = @ldap_bind($ldapConnect, $user, $pass);
if ($bind) {
    //验证成功
    echo "验证成功";
   /* $SEARCH_DN = 'OU=员工,DC=scetc,DC=local';
    $filter="(objectClass=*)";
    $userid = '030523';
    //搜索基本条件值(类似于数据库的库和表)
    //$filter = "(&(objectClass=user)(objectClass=person))";
    echo $filter;
    $fileds = array('givenName');
    $result = @ldap_search($ldapConnect, $SEARCH_DN, $filter, $fileds);
    $retData = @ldap_get_entries($ldapConnect, $result);
    var_dump($retData);*/
} else {
    echo "验证失败";
}
//关闭ldap连接
ldap_close($ldapConnect);