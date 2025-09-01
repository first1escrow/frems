<?php
include('sms_function.php');
include('/home/httpd/html/first.twhg.com.tw/adodb5/adodb.inc.php');
$testMail = new SMS_Gateway();
//send($pid,$sid,$bid,$target,$tid,$ok="n")
$_t = $testMail->send('010515625','14','87','income','1541','n');
print_r($_t);
?>