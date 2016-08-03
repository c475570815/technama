<?php
namespace app\index\controller;
use app\index\model\Test_table;
/*Demo 是一个控制器类 */
class Demo
{
    /* index函数是一个Action */
	public function index()
    {
        // 根据主键获取多个数据
		//$model = new Test_table();
		$list = Test_table::all();
		//打开一个文件名是application\index\view\demo\hello.html视图
		return view('hello',["data"=>$list]);
    }
	 
}
?>