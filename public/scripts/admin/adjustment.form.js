/**
 * Created by FuJinsong on 2016/7/21.
 */

var grid_id='#datagrd';
var grid='#datagrd';
var url_save='/index.php/admin/adjustment/save';
var frm_id="#ff";
//当整个页面全部载入后才执行
$(document).ready(function(){
    initForm();
});
/**
 *
 */
function initForm(){
    $('#dept_name').combobox({
        url: '/index.php/admin/adjustment/deptinfo',
        valueField: 'dept_name',
        textField: 'dept_name'
    });

}
/**
 * 通过Ajax方式保存
 */
function saveForm(){
    $(frm_id).form({
        url:url_save,
        onSubmit: function(){
            // 有效性验证
            var classname=$("#adj_id").val();
            if(classname==''){
                return false ;
            }
        },
        success:function(data){
            var data = eval('(' + data + ')');  // change the JSON string to javascript object
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
 * 查询
 */
function query(){
    var search_filter=$('#frm_search').serializeJson();
    $('#datagrd').datagrid(
        'load',
        search_filter
    );
}