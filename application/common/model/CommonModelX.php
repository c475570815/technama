<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 12:00
 */

namespace app\common\model;

use think\Model;
use think\Db;
use app\admin\model\classesModel;
use app\admin\model\DeptModel;
/**
 * 对数据表的基本操作
 * Class DictModel
 * @package app\common\model
 */
 class CommonModelX extends Model
{
	//where数组   
	protected $get_where;
	/**
	 *用于从外部获取值where//特别规定为：下方函数dataGridJson参数
	 * 所对应的类里的$defaults和网页传来的值在下方filer（）函数里
	 * 一起构建的where
	 */
	public function get_power($where){
	  $this->get_where=$where;
	}
    /**
	 * 函数的参数为你要查询的表所对应的模型的实例
	 */
    public  function dataGridJson($tel)
     {

         //统计总条数
        $total=intval($tel->count());
         //把查询的限制条件放入where方法中
        $tel->where($this->get_where);
        //获取客户端传递过来的参数 page=2&rows=20
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;

        $start = ($page - 1) * $rows;
        //$current_table->limit($start, $rows);
        $tel->limit($start,$rows);

        // 排序结果排序
        if(isset($_POST['sort']) &&  isset($_POST['order'])){
            $sort = $_POST['sort'] ;
            $order = $_POST['order'];
            //$current_table->order($sort,$order);
            $tel->order($sort,$order);
        }
        //执行查询语句返回数组
        $list=$tel->select();
        // 把数组和条数组合变成json语句    返回JSON
        return json(['total' => $total, 'rows' => $list]);
    }
	public static function filer($dict,$defaults)
	{
        $arr_where=array();
        //dump($dict);
        foreach($dict as $field => $condition) {
           //  echo $field.$condition;
            if ( $condition  <> ''  )
			{
                if($condition <> '全部')
                {
                    if(array_key_exists($field,$defaults)){
                        $opt= $defaults[$field];
                        switch ($opt) {
                            case 'like':$arr_where[$field] = array($defaults[$field],'%'.$condition.'%');break;
                            default: $arr_where[$field] = array('=',$condition ); break;
                        }
                    }else{
                        $arr_where[$field] = array('=',$condition);
                    }
                }
            }

        }
    return  $arr_where;
    }
}