<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {	
	$(".iframe").colorbox({iframe:true, width:"800px", height:"80%"}) ;
	
	/* 設定 UI dialog 屬性 */
	$('#msg').dialog({
		modal: true,
		buttons: {
			OK: function() {
				$(this).dialog("close") ;
			}
		}
	}) ;
}) ;

</script>
<style>
.ui-autocomplete-input {
	width:210px;
}
.iframe {
	font-size: 12pt;
}
.item{
	float:left;
	width:150px;
	/*padding:10px 0 0 10px;*/

	margin-left: 10px;
}
</style>
</head>
<body id="dt_example">
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
						
							<div style="padding-bottom:10px;padding-top:10px;width:100%;border:1px solid #CCC;">
								<{if $smarty.session.member_id|in_array: [1, 6] || $smarty.session.member_pDep == 6}>
								<div class="item">
									<a class="" href="https://escrow2.first1.com.tw/backEnd/escrowDocument" target="_blank">履保文件上傳</a>
								</div>
								<div class="item">
									<a class="iframe" href="news/newsList.php">最新訊息上線</a>
								</div>

								<!-- <div class="item">
									<a class="iframe" href="actives/activesList.php">課程管理</a>
								</div> -->
								<{/if}>
								<{if $smarty.session.pWebBanner == 1}>
								<div class="item">
									<a href="banner/banner.php" target="_blank">官網房貸專區</a>
								</div>
								<{/if}>
								
								<div style="clear:both;"></div>
							</div>
							
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