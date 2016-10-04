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
        'sub_dept' =>'in',
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
    /**
     * 根据传递过来的对象值，产生查询的条件数组
     * where('field','opt','condition');
     * where('name','like','%zhang%');
     * @param $dict
     * @return array
     */
    public function filer($dict)
    {
        $arr_where=array();
        //dump($dict);
        foreach($dict as $field => $condition) {
            //  echo $field.$condition;
            if ( $condition  <> ''  ) {
                if($condition <> '全部'){
                    if(array_key_exists($field,$this->defaults)){
                        $opt= $this->defaults[$field];
                        switch ($opt) {
                            case 'like':
                                $arr_where[$field] = array(
                                    $this->defaults[$field],
                                    '%'.$condition.'%'
                                );
                                break;
                            case 'in':
                                $arr_where[$field] = array(
                                    'in',
                                    $condition
                                );
                                break;

                            default:
                                $arr_where[$field] = array(
                                    '=',
                                    $condition
                                );
                        }
                    }else{
                        $arr_where[$field] = array(
                            '=',
                            $condition
                        );
                    }
                }
            }

        }
        return  $arr_where;

    }
}
