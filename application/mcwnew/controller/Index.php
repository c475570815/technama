<?php
 namespace app\mcwnew\controller;
 use think\View;	
 use app\mcwnew\model\DeptModel;
 use app\mcwnew\model\ClassesModel;
 use app\common\model\CommonModelX;
class index{
	//打开index.html
	public function index(){
        $dept=new DeptModel();
        $deptList=$dept->select();
        //dump($deptList);
        $view = new View();
        $view->assign("dept",  $deptList);
        return $view->fetch('datagrid');
	}
	
	public function ac1(){
		 $classes=new ClassesModel();  //1.创建班级模板实例
         $comm= new CommonModelX();   //实例化通用模块
         //2.获每个表对应的模板里的取defaults值
         $defaults= $classes->defaults;
		 //3.根据defaults值获取where数组
		 $get_where=$this->get_where($defaults);
          //传where数组进取进入common类里/以便于执行语句
         $comm->get_power($get_where);
		 //把具体要查询的类名输入进json里
		 return $comm->dataGridJson($classes);
	}
	public function get_where($defaults){
        //1.创建where数组
        $arr_where=array();
		//3.查看从网页传来的dict是否存在
        if(isset($_POST['dict'])){
            $dict=$_POST['dict'];
			//4存在时就构建where语句赋值给$arr_where
            $arr_where=CommonModelX::filer($dict,$defaults);
        }
		//返回$arr_where语句
        return $arr_where;
	}	
}	
?>