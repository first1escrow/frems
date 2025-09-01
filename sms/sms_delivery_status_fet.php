<?php
$deliveryStatusArr = array(
	'delivered' 	=> array('reason' => '簡訊已傳送至用戶手機', 'ny' => 'y', 'code' => '0'),
	'expired' 		=> array('reason' => '簡訊時間過期！無法送達用戶手機', 'ny' => 'y', 'code' => '2'),
	'undeliverable' => array('reason' => '資訊或門號不正確！無法送達用戶手機', 'ny' => 'y', 'code' => '3'),
	'rejected' 		=> array('reason' => '簡訊被訊息中心拒絕發送', 'ny' => 'y', 'code' => '4'),
	'deleted'		=> array('reason' => '簡訊被訊息中心刪除', 'ny' => 'y', 'code' => '5'),
	'unknown'		=> array('reason' => '簡訊無效或未知的狀態', 'ny' => 'y', 'code' => '6'),
	'unacceptable'	=> array('reason' => '訊息中心不接受此則簡訊', 'ny' => 'y', 'code' => '7'),
	'submitted'		=> array('reason' => '簡訊已傳送至用戶手機', 'ny' => 'y', 'code' => '0'),
	'enroute'		=> array('reason' => '簡訊傳送中', 'ny' => 'n', 'code' => '9'),
	'accepted'		=> array('reason' => '簡訊傳送中', 'ny' => 'n', 'code' => '10'),
	'blocked'		=> array('reason' => '用戶門號被列入黑名單！簡訊不發送', 'ny' => 'y', 'code' => '11'),
	'spam'			=> array('reason' => '簡訊包含NCC定義垃圾信關鍵字！不發送', 'ny' => 'y', 'code' => '12'),
) ;

//'submitted'		=> array('reason' => '簡訊傳送中', 'ny' => 'n', 'code' => '8'),

?>