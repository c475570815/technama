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
 * 部门模型的验证类
 * Class DeptValidate
 * @package app\common\validate
 */
class DeptValidate  extends Validate
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
        'dept_name'  =>  'require|max:25',
        'dept_category' =>  'require'

    ];

    protected $message  =   [
        'dept_name.require' => '名称必须',
        'dept_name.max'     => '名称最多不能超过25个字符',
        'dept_category'   => '年龄必须是数字'

    ];


    /**
     * 自定义验证规则(函数名就是规则名)
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