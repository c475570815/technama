
/**
 * 定义一个类
 */
var IndexLayout = function () {
    /* 属性 */
    this.socket = null;
    // this.element_tabs = null;
    // this.element_calander = $('#calendar');
    // this.btn_logout = $('#loginOut');
    /* 方法 */
    this.init = function (option){
        this.element_tabs =  option.tabs;
        this.element_calander = option.calendar;
        this.btn_logout = option.logout;
    }
    this.initTabs= function (element_tabs) {
        this.element_tabs = element_tabs;
        // 初始化Tabs组件
        element_tabs.tabs({
            border: false,
            fit: true,
            plain: true,
            pill: false,
            onBeforeClose: function (title, index) {
                var target = this;
                $.messager.confirm('请确认', '是否要关闭- ' + title, function (r) {
                    if (r) {
                        var opts = $(target).tabs('options');
                        var bc = opts.onBeforeClose;
                        opts.onBeforeClose = function () {
                        };  // allowed to close now
                        $(target).tabs('close', index);
                        opts.onBeforeClose = bc;  // restore the event function
                    }
                });
                return false;	// prevent from closing
            },
            onContextMenu: function (e, title, index) {
                e.preventDefault();
                e.stopPropagation();
                /*$('#bwl_m').menu('show', {
                 left: e.pageX,
                 top: e.pageY
                 });*/
            }
        });
    };

    this.addTab = function (title, url) {
        var self = this;
        var _easyTabs = this.element_tabs;
        if (_easyTabs.tabs('exists', title)) {
            //如果存在，则选中刷新
            _easyTabs.tabs('select', title);
            this.refreshTab();
        } else {
            _easyTabs.tabs('add', {
                title: title,
                content: self.createFrame(url),
                closable: true,
                fit: true,
                cache: true,
                loadingMessage: '正在为你加载...',
                tools: [{
                    iconCls: 'icon-mini-refresh',
                    handler: function () {
                        refreshTab();
                    }
                }],
                border: false
            });
        }
    }
    this.createFrame = function (url) {
        var iframe = '<iframe scrolling="auto" frameborder="0"  src="' + url + '" style="width:100%;height:100%;"></iframe>';
        return iframe;
    }
    this.refreshTab = function () {
        var selected_tab = this.element_tabs.tabs('getSelected');  // get selected panel
        selected_tab.panel('open').panel('refresh');
    }
    this.calendarInit = function (url_event) {
        this.element_calander.fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultDate: '2016-06-12',
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: {
                url: url_event,
                type: 'POST',
                custom_param1: 'something',
                custom_param2: 'somethingelse',
                color: 'yellow',   // a non-ajax option
                textColor: 'black', // a non-ajax option
                error: function () {
                    //  $('#script-warning').show();
                    console.log("error");
                }
            },
            loading: function (bool) {
                // $('#loading').toggle(bool);
                console.log(" loading" + "");
            }
        });
    }

    this.bindEvent = function () {
        var self = this;
        //绑定菜单的点击事件
        $(".menus li a").bind("click", function () {
            self.addTab($(this).text(), $(this).attr("href"));
            return false;
        });
        // 退出按钮事件绑定
        this.btn_logout.bind("click", function () {
            $.messager.confirm('提示', '是否要退出系统?', function (answer) {
                if (answer) {
                    location.href = "/index.php/admin/index/logout";
                } else {
                    return false;
                }
            });
            return false;
        });
    }
    this.socketInit = function (url) {
        socket = io.connect(url, {origin: '*'});
        // socket.set('transports', ['websocket', 'xhr-polling', 'jsonp-polling', 'htmlfile', 'flashsocket']);
        //  io.set('origins', '*:*');
        socket.on('message', function (data) {
            var div = $("<div></div>");
            div.text(data);
            $("#message").prepend(div);
        });
        socket.on('connect', function (data) {
            console.log("Connected to Server");
            global.reconnectFailCount = 0;
        });
        socket.on('connect_failed', function (data) {
            console.log("connect_failed to Server");
        });
        socket.on('error', function (data) {
            console.log("error");
            //alert("连接服务器失败！！！");
        });
        socket.on('reconnecting', function (data) {
            console.log("reconnecting");
            global.reconnectFailCount++;
            if (global.reconnectFailCount >= 6) {
                alert("连接服务器失败，请检查您当前的网络");
            }
        });
        socket.on('reconnect', function (data) {
            console.log("reconnect");
            global.reconnectFailCount--;
        });
        socket.on('disconnect', function (data) {
            console.log("disconnect");
        });
    }

};

/**
 *  当整个页面全部载入后才执行
 */
$(document).ready(function () {
    console.log("document ready!")
    var indexlayout = new IndexLayout();
    indexlayout.init({
        "tabs":$('#sy'),
        "calendar":$('#calendar'),
        "logout":$('#loginOut')
    })
    indexlayout.initTabs($('#sy'));
    indexlayout.bindEvent();
    indexlayout.calendarInit("/index.php/admin/event/getevents");
    //websocketinit("http://192.168.19.137:8080/");
});