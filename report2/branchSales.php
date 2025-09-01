<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;


$_POST = escapeStr($_POST) ;
$check = $_POST['ck'];


if ($check==1 && $_POST['cat2'] == 2) {

	include_once 'branchSalesExcel.php';

}elseif ($check==1 && $_POST['cat2'] == 1) {
	include_once 'scrivenerSalesExcel.php';
}

$sql = "SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY nid ASC";
$rs = $conn->Execute($sql);
$i = 1;
while (!$rs->EOF) {
	// $menu_City[$rs->fields['zCity']] = $rs->fields['zCity'];

	$menu_City .= "<input type=\"checkbox\" name=\"city[]\" value=\"".$rs->fields['zCity']."\" >".$rs->fields['zCity']."&nbsp;&nbsp;&nbsp;&nbsp;";
	if ($i % 5 == 0 && $i != 0) {
		$menu_City .= "<br>";
	}
	$i++;
	$rs->MoveNext();
}

##

$menuCategory = "<option value='11'>加盟(其他品牌)</option>\n" ;
$menuCategory .= "<option value='12'>加盟(台灣房屋)</option>\n" ;
$menuCategory .= "<option value='13'>加盟(優美地產)</option>\n" ;
$menuCategory .= "<option value='14'>加盟(永春不動產)</option>\n" ;
$menuCategory .= "<option value='1'>加盟</option>\n" ;
$menuCategory .= "<option value='2'>直營</option>\n" ;
$menuCategory .= "<option value='3'>非仲介成交</option>\n" ;
$menuCategory .= "<option value='4'>其他(未指定)</option>\n" ;
$menuCategory .= "<option value='5'>台屋集團</option>\n" ;
$menuCategory .= "<option value='6'>他牌+非仲</option>\n" ;
##
function checkCat($no,$brand) {
	global $conn;
	$val = '' ;
	
	if ($no) {
		$sql = 'SELECT
					(SELECT bId FROM tBrand AS br WHERE br.bId = '.$brand.') AS bBrand,
					bCategory
					
				FROM
					tBranch
				WHERE
					bId="'.$no.'" AND bId<>"0";' ;
		
		$rs = $conn->Execute($sql);

		
		if ($rs->fields['bCategory'] == '1') {
			
			if ($rs->fields['bBrand'] == '1') {
				$val = 12 ;
			}
			else if ($rs->fields['bBrand'] == '2') {
				$val = 3 ;
			}
			else if ($rs->fields['bBrand'] == '49') {
				$val = 13 ;
			}
			else if ($rs->fields['bBrand'] == '56') {
				$val = 14 ;
			}
			else {
				$val = 11 ;
			}
		}
		else if ($rs->fields['bCategory'] == '2') {
			$val = 2;
		}
		else if ($rs->fields['bCategory'] == '3') {
			$val = 3 ;
		}
		
	}
	// echo $val."-----";
	return $val ;
}
#####
$smarty->assign('menuCategory',$menuCategory);
$smarty->assign('menu_City',$menu_City);
$smarty->display('branchSales.inc.tpl', '', 'report2');

?>