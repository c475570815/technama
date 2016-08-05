/**
 * Created by guowushi on 2016/7/21.
 */
function addTab(title, url){
    if ($('#sy').tabs('exists', title)){
        $('#sy').tabs('select', title);
    } else {
        var content = '<iframe scrolling="auto" frameborder="0"  src="'+url+'" style="width:100%;height:100%;"></iframe>';
        $('#sy').tabs('add',{
            title:title,
            content:content,
            closable:true
        });
    }
}
function loadConent(){
    alert('loading');
    $("#main").attr('href','/index.php/admin/classes/index');
}

function logout() {

}
function menuHandler(item){
    $('#log').prepend('<p>Click Item: '+item.name+'</p>');
}


$(document).ready(function () {
    //当整个页面全部载入后才执行
    $(".menus li a").bind("click", function(){
        //alert( $(this).attr("href") );
        addTab($(this).text(), $(this).attr("href"));
        return false;
    });

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
    // 绑定上下文菜单
    $(document).bind('contextmenu',function(e){
        e.preventDefault();
        e.stopPropagation();
        $('#bwl_m').menu('show', {
            left: e.pageX,
            top: e.pageY
        });
    });
});
