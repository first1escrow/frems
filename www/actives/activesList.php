<?php

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>文章活動列表</title>
<link rel="stylesheet" type="text/css" href="/libs/datatables/media/css/demo_page.css" />
<link rel="stylesheet" type="text/css" href="/libs/datatables/media/css/demo_table.css" />
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" language="javascript" src="/libs/datatables/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="/js/ROCcalender_limit.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#dialog').dialog({
		autoOpen: false,
		width: 600,
		height: 350
	}) ;
	
	var oTable = $("#example").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sServerMethod": "POST", 
		"sAjaxSource": 'listData.php',
		"aaSorting": [[0,"desc"]]
		//"aaSorting": [[0,"desc"],[2,"desc"]]
	});
	
	$('#example tbody tr').live('dblclick', function () {
		var tmp = this.id.replace('row_', '');
		
		$('#myform').attr('action', 'actMemberList.php');
		$('[name="sn"]').val(tmp);
		// $('[name="op"]').val('m');
		$('#myform').submit();
	} );
}) ;

function addNews() {
	var url = 'newsAdd.php' ;
	
	$('#myform').prop('action',url) ;
	$('#myform').submit() ;
}

function closecb() {
	parent.$.fn.colorbox.close() ;
}
</script>
<style>
input {
	/* background-color: #FFFFFD ;*/
}

body {
	/* font-family: 標楷體; */
	font-size: 11pt;
	font-family: "微軟正黑體", serif;
}

#tbl .tdH {
	text-align: center;
	font-weight: bold;
	background-color: #FFB6C1;
}

#tbl td {
	text-align: center;
	padding: 5px;
}

</style>
</head>
<body style="background-color:#F8ECE9;">
<form method="POST" id="myform">
	<input type="hidden" name="sn">
	<input type="hidden" name="op">
</form>

<div id="dialog" title=""></div>

<center>

<div style="margin:10px 10px 20px 10px;text-align:left;">
	<img onclick="closecb()" style="cursor:pointer;" src="/images/close.png" title="關閉">
	<!-- <img onclick="addNews()" style="margin-left:20px;cursor:pointer;" src="/images/plus02.png" title="新增"> -->
</div>
<h2>課程列表</h2>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
	<thead>
		<tr>
			<th style="width:200px;">課程名稱</th>
			<th style="width:50px;">課程場次</th>
			<th style="width:50px;">限制人數</th>
			<th style="width:50px;">目前人數</th>
			<th style="width:80px;">課程期間起</th>
			<th style="width:80px;">課程期間迄</th>
			
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="6" class="dataTables_empty">Loading data from server</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="6"></th>
		</tr>
	</tfoot>
</table>

</center>

<div style="margin-top:30px;text-align:right;font-size:9pt;color:#FF0000;">(請雙點擊列表以觀看內容)</div>
</body>
</html>
