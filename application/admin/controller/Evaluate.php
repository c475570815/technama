<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/24
 * Time: 13:21
 */

namespace app\admin\controller;

use app\common\model\ItemModel;
use app\common\model\ScheduleModel;
use think\Controller;
use app\common\model\RecordModel;
use app\common\model\DeptModel;
use app\common\model\TeaModel;
use think\View;

/**
 * 评价结果
 * Class Evaluate
 * @package app\admin\controller
 */
class Evaluate extends Controller
{
    protected $beforeActionList = [
        // 'before'=>  ['except'=>'login,isLogined,authentication']
    ];

    public function before()
    {
        if (!$this->isLogined()) {
            return $this->error("没有登录哦！", 'login');
        }
    }

    public function isLogined()
    {
        if (Session::has('uid')) {
            return true;
        } else {
            return false;
        }
    }

    public function index()
    {
        $request = Request::instance();
        if ($request->method() == 'POST') {
            $dict_grid = new ClassesDataGrid();
            return $dict_grid->dataGridJson();

        } else {
            // 获取系部名称
            $dept = new DeptModel();
            $deptList = $dept->select();
            $view = new View();
            $view->assign("dept", $deptList);
            return $view->fetch('datagrid');
        }
    }

    /**
     * 跳转听课记录录入界面
     */
    public function add($pk)
    {
        // 部门信息
        $dept = new DeptModel();
        $deptList = $dept->distinct(true)->field('dept_name')->select();
        // 听课安排详细信息
        $rec=ScheduleModel::get($pk);
        // 视图显示
        $view = new View();
        $view->assign("dept", $deptList);
        $view->assign("schedule", $rec);
        return $view->fetch('evaluate');
    }

    function getItemById($id){
        return    ItemModel::get($id);
    }

    /**
     * 听课记录保存
     */
    public function save()
    {

        if(isset($_POST['data'])){
            $form_data = $_POST['data'];
            //对题目选项数据进行处理
            $items=$form_data['detail'];
            $answers=array();
            foreach($items as $key=>$item){
                $itm=ItemModel::get($key);

                $itm_json=json_decode($itm['item']);

                //var_dump($itm_json);

                $ans=array(
                   $itm_json->title=>$item
                );
                $answers[]=$ans;
            }

            $form_data['detail'] = json_encode($answers) ;
            // 新增听课记录
            $new_record = new RecordModel();
            $new_record->data($form_data);
            $new_record->save();
            $ret = ['success' => true, 'message' => '提交听课记录成功'];
        }else{
            $ret = ['success' => false, 'message' => '听课编号已存,在提交失败！'];
        }
        return json($ret);
    }

    /**
     * 根据姓名查询教师编号
     */
    private function convert($name)
    {
        $tea = new TeaModel();
        $id = $tea->where('teach_name', '=', $name)->column('teach_id');
        return implode(',', $id);
    }
}