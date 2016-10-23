/**
 * 定义闭包函数
 */
$(function () {
  'use strict';

    /**
     * 微信Auth2.0认证
     */
    function wxlogin(){
        var appid="wx06693892f83ef14c";  //企业的CorpID
        var redirect_url=encodeURI("http://dd.scetc.edu.cn/teacher/index/auth2"); //授权后重定向的回调链接地址，请使用urlencode对链接进行处理
        var  response_type="code"; //返回类型，此时固定为：code
        var scope="snsapi_base"; //应用授权作用域，此时固定为：snsapi_base
        var state="123456"; //重定向后会带上state参数，企业可以填写a-zA-Z0-9的参数值，长度不可超过128个字节
        var param ="#wechat_redirect";  //微信终端使用此参数判断是否需要带上身份信息
        var url="https://open.weixin.qq.com/connect/oauth2/authorize?";
        url=url+"appid=wx06693892f83ef14c";
        url=url+"&redirect_uri="+redirect_url;
        url=url+"&response_type=code&scope=snsapi_base&state=1234#wechat_redirect"
        console.log(url);
        window.location=url;
        /*
         企业或服务商网站引导用户进入登录授权页 企业或服务商可以在自己的网站首页中放置“微信企业号登录”的入口，引导用户（指企业号管理员或成员）进入登录授权页
         网址为: https://open.weixin.qq.com/connect/oauth2/authorize?appid=CORPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
         企业或服务商需要提供corp_id，跳转uri和state参数，其中uri需要经过一次urlencode作为参数，
         state用于企业或服务商自行校验session，        防止跨域攻击。
         */
//        $.toast("还在拼命开发中... :)");
    }

    /**
     * 删除cookies
     * @param name
     */
    function delCookie(name)
    {
        var exp = new Date();
        exp.setTime(exp.getTime() - 1);
        var cval=getCookie(name);
        if(cval!=null)
            document.cookie= name + "="+cval+";expires="+exp.toGMTString();
    }
    /**
     *
     * @param name
     * @param value
     * @param time
     */
    function setCookie(name,value,time)
    {
        var strsec = getsec(time);
        var exp = new Date();
        exp.setTime(exp.getTime() + strsec*1);
        document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
    }
    function getsec(str)
    {

        var str1=str.substring(1,str.length)*1;
        var str2=str.substring(0,1);
        if (str2=="s")
        {
            return str1*1000;
        }
        else if (str2=="h")
        {
            return str1*60*60*1000;
        }
        else if (str2=="d")
        {
            return str1*24*60*60*1000;
        }
    }

    /**
     * 登录
     */
    function login(){
        var id=document.getElementById("id");
        var pass=document.getElementById("password");
        var form=document.getElementById("frm_login");
        if(id.value=='' || pass.value==''){
            if(id.value==''){
                alert("请输入用户名")
            }
            if(pass.value==''){
                alert("请输入密码")
            }
        }else{
            setCookie("name","hayden","s20");
            form.submit();
        }
    }
  // 
  $.init();
});
