<?php
require_once('../tcpdf/tcpdf.php');
include_once '../openadodb.php' ;
$cCertifiedId = $_REQUEST['cCertifiedId'] ;
//設定行高
$cell_y1 = 4.5 ;			// 內容用
$cell_y2 = 5 ;				// 標題用
$cell_y3 = 1 ;				// 手動跳行調行距用
$cell_y4 = 5 ;				// 內容用
$cell_y5 = 8 ;				// 銀行框框加大
$cell_y6 = 4 ;				// 注意事項用
$cell_gap = 1 ;				// 單元分隔用
$line_gap = 0.4 ;			// 雙線條畫線用
##

################
$company = json_decode(file_get_contents(dirname(dirname(__FILE__)).'/includes/company.json'),true) ;
##

// 取得買賣方資料
$sql = 'SELECT * FROM tChecklist WHERE cCertifiedId="'.$cCertifiedId.'";' ;
$rs = $conn->Execute($sql);
$detail = $rs->fields;

##

// 賣方收支明細(收入部分)##日期為空的要排最後面
$sql = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="'.$cCertifiedId.'" AND oIncome<>"0" AND oDate!="" ORDER BY oDate ASC; ;' ;
$rs = $conn->Execute($sql);
$max_owner=$rs->RecordCount();
while (!$rs->EOF) {
	# code...
	$trans_owner[] = $rs->fields ;
	$rs->MoveNext();
}

$sql = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="'.$cCertifiedId.'" AND oIncome<>"0" AND oDate="" ORDER BY oDate ASC;' ;
$rs = $conn->Execute($sql);
$owner_max2 = $rs->RecordCount();
while (!$rs->EOF) {
	# code...
	$trans_owner[$max_owner++] =$rs->fields ;
	$rs->MoveNext();
}
##

// 賣方收支明細(支出)
$sql = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="'.$cCertifiedId.'" AND oExpense<>"0" ORDER BY oDate ASC; ;' ;
$rs = $conn->Execute($sql);
$max_owner_e =  $rs->RecordCount();
while (!$rs->EOF) {
	$trans_owner_e[] = $rs->fields ;

	$rs->MoveNext();
}
##

//讀取買方交易明細(收入部分)##日期為空的要排最後面
$sql = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId="'.$cCertifiedId.'" AND bIncome<>"0" AND bDate!="" ORDER BY bDate ASC;' ;
$rs = $conn->Execute($sql);
$buyer_max = $rs->RecordCount();
while (!$rs->EOF) {
	$buyer_income[] = $rs->fields ;
	$rs->MoveNext();
}

$sql = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId="'.$cCertifiedId.'" AND bIncome<>"0" AND bDate="" ORDER BY bDate ASC;' ;
$rs = $conn->Execute($sql);
$buyer_max2 = $rs->RecordCount();
while (!$rs->EOF) {
	
	$buyer_income[$buyer_max++] = $rs->fields ;

	$rs->MoveNext();
}
##

//讀取買方交易明細(支出部分)
$sql = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId="'.$cCertifiedId.'" AND bExpense<>"0" ORDER BY bDate ASC;' ;
$rs = $conn->Execute($sql);
$buyer_max_e = $rs->RecordCount();
while (!$rs->EOF) {
	
	$buyer_expense[] = $rs->fields ;
	$rs->MoveNext();
}
##

// 讀取經辦人員資料
$sql = '
	SELECT 
		peo.pFaxNum as FaxNum,
		peo.pId as pId,
		peo.pExt as Ext	
	FROM  
		tBankCode AS bkc 
	JOIN 
		tScrivener AS scr ON scr.sId=bkc.bSID
	JOIN 
		tPeopleInfo AS peo ON scr.sUndertaker1=peo.pId
	WHERE 
		bkc.bAccount LIKE "%'.$cCertifiedId.'"
' ;
$rs = $conn->Execute($sql);
$undertaker = $rs->fields;
if ($undertaker['FaxNum']) {
	$temp = $undertaker['FaxNum'] ;
	$undertaker['FaxNum'] = substr($temp,0,7).'-'.substr($temp,7) ;
	unset($temp) ;
}
##

//確認簽約日期
$cSignDate = '' ;
$sql = "SELECT cSignDate FROM tContractCase WHERE cCertifiedId='".$cCertifiedId."';" ;
$rs = $conn->Execute($sql);

$cSignDate = $rs->fields['cSignDate'] ;
##

//賣方結清撥付款項明細-其他
$sql="SELECT * FROM tChecklistOther WHERE cCertifiedId='".$cCertifiedId."' AND cIdentity = 2";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$tax_owner[] = $rs->fields;

	$rs->MoveNext();
}
##
//買方結清撥付款項明細-其他
$sql="SELECT * FROM tChecklistOther WHERE cCertifiedId='".$cCertifiedId."' AND cIdentity = 1";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	$tax_buyer[] = $rs->fields;

	$rs->MoveNext();
}
##
//結清撥付款項明細-其他-2
$sql = "SELECT * FROM tChecklistRemark WHERE cCertifiedId='".$cCertifiedId."' ORDER BY cId ASC" ;

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	if ($rs->fields['cIdentity']==1) {

		$remark_buy[] = $rs->fields;

	}elseif ($rs->fields['cIdentity']==2) {
		$remark_owner[] = $rs->fields;
	}

	$rs->MoveNext();
}


##
//建物

$sql="SELECT cAddr,(SELECT zCity FROM tZipArea WHERE zZip = cZip) AS city , (SELECT zArea FROM tZipArea WHERE zZip = cZip) AS area FROM tContractProperty WHERE cCertifiedId ='".$cCertifiedId."' ORDER BY cItem";
$rs = $conn->Execute($sql);
$property_max = $rs->RecordCount();
while (!$rs->EOF) {
	$property[] = $rs->fields ;

	$rs->MoveNext();
}
##
$style = array('width' => 0.01, 'cap' => 'butt', 'color' => array(0, 0, 0));
##
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetMargins(10,3,10,0);
$pdf->SetCreator(PDF_CREATOR);	
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->AddPage();

$pdf->SetFont('msungstdlight', 'B', 6);
// $pdf->Text(185, 5, $detail['last_modify']) ;
$pdf->Cell(185,5,$detail['last_modify'],0,1,'R') ;					// 寫入文字
// Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')

$pdf->SetFont('msungstdlight', 'B', 14);
$pdf->Cell(190,$cell_y1,'第一建築經理(股)公司',0,1,'C') ;					// 寫入文字
	
$pdf->SetFont('msungstdlight', 'B', 8);													// 設定字體大小
$pdf->Cell(190,$cell_y1,'履保專戶收支明細表暨點交確認單(買方)',0,1,'C') ;

$pdf->Ln($cell_gap);											// 手動換行

$pdf->SetFont('msungstdlight', 'B', 12);
$pdf->Cell(190,$cell_y2,'案件基本資料',0,1) ;
//畫線(雙線條)


$pdf->SetFont('msungstdlight', 'B', 12);
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;

$pdf->Line(10,$xy,200,$xy,$style) ;	

$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy,$style) ;	

//基本資料明細
$pdf->Cell(28,$cell_y1,'保證號碼：') ;
$pdf->Cell(82,$cell_y1,$cCertifiedId) ;


if ($cCertifiedId  == '030119750') {
	$pdf->SetX(130) ;
}else{
	$pdf->SetX(120) ;
}
$pdf->Cell(28,$cell_y1,'特約地政士：') ;
$pdf->Cell(82,$cell_y1,$detail['bScrivener'],0,1) ;

if (strlen($detail['bBuyerId']) == 10) {
	$idNew = substr($detail['bBuyerId'],1,4).'****'.substr($detail['bBuyerId'],-1) ;
}
else {
	$idNew = substr($detail['bBuyerId'],1) ;
}

$pdf->Cell(28,$cell_y1,'買方姓名：') ;
$xx = ceil($pdf->GetStringWidth($detail['bBuyer'])) ;
$pdf->Cell($xx,$cell_y1,$detail['bBuyer']) ;
$pdf->Cell(8,$cell_y1,substr($detail['bBuyerId'],0,1),0,0,'R') ;
$addX = 1 ;
if (preg_match("/[0-9]/",substr($detail['bBuyerId'],0,1))) {
	$addX = 2 ;
}
$pdf->SetX($pdf->GetX() - $addX) ;
$xx = 82 - $xx - 8 ;
$pdf->Cell($xx,$cell_y1,$idNew,0,0,'L') ;

$yy = $pdf->getY();

if ($cCertifiedId  == '030119750') {
	$pdf->SetX(130) ;
}else{
	$pdf->SetX(120) ;
}

$pdf->Cell(28,$cell_y1,'仲介店名：') ;

if (!$detail['bMoreStore']) {
	$pdf->Cell(52,$cell_y1,$detail['bBrand'],0,1) ;
}else{
	
	$bMoreStore = explode(',', $detail['bMoreStore']);
	$pdf->MultiCell(52,$cell_y1,$bMoreStore[0],0,'L') ;
	// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
	$yy2 = $pdf->getY();
}

if (strlen($detail['bOwnerId']) == 10) {
	$idNew = substr($detail['bOwnerId'],1,4).'****'.substr($detail['bOwnerId'],-1) ;
}
else {
	$idNew = substr($detail['bOwnerId'],1) ;
}

if ($detail['bMoreStore']) {
	$yy = $pdf->setY($yy+$cell_y1);
}

$pdf->Cell(28,$cell_y1,'賣方姓名：') ;
$xx = ceil($pdf->GetStringWidth($detail['bOwner'])) ;
$pdf->Cell($xx,$cell_y1,$detail['bOwner']) ;
$pdf->Cell(8,$cell_y1,substr($detail['bOwnerId'],0,1),0,0,'R') ;
$addX = 1 ;
if (preg_match("/[0-9]/",substr($detail['bOwnerId'],0,1))) {
	$addX = 2 ;
}
$pdf->SetX($pdf->GetX() - $addX) ;
$xx = 82 - $xx - 8 ;
$pdf->Cell($xx,$cell_y1,$idNew,0,0,'L') ;

if ($detail['bMoreStore']) {
	$yy = $pdf->getY();
	$pdf->setY($yy2);
}

if ($cCertifiedId  == '030119750') {
	$pdf->SetX(130) ;
}else{
	$pdf->SetX(120) ;
}
$pdf->Cell(28,$cell_y1,'') ;

if (!$detail['bMoreStore']) {
	$pdf->Cell(52,$cell_y1,$detail['bStore'],0,1) ;
}else{
	$pdf->MultiCell(52,$cell_y1,$bMoreStore[1],0,'L') ;
	$yy2 = $pdf->getY();
}	

if ($detail['bMoreStore']) {
	
	$pdf->setY($yy+$cell_y1);
}

$pdf->Cell(28,$cell_y1,'買賣總金額：') ;

$tt = "$".@number_format($detail['bTotalMoney'])."元";
if ($detail['bTotalMoneyNote']) {
	$tt .= "(".$detail['bTotalMoneyNote'].")";
}

$pdf->Cell(82,$cell_y1,$tt) ;
unset($tt);
if ($detail['bMoreStore'] && $bMoreStore[2]) {

	$pdf->setY($yy2);

	$pdf->SetX(120) ;
	$pdf->Cell(28,$cell_y1,'') ;
	$pdf->MultiCell(52,$cell_y1,$bMoreStore[2],0,'L') ;
}

if ($detail['bCompensation2'] > 0) {
	if ($cCertifiedId  == '030119750') {
		$pdf->SetX(130) ;
	}else{
		$pdf->SetX(120) ;
	}
	$pdf->Cell(28,$cell_y1,'專戶代償金額：') ;
	$pdf->Cell(52,$cell_y1,"$".@number_format($detail['bCompensation2'])."元",0,1) ;
}elseif ($detail['bCompensation3'] > 0 && $detail['bCompensation2'] <= 0) {
	$pdf->SetX(120) ;
	$pdf->Cell(28,$cell_y1,'買方銀行代償：') ;

	$pdf->Cell(52,$cell_y1,"$".@number_format($detail['bCompensation3'])."元",0,1) ;
}

if ($detail['bNotIntoMoney'] > 0) {
	$pdf->Cell(28,$cell_y1,'未入專戶：') ;
	$pdf->Cell(82,$cell_y1,"$".@number_format($detail['bNotIntoMoney'])."元") ;
}else{
	$pdf->Cell(28,$cell_y1,'') ;
	$pdf->Cell(82,$cell_y1,"") ;
}
if ($detail['bCompensation2'] > 0 && $detail['bCompensation3'] > 0) {
	$pdf->SetX(120) ;
	$pdf->Cell(28,$cell_y1,'買方銀行代償：') ;

	$pdf->Cell(52,$cell_y1,"$".@number_format($detail['bCompensation3'])."元",0,1) ;

	if ($detail['bCompensation4'] == 0) {
		# code...
		$detail['bCompensation4'] = $detail['bCompensation2']+$detail['bCompensation3'];
	}
	$pdf->SetX(120) ;
	$pdf->Cell(28,$cell_y1,'代償總金額：') ;
	$pdf->Cell(52,$cell_y1,"$".@number_format($detail['bCompensation4'])."元",0,1) ;
}else{
	$pdf->SetX(120) ;
	$pdf->Cell(40,$cell_y1,'') ;

	$pdf->Cell(82,$cell_y1,"",0,1) ;
}
//建物
for ($i = 0 ; $i < $property_max ; $i ++) {
	
	$property[$i]['cAddr'] = $property[$i]['city'].$property[$i]['area'].$property[$i]['cAddr'];
	$property[$i]['cAddr'] = n_to_w($property[$i]['cAddr']) ;
	$pdf->Cell(28,$cell_y1,'買賣標的物：') ;
	$pdf->MultiCell(162,$cell_y1,$property[$i]['cAddr'],0,'L') ;
}
##
// $pdf->Cell(190,'','',0,1) ;		
// $pdf->MultiCell(162,$cell_gap,'',0,'L') ;									// 手動換行
// Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
$pdf->Ln($cell_gap);
$pdf->Cell(190,$cell_y4,'買賣價金收支明細',0,1) ;

##

//畫線(雙線條)
// $pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy,$style) ;	

$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy,$style) ;
##
//收入明細 title
$pdf->Cell(23.75,$cell_y4,'日期',0,0,'C') ;
$pdf->Cell(35,$cell_y4,'摘要',0,0,'C') ;
$pdf->Cell(23.75,$cell_y4,'收入金額',0,0,'R') ;
$pdf->Cell(23.75,$cell_y4,'支出金額',0,0,'R') ;
$pdf->Cell(33.75,$cell_y4,'小計',0,0,'R') ;
$pdf->Cell(50,$cell_y4,'備註',0,1,'C') ;
##

//畫線(雙線條)
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy,$border) ;	

$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy,$border) ;

$pdf->Cell(190,$cell_y4,'【專戶收款】',0,1) ;
// 買方收入明細
$total = 0 ;
for ($i = 0 ; $i < $buyer_max ; $i ++) {
	$pdf->SetFont('msungstdlight', 'B', 12);	
	$total += $buyer_income[$i]['bIncome'] ;
	$showIncome = '' ;
	
	if ($i == ($buyer_max - 1)) {
		$showIncome = @number_format($total) ;
	}
	$buyer_income[$i]['bRemark'] = n_to_w($buyer_income[$i]['bRemark']) ;
	$buyer_income[$i]['bRemark'] = preg_replace("/^＋/","含",$buyer_income[$i]['bRemark']) ;
	
	$pdf->Cell(23.75,$cell_y1,$buyer_income[$i]['bDate']) ;		//,'RB'
	$pdf->Cell(35,$cell_y1,$buyer_income[$i]['bKind']) ;			//,'RB'
	$pdf->Cell(23.75,$cell_y1,@number_format($buyer_income[$i]['bIncome']),0,0,'R') ;	//RB
	$pdf->Cell(23.75,$cell_y1,@number_format($buyer_income[$i]['bExpense']),0,0,'R') ;	//RB
	$pdf->Cell(33.75,$cell_y1,$showIncome,0,0,'R') ;	//RB
	$pdf->SetX(160) ;
	$pdf->SetFont('msungstdlight', 'B', 9);		
	$pdf->MultiCell(50,$cell_y1,$buyer_income[$i]['bRemark'],0,'L') ;//B		
	
}
$pdf->SetFont('msungstdlight', 'B', 12);

$pdf->Ln($cell_gap);											// 手動換行

if ($buyer_max_e > 0) {
	$pdf->Cell(190,$cell_y4,'【專戶出款】',0,1) ;
}

// 買方支出明細
for ($i = 0 ; $i < $buyer_max_e ; $i ++) {
	$pdf->SetFont('msungstdlight', 'B', 12);	
	$total -= $buyer_expense[$i]['bExpense'] ;
	$expense += $buyer_expense[$i]['bExpense'] ;
	
	$showExpense = '' ;
	if ($i == ($buyer_max_e - 1)) {
		$showExpense = @number_format($expense) ;
	}
	
	$pdf->Cell(23.75,$cell_y1,$buyer_expense[$i]['bDate']) ;		//,'RB'
	$pdf->Cell(35,$cell_y1,$buyer_expense[$i]['bKind']) ;		//,'RB'
	$pdf->Cell(23.75,$cell_y1,@number_format($buyer_expense[$i]['bIncome']),0,0,'R') ;	//RB
	$pdf->Cell(23.75,$cell_y1,@number_format($buyer_expense[$i]['bExpense']),0,0,'R') ;	//RB
	$pdf->Cell(33.75,$cell_y1,$showExpense,0,0,'R') ;	//RB
	$pdf->SetX(160) ;
	$pdf->SetFont('msungstdlight', 'B', 9);		
	$pdf->MultiCell(50,$cell_y1,$buyer_expense[$i]['bRemark'],0,'L') ;	//B
			
}
$pdf->SetFont('msungstdlight', 'B', 12);

$count = 0 ;
$check = 0;
if ($detail['bRealestateBalance'] > 0) {	//買方應付仲介費餘額
	$count ++ ;
}
if ($detail['bCertifiedMoney'] > 0) {		//買方履保費
	$count ++ ;
	$check = 1;
}
if ($detail['bScrivenerMoney'] > 0) {		//買方代書費
	$count ++ ;
}
if ($detail['bNHITax'] > 0) {				//代扣補充保費
	$count ++ ;
}
if ($detail['bTax'] > 0) {					//代扣所得稅
	$count ++ ;
}

if (count($tax_buyer)>0) { //其它代扣
	$count ++ ;
}

//若代扣款明細有值則顯示下列帳戶資料
if ($count > 0) {
	$pdf->Ln($cell_gap);											// 手動換行
	
	$pdf->Cell(190,$cell_y4,'待扣款項明細',0,1) ;

	//畫線(雙線條)
	$xy = $pdf->GetY() ;
	$xy += ($line_gap / 2) ;
	$pdf->Line(10,$xy,200,$xy,$border) ;	
	
	$xy = $pdf->GetY() ;
	$xy -= $line_gap ;
	$pdf->Line(10,$xy,200,$xy,$border) ;
	##
	
	//結清付款項 Title
	$pdf->Cell(47.5,$cell_y4,'摘要') ;
	$pdf->Cell(30,$cell_y4,'金額',0,0,'R') ;
	$pdf->Cell(17.5,$cell_y4,'') ;
	
	$pdf->SetX(105) ;
	$pdf->Cell(95,$cell_y4,'備註',0,1) ;
	##
	
	//畫線(雙線條)
	$xy = $pdf->GetY() ;
	$xy += ($line_gap / 2) ;
	$pdf->Line(10,$xy,200,$xy,$border) ;	
	
	$xy = $pdf->GetY() ;
	$xy -= $line_gap ;
	$pdf->Line(10,$xy,200,$xy,$border) ;
	##
	
	//買方應付仲介費餘額
	if ($detail['bRealestateBalance'] > 0) {
		$pdf->Cell(47.5,$cell_y2,'*應付仲介服務費餘額') ;
		$pdf->Cell(30,$cell_y2,@number_format(round($detail['bRealestateBalance'])),0,0,'R') ;
		
		$pdf->SetX(105) ;
		$pdf->Cell(95,$cell_y2,'買方應付仲介服務費',0,1) ;
		$total -= (int)$detail['bRealestateBalance'] ;
	}
	##
	
	//買方履保費
	if ($detail['bCertifiedMoney'] > 0) {
		$pdf->Cell(47.5,$cell_y2,'*買方應付履約保證費') ;
		$pdf->Cell(30,$cell_y2,@number_format(round($detail['bCertifiedMoney'])),0,0,'R') ;
		
		$pdf->SetX(105) ;
		$pdf->Cell(95,$cell_y2,$detail['bcertify_remark'],0,1) ;
		$total -= (int)$detail['bCertifiedMoney'] ;
	}
	##
	
	//買方代書費
	if ($detail['bScrivenerMoney'] > 0) {
		$pdf->Cell(47.5,$cell_y2,'*應付代書費用及代支費') ;
		$pdf->Cell(30,$cell_y2,@number_format(round($detail['bScrivenerMoney'])),0,0,'R') ;
		
		$pdf->SetX(105) ;
		$pdf->Cell(95,$cell_y2,'',0,1) ;
		$total -= (int)$detail['bScrivenerMoney'] ;
	}
	##
	
	//代扣補充保費
	if ($detail['bNHITax'] > 0) {
		$pdf->Cell(47.5,$cell_y2,'*代扣健保補充保費') ;
		$pdf->Cell(30,$cell_y2,@number_format(round($detail['bNHITax'])),0,0,'R') ;
		
		$pdf->SetX(105) ;
		$pdf->Cell(95,$cell_y2,'代買方扣繳 1.91% 補充保費',0,1) ;
		$total -= (int)$detail['bNHITax'] ;
	}
	##
	
	//代扣所得稅
	if ($detail['bTax'] > 0) {
		$pdf->Cell(47.5,$cell_y2,'*代扣利息所得稅') ;
		$pdf->Cell(30,$cell_y2,@number_format(round($detail['bTax'])),0,0,'R') ;
		
		$pdf->SetX(105) ;
		
		if (preg_match("/[A-Za-z]{2}/",$detail['bBuyerId'])) {					// 判別是否為外國人(兩碼英文字母者) 外國人20%		
			$pdf->Cell(95,$cell_y2,'代買方扣繳20% 利息所得稅',0,1) ;
		}else{
			$pdf->Cell(95,$cell_y2,'代買方扣繳10% 利息所得稅',0,1) ;
		}

		$total -= (int)$detail['bTax'] ;
	}
	##代扣利息所得稅

	##買方待扣款項明細它項

	for ($i=0; $i < count($tax_buyer) ; $i++) { 

		$y = $pdf->gety();
		$x = $pdf->getx();

		$pdf->MultiCell(47.5,$cell_y2,'*'.$tax_buyer[$i]['cTaxTitle']) ;
		
		$pdf->setxy($x+47.5,$y);
		$pdf->Cell(30,$cell_y2,@number_format(round($tax_buyer[$i]['cTax'])),0,0,'R') ;
				
		$pdf->setxy(105,$y);
		$pdf->MultiCell(95,$cell_y2,$tax_buyer[$i]['cTaxRemark'],0,1) ;
	}
	$pdf->Ln();
	##
}

$pdf->Ln($cell_gap);	
// $pdf->SetFontSize(12) ;									// 手動換行
if($detail['other_remark_buyer']) {
	$pdf->MultiCell(190, $cell_y2, $detail['other_remark_buyer'], 0);
}

for ($i=0; $i < count($remark_buy); $i++) { 
	$pdf->MultiCell(190, $cell_y2, $remark_buy[$i]['cRemark'], 0);
}

if ($count > 0) {
	##
	if ($check == 0 || $count > 1 ) {
	
		$pdf->Ln($cell_gap);										// 手動換行
		
		// $pdf->SetFontSize(12) ;	
		$pdf->Cell(190,$cell_y2,'指定收受價金之帳戶',0,1) ;
		
		//畫線(雙線條)
		// $pdf->SetFontSize(12) ;
		$xy = $pdf->GetY() ;
		$xy += ($line_gap / 2) ;
		$pdf->Line(10,$xy,200,$xy,$border) ;	
		
		$pdf->SetFontSize(12) ;
		$xy = $pdf->GetY() ;
		$xy -= $line_gap ;
		$pdf->Line(10,$xy,200,$xy,$border) ;
		##

		$pdf->Cell(18.75,$cell_y4,'對象') ;									// 指定帳戶 Title
		$pdf->Cell(60,$cell_y4,'解匯行/分行') ;
		$pdf->Cell(46.25,$cell_y4,'帳號') ;
		$pdf->Cell(41.25,$cell_y4,'戶名') ;
		$pdf->Cell(23.75,$cell_y4,'金額',0,1) ;
		
		//畫線(單線條)
		$xy = $pdf->GetY() ;
		$xy -= $line_gap ;
		$pdf->Line(10,$xy,200,$xy,$border) ;
		##
		
		//建立銀行帳號表格
		$sql = '
			SELECT
				*,
				(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as bankMain,
				(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as bankBranch
			FROM
				tChecklistBank AS a
			WHERE
				cCertifiedId="'.$detail['cCertifiedId'].'"
				AND cIdentity IN ("1","33","43","53")
				AND cHide = 0
			ORDER BY
				cOrder
			ASC,
				cBankAccountNo
			DESC;
		' ;
		$rs = $conn->Execute($sql);

		
		while (!$rs->EOF) {
			$tmp = $rs->fields;
			//確認身分顯示
			switch($tmp['cIdentity']) {
				case '1' :
						$tmp['cIdentity'] = '買方' ;
						break ;
				case '33' :
						$tmp['cIdentity'] = '仲介' ;
						break ;
				case '43' :
						$tmp['cIdentity'] = '地政士' ;
						break ;
				case '53' :
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
				
				$tmp['bank'] = $tmp['bankMain'].'/'.$tmpArr[0] ;
			}
			##
			
			// 指定帳戶表格
			$pdf->Cell(18.75,$cell_y5,$tmp['cIdentity'],1) ;						// 對象
			
			$pdf->SetFont('msungstdlight', 'B', 9);
			$pdf->Cell(60,$cell_y5,$tmp['bank'],1) ;								// 解匯行/分行
			
			$pdf->SetFont('msungstdlight', 'B', 12);
			$pdf->Cell(46.25,$cell_y5,$tmp['cBankAccountNo'],1) ;					// 帳號
			
			$strLen = mb_strlen($tmp['cBankAccountName']);

			if (mb_strlen($tmp['cBankAccountName']) > 27) {
				$pdf->SetFont('msungstdlight', 'B', 8);
			}else{
				$pdf->SetFont('msungstdlight', 'B', 12);
			}
			$pdf->Cell(41.25,$cell_y5,$tmp['cBankAccountName'],1) ;					// 戶名
			//$pdf->MultiCell(41.25,$cell_y5,$tmp['cBankAccountName'],1,0) ;					// 戶名
			
			$pdf->SetFont('msungstdlight', 'B', 12);
			if ($tmp['cMoney'] == 0) {
				$tmp['cMoney'] = '';
			}
			$pdf->Cell(23.75,$cell_y5,$tmp['cMoney'],1,1) ;										// 金額
			##
			
			unset($tmp) ;
			$rs->MoveNext();
		}
	}
}

##
$pdf->SetFont('msungstdlight', 'B', 10);
$pdf->Cell(190,$cell_y6,'其他注意事項',0,1) ;
$pdf->Cell(5,$cell_y6,'1.') ;
$pdf->Cell(190,$cell_y6,'本案業由買方已取回權狀及隨案謄本並結案。',0,1) ;
$pdf->Cell(5,$cell_y6,'2.') ;
$pdf->Cell(190,$cell_y6,'此證明書將做為第一建築經理股份有限公司辦理專戶價金結算及撥付之依據',0,1) ;

$pdf->Cell(5,$cell_y6,'3.') ;
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
$pdf->Cell($x,$cell_y6,'□捐贈「財團法人台灣兒童暨家庭扶助基金會」□我要索取紙本電子發票 地址:') ;

$x = $pdf->GetX()+115 ;
$pdf->Line($x,($y+8), ($x+60), ($y+8));

$x = $pdf->GetX()-9 ;
$y = $pdf->GetY() ;
$pdf->Line($x,($y+10), ($x+185), ($y+10));

$y = $pdf->GetY() ;
$pdf->SetY($y+12);
$x = $pdf->GetX() ;
$pdf->SetX($x+5);
$pdf->Cell($x,$cell_y6,'未勾選視為同意不索取紙本電子發票,台端簽名後即代表知悉上開通知內容,您可至本公司官網查詢發票內容。') ;

$pdf->Ln() ;
$pdf->Cell(190,$cell_y3,'',0,1) ;											// 手動換行

$pdf->Cell(5,$cell_y6,'4.') ;
$pdf->Cell(190,$cell_y6,'點交手續完成及上述事項確認無誤後，請於下方簽章處簽名蓋章：',0,1) ;
$pdf->Ln() ;
$pdf->Cell(63,$cell_y6,'買方簽章：') ;
$pdf->Cell(64,$cell_y6,'仲介方簽章：') ;
$pdf->Cell(63,$cell_y6,'地政士簽章：',0,1) ;

$title_no = 5;
for ($i = 0 ; $i < 10 ; $i ++) {
	$pdf->Cell(190,$cell_y6,'',0,1) ;
}

/*20150505加入預售屋換約備註事項*/
if ($detail['bNote']==1) {
	$y = $pdf->GetY() ;
	$pdf->SetY($y+10);
	$pdf->Cell(5,$cell_y6,$title_no.'.') ;
	$pdf->Cell(200,$cell_y6,'※買賣雙方業於____年____月____日已向建設公司完成換約事宜，經買方確認無誤，請第一建築經理股份有限',0,1) ;
	
	$pdf->SetY($pdf->GetY() + 1) ;
	$pdf->SetX(19) ;
	$pdf->Cell(63,$cell_y6,'公司將履保專戶款項全數撥付至賣方指定帳戶。',0,1) ;
	$title_no ++;
}
// $pdf->SetFont('msungstdlight', 'B', 10);
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

$pdf->MultiCell('',$cell_y6,'中華民國 ________ 年 ________ 月 ________ 日　　聯絡電話：'.$company['tel'].' Ext.'.$undertaker['Ext'].'　　傳真電話：'.$undertaker['FaxNum'],0,'C',0,1,12,290) ;					// 寫入文字
// $pdf->Text(12,295,) ;
//////////////////////// 賣方 ///////////////////////////
$pdf->AddPage() ;
$pdf->SetFont('msungstdlight', 'B', 6);
$pdf->Cell(185,5,$detail['last_modify'],0,1,'R') ;
$pdf->SetFont('msungstdlight', 'B', 14);


$pdf->Cell(190,$cell_y1,'第一建築經理(股)公司',0,1,'C') ;					// 寫入文字
	
$pdf->SetFont('msungstdlight', 'B', 8);
$pdf->Cell(190,$cell_y1,'履保專戶收支明細表暨點交確認單(賣方)',0,1,'C') ;

$pdf->Ln($cell_gap);											// 手動換行

$pdf->SetFont('msungstdlight', 'B', 12);
$pdf->Cell(190,$cell_y4,'案件基本資料',0,1) ;

//畫線(雙線條)
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy,$border) ;	

$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy,$border) ;
##
$pdf->Cell(28,$cell_y1,'保證號碼：') ;										// 基本資料明細
$pdf->Cell(82,$cell_y1,$detail['cCertifiedId']) ;

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
$pdf->Cell(28,$cell_y1,'買方姓名：') ;
$xx = ceil($pdf->GetStringWidth($detail['cBuyer'])) ;
$pdf->Cell($xx,$cell_y1,$detail['cBuyer']) ;
$pdf->Cell(8,$cell_y1,substr($detail['cBuyerId'],0,1),0,0,'R') ;

$addX = 1 ;
if (preg_match("/[0-9]/",substr($detail['cBuyerId'],0,1))) {
	$addX = 2 ;
}
$pdf->SetX($pdf->GetX() - $addX) ;
$xx = 82 - $xx - 8 ;
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
	$pdf->setY($yy+$cell_y1);
	$yy = $pdf->getY();
}

$pdf->Cell(28,$cell_y1,'賣方姓名：') ;
$xx = ceil($pdf->GetStringWidth($detail['cOwner'])) ;
$pdf->Cell($xx,$cell_y1,$detail['cOwner']) ;
$pdf->Cell(8,$cell_y1,substr($detail['cOwnerId'],0,1),0,0,'R') ;
$addX = 1 ;
if (preg_match("/[0-9]/",substr($detail['cOwnerId'],0,1))) {
	$addX = 2 ;
}
$pdf->SetX($pdf->GetX() - $addX) ;
$xx = 82 - $xx - 8 ;
$pdf->Cell($xx,$cell_y1,$idNew,0,0,'L') ;


if ($detail['cMoreStore']) {
	$pdf->setY($yy2);
}
if ($cCertifiedId  == '030119750') {
	$pdf->SetX(130) ;
}else{
	$pdf->SetX(120) ;
}
$pdf->Cell(28,$cell_y1,'') ;

if (!$detail['cMoreStore']) {
	$pdf->Cell(52,$cell_y1,$detail['cStore'],0,1) ;
}else{
	
	$pdf->MultiCell(52,$cell_y1,$cMoreStore[1],0,1) ;
	$yy2 = $pdf->getY();

	$yy = $pdf->setY($yy+$cell_y1);	
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
	$pdf->MultiCell(52,$cell_y1,$cMoreStore[2],0,'L') ;
}

if ($detail['cCompensation2'] > 0) {
	if ($cCertifiedId  == '030119750') {
		$pdf->SetX(130) ;
	}else{
		$pdf->SetX(120) ;
	}
	$pdf->Cell(28,$cell_y1,'專戶代償金額：') ;
	$pdf->Cell(52,$cell_y1,"$".@number_format($detail['cCompensation2'])."元",0,1) ;
}elseif ($detail['cCompensation3'] > 0 && $detail['cCompensation2'] <= 0) {
	$pdf->SetX(120) ;
	$pdf->Cell(28,$cell_y1,'買方銀行代償：') ;

	$pdf->Cell(52,$cell_y1,"$".@number_format($detail['cCompensation3'])."元",0,1) ;
}

if ($detail['cNotIntoMoney'] > 0) {
	$pdf->Cell(28,$cell_y1,'未入專戶：') ;
	$pdf->Cell(82,$cell_y1,"$".@number_format($detail['cNotIntoMoney'])."元") ;
}else{
	$pdf->Cell(28,$cell_y1,'') ;
	$pdf->Cell(82,$cell_y1,'') ;
}

if ($detail['cCompensation2'] > 0 && $detail['cCompensation3'] > 0) {
	$pdf->SetX(120) ;
	$pdf->Cell(28,$cell_y1,'買方銀行代償：') ;

	$pdf->Cell(52,$cell_y1,"$".@number_format($detail['cCompensation3'])."元",0,1) ;

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
for ($i = 0 ; $i < $property_max ; $i ++) {
	$pdf->Cell(28,$cell_y1,'買賣標的物：') ;
	$pdf->MultiCell(162,$cell_y1,$property[$i]['cAddr'],0,'L') ;
}
$pdf->Ln($cell_gap);											// 手動換行

$pdf->SetFontSize(12) ;	
$pdf->Cell(190,$cell_y4,'買賣價金收支明細',0,1) ;

//畫線(雙線條)
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy,$border) ;	

$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy,$border) ;
##

$pdf->Cell(23.75,$cell_y4,'日期',0,0,'C') ;									// 收支明細 title
$pdf->Cell(45,$cell_y4,'摘要',0,0,'C') ;
$pdf->Cell(23.75,$cell_y4,'收入/支出',0,0,'C') ;
$pdf->Cell(23.75,$cell_y4,'金額',0,0,'R') ;
$pdf->Cell(36.25,$cell_y4,'小計',0,0,'R') ;
$pdf->Cell(37.5,$cell_y4,'備註',0,1,'C') ;

//畫線(雙線條)
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy,$border) ;	

$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy,$border) ;
// 賣方收支明細
$pdf->Cell(190,$cell_y4,'【專戶收款】',0,1) ;

unset($tmp);

$income = 0 ;
for ($i = 0 ; $i < $max_owner ; $i ++) {
	
	$income += $trans_owner[$i]['oIncome'] ;
	
	$trans_owner[$i]['oRemark'] = n_to_w($trans_owner[$i]['oRemark']) ;
	$trans_owner[$i]['oRemark'] = preg_replace("/^＋/","含",$trans_owner[$i]['oRemark']) ;
	$pdf->SetFont('msungstdlight', 'B', 12);
	$pdf->Cell(23.75,$cell_y1,$trans_owner[$i]['oDate']) ;
	$pdf->Cell(45,$cell_y1,$trans_owner[$i]['oKind']) ;
	$pdf->Cell(23.75,$cell_y1,'收入',0,0,'C') ;
	$pdf->Cell(23.75,$cell_y1,@number_format($trans_owner[$i]['oIncome']),0,0,'R') ;
	$pdf->Cell(36.25,$cell_y1,'',0,0,'R') ;
	$pdf->SetX(165) ;
	$pdf->SetFont('msungstdlight', 'B', 9);
	$pdf->MultiCell(37.5,$cell_y1,$trans_owner[$i]['oRemark'],0,'L') ;
}
$pdf->SetFont('msungstdlight', 'B', 12);
$income += $detail['cInterest'] ;
$pdf->Cell(23.75,$cell_y1,'') ;
$pdf->Cell(45,$cell_y1,'利息') ;
$pdf->Cell(23.75,$cell_y1,'收入',0,0,'C') ;
$pdf->Cell(23.75,$cell_y1,@number_format($detail['cInterest']),0,0,'R') ;
$pdf->Cell(36.25,$cell_y1,@number_format($income),0,0,'R') ;
$pdf->SetX(165) ;
$pdf->Cell(37.5,$cell_y1,'',0,1) ;

if ($max_owner > 0) {
	$pdf->Ln($cell_gap);											// 手動換行
}
$pdf->SetFont('msungstdlight', 'B', 12);
$pdf->Cell(190,$cell_y4,'【專戶出款】',0,1) ;

$x_pos = $pdf->GetX() ;
$y_pos = $pdf->GetY() ;
			
$outgoing = 0 ;
for ($i = 0 ; $i < $max_owner_e ; $i ++) {
	$pdf->SetFont('msungstdlight', 'B', 12);
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
	$pdf->SetFont('msungstdlight', 'B', 9);	
	$pdf->MultiCell(37.5,$cell_y1,$trans_owner_e[$i]['oRemark'],0,1) ;
}
$pdf->SetFont('msungstdlight', 'B', 12);
if ($max_owner_e > 0) {
	$pdf->SetXY($x_pos,$y_pos) ;
	$pdf->Cell(36.25,$cell_y1,@number_format($outgoing),0,1,'R') ;
}

$y1 = $pdf->getY()+$cell_y1;
// $pdf->Cell(190,0.5,'',0,1) ;	
$pdf->Ln($cell_gap);
$y = $pdf->getY();

$pdf->Cell(190,$cell_y4,'待扣款項明細',0,1) ;
//畫線(雙線條)
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy,$border) ;	

$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy,$border) ;
##
$pdf->Cell(47.5,$cell_y4,'摘要') ;										// 結清付款項 Title
$pdf->Cell(30,$cell_y4,'金額',0,0,'R') ;
$pdf->Cell(17.5,$cell_y4,'') ;
$pdf->Cell(95,$cell_y4,'備註',0,1) ;

//畫線(雙線條)
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy,$border) ;	

$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy,$border) ;
##
$pdf->Cell(47.5,$cell_y2,'*應付仲介服務費餘額') ;
$pdf->Cell(30,$cell_y2,@number_format($detail['cRealestateBalance']),0,0,'R') ;	

$pdf->SetX(105) ;
$pdf->MultiCell(95,$cell_y2,$detail['realty_remark'],0,1) ;

$pdf->Cell(47.5,$cell_y2,'*賣方應付履約保證費') ;
$pdf->Cell(30,$cell_y2,@number_format($detail['cCertifiedMoney']),0,0,'R') ;	

$pdf->SetX(105) ;
$pdf->Cell(95,$cell_y2,$detail['certify_remark'],0,1) ;
##
if ($detail['cCertifiedMoney2'] > 0) {
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

//代扣補充保費
if ($detail['cNHITax'] > 0) {
	$pdf->Cell(47.5,$cell_y2,'*代扣健保補充保費') ;
	$pdf->Cell(30,$cell_y2,@number_format(round($detail['cNHITax'])),0,0,'R') ;
	
	$pdf->SetX(105) ;
	$pdf->Cell(95,$cell_y2,'代賣方扣繳 1.91% 補充保費',0,1) ;
}
##

//代扣所得稅
if ($detail['cTax'] > 0) {
	$pdf->Cell(47.5,$cell_y2,'*代扣利息所得稅') ;
	$pdf->Cell(30,$cell_y2,@number_format(round($detail['cTax'])),0,0,'R') ;
	
	$pdf->SetX(105) ;
	if (preg_match("/[A-Za-z]{2}/",$detail['cOwnerId'])) {					// 判別是否為外國人(兩碼英文字母者) 外國人20%		
		$pdf->Cell(95,$cell_y2,'代賣方扣繳20% 利息所得稅',0,1) ;
	}else{
		$pdf->Cell(95,$cell_y2,'代賣方扣繳10% 利息所得稅',0,1) ;
	}
}

##賣方待扣款項明細它項
$other = 0;

for ($i=0; $i < count($tax_owner) ; $i++) { 
	
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

$pdf->Cell(47.5,$cell_y2,'*賣方實收金額') ;

$_got_money = $income - $outgoing - $detail['cRealestateBalance'] - $detail['cCertifiedMoney'] - $detail['cScrivenerMoney'] - $detail['cTax'] - $detail['cNHITax'] - $other-$detail['cCertifiedMoney2'];
$pdf->Cell(30,$cell_y2,@number_format($_got_money),0,0,'R') ;

$pdf->SetX(105) ;
$pdf->Cell(95,$cell_y2,'委由第一建經撥入下列指定帳戶',0,1) ;
if($detail['other_remark']) {
	$pdf->MultiCell(190, $cell_y2, $detail['other_remark'], 0);
}

for ($i=0; $i < count($remark_owner); $i++) { 
	$pdf->MultiCell(190, $cell_y2, $remark_owner[$i]['cRemark'], 0);
}

$pdf->Ln($cell_gap);											// 手動換行


$pdf->Cell(190,$cell_y2,'指定收受價金之帳戶',0,1) ;

//畫線(雙線條)
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy,$border) ;	

$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy,$border) ;
##

$pdf->Cell(18.75,$cell_y4,'對象') ;									// 指定帳戶 Title
$pdf->Cell(60,$cell_y4,'解匯行/分行') ;
$pdf->Cell(46.25,$cell_y4,'帳號') ;
$pdf->Cell(41.25,$cell_y4,'戶名') ;
$pdf->Cell(23.75,$cell_y4,'金額',0,1) ;
	
//畫線(單線條)
$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy,$border) ;
##
//建立銀行帳號表格
$sql = '
	SELECT
		*,
		(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as bankMain,
		(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as bankBranch
	FROM
		tChecklistBank AS a
	WHERE
		cCertifiedId="'.$detail['cCertifiedId'].'"
		AND cIdentity IN ("2","31","32","42","52")
		AND cHide = 0
	ORDER BY
			cOrder
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
				$tmp['cIdentity'] = '賣方' ;
				
				break ;
		case '31' :
				$tmp['cIdentity'] = '買方' ;
				
				break ;
		case '32' :
				
				$tmp['cIdentity'] = '仲介' ;
				
				break ;
		case '42' :
					$tmp['cIdentity'] = '地政士' ;
				
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
		
		$tmp['bank'] = $tmp['bankMain'].'/'.$tmpArr[0] ;
	}
	##
	
	// 指定帳戶表格
	$pdf->Cell(18.75,$cell_y5,$tmp['cIdentity'],1) ;						// 對象

	if (mb_strlen($tmp['bank']) > 60) {
		$pdf->SetFont('msungstdlight', 'B', 7);
	}else{
		$pdf->SetFont('msungstdlight', 'B', 9);
	}

	$pdf->Cell(60,$cell_y5,$tmp['bank'],1) ;	

	$pdf->SetFont('msungstdlight', 'B', 12);
	$pdf->Cell(46.25,$cell_y5,$tmp['cBankAccountNo'],1) ;					// 帳號

	$strLen = mb_strlen($tmp['cBankAccountName']);

	if (mb_strlen($tmp['cBankAccountName']) > 27) {
		$pdf->SetFont('msungstdlight', 'B', 8);
	}else{
		$pdf->SetFont('msungstdlight', 'B', 12);
	}
	
	$pdf->Cell(41.25,$cell_y5,$tmp['cBankAccountName'],1) ;					// 戶名
	
	$pdf->SetFont('msungstdlight', 'B', 12);
	if ($tmp['cMoney'] == 0) {
		$tmp['cMoney'] = '';
	}
	$pdf->Cell(23.75,$cell_y5,$tmp['cMoney'],1,1) ;										// 金額
	##
	
	unset($tmp) ;

	$rs->MoveNext();
}

$pdf->Ln($cell_gap);											// 手動換行

$pdf->SetFont('msungstdlight', 'B', 10);
$pdf->Cell(190,$cell_y6,'應注意事項',0,1) ;
$pdf->Cell(5,$cell_y6,'1.') ;
$pdf->Cell(185,$cell_y6,'此證明書將做為第一建築經理股份有限公司辦理專戶價金結算及撥付之依據',0,1) ;
$pdf->Cell(5,$cell_y6,'2.') ;
$pdf->MultiCell(185,$cell_y6,'年度給付利息所得將依法開立扣繳憑單,將依法開立扣繳憑單;該所得非「儲蓄投資特別扣除額」之27萬免扣繳範圍',0,1) ;

$pdf->Cell(5,$cell_y6,'3.') ;
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
$pdf->Cell($x,$cell_y6,'□捐贈「財團法人台灣兒童暨家庭扶助基金會」□我要索取紙本電子發票 地址:') ;

$x = $pdf->GetX()+115 ;
$pdf->Line($x,($y+8), ($x+60), ($y+8));

$x = $pdf->GetX()-9 ;
$y = $pdf->GetY() ;
$pdf->Line($x,($y+10), ($x+185), ($y+10));

$y = $pdf->GetY() ;
$pdf->SetY($y+12);
$x = $pdf->GetX() ;
$pdf->SetX($x+5);
$pdf->Cell($x,$cell_y6,'未勾選視為同意不索取紙本電子發票,台端簽名後即代表知悉上開通知內容,您可至本公司官網查詢發票內容。') ;


$pdf->Ln() ;
$pdf->Cell(190,$cell_y3,'',0,1) ;											// 手動換行

$pdf->Cell(5,$cell_y6,'4.') ;
$pdf->Cell(185,$cell_y6,'點交手續完成及上述事項確認無誤後，請於下方簽章處簽名蓋章：',0,1) ;
$pdf->Cell(63,$cell_y6,'賣方簽章：') ;
$pdf->Cell(64,$cell_y6,'仲介方簽章：') ;
$pdf->Cell(63,$cell_y6,'地政士簽章：',0,1) ;

$title_no=5;
$pdf->SetY(245) ;
/*20150505加入預售屋換約備註事項 */
if ($detail['cNote']==1) {
	$y=$pdf->GetY();
	$pdf->SetY($y+5);
	$pdf->Cell(5,$cell_y6,$title_no.'.') ;
	$pdf->Cell(200,$cell_y6,'※買賣雙方業於____年____月____日已向建設公司完成換約事宜，經買方確認無誤，請第一建築經理股份有限',0,1) ;
	
	$pdf->SetY($pdf->GetY() + 1) ;
	$pdf->SetX(19) ;
	$pdf->Cell(63,$cell_y6,'公司將履保專戶款項全數撥付至賣方指定帳戶。',0,1) ;
	$title_no ++;
}
$pdf->SetFontSize(10) ;
$pdf->MultiCell('',$cell_y6,'中華民國 ________ 年 ________ 月 ________ 日　　聯絡電話：'.$company['tel'].' Ext.'.$undertaker['Ext'].'　　傳真電話：'.$undertaker['FaxNum'],0,'C',0,1,12,290) ;					// 寫入文字

// $pdf->Output() ;
if ($download) {
	$pdf->Output($download,'F') ;
}else{
	$pdf->Output() ;
}
die;
#######################
//半形<=>全形
function n_to_w($strs, $types = '0'){  // narrow to wide , or wide to narrow
	$nt = array(
        "(", ")", "[", "]", "{", "}", ".", ",", ";", ":",
        "-", "?", "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~", "`", "'", "\"", "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " "
	);
	$wt = array(
        "（", "）", "〔", "〕", "｛", "｝", "﹒", "，", "；", "：",
        "－", "？", "！", "＠", "＃", "＄", "％", "＆", "｜", "＼",
        "／", "＋", "＝", "＊", "～", "、", "、", "＂", "＜", "＞",
        "︿", "＿",
        "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
        "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
        "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
        "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
        "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
        "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
        "　"
	);
 
	if ($types == '0') {		//半形轉全形
		// narrow to wide
		$strtmp = str_replace($nt, $wt, $strs);
	}
	else {						//全形轉半形
		// wide to narrow
		$strtmp = str_replace($wt, $nt, $strs);
	}
	return $strtmp;
}
##

//遮蔽部分文數字
function newName($nameStr) {
	for ($i = 0 ; $i < mb_strlen($nameStr,'UTF-8') ; $i ++) {
		$arrName[$i] = mb_substr($nameStr,$i,1,'UTF-8') ;
		if (($i > 0) && ($i < (mb_strlen($nameStr,'UTF-8') - 1))) {
			$arrName[$i] = 'Ｏ' ;
		}
	}
	return implode('',$arrName) ;
}
##
?>