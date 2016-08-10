<?php
/**
 * Created by PhpStorm.
 * User: FuJinsong
 * Date: 2016/8/9
 * Time: 15:46
 */

namespace app\admin\controller;
use app\admin\TermDataGrid;
use think\View;
use think\Request;
use app\common\model\TermModel;
use app\common\model\DictCategoryModel;
use app\admin\DictDataGrid;
use app\common\custom\InterfaceDataGrid;
use think\Db;
use  \think\Controller;

class Term extends Controller implements InterfaceDataGrid
{

    private $url=array();
    protected $request;

    /**
     * 用于显示Grid
     */
    public function index()
    {
        $term = new TermModel();
        $view = new View();
        return $view->fetch('datagrid');
    }



       public function getTermByDefault($default){
       $model_term=new TermModel();
       return  $model_term->where('default',$default)->find();
   }
    /**
     * 通过ajax方式从服务器上获取JSON格式的数据给网格显示
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public  function json(){
        $model_term= new TermModel();

        $arr_where=array();
        if(isset($_POST['dict'])){
            $dict=$_POST['dict'];
            $arr_where= $model_term->filer($dict);
        }


        $total = intval($model_term->count());

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        //$model_course->where($this->getWhere());//重新获取条件
        $start = ($page - 1) * $rows;
        $model_term->limit($start, $rows);
        // （3）对分页后的数据进行排序
        if(isset($_POST['sort']) &&  isset($_POST['order'])){
            $sort = $_POST['sort'] ;
            $order = $_POST['order'];
            $model_term->order($sort,$order);
        }
        // (4) 根据分页条件，排序条件进行数据查询
        $course_list=$model_term->select();

        $default_int_str=$this->getTermByDefault($default)->find();
        $ret_array[]=$model_term->select();
        //将default的数字转化为字符串
        if($default_int_str==1){
            $default_int_str='是';
        }else{
            $default_int_str='否';
        }
        $term['default_str']=$default_int_str;
        $ret_array[]=$term;
        return json(['total' => $total, 'rows' => $ret_array]);
    }



    /**
     * 接收表单填写的数据，并保存到数据库中
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function save(){
        $form_data=$_POST['data'];
        $ret=array(
            'success'=>false,'message'=>'添加失败'
        );
        $operation=$_POST['operation'];
        if($operation=='add'){
            $new_record = new TermModel();
            if( !$new_record->isExist($form_data['term_name']) ){
                // 验证
                /* $validate = Loader::validate('ClassesValidate');
                 if(!$validate->check($form_data)){
                     dump($validate->getError());
                 }*/
                // 保存
                $new_record->data($form_data);
                $new_record->save();
                $ret=['success'=>true,'message'=>'添加成功'];
            }else{
                $ret=['success'=>false,'message'=>'该用户已存在，添加失败！'];
            }
        }else{
            $pk=$form_data['term_name'];
            $new_record= new TermModel();
            $new_record->save($form_data,['class_name'=>$pk]);
            $ret=['success'=>true,'message'=>'修改成功'];

        }
        return json($ret);
    }
    /*  显示添加页面 */
    public function add(){
        $view = new View();
        $view->assign("operation",  '新增');
        return $view->fetch('form');
    }


    /**
     * 删除记录
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function remove(){
        $id=$_POST['id'];
        $mo=new TermModel();
        $count= $mo->where('term_name','in',$id)->delete();
        if($count>0){
            $ret=['success'=>'true','message'=>'删除成功,共删除'.$count.'条记录'];
        }else{
            $ret=['success'=>'false','message'=>'删除失败！'];
        }
        return json($ret);
    }
}