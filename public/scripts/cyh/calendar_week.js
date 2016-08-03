/**
 * Created by Administrator on 2016/8/1.
 */
var grid_id='#datagrd';//表单名
var url_get='http://10.127.98.246/index.php/admin/termclendar/get_week_josn';
var week_calender=[[
    {field:'1',title:'星期一',width:70},//filed:该列对应字段名   title那一列
    {field:'2',title:'星期二',width:70},
    {field:'3',title:'星期三',width:70},
    {field:'4',title:'星期四',width:70},
    {field:'5',title:'星期五',width:70},
    {field:'6',title:'星期六',width:70},
    {field:'7',title:'星期天',width:70},
]];
function initGrid(grid_id,url_get,week_calender){
    $(grid_id).datagrid({
        url: url_get,
        method: 'get',
        showGroup:false,
    });
}
$(document).ready(function () {
    //当整个页面全部载入后才执行
    initGrid(grid_id,url_get,week_calender);
    //console.log(111111111111);
});