/**
 * Created by guowushi on 2016/7/20.
 */
/**
 *
 */
var grid_id='#datagrd';
var url_save='/index.php/admin/classes/save';
//当整个页面全部载入后才执行
$(document).ready(function(){
    initForm();
});

function initForm(){
    $('#dept_name').combobox({
        url: '/index.php/admin/classes/deptinfo',
        valueField: 'dept_name',
        textField: 'dept_name'
    });
}
/**
 * 通过Ajax方式保存
*/
function saveForm(){
    $('#ff').form({
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
    $('#ff').submit();
}
/**
 * 清除表单
 */
function clearForm() {
    $('#ff').form('clear');
}
/**
 * 返回
 */
function returnGrid(){
    history.back();
}
