<?php
/**
 * Created by PhpStorm.
 * User: guowushi
 * Date: 2016/7/20
 * Time: 12:00
 */

namespace app\common\model;

use think\Model;

/**
 * 对logs表的基本操作
 * @package app\common\model
 */
class LogsModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'tbl_logs';

}