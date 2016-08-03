<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/22
 * Time: 15:22
 */

namespace app\common\custom;


/**/
interface InterfaceController
{
    public function index();    //  默认显示index.htm
    public function getall();   // 返回 Grid需要的JSON数据
    public function update();   //  显示form,htm编辑页面
    public  function add();     // 显示form.htm添加页面
    public function remove();   //  删除
    public function save();     // 保存新增和修改的记录
    public function download();  // Grid中数据下载


}