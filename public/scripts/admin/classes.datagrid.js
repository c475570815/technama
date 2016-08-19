var SearchForm = function (element) {
    var that = this;//作为函数调用时，this 绑定到全局对象；作为方法调用时，this 绑定到该方法所属的对象。
    this.element = element;
    var element_form_search=element;
    var element_grid; //与查找表单关联的Grid
    /**
     * 初始化
     * (1)将搜索表单与Gird绑定
     * (2)初始化数据
     * @param option
     */
    this.init = function (option) {
        this.BTN_SEARCH=option.btn_search;
        this.BTN_RESET=option.btn_reset;
        if(this.BTN_SEARCH){
            this.BTN_SEARCH.bind("click",this.query);
        }
        if(this.BTN_RESET){
            this.BTN_RESET.bind("click",this.reset);
        }
    }
    /**
     * 将搜索表单与Gird 绑定
     * @param grid
     */
    this.bindGrid=function(grid){
        this._bindGrid=grid;
        element_grid=grid;
    }
    /**
     * 表单查询
     */
    this.query = function () {
        //console.log(that.serializeJson());
        var search_filter =  element.serializeJson();//表单数据序列化成JSON数组
        var acion = {'action': 'search'};
        var postdata = $.extend({}, search_filter, acion);
        element_grid.datagrid("load", postdata);
        grid_options = element_grid.datagrid('options').queryParams;
        // console.log(grid_options);
    }
    /**
     * 查找表单清除
     */
    this.reset = function () {
        element_form_search.form('clear');
    }
}
/*  增加一个UI初始化的方法 */
SearchForm.prototype.initUI=function(){
    // 系部列表
    // var cbo_dept=element_form_search.find("input[name=dict\\[dept_name\\]]");
    // cbo_dept.combobox({
    //     url: '/index.php/admin/term/getterm',
    //     method:'POST',
    //     valueField: 'term_name',
    //     textField: 'term_name',
    //     limitToList:false
    // });

}
/*   --------------------------------------------------------        */
var MyDataGrid = function (element) {
    self = this;
    // grid 的默认参数
    this.options = {
        method: 'post',
        title: "详细信息",
        singleSelect: false,
        collapsible: false,
        pagination: true,
        fitColumns: true,
        rownumbers: true,
        pageSize: 20
    };

    //工具栏的参数
    this.toolbaroption={};
    this.grid = element;   //必须
    // 与Grid 相关的组件
    var _dialog_upload;  //对话框
    var _toolbar  ;   // 工具栏
    this.setToolbar=function(toolbar){
        this.toolbar = toolbar;
        _toolbar=toolbar;
    }
    this.setDiaglog=function(diaglog){
        _dialog_upload=diaglog;
    }
    /**
     * 初始化网格对象
     */
    this.init = function (option) {
        if (option.url) {
            this.get_url = option.url;
        }
        if (option.idField) {
            this.idField = option.idField;
        }
        if (option.columns) {
            this.columns = option.columns;
        }

        // var option=$.extend({}, options, acion);
        var default_option = {
            method: 'post',
            title: "详细信息",
            singleSelect: false,
            collapsible: false,
            pagination: true,
            fitColumns: true,
            rownumbers: true,
            pageSize: 20
        };
        var options = $.extend({}, default_option, option);
        this.initGird(options);
    }

    /**
     * 工具栏初始化
     * btn_edit btn_add btn_remove btn_export  btn_import
     * btn_print
     * btn_removeall
     * btn_refresh
     * btn_help
     * @param options
     */
    this.initToolbar = function (options) {
        this.toolbaroption=options; //工具栏选项
        // 导入：如果没有定义url，则禁用该按钮；否则绑定事件
        var btn_import = this.toolbar.find(options.btn_import.id);
        if (!options.btn_import.url) {
            btn_import.linkbutton('disable');
        } else {
            btn_import.bind("click", this.importDialog);
        }
        // 导出
        var btn_export = this.toolbar.find(options.btn_export.id);
        if (!options.btn_export.url) {
            btn_export.linkbutton('disable');
        } else {
            btn_export.bind("click", this.exportXls);
        }
        // 添加
        var btn_add = this.toolbar.find(options.btn_add.id);
        if (!options.btn_add.url){
            btn_add.linkbutton('disable');
        } else {
            btn_add.bind("click", this.addRecord);
        }
        // 修改
        var btn_edit = this.toolbar.find(options.btn_edit.id);
        if (!options.btn_edit.url) {
            btn_edit.linkbutton('disable');
        } else {
            btn_edit.bind("click", this.edit);
        }
        // 删除
        var btn_remove = this.toolbar.find(options.btn_remove.id);
        if (!options.btn_remove.url) {
            btn_remove.linkbutton('disable');
        } else {
            btn_remove.bind("click", this.removeRecord);
        }
        // 清除所有记录
        var btn_removeall = this.toolbar.find(options.btn_removeall.id);
        if (!options.btn_removeall.url) {
            btn_removeall.linkbutton('disable');
        } else {
            btn_removeall.bind("click", this.removeall);
        }
        // 刷新
        var btn_refresh = this.toolbar.find(options.btn_refresh);
        if (!options.btn_refresh) {
            btn_refresh.linkbutton('disable');
        } else {
            btn_refresh.bind("click", this.reload);
        }
        // 打印
        var btn_print = this.toolbar.find(options.btn_print);
        if (!options.btn_print) {
            btn_print.linkbutton('disable');
        } else {
            btn_print.bind("click", this.printGrid);
        }
    }
    /**
     * 初始化Grid显示
     * @param option
     */
    this.initGird = function (options) {
        // 网格必须有url,idField,columns设置才能正常显示
        if (options.url && options.idField && options.columns) {
            this.grid.datagrid(options);
        } else {
            $.messager.alert("提示", "网格参数设置错误！", "info");
        }

    }
    /**
     * 列格式函数
     * @param val
     * @param row
     * @param index
     * @returns {string}
     */
    this.formatOptColumn = function (val, row, index) {
        var updateUrl =  self.toolbaroption.btn_edit.url + "/pk/" + row.class_id;
        var opt_formatter = "<a href='" + updateUrl + "' target='_self' title='编辑当前记录'> 编辑 </a>";
        return opt_formatter;
    }
    /**
     * 刷新网格(清除选中行，重载记录)
     */
    this.reload = function () {
        this.grid.datagrid('clearSelections');
        this.grid.datagrid('reload');
    }
    /**
     *  删除所有记录,清除前提示
     */
    this.removeall = function () {
        $.messager.confirm('提示', '是否删除所有的数据?', function (r) {
            if (!r) {
                return;
            }
            $.ajax({
                type: "POST",
                url:  self.toolbaroption.btn_removeall.url,
                data: {id: 1},//传递给服务器的参数
                success: function (jsonresult) {
                    self.reload(); //重新载入
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
     * 删除选中的多条记录
     */
    this.removeRecord = function () {
        var checkedItems = self.grid.datagrid('getChecked');//返回选中记录的数组
        if (checkedItems.length == 0) {
            $.messager.alert("提示", "请选择要删除的行！", "info");
            return;
        }
        //将数组中的主健值放到一个数组中 ,['软件1','网络1']
        var removeID = [];
        $.each(checkedItems, function (index, item) {
            removeID.push(item[self.idField]);
        });
        // console.log(removeID);
        $.messager.confirm('提示', '是否删除选中数据?', function (r) {
            if (!r) {
                return;
            }
            //Ajax提交
            $.ajax({
                type: "POST",
                url: self.toolbaroption.btn_remove.url,
                data: {id: removeID},//传递给服务器的参数
                success: function (jsonresult) {
                    self.reload();
                    if (jsonresult.isSuccess == true) {
                        $.messager.alert("提示", jsonresult.message, "info");
                    } else {
                        $.messager.alert("提示", jsonresult.message, "info");
                        return;
                    }
                }
            });
        });
        //console.log(names.join(","));
    }
    /**
     * 添加纪录
     */
    this.addRecord = function () {
        location.href = self.toolbaroption.btn_add.url;
    }
    /**
     * 编辑选择的一条记录
     */
    this.edit = function () {
        var rows = self.grid.datagrid('getSelections');
        if (rows.length != 1) {
            $.messager.show({
                title: "",
                msg: "请选择一条记录"
            });
            return;
        }
        var row = self.grid.datagrid('getSelected');
        var idValue = row[self.idField];
        location.href =  self.toolbaroption.btn_edit.url + "/pk/" + idValue;
    }

    /**
     *  下载表格中的数据
     */
    this.exportXls = function () {
        var grid_options = self.grid.datagrid('options').queryParams;
        var acion = {'action': 'export'};
        var postdata = $.extend({}, grid_options, acion);
        // console.log(self.toolbaroption.btn_export.url);
        var _form=$("<form method='post'></form>");
        _form.form('submit', {
            url: self.toolbaroption.btn_export.url,
            queryParams: postdata,
            onSubmit: function () {
            },
            success: function (data) {
                var dataObj = eval("(" + data + ")");
                $.messager.show({
                    msg: '导出成功！',
                    showType: 'show',
                    style: {
                        right: '',
                        top: document.body.scrollTop + document.documentElement.scrollTop,
                        bottom: ''
                    }
                });
            }
        });
    }
    /**
     *  显示上传窗体
     */
    this.importDialog = function () {
        // console.log(self.dialog_upload);
        if(_dialog_upload){
            _dialog_upload.dialog({
                title: '导入数据',
                width: 400,
                height: 400,
                closed: false,
                cache: true,
                // href: 'get_content.php',
                modal: true
            });
            // $(_dialog_upload).find(".btn_submit").bind("click",self.importxls);
            $(".btn_submit").bind("click",self.importxls);
        }
    }
    /**
     * 提交上传
     */
    this.importxls = function () {
        var grid_options = self.grid.datagrid('options').queryParams; //保存grid原有的参数
        var acion = {'action': 'import'};    // 增加一个action参数
        var postdata = $.extend({}, grid_options, acion); //合并参数
        $(_dialog_upload).find("form").form('submit', {
            url: self.toolbaroption.btn_import.url,
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
                $(_dialog_upload).find("#msgbox").val(message);//显示
                // console.log(dataObj.data);
                self.reload();
            }
        });
    }
    /**
     * 打印
     */
    this.printGrid = function () {
        // $(grid_id).print();
        window.open(this.url_print, "_blank")
    }


}
// var grid_id = '#datagrd';
// var search_form_id="#frm_search";
// var download_form_id="#frm_download";
// var url_get = '/index.php/admin/classes/index';
// var url_remove = '/index.php/admin/classes/remove';
// var url_remove_all = '/index.php/admin/classes/removeall';
// var url_update = '/index.php/admin/classes/update';
// var url_export = '/index.php/admin/classes/download';
// var pk_field = 'class_id';
// var grid_options;
// var columns_def = [[
//     {field: 'chkbox', checkbox: true},
//     {field: 'dept_name', title: '所属系部', sortable: true},
//     {field: 'class_name', title: '班级名称', sortable: true},
//     {field: 'class_room', title: '班级固定教室', sortable: true},
//     {field: 'class_supervisor', title: '班级导师编号', sortable: true},
//     {field: 'calss_adviser', title: '班级班主任', sortable: true},
//     {field:"operation", title: '操作', formatter:formatOptColumn }
// ]];
// /**
//  *  初始化网格对象
//  *  idField:主键
//  * @param grid
//  * @param url
//  * @param columns_def
//  */
// function initGrid(grid, url, columns_def) {
//     $(grid_id).datagrid({
//         url: url_get,
//         idField:pk_field,
//         method: 'post',
//         title: "详细信息",
//         singleSelect: false,
//         collapsible: false,
//         pagination: true,
//         fitColumns:true,
//         rownumbers: true,
//         pageSize:20,
//         columns: columns_def
//     });
// }
// /**
//  * 定义列的显示
//  * @param val
//  * @param row
//  * @returns {*}
//  */
// function formatOptColumn(val,row,index){
//     var updateUrl = url_update + "/pk/" + row.class_id;
//     var opt_formatter="<a href='"+updateUrl+"' target='_self' title='编辑当前记录'> 编辑 </a>";
//     return opt_formatter;
// }
//
// /**
//  * 查询
//  */
// function query() {
//     var search_filter = $(search_form_id).serializeJson();
//     var acion = {'action': 'search'};
//     var postdata = $.extend({}, search_filter, acion);
//     $(grid_id).datagrid(
//         'load',
//          postdata
//     );
//     grid_options = $(grid_id).datagrid('options').queryParams;
//     console.log(grid_options);
// }
// /**
//  *  清楚所有记录,清除前提示
//  */
// function removeall(){
//     $.messager.confirm('提示', '是否删除所有的数据?', function (r) {
//         if (!r) {
//             return;
//         }
//         //Ajax提交
//         $.ajax({
//             type: "POST",
//             url: url_remove_all,
//             data: {id: 1},//传递给服务器的参数
//             success: function (jsonresult) {
//                 reload();
//                 if (jsonresult.isSuccess == true) {
//                     $.messager.alert("提示", jsonresult.message, "info");
//                 } else {
//                     $.messager.alert("提示", jsonresult.message, "info");
//                     return;
//                 }
//             }
//         });
//     });
// }
// /**
//  * 删除选中的记录
//  */
// function removeRecord() {
//     var checkedItems = $(grid_id).datagrid('getChecked');//返回选中记录的数组
//     if (checkedItems.length == 0) {
//         $.messager.alert("提示", "请选择要删除的行！", "info");
//         return;
//     }
//     //将数组中的主健值放到一个数组中 ,['软件1','网络1']
//     var removeID = [];
//     $.each(checkedItems, function (index, item) {
//         removeID.push(item.class_id);
//     });
//     console.log(removeID);
//     $.messager.confirm('提示', '是否删除选中数据?', function (r) {
//         if (!r) {
//             return;
//         }
//         //Ajax提交
//         $.ajax({
//             type: "POST",
//             url: url_remove,
//             data: {id: removeID},//传递给服务器的参数
//             success: function (jsonresult) {
//                 reload();
//                 if (jsonresult.isSuccess == true) {
//                     $.messager.alert("提示", jsonresult.message, "info");
//                 } else {
//                     $.messager.alert("提示", jsonresult.message, "info");
//                     return;
//                 }
//             }
//         });
//     });
//     //console.log(names.join(","));
// }
// /**
//  * 刷新网格
//  */
// function reload() {
//     $(grid_id).datagrid('clearSelections');
//     $(grid_id).datagrid('reload');
// }
// /**
//  * 编辑
//  */
// function edit() {
//     var rows = $(grid_id).datagrid('getSelections');
//     if (rows.length != 1) {
//         $.messager.show({
//             title: "",
//             msg: "请选择一条记录"
//         });
//         return;
//     }
//     var row = $(grid_id).datagrid('getSelected');
//     var idValue = row[pk_field];
//     console.log(idValue);
//     var updateUrl = url_update + "/pk/" + idValue;
//     location.href = updateUrl;
// }
//
// /**
//  *  下载
//  */
// function exportXls() {
//     var grid_options = $(grid_id).datagrid('options').queryParams;
//     var acion = {'action': 'export'};
//     var postdata = $.extend({}, grid_options, acion);
//     $(download_form_id).form('submit', {
//         url: url_export,
//         queryParams: postdata,
//         onSubmit: function () {
//         },
//         success: function (data) {
//             var dataObj=eval("("+data+")");
//             $.messager.show({
//                 msg:'删除成功！',
//                 showType:'show',
//                 style:{
//                     right:'',
//                     top:document.body.scrollTop+document.documentElement.scrollTop,
//                     bottom:''
//                 }
//             });
//             console.log(data);
//
//         }
//     });
// }
// /**
//  *  显示上传窗体
//  */
// function importDialog() {
//     var dialog_id="#dd";
//     $(dialog_id).dialog({
//         title: '导入数据',
//         width: 400,
//         height: 400,
//         closed: false,
//         cache: true,
//        // href: 'get_content.php',
//         modal: true
//     });
// }
// /**
//  * 上传提交
//  */
// function importxls() {
//     var url_import="/index.php/admin/classes/upload";
//     var grid_options = $(grid_id).datagrid('options').queryParams; //保存grid原有的参数
//     var acion = {'action': 'import'};// 增加一个参数
//     var postdata = $.extend({}, grid_options, acion); //合并参数
//     $("#frm_upload").form('submit', {
//         url: url_import,
//         queryParams: postdata,
//         onSubmit: function () {
//         },
//         success: function (data) {
//             //解析返回的JSON
//             var dataObj=eval("("+data+")");
//             var isok=dataObj.success;
//             var errors=dataObj.data;
//             var message=dataObj.message;
//             for(var key in errors){
//                 message=message+"\n "+key+"行："+errors[key];
//             }
//             $("#msgbox").val(message);
//             console.log(dataObj.data);
//             reload();
//         }
//     });
// }
// /**
//  * 打印
//  */
// function printGrid(){
//    // $(grid_id).print();
//     window.open("/index.php/admin/classes/printgrid","_blank")
// }
// /**
//  * 添加纪录
//  */
// function addRecord(){
//    location.href='/index.php/admin/classes/add';
// }
// /**
//  * 查找表单清除
//  */
// function reset(){
//     $("#frm_search").form('clear');
//     $("option[name='all']").attr("selected","selected");
// }

/**
 * 当整个页面全部载入后才执行
 */
$(document).ready(function () {
    // initGrid(grid_id, url_get, columns_def);
    /* （1）初始化网格 */
    var datagrid = new MyDataGrid($("#datagrd"));
    var grid_option = {
        "url": '/index.php/admin/classes/index',
        "idField": "class_id",
        "columns": [[
            {field: 'chkbox', checkbox: true},
            {field: 'dept_name', title: '所属系部', sortable: true},
            {field: 'class_name', title: '班级名称', sortable: true},
            {field: 'class_room', title: '班级固定教室', sortable: true},
            {field: 'class_supervisor', title: '班级导师编号', sortable: true},
            {field: 'calss_adviser', title: '班级班主任', sortable: true},
            {field: "operation", title: '操作', formatter: datagrid.formatOptColumn}
        ]]
    };
    datagrid.init(grid_option);
    /**
     *  （2）定义Toolbar工具栏
     * */
    var toolbar_options = {
        "btn_add": {"id":"#btn_add","url":"/index.php/admin/classes/add"},
        "btn_edit":{"id":"#btn_edit","url":"/index.php/admin/classes/update"},
        "btn_remove": {"id":"#btn_remove","url":"/index.php/admin/classes/remove"},
        "btn_removeall":{"id": "#btn_removeall","url":"/index.php/admin/classes/removeall"},
        "btn_export": {"id":"#btn_export","url":"/index.php/admin/classes/download"},
        "btn_import": {"id":"#btn_import","url":"/index.php/admin/classes/upload"},
        "btn_print": {"id":"#btn_print","url":"/index.php/admin/classes/printgrid"},
        "btn_refresh": {"id":"#btn_refresh","url":""},
        "btn_help": {"id":"#btn_help","url":""}
    };
    datagrid.setToolbar($("#editToolbar"));//工具栏
    datagrid.initToolbar(toolbar_options);
    datagrid.setDiaglog($("#dd"));//对话框
    /* （3）初始化查找表单  */
    var frm_search = new SearchForm($("#frm_search"));
    frm_search.init({
        "btn_search":$("#btn_search"),
        "btn_reset":$("#btn_reset")
    });

    frm_search.bindGrid($("#datagrd"));

});
