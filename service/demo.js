/**
 * Created by guowushi on 2016/8/14.
 */
// var App = require('alidayu-node');
// var app = new App('App Key', 'App Secret');
var yuntongxun = require('yuntongxun-node');
var app = new yuntongxun('1', '2');

app.setAppId("8a48b5514f49079e014f4ae30b730723");
function sms(){
    alert(app.sendTemplateSMS('18161343161',['S','bbbbb'],1));
}
