<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<script type="text/javascript">
function post_data(url,cp) {
	var bk = $('[name="bank"]').val() ;
	var no = $('[name="sn"]').val() ;
	var uk = $('[name="undertaker"]').val() ;
	var by = $('[name="buyer"]').val() ;
	var ow = $('[name="owner"]').val() ;
	var sc = $('[name="scrivener"]').val() ;
	var bd = $('[name="brand"]').val() ;
	var bh = $('[name="branch"]').val() ;
	var sd = $('[name="signdate"]').val() ;
	var sd2 = $('[name="sign2date"]').val() ;
	var st = $('[name="status"]').val() ;
	var ed = $('[name="enddate"]').val() ;
	var zp = $('[name="zip"]').val() ;
	var ba = $('[name="buyer_agent"]').val() ;
	var oa = $('[name="owner_agent"]').val() ;
	var uid = $('[name="uid"]').val();

	var tp = $('[name="total_page"]').val() ;
	var rl = $('[name="record_limit"]').val() ;
	
	var tp = parseInt($('[name="total_page"]').val()) ;
	var rl = $('[name="record_limit"]').val() ;
	
	$.post(url,
		{'bank':bk,'sn':no,'undertaker':uk,'buyer':by,'owner':ow,'scrivener':sc,'buyer_agent':ba,
		'brand':bd,'branch':bh,'signdate':sd,'sign2date':sd2,'status':st,'enddate':ed,'zip':zp,'owner_agent':oa,
		'total_page':tp,'current_page':cp,'record_limit':rl,'uid':uid},
		function(txt) {
			$('#container').html(txt) ;
	}) ;

}

function first() {
	var current_page = parseInt($('[name="current_page"]').val()) ;

	if (current_page <= 1) { return false ; }
	else { current_page = 1 ; }

	post_data('buyerownerinquery_result.php',current_page) ;
}
function back() {
	var current_page = parseInt($('[name="current_page"]').val()) - 1 ;

	if (current_page <= 0) { return false ; }

	post_data('buyerownerinquery_result.php',current_page) ;
}
function next() {
	var current_page = parseInt($('[name="current_page"]').val()) + 1 ;
	var total_page = parseInt($('[name="total_page"]').val()) ;
	
	if (current_page > total_page) { return false ; }

	post_data('buyerownerinquery_result.php',current_page) ;
}
function last() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { return false ; }
	else { current_page = total_page ; }

	post_data('buyerownerinquery_result.php',current_page) ;
}
function direct() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { current_page = total_page ; }
	else if (current_page <= 0) { current_page = 1 ; }

	post_data('buyerownerinquery_result.php',current_page) ;
}
function show_limit() {
	var current_page = parseInt($('[name="current_page"]').val()) ;

	post_data('buyerownerinquery_result.php',current_page) ;
}

function go_back(url) {
	location.reload() ;
}

function contract(sn) {
	// $('[name="id"]').val(sn) ;
	//alert($("#id").val()) ;
	// $('[name="form_edit"]').submit() ;
    var url = '/escrow/formbuyowneredit.php?id=' + sn ;
    window.open(url) ;
}
function detail(no,v) {
	$('[name="sn"]').val(no) ;
	//$('[name="vr"]').val(v) ;
	$('form[name="form_detail"]').submit() ;
    
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
<form name="form_edit" method="POST" action="/escrow/formbuyowneredit.php" target="_blank">
	<input type="hidden" name="id" value='' />
</form>
<form name="form_detail" method="POST" action="/inquire/buyerowner_detail.php" target="case_detail">
<input type="hidden" name="sn" value="">
</form>
<center>
<div>
<div style="width:990px;padding-bottom:20px;text-align:left;">
	<input type="button" class="bt4 small_font" value="回上一頁" onclick="go_back('applycase.php')">
</div>

<table cellspacing="0" cellpadding="0" style="margin-left:-50px;width:990px;">
<tr style="background-color:#E4BeB1;text-align:center;height:40px;">
	<td>保證號碼</td>
	<td>系統別</td>
	<td>簽約日期</td>
	<td>案件地區</td>
	<td>仲介品牌</td>
	<td>仲介店名</td>
	<td>地政士</td>
	<td>買方</td>
	<td>賣方</td>
	<td>承辦人</td>
	<td>案件狀態</td>
</tr>
<{$tbl}>
</table>
<div style="height:20px;"></div>
<div style="margin-left:0px;width:900px;height:20px;padding:4px;text-align:left;">
<span style="float:right;">
	<input type="button" class="bt4 small_font" value="回上一頁" onclick="go_back('applycase.php')">
</span>
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
<input type="hidden" name="sn" value="<{$sn}>">
<input type="hidden" name="undertaker" value="<{$undertaker}>">
<input type="hidden" name="buyer" value="<{$buyer}>">
<input type="hidden" name="owner" value="<{$owner}>">
<input type="hidden" name="scrivener" value="<{$scrivener}>">
<input type="hidden" name="brand" value="<{$brand}>">
<input type="hidden" name="branch" value="<{$branch}>">
<input type="hidden" name="signdate" value="<{$signdate}>">
<input type="hidden" name="sign2date" value="<{$sign2date}>">
<input type="hidden" name="status" value="<{$status}>">
<input type="hidden" name="enddate" value="<{$enddate}>">
<input type="hidden" name="zip" value="<{$zip}>">
<input type="hidden" name="buyer_agent" value="<{$buyer_agent}>">
<input type="hidden" name="owner_agent" value="<{$owner_agent}>">
<input type="hidden" name="uid" value="<{$uid}>">
</div>
</center>
</body>
</html>