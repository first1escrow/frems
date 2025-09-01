<?php
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../openadodb.php' ;
require_once('../tcpdf/tcpdf.php');
// $_POST = escapeStr($_POST) ;

$nu = $_GET['sn'];

// $sql = "SELECT tPayOk,tExport_nu,SUM(tMoney) as M,tExport_time,tVR_Code,tBank_kind,tObjKind2 FROM tBankTrans WHERE tExport_nu = '".$nu."'  ";
//查詢返還的資料
$sql = "SELECT tId,tObjKind2Date,tBankLoansDate FROM tBankTrans WHERE tExport_nu = '".$nu."'";
// echo $sql;
$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {
	$id = getTaxData($rs->fields['tId']); //反查申請時出款的ID
	$list[$i]['tId'] = $id;
	$list[$i]['tObjKind2Date'] = $rs->fields['tObjKind2Date'];
	$list[$i]['tBankLoansDate'] = $rs->fields['tBankLoansDate'];
	$i++;

	$rs->MoveNext();
}

for ($i=0; $i < count($list); $i++) { 
	$sql = "SELECT eMoney,eCertifiedId,eItem,(SELECT cName FROM tCategoryExpense WHERE cId =eItem) AS ItmeName FROM  tExpenseDetail WHERE eOK = '".$list[$i]['tId']."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		

		$data[$rs->fields['eCertifiedId']][$rs->fields['ItmeName']] =  $rs->fields['eMoney'];
		$data[$rs->fields['eCertifiedId']]['tObjKind2Date'] = $list[$i]['tObjKind2Date'];
		$data[$rs->fields['eCertifiedId']]['totalMoney'] += $rs->fields['eMoney'];
		$data[$rs->fields['eCertifiedId']]['tBankLoansDate'] = $list[$i]['tBankLoansDate'];
		$rs->MoveNext();
	}
}

## L
$pdf = new TCPDF('L', 'cm', 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetMargins('1', '1', '1');
$pdf->SetCreator(PDF_CREATOR);	
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// 
//MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
$pdf->AddPage();
$pdf->SetFont('msungstdlight', 'B', 18);
$title = (date('Y')-1911).date('m').date('d').'第一建經代墊稅請款明細總表';
$pdf->MultiCell('', '', $title, 0, 'C', 0, 1);

$pdf->SetFont('msungstdlight', 'B', 11);
// 					
$h = 0.5;
$pdf->MultiCell(1.5,$h, '序號', 1, 'C', 0, 0);
$pdf->MultiCell(2,$h, '履保號碼', 1, 'C', 0, 0);
$pdf->MultiCell(2,$h, '繳稅日期', 1, 'C', 0, 0);
$pdf->MultiCell(2,$h, '請款日期', 1, 'C', 0, 0);
$pdf->MultiCell(2.5,$h, '土地增值稅', 1, 'C', 0, 0);
$pdf->MultiCell(2,$h, '房屋稅', 1, 'C', 0, 0);
$pdf->MultiCell(2,$h, '地價稅', 1, 'C', 0, 0);
$pdf->MultiCell(2,$h, '契稅', 1, 'C', 0, 0);
$pdf->MultiCell(2,$h, '印花稅', 1, 'C', 0, 0);
$pdf->MultiCell(3,$h, '買方預收款項', 1, 'C', 0, 0);
$pdf->MultiCell(2.5,$h, '工程受益費', 1, 'C', 0, 0);
$pdf->MultiCell(2,$h, '其他', 1, 'C', 0, 0);
$pdf->MultiCell(2,$h, '合計金額', 1, 'C', 0, 1);
##

$num = 1;
$totalMoney = 0;
if (is_array($data)) {
	foreach ($data as $k => $v) {
		$pdf->MultiCell(1.5,$h, ($num++), 1, 'C', 0, 0);
		$pdf->MultiCell(2,$h,$k, 1, 'C', 0, 0);
		$pdf->MultiCell(2,$h,$v['tBankLoansDate'], 1, 'C', 0, 0);
		$pdf->MultiCell(2,$h,$v['tObjKind2Date'], 1, 'C', 0, 0);
		$pdf->MultiCell(2.5,$h,getZero($v['土地增值稅']), 1, 'C', 0, 0);
		$pdf->MultiCell(2,$h,getZero($v['房屋稅']), 1, 'C', 0, 0);
		$pdf->MultiCell(2,$h,getZero($v['地價稅']), 1, 'C', 0, 0);
		$pdf->MultiCell(2,$h,getZero($v['契稅']), 1, 'C', 0, 0);
		$pdf->MultiCell(2,$h,getZero($v['印花稅']), 1, 'C', 0, 0);
		$pdf->MultiCell(3,$h,getZero($v['買方預收款項']), 1, 'C', 0, 0);
		$pdf->MultiCell(2.5,$h,getZero($v['工程受益費']), 1, 'C', 0, 0);
		$pdf->MultiCell(2,$h,getZero($v['其他']), 1, 'C', 0, 0);
		$pdf->MultiCell(2,$h,getZero($v['totalMoney']), 1, 'C', 0, 1);

		$totalMoney += $v['totalMoney'];
	}
}
$pdf->MultiCell('','','總筆數：'.count($data), 0, 'L', 0, 1);
$pdf->MultiCell('','','總金額：'.$totalMoney, 0, 'L', 0, 1);
$pdf->MultiCell('',$h,'此至', 0, 'C', 0, 1);
$pdf->MultiCell('',$h,'台新國際商業銀行', 0, 'C', 0, 1);
$pdf->MultiCell('',$h,'委託人簽章', 0, 'C', 0, 1);

$y = $pdf->getY();

$img_file ='instructions/images/stamp.png';
$pdf->Image($img_file, 13, ($y+1),6,3.43);
// 													
// 													
// 		

$pdf->Output() ;
##
die;


function getTaxData($id){
	global $conn;
	$sql = "SELECT tId FROM tBankTrans WHERE tObjKind2Item = '".$id."'";
	$rs = $conn->Execute($sql);


	return $rs->fields['tId'];
}

function getZero($val){
	if ($val == '') {
		$val = 0;
	}

	return $val;
}


?>
