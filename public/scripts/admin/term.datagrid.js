/**
 * Created by FuJinsong on 2016/8/9.
 */
var grid_id='#datagrd';
var url='/index.php/admin/term/json';
var url_remove='/index.php/admin/term/remove';
var url_update='/index.php/admin/term/update';
var columns_def=[[
    {field:'chkbox',checkbox:true },
    {field:'term_name',title:'学期',sortable:true},
    {field:'start',title:'开始时间',sortable:true},
    {field:'end',title:'结束时间',sortable:true},
    {field:'default',title:'当前学期',formatter: function(value,row,index){
        if (row.default==1){
            return row.default='是'
        } else {
            return row.default='否'
        }
    },
    sortable:true}
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
    initGrid(grid_id,url,columns_def);
});

/**
 * 删除选中的记录
 */
function removeRecord() {
    var checkedItems = $(grid_id).datagrid('getChecked');//返回选中记录的数组
    if (checkedItems.length == 0) {
        $.messager.alert("提示", "请选择要删除的行！", "info");
        return;
    }
    //将数组中的主健值放到一个数组中 ['软件1','网络1']
    var removeID = [];
    $.each(checkedItems, function (index, item) {
        removeID.push(item.id);
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

function reload(){
    $(grid_id).datagrid('clearSelections');
    $(grid_id).datagrid('reload',{'nnn':'aaa'});

}