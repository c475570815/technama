/**
 * Created by wangzhen on 2016/8/16.
 */
var nodemailer=require("nodemailer");
//设置属性
    var smtpTransport = nodemailer.createTransport({
        service: "QQ",
        auth: {
            user: "1058759007@qq.com", // 账号
            pass: "kfaddnfondiwbbdb" // 密码
        }
    });
// 设置邮件内容
    var mailOptions = {
        from: "Fred Foo <1058759007@qq.com>", // 发件地址
        to: "2448971468@qq.com", // 收件列表，多个联系人逗号分开
        subject: "Hello world", // 标题
        html: "<b>thanks a for visiting!</b> 世界，你好！" // html 内容
    }
// 发送邮件
    smtpTransport.sendMail(mailOptions, function(error, response){

            console.log(response);


    });

