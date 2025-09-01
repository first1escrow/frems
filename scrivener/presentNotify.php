<?php
// include_once '../openadodb.php' ;
// include_once '../session_check.php' ;
// include_once dirname(dirname(__FILE__)).'/openadodb.php';
// file_get_contents("https://firstbotnew.azurewebsites.net/bot/api/linePush.php?lineId=U4b14569b842b0d5d4613b77b94af02b6&txt="+urlencode('CC'));
require_once dirname(__DIR__).'/first1DB.php';

Function getBirthday() {
	$conn = new first1DB;

	$sql = "SELECT	
			sl.sId,
			sl.sScrivener,
			s.sName,
			CONCAT('SC', LPAD(s.sId,4,'0')) as sCode2,
			s.sBirthday,
			sl.sLevel,
			(SELECT pName FROM tPeopleInfo WHERE pId = ss.sSales) AS salesName
		FROM
			tScrivener AS s
		LEFT JOIN
			tScrivenerLevel AS sl ON sl.sScrivener=s.sId
		LEFT JOIN
		    tScrivenerSales AS ss ON ss.sScrivener=s.sId
		WHERE
			sl.sStatus = 2";

	$rs = $conn->all($sql);

	$txt = '';
	foreach ($rs as $v) {
		$txt.= $v['salesName']."申請".$v['sCode2']."_".$v['sName']."的生日禮\r\n";
	}
	
	$fw = dirname(__DIR__).'/log2/smscheck';
	if (!is_dir($fw)) {
		mkdir($fw);
	}
	$fw .= '/presentNotify_'.date('Ymd').'.log';
	file_put_contents($fw, date('Y-m-d H:i:s')."\n".$txt."\n", FILE_APPEND);
	
	echo $txt;
}

$member_id = empty($_COOKIE['member_id']) ? '' : $_COOKIE['member_id'];
if (empty($member_id)) {
	if (session_status() != 2) {
    	session_start() ;
	}

	$member_id = empty($_SESSION['member_id']) ? '' : $_SESSION['member_id'];
}

$txt = '';
if (!file_exists(dirname(__DIR__).'/log2/smscheck/b'.$member_id.'.log')) {
	getBirthday();
} else {
	$notifyTime = date('Y-m-d')." 12:00";
	
	if (date('Y-m-d H:i') == $notifyTime) {
		getBirthday();
	}
}

$fw = dirname(__DIR__).'/log2/smscheck';
if (!is_dir($fw)) {
	mkdir($fw);
}
$fw .= '/b'.$member_id.'.log';
file_put_contents($fw, date('Y-m-d H:i').":00\n", FILE_APPEND);

?>