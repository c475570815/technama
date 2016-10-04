/**
 * Created by Administrator on 2016/7/21.
 */
var grid_id = '#datagrd';//表单名
var url_get = '/index.php/admin/Tea/getlist';
var url_remove = '/index.php/admin/Tea/remove';
var url_update = '/index.php/admin/Tea/update';
var url_tree = '/index.php/admin/Tea/ac1';
var url_export = '/index.php/admin/Tea/download';
var pk_field = 'teach_id';
var cc = "#cc";
var frm_search = "#frm_search";
var url_remove_all = '/index.php/admin/tea/removeall';
var columns_def = [[
    {field: 'chkbox', checkbox: true},
    {field: 'dept_name', title: '所属系部', sortable: true},
    {field: 'sub_dept', title: '子部门', sortable: true},
    {field: 'teach_name', title: '教师名', sortable: true,},
    {field: 'sex', title: '性别', sortable: true},
    {field: 'teach_id', title: '教师编号', sortable: true},
    // {field: 'profess_duty', title: '专业技术职务', sortable: true},

    {field: 'email', title: '电子邮箱', sortable: true},
    {field: 'email_validated', title: '邮箱验证', sortable: true},
    {field: 'teach_phone', title: '电话', sortable: true},
    {field: 'mobile_validated', title: '电话验证', sortable: true},
    // {field: 'holds_teacher', title: '兼职教师', sortable: true},
    // {field: 'conuncilor', title: '督导', sortable: true},
    {field: 'location', title: '职位', sortable: true}
    // {field: 'passed', title: '是否免听', sortable: true},
    // {field: 'limit', title: '听课限制', sortable: true},
    // {field: "op", title: '操作', formatter: formatOptColumn}
]];
var checkedItems = [];

function initGrid(grid_id, url_get, columns_def) {
    $(grid_id).datagrid({
        url: url_get,
        method: 'post',
        title: "详细信息",
        idField: "teach_id",// 很重要
        singleSelect: false,
        collapsible: false,
        pagination: true,
        rownumbers: true,
        pageSize: 20,
        iconCls: 'icon-more',

        columns: columns_def
    });
}
//当整个页面全部载入后才执行
$(document).ready(function () {
    //textbox初始化
    $('.tb').textbox({
        // buttonText:'Search',
        // iconCls:'icon-man',
        prompt: "请输入通知标题",
        iconAlign: 'left',
        label: "邮件标题",
        labelWidth: 100,
        labelPosition: "top",


    });

    // 网格初始化
    initGrid(grid_id, url_get, columns_def);
    //部门树形
    $("#tt").tree({
        method: 'post',
        url: "/index.php/admin/dept/deptTree",
        required: true,
        multiple: true,
        checkbox: true,
        lines: true,
        onCheck: function (node, checked) {
            // 获取所有选中的节点
            var nodes = $('#tt').tree('getChecked');
            var param = [];
            var param_subdept = [];
            for (var i in nodes) {//
                // console.log(nodes[i].attributes.level);
                if(nodes[i].attributes.level=="1"){
                    param.push(nodes[i].text);
                }else if(nodes[i].attributes.level=="2"){
                    param_subdept.push(nodes[i].text);
                }

            }
            // console.log(param);
            $(grid_id).datagrid(
                'load',
                {
                    "dict[dept_name]": param,
                    "dict[sub_dept]": param_subdept
                }
            );
        }
    });
    // $.getJSON("/index.php/admin/Tea/treejosn",function(data) {
    //     $(cc).combotree('loadData', data);
    // });
    //console.log(111111111111);
    // 定时短信
    $('#dt_sms').datetimebox({
        width:200,
        disabled:true,
        editable:false
    });
    $('#cbo_dt_sms').bind('click', function () {
        if($("#cbo_dt_sms").prop('checked')){
            $('#dt_sms').datetimebox('enable');
        }else{
            $('#dt_sms').datetimebox('disable');
        }

    });
    // 定时邮件
    $('#dt_email').datetimebox({
        width:200,
        disabled:true,
        editable:false
    });
    $('#cbo_dt_email').bind('click', function () {

        if($("#cbo_dt_email").prop('checked')){
            $('#dt_email').datetimebox('enable');
        }else{
            $('#dt_email').datetimebox('disable');
        }
    });
});
    /**
     * 根据条件查询
     */
    function query() {
        // 把数据做成josn格式，得到最新的查询条件
        var search_filter = $(frm_search).serializeJson();
        // 获取网格现有的查询参数
        var _queryParams = $(grid_id).datagrid('options').queryParams;
        // 将原有查询条件和新查询条件合并
        var search_filter = $.extend({}, search_filter, _queryParams);
        // console.log(search_filter);
        //利用load方法提交search_filter 控制器ac1方法
        $(grid_id).datagrid(
            'load',
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
        $.each(checkedItems, function () {
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
                data: {id: removeID},//传递给服务器的参数
                success: function (result) {
                    //result = eval('(' + result + ')');//把字符串变成一个JS对象
                    //result= eval('(' + result + ')');
                    $(grid_id).datagrid('clearSelections');
                    $(grid_id).datagrid('reload');
                    if (result.success == true) {
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
    function down() {
        var url_down = "ftp://10.127.98.242/static/picture/logo.png"
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
    function formatOptColumn(val, row, index) {
        var updateUrl = url_update + "/pk/" + row.teach_id;
        return "<a href='" + updateUrl + "' target='_self'> 操作 </a>";

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
        var dialog_id = "#dd";
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
    function importxls() {
        var url_import = "/index.php/admin/tea/upload";
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
                var dataObj = eval("(" + data + ")");
                var isok = dataObj.success;
                var errors = dataObj.data;
                var message = dataObj.message;
                for (var key in errors) {
                    message = message + "\n " + key + "行：" + errors[key];
                }
                $("#msgbox").val(message);
                console.log(dataObj.data);
                reload();
            }
        });
    }

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

    function printGrid() {
        // $(grid_id).print();
        window.open("/index.php/admin/classes/printgrid", "_blank")
        //  location.href="http://10.127.98.246/index.php/admin/classes/printgrid";
        //$("#feeds").load("http://10.127.98.246/index.php/admin/classes/printgrid");
        // $("#feeds").print();
        /* $.get("http://10.127.98.246/index.php/admin/classes/printgrid",function(data,status){
         // console.log($(data).find("h1"))
         // alert($(data).find("#datagrd").html());
         });*/

    }

    function email() {
        //
        var url_email = "/index.php/admin/tea/email";
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
        var post_params;
        // 获取时间
        var dt = $('#dt').datetimebox('getValue');
        if($("#cbo_dt").prop('checked')){

            post_params={id: selectedRowsID,schedule:dt};
        }else{
            post_params={id: selectedRowsID};
        }

        console.log(dt);
        $.messager.confirm('提示', '是否发送电邮通知?', function (ans) {
            if (!ans) {
                return;
            }
            $.ajax({
                type: "POST",
                url: url_email,
                data: post_params,//传递给服务器的参数
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

