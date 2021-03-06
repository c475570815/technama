<?php
namespace app\teacher\controller;

use app\common\model\ConfigModel;
use app\common\model\CourseModel;
use app\common\model\DeptModel;
use app\common\model\ScheduleModel;
use app\common\model\TeacherModel;
use app\common\model\ItemModel;
use app\common\model\RecordModel;
use app\common\Qyweixin;
use app\common\Weixin;
use think\Config;
use think\Controller;
use app\common\model\TeaModel;
use think\View;
use think\Session;
use app\common\helper\VerifyHelper;
use app\common\model\TermModel;
use app\common\TermCalendar;
use think\Cache;
/**
 * admin模块默认的控制器
 * Class Index
 * @package app\index\controller
 */
class Index extends Controller
{
    //在执行Action之前需要执行的方法
    protected $beforeActionList = [
        'before' => ['except' => 'login,isLogined,authentication,auth2']
    ];

    /**
     * 前置处理函数，在执行其他action前需要执行
     */
    public function before()
    {
        if (!$this->isLogined()) {
            return $this->error("没有登录哦！", 'login');
        }
    }

    /**
     * 判断是否登录
     * @return bool
     */
    public function isLogined()
    {
        if (Session::has('uid')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 考核标准
     * @return bool
     */
    public function standard()
    {
        $view = new View();
        $view->name = Session::get('name');
        $view->uid = Session::get('uid');
        $view->roles = Session::get('roles');
        $view->current_term = $this->getCurrentTerm();
        // 获取登录者的听课计划
        $where = [
            "term" => $this->getCurrentTerm(),
            "conuncilor" => Session::get('uid')
        ];
        $view->lists = ScheduleModel::where($where)->select(); //当前学期的听课
        return $view->fetch('standard');
    }

    /**
     * 显示所有发布的听课安排
     * @return string
     */
    public function index()
    {
        // 实例化视图类
        $view = new View();
        $view->name = Session::get('name');
        $view->uid = Session::get('uid');
        $view->roles = Session::get('roles');

        $view->current_term = $this->getCurrentTerm();
        // 获取登录者的听课计划
        $where = [
            "term" => $this->getCurrentTerm(),
            "passed" => '发布',
            "conuncilor" => Session::get('uid')
        ];
        $sch = ScheduleModel::where($where)->select();
        $view->lists = $sch;
        // 获取被听课教师的个人信息
        $teachers = array();
        foreach ($sch as $sc) {
            $where = array(
                "teach_id" => $sc['teach_id']
            );
            $teacher = TeacherModel::where($where)->find();
            $teachers[$sc['teach_id']] = $teacher;
        }
        $view->teachers = $teachers;
        return $view->fetch('list');

    }

    /**
     * 获取当前学期
     * @return mixed
     */
    public function getCurrentTerm()
    {
       if(Cache::get('current_term')==false){
           $term = new TermModel();
           $current_term = $term->where("default", 1)->find();
           Cache::set('current_term',$current_term['term_name'],3600);
       }
        return Cache::get('current_term');
    }


    /**
     * 显示验证码图片
     */
    public function verify()
    {
        VerifyHelper::verify();
    }

    /**
     * 显示登录登录界面
     * @return string
     */
    public function login()
    {
        $view = new View();
        if (ismobile()) {
            // 获取微信参数
//            $wx=new Qyweixin();
            $wx = new Weixin();
            $view->signPackage = $wx->getSignPackage();
            $view->token = $wx->getAccessToken();
            return $view->fetch('login');
        } else {
            return $view->fetch('login_pc');
        }
    }

    /**
     * 退出登录
     * @return array
     */
    public function logout()
    {
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
        $id = isset($_POST['id']) ? $_POST['id'] : "";
        $password = isset($_POST['password']) ? $_POST['password'] : "";
        //验证码
//        $captcha = isset($_POST['captcha']) ? $_POST['captcha'] : "";
//        if (!captcha_check($captcha)) {
//            $ret=['success'=>'false','message'=>'验证码错误'];
//            return $ret;
//            return $this->error("验证码错误", 'login');
//        };
        if (ldapValid($id, $password)) {
            //如果用户选择了，记录登录状态就把用户名和加了密的密码放到cookie里面
//            $remember = $_POST['remember'];
//            if(!empty($remember)){
//             setcookie("id", $id, time()+3600*24*30);
//             setcookie("password", $password, time()+3600*24*30);
//            }

            $table = new TeacherModel();
            $row = $table->where('teach_id', $id)->find();
            if ($row) {
                $roles = $row['teach_role'];
                Session::set('name', $row['teach_name']);
                Session::set('uid', $id);
                Session::set('roles', $roles);
                $ret = ['success' => 'true', 'message' => '教师认证成功'];
//                return json($ret);
                return $this->success("登录成功", 'index');
            } else {
                $ret = ['success' => 'false', 'message' => '教师详细信息获取失败'];
//                return json($ret);
                return $this->error("教师详细信息获取失败！", 'login');
            }
        } else {
            $ret = ['success' => 'false', 'message' => '登录失败'];
//            return json($ret);
            return $this->error("登录失败", 'login');
        }

    }

    public function authentication_ldap()
    {

    }

    /**
     * 发布消息到redis
     */
    public function pub()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        //$redis->set('test','hello world!');

        for ($i = 1; $i < 100; $i++) {
//            $redis->lPush('usr', "message $i from server");
//            $redis->publish('first', "message $i from server");
//            $redis->publish('SMS_CHANNEL', "SMS message $i from server");
        }
        for ($i = 1; $i < 10; $i++) {
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

    public function lists()
    {
        $view = new View();
        // 模板输出
        return $view->fetch('list');
    }

    /**
     * 显示录入听课结果的页面
     * @return string
     */
    public function form()
    {
        // 获取表单的编号
        $id = $_GET['id'];
        $view = new View();
        $view->name = Session::get('name');
        $view->uid = Session::get('uid');
        $view->roles = Session::get('roles');
        $view->current_term = $this->getCurrentTerm();
        $view->vo = ScheduleModel::find($id); //当前听课
        // 模板输出
        return $view->fetch('form');
    }

    /**
     * 设置finished字段
     */
    public function setfinished()
    {
        $id = $_POST['id'];
        $row = ScheduleModel::get($id);
        $row->finished = '完成';
        $row->save();
        $ret = ['success' => 'true', 'message' => '听课已完成，请及时填写评价表'];
        return json($ret);
    }
    /**
     * 设置finished字段
     */
    public function setunfinished()
    {
        $id = $_POST['id'];
        $row = ScheduleModel::get($id);
        $row->finished =null;
        $row->save();
        $ret = ['success' => 'true', 'message' => '设置听课为未完成'];
        return json($ret);
    }
    /**
     * 设置finished字段
     */
    public function setcancel()
    {
        $id = $_POST['id'];
        $row = ScheduleModel::get($id);
        $row->finished ='取消';
        $row->save();
        $ret = ['success' => 'true', 'message' => '取消听课，请查看教师课表，并自行选择听课时间'];
        return json($ret);
    }
    /**
     * 保存听课记录
     */
    public function saverecord()
    {
        if (isset($_POST['data'])) {
            $form_data = $_POST['data'];
            //对题目选项数据进行处理,返回一个所有题目答案的数组
            if (isset($form_data['detail'])) {
                $items = $form_data['detail'];
                $answers = array();
                //次数需要修改！！！！！！！！！！！！！！！！！！！！！！！！！！！
                foreach ($items as $key => $item) {
                    $itm = ItemModel::get($key);//根据题目编号，获取题目信息
                    $itm_json = json_decode($itm['item']);
                    $ans = array(
                        $itm_json->title => $item
                    );
                    $answers[] = $ans;
                }
                // 详细记录
                $form_data['detail'] = json_encode($answers);
            }


            // 获取听课安排编号
            $schedule_id = $_POST['id'];

            $schedule = ScheduleModel::get($schedule_id);
            $current_date= date("Y-m-d");
            $schedule_date=$schedule->time;

            $term=new TermModel();
            $current_term=$term->where("default",1)->find();
            $tc=new TermCalendar( $current_term['start'],$current_term['end']);
            $current_week = $tc->getWeek(date('Y-m-d')); //当前是第几周
            $today_weekday =date('N', strtotime(date('Y-m-d')));; //当前是星期几
            $tc->getDate(2,6)->format('Y-m-d');  // 第几周

            if($current_date<$schedule_date){
                $ret = ['success' => false, 'message' => '不能提前提交听课数据！'];
                return json($ret);
            }
            $schedule->finished = "完成";
            $schedule->locked = "锁定";
            $schedule->save();

            // 获取听课人信息
            $listener_no = $schedule['conuncilor'];
            $listener = TeacherModel::get($listener_no);
            $form_data['listener'] = $listener["teach_name"];
            $form_data['listener_no'] = $listener_no;
            // 新增听课记录
            $new_record = new RecordModel();
            $form_data['term'] = $schedule["term"];
            $form_data['week'] = $schedule["week"];
            $form_data['xing_qi_ji'] = $schedule["xing_qi_ji"];
            $form_data['section'] = $schedule["section"];
            $form_data['class_name'] = $schedule["class_name"];
            $form_data['course_name'] = $schedule["course_name"];
            $form_data['class_room'] = $schedule["class_room"];
            $form_data['teacher_id'] = $schedule["teach_id"];
            $form_data['teacher'] = $schedule["teach_name"];
            $form_data['dept_name'] = $schedule["dept_name"];
            $new_record->data($form_data);
            $new_record->save();
            $ret = ['success' => true, 'message' => '提交听课记录成功'];
        } else {
            $ret = ['success' => false, 'message' => '听课编号已存,在提交失败！'];
        }
        return json($ret);
    }


    /**
     * URL是企业应用接收企业号推送请求的访问协议和地址，支持http或https协议。
     * Token可由企业任意填写，用于生成签名。
     * EncodingAESKey用于消息体的加密，是AES密钥的Base64编码。
     */
    public function wxAction()
    {
        $url = "";
        $token = "";
        $encodingAESKey = "";

        vendor("dodgepudding.wechat-php-sdk.qywechat#class");

        $options = array(
            'token' => 'tokenaccesskey', //填写应用回调接口的Token
            'encodingaeskey' => 'encodingaeskey', //填写回调加密用的EncodingAESKey
            'appid' => 'wx06693892f83ef14c', //填写高级调用功能的app id
            'appsecret' => 'gpVpxWBga2dtl2oi5vtRKktzUNpxQLro7zoqE81LLijLbKW_nmewbCZ7ZWj5jpn8', //填写高级调用功能的密钥
            'agentid' => '26', //应用的id
            'debug' => false, //调试开关
            'logcallback' => 'logg', //调试输出方法，需要有一个string类型的参数
        );
        $wx = new \QyWechat($options);
        $user = array(
            "userid" => "zhangsan2",//成员UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节
            "name" => "张三",
            "department" => [1, 2],//成员所属部门id列表,不超过20个
            "position" => "产品经理",
            "mobile" => "15913215422",
            "gender" => 1,     //性别。gender=0表示男，=1表示女
            "tel" => "62394",
            "email" => "zhangsan@gzdev.com",
            "weixinid" => "zhangsan4dev"
        );

        return json($wx->createUser($user));


    }

    /**
     * 将系统中的部门同步到微信企业号
     */
    public function   syncdept()
    {
        vendor("dodgepudding.wechat-php-sdk.qywechat#class");
        $options = array(
            'token' => 'tokenaccesskey', //填写应用回调接口的Token
            'encodingaeskey' => 'encodingaeskey', //填写回调加密用的EncodingAESKey
            'appid' => 'wx06693892f83ef14c', //填写高级调用功能的app id
            'appsecret' => 'gpVpxWBga2dtl2oi5vtRKktzUNpxQLro7zoqE81LLijLbKW_nmewbCZ7ZWj5jpn8', //填写高级调用功能的密钥
            'agentid' => '26', //应用的id
            'debug' => false, //调试开关
            'logcallback' => 'logg', //调试输出方法，需要有一个string类型的参数
        );
        $wx = new \QyWechat($options);
        //获取一级目录
        $dept_list = DeptModel::where("dept_parent", "")->select();
        // 创建一级部门，获取部门编号
        $dept_array = array();
        foreach ($dept_list as $dept) {
            $dept_data = array(
                "name" => $dept['dept_name'],
                "parentid" => 1,
                "order" => $dept['dept_id']
            );
            $created_dept = $wx->createDepartment($dept_data);
            if ($created_dept['errcode'] == 0) {
                $dept['id'] = $created_dept['id'];
                $dept_array[$dept['dept_name']] = $dept;//保存一级部门数组
            }
        }
        var_dump($dept_array);
        // 获取二级部门，创建二级部门
        $subdept_list = DeptModel::where("dept_parent", "<>", "")->select();
        foreach ($subdept_list as $subdept) {
            $subdept_data = array(
                "name" => $dept['dept_name'],
                "parentid" => $dept_array[$dept['dept_name']]['id'],
                "order" => $dept['dept_id']
            );
//           $created_subdept=$wx->createDepartment($subdept_data);
        }

    }

    /**
     * 将系统中的用户同步到微信企业号
     * (1)没有电话或微信的无法同步
     * （2）
     */
    public function syncusers()
    {

    }

    /**
     * 回调地址(http://dd.scetc.edu.cn/index.php/teacher/index/auth2)
     * 在回调模式下，企业不仅可以主动调用企业号接口，还可以接收成员的消息或事件。
     * 接收的信息使用XML数据格式、UTF8编码，并以AES方式加密。
     */
    public function auth2()
    {
        /*
         * （1）根据跳转网址获取code
         * 每次成员授权带上的code将不一样，code只能使用一次，10分钟未被使用自动过期
        */
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
        } else {
            echo "NO CODE";
        }
        //（2）使用code换取用户编号（userid）
        vendor("dodgepudding.wechat-php-sdk.qywechat#class");
        $options = array(
            'token' => 'sb8q0QcV053HAjGT', //填写应用回调接口的Token
            'encodingaeskey' => 'UGS4YIkSfAigOGxyhRHcdFfWtrQjD2pDlAGqFWlI5cN', //填写回调加密用的EncodingAESKey
            'appid' => 'wx06693892f83ef14c', //填写高级调用功能的app id
            'appsecret' => 'gpVpxWBga2dtl2oi5vtRKktzUNpxQLro7zoqE81LLijLbKW_nmewbCZ7ZWj5jpn8', //填写高级调用功能的密钥
            'agentid' => '26', //应用的id
            'debug' => true, //调试开关
            'logcallback' => 'logg', //调试输出方法，需要有一个string类型的参数
        );
        $wx = new \QyWechat($options);
//        var_dump($code);
//       var_dump( $wx->getServerIp());
//        $userid=$wx->getUserId($code);
//        var_dump($userid);
        $get_token_url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=' . $options['appid'] . '&corpsecret=' . $options['appsecret'] . '';
        $access_token = json_decode(file_get_contents($get_token_url));
        $access_tokens = $access_token->access_token;
        $get_Userid = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=' . $access_tokens . '&code=' . $code . '';
        $userIds = json_decode(file_get_contents($get_Userid));
        $users = $userIds->UserId;
        if ($userIds->UserId) {
            $table = new TeacherModel();
            $row = $table->where('teach_id', $users)->find();
            if ($row) {
                $roles = $row['teach_role'];
                Session::set('name', $row['teach_name']);
                Session::set('uid', $users);
                Session::set('roles', $roles);
                $ret = ['success' => 'true', 'message' => '教师认证成功'];
                return $this->success("登录成功", 'index');
            } else {
                $ret = ['success' => 'false', 'message' => '教师详细信息获取失败'];
                return $this->error("教师详细信息获取失败！", 'login');
            }
        } else if ($userIds->OpenId) {
            //print_r("error");
//            echo '<script language="JavaScript">alter("您当前未授权，请联系管理员！");</script>';
        }
        //（3）根据用户编号获取详细用户信息
//        $user=$wx->getUserInfo($userid['UserId']);
//        echo $user['name'];

    }

    /**
     * 个人信息
     */
    public function profile()
    {
        $view = new View();
        // 模板输出
        $view->name = Session::get('name');
        $view->uid = Session::get('uid');
        $view->roles = Session::get('roles');
        $view->current_term = $this->getCurrentTerm();
        // 获取登录者的个人详细信息
        $view->profile = TeacherModel::get(Session::get('uid'));
        return $view->fetch('profile');
    }

    /**
     * 返回某个教师的课表数据
     */
    public function lessontable()
    {
        //获取该学期教师的course课表记录
        $current_term = TermModel::where("default", 1)->find();
        // 获取教师编号，构造条件数组
        $tid = $_REQUEST['id'];
        $map = array();
        $map['term'] = $current_term['term_name'];
        $map['teach_id'] = $tid;
        $course_list = CourseModel::where($map)->select();
        // 获取该教师本学期的所有课表
        $table = array();
        foreach ($course_list as $row) {
            $table[$row["xing_qi_ji"]][$row['section']] = $row['class_name'] . "<br/>" . $row['course_name'] . "<br/>" . $row['class_room'] . "<br/>" . $row["week"] . $row["single_double"];
        }
        // 获取教师信息
        $teacher = TeacherModel::get($tid);
        // 获取听课安排
        $sid = $_REQUEST['sid'];
        $schedule = ScheduleModel::get($sid);
        // 模板输出
        $view = new View();
        $view->teacher = $teacher;
        $view->table = $table;
        $view->schedule = $schedule;
        $def_week = array(
            1 => "第1周",
            2 => "第2周",
            3 => "第3周",
            4 => "第4周",
            5 => "第5周",
            6 => "第6周",
            7 => "第7周",
            8 => "第8周",
            9 => "第9周",
            10 => "第10周",
            11 => "第11周",
            12 => "第12周",
            13 => "第13周",
            14 => "第14周",
            15 => "第15周",
            16 => "第16周",
            17 => "第17周",
            18 => "第18周",
            19 => "第19周",
            20 => "第20周"
        );
        $def_weekday = array(
            1 => "星期一",
            2 => "星期二",
            3 => "星期三",
            4 => "星期四",
            5 => "星期五",
            6 => "星期六",
            7 => "星期日"
        );
        $section = array(
            1 => "第一节",
            2 => "第二节",
            3 => "第三节",
            4 => "第四节",
            5 => "第五节",
        );
        $view->sc = $def_week[$schedule['week']] . " " . $def_weekday[$schedule['xing_qi_ji']] . " " . $section[$schedule['section']];
        $term=new TermModel();
        $current_term=$term->where("default",1)->find();
        $tc=new TermCalendar( $current_term['start'],$current_term['end']);
        $view->current_date=date('Y-m-d');
        $view->current_week = $tc->getWeek(date('Y-m-d')); //当前是第几周
        $view->today_weekday =date('N', strtotime(date('Y-m-d')));; //当前是星期几
        return $view->fetch('tblcourse');
    }

    /**
     * 根据教师编号tid，显示该教师的课表
     * @return string
     */
    public function lessontable2()
    {
        //获取当前学期
        $current_term = TermModel::where("default", 1)->find();
        // 条件数组
        $tid = $_REQUEST['id'];
        $map = array();
        $map['term'] = $current_term['term_name'];
        $map['teach_id'] = $tid;
        $course_list = CourseModel::where($map)->select();
        // 获取该教师本学期的所有课表
        $table = array();
        foreach ($course_list as $row) {
            $table[$row["xing_qi_ji"]][$row['section']] = $row['class_name'] . "<br/>" . $row['course_name'] . "<br/>" . $row['class_room'] . "<br/>" . $row["week"] . $row["single_double"];
        }
        // 获取教师信息
        $teacher = TeacherModel::get($tid);
        $view = new View();
        $view->teacher = $teacher;
        $view->table = $table;
        // 模板输出
        $term=new TermModel();
        $current_term=$term->where("default",1)->find();
        $tc=new TermCalendar( $current_term['start'],$current_term['end']);
        $view->current_date=date('Y-m-d');
        $view->current_week = $tc->getWeek(date('Y-m-d')); //当前是第几周
        $view->today_weekday =date('N', strtotime(date('Y-m-d')));; //当前是星期几
        return $view->fetch('course_table');
    }

    /**
     * 自定义听课时间
     * @return string
     */
    public function custom()
    {
        $def_week = array(
            1 => "第1周",
            2 => "第2周",
            3 => "第3周",
            4 => "第4周",
            5 => "第5周",
            6 => "第6周",
            7 => "第7周",
            8 => "第8周",
            9 => "第9周",
            10 => "第10周",
            11 => "第11周",
            12 => "第12周",
            13 => "第13周",
            14 => "第14周",
            15 => "第15周",
            16 => "第16周",
            17 => "第17周",
            18 => "第18周",
            19 => "第19周",
            20 => "第20周"
        );
        $def_weekday = array(
            1 => "星期一",
            2 => "星期二",
            3 => "星期三",
            4 => "星期四",
            5 => "星期五",
            6 => "星期六",
            7 => "星期日"
        );
        $def_section = array(
            1 => "第一节",
            2 => "第二节",
            3 => "第三节",
            4 => "第四节",
            5 => "第五节",
        );

        $teach_id = $_REQUEST['teach_id'];//教师编号
        $sid = $_REQUEST['sid'];//安排编号
        $wws=$_REQUEST['wws'];  //获取新日期
        $arr=explode("  ",$wws);
//        var_dump(array_filter($arr));
        $week=trim($arr[0]);
        $weekday=trim($arr[1]);
        $section=trim($arr[2]);
        $week=array_flip($def_week)[$week];
        $weekday=array_flip($def_weekday)[$weekday];
        $section=array_flip($def_section)[$section];
        //(1)原先的安排取消
        $user = ScheduleModel::get($sid);
        $user->finished = '取消';
        $conuncilor=$user->conuncilor;
        $user->save();

        // （2）根据时间查找对应教师的课表记录
        $conditions=array();
        $conditions["teach_id"]=$teach_id;
        $conditions["xing_qi_ji"]=$weekday;
        $conditions["section"]=$section;
        $course= CourseModel::where($conditions)->find();
        $ret = ['success' => false, 'message' => '设置听课时间错误！'];
        if($course){
            if (($week % 2 == 0 && $course['single_double'] == '单') || ($week % 2 == 1 && $course['single_double'] == '双')) {
                // 单双周不同
                $ret = ['success' => false, 'message' => '单双周错误！'];
            } else {
                // 如果week在上课周内
                if (in_array($week, getWeeksByString($course['week']))) {
                    $ret = ['success' => true, 'message' => '设置新的听课任务成功！'];
                    //（3）新增安排记录
                    $record=new ScheduleModel();
                    $record->term= $this->getCurrentTerm() ;
                    $record->dept_name=$course['dept_name'];
                    $record->teach_id=$course['teach_id'];
                    $record->teach_name=$course['teach_name'];
                    $record->time="";
                    $record->week=$week;
                    $record->xing_qi_ji=$weekday;
                    $record->section=$section;
                    $record->class_name=$course['class_name'];
                    $record->class_room=$course['class_room'];
                    $record->course_name=$course['course_name'];
//        $record->teacher_info="";
//        $record->stu_due_number="";
                    $record->conuncilor=Session::get('uid');
                    $record->save();
                }else{
                    $ret = ['success' => false, 'message' => '该对应周次没有课'];
                }
            }
        }else{
            $ret = ['success' => false, 'message' => '该对应时间没有课'];
        }
        return json($ret);
    }

    /**
     * 返回菜单项目
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function actionbuttons(){
        $_id=$_POST['sid'];//听课安排编号
        $sc=ScheduleModel::get($_id);
        $finished=$sc['finished'];
        $locked=$sc['locked'];
        //
        $buttons=array();
        $button1=array(
            "text"=>"查看教师课表"
        );
        $buttons[]=$button1;
        // 没有锁定情况下
        if($locked=='未锁定' || is_null($locked)){
            if($finished=="完成"){
                $button21=array(
                    "text"=>"未完成听课",
                );
                $button23=array(
                    "text"=>"取消听课",
                );
                $buttons[]=$button21;
                $buttons[]=$button23;
            }elseif(is_null($finished)){
                $button21=array(
                    "text"=>"完成听课",
                );
                $button23=array(
                    "text"=>"取消听课",
                );
                $buttons[]=$button21;
                $buttons[]=$button23;
            }else{
                $button21=array(
                    "text"=>"完成听课",
                );
                $button23=array(
                    "text"=>"未完成听课",
                );
                $buttons[]=$button21;
                $buttons[]=$button23;
            }
            $button3=array(
                "text"=>"填写听课记录",

            );
            $buttons[]=$button3;
        }
        return json($buttons);
    }
}
