<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 11:50
 */

namespace app\common;
use think\Db;
use think\Model;
use \PHPExcel_Cell_DataType;

/**
 * 一个用于DataGird数据操作的抽象类
 * 具体应从该类派生，并引用一个具体的模型类
 * Class DataGrid
 * @package app\common\model
 */
abstract class DataGrid
{

    /*  下面是抽象方法，也就是子类需要具体实现的方法 */
    /**
     * 返回Where条件数组
     * @return mixed
     */
    public abstract function getWhere();

    /**
     * 返回当前的表对象
     * @return mixed
     */
    public abstract function getCurrentTable();


    /**
     * 返回针对DataGrid表的的数据（带筛选、排序、分页）
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public  function dataGridJson(){

        $current_table =$this->getCurrentTable();
        $current_table->where($this->getWhere());
        //先获取筛选后记录的总数
        $total = intval($current_table->count());
        //获取客户端传递过来的参数 page=2&rows=20
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $current_table->where($this->getWhere());//重新获取条件
        $start = ($page - 1) * $rows;
        $current_table->limit($start, $rows);

        // 排序
        if(isset($_POST['sort']) &&  isset($_POST['order'])){
            $sort = $_POST['sort'] ;
            $order = $_POST['order'];
            $current_table->order($sort,$order);

        }

        Db::listen(function($sql,$time,$explain){
            // 记录SQL
           // echo $sql. ' ['.$time.'s]';
            // 查看性能分析结果
            //dump($explain);
        });
        // 获取数组

        $list = $current_table->select();

        // 返回JSON
        return json(['total' => $total, 'rows' => $list]);

    }

    /**
     * 返回筛选后的数据，没有分页
     * @return mixed
     */
    public function getList(){
        $current_table =$this->getCurrentTable();
        // 获取查询条件
        $current_table->where($this->getWhere());
        // 排序
        if(isset($_POST['sort']) &&  isset($_POST['order'])){
            $sort = $_POST['sort'] ;
            $order = $_POST['order'];
            $current_table->order($sort,$order);
        }
        // 获取数组
        $list = $current_table->select();
        return $list;
    }
    /**
     * 导出EXCEL
     * @param $expTitle  导出的excel文件名
     * @param $expCellName   表和单元格的字段对应（二维数组）
     * @param $expTableData   二维数组数据
     * 例如：
     *
     */
    public function exportExcel($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件标题
        $fileName =$expTitle.date('_YmdHis');   // 导出文件名称
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        // 创建并设置属性
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new  \PHPExcel(); //注意前面的\
        $objPHPExcel->getProperties()->setCreator("WWW.DreamStudio.TOP")
            ->setLastModifiedBy("DreamStudio")
            ->setTitle("Office 2007 XLSX  Document")
            ->setSubject("Office 2007 XLSX  Document")
            ->setDescription("")
            ->setKeywords("")
            ->setCategory("");
        $cellName = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
        );
        // 设置标题
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]);
        }
        // 数据填写
        $sheet=$objPHPExcel->getActiveSheet();
        for($row=0;$row<$dataNum;$row++){
            for($col=0;$col<$cellNum;$col++){
                $sheet->setCellValueExplicit($cellName[$col].($row+2),$expTableData[$row][$expCellName[$col][0]],PHPExcel_Cell_DataType::TYPE_STRING);
//                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle($expTitle);
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();//清除缓冲区,避免乱码
        // 头部信息
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//注意前面的\
        $objWriter->save('php://output');
        exit;
    }
}