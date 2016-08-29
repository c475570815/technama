<?php
namespace app\admin\controller;

use app\common\model\ConfigModel;
use app\common\model\DeptModel;
use app\common\model\TeacherModel;
use think\Config;
use think\Controller;
use app\common\model\TeaModel;
use think\View;
use think\Session;
use app\common\helper\VerifyHelper;
/**
 * admin模块默认的控制器
 * Class Index
 * @package app\index\controller
 */
class Index extends Controller
{
    //在执行Action之前需要执行的方法
    protected $beforeActionList = [
//        'before'=>  ['except'=>'login,isLogined,authentication']
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
        $view->current_term =$this->getCurrentTerm();

        return $view->fetch('layout');

    }

    /**
     * 获取当前学期
     * @return mixed
     */
    public function getCurrentTerm(){
        $model=new ConfigModel();
        $row= $model->where('cfg_name','current_term')->find();
        return $row['cfg_term'];
    }

    /**
     * 部门人员柱状图
     * @return View
     */
    public function chart()
    {
        $view = new View();
        $dept=new DeptModel();
        $rows=$dept->distinct(true)->where("dept_parent","")->field("dept_name")->select();

        foreach ($rows as $row){
            $xAxis_data[]=$row['dept_name'];
            $series_data[]=TeacherModel::where('dept_name',$row['dept_name'])->count();
        }
        //return json($xAxis_data);
        $view->xAxis =json_encode($xAxis_data) ;
        $view->series =implode(",", $series_data);

        return $view->fetch('chart');

    }
    /**
     * 显示验证码图片
     */
    public function verify()
    {
        VerifyHelper::verify();
    }
    /**
     * 登录界面
     * @return string
     */
    public function login()
    {
        $view = new View();
        // 模板输出
        return  $view->fetch('login');
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

//        if (!captcha_check($captcha)) {
//            //验证失败
//            $ret=['success'=>'false','message'=>'验证码错误'];
//            return $ret;
//            return $this->error("验证码错误", 'login');
//        };
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
                $ret=['success'=>'false','message'=>'教师详细信息获取失败'];
                return $ret;
                return $this->error("教师详细信息获取失败！", 'login');
            }
        }else{
            $ret=['success'=>'false','message'=>'登录失败'];
            return $ret;
            return $this->error("登录失败", 'login');
        }

    }

    public function authentication_ldap(){

    }

    /**
     * 发布消息到redis
     */
    public function pub(){
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        //$redis->set('test','hello world!');

        for($i=1;$i<100;$i++){
//            $redis->lPush('usr', "message $i from server");
//            $redis->publish('first', "message $i from server");
//            $redis->publish('SMS_CHANNEL', "SMS message $i from server");
        }

        for($i=1;$i<10 ;$i++){
            echo $i;
           $redis->publish('MESSAGE_CHANNEL', " MESSAGE $i from server");
        }


        // 发送email

//        $schedule =  [
//            'year'=>'2016',  //听课教师
//            'month' => '8', // 收件列表，多个联系人逗号分开
//            'day' => '16',      // 标题
//            'hour' => '4' ,        // html 内容
//            'min'=>'16',
//            'sec'=>'0',
//        ];
//        $message=[
//            "subject"=>"测试定时主题",
//            "to"=>"18942891954@qq.com",
//            "body"=>"测试邮件内容",
//            "schedule"=>$schedule
//        ];
//
//
//        $redis->publish('EMAIL_CHANNEL', json_encode($message,JSON_UNESCAPED_UNICODE));
//        $message=[
//            "subject"=>"测试立即主题",
//            "to"=>"18942891954@qq.com",
//            "body"=>"测试邮件内容"
//
//        ];
//        $redis->publish('EMAIL_CHANNEL', json_encode($message,JSON_UNESCAPED_UNICODE));
        echo "ok";
    }

}
