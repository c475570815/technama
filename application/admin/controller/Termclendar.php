<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29
 * Time: 11:58
 */
namespace app\admin\controller;

use think\Controller;
use think\View;
use app\common\TermCalendar;
use app\common\model\ConfigModel;

class Termclendar extends Controller
{
    public function index()
    {
        $view = new View();
        return $view->fetch('calendar');
    }

    /**调用week视图
     * @return stringt
     */
    public function week()
    {
        $view = new View();
        $view->assign('now',date("Y-m-d"));//把现在的时间传到视图
        return $view->fetch('week');
    }
    /**
     * 通过给出的开始结束时间参数 调用类方法构造出表格对应的josn格式(所有日期)
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function get_cal_josn()
    {
        //$view = new View();
        //$view->assign('now',date("y-m-d"));//把现在的时间传到视图
        $config=new ConfigModel();
        $start=$config->where('id',1)->column('cfg_term');
        $s=(Array)json_decode($start[0]);
        $start=$s['start'];
        $end=$s['end'];
        $a=new TermCalendar($start,$end);
        return  $a->objct_tojosn();
    }
    /**通过给出的开始结束时间参数 调用类方法构造出表格对应的josn格式(一周日期)
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function get_week_josn(){
        $a=new TermCalendar("2015-9-10","2016-1-20");
        return  $a->get_weekjosn($a->get_week(date("15-10-10")));
    }

    /*返回到页面
     * @return string
     */
    public function  vacation(){
        $view = new View();
        return $view->fetch('add_vacation');
    }

    /**
     * 保存学期参数
     */
    public function  add_vacation()
    {
            $form_start=$_POST['start'];
            $form_end=$_POST['end'];
            $cfg_term= $this->add_josn( $form_start,  $form_end);
            $a=['cfg_term' => $cfg_term];
            $new_record = new ConfigModel();
            $new_record->insert($a);
            $ret=['success'=>true,'message'=>'添加成功'];
            return json($ret);
    }
    public function  add_josn($start,$end){
        return "{\"start\":\"$start\",\"end\":\"$end\" }";
    }
}