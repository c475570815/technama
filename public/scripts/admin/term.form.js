/**
 * Created by FuJinsong on 2016/8/9.
 */

var grid_id='#datagrd';
var url_save='/index.php/admin/term/save';
var form_id='#ff';
//当整个页面全部载入后才执行

/**
 * 通过Ajax方式保存
 */
function saveForm(){
    $(form_id).form({
        url:url_save,
        onSubmit: function(){
            // 有效性验证
            var termname=$("#term_name").val();
            if(termname==''){
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
