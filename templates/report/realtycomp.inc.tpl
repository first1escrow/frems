<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta2.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {
	// $('[name="branchA"]').combobox() ;
	// $('[name="branchB"]').combobox() ;
	
	$('#formList').click(function() {
		$('[name="export"]').val('ok') ;
		$('[name="formList"]').submit() ;
	}) ;
	
	$('#formList').button({
		icons:{
			primary: "ui-icon-document"
		}
	}) ;
}) ;

//取得縣市的鄉鎮市
function get_area() {
	var url = 'zipArea.php' ;
	var _city = $('[name="city"] option:selected').val() ;
	
	if (_city == "") {
	
	}
	else {
		$.post(url,{c:_city},function(txt) {
			$('[name="area"]').empty().html(txt) ;
		}) ;
	}
}
////

</script>
<style>
.ui-autocomplete-input {
	width:210px;
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
							<form name="formList" method="POST">
								<div>
									<div style="margin:0px auto;width:500px;text-align:left;padding:10px;border:1px solid #ccc;">
										<div style="height:30px;">
											選擇地區：
											<span>
											<select name="city" style="width:90px;" onchange="get_area()">
											<option value="" selected="selected">全部縣市</option>
											<{$a}>
											</select>
											</span>
											<span>
											<select name="area" style="width:90px;">
											<option value="" selected="selected">全區</option>
											</select>
											</span>
											<span style="margin-left:20px;">
											<input type="checkbox" id="statusOff" name="statusOff" value="1" checked="checked">
											<label for="statusOff">過濾已關店</label>
											</span>
											
											
											
										</div>
										<div style="height:30px;">
											<span style="margin-left:20px;">
											<input type="checkbox" id="twhgBranch" name="twhgBranch" value="1">
											<label for="twhgBranch" style="">台屋加盟</label>
											</span>
											<span style="margin-left:20px;">
											<input type="checkbox" id="twhgBranch" name="twhgBranch2" value="1">
											<label for="twhgBranch2" style="">台屋直營</label>
											</span>
											
											<span style="margin-left:20px;">
											<input type="checkbox" id="other" name="other" value="1">
											<label for="other" style="">其他品牌</label>
											</span>
											
										</div>
										<div style="height:30px;">
											比較時間：
										</div>
										<div style="padding-left:10px;height:30px;">
											比較時間
											民國
											<select name="Af_year" style="width:50px;">
											<{$y}>
											</select>
											年
											<select name="Af_month" style="width:50px;">
											<{$m}>
											</select>
											月
											～
											民國
											<select name="At_year" style="width:50px;">
											<{$y}>
											</select>
											年
											<select name="At_month" style="width:50px;">
											<{$m}>
											</select>
											月
										</div>
										<div style="padding-left:10px;height:30px;">
											對照時間
											民國
											<select name="Bf_year" style="width:50px;">
											<{$y}>
											</select>
											年
											<select name="Bf_month" style="width:50px;">
											<{$m}>
											</select>
											月
											～
											民國
											<select name="Bt_year" style="width:50px;">
											<{$y}>
											</select>
											年
											<select name="Bt_month" style="width:50px;">
											<{$m}>
											</select>
											月
										</div>
										
										<center>
										<div style="height:20px;">
											<span>
												<input type="checkbox" name="incoming" checked="checked" value="1" disabled="disabled">
												履保收入
											</span>
											<span>
												<input type="checkbox" name="numbering" checked="checked" value="1" disabled="disabled">
												進案件數
											</span>
											<span>
												<input type="checkbox" name="HDnumbering" checked="checked" value="1" disabled="disabled">
												總部月成交件數
											</span>
										</div>
										</center>
									</div>
									
									<div style="height:5px;">&nbsp;</div>
									
									<div style="padding-left:10px;margin:0px auto;width:500px;">
										<div style="float:left;width:250px;">
											<div style="text-align:center;width:230px;">A 店家</div>
											<div style="width:230px;">
												<select name="branchA" class="easyui-combobox">
													<option value="" selected="selected">無指定</option>
													<{$b}>
												</select>
											</div>
										</div>
										<div id="showB" style="float:right;width:250px;">
											<div style="text-align:center;width:230px;">B 店家</div>
											<div style="width:230px;">
												<select name="branchB" class="easyui-combobox">
													<option value="" selected="selected">請選擇店家</option>
													<{$b}>
												</select>
											</div>
										</div>
									</div>
									
									<div style="height:50px;">&nbsp;</div>
									
									<div style="text-align:center;">
										<input type="hidden" name="export">
										<!--<input type="button" value="輸出報表" style="width:100px;" onclick="formList()">-->
										<button id="formList">輸出報表</button>
									</div>
									
									<div style="height:10px;">&nbsp;</div>
									
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