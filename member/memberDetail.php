<?php
include_once '../configs/config.class.php';
include_once 'class/intolog.php' ;
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$id = $_REQUEST['id'] ;
$update = $_REQUEST['update'] ;

//預載log物件
$logs = new Intolog() ;
##

//更新權限
if ($update == 'ok') {
	$logs->writelog('memberDetailChange','修改'.$_REQUEST['pName'].'('.$id.')的資料') ;
	
	//取得變數值
	$tName = $_REQUEST['pName'] ;
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
	
	//更新權限
	if ($tJob == '1') {
		$sql = '
			UPDATE
				tPeopleInfo
			SET
				pName="'.$tName.'",
				pJob="'.$tJob.'",
				pGender="'.$_REQUEST['pGender'].'",
				pDep="'.$_REQUEST['pDep'].'",
				pExt="'.$tExt.'",
				pFaxNum="'.$tFaxNum.'",
				pBankCheck="'.$tBankCheck.'",
				pAccList="'.$tAccList.'",
				pApplyCase="'.$tApplyCase.'",
				pCertifiedMoney="'.$tCertifiedMoney.'",
				pFeedBack="'.$tFeedBack.'",
				pFeedBackModify="'.$tFeedBackModify.'",
				pRealtyCharge="'.$tRealtyCharge.'",
				pExpenseIncome="'.$tExpenseIncome.'",
				pRealtyCaseList="'.$tRealtyCaseList.'",
				pStaffManage="'.$tStaffManage.'",
				pBusinessEdit="'.$pBusinessEdit.'",
				pBusinessView="'.$pBusinessView.'"
			WHERE
				pId="'.$id.'"
		' ;
		
		$conn->Execute($sql) ;
	}
	else {
		//取消後台登入資格
		$sql = 'UPDATE tPeopleInfo SET pJob="'.$tJob.'" WHERE pId="'.$id.'";' ;
		$conn->Execute($sql) ;
		##
		
		//將區域負責業務預設改回政耀
		$sql = 'UPDATE tZipArea SET zSales="0" WHERE zSales="'.$id.'";' ;
		$conn->Execute($sql) ;
		##
		
		//將代書負責業務預設改回政耀
		$sql = 'UPDATE tScrivenerSales SET sSales="3", sStage="0" WHERE sSales="'.$id.'";' ;
		$conn->Execute($sql) ;
		##
		
		//將店頭負責業務預設改回政耀
		$sql = 'UPDATE tBranchSales SET bSales="3", bStage="0" WHERE bSales="'.$id.'";' ;
		$conn->Execute($sql) ;
		##
	}
	//$conn->Execute($sql) ;
	##
}
##

//取得被編輯者權限
$sql = 'SELECT * FROM tPeopleInfo WHERE pId="'.$id.'";' ;
$rs = $conn->Execute($sql) ;
$detail = $rs->fields ;
unset($rs) ;
##

//修改傳真號碼顯示方式
if ($detail['pFaxNum']) {
	$tmp = explode('-',$detail['pFaxNum']) ;
	$detail['pFaxNumArea'] = $tmp[0] ;
	$detail['pFaxNum'] = $tmp[1] ;
	unset($tmp) ;
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
if ($update) {
	echo '
		$("#dialog").html("權限已更新!!新權限將於被編輯者下次登入時生效!!")
		$("#dialog").dialog("open") ;
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

/* 更新權限 */
function save() {
	if (checkValid()) {
		$('[name="update"]').val('ok') ;
		$('form[name="membersUpdate"]').submit() ;
	}
}
////

/* 檢查輸入欄位是否正確 */
function checkValid() {
	var u = $('[name="pName"]') ;
	
	if (u.val() == '') {
		alert('使用者姓名不可為空白!!') ;
		u.select() ;
		return false ;
	}
	else {
		return true ;
	}	
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

<form name="membersUpdate" method="POST">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="update">
<!--<div style="font-size:10pt;text-align:left;width:800px;"><a href="#" onclick="newMemberPWD()">重置密碼</a></div>-->
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
			<input type="text" name="pName" class="lock" maxlength="20" style="width:90px;" value="<?=$detail['pName']?>">
		</td>
		<td class="memberCell" style="text-align:left;">
			帳戶擁有者姓名。
		</td>
	</tr>

	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			帳號
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="text" name="pAccount" disabled="disabled" style="width:90px;" value="<?=$detail['pAccount']?>">
		</td>
		<td class="memberCell" style="text-align:left;">
			後台登入帳號；請填入"數字、字母" 4~12 碼，英文字母大小寫視為相異。
		</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			帳號管理
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pJob"<?php if ($detail['pJob'] == '1') { echo ' checked="checked"' ;}?> value="1" onclick="lockAll(false)">
			啟用
			<input type="radio" name="pJob"<?php if ($detail['pJob'] == '2') { echo ' checked="checked"' ;}?> value="2" onclick="lockAll(true)">
			停用
		</td>
		<td class="memberCell" style="text-align:left;">
			啟用或停止帳號登入。
		</td>
	</tr>

	<tr style="background-color:#FFFAFA;">
		<td class="memberCell" style="text-align:center;">
			性別
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pGender" class="lock"<?php if ($detail['pGender'] == 'M') { echo 'checked="checked" ' ;}?> value="M">
			男性
			<input type="radio" name="pGender" class="lock"<?php if ($detail['pGender'] == 'F') { echo 'checked="checked" ' ;}?> value="F">
			女性
		</td>
		<td class="memberCell" style="text-align:left;">
			使用者性別、後台系統自動帶入稱謂用。
		</td>
	</tr>

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
			<input type="text" name="pExt" class="lock" maxlength="3" style="width:100px;" value="<?=$detail['pExt']?>">
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
			<input type="text" name="pFaxNumArea" class="lock" maxlength="2" style="width:20px;" value="<?=$detail['pFaxNumArea']?>">-
			<input type="text" name="pFaxNum" class="lock" maxlength="8" style="width:70px;" value="<?=$detail['pFaxNum']?>">
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
			<input type="radio" name="pBankCheck" class="lock"<?php if ($detail['pBankCheck'] == '1') { echo 'checked="checked" ' ;}?> value="1">
			啟用
			<input type="radio" name="pBankCheck" class="lock"<?php if ($detail['pBankCheck'] == '0') { echo 'checked="checked" ' ;}?> value="0">
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
			<input type="radio" name="pAccList" class="lock"<?php if ($detail['pAccList'] == '1') { echo ' checked="checked"' ; }?> value="1">
			啟用
			<input type="radio" name="pAccList" class="lock"<?php if ($detail['pAccList'] == '0') { echo ' checked="checked"' ; }?> value="0">
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
			<input type="radio" name="pApplyCase" class="lock"<?php if ($detail['pApplyCase'] == '1') { echo ' checked="checked"' ;}?> value="1">
			啟用
			<input type="radio" name="pApplyCase" class="lock"<?php if ($detail['pApplyCase'] == '0') { echo ' checked="checked"' ;}?> value="0">
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
			<input type="radio" name="pCertifiedMoney" class="lock"<?php if ($detail['pCertifiedMoney'] == '1') { echo ' checked="checked"' ;}?> value="1">
			啟用
			<input type="radio" name="pCertifiedMoney" class="lock"<?php if ($detail['pCertifiedMoney'] == '0') { echo ' checked="checked"' ;}?> value="0">
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
			<input type="radio" name="pFeedBack" class="lock"<?php if ($detail['pFeedBack'] == '1') { echo ' checked="checked"' ;}?> value="1">
			啟用
			<input type="radio" name="pFeedBack" class="lock"<?php if ($detail['pFeedBack'] == '0') { echo ' checked="checked"' ;}?> value="0">
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
			<input type="radio" name="pFeedBackModify" class="lock"<?php if ($detail['pFeedBackModify'] == '1') { echo ' checked="checked"' ; }?> value="1">
			啟用
			<input type="radio" name="pFeedBackModify" class="lock"<?php if ($detail['pFeedBackModify'] == '0') { echo ' checked="checked"' ; }?> value="0">
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
			<input type="radio" name="pRealtyCharge" class="lock"<?php if ($detail['pRealtyCharge'] == '1') { echo ' checked="checked"' ;}?> value="1">
			啟用
			<input type="radio" name="pRealtyCharge" class="lock"<?php if ($detail['pRealtyCharge'] == '0') { echo ' checked="checked"' ;}?> value="0">
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
			<input type="radio" name="pExpenseIncome" class="lock"<?php if ($detail['pExpenseIncome'] == '1') { echo ' checked="checked"' ;}?> value="1">
			全部
			<input type="radio" name="pExpenseIncome" class="lock"<?php if ($detail['pExpenseIncome'] == '0') { echo ' checked="checked"' ;}?> value="0">
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
		<input type="radio" name="pRealtyCaseList" class="lock"<?php if ($detail['pRealtyCaseList'] != '0') { echo ' checked="checked"' ;}?> value="1" onclick="HQS(1)">
		啟用
		<input type="radio" name="pRealtyCaseList" class="lock"<?php if ($detail['pRealtyCaseList'] == '0') { echo ' checked="checked"' ;}?> value="0" onclick="HQS(0)">
		停用
		</td>
		<td class="memberCell" style="text-align:left;">
			仲介店案件與保證費資料查詢與下載權限。
			<?php
			$checked = '' ;
			if ($detail['pRealtyCaseList'] == '2') {
				$checked = ' checked="checked"' ;
			}
			$disabled = ' disabled="disabled"' ;
			if ($detail['pRealtyCaseList'] != '0') {
				$disabled = '' ;
			}
			/*
			（<input type="checkbox" name="pRealtyCaseListAdd"<?=$checked?> value="1"<?=$disabled?>>
				可檢視總部成交案件數。）
			*/
			?>
		</td>
	</tr>

	<tr style="background-color:#FFF0F5;">
		<td class="memberCell" style="text-align:center;">
			業績編輯統計
		</td>
		<td class="memberCell" style="text-align:center;">
			<input type="radio" name="pBusinessEdit" class="lock"<?php if ($detail['pBusinessEdit'] == '1') { echo ' checked="checked"' ;}?> value="1">
			啟用
			<input type="radio" name="pBusinessEdit" class="lock"<?php if ($detail['pBusinessEdit'] == '0') { echo ' checked="checked"' ;}?> value="0">
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
			<input type="radio" name="pBusinessView" class="lock"<?php if ($detail['pBusinessView'] == '1') { echo ' checked="checked"' ;}?> value="1">
			啟用
			<input type="radio" name="pBusinessView" class="lock"<?php if ($detail['pBusinessView'] == '0') { echo ' checked="checked"' ;}?> value="0">
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
			<input type="radio" name="pStaffManage" class="lock"<?php if ($detail['pStaffManage'] == '1') { echo ' checked="checked"' ;}?> value="1">
			全部
			<input type="radio" name="pStaffManage" class="lock"<?php if ($detail['pStaffManage'] == '0') { echo ' checked="checked"' ;}?> value="0">
			個人
		</td>
		<td class="memberCell" style="text-align:left;">
			後台功能操作權限。
		</td>
	</tr>

</table>

<div>&nbsp;</div>

<div style="width:800px;">
<input type="button" style="width:100px;" value="更新" onclick="save()">
<input type="button" style="width:100px;" value="返回" onclick="cancel()">
</div>
</form>

</center>
<div id="dialog"></div>
<div id="confirm"></div>
</body>
</html>