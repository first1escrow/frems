<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../sms/sms_function.php';
// include_once 'sms_function.php';
$id = empty($_POST["id"]) 
        ? $_GET["id"]
        : $_POST["id"];

 $sid=$_GET['sid'];//地政士編號

// ####
// $id='7733';
// $sid='152';
// ####

$sql='
		SELECT
			 cr.cBranchNum,
			 SUBSTRING(ec.eDepAccount, -14) AS CertifiedId,
			 ec.eLender,
			 ec.eDebit,
			 ec.eTradeDate,
			 ec.id
		FROM
			tExpense_cheque AS ec
		INNER JOIN
			tContractCase AS cc
		ON
			cc.cCertifiedId=SUBSTRING(ec.eDepAccount,-9) 
		LEFT JOIN 
			tContractRealestate AS cr 
		ON 
			cr.cCertifyId=SUBSTRING(ec.eDepAccount,-9) 
		WHERE 
			ec.id='.$id;

	$rs = $conn->Execute($sql);
	
	$check_data=$rs->fields["id"];
	$sms = new SMS_Gateway();
 //判斷有沒有合約書
if(empty($check_data))
{
	  die("<script>alert('合約書尚未建立!!請先建立合約書後再發送簡訊');location.href='expense_cheque.php'</script>");
	
}

	

if(isset($_POST['id']))
{
	
	$sql='UPDATE tExpense_cheque SET eSms=1 WHERE id='.$id;

	$conn->Execute($sql);

	//  $sms->send( $rs->fields["CertifiedId"] , $sid, $rs->fields["cBranchNum"], 'cheque', $id, 'y', 0);
	//  // $sms->send('14碼保證號碼' , '地政士id', '仲介店id', 'cheque', 'tExpense_cheque id', 'n', 0);
	$sms->send( $rs->fields["CertifiedId"] , $sid, $rs->fields["cBranchNum"], 'cheque', $id, 'y', 0,$_POST['mobile'],$_POST['txt']);


	die("<script>alert('已發送簡訊');location.href='expense_cheque.php'</script>");
}
###
$array = $sms->send( $rs->fields["CertifiedId"] , $sid, $rs->fields["cBranchNum"], 'cheque', $id, 'n', 0);
				
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>支票通知發送</title>
<script src="/js/jquery-1.7.2.min.js"></script>
<!-- <script src="js/jquery.colorbox.js"></script> -->
<!-- <script type="text/javascript" src="js/jquery.highlight-3.js"></script> -->
<script type="text/javascript">
$(document).ready(function() {

 		var text = $('[name="text"]').val();

		// if(confirm(text))
		// {
		// 	$('form[name="ecform"]').submit();
		// }
		// else
		// {
		// 	 location.href='expense_cheque.php'
		// }


}) ;
function goSms(){
 	var tmp = new Array();
	$('input:checkbox:checked[name="mobile[]"]').each(function(i) { tmp[i] = this.value; });

	if (tmp.length > 0) {
		// console.log(tmp);
		$("[name='ecform']").submit();
	}

}

function checkAll(){
	

	$("[name='mobile[]']").each(function() {
			
		if ($("[name=all]").attr('checked')) {

			$(this).prop('checked',true);

		}else{
			$(this).prop('checked','');
		}
			
	});

	
}
</script>
<style>
	.tb{
		padding:5px;
		border: 1px solid #999;
		width:800px;
	}
	.tb td{
		border:1px solid #CCC;
		padding-left: 5px;
	}
	.tb th{
		border:1px solid #CCC;
		background-color: #E4BEB1;
	}
</style>
</head>
<body>
	<form name="ecform" method="POST">

		<input type="hidden" name="id" value="<?php echo $id; ?>">
			

			<div style="width:800px;">
				<textarea name="txt" cols="110" rows="5"><?=$array['txt']?></textarea>
			</div>
			<br />
			<table cellspacing="0" cellspacing="0" class="tb">
				
				<tr>
					<th><input type="checkbox" name="all" id="" onclick="checkAll()" checked="checked" /></th>
					<th>姓名</th>
					<th>電話</th>
				</tr>
				
				<?php
				

				foreach ($array['sms'] as $key => $value) { ?>
					<tr>
						<td align="center"><input type="checkbox" name="mobile[]" value="<?=$value['mMobile']?>" checked="checked"/></td>
						<td><?=$value['mName']?></td>
						<td><?=$value['mMobile']?></td>
					</tr>
				<?php }
				?>
				<tr>
					<td colspan="3" align="center">
						<input type="button" value="送出" onclick="goSms()" />
					</td>
				</tr>
			</table>
		
		
		
	</form>
</table>
</body>
</html>
