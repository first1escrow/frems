<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<{include file='meta.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {	
	/* enter 輸入 */
	$(this).keypress(function(e) {
		if (e.keyCode == 13) {
			comp() ;
		}
	}) ;
	////
	
	$(".inline").colorbox({inline:true, width:"70%", height:"90%"}) ;
});

function comp() {
	var dd = $('#qDate').val() ;
	
	if (dd == '') {
		alert("請選擇欲比對的日期!!") ;
		return false ;
	}
	else {
		$('#qC').val('ok') ;
		$('#myform').submit() ;
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
	background: #F8EDEB;border:1px #727272 dotted;color:font-size:12px;margin-left:2px
}
input.bt4:hover {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background:  #EBD1C8;border:1px #727272 dotted;font-size:12px;margin-left:2px;cursor:pointer
}
.btn {
	padding:10px 20px 10px 20px ;
	color:#212121 ;
	background-color:#F8ECE9 ;
	margin:2px ;
	border:1px outset #F8ECE0 ;
	cursor:pointer ;
}
.btn:hover {
	padding:10px 20px 10px 20px ;
	color:#212121 ;
	background-color:#EBD1C8 ;
	margin:2px;
	border:1px outset #F8ECE0;
	cursor:pointer;
}
.qScr {
	-webkit-border-radius: 10px;
	/* support firefox */
	-moz-border-radius: 10px;
	border-radius: 10px;
}
#qs td {
	padding: 5px;
}
</style>
</head>
    <body id="dt_example">
        <div id="wrapper">
            <div id="header">
                <table width="1000" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="233" height="72">&nbsp;</td>
                        <td width="753"><table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
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

<div style="text-align:center;">
	<center>
	<form method="POST" id="myform" name="myform">
		<div style="margin-bottom: 10px;font-size:12pt;font-weight:bold;">
			比對日期：
		<span style="color:#000088;">
			<!-- <{$qDate}> -->
			<input type="text" id="qDate" name="qDate" value="<{$qDate}>" style="width:100px;" onclick="show_calendar('myform.qDate')">
		</span>
		</div>
		
		<div style="margin-bottom: 10px;font-size:12pt;font-weight:bold;">
		品　　牌：
		<span style="color:#000088;">
			<input type="radio" id="qbCF" name="qBrand" value="CF" <{if $qBrand eq '僑馥'}>checked="checked" <{/if}>style="margin-left:20px;">
			<label for="qbCF">僑馥</label>
			<input type="radio" id="qbAS" name="qBrand" value="AS"  <{if $qBrand eq '安新'}>checked="checked" <{/if}>style="margin-left:20px;">
			<label for="qbAS">安新</label>
		</span>
		</div>
		
		<div class="qScr" style="border:1px solid #CCC; width:300px;padding: 10px;">
		</div>
		
		<div style="margin: 10px;">
		<table id="qs" cellspacing=0 cellpadding=0>
			<tr style="background-color: #F0F8FF;">
				<td style="width:200px;text-align:left;">本日特約地政士總數：</td>
				<td style="width: 100px;text-align:right;"><{$maxAll}></td>
			</tr>
			<tr>
				<td style="width:200px;text-align:left;">本日新增地政士數量：</td>
				<td style="width: 100px;text-align:right;"><{$maxAdd}></td>
			</tr>
			<tr style="background-color: #F0F8FF;">
				<td style="width:200px;text-align:left;">本日解約地政士數量：</td>
				<td style="width: 100px;text-align:right;"><{$maxDel}></td>
			</tr>
		</table>
		</div>
		
		<div class="qScr" style="border:1px solid #CCC; width:300px;padding: 10px;">
		</div>
		
		<div>
			<input type="button" value="開始比對" style="padding:5px;margin-top:20px;" onclick="comp()">
			<input type="hidden" name="qC" id="qC">
		</div>
		
		<div style="display:none;">
			<div id="alls"><{$alls}></div>
			<div id="adds"><{$adds}></div>
			<div id="dels"><{$dels}></div>
		</div>
	</form>
	</center>
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