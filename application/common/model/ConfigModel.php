<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/1
 * Time: 20:44
 */

namespace app\common\model;
use think\Model;

class ConfigModel extends \app\common\model\CommonModel
{
    protected $table = 'tbl_config';
    /**
     * 判断一个教师编号是否已存在
     * @param $pk
     * @return bool
     */
    protected $defaults = [
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