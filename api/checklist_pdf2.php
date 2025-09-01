<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once $GLOBALS['webssl_upload'] . '/api/api_function.php';

// $cCertifiedId = '005049133 ';
$cCertifiedId = $_GET['cId'];
// $cCertifiedId = '004096994 ';
$company = json_decode(file_get_contents(dirname(dirname(dirname(__FILE__))) . '/lib/company.json'), true);
// 取得買賣方資料
$sql    = 'SELECT * FROM tChecklist WHERE cCertifiedId="' . $cCertifiedId . '";';
$rs     = $conn->Execute($sql);
$detail = $rs->fields;

//買方ID
if (strlen($detail['bBuyerId']) == 10) {
    $detail['bBuyerId'] = substr($detail['bBuyerId'], 0, 1) . substr($detail['bBuyerId'], 1, 4) . '****' . substr($detail['bBuyerId'], -1);
} else {
    $detail['bBuyerId'] = substr($detail['bBuyerId'], 0, 1) . substr($detail['bBuyerId'], 1);
}

//賣方ID
if (strlen($detail['bOwnerId']) == 10) {
    $detail['bOwnerId'] = substr($detail['bOwnerId'], 0, 1) . substr($detail['bOwnerId'], 1, 4) . '****' . substr($detail['bOwnerId'], -1);
} else {
    $detail['bOwnerId'] = substr($detail['bOwnerId'], 0, 1) . substr($detail['bOwnerId'], 1);
}
##

// 賣方收支明細(收入部分)##日期為空的要排最後面
$sql       = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="' . $cCertifiedId . '" AND oIncome<>"0" AND oDate!="" ORDER BY oDate ASC; ;';
$rs        = $conn->Execute($sql);
$max_owner = $rs->RecordCount();
while (!$rs->EOF) {
    # code...
    $trans_owner[] = $rs->fields;
    $rs->MoveNext();
}

$sql        = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="' . $cCertifiedId . '" AND oIncome<>"0" AND oDate="" ORDER BY oDate ASC;';
$rs         = $conn->Execute($sql);
$owner_max2 = $rs->RecordCount();
while (!$rs->EOF) {
    # code...
    $trans_owner[$max_owner++] = $rs->fields;
    $rs->MoveNext();
}
##

// 賣方收支明細(支出)
$sql         = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="' . $cCertifiedId . '" AND oExpense<>"0" ORDER BY oDate ASC; ;';
$rs          = $conn->Execute($sql);
$max_owner_e = $rs->RecordCount();
while (!$rs->EOF) {
    $trans_owner_e[] = $rs->fields;

    $rs->MoveNext();
}
##

//讀取買方交易明細(收入部分)##日期為空的要排最後面
$sql       = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId="' . $cCertifiedId . '" AND bIncome<>"0" AND bDate!="" ORDER BY bDate ASC;';
$rs        = $conn->Execute($sql);
$buyer_max = $rs->RecordCount();
while (!$rs->EOF) {
    $buyer_income[] = $rs->fields;
    $rs->MoveNext();
}

$sql        = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId="' . $cCertifiedId . '" AND bIncome<>"0" AND bDate="" ORDER BY bDate ASC;';
$rs         = $conn->Execute($sql);
$buyer_max2 = $rs->RecordCount();
while (!$rs->EOF) {

    $buyer_income[$buyer_max++] = $rs->fields;

    $rs->MoveNext();
}
##

//讀取買方交易明細(支出部分)
$sql         = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId="' . $cCertifiedId . '" AND bExpense<>"0" ORDER BY bDate ASC;';
$rs          = $conn->Execute($sql);
$buyer_max_e = $rs->RecordCount();
while (!$rs->EOF) {

    $buyer_expense[] = $rs->fields;
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
		bkc.bAccount LIKE "%' . $cCertifiedId . '"
';
$rs         = $conn->Execute($sql);
$undertaker = $rs->fields;
if ($undertaker['FaxNum']) {
    $temp                 = $undertaker['FaxNum'];
    $undertaker['FaxNum'] = substr($temp, 0, 7) . '-' . substr($temp, 7);
    unset($temp);
}
##

//確認簽約日期
$cSignDate = '';
$sql       = "SELECT cSignDate FROM tContractCase WHERE cCertifiedId='" . $cCertifiedId . "';";
$rs        = $conn->Execute($sql);

$cSignDate = $rs->fields['cSignDate'];
##

//賣方結清撥付款項明細-其他
$sql = "SELECT * FROM tChecklistOther WHERE cCertifiedId='" . $cCertifiedId . "' AND cIdentity = 2";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $tax_owner[] = $rs->fields;

    $rs->MoveNext();
}
##
//買方結清撥付款項明細-其他
$sql = "SELECT * FROM tChecklistOther WHERE cCertifiedId='" . $cCertifiedId . "' AND cIdentity = 1";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {

    $tax_buyer[] = $rs->fields;

    $rs->MoveNext();
}
##
//結清撥付款項明細-其他-2
$sql = "SELECT * FROM tChecklistRemark WHERE cCertifiedId='" . $cCertifiedId . "' ORDER BY cId ASC";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {

    if ($rs->fields['cIdentity'] == 1) {

        $remark_buy[] = $rs->fields;

    } elseif ($rs->fields['cIdentity'] == 2) {
        $remark_owner[] = $rs->fields;
    }

    $rs->MoveNext();
}

##
//建物

$sql          = "SELECT cAddr,(SELECT zCity FROM tZipArea WHERE zZip = cZip) AS city , (SELECT zArea FROM tZipArea WHERE zZip = cZip) AS area FROM tContractProperty WHERE cCertifiedId ='" . $cCertifiedId . "' ORDER BY cItem";
$rs           = $conn->Execute($sql);
$property_max = $rs->RecordCount();
while (!$rs->EOF) {
    $property[] = $rs->fields;

    $rs->MoveNext();
}
//買方銀行
$sql = '
		SELECT
			*,
			(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as bankMain,
			(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as bankBranch
		FROM
			tChecklistBank AS a
		WHERE
			cCertifiedId="' . $detail['cCertifiedId'] . '"
			AND cIdentity IN ("1","33","43","53")
			AND cHide = 0
		ORDER BY
			cOrder
		ASC,
			cBankAccountNo
		DESC;
	';
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
    //確認身分顯示
    switch ($rs->fields['cIdentity']) {
        case '1':
            $rs->fields['cIdentity'] = '買方';
            break;
        case '33':
            $rs->fields['cIdentity'] = '仲介';
            break;
        case '43':
            $rs->fields['cIdentity'] = '地政士';
            break;
        case '53':
            $rs->fields['cIdentity'] = '';
            break;
        default:
            $rs->fields['cIdentity'] = '';
            break;
    }

    //確認銀行顯示
    if ($rs->fields['bankMain'] && $rs->fields['bankBranch']) {
        $tmpArr = array();
        $tmpArr = explode('（', $rs->fields['bankBranch']);

        $rs->fields['bank'] = $rs->fields['bankMain'] . '/' . $tmpArr[0];
        unset($tmpArr);
    }

    if ($rs->fields['cMoney'] == 0) {
        $rs->fields['cMoney'] = '';
    }

    $buyerbank[] = $rs->fields;

    $rs->MoveNext();
}
//賣方銀行
$sql = '
	SELECT
		*,
		(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as bankMain,
		(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as bankBranch
	FROM
		tChecklistBank AS a
	WHERE
		cCertifiedId="' . $detail['cCertifiedId'] . '"
		AND cIdentity IN ("2","31","32","42","52")
		AND cHide = 0
	ORDER BY
			cOrder
		ASC,
			cBankAccountNo
		DESC;
';
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
    //確認身分顯示
    switch ($rs->fields['cIdentity']) {
        case '2':
            $rs->fields['cIdentity'] = '賣方';
            break;
        case '31':
            $rs->fields['cIdentity'] = '買方';
            break;
        case '32':

            $rs->fields['cIdentity'] = '仲介';

            break;
        case '42':
            $rs->fields['cIdentity'] = '地政士';
            break;
        case '52':
            $rs->fields['cIdentity'] = '';
            break;
        default:
            $rs->fields['cIdentity'] = '';
            break;
    }
    ##
    //確認銀行顯示
    if ($rs->fields['bankMain'] && $rs->fields['bankBranch']) {
        $tmpArr = array();
        $tmpArr = explode('（', $rs->fields['bankBranch']);

        $rs->fields['bank'] = $rs->fields['bankMain'] . '/' . $tmpArr[0];
        unset($tmpArr);
    }
    ##
    if ($rs->fields['cMoney'] == 0) {
        $rs->fields['cMoney'] = '';
    }
    $bankowner[] = $rs->fields;

    $rs->MoveNext();
}
$title_txt = ($detail['bNote'] != 1) ? '履保專戶收支明細表暨點交確認單' : '履約專戶收支明細表暨換約確認單';
?>
<html>
<head>
	<meta charset="UTF-8">
	<title>點交單</title>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			// var total = 0;
			// var pageHeight = 842;
			// $(".checkPage1").each(function() {
			// 	// var id = $(this).attr('id');
			// 	var height = $(this).height();
			// 	total = total+parseInt(height);
			// 	if (total > pageHeight) {
			// 		$('<div style="page-break-after:always;">&nbsp;</div>').insertBefore(this);
			// 		total = 0;
			// 	}
			// 	console.log(height+"_"+total);
			// });
			// total = 0;
			// $(".checkPage2").each(function() {
			// 	// var id = $(this).attr('id');
			// 	var height = $(this).height();
			// 	total = total+parseInt(height);
			// 	if (total > pageHeight) {
			// 		$('<div style="page-break-after:always;">&nbsp;</div>').insertBefore(this);
			// 		total = 0;
			// 	}
			// 	console.log('2:'+height+"_"+total);
			// });
		});
	//
	</script>
	<style>
		body{
			background-color: #FFF;
		}
		body td{
			font-size: 12px;
			/*寬595像素，高842像素 */
		}
		th{
			font-size: 14px;
		}
		.page{
			width: 595px;
			/*height: 842px;*/
			background-color: #FFF;

		}
		.main{
			/*width:90%;*/
			float:center;

		}
		.title{
			/*position:absolute;*/
			display: block;
			line-height: 30px;
			width: 595px;
			text-align: center;


		}

		.title1{
			display:inline;
			font-size: 26px;
			padding-left: 90px;


		}
		.title2{
			display:inline;
			font-size: 9px;
			float: right;


		}
		.title_s{
			clear:both;
			font-size:15px;
			line-height: 25px;
			float:left;
			text-align: center;
			width: 90%;
			/*z-index: 1;*/
		}

		.checkPage1{
			/*background-color: #CCC;*/
			padding-left: 10px;
			padding-right: 10px;
			width: 580px;

		}
		.checkPage2{
			/*background-color: #CCC;*/
			padding-left: 10px;
			padding-right: 10px;
			width: 580px;

		}

	</style>
</head>
<body>
<div>

	<div class="page">
		<div class="main">
			<div class="title checkPage1">
				<div class="title1">第一建築經理(股)公司</div>


				<div class="title2"><?=$detail['last_modify']?></div>

			</div>
			<div class="title_s checkPage1" ><?=$title_txt?>(買方)</div>
			<div style="clear:both;display:block;"></div>
			<div style="width:95%;" class="checkPage1"><div style="border-bottom:double;">案件基本資料</div></div>
			<div style="clear:both;display:block;"></div>
			<div class="checkPage1">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<th width="18%" align="left">保證號碼：</th>
						<td width="32%" align="left"><?=$cCertifiedId?></td>
						<th width="18%" align="left">特約地政士：</th>
						<td width="32%" align="left"><?=$detail['bScrivener']?></td>
					</tr>
					<tr>
						<th width="18%" align="left">買方姓名：</th>
						<td width="32%" align="left"><?=$detail['bBuyer']?>&nbsp;&nbsp;<?=$detail['bBuyerId']?></td>
						<th width="18%" align="left">仲介店名：</th>
						<td width="32%" align="left"><?=$detail['bBrand']?></td>
					</tr>

					<tr >
						<th width="18%" align="left">賣方姓名：</th>
						<td width="32%" align="left"><?=$detail['bOwner']?>&nbsp;&nbsp;<?=$detail['bOwnerId']?></td>
						<th width="18%" align="left">&nbsp;</th>
						<td width="32%" align="left"><?=$detail['bStore']?></td>
					</tr>
					<tr>
						<th width="18%" align="left">買賣總金額：</th>
						<td width="32%" align="left"><?="$" . @number_format($detail['bTotalMoney']) . "元"?></td>
						<?php
if ($detail['bCompensation2'] > 0) {?>
								<th width="18%" align="left">專戶代償金額：</th>
								<td width="32%" align="left"><?="$" . @number_format($detail['bCompensation2']) . "元"?></td>
						<?php
} elseif ($detail['bCompensation3'] > 0 && $detail['bCompensation2'] <= 0) {?>
								<th width="18%" align="left">買方銀行代償：</th>
								<td width="32%" align="left"><?="$" . @number_format($detail['bCompensation3']) . "元"?></td>
					  	<?php }?>
					</tr>

					<?php
if ($detail['bNotIntoMoney'] > 0) {?>
					<tr>
						<th width="18%" align="left">未入專戶：</th>
						<td width="32%" align="left"><?="$" . @number_format($detail['bNotIntoMoney']) . "元"?></td>
					</tr>
					<?php
}?>


					<?php
if ($detail['bCompensation2'] > 0 && $detail['bCompensation3'] > 0) {?>
					<tr>
						<th width="18%" align="left">買方銀行代償：</th>
						<td width="32%" align="left"><?="$" . @number_format($detail['bCompensation3']) . "元"?></td>
					<?php
if ($detail['bCompensation4'] == 0) {
    $detail['bCompensation4'] = $detail['bCompensation2'] + $detail['bCompensation3'];
}?>
						<th width="18%" align="left">代償總金額：</th>
						<td width="32%" align="left"><?="$" . @number_format($detail['bCompensation4']) . "元"?></td>

					</tr>
				<?php
}
for ($i = 0; $i < count($property_max); $i++) {
    $property[$i]['cAddr'] = $property[$i]['city'] . $property[$i]['area'] . $property[$i]['cAddr'];
    $property[$i]['cAddr'] = n_to_w($property[$i]['cAddr']);
    ?>
					<tr>
						<th width="18%" align="left">買賣標的物：</th>
						<td align="left" colspan="4"><?=$property[$i]['cAddr']?></td>

					</tr>
				<?php	}?>

					<tr>
						<td colspan="4">&nbsp;</td>
					</tr>

				</table>
			</div>
			<div class="checkPage1">


				<table border="0" cellpadding="0" cellspacing="0" width="100%" >
					<tr>
						<td colspan="6" style="border-bottom:double;">買賣價金收支明細</td>
					</tr>
					<tr>
						<th width="15%" align="center" style="border-bottom:double;">日期</th>
						<th width="18%" align="center" style="border-bottom:double;">摘要</th>
						<th width="14%" align="right" style="border-bottom:double;">收入金額</th>
						<th width="14%" align="right" style="border-bottom:double;">支出金額</th>
						<th width="14%" align="right" style="border-bottom:double;">小計</th>
						<th width="30%" align="center" style="border-bottom:double;">備註</th>
					</tr>
					<tr>
						<td colspan="6">【專戶收款】</td>
					</tr>
					<?php
$total = 0;
for ($i = 0; $i < $buyer_max; $i++) {
    $total += $buyer_income[$i]['bIncome'];
    $showIncome = '';

    if ($i == ($buyer_max - 1)) {
        $showIncome = @number_format($total);
    }
    $buyer_income[$i]['bRemark'] = n_to_w($buyer_income[$i]['bRemark']);
    $buyer_income[$i]['bRemark'] = preg_replace("/^＋/", "含", $buyer_income[$i]['bRemark']);
    ?>
					<tr>
						<td width="15%" align="center"><?=$buyer_income[$i]['bDate']?></td>
						<td width="18%" align="left"><?=$buyer_income[$i]['bKind']?></td>
						<td width="14%" align="right"><?=@number_format($buyer_income[$i]['bIncome'])?></td>
						<td width="14%" align="right"><?=@number_format($buyer_income[$i]['bExpense'])?></td>
						<td width="14%" align="right"><?=$showIncome?></td>
						<td width="30%" align="left" style="padding-left:10px;font-size:9px"><?=$buyer_income[$i]['bRemark']?></td>
					</tr>

					<?php }
if ($buyer_max_e > 0) {?>
					<tr>
						<td colspan="6">【專戶出款】</td>
					</tr>
					<?php
for ($i = 0; $i < $buyer_max_e; $i++) {
    $total -= $buyer_expense[$i]['bExpense'];
    $expense += $buyer_expense[$i]['bExpense'];

    $showExpense = '';
    if ($i == ($buyer_max_e - 1)) {
        $showExpense = @number_format($expense);
    }
    ?>
					<tr>
						<td width="15%" align="center"><?=$buyer_expense[$i]['bDate']?></td>
						<td width="18%" align="left"><?=$buyer_expense[$i]['bKind']?></td>
						<td width="14%" align="right"><?=@number_format($buyer_expense[$i]['bIncome'])?></td>
						<td width="14%" align="right"><?=@number_format($buyer_expense[$i]['bExpense'])?></td>
						<td width="14%" align="right"><?=$showExpense?></td>
						<td width="30%" align="left" style="padding-left:10px;font-size:9px"><?=$buyer_expense[$i]['bRemark']?></td>
					</tr>

					<?php }
}

$count = 0;
$check = 0;
if ($detail['bRealestateBalance'] > 0) { //買方應付仲介費餘額
    $count++;
}
if ($detail['bCertifiedMoney'] > 0) { //買方履保費
    $count++;
    $check = 1;
}
if ($detail['bScrivenerMoney'] > 0) { //買方代書費
    $count++;
}
if ($detail['bNHITax'] > 0) { //代扣補充保費
    $count++;
}
if ($detail['bTax'] > 0) { //代扣所得稅
    $count++;
}

if (count($tax_buyer) > 0) { //其它代扣
    $count++;
}
//若代扣款明細有值則顯示下列帳戶資料
if ($count > 0) {?>
					<tr>
						<td colspan="6">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="6" style="border-bottom:double;">待扣款項明細</td>
					</tr>
					<tr>
						<th colspan="2" align="left" style="border-bottom:double;">摘要</th>
						<th align="right" style="border-bottom:double;">金額</th>
						<th colspan="3" align="left" style="border-bottom:double; padding-left:30px;">備註</th>
					</tr>
					<?php
if ($detail['bRealestateBalance'] > 0) {?>
					<tr>
						<td colspan="2" align="left">*應付仲介服務費餘額</td>
						<td align="right"><?=@number_format(round($detail['bRealestateBalance']))?></td>
						<td colspan="3" align="left" style="padding-left:30px;">買方應付仲介服務費</td>
					</tr>
					<?php $total -= (int) $detail['bRealestateBalance'];
}
    //買方履保費
    if ($detail['bCertifiedMoney'] > 0) {?>
						<tr>
							<td colspan="2" align="left">*買方應付履約保證費</td>
							<td align="right"><?=@number_format(round($detail['bCertifiedMoney']))?></td>
							<td colspan="3" align="left" style="padding-left:30px;"><?=$detail['bcertify_remark']?></td>
						</tr>
					<?php $total -= (int) $detail['bCertifiedMoney'];
    }
    //買方代書費
    if ($detail['bScrivenerMoney'] > 0) {?>
						<tr>
							<td colspan="2" align="left">*應付代書費用及代支費</td>
							<td align="right"><?=@number_format(round($detail['bScrivenerMoney']))?></td>
							<td colspan="3" align="left" style="padding-left:30px;">&nbsp;</td>
						</tr>
					<?php $total -= (int) $detail['bScrivenerMoney'];
    }
    //代扣補充保費
    if ($detail['bNHITax'] > 0) {?>
						<tr>
							<td colspan="2" align="left">*代扣健保補充保費</td>
							<td align="right"><?=@number_format(round($detail['bNHITax']))?></td>
							<td colspan="3" align="left" style="padding-left:30px;">代買方扣繳 2.11% 補充保費</td>
						</tr>
					<?php $total -= (int) $detail['bNHITax'];
    }
    if ($detail['bTax'] > 0) {?>
						<tr>
							<td colspan="2" align="left">*代扣利息所得稅</td>
							<td align="right"><?=@number_format(round($detail['bTax']))?></td>
							<td colspan="3" align="left" style="padding-left:30px;">
								<?php
if (preg_match("/[A-Za-z]{2}/", $detail['cBuyerId'])) { // 判別是否為外國人(兩碼英文字母者) 外國人20%
        $pdf->Cell(95, $cell_y2, '代買方扣繳20% 利息所得稅', 0, 1);
    } else {
        $pdf->Cell(95, $cell_y2, '代買方扣繳10%利息所得稅', 0, 1);
    }

        ?>

							</td>
						</tr>
					<?php $total -= (int) $detail['bTax'];

    }
    ##買方待扣款項明細它項

    for ($i = 0; $i < count($tax_buyer); $i++) {?>
						<tr>
							<td colspan="2" align="left">*<?=$tax_buyer[$i]['cTaxTitle']?></td>
							<td align="right"><?=@number_format(round($tax_buyer[$i]['cTax']))?></td>
							<td colspan="3" align="left" style="padding-left:30px;"><?=$tax_buyer[$i]['cTaxRemark']?>%利息所得稅</td>
						</tr>


					<?php
}
    ##
}
?>
					<tr>
						<td colspan="6">&nbsp;</td>
					</tr>
				</table>
			</div>
			<?php
if ($count > 1 || $check == 0) {?>
			<div class="checkPage1">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td colspan="5" style="border-bottom:double;">指定收受價金之帳戶</td>
					</tr>
					<tr>
						<th width="10%" style="border-bottom:double;">對象</th>
						<th width="30%" style="border-bottom:double;">解匯行/分行</th>
						<th width="20%" style="border-bottom:double;">帳號</th>
						<th width="20%" style="border-bottom:double;">戶名</th>
						<th width="20%" style="border-bottom:double;">金額</th>
					</tr>
				<?php
for ($i = 0; $i < count($buyerbank); $i++) {?>
						<tr>
							<td width="10%" style="border:1px solid;line-height: 20px;">&nbsp;<?=$buyerbank[$i]['cIdentity']?></td>
							<td width="30%" style="font-size:9px;border:1px solid;line-height: 20px;"><?=$buyerbank[$i]['bank']?></td>
							<td width="20%" style="border:1px solid;line-height: 20px;"><?=$buyerbank[$i]['cBankAccountNo']?></td>
							<td width="20%" style="font-size:9px;border:1px solid;line-height: 20px;"><?=$buyerbank[$i]['cBankAccountName']?></td>
							<td width="20%" style="border:1px solid;line-height: 20px;"><?=$buyerbank[$i]['cMoney']?></td>
						</tr>
				<?php	}

    ?>
				</table>
			</div>
			<?php }?>
			<div style="font-size:10px;width:100%" class="checkPage1">
				<div>其他注意事項</div>
				<?php
$itemNo = 1;
?>

				<?php if ($detail['bNote'] != 1): ?>
					<div><?=$itemNo++?>.本案業由買方已取回權狀及隨案謄本並結案。</div>

				<?php endif?>
				<div><?=$itemNo++?>.此證明書將做為第一建築經理股份有限公司辦理專戶價金結算及撥付之依據</div>
				<div><?=$itemNo++?>.本公司依財政部「電子發票實施作業要點」,電子發票於結案日後５日內開立完成,將不郵寄實體發票,請勾選:</div>
				<div style="padding-left:10px;">□我不需索取紙本電子發票,由第一建經託管並兌獎,中獎後由第一建經主動通知我領獎事宜。</div>
				<div style="padding-left:10px;">
					<div style="display:inline;float:left;">□捐贈「財團法人台灣兒童暨家庭扶助基金會」&nbsp;&nbsp;&nbsp;&nbsp;我要索取紙本電子發票 □同戶籍地址□同契約書連絡地址</div>
					<!-- <div style="width:30%;border-bottom:1px solid;display:inline;float:left;">&nbsp;</div> -->
				</div>
				<div style="clear:both;">
				<div style="padding-left:10px;">□指定地址:_______縣（市）_________鄉（鎮、市、區）________________路（街）____段</div>
				<div style="line-height: 20px;padding-left:20px;">____巷_____弄____號____樓之 ___。</div>
				<div style="padding-top:10px;">未勾選視為同意不索取紙本電子發票,台端簽名後即代表知悉上開通知內容,您可至本公司官網查詢發票內容。</div>
				<div><?=$itemNo?>.點交手續完成及上述事項確認無誤後，請於下方簽章處簽名蓋章：</div>
				<div style="width:30%;display:inline;float:left;height:200px;">買方簽章：</div>
				<div style="width:30%;display:inline;float:left;height:200px;">仲介方簽章：</div>
				<div style="width:30%;display:inline;float:left;height:200px;">地政士簽章：</div>
			</div>
			<div style="clear:both"></div>
			<?php
// $detail['bNote'] = 1;
/*20150505加入預售屋換約備註事項*/
if ($detail['bNote'] == 1) {?>
					<div class="checkPage1"><?=$itemNo++?>.※買賣雙方業於____年____月____日已向建設公司完成換約事宜，經買方確認無誤，請第一建築經理股份有限公司將履保專戶款項全數撥付至賣方指定帳戶。</div>

			<?php }
?>





		</div>
	</div>
	<div style="clear:both"></div>
	<div style="page-break-after:always;">&nbsp;</div>
	<div class="page">
		<div class="main">
			<div class="title checkPage2">
				<div class="title1">第一建築經理(股)公司</div>
				<div class="title2"><?=$detail['last_modify']?></div>

			</div>
			<div class="title_s checkPage2"><?=$title_txt?>(賣方)</div>
			<div style="width:95%;" class="checkPage1"><div style="border-bottom:double;">案件基本資料</div></div>
			<div style="clear:both;display:block;" class="checkPage2"></div>
			<div class="checkPage2">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<th width="18%" align="left">保證號碼：</th>
						<td width="32%" align="left"><?=$cCertifiedId?></td>
						<th width="18%" align="left">特約地政士：</th>
						<td width="32%" align="left"><?=$detail['bScrivener']?></td>
					</tr>
					<tr>
						<th width="18%" align="left">買方姓名：</th>
						<td width="32%" align="left"><?=$detail['bBuyer']?>&nbsp;&nbsp;<?=$detail['bBuyerId']?></td>
						<th width="18%" align="left">仲介店名：</th>
						<td width="32%" align="left"><?=$detail['bBrand']?></td>
					</tr>
					<tr>
						<th width="18%" align="left">賣方姓名：</th>
						<td width="32%" align="left"><?=$detail['bOwner']?>&nbsp;&nbsp;<?=$detail['bOwnerId']?></td>
						<th width="18%" align="left">&nbsp;</th>
						<td width="32%" align="left"><?=$detail['bStore']?></td>
					</tr>
					<tr>
						<th width="18%" align="left">買賣總金額：</th>
						<td width="32%" align="left"><?="$" . @number_format($detail['bTotalMoney']) . "元"?></td>
						<?php
if ($detail['cCompensation2'] > 0) {?>
								<th width="18%" align="left">專戶代償金額：</th>
								<td width="32%" align="left"><?="$" . @number_format($detail['cCompensation2']) . "元"?></td>
						<?php
} elseif ($detail['cCompensation3'] > 0 && $detail['cCompensation2'] <= 0) {?>
								<th width="18%" align="left">買方銀行代償：</th>
								<td width="32%" align="left"><?="$" . @number_format($detail['cCompensation3']) . "元"?></td>
					  <?php }?>
					</tr>

					<?php
if ($detail['cNotIntoMoney'] > 0) {?>
						<tr>
							<th width="18%" align="left">未入專戶：</th>
							<td width="32%" align="left"><?="$" . @number_format($detail['cNotIntoMoney']) . "元"?></td>
						</tr>
					<?php
}?>


					<?php
if ($detail['cCompensation2'] > 0 && $detail['cCompensation3'] > 0) {?>
						<tr>
							<th width="18%" align="left">買方銀行代償：</th>
							<td width="32%" align="left"><?="$" . @number_format($detail['cCompensation3']) . "元"?></td>
					<?php
if ($detail['cCompensation4'] == 0) {
    $detail['cCompensation4'] = $detail['cCompensation2'] + $detail['cCompensation3'];
}?>
						<th width="18%" align="left">代償總金額：</th>
						<td width="32%" align="left"><?="$" . @number_format($detail['cCompensation4']) . "元"?></td>

					</tr>
					<?php	}
for ($i = 0; $i < count($property_max); $i++) {
    $property[$i]['cAddr'] = $property[$i]['city'] . $property[$i]['area'] . $property[$i]['cAddr'];
    $property[$i]['cAddr'] = n_to_w($property[$i]['cAddr']);
    ?>
					<tr>
						<th width="18%" align="left">買賣標的物：</th>
						<td align="left" colspan="4"><?=$property[$i]['cAddr']?></td>

					</tr>
					<?php	}?>

					<tr>
						<td colspan="4">&nbsp;</td>
					</tr>

				</table>
			</div>
			<div class="checkPage2">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td colspan="6" style="border-bottom:double;">買賣價金收支明細</td>
					</tr>
					<tr>
						<th width="15%" align="center" style="border-bottom:double;">日期</th>
						<th width="18%" align="center" style="border-bottom:double;">摘要</th>
						<th width="14%" align="right" style="border-bottom:double;">收入/支出</th>
						<th width="14%" align="right" style="border-bottom:double;">金額</th>
						<th width="14%" align="right" style="border-bottom:double;">小計</th>
						<th width="30%" align="center" style="border-bottom:double;">備註</th>
					</tr>
					<tr>
						<td colspan="6">【專戶收款】</td>
					</tr>
					<?php
$income = 0;
for ($i = 0; $i < $max_owner; $i++) {
    $income += $trans_owner[$i]['oIncome'];

    $trans_owner[$i]['oRemark'] = n_to_w($trans_owner[$i]['oRemark']);
    $trans_owner[$i]['oRemark'] = preg_replace("/^＋/", "含", $trans_owner[$i]['oRemark']);
    ?>
						<tr>
							<td width="15%" align="center"><?=$trans_owner[$i]['oDate']?></td>
							<td width="18%" align="center"><?=$trans_owner[$i]['oKind']?></td>
							<td width="14%" align="right">收入</td>
							<td width="14%" align="right"><?=@number_format($trans_owner[$i]['oIncome'])?></td>
							<td width="14%" align="right">&nbsp;</td>
							<td width="30%" align="center"><?=$trans_owner[$i]['oRemark']?></td>
						</tr>
					<?php
}

$income += $detail['cInterest'];?>
					<?php if ($detail['cInterest'] > 0): ?>
						<tr>
							<td width="15%" align="center"></td>
							<td width="18%" align="center">利息</td>
							<td width="14%" align="right">收入</td>
							<td width="14%" align="right"><?=@number_format($detail['cInterest'])?></td>
							<td width="14%" align="right"><?=@number_format($income)?></td>
							<td width="30%" align="center">&nbsp;</td>
						</tr>
					<?php endif?>


				</table>
			</div>
			<div class="checkPage2">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
						<td colspan="6">【專戶出款】</td>
					</tr>
					<?php
$outgoing = 0;
for ($i = 0; $i < $max_owner_e; $i++) {
    $outgoing += $trans_owner_e[$i]['oExpense'];

    $trans_owner_e[$i]['oRemark'] = n_to_w($trans_owner_e[$i]['oRemark']);
    $trans_owner_e[$i]['oRemark'] = preg_replace("/^＋/", "含", $trans_owner_e[$i]['oRemark']);
    ?>
						<tr>
							<td width="15%" align="center"><?=$trans_owner_e[$i]['oDate']?></td>
							<td width="18%" align="center"><?=$trans_owner_e[$i]['oKind']?></td>
							<td width="14%" align="right">支出</td>
							<td width="14%" align="right"><?=@number_format($trans_owner_e[$i]['oExpense'])?></td>
							<td width="14%" align="right"><?=($max_owner_e > 0) ? @number_format($outgoing) : '&nbsp'?></td>
							<td width="30%" align="center"><?=$trans_owner_e[$i]['oRemark']?></td>
						</tr>

					<?php
}?>


					<tr>
						<td colspan="6">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="6" style="border-bottom:double;">待扣款項明細</td>
					</tr>
					<tr>
						<th colspan="2" align="left" style="border-bottom:double;">摘要</th>
						<th align="right" style="border-bottom:double;">金額</th>
						<th colspan="3" align="left" style="border-bottom:double; padding-left:30px;">備註</th>
					</tr>
					<!-- <tr>
						<td colspan="2" align="left" style="">*專戶餘額</td>
						<td align="right" style=""><?=@number_format($income - $outgoing)?></td>
						<td colspan="3" align="left" style=" padding-left:30px;"><?=$detail['balance_remark']?></td>
					</tr> -->
					<?php if ($detail['cRealestateBalance'] > 0): ?>
						<tr>
							<td colspan="2" align="left" style="">*應付仲介服務費餘額</td>
							<td align="right" style=""><?=@number_format($detail['cRealestateBalance'])?></td>
							<td colspan="3" align="left" style=" padding-left:30px;"><?=$detail['realty_remark']?></td>
						</tr>
					<?php endif?>

					<tr>
						<td colspan="2" align="left">*賣方應付履約保證費</td>
						<td align="right" ><?=@number_format($detail['cCertifiedMoney'])?></td>
						<td colspan="3" align="left" style="padding-left:30px;"><?=$detail['certify_remark']?></td>
					</tr>
					<?php
##
if ($detail['cCertifiedMoney2'] > 0) {?>
						<tr>
							<td colspan="2" align="left">*代扣買方履約保證費</td>
							<td align="right"><?=@number_format($detail['cCertifiedMoney2'])?></td>
							<td colspan="3" align="left" style="padding-left:30px;"><?=$detail['certify_remark2']?></td>
						</tr>

					<?php
}?>

					<tr>
						<td colspan="2" align="left">*應付代書費用及代支費</td>
						<td align="right"><?=@number_format($detail['cScrivenerMoney'])?></td>
						<td colspan="3" align="left" style="padding-left:30px;"><?=$detail['scrivener_remark']?></td>
					</tr>
					<?php	if ($detail['cNHITax'] > 0) { //代扣補充保費 ?>
						<tr>
							<td colspan="2" align="left">*代扣健保補充保費</td>
							<td align="right"><?=@number_format(round($detail['cNHITax']))?></td>
							<td colspan="3" align="left" style="padding-left:30px;">代賣方扣繳 2.11% 補充保費</td>
						</tr>

					<?php	}
//代扣所得稅
if ($detail['cTax'] > 0) {?>
						<tr>
							<td colspan="2" align="left">*<?=$detail['cTaxTitle']?></td>
							<td align="right"><?=@number_format(round($detail['cTax']))?></td>
							<td colspan="3" align="left" style="padding-left:30px;">
								<?php
if (preg_match("/[A-Za-z]{2}/", $detail['cOwnerId'])) { // 判別是否為外國人(兩碼英文字母者) 外國人20%
    echo '代賣方扣繳20% 利息所得稅';
} else {
    echo '代賣方扣繳10% 利息所得稅';
}
    ?></td>
						</tr>

					<?php	}
##賣方待扣款項明細它項
$other = 0;

for ($i = 0; $i < count($tax_owner); $i++) {?>
						<tr>
							<td colspan="2" align="left">*<?=$detail['cTaxTitle']?></td>
							<td align="right"><?=@number_format(round($tax_owner[$i]['cTax']))?></td>
							<td colspan="3" align="left" style="padding-left:30px;"><?=$tax_owner[$i]['cTaxRemark']?></td>
						</tr>

					<?php
$other = $other + $tax_owner[$i]['cTax']; //其他款項加總.
}
##
$_got_money = $income - $outgoing - $detail['cRealestateBalance'] - $detail['cCertifiedMoney'] - $detail['cScrivenerMoney'] - $detail['cTax'] - $detail['cNHITax'] - $other - $detail['cCertifiedMoney2'];

?>
					<tr>
						<td colspan="2" align="left">*賣方實收金額</td>
						<td align="right"><?=@number_format($_got_money)?></td>
						<td colspan="3" align="left" style="padding-left:30px;">委由第一建經撥入下列指定帳戶</td>
					</tr>
					<?php	if ($detail['other_remark']) {?>
						<tr>
							<td colspan="6"><?=$detail['other_remark']?></td>
						</tr>
					<?php	}
for ($i = 0; $i < count($remark_owner); $i++) {?>
						<tr>
							<td colspan="6"><?=$remark_owner[$i]['cRemark']?></td>
						</tr>

					<?php	}
?>
					<tr>
						<td colspan="6">&nbsp;</td>
					</tr>
				</table>
			</div>
			<div class="checkPage2">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td colspan="5" style="border-bottom:double;">指定收受價金之帳戶</td>
					</tr>
					<tr>
						<th width="10%" style="border-bottom:double;">對象</th>
						<th width="30%" style="border-bottom:double;">解匯行/分行</th>
						<th width="20%" style="border-bottom:double;">帳號</th>
						<th width="20%" style="border-bottom:double;">戶名</th>
						<th width="20%" style="border-bottom:double;">金額</th>
					</tr>
					<?php
for ($i = 0; $i < count($bankowner); $i++) {?>
							<tr>
								<td width="10%" style="border:1px solid;line-height: 30px;">&nbsp;<?=$bankowner[$i]['cIdentity']?></td>
								<td width="30%" style="font-size:9px;border:1px solid;line-height: 30px;"><?=$bankowner[$i]['bank']?></td>
								<td width="20%" style="border:1px solid;line-height: 30px;"><?=$bankowner[$i]['cBankAccountNo']?></td>
								<td width="20%" style="font-size:9px;border:1px solid;line-height: 30px;"><?=$bankowner[$i]['cBankAccountName']?></td>
								<td width="20%" style="border:1px solid;line-height: 30px;"><?=$bankowner[$i]['cMoney']?></td>
							</tr>
					<?php	}

?>
				</table>
			</div>

			<div style="font-size:10px;width:100%" class="checkPage2">
				<div>應注意事項</div>
				<?php
$itemNo = 1;
?>
				<div><?=$itemNo++?>.此證明書將做為第一建築經理股份有限公司辦理專戶價金結算及撥付之依據。</div>
				<div><?=$itemNo++?>.年度給付利息所得將依法開立扣繳憑單,將依法開立扣繳憑單;該所得非「儲蓄投資特別扣除額」之27萬免扣繳範圍</div>
				<div><?=$itemNo++?>.本公司依財政部「電子發票實施作業要點」,電子發票於結案日後５日內開立完成,將不郵寄實體發票,請勾選:</div>
				<div style="padding-left:10px;">□我不需索取紙本電子發票,由第一建經託管並兌獎,中獎後由第一建經主動通知我領獎事宜。</div>
				<div style="padding-left:10px;">
					<div style="display:inline;float:left;">□捐贈「財團法人台灣兒童暨家庭扶助基金會」&nbsp;&nbsp;&nbsp;&nbsp;我要索取紙本電子發票 □同戶籍地址□同買賣標的物地址</div>
					<!-- <div style="width:30%;border-bottom:1px solid;display:inline;float:left;">&nbsp;</div> -->
				</div>
				<div style="clear:both;">
				<div style="padding-left:10px;">□指定地址:_______縣（市）_________鄉（鎮、市、區）________________路（街）____段</div>
				<div style="line-height: 20px;padding-left:20px;">____巷_____弄____號____樓之 ___。</div>
				<div style="padding-top:10px;">未勾選視為同意不索取紙本電子發票,台端簽名後即代表知悉上開通知內容,您可至本公司官網查詢發票內容。</div>
				<div><?=$itemNo++?>.點交手續完成及上述事項確認無誤後，請於下方簽章處簽名蓋章：</div>
				<div style="width:30%;display:inline;float:left;height:170px;">賣方簽章：</div>
				<div style="width:30%;display:inline;float:left;height:170px;">仲介方簽章：</div>
				<div style="width:30%;display:inline;float:left;height:170px;">地政士簽章：</div>
			</div>
			<div style="clear:both"></div>
			<?php
// $detail['bNote'] = 1;
/*20150505加入預售屋換約備註事項*/
if ($detail['cNote'] == 1) {?>
					<div class="checkPage2"><?=$itemNo++?>.※買賣雙方業於____年____月____日已向建設公司完成換約事宜，經買方確認無誤，請第一建築經理股份有限公司將履保專戶款項全數撥付至賣方指定帳戶。</div>

			<?php }
?>
			<div class="checkPage2">
				中華民國 ________ 年 ________ 月 ________ 日　　聯絡電話：<?=$company['tel']?> Ext.<?=$undertaker['Ext']?>　　傳真電話：<?=$undertaker['FaxNum']?>
			</div>
		</div>
	</div>
</div>
</body>
</html>
<?php
#######################
//半形<=>全形
function n_to_w($strs, $types = '0')
{ // narrow to wide , or wide to narrow
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
        " ",
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
        "　",
    );

    if ($types == '0') { //半形轉全形
        // narrow to wide
        $strtmp = str_replace($nt, $wt, $strs);
    } else { //全形轉半形
        // wide to narrow
        $strtmp = str_replace($wt, $nt, $strs);
    }
    return $strtmp;
}
##

//遮蔽部分文數字
function newName($nameStr)
{
    for ($i = 0; $i < mb_strlen($nameStr, 'UTF-8'); $i++) {
        $arrName[$i] = mb_substr($nameStr, $i, 1, 'UTF-8');
        if (($i > 0) && ($i < (mb_strlen($nameStr, 'UTF-8') - 1))) {
            $arrName[$i] = 'Ｏ';
        }
    }
    return implode('', $arrName);
}
##

?>