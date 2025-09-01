<?php
// include_once dirname(__FILE__)."/rc4.php";
// include_once dirname(dirname(__FILE__))."/includes/channel_token.php";
// ini_set('date.timezone','Asia/Taipei') ;

$data = array();
// $data = json_decode(deCrypt($_REQUEST['v']),true);
setFlexTemplates($data);
##
//
function setFlexTemplates($data){
	$templates = array();
	$templates_body = array();

	$templates['type'] = 'bubble';
	$templates['body'] = array();
	$templates['footer'] = array();
	

	$templates_body = array();
	$templates_body['type'] = 'box';
	$templates_body['layout'] = 'vertical';
	$templates_body['contents'] = array();

	$contents = array();
	$contents['type'] = 'text';
	$contents['text'] = "Brown Cafe";
	$contents['weight'] = "bold";
	$contents['size'] = "xl";

	array_push($templates_body['contents'],$contents);
	unset($contents);

	$contents = array();
	$contents['type'] = 'box';
	$contents['layout'] = "vertical";
	$contents['margin'] = "lg";
	$contents['spacing'] = "sm";
	$contents['contents']  = array(); 

	$contents2 = array();
	$contents2['type'] = "box";
	$contents2['layout'] = "baseline";
	$contents2['spacing'] = "sm";
	$contents2['contents'] = array();

	$contents3 = array();
	$contents3['type'] = "text";
	$contents3['text'] = "Miraina Tower, 4-1-6 Shinjuku, Tokyo";
	$contents3['wrap'] = true;
	$contents3["color"] = "#666666";
	$contents3['size'] = "sm";
	$contents3['flex'] = 5;

	array_push($contents2['contents'],$contents3);
	array_push($contents['contents'],$contents2);
	array_push($templates_body['contents'],$contents);
	// array_push($templates['body'],$templates_body);
	$templates['body'] = $templates_body;

	unset($contents);unset($contents2);unset($contents3);

	$footer['type'] = "box";
	$footer['layout'] = 'vertical';
	$footer['spacing'] = "sm";
	$footer['contents'] = array();
	$contents = array();
	$contents['type'] = "button";
	$contents['style'] = "link";
	$contents['height'] = "sm";
	$contents['action'] = array();
	$action = array();
	$action['type'] = "uri";
	$action['label'] = "點我";
	$action['uri'] = "https://www.first1.com.tw/";
	$contents['action'] = $action;
	
	array_push($footer['contents'],$contents);

	// array_push($templates['footer'],$footer);
	$footer['flex'] = 0;
	$templates['footer'] = $footer;


	
	$post_data = array();
	$post_data['to'] =  $data['lineId'];

	$post_data['messages'] = array();

	$messages = array();
	$messages['type'] = 'flex';
	$messages['altText'] = '測試';
	$messages["contents"] = array();

	$messages["contents"] =$templates;
	// array_push($messages["contents"],$templates); //模板塞進陣列

	array_push($post_data['messages'],$messages);

	echo json_encode($templates);
	die;

	unset($templates);

	// echo "<pre>";
	// print_r($post_data);


	echo json_encode($post_data);

	pushSend($post_data);
}


//發送訊息
function pushSend($post_data=array()) {
	global $channel_token;
	$postLog = dirname(dirname(__FILE__)).'/log/pushLog.log' ;
	
	file_put_contents($postLog, date("Y-m-d H:i:s ").'POST: '.json_encode($post_data)."\n", FILE_APPEND) ;
	
	$header = array(
		'Authorization: Bearer {'.$channel_token.'}',
		'Content-Type: application/json; charset=utf-8'
	) ;
	
	// $url = 'https://api.line.me/v2/bot/message/push' ;
	$url = 'https://api.line.me/v2/bot/message/push';
	
	$ch = curl_init($url) ;
	curl_setopt($ch, CURLOPT_POST, true) ;
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST') ;
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ;
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data)) ;
	curl_setopt($ch, CURLOPT_HTTPHEADER,$header) ;
	
	$result = curl_exec($ch) ;
	echo "<pre>";
	print_r($result);
	// $returnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE) ;
	$returnCode = curl_getinfo($ch) ;
	curl_close($ch);
	
	file_put_contents($postLog, 'RETURNS: '.json_encode($returnCode)."\n\n", FILE_APPEND) ;
	
	return $returnCode ;
}
##
function enCrypt($str, $seed='first1app24602') {
	global $psiArr ;
	
	$encode = '' ;
	$rc = new Crypt_RC4 ;
	$rc->setKey($seed) ;
	$encode = $rc->encrypt($str) ;
	
	return $encode ;
}
##

//字串解碼
function deCrypt($str, $seed='first1app24602') {
	global $psiArr ;
	
	$decode = '' ;
	$rc = new Crypt_RC4 ;
	$rc->setKey($seed) ;
	$decode = $rc->decrypt($str) ;
	
	return $decode ;
}
##
// $data = $_REQUEST['v'] ;





// Bubble($data);
?>