
/*
	定义需要加载模块的路径
	非模块化的JS库需要定义shim
*/
require.config({
	//baseUrl: 'js',
    shim: {
         'easyui': ['jquery']
    },
　　paths: {
　　　　　　"jquery": "/static/jquery-easyui-1.4.5/jquery.min",
　　　　　　"easyui": "/static/jquery-easyui-1.4.5/jquery.easyui.min"
　　　　}
});
/*引用JS库模块*/
require(['jquery', 'easyui','main-logic'], function ($, easyui){
	//模块加载完成后需要执行的代码

});
