<?php
include_once 'appPush.php';
$userDeviceToken = 'eq_ZK3iJxMs:APA91bGKVqUQIgoBkANKSQ24EgujRTGieGD1z-B6tUrn8d8WLV0PyQW9_g7rheUskCK48CSqYL1vFEl-fvQETWIPkWek2FnuPFUaT-hoV4pSQJb7xkfKv0C8pNV_cUTpmvy2AVzgv60I';
$msg = '測試';
echo pushMsg($userDeviceToken, $msg, $notifyType='1', $title='第一建經通知')

?>