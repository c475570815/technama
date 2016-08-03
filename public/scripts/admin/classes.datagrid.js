/**
 * Created by guowushi on 2016/7/20.
 */
/**
 *
 */
var grid_id = '#datagrd';
var search_form_id="#frm_search";
var download_form_id="#frm_download";
var url_get = 'http://10.127.98.246/index.php/admin/classes/index';
var url_remove = 'http://10.127.98.246/index.php/admin/classes/remove';
var url_update = 'http://10.127.98.246/index.php/admin/classes/update';
var url_export = 'http://10.127.98.246/index.php/admin/classes/download';
var pk_field = 'class_name';
var grid_options;
var columns_def = [[
    {field: 'chkbox', checkbox: true},
    {field: 'dept_name', title: '所属系部', sortable: true},
    {field: 'class_name', title: '班级名称', sortable: true},
    {field: 'class_room', title: '班级固定教室', sortable: true},
    {field: 'class_supervisor', title: '班级导师编号', sortable: true},
    {field: 'calss_adviser', title: '班级班主任', sortable: true},
    {field:"operation", title: '操作', formatter:formatOptColumn }
]];
function initGrid(grid, url, columns_def) {
    $(grid_id).datagrid({
        url: url_get,
        idField:pk_field,
        method: 'post',
        title: "详细信息",
        singleSelect: false,
        collapsible: false,
        pagination: true,
        fitColumns:true,
        rownumbers: true,
        columns: columns_def
    });
}
/**
 * 当整个页面全部载入后才执行
 */
$(document).ready(function () {
    initGrid(grid_id, url_get, columns_def);
});
/**
 * 定义列的显示
 * @param val
 * @param row
 * @returns {*}
 */
function formatOptColumn(val,row,index){
    var updateUrl = url_update + "/pk/" + row.class_name;

       return "<a href='"+updateUrl+"' target='_self'> 操作 </a>";

}
/**
 * 查询
 */
function query() {
    var search_filter = $(search_form_id).serializeJson();
    var acion = {'action': 'search'};
    var postdata = $.extend({}, search_filter, acion);
    $(grid_id).datagrid(
        'load',
         postdata
    );
    grid_options = $(grid_id).datagrid('options').queryParams;
    console.log(grid_options);
}
/**
 * 删除选中的记录
 */
function removeRecord() {
    var checkedItems = $(grid_id).datagrid('getChecked');//返回选中记录的数组
    if (checkedItems.length == 0) {
        $.messager.alert("提示", "请选择要删除的行！", "info");
        return;
    }
    //将数组中的主健值放到一个数组中 ,['软件1','网络1']
    var removeID = [];
    $.each(checkedItems, function (index, item) {
        removeID.push(item.pk_field);
    });
    $.messager.confirm('提示', '是否删除选中数据?', function (r) {
        if (!r) {
            return;
        }
        //Ajax提交
        $.ajax({
            type: "POST",
            url: url_remove,
            data: {id: removeID},//传递给服务器的参数
            success: function (jsonresult) {
                reload();
                if (jsonresult.isSuccess == true) {
                    $.messager.alert("提示", jsonresult.message, "info");
                } else {
                    $.messager.alert("提示", jsonresult.message, "info");
                    return;
                }
            }
        });
    });
    //console.log(names.join(","));
}
/**
 * 刷新网格
 */
function reload() {
    $(grid_id).datagrid('clearSelections');
    $(grid_id).datagrid('reload');

}
/**
 * 编辑
 */
function edit() {
    var rows = $(grid_id).datagrid('getSelections');
    if (rows.length != 1) {
        $.messager.show({
            title: "",
            msg: "请选择一条记录"
        });
        return;
    }
    var row = $(grid_id).datagrid('getSelected');
    var idValue = row[pk_field];
    console.log(idValue);
    var updateUrl = url_update + "/pk/" + idValue;
    location.href = updateUrl;
}

/**
 *  下载
 */
function exportXls() {
    var grid_options = $(grid_id).datagrid('options').queryParams;
    var acion = {'action': 'export'};
    var postdata = $.extend({}, grid_options, acion);
    $(download_form_id).form('submit', {
        url: url_export,
        queryParams: postdata,
        onSubmit: function () {
        },
        success: function (data) {
            alert(data);
        }
    });
}
/**
 * 打印
 */
function printGrid(){
   // $(grid_id).print();
    window.open("http://10.127.98.246/index.php/admin/classes/printgrid","_blank")
   //  location.href="http://10.127.98.246/index.php/admin/classes/printgrid";
   //$("#feeds").load("http://10.127.98.246/index.php/admin/classes/printgrid");
   // $("#feeds").print();

    /* $.get("http://10.127.98.246/index.php/admin/classes/printgrid",function(data,status){

        // console.log($(data).find("h1"))
           // alert($(data).find("#datagrd").html());
    });*/

}