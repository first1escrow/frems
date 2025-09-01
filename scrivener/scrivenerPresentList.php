<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
header("Content-Type:text/html; charset=utf-8"); 
$_POST = escapeStr($_POST) ;
##
$sql = "SELECT gId,gCode,gName FROM tGift WHERE gDel = 0 AND sTop = 1 ORDER BY gId ASC";
$rs = $conn->Execute($sql);
$option_gift ='';
$list_gift = array();
$menu_gift = array();
while (!$rs->EOF) {
	$selected = ($rs->fields['gId'] == $_POST['gift'])?'selected=selected':'';
	$option_gift .= "<option value=\"".$rs->fields['gId']."\" ".$selected.">".$rs->fields['gCode'].$rs->fields['gName']."</option>";
	array_push($list_gift, $rs->fields['gId']);
	// array_push($menu_gift,$rs->fields);
	$menu_gift[$rs->fields['gId']] = $rs->fields['gName'];
	$rs->MoveNext();
}
##

if ($_POST['cat']) {

	
	
	if ($_POST['gift']) {
		$str = 'sl.sGift = "'.$_POST['gift'].'"';
	}else{
		$str = 'sl.sGift IN ('.@implode(',', $list_gift).')';
	}

	if ($_POST['year']) {
		$str .= " AND LEFT(sl.sTime,4) = '".($_POST['year']+1911)."'";
	}

	if ($_POST['scrivener']) {
		$str .= " AND sl.sScrivener = '".$_POST['scrivener']."'";
	}

	if ($_SESSION['member_pDep'] == 7) {
		$tb = "LEFT JOIN
				tScrivenerSales AS ss ON ss.sScrivener = sl.sScrivener";

		$str .= " AND ss.sSales = '".$_SESSION['member_id']."' ";
	}


	$sql = "SELECT 
				s.sId,
				CONCAT('SC',LPAD(s.sId,4,'0')) as code,
				s.sName,
				(SELECT gName FROM tGift WHERE gId = sGift) AS gift,
				sl.sTime,
				sl.sGift
			FROM
				tScrivenerLevel AS sl
			LEFT JOIN
				tScrivener AS s ON s.sId=sl.sScrivener
			".$tb."
			WHERE
				".$str." AND sl.sStatus NOT IN(5,3) GROUP BY sl.sScrivener,sl.sGift ORDER BY sl.sYear";
	
	$rs = $conn->Execute($sql);

	$list = array();
	while (!$rs->EOF) {

		$list[$rs->fields['sId']]['name'] = $rs->fields['sName'];
		$list[$rs->fields['sId']]['code'] = $rs->fields['code'];

		$tmp = array();
		$tmp['gift'] = $rs->fields['gift'];
		$tmp['applyDate'] = substr($rs->fields['sTime'], 0,10);
		

		if (empty($list[$rs->fields['sId']]['gift'])) {
			$list[$rs->fields['sId']]['gift'] = array();
		}
		$list[$rs->fields['sId']]['gift'][$rs->fields['sGift']] = $tmp;
		// array_push($list[$rs->fields['sId']]['gift'], $tmp);
		
		$rs->MoveNext();
	}

	ksort($list);

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("第一建經");
	$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
	$objPHPExcel->getProperties()->setTitle("第一建經");
	$objPHPExcel->getProperties()->setSubject("地政士生日禮表(非禮券類)");
	$objPHPExcel->getProperties()->setDescription("地政士生日禮表(非禮券類)");

	//指定目前工作頁
	$objPHPExcel->setActiveSheetIndex(0);

	//
	$objPHPExcel->getActiveSheet()->setCellValue('A1',"地政士生日禮表(非禮券類)");

	$col = 65;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2',"地政士編號");
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2',"地政士姓名");

	if ($_POST['gift']) {
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2',$menu_gift[$_POST['gift']]);
	}else{
		foreach ($menu_gift as $k => $v) {
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2',$v);
		}
	}

	
	##
	$row = 3;
	foreach ($list as $k => $v) {
		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['code']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['name']);

		foreach ($menu_gift as $key => $value) {
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['gift'][$key]['applyDate']);
		}

		
		$row++;
	}
	
	##
	
	$_file = 'scrivnerPresent.xlsx' ;

	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header('Content-type:application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename='.$_file);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("php://output");

	exit ;
}
unset($str);
##
$nowYear = date('Y')-1911;
$option_year = '';
for ($i=$nowYear; $i >=107 ; $i--) { 

	$selected = ($i == $_POST['year'] && $_POST['year']) ? "selected=selected" :'';
	
	$option_year .= "<option value=\"".$i."\" ".$selected.">".$i."</option>";
}


##
#
if ($_SESSION['member_pDep'] == 7) {
	$tb = "LEFT JOIN tScrivenerSales AS ss ON ss.sScrivener=s.sId";
	$str = " WHERE ss.sSales = '".$_SESSION['member_id']."'";
}
$sql = "SELECT s.sId,s.sName,s.sOffice FROM tScrivener AS s ".$tb.$str." ORDER BY s.sId";

$rs = $conn->Execute($sql);
// $menu_scr[0] = '';
while (!$rs->EOF) {
	
	$scrivener_search .= '<option value="'.$rs->fields['sId'].'">SC'.str_pad($rs->fields['sId'],4,'0',STR_PAD_LEFT).$rs->fields['sName'].'('.$rs->fields['sOffice'].')</option>';
	$rs->MoveNext();
}
##
$smarty->assign('option_gift',$option_gift);
$smarty->assign('option_year',$option_year);
$smarty->assign('scrivener_search',$scrivener_search);
$smarty->display('scrivenerPresentList.inc.tpl', '', 'scrivener') ;
?> 
