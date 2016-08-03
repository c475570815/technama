<?php
/**
 * Created by PhpStorm.
 * User: FuJinsong
 * Date: 2016/7/21
 * Time: 20:13
 */

namespace app\admin;
use think\Db;
use app\common\model\ScheduleModel;
use think\Request;

class ScheduleDataGrid extends   \app\common\DataGrid
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
            $model=new ScheduleModel();
            $arr_where= $model->filer($dict);
            //print_r($arr_where);
        }
        return $arr_where;
    }

    /**
     * DataGird 对应的Model
     * @return ScheduleModel
     */
    public function getCurrentTable()
    {
        return  new ScheduleModel();

    }

}