/**
 * Created by guowushi on 2016/7/21.
 */
/**
 * 添加一个TAB
 * @param title  Tab 标题
 * @param url     地址
 */
function addTab(title, url){

    if ($('#sy').tabs('exists', title)){
        //如果存在，则选中刷新
           //   $('#sy').tabs('select', title);
        refreshTab();
    } else {
         $('#sy').tabs('add',{
            title:title,
            content:createFrame(url),
            closable:true,
            fit:true,
            cache:true,
             loadingMessage:'正在为你加载...',
            tools:[{
                iconCls:'icon-mini-refresh',
                handler:function(){
                    refreshTab();
                }
            }],
            border:false

        });
    }
}
function createFrame(url)
{
    var s = '<iframe scrolling="auto" frameborder="0"  src="'+url+'" style="width:100%;height:100%;"></iframe>';
    return s;
}
/**
 * 菜单处理
 * @param item
 */
function menuHandler(item){
    $('#log').prepend('<p>Click Item: '+item.name+'</p>');
}
function refreshTab(){
    var selected_tab = $('#sy').tabs('getSelected');  // get selected panel
    selected_tab.panel('open').panel('refresh');
}

/**
 *  当整个页面全部载入后才执行
 */
$(document).ready(function () {
    //绑定菜单的点击事件
    $(".menus li a").bind("click", function(){
        addTab($(this).text(), $(this).attr("href"));
        return false;
    });
    // 初始化Tabs
    $('#sy').tabs({
        border: false,
        fit:true,
        plain:true,
        pill:false,
        onBeforeClose: function(title,index){
            var target = this;
            $.messager.confirm('请确认','是否要关闭- '+title,function(r){
                if (r){
                    var opts = $(target).tabs('options');
                    var bc = opts.onBeforeClose;
                    opts.onBeforeClose = function(){};  // allowed to close now
                    $(target).tabs('close',index);
                    opts.onBeforeClose = bc;  // restore the event function
                }
            });
            return false;	// prevent from closing
        },
        onContextMenu:function(e, title,index){
            e.preventDefault();
            e.stopPropagation();
            /*$('#bwl_m').menu('show', {
                left: e.pageX,
                top: e.pageY
            });*/
        }
    });
    // 退出
    $("#loginOut").bind("click", function(){
        $.messager.confirm('提示', '是否要退出系统?', function (answer) {
            if (answer) {
                location.href="/index.php/admin/index/logout";
            }else{
                return false;
            }

        });
        return false;
    });
    //日历
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultDate: '2016-06-12',
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        events: {
            url: 'php/get-events.php',
            type: 'POST',
            custom_param1: 'something',
            custom_param2: 'somethingelse',
            color: 'yellow',   // a non-ajax option
            textColor: 'black', // a non-ajax option
            error: function() {
                $('#script-warning').show();
            }
        },

        loading: function(bool) {
            $('#loading').toggle(bool);
        }
    });
});
