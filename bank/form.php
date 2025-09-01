<?php
#顯示錯誤
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';

$_GET = escapeStr($_GET);

$formData = array();

// $sql  = "SELECT *,CONCAT('SC',LPAD(bSID,4,'0')) code,(SELECT sName FROM tScrivener WHERE sId = bSID) as scrivener,(SELECT bName FROM tBrand WHERE bId = bBrand) As brand,(SELECT pName FROM tPeopleInfo WHERE pId = bApplicant) AS Applicant,(SELECT CONCAT(cBankName, cBranchName) FROM tContractBank WHERE cId = bBank) AS bank FROM tBankCodeForm2 WHERE bNo = '" . $_GET['no'] . "'";
$sql = "SELECT
	a.*,
	CONCAT('SC', LPAD(a.bSID,4,'0')) code,
	b.sName AS scrivener,
	b.sRemark5 AS remark,
	(SELECT bName FROM tBrand WHERE bId = a.bBrand) AS brand,
	(SELECT pName FROM tPeopleInfo WHERE pId = a.bApplicant) AS Applicant,
	(SELECT CONCAT(cBankName, cBranchName) FROM tContractBank WHERE cId = a.bBank) AS bank
FROM
	tBankCodeForm2 AS a
JOIN
	tScrivener AS b ON a.bSID = b.sId
WHERE
	bNo = '" . $_GET['no'] . "';";
$rs   = $conn->Execute($sql);
$data = $rs->fields;
if (!$rs->EOF) {

    $formData['scrivener'] = $data['scrivener'];
    $formData['remark']    = $data['remark'];
    $formData['brand']     = $data['brand'];
    $formData['code']      = $data['code'];
    $formData['applicant'] = $data['Applicant'];
    $formData['no']        = $data['bNo'];
    $formData['bank']      = $data['bank'];
    // $sql = "SELECT bId,bApplication,CONCAT('SC',LPAD(bSID,4,'0')) code,(SELECT sName FROM tScrivener WHERE sId = bSID) as scrivener,(SELECT bName FROM tBrand WHERE bId = bBrand)  As brand,bCategory,(SELECT pName FROM tPeopleInfo WHERE pId = bApplicant) AS Applicant  FROM tBankCodeForm WHERE bSID = '".$sId."' AND bDate = '".$date."' AND bBrand = '".$brand."' AND bCategory = '".$category."'";

    if ($data['bCategory'] == 1) { //仲介類型(1加盟2直營3非仲介)
        $formData['category'] = '加盟';
    } elseif ($data['bCategory'] == 2) {
        $formData['category'] = '直營';
    } elseif ($data['bCategory'] == 3) {
        $formData['category'] = '非仲介成交';
    }

    if ($datas['bApplication'] == 1) { //仲介類型(用途(1土地2建物3預售屋))
        $formData['app'] = '土地';
    } elseif ($datas['bApplication'] == 2) {
        $formData['app'] = '建物';
    } elseif ($datas['bApplication'] == 3) {
        $formData['app'] = '預售屋';
    }

    // if (preg_match("/60001/", $data['bAccount'])) {
    //       $formData['bank'] = '一銀';
    //    }elseif (preg_match("/9998[5|6]/", $data['bAccount'])) {
    //         $formData['bank'] = '永豐';
    //    }elseif (preg_match("/96988/", $data['bAccount'])) {
    //         $formData['bank'] = '台新';
    //    }

    $sql = "SELECT * FROM tBankCode WHERE bFormNo2 = '" . $data['bId'] . "'";
    $rs  = $conn->Execute($sql);
    while (!$rs->EOF) {

        // echo $formData['bank'];

        $data[$rs->fields['bApplication']]['account'][] = $rs->fields['bAccount'];

        $rs->MoveNext();
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0041)http://first2.twhg.com.tw/bank/create.php -->
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<meta http-equiv="X-UA-Compatible" content="IE=9">
<title>申請專屬帳號</title>
<!-- <link type="text/css" href="../css/jquery-ui-1.8.21.custom.css" rel="stylesheet"> -->
<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
<!-- <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script> -->
<!-- <script type="text/javascript" src='codebase/message.js'></script> -->
<!-- <script type="text/javascript" src="../js/combobox.js"></script> -->
<!-- <link rel="stylesheet" type="text/css" href="codebase/themes/message_default.css"> -->
<style>

#showB td {
	padding-left: 5px;
}
body{
		font-size: 20px;
}
.num{
	font-size: 29px;
	letter-spacing:3px;
	border: 1px solid #000;
}

@media page{
	size: auto;   /* auto is the initial value */
    margin: 5%;
}
@media print{


	.tb{
		width: 100%;
	}

	.num{
		/*font-size: 25px;*/
	}
}

@media screen{
	.tb{
		width: 600px;
	}
}

</style>
<script>
$(document).ready(function() {


});
function showApplyFrom(no){
	window.open('form.php?no='+no, '_blank', config='height=700,width=650,scrollbars=yes');
}
</script>
</head>

<body>
<center>
<p>
  </p>
<div id="showB">

</div>
<!-- <a href="#" onclick="showApplyFrom('20201202-002')">AAAA</a> -->
<div class="block">
	<table border="0" cellpadding="10" cellspacing="10" class="tb">
		<tbody>
		<tr>
			<td colspan="3">申請單編號:<?=$formData['no']?></td>
		</tr>
		<tr>
			<td colspan="3" style="border:1px dotted #333333;">
				<div class="title">
					<div>本次產生保證號碼資訊如下：</div>
					<hr style="border:1px dotted #CCCCCC;">
					<div>保證號碼專屬地政士：<?=$formData['code'] . $formData['scrivener']?></div>
					<div>銀行系統：<?=$formData['bank']?></div>
					<!-- <div>合約版本：新版</div> -->
					<div>合約版本：<?=$formData['brand']?></div>
					<div>仲介類型：<?=$formData['category']?></div>
					<div>注意事項：<?=nl2br($formData['remark'])?></div>
				</div>
			</td>
		</tr>
		<tr>
			<td width="33%" valign="top" style="border:1px dotted #333333;">
			<div style="text-align:center;">土地(<?=count($data[1]['account'])?>組)</div>
			<hr style="border:1px dotted #CCCCCC;">

			<?php if (count($data[1]['account']) > 0): ?>
				<?php foreach ($data[1]['account'] as $key => $value): ?>
	                <div class="num"><?=$value?></div>
	                <div class="num">&nbsp;</div>
	                <div >&nbsp;</div>
	            <?php endforeach?>
			<?php endif?>



			</td>
			<td width="33%" valign="top" style="border:1px dotted #333333;">
			<div style="text-align:center;">建物(<?=count($data[2]['account'])?>組)</div>
			<hr style="border:1px dotted #CCCCCC;">
			<?php if (count($data[2]['account']) > 0): ?>
				<?php foreach ($data[2]['account'] as $key => $value): ?>
	                <div class="num"><?=$value?></div>
	                <div class="num">&nbsp;</div>
	                <div >&nbsp;</div>
	            <?php endforeach?>
			<?php endif?>

			</td>



			<td width="34%" valign="top" style="border:1px dotted #333333;">
				<div style="text-align:center;">預售屋(<?=count($data[3]['account'])?>組)</div>
				<hr style="border:1px dotted #CCCCCC;">
				<?php if (count($data[3]['account']) > 0): ?>
				<?php foreach ($data[3]['account'] as $key => $value): ?>
	                <div class="num"><?=$value?></div>
	                <div class="num">&nbsp;</div>
	                <div >&nbsp;</div>
	            <?php endforeach?>

			</td>
			<?php endif?>
		</tr>
		<tr>
			<td>
				<b>申請人：<?=$formData['applicant']?></b>


			</td>
			<td colspan="2"><b>出貨人：</b></td>
		</tr>
		<tr >
			<td colspan="3" style="padding-top: 50px;">
				<table cellpadding="0" cellspacing="0" width="100%" style="line-height: 20px;">
					<tr>
						<td><b>覆核1版本檢查：</b></td>
						<td><b>製作：</b></td>
						<td><b>覆核2編號檢查：</b></td>

					</tr>
				</table>
				<span ></span>

			</td>

		</tr>
	</tbody></table>

</div>




</center>



</body></html>