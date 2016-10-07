/*
 引用需要的模块：
 （1）node-schedule定时任务模块
 （2）nodemailer 邮件发送模块
 （3）yuntongxun-node 短信通知模块
 （4）socket.io 实现websocket
 （5）redis  访问redis服务器
 */
var redis = require("redis");
var http = require("http");
var io = require("socket.io");
var schedule = require('node-schedule');
var nodemailer = require("nodemailer");
var yuntongxun = require("yuntongxun-node");
// 应用配置项目
var cfg = require("./config.json");
var WEBSOCKET_PORT = 8080;

/*
    在8080端口启动HTTP服务器
*/
var server = http.createServer(function (req, res) {
    //报头添加Access-Control-Allow-Origin标签，值为特定的URL或“*”
    res.setHeader("Access-Control-Allow-Origin", "*");
    res.writeHead(200, {
        'Content-Type': 'text/html'
    });
    res.end('<h1>Hello DreamStudio!</h1>');
});
server.listen(WEBSOCKET_PORT);
/*
 创建Socket.IO 实例，绑定到HTTP服务器
 参数{origins: "*:*"} 允许跨域访问
 */
var socket = io.listen(server, {origins: "*:*"});

/* 定义客户端 */
var sockets = [];
var user_info_hash = {};
var socket_user_hash = {};
var RDS_PORT = 6379,		//REDIS 端口号
    RDS_HOST = '127.0.0.1',	//REDIS服务器IP
    RDS_OPTS = {};			// REDIS设置项

/*设置QQ邮件服务器属性*/
var smtpTransport = nodemailer.createTransport(cfg.qqmail);
/* 云通讯参数 */
var yuntongxun = new yuntongxun(cfg.yuntongxun.ip,cfg.yuntongxun.port,cfg.yuntongxun.version);
yuntongxun.setAccount(cfg.yuntongxun.sid, cfg.yuntongxun.token);
yuntongxun.setAppId("8a48b5514f49079e014f4ae30b730723");
/* -------------------------------------------------------------------------------
  创建redis客户端，并订阅三个消息通道
 （1）SMS_CHANNEL 用于发送短信
 （2）EMAIL_CHANNEL 用于发送邮件
 （3）MESSAGE_CHANNEL 用于发送短信
* */
var redis_client = redis.createClient(RDS_PORT, RDS_HOST);
redis_client.subscribe('SMS_CHANNEL');
redis_client.subscribe('EMAIL_CHANNEL');
redis_client.subscribe('MESSAGE_CHANNEL');
// redis客户端监听消息订阅的事件
redis_client.on("message", function (channel, message) {
    console.log("C:"+channel+" M:"+message);
    // 如果redis收到的是MESSAGE_CHANNEL的消息，则通过websocket广播消息
    if (channel == "MESSAGE_CHANNEL") {
        ws_client.emit('public', message);
    }
    /**
     * 如果redis收到的消息来自于SMS_CHANNEL通道，则发送短信
     * 收到的消息格式如下：
     *     {
         *     "to":"18942891954",
         *     "templateId":1,
         *     "datas":['param a','param b'],
         *     "schedule":"2015-2-2 13:40"
         *     }
     */
    if (channel == "SMS_CHANNEL") {
        var response = JSON.parse(message);
        // 发送短信的回调函数
        var callback_sms = function (error, res) {
            if (typeof(res) == "undefined") {
                ws_client.send("发送给：" + response.to + "的短信发送失败");
            } else {
                ws_client.send(response.to + "短信发送成功");
            }
        };
        // 实际要发送的短信内容
        var sms_cfg={
            "to" : response.to,
            "templateId":response.templateId,
            "datas":response.datas
        };
        if (response.schedule) {
            // 定时发送短信
            var scheduleTime = response.schedule;
            /* 定时任务 月份以0开始  0-11 为1-12月 */
            var execute_date = new Date(scheduleTime.year, scheduleTime.month - 1, scheduleTime.day, scheduleTime.hour, scheduleTime.min, 0);
            var j = schedule.scheduleJob(execute_date, function () {
                // yuntongxun.sendSMS(sms_cfg,callback_sms);
                // yuntongxun.sendTemplateSMS(response.to,response.datas,response.templateId);
            });
        } else {
            // 异步调用立即发送短信
            console.log(response);
            // yuntongxun.sendTemplateSMS(response.to,response.datas,response.templateId);

        }
    }
    /**
     * redis通道：EMAIL_CHANNEL
     * 消息格式：
     * {
                from: "guowushi@qq.com",   // 发件地址
                to: "zhangsan@qq.com,89898@qq.com", // 收件列表，多个联系人逗号分开
                subject: "", // 标题
                html: "" // html 内容
                schedule:"2015-2-2 13:40"
            }
     */
    if (channel == "EMAIL_CHANNEL") {
        var response = JSON.parse(message);
        // 邮件内容
        var mailOptions = {
            from: cfg.from, // 发件地址
            to: response.to, // 收件列表，多个联系人逗号分开
            subject: response.subject, // 标题
            html: response.body // html 内容

        };
        // 发送邮件的回调函数
        var callback_email = function (error, res) {
            if (typeof(res) == "undefined") {
                ws_client.send("发送给：" + response.to + "的邮件发送失败");
            } else {
                ws_client.send(response.to + "邮件发送成功");
            }
        };
        if (response.schedule) {
            var scheduleTime = response.schedule;  // 时间
            /* 定时任务 月份以0开始  0-11 为1-12月 */
            var execute_date = new Date(scheduleTime.year, scheduleTime.month - 1, scheduleTime.day, scheduleTime.hour, scheduleTime.min, 0);
            var j = schedule.scheduleJob(execute_date, function () {
                smtpTransport.sendMail(mailOptions, callback_email);
            });
        } else {
            // 立即发送邮件
            smtpTransport.sendMail(mailOptions, callback_email);
        }
    }

});
/* ----------------------------------------------------------------------------
 设置WebSocket的事件监听器
 (1)连接成功后，创建redis客户端，订阅redis消息
 （2）监听redis的message事件，当收到redis消息后，将消息发送给websocket客户端
 */
var count=0;
socket.on('connection', function (ws_client) {
    count++;
   // sockets.push(ws_client);
   // console.log("conecting! user:"+sockets.length);
    //（1）发送this事件给客户端
    //socket.sockets.emit('public', "hello i conneted");
    ws_client.emit('public', "第"+count+"位新用户连接上来 ");
   // ws_client.broadcast.emit('user connected');

    //（2）监听断开连接事件
    ws_client.on('disconnect', function () {
        socket.sockets.emit('public','user disconnected');//发送消息给所有用户
    })
    // (3) 接收到system事件
    ws_client.on('system', function (from, msg) {
        console.log('I received a private message by ', from, ' saying ', msg);
    });
});


