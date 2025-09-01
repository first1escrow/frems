<?php
include_once '../session_check.php' ;
include_once '../openadodb.php' ;

$_REQUEST = escapeStr($_REQUEST) ;
$_POST = escapeStr($_POST) ;
$sn = trim($_REQUEST['sn']) ;
$tf = false ;

if ($_POST['save'] == 'ok') {
	$sql = '' ;
	$detail = array() ;
	
	$detail = $_POST ;
	//print_r($detail) ; exit ;
	$detail['cInvoiceDate'] = str_replace('-','/',$detail['cInvoiceDate']) ;
	
	if ($detail['cAcc'] && $detail['cPass']) $sql .= ' cAcc="'.$detail['cAcc'].'", cPass="'.$detail['cPass'].'", ' ;
	
	
	$sql = '
		UPDATE
			tContractInvoiceQuery
		SET
			cQuery="'.$detail['cQuery'].'",
			cInvoiceNo="'.$detail['cInvoiceNo'].'",
			cInvoiceDate="'.$detail['cInvoiceDate'].'",
			cMoney="'.$detail['cMoney'].'",
			cName="'.$detail['cName'].'",
			cCode = "'.$detail['cCode'].'",
			'.$sql.'
			cIdentifyId="'.$detail['cIdentifyId'].'",
			cLastModify="'.$_SESSION['member_name'].'"
		WHERE
			cId="'.$detail['sn'].'";' ;
	//echo $sql ; exit ;
	
	if ($conn->Execute($sql)) $tf = true ;

	setPrint($conn,$detail['sn'],$detail['cPrint']);
}

$list = array() ;
$sql = 'SELECT * FROM tContractInvoiceQuery WHERE cId="'.$sn.'";' ;
$rs = $conn->Execute($sql) ;
if (!$rs->EOF) {
	
	$rs->fields['cPrint'] =inv_print($conn,$rs->fields['cTB'],$rs->fields['cTargetId']);

	$list = $rs->fields ;


}

$chg = false ;
if (preg_match("/^tContractRealestate/is",$list['cTB'])) $chg = true ;

if ($list['cInvoiceDate']) {
	$list['cInvoiceDate'] = str_replace('/','-',$list['cInvoiceDate']) ;
}
//print_r($list) ;

function setPrint($conn,$iq_id,$p) //20150917++
{
	$sql = 'SELECT * FROM tContractInvoiceQuery WHERE cId="'.$iq_id.'";' ;
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);
	$tb = $rs->fields['cTB'];
	$id = $rs->fields['cTargetId'];


	// echo $tb.$id;
	if ($tb =='tContractRealestate_R' || $tb =='tContractRealestate_R1' || $tb =='tContractRealestate_R2') {

		$tmp = explode('_', $tb);

		// // $sql = "SELECT * FROM ".$tmp[0]." WHERE cId = '".$id."' ";


		// $rs = $conn->Execute($sql);
		
 
		if ($tmp[1] == 'R') {
			$sql = "UPDATE ".$tmp[0]." SET cInvoicePrint ='".$p."' WHERE cId ='".$id."'";


		
		}elseif ($tmp[1] == 'R1') {
			$sql = "UPDATE ".$tmp[0]." SET cInvoicePrint1 ='".$p."' WHERE cId ='".$id."'";


		}elseif ($tmp[1] == 'R2') {

			$sql = "UPDATE ".$tmp[0]." SET cInvoicePrint2 ='".$p."' WHERE cId ='".$id."'";

		}

		// $conn->Execute($sql);

	}else{
		$tmp = explode('_', $tb);

		// $sql = "SELECT * FROM ".$tmp[0]." WHERE cId ='".$id."'";

		$sql = "UPDATE ".$tmp[0]."  SET cInvoicePrint ='".$p."' WHERE cId ='".$id."'";
		// echo $sql;

		 // $conn->Execute($sql);

	
		
	}

	// echo $sql;
	$conn->Execute($sql);
	// return $data;
}

function inv_print($conn,$tb,$id) //20150917++
{
	// echo $tb.$id;
	if ($tb =='tContractRealestate_R' || $tb =='tContractRealestate_R1' || $tb =='tContractRealestate_R2') {

		$tmp = explode('_', $tb);

		$sql = "SELECT * FROM ".$tmp[0]." WHERE cId = '".$id."' ";

		$rs = $conn->Execute($sql);
		
 
		if ($tmp[1] == 'R') {
			$data = $rs->fields['cInvoicePrint'];

		

		}elseif ($tmp[1] == 'R1') {
			$data = $rs->fields['cInvoicePrint1'];

		

		}elseif ($tmp[1] == 'R2') {

			$data = $rs->fields['cInvoicePrint2'];

		}

	}else{
		$tmp = explode('_', $tb);

		$sql = "SELECT * FROM ".$tmp[0]." WHERE cId ='".$id."'";

		// echo $sql;

		$rs = $conn->Execute($sql);

	
		$data = $rs->fields['cInvoicePrint'];
	}


	return $data;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>電子發票明細</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="/js/ROCcalender_limit.js"></script>
<script type="text/javascript">
$(document).ready(function(){
<?php
if ($tf) echo 'alert("更新成功!!") ;' ;
?>
}) ;

function checkValid() {
	$('[name="save"]').val('ok') ;
	$('#updateform').submit() ;
}
</script>
<style>
td {
	padding: 5px;
}
</style>
</head>
<body>
	<form name="updateform" id="updateform" method="POST" enctype="multipart/form-data">
	<center>
		<div style="padding-bottom:20px;"></div>
		<table cellpadding="0" cellspacing="0">
			<tr style="background-color:#FCEEEE;">
				<td style="width:80px;">可否查詢：</td>
				<td style="width:180px;" >
					<input type="radio" name="cQuery" <?php if ($list['cQuery'] == 'Y') echo 'checked="checked"' ?>value="Y">&nbsp;是
					<input type="radio" name="cQuery" <?php if ($list['cQuery'] == 'N') echo 'checked="checked"' ?>value="N">&nbsp;否
				</td>
				<td style="width:80px;">列印發票：</td>
				<td colspan="3">
					<input type="radio" name="cPrint" <?php if ($list['cPrint'] == 'Y') echo 'checked="checked"' ?>value="Y"/>&nbsp;是
					<input type="radio" name="cPrint" <?php if ($list['cPrint'] == 'N') echo 'checked="checked"' ?>value="N">&nbsp;否 
				</td>
			</tr>
			<tr style="background-color:;">
				<td style="width:80px;">保證號碼：</td>	<td style="width:180px;"><input type="text" name="cCertifiedId" disabled="disabled" value="<?=$list['cCertifiedId']?>" style="width:120px;"></td>
				<td style="width:80px;">索引代碼：</td><td colspan="3"><input type="text" name="cDefineFields" disabled="disabled" value="<?=$list['cDefineFields']?>" style="width:180px;"></td>
			</tr>
			<tr style="background-color:#FCEEEE;">
				<td style="width:80px;">發票號碼：</td>	<td style="width:180px;"><input type="text" name="cInvoiceNo" value="<?=$list['cInvoiceNo']?>" style="width:120px;"></td>
				<td style="width:80px;">發票日期：</td><td style="width:180px;"><input type="text" name="cInvoiceDate" value="<?=$list['cInvoiceDate']?>" style="width:120px;" onclick="show_calendar('updateform.cInvoiceDate')"></td>
				<td style="width:80px;">發票金額：</td><td style="width:180px;"><input type="text" name="cMoney" value="<?=$list['cMoney']?>" style="width:120px;text-align:right;"></td>
			</tr>
			<tr style="background-color:;">
				<td style="width:80px;">發票抬頭：</td>	<td style="width:180px;"><input type="text" name="cName" value="<?=$list['cName']?>" style="width:120px;"></td>
				<td style="width:80px;">統一編號：</td><td style="width:180px;"><input type="text" name="cIdentifyId" value="<?=$list['cIdentifyId']?>" style="width:120px;"></td>
				<td style="width:80px;">隨機碼：</td><td style="width:180px;"><input type="text" name="cCode" value="<?=$list['cCode']?>"></td>
			</tr>
			<tr style="background-color:#FCEEEE;">
				<td style="width:80px;">查詢帳號：</td>	<td style="width:180px;"><input type="text" name="cAcc" <?php if($chg) echo 'disabled="disabled" ' ; ?>value="<?=$list['cAcc']?>" style="width:120px;"></td>
				<td style="width:80px;">查詢密碼：</td><td style="width:180px;"><input type="text" name="cPass" <?php if($chg) echo 'disabled="disabled" ' ; ?>value="<?=$list['cPass']?>" style="width:120px;"></td>
				<td style="width:80px;">&nbsp;</td><td style="width:180px;">&nbsp;</td>
			</tr>
		</table>
		<div style="margin-top: 20px;">
			<input type="hidden" name="save">
			<input type="hidden" name="sn" value="<?=$sn?>">
			<input type="button" style="padding:5px;" value="更新" onclick="checkValid()">
			<input type="button" style="padding:5px;" value="返回" onclick="parent.$.colorbox.close()">
		</div>
	</center>
	</form>
</body>
</html>