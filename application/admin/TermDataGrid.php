<?php

/**
 * Created by PhpStorm.
 * User: FuJinsong
 * Date: 2016/8/9
 * Time: 16:07
 */

namespace app\admin;
use think\Db;
use app\common\model\TermModel;

class TermDataGrid extends   \app\common\DataGrid
{
    public $model;
    /**
     * 返回一个包含查询条件的数组
     * @return array
     */
    public function getWhere()
    {
        // TODO: Implement getWhere() method.
        // 获取用户提交的查找数据.构造查询语句
        $arr_where=array();
        $request = request();
        if(isset($_POST['dict'])){
            $dict=$_POST['dict'];
            $model=new TermModel();
            $arr_where= $model->filer($dict);
            //print_r($arr_where);
        }
        return $arr_where;
    }

    /**
     * DataGird 对应的Model
     * @return TermModel
     */
    public function getCurrentTable()
    {
        return  new TermModel();

    }
}