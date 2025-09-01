<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>簡訊狀態列表</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>-->
<script type="text/javascript" src="../libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="/js/datepickerRoc.js"></script>
<script type="text/javascript" src="../js/ROCcalender_limit.js"></script>
<script type="text/javascript" src="../js/rocCal.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	window.resizeTo('1200','800') ;
	
	$('#dialog').dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		buttons: {
			OK: function() {
				$(this).dialog('close') ;
			}
		}
	});

}) ;

function clear_search_bar1() {
	$('[name="ng_certifiedid"]').val('') ;
	$('[name="ng_mobile"]').val('') ;
	$('[name="ng_start_date"]').val('') ;
	$('[name="ng_end_date"]').val('') ;
}
function clear_search_bar2() {
	$('[name="ok_certifiedid"]').val('') ;
	$('[name="ok_mobile"]').val('') ;
	$('[name="ok_start_date"]').val('') ;
	$('[name="ok_end_date"]').val('') ;
}
function msgId(no) {
	var v = $('#msgVendor'+no).val() ;
	var k = $('#msgKey'+no).val() ;
	var m = $('#msgMobile'+no).val() ;
	
	$.post('fet_api_query.php',{'qMobile':m,'qId':k},function(txt) {
		$('#dialog').html(txt) ;
		$('#dialog').dialog('open') ;
	}) ;
	
	/*
	var str = '簡訊系統：' + v + '<hr>門號：' + m + '<br>Message Id：' + k ;
	
	$('#dialog').html(str) ;
	$('#dialog').dialog('open') ;
	*/
}

function setConfirm(tId){
	$.ajax({
		url: 'setSmsErrorComfirm.php',
		type: 'POST',
		dataType: 'html',
		data: {tId: tId},
	})
	.done(function(msg) {
		// console.log(msg);

		alert(msg);
		location.href="sms_list.php?ch=ff";
	});
	
}
</script>
<style>
</style>
</head>

<body>
<form method="POST" name="FET_SMS_Query" target="_blank" action="fet_api_query.php">

</form>
<form method="POST" name="search_bar1" action="sms_list.php">
<div style="width:1254px; margin-bottom:5px;padding-top:10px;padding-left:10px; height:30px; background-color: #CCC">
<strong style="color:#002060;">
<{if $_ch == 'ff'}>
<font color="red">未處理失敗簡訊名單</font>
	

<{else}>
	簡訊狀態查詢列表
<{/if}>

</strong>　　

<input type="hidden" name="ng" value="1">
<span style="font-size:12px;color:#000080;">保證號碼：<input type="text" name="ng_certifiedid" style="width:150px;" value="<{$ng_certifiedid}>"></span>　
<span style="font-size:12px;color:#000080;">門號：<input type="text" name="ng_mobile" style="width:150px;" value="<{$ng_mobile}>"></span>　
<span style="font-size:12px;color:#000080;">日期：</span>
<span style="font-size:12px;color:#000080;">起</span>&nbsp;
<input type="text" name="ng_start_date" style="width:80px;" value="<{$ng_start_date}>" class="datepickerROC">&nbsp;
<span style="font-size:12px;color:#000080;">迄</span>&nbsp;
<input type="text" name="ng_end_date" style="width:80px;" value="<{$ng_end_date}>" class="datepickerROC">&nbsp;
<input type="button" value="搜尋" onclick="search_bar1.submit()">&nbsp;
<input type="button" value="清除" onclick="clear_search_bar1()">


<a href="sms_list.php?ch=ff">未處理名單</a>

</div>
</form>

<div id="frame1">
<table border="1" cellpadding="1" cellspacing="1" style="width:1254px;">
	<tr style="text-align:center;font-weight:bold;">
		<td style="width:40px;">序號</td>
		<td style="width:80px;">保證號碼</td>
		<td style="width:40px;">類別</td>
		<td style="width:180px;">目前狀態</td>
		<td style="width:80px;">發送對象</td>
		<td style="width:80px;">發送時間</td>
		<td>簡訊內容</td>
		<!--<td>刪除顯示</td>-->
		<!--<td>重發簡訊</td>-->
		<td style="width:80px;">經辦人員</td>
		<{if $_ch == 'ff'}>
		<td>&nbsp;</td>
		<{/if}>
	</tr>     

	<{foreach from=$sms_list key=key item=item}>
	<tr align='center' style='font-size:10pt;'>   
		<td style='<{$finger}>' onclick='msgId("<{$key}>")'>
			<input type='hidden' id='msgKey<{$key}>' value='<{$item.tTaskID}>'>
			<input type='hidden' id='msgMobile<{$key}>' value='<{$item.tTo}>'>
			<input type='hidden' id='msgVendor<{$key}>' value='<{$item.tSystem}>'>
			<span style='<{$item.style}>'><{$key}></span>&nbsp;
		</td>
		<td><{$item.tPID}><br>(<{$item.tSystem}>)&nbsp;</td>
		<td><{$item.tKind}>&nbsp;</td>
		<td><{$item.tReason}>&nbsp;</td>
		<td><{$item.tName}><br><{$item.tTo}>&nbsp;</td>
		<td><{$item.sSend_Time}></td>
		<td style="text-align:left;"><{$item.tSMS}></td>
		<td><{$item.staff}></td>
		<{if $_ch == 'ff'}>
		<td><input type="button" value="已處理" onclick="setConfirm('<{$item.tTaskID}>')"></td>
		<{/if}>
	</tr>
	<{/foreach}>



	

  </table>
</div>
<div style="height:20px;">
</div>
<div id="dialog" title="簡訊進階查詢"></div>
</body>
</html>
