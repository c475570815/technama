<?php
//定义命名空间
//也就是说类的完整名字app.test.controller.Index
namespace  app\cyh\controller;
use think\View;//视图模块使用
//use app\cyh\Model\Information;//
use app\cyh\model\Tbl_teacher;
use think\Db;//类库方法
use app\cyh\test\Test;//模型类使用
use think\Controller;
use app\common\model\TeaModel;
use app\common\TermCalendar;
/*定义controller类，一个类中有多个方法(action)
类名与文件名一至且首字母大写
方法名应小写*/
class Index extends Controller
{
    public function demo()
    {
        // echo "hello word";
        $list = tbl_teacher::all();
        //;通过模型获取数据
        $view = new View();
        //实例化视图类
        $view->data = $list;
        //$view->name='ThinkPHP';
        $view->email = 'thinkphp@qq.com';
       // return $view->fetch('fjs@index/teacher');//调用与方法同名的视图
        return $view->fetch();
    }

    public function get_josn()
    {
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 1;
        $order = isset($_GET['order']) ? $_GET['order'] : 10;
        //获取客户端传递过来的参数 page=2&rows=20
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;//使用post方式取值如果是空的则page=1
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        //记录的总数
        $total = intval(tbl_teacher::count());
        $start = ($page - 1) * $rows;
        if ($sort == 1 && $order == 10)
            $list = Db::table('tbl_teacher')->limit($start, $rows)->select();
        else
            $list = Db::table('tbl_teacher')->order($sort, $order)->limit($start, $rows)->select();
        return json(['total' => $total, 'rows' => $list]);
    }

    public function datagrid()
    {
        //$list = Information::all();
        $view = new View();//实例化视图
        return $view->fetch('datagrid');//调用视图
    }

    public function Tablsf()
    {
        $view = new View();//实例化视图
        return $view->fetch('tabs');//调用视图
    }

    public function form()
    {
        $view = new View();//实例化视图
        return $view->fetch();//调用视图
    }

    /**
     * 根据身份、编号密码做登录判断
     */
    public function pass($id, $password)
    {
        $statu=isset($_GET['statu']) ? $_GET['statu'] : "";
        $id = isset($_GET['id']) ? $_GET['id'] : "";
        $password = isset($_GET['password']) ? $_GET['password'] : "";
        $table=new TeaModel();
        //$wher=$this->getfindwhere($statu,$id,$password);
        $view=new View();
        if ($table->where('teach_id',$id)->where('teach_role',$statu)->where('teach_pass',$password)->find()) {
            if($statu=="Administrator")
                return $view->fetch('fjs@index/admin');
            elseif ($statu=="supervisor")
                return  $view->fetch('cyh@tea/form');
            elseif ($statu=="teacher")
                return  $view->fetch('cyh@index/tabs');
        } else {
            return $this->error("登录失败", 'form');
        }
    }
    //teach_role
    /**
     *   带两个字段参数构造查询where语句  @param $
     * 构造 原声sql语句 ;
     */
    public function  getfindwhere($status,$id,$password){
        $arry="teach_id = $id AND teach_role LIKE '".$status."'";// AND teach_pass = $password
       //dump($arry);
        return $arry;
    }

    /**
     * TemCalendar  根据年月日判断星期 测试
     */
    public function  test(){
        $a=new TermCalendar();
        $arr=["year"=>2016,"month"=>3,"day"=>20];
        return $a->data_to_weekday($arr,$arr);
    }
    /**
     * TemCalendar 根据开始结束日期得到学期。日期数组
     */
    public function  test2(){
        $startday = '2015-9-10';
        $endday = '2016-1-20';
        $a=new TermCalendar($startday,$endday);
       // $b=$a->add_vacation(2,2,"劳动节");
        $b="15-12-03";
        dump($a->get_week($b));
        //dump($b);
        //return $b;
}
    public function get_cal_josn()
    {
        $startday = '2015-9-10';
        $endday = '2016-1-20';
        $calendar = new TermCalendar($startday, $endday);
        $all_array = $calendar->get_allarr();
        $re_array=array();
        for ($i = 1;isset($all_array [$i]); $i++) {//$i为周次
            //为开始日期为星期几做判断赋值
            if ($i == 1) {
                for ($p = 1; $p <= 7; $p++) {
                    if (isset($all_array [1][$p])) {
                        $j = $p;
                        break;
                    } else {
                        continue;
                    }
                }
            } else {
                $j = 1;
            }
            //为开始日期为星期几做判断赋值
            $week = array();//置空数组
            for ( ;isset($all_array [$i][$j]);$j++) {//$j为星期几
                $a = $all_array[$i][$j]->format('Y-m-d H:i:s');
                $week[$j]=date('y-m-d',strtotime($a));
            }
            $week['group'] = "第" . $i . "周";
            //dump($week);
            $list[$i] = $week;
        }
        //dump($list);
        $total=$calendar->get_allweek();
        //dump(json(['total' => $total,'rows' => $list]));
        return; json(['total' => $total,'rows' => $list]);

    }
}      //$a=Db::table('tbl_teacher')->where('tech_id=010001 AND teach_pass=500210')->select();
?>