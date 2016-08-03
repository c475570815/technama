/**
 * Created by guowushi on 2016/7/18.
 */

/* 对datagrid 的扩展 */
$.extend($.fn.datagrid.methods, {
    /**
     * 更新 非编辑列值
     * @param rowIndex    : 行索引
     * @param cellName    : 列索引或列名
     * @param cellValue    : 列值
     * @author WUYF
     */
    updateRowCell: function (jq, param) {
        var oGrid = $(jq);
        var jqId = $(jq).attr("id");
        var curRow = (oGrid.datagrid('getRows')[param.rowIndex]);
        /*                 curRow[param.cellName] = param.cellValue;
         oGrid.datagrid('endEdit',param.rowIndex);
         oGrid.datagrid('beginEdit',param.rowIndex); */
        oGrid.datagrid('updateRow', {
            index: param.rowIndex,
            row: param.row
        });

    }
});

/* 对datagrid 默认值的扩展 */
$.extend($.fn.datagrid.defaults.editors, {
    datetimebox: {// datetimebox就是你要自定义editor的名称
        init: function (container, options) {
            var input = $('<input class="easyuidatetimebox">').appendTo(container);
            return input.datetimebox({
                formatter: function (date) {
                    return new Date(date).format("yyyy-MM-dd hh:mm:ss");
                }
            });
        },
        getValue: function (target) {
            return $(target).parent().find('input.combo-value').val();
        },
        setValue: function (target, value) {
            $(target).datetimebox("setValue", value);
        },
        resize: function (target, width) {
            var input = $(target);
            if ($.boxModel == true) {
                input.width(width - (input.outerWidth() - input.width()));
            } else {
                input.width(width);
            }
        }
    }
});
// 时间格式化
Date.prototype.format = function (format) {
    /*
     * eg:format="yyyy-MM-dd hh:mm:ss";
     */
    if (!format) {
        format = "yyyy-MM-dd hh:mm:ss";
    }
    var o = {
        "M+": this.getMonth() + 1, // month
        "d+": this.getDate(), // day
        "h+": this.getHours(), // hour
        "m+": this.getMinutes(), // minute
        "s+": this.getSeconds(), // second
        "q+": Math.floor((this.getMonth() + 3) / 3), // quarter
        "S": this.getMilliseconds()
        // millisecond
    };
    if (/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
        }
    }
    return format;
};
//validatebox增加对time、date、datetime的验证
$.extend($.fn.validatebox.defaults.rules, {
    time: {
        validator: function (value) {
            var a = value.match(/^(\d{1,2})(:)?(\d{1,2})\2(\d{1,2})$/);
            if (a == null) {
                return false;
            } else if (a[1] > 24 || a[3] > 60 || a[4] > 60) {
                return false;
            }
        },
        message: '时间格式不正确，请重新输入。'
    },
    /*2014-01-01*/
    date: {
        validator: function (value) {
            var r = value.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/);
            if (r == null) {
                return false;
            }
            var d = new Date(r[1], r[3] - 1, r[4]);
            return (d.getFullYear() == r[1] && (d.getMonth() + 1) == r[3] && d.getDate() == r[4]);
        },
        message: '时间格式不正确，请重新输入。'
    },
    /*2014-01-01 13:04:06*/
    datetime: {
        validator: function (value) {
            var r = value.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/);
            if (r == null) return false;
            var d = new Date(r[1], r[3] - 1, r[4], r[5], r[6], r[7]);
            return (d.getFullYear() == r[1] && (d.getMonth() + 1) == r[3] && d.getDate() == r[4] && d.getHours() == r[5] && d.getMinutes() == r[6] && d.getSeconds() == r[7]);
        },
        message: '时间格式不正确，请重新输入。'
    }
});