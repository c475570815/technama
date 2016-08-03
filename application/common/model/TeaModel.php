<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/20
 * Time: 17:19
 */

namespace app\common\model;
use think\Model;

class TeaModel extends \app\common\model\CommonModel
{
    protected $table = 'tbl_teacher';
    /**
     * 判断一个教师编号是否已存在
     * @param $pk
     * @return bool
     */
    protected $defaults = [
        'profess_duty'=>'like',
        'teach_name' => 'like',
        'teach_pass' => 'like',
        'teach_id' => 'like',
        'dept_name' =>'in'
    ];
    public function isExist($pk){

        if($this->get($pk)) {
            return true;
        }
        else{
            return false;
        }
   //////////////////11111111111
    }
}
