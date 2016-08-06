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