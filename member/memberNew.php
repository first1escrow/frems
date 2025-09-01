<?php
include_once '../configs/config.class.php';
include_once 'class/intolog.php' ;
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$new = $_REQUEST['new'] ;

//預載log物件
$logs = new Intolog() ;
##

//更新權限
if ($new == 'ok') {
	$logs->writelog('memberNew','建立'.$_REQUEST['pName'].'的資料') ;
	
	include_once '../genPWD.php' ;
	
	//取得變數值
	$tName = $_REQUEST['pName'] ;
	$pGender = $_REQUEST['pGender'] ;
	$tAccount = $_REQUEST['pAccount'] ;
	$tJob = $_REQUEST['pJob'] ;
	$tExt = $_REQUEST['pExt'] ;
	$tFaxNumArea = $_REQUEST['pFaxNumArea'] ;
	$tFaxNum = $_REQUEST['pFaxNum'] ;
	$tBankCheck = $_REQUEST['pBankCheck'] ;
	$tAccList = $_REQUEST['pAccList'] ;
	$tApplyCase = $_REQUEST['pApplyCase'] ;
	$tCertifiedMoney = $_REQUEST['pCertifiedMoney'] ;
	$tFeedBack = $_REQUEST['pFeedBack'] ;
	$tFeedBackModify = $_REQUEST['pFeedBackModify'] ;
	$tRealtyCharge = $_REQUEST['pRealtyCharge'] ;
	$tExpenseIncome = $_REQUEST['pExpenseIncome'] ;
	$tRealtyCaseList = $_REQUEST['pRealtyCaseList'] ;
	$tRealtyCaseListAdd = $_REQUEST['pRealtyCaseListAdd'] ;
	$tStaffManage = $_REQUEST['pStaffManage'] ;
	$pBusinessEdit = $_REQUEST['pBusinessEdit'] ;
	$pBusinessView = $_REQUEST['pBusinessView'] ;
	$pwd = $_REQUEST['pwd'] ;
	$pp = '' ;
	
	if (!$pwd) {
		$pwd = genPwd(12) ;		//若未指定密碼，則自動產出一組密碼
		$pp = '\n新帳戶密碼為：'.$pwd.'。' ;
	}
	##
	
	//調整傳真號碼格式
	if ($tFaxNumArea) {
		$tFaxNum = $tFaxNumArea.'-'.$tFaxNum ;
	}
	##
	
	//調整仲介選項
	if ($tRealtyCaseListAdd) {
		$tRealtyCaseList += $tRealtyCaseListAdd + 1 - 1 ;
	}
	##
	
	//新增帳戶
	$sql = '
		INSERT INTO
			tPeopleInfo
		(
			pName,
			pGender,
			pAccount,
			pJob,
			pPassword,
			pDep,
			pExt,
			pFaxNum,
			pBankCheck,
			pAccList,
			pApplyCase,
			pCertifiedMoney,
			pFeedBack,
			pFeedBackModify,
			pRealtyCharge,
			pExpenseIncome,
			pRealtyCaseList,
			pStaffManage,
			pBusinessEdit,
			pBusinessView
		)
		VALUES
		(
			"'.$tName.'",
			"'.$pGender.'",
			"'.$tAccount.'",
			"'.$tJob.'",
			"'.$pwd.'",
			"'.$_REQUEST['pDep'].'",
			"'.$tExt.'",
			"'.$tFaxNum.'",
			"'.$tBankCheck.'",
			"'.$tAccList.'",
			"'.$tApplyCase.'",
			"'.$tCertifiedMoney.'",
			"'.$tFeedBack.'",
			"'.$tFeedBackModify.'",
			"'.$tRealtyCharge.'",
			"'.$tExpenseIncome.'",
			"'.$tRealtyCaseList.'",
			"'.$tStaffManage.'",
			"'.$pBusinessEdit.'",
			"'.$pBusinessView.'"
		)
	' ;
	
	$conn->Execute($sql) ;
	
	//header("location:memberTable.php") ;
	##
}
##

//單位部門選單
$depMenu = '<option value=""></option>' ;
$sql = 'SELECT * FROM tDepartment WHERE dId!="1" ORDER BY dId ASC' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$_no = $rs->fields['dId'] ;
	$depMenu .= '<option value="'.$_no.'"' ;
	if ($detail['pDep'] == $_no) $depMenu .= ' selected="selected"' ;
	$depMenu .= '>'.$rs->fields['dTitle'] ;
	
	if (($_no != '1') && ($_no != '2') && ($_no != '3')) $depMenu .= '('.$rs->fields['dDep'].')' ;
	
	$depMenu .= "</option>\n" ;
	
	$rs->MoveNext() ;
}
unset($_no) ;
##
?>
<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<link rel="stylesheet" href="/css/colorbox.css" />
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="/libs/jquery.colorbox-min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('[name="pName"]').focus() ;
	
	$('#dialog').dialog({
		modal: true,
		autoOpen: false,
		buttons:{
			"OK": function() {
				$(this).dialog("close") ;
			}
		}
	}) ;
<?php
$lock = 'false' ;
if ($new == 'ok') {
	echo '
		alert("'.$tAccount.' 帳戶新增成功!!'.$pp.'\n\n帳戶登入後請立即變更密碼!!") ;
		location = "memberTable.php" ;
	' ;
}

if ($detail['pJob'] == '2') {
	$lock = 'true' ;
}
?>
	lockAll(<?=$lock?>) ;

}) ;

/* 重置帳戶密碼 */
function newMemberPWD() {
	$('#confirm').html("<div>一旦執行後，系統將重新產生一組新密碼<br>並於用戶下次登入時生效!!</div><br><h3 style='font-weight:bold;color:red;'>確認是否仍要執行??</h3>") ;
	$('#confirm').prop("title","您正要重新製作此帳戶的密碼!!") ;
	$('#confirm').dialog({
		modal: true,
		buttons: {
			"確認": function() {
				$(this).dialog("close") ;
				var url = 'memberPWD.php' ;
				$.post(url,{'id':'<?=$id?>'},function(txt) {
					$('#dialog').html('新密碼:'+txt) ;
					$('#dialog').dialog("open") ;
				}) ;
			},
			"取消": function() {
				$(this).dialog("close") ;
			}
		}
	}) ;
}
////

/* 檢核字串是否符合格式 */
function checkFormat(str) {
	var patt = /^[a-zA-Z0-9]+$/ ;
	
	if (!patt.test(str)) {
		return true ;
	}
	else {
		return false ;
	}
}
////

/* 檢核字串長度 */
function checkLength(str) {
	
	re = /^.{4,12}$/
	
	if (!re.test(str)) {
		return true ;
	}
	else {
		return false ;
	}
}
////

/* 確認總部案件選項 */
function HQS(no) {
	if (no == 0) {
		$('[name="pRealtyCaseListAdd"]').prop("checked",false) ;
		$('[name="pRealtyCaseListAdd"]').prop("disabled",true) ;
	}
	else {
		$('[name="pRealtyCaseListAdd"]').prop("disabled",false) ;
	}
}
////

/* 返回權限列表 */
function cancel() {
	$('form[name="members"]').submit() ;
}
////

/* 新增帳戶 */
function save() {
	var pn = $('[name="pName"]').val() ;
	var pa = $('[name="pAccount"]').val() ;
	var pp = $('[name="pwd"]').val() ;
	
	if (pn == '') {
		alert('請確認輸入使用者名稱!!') ;
		focusOn('pName') ;
		return false ;
	}
	
	if (checkFormat(pa)) {
		alert('請確認輸入帳號須為英文字母或數字!!') ;
		focusOn('pAccount') ;
		return false ;
	}
	
	if (checkLength(pa)) {
		alert('請確認輸入帳號長度需 4~12 碼!!') ;
		focusOn('pAccount') ;
		return false ;
	}
	
	if (pp != '') {
		if (checkFormat(pp)) {
			alert('請確認輸入密碼須為英文字母或數字!!') ;
			focusOn('pwd') ;
			return false ;
		}
		
		if (checkLength(pp)) {
			alert('請確認輸入密碼長度需 4~12 碼!!') ;
			focusOn('pwd') ;
			return false ;
		}
	}
	
	$('[name="new"]').val('ok') ;
	$('form[name="membersNew"]').submit() ;
	
}
////

/* 選取欄位 */
function focusOn(fds) {
	var fields = $('[name="'+fds+'"]') ;
	fields.select() ;
	fields.focus() ;
}
////

/* 凍結帳戶所有權限 */
function lockAll(tre) {
	if (tre) {
		$('.lock').each(function() {
			$(this).prop("disabled",true) ;
		}) ;
		$('[name="pRealtyCaseListAdd"]').prop("disabled",true) ;
	}
	else {
		$('.lock').each(function() {
			$(this).prop("disabled",false) ;
		}) ;
		
		var cs = $('[name="pRealtyCaseList"]:checked').val() ;
		if (cs == '1') {
			$('[name="pRealtyCaseListAdd"]').prop("disabled",false) ;
		}
		else {
			$('[name="pRealtyCaseListAdd"]').prop("disabled",true) ;
		}
		
	}
}
////

</script>
<style>
body {
	margin: 0px;
	padding: 0px;
}
.memberTB {
	border: 1px solid #ccc;
	margin: 0px auto;
	padding: 5px;
	font-size: 10pt;
	font-weight: bold;
	text-align: center;
	background-color: #EEE0E5 ;
}
.memberCell {
	width: 60px;
	padding: 5px;
	font-size: 9pt;
	border: 1px solid #ccc;
}
div {
	text-align:center;
	margin:0 auto;
	padding: 5px;
}
table {
	border:0px;
	padding:0px;
	margin:0px;
}
td {
	margin: 0px;
}
</style>
</head>
<body>

<center>

<form name="members" method="POST" action="memberTable.php">
</form>

<form name="membersNew" method="POST">
<input type="hidden" name="new">
<table cellspacing="0" width="800px;">
	<tr>
		<td class="memberTB" style="width:100px;">&nbsp;</td>
		<td class="memberTB" style="width:100px;">權限／資訊</td>
		<td class="memberTB" style="width:600px;">說明</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			使用者
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="text" name="pName" class="lock" maxlength="20" style="width:90px;" value="">
		</td>
		<td class="memberCell" style="text-align:left;">
			帳戶擁有者姓名。
		</td>
	</tr>

	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			性別
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pGender" value="M">
			男性
			<input type="radio" name="pGender" checked="checked" value="F">
			女性
		</td>
		<td class="memberCell" style="text-align:left;">
			使用者性別、後台系統自動帶入稱謂用。
		</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			帳號
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="text" name="pAccount" maxlength="12" style="width:90px;" value="">
		</td>
		<td class="memberCell" style="text-align:left;">
			後台登入帳號；請填入"數字、字母" 4~12 碼，英文字母大小寫視為相異。
		</td>
	</tr>

	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			密碼
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="password" name="pwd" style="width:90px;" maxlength="12" value="">
		</td>
		<td class="memberCell" style="text-align:left;">
			後台登入密碼；請填入"數字、字母" 4~12 碼，英文字母大小寫視為相異。若未輸入密碼、則系統將自動產生一組密碼。
		</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			帳號管理
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pJob" value="1">
			啟用
			<input type="radio" name="pJob" checked="checked" value="2">
			停用
		</td>
		<td class="memberCell" style="text-align:left;">
			啟用或停止帳號登入。
		</td>
	</tr>

<!-- 	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			性別
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pGender" class="lock"<?php if ($detail['pGender'] == 'F') { echo 'checked="checked" ' ;}?> value="F">
			女
			<input type="radio" name="pGender" class="lock"<?php if ($detail['pGender'] == 'M') { echo 'checked="checked" ' ;}?> value="M">
			男
		</td>
		<td class="memberCell" style="text-align:left;">
			後台系統自動帶入稱謂用。
		</td>
	</tr> -->

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			部門
		</td>
		<td class="memberCell" style="text-align:center;">
			<select name="pDep">
			<?=$depMenu?>
			</select>
		</td>
		<td class="memberCell" style="text-align:left;">
			使用者部門單位。
		</td>
	</tr>

	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			分機
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="text" name="pExt" class="lock" maxlength="3" style="width:100px;" value="">
		</td>
		<td class="memberCell" style="text-align:left;">
			使用者電話分機號碼。
		</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			傳真號碼
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="text" name="pFaxNumArea" class="lock" maxlength="2" style="width:20px;" value="">-
			<input type="text" name="pFaxNum" class="lock" maxlength="8" style="width:70px;" value="">
		</td>
		<td class="memberCell" style="text-align:left;">
			使用者傳真號碼。(xx-xxxxxxxx)
		</td>
	</tr>

	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			出款審核
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pBankCheck" class="lock" value="1">
			啟用
			<input type="radio" name="pBankCheck" class="lock" checked="checked" value="0">
			停用
		</td>
		<td class="memberCell" style="text-align:left;">
			審核出款案件與出款功能權限。
		</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			會計功能
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pAccList" class="lock" value="1">
			啟用
			<input type="radio" name="pAccList" class="lock" checked="checked" value="0">
			停用
		</td>
		<td class="memberCell" style="text-align:left;">
			會計相關功能權限。
		</td>
	</tr>

	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			案件統計
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pApplyCase" class="lock" value="1">
			啟用
			<input type="radio" name="pApplyCase" class="lock" checked="checked" value="0">
			停用
		</td>
		<td class="memberCell" style="text-align:left;">
			案件資料查詢與下載權限。
		</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			保證費統計
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pCertifiedMoney" class="lock" value="1">
			啟用
			<input type="radio" name="pCertifiedMoney" class="lock" checked="checked" value="0">
			停用
		</td>
		<td class="memberCell" style="text-align:left;">
			保證費資料統計與下載權限。
		</td>
	</tr>

	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			回饋統計
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pFeedBack" class="lock" value="1">
			啟用
			<input type="radio" name="pFeedBack" class="lock" checked="checked" value="0">
			停用
		</td>
		<td class="memberCell" style="text-align:left;">
			回饋案件資料查詢與下載權限。
		</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			回饋金統計
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pFeedBackModify" class="lock" value="1">
			啟用
			<input type="radio" name="pFeedBackModify" class="lock" checked="checked" value="0">
			停用
		</td>
		<td class="memberCell" style="text-align:left;">
			調整回饋金金額權限。
		</td>
	</tr>

	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			直營服務費統計
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pRealtyCharge" class="lock" value="1">
			啟用
			<input type="radio" name="pRealtyCharge" class="lock" checked="checked" value="0">
			停用
		</td>
		<td class="memberCell" style="text-align:left;">
			直營服務費案件資料查詢與下載權限。
		</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			配帳畫面顯示
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pExpenseIncome" class="lock" value="1">
			全部
			<input type="radio" name="pExpenseIncome" class="lock" checked="checked" value="0">
			個人
		</td>
		<td class="memberCell" style="text-align:left;">
			配帳畫面顯示方式(個人或全部)權限。
		</td>
	</tr>

	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			仲介店比較
		</td>
		<td class="memberCell" style="text-align:center;">
		<input type="radio" name="pRealtyCaseList" class="lock" value="1" onclick="HQS(1)">
		啟用
		<input type="radio" name="pRealtyCaseList" class="lock" checked="checked" value="0" onclick="HQS(0)">
		停用
		</td>
		<td class="memberCell" style="text-align:left;">
			仲介店案件與保證費資料查詢與下載權限。
			<!--（<input type="checkbox" name="pRealtyCaseListAdd" value="1" disabled="disabled">
				可檢視總部成交案件數。）-->
		</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			業績編輯統計
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pBusinessEdit" class="lock" value="1">
			啟用
			<input type="radio" name="pBusinessEdit" class="lock" checked="checked" value="0">
			停用
		</td>
		<td class="memberCell" style="text-align:left;">
			可指定預設的業績歸屬業務人員並可統計產出業績報表。
		</td>
	</tr>

	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			個案業績指定
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pBusinessView" class="lock" value="1">
			啟用
			<input type="radio" name="pBusinessView" class="lock" checked="checked" value="0">
			停用
		</td>
		<td class="memberCell" style="text-align:left;">
			可編輯個別案件的業績歸屬業務人員。
		</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			權限管理
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pStaffManage" class="lock" value="1">
			全部
			<input type="radio" name="pStaffManage" class="lock" checked="checked" value="0">
			個人
		</td>
		<td class="memberCell" style="text-align:left;">
			後台功能操作權限。
		</td>
	</tr>

</table>

<div>&nbsp;</div>

<div style="width:800px;">
<input type="button" style="width:100px;" value="新增" onclick="save()">
<input type="button" style="width:100px;" value="返回" onclick="cancel()">
</div>
</form>

</center>
<div id="dialog"></div>
<div id="confirm"></div>
</body>
</html>