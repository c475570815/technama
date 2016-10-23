/**
 * 定义闭包函数
 */
$(function () {
    'use strict';

    // 提交完成情况
    $(document).on('click', '.confirm-ok-cancel', function () {
        var _current = $(this);
        var _id = $(this).attr("id");
        var href_txt = $.trim($(this).text());
        console.log(href_txt);
        if (href_txt == "已完成听课") {
            $.toast("你已经完成了听课，不用再点了:)");
        } else {
            $.confirm('确定已完成听课任务?',
                function () {
                    $.ajax({
                        type: "POST",   //post提交方式默认是get
                        url: "/index.php/teacher/index/setfinished",
                        data: {id: _id},   //序列化
                        success: function (data) {
                            console.log(data);
                            if (data.success == "true") {
                                document.location = "/index.php/teacher/index/index";
                            }
                        }
                    });
                },
                function () {  //$.alert('取消');
                }
            );
        }
    });
    // 提交评价结果
    $(document).on('click', '.confirm-input', function () {
        var href_txt = $.trim($(this).text());
        console.log(href_txt);
        if (href_txt == "已完成录入") {
            $.toast("结果已经录入，修改则需要管理员解锁 :)");
        } else {
            $.confirm('确定已完成听课任务?',
                function () {
                    $.ajax({
                        type: "POST",   //post提交方式默认是get
                        url: "/index.php/teacher/index/setfinished",
                        data: {id: _id},   //序列化
                        success: function (data) {
                            console.log(data);
                            if (data.success == "true") {
                                document.location = "/index.php/teacher/index/index";
                            }
                        }
                    });
                },
                function () {  //$.alert('取消');
                }
            );
        }
    });
    // 显示操作表
    $(document).on('click', '.create-actions', function () {
        var _tid = $(this).attr("id");
        var buttons1 = [
            {
                text: '请选择',
                label: true
            },
            {
                text: '查看课表',
                bold: true,
                color: 'danger',
                onClick: function () {
//                   $.router.load("#lessontable");  //加载ajax页面
                    document.location = "/index.php/teacher/index/lessontable?id=" + _tid;
                }
            },
            {
                text: '重新选择听课时间',
                onClick: function () {
                    $.alert("你选择了“买入“");
                    $.ajax({
                        type: "POST",
                        url: "/index.php/teacher/index/changeschedule",
                        data: {id: _id},   //序列化
                        success: function (data) {
                            console.log(data);
                            if (data.success == "true") {
                                document.location = "/index.php/teacher/index/index";
                            }
                        }
                    });
                }
            }
        ];
        var buttons2 = [
            {
                text: '取消',
                bg: 'danger'
            }
        ];
        var groups = [buttons1, buttons2];
        $.actions(groups);
    });
    // 页面显示
    $(document).on("pageInit", function (e, pageId, $page) {
        if (pageId == "lessontable") {
            $.alert("你选择了“买入“");
        }
    });

    // 选择新的听课时间
    $("#picker-name").picker({
        toolbarTemplate: '<header class="bar bar-nav">\
  <button class="button button-link pull-right close-picker">确定</button>\
  <h1 class="title">请选择听课的“周-星期-节次”</h1>\
  </header>',
        cols: [
            {
                textAlign: 'center',
                values: ['1', '2', '3', '4', '5', '6', '7', '8']
                //如果你希望显示文案和实际值不同，可以在这里加一个displayValues: [.....]
            },
            {
                textAlign: 'center',
                values: ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期七']
            },
            {
                textAlign: 'center',
                values: ['一节', '二节', '三节', '四节', '五节']
            }
        ]
    });
    //  初始化页面
    $.init();
});
