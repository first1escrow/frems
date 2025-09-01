<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript">
var sh = "<{$show_hide}>" ;
if (sh=='show') {
	$('#detail_table').css({'display':''}) ;
	$('#page_show').css({'display':''}) ;
	$('#showhide').html('－') ;
}

function post_data(url,cp) {

	var sd = $('[name="start_date"]').val() ;
	var ed = $('[name="end_date"]').val() ;
	var by = $('[name="buyer"]').val() ;
	var ow = $('[name="owner"]').val() ;
	var sc = $('[name="scrivener"]').val() ;
	var bh = $('[name="branch"]').val() ;
	var ct = $('[name="category"]').val() ;
	var cd = $('[name="certifiedid"]').val() ;
	var np = $('[name="next_page"]').val() ;

	var tp = parseInt($('[name="total_page"]').val()) ;
	var rl = $('[name="record_limit"]').val() ;
	
	$.post(url,
		{'start_date':sd,'end_date':ed,'buyer':by,'owner':ow,'scrivener':sc,'branch':bh,'category':ct,
		'certifiedid':cd,'current_page':cp,'total_page':tp,'record_limit':rl,'next_page':np,'show_hide':'show'},
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
		post_data('certified_result.php',current_page) ;
		
	}
}
function back() {
	var current_page = parseInt($('[name="current_page"]').val()) - 1 ;

	if (current_page <= 0) { return false ; }

	post_data('certified_result.php',current_page) ;
}
function next() {
	var current_page = parseInt($('[name="current_page"]').val()) + 1 ;
	var total_page = parseInt($('[name="total_page"]').val()) ;
	
	if (current_page > total_page) { return false ; }

	post_data('certified_result.php',current_page) ;
}
function last() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { return false ; }
	else { current_page = total_page ; }

	post_data('certified_result.php',current_page) ;
}
function direct() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { current_page = total_page ; }
	else if (current_page <= 0) { current_page = 1 ; }

	post_data('certified_result.php',current_page) ;
}
function show_limit() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	
	post_data('certified_result.php',current_page) ;
}

function go_back(url) {
	location.reload() ;
}
function detail() {
	$('#detail_table').css({'display':''}) ;
	$('#page_show').css({'display':''}) ;
	$('#showhide').html('－') ;
	$('#show_hide').val('show') ;
}
function xls(url) {
	$('form[name="myform"]').attr('action',url) ;
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
<form method="post" name="myform" target="_blank">
<table cellspacing="0" cellpadding="0" style="margin-left:-50px;width:600px;">
<tr style="background-color:#E4BeB1;text-align:center;">
	<td>總案件筆數</td>
	<td>保證費總金額</td>
	<td>功能</td>
</tr>
<{$tb1}>
</table>

<div style="height:20px;"></div>
<table cellspacing="0" cellpadding="0" id="detail_table" style="margin-left:-50px;width:800px;display:<{$display}>;">
<tr style="background-color:#E4BeB1;text-align:center;height:40px;">
	<td>序號</td>
	<td>保證號碼</td>
	<td>匯款日期</td>
	<td>買方</td>
	<td>賣方</td>
	<td>地政士姓名</td>
	<td>保證費</td>	
	<td>案件狀態</td>
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

<input type="hidden" name="start_date" value="<{$start_date}>">
<input type="hidden" name="end_date" value="<{$end_date}>">
<input type="hidden" name="certifiedid" value="<{$certifiedid}>">
<input type="hidden" name="buyer" value="<{$buyer}>">
<input type="hidden" name="owner" value="<{$owner}>">
<input type="hidden" name="scrivener" value="<{$scrivener}>">
<input type="hidden" name="branch" value="<{$branch}>">
<input type="hidden" name="category" value="<{$category}>">
<input type="hidden" name="next_page" value="1">
<input type="hidden" name="show_hide" value="<{$show_hide}>">

</div>
</center>
</form>
</body>
</html>