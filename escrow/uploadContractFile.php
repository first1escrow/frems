<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;
//upload_max_filesiz = 10M
$tlog = new TraceLog() ;
$tlog->insertWrite($_SESSION['member_id'], json_encode($_GET), '檔案上傳') ;
$tlog->insertWrite($_SESSION['member_id'], json_encode($_FILES), '檔案上傳') ;
$_GET = escapeStr($_GET) ;

$id = $_GET['id'];
$failData = array();
if (!empty($_FILES)) {

	$uploadData = array();

	$saveUrl = dirname(dirname(__FILE__))."/contractFile/".$id;
	// echo $saveUrl;
	checkFile($_FILES);

	foreach ($_FILES as $k => $v) {
		
		//沒有資料夾就建立資料夾
		if (!is_dir($saveUrl)) {			
			mkdir($saveUrl."/",0775);	
		}
		// echo $saveUrl."\r\n";
		$uploadfile = $saveUrl.'/'.$v['name'];
		// echo $uploadfile."\r\n";

		// print_r($v);


		if (file_exists($uploadfile)) {
			$EXTENSION = pathinfo($uploadfile,PATHINFO_EXTENSION);
			$uploadfile = $saveUrl.'/'.str_replace('.'.$EXTENSION, '', $v['name']).'_'.date('YmdHis').".".$EXTENSION;
			unset($EXTENSION);
		}
		
		if (!move_uploaded_file($v['tmp_name'],$uploadfile)) {
			array_push($failData, $v['name']);
		}
		
	}


	if (!empty($failData)) {
		// echo implode(',', $failData);
		echo '失敗';

		exit;
	}else{

		echo "上傳成功";
		exit;
	}
	
}

function checkFile($fileData){
	$file_ext = array('pdf','xls','xlsx','csv','doc','docx','png','jpg','jpeg','bmp','gif','tiff','txt','rar','zip','wav');

	foreach ($fileData as $k => $v) {

		$extension = strtolower(pathinfo($v['name'],PATHINFO_EXTENSION));

		// echo $k."_".$v['name']."\r\n";
		if (!in_array($extension, $file_ext)) {			
			$msg = "不符合可以上傳的檔案類型";
			echo $msg;
			exit;
		}
	}
	

}
$conn->close();
?>