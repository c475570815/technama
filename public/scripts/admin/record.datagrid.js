/**
 * Created by guowushi on 2016/7/20.
 */
/**
 *
 */
var grid_id = '#datagrd';
var search_form_id="#frm_search";
var download_form_id="#frm_download";
var url_get = '/index.php/admin/record/getlist';
var url_remove = '/index.php/admin/record/remove';
var url_update = '/index.php/admin/classes/update';
var url_export = '/index.php/admin/classes/download';
var pk_field = 'id';
var grid_options;
var columns_def = [[
    {field: 'chkbox', checkbox: true},
    {field: 'term', title: '学期', sortable: true},
    {field: 'week', title: '周', sortable: true},
    {field: 'xing_qi_ji', title: '星期', sortable: true},
    {field: 'section', title: '节次', sortable: true},
    {field: 'class_name', title: '班级', sortable: true},
    {field: 'course_name', title: '课程', sortable: true},
    {field: 'teacher', title: '教师', sortable: true},
    {field: 'dept_name', title: '系部', sortable: true},
    {field: 'listener', title: '听课人', sortable: true},
    {field: 'last_evaluate', title: '总体评价', sortable: true,formatter:function(val,row,index){
    var opt_formatter="";
        if(val=="优"){
            color="green";
        }else if(val=="良"){
            color="blue";
        }else if(val=="合格"){
            color="indigo";
        }else if(val=="不合格"){
            color="red";
        }
    opt_formatter=opt_formatter+"<a class='' style='color:"+color +"' href='#' target='_self' title='"+row.comments+"'>"+val+" </a>";
    return opt_formatter;
}},
    {field: 'score', title: '得分', sortable: true},
    {field:"operation", title: '操作', formatter:formatOptColumn }
]];

function initGrid(grid, url, columns_def) {
    $(grid_id).datagrid({
        url: url_get,
        idField:pk_field,
        method: 'post',
        title: "详细信息",
        singleSelect: false,
        checkOnSelect: true,
        ctrlSelect:true,
        pageSize:100,
        pageList:[20,40,50,100],
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
    initTermCombo();
});

/**
 * 学期初始化
 */
function initTermCombo(){
    // 学期列表初始化
    var dept=$("input[name=dict\\[term\\]]").val();
    $("input[name=dict\\[term\\]]").combobox({
        url: '/index.php/admin/term/getterm',
        method:'POST',
        valueField: 'term_name',
        textField: 'term_name',
        limitToList:false
    });
}

/**
 * 定义列的显示
 * @param val
 * @param row
 * @returns {*}
 */
function formatOptColumn(val,row,index){
    var updateUrl = url_update + "/pk/" + row.class_name;

       return "<a href='"+updateUrl+"' class='link-edit' target='_self'> 操作 </a>";

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
                // show message window on top center
                // $.messager.show({
                //     title:'提示',
                //     msg:jsonresult.message,
                //     showType:'show',
                //     style:{
                //         right:'',
                //         top:document.body.scrollTop+document.documentElement.scrollTop,
                //         bottom:''
                //     }
                // });
                if (jsonresult.success == true) {
                    $.messager.alert("提示", jsonresult.message, "info");
                } else {
                    $.messager.alert("提示", jsonresult.message, "error");
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
    window.open("/index.php/admin/classes/printgrid","_blank")

}