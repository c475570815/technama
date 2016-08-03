<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/21
 * Time: 17:17
 */

namespace app\mcw\controller;
use app\common\model\ClassesModel;
use think\View;
use app\common\model\CourseModel;
use app\common\model\DeptModel;
use  \think\Controller;

class Add extends Controller
{
 public function  add(){
     $view=new View();
     return $view->fetch();
 }
 public function  get_classes(){
     $classes=new CourseModel();
     $list=$classes->select();
     return $list;
 }
 public function save(){
     $form_data=$_POST['data'];
     $ret=array(
         'success'=>false,'message'=>'添加失败'
     );
     $CM=new CourseModel();
     if(!$CM->get(['course_name'=>$form_data['course_name']]))
     {
        $CM->data($form_data);
        $CM->save();
        $ret=['success'=>true,'message'=>'添加成功'];
     }
     else
     {
         $ret=['success'=>false,'message'=>'该用户已存在，添加失败！'];
     }
     return json($ret);
   }
 }
?>