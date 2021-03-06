/**
 * Created by Administrator on 2016/7/21.
 */
var grid_id='#datagrd';//表单名
var url_get='/index.php/admin/Tea/ac1';
var url_remove='/index.php/admin/Tea/remove';
var url_update='/index.php/admin/Tea/update';
var url_tree='/index.php/admin/Tea/ac1';
var url_export='/index.php/admin/Tea/download';
var pk_field='teach_id';
var cc="#cc";
var frm_search="#frm_search";
var url_remove_all = '/index.php/admin/tea/removeall';
var columns_def=[[
    {field: 'chkbox', checkbox: true},
    {field:'dept_name',title:'所属系部',sortable:true},
    {field:'sub_dept',title:'子部门',sortable:true},
    {field:'teach_name',title:'教师名',sortable:true,},
    {field:'sex',title:'性别',sortable:true},
    {field:'teach_id',title:'教师编号',sortable:true},
    {field:'teach_jw_name',title:'教务姓名',sortable:true},
    {field:'profess_duty',title:'专业技术职务',sortable:true},
    {field:'teach_phone',title:'电话',sortable:true},
    {field:'email',title:'电子邮箱',sortable:true},
    {field:'qq',title:'QQ号',sortable:true},
    {field:'holds_teacher',title:'兼职教师',sortable:true},
    {field:'conuncilor',title:'督导',sortable:true},
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
        idField:"teach_id",
        singleSelect:false,
        checkOnSelect: true,
        ctrlSelect:true,
        collapsible:false,
        pagination:true,
        rownumbers:true,
        pageSize:50,
        pageList:[20,40,50,80,100],
        iconCls:'icon-more',
        columns:columns_def,
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
        },
        onClickRow:function(index,row){
            detail(row.teach_id);
        }
    });
}
/**
 * 更新按钮状态
 * @param oGrid  网格对象
 */
function buttonStatus(oGrid) {
    var checkedItems = $(oGrid).datagrid('getChecked');
    if (checkedItems.length > 0) {

        $("#btn_remove").linkbutton({disabled: false});
        $("#btn_edit").linkbutton({disabled: false});
        $("#btn_listener").linkbutton({disabled: false});
        $("#btn_free").linkbutton({disabled: false});
    } else {

        $("#btn_remove").linkbutton({disabled: true});
        $("#btn_edit").linkbutton({disabled: true});
        $("#btn_listener").linkbutton({disabled: true});
        $("#btn_free").linkbutton({disabled: true});

    }
}
//当整个页面全部载入后才执行
$(document).ready(function () {

    initGrid(grid_id,url_get,columns_def);
    // 部门树
    $(cc).combotree({
        method: 'post',
        url:"/index.php/admin/dept/deptTree",
        required: true,
        multiple:true,
        lines:true,
        panelWidth:300,
        panelHeight:400
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



    // $('#pg').propertygrid({
    //     url: '/index.php/admin/tea/property',
    //     showGroup: false,
    //     scrollbarSize: 0
    // });
    // $.getJSON("/index.php/admin/Tea/treejosn",function(data) {
    //     $(cc).combotree('loadData', data);
    // });
    // 初始化按钮状态
    $("#btn_listener").linkbutton({disabled: true});
    $("#btn_free").linkbutton({disabled: true});
    $("#btn_remove").linkbutton({disabled: true});
    $("#btn_edit").linkbutton({disabled: true});
});
/**
 * 查询
 */
function query(){
     //var  a=$("#cc").combotree('getText');
    //document.getElementsByName("dict[dept_name]").value=a;
    var search_filter=$(frm_search).serializeJson();//把数据做成josn格式
   $(grid_id).datagrid(
       'load',//利用load方法提交search_filter 控制器ac1方法
       search_filter
    );
}
/**
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
    var url_down="ftp://10.127.98.242/static/picture/logo.png"
    location.href = url_down;
}

/**
 *  下载
 */

function exportXls() {
    var grid_options = $(grid_id).datagrid('options').queryParams;
    var acion = {'action': 'export'};
    var postdata = $.extend({}, grid_options, acion);
    $('#frm_download').form('submit', {
        url: '/index.php/admin/Tea/download',
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
    var opt_formatter="";
    opt_formatter=opt_formatter+"<a href='"+updateUrl+"' target='_self' title='编辑当前记录'> 编辑  </a>";
    opt_formatter=opt_formatter+" | <a href='#' onclick=\"detail(\'"+row.teach_id+"\')\" target='_self' title='查看当前记录的详细信息'> 查看详细信息  </a>";
    return opt_formatter;
}
/**
 * 属性网格初始化
 * @param id
 */
function detail(id){
    var mycolumns = [[
        {field:'name',title:'字段',width:100,sortable:true},
        {field:'value',title:'值',width:100,resizable:false}
    ]];
    $('#pg').propertygrid({
        url: '/index.php/admin/tea/property',
        method:"post",
        queryParams:{"id":id},
        showGroup: true,
        scrollbarSize: 0,
        columns: mycolumns
    });
    // $('#pg').propertygrid('reload', {'nnn': 'aaa'});
}
/**
 * 清空
 */
function clearForm() {
    $("input[name='dict[passed]']").val("");
    $("input[name='dict[profess_duty]']").val("");
    $("input[name='dict[teach_name]']").val("");
    $("input[name='dict[location]']").val("");
}
function importDialog() {
    var dialog_id="#dd";
    $(dialog_id).dialog({
        title: '导入数据',
        width: 400,
        height: 400,
        closed: false,
        cache: true,
        // href: 'get_content.php',
        modal: true
    });
}
/**
 * 上传提交
 */
function importxls(){
    var url_import="/index.php/admin/tea/upload";
    var grid_options = $(grid_id).datagrid('options').queryParams; //保存grid原有的参数
    var acion = {'action': 'import'};// 增加一个参数
    var postdata = $.extend({}, grid_options, acion); //合并参数
    $("#frm_upload").form('submit', {
        url: url_import,
        queryParams: postdata,
        onSubmit: function () {
        },
        success: function (data) {
            //解析返回的JSON
            var dataObj=eval("("+data+")");
            var isok=dataObj.success;
            var errors=dataObj.data;
            var message=dataObj.message;
            for(var key in errors){
                message=message+"\n "+key+"行："+errors[key];
            }
            $("#msgbox").val(message);
            console.log(dataObj.data);
            reload();
        }
    });
}
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

function printGrid(){
    // $(grid_id).print();
    window.open("/index.php/admin/classes/printgrid","_blank")
    //  location.href="http://10.127.98.246/index.php/admin/classes/printgrid";
    //$("#feeds").load("http://10.127.98.246/index.php/admin/classes/printgrid");
    // $("#feeds").print();
    /* $.get("http://10.127.98.246/index.php/admin/classes/printgrid",function(data,status){
     // console.log($(data).find("h1"))
     // alert($(data).find("#datagrd").html());
     });*/

}
function email(){
    //
    var url_email="/index.php/admin/tea/email";
    var checkedItems = $(grid_id).datagrid('getChecked');
    if (checkedItems.length == 0) {
        $.messager.alert("提示", "请选择需要操作的行！", "info");
        return;
    }
    //将选中行的主健值放到一个数组中
    var selectedRowsID = [];
    $.each(checkedItems, function (index, item) {
        selectedRowsID.push(this.teach_id);
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
                //if (jsonresult.isSuccess == true) {
                //    $.messager.alert("提示", jsonresult.message, "info");
                //} else {
                //    $.messager.alert("提示", jsonresult.message, "info");
                //    return;
                //}
            }
        });
    });
}
/**
 * 显示教师课表
 */
function showCourseTable(){
    var url='/index.php/admin/tea/lessontable?id=';

    var checkedItems = $(grid_id).datagrid('getChecked');
    if (checkedItems.length != 1) {
        $.messager.alert("提示", "请选择需要操作的行！", "info");
        return;
    }
    var tid=checkedItems[0].teach_id;
    url=url+tid;
    $('#dialog_course_table').dialog({
        title: '教师课表',
        width: 600,
        height: 500,
        closed: false,
        cache: false,
        modal: true
    });
    $('#dialog_course_table').dialog('refresh', url);
}
