<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/21
 * Time: 9:42
 */

namespace app\admin\controller;
use think\Controller;
use app\common\model\TeacherModel;
use app\common\model\TeaModel;
use app\admin\TeaDataGrid;
use app\common\model\DeptModel;
use think\View;
use think\Db;
use think\Request;
use think\Loader;
class Tea extends Controller
{
    /**s
     * 用于显示Grid
     */
    public function index(){
        // 从教师表中获取系部名称
        $dept=new TeaModel();
        $deptList=$dept->distinct(true)->field('dept_name')->select();
        $view = new View();
        //
//        $tree=$this->treejosn();
        $view->assign("dept",$deptList);
//        $view->assign("tree",$tree);
        return $view->fetch('datagrid');
    }
    /**s
     * 用于显示Grid
     */
    public function tree(){
        // 从教师表中获取系部名称
        $dept=new TeaModel();
        $deptList=$dept->distinct(true)->field('dept_name')->select();
        $view = new View();
        //
//        $tree=$this->treejosn();
        $view->assign("dept",$deptList);
//        $view->assign("tree",$tree);
        return $view->fetch('treegrid');
    }
    /**
     * 通过ajax方式从服务器上获取JSON格式的数据给网格显示
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public  function ac1(){
        $tea_grid= new TeaDataGrid();
        return $tea_grid->dataGridJson();
    }

    public function getlist(){

        $current_table=new TeaModel();
        $arr_where=array();
        if (isset($_POST['dict'])) {
            $dict = $_POST['dict'];
            $arr_where=$current_table->filer($dict);
            $current_table->where($arr_where);
        }
         //print_r($arr_where);

        //先获取筛选后记录的总数
        $total = intval($current_table->count());
        //重新获取条件
        $current_table->where($arr_where);
        //获取客户端传递过来的参数 page=2&rows=20
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $start = ($page - 1) * $rows;
        $current_table->limit($start, $rows);
        // 排序
        if(isset($_POST['sort']) &&  isset($_POST['order'])){
            $sort = $_POST['sort'] ;
            $order = $_POST['order'];
            $current_table->order($sort,$order);
        }
        // 获取数组
        $list = $current_table->select();
        // 返回JSON
        return json(['total' => $total, 'rows' => $list]);
    }
    /**
     * 处理修改或者保存有效性
     */
    public function save(){
        $form_data=$_POST['data'];
        $ret=array(
            'success'=>false,'message'=>'添加失败'
        );
        $operation=$_POST['operation'];
        if($operation=='add'){
            $new_record = new TeaModel();
            if( !$new_record->isExist($form_data['teach_id']) ){
                $new_record->data($form_data);
                $new_record->save();
                $ret=['success'=>true,'message'=>'添加成功'];
            }else{
                $ret=['success'=>false,'message'=>'该用户已存在，添加失败！'];
            }
        }else{
            $pk=$form_data['teach_id'];
            $new_record= new TeaModel();
            $new_record->save($form_data,['teach_id'=>$pk]);
            $ret=['success'=>true,'message'=>'修改成功'];
        }
        return json($ret);
    }
    /*  显示添加页面 */
    public function add(){
        $view = new View();
        $view->assign("operation",'新增');
        return $view->fetch('Addform');
    }
    /**
     * 删除
     */
    public function  remove(){
        $id=$_POST['id'];
        //var_dump($id);
        //echo implode(",",$id);
        $mo=new TeaModel();
        // delete from table where class_name in ('id1'，’id2‘)
        $count= $mo->where('teach_id','in',$id)->delete();
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
        $mo=new TeaModel();
        $record=$mo->where('teach_id',$pk)->find();
        $dept=new DeptModel();
        $deptList=$dept->select();
        $view->assign("dept",  $deptList);
        $view->assign("operation",'编辑');
        $view->assign("record",  $record);
        return $view->fetch('Addform');
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
     * 跳转打印a
     * @return \think\Response|\think\response\Json|\think\re
    /**sponse\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function  pt(){



    }
    /**
     * 下载EXCEL文件
     */
    public function download(){
        $request = Request::instance();
        if($request->method()=='POST'){
            if($request->post('action')=='export'){
                $dict_grid= new TeaDataGrid();
                $list=$dict_grid->getList();//
                $xlsName  = "教师信息表";
                $xlsCell  = array(
                    array('dept_name','部门名'),
                    array('teach_name','教师名'),
                    array('sex','性别'),
                    array('teach_id','教师编号'),
                    array('teach_phone','电话')
                );
                $dict_grid->exportExcel($xlsName,$xlsCell,$list);
            }
        }
    }



//导入excel
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
            $data['teach_id']= (String)  $objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
            $data['dept_name']= (String)  $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
            $data['sub_dept']=(String)   $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
            $data['teach_role']= (String)  $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
            $data['teach_name']=(String)   $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
            $data['sex']= (String)  $objPHPExcel->getActiveSheet()->getCell("T".$i)->getValue();
            $data['profess_duty']= (String)  $objPHPExcel->getActiveSheet()->getCell("AL".$i)->getValue();
            $data['now_major']=(String)   $objPHPExcel->getActiveSheet()->getCell("AL".$i)->getValue();
            $data['holds_teacher']= (String)  $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
            $data['teach_pass']=(String)   $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
            $data['qq']= (String)  $objPHPExcel->getActiveSheet()->getCell("Z".$i)->getValue();
            $data['wechat_id']=  (String) $objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
            $data['email']= (String)  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
            $data['teach_phone']= (String)  $objPHPExcel->getActiveSheet()->getCell("Z".$i)->getValue();
            $data['conuncilor']= (String)  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
            $data['in_school_time']= (String)  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
            $data['location']= (String)  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
            $data['limit']= (String)  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
            $data['passed']=(String)   $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
            $data['teach_jw_id']=(String)  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
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
        $i=3;
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

    public function upload(){
    // 获取上传文件并放到/public/uploads/ 目录下
    $file = request()->file('file');
    if($file){
        $tmp_file = $file->move(ROOT_PATH  . 'public' . DS . 'uploads');
        if($tmp_file->getExtension()!='xls'){
            // 上传失败获取错误信息
            @unlink($tmp_file);//删除上传的临时文件
            $ret1=['success'=>'false','message'=>'文件格式必须是XLS'];
            //echo $file->getError();
            return json($ret1);
        }
        // (1)将excel记录一次性读入到数组中
        $columns=array(
            'dept_name'=>'所属系部',
            'teach_name'=>'教师名',
            'sex'=>'性别',
            'teach_id'=>'教师编号',
            'profess_duty'=>'专业技术职务',
            'teach_phone'=>'电话',
            'email'=>'电子邮箱',
            'qq'=>'QQ号',
            'holds_teacher'=>'是否兼课',
            'teach_role'=>'职位',
            'conuncilor'=>'是否免听',
            'limit'=>'听课限制'
        );

        $start_row=3;
        $datas=$this->excel2array($tmp_file,$start_row,$columns);
        // (2)对数组进行有效性验证（如是否唯一）,返回验证结果，结果是错误信息的数组
        //对数组做清除处理
        $validate = Loader::validate('TeacherValidate');
        $ret=$this->excelValidate($datas,$validate);
        // var_dump($ret);
        @unlink($tmp_file);
        if(count($ret)==0){
            //（3）保存数组中的数据到数据库
            $mo= new TeaModel();
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
    public function removeall(){
        //$mo = new ClassesModel();
        //$count = $mo->where("1=1")->delete();//如果不接条件，则无法删除
        $result=Db::execute("TRUNCATE tbl_teacher");
        $ret = ['success' => 'true', 'message' => '清除成功！'];
        return json($ret);
    }
    public function email(){
        $current_table = new TeacherModel();
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $list = $current_table->where("teach_id", "in", $id)->select();
            foreach ($list as $row) {
                // 取听课教师邮件
                    if ($row['email_validated']) {
                        $teacher_name = $row['teach_name'];
                        // 发送email
                        $subject = "听课安排";
                        $body = $teacher_name;
                        $message_json =  [
                            'year'=>'2016',  //听课教师
                            'month' => '7', // 收件列表，多个联系人逗号分开
                            'day' => '19',      // 标题
                            'hour' => '11' ,        // html 内容
                            'min'=>'21',
                            'sec'=>'0',
                        ];
                        $redis = new \Redis();
                        $redis->connect('127.0.0.1', 6379);
                        $redis->publish('email', json_encode($message_json,JSON_UNESCAPED_UNICODE));
                    }
            }
        }
    }
}
