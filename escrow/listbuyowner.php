<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../session_check.php' ;

Function opt($l,$p) {
	if ($l==$p) {
		return " selected='selected'" ;
	}
}

if ($_SESSION['member_job'] != '1') {
    header('Location: http://' . $GLOBALS['DOMAIN']);
} 

$tt = '' ;
if (!$_REQUEST['time_limit']) {
	//$time_limit = date("n") ;
	$time_limit = '14' ;
}
else {
	//$time_limit = preg_replace("/^0/","",$_REQUEST['time_limit']) ;
	$time_limit = $_REQUEST['time_limit'] ;
}

$tt .= "<option value='14'".opt('14',$time_limit).">本年度</option>\n" ;
$tt .= "<option value='13'".opt('13',$time_limit).">去年度</option>\n" ;
$tt .= "<option value='15'".opt('15',$time_limit).">所有年度</option>\n" ;
for ($i = 1 ; $i < 13 ; $i ++) {
	//$tt .= "<option value='".$i."'".opt($i,$time_limit).">".$i."月份</option>\n" ;
	$tt .= "<option value='".(13-$i)."'".opt((13-$i),$time_limit).">".(13-$i)."月份</option>\n" ;
}


$tt = "<select name='timeLimit' id='timeLimit' onchange='tl()'>\n".$tt."</select>\n" ;


$smarty->assign('time_limit',$tt) ;

//$smarty->assign('menu_lv1', $GLOBALS['SYSTEM_MENU_LV1']);
//$smarty->assign('menu_lv2', $GLOBALS['SYSTEM_MENU_LV2']);
$smarty->display('listbuyowner.inc.tpl', '', 'escrow');
?> 
