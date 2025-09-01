<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../opendb.php' ;
include_once '../openadodb.php' ;
$brand = '' ;
$status = '' ;
$contract_bank = '' ;

$query = 'SELECT bId,bName FROM tBrand ORDER BY bId ASC;' ;
$result = @mysql_query($query,$link) ;
$max = @mysql_num_rows($result) ;

for ($i = 0 ; $i < $max ; $i ++) {
	$tmp = @mysql_fetch_array($result) ;
	$brand .= "<option value='".$tmp['bId']."'>".$tmp['bName']."</option>\n" ; ;
	unset($tmp) ;
}

$query = 'SELECT sId,sName FROM tStatusCase WHERE sId<>"0" ORDER BY sId ASC;' ;
$result = @mysql_query($query,$link) ;
$max = @mysql_num_rows($result) ;

for($i = 0 ; $i < $max ; $i ++) {
	$tmp = @mysql_fetch_array($result) ;
	$status .= "<option value='".$tmp['sId']."'>".$tmp['sName']."</option>\n" ;
	unset($tmp) ;
}

// 簽約銀行
$query = 'SELECT cBankCode,(SELECT cBankName FROM tCategoryBank WHERE cId=cbk.cBankCode) cBankName FROM tContractBank AS cbk WHERE (cbk.cShow="1" OR cId = 5) ORDER BY cId ASC;' ;
$result = mysql_query($query,$link) ;
$max = mysql_num_rows($result) ;

for ($i = 0 ; $i < $max ; $i ++) {
	$tmp = mysql_fetch_Array($result) ;
	$contract_bank .= "<option value='".$tmp['cBankCode']."'" ;
	//if ($tmp['cBankCode']=='8') { $contract_bank .= " selected='selected'" ; }
	$contract_bank .= ">".$tmp['cBankName']."</option>\n" ;
	unset($tmp) ;
}
##

// 經辦人員篩選
$undertaker = '' ;

if ($_SESSION['member_id'] != 6 && $_SESSION['member_id'] != 1 && $_SESSION['member_id'] != 39) {
	$str = "AND b.pId = '".$_SESSION['member_id']."'";
}

$sql = '
	SELECT 
		b.pName as cUndertaker,
		b.pId as cUndertakerId 
	FROM 
		tContractCase AS a
	JOIN
		tPeopleInfo AS b ON b.pId=a.cUndertakerId
	WHERE
		b.pJob="1" 
		AND b.pId<>"6"
		'.$str.'
	GROUP BY
		b.pId
	;' ;
$rel = mysql_query($sql,$link) ;
while ($tmp = mysql_fetch_Array($rel)) {
	if ($tmp['cUndertakerId']!='6') {
		$undertaker .= "<option value='".$tmp['cUndertakerId']."'>".$tmp['cUndertaker']."</option>\n" ;
	}
	unset($tmp) ;
}
##

//是否顯示簡訊資訊視窗
$sms_window = '' ;
$sql = 'SELECT * FROM tSmsSystem WHERE sUsed="1";' ;
$rel = mysql_query($sql,$link) ;
if (mysql_num_rows($rel)) {
	if ($_SESSION['sms_window'] != '1') {
		$sms_window = 'window.open("../sms/sms_summary.php","sms_summary","height=60px,width=300px,status=no") ;' ;
		
		if (($_SESSION['member_pDep'] == 5 || $_SESSION['member_id'] == 6) && $_SESSION['sms_window'] != '1') {
			$sms_window .= 'window.open("../report/transNoEnd.php?s=1","UnEnd","status=no,scrollbars=1") ;' ;
			
		}

		if ($_SESSION['member_id'] == 6 || $_SESSION['member_pDep'] == 4) {
			$sms_window .= 'window.open("../sales/certifiedFee.php?s=1","cf","status=no") ;' ;
			
		}
		$_SESSION['sms_window'] = '1' ;

	}
}
##
//7
// if ($_SESSION['member_id'] == 38) {
// 	$z_str = ' AND zCity IN ("高雄市","屏東縣","台東縣")';
// }elseif ($_SESSION['member_id'] == 34) {
// 	$z_str = ' AND zCity IN ("嘉義縣","嘉義市","雲林縣","台南市")';
// }elseif ($_SESSION['member_id'] == 25) {
// 	$z_str = ' AND zSales ="25"';
// }

if ($_SESSION['member_test'] != 0) {
	// $z_str = ' AND zCity IN ("新竹縣","新竹市","苗栗縣")';
	$sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '".$_SESSION['member_test']."'";
	
		
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$test_tmp[] = "'".$rs->fields['zZip']."'";

			$rs->MoveNext();
		}
		$z_str = " AND zZip IN(".implode(',', $test_tmp).")";
		unset($test_tmp);

}elseif ($_SESSION['member_pDep'] == 7) {
	$z_str = 'AND FIND_IN_SET('.$_SESSION['member_id'].',zSales)';
}



//縣市
$sql="SELECT zZip,zCity FROM tZipArea WHERE 1=1 ".$z_str." GROUP BY zCity ORDER BY zZip ASC;";

$rel = mysql_query($sql,$link) ;
while ($tmp = mysql_fetch_Array($rel)) {
	$listCity .= "<option value='".$tmp['zCity']."'>".$tmp['zCity']."</option>";
}
//區域

@mysql_close($link) ;


$smarty->assign('Y',date('Y'));
$smarty->assign('country', $listCity) ;						//縣市
// $smarty->assign('land_area', $listArea) ;							//區域
$smarty->assign('sms_window', $sms_window);
$smarty->assign('brand', $brand);
$smarty->assign('status', $status);
$smarty->assign('contract_bank', $contract_bank);
$smarty->assign('undertaker', $undertaker);
$smarty->assign('web_addr', $web_addr);

$smarty->display('buyerownerinquery.inc.tpl', '', 'inquire');
?> 
