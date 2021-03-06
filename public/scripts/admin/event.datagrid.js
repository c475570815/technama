

var grid_id = '#datagrd';
var search_form_id="#frm_search";
var download_form_id="#frm_download";
var url_get = '/index.php/admin/event/getlist';
var url_remove = '/index.php/admin/event/remove';
var url_remove_all = '/index.php/admin/event/removeall';
var url_update = '/index.php/admin/event/update';
var url_save = '/index.php/admin/event/save';
var url_export = '/index.php/admin/event/download';
var pk_field = 'id';
var grid_options;
var columns_def = [[
    {field: 'chkbox', checkbox: true},
    {field:'term',title:'学期',sortable:true},
    {field: 'title', title: '事件', sortable: true},
    {field: 'start', title: '开始时间', sortable: true},
    {field: 'end', title: '结束时间', sortable: true},
    {field: 'url', title: '链接', sortable: true},
    {field: 'css', title: '样式', sortable: true},
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
    var updateUrl = url_update + "/pk/" + row.id;
    var row_format="<a href='"+updateUrl+"' target='_self' title='编辑'>编辑 </a>";
    return row_format;

}
/**
 * 当整个页面全部载入后才执行
 */
$(document).ready(function () {
    initGrid(grid_id, url_get, columns_def);
    // 学期列表
    var dept=$("input[name=dict\\[term\\]]").val();
    $("input[name=dict\\[term\\]]").combobox({
        url: '/index.php/admin/term/getterm',
        method:'POST',
        valueField: 'term_name',
        textField: 'term_name',
        limitToList:false
    });
    // 开始时间
    $('input[name=dict\\[start\\]]').datetimebox({
        required: true,
        showSeconds: false
    });

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
 *  删除选中的记录
 */
function removeRecord() {
    //（1）返回选中记录的数组
    var checkedItems = $(grid_id).datagrid('getChecked');
    if (checkedItems.length == 0) {
        $.messager.alert("提示", "请选择要删除的行！", "info");
        return;
    }
    //便利选中的行，并将行的主健值放到一个数组中 ,[3]
    var removeID = [];
    $.each(checkedItems, function (index, item) {

        removeID.push(item.id);
    });
    // console.log(removeID);
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
    // 获取当前选中的行
    var row = $(grid_id).datagrid('getSelected');
    var idValue = row[pk_field];
    console.log(row.end);
    $("input[name=dict\\[term\\]]").val(row.term);
    $("input[name=dict\\[title\\]]").val(row.title);
    $("input[name=dict\\[start\\]]").datetimebox('setValue',row.start);

    $("input[name=dict\\[end\\]]").datetimebox('setValue', row.end);
    $("input[name=dict\\[url\\]]").val(row.url);
    // var updateUrl = url_update + "/pk/" + idValue;
    // location.href = updateUrl;
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
/**
 * 通过Ajax方式保存
 */
function saveForm(){
    $('#frm_add').form({
        url:url_save,
        onSubmit: function(){
            // 有效性验证
            /*var classname=$("#class_name").val();
            if(classname==''){
                return false ;
            }*/
        },
        success:function(data){
            var data = eval('(' + data + ')');
            $.messager.alert('Info', data.message, 'info');
            reload();
        }
    });
    $('#frm_add').submit();
}
/**
 * 清除表单
 */
function clearForm() {
    $('#ff').form('clear');
}