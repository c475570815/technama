<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 12:00
 */

namespace app\common\model;

use think\Model;

/**
 * 对数据表的基本操作
 * Class DictModel
 * @package app\common\model
 */
class CommonModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '';
    /*
     * 存放默认的字段筛选规则 字段和值之间默认是=
     *  field=value
     *  [
     *     field1 => " like "
     *     field2 => " >  "
     *   ]
     */
    protected $defaults = [

    ];


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