<?php
/**
 * Created by PhpStorm.
 * User: FuJinsong
 * Date: 2016/7/24
 * Time: 15:37
 */

namespace app\admin\controller;
use app\admin\CourseForm;
use app\common\model\AdjustmentModel;
use app\common\model\TeacherModel;
use app\common\model\TeaModel;
use think\View;
use app\common\model\ScheduleModel;
use app\common\model\DeptModel;
use app\common\model\CourseModel;
use think\Db;
use  \think\Controller;

class Course extends Controller
{

    /**
     * 用于显示Grid
     */
    public function index()
    {

        // 获取系部名称
        $dept = new DeptModel();
        $deptList = $dept->select();

        $view = new View();
        $view->assign("dept", $deptList);
        return $view->fetch('datagrid');
    }


    public function getTeacherById($teacher_id){
        $model_techer=new TeaModel();
        return  $model_techer->where('teach_id',$teacher_id)->find();
    }
    public function getScheduleById($teacher_id){
        $model_techer=new ScheduleModel();
        return  $model_techer->where('teacher_no',$teacher_id)->find();
    }
    public function getAdjustmentById($teacher_id){
        $model_techer=new AdjustmentModel();
        return  $model_techer->where('teach_id',$teacher_id)->find();
    }


    public function getListeners(){

        //（1）先从tbl_course 表中查所有记录的条数
        $model_course= new TeacherModel();

        // (2) 筛选条件
        $model_course->where( 'conuncilor','是');
        $total = intval($model_course->count());
        $model_course->where( 'conuncilor','是');
        // (2) 获取分页信息，设置分页条件
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        //$model_course->where($this->getWhere());//重新获取条件
        $start = ($page - 1) * $rows;
        $model_course->limit($start, $rows);
        // （3）对分页后的数据进行排序
        if(isset($_POST['sort']) &&  isset($_POST['order'])){
            $sort = $_POST['sort'] ;
            $order = $_POST['order'];
            $model_course->order($sort,$order);
        }
        // (4) 根据分页条件，排序条件进行数据查询
        $course_list=$model_course->select();

        // （6）将数据进行JSON编码
        return json(['total' => $total, 'rows' => $course_list]);
    }
    /**
     * 通过ajax方式从服务器上获取JSON格式的数据给网格显示
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function ac1()
    {

          //（1）先从tbl_course 表中查所有记录的条数
         $model_course= new CourseModel();
         $total = intval($model_course->count());

        // (2) 获取分页信息，设置分页条件
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        //$model_course->where($this->getWhere());//重新获取条件
        $start = ($page - 1) * $rows;
        $model_course->limit($start, $rows);
        // （3）对分页后的数据进行排序
        if(isset($_POST['sort']) &&  isset($_POST['order'])){
            $sort = $_POST['sort'] ;
            $order = $_POST['order'];
            $model_course->order($sort,$order);
        }
        // (4) 根据分页条件，排序条件进行数据查询
        $course_list=$model_course->select();

        // （5） 对返回的记录进行循环,对数据进行处理放入一个数组中
             $ret_array=array();//需要返回的数组
             foreach($course_list as $course){
                 $techer_id=$course['teach_id'];
                 $schedule_id=$course['teach_id'];
                 $adjustment_id=$course['teach_id'];

                 //根据教师编号从教师表中获取该教教师的是否免听课信息
                 $techer=$this->getTeacherById($techer_id);
                 $ispassed=$techer['passed'];
                 $course['passed']=isset($ispassed)?$ispassed:'';
                //根据教师编号从听课表中获取该教教师的听课状态信息
                 $schedule=$this->getScheduleById($schedule_id);
                 $state_information=$schedule['state'];
                 $conuncilor_information=$schedule['conuncilor'];
                 $course['state']=$state_information;
                 $course['teach_dd']=$conuncilor_information;
                 //根据教师编号从调课表中获取该教师的调停课信息
                 $adjustment=$this->getAdjustmentById($adjustment_id);
                 $adjustment_information[]=$adjustment['week'];
                 $adjustment_information[]=$adjustment['xing_qi_ji'];
                 $adjustment_information[]=$adjustment['section'];
                 //将数组转化为字符串
                 $course['adj_exchange']= implode('-',$adjustment_information);
                 $ret_array[]=$course;
                 unset($adjustment_information);
          }
        // （6）将数据进行JSON编码
        return json(['total' => $total, 'rows' => $ret_array]);


    }
    public function ac2()
    {
        $view = new View();
        return $view->fetch('listen');
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

    /**
     * 接受数据， 添加听课人
     */
    public function addlisteners(){
        $model_course= new CourseModel();
        $model_schedule=new ScheduleModel();
        //(1) 获取POST数据
        $course_id = $_POST['courseid'];
        $teachers = $_POST['teachers'];
        //（2）根据编号从Courses表中获取详细的课表信息
        $listen_teach_information=$model_course->where('c_id',$course_id)->find();

        // (3) 向tbl_schedule中添加数据
        $techer_id=$listen_teach_information['teach_id'];
        $model_schedule->where("teacher_no",$techer_id)->setField('conuncilor',implode('|',$teachers));
    }

}