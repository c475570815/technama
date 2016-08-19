/**
 * Created by wangzhen on 2016/8/19.
 */
var schedule = require('node-schedule');
/* 引用需要的模块 */
var redis = require("redis");
var http = require("http");
var io = require("socket.io");
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
socket.on('connection', function (ws_client) {
    console.log("111111");
    var redis_client = redis.createClient(RDS_PORT, RDS_HOST, RDS_OPTS);
    //订阅email消息通道
    redis_client.subscribe('email');
    redis_client.on('message', function (channel, message) {
        var response = JSON.parse(message);
        var date = new Date(response.year, response.month, response.day, response.hour, response.min, response.sec);//月份以0开始  0-11 为1-12月
        var j=schedule.scheduleJob(date, function(){
            console.log('The world is going to end today.')
        });
    ws_client.on('disconnect', function () {


        });
    });

});
//定时进行
//var date = new Date(2016, 7, 19, 10, 56, 23);//月份以0开始  0-11 为1-12月
//var date2 = new Date(2016, 7, 19, 10, 57, 23);
//var j=schedule.scheduleJob(date, function(){
//    console.log('The world is going to end today.');
//});

//每秒进行　
//var rule = new schedule.RecurrenceRule();
//　
//var times = [];
//for (var i = 1; i < 60; i++) {
//
//    times.push(i);
//}
//rule.second = times;
//var c = 0;
//var j = schedule.scheduleJob(rule, function () {
//c++;console.log(c);
//});
