/**
 * Created by guowushi on 2016/10/14.
 */
/**
 * 扩展自定的编辑器
 * text:
 * email: 邮件编辑器
 */
$.extend($.fn.datagrid.defaults.editors, {
    text_email: {
        init: function(container, options){
            var input = $('<input type="text" class="datagrid-editable-input">').appendTo(container);
            input.combogrid(options);//根据选项初始化
            return input;
        },
        destroy: function(target){
            $(target).remove();
        },
        getValue: function(target){
            return $(target).val();
        },
        setValue: function(target, value){
            $(target).val(value);
        },
        resize: function(target, width){
            $(target)._outerWidth(width);
        }
    }
});
/**
 *
 */
$.extend($.fn.datagrid.defaults.editors, {
    checkbox: {//调用名称
        init: function (container, options) {
            //container 用于装载编辑器 options,提供编辑器初始参数
            var input = $('<input type="checkbox" class="datagrid-editable-input">').appendTo(container);
            //这里我把一个 checkbox类型的输入控件添加到容器container中
            // 需要渲染成easyu提供的控件，需要时用传入options,我这里如果需要一个combobox，就可以 这样调用 input.combobox(options);
            return input;
        },
        getValue: function (target) {
            //datagrid 结束编辑模式，通过该方法返回编辑最终值
            //这里如果用户勾选中checkbox返回1否则返回0
            return $(target).prop("checked") ? 1 : 0;
        },
        setValue: function (target, value) {
            //datagrid 进入编辑器模式，通过该方法为编辑赋值
            //我传入value 为0或者1，若用户传入1则勾选编辑器
            if (value)
                $(target).prop("checked", "checked")
        },
        resize: function (target, width) {
            //列宽改变后调整编辑器宽度
            var input = $(target);
            if ($.boxModel == true) {
                input.width(width - (input.outerWidth() - input.width()));
            } else {
                input.width(width);
            }
        }
    }
});
