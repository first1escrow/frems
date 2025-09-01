<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


if ($_POST['check']==1) {
	include_once 'storeFeedBackUpload_file.php';
}

$this_year = date("Y") - 1911 ;
$this_month = date("m") ;

// 年度
$menu_year = '' ;
for ($i = $this_year + 2 ; $i > $this_year - 100 ; $i --) {
	$menu_year .= '<option value="'.($i + 1911).'"' ;
	if ($i == $this_year) { $menu_year .= ' selected="selected"' ; }
	$menu_year .= '>'.$i."</option>\n" ;
}
##
//仲介選單
$sql = "SELECT
			CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) as code,
			(Select bName From `tBrand` c Where c.bId = bBrand ) AS bBrand,
			bStore
		FROM
			tBranch WHERE bStatus = 1";
			
$rs = $conn->Execute($sql);
$menuBranch[0] ='';
while (!$rs->EOF) {
	$menuBranch[$rs->fields['code']] = $rs->fields['code'].$rs->fields['bBrand'].$rs->fields['bStore'];
	$rs->MoveNext();
}

//地政
$sql = "SELECT
			CONCAT('SC',LPAD(sId,4,'0')) AS code,
			sName,
			sOffice
		FROM
			tScrivener
		WHERE
			sStatus = 1";
$rs = $conn->Execute($sql);

$menuScrivener[0] ='';
while (!$rs->EOF) {
	$menuScrivener[$rs->fields['code']] = $rs->fields['code'].$rs->fields['sName'];

	if ($rs->fields['sOffice']) {
		$menuScrivener[$rs->fields['code']] .= '('.$rs->fields['sOffice'].')';
	}

	$rs->MoveNext();
}

##
$smarty->assign('menuBranch',$menuBranch);
$smarty->assign('menuScrivener',$menuScrivener);
$smarty->assign('msg',$msg);
$smarty->assign('menu_year',$menu_year);
$smarty->assign('menu_season', array(
									'S1' => '第一季', 
									'S2' => '第二季',
									'S3' => '第三季',
									'S4' => '第四季',
									'01' => '1月份',
									'02' => '2月份',
									'03' => '3月份',
									'04' => '4月份',
									'05' => '5月份',
									'06' => '6月份',
									'07' => '7月份',
									'08' => '8月份',
									'09' => '9月份',
									'10' => '10月份',
									'11' => '11月份',
									'12' => '12月份')
									);
$smarty->display('storeFeedBackUpload.inc.tpl', '', 'accounting');
?>