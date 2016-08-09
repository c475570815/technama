/**
 * Created by guowushi on 2016/7/20.
 */
/**
 *
 */
var form_id='#ff'; //表单对象的ID
var url_save='/index.php/admin/dict/save';  //远程保存数据的Action
//当整个页面全部载入后才执行
$(document).ready(function(){
    initForm();
});
/**
 * 通过Ajax方式保存
 */
function saveForm(){
    $(form_id).form({
        url:url_save,
        onSubmit: function(){
            // 有效性验证
            return  validate();
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

/* 实现自己的有效性验证 ,返回值false则取消表单的提交 */
function validate(){

}

/* 实现自己的表单界面初始化 */
function initForm(){
    /* $('#dept_name').combobox({
     url: 'http://10.127.98.246/index.php/admin/classes/deptinfo',
     valueField: 'dept_name',
     textField: 'dept_name'
     });*/
}

