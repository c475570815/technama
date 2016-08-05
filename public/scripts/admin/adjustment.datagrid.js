/**
 * Created by guowushi on 2016/7/20.
 */
/**
 *
 */
var grid_id='#datagrd';
var grid='#datagrd';
var download_form_id="#frm_download";
var url='http://10.127.98.242/index.php/admin/adjustment/ac1';
var url_remove='http://10.127.98.242/index.php/admin/adjustment/remove';
var url_update='http://10.127.98.242/index.php/admin/adjustment/update';
var url_export = 'http://10.127.98.242/index.php/admin/classes/download';
var pk_field='adj_id';
var grid_options;
var columns_def=[[
    {field:'chkbox',checkbox:true },
    {field:'teach_id',title:'教师编号',sortable:true},
    {field:'class_name',title:'班级名称',sortable:true},
    {field:'class_room',title:'班级固定教室',sortable:true},
    {field:'course_name',title:'课程名称',sortable:true},
    {field:'week',title:'周次',sortable:true},
    {field:'xing_qi_ji',title:'星期',sortable:true},
    {field:'section',title:'节次',sortable:true},
    {field:'reason',title:'原因',sortable:true},
    {field:'alt_week',title:'调后周次',sortable:true},
    {field:'alt_xq',title:'调后星期',sortable:true},
    {field:'alt_section',title:'调后节次',sortable:true},
    {field:'alt_class_room',title:'调后教室',sortable:true},


]];
function initGrid(grid,url,columns_def){
    $(grid_id).datagrid({
        url:url,
        method:'post',
        title:"详细信息",
        fit:true,
        singleSelect:false,
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
    $(grid_id).datagrid(
        'load',
        search_filter
    );
}
function add(){

}

/**
 * 删除选中的记录
 */
function removeRecord() {
    var checkedItems = $(grid).datagrid('getChecked');//返回选中记录的数组
    if (checkedItems.length == 0) {
        $.messager.alert("提示", "请选择要删除的行！", "info");
        return;
    }
    //将数组中的主健值放到一个数组中 ['软件1','网络1']
    var removeID = [];
    $.each(checkedItems, function (index, item) {
        removeID.push(item.adj_id);
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
}
    $(document).ready(function () {
        //当整个页面全部载入后才执行
        initGrid(grid,url,columns_def);
    });
function reload(){
    $(grid_id).datagrid('clearSelections');
    $(grid_id).datagrid('reload',{'nnn':'aaa'});

}
/**
 * 编辑
 */
function edit(){
    var rows = $(grid).datagrid('getSelections');
    if (rows.length != 1) {
        $.messager.show({
            title: "",
            msg: "请选择一条记录"
        });
        return;
    }
    var row = $(grid).datagrid('getSelected');
    var idValue = row[pk_field];
    var updateUrl = url_update+"/pk/"+idValue;
    location.href=updateUrl;
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