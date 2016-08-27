/**
 * Created by FuJinsong on 2016/7/25.
 */
var grid_id = '#datagrd';
var search_form_id="#frm_search";
var url = '/index.php/admin/weekcourse/getlist';
var url_update = '/index.php/admin/weekcourse/update';
var url_remove_all= '/index.php/admin/weekcourse/removeall';
var columns_def = [[
   /* {field: 'checkbox', checkbox: true},*/
    {field: 'term', title: '学期', sortable: true},
    {field: 'class_name', title: '班级名称', sortable: true},
    {field: 'course_name', title: '课程名称', sortable: true},
    {field: 'class_room', title: '教室', sortable: true},
    {field: 'dept_name', title: '开课单位', sortable: true},
    {field: 'teach_name', title: '教师', sortable: true},
    {field: 'teach_id', title: '教师编号', sortable: true},
    {field: 'week', title: '周次', sortable: true},
    {field: 'xing_qi_ji', title: '星期', sortable: true},
    {field: 'section', title: '节次', sortable: true},
    {field: 'free', title: '是否免听', sortable: true},
    {field: 'onduty', title: '没有调停课', sortable: true},
    {field: 'check_times', title: '已听课次数', sortable: true},
    {field: 'status', title: '听课情况', sortable: true},
    {field:'operation', title: '操作', formatter:formatOptColumn }
    /*    {field:'class_room',title:'教室',sortable:true},
     {field:'state',title:'已听课',sortable:true},//数据读取听课表
     {field:'passed',title:'免听课',sortable:true},//数据读取听课表
     {field:'adj_exchange',title:'调停课',sortable:true},//数据读取调课表
     {field:'opt',title:'听课',width:80,align:'center', formatter:optFormater   },*/
  /*  {field: 'teach_dd', title: '听课教师', sortable: true}//数据录入听课表*/

]];
function formatOptColumn(val,row,index){
    var updateUrl = url_update + "/pk/" +row.c_id;
    var opt_formatter="<a href='#' target='_self'  onclick='selectTech("+ index + ")' title='安排听课教师'> 听课 </a>";
    return opt_formatter;
}
/**
 *
 * @param grid_id
 * @param url
 * @param columns_def
 */
function initGrid(grid_id, url, columns_def) {
    $(grid_id).datagrid({
            url: url,
            method: 'post',
            title: "详细信息",
            fit: true,
            idField:"c_id",
            singleSelect: true,
            checkOnSelect: true,
            selectOnCheck: true,
            collapsible: false,
            pagination: true,
            pageSize:20,
            rownumbers: true,
            columns: columns_def,
            onSelect: function (rowIndex, rowData) {
                // $(this).datagrid('clearSelections');
                //$(this).datagrid('selectRow',rowIndex);
                // return false;
            },
            onCheck: function (rowIndex, rowData) {
                //alert(rowIndex)
            },
            onLoadSuccess: function (data) {
               // $('.editcls').linkbutton({text: '听课', plain: true, width: '100%', iconCls: 'icon-edit'});
               var queryParams=$(this).datagrid('options').queryParams;
                queryParams.action='';
                //console.log(queryParams);
            }
        }
    )
}
function onSelectHandler(rowIndex, rowData){
    console.log();
}
//当整个页面全部载入后才执行
$(document).ready(function () {

    initGrid(grid_id, url, columns_def);
   // listen(grid, url, columns_listen);
    //听课对话框
    $('#dialog_listen').window({
        width:600,
        height:400,
        modal:true,
        constrain:true,
        //top:40,
        iconCls:'icon-save',
        closed:true,
        collapsible:false,
        minimizable:false,
        maximizable:false
    });
});



/**
 *  清楚所有记录,清除前提示
 *  url_remove_all
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
 * 刷新网格
 */
function reload() {
    $(grid_id).datagrid('clearSelections');
    $(grid_id).datagrid('reload');
}
/**
 *  对某个教师设置听课人员，显示督导选择页面
 * @param id  记录
 */
function selectTech(id) {
    //打开对话框
    $('#dialog_listen').window('open');
    $('#dialog_listen').window('center');
    // 获取当前行
    $(grid_id).datagrid('selectRow',id);
    var selectedRow=$(grid_id).datagrid('getSelected');
    var param={
        'week':selectedRow.week,
        'xing_qi_ji':selectedRow.xing_qi_ji,
        'section':selectedRow.section,
        'teacher_id':selectedRow.teach_id
    };
    // current_row_id=id;
    // 显示所有的督导人员
    var grid_listener = '#listen';
    var listen_url = '/index.php/admin/weekcourse/getlisteners';
    var columns_listen = [[
        {field: 'chkbox', checkbox: true},
        {field: 'dept_name', title: '部门', sortable: true},
        {field: 'teach_id', title: '教师编号', sortable: true},
        {field: 'teach_name', title: '教师', sortable: true},
        {field: 'has_lesson', title: '是否有课'},
        {field: 'checked_times', title: '听课次数'},
        {field: 'has_listened', title: '已听过'},
        {field: 'operation', title: '操作', formatter:function(val,row,index) {
            var opt_formatter="<a href='#' target='_self'   title='详情'> 详情 </a>";
            return opt_formatter;
        } }
    ]];
    listenGridInit(grid_listener, listen_url, columns_listen,param);
}
/**
 *  显示督导人员的网格
 * @param grid
 * @param url
 * @param columns_listen
 */
function listenGridInit(grid, url, columns_listen,param) {
    $(grid).datagrid({
        url: url,
        method: 'post',
        fit: true,
        collapsible: false,
        pagination: true,
        checkOnSelect: false,
        remoteSort:false,
        queryParams:param,
        columns: columns_listen
    });
}
/**
 * 查询周课表
 */
function query() {
    var search_filter = $(search_form_id).serializeJson();
    var acion = {'action': 'search'};
    var postdata = $.extend({}, search_filter, acion);
    $(grid_id).datagrid(
        'load', postdata
    );
    grid_options = $(grid_id).datagrid('options').queryParams;
    console.log(grid_options);
}
function load(){

}
/*
 *  提交听课人
 * */
function affirm() {
    // 获取周课表的当前行
    var selectedRow = $(grid_id).datagrid('getSelected');
    // var param = {
    //     'teach_id': selectedRow.teach_id,
    //     'class_name': selectedRow.class_name,
    //     'course_name': selectedRow.course_name,
    //     'week': selectedRow.week,
    //     'xing_qi_ji': selectedRow.xing_qi_ji,
    //     'section': selectedRow.section,
    //     'term':selectedRow.term
    // };
    //(1)获取听课人网格中选中行的数量
    var checkedItems = $('#listen').datagrid('getChecked');
    //(2) 判断是否选择的是两条记录
    if (checkedItems.length != 2) {
        $.messager.alert("提示", "请选择2位听课教师！", "info");
        return;
    }
    //(3) 获取听课人的工号
    var teacher_ids = [];
    $.each(checkedItems, function (index, item) {
        teacher_ids.push(item.teach_id);
    });
    //（4） 提示是否安排指定的听课人
    $.messager.confirm('提示', '是否安排指定听课人？', function (r) {
        if (!r) {
            return;
        } else {
            // （5）将听课人数据，当前行数据AJAX提交到服务器
            var params = {
                'lesson': selectedRow,
                'teachers': teacher_ids
            };
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "/index.php/admin/weekcourse/addlisteners",
                data: params,//传递给服务器的参数
                success: function (jsonresult) {
                    reload();//放在提示消息之前
                    if (jsonresult.success == 'true') {
                        $.messager.alert("提示", '安排指定听课人成功！', "info");
                        // (6)通知 课程表Grid 刷新数据
                    } else {
                        $.messager.alert("提示", '安排指定听课人失败', "info");
                        return;
                    }
                }
            });

        }
    });
}
/**
 * 关闭对话框窗体
 */
function closeDialog(){
    //打开对话框
    $('#dialog_listen').window('close');

}

