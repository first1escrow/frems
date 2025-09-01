<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


 $_POST = escapeStr($_POST) ;
 $cat = ($_POST['cat'])?$_POST['cat']:'1';


 
if ($_POST) {
	// $year = $_POST['year']+1911;
	// $scrivener = $_POST['scrivener'] ;
	include_once 'scrivenerBirthday_result3.php'; //排程算
	

	if ($_POST['xls'] == 1) {
		include_once 'scrivenerBirthday_excel.php';
	}
	$xls = 1;
}

//地政士
$sql = '
SELECT 
	sId,
	sName,
	CONCAT("SC",LPAD(sId,4,"0")) as Code 
FROM 
	tScrivener 
 
ORDER BY sId
ASC;
' ;
$rs = $conn->Execute($sql);
$scrivener_search = '' ;
while (!$rs->EOF) {
	$selected = ($rs->fields['sId'] == $_POST['scrivener'])?'selected=selected':'';
	$scrivener_search .= "<option value='".$rs->fields['sId']."'>".$rs->fields['Code'].$rs->fields['sName']."</option>\n" ;

	$rs->MoveNext();
}

$nowYear = date('Y')-1911;
$option_year = '';
for ($i=($nowYear+1); $i >=101 ; $i--) { 

	$selected = ($i == $_POST['year'] && $_POST['year']) ? "selected=selected" :'';
	
	$option_year .= "<option value=\"".$i."\" ".$selected.">".$i."</option>";
}
$option_month = "<option value=''></option>";
for ($i=1; $i <= 12; $i++) { 
	
	$selected = ($i == $_POST['month'] && $_POST['month']) ?"selected=selected":"";

	$option_month .= "<option value=\"".$i."\" ".$selected.">".$i."</option>";
}

$category = "<option value=''>全部</option>\n" ;
$selected = ($_POST['realestate'] == 11)?"selected=selected":'';
$category .= "<option value='11' ".$selected.">加盟(其他品牌)</option>\n" ;
$selected = ($_POST['realestate'] == 12)?"selected=selected":'';
$category .= "<option value='12' ".$selected.">加盟(台灣房屋)</option>\n" ;
$selected = ($_POST['realestate'] == 13)?"selected=selected":'';
$category .= "<option value='13' ".$selected.">加盟(優美地產)</option>\n" ;
$selected = ($_POST['realestate'] == 14)?"selected=selected":'';
$category .= "<option value='14' ".$selected.">加盟(永春不動產)</option>\n" ;
$selected = ($_POST['realestate'] == 1)?"selected=selected":'';
$category .= "<option value='1' ".$selected.">加盟</option>\n" ;
$selected = ($_POST['realestate'] == 2)?"selected=selected":'';
$category .= "<option value='2' ".$selected.">直營</option>\n" ;
$selected = ($_POST['realestate'] == 3)?"selected=selected":'';
$category .= "<option value='3' ".$selected.">非仲介成交</option>\n" ;
$selected = ($_POST['realestate'] == 4)?"selected=selected":'';
$category .= "<option value='4' ".$selected.">其他(未指定)</option>\n" ;
$selected = ($_POST['realestate'] == 5)?"selected=selected":'';
$category .= "<option value='5' ".$selected.">台屋集團</option>\n" ;
$selected = ($_POST['realestate'] == 6)?"selected=selected":'';
$category .= "<option value='6' ".$selected.">他牌+非仲</option>\n" ;
unset($selected);
function dateCg($val)
{
	$val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
	$tmp = explode('-',$val) ;
		
	if (preg_match("/0000/",$tmp[0])) {	$tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
		
	$val = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	return $val;
}
##
$smarty->assign('Datalevel',$Datalevel);
$smarty->assign('scrivener_search', $scrivener_search) ;
$smarty->assign('option_year',$option_year);
$smarty->assign('option_month',$option_month);
$smarty->assign('category',$category);
$smarty->assign('cat',$cat);
$smarty->assign('xls',$xls);
// $smarty->assign('scrivener',$_POST['scrivener']);
$smarty->assign('menuCat',array('1' => '地政士生日','2'=>'進案時間(查詢年度往前推一年)' ));
$smarty->display('scrivenerBirthdayForSales.inc.tpl', '', 'report') ;

?> 
