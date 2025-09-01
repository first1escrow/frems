<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$exports = $_POST['exp'] ;
$date_start = $_POST['date_start'];
$date_end = $_POST['date_end'];
//輸出Excel檔案
if ($exports == 'ok') {
	

	$People = $_POST['peo'];
	
	
	// $logs->writelog('accChecklistExcel') ;
	include_once 'caseProcessingCount_excel.php' ;
}



##

$sql  = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(5,6) AND pBankTrans IN(1,2) AND pId!=6 AND pJob = 1 ORDER BY pId ASC ";
$rs = $conn->Execute($sql);
$menuPeople[0] = '請選擇';
while (!$rs->EOF) {
	$list_People[$rs->fields['pId']]['name'] = $rs->fields['pName']; //選項
	// $data_People[]=$rs->fields['pId']; //被選取的
	$menuPeople[$rs->fields['pId']] = $rs->fields['pName']; //選項

	$rs->MoveNext();
}


if ($_POST) {
	$tmp = explode('-', $date_start);
	$sdate_start = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];
	$tmp = explode('-', $date_end);
	$sdate_end = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];


	if ($_POST['People'] > 0) {
		
		$People = $_POST['People'];

		$sql = "SELECT *,(SELECT pName FROM tPeopleInfo WHERE pId =rPId) AS pName FROM tReportUndertakerCase WHERE rDate >= '".$sdate_start."' AND rDate <= '".$sdate_end."' AND rPId = '".$People."' ORDER BY rDate";
		// $i = 0;
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$list_People2[] = $rs->fields;
			$rs->MoveNext();
		}

		
	}else{


		foreach ($list_People as $key => $value) {
			$sql = "SELECT * FROM tReportUndertakerCase WHERE rDate >= '".$sdate_start."' AND rDate <= '".$sdate_end."' AND rPId = '".$key."' ORDER BY rCaseCount DESC,rModifyDate LIMIT 1";
			if ($_SESSION['member_id'] == 6) {

		}
			$rs = $conn->Execute($sql);
			$list_People[$rs->fields['rPId']]['count'] = $rs->fields['rCaseCount'] ;
			$list_People[$rs->fields['rPId']]['time'] = substr($rs->fields['rDate'], 0,10) ;//
		}
	}
	
	

	// $sql = "SELECT
	// 			MAX(  `rCaseCount` ) AS MaxCount,
	// 			`rPId`
	// 		FROM 
	// 			tReportUndertakerCase
	// 		WHERE
	// 			rDate >= '".$sdate_start."' AND rDate <= '".$sdate_end."' GROUP BY  `rPId`";
	
	// $rs = $conn->Execute($sql);

	// while (!$rs->EOF) {
	// 	$list_People[$rs->fields['rPId']]['count'] = $rs->fields['MaxCount'] ;


	// 	$rs->MoveNext();
	// }

	
}

##


##
$smarty->assign('date_start',$date_start); //預設時間(今天)
$smarty->assign('date_end',$date_end); //預設時間(今天)
$smarty->assign('list_People',$list_People); //人員選項
$smarty->assign('list_People2',$list_People2);
$smarty->assign('menuPeople',$menuPeople);
$smarty->assign('People',$People);
$smarty->display('caseProcessingCount.inc.tpl', '', 'banktrans');
?>