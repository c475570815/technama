/**
 * Created by FuJinsong on 2016/7/25.
 */
var grid_id = '#datagrd';
var url = '/index.php/admin/course/ac1';
var columns_def = [[
   /* {field: 'checkbox', checkbox: true},*/
    {field: 'term', title: '学期', sortable: true},
    {field: 'class_name', title: '班级名称', sortable: true},
    {field: 'course_name', title: '课程名称', sortable: true},
    {field: 'dept_name', title: '开课单位', sortable: true},
    {field: 'teach_name', title: '教师', sortable: true},
    {field: 'teach_id', title: '教师编号', sortable: true},
    {field: 'week', title: '周次', sortable: true},
    {field: 'xing_qi_ji', title: '星期', sortable: true},
    {field: 'section', title: '节次', sortable: true}

    /*    {field:'class_room',title:'教室',sortable:true},
     {field:'state',title:'已听课',sortable:true},//数据读取听课表
     {field:'passed',title:'免听课',sortable:true},//数据读取听课表
     {field:'adj_exchange',title:'调停课',sortable:true},//数据读取调课表
     {field:'opt',title:'听课',width:80,align:'center', formatter:optFormater   },*/
  /*  {field: 'teach_dd', title: '听课教师', sortable: true}//数据录入听课表*/

]];


function initGrid(grid_id, url, columns_def) {
    $(grid_id).datagrid({
            url: url,
            method: 'post',
            title: "详细信息",
            fit: true,
            singleSelect: false,
            checkOnSelect: true,
            selectOnCheck: true,
            collapsible: false,
            pagination: true,
            rownumbers: true,
            height: 345,
            columns: columns_def,
            onSelect: function (rowIndex, rowData) {
                // $(this).datagrid('clearSelections');
                //$(this).datagrid('selectRow',rowIndex);
                // return false;
            },
            onCheck: function (rowIndex, rowData) {
                //alert(rowIndex)
            },
            onLoadSuccess: function (data) {
               // $('.editcls').linkbutton({text: '听课', plain: true, width: '100%', iconCls: 'icon-edit'});
            }
        }
    )
}

$(document).ready(function () {
    //当整个页面全部载入后才执行
    initGrid(grid_id, url, columns_def);
   // listen(grid, url, columns_listen);
});

