/**
 * Created by guowushi on 2016/7/22.
 */

$(document).ready(function () {
    //当整个页面全部载入后才执行
    $('#datagrd').datagrid({
        url:'/index.php/admin/dict/ac1',
        method:'post',
        title:"详细信息",
        idField:"dict_id",
        fit:true,
        singleSelect:true,
        collapsible:false,
        pagination:true,
        rownumbers:true,
        columns:[[
            {field:'dict_category',title:'类别',sortable:true,width:'30%'},
            {field:'dict_key',title:'键',sortable:true,width:'10%'},
            {field:'dict_value',title:'显示值',sortable:true,width:'10%'},
            {field:'dict_point',title:'显示顺序',sortable:true,width:'10%'},
            {field:'dict_enabled',title:'启用',sortable:true,width:'10%'}

        ]]
    });
});
function query(){
    var search_filter=$('#frm_search').serializeJson();
    /*var search_filter={
     'dict[dict_value]':$("#v").val(),
     'dict[dict_category]':$("#a").val()
     }*/
    /*
     {
     dict[dict_category]:'基本技能',
     dict[dict_value]:'优秀'
     }
     */
    $('#datagrd').datagrid(
        'load',
        search_filter
    );
}

function update(){
    //获取用户选择了哪些行，返回的是一个数组
    var rows = $('#datagrd').datagrid('getSelections');
    if (rows.length != 1) {
        $.messager.show({
            title: "",
            msg: "请选择一条记录"
        });
        return;
    }
    //获取 用户选择的一行记录
    var row = $('#datagrd').datagrid('getSelected');
    // 获取该记录的主健值
    var idValue = row['dict_id'];
    // 让页面跳转到指定地址
    var updateUrl = '/index.php/admin/dict/update？pk=7';
    var updateUrl = '/index.php/admin/dict/update'+"/pk/"+idValue;
    location.href=updateUrl;
}