<?php
namespace app\admin\controller;
use app\admin\AdjustmentDataGrid;
    use think\View;
    use think\Request;
    use app\common\model\AdjustmentModel;
    use app\common\model\DictCategoryModel;
    use app\common\model\DeptModel;
    use app\admin\DictDataGrid;
    use app\common\custom\InterfaceDataGrid;
    use think\Db;
    use  \think\Controller;

    /*
     定义Controller类，一个类中有多个方法（Action）
     (1)类名应与文件名一致，且首字母大写
     (2)方法名应小写
    */

    class Adjustment extends Controller implements InterfaceDataGrid
    {

        private $url=array();
        protected $request;

        /**
         * 用于显示Grid
         */
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
                        array('teach_id','教师编号'),
                        array('class_name','班级名'),
                        array('class_room','教室'),
                        array('course_name','课程名称'),
                        array('week','周次'),
                        array('xing_qi_ji','星期'),
                        array('section','节次'),
                        array('reason','原因'),
                        array('alt_week','调后周次'),
                        array('alt_xq','调后星期'),
                        array('alt_section','调后节次'),
                        array('alt_class_room','调后教室')
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
                array('teach_id','教师编号'),
                array('class_name','班级名'),
                array('class_room','教室'),
                array('course_name','课程名称'),
                array('week','周次'),
                array('xing_qi_ji','星期'),
                array('section','节次'),
                array('reason','原因'),
                array('alt_week','调后周次'),
                array('alt_xq','调后星期'),
                array('alt_section','调后节次'),
                array('alt_class_room','调后教室')
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
                    $data['teach_id']=  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    $data['class_name']=  $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                    $data['class_room']=  strtotime($objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue());
                    $data['course_name']=  $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                    $data['week']=  $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                    $data['xing_qi_ji']=  $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
                    $data['section']=  $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                    $data['reason']=  $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                    $data['alt_week']=  $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                    $data['alt_xq']=  $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                    $data['alt_section']=  $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                    $data['alt_class_room']=  $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
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
        public function ac1()
        {
            $dict_grid = new AdjustmentDataGrid();
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


        public function save(){
            $form_data=$_POST['data'];
            $ret=array(
                'success'=>false,'message'=>'添加失败'
            );
            $operation=$_POST['operation'];
            if($operation=='add'){
                $new_record = new AdjustmentModel();
                if( !$new_record->isExist($form_data['adj_id']) ){
                    $new_record->data($form_data);
                    $new_record->save();
                    $ret=['success'=>true,'message'=>'添加成功'];
                }else{
                    $ret=['success'=>false,'message'=>'该用户已存在，添加失败！'];
                }
            }else{
                $pk=$form_data['adj_id'];
                $new_record= new Adjustment();
                $new_record->save($form_data,['adj_id'=>$pk]);
                $ret=['success'=>true,'message'=>'修改成功'];

            }
            return json($ret);
        }
        /* 保存数据 */
        public function remove(){
            $id=$_POST['id'];
            $mo=new AdjustmentModel();
            $count= $mo->where('adj_id','in',$id)->delete();
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
            $result = Db::execute("TRUNCATE tbl_adjustment;");
            if($result){
                $ret = ['success' => 'true', 'message' => '清除成功！'];
            }else{
                $ret = ['success' => 'false', 'message' => '清除失败！'];
            }
            return json($ret);
        }



        /*  添加页面 */
        public function add()
        {
            $view = new View();
            //$view->assign("dict_category",  $dict_category);
            $view->assign("operation",  '新增');
            return $view->fetch('form');
        }

        /**
         * 显示修改页面
         * @param $pk
         * @return string
         */
        public function update($pk){
            $view = new View();
            $mo=new AdjustmentModel();
            $record=$mo->where('adj_id',$pk)->find();
            $dept=new DeptModel();
            $deptList=$dept->select();
            $view->assign("dept",  $deptList);
            $view->assign("operation",  '编辑');
            $view->assign("record",  $record);
            return $view->fetch('form');
        }

        /**
         * 根据教务的教师姓名查找教师编号
         * @param $name  在教务系统中的教师名
         * @return string  教师的编号
         */
        public function findTeacherId($name){
            $sql="SELECT [教师编号] FROM [教师信息v] WHERE [姓名]='".$name."'";
            $sth= Db::connect(config('jwc_odbc'))->query($sql);
     ;
            if($sth){
//                $row=$sth->fetch(PDO::FETCH_BOTH);
                return $sth[0]['教师编号'];
            }else{
                return "";
            }
        }

        /**
         * 将教务的调课信息同步到mysql表中
         */
        public function sync(){
            $result = Db::execute("TRUNCATE tbl_adjustment;");//删除原有记录
           $sql = " SELECT  [id] ,[学期] ,convert(varchar(255), [班级]) as class ,convert(varchar(255), [课程]) as course ,
                [教师] ,[教室] ,[周次]
      ,[上课时间] ,[调后周次],[调后时间],[调后教室] ,[调停类别] ,[备注],[开课单位]
      ,[审核],[申请时间]
  FROM [gc].[dbo].[tk调停记录]";
           $sth= Db::connect(config('jwc_odbc'))->query($sql);

            if ($sth){
                    $count=0;
                    foreach ($sth as $row){

                        $adj=new AdjustmentModel();
                        $adj->term=$row['学期'];
                        $adj->teach_name=$row['教师'];
                        $adj->teach_id=$this->findTeacherId($row['教师']);
                        $adj->class_room=$row['教室'];
                        $adj->class_name=$row['class'];
                        $adj->course_name=$row['course'];
                        $adj->dept_name=$row['开课单位'];
                        $adj->week=$row['周次'];
                        $arr=explode("-",$row['上课时间']);
                        $adj->xing_qi_ji=$arr[0];
                        $adj->section=$arr[1];
                        $adj->reason=$row['调停类别'];
                        $adj->alt_week=$row['调后周次'];
                        if(strpos($row['调后时间'],'-')>0){
                            $adj_arr=explode("-",$row['调后时间']);
                            $adj->alt_xq=$adj_arr[0];
                            $adj->alt_section=$adj_arr[1];
                        }else{
                            $adj->comment=$row['调后时间'];
                        }

                        $adj->alt_class_room=$row['调后教室'];
//                        $adj->alt_class_room=$row[''];
                        $adj->passed=$row['审核'];
                        $adj->apply_time=$row['申请时间'];

                        $adj->save();
                        $count++;

                    }
                $ret=['success'=>'true','message'=>'同步成功,共同步'.$count.'条记录'];
            }else{
                $ret=['success'=>'false','message'=>'同步失败！'];

            }
            return json($ret);

        }

    }

?>