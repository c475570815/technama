/**
 * Created by FuJinsong on 2016/7/25.
 */
var grid_id = '#datagrd';
var grid = '#datagrd';
var grid_listener = '#listen';
var url = '/index.php/admin/course/ac1';
var lis_url = '/index.php/admin/schedule/ac1';
var url_remove = '/index.php/admin/course/remove';
var url_update = '/index.php/admin/course/update';
var pk_field = 'adj_id';
/* 用于课程表的网格列定义 */
var columns_def = [[
    {field: 'checkbox', checkbox: true},
    {field: 'term', title: '学期', sortable: true},
    {field: 'class_name', title: '班级名称', sortable: true},
    {field: 'course_name', title: '课程名称', sortable: true},
    {field: 'dept_name', title: '开课单位', sortable: true},
    {field: 'teach_name', title: '教师', sortable: true},
    {field: 'teach_id', title: '教师编号', sortable: true},
    {field: 'week', title: '周次', sortable: true},
    {field: 'class_week', title: '上课周次', sortable: true},
    {field: 'xing_qi_ji', title: '星期', sortable: true},
    {field: 'section', title: '节次', sortable: true},
    {field:'class_room',title:'教室',sortable:true}
/*    {field:'state',title:'已听课',sortable:true},//数据读取听课表
    {field:'passed',title:'免听课',sortable:true},//数据读取听课表
    {field:'adj_exchange',title:'调停课',sortable:true},//数据读取调课表
    {field:'opt',title:'听课',width:80,align:'center', formatter:optFormater   },
    {field: 'teach_dd', title: '听课教师', sortable: true}//数据录入听课表*/
]];

function optFormater(value, row, index) {
    var btn = '<a class="editcls" onclick="selectTech(' + row.c_id + ')" href="#">听课</a>';
    return btn;
}

/**
 * 清除表格已选择项
 */
function clear() {
    $(lis).datagrid('unselectAll');
    $(lis).datagrid('reload', {'nnn': 'aaa'});
}

var columns_listen = [[
    {field: 'chkbox', checkbox: true},
    {field: 'dept_name', title: '部门', sortable: true},
    {field: 'teach_name', title: '教师', sortable: true},
    {field: 'teach_id', title: '教师编号', sortable: true}
]];

/**
 *  用于初始化课程表的网格显示的
 * @param grid   网格的
 * @param url
 * @param columns_def
 */
function initGrid(grid, url,  columns_def) {
    $(grid).datagrid({
            url: url,
            method: 'post',
            title: "详细信息",
            fit: true,
            singleSelect: false,
            checkOnSelect: true,
            selectOnCheck: true,
            collapsible: false,
            pagination: true,
            rownumbers: true,
            height: 345,
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
            }
        }
    )
}


var current_row_id;
/*
 *  提交听课人
 * */
function affirm() {

    //(1)获取选中行的数量
    var checkedItems = $(grid_listener).datagrid('getChecked');
    //(2) 判断是否选择的是两条记录
    if (checkedItems.length != 2) {
        $.messager.alert("提示", "请选择2位听课教师！", "info");
        return;
    }
    //(3) 获取听课人姓名
    var ids = [];
    $.each(checkedItems, function (index, item) {
        ids.push(item.teach_name);
    });
    //（4） 提示是否安排指定的听课人
    $.messager.confirm('提示', '是否安排指定听课人？', function (r) {
        if (!r) {
            return;
        }else{
            // （5）将听课人数据，当前行数据AJAX提交到服务器
            /*
             {
             courseid:111,
             teachers:[zhangsan,lisi]
             }
             */
            var params= {
                'courseid': current_row_id,
                'teachers': ids
            };
            $.ajax({
                type: "POST",
                url: "http://10.127.98.246/index.php/admin/course/addlisteners",
                data: params,//传递给服务器的参数
                success: function (jsonresult) {
                    reload();
                    if (jsonresult.isSuccess == true) {
                        $.messager.alert("提示", '设置成功！', "info");
                        // (6)通知 课程表Grid 刷新数据
                    } else {
                        $.messager.alert("提示", '设置失败', "info");
                        return;
                    }
                }
            });

        }
    });


}
/**
 *  对某个教师设置听课人员，显示督导选择页面
 * @param id  记录
 */
function selectTech(id) {
    $('#listen_f').window('open');
     current_row_id=id;
    // 显示所有的督导人员
    var listen_url = '/index.php/admin/course/getlisteners';
    listen(grid_listener, listen_url, columns_listen);

}
/**
 *  显示督导人员的网格
 * @param grid
 * @param url
 * @param columns_listen
 */
function listen(grid, url, columns_listen) {
    $(grid).datagrid({
        url: url,
        method: 'post',
        fit: true,
        collapsible: false,
        pagination: true,
        checkOnSelect: false,
        height: 345,
        columns: columns_listen
    });
}
$(document).ready(function () {
    //当整个页面全部载入后才执行
    initGrid(grid_id, url, columns_def);
   // listen(grid, url, columns_listen);
    // 学期列表初始化
    initTermCombobox();
});

function initTermCombobox(){
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
 * 查询
 */
function query() {
    var search_filter = $('#frm_search').serializeJson();
    $(grid_id).datagrid(
        'load',
        search_filter
    );
}
/* add */
function add() {

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

/* reload */
function reload() {
    $(grid_id).datagrid('clearSelections');
    $(grid_id).datagrid('reload', {'nnn': 'aaa'});

}
/**
 * 编辑
 */
function edit() {
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
    var updateUrl = url_update + "/pk/" + idValue;
    location.href = updateUrl;
}

