<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$id = (empty($_POST['id']))?$_GET['id']:$_POST['id'];
$cat = (empty($_POST['cat']))?$_GET['cat']:$_POST['cat'];

if (!empty($_POST)) {

	if ($cat == 'add') {
		foreach ($_POST as $k => $v) {
			$str[]= $k.'="'.$v.'"';
		}

		$sql = "INSERT INTO tPeopleInfo SET ".implode(',', $str);
		$conn->Execute($sql);
		$id = $conn->Insert_ID(); 

	}elseif ($cat == 'modify') {
		

		$sql = "UPDATE tPeopleInfo SET".implode(',', $str);
		$conn->Execute($sql);
	}
	// print_r($_POST);
	
	
	// echo $sql;
}

##
$sql = "SELECT * FROM tPowerList ORDER BY pId ASC";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$optionMenu[$rs->fields['pId']] = $rs->fields['pTitle'];

	$rs->MoveNext();
}
##

$sql = "SELECT * FROM tPeopleInfo WHERE pId = '".$id."'";


$rs = $conn->Execute($sql);

$data = $rs->fields;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>新增</title>
	<script src="../../js/jquery-1.10.2.min.js"></script>
	<script>
		$(document).ready(function() {
		});

		function changeTitle(){
			var val = $('[name="pDep"]').val();
			$.ajax({
				url: 'titlePower.php',
				type: 'POST',
				dataType: 'html',
				data: {id: val},
			})
			.done(function(msg) {

				var obj = JSON.parse(msg);
				// console.log(msg);
				$.each(obj, function(index, val) {
					// console.log(index+"_"+val);
					$('input:radio[name="'+index+'"]').filter('[value="'+val+'"]').attr('checked',true) ;
					
				});
				
			});
			
		}
	</script>
	<style>
	body{
		float :center;

	}
	.section{
		width: 100%;
		float :center;
	}
	.title{
		background-color:#E4BEB1;
		border: 1px solid #999;
		padding: 5px;
	}
	.content{
		border:1px #FFF solid;
		border: 1px solid #CCC;
		/*background-color: #F8ECE9;*/
	}
	.item-title{		
		width: 28%;
		background-color: #F8ECE9;
		display: inline-block;
		text-align: right;
		
		line-height: 40px;
		height: 40px;

	}
	.item-field{
		
		display: inline-block;
		background-color: #FFF;
		line-height: 40px;
		height: 40px;
		
	}
	.xxx-input {
		color:#666666;
		font-size:16px;
		font-weight:normal;
		background-color:#FFFFFF;
		text-align:left;
		height:34px;
		padding:0 5px;
		border:1px solid #CCCCCC;
		border-radius: 0.35em;
	}
	.xxx-input:focus {
		border-color: rgba(82, 168, 236, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
		-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
		outline: 0 none;
	}
	/*ie8-9下拉清單*/
	.xxx-select {
		color:#666666;
		font-size:16px;
		font-weight:normal;
		background-color:#FFFFFF;
		text-align:left;
		height:34px;
		padding:0 0px 0 5px;
		border:1px solid #CCCCCC;
		border-radius: 0em;
	}
	/*moz下拉清單*/
	@-moz-document url-prefix(){
	.xxx-select {
		color:#666666;
		font-size:16px;
		font-weight:normal;
		background-color:#FFFFFF;
		background-image:url("images/select_icon001.png");
		background-repeat:no-repeat;
		background-position:center right;
		background-size: 18px auto;
		text-align:left;
		height:34px;
		padding:0 23px 0 5px;
		border:1px solid #CCCCCC;
		border-radius: 0.35em;
		appearance: none;
		-webkit-appearance: none;
		-moz-appearance: none;
		-ms-appearance: none;
		-o-appearance: none;
	}
	.bg1-select {
		background-image:url("images/select_icon001.png");
		background-size: 22px auto;
		padding:0 32px 0 5px;
	}
	}
	/*google下拉清單*/
	@media screen and (-webkit-min-device-pixel-ratio:0){
	.xxx-select {
		color:#666666;
		font-size:16px;
		font-weight:normal;
		background-color:#FFFFFF;
		background-image:url("images/select_icon001.png");
		background-repeat:no-repeat;
		background-position:center right;
		background-size: 18px auto;
		text-align:left;
		height:34px;
		padding:0 23px 0 5px;
		border:1px solid #CCCCCC;
		border-radius: 0.35em;
		appearance: none;
		-webkit-appearance: none;
		-moz-appearance: none;
		-ms-appearance: none;
		-o-appearance: none;
	}
	.bg1-select {
		background-image:url("images/select_icon001.png");
		background-size: 22px auto;
		padding:0 32px 0 5px;
	}
	}
	/*ie10-11下拉清單*/
	@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none){
	.xxx-select {
		color:#666666;
		font-size:16px;
		font-weight:normal;
		background-color:#FFFFFF;
		background-image:url("images/select_icon001.png");
		background-repeat:no-repeat;
		background-position:center right;
		background-size: 18px auto;
		text-align:left;
		height:34px;
		padding:0 23px 0 5px;
		border:1px solid #CCCCCC;
		border-radius: 0.35em;
		appearance: none;
		-webkit-appearance: none;
		-moz-appearance: none;
		-ms-appearance: none;
		-o-appearance: none;
	}
	.xxx-select::-ms-expand {
		display: none;
	}
	.bg1-select {
		background-image:url("images/select_icon001.png");
		background-size: 22px auto;
		padding:0 32px 0 5px;
	}
	}
	.xxx-select:focus {
		border-color: rgba(82, 168, 236, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
		-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
		outline: 0 none;
	}
	.xxx-button {
		background-color: #a63c38;
	    border: 1px solid #a63c38;
	    border-radius: 0;
	    font-weight: bold;
	    padding: 0 50px;
	    margin: 40px auto 40px auto;
	    color:#FFF;
	    line-height: 40px;
	    text-align: center;
	    cursor: pointer;

	}
	
	.bt{
		width: 70%;
		float :center;
		text-align: center;
	}

	</style>
</head>
<body>
	
	<form action="" method="POST">
	<input type="hidden" name="id" value="">
	<!-- 基本資料 -->
	<div class="section">
		<div class="title">基本資料</div>
		<div class="content">
				<div class="item">
					<div class="item-title">姓名：</div>
					<div class="item-field"><input type="text" name="pName" class="xxx-input" value="<?=$data['pName']?>" /></div>      
				</div>
				<div class="item">
					<div class="item-title">職稱：</div>
					<div class="item-field">
						<select name="pDep" id="" onchange="changeTitle()" class="xxx-select">
							<?php
							foreach ($optionMenu as $k => $v) { 
								if ($k == $data['pDep']) { ?>
									<option value="<?=$k?>" selected><?=$v?></option>
							<?php }else{ ?>
									<option value="<?=$k?>"><?=$v?></option>
							<?php }
							?>
								
							<?php }
							?>
						</select>
					</div>      
				</div>
				<div class="item">
					<div class="item-title">帳號：</div>
					<div class="item-field"><input type="text" name="pAccount" class="xxx-input" value="<?=$data['pAccount']?>"/></div>      
				</div>
				<div class="item">
					<div class="item-title">密碼：</div>
					<div class="item-field"><input type="password" name="pPassword" class="xxx-input" value="<?=$data['pPassword']?>"/></div>      
				</div>
				<div class="item">
					<div class="item-title">性別：</div>
					<div class="item-field">
						<?php
							if ($data['pGender'] == 'F') {
								$check = '';
								$check2 = 'checked=checked';
							}else{
								$check = 'checked=checked';
								$check2 = '';
								
							}
						?>
						<input type="radio" name="pJob" value="M" <?=$check?>> 男
						<input type="radio" name="pJob" value="F" <?=$check2?>>女
					</div>      
				</div>
				<div class="item">
					<div class="item-title">是否有效：</div>
					<div class="item-field">
						<?php
							if ($data['pJob'] == 1) {
								$check = 'checked=checked';
								$check2 = '';
							}else{
								
								$check = '';
								$check2 = 'checked=checked';
							}
						?>
						<input type="radio" name="pJob" value="1" <?=$check?>> 是 
						<input type="radio" name="pJob" id="" value="2" <?=$check2?>> 否
					</div>      
				</div>
				<div class="item">
					<div class="item-title">是否為正式員工：</div>
					<div class="item-field">
						<?php
							if ($data['pCategory'] == 1) {
								$check = 'checked=checked';
								$check2 = '';
							}else{
								
								$check = '';
								$check2 = 'checked=checked';
							}
						?>
						<input type="radio" name="pCategory" value="1" <?=$check?>>是
						<input type="radio" name="pCategory" value="2" <?=$check2?>>否
					</div>      
				</div>
				<div class="item">
					<div class="item-title">分機：</div>
					<div class="item-field"><input type="text" name="pExt" class="xxx-input" value="<?=$data['pExt']?>" /></div>      
				</div>
				<div class="item">
					<div class="item-title">傳真號碼：</div>
					<div class="item-field"><input type="text" name="pFaxNum" class="xxx-input" value="<?=$data['pFaxNum']?>" /></div>      
				</div>
				
				<div class="item">
					<div class="item-title">email帳號：</div>
					<div class="item-field"><input type="text" name="pEmailAccount" class="xxx-input" value="<?=$data['pEmailAccount']?>" /></div>      
				</div>
				<div class="item">
					<div class="item-title">HiFax帳號：</div>
					<div class="item-field"><input type="text" name="pHiFaxAccount" class="xxx-input" value="<?=$data['pHiFaxAccount']?>" /></div>      
				</div>
			
		</div>
	</div>
	<!-- 基本資料 -->
	<!-- 回饋金 -->
	<div class="section">
		<div class="title">回饋金</div>
		<div class="content">
			<div class="item">
				<div class="item-title">回饋金調整權限：</div>
				<div class="item-field">
					<?php
					if ($data['pFeedBackModify'] == 0) {
						$check = 'checked=checked';
						$check2 = '';
						$check3 = '';
					}elseif($data['pFeedBackModify'] == 1){
						$check = '';
						$check2 = 'checked=checked';
						$check3 = '';
					}else{
						$check = '';
						$check2 = '';
						$check3 = 'checked=checked';
								
					}
					?>
					<input type="radio" name="pFeedBackModify" value="0" <?=$check?>>無權限
					<input type="radio" name="pFeedBackModify" value="1" <?=$check2?>>最大
					<input type="radio" name="pFeedBackModify" value="2" <?=$check3?>>限觀看
				</div>      
			</div>

		</div>
	</div>
	<!-- 回饋金 -->
	<!-- 會計作業 -->
	<div class="section">
		<div class="title">會計作業</div>
		<div class="content">
			<div class="item">
				<div class="item-title">會計作業全部：</div>
				<div class="item-field">
					<?php
					if ($data['pAccList'] == 0) {
						$check = 'checked=checked';
						$check2 = '';	
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pAccList" value="0" <?=$check?>>無權限
					<input type="radio" name="pAccList" value="1" <?=$check2?>>有權限
				</div>
			</div>
			<div class="item">
				<div class="item-title">發票：</div>
				<div class="item-field">
					<?php
					if ($data['pInvoice'] == 1) {
						$check = 'checked=checked';
						$check2 = '';	
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pInvoice" value="1" <?=$check?>>有
					<input type="radio" name="pInvoice" value="2" <?=$check2?>>無
				</div>
			</div>
			<div class="item">
				<div class="item-title">銀行點交單：</div>
				<div class="item-field">
					<?php
					if ($data['pBank_checklist'] == 1) {
						$check = 'checked=checked';
						$check2 = '';	
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pBank_checklist" value="1" <?=$check?>>有
					<input type="radio" name="pBank_checklist" value="2" <?=$check2?>>無
				</div>
			</div>
			<div class="item">
				<div class="item-title">報稅：</div>
				<div class="item-field">
					<?php
					if ($data['pTax'] == 1) {
						$check = 'checked=checked';
						$check2 = '';	
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pTax" value="1" <?=$check?>>有
					<input type="radio" name="pTax" value="0" <?=$check2?>>無
				</div>
			</div>
		</div>
	</div>

	<!-- 會計作業 -->

	<!-- 案件管理 -->
	<div class="section">
		<div class="title">案件管理</div>
		<div class="content">
			<div class="item">
				<div class="item-title">案件管理：</div>
				<div class="item-field">
					<?php
					if ($data['pCaseManage'] == 1) {
						$check = 'checked=checked';
						$check2 = '';	
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pCaseManage" value="1" <?=$check?>>有
					<input type="radio" name="pCaseManage" value="0" <?=$check2?>>無
				</div>
			</div>
			<div class="item">
				<div class="item-title">申請保號：</div>
				<div class="item-field">
					<?php
					if ($data['pCreatCertifiedId'] == 1) {
						$check = 'checked=checked';
						$check2 = '';	
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pCreatCertifiedId" value="1" <?=$check?>>有
					<input type="radio" name="pCreatCertifiedId" value="0" <?=$check2?>>無
				</div>
			</div>
			<div class="item">
				<div class="item-title">合約書修改權限：</div>
				<div class="item-field">
					<?php
					if ($data['pModifyCase'] == 1) {
						$check = 'checked=checked';
						$check2 = '';	
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pModifyCase" value="1" <?=$check?>>有
					<input type="radio" name="pModifyCase" value="0" <?=$check2?>>無
				</div>
			</div>
			<div class="item">
				<div class="item-title">業務標籤編輯：</div>
				<div class="item-field">
					<?php
					if ($data['pBusinessView'] == 0) {
						$check = 'checked=checked';
						$check2 = '';	
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pBusinessView" value="0" <?=$check?>>無權限
					<input type="radio" name="pBusinessView" value="1" <?=$check2?>>有權限
				</div>
			</div>
			<div class="item">
				<div class="item-title">入賬檔顯示：</div>
				<div class="item-field">
					<?php
					if ($data['pBusinessView'] == 0) {
						$check = 'checked=checked';
						$check2 = '';	
						$check3 = '';
					}elseif($data['pBusinessView'] == 1){
						$check = '';
						$check2 = 'checked=checked';	
						$check3 = '';
					}else{
						$check = '';						
						$check2 = '';
						$check3 = 'checked=checked';
					}
					?>
					<input type="radio" name="pExpenseIncome" value="0" <?=$check?>>個人
					<input type="radio" name="pExpenseIncome" value="1" <?=$check2?>>全部
					<input type="radio" name="pExpenseIncome" value="2" <?=$check3?>>無權限
				</div>
			</div>
			<div class="item">
				<div class="item-title">保號->代書：</div>
				<div class="item-field">
					<?php
					if ($data['pCodeCange'] == 2) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pCodeCange" value="2" <?=$check?>>無權限
					<input type="radio" name="pCodeCange" value="1" <?=$check2?>>有權限
				</div>
			</div>
			<div class="item">
				<div class="item-title">保號->代書轉換：</div>
				<div class="item-field">
					<?php
					if ($data['pCodeCange'] == 0) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pCodeCange2" value="0" <?=$check?>>單一轉換
					<input type="radio" name="pCodeCange2" value="1" <?=$check2?>>多筆轉換
				</div>
			</div>
			<div class="item">
				<div class="item-title">電子合約書：</div>
				<div class="item-field">
					<?php
					if ($data['tEcontract'] == 0) {
						$check = 'checked=checked';
						$check2 = '';
						$check3 = '';
					}elseif($data['tEcontract'] == 1){
						$check = '';						
						$check2 = 'checked=checked';
						$check3 = '';
					}else{
						$check = '';						
						$check2 = '';
						$check3 = 'checked=checked';
					}
					?>
					<input type="radio" name="tEcontract" value="0" <?=$check?>>無權限
					<input type="radio" name="tEcontract" value="1" <?=$check2?>>全部
					<input type="radio" name="tEcontract" value="2" <?=$check3?>>個人
				</div>
			</div>
			<div class="item">
				<div class="item-title">新增案件：</div>
				<div class="item-field">
					<?php
					if ($data['pAddCase'] == 0) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pAddCase" value="0" <?=$check?>>無權限
					<input type="radio" name="pAddCase" value="1" <?=$check2?>>有權限
				</div>
			</div>
			<div class="item">
				<div class="item-title">案件搜尋：</div>
				<div class="item-field">
					<?php
					if ($data['pSearchCase'] == 0) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pSearchCase" value="0" <?=$check?>>無權限
					<input type="radio" name="pSearchCase" value="1" <?=$check2?>>有權限
				</div>
			</div>
		</div>
	</div>
	<!-- 案件管理 -->
	<!-- 基本資料 -->
	<div class="section">
		<div class="title">基本資料</div>
		<div class="content">
			<div class="item">
				<div class="item-title">基本資料：</div>
				<div class="item-field">
					<?php
					if ($data['pBasicManage'] == 1) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pBasicManage" value="1" <?=$check?>>有
					<input type="radio" name="pBasicManage" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">仲介店及地政士搜尋：</div>
				<div class="item-field">
					<?php
					if ($data['pacc_search'] == 1) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pacc_search" value="1" <?=$check?>>有
					<input type="radio" name="pacc_search" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">仲介群組：</div>
				<div class="item-field">
					<?php
					if ($data['pBranchGroup'] == 1) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pBranchGroup" value="0" <?=$check?>>有
					<input type="radio" name="pBranchGroup" value="1" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">品牌維護：</div>
				<div class="item-field">
					<?php
					if ($data['pBrand'] == 0) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pBrand" value="0" <?=$check?>>無權限
					<input type="radio" name="pBrand" value="1" <?=$check2?>>有權限
				</div>      
			</div>
			<div class="item">
				<div class="item-title">仲介店維護：</div>
				<div class="item-field">
					<?php
					if ($data['pBranch'] == 0) {
						$check = 'checked=checked';
						$check2 = '';
						$check3 = '';
					}elseif($data['pBranch'] == 1){
						$check = '';
						$check2 = 'checked=checked';
						$check3 = '';
					}else{
						$check = '';						
						$check2 = '';
						$check3 = 'checked=checked';
					}
					?>
					<input type="radio" name="pBranch" value="0" <?=$check?>>無權限
					<input type="radio" name="pBranch" value="1" <?=$check2?>>只能看未停用店家
					<input type="radio" name="pBranch" value="2" <?=$check3?>>全部都可看
				</div>      
			</div>
			<div class="item">
				<div class="item-title">地政士維護：</div>
				<div class="item-field">
					<?php
					if ($data['pBranch'] == 0) {
						$check = 'checked=checked';
						$check2 = '';
						$check3 = '';
					}elseif($data['pBranch'] == 1){
						$check = '';
						$check2 = 'checked=checked';
						$check3 = '';
					}else{
						$check = '';						
						$check2 = '';
						$check3 = 'checked=checked';
					}
					?>
					<input type="radio" name="pScrivener" value="0" <?=$check?>>無權限
					<input type="radio" name="pScrivener" value="1" <?=$check2?>>只能看未停用店家
					<input type="radio" name="pScrivener" value="2" <?=$check3?>>全部都可看
				</div>      
			</div>
		</div>
	</div>

	<!-- 基本資料 -->
	<!-- 報表作業 -->
	<div class="section">
		<div class="title">報表作業</div>
		<div class="content">
			<div class="item">
				<div class="item-title">報表作業：</div>
				<div class="item-field">
					<?php
					if ($data['pReportManage'] == 0) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pReportManage" value="1" <?=$check?>>有
					<input type="radio" name="pReportManage" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">回饋案件表(FOR業務)：</div>
				<div class="item-field">
					<?php
					if ($data['pFeedBackError'] == 0) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pFeedBackError" value="1" <?=$check?>>有
					<input type="radio" name="pFeedBackError" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">業務個人業績報表：</div>
				<div class="item-field">
					<?php
					if ($data['pSalesBusinessReport'] == 1) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pSalesBusinessReport" value="1" <?=$check?>>有
					<input type="radio" name="pSalesBusinessReport" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">仲介店排名：</div>
				<div class="item-field">
					<?php
					if ($data['pBranchSalse'] == 1) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pBranchSalse" value="1" <?=$check?>>有
					<input type="radio" name="pBranchSalse" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">代書庫存有效合約書：</div>
				<div class="item-field">
					<?php
					if ($data['pScrivenerCase'] == 1) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pScrivenerCase" value="1" <?=$check?>>有
					<input type="radio" name="pScrivenerCase" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">未進案統計功能：</div>
				<div class="item-field">
					<?php
					if ($data['pNoCaseReport'] == 1) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pNoCaseReport" value="1" <?=$check?>>有
					<input type="radio" name="pNoCaseReport" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">業務案件統計：</div>
				<div class="item-field">
					<?php
					if ($data['pSalesCase'] == 1) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pSalesCase" value="1" <?=$check?>>有
					<input type="radio" name="pSalesCase" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">房價指數統計表：</div>
				<div class="item-field">
					<?php
					if ($data['pHouseExponent'] == 1) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pHouseExponent" value="1" <?=$check?>>有
					<input type="radio" name="pHouseExponent" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">仲介地政士CSV檔案下載：</div>
				<div class="item-field">
					<?php
					if ($data['pCsv_report'] == 1) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pCsv_report" value="1" <?=$check?>>有
					<input type="radio" name="pCsv_report" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">活動報表：</div>
				<div class="item-field">
					<?php
					if ($data['pAct_Report'] == 1) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
					?>
					<input type="radio" name="pAct_Report" value="1" <?=$check?>>有
					<input type="radio" name="pAct_Report" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">案件統計表：</div>
				<div class="item-field">
				<?php
					if ($data['pApplyCase'] == 0) {
						$check = 'checked=checked';
						$check2 = '';
					}else{
						$check = '';						
						$check2 = 'checked=checked';
					}
				?>
					<input type="radio" name="pApplyCase" value="0" <?=$check?>>無權限
					<input type="radio" name="pApplyCase" value="1" <?=$check2?>>有權限
				</div>      
			</div>
			<div class="item">
				<div class="item-title">案件數量統計：</div>
				<div class="item-field">
					<?php
						if ($data['pAnalysisCase'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pAnalysisCase" value="0" <?=$check?>>無權限
					<input type="radio" name="pAnalysisCase" value="1" <?=$check2?>>有權限
				</div>      
			</div>
			<div class="item">
				<div class="item-title">保證費統計表：</div>
				<div class="item-field">
					<?php
						if ($data['pCertifiedMoney'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pCertifiedMoney" value="0" <?=$check?>>無權限
					<input type="radio" name="pCertifiedMoney" value="1" <?=$check2?>>有權限
				</div>      
			</div>
			<div class="item">
				<div class="item-title">回饋案件查詢：</div>
				<div class="item-field">
					<?php
						if ($data['pFeedBack'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pFeedBack" value="0" <?=$check?>>無權限
					<input type="radio" name="pFeedBack" value="1" <?=$check2?>>有權限
				</div>      
			</div>
			<div class="item">
				<div class="item-title">直營服務費統計表：</div>
				<div class="item-field">
					<?php
						if ($data['pRealtyCharge'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pRealtyCharge" value="0" <?=$check?>>無權限
					<input type="radio" name="pRealtyCharge" value="1" <?=$check2?>>有權限
				</div>      
			</div>
			<div class="item">
				<div class="item-title">業務報表：</div>
				<div class="item-field">
					<?php
						if ($data['pBusinessEdit'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pBusinessEdit" value="0" <?=$check?>>無權限
					<input type="radio" name="pBusinessEdit" value="1" <?=$check2?>>有權限
				</div>      
			</div>
			<div class="item">
				<div class="item-title">報表作業>業績統計表權限：</div>
				<div class="item-field">
					<?php
						if ($data['pBusiness_report'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pBusiness_report" value="0" <?=$check?>>無權限
					<input type="radio" name="pBusiness_report" value="1" <?=$check2?>>有權限
				</div>      
			</div>
			<div class="item">
				<div class="item-title">已收款未結案報表：</div>
				<div class="item-field">
					<?php
						if ($data['pTransNoEnd'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pTransNoEnd" value="0" <?=$check?>>無權限
					<input type="radio" name="pTransNoEnd" value="1" <?=$check2?>>有權限
				</div>      
			</div>
			<div class="item">
				<div class="item-title">簽約列表權限：</div>
				<div class="item-field">
					<?php
						if ($data['pSignList'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pSignList" value="0" <?=$check?>>無權限
					<input type="radio" name="pSignList" value="1" <?=$check2?>>有權限	
				</div>      
			</div>
			<div class="item">
				<div class="item-title">仲介期間比較：</div>
				<div class="item-field">
					<?php
						if ($data['pRealtyCaseList'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
							$check3 = '';
						}elseif($data['pRealtyCaseList'] == 1){
							$check = '';						
							$check2 = 'checked=checked';
							$check3 = '';
						}else{
							$check = '';						
							$check2 = '';
							$check3 = 'checked=checked';
						}
					?>
					<input type="radio" name="pRealtyCaseList" value="0" <?=$check?>>無權限
					<input type="radio" name="pRealtyCaseList" value="1" <?=$check2?>>比較
					<input type="radio" name="pRealtyCaseList" value="2" <?=$check3?>>+總部
				</div>      
			</div>
		</div>
	</div>
	
	<!-- 報表作業 -->

	<!-- 業務管理 -->
	<div class="section">
		<div class="title">業務管理</div>
		<div class="content">
			<div class="item">
				<div class="item-title">業務管理：</div>
				<div class="item-field">
					<?php
						if ($data['pSalesManage'] == 1) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pSalesManage" value="1" <?=$check?>>有
					<input type="radio" name="pSalesManage" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">實價登錄：</div>
				<div class="item-field">
					<?php
						if ($data['pRealPrice'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pRealPrice" value="1" <?=$check?>>有
					<input type="radio" name="pRealPrice" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">行程記錄：</div>
				<div class="item-field">
					<?php
						if ($data['pSalesCal'] == 1) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pSalesCal" value="1" <?=$check?>>有
					<input type="radio" name="pSalesCal" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">行程管理統計：</div>
				<div class="item-field">
					<?php
						if ($data['pSalesScheduleAcc'] == 1) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pSalesScheduleAcc" value="1" <?=$check?>>有
					<input type="radio" name="pSalesScheduleAcc" value="0" <?=$check2?>>無
				</div>      
			</div>
		</div>
	</div>
	
	<!-- 業務管理 -->
	<!-- 系統管理 -->
	<div class="section">
		<div class="title">系統管理</div>
		<div class="content">
			<div class="item">
				<div class="item-title">系統管理：</div>
				<div class="item-field">
					<?php
						if ($data['pSystemManage'] == 1) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pSystemManage" value="1" <?=$check?>>有
					<input type="radio" name="pSystemManage" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">資訊視窗：</div>
				<div class="item-field">
					<?php
						if ($data['pInfo'] == 1) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pInfo" value="1" <?=$check?>>有
					<input type="radio" name="pInfo" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">手動簡訊功能：</div>
				<div class="item-field">
					<?php
						if ($data['pSmsManually'] == 1) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pSmsManually" value="1" <?=$check?>>有
					<input type="radio" name="pSmsManually" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">銀行資訊：</div>
				<div class="item-field">
					<?php
						if ($data['pBankInfo'] == 1) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pBankInfo" value="1" <?=$check?>>有
					<input type="radio" name="pBankInfo" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">異常簡訊：</div>
				<div class="item-field">
					<?php
						if ($data['pSms_Error'] == 1) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pSms_Error" value="1" <?=$check?>>有
					<input type="radio" name="pSms_Error" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">檔案上傳：</div>
				<div class="item-field">
					<?php
						if ($data['pUpload'] == 1) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pUpload" value="1" <?=$check?>>有
					  <input type="radio" name="pUpload" value="0" <?=$check2?>>無
				</div>      
			</div>
			<div class="item">
				<div class="item-title">人員管理權限：</div>
				<div class="item-field">
					<?php
						if ($data['pStaffManage'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pStaffManage" value="0" <?=$check?>>無權限
					<input type="radio" name="pStaffManage" value="1" <?=$check2?>>有權限
				</div>      
			</div>
			<div class="item">
				<div class="item-title">後台人事功能顯示：</div>
				<div class="item-field">
					<?php
						if ($data['pShow'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
						}else{
							$check = '';						
							$check2 = 'checked=checked';
						}
					?>
					<input type="radio" name="pShow" value="0" <?=$check?>>無權限
					<input type="radio" name="pShow" value="1" <?=$check2?>>有權限
				</div>      
			</div>
		</div>
	</div>
	
	<!-- 系統管理 -->

	
	<!-- 出款 -->
	<div class="section">
		<div class="title">出款</div>
		<div class="content">
			<div class="item">
				<div class="item-title">審核列表顯示：</div>
				<div class="item-field">
					<?php
						if ($data['pBankCheck'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
							$check3 = '';
						}else if ($data['pBankCheck'] == 1) {
							$check = '';
							$check2 = 'checked=checked';
							$check3 = '';
						}else{
							$check = '';						
							$check2 = '';
							$check3 = 'checked=checked';
						}
					?>
					<input type="radio" name="pBankCheck" value="0" <?=$check?>>無審核可修改
					<input type="radio" name="pBankCheck" value="1" <?=$check2?>> 可審核可修改
					<input type="radio" name="pBankCheck" value="2" <?=$check3?>>無權限
				</div>      
			</div>
			<div class="item">
				<div class="item-title">指示書：</div>
				<div class="item-field" style="font-size:14px;">
					<?php
						if ($data['pBankCheck'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
							$check3 = '';
							$check4 = '';
						}else if ($data['pBankCheck'] == 1) {
							$check = '';
							$check2 = 'checked=checked';
							$check3 = '';
							$check4 = '';
						}else if ($data['pBankCheck'] == 2) {
							$check = '';
							$check2 = '';
							$check3 = 'checked=checked';
							$check4 = '';
						}else{
							$check = '';						
							$check2 = '';
							$check3 = '';
							$check4 = 'checked=checked';
						}
					?>
					<input type="radio" name="pBankBook" value="1" <?=$check?>>可看全部，只能把狀態改成待審核
					<input type="radio" name="pBankBook" value="0" <?=$check2?>>只有編輯功能(經辦)
					<input type="radio" name="pBankBook" value="2" <?=$check3?>>只看待審核案件，能更改狀態
					<input type="radio" name="pBankBook" value="3" <?=$check4?>>全部
				</div>      
			</div>
			<div class="item">
				<div class="item-title" style="font-size:14px;">出款數量統計權限資訊視窗案件資訊：</div>
				<div class="item-field">
					<?php
						if ($data['pBankTrans'] == 0) {
							$check = 'checked=checked';
							$check2 = '';
							$check3 = '';
						}else if ($data['pBankTrans'] == 1) {
							$check = '';
							$check2 = 'checked=checked';
							$check3 = '';
						}else{
							$check = '';						
							$check2 = '';
							$check3 = 'checked=checked';
						}
					?>
					<input type="radio" name="pBankTrans" value="0" <?=$check?>>非相關
					<input type="radio" name="pBankTrans" value="1" <?=$check2?>>最大權限
					<input type="radio" name="pBankTrans" value="2" <?=$check3?>>限經辦	
				</div>      
			</div>
		</div>
	</div>
	<div class="bt"><input type="submit" value="送出" class="xxx-button"></div>
	
	</form>
</body>
</html>

<?php

##sales
// $arr['pApplyCase'] = 1;
// $arr['pFeedBackModify'] = 2;
// $arr['pBusinessView'] = 1;
// $arr['pShow'] = 1;
// $arr['pAccList'] = 0;
// $arr['pAddCase'] = 1;
// $arr['pTax'] = 0;
// $arr['pSearchCase'] = 1;
// $arr['pCodeCange'] = 1;
// $arr['pBranchGroup'] = 1;
// $arr['pBrand'] = 1;
// $arr['pBranch'] = 1;
// $arr['pScrivener'] = 1;
// $arr['pInvoice'] = 1;
// $arr['pBank_checklist'] = 1;
// $arr['pacc_search'] = 1;
// $arr['pCsv_report'] = 1;
// $arr['pAct_Report'] = 0;
// $arr['pSms_Error'] = 1;
// $arr['pInfo'] = 1;
// $arr['pSalesCal'] = 1;
// $arr['pRealPrice'] = 1;
// $arr['pCaseManage'] = 1;
// $arr['pBasicManage'] = 1;
// $arr['pReportManage'] = 1;
// $arr['pSystemManage'] = 1;
// $arr['pSalesManage'] = 1;
// $arr['pCreatCertifiedId'] =1;
// $arr['pModifyCase'] = 1;
// $arr['pSalesBusinessReport'] = 1;
// $arr['pSmsManually'] = 1;
// $arr['pFeedBackError'] = 1;
// $arr['pCodeCange2'] = 0;
// $arr['pAnalysisCase'] = 0;
// $arr['pBranchSalse'] = 0;
// $arr['pScrivenerCase'] = 0;
// $arr['pNoCaseReport'] = 0;
// $arr['pSalesCase'] = 0;
// $arr['pHouseExponent'] = 0;
// $arr['pCertifiedMoney'] = 0;
// $arr['pExpenseIncome'] = 0;
// $arr['pRealtyCharge'] = 0;
// $arr['pFeedBack'] = 0;
// $arr['pBusinessEdit'] = 0;
// $arr['pBusiness_report'] = 0;
// $arr['pSignList'] = 0;
// $arr['pRealtyCaseList'] = 0;
// $arr['pSalesScheduleAcc'] = 0;
// $arr['pUpload'] = 0;
// $arr['pStaffManage'] = 0;
// $arr['pBankCheck'] = 0;
// $arr['pBankBook'] = 0;

//sUndertaker
// $arr['pApplyCase'] = 0;
// $arr['pFeedBackModify'] = 0;
// $arr['pBusinessView'] = 0;
// $arr['pShow'] = 1;
// $arr['pAccList'] = 0;
// $arr['pAddCase'] = 1;
// $arr['pTax'] = 0;
// $arr['pSearchCase'] = 1;
// $arr['pCodeCange'] = 1;
// $arr['pBranchGroup'] = 1;
// $arr['pBrand'] = 1;
// $arr['pBranch'] = 2;
// $arr['pScrivener'] = 2;
// $arr['pInvoice'] = 1;
// $arr['pBank_checklist'] = 1;
// $arr['pacc_search'] = 1;
// $arr['pCsv_report'] = 1;
// $arr['pAct_Report'] = 1;
// $arr['pSms_Error'] = 1;
// $arr['pInfo'] = 1;
// $arr['pSalesCal'] = 0;
// $arr['pRealPrice'] = 0;
// $arr['pCaseManage'] = 1;
// $arr['pBasicManage'] = 1;
// $arr['pReportManage'] = 1;
// $arr['pSystemManage'] = 1;
// $arr['pSalesManage'] = 0;
// $arr['pCreatCertifiedId'] = 1;
// $arr['pModifyCase'] = 1;
// $arr['pSalesBusinessReport'] = 0;
// $arr['pSmsManually'] = 1;
// $arr['pBankInfo'] = 1;
// $arr['pBankTrans'] = 2;
// $arr['pTransNoEnd'] = 1;
// $arr['tEcontract'] = 2;
// $arr['pFeedBackError'] = 0;
// $arr['pCodeCange2'] = 0;
// $arr['pAnalysisCase'] = 0;
// $arr['pBranchSalse'] = 0;
// $arr['pScrivenerCase'] = 0;
// $arr['pNoCaseReport'] = 0;
// $arr['pSalesCase'] = 0;
// $arr['pHouseExponent'] = 0;
// $arr['pCertifiedMoney'] = 0;
// $arr['pExpenseIncome'] = 0;
// $arr['pRealtyCharge'] = 0;
// $arr['pFeedBack'] = 0;
// $arr['pBusinessEdit'] = 0;
// $arr['pBusiness_report'] = 0;
// $arr['pSignList'] = 0;
// $arr['pRealtyCaseList'] = 0;
// $arr['pSalesScheduleAcc'] = 0;
// $arr['pUpload'] = 0;
// $arr['pStaffManage'] = 0;
// $arr['pBankCheck'] = 0;
// $arr['pBankBook'] = 0;

// echo json_encode($arr);

##

?>