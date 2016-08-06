/**
 * Created by Administrator on 2016/7/21.
 */
var grid_id='#datagrd';//表单名
var url_get='http://10.127.98.246/index.php/admin/Tea/ac1';
var url_remove='http://10.127.98.246/index.php/admin/Tea/remove';
var url_update='http://10.127.98.246/index.php/admin/Tea/update';
var url_tree='http://10.127.98.246/index.php/admin/Tea/ac1';
var url_export='http://10.127.98.246/index.php/admin/Tea/download';
var pk_field='teach_id';
var columns_def=[[
    {field: 'chkbox', checkbox: true},
    {field:'dept_name',title:'所属系部',sortable:true},
    {field:'teach_name',title:'教师名',sortable:true,},
    {field:'sex',title:'性别',sortable:true},
    {field:'teach_id',title:'教师编号',sortable:true},
    {field:'profess_duty',title:'专业技术职务',sortable:true},
    {field:'teach_phone',title:'电话',sortable:true},
    {field:'email',title:'电子邮箱',sortable:true},
    {field:'qq',title:'QQ号',sortable:true},
    {field:'conuncilor',title:'是否兼课',sortable:true},
    {field:'location',title:'职位',sortable:true},
    {field:'passed',title:'是否免听',sortable:true},
    {field:'limit',title:'听课限制',sortable:true},
    {field:"op", title: '操作', formatter:formatOptColumn }
]];
function initGrid(grid_id,url_get,columns_def){
    $(grid_id).datagrid({
        url:url_get,
        method:'post',
        title:"详细信息",
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
    initGrid(grid_id,url_get,columns_def);
    load2();
    //console.log(111111111111);
});
/**
 * 查询
 */
function query(){
     //var  a=$("#cc").combotree('getText');
    //document.getElementsByName("dict[dept_name]").value=a;
    var search_filter=$('#frm_search').serializeJson();//把数据做成josn格式
   $("#datagrd").datagrid(
       'load',//利用load方法提交search_filter 控制器ac1方法
       search_filter
    );
}/**
 删除
 */

function removeRecord() {
    var checkedItems = $(grid_id).datagrid('getChecked');//返回选中记录的数组
    if (checkedItems.length == 0) {
        $.messager.alert("提示", "请选择要删除的行！", "info");
        return;
    }
//将数组中的主健值放到一个数组中 ['软件1','网络1']
    var removeID = [];
    console.log(checkedItems);
    $.each(checkedItems, function(){
        removeID.push(this.teach_id);
    });
    $.messager.confirm('提示', '是否删除选中数据?', function (r) {
        if (!r) {
            return;
        }
        //Ajax提交
        $.ajax({
            type: "POST",
            async: false,
            url: url_remove,
            data: {id:removeID},//传递给服务器的参数
            success: function (result) {
                //result = eval('(' + result + ')');//把字符串变成一个JS对象
                //result= eval('(' + result + ')');
                $(grid_id).datagrid('clearSelections');
                $(grid_id).datagrid('reload');
                if (result.success ==true) {
                    $.messager.alert("提示", result.message, "info");
                } else {
                    $.messager.alert("提示", result.message, "info");
                    return;
                }
            }
        });
    });
}
/**
 * 刷新网格
 */
function reload() {
    $(grid_id).datagrid('clearSelections');
    $(grid_id).datagrid('reload', {'nnn': 'aaa'});
}
/**
 * 编辑
 */
function edit() {
    var rows = $(grid_id).datagrid('getSelections');
    if (rows.length != 1) {
      //  console.log('t')
        $.messager.show({
            title: "",
            msg: "请选择一条记录"
        });
        return;
    }
    var row = $(grid_id).datagrid('getSelected');
    //console.log(row);
    var idValue = row[pk_field];
    //console.log(idValue);
    var updateUrl = url_update + "/pk/" + idValue;
    location.href = updateUrl;
}
/*
* 把数据以表格的形式下载
* */
function  down() {
    var url_down="ftp://10.127.98.246/static/picture/logo.png"
    location.href = url_down;
}
/**
 * 加载下拉树的josn
 */
function  load2() {
   $.getJSON("http://10.127.98.246/index.php/admin/Tea/treejosn",function(data){
        var str = data.toString();
        var s=eval(str);
        $('#cc').combotree('loadData',s);
});
}
/**
 *  下载
 */

function exportXls() {
    console.log("llll");
    var grid_options = $(grid_id).datagrid('options').queryParams;
    var acion = {'action': 'export'};
    var postdata = $.extend({}, grid_options, acion);
    $('#frm_download').form('submit', {
        url: 'http://10.127.98.246/index.php/admin/Tea/download',
        queryParams: postdata,
        onSubmit: function () {
        },
        success: function (data) {
            alert(data);
           // console.log("2222");
        }
    });
}
/**
 * 定义列的显示
 * @param val
 * @param row
 * @returns {*}
 */
function formatOptColumn(val,row,index){
    var updateUrl = url_update + "/pk/" + row.teach_id;
    return "<a href='"+updateUrl+"' target='_self'> 操作 </a>";

}
/**
 * 清空
 */
function clearForm() {
    $('#frm_search').form('clear');
}
