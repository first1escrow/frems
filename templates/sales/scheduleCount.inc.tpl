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
	
	var tt = $('#tab').val() ;
	if (tt == '2') {
		$('#perYear').hide() ;
		$('#perMonth').show() ;
	}
	else {
		$('#perYear').show() ;
		$('#perMonth').hide() ;
	}
});

function tabs(no) {
	$('#tab').val(no) ;
	$('#myform').submit() ;
}

function tab1() {
	tabs($('#tab').val()) ;
}

function goto(yy, mm ,pp) {
	var url = 'scheduleList.php?yy=' + yy + '&mm=' + mm + '&pp=' + pp ;
	$.colorbox({iframe:true, width:"1000px", height:"100%", href:url}) ; 
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
#qScr {
	-webkit-border-radius: 10px;
	/* support firefox */
	-moz-border-radius: 10px;
	border-radius: 10px;
}

th {
	padding: 10px;
}

td {
	padding: 5px;
}

#tabChoose a:hover {
	font-size: 14pt;
	color: #DC143C;
}

table.YourClass tr:hover td {
	font-size: 14pt;
	font-weight: bold;
	background-color: #DAA520;
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

<div style="">
	<form id="myform" method="POST">
		<input type="hidden" id="tab" name="tab" value="<{$tab}>">
		<div id="tabChoose" style="margin-bottom:5px;">
			<{if $tab eq "1"}>
				<a href="Javascript: tabs(1)" style="font-weight:bold;font-size:14pt;">年度</a>
			<{else}>
				<a href="Javascript: tabs(1)" style="">年度</a>
			<{/if}>
			|
			<{if $tab eq "2"}>
				<a href="Javascript: tabs(2)" style="font-weight:bold;font-size:14pt;">本月</a></div>
			<{else}>
				<a href="Javascript: tabs(2)" style="">本月</a>
			<{/if}>
		</div>
		<hr>
		<div>
			<div id="perYear" style="display:;">
				<div style="text-align:left;margin-bottom:10px;margin-top:10px;">
					<select name="years" onchange="tab1()">
						<{$yr}>
					</select>
					年度
				</div>
				
				<div>
					<table id="yr" class="YourClass" style="width:900px;border:1px solid #ccc;">
					<{$tables}>
					</table>
				</div>
			</div>
			
			<div id="perMonth" style="display:none;">
				<div style="margin-bottom:10px;margin-top:10px;">
					<select name="months">
						<{$mn}>
					</select>
					月份
				</div>
				
				<div>
					<table id="mn" class="YourClass" style="width:900px;border:1px solid #ccc;">
					<{$tables}>
					</table>
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