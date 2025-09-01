<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<html>
<head>
<link rel="stylesheet" href="colorbox.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<{include file='meta.inc.tpl'}> 		
<script type="text/javascript">
$(document).ready(function() {
	getMarguee(<{$smarty.session.member_id}>) ;
	setInterval(function() { getMarguee2(<{$smarty.session.member_id}>); }, 180000)
	
	$("#show").colorbox({iframe:true, width:"1200px", height:"90%"}) ;

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
	
	$('#myBtn').button({
		icons: {
			primary: "ui-icon-transferthick-e-w",
			secondary: "ui-icon-note"
		}
	}) ;
	
	selectAll() ;
}) ;

function selectAll() {
	$('[name="cid"]').focus().select() ;
}

function dia(op) {
	$( "#dialog" ).dialog(op) ;
}

function checlCid() {
	var c = $('[name="cid"]').val() ;
	if (!c.match(/^[0-9]{9}$/)) {
		alert("請確認保證號碼是否正確!?") ;
		selectAll() ;
		
		window.event.returnValue = false
	}
}
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
	width:150px;
}
#dialog {
			background-image:url("/images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
.button_style{
	padding: 5px 5px 5px 5px;
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
					<td colspan="3" align="right">
						<div id="abgne_marquee" style="display:none;">
							<ul>
							</ul>
						</div>
					</td>
				</tr>
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
						<div id="dialog"></div>
						
						<div>
							<form name="myform" method="POST" action="interestData.php" onsubmit="checlCid()" target="_blank">
								<div style="padding:10px;border:1px solid #CCC;width:800px;min-height:100px;">
									<div style="font-size:12pt;font-weight:bold;">
										<span style="font-size:16pt;color:#8B008B;"><{$mm}></span>&nbsp;月
										&nbsp;<span style="font-size:16pt;color:#8B008B;"><{$dd}></span>&nbsp;日&nbsp;&nbsp;年利率
									</div>
									<div style="margin-top: 30px;text-align:center;">
										<table>
											<tr>
												<td style="width:260px;">
													<span style="font-size:12pt;font-weight:bold;color:#000088;">一銀</span>
													<span style="margin-left:10px;font-size:24pt;font-weight:bold;color:#FF4500;"><{$firstRate}></span>
													<span style="font-size:12pt;font-weight:bold;color:#FF4500;"><{$firstPercent}>&nbsp;</span>
												</td>
												<td style="width:260px;">
													<span style="font-size:12pt;font-weight:bold;color:#000088;">永豐</span>
													<span style="margin-left:10px;font-size:24pt;font-weight:bold;color:#FF4500;"><{$sinopacRate}></span>
													<span style="font-size:12pt;font-weight:bold;color:#FF4500;"><{$sinopacPercent}>&nbsp;</span>
												</td>
												<td style="width:260px;">
													<span style="font-size:12pt;font-weight:bold;color:#000088;">台新</span>
													<span style="margin-left:10px;font-size:24pt;font-weight:bold;color:#FF4500;"><{$taishinRate}></span>
													<span style="font-size:12pt;font-weight:bold;color:#FF4500;"><{$taishinPercent}>&nbsp;</span>
												</td>
											</tr>
										</table>
									</div>
								</div>
								
								<div style="margin-top:30px;padding:10px;border:1px solid #CCC;width:800px;min-height:100px;">
									<div style="font-size:12pt;font-weight:bold;">
										保證號碼利息明細
									</div>
									<div style="margin-top: 30px;text-align:center;">
										<div style="display:inline;">
											保證號碼：<input type="text" name="cid" style="width:90px;" maxlength="9" value="">
										</div>
										<div style="display:inline;margin-left:20px;">
											結算日期：<input type="text" name="lastDate" class="datepickerROC" style="width:80px;" value="<{$dt}>">
										</div>
										<div style="display:inline;margin-left:20px;">
											<button id="myBtn" type="submit">產出明細</button>
										</div>
									</div>
								</div>
							</form>
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