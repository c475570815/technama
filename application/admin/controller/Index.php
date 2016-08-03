<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\TeaModel;
use think\View;
use think\Session;

/**
 * admin模块默认的控制器
 * Class Index
 * @package app\index\controller
 */
class Index extends Controller
{

    protected $beforeActionList = [
        // 'before'=>  ['except'=>'login,isLogined,authentication']
    ];

    public function before()
    {
        if (!$this->isLogined()) {
            return $this->error("没有登录哦！", 'login');
        }
    }

    public function isLogined()
    {
        if (Session::has('uid')) {
            return true;
        } else {
            return false;
        }
    }

    public function index()
    {
        // 实例化视图类
        $view = new View();
        $view->name = Session::get('name');
        $view->roles = Session::get('roles');
        // 模板输出
        return $view->fetch('layout');

    }

    public function chart()
    {
        return view('chart');
    }

    public function login()
    {
        return view('login');
    }

    public function logout()
    {
        // 删除（当前作用域）
        //Session::delete('uid');
        // 删除think作用域下面的值
        //Session::delete('name','think');
        // 清除session（当前作用域）
        Session::clear();
        return $this->success("退出成功", 'login');
    }


    /**
     * 对用户进行登录验证
     * @return mixed
     */
    public function authentication()
    {
        $statu = isset($_POST['statu']) ? $_POST['statu'] : "";
        $id = isset($_POST['id']) ? $_POST['id'] : "";
        $password = isset($_POST['password']) ? $_POST['password'] : "";
        $captcha = isset($_POST['captcha']) ? $_POST['captcha'] : "";

        //$wher=$this->getfindwhere($statu,$id,$password);
        $view = new View();
        if (!captcha_check($captcha)) {
            //验证失败
            return $this->error("验证码错误", 'login');
        };
        if(ldapValid($id,$password)){
            $table = new TeaModel();
            $row = $table->where('teach_id', $id)->find();
      /*      $table = new TeaModel();
            $row = $table->where('teach_id', $id)->where('teach_pass', $password)->find();*/
            if ($row) {
                $roles = $row['teach_role'];
                Session::set('name', $row['teach_name']);
                Session::set('uid', $id);
                Session::set('roles', $roles);
                // return view('layout');
                return $this->success("登录成功", 'index');
            } else {
                return $this->error("教师详细信息获取失败！", 'login');
            }
        }else{
            return $this->error("登录失败", 'login');
        }

    }

    public function authentication_ldap(){

    }

}
