<?php
$startday = '2015-9-10';
$endday = '2015-11-20';

$reserve_day = date('N', $startday);
echo "开头". $reserve_day."<br/>";
$dt_start = new DateTime($startday);
$dt_end = new DateTime($endday);
$days = $dt_start->diff($dt_end)->format("%a")+1;

echo "总天数". $days."<br/>";

$all_weeks = array();
for ($i = 0; $i < $days; $i++) {
    $week = array();
    if ($i == 0) {
        for ($j = $reserve_day; $j <= 7 && $i<$days; $j++) {
            $s = 'P' . $i . 'D';
            $interval = new DateInterval($s);
            $current_dt=  clone $dt_start;
            $week[$j] = $current_dt->add($interval);
            $i++;
        }
    } else {
        for ($j = 1; $j <= 7 && $i<$days; $j++) {
            $s = 'P' . $i . 'D';
            $interval = new DateInterval($s);
            $current_dt=  clone $dt_start;
            $week[$j] = $current_dt->add($interval);
            $i++;

        }
    }
    $i--;//!!!!!!!
    $all_weeks[] = $week;
}
var_dump($all_weeks);



?>
