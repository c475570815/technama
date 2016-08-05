/**
 * Created by Administrator on 2016/7/29.
 */
var grid_id='#datagrd';//表单名
var url_get='http://10.127.98.242/index.php/admin/termclendar/get_cal_josn';
var week_calender=[[
    {field:'Monday',title:'星期一',width:100},//filed:该列对应字段名   title那一列
    {field:'Tuesday',title:'星期二',width:100},
    {field:'Wednesday',title:'星期三',width:100},
    {field:'Thursday',title:'星期四',width:100},
    {field:'Friday',title:'星期五',width:100},
    {field:'Saturday',title:'星期六',width:100},
    {field:'Sunday',title:'星期天',width:100},
]];
function initGrid(grid_id,url_get,week_calender){
    $(grid_id).datagrid({
        url: url_get,
        method: 'get',
        showGroup:false,
        rownumbers:true,
        singleSelect:true,
    });
}
$(document).ready(function () {
    //当整个页面全部载入后才执行
    initGrid(grid_id,url_get,week_calender);
    $('#datagrd').datagrid({
        rowStyler:function(index,row){
            if(row.maxday!=null) {
                var mday = row.maxday.split("-");
                var miday = row.minday.split("-");
                console.log(row.minday);
                var nowdate = new Date("2015","09","01");//参数为空时为现在时间
                var minday=new Date(miday[0],miday[1],miday[2]);
                var maxday = new Date(mday[0], mday[1], mday[2]);//把年月日分别作为参数使用
                if (nowdate>=minday&&nowdate<=maxday) {
                    return 'background-color:#96CDCD;';
                }
            }
        }
    });
});

