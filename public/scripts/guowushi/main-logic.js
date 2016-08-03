/**
 * Created by guowushi on 2016/7/18.
 */
/*  定义一个AMD规范的模块  */
define(['jquery','easyui'],function(){
   //模块实现代码
    $(document).ready(function () {
        $('#datagrd').datagrid({
            url:'http://10.127.98.246/index.php/guowushi/controllerguowushi/action4',
            method:'post',
            singleSelect:true,
            collapsible:false,
            pagination:true,
            toolbar:'#searchToolbar',
            columns:[[
                {field:'banji',title:'班级'},
                {field:'student_id',title:'学号'},
                {field:'name',title:'姓名',sortable:true},
                {field:'sex',title:'性别'},
                {field:'id_card',title:'身份证'},
                {field:'phone',title:'电话'}
            ]]
        });
    });

})

