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

function initForm(){
    $('#dept_name').combobox({
        url: '/index.php/admin/Tea/deptinfo',
        valueField: 'dept_name',
        textField: 'dept_name'
    });
}
function saveForm(){
    $(form_id).form({
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
    $(form_id).submit();
}
/**
 * 清空
 */
function clearForm() {
    $(form_id).form('clear');
}
