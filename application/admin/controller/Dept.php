<?php
/*
  定义类的命名空间,
  也就是说类的完整名字 app.test.controller.Controllerguowushi
  PHP的命名空间是按目录来整理的
 */
namespace app\admin\controller;

use app\admin\DeptTreeGrid;
use app\common\model\DeptModel;
use think\View;
use app\common\model\DictModel;
use app\common\model\DictCategoryModel;
use app\admin\DictDataGrid;
use think\Db;

/*
 定义Controller类，一个类中有多个方法（Action）
 类名应与文件名一致，且首字母大写
 方法名应小写
*/

class Dept
{

    /**
     * 显示dataGrid
     */
    public function index(){

        //$dict= new DictCategoryModel();
        //$dict_category=$dict->select();
        //dump($dict_category);
        $view = new View();
        //$view->assign("dict_category",  $dict_category);
        return $view->fetch('treegrid');
    }

    /**
     * ajax数据显示
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public  function getlist(){
        $grid= new DeptTreeGrid();
        return $grid->getTreeJson();
    }

    /* 返回部门类型 */
    public function deptcat(){
        $model=new DictModel();
        $list= $model->where('dict_category','部门类型')->select();
        return json($list);
    }

    public function import(){

    }
}

?>