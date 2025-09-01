<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta2.inc.tpl'}>
<script src="/js/lib/comboboxNormal.js"></script>
<script src="/js/IDCheck.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		// setComboboxNormal('city select','id');
		// setComboboxNormal('area select','id');

		// $('[name="pName"]').focus() ;
		$( "#subTabs" ).tabs();
		/* 檢核輸入身分證字號是否合法 */	

		// console.log($('[name="IdentifyId"]').val());

			if (checkUID($('[name="IdentifyId"]').val()) ) {
				$('#ssId').html('<img src="/images/ok.png">') ;
			}else {
				$('#ssId').html('<img src="/images/ng.png">') ;
			}
		
		
				
		$('[name="IdentifyId"]').keyup(function() {
			if (checkUID($('[name="IdentifyId"]').val())) {
				$('#ssId').html('<img src="/images/ok.png">') ;
			}else {
				$('#ssId').html('<img src="/images/ng.png">') ;
			}
		}) ;

	}) ;
function save(){

	// if (!checkUID($('[name="IdentifyId"]').val())) {
	// 	alert("請輸入正確的身分證字號")
	// 	return false;
	// }		


	$("[name='membersNew']").submit();
}

function cancel(){
	location.href = 'scrivenerBlackList.php';
}
function del(){
	$("[name='cat']").val("del");
	$("[name='membersNew']").submit();
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

.easyui-tabs table {
    margin-left:auto; 
    margin-right:auto;
 }

.easyui-tabs table th {
    text-align:right;
    background: #E4BEB1;
    padding-top:10px;
    padding-bottom:10px;
    border: white 1px solid;
}
            
.easyui-tabs table th .sml {
    text-align:right;
    background: #E4BEB1;
    padding-top:10px;
    padding-bottom:10px;
    font-size: 10px;
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
.tb-title {
    font-size: 18px;
    padding-left:15px; 
    padding-top:10px; 
    padding-bottom:10px; 
    background: #E4BEB1;
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
							<h1>編輯資料</h1>
							<br>

							<form name="membersNew" method="POST">
								<div id="tt" class="easyui-tabs" style="">
									<div title="資料" style="padding:20px;display:none;">
										<input type="hidden" name="cat" value="<{$cat}>">
										<input type="hidden" name="id" value="<{$id}>">
										<table cellspacing="0" width="100%">
											<tr>
												<th>&nbsp;</th>
												<th colspan="3">&nbsp;</th>
												
											</tr>

											<tr>
												<th>姓名：</th>
												<td colspan="3">
													<input type="text" name="name" class="lock" maxlength="20" value="<{$data.sName}>">
												</td>
												
											</tr>
											<tr>
												<th>事務所名稱：</th>
												<td colspan="3">
													<input type="text" name="office" id="office" value="<{$data.sOffice}>">
												</td>
												
											</tr>
											<tr>
												<th>身分證字號：</th>
												<td colspan="3">
													<input type="text" name="IdentifyId" maxlength="12" value="<{$data.sIdentifyId}>"><span id="ssId"></span>
												</td>
											</tr>

											<tr>
												<th>地址：</th>
												<td colspan="3">
													<input type="hidden" name="zip" id="zip" value="<{$data.sZip}>" />
                            						<input type="text" maxlength="6" name="zipF" id="zipF" class="input-text-sml text-center" disabled="disabled" value="<{$data.sZip|substr:0:3}>" size="3"/>
                           
													<select name="city" id="city" onchange="getArea('city','area','zip')">
														<{$listCity}>
													</select>
													<span id="areaR">
													<select name="area" id="area" onchange="getZip('area','zip')">
														<{$listArea}>
													</select>
													</span>
													<input type="text" name="address" value="<{$data.sAddress}>" style="width:500px">
												</td>
											</tr>
											<tr>
												<th colspan="4"></th>
											</tr>
											<tr>
												<th width="20%">建立者:</th>
												<td ><{$data.creator}></td>
												<th width="20%">建立時間:</th>
												<td><{$data.sCreatTime}></td>
											</tr>
											<tr>
												<th width="20%">最後修改者:</th>
												<td ><{$data.editor}></td>
												<th width="20%">最後修改時間:</th>
												<td><{$data.sEditTime}></td>
											</tr>
										</table>

										
									</div>
									
								</div>

							<div>&nbsp;</div>

							<div style="text-align: center;width: 100%">
							<input type="button" value="儲存" onclick="save()">
							<input type="button" value="返回" onclick="cancel()">
							<input type="button" value="刪除" onclick="del()">
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