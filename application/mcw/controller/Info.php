<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 10:37
 */

namespace app\mcw\controller;
use app\common\model\TeacherModel;
use think\View;
use think\Db;

class Info
{
 public function meninfo(){
     $TeachMod=new TeacherModel();
     $list=Db::table('tbl_teacher')
            ->where('teach_id','010001')
            ->select();
    $view=new  View();
     $view->assign('data',$list);
     return $view->fetch();
 }
}