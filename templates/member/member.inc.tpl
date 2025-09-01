<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta2.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {	
	<{$alert}>
	$(".iframe").colorbox({iframe:true, width:"85%", height:"80%"}) ;
}) ;

/* 變更密碼 */
function modify() {
	var o = $('[name="memberOld"]').val() ;				//舊密碼
	var n1 = $('[name="memberNew"]').val() ;			//新密碼
	var n2 = $('[name="memberRe"]').val() ;				//重複新密碼
	
	/* 檢核舊密碼格式 */
	if (checkFormat(o)) {
		return false ;
	}
	////
	
	/* 檢核新密碼格式 */
	if (checkFormat(n1)) {
		return false ;
	}
	////
	
	/* 檢核舊密碼長度 */
	if (checkLength(n1)) {
		return false ;
	}
	////
	
	/* 檢核新密碼長度 */
	if (checkLength(n2)) {
		return false ;
	}
	////
	
	if (n1 == n2) {
		$('[name="save"]').val('ok') ;
		$('[name="formList"]').submit() ;

	}
	else {
		alert('新密碼輸入不一致!!')
		clearAll() ;
	}
}
////

/* 檢核字串是否符合格式 */
function checkFormat(str) {
	var patt = /^\w+$/ ;
	
	if (!patt.test(str)) {
		alert('密碼須為英文字母或數字!!') ;
		clearAll() ;
		return true ;
	}
	else {
		return false ;
	}
	
}
////

/* 檢核密碼長度 */
function checkLength(str) {
	var l = str.length ;
	if (l < 4) {
		alert('密碼長度不可小於4個字!!') ;
		clearAll() ;
		return true ;
	}
	else {
		return false ;
	}
}
////

/* 清除所有密碼欄位 */
function clearAll() {
	$('[name="memberOld"]').val('') ;
	$('[name="memberNew"]').val('') ;
	$('[name="memberRe"]').val('') ;
	$('[name="save"]').val('') ;
	$('[name="memberOld"]').focus() ;
}
////

</script>
<style>
.ui-autocomplete-input {
	width:210px;
}
.iframe {
	font-size: 10pt;
}

.btn-block {
    width:100px;
    margin:10px;
    padding:10px;
    border:1px solid #ccc;
    text-align:center;
    border-radius:5px;
}

.btn-area {
    display:flex;
    justify-content:center;
    width:600px;
    margin:0 auto;
    /* border:1px solid #000; */
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
							<td width="81%" align="right">&nbsp;</td>
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

						<div id="container">
                            <h1 style="text-align:left;">員工資料</h1>
                            <br>
							<form name="formList" method="POST">
								<div>
                                    <div class="btn-area">
                                        <div class="btn-block">
                                            <a href="memberTable.php" >員工管理</a>
                                        </div>
                                        <div class="btn-block">
                                            <a href="memberDepAuthorityList.php">部門權限</a>
                                        </div>
                                    </div>
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