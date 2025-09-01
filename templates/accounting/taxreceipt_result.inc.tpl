<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="../libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="../libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript">
$(function() {
	$( "#dialog" ).dialog({
		autoOpen: false,
		modal: true,
		minHeight:50,
		show: {
			effect: "blind",
			duration: 1000
		},
		hide: {
			effect: "explode",
			duration: 1000
		}
	});
	$(".ui-dialog-titlebar").hide() ;	
	
	var sh = "<{$show_hide}>" ;
	
	
	
	if (sh=='show') {
		$('#detail_table').css({'display':''}) ;
		$('#page_show').css({'display':''}) ;
		$('#showhide').html('－') ;
	}
	
	$('#go_back_id').button({
		icons:{
			primary: "ui-icon-arrowreturn-1-w"
		}
	}) ;
	$('#export_receipt_id').button({
		icons:{
			primary: "ui-icon-copy"
		}
	}) ;

}) ;

function post_data(cp) {
	var url = 'taxreceipt_result.php' ;
	
	var id = $('[name="identity"]').val() ;
	var fy = $('[name="feedback_year"]').val() ;
	var fm = $('[name="feedback_month"]').val() ;
	var no = $('[name="sn"]').val() ;
	var tn = $('[name="tax_name"]').val() ;
	var ti = $('[name="tax_id"]').val() ;

	var tp = parseInt($('[name="total_page"]').val()) ;
	var rl = $('[name="record_limit"]').val() ;
	
	$.post(url,
		{'identity':id,'feedback_year':fy,'feedback_month':fm,'sn':no,'tax_name':tn,'tax_id':ti,'current_page':cp,'total_page':tp,'record_limit':rl},
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
		post_data(current_page) ;
		
	}
}
function back() {
	var current_page = parseInt($('[name="current_page"]').val()) - 1 ;

	if (current_page <= 0) { return false ; }

	post_data(current_page) ;
}
function next() {
	var current_page = parseInt($('[name="current_page"]').val()) + 1 ;
	var total_page = parseInt($('[name="total_page"]').val()) ;
	
	if (current_page > total_page) { return false ; }

	post_data(current_page) ;
}
function last() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { return false ; }
	else { current_page = total_page ; }

	post_data(current_page) ;
}
function direct() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { current_page = total_page ; }
	else if (current_page <= 0) { current_page = 1 ; }

	post_data(current_page) ;
}
function show_limit() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	
	post_data(current_page) ;
}

function go_back(url) {
	location = 'taxreceipt.php' ;
}

function xls() {
	var url = 'taxreceipt_result_excel.php' ;
	$('form[name="myform"]').attr('action',url) ;
	$('[name="exp"]').val('ok') ;
	$( "#dialog" ).dialog("open") ;
	
	$('form[name="myform"]').submit() ;
	setTimeout("$('#dialog').dialog('close')",15000) ;
}
function opt() {
	var url = 'taxreceipt_result_output.php' ;
	
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
.td_border {
	border: 1px solid #DBDBDB;
	font-size: 10pt;
}
.join {
	width: 60px;
	border: 1px dashed #ccc;
	background-color: #F8EDEA;
}
.join:hover {
	width: 60px;
	border: 1px solid #000;
	background-color: #EBD1C8;
}
</style>
</head>
<body>
<div style="padding:20px;text-align:left;">
<button id="go_back_id" onclick="go_back()">重新查詢</button>
<button id="export_receipt_id" onclick="xls()">匯出報表</button>
</div>
<center>
<div>

<form method="POST" name="myform">
<input type="hidden" name="exp" value="">
<table cellspacing="0" cellpadding="0" id="detail_table" style="margin-left:-50px;width:800px;">
<tr style="background-color:#E4BeB1;text-align:center;height:40px;">
	<td class="td_border" style="width:130px;">店編號(保證號碼)</td>
	<td class="td_border" style="width:150px;">姓名</td>
	<td class="td_border" style="width:150px;">統一編號(身份證字號)</td>
	<td class="td_border" style="width:110px;"><{if $identity == 3}>地政士回饋金<{else}>所得金額<{/if}></td>
	<td class="td_border" style="width:130px;">代扣稅款</td>
	<td class="td_border" style="width:130px;">地址</td>	
</tr>
<{$tbl}>
</table>
<div style="height:30px;width:800px;text-align:left;">

</div>

<div id="page_show" style="margin-left:-46px;width:800px;height:20px;padding:0px;text-align:left;">
	<select name="record_limit" size="1" onchange="show_limit()" style="font-size:9pt;width:48;">
	<{$record_limit}>
	</select>

	<span onclick="first()" style="cursor:pointer;"><img src="/images/first.jpg" style="border:0px;"></span>
	<span onclick="back()" style="cursor:pointer;"><img src="/images/backward.jpg" style="border:0px;"></span>
	
	<span style="font-size:9pt;">
	第&nbsp;<input type="text" name="current_page" onchange="direct()" value="<{$current_page}>" style="font-size:9pt;text-align:right;width:30px;">&nbsp;頁
	／共&nbsp;<{$total_page}>&nbsp;頁
	</span>

	<span onclick="next()" style="cursor:pointer;"><img src="/images/forward.jpg" style="border:0px;"></span>
	<span onclick="last()" style="cursor:pointer;"><img src="/images/last.jpg" style="border:0px;"></span>

	<span style="font-size:9pt;">
	顯示第&nbsp;<{$i_begin}>&nbsp;筆到第&nbsp;<{$i_end}>&nbsp;筆的紀錄，共&nbsp;<{$max}>&nbsp;筆紀錄
	</span>
</div>
<input type="hidden" name="current_page" value="<{$current_page}>">
<input type="hidden" name="total_page" value="<{$total_page}>">

<input type="hidden" name="identity" value="<{$identity}>">
<input type="hidden" name="feedback_year" value="<{$feedback_year}>">
<input type="hidden" name="feedback_month" value="<{$feedback_month}>">
<input type="hidden" name="sn" value="<{$sn}>">
<input type="hidden" name="tax_name" value="<{$tax_name}>">
<input type="hidden" name="tax_id" value="<{$tax_id}>">


</div>
</center>
</form>
</body>
</html>