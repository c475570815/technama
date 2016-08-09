<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/25
 * Time: 12:11
 */

namespace app\common;

/**
 *  学期日历类
 * Class TermCalendar
 * @package app\common
 */
class TermCalendar
{
    //public $weeks;
    // private $_week_start;
    //public $holiday = [];//结束周次
    //private $_current_date; //当前学期
    //private $_week_end; //当前第X周
    private $_start_date; //学期开始日期     类型 ：字符串:"year-month-day"
    private $_end_date;//学期结束日期    类型 ：字符串:"year-month-day"
    private $_overall_week;//学期总周数
    private $_overall_day;//学期总天数
    private $_vacation_data; //假期数据 [[date=>'2014-5-1',comment=>'劳动节'],[date=>'2014-10-1',comment=>'国庆节']]
    private $_arrWeekday = array(0 => '星期天', 1 => '星期一', 2 => '星期二', 3 => '星期三', 4 => '星期四', 5 => '星期五', 6 => '星期六');
    private $_arrMonth = array(
        '1' => '一月',
        '2' => '二月',
        '3' => '三月',
        '4' => '四月',
        '5' => '五月',
        '6' => '六月',
        '7' => '七月',
        '8' => '八月',
        '9' => '九月',
        '10' => '十月',
        '11' => '十一月',
        '12' => '十二月',);
    private $_overall_object;      //当前学期的所有日期（数组  对象）
    private $_overall_josn;  ///适合easyui表格josn格式
    /**
     * 通过给出的开学日期和结束日期 构造出基本日期数组
     * TermCalendar constructor.
     * @param $startday 开学日期 2012-1-1
     * @param $endday   结束日期
     */
    public function __construct($startday, $endday)
    {
        $reserve_day = date('N',strtotime($startday));
        //print_r("开始周次:  $reserve_day");
        $dt_start = new \DateTime($startday);
        $dt_end = new \DateTime($endday);
        $days = $dt_start->diff($dt_end)->format("%a")+1;
        $weeks=0;
        $all_weeks = array();
        for ($i = 0; $i < $days; $i++) {
            $week = array();
            if ($i == 0) {
                for ($j = $reserve_day; $j <= 7 && $i<$days; $j++) {
                    $s = 'P' . $i . 'D';//i为增加天数
                    $interval = new \DateInterval($s);
                    $current_dt=  clone $dt_start;
                    $week[$j] = $current_dt->add($interval);//增加
                    $i++;
                }
            } else {
                for ($j = 1; $j <= 7 && $i<$days; $j++) {
                    $s = 'P' . $i . 'D';
                    $interval = new \DateInterval($s);
                    $current_dt=  clone $dt_start;
                    $week[$j] = $current_dt->add($interval);
                    $i++;
                }
            }
            $weeks++;
            $i--;
            $all_weeks[$weeks] = $week;
        }
        $this->_start_date=$startday;
        //print_r("开始日期:  $this->_start_date");
        $this->_end_date=$endday;
        //print_r("结束日期:  $this->_end_date");
        $this->_overall_week=$weeks;
        //print_r("总周次:  $this->_overall_week");
        $this->_overall_object=$all_weeks;
        $this->_overall_day=$days;
        //dump($all_weeks);
    }

    /**
     * 根据日期字符串返回该日期是星期几
     * @param $data  日期，如2012-1-2  Date（）
     * @return string  数字
     */
    public function data_to_weekday($data)
    {
        $data = explode("-", $data);
        $year_value = $data[0];
        $month_value = $data[1];
        $day_value = $data[2];
        //构建代码数组
        $x = substr($year_value, 2, 2);//截取取年份的后两位数用于计算年份代码
        $year_code = ($x / 4 + $x) % 7;
        $month_code_arr = array();
        $day_code = $day_value;
        if ($this->is_leapyear($year_value)) {//闰年
            $month_code_arr = [5, 1, 2, 5, 0, 3, 5, 1, 4, 6, 2, 4];
        } else {//平年
            $month_code_arr = [6, 2, 2, 5, 0, 3, 5, 1, 4, 6, 2, 4];
        }
        //构建代码数组

        //取值
        $week_code = ($year_code + $month_code_arr[$month_value - 1] + $day_code) % 7;
        $week = "错误周次代码";
        switch ($week_code) {
            case 0:
                $week = "7";
                break;
            case 1:
                $week = "1";
                break;
            case 2:
                $week = "2";
                break;
            case 3:
                $week = "3";
                break;
            case 4:
                $week = "4";
                break;
            case 5:
                $week = "5";
                break;
            case 6:
                $week = "6";
                break;
            default:
                $week = "错误周次代码";
                break;
        }
        return $week;
    }
    /* 给出月份返回天数
     * cal_days_in_month()
     */

    /**给出年份判断是否是闰年
     * @param $year_value  被判断年数
     * @return bool  判断结果
     */
    private function is_leapyear($year_value)
    {
        if (($year_value % 4 == 0 && $year_value % 100 != 0) || ($year_value % 400 == 0)) {
            return true;
        } else {
            return false;
        }
    }

    /**取得当前对象(学期)的总天数
     * @return string  14
     */
    public  function  get_allday(){

    return $this->_overall_day;

}

    /**取得当前对象(学期)的总周数
     * @return int
     */
    public function  get_allweek(){

        return $this->_overall_week;
    }

    /**添加假期 到假期数组  数组格式为   _vacation_data[2015-9-1]=放假原因
     * @param $vacation_day 2015-9-1
     * @param $reason   “劳动节”
     */
    public function  add_vacation($vacation_day,$reason){
        if(!isset($this->_vacation_data[$vacation_day])){
            $this->_vacation_data[$vacation_day]="$reason";
        }
    }

    /**给出一个日期 16-09-01    得到其在本学期的周次
     * @param $date  给出日期 16-09-01
     * @return int 返回周数
     */
    public function  get_week($date){
      //$key = array_search('vegetable', $items);  返回key
        $all_array = $this->get_allarr();
        $reserve_day = date('N',strtotime($this->_start_date));
        for ($i = 1;isset($all_array [$i]); $i++){
            if($i==1){
                $j=$reserve_day;
            }
            else{
                $j=1;
            }
            for(;$j<=7;$j++){
                if(isset($all_array[$i][$j])) {
                    $a = $all_array[$i][$j]->format('Y-m-d H:i:s');
                }
                else{
                    break;
                }
                //print_r(date('y-m-d',strtotime($a))."\n");
                if($date==date('y-m-d',strtotime($a))){
                    return $i;
                }
            }
        }
    }

    /**取得对象的所有日期(数组)
     * @return array
     */
    public function get_allarr()
    {
        return $this->_overall_object;

    }

    /**把做好的 数组转化为适合esayui 表格的josn格式数据
     * @return mixed   esayui 表格的josn格式数据
     */
    public function  objct_tojosn(){
        $calendar = $this->_overall_object;
        $all_array = $this->get_allarr();
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
            $a=0;
            for ( ;;$j++) {//$j为星期几
                if(!isset($all_array [$i][$j])){
                    $week['maxday']=date('Y-m-d',strtotime($a));
                    break;
                }
                $a = $all_array[$i][$j]->format('Y-m-d H:i:s');//对象转换字符串
                if($j==1||($j==$p&&$i==1)){
                    $week['minday']=date('Y-m-d',strtotime($a));
                }
                switch ($j) {
                    case "1":
                        $day='Monday';
                        break;
                    case "2":
                        $day='Tuesday';
                        break;
                    case "3":
                        $day='Wednesday';
                        break;
                    case "4":
                        $day='Thursday';
                        break;
                    case "5":
                        $day='Friday';
                        break;
                    case "6":
                        $day='Saturday';
                        break;
                    case "7":
                        $day='Sunday';
                        break;
                }
                $week[$day]=date('Y-m-d',strtotime($a));//在字符串中取年月日
            }
            $week['group'] = "第" . $i . "周";
            $list[] = $week;
        }
        $total=count($this->get_allarr());
        $this->_overall_josn = json(['total' => $total, 'rows' => $list]);
        return $this->_overall_josn;
    }

    /**通过给出的周次得到一周的josn   格式
     * @param $week   第几周
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml   符合easy ui 表格的josn  格式
     */
    public function get_weekjosn($week)
    {
        $calendar = $this->_overall_object;
        $all_array = $this->get_allarr();
        //print_r($all_array);
        $re_array = array();
        $i = $week;
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
        for (; $j <= 7; $j++) {//$j为星期几
            $a = $all_array[$i][$j]->format('Y-m-d H:i:s');//对象转换字符串
            //print_r(date('y-m-d',strtotime($a)));
            $week[$j] = date('y-m-d', strtotime($a));//在字符串中取年月日
            //print_r($week);
        }
        $week['group'] = "第" . $i . "周";
        //print_r($week);
        $total = 1;
        $list[] = $week;
        $week_josn = json(['total' => $total, 'rows' => $list]);
        // print_r($week_josn);
        return $week_josn;
    }
    /**给出月份和是否是闰年(bool) 返回当月天数
     * @param $month
     *
     * @param $leap
     * @return int|string
    31dasdwa32132sd12    */
    private function month_to_day($month, $leap)
    {
        $day = "月份参数有误,月份转换失败（month_to_day）";
        switch ($month) {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                $day = 31;
                break;
            case 2:
                if ($leap) {
                    $day = 29;
                } else {
                    $day = 28;
                }
                break;
            case 4:
            case 6:
            case 9:
            case 11:
                $day = 30;
                break;
            default:
                print_r("月份参数有误,月份转换失败（month_to_day）");
        }
        return $day;
    }

    /**
     * 显示一个HTML格式的年历
     */
    public function toHTML(){
        $header="<table width='700px' border='1px'>";
        $header=$header."<tr>"."<th>周一</th><th>周二</th><th>周三</th><th>周四</th><th>周五</th><th>周六</th><th>周日</th>"."</tr>";
        foreach ($this->_overall_day as $week){
            for($i=1;$i<=7;$i++){

            }
        }
        $tbody="";
        $header=$header.$tbody;
        $header=$header."<table>";
        return $header;
    }

}