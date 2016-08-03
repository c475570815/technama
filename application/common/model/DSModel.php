<?php
namespace app\common\model;
use think\Model;
/**
 * 通用的模型类
 * User: guowushi
 * Date: 2016/7/20
 * Time: 11:43
 */
class DSModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    //protected $table = 'think_user';
    //手工设置主键
    //protected $pk = 'uid';

    /**
     *  自定义的初始化
     */
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }
    /**
     * 返回针对DataGrid表的的数据
     */
    public function dataGridJson(){

        // 获取查询条件
         $this->db()->where($this->getwhere());
        //获取客户端传递过来的参数 page=2&rows=20
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        //记录的总数
        $total = intval(TestTable::count());
        $start = ($page - 1) * $rows;
        $this->db()->limit($start, $rows);
        // 条件，排序，数量，选择
        Db::listen(function ($sql, $time, $explain) {
            // 记录SQL
            //echo $sql . ' [' . $time . 's]';
            // 查看性能分析结果
            //dump($explain);
        });
        $list = $this->db()->select();
        return json(['total' => $total, 'rows' => $list]);
    }
}