/**
 * Created by guowushi on 2016/7/22.
 */
var grid_id="#datagrid";
var search_form_id="#frm_search";
var url_get="/index.php/admin/dept/getlist";
$(document).ready(function () {
    //当整个页面全部载入后才执行
    initDeptGrid();
    initCboDeptCategory();
});

function initDeptGrid(){

    $("#datagrid").treegrid({
        url:"/index.php/admin/dept/getlist",
        method:'post',
        title:"部门信息",
        idField:'dept_name',
        treeField:'dept_name',
        checkbox:false,
        rownumbers:true,
        columns:[[
            {field:'dept_name',title:'部门'},
            {field:'dept_staff_number',title:'人数'},
            {field:'dept_category',title:'部门类型'}
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