/**
 * Created by guowushi on 2016/8/14.
 */
var App = require('alidayu-node');
var app = new App('23431854', '1d8886b871e854deae2d52cb2c5b3965');

// app.smsSend({
//     sms_free_sign_name: '注册验证',
//     sms_param: {"name": "123456", "info": "测试网站"},
//     rec_num: '18942891954',
//     sms_template_code: 'SMS_13032650'
// });

app.ttsSinglecall({
    tts_param: {"name": "易欣老师", "info": "在干什么呢，郭嘉遗玩的开心不"},
    called_num: "13678380878",
    called_show_num: '051482043270',
    tts_code: 'TTS_13037826'
});

