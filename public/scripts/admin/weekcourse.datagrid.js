/**
 * Created by FuJinsong on 2016/7/25.
 */
var grid_id = '#datagrd';
var search_form_id = "#frm_search";
var url = '/index.php/admin/weekcourse/getlist';
var url_update = '/index.php/admin/weekcourse/update';
var url_remove_all = '/index.php/admin/weekcourse/removeall';
var columns_def = [[
    {field: 'checkbox', checkbox: true},
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
    {field: 'status', title: '听课情况', sortable: true, formatter: columnStyle},
    {field: 'free', title: '是否免听', sortable: true},
    {field: 'onduty', title: '没有调停课', sortable: true},
    {field: 'check_times', title: '已听课次数', sortable: true},
    {field: 'plan_times', title: '安排听课人数', sortable: true}

    // {field:'operation', title: '操作', formatter:formatOptColumn }
    /*    {field:'class_room',title:'教室',sortable:true},
     {field:'state',title:'已听课',sortable:true},//数据读取听课表
     {field:'passed',title:'免听课',sortable:true},//数据读取听课表
     {field:'adj_exchange',title:'调停课',sortable:true},//数据读取调课表
     {field:'opt',title:'听课',width:80,align:'center', formatter:optFormater   },*/
    /*  {field: 'teach_dd', title: '听课教师', sortable: true}//数据录入听课表*/

]];

function columnStyle(val, row, index) {
    var opt_formatter = "<a href='#' class='' target='_self'  title=''> " + val + " </a>";
    return opt_formatter;
}

function formatOptColumn(val, row, index) {
    // var updateUrl = url_update + "/pk/" +row.c_id;
    var opt_formatter = "";
    if (row.plan_times >= 2) {
        opt_formatter = "<a href='#' class='link-edit' target='_self'  onclick='selectTech(" + index + ")' title='安排听课教师'> 重新安排听课 </a>";
    } else {
        opt_formatter = "<a href='#' class='link-edit' target='_self'  onclick='selectTech(" + index + ")' title='安排听课教师'> 安排听课 </a>";
    }
    return opt_formatter;
}
/**
 * 网格初始化
 * @param grid_id
 * @param url
 * @param columns_def
 */
function initGrid(grid_id, url, columns_def) {
    $(grid_id).datagrid({
            url: url,
            method: 'post',
            title: "周课表详细信息",
            fit: true,
            idField: "c_id",
            singleSelect: false,
            checkOnSelect: true,
            ctrlSelect: true,
            collapsible: false,
            pagination: true,
            pageSize: 100,
            pageList:[20,40,50,100],
            rownumbers: true,
            columns: columns_def,
            onSelect: function (rowIndex, rowData) {
                buttonStatus(this);
            },
            onUnselect: function (rowIndex, rowData) {
                buttonStatus(this);
            },

            onCheck: function (rowIndex, rowData) {
                buttonStatus(this);
            },
            onUncheck: function (rowIndex, rowData) {
                buttonStatus(this);
            },
            onLoadSuccess: function (data) {
                // $('.editcls').linkbutton({text: '听课', plain: true, width: '100%', iconCls: 'icon-edit'});
                var queryParams = $(this).datagrid('options').queryParams;
                queryParams.action = '';
                //console.log(queryParams);
            }
        }
    )
}
/**
 * 更新按钮状态
 * @param oGrid  网格对象
 */
function buttonStatus(oGrid) {
    var checkedItems = $(oGrid).datagrid('getChecked');
    $("#btn_selected").linkbutton({text:"共选中"+checkedItems.length+"条记录" });
    if (checkedItems.length > 0) {
        $("#btn_autoplan").linkbutton({disabled: false});
        if(checkedItems.length ==1){
            $("#btn_scedule").linkbutton({disabled: false});
            $("#btn_rescedule").linkbutton({disabled: false});
        }else{
            $("#btn_scedule").linkbutton({disabled: true});
            $("#btn_rescedule").linkbutton({disabled: true});
        }
    } else {
        $("#btn_autoplan").linkbutton({disabled: true});
        $("#btn_scedule").linkbutton({disabled: true});
        $("#btn_rescedule").linkbutton({disabled: true});
    }
}

//当整个页面全部载入后才执行
$(document).ready(function () {

    initGrid(grid_id, url, columns_def);
    // listen(grid, url, columns_listen);
    //听课对话框
    $('#dialog_listen').window({
        width: 600,
        height: 500,
        modal: true,
        constrain: true,
        //top:40,
        iconCls: 'icon-save',
        closed: true,
        collapsible: false,
        minimizable: false,
        maximizable: false
    });

    //自动安排对话框
    $('#dialog_autoplan').window({
        width: 600,
        height: 500,
        modal: true,
        constrain: true,
        //top:40,
        iconCls: 'icon-save',
        closed: true,
        collapsible: false,
        minimizable: false,
        maximizable: false
    });
//绑定显示隐藏列
    $('#combo_columns').combobox({
        limitToList:true,
        onSelect:function(record){
            $("#datagrd").datagrid('showColumn',record.value);
            console.log(record);
        },
        onUnselect:function(record){
            $("#datagrd").datagrid('hideColumn',record.value);
            console.log("unselect"+record.text);
        }
    });
    // 学期列表初始化
    // $("input[name=dict\\[term\\]]").combobox({
    //     url: '/index.php/admin/term/getterm',
    //     method: 'POST',
    //     valueField: 'term_name',
    //     textField: 'term_name',
    //     limitToList: false
    // });
    //  初始化周次列表
    $("#cbo_week").combobox({
        valueField: 'value',
        textField: 'label',
        data: [
             {  label: '1',      value: '1'  }
            ,{  label: '2',      value: '2' }
            ,{  label: '3',      value: '3'  }
            ,{  label: '4',      value: '4'  }
            ,{  label: '5',      value: '5'  }
            ,{  label: '6',      value: '6'  }
            ,{  label: '7',      value: '7'  }
            ,{  label: '8',      value: '8'  }
            ,{  label: '9',      value: '9'  }
            ,{  label: '10',      value: '10'  }
            ,{  label: '11',      value: '11'  }
            ,{  label: '12',      value: '12'  }
            ,{  label: '13',      value: '13'  }
            ,{  label: '14',      value: '14'  }
            ,{  label: '15',      value: '15'  }
            ,{  label: '16',      value: '16'  }
            ,{  label: '17',      value: '17'  }

        ]
    });

    //-------操作按钮初始化-------------
    $("#btn_autoplan").linkbutton({disabled: true});
    $("#btn_scedule").linkbutton({disabled: true});
    $("#btn_rescedule").linkbutton({disabled: true});
});


/**
 *  清楚所有记录,清除前提示
 *  url_remove_all
 */
function removeall() {
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
 * @param id  选中的记录编号
 */
function selectTech(id) {
    //打开对话框
    $('#dialog_listen').window('open');
    $('#dialog_listen').window('center');
    // 获取当前行
    $(grid_id).datagrid('selectRow', id);
    $(grid_id).datagrid('checkRow', id);
    var selectedRow = $(grid_id).datagrid('getSelected');//返回第一个选中的行

    var selectedRowIndex = $(grid_id).datagrid('getRowIndex', selectedRow);
    $("#index").text(selectedRowIndex);
    $("#num").text(selectedRow.plan_times);
    // console.log(selectedRow);
    var param = {
        'week': selectedRow.week,
        'xing_qi_ji': selectedRow.xing_qi_ji,
        'section': selectedRow.section,
        'teacher_id': selectedRow.teach_id
        // 'plan_times':selectedRow.plan_times
    };
    // current_row_id=id;
    // 使用网格显示所有的督导人员
    var grid_listener = '#listen';
    var listen_url = '/index.php/admin/weekcourse/getlisteners';
    var columns_listen = [[
        {field: 'chkbox', checkbox: true},
        {field: 'dept_name', title: '部门', sortable: true},
        {field: 'teach_id', title: '教师编号', sortable: true},
        {field: 'teach_name', title: '教师', sortable: true},
        {
            field: 'has_lesson', title: '是否有课', formatter: function (val, row, index) {
            if (val == '是') {
                opt_formatter = "<strong style='color: red'>" + val + "</strong>";
            } else {
                opt_formatter = "<strong style='color: green'>" + val + "</strong>";
            }
            return opt_formatter;
        }
        },
        {field: 'checked_times', title: '听课次数'},
        {field: 'has_listened', title: '已听过'},
        {
            field: 'operation', title: '操作', formatter: function (val, row, index) {
            var opt_formatter = "<a href='#' target='_self'   title='详情'> 详情 </a>";
            return opt_formatter;
        }
        }
    ]];
    var custom_options = {
        "url": '/index.php/admin/weekcourse/getlisteners',
        "columns": columns_listen,
        "queryParams": param
    };
    listenGridInit(grid_listener, listen_url, columns_listen, param);
    $(grid_listener).datagrid("clearSelections");
    $(grid_listener).datagrid("clearChecked");
   // $(grid_listener).datagrid('reload');


}
/**
 *  显示督导人员的网格
 * @param grid
 * @param url
 * @param columns_listen
 */
function listenGridInit(grid, url, columns_listen, param) {
    $(grid).datagrid({
        url: url,
        method: 'post',
        fit: true,
        rownumbers: true,
        idField: "teach_id",
        collapsible: false,
        pagination: true,
        ctrlSelect: true,
        checkOnSelect: true,
        singleSelect: false,
        pageSize: 100,
        pageList: [10, 20, 50, 100],
        remoteSort: false,
        queryParams: param,
        onLoadSuccess: function (data) {
            // $(this).datagrid('selectRow',3);
        },
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


/**
 * 提交听课人
 */
function affirm() {
    // 获取周课表的当前行
    // var selectedRows = $(grid_id).datagrid('getSelected');
    $(grid_id).datagrid('selectRow', $("#index").text());
    var selectedRows = $(grid_id).datagrid('getSelected');
    //(1)获取听课人网格中选中行的数量
    var checkedItems = $('#listen').datagrid('getChecked');
    //(2) 判断是否选择的是两条记录
    // 获取周课表的当前行
    var week_course_selected_row = $(grid_id).datagrid('getSelected');
    console.log(week_course_selected_row);
    // var tt = $("#num").text();
    // var _num=2-tt;
    // if (checkedItems.length != _num) {
    //     $.messager.alert("提示", "请选择"+_num+"位听课教师！", "info");
    //     return;
    // }
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
                'lesson': week_course_selected_row,    //课程
                'teachers': teacher_ids   //听课教师
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
function closeDialog() {
    //打开对话框
    $('#dialog_listen').window('close');

}
/*
 *  按相应规则，自动安排听课人
 * */
function autoplan() {
    alert("正在开发中...");
}

/*
 *  按相应规则，重新安排听课人
 * */
function rescedule() {
    alert("正在开发中...");
}
