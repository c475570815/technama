<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 19:13
 */

namespace app\common\model;


class RecordModel extends \app\common\model\CommonModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'tbl_record';
    protected  $validate;
    /*
     * 存放默认的字段筛选规则数组。
     * 格式：
     *  [  field1 => " like "， field2 => " >  "   ]
     *  字段和值之间默认是等号
     */
    protected $defaults = [

    ];



    public function isValid(){
        $this->validate = Loader::validate('ClassesValidate');
        if(!$this->validate->check($this)){
            return false;
            //dump($validate->getError());
        }else{
            return true;
        }
    }
}