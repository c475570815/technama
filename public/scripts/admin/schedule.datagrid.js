/**
 * Created by FuJinsong on 2016/7/21.
 */

var grid='#datagrd';
var url='/index.php/admin/schedule/getlist';
var url_record='/index.php/admin/evaluate/add';
var columns_def=[[
    {field: 'chkbox', checkbox: true},
    {field:'term',title:'学期',sortable:true},
    {field:'time',title:'时间',sortable:true},
    {field:'week',title:'周次',sortable:true},
    {field:'xing_qi_ji',title:'星期',sortable:true},
    {field:'section',title:'节次',sortable:true},
    {field:'class_name',title:'班级名称',sortable:true},
    {field:'class_room',title:'班级教室',sortable:true},
    {field:'teacher',title:'教室',sortable:true},
    {field:'course_name',title:'课程名称',sortable:true},
    {field:'teacher_info',title:'教师信息',sortable:true},
    {field:'dept_name',title:'系部名称',sortable:true},
    {field:'stu_due_number',title:'学生人数',sortable:true},
    {field:'conuncilor',title:'督导',sortable:true},
    {field:'state',title:'状态',sortable:true},
    {field:'operation',title:'操作',formatter:function(val,row,index){
        var updateUrl = url_record + "/pk/" + row.id;
        var opt_formatter="<a class='link-edit' href='"+updateUrl+"' target='_self' title='编辑当前记录'> 编辑 </a>";
        opt_formatter=opt_formatter+"<a class='link-edit' href='"+updateUrl+"' target='_self' title='录入听课结果'> 录入结果 </a>";
        return opt_formatter;
    }}

]];
function initGrid(grid,url,columns_def){
    $(grid).datagrid({
        url:url,
        method:'post',
        title:"详细信息",
        singleSelect:true,
        collapsible:false,
        pagination:true,
        rownumbers:true,
        columns:columns_def
        //onLoadSuccess:loadSuccessHandler
    });
}
function loadSuccessHandler(data){
    $(".note").tooltip({
            content: $('<div></div>'),
            onShow: function(){
                /*$(this).tooltip('arrow').css('left', 20);
                $(this).tooltip('tip').css('left', $(this).offset().left);*/
                $(this).tooltip('tip').css({
                    width:'300',
                    boxShadow: '1px 1px 3px #292929'
                });
            },
            onUpdate: function(cc){
 /*               cc.panel({
                    width: 500,
                    height: 'auto',
                    border: false,
                    cache:false,
                    href: '/index.php/admin/schedule/tip'
                });*/
                cc.html("sasasasas");
            }
        });
}
function listenerFormater(value,row,index){
    var abValue = value;
    if (value.length>=22) {
        abValue = value.substring(0,19) + "...";
    }
    return "<a href='/index.php/admin/schedule/tip/id/"+index+"' class='note'>"+value+"</a>";
}

$(document).ready(function () {
    //当整个页面全部载入后才执行
    initGrid(grid,url,columns_def);
    initCbGrid('#cbogrid_of_teacher','/index.php/admin/schedule/teacher_cg');
    // 学期列表初始化
    var dept=$("input[name=dict\\[term\\]]").val();
    $("input[name=dict\\[term\\]]").combobox({
        url: '/index.php/admin/term/getterm',
        method:'POST',
        valueField: 'term_name',
        textField: 'term_name',
        limitToList:false
    });
});

/**
 * 查询
 */
function query(){
    var search_filter=$('#frm_search').serializeJson();
    $(grid).datagrid(
        'load',
        search_filter
    );
}
function add(){

}
/**
 * 初始化教师Combogrid
 * @param cbgrid
 */
function initCbGrid(cbgrid,url){
    $(cbgrid).combogrid({
        delay: 500,
        mode: 'remote',
        panelWidth:450,
        url: url,
        idField: 'teach_id',
        textField: 'teach_name',
        columns: [[
            {field:'teach_id',title:'教师编号',width:60,sortable:true},
            {field:'teach_name',title:'教师姓名',width:80,sortable:true},
            {field:'dept_name',title:'部门',width:120,sortable:true},
            {field:'sex',title:'性别',width:40,sortable:true}
        ]]
    });
}

