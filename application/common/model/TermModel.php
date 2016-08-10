<?php
/**
 * Created by PhpStorm.
 * User: FuJinsong
 * Date: 2016/8/9
 * Time: 15:50
 */

namespace app\common\model;


class TermModel extends \app\common\model\CommonModel
{
// 设置当前模型对应的完整数据表名称
    protected $table = 'tbl_term';

    /**
     * 判断一个学期是否已存在
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