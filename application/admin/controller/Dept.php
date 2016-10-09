<?php
/*
  定义类的命名空间,
  也就是说类的完整名字 app.test.controller.Controllerguowushi
  PHP的命名空间是按目录来整理的
 */
namespace app\admin\controller;

use app\admin\DeptTreeGrid;
use app\common\model\DeptModel;
use think\View;
use think\Request;
use app\common\custom\InterfaceDataGrid;
use  \think\Controller;
use app\common\model\DictModel;
use app\common\model\DictCategoryModel;
use app\admin\DictDataGrid;
use think\Db;
use think\Loader;

/*
 定义Controller类，一个类中有多个方法（Action）
 类名应与文件名一致，且首字母大写
 方法名应小写
*/

class Dept extends Controller implements InterfaceDataGrid
{

    /**
     * 显示dataGrid
     */
    public function index(){

        //$dict= new DictCategoryModel();
        //$dict_category=$dict->select();
        //dump($dict_category);
        $view = new View();
        //$view->assign("dict_category",  $dict_category);
        return $view->fetch('treegrid');
    }

    /**
     * ajax数据显示
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public  function getlist(){
        $grid= new DeptTreeGrid();
        return $grid->getTreeJson();
    }

    /* 返回部门类型 */
    public function deptcat(){
        $model=new DictModel();
        $list= $model->where('dict_category','部门类型')->select();
        return json($list);
    }

    /**
     * 返回一级部门
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function rootdept(){
        $model=new DeptModel();
        $list= $model->where('dept_parent','')->where('dept_enabled','1')->select();
        return json($list);
    }


    public function import(){

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
            $new_record = new DeptModel();

            if( ! $new_record->where('dept_name',$form_data['dept_name'])->find() ){
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
                $ret=['success'=>false,'message'=>'该部门已存在，添加失败！'];
            }
        }else{
            $pk=$form_data['dept_name'];
            $new_record= new DeptModel();
            $new_record->save($form_data,['class_name'=>$pk]);
            $ret=['success'=>true,'message'=>'修改成功'];

        }
        return json($ret);
    }
    /*  显示添加页面 */
    public function add()
    {
        $view = new View();
        $view->assign("operation", '新增');
        return $view->fetch('form');
    }


    /**
     * 显示修改页面 index.php/admin/classes/update/pk/2
     * @param $pk  自动获取pk参数的值
     * @return string
     */
    public function update($pk){
        // 根据主键获取一条记录

     //   $record=$mo->where('class_name',$pk)->find();
        $record=DeptModel::get($pk);

        // 编辑页面的部门信息
        $dept=new DeptModel();
        $deptList=$dept->select();
        // 视图页面
        $view = new View();
        $view->assign("dept",  $deptList);
        $view->assign("operation",  '编辑');
        $view->assign("record",  $record);
        return $view->fetch('form');
    }
        /**
     * 删除记录
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function remove(){
        $id=$_POST['id'];
        $mo=new DeptModel();
        $count= $mo->where('dept_id','in',$id)->delete();
        if($count>0){
            $ret=['success'=>'true','message'=>'删除成功,共删除'.$count.'条记录'];
        }else{
            $ret=['success'=>'false','message'=>'删除失败！'];
        }
        return json($ret);
    }

    /**
     * 删除所有记录
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function removeall(){
        $result = Db::execute("TRUNCATE tbl_department;");

        if($result){
            $ret = ['success' => true, 'message' => '清除成功！'];
        }else{
            $ret = ['success' => false, 'message' => '清除失败！'];
        }
        $ret = ['success' => true, 'message' => '清除成功！'];
        return json($ret);
    }
    /**
     * 下载EXCEL文件
     */
    public function download(){
        $request = Request::instance();
            if($request->method()=='POST'){
                if($request->post('action')=='export'){
                    $dict_grid= new DeptTreeGrid();
                    $list=$dict_grid->getList();//记录
                    $xlsName  = "部门表";//电子表格名称
                    //  表中字段和EXCEL列的对应关系
                    $xlsCell  = array(
                        array('dept_name','部门名'),
                        array('dept_parent','父部门'),
                        array('dept_category','部门类型'),
                        array('dept_staff_number','人数'),
                        array('dept_comment','部门说明'),
                        array('dept_phone','部门电话'),
                        array('dept_addr','部门地址'),
                        array('dept_header','部门负责人'),
                        array('dept_enabled','启用')
                    );
                    $dict_grid->exportExcel($xlsName,$xlsCell,$list);
                }
        }
    }




    /**
     * 将excel文件转换成数组
     * @param $file
     * @param $start_row
     * @param $columns   表格字段定义 ['dept_name'=>'系部名称','dept_id'=>'系部编号']
     * ['A'=>'dept_name','B'=>'dept_id']
     * @return array
     */
    public function excel2array($file,$start_row,$columns){

        vendor("PHPExcel.PHPExcel");
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load($file,$encode='utf-8');
        $sheet = $objPHPExcel->getSheet(0);  // sheet1
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumn = $sheet->getHighestColumn(); // 取得总列数
        for($i=$start_row;$i<=$highestColumn;$i++){

        }
        $columns=array(
            array('dept_name','部门名'),
            array('dept_parent','父部门'),
            array('dept_category','部门类型'),
            array('dept_staff_number','人数'),
            array('dept_comment','部门说明'),
            array('dept_phone','部门电话'),
            array('dept_addr','部门地址'),
            array('dept_header','部门复制人'),
            array('dept_enabled','启用')
        );
        $list=array();// excel数据二维数组3
        $current_sheet=$objPHPExcel->getActiveSheet();
        for($i=$start_row;$i<=$highestRow;$i++)
        {
            $data=array();
            // array_flip($columns)['dept_name']  =='A'
            $data['dept_name']=  (string)$current_sheet->getCell("A".$i)->getValue();
            $data['dept_parent']=  (string)$current_sheet->getCell("B".$i)->getValue();
            $data['dept_category']=  (string)$current_sheet->getCell("C".$i)->getValue();
            $data['dept_staff_number']= (string) $current_sheet->getCell("D".$i)->getValue();
            $data['dept_comment']= (string)$current_sheet->getCell("E".$i)->getValue();
             $data['dept_phone']=  (string)$current_sheet->getCell("F".$i)->getValue();
            $data['dept_addr']=  (string)$current_sheet->getCell("G".$i)->getValue();
            $data['dept_header']=  (string)$current_sheet->getCell("H".$i)->getValue();
            $data['dept_enabled']=  (string)$current_sheet->getCell("I".$i)->getValue();
            //富文本转换字符串
//            if($cellVal instanceof PHPExcel_RichText){
//                $cellVal = $cellVal->__toString();
//            }
            // excel中日期读取出来是个数字，需要转化
//            $date = date("Y-m-d",PHPExcel_Shared_Date::ExcelToPHP($date) );
            $list[]=$data;
        }
        return $list;
    }

    /**
     * 对每行数据进行验证，返回验证结果
     * @param $data  数组，需要验证的数据
     * @param $validate  验证类
     * @return array   结果如 [ '1'=>[ '姓名为空','编号不是数字']]
     */
    public function excelValidate($datas,$validate){
        $ret= array();
        $i=2;
        foreach ($datas as $current_row){
            if(!$validate->check($current_row)){
                //dump($validate->getError());
                $arr_error=$validate->getError();
                $ret[$i]=$arr_error;
            }
            $i++;
        }
        return $ret;
    }

/**
 *  ajax上传/public/uploads/ 目录下并导入
 * {
    success：true，
     *   message：‘成功’
     *  data: {
    1: '用户名为空',5:'年龄必须是数字'
     *          }
     *
     * }
 */
public function upload(){
    // 获取上传文件并放到/public/uploads/ 目录下
    $file = request()->file('file');
    $tmp_file = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
    if($tmp_file){
        if($tmp_file->getExtension()!='xls'){
            // 上传失败获取错误信息
            @unlink($tmp_file);//删除上传的临时文件
            $ret1=['success'=>'false','message'=>'文件格式必须是XLS'];
            //echo $file->getError();
            return json($ret1);
        }
        // (1)将excel记录一次性读入到数组中
        $columns=array(
            array('dept_name','部门名'),
            array('dept_parent','父部门'),
            array('dept_category','部门类型'),
            array('dept_staff_number','人数'),
            array('dept_comment','部门说明'),
            array('dept_enabled','启用')
        );

        $start_row=2;
        $datas=$this->excel2array($tmp_file,$start_row,$columns);
//        var_dump($datas);


        // (2)对数组进行有效性验证（如是否唯一）,返回验证结果，结果是错误信息的数组
        //对数组做清除处理
        $validate = Loader::validate('DeptValidate');
        $ret=$this->excelValidate($datas,$validate);

        @unlink($tmp_file);
        if(count($ret)==0){
            //（3）保存数组中的数据到数据库
            $mo= new DeptModel();
            $mo->saveAll($datas,true);
            $ret1=['success'=>'true','message'=>'导入成功,共导入'.count($datas).'条记录'];
            // $this->success('导入成功！');
            return json($ret1);
        }else{
            $ret1=[
                'success'=>'false',
                'message'=>'导入失败,共有'.count($ret).'记录格式不对',
                'data'=>$ret
            ];
            return json($ret1);
            // $this->success('导入失败！');
        }

    }else{
        // 上传失败获取错误信息
        $ret1=['success'=>'false','message'=>'文件上传失败！'];
        //echo $file->getError();
        return json($ret1);
    }
}

    /**
     * 返回子部门
     * @return false|\PDOStatement|string|\think\Collection
     */
   public  function getSubDept(){
       $parent=$_POST['parent'];
       $mo=new DeptModel();
       return $mo->where('dept_parent',$parent)->select();
   }

    /**
     * 返回tree组件需要的JSON数据
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
   public function deptTree(){
       //先定义一个根节点
       $tree_root=array(
           'id'=>0,
           'state'=>'closed',
           'text'=>'全部系部',
           'checked'=>false
       );
       //现查找一级部门
       $dept=new DeptModel();
       $cond["dept_parent"]=array("EQ","");
       $de=$dept->where($cond)->field('dept_name,dept_id')->select();
       $nodes=array();
       foreach($de as $current_dept){
           $node=array(
               'id'=>$current_dept['dept_id'],
               'text'=>$current_dept['dept_name'],
               'iconCls'=>"ico-blank",
               'attributes'=>array('level'=>1)
           );
           // 获取二级部门节点
           $sub_depts=$dept->where("dept_parent",$current_dept['dept_name'])->field('dept_name,dept_id')->select();
           $childs=array();
           foreach ($sub_depts as $sub){
               $child=array(
                   'id'=>$sub['dept_id'],
                   'text'=>$sub['dept_name'],
                   'iconCls'=>"ico-blank",
                   'attributes'=>array('level'=>2)
               );
               $childs[]=$child;
           }
           if(count($childs)>0){
               $node['state']='closed';
               $node['children']=$childs;
           }
           $nodes[]=$node;
       }
       $tree_root['children']= $nodes;
       return json([$tree_root]);
   }
}

?>