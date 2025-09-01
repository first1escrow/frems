<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
include_once dirname(dirname(dirname(__FILE__))).'/session_check.php' ;

$member_id = $_SESSION['member_id'] ;

if (!preg_match("/^\d+$/", $member_id)) exit('失敗') ;

if (($member_id == '6') || ($member_id == '1')) $sql = 'SELECT * FROM tAppInform WHERE aProcessOK = "N" ORDER BY aId, aCreateTime ASC;' ;
else $sql = 'SELECT * FROM tAppInform WHERE aStaffId = "'.$member_id.'" AND aProcessOK = "N" ORDER BY aId, aCreateTime ASC;' ;

$rs = $conn->Execute($sql) ;
$list = array() ;
while (!$rs->EOF) {
	$list[] = $rs->fields ;
	$rs->MoveNext() ;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server"><meta http-equiv="cache-control" content="no-store"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>APP 通知事項</title>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="http://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script>
	$(function() {
		$("#dialog").dialog({
			width: 800,
			autoOpen: false
		});
		
		$('[name="acc"]').focus().select() ;
		
		$('#ttg').button({
			icon: "ui-icon-script"
		}) ;
		
		$('#rg').button({
			icon: "ui-icon-script"
		}) ;
		
		$('#ttg').click(function () {
			switchOff('on', 'T', '') ;
		}) ;
		
		$('#rg').click(function () {
			switchOff('on', 'R', '') ;
		}) ;
		
		$('.offline').button({
			icon: "ui-icon-power"
		}) ;
	}) ;
	
	function detail(no) {
		// alert(no) ;
		var url = 'getInformMsg.php' ;
		$.post(url, {'id':no}, function(txt) {
			$('#dialog').empty().html(txt) ;
			$("#dialog").dialog("open") ;
		}) ;
	}
	
	function checkOk(no) {
		// alert(no) ;
		var url = 'checkInformOk.php' ;
		$.post(url, {'id':no}, function(txt) {
			if (txt == 'ok') {
				$('#myform').submit() ;
			}
			else {
				alert('狀態更新失敗!!') ;
			}
		}) ;
	}
	
	function switchOff(s, ch, id) {
		// alert('s = ' + s + ',ch = '+ch+', id = ' + id) ;
		
		$.ajax({
			url: "annSwitch.php",
			data: "s=" + s + "&c=" + ch + "&i=" + id,
			type: "POST",
			dataType: "text",
			
			success: function(txt) {
				alert(txt) ;
				location.reload() ;
			},
			
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status) ;
				alert(thrownError) ;
			}
		}) ;
		
	}
	</script>
	<style>
		body {
			font-family:Microsoft JhengHei;
		}
		th {
			/*border-bottom-style: dotted;*/
			/*border-bottom-width: 2px;*/
			/*border-bottom-color: dotted;*/
			/*padding-top: 10px;*/
			padding: 5px;
			font-size: 14pt;
			font-weight: bold;
		}
		td {
			/*border-bottom-style: dotted;*/
			/*border-bottom-width: 2px;*/
			/*border-bottom-color: dotted;*/
			/*padding-top: 10px;*/
			padding: 5px;
		}
		b {
			color: #000088;
		}
		
		#block1 {
			/* support Safari, Chrome */
			-webkit-border-radius: 25px;
			/* support firefox */
			-moz-border-radius: 25px;
			border-radius: 25px;
		}
	</style>
</head>
<body>
	<form method="POST" id="myform">
	</form>
	<center>
		<div style="width:1100px;padding:10px;">
			<div id="block1" style="width:900px;border:1px solid #CCC;padding:20px;">
				<div style="width:800px;font-size:14pt;font-weight:bold;color:#000088;margin-bottom:10px;">
					APP 通知列表：
				</div>
				<table style="background-color:#BBFFEE;width:800px;">
					<tr>
						<th>&nbsp;</th><th>待處理事項</th><th>處理完成</th>
					</tr>
<?php
foreach ($list as $k => $v) {
	$cIndex = '' ;
	if ($k % 2 == 0) $cIndex = '#33FFDD' ;
	echo '
	<tr style="background-color:'.$cIndex.';">
		<td>'.($k + 1).'</td>
		<td><a href="#" onclick="detail(\''.$v['id'].'\')">'.$v['aTitle'].'</a></td>
		<td style="text-align:center;cursor:pointer;" onclick="checkOk(\''.$v['id'].'\')"><img src="/images/checklist_ok.png"></td>
	</tr>
	' ;
}
?>
				</table>
			</div>
		</div>
		<div id="dialog" style="width:800px;">
			
		</div>
	</center>
</body>
</html>
