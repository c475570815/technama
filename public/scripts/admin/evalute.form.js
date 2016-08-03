/**
 * Created by guowushi on 2016/7/27.
 */
/**
 * Ajax方式保存
 */
var url_save = 'http://10.127.98.246/index.php/admin/Evaluate/save';
//当整个页面全部载入后才执行
function saveForm() {
    $('#ff').form({
        url: url_save,
        onSubmit: function () {
            var id = document.getElementsByName("data[id]")[0].value;
            if (id == '') {
                $.messager.alert('提示', 'ID不能为空', 'info');
                return false;
            }
        },
        /**
         * getElementById是element单数，
         getElementsByName是elements复数。
         *
         */
        success: function (data) {
            var data = eval('(' + data + ')');  // change the JSON string to javascript object
            $.messager.alert('提示', data.message, 'info');
        }
    });
    if ($('#ff').form('validate'))
        $('#ff').submit();

}
/**
 * 清空
 */
function clearForm() {
    $('#ff').form('clear');
}
/*
 自定义验证规则
 range
 classroom
 */
$.extend($.fn.textbox.defaults.rules, {
    range: {//数字1到7
        validator: function (value, param) {
            if (/^[1-7]\d*$/.test(value)) {
                return value >= param[0] && value <= param[7]
            } else {
                return false;
            }
        },
        message: '输入的数字在0到7之间'
    },
    classroom: {// 验证教室填写格式[1-6][A-D][a-d][0-9][1-9]
        validator: function (value) {
            return /^[1-6][A-Da-d][1-9][0-9][1-9](?!\d)/i.test(value);
        },
        message: '请填写数据格式如同5c404'
    }
});

$(document).ready(function () {
    // 教室
    $("input[name='data[class_room]']").validatebox({
        buttonText: 'Search',
        iconCls: 'icon-man',
        iconAlign: 'left',
        validType: classroom
    });
    // 应到学生
    $("#stu_due_num").validatebox({
        required:true,
        validType:'email',
        missingMessage:"必须输入",
        invalidMessage:"格式不对",
        validateOnBlur:true
    });
    // 应到学生
    $("select[name='data[week]']").attr('width','200');
    // 周
    $("select[name='data[week]']").combobox({

        });



        $('#vv').validatebox({
            required: true,
            validType: 'email',
            missingMessage:"必须输入",
            invalidMessage:"格式不对",
            validateOnBlur:true
        });

});
