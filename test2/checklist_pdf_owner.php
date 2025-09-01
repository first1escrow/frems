<?php
$pdf->SetFontSize(6); 
$pdf->Text(185, 5, $detail['last_modify']) ;

$pdf->SetFontSize(14); 
$pdf->Cell(190,$cell_y1,'第一建築經理(股)公司',0,1,'C') ;					// 寫入文字
	
$pdf->SetFontSize(12) ;	
$title_txt = ($detail['bNote']!= 1)?'履保專戶收支明細表暨點交確認單(賣方)':'履約專戶收支明細表暨換約確認單(賣方)';
$pdf->Cell(190,$cell_y1,$title_txt,0,1,'C') ;

$pdf->Cell(190,$cell_y3,'',0,1) ;											// 手動換行

$pdf->SetFontSize(12) ;	
$pdf->Cell(190,$cell_y4,'案件基本資料',0,1) ;

//畫線(雙線條)
$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy) ;	

$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy) ;
##

$pdf->Cell(20,$cell_y1,'保證號碼：') ;										// 基本資料明細
// $pdf->Cell(82,$cell_y1,$detail['cCertifiedId']) ;
if ($cCertifiedId == '090020924') {
	$pdf->Cell(82,$cell_y1,$cCertifiedId.'(080146177)') ;
}else{
	$pdf->Cell(82,$cell_y1,$cCertifiedId) ;
}


if ($cCertifiedId  == '030119750') {
	$pdf->SetX(130) ;
}else{
	$pdf->SetX(120) ;
}

$pdf->Cell(28,$cell_y1,'特約地政士：') ;
$pdf->Cell(82,$cell_y1,$detail['cScrivener'],0,1) ;

if (strlen($detail['cBuyerId']) == 10) {
	$idNew = substr($detail['cBuyerId'],1,4).'****'.substr($detail['cBuyerId'],-1) ;
}
else {
	$idNew = substr($detail['cBuyerId'],1) ;
}
$pdf->Cell(20,$cell_y1,'買方姓名：') ;
$xx = ceil($pdf->GetStringWidth($detail['cBuyer'])) ;
$pdf->Cell($xx,$cell_y1,$detail['cBuyer']) ;
$pdf->Cell(5,$cell_y1,substr($detail['cBuyerId'],0,1),0,0,'R') ;
$addX = 1 ;
if (preg_match("/[0-9]/",substr($detail['cBuyerId'],0,1))) {
	$addX = 2 ;
}
$pdf->SetX($pdf->GetX() - $addX) ;
$xx = 82 - $xx - 5 ;
$pdf->Cell($xx,$cell_y1,$idNew,0,0,'L') ;

$yy = $pdf->getY();
if ($cCertifiedId  == '030119750') {
	$pdf->SetX(130) ;
}else{
	$pdf->SetX(120) ;
}
$pdf->Cell(28,$cell_y1,'仲介店名：') ;

if (!$detail['cMoreStore']) {
	$pdf->Cell(52,$cell_y1,$detail['cBrand'],0,1) ;
}else{
	$detail['cMoreStore'] = str_replace('(待停用)', '', $detail['cMoreStore']);
	$cMoreStore = explode(',', $detail['cMoreStore']);
	$pdf->MultiCell(52,$cell_y1,$cMoreStore[0],0,1) ;
	$yy2 = $pdf->getY();
}


if (strlen($detail['cOwnerId']) == 10) {
	$idNew = substr($detail['cOwnerId'],1,4).'****'.substr($detail['cOwnerId'],-1) ;
}
else {
	$idNew = substr($detail['cOwnerId'],1) ;
}

if ($detail['cMoreStore']) {
	// $pdf->setY($yy+$cell_y1);
	// $yy = $pdf->getY();
	$yy = $pdf->setY($yy+$cell_y1);
}

$pdf->Cell(20,$cell_y1,'賣方姓名：') ;
$xx = ceil($pdf->GetStringWidth($detail['cOwner'])) ;
$pdf->Cell($xx,$cell_y1,$detail['cOwner']) ;
$pdf->Cell(5,$cell_y1,substr($detail['cOwnerId'],0,1),0,0,'R') ;
$addX = 1 ;
if (preg_match("/[0-9]/",substr($detail['cOwnerId'],0,1))) {
	$addX = 2 ;
}
$pdf->SetX($pdf->GetX() - $addX) ;
$xx = 82 - $xx - 5 ;
$pdf->Cell($xx,$cell_y1,$idNew,0,0,'L') ;


if ($detail['cMoreStore']) {
	// $pdf->setY($yy2);
	$yy = $pdf->getY();
	$pdf->setY($yy2);


}

if ($cCertifiedId  == '030119750') {
	$pdf->SetX(130) ;
}else{
	$pdf->SetX(120) ;
}
$pdf->Cell(28,$cell_y1,'') ;

if (!$detail['cMoreStore']) {
	$detail['cStore'] = str_replace('(待停用)', '', $detail['cStore']);
	$pdf->Cell(52,$cell_y1,$detail['cStore'],0,1) ;
}else{
	
	$pdf->MultiCell(52,$cell_y1,$cMoreStore[1],0,1) ;
	$yy2 = $pdf->getY();

	// $yy = $pdf->setY($yy+$cell_y1);
	
}

if ($detail['cMoreStore']) {
	
	$pdf->setY($yy2+$cell_y1);
}

$pdf->Cell(28,$cell_y1,'買賣總金額：') ;
$tt ="$".@number_format($detail['cTotalMoney'])."元";
if ($detail['cTotalMoneyNote']) {
	$tt .="(".$detail['cTotalMoneyNote'].")";
}

$pdf->Cell(82,$cell_y1,$tt) ;
unset($tt);
if ($detail['cMoreStore'] && $cMoreStore[2]) {
	$pdf->setY($yy2);
	$pdf->SetX(120) ;
	
	$pdf->Cell(28,$cell_y1,'') ;
	$pdf->MultiCell(52,$cell_y1,$cMoreStore[2],0,1) ;
	// $yy2 = $pdf->getY();
	// $pdf->setY($yy2);
}

// $pdf->SetX(120) ;
// $pdf->Cell(28,$cell_y1,'代償金額：') ;
// $pdf->Cell(52,$cell_y1,"$".@number_format($detail['cCompensation'])."元",0,1) ;
if ($detail['cCompensation2'] > 0) {
	if ($cCertifiedId  == '030119750') {
		$pdf->SetX(130) ;
	}else{
		$pdf->SetX(120) ;
	}
	$pdf->Cell(28,$cell_y1,'專戶代償金額：') ;
	$pdf->Cell(52,$cell_y1,"$".@number_format($detail['cCompensation2'])."元",0,1) ;
}elseif ($detail['cCompensation3'] > 0 && $detail['cCompensation2'] <= 0) {

	// if ($detail['cMoreStore']) {
	// 	$pdf->setY($yy2);
	// }
	
	$pdf->SetX(120) ;

	$pdf->Cell(25,$cell_y1,'買方銀行代償：'."$".@number_format($detail['cCompensation3'])."元",0,1) ;

	// $pdf->Cell(55,$cell_y1,,0,1) ;

}

if ($detail['cNotIntoMoney'] > 0) {
	$pdf->Cell(28,$cell_y1,'未入專戶：') ;
	$pdf->Cell(82,$cell_y1,"$".@number_format($detail['cNotIntoMoney'])."元") ;
}else{
	$pdf->Cell(28,$cell_y1,'') ;
	$pdf->Cell(82,$cell_y1,'') ;
}


if ($detail['cCompensation2'] > 0 && $detail['cCompensation3'] > 0) {
	$pdf->setY($pdf->getY());
	$pdf->SetX(120) ;
	$pdf->Cell(28,$cell_y1,'買方銀行代償：') ;

	$pdf->Cell(54,$cell_y1,"$".@number_format($detail['cCompensation3'])."元",0,1) ;

	if ($detail['cCompensation4']  == 0) {
		$detail['cCompensation4'] = $detail['cCompensation2']+$detail['cCompensation3'];
	}
	$pdf->SetX(120) ;
	$pdf->Cell(28,$cell_y1,'代償總金額：') ;
	$pdf->Cell(52,$cell_y1,"$".@number_format($detail['cCompensation4'])."元",0,1) ;
}else{
	$pdf->SetX(120) ;
	$pdf->Cell(40,$cell_y1,'') ;

	$pdf->Cell(82,$cell_y1,"",0,1) ;
}
//建物
// if ($detail['cCompensation3'] > 0 && $detail['cCompensation2'] <= 0 ) {
// 	if ($detail['cMoreStore']) {
// 		$pdf->setY($yy2);
// 	}
	
// }
//建物
for ($i = 0 ; $i < $property_max ; $i ++) {
	$addr = n_to_w($property[$i]['city'].$property[$i]['area'].$property[$i]['cAddr']);
	// $property[$i]['cAddr'] = $property[$i]['city'].$property[$i]['area'].$property[$i]['cAddr'];
	// $property[$i]['cAddr'] = n_to_w($property[$i]['cAddr']) ;
	$pdf->Cell(28,$cell_y1,'買賣標的物：') ;
	$pdf->MultiCell(162,$cell_y1,$addr,0,1) ;
}
##


unset($tmp);

$pdf->Cell(190,$cell_gap,'',0,1) ;											// 手動換行

$pdf->SetFontSize(12) ;	
$pdf->Cell(190,$cell_y4,'買賣價金收支明細',0,1) ;

//畫線(雙線條)
$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy) ;	

$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy) ;
##

$pdf->SetFontSize(12) ;	
$pdf->Cell(23.75,$cell_y4,'日期',0,0,'C') ;									// 收支明細 title
$pdf->Cell(45,$cell_y4,'摘要',0,0,'C') ;
$pdf->Cell(23.75,$cell_y4,'收入/支出',0,0,'C') ;
$pdf->Cell(23.75,$cell_y4,'金額',0,0,'R') ;
$pdf->Cell(36.25,$cell_y4,'小計',0,0,'R') ;
$pdf->Cell(37.5,$cell_y4,'備註',0,1,'C') ;

//畫線(雙線條)
$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy) ;	

$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy) ;
##

// 賣方收支明細
$pdf->SetFontSize(12) ;	
$pdf->Cell(190,$cell_y4,'【專戶收款】',0,1) ;
$pdf->SetFontSize(12) ;

$x_pos = $pdf->GetX() ;
$y_pos = $pdf->GetY() ;
$income = 0 ;
for ($i = 0 ; $i < $max_owner ; $i ++) {
	$income += $trans_owner[$i]['oIncome'] ;
	
	$trans_owner[$i]['oRemark'] = n_to_w($trans_owner[$i]['oRemark']) ;
	$trans_owner[$i]['oRemark'] = preg_replace("/^＋/","含",$trans_owner[$i]['oRemark']) ;
	
	$pdf->Cell(23.75,$cell_y1,$trans_owner[$i]['oDate']) ;
	$pdf->Cell(45,$cell_y1,$trans_owner[$i]['oKind']) ;
	$pdf->Cell(23.75,$cell_y1,'收入',0,0,'C') ;
	$pdf->Cell(23.75,$cell_y1,@number_format($trans_owner[$i]['oIncome']),0,0,'R') ;
	$x_pos = $pdf->GetX() ;
	$y_pos = $pdf->GetY() ;
	$pdf->Cell(36.25,$cell_y1,'',0,0,'R') ;
	$pdf->SetX(165) ;
	$pdf->SetFontSize(9) ;	
	$pdf->MultiCell(37.5,$cell_y1,$trans_owner[$i]['oRemark'],0,1) ;
	$pdf->SetFontSize(12) ;	
}
$income += $detail['cInterest'] ;

if ($detail['cInterestHidden'] == 0) {
	$pdf->Cell(23.75,$cell_y1,'') ;
	$pdf->Cell(45,$cell_y1,'利息') ;
	$pdf->Cell(23.75,$cell_y1,'收入',0,0,'C') ;
	$pdf->Cell(23.75,$cell_y1,@number_format($detail['cInterest']),0,0,'R') ;

	$pdf->Cell(36.25,$cell_y1,@number_format($income),0,0,'R') ;
	$pdf->SetX(165) ;
	$pdf->Cell(37.5,$cell_y1,'',0,1) ;
}else{
	if ($max_owner > 0) {
		$pdf->SetXY($x_pos,$y_pos) ;
		$pdf->Cell(36.25,$cell_y1,@number_format($income),0,1,'R') ;
		// $pdf->Cell(190,$cell_gap,'',0,1) ;	
		// $pdf->Ln() ;
	}
	// $pdf->Cell(23.75,$cell_y1,'') ;
	// $pdf->Cell(45,$cell_y1,'') ;
	// $pdf->Cell(23.75,$cell_y1,'收入',0,0,'C') ;
	// $pdf->Cell(23.75,$cell_y1,'',0,0,'R') ;
	// $pdf->Cell(36.25,$cell_y1,@number_format($income),0,0,'R') ;
	// $pdf->SetX(165) ;
	// $pdf->Cell(37.5,$cell_y1,'',0,1) ;
}


if ($max_owner > 0) {
	$pdf->Cell(190,$cell_gap,'',0,1) ;											// 手動換行
}

$pdf->SetFontSize(12) ;
$pdf->Cell(190,$cell_y4,'【專戶出款】',0,1) ;

$pdf->SetFontSize(12) ;
$x_pos = $pdf->GetX() ;
$y_pos = $pdf->GetY() ;
			
$outgoing = 0 ;
for ($i = 0 ; $i < $max_owner_e ; $i ++) {
	$outgoing += $trans_owner_e[$i]['oExpense'] ;
	
	$trans_owner_e[$i]['oRemark'] = n_to_w($trans_owner_e[$i]['oRemark']) ;
	$trans_owner_e[$i]['oRemark'] = preg_replace("/^＋/","含",$trans_owner_e[$i]['oRemark']) ;

	$pdf->Cell(23.75,$cell_y1,$trans_owner_e[$i]['oDate']) ;
	$pdf->Cell(45,$cell_y1,$trans_owner_e[$i]['oKind']) ;
	$pdf->Cell(23.75,$cell_y1,'支出',0,0,'C') ;
	$pdf->Cell(23.75,$cell_y1,@number_format($trans_owner_e[$i]['oExpense']),0,0,'R') ;
	$x_pos = $pdf->GetX() ;
	$y_pos = $pdf->GetY() ;
	$pdf->Cell(36.25,$cell_y1,'',0,0,'R') ;
	$pdf->SetX(165) ;
	$pdf->SetFontSize(9) ;	
	$pdf->MultiCell(37.5,$cell_y1,$trans_owner_e[$i]['oRemark'],0,1) ;
	$pdf->SetFontSize(12) ;	
}



if ($max_owner_e > 0) {
	$pdf->SetXY($x_pos,$y_pos) ;
	$pdf->Cell(36.25,$cell_y1,@number_format($outgoing),0,1,'R') ;
	
	// $pdf->Ln() ;
}

$y1 = $pdf->getY()+$cell_y1;

// $pdf->Cell(190,$cell_gap,'',0,1) ;											// 手動換行

// $pdf->Cell(116.25,$cell_y1,'【專戶餘額】',0,1) ;

// $pdf->Cell(116.25,$cell_y1,'') ;
// $pdf->Cell(36.25,$cell_y1,@number_format($income-$outgoing),0,0,'R') ;
// $pdf->SetX(165) ;
// $pdf->SetFontSize(9) ;	
// $pdf->Cell(37.5,$cell_y1,'(專戶收款-專戶出款)',0,1) ;
// $pdf->SetFontSize(12) ;	

// $pdf->Cell(190,$cell_gap,'',0,1) ;											// 手動換行
// $pdf->SetX(165) ;
$pdf->Cell(190,0.5,'',0,1) ;	
$y = $pdf->getY();
// $pdf->Ln() ;$pdf->Ln() ;$pdf->Ln() ;$pdf->Ln() ;
if ($y1>= $y) {
	// $pdf->Cell(190,($cell_y4+$cell_y4),'',0,1) ;
	// $pdf->Cell(0,$cell_gap,'',1,1) ;
}
$pdf->SetFontSize(12) ;
$pdf->Cell(190,$cell_y4,'待扣款項明細',0,1) ;

//畫線(雙線條)
$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy) ;	

$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy) ;
##

$pdf->SetFontSize(12) ;	
$pdf->Cell(47.5,$cell_y4,'摘要') ;										// 結清付款項 Title
$pdf->Cell(30,$cell_y4,'金額',0,0,'R') ;
$pdf->Cell(17.5,$cell_y4,'') ;
$pdf->Cell(95,$cell_y4,'備註',0,1) ;

//畫線(雙線條)
$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy) ;	

$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy) ;
##

// $pdf->Cell(47.5,$cell_y2,'*專戶餘額') ;										// 結清付款項明細
// $pdf->Cell(30,$cell_y2,@number_format($income-$outgoing),0,0,'R') ;	

// $pdf->SetX(105) ;
// $pdf->Cell(95,$cell_y2,$detail['balance_remark'],0,1) ;

if ($detail['cRealestateBalanceHide'] == 0) {
	$pdf->Cell(47.5,$cell_y2,'*應付仲介服務費餘額') ;
	$pdf->Cell(30,$cell_y2,@number_format($detail['cRealestateBalance']),0,0,'R') ;	

	$pdf->SetX(105) ;
	// $pdf->SetFontSize(10) ;
	// $pdf->Cell(95,$cell_y2,$detail['realty_remark'],0,1) ;
	$pdf->MultiCell(95,$cell_y2,$detail['realty_remark'],0,1) ;
}



$pdf->SetFontSize(12) ;
$pdf->Cell(47.5,$cell_y2,'*賣方應付履約保證費') ;
$pdf->Cell(30,$cell_y2,@number_format($detail['cCertifiedMoney']),0,0,'R') ;	

$pdf->SetX(105) ;
$pdf->Cell(95,$cell_y2,$detail['certify_remark'],0,1) ;
##
if ($detail['cCertifiedMoney2'] > 0) {
	// cCertifiedMoney2
	$pdf->Cell(47.5,$cell_y2,'*代扣買方履約保證費') ;
	$pdf->Cell(30,$cell_y2,@number_format($detail['cCertifiedMoney2']),0,0,'R') ;	

	$pdf->SetX(105) ;
	$pdf->Cell(95,$cell_y2,$detail['certify_remark2'],0,1) ;
}

##
$pdf->Cell(47.5,$cell_y2,'*應付代書費用及代支費') ;
$pdf->Cell(30,$cell_y2,@number_format($detail['cScrivenerMoney']),0,0,'R') ;	

$pdf->SetX(105) ;
$pdf->MultiCell(95,$cell_y2,$detail['scrivener_remark'],0,1) ;

// $pdf->Cell(95,$cell_y2,$detail['scrivener_remark'],0,1) ;

//代扣補充保費
if ($detail['cNHITax'] > 0) {
	$pdf->Cell(47.5,$cell_y2,'*代扣健保補充保費') ;
	$pdf->Cell(30,$cell_y2,@number_format(round($detail['cNHITax'])),0,0,'R') ;
	
	$pdf->SetX(105) ;
	$pdf->Cell(95,$cell_y2,'代賣方扣繳 2.11% 補充保費',0,1) ;
}
##

//代扣所得稅
if ($detail['cTax'] > 0) {
	// $pdf->Cell(47.5,$cell_y2,'*'.$detail['cTaxTitle']) ; 
	$pdf->Cell(47.5,$cell_y2,'*代扣利息所得稅') ;
	$pdf->Cell(30,$cell_y2,@number_format(round($detail['cTax'])),0,0,'R') ;
	
	$pdf->SetX(105) ;
	// $pdf->Cell(95,$cell_y2,$detail['cTaxRemark'],0,1) ; 
	 // $detail['cBuyerId'] = 'GA12345679';
	$sql = "SELECT * FROM tContractOthers WHERE cCertifiedId = '".$cCertifiedId."' AND cIdentity='2'";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		if (preg_match("/[A-Za-z]{2}/",$rs->fields['cIdentifyId'])) { //
				$detail['cOwnerId'] = $rs->fields['cIdentifyId'];
		}

		$rs->MoveNext();
	}

	if (preg_match("/[A-Za-z]{2}/",$detail['cOwnerId'])) {					// 判別是否為外國人(兩碼英文字母者) 外國人20%		
		$pdf->Cell(95,$cell_y2,'代賣方扣繳20% 利息所得稅',0,1) ;
	}else{
		$pdf->Cell(95,$cell_y2,'代賣方扣繳10% 利息所得稅',0,1) ;
	}
}
##
##賣方待扣款項明細它項
$other = 0;

for ($i=0; $i < count($tax_owner) ; $i++) { 
	// $pdf->Cell(47.5,$cell_y2,'*'.$tax_owner[$i]['cTaxTitle']) ;
	// $pdf->Cell(30,$cell_y2,@number_format(round($tax_owner[$i]['cTax'])),0,0,'R') ;
	
	// $pdf->SetX(105) ;
	// $pdf->Cell(95,$cell_y2,$tax_owner[$i]['cTaxRemark'],0,1) ;

	$y = $pdf->gety();
		$x = $pdf->getx();

		$pdf->MultiCell(47.5,$cell_y2,'*'.$tax_owner[$i]['cTaxTitle'],0) ;
		
		$pdf->setxy($x+47.5,$y);
		$pdf->Cell(30,$cell_y2,@number_format(round($tax_owner[$i]['cTax'])),0,0,'R') ;
			
		$pdf->setxy(105,$y);
		$pdf->MultiCell(95,$cell_y2,$tax_owner[$i]['cTaxRemark'],0,1) ;

	$other = $other+$tax_owner[$i]['cTax']; //其他款項加總.
}
##
$pdf->Cell(190,$cell_gap,'',0,1) ;	
$pdf->Cell(190,$cell_gap,'',0,1) ;	
$pdf->Cell(47.5,$cell_y2,'*賣方實收金額') ;

$_got_money = $income - $outgoing - $detail['cRealestateBalance'] - $detail['cCertifiedMoney'] - $detail['cScrivenerMoney'] - $detail['cTax'] - $detail['cNHITax'] - $other-$detail['cCertifiedMoney2'];
$pdf->Cell(30,$cell_y2,@number_format($_got_money),0,0,'R') ;

$pdf->SetX(105) ;
$pdf->Cell(95,$cell_y2,'委由第一建經撥入下列指定帳戶',0,1) ;
if($detail['other_remark']) {
	// $pdf->Cell(190,$cell_y2,$detail['other_remark'],0,1) ;
	$pdf->MultiCell(190, $cell_y2, $detail['other_remark'], 0);
}

for ($i=0; $i < count($remark_owner); $i++) { 
		// $pdf->Cell(190,$cell_y2,$remark_owner[$i]['cRemark'],0,1) ;
	$pdf->MultiCell(190, $cell_y2, $remark_owner[$i]['cRemark'], 0);
	}

$pdf->Cell(190,$cell_gap,'',0,1) ;											// 手動換行

$pdf->SetFontSize(12) ;	
$pdf->Cell(190,$cell_y2,'指定收受價金之帳戶',0,1) ;

//畫線(雙線條)
$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy) ;	

$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy) ;
##

$pdf->SetFontSize(12) ;	
$pdf->Cell(18.75,$cell_y4,'對象') ;									// 指定帳戶 Title
$pdf->Cell(60,$cell_y4,'解匯行/分行') ;
$pdf->Cell(44.25,$cell_y4,'帳號') ;
$pdf->Cell(46.25,$cell_y4,'戶名') ;
$pdf->Cell(20.75,$cell_y4,'金額',0,1) ;
	
//畫線(單線條)
$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy) ;
##
	
//建立銀行帳號表格
$pdf->SetFontSize(12) ;	
$sql = '
	SELECT
		*,
		(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="" AND bOK = 0) as bankMain,
		(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as bankBranch
	FROM
		tChecklistBank AS a
	WHERE
		cCertifiedId="'.$detail['cCertifiedId'].'"
		AND cIdentity IN ("2","31","32","42","52")
		AND cHide = 0
	ORDER BY
			cOrder,cId
		ASC,
			cBankAccountNo
		DESC;
' ;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$tmp = $rs->fields;
	//確認身分顯示
	switch($tmp['cIdentity']) {
		case '2' :
				//if ($tmp['cBankAccountNo']) {
					$tmp['cIdentity'] = '賣方' ;
				//}
				//else {
				//	$tmp['cIdentity'] = '' ;
				//}
				break ;
		case '31' :
				//if ($tmp['cBankAccountNo']) {
					$tmp['cIdentity'] = '買方' ;
				//}
				//else {
				//	$tmp['cIdentity'] = '' ;
				//}
				break ;
		case '32' :
				//if ($tmp['cBankAccountNo']) {
					$tmp['cIdentity'] = '仲介' ;
				//}
				//else {
				//	$tmp['cIdentity'] = '' ;
				//}
				break ;
		case '42' :
				//if ($tmp['cBankAccountNo']) {
					$tmp['cIdentity'] = '地政士' ;
				//}
				//else {
				//	$tmp['cIdentity'] = '' ;
				//}
				break ;
		case '52' :
				$tmp['cIdentity'] = '' ;
				break ;
		default :
				$tmp['cIdentity'] = '' ;
				break ;
	}
	##
	
	//確認銀行顯示
	if ($tmp['bankMain'] && $tmp['bankBranch']) {
		$tmpArr = array() ;
		$tmpArr = explode('（',$tmp['bankBranch']) ;
		$tmp['bankMain'] = str_replace('（農金資中心所屬會員）','', $tmp['bankMain']);
		$tmp['bank'] = $tmp['bankMain'].'/'.$tmpArr[0] ;
	}
	##
	
	// 指定帳戶表格
	$pdf->Cell(18.75,$cell_y5,$tmp['cIdentity'],1) ;						// 對象
	
	
	// $pdf->Cell(60,$cell_y5,$tmp['bank'],1) ;								// 解匯行/分行
	// $tmp['bank'] = mb_strlen($tmp['bank']);//$tmp['bank']
	// $tmp['bank'] = $tmp['bank'].mb_substr($tmp['bank'], 52);

	if (mb_strlen($tmp['bank']) > 60) {
		$pdf->SetFontSize(7) ;
	}else{
		$pdf->SetFontSize(9) ;
	}

	$pdf->Cell(60,$cell_y5,$tmp['bank'],1) ;	
	
	$pdf->SetFontSize(12) ;
	$pdf->Cell(44.25,$cell_y5,$tmp['cBankAccountNo'],1) ;					// 帳號
		

	
	$strLen = mb_strlen($tmp['cBankAccountName']);

	if (mb_strlen($tmp['cBankAccountName']) > 27) {
		$pdf->SetFontSize(8) ;
	}else{
		$pdf->SetFontSize(12) ;
	}
			// echo $strLen;

			

	$pdf->Cell(46.25,$cell_y5,$tmp['cBankAccountName'],1) ;					// 戶名
	// $pdf->MultiCell(41.25,$cell_y5,$tmp['cBankAccountName'],1,1) ;

	$pdf->SetFontSize(12) ;
	if ($tmp['cMoney'] == 0) {
		$tmp['cMoney'] = '';
	}
	$pdf->Cell(20.75,$cell_y5,$tmp['cMoney'],1,1) ;										// 金額
	##
	
	unset($tmp) ;

	$rs->MoveNext();
}
##
// die;



$checkPage  = 0; //檢查是否超出一頁

//檢查是否超出一頁
if ($pdf->GetY() >= 270 && $checkPage == 0) {
	$pdf->AddPage();
	$checkPage++;
}


$itemNo = 1;
$pdf->Cell(190,$cell_gap,'',0,1) ;											// 手動換行

$pdf->SetFontSize(10) ;	
$pdf->Cell(190,$cell_y6,'應注意事項',0,1) ;


$pdf->Cell(5,$cell_y6,$itemNo.'.') ;
$pdf->Cell(185,$cell_y6,'此證明書將做為第一建築經理股份有限公司辦理專戶價金結算及撥付之依據',0,1) ;
$itemNo++;


//檢查是否超出一頁
if ($pdf->GetY() >= 270 && $checkPage == 0) {
	$pdf->AddPage();
	$checkPage++;
}

$pdf->Cell(5,$cell_y6,$itemNo.'.') ;
$pdf->MultiCell(185,$cell_y6,'年度給付利息所得將依法開立扣繳憑單,將依法開立扣繳憑單;該所得非「儲蓄投資特別扣除額」之27萬免扣繳範圍',0,1) ;
$itemNo++;


//檢查是否超出一頁
if ($pdf->GetY() >= 270 && $checkPage == 0) {
	$pdf->AddPage();
	$checkPage++;
}

$pdf->SetFontSize(10) ;
$pdf->Cell(5,$cell_y6,$itemNo.'.') ;
$pdf->Cell(45,$cell_y6,'本公司依財政部「電子發票實施作業要點」,電子發票於結案日後５日內開立完成,將不郵寄實體發票,請勾選:') ;

$y = $pdf->GetY() ;
$pdf->SetY($y+5);
$x = $pdf->GetX() ;
$pdf->SetX($x+5);
$pdf->Cell($x,$cell_y6,'□我不需索取紙本電子發票,由第一建經託管並兌獎,中獎後由第一建經主動通知我領獎事宜。') ;

$y = $pdf->GetY() ;
$pdf->SetY($y+5);
$x = $pdf->GetX() ;
$pdf->SetX($x+5);
$pdf->Cell($x,$cell_y6,'□捐贈「財團法人台灣兒童暨家庭扶助基金會」　我要索取紙本電子發票 □同戶籍地址□同買賣標的物地址') ;

$y = $pdf->GetY() ;
$pdf->SetY($y+5);
$pdf->SetX($x+5);
$pdf->Cell($x,$cell_y6,'□指定地址:_______縣（市）_________鄉（鎮、市、區）________________路（街）____段');
$y = $pdf->GetY() ;
$pdf->SetY($y+5);
$pdf->SetX($x+5);
$pdf->Cell($x,$cell_y6,'____巷_____弄____號____樓之 ___。');
// $x = $pdf->GetX()+12 ;
// $pdf->Line($x,($y+10), ($x+165), ($y+10));

// $x = $pdf->GetX()-9 ;
// $y = $pdf->GetY() ;
// $pdf->Line($x,($y+12), ($x+185), ($y+12));

$y = $pdf->GetY() ;
$pdf->SetY($y+8);
$x = $pdf->GetX() ;
$pdf->SetX($x+5);
$pdf->Cell($x,$cell_y6,'未勾選視為同意不索取紙本電子發票,台端簽名後即代表知悉上開通知內容,您可至本公司官網查詢發票內容。') ;
$itemNo++;

$pdf->Ln() ;
$pdf->Cell(190,$cell_y3,'',0,1) ;											// 手動換行


//檢查是否超出一頁
if ($pdf->GetY() >= 270 && $checkPage == 0) {
	$pdf->AddPage();
	$checkPage++;
}
$y = $pdf->GetY();

$pdf->Cell(5,$cell_y6,$itemNo.'.') ;
$pdf->Cell(185,$cell_y6,'點交手續完成及上述事項確認無誤後，請於下方簽章處簽名蓋章：',0,1) ;
$pdf->Cell(63,$cell_y6,'賣方簽章：') ;
$pdf->Cell(64,$cell_y6,'仲介方簽章：') ;
$pdf->Cell(63,$cell_y6,'地政士簽章：',0,1) ;
$itemNo++;
// if ($_SESSION['member_id'] == 6) {
// 	$pdf->Cell(63,$cell_y6,$pdf->GetY(),0,1) ;
// }

/*
for ($i = 0 ; $i < 6 ; $i ++) {
	$pdf->Cell(190,$cell_y6,'',0,1) ;
}
*/






/* 2014/11/01 for 美亞 */ //20150507時間已過直接隱藏
// if (($cSignDate >= '2014-11-01 00:00:00') && ($cSignDate <= '2015-04-30 23:59:59')) {
	
// 	$pdf->SetFont('','B',11) ;
// 	$pdf->Cell(5,$cell_y6,'5.') ;
// 	$pdf->Cell(185,$cell_y6,'※恭禧您獲得第一建經提供『個人居家綜合險』三個月保障，請填寫要保書郵寄地址：',0,1) ;
// 	$pdf->SetY($pdf->GetY() + 1) ;
// 	$pdf->SetX(19) ;

// 	$pdf->Cell(185,$cell_y6,'□與履保費發票郵寄地址相同　□',0,2) ;
// 	$pdf->Cell(50,$cell_y6,'') ;
// 	$pdf->Cell(20,$cell_y6,'',0,2) ;
// 	$pdf->Line(80,$pdf->GetY()-0.5,195,$pdf->GetY()-0.5) ;
// 	$pdf->Rect(15,251,185,15) ;

// 	$title_no++;
// }
/*20150505加入預售屋換約備註事項 */
if ($detail['cNote']==1) {
	$y=$pdf->GetY();

	if ($y <= 245) {
		
		$pdf->SetY($y+30);
	}else{
		$y=$pdf->GetY();
		$pdf->SetY($y+5);
	}
	
	$pdf->Cell(5,$cell_y6,$itemNo.'.') ;
	$pdf->Cell(200,$cell_y6,'※買賣雙方業於____年____月____日已向建設公司完成換約事宜，經買方確認無誤，請第一建築經理股份有限',0,1) ;
	
	$pdf->SetY($pdf->GetY() + 1) ;
	$pdf->SetX(19) ;
	$pdf->Cell(63,$cell_y6,'公司將履保專戶款項全數撥付至賣方指定帳戶。',0,1) ;
	$itemNo ++;
}

unset($itemNo);

// if ($cCertifiedId == '090402219') {
// 	$y=$pdf->GetY();
// }

$pdf->SetFontSize(10) ;
$pdf->Text(12,295,'中華民國 ________ 年 ________ 月 ________ 日　　聯絡電話：'.$company['tel'].' Ext.'.$undertaker['Ext'].'　　傳真電話：'.$undertaker['FaxNum']) ;
?>