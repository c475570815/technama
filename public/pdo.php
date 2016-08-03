<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Basic Layout - jQuery EasyUI Demo</title>
	<link rel="stylesheet" type="text/css" href="../../themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="../../themes/icon.css">
	<link rel="stylesheet" type="text/css" href="../demo.css">
	<script type="text/javascript" src="../../jquery.min.js"></script>
	<script type="text/javascript" src="../../jquery.easyui.min.js"></script>
</head>
<body>
<?php

 /*定义基本的配置（可根据需要修改）*/
 define("DB_TYPE","mysql");        //数据库类型
 define("DB_URL","127.0.0.1");     //数据库地址
 define("DB_NAME","techman");         //数据库名字
 define("DB_USER","root");  //登录用户名
 define("DB_PASS","908351632@qq.com");      //登录密码

 /*
 (1)连接数据库
 链接时需要指定数据库类型DB_TYPE，数据库服务器地址，数据库，用户名，密码
 */
 
try {       
       $dbh = new PDO(DB_TYPE.':host='.DB_URL.';dbname='.DB_NAME,DB_USER,DB_PASS);
	   //echo '连接成功<br>';
    }catch (PDOException $e){     
		//没有成功则抛出异常,并结束后面的运行
        print "Error!: " . $e->getMessage() . "<br/>";    
        die();
}
//设置默认编码
	$dbh->query("set names 'utf8'"); 
/*(2) 执行sql命令， 返回一个rs对象  */
	$sql1="SELECT name,sex,id_card,banji,phone,student_id,kemu from test_table ";
   $rs = $dbh->query($sql1);      
    //设置格式（字段型）   
   $rs->setFetchMode(PDO::FETCH_ASSOC);  
    
/*(3)  循环显示 */
?>

<table>
<?php
 
   while($row=$rs->fetch()){
	   
	    echo "<tr>";
		echo "<td>".$row['name'] ."</td>"; 
		echo "<td>".$row['sex'] ."</td>";
		echo "<td>".$row['id_card'] ."</td>";
		echo "<td>".$row['banji'] ."</td>";
		 echo "</tr>";
		 
   }
   
   
   echo '获取完成<br>';  
    
   ?>
  </table> 
   </body>
  </html>