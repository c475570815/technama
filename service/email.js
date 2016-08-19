/* 引用需要的模块 */
var redis = require("redis");
var http = require("http");
var io = require("socket.io");
var cfg=require("./config.json");

/* 在8080端口启动HTTP服务器 */
var server = http.createServer(function (req, res) {
    //报头添加Access-Control-Allow-Origin标签，值为特定的URL或“*”
    res.setHeader("Access-Control-Allow-Origin", "*");
    res.writeHead(200, {
        'Content-Type': 'text/html'
    });
    res.end('<h1>Hello DreamStudio!</h1>');
});
server.listen(8080);
/* 创建Socket.IO实例，绑定到HTTP服务器 */
var socket = io.listen(server, {origins: "*:*"});
/* 定义客户端 */
var sockets = [];
var user_info_hash = {};
var socket_user_hash = {};
var RDS_PORT = 6379,		//端口号
    RDS_HOST = '127.0.0.1',	//服务器IP
    RDS_OPTS = {};			//设置项
var nodemailer = require("nodemailer");
//设置属性
var smtpTransport = nodemailer.createTransport(cfg.qqmail);


/*
 设置WebSocket的事件监听器
 (1)连接成功后，创建redis客户端，订阅redis消息
 （2）监听redis的message事件，当收到redis消息后，将消息发送给websocket客户端
 */
socket.on('connection', function (ws_client) {
    var redis_client = redis.createClient(RDS_PORT, RDS_HOST, RDS_OPTS);
    //订阅email消息通道
    redis_client.subscribe('email');
    redis_client.on('message', function (channel, message) {
        var response = JSON.parse(message);
        // 邮件内容
        var mailOptions = {
            from: cfg.from, // 发件地址
            to: response.to, // 收件列表，多个联系人逗号分开
            subject: response.subject, // 标题
            html: response.html // html 内容
        }

        // 发送邮件
        smtpTransport.sendMail(mailOptions, function (error, res) {
            console.log(mailOptions);
            console.log(error);
            if(typeof(res) == "undefined"){
                //给websocket发送
                ws_client.send("发送给："+response.toname+"的邮件发送失败");
            }else{
                //给websocket发送
                ws_client.send(response.toname+"邮件发送成功");
            }

        });

    });
    ws_client.on('disconnect', function () {


    })
});
