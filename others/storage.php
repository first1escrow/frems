<?php


// $total = disk_total_space('/home/httpd/html/first.twhg.com.tw') ;

// $total_G = round(($total /1024 /1024 /1024),1);

// echo '總容量'.$total_G."\r\n";



$HD_free = disk_free_space('/home/httpd/html/first.twhg.com.tw'); //取得單位為Bytes

$HD_free = round(($HD_free /1024 /1024 /1024),1);
echo '可用空間'.$HD_free."\r\n";
// $info = system('df');

// print_r($info);


?>