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
use app\common\model\DictCategoryModel;
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

        $view = new View();
        $view->assign("dept", $deptList);
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
        $dict_grid = new ScheduleDataGrid();
        return $dict_grid->dataGridJson();
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

}

?>