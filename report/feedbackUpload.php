<?php
// echo $_POST['upfile'] ;
if ($_POST['upfile']) {
	/* 
	echo "錯誤代碼：" . $_FILES["upload_file"]["error"] . "<br>\n" ;
	echo "檔案名稱: " . $_FILES["upload_file"]["name"]."<br/>"; 
	echo "檔	案類型: " . $_FILES["upload_file"]["type"]."<br/>";
	echo "檔案大小: " . ($_FILES["upload_file"]["size"] / 1024)." Kb<br />";
	echo "暫存名稱: " . $_FILES["upload_file"]["tmp_name"]; 
	exit ;
	 */
	$xlsName = date("YmdHis").'.xlsx' ;
	if (move_uploaded_file($_FILES["upload_file"]["tmp_name"], dirname(__FILE__)."/excel/".$xlsName)) {
		require 'feedbackPrint.php' ;
		exit ;
	}
	else {
		echo "系統錯誤!! (錯誤代碼：0x000001)\n" ;
		exit ;
	}
}
else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>回饋金轉檔列印</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="/js/calender_limit.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#dialog').dialog({
		autoOpen: false,
		modal: true,
		width: 600,
		height: 350
	}) ;
}) ;

function uploadFile() {
	var fn = $('#upload_file').val() ;
	fn = fn.replace(/\\/g,'/',fn) ;
	
	var arr = fn.split('/') ;
	fn = arr[(arr.length-1)] ;
	
	var arr1 = fn.split('.') ;
	var ext = arr1[(arr1.length-1)] ;
	
	if (ext != 'xlsx') {
		alert("轉檔格式錯誤!!本系統僅適用 Excel 2007(含)以上版本格式...") ;
		return false ;
	}
	else {
		$('[name="upfile"]').val('ok') ;
		
		//$('#dialog').html('') ;
		$('#dialog').dialog('open') ;
		
		$('form[name="myform"]').submit() ;
	}
}
</script>
<style>
input {
	/* background-color: #FFFFFD ;*/
}
</style>
</head>
<body>
<div id="dialog" title="開始轉檔作業...請稍候!!"><img src="/images/please_wait.gif"></div>
<form name="myform" method="POST" enctype="multipart/form-data">
檔案名稱：
<input type="file" name="upload_file" id="upload_file">
<input type="hidden" name="upfile">　　
<input type="button" value="轉檔列印" onclick="uploadFile()">
</form>
</body>
</html>
<?php
}
?>