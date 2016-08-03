/**
 * Created by Administrator on 2016/7/22.
 */
/**
 * Ajax方式保存
 */
var grid_id='#datagrd';
var url_save='http://10.127.98.246/index.php/admin/Tea/save';
//当整个页面全部载入后才执行
$(document).ready(function(){
    initForm();
});

function initForm(){
    $('#dept_name').combobox({
        url: 'http://10.127.98.246/index.php/admin/Tea/deptinfo',
        valueField: 'dept_name',
        textField: 'dept_name'
    });
}
function saveForm(){
    $('#ff').form({
        url:url_save,
        onSubmit: function(){
            var tech_id=$("#tech_id").val();
            if(tech_id==''){
                return false ;
            }
        },
        success:function(data){
            var data = eval('(' + data + ')');  // change the JSON string to javascript object
            //alert(data);
            $.messager.alert('提示', data.message, 'info');
        }
    });
    $('#ff').submit();
}
/**
 * 清空
 */
function clearForm() {
    $('#ff').form('clear');
}
