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
use think\Loader;

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
     * 删除所有数据
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function removeall(){
        $mo=new ClassesModel();
        $count= $mo->where("1=1")->delete();//如果不接条件，则无法删除
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
        $list=array();// excel数据二维数组
        for($i=$start_row;$i<=$highestRow;$i++)
        {
            $data=array();
            // array_flip($columns)['dept_name']  =='A'
            $data['dept_name']=  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
            $data['class_name']=  $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
            $data['class_room']=  $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
            $data['class_supervisor']=  $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
            $data['class_adviser']=  $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
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
                'dept_name'=>'部门名',
                'class_name'=>'班级名',
                'class_room'=>'教室',
                'class_supervisor'=>'导师',
                'class_adviser'=>'班主任'
            );

            $start_row=2;
            $datas=$this->excel2array($tmp_file,$start_row,$columns);
            // (2)对数组进行有效性验证（如是否唯一）,返回验证结果，结果是错误信息的数组
            //对数组做清除处理
            $validate = Loader::validate('ClassesValidate');
            $ret=$this->excelValidate($datas,$validate);
           // var_dump($ret);
            @unlink($tmp_file);
            if(count($ret)==0){
                //（3）保存数组中的数据到数据库
                $mo= new ClassesModel();
                $mo->saveAll($datas);
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

}

?>