<?php
include_once dirname(dirname(__FILE__)).'/configs/config.class.php';
include_once dirname(dirname(__FILE__)).'/class/SmartyMain.class.php';
include_once dirname(dirname(__FILE__)).'/web_addr.php' ;
include_once dirname(dirname(__FILE__)).'/openadodb.php' ;
include_once dirname(dirname(__FILE__)).'/session_check.php' ;


$menuCategory = array(1=>'土地',2=>'建物');

$sql = "SELECT * FROM tEContract WHERE eDel = 0";

$rs = $conn->Execute($sql);
// $i = 0;
while (!$rs->EOF) {

	$list[$i] = $rs->fields; //<{$item.bStart}><{$item.bEnd}>
	$list[$i]['eApplication'] = $menuCategory[$rs->fields['eApplication']];
	$list[$i]['eSendIden'] = ($list[$i]['eSendIden'] == 1)?'是':'否';

	$i++;
	$rs->MoveNext();
}


####
$smarty->assign('list',$list);
$smarty->display('contractlist.inc.tpl', '', 'contract');
?>
