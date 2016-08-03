<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 12:03
 */
namespace app\admin\component;
use app\common\model\RecordModel;
use think\Request;
/**
 * 实际获取DataGrid数据的辅助类
 */
class RecordDataGrid extends   \app\common\DataGrid
{

    /* 对应的Model */
    private $model;

    /**
     * RecordDataGrid constructor.
     */
    public function __construct()
    {
        $this->model=new RecordModel();
    }

    public function getModel(){
        return $this->model;
    }
    /**
     * 返回一个包含查询条件的数组
     * 数组格式[ 'fields'  ]
     * @return array
     */
    public function getWhere()
    {
        // 获取用户提交的查找数据.构造查询语句
        $arr_where=array();
        $request = request();
        $req_method=$request->method();
        if($req_method=='POST' && $request->param('dict') ){
            $dict=$request->param('dict');//获取表单值
            $model=$this->getModel();
            $arr_where= $model->filer($dict); //根据表单值，产生默认的Where数组
        }
        return $arr_where;
    }
    /**
     * DataGird 对应的Model
     * @return ClassesModel
     */
    public function getCurrentTable()
    {
          return  new RecordModel();

    }

}