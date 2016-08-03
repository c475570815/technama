/**
 * Created by guowushi on 2016/7/20.
 */
/**
 *
 */
var grid='#datagrd';
var url='http://10.127.98.246/index.php/admin/classes/ac1';
var columns_def=[[
    {field:'dept_name',title:'所属系部',sortable:true,width:'30%'},
    {field:'class_name',title:'班级名称',sortable:true,width:'10%'},
    {field:'class_room',title:'班级固定教室',sortable:true,width:'10%'},
    {field:'class_supervisor',title:'班级导师编号',sortable:true,width:'10%'},
    {field:'calss_adviser',title:'班级班主任',sortable:true,width:'10%'}

]];
function initGrid(grid,url,columns_def){
    $(grid).datagrid({
        url:url,
        method:'post',
        title:"详细信息",
        singleSelect:true,
        collapsible:false,
        pagination:true,
        rownumbers:true,
        height:345,
        columns:columns_def
    });
}
$(document).ready(function () {
    //当整个页面全部载入后才执行
    initGrid(grid,url,columns_def);
});

/**
 * 查询
 */
function query(){
    var search_filter=$('#frm_search').serializeJson();
    $('#datagrd').datagrid(
        'load',
        search_filter
    );
}