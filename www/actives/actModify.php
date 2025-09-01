<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;

$op = $_POST['op'] ;
$sn = addslashes(trim($_POST['sn'])) ;
$v = $_POST ;
$act = addslashes(trim($_POST['act']));


if ($act == 'mod') {

	foreach ($_POST as $k => $v) {
		
		$data[$k] = addslashes(trim($v));
	}

	$sql = "UPDATE 
				tRegistOnline 
			SET 
				rField = '".$data['rField']."',
				rName = '".$data['rName']."',
				rIdentity = '".$data['rIdentity']."',
				rUnit = '".$data['rUnit']."',
				rNo = '".$data['rNo']."',
				rMobile = '".$data['rMobile']."',
				rEmail = '".$data['rEmail']."',
				rField = '".$data['rField']."',
				rMemo = '".$data['rMemo']."'
			WHERE 
				rId ='".$sn."'";

	// echo $sql;
	$conn->Execute($sql);
			
}



//
$list = array() ;
$sql = "SELECT 
			*,
			(SELECT nSubject FROM tNews WHERE nId = rActivity) AS nSubject, 
			(SELECT nField FROM tNews WHERE nId = rActivity) AS nField
		FROM 
			tRegistOnline WHERE rId ='".$sn."'";

$rs = $conn->Execute($sql);

$list = $rs->fields;
$ff = explode(',',$list['nField']);
foreach ($ff as $k => $v) {
		$items = explode(':',$v) ;
		$fields[$k]['field'] = $items[0] ;
		$fields[$k]['maxPeople'] = $items[1] ;
		$sql = 'SELECT SUM(rNo) as total FROM tRegistOnline WHERE rActivity="'.$list['rActivity'].'" AND rField="'.$list['rField'].'";' ;
		
		$rs = $conn->Execute($sql) ;
		
		
		$fields[$k]['currentPeople'] = $rs->fields['total'] ;
		unset($tmp) ;
	}

if ($act =='del') {
	
	$sql = "DELETE FROM tRegistOnline WHERE rId ='".$sn."'";

	$conn->Execute($sql);

	$script = "goback();";

	
}
##
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>報名人員資料修改</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="/js/ROCcalender.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	<?=$script?>
	$('#dialog').dialog({
		autoOpen: false,
		width: 260,
		height: 298
	}) ;
	
	$('#dialog1').dialog({
		autoOpen: false,
		width: 300,
		height: 280
	}) ;
	
}) ;
function update()
{
	$('[name="act"]').val('mod') ;
	$('#myform').submit() ;
}
function del() {
	if (confirm('是否確定刪除此筆資料!?') == true) {
		$('[name="act"]').val('del') ;
		$('#myform').submit() ;
	}
	else {
		return false ;
	}
}

function goback() {
	var url = 'actMemberList.php' ;
	
	$('#myform').prop('action',url) ;
	$("[name='sn']").val("<?=$list['rActivity'].'_'.$list['rField']?>") ;
	$('#myform').submit() ;
}


</script>
<style>
input {
	/* background-color: #FFFFFD ;*/
}

body {
	/*font-family: 標楷體;
	font-size: 11pt;*/
	font-family: "微軟正黑體", serif;
}

td,th{
	padding:5px;
}

</style>
</head>
<body style="background-color:#F8ECE9;">
<center>
<h2>報名修改</h2>

<form method="POST" name="myform" id="myform" enctype="multipart/form-data">
	<input type="hidden" name="sn" value="<?=$sn?>">
	<input type="hidden" name="act" value="">
	<div style="margin-top:20px;">&nbsp;</div>

	<table cellpadding="0" cellspacing="0" border="1">
		<tr>
			<th>課程名稱：</th>
			<td><input type="text" value="<?=$list['nSubject']?>" disabled="disabled" class="xxx-input" ></td>
		</tr>
		<tr>
			<th>場次：</th>
			<td>
				<select name="rField" class="xxx-select">
					<?php
					foreach ($fields as $k => $v) {
						if ($v['field'] == $list['rField']) {
							$select = 'selected=selected';
						}else{
							$select = '';
						}
						echo '<option value="'.$v['field'].'" '.$select.'>'.$v['field'].'('.number_format($v['currentPeople']).'/'.number_format($v['maxPeople']).')'."</option>\n" ;
					}
					?>
					</select>
			</td>
		</tr>
		<tr>
			<th>姓名：</th>
			<td><input type="text" value="<?=$list['rName']?>" name="rName"  class="xxx-input" ></td>
		</tr>
		<tr>
			<th>身份：</th>
			<td>
				<?php
					if ($list['rIdentity'] == 1) {
						$ck1 = 'checked=checked';
					}elseif ($list['rIdentity'] == 2) {
						$ck2 = 'checked=checked';
					}elseif ($list['rIdentity'] == 3) {
						$ck3 = 'checked=checked';
					}
				?>
					<input type="radio" name="rIdentity" id="" value="1" <?=$ck1?>/>一般人士
					<input type="radio" name="rIdentity" id="" value="2" <?=$ck2?>/>地政士
					<input type="radio" name="rIdentity" id="" value="3" <?=$ck3?>/>經紀業
				
			</td>
		</tr>
		<tr>
			<th>單位：</th>
			<td><input type="text" value="<?=$list['rUnit']?>" name="rUnit" class="xxx-input" ></td>
		</tr>
		<tr>
			<th>參加人數：</th>
			<td><input type="text" value="<?=$list['rNo']?>" name="rNo"  class="xxx-input" ></td>
		</tr>
		<tr>
			<th>連絡電話(市話)：</th>
			<td><input type="text" value="<?=$list['rTel1']?>" name="rTel1" class="xxx-input" ></td>
		</tr>
		<tr>
			<th>連絡電話(手機)：</th>
			<td><input type="text" value="<?=$list['rMobile']?>" name="rMobile" class="xxx-input" ></td>
		</tr>
		<tr>
			<th>E-Mail：</th>
			<td><input type="text" value="<?=$list['rEmail']?>" name="rEmail" class="xxx-input" ></td>
		</tr>
		<tr>
			<th>連絡地址：</th>
			<td><input type="text" value="<?=$list['rAddr']?>" name="rAddr" class="xxx-input" ></td>
		</tr>
		<tr>
			<th>備註：</th>
			<td><input type="text" value="<?=$list['rMemo']?>" name="rMemo" class="xxx-input" ></td>
		</tr>
	</table>
		
	<div style="width:100%;margin-top:20px;">
		<center>
			<input type="button" value="修改" onclick="update()">
			<input type="button" value="刪除" onclick="del()">
			<input type="button" value="列表" onclick="goback()">
		</center>
	</div>
	
</form>

</center>
</body>
</html>
