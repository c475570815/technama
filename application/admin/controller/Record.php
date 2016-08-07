<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/24
 * Time: 13:21
 */

namespace app\admin\controller;

use app\admin\component\RecordDataGrid;
use think\Controller;
use app\common\model\RecordModel;
use app\common\model\DeptModel;
use app\common\model\TeaModel;
use think\View;
use think\Config;

class  Record extends Controller
{


    protected $beforeActionList = [
        // 'before'=>  ['except'=>'login,isLogined,authentication']
    ];

    /**
     * Record constructor.
     */
    public function __construct()
    {

    }

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

        $dept = new DeptModel();
        $deptList = $dept->select();
        $view = new View();
        $view->assign("dept", $deptList);
        return $view->fetch('datagrid');

    }

    /**
     * 为Grid返回JSON格式数据
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function getlist()
    {
        $request = \think\Request::instance();
        //if($request->method()=='POST') {
        $dict_grid = new RecordDataGrid();
        return $dict_grid->dataGridJson();
        //}
    }

    /**
     * 跳转课程评估界面
     */
    public function add()
    {
        $dept = new DeptModel();
        $deptList = $dept->distinct(true)->field('dept_name')->select();
        $view = new View();
        $max = RecordModel::max('id');
        $view->assign("id", $max);
        $view->assign("dept", $deptList);
        return $view->fetch('evaluate');
    }

    /**
     * 听课记录保存
     */
    public function save()
    {
        $form_data = $_GET['data'];
        $form_data['teacher_id'] = $this->convert($form_data["teacher"]);
        $ret = array(
            'success' => false, 'message' => '添加失败'
        );
        $new_record = new RecordModel();
        if (!$new_record->isExist($form_data['id'])) {
            $new_record->data($form_data);
            $new_record->save();
            $ret = ['success' => true, 'message' => '提交听课记录成功'];
        } else {
            $ret = ['success' => false, 'message' => '听课编号已存,在提交失败！'];
        }
        return json($ret);
    }

    /**
     * 根据姓名查询教师编号
     */
    private function convert($name)
    {
        $tea = new TeaModel();
        $id = $tea->where('teach_name', '=', $name)->column('teach_id');
        return implode(',', $id);
    }

    public function sendmail($to, $subject, $body)
    {
        vendor("PHPMailer.PHPMailerAutoload");
        $mail = new \PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPDebug = 3;
        $mail->CharSet = 'UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
        $mail->SMTPAuth = true; //开启认证
        $mail->SMTPSecure = 'ssl';      // 使用TLS加密，也支持ssl
        $mail->Priority = 3;   // 设置邮件优先级 1高, 3正常（默认）, 5低 
        $mail->Port = Config::get('THINK_EMAIL.SMTP_PORT');                  // TCP 端口
        $mail->Host = Config::get('THINK_EMAIL.SMTP_HOST');
        $mail->Username = Config::get('THINK_EMAIL.SMTP_USER');;
        $mail->Password = Config::get('THINK_EMAIL.SMTP_PASS');;
        $mail->AddReplyTo(Config::get('THINK_EMAIL.REPLY_EMAIL'), Config::get('THINK_EMAIL.REPLY_NAME'));//回复地址
        $mail->From = Config::get('THINK_EMAIL.FROM_EMAIL');
        $mail->FromName = Config::get('THINK_EMAIL.FROM_NAME');
        $mail->AddAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->WordWrap = 80; // 设置每行字符串的长度
        //$mail->AddAttachment("f:/test.png"); //可以添加附件
        $mail->IsHTML(true);
        if ($mail->Send()) {
            return true;
        } else {
            return false;
        }
        $mail->Send();

    }

    public function sendmail2()
    {
        $to = "1058759007@qq.com";
        $body = "<h1>phpmail演示</h1>这是php点点通（<font color=red>www.phpddt.com</font>）对phpmailer的测试内容";
        $subject = "phpmailer测试标题";
        sendmail($to, $subject, $body);
        echo '邮件已发送';
    }

    /**
     * 发送短信息
     */
    public function sms()
    {

        // 初始化REST SDK
        $accountSid = Config::get('yuntongxun.ACCOUNT_SID');
        $accountToken = Config::get('yuntongxun.AUTH_TOKEN');
        $appId = Config::get('yuntongxun.APPID');
        $serverIP =Config::get('yuntongxun.REST_URL');
        $serverPort = Config::get('yuntongxun.PORT');
        $softVersion = Config::get('yuntongxun.VERSION');

        vendor("yuntongxun.CCPRestSDK");
        $rest = new \REST($serverIP, $serverPort, $softVersion);
        $rest->setAccount($accountSid, $accountToken);
        $rest->setAppId($appId);
        $result = $rest->sendTemplateSMS('18942891954', "你有听课安排,60", '1');
        if ($result == NULL) {
            $ret = ['success' => false, 'message' => '错误'];
        }
        if ($result->statusCode != 0) {
            $ret = ['success' => false, 'message' => $result->statusMsg];
            //TODO 添加错误处理逻辑
        } else {
            echo "Sendind TemplateSMS success!<br/>";
            // 获取返回信息
            $smsmessage = $result->TemplateSMS;
            $ret = ['success' => true, 'message' => $smsmessage->dateCreated.' 消息发送成功！'];

        }
        return json($ret);
    }


    /**
     * 发送模板短信
     * @param to 手机号码集合,用英文逗号分开
     * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
     * @param $tempId 模板Id
     */
    function sendTemplateSMS($to, $datas, $tempId)
    {
        // 初始化REST SDK
        $accountSid = Config::get('yuntongxun.ACCOUNT_SID');
        $accountToken = Config::get('yuntongxun.AUTH_TOKEN');
        $appId = Config::get('yuntongxun.APPID');
        $serverIP =Config::get('yuntongxun.REST_URL');
        $serverPort = Config::get('yuntongxun.PORT');
        $softVersion = Config::get('yuntongxun.VERSION');

        vendor("yuntongxun.CCPRestSDK");
        $rest = new \REST($serverIP, $serverPort, $softVersion);
        $rest->setAccount($accountSid, $accountToken);
        $rest->setAppId($appId);
        $result = $rest->sendTemplateSMS($to, $datas, $tempId);
        if ($result == NULL) {
            $ret = ['success' => false, 'message' => '错误'];
        }
        if ($result->statusCode != 0) {
            $ret = ['success' => false, 'message' => $result->statusMsg];
            //TODO 添加错误处理逻辑
        } else {
            echo "Sendind TemplateSMS success!<br/>";
            // 获取返回信息
            $smsmessage = $result->TemplateSMS;
            $ret = ['success' => true, 'message' => $smsmessage->dateCreated.' 消息发送成功！'];

        }
        return json($ret);
    }
}