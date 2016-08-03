<?php
namespace app\cyh\model;
use think\Model;
use think\Db;//类库方法
use app\cyh\model\Tbl_teacher;
class  Pass extends Model{
      public static function Judge($id,$password){
          if(Db::table('tbl_teacher')->where('tech_id',$id)->find()&&Db::table('tbl_teacher')->where('teach_pass',$password)->find())
              return true;
          else
              return false;
      }
      public static  function error()
      {
          window.close();
      }
}
?>