<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once __DIR__ . '/writelog.php';

$add          = $_REQUEST['add'];
$del          = $_REQUEST['del'];
$cCertifiedId = $_REQUEST['cCertifiedId'];
$oDate_new    = $_REQUEST['oDate_new'];
$oKind_new    = $_REQUEST['oKind_new'];
$oIncome_new  = $_REQUEST['oIncome_new'];
$oExpense_new = $_REQUEST['oExpense_new'];
$oRemark_new  = $_REQUEST['oRemark_new'];

##日期為空，備註為空的情況下，備註帶指定文字
if (empty($oDate_new) && empty($oRemark_new)) {
    $oRemark_new = '待買方入帳後，始行支付予賣方';
}
##

$conn = new first1DB;

if ($add) {
    $sql = '
		INSERT INTO	tChecklistOlist
		(
			oCertifiedId,
			oDate,
			oKind,
			oIncome,
			oExpense,
			oRemark
		)
		VALUES
		(
			"' . $cCertifiedId . '",
			"' . $oDate_new . '",
			"' . $oKind_new . '",
			"' . $oIncome_new . '",
			"' . $oExpense_new . '",
			"' . $oRemark_new . '"
		) ;
	';
    $conn->exeSql($sql);

    //埋log紀錄
    checklist_log('買賣價金收支明細-賣方新增(保證號碼:' . $cCertifiedId . ')');
    ##
} else if ($del) {
    $sql = 'DELETE FROM tChecklistOlist WHERE oId = :del;';
    $conn->exeSql($sql, ['del' => $del]);

    //埋log紀錄
    checklist_log('買賣價金收支明細-賣方刪除(保證號碼:' . $cCertifiedId . ')');
    ##
}

# 讀取買方交易明細
$sql = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId = :cCertifiedId ORDER BY oDate ASC;';
$rs  = $conn->all($sql, ['cCertifiedId' => $cCertifiedId]);

$total = 0;
$tbl   = '';

foreach ($rs as $v) {
    $total += $v['oIncome'];

    $tbl .= '	<tr>' . "\n";
    $tbl .= '		<td><input type="text" style="width:80px;" name="oDate[]" value="' . $v['oDate'] . '"></td>' . "\n";
    $tbl .= '		<td><input type="text" style="width:220px;" name="oKind[]" value="' . $v['oKind'] . '"></td>' . "\n";
    $tbl .= '		<td><input type="text" style="width:100px;" name="oIncome[]" value="' . $v['oIncome'] . '"></td>' . "\n";
    $tbl .= '		<td><input type="text" style="width:100px;" name="oExpense[]" value="' . $v['oExpense'] . '"></td>' . "\n";
    $tbl .= '		<td><input type="text" style="width:100px;" disabled value="' . $total . '"></td>' . "\n";
    $tbl .= '		<td><input type="text" style="width:150px;" name="oRemark[]" value="' . $v['oRemark'] . '"></td>' . "\n";
    $tbl .= '		<td style="width:50px;"><input type="button" onclick="del_buyer_trans(' . $v['oId'] . ')" value="刪除"></td>' . "\n";
    $tbl .= '	</tr>' . "\n";
}

exit($tbl);
