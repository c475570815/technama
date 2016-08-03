<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 19:13
 */

namespace app\mcwnew\model;
use think\Model;


class ClassesModel extends Model{

    public  $table = 'tbl_classes';
    /*
     * 存放默认的字段筛选规则 字段和值之间默认是=
     *  field=value
     *  [
     *     field1 => " like "
     *     field2 => " >  "
     *   ]
     */
    public  $defaults = [
        'class_name' => 'like',
        'class_room' => 'like',
         'class_supervisor' => 'like',
        'calss_adviser' => 'like',
    ];
	
}