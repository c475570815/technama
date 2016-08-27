/**
 * Created by guowushi on 2016/7/27.
 */
/**
 * Ajax方式保存
 */
var url_save = '/index.php/admin/evaluate/save';

var ff="#ff";

/**
 * 提交表单
 */
function saveForm() {
    var jsonData = $(ff).serializeArray();
    $(ff).form({
        url: url_save,
        onSubmit: function () {
            console.log($(this).serializeArray());
        },
        success: function (data) {
           var data = eval('(' + data + ')');  // change the JSON string to javascript object
           $.messager.alert('提示', data.message, 'info');
        }
    });
    // 验证后提交
    if ($(ff).form('validate')){
        $(ff).submit();
    }

}
function saveform1(){

}
/**
 * 清空表单内容
 */
function clearForm() {
    $(ff).form('clear');
}

/*
 自定义验证规则
 */
$.extend($.fn.validatebox.defaults.rules, {
    // 数值范围，如range[0-7]
    range: {
        //数字1到7
        validator: function (value, param) {
            if (/^[1-7]\d*$/.test(value)) {
                return value >= param[0] && value <= param[7]
            } else {
                return false;
            }
        },
        message: '输入的数字在0到7之间'
    },
    // 教室
    classroom: {
        // 验证教室填写格式[1-6][A-D][a-d][0-9][1-9]
        validator: function (value) {
            return /^[1-6][A-Da-d][1-9][0-9][1-9](?!\d)/i.test(value);
        },
        message: '请填写数据格式如同5c404'
    },
    //验证汉子
    CHS: {
        validator: function (value) {
            return /^[\u0391-\uFFE5]+$/.test(value);
        },
        message: '只能输入汉字'
    },
    //移动手机号码验证
    mobile: {//value值为文本框中的值
        validator: function (value) {
            var reg = /^1[3|4|5|8|9]\d{9}$/;
            return reg.test(value);
        },
        message: '输入手机号码格式不准确.'
    },
    //国内邮编验证
    zipcode: {
        validator: function (value) {
            var reg = /^[1-9]\d{5}$/;
            return reg.test(value);
        },
        message: '邮编必须是非0开始的6位数字.'
    }
});

$(document).ready(function () {

    // 教室
    $("input[name='data\\[class_room\\]']").textbox({

    });
    // 系部
    $("input[name=data\\[dept_name\\]]").combobox({
        url: '/index.php/admin/dept/rootdept',
        method:'POST',
        valueField: 'dept_name',
        textField: 'dept_name',
        limitToList:false
    });
    // 教师名称
    $('input[name=data\\[teacher\\]]').textbox({
        // buttonText:'Search',
        // iconCls:'icon-man',
        // iconAlign:'left'
    });
    // 听课日期
    $('input[name=data\\[time\\]]').datebox({
        required:true
        // buttonText:'Search',
        // iconCls:'icon-man',
        // iconAlign:'left'
    });
    //周
    $('input[name=data\\[week\\]]').combobox({
        url:'/index.php/admin/term/weeks',
        valueField:'id',
        textField:'text',
    });
    //星期
    $('input[name=data\\[xing_qi_ji\\]]').combobox({
        valueField:'id',
        textField:'text',
        data:[{"id":1,"text":"星期一"},{"id":2,"text":"星期二"},{"id":3,"text":"星期三"},{"id":4,"text":"星期四"},{"id":5,"text":"星期五"},{"id":6,"text":"星期六"},{"id":7,"text":"星期日"}]
    });
    //节次
    $('input[name=data\\[section\\]]').combobox({
        valueField:'id',
        textField:'text',
        data:[{"id":1,"text":"一大节"},{"id":2,"text":"二大节"},{"id":3,"text":"三大节"},{"id":4,"text":"四大节"},{"id":5,"text":"晚上"}]
    });
    // 应到学生
    // $("#stu_due_num").validatebox({
    //     required: true,
    //     validType: 'email',
    //     missingMessage: "必须输入",
    //     invalidMessage: "必须是数字",
    //     validateOnBlur: true
    // });
     // 周
  /*  $("select[name='data[week]']").combobox({

    });*/

});
