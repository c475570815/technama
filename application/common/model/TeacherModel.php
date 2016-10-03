<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 10:38
 */

namespace app\common\model;
use think\Model;

/**
 * 模型类支持
 * before_delete、after_delete、
 * before_write、after_write、
 * before_update、after_update、
 * before_insert、after_insert
 * 事件行为.
 * 注册的回调方法支持传入一个参数（当前的模型对象实例），并且before_write、before_insert、 before_update 、before_delete事件方法如果返回false，则不会继续执行
 * Class TeacherModel
 * @package app\common\model
 */
class TeacherModel extends \app\common\model\CommonModel
{
    protected $table = 'tbl_teacher';

    /**/
    public  static $fieldNameMap=[
        "teach_id"=>"教师编号",
        "dept_name"=>"部门",
        "sub_dept"=>"子部门",
        "teach_role"=>"角色",
        "teach_name"=>"姓名",
        "sex"=>"姓名",
        "profess_duty"=>"专业",
        "now_major"=>"现在专业",
        "holds_teacher"=>"兼职教师",
        "qq"=>"QQ",
        "email"=>"邮件",
        "teach_phone"=>"电话",
        "wechat_id"=>"微信号",
        "conuncilor"=>"是否是督导",
        "in_school_time"=>"进校时间",
        "location"=>"职位",
        "limit"=>"听课限制",
        "passed"=>"是否免听",
        "listen_count"=>"听课的次数",
        "listened_count"=>"被听课的次数",
        "email_validated"=>"邮件是否已验证",
        "mobile_validated"=>"手机是否已经验证",
        "teach_jw_id"=>"教务编号",




    ];

    public static function getFieldName($field){
        if(array_key_exists($field,self::$fieldNameMap)){
            return self::$fieldNameMap[$field];
        }else{
            return $field;
        }
   }

    /**
     * 获取器的作用是在获取数据的字段值后自动进行处理；还可以定义数据表中不存在的字段
     * @param $value
     * @return mixed
     */
    public function getStatusAttr($value)
    {
        $status = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
        return $status[$value];
    }
    /**
     * 构造函数
     * TeacherModel constructor.
     */
    public function __construct()
    {
        // 注册回调函数
        TeacherModel::event('after_insert','afterChange');
        TeacherModel::event('after_delete','afterChange');
        TeacherModel::event('after_write','afterChange');
        TeacherModel::event('after_update','afterChange');
    }
    /**
     * 在修改（写，修改，删除）之后执行。统计部门教师数
     */
    protected function afterChange($obj){
       $dept_list= DeptModel::column('dept_name');
        foreach ($dept_list as $dept){
          $tech_num= TeacherModel::where('dept_name',$dept['dept_name'])->count();
          DeptModel::where('dept_name',$dept['dept_name'])->update(['dept_staff_number' => $tech_num]);
        }
    }
    /**
     * 在插入之前执行.如果返回false，则不会继续执行
     */
    protected function beforeInsert($obj){

    }
    /**
     * 判断一个教师编号是否已存在
     * @param $pk
     * @return bool
     */
    public function isExist($pk){
        if($this->get($pk)) {
            return true;
        }
        else{
        }
    }
}