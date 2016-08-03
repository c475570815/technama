/**
 * Created by Administrator on 2016/7/21.
 */
var grid = '#datagrd';
var url = 'http://10.127.98.246/index.php/admin/classes/ac1';
var columns_def = [[
    {field: 'dept_name', title: '所属系部', sortable: true, width: '30%'},
    {field: 'class_name', title: '班级名称', sortable: true, width: '10%'},
    {field: 'class_room', title: '班级固定教室', sortable: true, width: '10%'},
    {field: 'class_supervisor', title: '班级导师编号', sortable: true, width: '10%'},
    {field: 'calss_adviser', title: '班级班主任', sortable: true, width: '10%'}

]];
function initGrid(grid, url, columns_def) {
    $(grid).datagrid({
        url: url,
        method: 'post',
        title: "详细信息",
        singleSelect: true,
        collapsible: false,
        pagination: true,
        rownumbers: true,
        height: 345,
        columns: columns_def
    });
}
$(document).ready(function () {
    //当整个页面全部载入后才执行
    initForm();
});
function initForm() {
    $('#dept_name').combobox({
        url: 'http://10.127.98.246/index.php/mcw/add/get_classes',
        valueField: 'class_name',
        textField: 'class_name'
    });

}
/**
 * 通过Ajax方式保存
 */
function saveForm() {
    $('#ff').form({
        url: 'http://10.127.98.246/index.php/mcw/Add/save',
        onSubmit: function () {
            // 有效性验证
            var classname = $("#class_name").val();
            if (classname == '') {
                return false;
            }
        },
        success: function (data) {
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
function query() {
    var search_filter = $('#frm_search').serializeJson();
    $('#datagrd').datagrid(
        'load',
        search_filter
    );
}