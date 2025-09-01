<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<{include file='meta.inc.tpl'}>	
<script type="text/javascript">
$(document).ready(function() {
	$('.iframe').colorbox() ;
});

/* 各種統計報表 */
function targetChart(url) {
	if (url != '') {
		//$('[name="chartSubmit"]').attr('action',url) ;
		//$('[name="chartSubmit"]').submit() ;
		
		window.open(url+'.php',url,config="width=600px,height=550px,scrollbars=yes,resizable=yes") ;
	}
	else {
		alert('本功能尚在製作中...') ;
	}
}
////
</script>
<style>
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
.ui-autocomplete-input {
	width:300px;
}
#dialog {
	background-image:url("../images/animated-overlay.gif") ;
	background-repeat: repeat-x;
	margin: 0px auto;
}
#chartList div {
	border: 1px solid #000;
	width: 400px;
	padding: 10px;
	margin: 5px;
}
#chartList div div {
	border: 0px;
	width: 90px;
	float: left;
	margin: 2px;
	padding: 2px;
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
							<td width="81%" align="right"><!-- <a href="#" onClick="window.open('../bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
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
								<div id="dialog">
								
								</div>
								<div id="chartArea">
									<div id="chartList" style="margin:0px auto;">
										<center>
											<div style="float:left;text-align:center;">
												<h3 style="text-align:center;">案件統計</h3><br>
												<div style="border-right-width:1px;border-right-style:dashed;border-right-color:#000;"><a href="#" onclick="targetChart('BankCharts')">金流系統統計</a></div>
												<div style="border-right-width:1px;border-right-style:dashed;border-right-color:#000;"><a href="#" onclick="targetChart('IncomeCharts')">進案件數統計</a></div>
												<div style="border-right-width:1px;border-right-style:dashed;border-right-color:#000;"><a href="#" onclick="targetChart('CloseCharts')">結案件數統計</a></div>
												<div><a href="#" onclick="targetChart('StateCharts')">狀態件數統計</a></div>
												<br>
												<div style="border-right-width:1px;border-right-style:dashed;border-right-color:#000;"><a href="#" onclick="targetChart('AllCase')">每月件數統計</a></div>
												<div style="border-right-width:1px;border-right-style:dashed;border-right-color:#000;"><a href="#" onclick="targetChart('AllCaseYear')">年度件數統計</a></div>
											</div>
											<div style="float:left;text-align:center;">
												<h3 style="text-align:center;">業務統計</h3><br>
												<div style="border-right-width:1px;border-right-style:dashed;border-right-color:#000;"><a href="#" onclick="targetChart('ScrivenerCharts')">地政士統計</a></div>
												<div style="border-right-width:1px;border-right-style:dashed;border-right-color:#000;"><a href="#" onclick="targetChart('RealtyCharts')">仲介統計</a></div>
												<div style="border-right-width:1px;border-right-style:dashed;border-right-color:#000;"><a href="#" onclick="targetChart('CaseArea')">案件分布點</a></div>
												<br>
												<div style="clear:both;">　</div>
											</div>
										</center>
									</div>
								</div>
								<div id="footer" style="height:50px;clear:both;">
									<p>2012 第一建築經理股份有限公司 版權所有</p>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<form name="chartSubmit" method="POST" target="_blank">
</form>
</body>
</html>