<?php
include_once '../../configs/config.class.php';
include_once '../../class/SmartyMain.class.php';
include_once '../../web_addr.php' ;
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;

##銀行區域##
$sql = "SELECT * FROM tBankBannerArea ORDER BY bId ASC";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {

	$menu_bank[$rs->fields['bBank']] = $rs->fields['bBankName'];

	$rs->MoveNext();
}
##


$sql = "SELECT * FROM tBankBanner WHERE bDel = 0 ORDER BY bSort ASC";

$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {

	if ($rs->fields['bLink'] != '') {
        $rs->fields['bUrl'] = $rs->fields['bLink'];
    }
    $rs->fields['bUrl'] = urldecode($rs->fields['bUrl']);

	$list[$i] = $rs->fields; //<{$item.bStart}><{$item.bEnd}>
	$list[$i]['bBank'] = $menu_bank[$rs->fields['bBank']];
	$list[$i]['status'] = ($rs->fields['bOk'] =='1') ? '已上架':'未上架';
	$list[$i]['status2'] = ($rs->fields['bOk2'] =='1') ? '已上架':'未上架';
	if ($list[$i]['bStart'] == '0000-00-00' && $list[$i]['bEnd'] == '0000-00-00') {
		$list[$i]['window'] = '不顯示';
	}else{
		$list[$i]['window'] = $list[$i]['bStart']."至".$list[$i]['bEnd'];
	}
	
	$i++;
	$rs->MoveNext();
}
####
$smarty->assign('list',$list);
$smarty->display('banner.inc.tpl', '', 'www');
?>
