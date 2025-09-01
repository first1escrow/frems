<?php
require_once dirname(__DIR__).'/openadodb.php';
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/tracelog.php';

$tlog = new TraceLog();
$tlog->insertWrite($_SESSION['member_id'], json_encode($_POST), '檔案上傳刪除');
$tlog = null; unset($tlog);

$conn->close();

$certifiedid = $_POST['certifiedid'];
if (!preg_match("/^\d{9}$/", $certifiedid)) {
	exit('查無此案件');
}

$name = $_POST['name'];
if (empty($name)) {
	exit('請指定欲刪除的檔案');
}

$fh = dirname(__DIR__).'/contractFile/'.$certifiedid.'/'.$name;

if (is_file($fh)) {
	if (unlink($fh)) {
		exit('刪除成功');
	} else {
		exit('刪除失敗');
	}
} else {
	exit('查無此檔案');
}

?>