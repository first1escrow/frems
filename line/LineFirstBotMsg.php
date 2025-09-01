<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


$_POST = escapeStr($_POST) ;

$zip = $_POST['zip'];
$iden = ($_POST['iden'])?$_POST['iden']:array('S','R','B');
##選單##
$sql = "SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY nid ASC";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$tmpZip[] = $rs->fields;

	$rs->MoveNext();
}
$checkedALL = (count($tmpZip) == count($zip) || count($zip) == 0 )?'checked=checked':'';//全選的是否要勾
$menuZip = '';
$i = 1;
foreach ($tmpZip as $k => $v) {

	if (is_array($zip)) {
		$checked = (in_array($v['zCity'], $zip))? 'checked=checked':'';
	}else{
		$checked = "checked=checked";
	}

	$menuZip .= "<input type=\"checkbox\" name=\"zip[]\" value=\"".$v['zCity']."\" ".$checked." onclick=\"checkClick()\" class=\"cb\"><span class=\"cb-title\">".$v['zCity']."</span>";


	if ($i != 1 && $i % 5 ==0) {
		$menuZip .= "<br>";
	}

	$i++;
}
unset($i);unset($tmpZip);
##

if ($_POST) {
	if ($zip) {
	
		foreach ($zip as $key => $value) {
			$strZip[]= '"'.$value.'"';
		}

		$sql = "SELECT zZip FROM tZipArea WHERE zCity IN(".implode(',', $strZip).")";
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$zipArr[] = '"'.$rs->fields['zZip'].'"';

			$rs->MoveNext();
		}
		$zipStr = implode(',', $zipArr);

		unset($strZip);unset($zipArr);
		
	}


	foreach ($iden as $k => $v) {
			
		if ($v == 'S') {
			if ($zip) {
				$str = "AND sCpZip1 IN (".$zipStr.")";
			}
			$sql = "SELECT CONCAT('SC',LPAD(sId,4,'0')) AS code,sName,sOffice FROM tScrivener WHERE sStatus = 1 ".$str." ORDER BY sId";
			// echo $sql;
			$rs = $conn->Execute($sql);

			while (!$rs->EOF) {
				$TargetCode[] = '"'.$rs->fields['code'].'"';
				$matchStore[$rs->fields['code']] = $rs->fields['sName']."(".$rs->fields['sOffice'].")";
				$rs->MoveNext();
			}
		}elseif ($v == 'R' || $v == 'B') {
			$sql = "SELECT 
						CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) AS code,
						(Select bName From `tBrand` c Where c.bId = bBrand) AS brand,
						bStore
					FROM
						tBranch WHERE bZip IN (".$zipStr.")";
			
			$rs = $conn->Execute($sql);
			while (!$rs->EOF) {
				$TargetCode[] = '"'.$rs->fields['code'].'"';
				$matchStore[$rs->fields['code']] = $rs->fields['brand'].$rs->fields['bStore'];
				$rs->MoveNext();
			}
		}


	}
	$i=0;
	$sql = "SELECT lId,lNickName,lTargetCode,lCaseMobile FROM tLineAccount WHERE lTargetCode IN(".@implode(',', $TargetCode).") AND lStatus = 'Y' AND lId NOT IN(4,7,8,12,13,817)";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$list[$i] = $rs->fields;
		$list[$i]['storeName'] = $matchStore[strtoupper($rs->fields['lTargetCode'])];

		$i++;
		$rs->MoveNext();
	}

	unset($i);
}
##

$sql = "SELECT * FROM tLineMoji";

$rs = $conn->Execute($sql);


while (!$rs->EOF) {
	$moji[] = $rs->fields;

	$rs->MoveNext();
}

// print_r($list);
##
$smarty->assign('moji',$moji);
$smarty->assign('checkedALL',$checkedALL);
$smarty->assign('menuIden',array('S'=>'地政士','R'=>'仲介','B'=>'經紀人'));
$smarty->assign('iden',$iden);
$smarty->assign('list',$list);
$smarty->assign('menuZip',$menuZip);
$smarty->display('LineFirstBotMsg.inc.tpl', '', 'line');
?>
