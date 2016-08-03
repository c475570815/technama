/**
 * Created by FuJinsong on 2016/7/21.
 */

var grid='#datagrd';
var url='http://10.127.98.246/index.php/admin/schedule/ac1';
var columns_def=[[
    {field:'term',title:'学期',sortable:true},
    {field:'time',title:'时间',sortable:true},
    {field:'week',title:'周次',sortable:true},
    {field:'xing_qi_ji',title:'星期',sortable:true},
    {field:'section',title:'节次',sortable:true},
    {field:'class_name',title:'班级名称',sortable:true},
    {field:'class_room',title:'班级教室',sortable:true},
    {field:'teacher',title:'教室',sortable:true},
    {field:'course_name',title:'课程名称',sortable:true},
    {field:'teacher_info',title:'教师信息',sortable:true},
    {field:'dept_name',title:'系部名称',sortable:true},
    {field:'stu_due_number',title:'学生人数',sortable:true},
    {field:'conuncilor',title:'督导',sortable:true},
    {field:'state',title:'状态',sortable:true},


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
    initForm();
});
function initForm(){
    $('#dept_name').combobox({
        url: 'http://10.127.98.246/index.php/admin/schedule/deptinfo',
        valueField: 'dept_name',
        textField: 'dept_name'
    });

}
/**
 * 通过Ajax方式保存
 */
function saveForm(){
    $('#ff').form({
        url:'http://10.127.98.246/index.php/admin/schedule/save',
        onSubmit: function(){
            // 有效性验证
            var classname=$("#class_name").val();
            if(classname==''){
                return false ;
            }
        },
        success:function(data){
            var data = eval('(' + data + ')');  // change the JSON string to javascript object
            $.messager.alert('Info', data.message, 'info');
        }
    });
    $('#ff').submit();
}
function clearForm() {
    $('#ff').form('clear');
}
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