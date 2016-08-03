<?php
/*
  定义类的命名空间,
  也就是说类的完整名字 app.test.controller.Controllerguowushi
  PHP的命名空间是按目录来整理的
 */
namespace app\admin\controller;
use think\Hook;
use think\Request;
use app\admin\ClassesDataGrid;
use app\common\custom\InterfaceDataGrid;
use think\View;
use app\common\model\ClassesModel;
use app\common\model\DeptModel;
use think\Db;
use  \think\Controller;

/*
 定义Controller类，一个类中有多个方法（Action）
 (1)类名应与文件名一致，且首字母大写
 (2)方法名应小写
*/

class Classes extends Controller implements InterfaceDataGrid
{

    private $url=array();
    protected $request;


    /**
     * 用于显示Grid
     */
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
     * 下载EXCEL文件
     */
    public function download(){
        $request = Request::instance();
        if($request->method()=='POST'){
            if($request->post('action')=='export'){
                $dict_grid= new ClassesDataGrid();
                $list=$dict_grid->getList();//
                $xlsName  = "班级表";
                $xlsCell  = array(
                    array('dept_name','部门名'),
                    array('class_name','班级名'),
                    array('class_room','教室'),
                    array('class_supervisor','导师'),
                    array('class_adviser','班主任')
                );
                $dict_grid->exportExcel($xlsName,$xlsCell,$list);
            }
        }
    }

    public function dataValid(){
        $validate = new Validate([
            'name'  => 'require|max:25',
            'email' => 'email'
        ]);
        $data = [
            'name'  => 'thinkphp',
            'email' => 'thinkphp@qq.com'
        ];
        if (!$validate->check($data)) {
            dump($validate->getError());
        }
    }
    /**
     * 将EXCEL导入表中
     * @param $file
     * @param int $start_row
     * @param int $title_row
     * @return bool
     */
    public  function excel2db($file,$start_row=2,$title_row=1){
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        vendor("PHPExcel.PHPExcel");
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load($file,$encode='utf-8');
        $sheet = $objPHPExcel->getSheet(0);  //
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumn = $sheet->getHighestColumn(); // 取得总列数
        $list=array();
        for($i=$start_row;$i<=$highestRow;$i++)
        {
            $data=array();
            $data['dept_name']=  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
            $data['class_name']=  $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
            $data['class_room']=  strtotime($objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue());
            $data['class_supervisor']=  $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
            $data['class_adviser']=  $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
            $list[]=$data;
        }
        $mo= new ClassesModel();
        $mo->saveAll($list);
        $this->success('导入成功！');
        return true;
    }
    public function importExcel(){
        //定义列和字段对应关系
        $xlsCell  = array(
            array('dept_name','部门名'),
            array('class_name','班级名'),
            array('class_room','教室'),
            array('class_supervisor','导师'),
            array('class_adviser','班主任')
        );
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            $filename=$info->getFilename();
            vendor("PHPExcel.PHPExcel");
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($filename,$encode='utf-8');
            $sheet = $objPHPExcel->getSheet(0);  //
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            //第三行B列起
            $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
            $start_row=2;

            $list=array();
            for($i=$start_row;$i<=$highestRow;$i++)
            {
                $data=array();
                $data['dept_name']=  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                $data['class_name']=  $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                $data['class_room']=  strtotime($objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue());
                $data['class_supervisor']=  $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                $data['class_adviser']=  $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                $list[]=$data;

            }
            $mo= new ClassesModel();
            $mo->saveAll($list);
            $this->success('导入成功！');
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }


    }
    /**
     * 打印网格
     */
    public function printGrid(){
        $view = new View();
        $dict_grid= new ClassesDataGrid();
        $list=$dict_grid->getList();//
        $view->assign("list",  $list);
        return $view->fetch('printgrid');
    }
    /**
     * 通过ajax方式从服务器上获取JSON格式的数据给网格显示
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public  function json(){
        $dict_grid= new ClassesDataGrid();
        return $dict_grid->dataGridJson();
    }

    /**
     * 返回系部信息
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public  function deptinfo(){
        $dept=new DeptModel();
        $deptList=$dept->select();
        return json($deptList);

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
            $new_record = new ClassesModel();
            if( !$new_record->isExist($form_data['class_name']) ){
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
            $pk=$form_data['class_name'];
            $new_record= new ClassesModel();
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
        $mo=new ClassesModel();
         $count= $mo->where('class_name','in',$id)->delete();
        if($count>0){
            $ret=['success'=>'true','message'=>'删除成功,共删除'.$count.'条记录'];
        }else{
            $ret=['success'=>'false','message'=>'删除失败！'];
        }
        return json($ret);
    }

    /**
     * 显示修改页面
     * @param $pk
     * @return string
     */
    public function update($pk){
        $view = new View();
        $mo=new ClassesModel();
        $record=$mo->where('class_name',$pk)->find();
        $dept=new DeptModel();
        $deptList=$dept->select();
        $view->assign("dept",  $deptList);
        $view->assign("operation",  '编辑');
        $view->assign("record",  $record);
        return $view->fetch('form');
    }


}

?>