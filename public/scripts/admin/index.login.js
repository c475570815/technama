/**
 * Created by guowushi on 2016/7/27.
 */
var ff = "#ff";

function clearForm() {
    $(ff).form('clear');
}
/**
 * 登录
 */
function dologin() {
    // $('#ff').form({
    //     url: "/index.php/admin/index/authentication",
    //     onSubmit: function () {
    //         // do some check
    //         // return false to prevent submit;
    //     },
    //     success: function (data) {
    //         var data = eval('(' + data + ')');
    //         if (data.success){
    //             alert(data.message)
    //         }
    //     }
    // });
    var post_param =$("#ff").serialize();
    console.log(post_param);
    $.ajax({
        type:"POST",   //post提交方式默认是get
        url:"/index.php/admin/index/authentication",
        data:$("#ff").serialize(),   //序列化
        error:function(request) {      // 设置表单提交出错
            $("#showMsg").html(request);  //登录错误提示信息
        },
        success:function(data) {
            console.log(data);
            if(data.success=="true"){
                $("#msg").html(data.message) ;
                document.location = "/index.php/admin/index/";
            }else{
               $("#msg").html(data.message) ;
            }

        }
    });
    // submit the form
  //  $('#ff').submit();
}

