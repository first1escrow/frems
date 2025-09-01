<?php
include_once '../session_check.php' ;

$save_ok = $_REQUEST['save_ok'] ;
if ($save_ok) {

	$FBYear = $_POST['FBYear'] ;
	$FBSeason = $_POST['FBSeason'] ;

	# 設定檔案存放目錄位置
	$uploaddir = './excel/' ;
	##

	# 設定檔案名稱
	$fn = $_FILES['upload_file']['name'] ;
	$tmp = explode('.',$fn) ;
	$uploadfile = $FBYear.'-S'.$FBSeason.'.'.$tmp[1] ;
	$uploadfile = $uploaddir.basename($uploadfile) ;
	unset($tmp) ; unset($fn) ;
	##

	if (move_uploaded_file($_FILES['upload_file']['tmp_name'],$uploadfile)) {

		require_once("../bank/Classes/PHPExcel.php");
		require_once("../bank/Classes/PHPExcel/IOFactory.php");
		require_once("../bank/Classes/PHPExcel/Reader/Excel5.php");
		require_once("../bank/Classes/PHPExcel/Reader/Excel2007.php");
		include_once('../openadodb.php') ;

		//讀取郵遞區號暫存資料
		$sql = 'SELECT * FROM tZipArea ORDER BY zZip ASC;' ;
		$rs_zip = $conn->Execute($sql) ;
		while(!$rs_zip->EOF) {
			$addrZip[$rs_zip->fields['zZip']]['city'] = $rs_zip->fields['zCity'] ;
			$addrZip[$rs_zip->fields['zZip']]['area'] = $rs_zip->fields['zArea'] ;
			$rs_zip->MoveNext() ;
		}
		
		//讀取 excel 檔案
		$objReader = new PHPExcel_Reader_Excel2007(); 
		$objReader->setReadDataOnly(true); 
		
		//檔案名稱
		$objPHPExcel = $objReader->load($uploadfile); 
		$currentSheet = $objPHPExcel->getSheet(0);//讀取第一個工作表(編號從 0 開始) 
		$allLine = $currentSheet->getHighestRow() ;//取得總列數

		for($excel_line = 2 ; $excel_line<=$allLine ; $excel_line ++) {
			//取得店編號
			$branch = $currentSheet->getCell("A{$excel_line}")->getValue() ;
			$branch = preg_replace("/[a-zA-Z]{2}/","",$branch) ;
			$branch += 1 - 1 ;
			
			//取得回饋方式
			$_feedback = $currentSheet->getCell("B{$excel_line}")->getValue() ;
			if ($_feedback=='整批') {
				$_feedback = '2' ;
			}
			else if ($_feedback=='結案') {
				$_feedback = '3' ;
			}
			else {
				$_feedback = '1' ;
			}
			
			//取得姓名/抬頭
			$_title = $currentSheet->getCell("D{$excel_line}")->getValue() ;
			
			//取得店長行動電話
			$_mobile = $currentSheet->getCell("E{$excel_line}")->getValue() ;

			//取得身份別
			$_iden = $currentSheet->getCell("F{$excel_line}")->getValue() ;
			if ($_iden=='身份證編號') {
				$_iden = '2' ;
			}
			else if ($_iden=='統一編號') {
				$_iden = '3' ;
			}
			else if ($_iden=='護照號碼') {
				$_iden = '4' ;
			}
			else {
				$_iden = '1' ;
			}
			
			//取得證件號碼
			$_idno = $currentSheet->getCell("G{$excel_line}")->getValue() ;
			
			//取得聯絡地址
			$_contact_zip = '' ;
			$_contact_addr = $currentSheet->getCell("H{$excel_line}")->getValue() ;
			
			if (preg_match("/^[0-9]{3}/",$_contact_addr)) {
				$_contact_zip = substr($_contact_addr,0,3) ;
				$_contact_addr = substr($_contact_addr,3) ;
				
				$tmp = $addrZip[$_contact_zip]['city'] ;
				$_contact_addr = preg_replace("/$tmp/","",$_contact_addr) ;
				$tmp = $addrZip[$_contact_zip]['area'] ;
				$_contact_addr = preg_replace("/$tmp/","",$_contact_addr) ;
				unset($tmp) ;
			}
			
			//取得銀行代號
			$_bank = $currentSheet->getCell("I{$excel_line}")->getValue() ;
			$_bank_main = substr($_bank,0,3) ;
			$_bank_branch = substr($_bank,3) ;
			unset($_bank) ;
			
			//取得帳戶號碼
			$_account_no = $currentSheet->getCell("L{$excel_line}")->getValue() ;
			
			//取得帳戶名稱
			$_account_name = $currentSheet->getCell("M{$excel_line}")->getValue() ;
			
			//取得當季匯款金額
			$FBS = $currentSheet->getCell("N{$excel_line}")->getValue() ;
			
			//取得戶籍地址
			$_regist_zip = '' ;
			$_regist_addr = $currentSheet->getCell("O{$excel_line}")->getValue() ;
			
			if (preg_match("/^[0-9]{3}/",$_regist_addr)) {
				$_regist_zip = substr($_regist_addr,0,3) ;
				$_regist_addr = substr($_regist_addr,3) ;
				
				$tmp = $addrZip[$_regist_zip]['city'] ;
				$_regist_addr = preg_replace("/$tmp/","",$_regist_addr) ;
				$tmp = $addrZip[$_contact_zip]['area'] ;
				$_regist_addr = preg_replace("/$tmp/","",$_regist_addr) ;
				unset($tmp) ;
			}
			
			$sql = '
				UPDATE 
					tBranch
				SET
					bFeedBack="'.$_feedback.'",
					bTtitle="'.$_title.'",
					bIdentity="'.$_iden.'",
					bIdentityNumber="'.$_idno.'",
					bZip2="'.$_regist_zip.'",
					bZip3="'.$_contact_zip.'",
					bAddr2="'.$_regist_addr.'",
					bAddr3="'.$_contact_addr.'",
					bMobileNum2="'.$_mobile.'",
					bAccountNum5="'.$_bank_main.'",
					bAccountNum6="'.$_bank_branch.'",
					bAccount7="'.$_account_no.'",
					bAccount8="'.$_account_name.'"
				WHERE
					bId="'.$branch.'"
			' ;
			//echo "Q=".$sql."<br>\n" ;
			$conn->Execute($sql) ;


			//確認仲介店資料是否已存在
			$sql = 'SELECT cBranchNum FROM tTaxFeedBack WHERE cBranchNum="'.$branch.'" AND FBYear="'.$FBYear.'"' ;
			$rs = $conn->Execute($sql) ;
			
			//新增/更新年度當季金額資料
			if ($rs->fields['cBranchNum']) {
				$sql = 'UPDATE tTaxFeedBack SET FBS'.$FBSeason.'="'.$FBS.'" WHERE cBranchNum="'.$branch.'" AND FBYear="'.$FBYear.'" ;' ;
				//echo "Q=".$sql."<br>\n" ;
				$conn->Execute($sql);
			}
			else {
				$sql = 'INSERT INTO tTaxFeedBack (cBranchNum,FBYear,FBS'.$FBSeason.') VALUES ("'.$branch.'", "'.$FBYear.'", "'.$FBS.'") ;' ;
				//echo "Q=".$sql."<br>\n"	;
				$conn->Execute($sql);
			}
			
		}
		

		//清空記憶體
		unset($objReader);
		unset($objPHPExcel);

		echo '
		<script>
		alert("檔案上傳、寫入成功!!") ;
		window.location = "upload_feedback.php" ;
		</script>
		' ;
	}
	else {
		echo '
		<script>
		alert("檔案上傳、寫入失敗!!") ;
		window.location = "upload_feedback.php" ;
		</script>
		' ;
	}
	
}

?> 

