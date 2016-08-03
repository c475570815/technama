<?php
//定义命名空间（也叫作类的完整名字）
namespace app\mcw\controller;
use think\View;
use think\Db;
use app\mcw\model\Test_table;
//定义控制器类（拥有多个方法Action）  //每个方法都有一个网页文件或者返回数据
//类名首字母大写其他小写/方法全部小写
class Index{
	//hello方法调用hello.html
	public function hello(){
		$view=new View();
		return $view->fetch();       //自动调用test/view/index/hello.html     //方法名称是什么就是调用什么.html
	}
	public function get(){
		$page=isset($_GET["page"])?$_GET["page"]:1;
		$rows=isset($_GET["rows"])?$_GET["rows"]:10;
		$total=intval(Test_table::count());
		$start=($page-1)*$rows;
		$list=Db::table("test_table")->limit($start,$rows)->select();
		return json(['total'=>$total,'rows'=>$list]);
	}
	public function index(){
		$view=new View();
		return $view->fetch();
	}
}



?>

