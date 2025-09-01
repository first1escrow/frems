<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<script type="text/javascript">
$(document).ready(function() {
	var sh = $('[name="show_hide"]').val() ;
	if (sh!='hide') {
		$('#small_table').removeClass('tbl_hide') ;
		$('#a_tag').html('－') ;
	}
}) ;

function post_data(url,cp) {
	var url = 'applycase_result.php' ;
	
	var bk = $('[name="bank"]').val() ;
	var sad = $('[name="sApplyDate"]').val() ;
	var ead = $('[name="eApplyDate"]').val() ;
	var sed = $('[name="sEndDate"]').val() ;
	var eed = $('[name="eEndDate"]').val() ;
	var ssd = $('[name="sSignDate"]').val() ;
	var esd = $('[name="eSignDate"]').val() ;
	var br = $('[name="branch"]').val() ;
	var sc = $('[name="scrivener"]').val() ;
	var zp = $('[name="zip"]').val() ;
	var ct = $('[name="citys"]').val() ;
	var brzp = $('[name="branchZip"]').val() ;
	var brct = $('[name="branchCitys"]').val() ;
	var sczp = $('[name="scrivenerZip"]').val() ;
	var scct = $('[name="scrivenerCitys"]').val() ;
	var bd = $('[name="brand"]').val() ;
	var ut = $('[name="undertaker"]').val() ;
	var st = $('[name="status"]').val() ;
	var es = $('[name="realestate"]').val() ;
	var cid = $('[name="cCertifiedId"]').val() ;
	var byr = $('[name="buyer"]').val() ;
	var owr = $('[name="owner"]').val() ;
	var sales  = $('[name="sales"]').val() ;
	var sales_performance = $('[name="sales_performance"]').val();

	
	var tp = parseInt($('[name="total_page"]').val()) ;
	var rl = $('[name="record_limit"]').val() ;
	
	$.post(url,
		{'bank':bk,'sApplyDate':sad,'eApplyDate':ead,'sEndDate':sed,'eEndDate':eed,'sSignDate':ssd,'eSignDate':esd,'branch':br,
		'scrivener':sc,'zip':zp,'citys':ct,'branchZip':brzp,'branchCitys':brct,'scrivenerZip':sczp,'scrivenerCitys':scct,'brand':bd,'undertaker':ut,'status':st,'realestate':es,'cCertifiedId':cid,
		'buyer':byr,'owner':owr,'total_page':tp,'record_limit':rl,'current_page':cp,'sales':sales,'sales':sales},
		function(txt) {
			$('#container').html(txt) ;
	}) ;
}

function first() {
	var current_page = parseInt($('[name="current_page"]').val()) ;

	if (current_page <= 1) { return false ; }
	else { current_page = 1 ; }

	post_data('applycase_result.php',current_page) ;
}
function back() {
	var current_page = parseInt($('[name="current_page"]').val()) - 1 ;

	if (current_page <= 0) { return false ; }

	post_data('applycase_result.php',current_page) ;
}
function next() {
	var current_page = parseInt($('[name="current_page"]').val()) + 1 ;
	var total_page = parseInt($('[name="total_page"]').val()) ;
	
	if (current_page > total_page) { return false ; }

	post_data('applycase_result.php',current_page) ;
}
function last() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { return false ; }
	else { current_page = total_page ; }
	
	post_data('applycase_result.php',current_page) ;
}
function direct() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { current_page = total_page ; }
	else if (current_page <= 0) { current_page = 1 ; }

	post_data('applycase_result.php',current_page) ;
}
function show_limit() {
	var current_page = parseInt($('[name="current_page"]').val()) ;

	post_data('applycase_result.php',current_page) ;
}
function list() {
	$('#a_tag').html('－') ;
	$('#small_table').removeClass('tbl_hide') ;
}
function go_back(url) {
	location.reload() ;
}
function xls_ep() {	
	$('form[name="myform"]').attr('action','applycase_result.php') ;
	$('[name="xls"]').val('ok') ;
	var max = "<{$max}>";
	max = max.replace(/,/, "");

	if (max > 3500) {
		alert("資料筆數過大，請增加搜尋條件");
		return false;
	}

	$('form[name="myform"]').submit() ;
	// /
}
function contract(sn) {
	$('[name="id"]').val(sn) ;

	$('[name="tform"]').submit() ;
}
</script>
<style>
#small_table td {
	font-size:9pt;
	
	padding:0px;
	line-height:20px;
}
.tbl_hide {
	display: none ;
}
.tbl_show {
	display: ;
}
.small_font {
	font-size: 8pt;
	line-height:0.5;
}
a.link {
	text-decoration: none ;
}
a.visited {
	text-decoration: none ;
}
a.hover {
	text-decoration: none ;
}
a.active {
	text-decoration: none ;
}
.small_font {
	font-size: 9pt;
	line-height:1;
}
input.bt4 {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background: #F8EDEB;border:1px #727272 outset;color:font-size:12px;margin-left:2px
}
input.bt4:hover {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background:  #EBD1C8;border:1px #727272 outset;font-size:12px;margin-left:2px;cursor:pointer
}
</style>
</head>
<body>
<form name="tform" method="POST" action="/escrow/formbuyowneredit.php" target="_blank">
	<input type="hidden" name="id" value='' />
</form>
<form method="post" name="myform">
	<center>
	<div >

	<table cellspacing="0" cellpadding="0" style="width:700px;border-spacing:0px;" >
	<tr style="line-height:24px;display:none;">
		<td colspan="3" style="padding:8px;border:0px;">簽約(進案)日期&nbsp;起&nbsp;<{$SsignDate}>&nbsp;~&nbsp;迄&nbsp;<{$EsignDate}></td>
	</tr>
	<tr style="background-color:#E4BEB1;line-height:24px;text-align:center;width:840px;">
		<td nowrap style="width:100px;border:0px;">案件總筆數</td>
		<td nowrap style="width:150px;border:0px;">買賣總價金額</td>
		<td nowrap style="width:150px;border:0px;">合約總保證費金額</td>
		<td nowrap style="width:100px;border:0px;">回饋總金額</td>
		<td nowrap style="width:100px;border:0px;">收入</td>
	
		<!-- <td nowrap style="width:180px;border:0px;">出款總保證費金額</td> -->
		<td nowrap style="width:100px;border:0px;">功能</td>
	</tr>
	<tr style="line-height:24px;text-align:center;background-color:#F8ECE9;">
		<td style="width:100px;border:0px;"><{$max}>&nbsp;</td>
		<td nowrap style="width:150px;border:0px;"><{$totalMoney}>&nbsp;</td>
		<td nowrap style="width:150px;border:0px;">
			<!-- <{if $branch != '' || $brand != ''}>
				<{$cCertifiedMoney}>
			<{else}>

			<{/if}> -->
			<{$certifiedMoney}>
			&nbsp;
		</td>
		<td nowrap style="width:100px;border:0px;"><{$cCaseFeedBackMoney}></td>
		<td nowrap style="width:100px;border:0px;"><{$income}></td>
		
		<!-- <td nowrap style="width:180px;border:0px;"><{$transMoney}>&nbsp;</td> -->
		<td style="width:100px;border:0px;"><{$functions}>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="5" style="height:40px;">
			<input type="button" class="bt4" value="回上一頁" onclick="go_back('applycase.php')">
			<input type="button" class="bt4" value="匯出excel檔" onclick="xls_ep()">
		</td>
	</tr>
	</table>
	<div style="height:10px;">
	</div>
	<div id="small_table" class="tbl_hide">
	<table cellspacing="0" cellpadding="0" style="margin-left:-50px;width:990px;">
	<tr style="background-color:#E4BeB1;text-align:center;">
		<td style="width:30px;line-height:30px;">序號</td>
		<td style="line-height:30px;">保證號碼</td>
		<td style="width:90px;line-height:30px;">仲介品牌</td>
		<td style="width:130px;line-height:30px;">仲介店名</td>
		<td style="width:80px;line-height:30px;">賣方</td>
		<td style="width:80px;line-height:30px;">買方</td>
		<td style="line-height:30px;">總價金</td>
		<td style="line-height:30px;">合約保證費</td>
		<td style="width:90px;line-height:30px;"><{$t_day}></td>
		<td style="width:90px;line-height:30px;">進案日期</td>
		<!-- <td style="width:90px;line-height:30px;">結案日期</td> -->
		<td style="width:90px;line-height:30px;">地政士姓名</td>
		<td style="width:60px;line-height:30px;">狀態</td>
	</tr>
	<{$tbl}>
	</table>
	<div style="height:20px;"></div>
	<div style="margin-left:0px;width:900px;height:20px;padding:4px;text-align:left;">

	<span style="font-size:9pt;">
	<select name="record_limit" size="1" onchange="show_limit()" style="font-size:9pt;width:48;">
	<{$record_limit}>
	</select>

	<span onclick="first()" style="cursor:pointer;"><img src="/images/first.jpg" style="border:0px;"></span>
	<span onclick="back()" style="cursor:pointer;"><img src="/images/backward.jpg" style="border:0px;"></span>

	第&nbsp;<input type="text" name="current_page" onchange="direct()" value="<{$current_page}>" style="font-size:9pt;text-align:right;width:30px;">&nbsp;頁
	／共&nbsp;<{$total_page}>&nbsp;頁


	<span onclick="next()" style="cursor:pointer;"><img src="/images/forward.jpg" style="border:0px;"></span>
	<span onclick="last()" style="cursor:pointer;"><img src="/images/last.jpg" style="border:0px;"></span>

	顯示第&nbsp;<{$i_begin}>&nbsp;筆到第&nbsp;<{$i_end}>&nbsp;筆的紀錄，共&nbsp;<{$max}>&nbsp;筆紀錄

	</span>

	</div>

	</div>
	<input type="hidden" name="current_page" value="<{$current_page}>">
	<input type="hidden" name="total_page" value="<{$total_page}>">

	<input type="hidden" name="bank" value="<{$bank}>">
	<input type="hidden" name="sApplyDate" value="<{$sApplyDate}>">
	<input type="hidden" name="eApplyDate" value="<{$eApplyDate}>">
	<input type="hidden" name="sEndDate" value="<{$sEndDate}>">
	<input type="hidden" name="eEndDate" value="<{$eEndDate}>">
	<input type="hidden" name="sSignDate" value="<{$sSignDate}>">
	<input type="hidden" name="eSignDate" value="<{$eSignDate}>">
	<input type="hidden" name="sbankLoansDate" value="<{$sbankLoansDate}>">
	<input type="hidden" name="ebankLoansDate" value="<{$ebankLoansDate}>">
	<input type="hidden" name="branch" value="<{$branch}>">
	<input type="hidden" name="scrivener" value="<{$scrivener}>">
	<input type="hidden" name="zip" value="<{$zip}>">
	<input type="hidden" name="citys" value="<{$citys}>">
	<input type="hidden" name="branchZip" value="<{$branchZip}>">
	<input type="hidden" name="branchCitys" value="<{$branchCitys}>">
	<input type="hidden" name="scrivenerZip" value="<{$scrivenerZip}>">
	<input type="hidden" name="scrivenerCitys" value="<{$scrivenerCitys}>">
	<input type="hidden" name="brand" value="<{$brand}>">
	<input type="hidden" name="undertaker" value="<{$undertaker}>">
	<input type="hidden" name="status" value="<{$status}>">
	<input type="hidden" name="show_hide" value="<{$show_hide}>">
	<input type="hidden" name="cCertifiedId" value="<{$cCertifiedId}>">
	<input type="hidden" name="buyer" value="<{$buyer}>">
	<input type="hidden" name="owner" value="<{$owner}>">
	<input type="hidden" name="realestate" value="<{$realestate}>">
	<input type="hidden" name="scrivener_category" value="<{$scrivener_category}>">
	<input type="hidden" name="sales" value="<{$sales}>">
	<input type="hidden" name="xls">
	<input type="hidden" name="sales_performance" value="<{$sales_performance}>">

	</div>
	</center>
</form>
</body>
</html>