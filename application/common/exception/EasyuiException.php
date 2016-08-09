<?php
namespace app\common\exception;
use think\exception\Handle;
use think\exception\HttpException;

/**
 * 自定义错误处理
 * Class EasyuiException
 * @package app\common\exception
 */
class EasyuiException extends Handle
{

    public function render(\Exception $e)
    {
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
        }
        //TODO::开发者对异常的操作
        //可以在此交由系统处理
        /*function alert2(){
            $.messager.alert('My Title','Here is a error message!','error');
        }*/
       // return parent::render($e);
    }

}