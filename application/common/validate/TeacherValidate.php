<?php
namespace app\common\validate;
use think\Db;
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/27
 * Time: 22:07
 */
use think\Validate;

/**
 *  Teacher模型的验证类
 * Class TeacherValidate
 * @package app\common\validate
 */
class TeacherValidate  extends Validate
{

    /**
     * 定义规则数组
     * 一条规则包括 验证字段,验证规则函数,错误提示[,验证条件][,附加规则][,验证时间]
     * 规则的写法有两种
     * (1)  'age'   => 'number|between:1,120',
     * (2)  'age'   => ['number','between'=>'1,120'],
     * @var array
     */
    protected $rule = [
        'teach_id'=> 'require|length:6',
//        'dept_name'  =>  'is_unique:200|require',
//        'email'=>'email'

    ];

    protected $message  =   [
        'teach_id'=>'教师工号必须填6位数字'
//        'dept_name.is_unique'=>'找不到此系部',
//        'email'=>'不是邮箱格式'
    ];


    /**
     * 自定义验证规则
     * @param $value 实际字段值
     * @param $rule  规则值
     * @param $data  数据
     * @return bool|string
     */
    protected function  is_unique($value,$rule)
    {

        //$db  = \think\Db::table('tbl_classes');
        //var_dump( $db->where('class_name',$value)->find());
        $list=Db::table('tbl_department')->where('dept_name',$value)->find();
        if($list){
            return true;
        }else{
            return false;
        }
    }
}