/**
 * Created by guowushi on 2016/7/22.
 */
(function($){
    $.fn.serializeJson=function(){
        var serializeObj={};
        var array=this.serializeArray();
        var str=this.serialize();
        $(array).each(function(){
            if(serializeObj[this.name]){
                if($.isArray(serializeObj[this.name])){
                    serializeObj[this.name].push(this.value);
                }else{
                    serializeObj[this.name]=[serializeObj[this.name],this.value];
                }
            }else{
                serializeObj[this.name]=this.value;
            }
        });
        return serializeObj;
    };
})(jQuery);

$(document).ready(function () {
    //当整个页面全部载入后才执行
    $('#datagrd').datagrid({
        url:'http://10.127.98.242/index.php/admin/dict/ac1',
        method:'post',
        title:"详细信息",
        fit:true,
        singleSelect:true,
        collapsible:false,
        pagination:true,
        rownumbers:true,
        height:345,
        columns:[[
            {field:'dict_category',title:'类别',sortable:true,width:'30%'},
            {field:'dict_key',title:'键',sortable:true,width:'10%'},
            {field:'dict_value',title:'显示值',sortable:true,width:'10%'},
            {field:'dict_id',title:'编号',sortable:true,width:'10%'}

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
    var updateUrl = 'http://10.127.98.242/index.php/admin/dict/update？pk=7';
    var updateUrl = 'http://10.127.98.242/index.php/admin/dict/update'+"/pk/"+idValue;
    location.href=updateUrl;
}