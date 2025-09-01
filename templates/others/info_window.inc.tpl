<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {	

}) ;

</script>
<style>
.ui-autocomplete-input {
	width:210px;
}
.iframe {
	font-size: 10pt;
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
                            <fieldset style="margin: 0px auto;padding: 10px 10px 10px 10px;width: 450px; height: 150px;">
                                <legend>資訊視窗</legend>
                                <div>
                                    <{if $smarty.session.pSMSInfoWindow == 1}>
                                    <div style="float:left;margin: 10px;">
                                        <a href='#' onclick='window.open("../sms/sms_summary.php","sms_summary","height=60px,width=300px,status=no")'>簡訊資訊視窗</a>
                                    </div>
                                    <{/if}>
                                    <{if $smarty.session.pLegalCaseNotify == 1}>
                                    <div style="float:left;margin: 10px;">
                                        <a href='#' onclick='window.open("../legal/legalNotifyInfo.php","legalNotifyInfo","height=60px,width=300px,status=no")'>催告資訊視窗</a>
                                    </div>
                                    <{/if}>

                                    <div style="clear:both;"></div>
                                </div>
                            </fieldset>
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