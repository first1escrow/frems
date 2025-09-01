<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {	
	$(".iframe").colorbox({iframe:true, width:"85%", height:"80%"}) ;
	
	/* 設定 UI dialog 屬性 */
	$('#msg').dialog({
		modal: true,
		buttons: {
			OK: function() {
				$(this).dialog("close") ;
			}
		}
	}) ;
	
	var oTable = $("#example").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sServerMethod": "POST", 
		"sAjaxSource": 'listData.php'
	});
	
	$('#example tbody tr').live('dblclick', function () {
		var sn = $(this).prop('id') ;
		var idCheck = /^SC/ ;
		
		if (idCheck.test(sn)) {
			var url = '/maintain/formscriveneredit.php' ;
			sn = parseInt(sn.substr(2)) ;
			
			$('[name="myform"]').prop('action',url) ;
			$('[name="id"]').val(sn) ;
			$("[name='from_sales']").val('sales');
		}
		else {
			var url = '/maintain/formbranchedit.php' ;
			sn = parseInt(sn.substr(2)) ;
			
			$('[name="myform"]').prop('action',url) ;
			$('[name="id"]').val(sn) ;
			$("[name='from_sales']").val('sales');
		}
		
		$('[name="myform"]').submit() ;
	}) ;
}) ;

function show(tags) {
	alert(tags) ;
}
</script>
<style>
.ui-autocomplete-input {
	width:210px;
}
.iframe {
	font-size: 12pt;
}
</style>
</head>
<body id="dt_example">
<form method="POST" name="myform">
<input type="hidden" name="certifyid">
<input type="hidden" name="id">
<input type="hidden" name="from_sales">
</form>
<div id="wrapper">
	<div id="header">
		<table width="1000" border="0" cellpadding="2" cellspacing="2">
			<tr>
				<td width="233" height="72">&nbsp;</td>
				<td width="753">
					<table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
						<tr>
							<td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
						</tr>
						<tr>
							<td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
							<td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
						</tr>
					</table>
				</td>
			</tr>
		</table> 
	</div>
	<{include file='menu1.inc.tpl'}>
	<table width="1000" border="0" cellpadding="4" cellspacing="0">
		<tr>
			<td bgcolor="#DBDBDB">
			<table width="100%" border="0" cellpadding="4" cellspacing="1">
				<tr>
					<td height="17" bgcolor="#FFFFFF">
						<div id="menu-lv2">
                                                        
						</div>
						<br/> 
						<h3>&nbsp;</h3>
						<div id="container">
						
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
	<thead>
		<tr>
			<th width="10%">地政士/店編號</th>
			<th width="20%">品牌</th>
			<th width="20%">地政士/店名</th>
			<th width="30%">事務所/公司</th>
			<th width="10%">統一編號</th>
			<th width="10%">業務審核</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="6" class="dataTables_empty">讀取資料中...</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="6"></th>
		</tr>
	</tfoot>
</table>
							
						</div>
						<div id="footer" style="height:50px;">
							<p>2012 第一建築經理股份有限公司 版權所有</p>
						</div>
					</td>
				</tr>
			</table>
			</td>
		</tr>
	</table>

</div>
</body>
</html>