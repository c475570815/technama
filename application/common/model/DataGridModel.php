<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 11:50
 */

namespace app\common\model;
use think\Db;
use think\Model;

abstract class DataGridModel
{

    /*  下面是抽象方法，也就是子类需要具体实现的方法 */
    public abstract function getWhere();// 返回Where条件数组
    public abstract function getCurrentTable();// 返回当前的表对象

    /**
     * 返回针对DataGrid表的的数据
     */
    public  function dataGridJson(){

        $current_table =$this->getCurrentTable();
        // 获取查询条件
        $current_table->where($this->getWhere());
        //获取客户端传递过来的参数 page=2&rows=20
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        //记录的总数
        $total = intval($current_table->count());
        $start = ($page - 1) * $rows;
        $current_table->limit($start, $rows);
        $list = $current_table->select();

        return json(['total' => $total, 'rows' => $list]);

    }
}