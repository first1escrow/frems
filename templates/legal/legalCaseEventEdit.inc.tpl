<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta2.inc.tpl'}>
<script type="text/javascript">
	$(document).ready(function() {
	});
	function save(){
		$("[name='form']").submit();
	}
	function cancel(){
		location.href = "legalCaseEventList.php";
	}
</script>
<style>
ul.tabs {
    width: 100%;
    height: auto;
    border-left: 0px solid #999;
    border-bottom: 1px solid #D99888;
           
}  
ul.tabs li {
    margin: 0;
    padding: 0;
    border: 0;
    font-size: 100%;
    font: inherit;
    vertical-align: baseline;
    height: auto;
}

#subTabs-1,#subTabs-2{
            background-color: #FFF;
        }
.memberTB {
	border: 1px solid #ccc;
	padding: 5px;
	font-size: 10pt;
	font-weight: bold;
	text-align: center;
	background-color: #EEE0E5 ;
}
.memberCell {
	padding: 5px;
	font-size: 9pt;
	text-align: center;
	border: 1px solid #ccc;
}

#table tbody td{
	padding: 5px;
	font-size: 9pt;
	text-align: center;
	border: 1px solid #ccc;
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
						<h3></h3>
						<div id="container">
							<h1>預設事項編輯</h1>
							<br>

							<form name="form" method="POST">
								<div id="tt" style="">
									<div style="padding:20px;">
										<input type="hidden" name="cat" value="<{$cat}>">
										<input type="hidden" name="id" value="<{$id}>">
										<center>
										<table cellspacing="0" width="80%" class="memberTB">
										

											<tr style="background-color:#FFF0F5;" >
												<td class="memberCell" style="text-align:center;">
													天數
												</td>
												<td class="memberCell" style="text-align:left;">
													<!-- <input type="text" name="day" maxlength="20" style="width:90px;" value="<{$data.lDays}>">天 -->
													 <{html_options name=day options=$menu_day selected=$data.lDays}>
												</td>
												
											</tr>

											<tr style="background-color:#FFFAFA;">
												<td class="memberCell" style="text-align:center;">
													事項
												</td>
												<td class="memberCell" style="text-align:left;">
													<textarea name="note" id="" cols="30" rows="10"><{$data.lNote}></textarea>
												</td>
												
											</tr>

											
										</table>
										</center>
										</div>
									</div>
									
								</div>
								
								<div>&nbsp;</div>

								<div style="text-align: center;width: 100%">
									<input type="button" style="width:100px;" value="儲存" onclick="save()">
									<input type="button" style="width:100px;" value="返回" onclick="cancel()">
								</div>
							</form>
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