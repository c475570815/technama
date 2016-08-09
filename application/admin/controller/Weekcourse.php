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

/**
 * 周课表控制器类
 * Class Course
 * @package app\admin\controller
 */
class WeekCourse extends Controller
{
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
        // 视图
        $view = new View();
        $view->assign("dept", $deptList);
        $view->assign("current_term", $row['cfg_term']);
        return $view->fetch('datagrid');
    }


    /**
     * 根据周次，产生周课表
     * @param $week
     */
    public function init($week)
    {
        // 获取所有课表
        $course_model = new CourseModel();
        $courses = $course_model->select();
        foreach ($courses as $course) {
            if (($week % 2 == 0 && $course['single_double'] == '单') || ($week % 2 == 1 && $course['single_double'] == '双')) {
                // 单双周不同
            } else {
                // 周数
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
                    if ($this->onduty($course['teach_id'], $course['course_name'], $course['class_name'], $week, $course['xing_qi_ji'], $course['section']) == true && $this->isfinished($course['teach_id'], $course['course_name'], $course['class_name'], $week, $course['xing_qi_ji'], $course['section']) == false) {
                        $weekcouse['onduty'] = '是'; //没有调课，课没有结束
                    } else {
                        $weekcouse['onduty'] = '否';
                    }
//                    $weekcouse['onduty'] =$this->onduty($course['teach_id'], $course['course_name'], $course['class_name'],$week , $course['xing_qi_ji'], $course['section']);
                    $weekcouse['check_times'] = $this->checktimes($course['teach_id'], $course['course_name'], $course['class_name'], $course['week'], $course['xing_qi_ji'], $course['section']);//已被听课次数
                    $weekcouse['status'] = $this->isPlaned($course['teach_id'], $course['course_name'], $course['class_name'], $course['week'], $course['xing_qi_ji'], $course['section'])>0?"已安排":"未安排";// 听课情况
                    $arr_weekcouse[] = $weekcouse;
                }
            }

        }
        Db::execute("TRUNCATE tbl_course_week");
        $mo = new WeekcourseModel();
       // $mo->where('week', $week)->delete();
        $mo->saveAll($arr_weekcouse);
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
     * 教师在本学期已被听课
     * @param $teacherid
     * @param $week
     * @param $weekday
     * @param $section
     */
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
    public function checktimes($teacherid, $course, $clazz, $week, $weekday, $section)
    {
        $model = new RecordModel();
        $map = array();
        $map['teacher_id'] = $teacherid;
//        $map['course_name'] = $course;
//        $map['class_name'] = $clazz;
//        $map['week'] = $week;
//        $map['xing_qi_ji'] = $weekday;
//        $map['section'] = $section;
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
     * 判断教师是否有课
     * @param $teacherid  教师职工号
     * @param $week
     * @param $weekday
     * @param $section
     * @return int
     */
    public function hasLesson($teacher_no, $week, $weekday, $section)
    {
        $model = new RecordModel();
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
        }
    }

    /**
     * 返回已有听课计划数量
     * @param $teacherid
     * @param $course
     * @param $clazz
     * @param $week
     * @param $weekday
     * @param $section
     * @return int
     */
    public function isPlaned($teacherid, $course, $clazz, $week, $weekday, $section)
    {
        $model = new ScheduleModel();
        $map = array();
        $map['teacher_no'] = $teacherid;
/*        $map['course_name'] = $course;
        $map['class_name'] = $clazz;
        $map['week'] = $week;
        $map['xing_qi_ji'] = $weekday;
        $map['section'] = $section;*/
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
        $row = $model->where($map)->find();

       // return AdjustmentModel::getLastSql();
        if ($row) {
            return false;//有调课，不在岗
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
        $model = new AdjustmentModel();
        $map = array();
        $map['teach_id'] = $teacherid;
        $map['course_name'] = $course;
        $map['class_name'] = $clazz;
        $map['reason'] = array("=", "课程结束");
        $row = $model->where($map)->find();

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
        return $model_techer->where('teacher_no', $teacher_id)->find();
    }

    public function getAdjustmentById($teacher_id)
    {
        $model_techer = new AdjustmentModel();
        return $model_techer->where('teach_id', $teacher_id)->find();
    }

    /**
     * 返回本周的课程表
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function getlist()
    {
        //（1）先从tbl_course 表中查所有记录的条数
        $model_course = new WeekcourseModel();

        // (2) 筛选条件
        $arr_where=array();
        if (isset($_POST['dict'])) {
            $dict= $_POST['dict'];
            $current_week = $dict['week'];
            $arr_where=$model_course->filer($dict);
            //$result=Db::execute("TRUNCATE tbl_course_week");
           // $model_course->where( $arr_where);
            $this->init($current_week);
        }
        $model_course->where( $arr_where);
        $total = intval($model_course->count());
        $model_course->where( $arr_where);
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
        $course_list = $model_course->select();
        Db::listen(function($sql,$time,$explain){
            // 记录SQL
            //echo $sql. ' ['.$time.'s]';
            // 查看性能分析结果
            //dump($explain);
        });
        // （6）将数据进行JSON编码
        return json(['total' => $total, 'rows' => $course_list]);
    }

    /**
     * 获取停课人列表（1.是督导，2.没课）
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

            $current['has_lesson'] = $this->hasLesson($row['teach_id'], $week, $weekday, $section) ? '是' : '否';
            $current['checked_times'] = $this->checkedTimes($row['teach_id']);
            $arr[] = $current;
        }
        // （6）将数据进行JSON编码
        return json(['total' => $total, 'rows' => $arr]);
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
        $model_course = new CourseModel();
        $model_schedule = new ScheduleModel();
        //(1) 获取POST数据
        $course_id = $_POST['courseid'];
        $teachers = $_POST['teachers'];
        //（2）根据编号从Courses表中获取详细的课表信息
        $listen_teach_information = $model_course->where('c_id', $course_id)->find();

        // (3) 向tbl_schedule中添加数据
        $techer_id = $listen_teach_information['teach_id'];
        $model_schedule->where("teacher_no", $techer_id)->setField('conuncilor', implode('|', $teachers));
    }

    public function removeall()
    {
        //$mo = new ClassesModel();
        //$count = $mo->where("1=1")->delete();//如果不接条件，则无法删除
        $result = Db::execute("TRUNCATE tbl_course_week");
        $ret = ['success' => 'true', 'message' => '清除成功！'];
        return json($ret);
    }

}