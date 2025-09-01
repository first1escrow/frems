<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
// print_r($_POST);

// print_r($_GET);


$uploaddir = '/home/httpd/html/SSL/newWeb/api_test/upload/' ;
$link = 'https://escrow.first1.com.tw/api_test/upload/';
	##
//Array ( [upload] => Array ( [name] => news_photo_001.jpg [type] => image/jpeg [tmp_name] => /tmp/phpTk8W8S [error] => 0 [size] => 27058 ) )
//設定檔案名稱[name] => news_photo_001.jpg
// $filename = str_replace(strchr($_FILES['upload']['name'],'.'), '', $_FILES['upload']['name']);
$uploadfile = $uploaddir.$_FILES['upload']['name'];
$url = $link.$_FILES['upload']['name'];
// $uploadfile = $uploaddir.$_FILES['upload']['name'] ;
// $localfile = $_FILES['upload']['tmp_name'] ;
// $filename = $_FILES['upload']['name'] ;

// echo strchr($_FILES['upload']['name'],'.') ;
##Array ( [CKEditor] => content [CKEditorFuncNum] => 1 [langCode] => zh ) .jpg
// $url = 'https://escrow.first1.com.tw/api_test/upload'.$filename;
if (move_uploaded_file($_FILES['upload']['tmp_name'],$uploadfile)) {
	echo '<html><body><script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$_GET['CKEditorFuncNum'].', "'.$url.'",true);</script></body></html>';

}
		// echo $uploadfile;

		// die;

	// include_once '../SFTP/Net/SFTP.php' ;
	// define('NET_SFTP_LOGGING', NET_SFTP_LOG_COMPLEX);
		
	// $sftp = new NET_SFTP('203.69.66.41') ;
	// if (!$sftp->login('twhg', 'twhG5008')) {
	// 	$error = "1" ;
	// }
	// else {
	// 	$sftp->chdir('./upload/') ;
	// 	$sftp->put($_FILES['upload']['name'], $_FILES['upload']['tmp_name'], NET_SFTP_LOCAL_FILE) ;
			
	// 	$error = "2" ;
	// }
?>

