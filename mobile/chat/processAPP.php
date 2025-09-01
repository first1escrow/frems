<?php

include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
require_once 'snoopy/Snoopy.class.php' ;
require_once 'appPush.php';
##

//檢查是否為上傳檔案
Function checkUploadFile() {
	global $_FILES ;
	
	if (!empty($_FILES['appfile']['name'])) return true ;
	else return false ;
}
##

//接收檔案並存檔
Function getSenderFile($acc, $dev, $flow) {
	global $_FILES ;
	// global $conn ;
	
	$showName = $_FILES["appfile"]["name"] ;
	$ext = pathinfo($showName, PATHINFO_EXTENSION) ;
	$uploadName = 'files/'.uniqid().'.'.$ext ;
	
	$tf = false ;
	
	if (checkUploadFile()) {
		if (move_uploaded_file($_FILES["appfile"]["tmp_name"], $uploadName)) {
			postFile($acc,$uploadName);
		}
	}
	
	return $tf ;
}
##
function postFile($acc,$upload){
	global $conn;
	global $_FILES ;

	$datafile['file'] = $_FILES['appfile'];//$_FILES['image']['tmp_name']
	$sql = "SELECT
				aa.aPushToken,
				(SELECT SlackId FROM tAppSlack AS asl WHERE asl.aId = aa.aSlackId) AS SlackId,
				(SELECT (SELECT pSlackToken FROM tPeopleInfoAccount AS p WHERE p.pInfoId= s.sUndertaker1) FROM tScrivener AS s WHERE s.sId = SUBSTR(aa.aParentId,3)) AS pSlackToken
		
			FROM
				tAppAccount AS aa
			WHERE 
				aa.aId = '".$acc."'"; //aSlackId

	$rs = $conn->Execute($sql);

	$slackId = $rs->fields['SlackId']; //群組
	$token = $rs->fields['pSlackToken'];//經辦TOKEN
	$push = $rs->fields['aPushToken'];//push

    $url = 'https://slack.com/api/files.upload' ; //rtm.start

	$post = array(
		"token"=>$token,
		"pretty"=>1,
		"channels"=>$slackId,
		"token"=>$token,
		"file"=>"@".getcwd()."/".$upload."",

	);

	$ch = curl_init();
	
	$options = array(
		CURLOPT_URL=>$url,
		CURLOPT_POST=>true,
		CURLOPT_POSTFIELDS=>$post, // 直接給array
		CURLOPT_SSL_VERIFYHOST => 0,
   		CURLOPT_SSL_VERIFYPEER => 0,
	);
	curl_setopt_array($ch, $options);

	if (curl_errno($ch)) $errString = curl_error($ch) ;
	curl_exec($ch);

	
	if ($errString == '') {
		unlink($upload);
		pushMsg($push, '您有新訊息', '1', '第一建經通知') ;
	}
	curl_close($ch);

	

}
##
//接收檔訊息
Function getSenderMsg($acc, $dev, $flow, $txt='') {
	global $conn ;
	$tf = false ;
	
	if ($txt) {
		postMessage($acc,$txt);
	}

	return $tf ;
}

function postMessage($acc,$txt){
	global $conn ;

	$sql = "SELECT
				aa.aId,
				aa.aPushToken,
				(SELECT SlackId FROM tAppSlack AS asl WHERE asl.aId = aa.aSlackId) AS SlackId,
				(SELECT (SELECT pSlackToken FROM tPeopleInfoAccount AS p WHERE p.pInfoId= s.sUndertaker1) FROM tScrivener AS s WHERE s.sId = SUBSTR(aa.aParentId,3)) AS pSlackToken,
				(SELECT (SELECT pSlackId FROM tPeopleInfoAccount AS p WHERE p.pInfoId= s.sUndertaker1) FROM tScrivener AS s WHERE s.sId = SUBSTR(aa.aParentId,3)) AS pSlackId
			FROM
				tAppAccount AS aa
			WHERE 
				aa.aId = '".$acc."'"; //aSlackId
	// echo $sql;
	$rs = $conn->Execute($sql);

	$slackId = $rs->fields['SlackId']; //對方ID
	$token = $rs->fields['pSlackToken'];//經辦TOKEN
	$pSlackId = $rs->fields['pSlackId'];
	$push = $rs->fields['aPushToken'];//push

	$args = array('token'=>$token,'pretty'=>1,'channel'=>$slackId,'text'=>$txt,'as_user'=>true) ;
   

    $url = 'https://slack.com/api/chat.postMessage' ; //rtm.start

    $snoopy = new Snoopy ; 
    $snoopy->submit($url,$args) ;
    $html = $snoopy->results ;
        
    $list = json_decode($html,true);
   
    	
    if ($list['ok'] == 1) {
    	pushMsg($push, '您有新訊息', '1', '第一建經通知') ;
    	$imId = getImId($token,$slackId);
    	getSlackMsg($token,$imId,$rs->fields['aId'],$pSlackId);
    	return true;
    }else{
    	die('error for slack chat post');
    }

}
function getImId($token,$slackId){

	$url = 'https://slack.com/api/im.list' ; //rtm.start
    $args = array('token'=>$token,'pretty'=>1) ;

    $snoopy = new Snoopy ; 
    $snoopy->submit($url,$args) ;
    $html = $snoopy->results ;
        
    $list = json_decode($html,true);
    foreach ($list['ims'] as $k => $v) {
    	if ($slackId == $v['user']) {
    		return $v['id'];
    		// getMsg($token,$v['id']);
    	}
    }
   
    
}
function getSlackMsg($token,$imId,$aId,$pSlackId){
	global $conn;
	$url = 'https://slack.com/api/im.history' ; //rtm.start
    $args = array('token'=>$token,'channel'=>$imId,'pretty'=>1) ;

    $snoopy = new Snoopy ; 
    $snoopy->submit($url,$args) ;
    $html = $snoopy->results ;
        
    $list = json_decode($html,true);
    
    if ($list['ok'] == 1) {
    	foreach ($list['messages'] as $k => $v) {
    		
    		$flow = ($v['user'] == $pSlackId) ? '2':'1';

    		if (is_array($v['file'])) {
    			
    			// base64_encode(file_get_contents($tmpname))
    			if (is_array($v['file']['initial_comment'])) { //如果上傳檔案有輸入文字的話
    				$txt = $v['file']['initial_comment']['comment'];
    			}else{
    				// $txt = $v['file']['name'];
    			}
    			
    		}else{
    			$txt = $v['text'];
    		}
    		$ck = checkMsg($v,$aId,$flow,$txt);
    		
    		// echo checkMsg($v,$aId,$flow);
    			if ($ck == 0) { //$pSlackId


    				$sql = "INSERT INTO
		    				tAppMessages
		    				(
		    					aAccount,
		    					aSlackId,
		    					aFlow,
		    					aContent,
		   						aAppFile,
		    					aAppFileName,
		    					aCreateTime
		    			 	)
							VALUES (
								'".$aId."',
								'".$v['user']."',
								'".$flow."',
								'".$txt."',
								'".$v['file']['id']."',
								'".$v['file']['name']."',
								'".date('Y-m-d H:i:s',$v['ts'])."'
							)";

					$conn->Execute($sql);

    		 }
	    	
	    }
	   
    }
}

function checkMsg($msg,$aId,$flow,$txt){
	global $conn;

	
	$sql = "SELECT
				*
			FROM
				tAppMessages
			WHERE
				aAccount ='".$aId."'
			    AND aFlow = '".$flow."'
			    AND aContent ='".$txt."'
			    AND aCreateTime ='".date('Y-m-d H:i:s',$msg['ts'])."'";

	// echo $sql."\r\n";
	// die;
	$rs = $conn->Execute($sql);

	$total=$rs->RecordCount();

	return $total;
	
}
##


$_POST = escapeStr($_POST) ;
$from = $_POST['flow'];//訊息流向
$myToken['acc'] = $_POST['target'] ;

foreach ($_POST as $k => $v) {
	parse_str($k.'='.$v) ;
}

if (empty($from)) exit('NG') ;


if ($from == 1) {
	if (checkUploadFile()) {
		//上傳檔案
		getSenderFile($myToken['acc'], $myToken['dev'], '1') ;
		
		header('Location: messageManager.php?from='.$from.'&target='.$target) ;
		exit ;
		##
	}
	else {
		//上傳訊息
		getSenderMsg($myToken['acc'], $myToken['dev'], '1', $content) ;
		
		header('Location: messageManager.php?from='.$from.'&target='.$target) ;
		exit ;
		##
	}
}
else if ($from = 2) { //經辦發送
	if (checkUploadFile()) {
		//上傳檔案
		getSenderFile($myToken['acc'], $myToken['dev'], '2') ;
		// include_once 'getSlackMsg.php';
		header('Location: messageManager.php?from='.$from.'&target='.$target) ;
		exit ;
		##
	}
	else {
		//上傳訊息
		getSenderMsg($myToken['acc'], $myToken['dev'], '2', $content) ;

		// include_once 'getSlackMsg.php';
		header('Location: messageManager.php?from='.$from.'&target='.$target) ;
		exit ;
		##
	}
}

?>