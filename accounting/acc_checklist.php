<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/intolog.php' ;
require_once dirname(__DIR__) . '/openadodb.php' ;
require_once dirname(__DIR__) . '/session_check.php';

$exports = trim($_POST['exp']) ;

//預載log物件
$logs = new Intolog() ;
##

//輸出Excel檔案
if ($exports == 'ok') {
	$bank_option = trim($_REQUEST['bke']) ;
	$startDate = $fds = trim($_REQUEST['fds']) ;
	$endDate = $fde = trim($_REQUEST['fde']) ;
	
	$logs->writelog('accChecklistExcel') ;
	include_once 'acc_checklist_excel.php' ;
}
##

//取得合約銀行列表
$bank_option = '' ;
//$i = 4 ;	//永豐銀行
$i = 1 ;	//第一銀行

$sql = 'SELECT * FROM tContractBank WHERE cShow="1";' ;
$rs = $conn->Execute($sql) ;

while (!$rs->EOF) {
	if ($rs->fields['cId'] != '6' && $rs->fields['cId'] != '1') {				//由於永豐兩家分行都匯到同一個永豐活儲帳號，因此只取一家永豐活儲資料即可 //20220407 一銀只顯示一個不用拆開顯示
		$bank = $rs->fields['cBankFullName'] ;
		
		$bank_option .= '<option value="'.$rs->fields['cBankCode'].'"' ;
		
		if ($rs->fields['cId'] == $i) {
			$bank_option .= ' selected="selected"' ;
		}
		
		$bank_option .= '>'.$bank.'</option>'."\n" ;
	}
	
	$rs->MoveNext() ;
}
#

$smarty->assign('bank_option',$bank_option) ;

$smarty->display('acc_checklist.inc.tpl', '', 'accounting');
?>
