<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 12:03
 */

namespace app\admin;
use think\Db;

use app\common\model\DeptModel;
use think\Request;
/**
 * 实际获取DataGrid数据的辅助类
 * Class DictDataGrid
 * @package app\admin
 */
class DeptTreeGrid  extends   \app\common\DataGrid
{


    /**
     * 获取指定节点下的所有子节点
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function getTreeJson(){
        // 节点id
        if(isset( $_POST['id'])){
            $id =  $_POST['id'];
        }else{
            $id='';
        }
        // 所有记录
        $result = array();
        $tbl=new DeptModel();
        if(isset( $_POST['dept_category'])){
            $dept_category =  $_POST['dept_category'];
            $tbl->where("dept_category","in",$dept_category);
        }
        // 筛选
//        if(isset($_POST['sort']) &&  isset($_POST['order'])){
//            $sort = $_POST['sort'] ;
//            $order = $_POST['order'];
//            $tbl->order($sort,$order);
//        }
        // 排序
        if(isset($_POST['sort']) &&  isset($_POST['order'])){
            $sort = $_POST['sort'] ;
            $order = $_POST['order'];
            $tbl->order($sort,$order);
        }
//        Db::listen(function($sql, $time, $explain){
//            // 记录SQL
//            echo $sql. ' ['.$time.'s]';
//            // 查看性能分析结果
//            dump($explain);
//        });
        if($id==""){
            $cond['dept_parent']=array('EXP','is NULL');
        }else{
            $cond['dept_parent']=array('EQ',$id);
        }

        $children=$tbl->where($cond)->select();


        foreach ($children as $child){
            $child['state'] = $this->has_child($child['dept_name']) ? 'closed' : 'open';
            array_push($result, $child);
        }
        return json($result);
    }

    /**
     * 判断是否还有有子节点
     * @param $id
     * @return bool
     */
    function has_child($id){
        $dept=new DeptModel();
        $childCount=$dept->where('dept_parent','=',$id)->count();
        return $childCount > 0 ? true : false;
    }

    public function getWhere()
    {
        // TODO: Implement getWhere() method.
        // TODO: Implement getWhere() method.
        // 获取用户提交的查找数据.构造查询语句
        $arr_where=array();
        $request = request();
        if(isset($_POST['dict'])){
            $dict=$_POST['dict'];
            $model=$this->getCurrentTable();
            $arr_where= $model->filer($dict);
            //print_r($arr_where);
        }
        return $arr_where;
    }

    public function getCurrentTable()
    {
        // TODO: Implement getCurrentTable() method.
        return new DeptModel();
    }
}