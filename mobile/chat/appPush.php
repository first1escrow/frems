<?php
// require_once 'snoopy/Snoopy.class.php' ;
//Push to APP 
//notifyType = 1: Data messages, which are handled by the client app.
//notifyType = 2: Notification messages, sometimes thought of as "display messages."


Function pushMsg($userDeviceToken, $msg, $notifyType='2', $title='第一建經通知') {
	//Messenger Data
	$api_access_key = 'AIzaSyAthvK7Zj6ygkMjeYESiAtzFO6O3BeZ4Mo' ;	//First1
	// $api_access_key ='AIzaSyBrw3iJWRLyFKds9KhUnH1cJz3i1C2t0h0';
	##
	
	//header data
	$header = array(
		'Authorization: key='.$api_access_key,
		'Content-Type: application/json'
	) ;
	##
	
	//Start to push
	if (!empty($userDeviceToken)) {
		$post_data = array() ;
		$url = 'https://fcm.googleapis.com/fcm/send' ;

		$post_data = array(
				'to' => $userDeviceToken,
				'priority' => 'normal',
				'notification' => array(
					'body' => $msg,
					'title' => $title,
					'dataType'=> $notifyType
				)
			) ;

		// 	$post_data = array(
		// 		'to' => $userDeviceToken,
		// 		'priority' => 'normal',
		// 		'data' => array('title' => $title, 'body' => $msg, 'data_type' => $notifyType)
		// 	) ;

		// print_r($post_data);
		
		// if ($notifyType == '1') {
		// 	$post_data = array(
		// 		'to' => $userDeviceToken,
				// 'priority' => 'normal',
				// 'data' => array('title' => $title, 'body' => $msg, 'data_type' => '1')

				// 'data' => array($title => $msg, 'data_type' => '1')
		// 	) ;
		// }
		// else {
			// $post_data = array(
				// 'to' => $userDeviceToken,
				// 'priority' => 'normal',
				// 'data_type' => '2',		//1=Slack、2=SMS
				// 'notification' => array($title => $msg)
			// ) ;
			// $post_data = array(
				// 'to' => $userDeviceToken,
				// 'priority' => 'normal',
				// 'notification' => array(
					// 'data_type' => '2',		//1=Slack、2=SMS
					// 'notification' => array($title => $msg)
				// )
			// ) ;
			// $post_data = array(
				// 'to' => $userDeviceToken,
				// 'priority' => 'normal',
				// 'notification' => array(
					// 'body' => 'great match!',
					// 'title' => 'Portugal vs. Denmark',
					// 'icon' => 'myicon'
				// )
			// ) ;
			// $post_data = array(
			// 	'to' => $userDeviceToken,
			// 	'priority' => 'normal',
			// 	'notification' => array(
			// 		'body' => $msg,
			// 		'title' => $title,
			// 	)
			// ) ;
		
		// print_r($post_data) ;
		$errString = '' ;
		
		$ch = curl_init($url) ;
		
		curl_setopt($ch, CURLOPT_POST, true) ;
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header) ;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ;
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data)) ;
		
		$result = curl_exec($ch) ;
		if (curl_errno($ch)) $errString = curl_error($ch) ;
		
		curl_close($ch) ;
		// echo $errString;
		if ($errString) return $errString ;
		else return $result ;
	}
	else return false ;
	##
}
##
// $msg = '您好';
// // $msg = json_decode($msg) ;
// $userId = 'doij2mSD664:APA91bH4bzXT2y3O7E7uU9oiFZI12gm2fCeY-UJWfUiPI0SoNpsn8M7-Q7uUkJR1BcWus5pbWVdvze1BaXb4AGsGXrAs5syg_9rsUas1P7o3qg5g34ElqCn-HooZuwUNpObNFAUsPfek';
// pushMsg($userId, $msg, '2', '第一建經通知1') ;
//Get the data to push
// $msg = trim($_REQUEST['msg']) ;
// $msg = '第一建經信託履約保證專戶已於10月8日收到保證編號004000000（買方郭OO賣方黃OO等2人）存入票據金額600000元,待票據兌現後再另行簡訊通知(157)' ;
// if (empty($msg)) {
// 	echo "No Messages Found!!\n" ;
// 	exit ;
// }
// else if (preg_match("/^\".*\"$/is", $msg)) $msg = json_decode($msg) ;
// ##

// //To 
// // $userId = 'APA91bEqU2Zn1ac6I4rtDVf0Gs-gvRJAB_3gI-nbYgffgjv63SRR-flOPYxowx23QGHtcb-q3KHSK2yFggF0CIYf5H7tLOntQ4E7YyMgpDZ_12bF6zQB3Uk9NrShxK8B7SJfvxWLwGe8bBKcgelJjdUcQNQDsIYXug' ;	//first1
// // $userId = 'cE5HsH40Z2k:APA91bGm0oQiSnosk-UFwNcxvMzcmCzHwaJXYGQ5tyjmBRX_3JWlfJEgWTtYkiH8a6QmI0bq1p5y9GqDVg34l-kPp9AhJZAc-NkboVnTEObKhddUl0BrtLFjZ6iDKXA0_GxzEgoVIfBt' ;	//APP
// $userId = 'APA91bGYNND_xO5dL8qDKCIUEd37rRL9adT7TAucmpvQHDqzUFSzOTMSB2mhdpmhMfx1xyb8OJlVzXRH4bMRQbTmYz2UXYmJQMPJQB0PWxhviifzkvDokp4K-14VAFa-rR1ctf0YvIks6IrNjuieNUH6h7E_Xf5GpA' ;	//jean
// //$userId = 'eTYMBQ0ji0Q:APA91bHDE-k0D-5oKiVXNwCXgpqCUPypugZ63ywe3m9wIdeyUOv0VgAzd1UvAoBSLPJWyTOkEOjRiSdXISliiOv0pp7fjQrZv0v1NLubPdTMXRSDNXGi' ;	//jason
// ##

// //
// echo pushMsg($userId, $msg, '2', '第一建經通知') ;
// echo pushMsg($userId, $msg, '1') ;
?>
