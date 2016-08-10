/**
 * Created by guowushi on 2016/7/22.
 */
var grid_id="#datagrid";
var dialog_id="#dd";
var search_form_id="#frm_search";
var download_form_id="#frm_download";
var url_import="/index.php/admin/dept/upload";
var url_get="/index.php/admin/dept/getlist";
var url_remove="/index.php/admin/dept/remove";
var url_export ="/index.php/admin/dept/download";
$(document).ready(function () {
    //当整个页面全部载入后才执行
    initDeptGrid();
    initCboDeptCategory();
});

function initDeptGrid(){

    $(grid_id).treegrid({
        url:url_get,
        method:'post',
        title:"部门信息",
        idField:'dept_name',
        treeField:'dept_name',
        checkbox:false,
        rownumbers:true,
        columns:[[
            {field:'dept_name',title:'部门',sortable: true},
            {field:'dept_staff_number',title:'人数',sortable: true},
            {field:'dept_category',title:'部门类型',sortable: true}
        ]]
    });
}
function initCboDeptCategory(){
    $('#cc').combobox({
        url:'/index.php/admin/dept/deptcat',
        valueField:'dict_key',
        textField:'dict_value',
        multiple:true
    });
}

function query() {
    var search_filter = $(search_form_id).serializeJson();
    var acion = {'action': 'search'};
    var postdata = $.extend({}, search_filter, acion);
    $(grid_id).treegrid(
        'load',
        postdata
    );
    grid_options = $(grid_id).treegrid('options').queryParams;
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
    var checkedItems = $(grid_id).treegrid('getChecked');//返回选中记录的数组
    if (checkedItems.length == 0) {
        $.messager.alert("提示", "请选择要删除的行！", "info");
        return;
    }
    //将数组中的主健值放到一个数组中 ,['软件1','网络1']
    var removeID = [];
    $.each(checkedItems, function (index, item) {
        removeID.push(item.dept_id);
    });
    console.log(removeID);
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
function exportXls1() {

   // var grid_options = $(grid_id).treegrid('options');
   // console.log(grid_options);
    var acion = {'action': 'export'};
    //var postdata = $.extend({}, grid_options, acion);
    $(download_form_id).form('submit', {
        url: url_export,
        queryParams: acion,
        onSubmit: function () {
        },
        success: function (data) {
            var dataObj=eval("("+data+")");
            $.messager.show({
                msg:'导出成功！',
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