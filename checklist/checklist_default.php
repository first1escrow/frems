<?php
require_once dirname(__DIR__).'/configs/config.class.php';
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';
require_once __DIR__.'/writelog.php';

$cCertifiedId = $_REQUEST['cCertifiedId'];

$conn = new first1DB;

$sql = 'SELECT cInvoiceClose FROM tContractCase WHERE cCertifiedId = :cCertifiedId;';
$detail = $conn->one($sql, ['cCertifiedId' => $cCertifiedId]);

// 買賣方點交表基本資料刪除
$sql = 'DELETE FROM tChecklist WHERE cCertifiedId = :cCertifiedId;';
$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);

// 買方收支明細刪除
$sql = 'DELETE FROM tChecklistBlist WHERE bCertifiedId = :cCertifiedId;';
$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);

// 賣方收支明細刪除
$sql = 'DELETE FROM tChecklistOlist WHERE oCertifiedId = :cCertifiedId;';
$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);

// 前台點交表紀錄刪除
$sql = 'DELETE FROM tUploadFile WHERE tCertifiedId = :cCertifiedId;';
$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);

// 刪除點交單銀行資訊列表
$sql = 'DELETE FROM tChecklistBank WHERE cCertifiedId = :cCertifiedId;';
$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);
##

if ($detail['cInvoiceClose'] != 'Y') { //關閉禁止重做
	// 清除買方利息金額
	$sql = 'UPDATE tContractBuyer SET cInterestMoney = "0" WHERE cCertifiedId = :cCertifiedId;';
	$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);
	##

	// 清除賣方利息金額
	$sql = 'UPDATE tContractOwner SET cInterestMoney = "0" WHERE cCertifiedId = :cCertifiedId;';
	$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);
	##

	// 清除其他買賣方利息金額
	$sql = 'UPDATE tContractOthers SET cInterestMoney = "0" WHERE cCertifiedId = :cCertifiedId AND cIdentity IN ("1", "2");';
	$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);
	##

	// 清除仲介利息金額
	$sql = 'UPDATE tContractRealestate SET cInterestMoney = "0", cInterestMoney1 = "0", cInterestMoney2 = "0" WHERE cCertifyId = :cCertifiedId;';
	$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);
	##

	// 清除地政士利息金額
	$sql = 'UPDATE tContractScrivener SET cInterestMoney = "0" WHERE cCertifiedId = :cCertifiedId;';
	$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);
	##

	// 清除無履保但有利息之出款日期
	$sql = 'UPDATE tContractCase SET cBankList="" WHERE cCertifiedId = :cCertifiedId;';
	$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);
	##
}


//刪除結清撥付款項明細其他新增的
$sql = 'DELETE FROM tChecklistOther WHERE  cCertifiedId = :cCertifiedId;';
$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);

$sql = 'DELETE FROM tChecklistRemark WHERE  cCertifiedId = :cCertifiedId;';
$conn->exeSql($sql, ['cCertifiedId' => $cCertifiedId]);

//埋log紀錄
checklist_log('點交表重設(保證號碼:'.$cCertifiedId.')');
##

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>點交表</title>
<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">

</script>
<style>
</style>
</head>
<body bgcolor="#F8ECE9">
<script type="text/javascript">
alert('<?=$cCertifiedId?> 的點交表已經回復為原始預設值了!!請重新製作點交表...') ;
location = "form_list_db.php?cCertifiedId=<?=$cCertifiedId?>" ;
</script>
</body>
</html>