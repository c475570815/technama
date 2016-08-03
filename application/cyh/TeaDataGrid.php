<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/20
 * Time: 17:08
 */
namespace app\cyh;
use app\common\model\TeaModel;
use think\Db;
use app\common\model\DictModel;
use think\Request;
class TeaDataGrid  extends  \app\common\DataGrid
{

    /**
     * 返回Where条件数组
     * @return mixed
     */
    public function getWhere()
    {
        $arr_where=array();
        //$request = request();
        if(isset($_POST['dict'])){
            $dict=$_POST['dict'];
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
}