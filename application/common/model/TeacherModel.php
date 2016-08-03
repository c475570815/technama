<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 10:38
 */

namespace app\common\model;
use think\Model;

class TeacherModel extends \app\common\model\CommonModel
{
    protected $table = 'tbl_teacher';
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
            return false;
        }

    }
}