<?php
/*
  定义类的命名空间,
  也就是说类的完整名字 app.test.controller.Controllerguowushi
  PHP的命名空间是按目录来整理的
 */
namespace app\admin\controller;

use app\common\model\EventModel;
use app\common\model\DictModel;
use app\common\model\TermModel;
use think\Controller;
use think\View;
use think\Db;

/*
 定义Controller类，一个类中有多个方法（Action）
 类名应与文件名一致，且首字母大写
 方法名应小写
*/

class Event extends Controller
{

    /**
     * 用于显示、查找、排序
     */
    public function index(){
        $view = new View();
        // 获取当前学期
        $mo=new TermModel();
        $rec= $record=$mo->where('default',1)->find();
        $view->assign('current_term',$rec['term_name']);
        return $view->fetch('datagrid');
    }

    /**
     * 返回Grid需要的数据
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public  function getlist(){
        $model=new EventModel();
        $arr_where=array();
        if(isset($_POST['dict'])){
            $dict=$_POST['dict'];
            $arr_where= $model->filer($dict);
            //print_r($arr_where);
        }
        $model->where($arr_where);
        //先获取筛选后记录的总数
        $total = intval($model->count());
        //获取客户端传递过来的参数 page=2&rows=20
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $model->where($arr_where);//重新获取条件
        $start = ($page - 1) * $rows;
        $model->limit($start, $rows);
        // 排序
        if(isset($_POST['sort']) &&  isset($_POST['order'])){
            $sort = $_POST['sort'] ;
            $order = $_POST['order'];
            $model->order($sort,$order);

        }

        Db::listen(function($sql,$time,$explain){
            // 记录SQL
             // echo $sql. ' ['.$time.'s]';
            // 查看性能分析结果
            //dump($explain);
        });
        // 获取数组
        $list = $model->select();
        // 返回JSON
        return json(['total' => $total, 'rows' => $list]);
    }


    /**
     * 保存数据(添加和修改通用)
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function save(){
        $form_data=$_POST['dict'];
        $ret=array(
            'success'=>false,'message'=>'添加失败'
        );
        $model=new EventModel();
        if($model->save($form_data)){
            $ret=['success'=>true,'message'=>'添加成功'];
        }else{
            $ret=['success'=>false,'message'=>'添加失败'];
        }
        return json($ret);
    }

    /**
     * 根据表单数据添加到表中，带有逻辑判断
     * @param $form_data
     * @return bool
     */
    private function addRecord($form_data){
        $mo=new DictModel();
        $mo->dict_category=$form_data['dict_category'];
        $mo->dict_key=$form_data['dict_key'];
        $mo->dict_value=$form_data['dict_value'];
        if(  !$mo->isExist() ){
            $mo->data($form_data);
            $mo->save();
            return true;
        }else{
            return false;
        }

    }

    /**
     * 根据表单数据修改，带有逻辑判断
     * @param $form_data
     * @return bool
     */
    private function updateRecord($form_data){
        $pk=$form_data['dict_id'];
        $new_record= new DictModel();
        $new_record->dict_category=$form_data['dict_category'];
        $new_record->dict_key=$form_data['dict_key'];
        $new_record->dict_value=$form_data['dict_value'];
        $new_record->dict_id=$form_data['dict_id'];
        if(  !$new_record->isExist() ) {
            $new_record->save($form_data, ['dict_id' => $pk]);
            return true;
        }else{
            return false;
        }
    }

    /*  添加页面 */
    public function add(){
        $view = new View();
        //$view->assign("dict_category",  $dict_category);
        return $view->fetch('form');
    }
    /* 处理用户编辑那一条记录 */
    public  function update($pk){
        $view = new View();
        $mo=new DictModel();
        $record=$mo->where('dict_id',$pk)->find();
        $view->assign("operation",  '编辑');
        $view->assign("record",  $record);
        return $view->fetch('form');

    }


    /**
     * 返回事件格式
     * @return string
     */
    public function getevents(){
        $event_model=new EventModel();
        $event_records = $event_model->select();
        $events=array();
        foreach ($event_records as $event_record){
            $event=array();
            $event['title']=$event_record['title'];
            $event['start']=$event_record['start'];
            $event['end']=$event_record['end'];
            $event['url']=$event_record['url'];
            $event['allDay']=$event_record['all_day'];
            $event['textColor']=$event_record['textColor'];
            $event['backgroundColor']=$event_record['backgroundColor'];
            $events[]=$event;
        }

        return json($events);

    }

    /**
     * 删除记录
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function remove(){
        $id=$_POST['id'];
        $mo=new EventModel();
        $count= $mo->where('id','in',$id)->delete();
        if($count>0){
            $ret=['success'=>'true','message'=>'删除成功,共删除'.$count.'条记录'];
        }else{
            $ret=['success'=>'false','message'=>'删除失败！'];
        }
        return json($ret);
    }
}

?>