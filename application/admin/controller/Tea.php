<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/21
 * Time: 9:42
 */

namespace app\admin\controller;
use think\Controller;
use app\common\model\TeaModel;
use app\admin\TeaDataGrid;
use app\common\model\DeptModel;
use think\View;
use think\Db;
use think\Request;
class Tea extends Controller
{
    /**s
     * 用于显示Grid
     */
    public function index(){
        // 获取系部名称
        $dept=new TeaModel();
        $deptList=$dept->distinct(true)->field('dept_name')->select();
        $view = new View();
        $tree=$this->treejosn();
        $view->assign("dept",$deptList);
        $view->assign("tree",$tree);
        return $view->fetch('datagrid');
    }

    /**
     * 通过ajax方式从服务器上获取JSON格式的数据给网格显示
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public  function ac1(){
        $tea_grid= new TeaDataGrid();
        return $tea_grid->dataGridJson();
    }

    /**
     * 处理修改或者保存有效性
     */
    public function save(){
        $form_data=$_POST['data'];
        $ret=array(
            'success'=>false,'message'=>'添加失败'
        );
        $operation=$_POST['operation'];
        if($operation=='add'){
            $new_record = new TeaModel();
            if( !$new_record->isExist($form_data['teach_id']) ){
                $new_record->data($form_data);
                $new_record->save();
                $ret=['success'=>true,'message'=>'添加成功'];
            }else{
                $ret=['success'=>false,'message'=>'该用户已存在，添加失败！'];
            }
        }else{
            $pk=$form_data['teach_id'];
            $new_record= new TeaModel();
            $new_record->save($form_data,['teach_id'=>$pk]);
            $ret=['success'=>true,'message'=>'修改成功'];
        }
        return json($ret);
    }
    /*  显示添加页面 */
    public function add(){
        $view = new View();
        $view->assign("operation",'新增');
        return $view->fetch('Addform');
    }
    /**
     * 删除
     */
    public function  remove(){
        $id=$_POST['id'];
        //var_dump($id);
        //echo implode(",",$id);
        $mo=new TeaModel();
        // delete from table where class_name in ('id1'，’id2‘)
        $count= $mo->where('teach_id','in',$id)->delete();
        if($count>0){
            $ret=['success'=>'true','message'=>'删除成功,共删除'.$count.'条记录'];
        }else{
            $ret=['success'=>'false','message'=>'删除失败！'];
        }
        return json($ret);
    }
    /**
     * 显示修改页面
     * @param $pk
     * @return string
     */
    public function update($pk){
        $view = new View();
        $mo=new TeaModel();
        $record=$mo->where('teach_id',$pk)->find();
        $dept=new DeptModel();
        $deptList=$dept->select();
        $view->assign("dept",  $deptList);
        $view->assign("operation",'编辑');
        $view->assign("record",  $record);
        return $view->fetch('Addform');
    }
    /**
     * 返回系部信息
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public  function deptinfo(){
        $dept=new DeptModel();
        $deptList=$dept->select();
        return json($deptList);
    }
    /*
     *返回树状图josn
     */
    public   function  treejosn(){
        $dept=new DeptModel();
        $de=$dept->field('dept_name')->select();
        $n=count($de);
        //dump($m);
        //系部
        $a="[{
         \"id\":0,
         \"state\":\"closed\",
        \"text\":\"全部系部\",
        \"checked\":\"true\",
        \"state\":\"open\",
         \"children\":[";
        for($i=0;$i<$n;$i++) {
            $deptList=$dept->field('dept_name')->where("dept_id",$i+1)->select();
                $a=$a."{
		      \"id\":".($i+1).",
		      \"text\":"."\"".$deptList[0]['dept_name']."\""."";
		       if($i+1==$n){
		          $a=$a."}";
		       }
		       else{
		           $a=$a."},";
               }
                }
            $a=$a."]}]";//]
       // print_r($a);
        return $a;
    }
    /**
     * 跳转打印a
     * @return \think\Response|\think\response\Json|\think\re
    /**sponse\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function  pt(){



    }
    /**
     * 下载EXCEL文件
     */
    public function download(){
        $request = Request::instance();
        if($request->method()=='POST'){
            if($request->post('action')=='export'){
                $dict_grid= new TeaDataGrid();
                $list=$dict_grid->getList();//
                $xlsName  = "教师信息表";
                $xlsCell  = array(
                    array('dept_name','部门名'),
                    array('teach_name','教师名'),
                    array('sex','性别'),
                    array('teach_id','教师编号'),
                    array('teach_phone','电话')
                );
                $dict_grid->exportExcel($xlsName,$xlsCell,$list);
            }
        }
    }
}