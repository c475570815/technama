/**
 * Created by FuJinsong on 2016/8/11.
 */

var form_id="#ff";
var url_save='/index.php/admin/dept/save';

/**
 * 通过Ajax方式保存
 */
$(document).ready(function(){
    initForm();
});
function saveForm(){
    $(form_id).form({
        url:url_save,
        onSubmit: function(){
            // 有效性验证
            var classname=$("#class_name").val();
            if(classname==''){
                return false ;
            }
        },
        success:function(data){
            var data = eval('(' + data + ')');
            $.messager.alert('Info', data.message, 'info');
        }
    });
    $(form_id).submit();
}
/**
 * 清除表单
 */
function clearForm() {
    $(form_id).form('clear');
}
/**
 * 返回
 */
function returnGrid(){
    history.back();
}

function initForm(){
    $("input[name=data\\[dept_category\\]]").combobox({
        url: '/index.php/admin/dict/getDictByCategory',
        method:'POST',
        queryParams:{category:'部门类型'},
        valueField: 'dict_value',
        textField: 'dict_key',
        multiple:false
    });

}