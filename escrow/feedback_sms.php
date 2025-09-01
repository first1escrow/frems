<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;



$sql = "
		SELECT
			CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ), LPAD(bId,5,'0'))  bCode2,
			(SELECT bName FROM tBrand AS a WHERE a.bId= b.bBrand) AS brand,
			bStore,
			bId
		FROM 
			tBranch AS b
		
		ORDER BY bBrand,bId ASC
";


$rs = $conn->Execute($sql);
$menu[0]='請輸入名稱';
while (!$rs->EOF) {
	
	$menu[$rs->fields['bCode2']] = '('.$rs->fields['bCode2'].')'.$rs->fields['brand'].$rs->fields['bStore'] ;

	$rs->MoveNext();
}

$sql = "SELECT sMobileNum,sId,sName FROM tScrivener ORDER BY sId ASC";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$code = 'SC'.str_pad($rs->fields['sId'],4,'0',STR_PAD_LEFT);
	
	$menu[$code] = '('.$code.')'.$rs->fields['sName'];
	$rs->MoveNext();
}
$company = json_decode(file_get_contents(dirname(dirname(__FILE__)).'/includes/company.json'),true) ;

##
//
$season = (date('Y')-1911)."年";
$month = date('m');
if ($month >= 1 && $month <= 3 ) {
	$season = ((date('Y')-1911)-1)."年";
	$season .= "第4季";
}elseif ($month >= 4 && $month <= 6) {
	$season .= "第1季";
}elseif ($month >= 7 && $month <= 9) {
	$season .= "第2季";
}elseif ($month >= 10 && $month <= 12) {
	$season .= "第3季";
}

##
$smarty->assign('company',$company);
$smarty->assign('season',$season);
$smarty->assign('today',date('m/d'));
$smarty->assign('menu', $menu) ;
$smarty->display('feedback_sms.inc.tpl', '', 'escrow');
?>
