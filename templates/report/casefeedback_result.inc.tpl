<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	var sh = "<{$show_hide}>" ;
	if (sh=='show') {
		$('#detail_table').css({'display':''}) ;
		$('#page_show').css({'display':''}) ;
		$('#showhide').html('<a href="#" onclick="excel()">匯出結算明細通知書</a>') ;
	}
}) ;

function post_data(url,cp) {
	var url = 'casefeedback_result.php' ;

	var bk = $('[name="bank"]').val() ;
	var sc = $('[name="bStoreClass"]').val() ;
	var br = $('[name="branch"]').val() ;
	var sy = $('[name="sales_year"]').val() ;
	var se = $('[name="sales_season"]').val() ;
	var cd = $('[name="certifiedid"]').val() ;
	var bc = $('[name="bCategory"]').val() ;
	var ir = $('[name="invert_result"]').val() ;
	
	var tp = parseInt($('[name="total_page"]').val()) ;
	var rl = $('[name="record_limit"]').val() ;	
	var np = $('[name="next_page"]').val() ;
	var scr = $('[name="scrivener"]').val();
	var bck = $('[name="bck"]').val();
	var brand = $('[name="bd"]').val();
	/*
	alert('銀行別='+bk) ; 
	alert('店身分='+sc) ;
	alert('店名='+br) ;
	alert('年度='+sy) ;
	alert('季別='+se) ;
	alert('保證號碼='+cd) ; 
	*/
	
	$.post(url,
		{'bank':bk,'bStoreClass':sc,'branch':br,'bCategory':bc,'invert_result':ir,
		'sales_year':sy,'sales_season':se,'certifiedid':cd,
		'current_page':cp,'total_page':tp,'record_limit':rl,'next_page':np,'show_hide':'show','scrivener':scr,'bck':bck,'bd':brand},
		function(txt) {
			$('#container').html(txt) ;
	}) ;

}

function first() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	
	if (current_page <= 1) {
		return false ;
	}
	else {
		current_page = 1 ;
		post_data('casefeedback_result.php',current_page) ;
		
	}
}
function back() {
	var current_page = parseInt($('[name="current_page"]').val()) - 1 ;

	if (current_page <= 0) { return false ; }

	post_data('casefeedback_result.php',current_page) ;
}
function next() {
	var current_page = parseInt($('[name="current_page"]').val()) + 1 ;
	var total_page = parseInt($('[name="total_page"]').val()) ;
	
	if (current_page > total_page) { return false ; }

	post_data('casefeedback_result.php',current_page) ;
}
function last() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { return false ; }
	else { current_page = total_page ; }

	post_data('casefeedback_result.php',current_page) ;
}
function direct() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { current_page = total_page ; }
	else if (current_page <= 0) { current_page = 1 ; }

	post_data('casefeedback_result.php',current_page) ;
}
function show_limit() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	
	post_data('casefeedback_result.php',current_page) ;
}

function go_back(url) {
	location.reload() ;
}
function detail() {
	$('#detail_table').css({'display':''}) ;
	$('#page_show').css({'display':''}) ;
	$('#showhide').html('<a href="#" onclick="excel()">匯出結算明細通知書</a>') ;
	$('#show_hide').val('show') ;
}
function excel() {
	$('form[name="myform"]').attr('action','casefeedback_result.php') ;
	$('[name="exports"]').val('ok') ;
	
	$('form[name="myform"]').submit() ;
}
<{$functions}>
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
td {
	padding: 4px ;
}
</style>
</head>
<body>

<center>
<div>
<form method="post" name="myform">
<{$tb1}>

<div style="height:20px;"></div>
<table cellspacing="0" cellpadding="0" id="detail_table" style="margin-left:-50px;width:900px;display:<{$display}>;">
<tr style="background-color:#E4BeB1;text-align:center;height:40px;">
	<td>日期</td>
	<td>銀行別</td>
	<td>保證號碼</td>
	<td>店編號</td>
	<td>店名</td>
	<td>身份別</td>
	<td>買方</td>
	<td>賣方</td>
	<td>買賣總價金</td>
	<td>回饋金額</td>
	<td>仲介類型</td>
</tr>
<{$tb2}>
</table>
<div style="height:20px;"></div>
<div id="page_show" style="margin-left:0px;width:900px;height:20px;padding:4px;text-align:left;display:none;">
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
<input type="hidden" name="current_page" value="<{$current_page}>">
<input type="hidden" name="total_page" value="<{$total_page}>">

<input type="hidden" name="bank" value="<{$bank}>">
<input type="hidden" name="bStoreClass" value="<{$bStoreClass}>">
<input type="hidden" name="branch" value="<{$branch}>">
<input type="hidden" name="sales_year" value="<{$sales_year}>">
<input type="hidden" name="sales_season" value="<{$sales_season}>">
<input type="hidden" name="certifiedid" value="<{$certifiedid}>">
<input type="hidden" name="bCategory" value="<{$bCategory}>">
<input type="hidden" name="invert_result" value="<{$invert_result}>">
<input type="hidden" name="scrivener" value="<{$scrivener}>">
<input type="hidden" name="next_page" value="1">
<input type="hidden" name="show_hide" value="<{$show_hide}>">
<input type="hidden" name="exports">
<input type="hidden" name="bck" value="<{$bck}>">
<input type="hidden" name="bd" value="<{$brand}>">
</div>
</center>
</form>
</body>
</html>