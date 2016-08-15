/* 引用需要的模块 */
var redis = require("redis");
var http = require("http");
var io = require("socket.io");
/* 在8080端口启动HTTP服务器 */
var server = http.createServer(function (req, res) {
    //报头添加Access-Control-Allow-Origin标签，值为特定的URL或“*”
    res.setHeader("Access-Control-Allow-Origin","*");
    res.writeHead(200, {
        'Content-Type': 'text/html'
    });
    res.end('<h1>Hello DreamStudio!</h1>');
});
server.listen(8080);
/* 创建Socket.IO实例，绑定到HTTP服务器 */
var socket = io.listen(server,{origins: "*:*"});
/*
 socket.set(“origins”,"");*/
 /*socket.set('transports', [
 'websocket'
, 'flashsocket'
 , 'htmlfile'
 , 'xhr-polling'
 , 'jsonp-polling'
 ]);*/
/* 定义客户端 */
var sockets = [];
var user_info_hash = {};
var socket_user_hash = {};
var RDS_PORT = 6379,		//端口号
    RDS_HOST = '127.0.1.1',	//服务器IP
    RDS_OPTS = {};			//设置项

/*
 设置WebSocket的事件监听器
   (1)连接成功后，创建redis客户端，订阅redis消息
   （2）监听redis的message事件，当收到redis消息后，将消息发送给websocket客户端
*/
socket.on('connection', function (ws_client) {
    var redis_client = redis.createClient(RDS_PORT,RDS_HOST,RDS_OPTS);
    redis_client.subscribe('sms');
    redis_client.on('message', function (channel, message) {
        // console.log("channel"+channel,message)
        //发短信，并监听发短信的操作的结果，将结果发送给websocket客户端浏览器
        ws_client.send('message','everyone message');
    });
    console.log("connected");
   /* client.on('message',function(data){
        console.log("socket:"+data);
    })*/
    // //现在开始监听接收到的消息
    // client.on('message', function (channel, message) {
    //     //发短信，并监听发短信的操作的结果，将结果发送给websocket客户端浏览器
    //     io.sockets.emit('message', message.toString());
    // })
    ws_client.on('disconnect', function () {


    })
});


