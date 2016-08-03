<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/24
 * Time: 13:21
 */

namespace app\admin\controller;
use think\Controller;
use app\common\model\RecordModel;
use app\common\model\DeptModel;
use app\common\model\TeaModel;
use think\View;

class Evaluate  extends Controller
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

   public function index(){
       $request = Request::instance();
       if($request->method()=='POST'){
           $dict_grid= new ClassesDataGrid();
           return $dict_grid->dataGridJson();

       }else{
           // 获取系部名称
           $dept=new DeptModel();
           $deptList=$dept->select();
           $view = new View();
           $view->assign("dept",  $deptList);
           return $view->fetch('datagrid');
       }
   }
    /**
     * 跳转课程评估界面
     */
    public function  add(){
        $dept=new DeptModel();
        $deptList=$dept->distinct(true)->field('dept_name')->select();
        $view=new View();
        $max=RecordModel::max('id');
        $view->assign("id",$max);
        $view->assign("dept",$deptList);
        return $view->fetch('evaluate');
    }
    /**
     * 听课记录保存
     */
    public function save(){
        $form_data=$_GET['data'];
        $form_data['teacher_id']=$this->convert($form_data["teacher"]);
        $ret=array(
            'success'=>false,'message'=>'添加失败'
        );
            $new_record = new RecordModel();
            if( !$new_record->isExist($form_data['id']) ){
                $new_record->data($form_data);
                $new_record->save();
                $ret=['success'=>true,'message'=>'提交听课记录成功'];
            }else{
                $ret=['success'=>false,'message'=>'听课编号已存,在提交失败！'];
            }
        return json($ret);
    }
    /**
     * 根据姓名查询教师编号
     */
    private  function convert($name) {
        $tea=new TeaModel();
         $id=$tea->where('teach_name','=',$name)->column('teach_id');
        return  implode(',',$id);
    }
}