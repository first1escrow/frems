<?php
include_once dirname(__DIR__) . '/configs/config.class.php';
include_once dirname(__DIR__) . '/class/SmartyMain.class.php';
include_once dirname(__DIR__) . '/session_check.php' ;

$feedback_year = '' ;
$feedback_month = '<option value="" selected="selected">全</option>'."\n" ;
$arr = array("一","二","三","四","五","六","七","八","九","十","十一","十二") ;

$y = date("Y") ;	//取得本年度
$yb = 2012 ;		//建經扣繳起始年度
$ye = $y + 10 ;		//設定年度結束範圍

//設定結算顯示年度
for ($i = $yb ; $i < $ye ; $i ++) {
	$feedback_year .= "\t\t\t\t<option value='".$i."'" ;
	if ($i == $y) { $feedback_year .= " selected='selected'" ;	}
	$feedback_year .= ">".($i - 1911)."</option>\n" ; 
}
##

//設定結算顯示月份
for ($i = 0 ; $i < 12 ; $i ++) {
	//$feedback_month .= '<option value="'.($i+1).'">'.$arr[$i].'</option>'."\n" ;
	$feedback_month .= '<option value="'.($i+1).'">'.($i+1).'</option>'."\n" ;
}
##

$smarty->assign('feedback_year', $feedback_year) ;
$smarty->assign('feedback_month', $feedback_month) ;

$smarty->display('taxreceipt.inc.tpl', '', 'accounting') ;
?> 
