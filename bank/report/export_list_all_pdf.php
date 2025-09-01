<?php
// echo "<pre>"
// print_r($data);
// die;

//  ini_set("display_errors", "On"); 
// error_reporting(E_ALL & ~E_NOTICE);
################

$pdf->AddPage();
$pdf->SetFont('msjh', 'B', 10);
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
//writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
$border = array(
		  'B' => array('width' => 0.01, 'color' => array(195,195,195)),
		  'L' => array('width' => 0.01, 'color' => array(195,195,195)),
		  'T' => array('width' => 0.01, 'color' => array(195,195,195)),
		  'R' => array('width' => 0.01, 'color' => array(195,195,195)),
	);
$border2 = array(
		  'L' => array('width' => 0.01, 'color' => array(195,195,195)),
		  'R' => array('width' => 0.01, 'color' => array(195,195,195)),
		  'T' => array('width' => 0.01, 'color' => array(195,195,195)),
		  
	);
$border22 = array(
		  'L' => array('width' => 0.01, 'color' => array(195,195,195)),
		  'R' => array('width' => 0.01, 'color' => array(195,195,195)),
		  'B' => array('width' => 0.01, 'color' => array(195,195,195)),
	);
//紅框
$borderRed = array(
		  'B' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),
		  'L' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),
		  'T' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),
		  'R' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),
	);
//紅框二
$borderRed2 = array(
		  'L' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),
		  'R' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),
		  'T' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),
	);
//紅框二
$borderRed22 = array(
		  'B' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),
		  'L' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),
		  'R' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),

	);

$borderRed23 = array(
		  'L' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),
		  'R' => array('width' => 0.01, 'cap' => 'double', 'join' => 'miter', 'dash' => 0, 'color' => array(144, 0, 0)),

	);
##表頭##


$pdf->MultiCell('', 0.8, '出款確認單', $border, 'C', 0, 0.8, '', '', true, 0, false, true, 0.8, 'M');


$pdf->Ln(0.05);
$pdf->SetFont('msjh', '', 8);
$pdf->MultiCell('', 0.3, $data['cBank'], $border, 'R', 0, 0.8, '', '', true, 0, false, true, true, 'M');

$pdf->Ln(0.05);
$x = 4;
$y = $pdf->getY()+0.05;
$pdf->MultiCell('', 0.6, '', $border, 'L', 0, 0.8, '', '', true, 0, false, true, 0.5, 'M');
$pdf->MultiCell(6, 0.48, '', $border, 'L', 0, 0.8, $x, $y, true, 0, false, true, true, 'M');

$x = $x +6+0.05;
$pdf->MultiCell(2, 0.48, '', $border, 'L', 0, 0.8, $x, $y, true, 0, false, true, true, 'M');

$x = $x +2+0.05;
$pdf->MultiCell(4, 0.48, '', $border, 'L', 0, 0.8, $x, $y, true, 0, false, true, true, 'M');

$x = $x +4+0.05;
$pdf->MultiCell(4, 0.48, '單號：'. $data['cCertifiedId'], $border, 'L', 0, 0.8, $x, $y, true, 0, false, true, true, 'M');
##
##案件資料 : ##
$pdf->SetFont('msjh', '', 8);
$pdf->Ln(0.15);

$borderY = $pdf->getY()+0.5;
$borderY2 = $pdf->getY()+0.1;

$x = 1.2;
$y = $borderY+0.4;
//內容
$table = '
<table width="98%" cellspacing="2" cellpadding="2">          
	            <tr>
	               <td cosplan="8" style="border:1px solid #CCC;line-height:8px;" width="520px;">&nbsp;買賣總價金額：'.number_format($data['cTotalMoney']).'元</td>
	            </tr>
	            <tr >
	               <td style="border:1px solid #CCC;line-height:8px;" width="70px">&nbsp;買受人：</td>
	               <td style="border:1px solid #CCC;line-height:8px;" width="60px">&nbsp;'.$data['buyer'].$data['buyerO'].'</td>
	               <td style="border:1px solid #CCC;line-height:8px;" width="58px">&nbsp;統一編號：</td>
	               <td style="border:1px solid #CCC;line-height:8px;" width="70px">&nbsp;'.$data['b_ID'].'</td>
	               <td style="border:1px solid #CCC;line-height:8px;" width="60px">&nbsp;出賣人：</td>
	               <td style="border:1px solid #CCC;line-height:8px;" width="60px">&nbsp;'.$data['owner'].$data['ownerO'].'</td>
	               <td style="border:1px solid #CCC;line-height:8px;" width="58px">&nbsp;統一編號:</td>
	               <td style="border:1px solid #CCC;line-height:8px;" width="70px">&nbsp;'.$data['o_ID'].'</td>
	            </tr>';

if (is_array($data['ContractProperty'])) {
	
	foreach ($data['ContractProperty'] as $k => $v) {
		$table .= '<tr>
			<td width="13%" style="border:1px solid #CCC;line-height:8px;" width="70px">&nbsp;標的物地址：</td>
			<td cosplan="7" style="border:1px solid #CCC;line-height:8px;" width="448px">&nbsp;'.$v['zCity'].$v['zArea'].$v['cAddr'].'</td>
		</tr>';
	}
}else{
	$table .= '<tr>
			<td width="13%" style="border:1px solid #CCC;line-height:8px;" width="70px">&nbsp;標的物地址：</td>
			<td cosplan="7" style="border:1px solid #CCC;line-height:8px;" width="448px">&nbsp;</td>
		</tr>';
}


$table .='<tr>
	<td style="border:1px solid #CCC;line-height:8px;" width="70px">仲介單位：('.$data['cServiceTarget'].')</td>
	<td style="border:1px solid #CCC;line-height:8px;" cosplan="4" width="262px">'.$data["brand"]."&nbsp;".$data["comp"]."&nbsp;".$data["store"].'</td>
	<td style="border:1px solid #CCC;line-height:8px;" width="52px">&nbsp;</td>
	<td style="border:1px solid #CCC;line-height:8px;" width="58px">承辦代書：</td>
	<td style="border:1px solid #CCC;line-height:8px;" width="70px">'.$data['scrivenerName'].'</td>
</tr>';
if ($data['cBranchNum1'] > 0) {
	$table .='<tr>
		<td style="border:1px solid #CCC;line-height:8px;" width="70px">仲介單位：('.$data['cServiceTarget1'].')</td>
		<td style="border:1px solid #CCC;line-height:8px;" cosplan="6" width="376px">'.$data["brand1"]."&nbsp;".$data["comp1"]."&nbsp;".$data["store1"].'</td>
		<td width="70px" style="border:1px solid #CCC;line-height:8px;">&nbsp;</td>
	</tr>';
}

if ($data['cBranchNum2'] > 0) {
	$table .='<tr>
		<td style="border:1px solid #CCC;line-height:8px;" width="70px">仲介單位：('.$data['cServiceTarget2'].')</td>
		<td style="border:1px solid #CCC;line-height:8px;" cosplan="6" width="376px">'.$data["brand2"]."&nbsp;".$data["comp2"]."&nbsp;".$data["store2"].'</td>
		<td width="70px" style="border:1px solid #CCC;line-height:8px;">&nbsp;</td>
	</tr>';
}

$table .= '</table>';
$pdf->writeHTMLCell(0, 0, $x, $y, $table, 0, 1, 0, true, '', true);

//紅色雙框
$borderX = 1;
$height = $pdf->getY()-$borderY+0.1;//
$pdf->MultiCell(19, $height, '', $borderRed, 'L', 0, 0.8, $borderX, ($borderY-0.05), true, 0, false, true, 0.5, 'M');  

$height = $pdf->getY()-$borderY-0.05;
$borderX = $borderX+0.05;
$pdf->MultiCell(18.9, $height, '', $borderRed, 'L', 0, 0.8, $borderX, ($borderY), true, 0, false, true, 0.5, 'M');  


$height = $pdf->getY()-$borderY+0.7;//外圍框用
//灰底標題
$borderX = $borderX+1;

$pdf->SetFillColor(195,195,195);
$pdf->MultiCell(5, 0.6, '案件資料 : ', 0, 'L', 1, 0.8, $borderX, $borderY2, true, 0, false, true, 0.6, 'M');

//外圍框
$pdf->MultiCell('', $height, '', $border, 'L', 0, 0.8, '', ($borderY-0.5), true, 0, false, true, 0.5, 'M');

##取款戶名、帳號及金額：##
$pdf->Ln(0.05);

if ($pdf->getY() >= 22.5) {
	$pdf->AddPage();
	$pdf->setY(0.8);
}

$borderY = $pdf->getY()+0.5;
$borderY2 = $pdf->getY()+0.1;

$x = 1.2;
$y = $borderY+0.4;

$table = '<table width="98%" cellspacing="2" cellpadding="2"> ';
$table .= '<tr>
		<td width="35%" style="border:1px solid #CCC;line-height:8px;">&nbsp;取款戶名：</td>
		<td cosplan="4" style="border:1px solid #CCC;line-height:8px;" width="65%">&nbsp;'.$data['cTrustAccountName'].'</td>
	</tr>';
$table .= '<tr>
		<td width="35%" style="border:1px solid #CCC;line-height:8px;">&nbsp;取款帳號：</td>
		<td cosplan="4" style="border:1px solid #CCC;line-height:8px;" width="65%">&nbsp;'.$data['cBankTrustAccount'].'</td>
	</tr>';
$table .= '<tr>
		<td width="35%" style="border:1px solid #CCC;line-height:8px;">&nbsp;本指示單取款總金額新台幣：</td>
		<td cosplan="4" style="border:1px solid #CCC;line-height:8px;" width="65%">&nbsp;'.NumtoStr($data['BankTransMoney']).'('.number_format($data['BankTransMoney']).'元 )';
$table .= (!$data['checkBuyerMoney'])?'<font color="red">[買服未入款]</font>':'';

$table .='</td>
	</tr>';
$table .= '<tr>
		<td width="35%" style="border:1px solid #CCC;line-height:8px;">&nbsp;本次出款後預計帳戶餘額新台幣：</td>
		<td cosplan="4" style="border:1px solid #CCC;line-height:8px;" width="65%">&nbsp;'.NumtoStr($data['cCaseMoney']-$data['BankTransMoney']).'('.number_format($data['cCaseMoney']-$data['BankTransMoney']).'元)</td>
	</tr>';
$table .= '<tr>
		<td width="35%" style="border:1px solid #CCC;line-height:8px;">買方另計金額：'.number_format($data['buyerExtraMoney']).'可支付餘額：'.number_format($data['buyerExtraPay']).'</td>
		<td width="25%" style="border:1px solid #CCC;line-height:8px;">&nbsp;</td>
		<td width="10%" style="border:1px solid #CCC;line-height:8px;">&nbsp;</td>
		<td width="10%" style="border:1px solid #CCC;line-height:8px;">&nbsp;</td>
		<td width="19%" style="border:1px solid #CCC;line-height:8px;">&nbsp;</td>
	</tr>';
$table .= '</table>';
$pdf->writeHTMLCell(0, 0, $x, $y, $table, 0, 1, 0, true, '', true);


//紅色雙框
$borderX = 1;
$height = $pdf->getY()-$borderY;//
$pdf->MultiCell(19, $height, '', $borderRed, 'L', 0, 0.8, $borderX, $borderY, true, 0, false, true, 0.5, 'M');  

$height = $pdf->getY()-$borderY-0.1;
$borderX = $borderX+0.05;
$pdf->MultiCell(18.9, $height, '', $borderRed, 'L', 0, 0.8, $borderX, ($borderY+0.05), true, 0, false, true, 0.5, 'M');  


$height = $pdf->getY()-$borderY+0.7;//外圍框用
//灰底標題
$borderX = $borderX+1;

$pdf->SetFillColor(195,195,195);
$pdf->MultiCell(5, 0.6, '取款戶名、帳號及金額： ', 0, 'L', 1, 0.8, $borderX, ($borderY2+0.01), true, 0, false, true, 0.6, 'M');

//外圍框
$pdf->MultiCell('', $height, '', $border, 'L', 0, 0.8, '', ($borderY-0.5), true, 0, false, true, 0.5, 'M');
##
##出款項目及明細：##
$pdf->Ln(0.05);

$borderY = $pdf->getY()+0.5;
$borderY2 = $pdf->getY()+0.1;

$x = 1.2;
$y = $borderY+0.4;

// echo "<pre>";
// print_r($data['BankTrans']);
$checkY = $pdf->getY();

$checkNext = false;
if (is_array($data['BankTrans'])) {
	foreach ($data['BankTrans'] as $k => $v) {

//        if ($_SESSION['member_id'] != $v['staff']) {
//            continue;
//        }
        if(is_array($_POST['account'])) {
            if(!in_array($v["tAccount"], $_POST['account'])) {
                continue;
            }
        }
		
		$tableHeight = 3.8;
		$table = '<table width="98%" cellspacing="2" cellpadding="2"> ';
		$table .= '<tr>
				<td width="100.8%" style="border:1px solid #CCC;line-height:8px;" cosplan="3" >&nbsp;項目：'.$v["tObjKind"];
		if ($v["tObjKind"] == '扣繳稅款') {
			$table .= '<font color="red">(增值稅：'.number_format($data['cAddedTaxMoney']).')</font>';	
			
		}

		if ($v['tObjKind2'] == '01') {
				$table .= '<font color="red">[申請公司代墊]</font>';
			}elseif ($v['tObjKind2'] == '02') {
				$table .= '<font color="red">[返還公司代墊]</font>';
			}
		if ($v['tSend'] == 1) {
			$table .= '(不發送簡訊)';
		}
			
		$table .='</td></tr>';
			
		$table .= '<tr>
				<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;解匯行</td>
				<td width="20%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;金 額</td>
				<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;出帳建檔日期</td>
			</tr>';
		$table .= '<tr>
				<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;'.$v["tBankCode"].' / '.trim($v['Bank']).'&nbsp;'.$v['BankBranch'].'</td>
				<td width="20%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;NT$'.number_format($v["tMoney"]).'</td>
				<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;'.$v['tDate'].'</td>
			</tr>';

		$table .= '<tr>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;戶 名</td>
			<td width="20%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;帳 號</td>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;附言</td>
		</tr>';
		$table .= '<tr>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;'.$v["tAccountName"].'</td>
			<td width="20%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;'.$v["tAccount"].'</td>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;'.$v["tTxt"].'</td>
		</tr>';


		$table .= '<tr>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;電郵</td>
			<td width="20%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;傳真</td>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;交易類別</td>
		</tr>';
		$table .= '<tr>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;'.$v["tEmail"].'</td>
			<td width="20%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;'.$v["tFax"].'</td>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;'.$v["title"].'</td>
		</tr>';

        //出款照會紀錄
        if(isset($data['ConfirmCall'][$v["tId"]]) && count($data['ConfirmCall'][$v["tId"]]) > 0){
            $table .= '<tr>
			<td style="border:1px solid #CCC;line-height:8px;text-align:center;" colspan="3">&nbsp;出款照會紀錄</td>
		</tr>';
            foreach($data['ConfirmCall'][$v["tId"]] as $call_k => $call_v) {
                $table .= '<tr>
                <td style="border:1px solid #CCC;line-height:8px;text-align:center;" colspan="3">&nbsp;'.$call_v.'</td>
            </tr>';
                $tableHeight += 0.6;
            }
        }

		$table .= '<tr><td width="100%" style="line-height:1px;" cosplan="3">&nbsp;<hr></td></tr>';
		$table .= '</table>';
		$pdf->writeHTMLCell(0, 0, $x, $y, $table, 0, 1, 0, true, '', true);
		$y += $tableHeight;
		$height = $y;
		if ( $y >= 24.5 && !$checkNext) { // 
			

			//紅色雙框
			$borderX = 1;
			$height = $pdf->getY()-$borderY-0.5;//
			$pdf->MultiCell(19, $height, '', $borderRed2, 'L', 0, 0.8, $borderX, $borderY, true, 0, false, true, 0.5, 'M');  

			$borderX = $borderX+0.05;
			$pdf->MultiCell(18.9, $height, '', $borderRed2, 'L', 0, 0.8, $borderX, ($borderY+0.05), true, 0, false, true, 0.5, 'M');  


			$height = $pdf->getY()-$borderY+0.5;//外圍框用
			// //灰底標題
			$borderX = $borderX+1;

			$pdf->SetFillColor(195,195,195);
			$pdf->MultiCell(5, 0.6, '出款項目及明細： ', 0, 'L', 1, 0.8, $borderX, ($borderY2+0.1), true, 0, false, true, 0.6, 'M');

			// //外圍框
			$pdf->MultiCell('', $height, '', $border2, 'L', 0, 0.8, '', ($borderY-0.5), true, 0, false, true, 0.5, 'M');
			

			##
			$checkNext = true;
			

			

		}

		if ($y >= 24.5 ) {

			$y = 0.8;
			$borderX = 1;
			$height = $pdf->getY()-$borderY-0.5;//
			$pdf->MultiCell(19, $height, '', $borderRed23, 'L', 0, 0.8, $borderX, $borderY, true, 0, false, true, 0.5, 'M');  

			$borderX = $borderX+0.05;
			$pdf->MultiCell(18.9, $height, '', $borderRed23, 'L', 0, 0.8, $borderX, ($borderY+0.05), true, 0, false, true, 0.5, 'M');  

			
			$pdf->AddPage();
			$borderY = $pdf->getY()+0.5;
			$borderY2 = $pdf->getY()+0.1;
			$borderX = 1;
			$height = 0;



		}

		
	}
	

	if ($checkNext) {
		//擠到下一頁的框
			
			// $height = $pdf->getY();//
			//紅色雙框
			$height = $height -0.5;
			
			$pdf->MultiCell(19, $height, '', $borderRed22, 'L', 0, 0.8, $borderX, ($borderY-0.7), true, 0, false, true, 0.5, 'M');  

			$height = $height-0.1;
			$borderX = $borderX+0.05;
			$pdf->MultiCell(18.9, $height, '', $borderRed22, 'L', 0, 0.8, $borderX, ($borderY+0.05-0.7), true, 0, false, true, 0.5, 'M');  


			$height = $pdf->getY()-$borderY+0.7;//外圍框用
			

			//外圍框
			$pdf->MultiCell('', $height, '', $border22, 'L', 0, 0.8, '', ($borderY-0.5), true, 0, false, true, 0.5, 'M');


	}
		$checkY2 = $pdf->getY();
		// $pdf->writeHTMLCell(0, 0, $x, $y, $checkY."_".$checkY2, 0, 1, 0, true, '', true);

	
}else{
	$table .= '<tr>
			<td width="100%" style="border:1px solid #CCC;line-height:8px;" cosplan="3" >&nbsp;項目：</td>
		</tr>';
	$table .= '<tr>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;賣方解匯行</td>
			<td width="20%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;金 額</td>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;出帳建檔日期</td>
		</tr>';
	$table .= '<tr>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;</td>
			<td width="20%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;</td>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;</td>
		</tr>';
	$table .= '<tr>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;戶 名</td>
			<td width="20%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;帳 號</td>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;附言</td>
		</tr>';
		$table .= '<tr>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;</td>
			<td width="20%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;</td>
			<td width="40%" style="border:1px solid #CCC;line-height:8px;text-align:center;">&nbsp;</td>
		</tr>';
	$table .= '</table>';
	
}


if (!$checkNext) { //如果沒有換頁

	//紅色雙框
	$borderX = 1;
	$height = $pdf->getY()-$borderY;//
	$pdf->MultiCell(19, $height, '', $borderRed, 'L', 0, 0.8, $borderX, $borderY, true, 0, false, true, 0.5, 'M');  

	$height = $pdf->getY()-$borderY-0.1;
	$borderX = $borderX+0.05;
	$pdf->MultiCell(18.9, $height, '', $borderRed, 'L', 0, 0.8, $borderX, ($borderY+0.05), true, 0, false, true, 0.5, 'M');  


	$height = $pdf->getY()-$borderY+0.7;//外圍框用
	//灰底標題
	$borderX = $borderX+1;

	$pdf->SetFillColor(195,195,195);
	$pdf->MultiCell(5, 0.6, '出款項目及明細：', 0, 'L', 1, 0.8, $borderX, ($borderY2+0.1), true, 0, false, true, 0.6, 'M');

	//外圍框
	$pdf->MultiCell('', $height, '', $border, 'L', 0, 0.8, '', ($borderY-0.5), true, 0, false, true, 0.5, 'M');
}

###
##簽章處##
$x = 0.7;
$table = '
<table width="100.5%" cellspacing="2" cellpadding="2" style="border:1px solid #CCC;line-height:8px;">';
$table .= '<tr>
		<td width="100%" style="border:1px solid #CCC;line-height:8px;" cosplan="6">&nbsp;</td>
	</tr>';
$table .= '<tr>
		<td width="13%" style="border:1px solid #CCC;line-height:60px;">&nbsp;核定</td>
		<td width="20%" style="border:1px solid #CCC;line-height:60px;">&nbsp;</td>
		<td width="13%" style="border:1px solid #CCC;line-height:60px;">&nbsp;審核</td>
		<td width="20%" style="border:1px solid #CCC;line-height:60px;">&nbsp;</td>
		<td width="13%" style="border:1px solid #CCC;line-height:60px;">&nbsp;經辦</td>
		<td width="19%" style="border:1px solid #CCC;line-height:60px;">&nbsp;</td>
	</tr>';
$table .= '<tr>
		<td width="100%" style="border:1px solid #CCC;line-height:8px;" cosplan="6">&nbsp;</td>
	</tr>';
$table .= '<tr>
		<td width="100%" style="border:1px solid #CCC;line-height:8px;" cosplan="6">&nbsp;</td>
	</tr>';
$table .= '<tr>
		<td width="100%" style="border:1px solid #CCC;line-height:8px;" cosplan="6">&nbsp;</td>
	</tr>';
$table .= '<tr>
		<td width="100%" style="border:1px solid #CCC;line-height:8px;" cosplan="6">&nbsp;</td>
	</tr>';


$table .= '</table>';

$pdf->writeHTMLCell(0, 0, $x, '', $table, 0, 1, 0, true, '', true);
##

?>