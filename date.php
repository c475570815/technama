<?php
  $startday='2015-1-1';
$endday='2015-1-3';
 $dt_start=strtotime($startday);
        $dt_end=strtotime($endday);
echo $dt_start-$dt_end;
?>
