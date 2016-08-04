<?php
namespace app\common\validate;
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/27
 * Time: 22:07
 */
use think\Validate;
use think\Db;
class ClassesValidate  extends Validate
{

    /**
     * 定义规则数组
     * 一条规则包括 验证字段,验证规则函数,错误提示[,验证条件][,附加规则][,验证时间]
     * 规则的写法有两种
     * (1)  'age'   => 'number|between:1,120',
     * (2)  'age'   => ['number','between'=>'1,120'],
     * 注意：UTF8格式一个中文为长度为3
     * @var array
     */
    protected $rule = [
        'class_name'  =>  'is_unique:200|max:40',
        'dept_name' =>  'require',


    ];

    protected $message  =   [
        'class_name.require' => '班级名不能为空',
        'class_name.max'     => '班级名称最多不能超过40个字符',
        'class_name.is_unique' => '班级名已存在',
        'dept_name'     => '系部不能为空',

    ];


    /**
     * 自定义验证规则
     * @param $value 实际字段值
     * @param $rule  规则值
     * @param $data  数据
     * @return bool|string 通过验证返回true，否则返回错误提示
     */
    protected function checkName($value,$rule,$data)
    {
         return $rule == $value ? true : '名称错误';
        //return true;
    }

    /**
     * 必须
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    protected function  is_unique($value,$rule)
    {

        //$db  = \think\Db::table('tbl_classes');
        //var_dump( $db->where('class_name',$value)->find());
        $list=Db::table('tbl_classes')->where('class_name',$value)->find();
        if($list){
            return false;
        }else{
            return true;
        }
    }

    protected function max($value, $rule)
    {
       // $length = strlen((string) $value);
        $length = mb_strlen((string) $value);
        return $length <= $rule;
    }
}