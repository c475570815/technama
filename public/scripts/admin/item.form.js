/**
 * Created by Administrator on 2016/7/24.
 */
/**
 * Ajax方式保存
 */
var url_save='http://10.127.98.242/index.php/admin/Evaluate/save';
var ff="#ff";
//当整个页面全部载入后才执行
function saveForm(){
    $(ff).form({
        url:url_save,
        onSubmit: function(){
            var id=document.getElementsByName("data[id]")[0].value;
            if(id==''){
                $.messager.alert('提示','ID不能为空', 'info');
                return false ;
            }
        },
        /**
         * getElementById是element单数，
            getElementsByName是elements复数。
         *
         */
        success:function(data){
            var data = eval('(' + data + ')');  // change the JSON string to javascript object
            $.messager.alert('提示', data.message, 'info');
        }
    });
    if($(ff).form('validate'))
    $(ff).submit();

}
/**
 * 清空
 */
function clearForm() {
    $(ff).form('clear');
}
/**
 * 删除
 */
/*function delForm() {
 $(ff).form({
 url:'http://10.127.98.242/index.php/cyh/Tea/abandon',
 onSubmit: function(){
 var teacherid=$("#tech_id").val();
 if(teacherid==''){
 return false ;
 }
 },
 success:function(data){
 var data = eval('(' + data + ')');  // change the JSON string to javascript object
 $.messager.alert('Info', data.message, 'info');
 }
 });
 $(ff).submit();
 }
 function clearForm() {
 $(ff).form('clear');
 }*/
/*
自定义验证规则
 */
$.extend($.fn.textbox.defaults.rules, {
    range:{//数字1到7
        validator: function (value, param) {
            if (/^[1-7]\d*$/.test(value)) {
                return value >= param[0] && value <= param[7]
            } else {
                return false;
            }
        },
        message: '输入的数字在0到7之间'
    },
    classroom : {// 验证教室填写格式[1-6][A-D][a-d][0-9][1-9]
        validator : function(value) {
            return /^[1-6][A-Da-d][1-9][0-9][1-9](?!\d)/i.test(value);
        },
        message : '请填写数据格式如同5c404'
    }
});