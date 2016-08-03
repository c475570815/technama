<?php
/**
 * Created by PhpStorm.
 * User: FuJinsong
 * Date: 2016/7/21
 * Time: 20:09
 */

namespace app\common\model;


class ScheduleModel extends \app\common\model\CommonModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'tbl_schedule';
    /*
     * 存放默认的字段筛选规则 字段和值之间默认是=
     *  field=value
     *  [
     *     field1 => " like "
     *     field2 => " >  "
     *   ]
     */

    protected $defaults = [
        'term' => 'like',
        'time' => 'like',
        'class_name' => 'like',
        'course_name' => 'like',
        'week' => '=',
    ];
}