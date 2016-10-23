/**
 * 定义闭包函数
 */
$(function () {
  'use strict';

  //picker
  $(document).on("pageInit", "#page-picker", function(e, id, page) {
    

  });
 // 选择系部
  $("#dept_name").picker({
      toolbarTemplate: '<header class="bar bar-nav">\
       <button class="button button-link pull-right close-picker">\
      确定\
      </button>\
      <h1 class="title">标题</h1>\
      </header>',
      cols: [
        {
          textAlign: 'center',
          values: ['优良', '一般', '差', '很差'],
          cssClass: 'picker-items-col-normal'
        }
      ]
    });
    // 退出按钮
    $(document).on('click', '#btn_logout', function () {
        $.confirm('确定退出系统?',
            function () {
                var t=setTimeout("document.location = '/index.php/teacher/index/logout';",1000);
//                    $.ajax({
//                        type: "POST",
//                        url: "/index.php/teacher/index/logout",
//                        success: function (data) {
////                                var t=setTimeout("document.location = '/index.php/teacher/index/index';",4000)
//                        }
//                    });
            },
            function () {

            }
        );
    })
    // 保存结果
    $(document).on('click', '#btn_save', function () {
        var _id = $(this).attr("id");
        //判断输入是否完成
        var _time=$.trim($("#time").val());
        var _picker=$.trim($("#picker").val());
        var _comments=$.trim($("#comments").val());
        if(_time=="" || _picker=="" || _comments==""){
            $.toast("你没有完成，请完成填写后再提交！");
            return ;
        }
        var jsonData = $("#frm_record").serializeArray();

        $.confirm('确定已完成听课任务?',
            function () {
                $.ajax({
                    type: "POST",
                    url: "/index.php/teacher/index/saverecord",
                    data: jsonData,   //序列化
                    success: function (data) {
                        if (data.success == true) {
                            $.toast('操作成功，4秒后跳转...', 3000, 'success top');
                            var t=setTimeout("document.location = '/index.php/teacher/index/index';",4000)

                        }
                    }
                });
            },
            function () {
                $.alert('取消');
            }
        );
    });
    // 显示操作表
    $(document).on('click','.create-actions', function () {
        var _tid=$(this).attr("id");
        var buttons1 = [
            {
                text: '请选择',
                label: true
            },
            {
                text: '查看课表',
                bold: true,
                color: 'danger',
                onClick: function() {
//                   $.router.load("#lessontable");  //加载ajax页面
                    document.location = "/index.php/teacher/index/lessontable?id="+_tid;
                }
            },
            {
                text: '重新选择听课时间',
                onClick: function() {
                    $.alert("你选择了“买入“");
                    $.ajax({
                        type:"POST",
                        url:"/index.php/teacher/index/changeschedule",
                        data:{id:_id},   //序列化
                        success:function(data) {
                            console.log(data);
                            if(data.success=="true"){
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
  //  初始化页面
  $.init();
});
