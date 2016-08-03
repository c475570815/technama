<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/21
 * Time: 9:42
 */

namespace app\cyh\controller;
use think\Controller;
use app\common\model\TeaModel;
use app\cyh\TeaDataGrid;
use think\View;
use think\Db;
class Tea extends Controller
{
    /**
     * 用于显示Grid
     */
    public function index(){
        // 获取系部名称
        $dept=new TeaModel();
        $deptList=$dept->distinct(true)->field('dept_name')->select();
        $view = new View();
        $view->assign("dept",$deptList);
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
    public function save(){

        $form_data=$_POST['data'];
        $ret=array(
            'success'=>false,'message'=>'添加失败'
        );
        $new_record = new TeaModel();
        if( !$new_record->isExist($form_data['teach_id']) ){
            $new_record->data($form_data);
            $new_record->save();
            $ret=['success'=>true,'message'=>'添加成功'];
        }else{
            $ret=['success'=>false,'message'=>'该用户已存在，添加失败！'];
        }

        return json($ret);
    }
    /*  显示添加页面 */
    public function add(){
        $view = new View();
        return $view->fetch('Addform');
    }
    /**
     * 做删除处理
     */
    /*
    public function  abandon(){

        $form_data=$_POST['data'];
        $ret=array(
            'success'=>false,'message'=>'删除失败'
        );
        $new_record = new TeaModel();
        if( $new_record->isExist($form_data['tech_id']) ){
            $new_record::destroy($form_data['tech_id']);
            $ret=['success'=>true,'message'=>'删除成功'];
        }else{
            $ret=['success'=>false,'message'=>'用户不存在，删除失败！'];
        }
        return json($ret);
    }
    */
    /**
     *调用删除视图
     */
    /*
    public function  del(){
        $view=new View();
        return $view->fetch('Delform');
    }*/
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
}