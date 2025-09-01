<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../openadodb.php' ;

$id = $_REQUEST['id'] ;
$tmp = explode("T",$_REQUEST['sdate']) ;
$sdate = $tmp[0] ;
$stime = $tmp[1] ;
unset ($tmp) ;

$tmp = explode("T",$_REQUEST['edate']) ;
$edate = $tmp[0] ;
$etime = $tmp[1] ;
unset($tmp) ;
//echo 'ID='.$id.'==>'.$sdate.' '.$stime.' and '.$edate.' '.$etime ;
//
if ($id) {
	$sql = 'UPDATE tCalendar SET cStartDateTime="'.$sdate.' '.$stime.'", cEndDateTime="'.$edate.' '.$etime.'" WHERE cId="'.$id.'";' ;
	if ($conn->Execute($sql)) {
		echo '已更新' ;
	}
}
##
?>