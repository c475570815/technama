<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 19:13
 */

namespace app\common\model;


class AdjustmentModel extends \app\common\model\CommonModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'tbl_adjustment';
    /*
     * 存放默认的字段筛选规则 字段和值之间默认是=
     *  field=value
     *  [
     *     field1 => " like "
     *     field2 => " >  "
     *   ]
     */

    protected $defaults = [
        'teach_id' => 'like',
        'class_room' => 'like',
         'class_name' => 'like',
        'course_name' => 'like',
        'week' => '=',
    ];
    public function isExist($pk){

        if($this->get($pk)) {
            return true;
        }
        else{
            return false;
        }

    }
}