/**
 * Created by guowushi on 2016/8/14.
 */
// var App = require('alidayu-node');
// var app = new App('App Key', 'App Secret');
var yuntongxun = require('yuntongxun-node');
var app = new yuntongxun('1', '2');
app.setAppId("8a48b5514f49079e014f4ae30b730723");
console.log('sms start');
// for(i=1;i<=5;i++){
//     ret=  app.sendTemplateSMS('18942891954',[i,'bbbbb'],1);
//     console.log(ret);
//     // if(ret.statusCode!='000000'){
//     //     console.log(ret.body);
//     // }
// }
for(i=1;i<=5;i++){
    cfg={
        "to" : "18942891954",
        "templateId":"1",
        "datas":[i,'bbbbb']
    };
    app.sendSMS(cfg,function(data){
        console.log(data);
    });

}
console.log('sms end');
