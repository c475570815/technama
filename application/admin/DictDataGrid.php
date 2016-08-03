<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 12:03
 */

namespace app\admin;
use think\Db;

use app\common\model\DictModel;
use think\Request;
/**
 * 实际获取DataGrid数据的辅助类
 * Class DictDataGrid
 * @package app\admin
 */
class DictDataGrid extends   \app\common\DataGrid
{

    public $model;
    public function getWhere()
    {
        // TODO: Implement getWhere() method.
        // 获取用户提交的查找数据.构造查询语句
        $arr_where=array();
        $request = request();
        if(isset($_POST['dict'])){
            $dict=$_POST['dict'];
            /*
            if($dict['dict_category'] && $dict['dict_category']<>'全部'){
                $arr_where["dict_category"]=['=',$dict['dict_category']];
            }
            if($dict['dict_value']){
                $arr_where["dict_value"]=['like','%'.$dict['dict_value'].'%'];
            }*/
            $model=new DictModel();
            $arr_where= $model->filer($dict);
            //print_r($arr_where);
        }
        return $arr_where;
    }

    public function getCurrentTable()
    {
        // TODO: Implement getCurrentTable() method.
        return  new DictModel();

    }

}