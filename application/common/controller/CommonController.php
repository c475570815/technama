<?php
/*
  定义类的命名空间,
  也就是说类的完整名字 app.test.controller.Controllerguowushi
  PHP的命名空间是按目录来整理的
 */
namespace app\common\controller;

use think\View;
use think\Db;
use  \think\Controller;
use think\Request;
/*
 定义通用的Controller类，其他的控制器必须从该类派生。并实现以下方法
 (1) index 显示资源列表
 (2) create 显示创建资源表单页
 (3）save 保存新建的资源
（4） read  显示指定的资源
（5）edit 显示编辑资源表单页
（6）update 保存更新的资源
（7）delete($id)删除指定资源
*/

abstract class  CommonController extends Controller
{
    /**
     * 用于显示Grid
     */
    public abstract function index();
    public abstract function json();
    /* 接收表单填写的数据，并保存到数据库中 */
    public abstract function save();
    public abstract  function ceate();
    public abstract function delete();

    public function _initialize(){
        /*import('ORG.Util.Auth');//加载类库
        $auth=new Auth();
        if(!$auth->check(MODULE_NAME.'-'.ACTION_NAME,session('uid'))){
            $this->error('你没有权限');
        }*/
    }
    public function cacheDataInit(){
        //如果没有缓存，则读取数据库当中的数据放入缓存
        //如果有缓存，则读取缓存数据
        // 设置缓存数据
        if(cache('name')){

        }else{

            //cache('name', $value, 3600);
        }

    }

    public function auth(){

    }
}

?>