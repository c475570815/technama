<?php
/**
 * Created by PhpStorm.
 * User: FuJinsong
 * Date: 2016/7/24
 * Time: 15:37
 */

namespace app\admin\controller;

use app\admin\AdjustmentDataGrid;
use app\admin\CourseForm;
use app\common\model\AdjustmentModel;
use app\common\model\RecordModel;
use app\common\model\TeacherModel;
use app\common\model\TeaModel;
use app\common\model\WeekcourseModel;
use think\View;
use app\common\model\ScheduleModel;
use app\common\model\DeptModel;
use app\common\model\CourseModel;
use think\Db;
use  \think\Controller;
use app\common\model\ConfigModel;
use app\common\model\TermModel;
use app\common\TermCalendar;

/**
 * 周课表控制器类
 * Class Course
 * @package app\admin\controller
 */
class WeekCourse extends Controller
{

   public function test(){
      if( $this->hasLesson('030523', 12, 1, 5)){
          echo "有课";
      }else{
          echo "无可";
      }
   }
    /**
     * 用于显示Grid
     */
    public function index()
    {
        // 获取系部名称
        $dept = new DeptModel();
        $deptList = $dept->select();
        // 当前学期
        $model = new ConfigModel();
        $row = $model->where('cfg_name', 'current_term')->find();
//        $term=new TermModel();
//        $current_term=$term->where("default",1)->find();
//        return  $current_term['term_name'];
        // 视图
        $view = new View();
        $view->assign("dept", $deptList);
        $view->assign("current_term", $row['cfg_term']);
        return $view->fetch('datagrid');
    }


    /**
     * 根据课程表，产生指定周的周课表()
     * @param $week  当前周
     */
    public function init($week)
    {
        // 获取当前学期
        $term = new TermModel();
        $current_term = $term->where("default", 1)->find();
        // 获取当前学期的所有课表
        $course_model = new CourseModel();
        $courses = $course_model->where("term", $current_term['term_name'])->select();

        // 将该周有课的课表查找出来
        $arr_weekcouse = array();
        foreach ($courses as $course) {
            if (($week % 2 == 0 && $course['single_double'] == '单') || ($week % 2 == 1 && $course['single_double'] == '双')) {
                // 单双周不同
            } else {
                // 如果week在上课周内
                if (in_array($week, getWeeksByString($course['week']))) {
                    $weekcouse = array();
                    $weekcouse['term'] = $course['term'];
                    $weekcouse['teach_id'] = $course['teach_id'];
                    $weekcouse['teach_name'] = $course['teach_name'];
                    $weekcouse['class_room'] = $course['class_room'];
                    $weekcouse['class_name'] = $course['class_name'];
                    $weekcouse['course_name'] = $course['course_name'];
                    $weekcouse['single_double'] = $course['single_double'];
                    $weekcouse['week'] = $week;
                    $weekcouse['week_info'] = $course['week'];
                    $weekcouse['xing_qi_ji'] = $course['xing_qi_ji'];
                    $weekcouse['section'] = $course['section'];
                    $weekcouse['comment'] = $course['comment'];
                    $weekcouse['dept_name'] = $course['dept_name'];
                    $weekcouse['free'] = '否'; //是否免听
                    // 是否有条听课
                    if ($this->onduty($course['teach_id'], $course['course_name'], $course['class_name'], $week, $course['xing_qi_ji'], $course['section']) == true && $this->isfinished($course['teach_id'], $course['course_name'], $course['class_name'], $week, $course['xing_qi_ji'], $course['section']) == false) {
                        $weekcouse['onduty'] = '是'; //没有调课，课没有结束
                    } else {
                        $weekcouse['onduty'] = '否';
                    }
                    //已被听课次数
                    $weekcouse['check_times'] = $this->checktimes($course['teach_id'], $course['course_name'], $course['class_name'], $course['week'], $course['xing_qi_ji'], $course['section']);
                    // 已安排的听课情况
                    $planed_listener_count = $this->isPlaned($course['teach_id'], $course['week'], $course['xing_qi_ji'], $course['section']);
                    $weekcouse['plan_times'] = $planed_listener_count;
                    // 安排状态
                    $weekcouse['status'] = $planed_listener_count > 0 ? "已安排(" . $planed_listener_count . "人听课)" : "未安排";
                    $arr_weekcouse[] = $weekcouse;
                }
            }

        }
        //清除原有数据
        Db::execute("TRUNCATE tbl_course_week");
        $mo = new WeekcourseModel();
        // $mo->where('week', $week)->delete();
        if ($arr_weekcouse) {
            $mo->saveAll($arr_weekcouse);
        }

    }

    /**
     * 返回查询出来的课程表
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function getlist()
    {

        //（1）先从tbl_course 表中查所有记录的条数
        $model_course = new WeekcourseModel();
        // (2) 筛选条件
        $arr_where = array();
        if (isset($_POST['dict'])) {
            $dict = $_POST['dict'];
            $current_week = $dict['week'];
            $current_term = $dict['term'];
            $arr_where = $model_course->filer($dict); //产生某一周的筛选条件
            $this->init($current_week);// 获取周课表
        }
//        var_dump($arr_where);
        $model_course->where($arr_where);
        //统计记录数
        $total = intval($model_course->count());
        $model_course->where($arr_where);
        // (2) 获取分页信息，设置分页条件
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $start = ($page - 1) * $rows;
        $model_course->limit($start, $rows);
        // （3）对分页后的数据进行排序
        if (isset($_POST['sort']) && isset($_POST['order'])) {
            $sort = $_POST['sort'];
            $order = $_POST['order'];
            $model_course->order($sort, $order);
        }
        // (4) 根据分页条件，排序条件进行数据查询
        $course_list = $model_course->select();
        // （5）对数据进行处理
        $new_course_list=array();
        foreach ($course_list as $course){
                // 是否有条件听课（1）没有调课（2）课程没有结束

                    if ($this->onduty($course['teach_id'], $course['course_name'], $course['class_name'], $course['week'], $course['xing_qi_ji'], $course['section']) == true && $this->isfinished($course['teach_id'], $course['course_name'], $course['class_name'], $course['week'], $course['xing_qi_ji'], $course['section']) == false) {
                        $course['onduty'] = '是'; //没有调课，课没有结束
                    } else {
                        $course['onduty'] = '否';
                    }

            $course['check_times'] = $this->checktimes($course['teach_id'], $course['course_name'], $course['class_name'], $course['week'], $course['xing_qi_ji'], $course['section']);
            // 已安排的听课情况
            $planed_listener_count = $this->isPlaned($course['teach_id'], $course['week'], $course['xing_qi_ji'], $course['section']);
            $course['plan_times'] = $planed_listener_count;
            // 安排状态
            $course['status'] = $planed_listener_count > 0 ? "已安排(" . $planed_listener_count . "人听课)" : "未安排";
            $new_course_list[]=$course;
        }
        // （6）将数据进行JSON编码
        return json(['total' => $total, 'rows' => $new_course_list]);
    }

    /**
     * 教师在指定周、日、节的听课状态
     * @param $teacherid
     * @param $course
     * @param $clazz
     * @param $week
     * @param $weekday
     * @param $section
     */
    public function listenStatus($teacherid, $course, $clazz, $week, $weekday, $section)
    {
        if ($this->checktimes($teacherid, $course, $clazz, $week, $weekday, $section) > 0) {
            $ret = '已听';
        } elseif ($this->isPlaned($teacherid, $course, $clazz, $week, $weekday, $section) > 0) {
            $ret = '准备听';
        } else {
            $ret = '未听';
        }
        $ret = '未听';
        return $ret;
    }


    /**
     * 教师在本学期已被听课的次数
     * @param $teacherid
     * @param $course
     * @param $clazz
     * @param $week
     * @param $weekday
     * @param $section
     * @return int
     */
    public function checktimes($teacherid)
    {
        $model = new RecordModel();
        $map = array();
        $map['teacher_id'] = $teacherid;
        return $model->where($map)->count();
    }

    /**
     * 听课人本学期的听课次数
     * @param $listener_no
     * @return int
     */
    public function checkedTimes($listener_no)
    {
        $model = new RecordModel();
        $map = array();
        $map['listener_no'] = $listener_no;
        return $model->where($map)->count();
    }
    /**
     * 督导在该时间段是否有课
     * @param $teacherid  教师职工号
     * @param $week
     * @param $weekday
     * @param $section
     * @return int
     */
    public function hasScheduled($teacher_no, $week, $weekday, $section)
    {
        $model = new ScheduleModel();
        $map = array();
        $map['conuncilor'] = $teacher_no;
        $map['week'] = $week;
        $map['xing_qi_ji'] = $weekday;
        $map['section'] = $section;
        $count = $model->where($map)->count();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 查看教师课表，判断教师是否有课(注意单双周)
     * @param $teacherid  教师职工号
     * @param $week  周次
     * @param $weekday  星期
     * @param $section  节次
     * @return int
     */
    public function hasLesson($teacher_no, $week, $weekday, $section)
    {
        //查找教师，星期，节次对应的课表
        $map = array();
        $map['teach_id'] = $teacher_no;
        $map['xing_qi_ji'] = $weekday;
        $map['section'] = $section;
        $teacher_course=CourseModel::where($map)->select();
//        var_dump($teacher_course);
        $ret=false;
        //创建学期类，获取当前周
//        $current_term=TermModel::where("default",1)->find();
//        $cal= new TermCalendar($current_term['start'],$current_term['end']);
//        $current_week=$cal->get_week(date("Y/m/d"));
        //是否有课（单双周一样，且周次在指定范围内）
        foreach ($teacher_course as $course){
            $string_week=$course['week'];
            $week_array=getWeeksByString($string_week); //将周次字符串如1-2，4-9 转成数组
            $single_double=$course['single_double'];  //获取单双周
//            var_dump($single_double);
//            var_dump(( $week%2==0  || $single_double=='双'  ));
            if( ( $week%2==0  && $single_double=='双'  ) || ( $week%2==1  && $single_double=='单'  ) || $single_double=='2'){
                if(in_array($week,$week_array)){
                    $ret= true;
                }
            }
        }
        return $ret;

/*
        $model = new RecordModel();
//        $model= new

        $map = array();
        $map['teacher_id'] = $teacher_no;
        $map['week'] = $week;
        $map['xing_qi_ji'] = $weekday;
        $map['section'] = $section;
        $count = $model->where($map)->count();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }*/
    }

    /**
     * 返回已有听课计划数量
     * @param $teacherid  教师编号
     * @param $course
     * @param $clazz
     * @param $week
     * @param $weekday
     * @param $section
     * @return int
     */
    public function isPlaned($teacherid, $week, $weekday, $section)
    {
        $model = new ScheduleModel();
        $map = array();
        $map['teach_id'] = $teacherid;
        /*  $map['course_name'] = $course;
          $map['class_name'] = $clazz;*/
        $map['week'] = $week;
        $map['xing_qi_ji'] = $weekday;
        $map['section'] = $section;
        return $model->where($map)->count();
    }

    /**
     * 判断教师在指定的时间，指定班级，指定课程，是否有调课
     * @param $teacherid  教师工号
     * @param $course  课程名
     * @param $clazz 班级名
     * @param $week  周
     * @param $weekday  星期
     * @param $section  节次
     * @return bool
     */
    public function onduty($teacherid, $course, $clazz, $week, $weekday, $section)
    {
        $model = new AdjustmentModel();//调课表
        $map = array();
        $map['teach_id'] = $teacherid;
        $map['course_name'] = $course;
        $map['class_name'] = $clazz;
        $map['week'] = $week;
        $map['xing_qi_ji'] = $weekday;
        $map['section'] = $section;
        $map['reason'] = array("<>", "课程结束");
        $row = $model->where($map)->count();
        // return AdjustmentModel::getLastSql();
        if ($row>=1) {
            return false;   //有调课，不在岗
        } else {
            return true;   // 在岗
        }
    }


    /**
     * 该教师某课程某班级是否有停课
     * @param $teacherid
     * @param $course
     * @param $clazz
     * @param $week  当前周
     * @param $weekday
     * @param $section
     * @return bool
     */
    public function isfinished($teacherid, $course, $clazz, $week, $weekday, $section)
    {
        $map = array();
        $map['teach_id'] = $teacherid;
        $map['course_name'] = $course;
        $map['class_name'] = $clazz;
        $map['reason'] = array("=", "课程结束");
        $row = Db::table('tbl_adjustment')->field("week,xing_qi_ji,section")->where($map)->find();

        if ($row) {
            if ($row['week'] < $week) {
                $finished = true;
            } elseif ($row['week'] > $week) {
                $finished = false;
            } else {
                if ($row['xing_qi_ji'] < $weekday) {
                    $finished = true;
                } elseif ($row['xing_qi_ji'] > $weekday) {
                    $finished = false;
                } else {
                    if ($row['section'] < $section) {
                        $finished = true;
                    } elseif ($row['section'] > $section) {
                        $finished = false;
                    } else {
                        $finished = true;
                    }
                }
            }

        } else {
            $finished = false;
        }
        return $finished;
    }


    /**
     *   根据id获取教师
     * @param $teacher_id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getTeacherById($teacher_id)
    {
        $model_techer = new TeaModel();
        return $model_techer->where('teach_id', $teacher_id)->find();
    }

    /**
     * 根据教师编号，获取该教师对应的排课记录
     * @param $teacher_id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getScheduleById($teacher_id)
    {
        $model_techer = new ScheduleModel();
        return $model_techer->where('teach_id', $teacher_id)->find();
    }

    public function getAdjustmentById($teacher_id)
    {
        $model_techer = new AdjustmentModel();
        return $model_techer->where('teach_id', $teacher_id)->find();
    }


    /**
     * 获取听课人列表（条件1.是督导，条件2.没课）
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function getListeners()
    {
        //（1）先从tbl_course 表中查所有记录的条数
        $model_course = new TeacherModel();
        // (2) 筛选条件
        if (isset($_POST['week'])) {
            $week = $_POST['week'];
            $weekday = $_POST['xing_qi_ji'];
            $section = $_POST['section'];
        }
        if (isset($_POST['teacher_id'])) {
            $teacher_id = $_POST['teacher_id'];//被听课教师
        }
        $model_course->where('conuncilor', '是');

        $total = intval($model_course->count());
        $model_course->where('conuncilor', '是');
        // (2) 获取分页信息，设置分页条件
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        //$model_course->where($this->getWhere());//重新获取条件
        $start = ($page - 1) * $rows;
        $model_course->limit($start, $rows);
        // （3）对分页后的数据进行排序
        if (isset($_POST['sort']) && isset($_POST['order'])) {
            $sort = $_POST['sort'];
            $order = $_POST['order'];
            $model_course->order($sort, $order);
        }
        // (4) 根据分页条件，排序条件进行数据查询
        $list = $model_course->select();
        // (5) 对督导数据进行处理,返回听课次数
        $arr = array();

        foreach ($list as $row) {
            $current = array();
            $current['teach_id'] = $row['teach_id'];
            $current['dept_name'] = $row['dept_name'];
            $current['dept_name'] = $row['dept_name'];
            $current['teach_name'] = $row['teach_name'];
            $current['has_lesson'] = $this->hasLesson($row['teach_id'], $week, $weekday, $section) ? '是' : '否';//是否有课
            //该时间段是否有听课任务
            $has_scheduled = $this->hasScheduled($row['teach_id'], $week, $weekday, $section) ;
            //督导曾经听过该教师的课
            $has_listened_once = $this->hasListened($teacher_id, $row['teach_id']);
            $current['has_listened'] = $this->hasListened($teacher_id, $row['teach_id']) ? '是' : '否';//督导是否安排有听课
            $current['checked_times'] = $this->checkedTimes($row['teach_id']);
            //没有
            if ($has_listened_once == false &&  $has_scheduled==false) {
                $arr[] = $current;
            }
        }
        // （6）将数据进行JSON编码
        return json(['total' => $total, 'rows' => $arr]);
    }

    /**
     * 判断老师是否被一个督导听过
     * @param $teacher
     * @param $listener
     * @return bool
     */
    private function hasListened($teacher, $listener)
    {
        $model = new RecordModel();
        $map = array();
        $map['teacher_id'] = $teacher;
        $map['listener_no'] = $listener;
        $count = $model->where($map)->count();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 返回督导已安排的听课次数
     * @param $teacher_no
     * @param int $finished
     * @return int
     */
    public function getListenTime($teacher_no, $finished = 0)
    {
        return 0;
    }


    /**
     * 接受数据， 添加听课人
     */
    public function addlisteners()
    {

        //(1) 获取课程信息的数组
        if (isset($_POST['lesson'])) {
            $lesson = $_POST['lesson'];
        }
        //（2）获取听课教师编号
        if(isset($_POST['teachers'])){
            $teachers = $_POST['teachers'];
        }
        //（2）根据编号从Courses表中获取详细的课表信息
//        $listen_teach_information = $model_course->where('c_id', $course_id)->find();

        // (3) 如果没有找到，则向tbl_schedule中添加数据；否则为修改
//        $model_schedule = new ScheduleModel();
//        if($this->isPlaned($lesson['teach_id'],$lesson['week'],$lesson['xing_qi_ji'],$lesson['section'])){
//            $map = array();
//            $map['teach_id'] = $lesson['teach_id'];
//            $map['week'] = $lesson['week'];
//            $map['xing_qi_ji'] = $lesson['xing_qi_ji'];
//            $map['section'] = $lesson['section'];
//            $result= $model_schedule->where($map)->delete();
//            //$result=  $model_schedule->delete();
//        }else{
//
//        }
//        $model_schedule->term=$lesson['term'];
//        $model_schedule->dept_name=$lesson['dept_name'];
//        $model_schedule->teach_id=$lesson['teach_id'];
//        $model_schedule->teach_name=$lesson['teach_name'];
//        $model_schedule->week=$lesson['week'];
//        $model_schedule->xing_qi_ji=$lesson['xing_qi_ji'];
//        $model_schedule->section=$lesson['section'];
//        $model_schedule->class_name=$lesson['class_name'];
//        $model_schedule->class_room=$lesson['class_room'];
//        $model_schedule->course_name=$lesson['course_name'];
//        $model_schedule->conuncilor=implode('|', $teachers);
//        $result=$model_schedule->save();
        //(3) 为每个督导添加一条听课计划记录
        foreach ($teachers as $listener) {
            $model_schedule = new ScheduleModel();
            $map = array();
            $map['teach_id'] = $lesson['teach_id'];
            $map['week'] = $lesson['week'];
            $map['xing_qi_ji'] = $lesson['xing_qi_ji'];
            $map['section'] = $lesson['section'];
            $map['conuncilor'] = $listener;;
            $model_schedule->where($map)->delete();//删除原来的记录
            $model_schedule->term = $lesson['term'];
            $model_schedule->dept_name = $lesson['dept_name'];
            $model_schedule->teach_id = $lesson['teach_id'];
            $model_schedule->teach_name = $lesson['teach_name'];
            $model_schedule->week = $lesson['week'];
            $model_schedule->xing_qi_ji = $lesson['xing_qi_ji'];
            $model_schedule->section = $lesson['section'];
            $model_schedule->class_name = $lesson['class_name'];
            $model_schedule->class_room = $lesson['class_room'];
            $model_schedule->course_name = $lesson['course_name'];
            $model_schedule->conuncilor = $listener;
            // 时间
            $term = new TermModel();
            $current_term = $term->where("default", 1)->find();
            $tc = new TermCalendar($current_term['start'], $current_term['end']);
            $model_schedule->time = $tc->getDate($lesson['week'], $lesson['xing_qi_ji'])->format('Y-m-d'); // 获取时间
            $result = $model_schedule->save();
        }
        if ($result) {
            $ret = ['success' => 'true', 'message' => '安排成功！'];
        } else {
            $ret = ['success' => 'false', 'message' => '安排失败！'];
        }
        return json($ret);
    }

    /**
     * 清除所有记录
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function removeall()
    {
        $result = Db::execute("TRUNCATE tbl_course_week;");
        if ($result) {
            $ret = ['success' => 'true', 'message' => '清除成功！'];
        } else {
            $ret = ['success' => 'false', 'message' => '清除失败！'];
        }

        return json($ret);
    }

}