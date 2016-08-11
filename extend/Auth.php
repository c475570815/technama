<?php
namespace Auth;

use think\Config;
use think\Db;
//数据库
/*
-- ----------------------------
-- think_auth_rule，规则表，
-- id:主键，name：规则唯一标识, title：规则中文名称 status 状态：为1正常，为0禁用，condition：规则表达式，为空表示存在就验证，不为空表示按照条件验证
-- ----------------------------
 DROP TABLE IF EXISTS `think_auth_rule`;
CREATE TABLE `think_auth_rule` (  
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,  
    `name` char(80) NOT NULL DEFAULT '',  
    `title` char(20) NOT NULL DEFAULT '',  
    `status` tinyint(1) NOT NULL DEFAULT '1',  
    `condition` char(100) NOT NULL DEFAULT '',  
    PRIMARY KEY (`id`),  
    UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
-- ----------------------------
-- think_auth_group 用户组表， 
-- id：主键， title:用户组中文名称， rules：用户组拥有的规则id， 多个规则","隔开，status 状态：为1正常，为0禁用
-- ----------------------------
 DROP TABLE IF EXISTS `think_auth_group`;
CREATE TABLE `think_auth_group` ( 
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
    `title` char(100) NOT NULL DEFAULT '', 
    `status` tinyint(1) NOT NULL DEFAULT '1', 
    `rules` char(80) NOT NULL DEFAULT '', 
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
-- ----------------------------
-- think_auth_group_access 用户组明细表
-- uid:用户id，group_id：用户组id
-- ----------------------------
DROP TABLE IF EXISTS `think_auth_group_access`;
CREATE TABLE `think_auth_group_access` (  
    `uid` mediumint(8) unsigned NOT NULL,  
    `group_id` mediumint(8) unsigned NOT NULL, 
    UNIQUE KEY `uid_group_id` (`uid`,`group_id`),  
    KEY `uid` (`uid`), 
    KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 */
/**
* 权限认证类
 * 功能特性：
 * 1，是对规则进行认证，不是对节点进行认证。用户可以把节点当作规则名称实现对节点进行认证。
 *      $auth=new Auth();
 *      $auth->check('规则名称','用户id')
 * 2，可以同时对多条规则进行认证，并设置多条规则的关系（or或者and）
 *      $auth=new Auth();  $auth->check('规则1,规则2','用户id','and') 
 *      第三个参数为and时表示，用户需要同时具有规则1和规则2的权限。 当第三个参数为or时，表示用户值需要具备其中一个条件即可。默认为or
 * 3，一个用户可以属于多个用户组(think_auth_group_access表 定义了用户所属用户组)。我们需要设置每个用户组拥有哪些规则(think_auth_group 定义了用户组权限)
 * 
 * 4，支持规则表达式。
 *      在think_auth_rule 表中定义一条规则时，如果type为1， condition字段就可以定义规则表达式。 如定义{score}>5  and {score}<100  表示用户的分数在5-100之间时这条规则才会通过。
*/
class Auth
{
    //默认配置
    private $_config = [
        'AUTH_ON'           => true, //认证开关
        'AUTH_TYPE'         => 1, // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP'        => 'c_auth_group', //用户组数据表名
        'AUTH_GROUP_ACCESS' => 'c_auth_group_access', //用户组明细表
        'AUTH_RULE'         => 'c_auth_rule', //权限规则表
        'AUTH_USER'         => 'c_user'//用户信息表
    ];

    public function __construct()
    {
        if (Config::get('auth')) {
            $this->_config = array_merge($this->_config, Config::get('auth'));
        }
    }

    /**
     * 校验权限
     * @param $name  name为逗号分隔字符串，只要字符串中有一个通过则通过，如果为false需要全部条件通过。
     * @param $uid 认证的用户id
     * @param string $relation $or 是否为or关系
     * @return bool
     */
    public function check($name, $uid, $relation = 'or')
    {
        if (!$this->_config['AUTH_ON']) {
            return true;
        }
        $authList = $this->getAuthList($uid);
        if (is_string($name)) {
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = array($name);
            }
        }
        $list = array();
        foreach ($authList as $val) {
            if (in_array($val, $name)) {
                $list[] = $val;
            }
        }
        if ($relation == 'or' and !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ($relation == 'and' and empty($diff)) {
            return true;
        }
        return false;
    }

    /**
     * 获得用户组
     * @param $uid 用户编号
     * @return mixed 用户组
     */
    public function getGroups($uid)
    {
        static $groups = array();
        if (isset($groups[$uid])) {
            return $groups[$uid];
        }
        $user_groups = Db::table($this->_config['AUTH_GROUP_ACCESS'])
            ->alias('aga')
            ->join($this->_config['AUTH_GROUP'] . ' ag', 'aga.group_id = ag.id')
            ->where(['aga.uid' => $uid])
            ->select();
        $groups[$uid] = $user_groups ? $user_groups : array();
        return $groups[$uid];
    }

    /**
     * 获得权限列表
     * @param $uid
     * @return array|mixed
     */
    protected function getAuthList($uid)
    {
        static $_authList = array();
        if (isset($_authList[$uid])) {
            return $_authList[$uid];
        }
        if (isset($_SESSION['_AUTH_LIST_' . $uid])) {
            return $_SESSION['_AUTH_LIST_' . $uid];
        }
        $groups = $this->getGroups($uid);
        $ids = array();
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid] = array();
            return array();
        }
        $map = array(
            'id'     => array('in', $ids),
            'status' => 1
        );
        $rules = Db::table($this->_config['AUTH_RULE'])
            ->where($map)
            ->select();
        $authList = array();
        foreach ($rules as $r) {
            if (!empty($r['condition'])) {
                $user = $this->getUserInfo($uid);
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $r['condition']);
                $condition = '';
                @(eval('$condition=(' . $command . ');'));
                if ($condition) {
                    $authList[] = $r['name'];
                }
            } else {
                $authList[] = $r['name'];
            }
        }
        $_authList[$uid] = $authList;
        if ($this->_config['AUTH_TYPE'] == 2) {
            $_SESSION['_AUTH_LIST' . $uid] = $authList;
        }
        return $authList;
    }

    /**
     * 获得用户资料
     * @param $uid 用户ID
     * @return mixed   存放用户记录的数组
     */
    protected function getUserInfo($uid)
    {
        static $userinfo = array();//存放用户记录的数组
        if (!isset($userinfo[$uid])) {
            $userinfo[$uid] = Db::table($this->_config['AUTH_USER'])
                ->find($uid);
        }
        return $userinfo[$uid];
    }
}