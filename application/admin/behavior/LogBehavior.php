<?php
namespace app\admin\behavior;
use app\common\model\LogsModel;
use think\Request;
use think\Session;
/**
 * 定义行为类,执行入口方法run
 * 行为定义完成后，就需要绑定到某个标签位置才能生效，否则是不会执行
 */

/**
 * 在每次action_end后执行，用来生成访问日志
 * @package app\admin\behavior
 */
class LogBehavior
{
    public function run(&$params)
    {
        $request = Request::instance();
        //Log::record('guowushi test');
        /*   import('ORG.Net.IpLocation');// 导入IpLocation类
        $Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
        $area = $Ip->getlocation('203.34.5.66'); // 获取某个IP地址所在的位置*/
        $data['userid'] = Session::get('uid');
        $data['url'] =$request->url(true) ;
        $data['module'] =$request->module();
        $data['controller'] =$request->controller();
        $data['action'] = $request->action();
        $data['method'] =$request->method();
        $data['type'] =$request->type();
        $data['isajax'] =$request->isAjax();
        //$data['param'] =implode(",",$request->param());
        $data['ip'] =$request->ip();
        //$data['from'] =get_client_ip();
         $data['atime'] =  date("Y-m-d H:i:s", time());
        $mo=new LogsModel();
        $mo->save($data);

    }
}