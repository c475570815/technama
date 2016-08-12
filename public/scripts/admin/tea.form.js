/**
 * Created by Administrator on 2016/7/22.
 */
/**
 * Ajax方式保存
 */
var grid_id='#datagrd';
var url_save='/index.php/admin/Tea/save';
var form_id="#ff";
//当整个页面全部载入后才执行
$(document).ready(function(){
    initForm();
});
// 初始化表单
function initForm(){
    // 系部
    $("input[name=data\\[dept_name\\]]").combobox({
        url: '/index.php/admin/Tea/deptinfo',
        valueField: 'dept_name',
        textField: 'dept_name',
        limitToList:true,
        onSelect:function(){
            // 子部门
            var dept=$("input[name=data\\[dept_name\\]]").val();
            $("input[name=data\\[sub_dept\\]]").combobox({
                url: '/index.php/admin/dept/getsubdept',
                method:'POST',
                queryParams:{parent:dept},
                valueField: 'dept_name',
                textField: 'dept_name',
                limitToList:false
            });
        }
    });


    // 角色
    $("input[name=data\\[teach_role\\]]").combobox({
        url: '/index.php/admin/dict/getDictByCategory',
        method:'POST',
        queryParams:{category:'角色'},
        valueField: 'dict_value',
        textField: 'dict_key',
        separator:"|",
        multiple:true
    });
    // 是否督导
    $("input[name=data\\[conuncilor\\]]").combobox({
        valueField: 'label',
        textField: 'value',
        data: [{
            label: '是',
            value: '是'
        },{
            label: '否',
            value: '否'
        }]
    });
    // 是否兼课
    $("input[name=data\\[holds_teacher\\]]").combobox({
        valueField: 'label',
        textField: 'value',
        data: [{
            label: '是',
            value: '是'
        },{
            label: '否',
            value: '否'
        }]
    });
    // 是否免听
    $("input[name=data\\[passed\\]]").combobox({
        valueField: 'label',
        textField: 'value',
        data: [{
            label: '是',
            value: '是'
        },{
            label: '否',
            value: '否'
        }]
    });
}
function saveForm(){
    $(form_id).form({
        url:url_save,
        onSubmit: function(){

        },
        success:function(data){
            var data = eval('(' + data + ')');  // change the JSON string to javascript object
            //alert(data);
            $.messager.alert('提示', data.message, 'info');
        }
    });
    $(form_id).submit();
}
/**
 * 清空
 */
function clearForm() {
    $(form_id).form('clear');
}
