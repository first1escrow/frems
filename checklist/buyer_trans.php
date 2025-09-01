<?php
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';
require_once __DIR__.'/writelog.php';

$add          = $_REQUEST['add'];
$del          = $_REQUEST['del'];
$cCertifiedId = $_REQUEST['cCertifiedId'];
$bDate_new    = $_REQUEST['bDate_new'];
$bKind_new    = $_REQUEST['bKind_new'];
$bIncome_new  = $_REQUEST['bIncome_new'];
$bExpense_new = $_REQUEST['bExpense_new'];
$bRemark_new  = $_REQUEST['bRemark_new'];

$conn = new first1DB;

##日期為空，備註為空的情況下，備註帶指定文字
if (empty($bDate_new) && empty($bRemark_new)) {
	$bRemark_new = '待買方入帳後，始行支付予賣方';
}
##

if ($add) {
	$sql = '
		INSERT INTO	tChecklistBlist 
		(
			bCertifiedId, 
			bDate,	
			bKind,	
			bIncome, 
			bExpense, 
			bRemark	
		)
		VALUES 
		(
			"'.$cCertifiedId.'",
			"'.$bDate_new.'",
			"'.$bKind_new.'",
			"'.$bIncome_new.'",
			"'.$bExpense_new.'",
			"'.$bRemark_new.'"
		);
	';
	$conn->exeSql($sql);

	//埋log紀錄
	checklist_log('買賣價金收支明細-買方新增(保證號碼:'.$cCertifiedId.')');
	##
}
else if ($del) {
	$sql = 'DELETE FROM tChecklistBlist WHERE bId="'.$del.'";';
	$conn->exeSql($sql);

	checklist_log('買賣價金收支明細-買方刪除(保證號碼:'.$cCertifiedId.')');
}

# 讀取買方交易明細
$sql = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId = :cId ORDER BY bDate ASC;';
$rs  = $conn->all($sql, ['cId' => $cCertifiedId]);

$total = 0 ;
$tbl   = '';

foreach ($rs as $v) {
	$total += $v['bIncome'];
	
	$tbl .= '	<tr>'."\n";
	$tbl .= '		<td><input type="text" style="width:80px;" name="bDate[]" value="'.$v['bDate'].'"></td>'."\n";
	$tbl .= '		<td><input type="text" style="width:220px;" name="bKind[]" value="'.$v['bKind'].'"></td>'."\n";
	$tbl .= '		<td><input type="text" style="width:100px;" name="bIncome[]" value="'.$v['bIncome'].'"></td>'."\n";
	$tbl .= '		<td><input type="text" style="width:100px;" name="bExpense[]" value="'.$v['bExpense'].'"></td>'."\n";
	$tbl .= '		<td><input type="text" style="width:100px;" disabled value="'.$total.'"></td>'."\n";
	$tbl .= '		<td><input type="text" style="width:150px;" name="bRemark[]" value="'.$v['bRemark'].'"></td>'."\n"; 
	$tbl .= '		<td style="width:50px;"><input type="button" onclick="del_buyer_trans('.$v['bId'].')" value="刪除"></td>'."\n";
	$tbl .= '	</tr>'."\n";
}

echo $tbl ;
?>