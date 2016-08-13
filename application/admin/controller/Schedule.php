<?php
/**
 * Created by PhpStorm.
 * User: FuJinsong
 * Date: 2016/7/21
 * Time: 20:19
 */
namespace app\admin\controller;
use app\admin\ScheduleDataGrid;
use app\common\model\TeacherModel;
use think\View;
use app\common\model\ScheduleModel;
use app\common\model\ConfigModel;
use app\common\model\DeptModel;
use app\admin\DictDataGrid;
use think\Db;
use  \think\Controller;

/*
 定义Controller类，一个类中有多个方法（Action）
 (1)类名应与文件名一致，且首字母大写
 (2)方法名应小写
*/

class Schedule extends Controller
{

    /**
     * 用于显示Grid
     */
    public function index()
    {

        // 获取系部名称
        $dept = new DeptModel();
        $deptList = $dept->select();

        $model=new ConfigModel();
        $row= $model->where('cfg_name','current_term')->find();

        $view = new View();
        $view->assign("dept", $deptList);
        $view->assign("current_term", $row['cfg_term']);
        return $view->fetch('datagrid');
    }
    public function cal()
    {

        // 获取系部名称
        $dept = new DeptModel();
        $deptList = $dept->select();

        $view = new View();
        $view->assign("dept", $deptList);
        return $view->fetch('default');
    }
    /**
     * 通过ajax方式从服务器上获取JSON格式的数据给网格显示
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function getlist()
    {
//        $dict_grid = new ScheduleDataGrid();
//        return $dict_grid->dataGridJson();
        $current_table=new ScheduleModel();
        $arr_where=array();
        if (isset($_POST['dict'])) {
            $dict = $_POST['dict'];
            $arr_where=$current_table->filer($dict);
            $current_table->where($arr_where);
        }
        //先获取筛选后记录的总数
        $total = intval($current_table->count());
        //重新获取条件
        $current_table->where($arr_where);
        //获取客户端传递过来的参数 page=2&rows=20
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $start = ($page - 1) * $rows;
        $current_table->limit($start, $rows);
        // 排序
        if(isset($_POST['sort']) &&  isset($_POST['order'])){
            $sort = $_POST['sort'] ;
            $order = $_POST['order'];
            $current_table->order($sort,$order);
        }
        // 获取数组
        $list = $current_table->select();
        $list_hand=array();
        foreach ($list as $row){
            $conuncilor=$row['conuncilor'];
            $arr=explode('|',$conuncilor) ;
            $teacher_name=array();
            foreach ($arr as $teacher_no){
               $teacher= TeacherModel::get(['teach_id' =>$teacher_no]);
              // $teacher_name[]=$teacher_no;
               $teacher_name[]=$teacher['teach_name'];
            }
            $row['conuncilor']=implode(',',$teacher_name);
            $list_hand[]=$row;
        }
        // 返回JSON
        return json(['total' => $total, 'rows' => $list_hand]);

    }

    /**
     * 返回系部信息
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function deptinfo()
    {
        $dept = new DeptModel();
        $deptList = $dept->select();
        return json($deptList);
    }

    public function tip(){
        echo "ddsdsdsdds";
    }
    /* 保存数据 */
    public function save()
    {
        $new_record = new ScheduleModel();
        $new_record->data([
            'name' => 'thinkphp',
            'email' => 'thinkphp@qq.com'
        ]);
        $new_record->save();
        return "";
    }

    /*  添加页面 */
    public function add()
    {
        $view = new View();
        //$view->assign("dict_category",  $dict_category);
        return $view->fetch('form');
    }

    public function teacher_cg()
    {
        $q = isset($_POST['q']) ? $_POST['q'] : '';  // the request parameter
        $mo=new TeacherModel();
        $list=$mo->where('teach_name','like',"%$q%")->select();
        return  json($list);
    }

    public function email(){
        $current_table=new ScheduleModel();
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $list = $current_table->where("id","in",$id)->select();
            $list_hand=array();
            foreach ($list as $row){
                $conuncilor=$row['conuncilor'];
                $arr=explode('|',$conuncilor) ;
                $teacher_mail=array();
                foreach ($arr as $teacher_no){
                    $teacher= TeacherModel::get(['teach_id' =>$teacher_no]);
                    if($teacher['email_validated']){
                        $teacher_mail[]=$teacher['email'];
                    }
                }
                // 发送email
                $subject="听课安排";
                $body="老师，以下听课安排：".$row['teacher'].$row['week'].$row['xing_qi_ji'].$row['section'].$row['class_room'].$row['course_name']."";
                sendmail($teacher_mail,$subject,$body);
               // $row['conuncilor']=implode(',',$teacher_name);
               // $list_hand[]=$row;
            }
        }
    }

    public function sms(){

        $current_table=new ScheduleModel();
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $list = $current_table->where("id","in",$id)->select();
            $list_hand=array();
            foreach ($list as $row){
                $conuncilor=$row['conuncilor'];
                $arr=explode('|',$conuncilor) ;
                $teacher_mail=array();
                foreach ($arr as $teacher_no){
                    $teacher= TeacherModel::get(['teach_id' =>$teacher_no]);
                    if($teacher['email_validated']){
                        $teacher_mail[]=$teacher['email'];
                    }
                }
                // $row['conuncilor']=implode(',',$teacher_name);
                // $list_hand[]=$row;
            }
            yuntongxun_sms('18942891954',['aaa','bbb'],1);
        }
    }

}

?>