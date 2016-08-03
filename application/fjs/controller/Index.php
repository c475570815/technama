<?php
	namespace app\fjs\controller;
	use think\View;
	use app\fjs\model\TestTable;
	use app\fjs\model\TblTeacher;
	use think\Db;
	
	class Index{
		public function index1()
		{
			$list = TestTable::all();
			$view = new View();
			$view->data = $list;
			return $view-> fetch('myview');
		}
		public function index2()
		{
			$list = TestTable::all();
			// 实例化视图类,并将模型获取的数据传递给视图
			$view = new View();
			$view->data = $list;
			//  模板输出
			return $view->fetch('mygrid');
		}
		public function index3()
		{
			$list = TestTable::all();
			$dt = new GridData();
			$dt->total=count($list);
			$rows=array();
			foreach($list as $key=>$row){
				$rows[]=$row;
			}
			$dt->rows=$rows;
			//echo json_encode($dt);
			return json($dt);
		}
		public function index4()
		{
			$page = isset($_GET['page']) ? $_GET['page'] : 1;
			$rows = isset($_GET['rows']) ? $_GET['rows'] : 10;
			$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
			$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
			//$start=($page-1)*$rows+1;
			$list = Db::table('test_table')->order($sort,$order)->page($page,$rows)->select();
			//page=1&rows=10 页面和每页的记录数
			//sort=name&order=asc 排序参数和排序方式（asc升序，desc降序）
			//$px = Db::table('test_table')->where('status=1')->order('$sort $order')->page($page,$rows)->select();
			$total = TestTable::all();
			return json(['total'=>count($total),'rows'=>$list]);
		}
		public function index5()
		{
			$list = TblTeacher::all();
			// 实例化视图类,并将模型获取的数据传递给视图
			$view = new View();
			$view->data = $list;
			// 模板输出
			return $view->fetch('teacher');
		}
		public function index6()
		{
			$page = isset($_GET['page']) ? $_GET['page'] : 1;
			$rows = isset($_GET['rows']) ? $_GET['rows'] : 10;
			$sort = isset($_GET['sort']) ? $_GET['sort'] : 'teach_name';
			$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
			$list = Db::table('tbl_teacher')->order($sort,$order)->page($page,$rows)->select();
			$total = TblTeacher::all();
			return json(['total'=>count($total),'rows'=>$list]);
		}
        public function index7()
        {
            $view = new View();
            return $view-> fetch('admin');
        }
        public function index8()
        {
            $view = new View();
            return $view-> fetch('test');
        }
	}
?>