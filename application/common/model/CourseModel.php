<?php
/**
 * Created by PhpStorm.
 * User: FuJinsong
 * Date: 2016/7/24
 * Time: 15:46
 */

namespace app\common\model;



class CourseModel extends \app\common\model\CommonModel
{
    protected $table = 'tbl_course';

     protected $defaults = [
    'teach_name' => 'like'

];

}
