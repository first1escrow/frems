<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
require_once 'snoopy/Snoopy.class.php' ;



$_GET = escapeStr($_GET) ;

$id = $_GET['id'];
$slackId = $_GET['sId'];
$id = 'F44RQ077H';
$slackId = 'U41V89FN0';
// $id = 'F35NG5VB9';
// $slackId =3;
//getSlackFile.php?id=F42TNTGFJ&sId=U41V89FN0
$sql = "SELECT aSlackToken FROM tAppSlack WHERE SlackId ='".$slackId."'";

$rs = $conn->Execute($sql);

$token = $rs->fields['aSlackToken'];
// echo $token."<br>";
$tmp = checkFile($id,$token,$slackId);

$url = $tmp['url'];
$file = $tmp['aFileContent'];
$fileName = $tmp['fileName'];

unset($tmp);
function checkFile($id,$token,$slackId){
	global $conn;

	$sql = "SELECT aFileLink,aAppFileName,aFileContent FROM tAppMessages WHERE aAppFile = '".$id."'";
	// echo $token."<br>";
	// $token = 'xoxp-61019054816-137994321748-137847485283-f6697fa7bee52b0ded1877f1c85c4351';
	$token = 'xoxp-61019054816-137240737953-138599724198-a92326d61e6b7b91acdd3577e61d9d91';
		// echo $token."<br>";
		// die;
	$rs = $conn->Execute($sql);

	if ($rs->fields['aFileLink'] != '') {
		$data['url'] = $rs->fields['aFileLink'];
		$data['file'] = $rs->fields['aFileContent'];
		$data['fileName'] = $rs->fields['aAppFileName'];
	}else{

		$url = 'https://slack.com/api/files.sharedPublicURL' ; //rtm.start
		$args = array('token'=>$token,'file'=>$id,'pretty'=>1) ;

		$snoopy = new Snoopy ; 
		$snoopy->submit($url,$args) ;
		$html = $snoopy->results ;
		        
		$list = json_decode($html,true);
		// print_r($html);
		//

		if ($list['ok'] == 1) {
			
			// $data['url'] = $list['file']['permalink_public'];
			// echo $list['file']['permalink_public'];

			$data['url'] = getPic($list['file']['permalink_public']);
			$data['file'] = base64_encode(file_get_contents($data['url'])) ;
			$data['fileName'] = $rs->fields['aAppFileName'];

			$sql = "UPDATE tAppMessages SET aFileLink ='".$data['url']."',aFileContent='".$data['file']."' WHERE aAppFile ='".$id."'";
			// echo $sql;
			$conn->Execute($sql);

			
		}else{
			echo "檔案連結失效(1)";
			die;
		}
	}

	return $data;
}
function getPic($url){

	$snoopy = new Snoopy ; 
	$snoopy->submit($url,$args) ;
	$html = $snoopy->results ;
		        // print_r($html);
		        // die;
	//https://files.slack.com/files-pri/T1T0K1LQ0-F43EU0QFK/jpeg_20170211_111525_1991317321.jpg?pub_secret=f58672e669
	preg_match_all("/<a class=\"file_body image_body\" href=\"(.*)\">.*/", $html, $tmp);
	
	if ($tmp[1][0]) {
		return $tmp[1][0];
	}else{
		echo "檔案連結失效(2)";
		die;
	}
	
}

if ($url) {
	// echo $url;
	if ($file == '') {
		// $fileUrl = getPic($url);
		$file = base64_encode(file_get_contents($url)) ;
		$sql = "UPDATE tAppMessages SET aFileContent='".$file."' WHERE aAppFile ='".$id."'";
		// echo $sql;
		$conn->Execute($sql);
	}
	

	
	// echo "<script>location.href='".$fileUrl."'</script>";
	
	// $fileUrl ='http://www.first1.com.tw/bank/dbs/DBS_DM20170213.jpg';
	// $file = base64_encode(file_get_contents($fileUrl)) ;

	$img = str_replace('data:image/jpeg;base64,', '', $file);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	$file =  'files/'.$fileName. '';
	$success = file_put_contents($file, $data);

	// echo "<a href='".$url."'>按右鍵另存新檔</a>";
	// header("Content-type: image/jpeg") ; 
	// echo base64_decode($file) ;
	// <img src='picture/loading.gif' data-original="data:image/jpeg;base64,BASE64編碼" />
	echo "<img src='".$file."'/>";
}else{
	echo "檔案連結失效(2)";
	die;
}

// print_r($list);

?>
