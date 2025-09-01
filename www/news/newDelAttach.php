<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;

$fn = $_REQUEST['fn'] ;
$sn = $_REQUEST['sn'] ;

if ($fn == 'p') $fn = ' nPicture="" ' ;
else if ($fn == 'f') $fn = ' nForm="" ' ;

$res = 'NG' ;

if ($fn) {
	$fh = '' ;
	$sql = 'SELECT * FROM tNews WHERE nId="'.$sn.'";' ;
	$rs = $conn->Execute($sql) ;
	
	if (preg_match("/nPicture/",$fn)) {
		//刪除圖片檔
		$fh = dirname(__FILE__).'/pic/'.$rs->fields['nPicture'] ;
		if (is_file($fh)) unlink($fh) ;
		##
	}
	else if (preg_match("/nForm/",$fn)) {
		//刪除文件檔
		$fh = dirname(__FILE__).'/form/'.$rs->fields['nForm'] ;
		if (is_file($fh)) unlink($fh) ;
		##
	}
	
	$sql = 'UPDATE tNews SET'.$fn.' WHERE nId="'.$sn.'";' ;
	if ($conn->Execute($sql)) $res = 'OK' ;
}

echo $res ;
