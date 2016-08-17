'use strict';
/**
 * 引入第三方的模块
 * request：http请求模块
 * crypto:加密模块
 * lodash：工具模块
 * moment：日期处理模块
 */
var request = require('request');
var crypto = require('crypto');
var _ = require('lodash');
var moment = require('moment');
/**
 * yuntongxun的APP核心类
 * @param key
 * @param secret
 * @constructor
 */
function yuntongxun(key, secret){
    // this.url = 'https://app.cloopen.com:8883';
    this.server_ip = 'sandboxapp.cloopen.com';
    this.server_port = "8883";
    this.version="2013-12-26";
    this.ACCOUNT_SID='aaf98f894f402f15014f47296f9305b6';
     this.AUTH_TOKEN="4c3bf9a9f1114bd2a85d1de6552a3143";
};
/**
 * 设置主帐号
 */
yuntongxun.prototype.setAccount = function(accountSid, accountToken){
    this.ACCOUNT_SID = accountSid;
    this.AUTH_TOKEN = accountToken;
}
/**
 * 设置应用ID
 * @param appid
 */
yuntongxun.prototype.setAppId = function(appid){
    this.APPID = appid;
}
/**
 * 签名
 * @returns {string}
 */
yuntongxun.prototype.sign = function(){
    var timestamp = moment().format('YYYYMMDDHHmmss');
    var str = this.ACCOUNT_SID + this.AUTH_TOKEN + timestamp;
    // 大写的sig参数
    var sig = crypto.createHash('md5').update(str, 'utf-8').digest('hex').toUpperCase();
    return sig;
}
/**
 * 发送请求
 * @param params
 * @param callback
 */
yuntongxun.prototype.request = function(params, callback){

    var params = this.sign(params);
    var postData = {
        url: this.url,
        form: params,
        json: true,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8'
        }
    };
    request.post(postData, function (err, response, body) {
        // callback && callback.call(null, body);//执行回调
        if (!err && response.statusCode == 200) {
            return body;
        } else {
            return "{\"statusCode\":\"172001\",\"statusMsg\":\"网络错误\"}";
        }

    })
}

yuntongxun.prototype.sendTemplateSMS = function(to, datas, tempId){

    //主帐号鉴权信息验证，对必选参数进行判空。
    var auth = this.accAuth();
    if (auth == "") {
        return auth;
    }
    var body = {
        "to": to,
        'templateId': tempId,
        'appId': this.APPID,
        'datas': datas
    };
    // 生成请求URL
    var url = "https://" + this.server_ip + ":" + this.server_port + "/" + this.version + "/Accounts/" + this.ACCOUNT_SID + "/SMS/TemplateSMS?sig=" + this.sign();

    // 生成授权：主帐户Id + 英文冒号 + 时间戳,再进行base64编码。
    var timestamp = moment().format('YYYYMMDDHHmmss');

    var authen = this.ACCOUNT_SID + ":" + timestamp;
    var b = new Buffer(authen);
    var authen_base64 = b.toString('base64');
    // var authen_base64 = crypto.createHash('md5').update(authen, 'utf-8').digest('base64');
    // 生成包头
    var headers = {
        'Content-Type': 'application/json;charset=utf-8',
        'Accept': "application/json",
        'Authorization': authen_base64
    };
    // 发送请求
    var postData = {
        url: url,
        json: true,
        headers: headers,
         body:body
    };
    //console.log(url);
    request.post(postData, function (err, response, body) {
        // callback && callback.call(null, body);//执行回调
        console.log(response);
        if (!err && response.statusCode == 200) {
            return body;
        } else {
            return "{\"statusCode\":\"172001\",\"statusMsg\":\"网络错误\"}";
        }
    });

}
/**
 * 检查配置
 * @returns {*}
 */
yuntongxun.prototype.accAuth = function() {
    if (this.url == "") {
        return "{\"statusCode\":\"172001\",\"statusMsg\":\"IP为空\"}";
    }
    if (this.version == "") {
        return "{\"statusCode\":\"172013\",\"statusMsg\":\"版本号为空\"}";
    }
    if (this.ACCOUNT_SID == "") {
        return "{\"statusCode\":\"172006\",\"statusMsg\":\"主帐号为空\"}";
    }
    if (this.AUTH_TOKEN == "") {
        return "{\"statusCode\":\"172007\",\"statusMsg\":\"主帐号令牌为空\"}";
    }
    if (this.APPID == "") {
        return "{\"statusCode\":\"172012\",\"statusMsg\":\"应用ID为空\"}";
    }
}
module.exports =yuntongxun;
