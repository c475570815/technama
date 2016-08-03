<?php
/*
  定义类的命名空间,
  也就是说类的完整名字 app.test.controller.Controllerguowushi
  PHP的命名空间是按目录来整理的
 */
namespace app\guowushi\controller;

use think\View;
use app\guowushi\model\TestTable;
use app\guowushi\base\GridData;
use think\Db;

/*
 定义Controller类，一个类中有多个方法（Action）
 类名应与文件名一致，且首字母大写
 方法名应小写
*/

class Controllerguowushi
{
    /*  */
    public function index()
    {
        //通过模型获取数据

        // 实例化视图类,并将模型获取的数据传递给视图
        $view = new View();

        // 模板输出
        return $view->fetch('full');
    }


    public function action2()
    {
        //echo "hello";
        //通过模型获取数据
        //$list = TestTable::all();
        // 实例化视图类,并将模型获取的数据传递给视图
        $view = new View();
        //$view->data = $list;
        //$view->email = 'thinkphp@qq.com';
        // 模板输出
        return $view->fetch('datagrid');
    }

    public function action3()
    {
        $list = TestTable::all();
        $dt = new GridData();
        $dt->total = count($list);
        $rows = array();
        foreach ($list as $key => $row) {
            //echo $row->name;
            $rows[] = $row;
        }
        $dt->rows = $rows;
        echo json_encode($dt);
    }

    /**
     * 返回一个条件数组
     * [
        'name'  =>  ['like','thinkphp%'],
        'title' =>  ['like','%thinkphp'],
         'id'    =>  ['>',0],
         'status'=>  1
     ]
     * @return array
     */
    private function getwhere()
    {
        // 获取用户提交的查找数据.构造查询语句
        $arr_where=array();
        if (isset($_POST['name'])) {
            $name = $_POST['name'];
            $arr_where["name"]=['like',"$name%"];
        }
        if (isset($_POST['student_id'])) {
            $student_id = $_POST['student_id'];
            $arr_where["student_id"]=['=',"$student_id"];
        }
        return $arr_where;
    }

    private function getCurrentTable(){
       return  Db::table('test_table');

    }

    /* 获取JSON数据给DataGrid使用 */
    public function action4()
    {

        $current_table =$this->getCurrentTable();

        // 获取查询条件
        $current_table->where($this->getwhere());
        //获取客户端传递过来的参数 page=2&rows=20
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        //记录的总数
        $total = intval($current_table->count());
        $start = ($page - 1) * $rows;
        $current_table->limit($start, $rows);
        // 条件，排序，数量，选择
        Db::listen(function ($sql, $time, $explain) {
            // 记录SQL
            //echo $sql . ' [' . $time . 's]';
            // 查看性能分析结果
            //dump($explain);
        });
        $list = $current_table->select();
        return json(['total' => $total, 'rows' => $list]);
    }

}

?>