/**
 * Created by FuJinsong on 2016/8/9.
 */

var grid_id='#datagrd';
var url_save='/index.php/admin/term/save';
var form_id='#ff';
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

$.extend($.fn.validatebox.defaults.rules, {
    minLength: {
        validator: function(value, param){
            return value.length >= param[0];
        },
        message: 'Please enter at least {0} characters.'
    }
});
$('#term_name').validatebox({
    required: true,
    validType: 'minLength[5]'
});

/**
 * 学期名的验证
 * @constructor
 */
function TermValidate() {
    $.extend($.fn.validatebox.defaults.rules, {
        term_name_vali: {
            validator: function(value, param){
            //验证电话号码："/^(\(\d{3,4}-)|\d{3.4}-)?\d{7,8}$/"正确格式为："XXX-XXXXXXX"、"XXXX-XXXXXXXX"、"XXX-XXXXXXX"、"XXX-XXXXXXXX"、"XXXXXXX"和"XXXXXXXX"。
               // /^(?:13\d|15\d|18\d)-?\d{5}(\d{3}|\*{3})$/.test(value);

                return /^20\d{2}-20\d{2}-[1-2]$/.term_id(value);
            },
            message: '请输入当前学期，例如：2015-2016-2'
        }
    });
    $('#term_name').validatebox({
        required: true,
        validType: 'term_name_vali'
    });
}

// 初始化表单
function initForm() {
    // 系部
    $("input[name=data\\[default\\]]").combobox({
        valueField: 'label',
        textField: 'value',
        data: [{
            label: '是',
            value: '是'
        }, {
            label: '否',
            value: '否'
        }]
    });
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
