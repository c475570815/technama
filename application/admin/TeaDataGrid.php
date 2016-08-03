<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/20
 * Time: 17:08
 */
namespace app\admin;
use app\common\model\TeaModel;
use think\Db;
use app\common\model\DictModel;
use app\common\model\DeptModel;
use think\Request;
class TeaDataGrid  extends  \app\common\DataGrid
{

    /**
     * 返回Where条件数组,
     * @return mixed
     */
    public function getWhere()
    {
        $arr_where=array();
        //$request = request();
        if(isset($_POST['dict'])){
            $dict=$_POST['dict'];
            if(isset($dict['dept_name'])) {
               $dict["dept_name"] = $this->convert($dict["dept_name"]);//['','a']
          }
            $model=new TeaModel();
            $arr_where= $model->filer($dict);
            //print_r($arr_where);
        }
        return $arr_where;
    }

    /**
     * 返回当前的表对象
     * @return mixed
     */
    public function getCurrentTable()
    {
        return new TeaModel();
    }

    /**
     * [1，2，5]  转换成  [电气，艺术，管理]
     * @param $id
     * @return mixed
     */
    public function  convert($id){
        //print_r($id);
        $dept=new DeptModel();
        //$deptList=$dept->where("dept_id",'in',$id)->select();
        $deptList=$dept->where("dept_id",'in',$id)->column('dept_name');
       // print_r($deptList[0]["dept_name"]);
        //print_r($deptList);die();

        return implode(',',$deptList);
    }
}