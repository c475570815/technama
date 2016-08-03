<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 12:03
 */

namespace app\admin;
use think\Db;

use app\common\model\AdjustmentModel;
use think\Request;
/**
 * 实际获取DataGrid数据的辅助类
 * Class AdjustmentDataGrid
 * @package app\admin
 */
class AdjustmentDataGrid extends   \app\common\DataGrid
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
            $model=new AdjustmentModel();
            $arr_where= $model->filer($dict);
            //print_r($arr_where);
        }
        return $arr_where;
    }

    /**
     * DataGird 对应的Model
     * @return AdjustmentModel
     */
    public function getCurrentTable()
    {
          return  new AdjustmentModel();

    }

}