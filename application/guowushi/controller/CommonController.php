<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 11:30
 */

namespace app\guowushi\controller;
/**
 * 一个通用的Controller类，
 * （1）把通用的方法定义在这里
 * （2）不同的方法需要从该类派生，并重写
 * Class CommonController
 * @package app\guowushi\controller
 */
abstract class CommonController
{

    /*  下面是抽象方法，也就是子类需要具体实现的方法 */
    protected abstract function getWhere();// 返回Where条件数组
    protected abstract function getCurrentTable();// 返回Where条件数组

    /**
     * 返回针对DataGrid表的的数据
     */
   public function dataGridJson(){

   }
}