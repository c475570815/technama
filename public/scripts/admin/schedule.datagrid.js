/**
 * Created by FuJinsong on 2016/7/21.
 */

var grid='#datagrd';
var url='/index.php/admin/schedule/getlist';
var url_record='/index.php/admin/evaluate/add';
var columns_def=[[
    {field: 'chkbox', checkbox: true},
    {field:'term',title:'学期',sortable:true},
    {field:'time',title:'时间',sortable:true},
    {field:'week',title:'周次',sortable:true},
    {field:'xing_qi_ji',title:'星期',sortable:true},
    {field:'section',title:'节次',sortable:true},
    {field:'class_name',title:'班级名称',sortable:true},
    {field:'class_room',title:'班级教室',sortable:true},
    {field:'teach_id',title:'教师工号',sortable:true},
    {field:'teach_name',title:'教师',sortable:true},
    {field:'course_name',title:'课程名称',sortable:true},
    // {field:'teacher_info',title:'教师信息',sortable:true},
    {field:'dept_name',title:'系部名称',sortable:true},
    {field:'stu_due_number',title:'学生人数',sortable:true},
    {field:'conuncilor',title:'督导',sortable:true},
    {field:'notified',title:'通知情况',sortable:true},
    {field:'finished',title:'完成情况',sortable:true},
    {field:'locked',title:'锁定',sortable:true},
    {field:'operation',title:'操作',formatter:function(val,row,index){

        var opt_formatter="";
        if(row.finished !=="完成"){
            var updateUrl = url_record + "/pk/" + row.id;
            opt_formatter=opt_formatter+"<a class='link-edit' href='"+updateUrl+"' target='_self' title='录入听课结果'> 录入结果 </a>";
        }else{
            var url_detail="";
            opt_formatter="<a class='link-edit' href='"+url_detail+"' target='_self' title='查看详细结果'> 查看结果 </a>";
        }

        return opt_formatter;
    }}

]];
function initGrid(grid,url,columns_def){
    $(grid).datagrid({
        url:url,
        method:'post',
        idField:"id",
        title:"详细信息",
        singleSelect:false,
        checkOnSelect:true,
        selectOnCheck:true,
        collapsible:false,
        pagination:true,
        rownumbers:true,
        columns:columns_def
        //onLoadSuccess:loadSuccessHandler
    });
}
function loadSuccessHandler(data){
    $(".note").tooltip({
            content: $('<div></div>'),
            onShow: function(){
                /*$(this).tooltip('arrow').css('left', 20);
                $(this).tooltip('tip').css('left', $(this).offset().left);*/
                $(this).tooltip('tip').css({
                    width:'300',
                    boxShadow: '1px 1px 3px #292929'
                });
            },
            onUpdate: function(cc){
 /*               cc.panel({
                    width: 500,
                    height: 'auto',
                    border: false,
                    cache:false,
                    href: '/index.php/admin/schedule/tip'
                });*/
                cc.html("sasasasas");
            }
        });
}
function listenerFormater(value,row,index){
    var abValue = value;
    if (value.length>=22) {
        abValue = value.substring(0,19) + "...";
    }
    return "<a href='/index.php/admin/schedule/tip/id/"+index+"' class='note'>"+value+"</a>";
}
//当整个页面全部载入后才执行
$(document).ready(function () {

    initGrid(grid,url,columns_def);
    // 教师列表初始化
    initCbGrid('#cbogrid_of_teacher','/index.php/admin/schedule/teacher_cg');
    // 学期列表初始化
    var dept=$("input[name=dict\\[term\\]]").val();
    $("input[name=dict\\[term\\]]").combobox({
        url: '/index.php/admin/term/getterm',
        method:'POST',
        valueField: 'term_name',
        textField: 'term_name',
        limitToList:false
    });
});

/**
 * 查询
 */
function query(){
    var search_filter=$('#frm_search').serializeJson();
    $(grid).datagrid(
        'load',
        search_filter
    );
}
function add(){

}
/**
 * 初始化教师Combogrid
 * @param cbgrid
 */
function initCbGrid(cbgrid,url){
    $(cbgrid).combogrid({
        delay: 500,
        mode: 'remote',
        panelWidth:450,
        url: url,
        idField: 'teach_id',
        textField: 'teach_name',
        columns: [[
            {field:'teach_id',title:'教师编号',width:60,sortable:true},
            {field:'teach_name',title:'教师姓名',width:80,sortable:true},
            {field:'dept_name',title:'部门',width:120,sortable:true},
            {field:'sex',title:'性别',width:40,sortable:true}
        ]]
    });
}
/* 对datagrid 的扩展方法 */
$.extend($.fn.datagrid.methods, {
    /**
     * 更新 非编辑列值
     * @param rowIndex    : 行索引
     * @param cellName    : 列索引或列名
     * @param cellValue    : 列值
     * @author WUYF
     */
    updateRowCell: function (jq, param) {
        var oGrid = $(jq);
        var jqId = $(jq).attr("id");
        var curRow = (oGrid.datagrid('getRows')[param.rowIndex]);
        /*                 curRow[param.cellName] = param.cellValue;
         oGrid.datagrid('endEdit',param.rowIndex);
         oGrid.datagrid('beginEdit',param.rowIndex); */
        oGrid.datagrid('updateRow', {
            index: param.rowIndex,
            row: param.row
        });
    },
    confirm:function(url,confirm_message){
        var checkedItems = $(grid_id).datagrid('getChecked');
        if (checkedItems.length == 0) {
            $.messager.alert("提示", "请选择需要操作的行！", "info");
            return;
        }
        //将选中行的主健值放到一个数组中
        var selectedRowsID = [];
        $.each(checkedItems, function (index, item) {
            selectedRowsID.push(item.id);
        });
        $.messager.confirm('提示', confirm_message, function (ans) {
            if (!ans) {
                return;
            }
            $.ajax({
                type: "POST",
                url: url,
                data: {id: selectedRowsID},//传递给服务器的参数
                success: function (jsonresult) {
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
});

/**
 * 发送电子邮件通知
 */
function email(){
    //
    var url_email="/index.php/admin/schedule/email";
    var checkedItems = $(grid).datagrid('getChecked');
    if (checkedItems.length == 0) {
        $.messager.alert("提示", "请选择需要操作的行！", "info");
        return;
    }
    //将选中行的主健值放到一个数组中
    var selectedRowsID = [];
    $.each(checkedItems, function (index, item) {
        selectedRowsID.push(item.id);
    });
    $.messager.confirm('提示', '是否发送电邮通知?', function (ans) {
        if (!ans) {
            return;
        }
        $.ajax({
            type: "POST",
            url: url_email,
            data: {id: selectedRowsID},//传递给服务器的参数
            success: function (jsonresult) {
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
 * 发送短信通知
 */
function sms(){
    var url_email="/index.php/admin/schedule/sms";
    var checkedItems = $(grid).datagrid('getChecked');
    if (checkedItems.length == 0) {
        $.messager.alert("提示", "请选择需要操作的行！", "info");
        return;
    }
    //将选中行的主健值放到一个数组中
    var selectedRowsID = [];
    $.each(checkedItems, function (index, item) {
        selectedRowsID.push(item.id);
    });
    $.messager.confirm('提示', '共选中 '+checkedItems.length+' 条，是否发送短信通知?', function (ans) {
        if (!ans) {
            return;
        }
        $.ajax({
            type: "POST",
            url: url_email,
            data: {id: selectedRowsID},//传递给服务器的参数
            success: function (jsonresult) {
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
 删除
 */
function removeRecord() {
    var url_remove="/index.php/admin/schedule/remove";
    var checkedItems = $(grid).datagrid('getChecked');//返回选中记录的数组
    if (checkedItems.length == 0) {
        $.messager.alert("提示", "请选择要删除的行！", "info");
        return;
    }
//将数组中的主健值放到一个数组中 ['软件1','网络1']
    var removeID = [];
    console.log(checkedItems);
    $.each(checkedItems, function(){
        removeID.push(this.id);
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
            data: {id:removeID},//传递给服务器的参数id
            success: function (result) {
                $(grid).datagrid('clearSelections');
                $(grid).datagrid('reload');
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
 * Ajax处理选中的记录
 * @param option
 * option.url
 * option.grid_id
 * option.idField
 * option.msg_confirm
 * option.msg_no_selected
 *
 */
function ajaxAction(option){
    //合并默认选项和传递过来的用户选项
    var option=$.extend({
        method:"POST",
        msg_no_selected:"请选择记录",
        msg_confirm:"确定要指定的操作？"
    }, option);
    var gird=$(option.grid_id);
    var checkedItems = gird.datagrid('getChecked');
    if (checkedItems.length == 0) {
        $.messager.alert("提示", option.msg_no_selected, "info");
        return;
    }
    //将数组中的主健值放到一个数组中 ['软件1','网络1']
    var arr_id = [];
    $.each(checkedItems, function(){
        arr_id.push(eval("this."+option.idField));
    });
    $.messager.confirm('提示', option.msg_confirm, function (r) {
        if (!r) {
            return;
        }
        $.ajax({
            type: "POST",
            async: false,
            url: option.url,
            data: {id:arr_id},//传递给服务器的参数id
            success: function (result) {
                gird.datagrid('clearSelections');
                gird.datagrid('reload');
                if (result.success ==true) {
                    $.messager.alert("提示", result.message, "info");
                } else {
                    $.messager.alert("提示", result.message, "error");
                    return;
                }
            }
        });
    });
}

/**
  锁定
 */
function setlock() {
    ajaxAction(
        {
            url:"/index.php/admin/schedule/setlock",
            grid_id:"#datagrd",
            idField:"id"
        }
    )
}