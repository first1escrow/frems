<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
include_once '../session_check.php' ;
include_once '../opendb.php' ;
include_once '../openadodb.php' ;


##
$sql = "SELECT bName,bId FROM tBrand WHERE bContract = 1";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$BrandContract[$rs->fields['bId']] = $rs->fields['bName'];
	$BrandContractId[] = $rs->fields['bId'];
	$rs->MoveNext();
}
##

if ($_POST['export_ok']=='1') {
	//預載log物件
	$logs = new Intolog() ;
	##
	
	$logs->writelog('csvDownload') ;

	###
	
	$str = '' ;$queryStr = '';
	// 設定顯示名稱與資料表欄位(身份別)
	$ide = $_POST['identity'] ;

	if ($_SESSION['member_test'] != 0) {
		
		$sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '".$_SESSION['member_test']."'";
		
		
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$test_tmp[] = "'".$rs->fields['zZip']."'";

			$rs->MoveNext();
		}
		if ($ide=='scrivener') {
			$queryStr = ' AND sCpZip1 IN('.@implode(',', $test_tmp).') AND sCategory != 2'; 
		}else{
			$queryStr = ' AND bZip IN('.@implode(',', $test_tmp).') AND bCategory != 2'; 
		}
			// $query .= "bZip IN(".implode(',', $test_tmp).")";
		
		unset($test_tmp);
	}elseif ($_SESSION['member_pDep'] == 7) {
		// $sql = "SELECT FROM WHERE pDep IN()";
		if ($ide == 'scrivener') {
			$sql = "SELECT sScrivener AS store FROM tScrivenerSales WHERE sSales = '".$_SESSION['member_id']."'";
			$col = 'sId';
		}else{
			$sql = "SELECT bBranch AS store FROM tBranchSales WHERE bSales = '".$_SESSION['member_id']."'";	
			$col = 'bId';
		}

		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
				$test_tmp[] = "'".$rs->fields['store']."'";;

			$rs->MoveNext();
		}

		$queryStr = " AND ".$col." IN (".@implode(',', $test_tmp).")";

	}

	
	if ($ide=='scrivener') { 
		$ide = 'tScrivener' ; 
		$identity = '地政士' ;
		$where = 'sStatus = 1 AND sId NOT IN (632, 575,552,620,411,224) '.$queryStr;
	}
	else {
		$ide = 'tBranch' ; 
		$identity = '仲介' ;
		$where = 'bStatus = 1 AND bId NOT IN (0, 505, 980, 1012) '.$queryStr;
	}
	unset($queryStr);
	$str .= '身份別' ;
	
	// 設定顯示名稱與資料表欄位(編號)
	if ($ide=='tScrivener') { $_fields = ' sId as sn ' ; }
	else { $_fields = ' bId as sn ' ; }
	$str .= ',編號' ;
	
	// 設定顯示名稱與資料表欄位(名稱)
	if ($ide=='tScrivener') {
		$_fields .= ' ,sName as cName,sBirthday ' ;
		$str .= ',姓名,生日' ;
	}
	else {
		$_fields .= ' ,bBrand as brand ,bStore as cName ,bName as cCompany ,bManager ' ;
		$str .= ',仲介品牌,仲介公司,仲介店名,店長/店東' ;
		
	}

	
	
	// 若選擇行動電話選項，則顯示並選取手機號碼資料表欄位
	$field_mobile = $_POST['field_mobile'] ;
	if ($field_mobile) {
		if ($ide=='tScrivener') { $field_mobile = ' sMobileNum as mobile ' ; }
		else if ($ide=='tBranch') { $field_mobile = ' bMobileNum as mobile ' ; }
		
		if ($_fields) { $_fields .= ',' ; }
		$_fields .= $field_mobile ;
		$str .= ',行動電話' ;
	}
	
	// 若選擇電話選項，則顯示並選取電話區碼與號碼資料表欄位
	$field_tel = $_POST['field_tel'] ;
	if ($field_tel) {
		if ($ide=='tScrivener') { $field_tel = ' sTelArea as telArea, sTelMain as telMain ' ; }
		if ($ide=='tBranch') { $field_tel = ' bTelArea as telArea, bTelMain as telMain ' ; }
		
		if ($_fields) { $_fields .= ',' ; }
		$_fields .= $field_tel ;
		$str .= ',電話號碼' ;
	}
	
	// 若選擇傳真選項，則顯示並選取傳真區碼與號碼資料表欄位
	$field_fax = $_POST['field_fax'] ;
	if ($field_fax) {
		if ($ide=='tScrivener') { $field_fax = ' sFaxArea as faxArea, sFaxMain as faxMain ' ; }
		else if ($ide=='tBranch') { $field_fax = ' bFaxArea as faxArea, bFaxMain as faxMain ' ; }
		
		if ($_fields) { $_fields .= ',' ; }
		$_fields .= $field_fax ;
		$str .= ',傳真號碼' ;
	}
	
	// 若選擇 E-Mail 選項，則顯示並選取 E-Mail 資料表欄位
	$field_email = $_POST['field_email'] ;
	if ($field_email) {
		if ($ide=='tScrivener') { $field_email = ' sEmail as email ' ; }
		else if ($ide=='tBranch') { $field_email = ' bEmail as email ' ; }
		
		if ($_fields) { $_fields .= ',' ; }
		$_fields .= $field_email ;
		$str .= ',電子郵件' ;
	}

	// 若選擇地址選項，則顯示並選取地址與郵遞區號(前三碼)資料表欄位
	$field_address = $_POST['field_address'] ;
	if ($field_address) {
		if ($ide=='tScrivener') { 
			$field_address = ' (SELECT zCity FROM tZipArea WHERE zZip=a.sCpZip1) as zCity, (SELECT zArea FROM tZipArea WHERE zZip=a.sCpZip1) as zArea, sCpAddress as cAddress, sCpZip1 as cZip' ; 
		}
		else if ($ide=='tBranch') { 
			$field_address = ' (SELECT zCity FROM tZipArea WHERE zZip=a.bZip) as zCity, (SELECT zArea FROM tZipArea WHERE zZip=a.bZip) as zArea, bAddress as cAddress, bZip as cZip' ; 
		}
		
		if ($_fields) { $_fields .= ',' ; }
		$_fields .= $field_address ;
		$str .= ',郵遞區號,地址' ;
	}
	
	// 若選擇事務所，則顯示事務所欄位
	$field_office = $_POST['field_office'] ;
	if ($field_office) {
		if ($ide=='tScrivener') 
		{ 
			$field_office = ' sOffice as office ' ;
			if ($_fields) { $_fields .= ',' ; }
			$_fields .= $field_office ;
			$str .= ',事務所名稱' ;
		}
	}
	
	// 若選擇負責業務選項，則顯示負責業務欄位

	$field_sales = $_POST['field_sales'] ;
	if ($field_sales) {
		//if ($ide=='tScrivener') { $field_sales = ' sEmail as email ' ; }
		//else if ($ide=='tBranch') { $field_sales = ' (SELECT pName FROM tPeopleInfo WHERE a.=pId) as email ' ; }
		
		//if ($_fields) { $_fields .= ',' ; }
		//$_fields .= $field_sales ;
		$str .= ',負責業務' ;
	}

	if ($ide=='tScrivener') {
		if ($_fields) { $_fields .= ',' ; }
		$_fields .= 'sCreat_time,sRecall,sSpRecall';
		$str .= ',建立日期,回饋比率,特殊回饋比率,品牌回饋代書' ; //比率
	}
	else {
		if ($_fields) { $_fields .= ',' ; }
		$_fields .= 'bCreat_time,bTtitle,bAccount8,bRecall';
		$str .= ',建立日期,抬頭,戶名,回饋比率' ;
		// 
	}

	//查詢條件
	
		if ($_POST['area'] != '') {
			
			if ($ide== "tScrivener") {
				$where .= " AND sCpZip1 ='".$_POST['area']."'";
			}else{
				$where .= " AND bZip ='".$_POST['area']."'";
			}
		}else if ($_POST['country'] != ''){
				$sql = "SELECT zZip FROM  tZipArea WHERE zCity ='".$_POST['country']."' ";
				$rel = mysql_query($sql,$link) ;
				while ($tmp = mysql_fetch_array($rel)) {
					$zip[] = '"'.$tmp['zZip'].'"';
				}

			if ($ide== "tScrivener") {
				$where .= " AND sCpZip1 IN(".implode(',',$zip).")"; //bZip sZip1
			}else{
				$where .= " AND bZip IN(".implode(',',$zip).")"; //bZip sZip1
				
			}
		}
	

		if ($_POST['book']) {
			$cat = ($ide=='tScrivener') ? "1" : "2";
			// if ($ide=='tScrivener') { //sType 類型1地政2仲介
				
			// }else{
			// 	$cat
			// }
			for ($i=0; $i < count($_POST['book']); $i++) { 
				if ($_POST['book'][$i] == 1) { //1 => '特約',2 => '合契',3 =>'先行撥付同意書'
					if ($cat == 2) {
						$sql = "SELECT sStore FROM tSalesSign WHERE sType = '".$cat."'"; //有特約
						$rs = $conn->Execute($sql);
						while (!$rs->EOF) {
							$arrayStore[] = $rs->fields['sStore'];

							$rs->MoveNext();
						}
					}
					
				}

				if ($_POST['book'][$i] == 2) {
					if ($cat == 2) {
						// $sql = "SELECT fStoreId FROM tFeedBackData WHERE fType = '".$cat."' AND fStatus = 0"; //合契
						// $rs = $conn->Execute($sql);
						// while (!$rs->EOF) {
						// 	$arrayStore2[] = $rs->fields['fStoreId'];

						// 	$rs->MoveNext();
						// }
						$where .=' AND bCooperationHas = 1';
					}elseif ($cat == 1) {
						$sql = "SELECT fStoreId FROM tFeedBackData WHERE fType = '".$cat."' AND fStatus = 0"; //合契
						$rs = $conn->Execute($sql);
						while (!$rs->EOF) {
							$arrayStore2[] = $rs->fields['fStoreId'];

							$rs->MoveNext();
						}
					}
					
				}

				if ($_POST['book'][$i] == 3) { //1有
					if ($cat == 2) {
						$where .=' AND bServiceOrderHas = 1';
						
					}
					
				}
			}
			// $where .= "";
			unset($cat);
		}


		
		if ($ide!='tScrivener') {
			$str .= ',特約';
			$str .= ',合契';
			$str .= ',先行撥付同意書';
		}else{
			$str .= ',合契';
		}
		
		//經辦
		$field_undertaker = $_POST['field_undertaker'];
		if ($field_undertaker) {
			if ($ide=='tScrivener') {
			

				if ($_fields) { $_fields .= ',' ; }
				$_fields .= '(SELECT pName FROM tPeopleInfo WHERE pId = sUndertaker1) AS sUndertaker' ;
				$str .= ',經辦';
			}
		}



		//地政士 合作品牌
		if ($_POST['field_sBrand']) {
			if ($ide=='tScrivener') {
				$str .= ',合作品牌';
			}
		}

		//條件選擇某地政士合作品牌
		if ($_POST['BrandContract'] || $_POST['field_sBrand']) {
			if ($_fields) { $_fields .= ',' ; }
			$_fields .= 'sBrand';
		}

		if ($_POST['field_signSales']) {
			$str .= ',簽約業務';
		}
		
	
	$sql = '
	SELECT
		'.$_fields.'
	FROM
		'.$ide.' AS a
	WHERE 
		'.$where.'
	ORDER BY
		sn
	ASC ;
	' ;
	


	$rel = mysql_query($sql,$link) ;
	$str .= "\n" ;

	while ($tmp = mysql_fetch_array($rel)) {
		
		if (is_array($arrayStore)) { //
			
			if (!in_array($tmp['sn'], $arrayStore)) {
				continue;
			}
		}

		if (is_array($arrayStore2)) { //
			$col2 ='';
			if (!in_array($tmp['sn'], $arrayStore2)) {
				continue;
			}
		}
		//條件選擇某地政士合作品牌
		if ($_POST['BrandContract']) {
			if ($ide=='tScrivener') {
				$tmp2 = explode(',', $tmp['sBrand']);
				$check = false;
				
				foreach ($tmp2 as $k => $v) {
					
					if (in_array($v, $_POST['BrandContract'])) {
						// continue;
						$check = true;
					}
					
				}
				
				if (!$check) {
					
					continue;
				}
				
				
				unset($tmp2);
			}
		}
		

		// 顯示身份別與名稱
		$str .= $identity ;


		
		// 顯示編號與名稱
		if ($ide=='tScrivener') {
			$str .= ',SC'.str_pad($tmp['sn'],4,'0',STR_PAD_LEFT).','.$tmp['cName'].','.str_replace('-', '/', $tmp['sBirthday']) ;
		}
		else {
			$sql = "
				SELECT
					b.bCode,
					b.bName,
					bServiceOrderHas
				FROM
					tBranch AS a
				JOIN
					tBrand AS b ON a.bBrand = b.bId
					WHERE a.bId='".$tmp['sn']."' 
			;" ;
			//echo $sql."\n" ;
			$rs = mysql_query($sql,$link) ;
			$temp = mysql_fetch_array($rs) ;
			$prefix = $temp['bCode'] ;
			$brand = $temp['bName'];
			$bServiceOrderHas = ($temp['bServiceOrderHas'] == 1)? '有':'無';
			unset($temp) ;
			
			//$str .= ',TH'.str_pad($tmp['sn'],5,'0',STR_PAD_LEFT) ;
			$str .= ','.$prefix.str_pad($tmp['sn'],5,'0',STR_PAD_LEFT) ;
			$str .= ','.$brand.','.$tmp['cCompany'].','.$tmp['cName'].','.$tmp['bManager'] ;
		}

		// 顯示行動電話
		if ($field_mobile) {
			$str .= ','.$tmp['mobile'].'-' ;
		}
			
		// 顯示電話
		if ($field_tel) {
			$str .= ','.$tmp['telArea'].'-'.$tmp['telMain'] ;
		}
		else if ($tmp['telArea']) {
			$str .= ','.$tmp['telArea'].'-' ;
		}
		else if ($tmp['telMain']) {
			$str .= ','.$tmp['telMain'] ;
		}
		
		// 顯示傳真
		if ($field_fax) {
			$str .= ','.$tmp['faxArea'].'-'.$tmp['faxMain'] ;
		}
		else if ($tmp['faxArea']) {
			$str .= ','.$tmp['faxArea'].'-' ;
		}
		else if ($tmp['faxMain']) {
			$str .= ','.$tmp['faxMain'] ;
		}
		
		// 顯示 E-Mail
		if ($field_email) {
			$str .= ','.$tmp['email'] ;
		}

		// 顯示地址
		if ($field_address) {
			$zCity = $tmp['zCity'] ;
			$zArea = $tmp['zArea'] ;
			$tmp['cAddress'] = preg_replace("/$zCity/","",$tmp['cAddress']) ;
			$tmp['cAddress'] = preg_replace("/$zArea/","",$tmp['cAddress']) ;
			$str .= ',"'.preg_replace("/[a-z]/","",$tmp['cZip']).'","'.$tmp['zCity'].$tmp['zArea'].$tmp['cAddress'].'"' ;
		}
		
		// 顯示辦公室地址
		if ($field_office) {
			$str .= ','.$tmp['office'] ;
		}
		
		// 顯示負責業務
		if ($field_sales) {
			// if ($ide == 'tScrivener') $sql = 'SELECT (SELECT pName FROM tPeopleInfo WHERE pId=c.sSales) as sales FROM tScrivenerSales AS c WHERE sScrivener="'.$tmp['sn'].'" ORDER BY sId DESC ;' ;
			// else $sql = 'SELECT (SELECT pName FROM tPeopleInfo WHERE pId=c.bSales) as sales FROM tBranchSales AS c WHERE bBranch="'.$tmp['sn'].'" ORDER BY bId DESC ;' ;
			
			if ($ide == 'tScrivener') {
				$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId=sSales) AS sales FROM tScrivenerSales as sales WHERE sScrivener = '".$tmp['sn']."'";
			}else{
				$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId=bSales) AS sales FROM tBranchSales WHERE bBranch ='".$tmp['sn']."'";
			}
			$rs = mysql_query($sql,$link) ;
			
			$str .= ',';
			while ($_tmp = mysql_fetch_array($rs)) {
				$tmpSales[] = $_tmp['sales'];
			}
			$str .= implode('、', $tmpSales);
			unset($_tmp) ;unset($tmpSales) ;
		}

		//建立日期
		// 顯示編號與名稱
		if ($ide=='tScrivener') {
			$tmp['sCreat_time'] = DateChange($tmp['sCreat_time']); ///,sSpRecall
			$tmp['sSpRecall'] = ($tmp['sSpRecall'] =='')? '0':$tmp['sSpRecall'];
			$str .= ',"'.$tmp['sCreat_time'].'","'.$tmp['sRecall'].'%","'.$tmp['sSpRecall'].'%"' ;

			$sql = "SELECT *,(SELECT bName FROM tBrand WHERE bId = sBrand) AS BrandName FROM tScrivenerFeedSp WHERE sScrivener ='".$tmp['sn']."' AND sDel =0";
			$rel2 = mysql_query($sql,$link) ;

			while ($tmp2 = mysql_fetch_array($rel2)){
				$a[] = $tmp2['BrandName'].":".$tmp2['sReacllBrand']."%(品牌)、".$tmp2['sRecall']."%(地政士)";
			}
			$str .=',"'.@implode(';', $a).'"';
			unset($a);
		}
		else {
			$tmp['bCreat_time'] = DateChange($tmp['bCreat_time']);
			$sql = "SELECT fTitle,fAccountName FROM tFeedBackData WHERE fType = 2 AND fStoreId = ".$tmp['sn']." AND fStatus = 0";
			
			$rel2 = mysql_query($sql,$link) ;
			while ($tmp2 = mysql_fetch_array($rel2)){
				$t[] = $tmp2['fTitle'];
				$a[] = $tmp2['fAccountName'];
			}
			$tmp['bTtitle'] = @implode('_', $t);
			$tmp['bAccount8'] = @implode('_', $a);
			$str .= ',"'.$tmp['bCreat_time'].'",'.$tmp['bTtitle'].','.$tmp['bAccount8'].",".$tmp['bRecall']."%" ;

			unset($t);
			unset($a);
		}
		$cat = ($ide=='tScrivener') ? "1" : "2";

		

		if ($cat == 2) {
			$str .=','.sign($cat,$tmp['sn']).','.feed($cat,$tmp['sn']);//
			$str .= ','.$bServiceOrderHas;
		}else{
			$str .=','.feed($cat,$tmp['sn']);//
		}

		if ($field_undertaker) {
			$str .= ','.$tmp['sUndertaker'] ;
		}

		//地政士 合作品牌
		if ($_POST['field_sBrand']) {
			if ($ide=='tScrivener') {
				$tmpBrandContract = explode(',', $tmp['sBrand']);
				sort($tmpBrandContract);
				
				$str.= ',';
				foreach ($tmpBrandContract as $k => $v) {
					$str .= $BrandContract[$v].";";
				}
				
				unset($tmpBrandContract);
			}
		}

		// 顯示簽約業務
		if ($_POST['field_signSales']) {
			// if ($ide == 'tScrivener') $sql = 'SELECT (SELECT pName FROM tPeopleInfo WHERE pId=c.sSales) as sales FROM tScrivenerSales AS c WHERE sScrivener="'.$tmp['sn'].'" ORDER BY sId DESC ;' ;
			// else $sql = 'SELECT (SELECT pName FROM tPeopleInfo WHERE pId=c.bSales) as sales FROM tBranchSales AS c WHERE bBranch="'.$tmp['sn'].'" ORDER BY bId DESC ;' ;
			
			if ($ide == 'tScrivener') {
				// $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId=sSales) AS sales FROM tScrivenerSales as sales WHERE sScrivener = '".$tmp['sn']."'";
				$signSalesStr = " AND sType = '1'";
			}else{
				// $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId=bSales) AS sales FROM tBranchSales WHERE bBranch ='".$tmp['sn']."'";
				$signSalesStr = " AND sType = '2'";
			}

			$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId=sSales) as sSales  FROM tSalesSign WHERE sStore = '".$tmp['sn']."'".$signSalesStr;

			$rs = mysql_query($sql,$link) ;
			
			$str .= ',';
			while ($_tmp = mysql_fetch_array($rs)) {
				$tmpSales[] = $_tmp['sSales'];
			}
			$str .= implode('、', $tmpSales);
			unset($_tmp) ;unset($tmpSales) ;
		}

		// 結束換行
		$str .= "\n" ;
		unset($tmp) ;
		// echo $str;
		// die;	
	}
	
	header('Content-type:application/force-download');
	header("Content-type: text/x-csv");
	header("Content-Disposition: attachment; filename=csv_export.csv");

	echo $str ;
	include_once('../closedb.php') ;
	exit ;
}

$z_str = '';
if($_SESSION['member_test'] != 0){
	$sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '".$_SESSION['member_test']."'";
	
		
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$test_tmp[] = "'".$rs->fields['zZip']."'";

			$rs->MoveNext();
		}
		$z_str = " AND zZip IN(".implode(',', $test_tmp).")";
		unset($test_tmp);
    
}else if ($_SESSION['member_pDep'] == 7) {
	$z_str = 'AND FIND_IN_SET('.$_SESSION['member_id'].',zSales)';
}

//縣市
$sql = 'SELECT zCity FROM tZipArea WHERE 1=1 '.$z_str.'  GROUP BY zCity ORDER BY zZip,zCity ASC;' ;
$rel = mysql_query($sql,$link) ;
$citys = '<option selected="selected" value="">全部</option>'."\n" ;
while ($tmp = mysql_fetch_array($rel)) {
	$citys .= '<option value="'.$tmp['zCity'].'">'.$tmp['zCity']."</option>\n" ;
	unset($tmp) ;

}
##
$sql = "SELECT bName,bId FROM tBrand WHERE bContract = 1";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$BrandContract[$rs->fields['bId']] = $rs->fields['bName'];

	$rs->MoveNext();
}

######
function DateChange($val){
	
	// $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
	$tmp = explode('-',$val) ;
		
	if (preg_match("/0000/",$tmp[0])) {	$tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
		
	$val = $tmp[0].'/'.$tmp[1].'/'.$tmp[2] ;
	unset($tmp) ;

	return $val;
}

function sign($cat,$id){

	global $conn;

	$sql = "SELECT sStore FROM tSalesSign WHERE sType = '".$cat."' AND sStore = '".$id."'"; //有特約
	$rs = $conn->Execute($sql);
	
	$total=$rs->RecordCount();
	if ($total) {
		return '有';
	}else{
		return '無';
	}
	

}

function feed($cat,$id){
	global $conn;
	$sql = "SELECT fStoreId FROM tFeedBackData WHERE fType = '".$cat."' AND fStoreId ='".$id."' AND  fStatus = 0"; //合契
	$rs = $conn->Execute($sql);
	
	$total=$rs->RecordCount();
	if ($total) {
		return '有';
	}else{
		return '無';
	}
	

}
#######
$smarty->assign("y",$y) ;
$smarty->assign("m",$m) ;
$smarty->assign("citys",$citys);
$smarty->assign("menu",array(1 => '特約',2 => '合契',3 =>'先行撥付同意書'));
$smarty->assign('menuBrandContract',$BrandContract);
$smarty->display('csv_download.inc.tpl', '', 'report');
?>
