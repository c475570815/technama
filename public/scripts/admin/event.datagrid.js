/**
 * Created by guowushi on 2016/7/20.
 */
/**
 *
 */
var grid_id = '#datagrd';
var search_form_id="#frm_search";
var download_form_id="#frm_download";
var url_get = '/index.php/admin/classes/index';
var url_remove = '/index.php/admin/classes/remove';
var url_remove_all = '/index.php/admin/classes/removeall';
var url_update = '/index.php/admin/classes/update';
var url_export = '/index.php/admin/classes/download';
var pk_field = 'class_id';
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
/**
 *  初始化网格对象
 *  idField:主键
 * @param grid
 * @param url
 * @param columns_def
 */
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
        pageSize:20,
        columns: columns_def
    });
}
/**
 * 定义列的显示
 * @param val
 * @param row
 * @returns {*}
 */
function formatOptColumn(val,row,index){
    var updateUrl = url_update + "/pk/" + row.class_id;
    return "<a href='"+updateUrl+"' target='_self'> 操作 </a>";

}
/**
 * 当整个页面全部载入后才执行
 */
$(document).ready(function () {
    initGrid(grid_id, url_get, columns_def);
});

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
 *  清楚所有记录,清除前提示
 */
function removeall(){
    $.messager.confirm('提示', '是否删除所有的数据?', function (r) {
        if (!r) {
            return;
        }
        //Ajax提交
        $.ajax({
            type: "POST",
            url: url_remove_all,
            data: {id: 1},//传递给服务器的参数
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
            var dataObj=eval("("+data+")");
            $.messager.show({
                msg:'删除成功！',
                showType:'show',
                style:{
                    right:'',
                    top:document.body.scrollTop+document.documentElement.scrollTop,
                    bottom:''
                }
            });
            console.log(data);

        }
    });
}
/**
 *  显示上传窗体
 */
function importDialog() {
    var dialog_id="#dd";
    $(dialog_id).dialog({
        title: '导入数据',
        width: 400,
        height: 400,
        closed: false,
        cache: true,
       // href: 'get_content.php',
        modal: true
    });
}
/**
 * 上传提交
 */
function importxls() {
    var url_import="/index.php/admin/classes/upload";
    var grid_options = $(grid_id).datagrid('options').queryParams; //保存grid原有的参数
    var acion = {'action': 'import'};// 增加一个参数
    var postdata = $.extend({}, grid_options, acion); //合并参数
    $("#frm_upload").form('submit', {
        url: url_import,
        queryParams: postdata,
        onSubmit: function () {
        },
        success: function (data) {
            //解析返回的JSON
            var dataObj=eval("("+data+")");
            var isok=dataObj.success;
            var errors=dataObj.data;
            var message=dataObj.message;
            for(var key in errors){
                message=message+"\n "+key+"行："+errors[key];
            }
            $("#msgbox").val(message);
            console.log(dataObj.data);
            reload();
        }
    });
}
/**
 * 打印
 */
function printGrid(){
   // $(grid_id).print();
    window.open("/index.php/admin/classes/printgrid","_blank")
   //  location.href="http://10.127.98.246/index.php/admin/classes/printgrid";
   //$("#feeds").load("http://10.127.98.246/index.php/admin/classes/printgrid");
   // $("#feeds").print();
    /* $.get("http://10.127.98.246/index.php/admin/classes/printgrid",function(data,status){
        // console.log($(data).find("h1"))
           // alert($(data).find("#datagrd").html());
    });*/

}
function addRecord(){
   location.href='/index.php/admin/classes/add';
}