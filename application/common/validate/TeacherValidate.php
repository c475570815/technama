<?php
namespace app\common\validate;
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
        'class_name'  =>  'require|max:25',
        'class_room' =>  'require',
        'class_supervisor' => 'require',
        'calss_adviser' => 'require'
    ];

    protected $message  =   [
        'class_name.require' => '名称必须',
        'class_name.max'     => '名称最多不能超过25个字符',
        'age.number'   => '年龄必须是数字',
        'age.between'  => '年龄只能在1-120之间',
        'email'        => '邮箱格式错误',
    ];


    /**
     * 自定义验证规则
     * @param $value 实际字段值
     * @param $rule  规则值
     * @param $data  数据
     * @return bool|string
     */
    protected function checkName($value,$rule,$data)
    {
        // return $rule == $value ? true : '名称错误';
    }
}