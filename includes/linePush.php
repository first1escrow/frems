<?php
//Push to line
Function pushMsg($userId, $msg) {
	//Messenger Data
	$channel_token = 'jJ2jIw+YHZUVgu4q/ujKhmspb3zagO04jwHF2yDK6MLYWqR30J9JNCEndTstZrPPim7J/zvei3JjbNpvHe83+7t2tNDp2sp0k2Bd2bFOVS9FkfCMu88e5IfQPOr212Wz5A3T59U3MKtsVrt8zv4W+AdB04t89/1O/w1cDnyilFU=' ;
	##
	
	//header data
	$header = array(
		'Authorization: Bearer {'.$channel_token.'}',
		'Content-Type: application/json; charset=utf-8'
	) ;
	##
	
	//Start to push
	if (!empty($userId)) {
		$url = 'https://api.line.me/v2/bot/message/push' ;
		
		$post_data = array(
			'to' => $userId,
			'messages' => array(
				array('type' => 'text', 'text' => $msg)
			)
		) ;
		
		$ch = curl_init($url) ;
		curl_setopt($ch, CURLOPT_POST, true) ;
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST') ;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ;
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data)) ;
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header) ;
		
		$result = curl_exec($ch) ;
		// error_log($result);
		curl_close($ch) ;
		
		// echo $result ;
		return true ;
	}
	// else echo 'NG' ;
	else return false ;
	##
}
##

//Get the data to push
$msg = trim($_REQUEST['msg']) ;
$msg = urldecode($msg) ;
// $msg = 'aaaa' ;
if (empty($msg)) {
	echo "No Messages Found!!\n" ;
	exit ;
}
else if (preg_match("/^\".*\"$/is", $msg)) $msg = json_decode($msg) ;
// print_r($msg) ; exit ;
##

//To 
// $userId = 'Uab68d4fe3199f69c54ab80098351ed5f' ;
$userId = 'U62e7e4ca0ad872f7d38c603757f8fb7f' ;		//my
##

//
pushMsg($userId, $msg) ;
?>