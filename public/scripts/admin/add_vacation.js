/**
 * Created by Administrator on 2016/8/2.
 */
var url_save="http://10.127.98.246/index.php/admin/termclendar/add_vacation"
function hideHeader(){
    $('#pg').propertygrid({
        showHeader:false
    });
}
$(document).ready(function () {
    //当整个页面全部载入后才执行
    hideHeader();//隐藏头
});
function  save(){
    $rows=$('#pg').propertygrid('getRows');
    console.log($rows[0]['value']);
}
function saveForm(){
    $('#ff').form({
        url:url_save,
        onSubmit: function(){
            $rows=$('#pg').propertygrid('getRows');
            document.getElementById('start').value=$rows[0]['value'];
            document.getElementById('end').value=$rows[1]['value'];
        },
        success:function(data){
            var data = eval('(' + data + ')');  // change the JSON string to javascript object
            $.messager.alert('提示', data.message, 'info');
        }
    });
    $('#ff').submit();
}