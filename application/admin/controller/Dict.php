<?php
/*
  定义类的命名空间,
  也就是说类的完整名字 app.test.controller.Controllerguowushi
  PHP的命名空间是按目录来整理的
 */
namespace app\admin\controller;

use think\Controller;
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

class Dict extends Controller
{

    /**
     * 用于显示、查找、排序
     */
    public function index(){
        // 获取字典类型
        $dict= new DictModel();
        $dict_category= $dict->distinct(true)->field('dict_category')->select();
        // 视图显示
        $view = new View();
        $view->assign("dict_category",  $dict_category);
        return $view->fetch('datagrid');
    }

    /**
     * ajax数据显示
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public  function ac1(){
        $dict_grid= new DictDataGrid();
        return $dict_grid->dataGridJson();
    }


    /**
     * 保存数据(添加和修改通用)
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function save(){
        $form_data=$_POST['data'];
        $ret=array(
            'success'=>false,'message'=>'添加失败'
        );
        //操作是添加还是修改
        $operation=$_POST['operation'];
        if($operation=='add'){
            if( $this->addRecord($form_data) ){
                $ret=['success'=>true,'message'=>'添加成功'];
            }else{
                $ret=['success'=>false,'message'=>'该用户已存在，添加失败！'];
            }
        }else{
            if( $this->updateRecord($form_data)) {
                $ret = ['success' => true, 'message' => '修改成功'];
            }else{
                $ret = ['success' => false, 'message' => '记录已存在！'];
            }
        }
        return json($ret);
    }

    /**
     * 根据表单数据添加到表中，带有逻辑判断
     * @param $form_data
     * @return bool
     */
    private function addRecord($form_data){
        $mo=new DictModel();
        $mo->dict_category=$form_data['dict_category'];
        $mo->dict_key=$form_data['dict_key'];
        $mo->dict_value=$form_data['dict_value'];
        if(  !$mo->isExist() ){
            $mo->data($form_data);
            $mo->save();
            return true;
        }else{
            return false;
        }
    }

    /**
     * 根据表单数据修改，带有逻辑判断
     * @param $form_data
     * @return bool
     */
    private function updateRecord($form_data){
        $pk=$form_data['dict_id'];
        $new_record= new DictModel();
        $new_record->dict_category=$form_data['dict_category'];
        $new_record->dict_key=$form_data['dict_key'];
        $new_record->dict_value=$form_data['dict_value'];
        $new_record->dict_id=$form_data['dict_id'];
        if(  !$new_record->isExist() ) {
            $new_record->save($form_data, ['dict_id' => $pk]);
            return true;
        }else{
            return false;
        }
    }

    /*  添加页面 */
    public function add(){
        $view = new View();
        //$view->assign("dict_category",  $dict_category);
        return $view->fetch('form');
    }
    /* 处理用户编辑那一条记录 */
    public  function update($pk){
        $view = new View();
        $mo=new DictModel();
        $record=$mo->where('dict_id',$pk)->find();
        $view->assign("operation",  '编辑');
        $view->assign("record",  $record);
        return $view->fetch('form');

    }

    /**
     * 按类别获取字典
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getDictByCategory(){
        $category=$_POST['category'];
        $mo=new DictModel();
        return   $record=$mo->where('dict_category',$category)->select();
    }

}

?>