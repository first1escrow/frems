<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$year = ($_POST['year'])?$_POST['year']:(date('Y')-1911);
$month = ($_POST['month'])?$_POST['month']:date('m');
##
for ($i=109; $i <= $year; $i++) { 
	$menu_y[$i] =$i;
}


for ($i=1; $i <=12 ; $i++) { 
	$menu_m[str_pad($i, '2','0',STR_PAD_LEFT)] =  str_pad($i, '2','0',STR_PAD_LEFT);
}



//經辦列表
$pIdArray = array();
$sql  = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(5,6) AND pId!=6 AND pJob = 1 ORDER BY pId ASC ";
$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {
	$list_people[$rs->fields['pId']]['name'] = $rs->fields['pName']; //選項
	$list_people[$rs->fields['pId']]['id'] = $rs->fields['pId'];
	$pIdArray[] = $rs->fields['pId']; //選項
	
	$i++;
	$rs->MoveNext();
}

##

$pIdStr = implode(',', $pIdArray);
$sDate = ($year+1911)."-".$month."-01";
$eDate = ($year+1911)."-".$month."-".date('t',strtotime($sDate));

// echo $sDate."_";
// echo date('t',10)."<br>";
##出款數
$query_date = "AND tDate >='".$sDate." 00:00:00' AND tDate <= '".$eDate." 23:59:59'";
$query = " AND s.sUndertaker1 IN (".$pIdStr.")";
$sql = "
		SELECT 
			bt.tMemo,
			s.sUndertaker1,
			(SELECT pName FROM tPeopleInfo WHERE pId=s.sUndertaker1) as name,
			(SELECT pTest FROM tPeopleInfo WHERE pId=bt.tOwner) as pTest,
			(SELECT pCategory_stime FROM tPeopleInfo WHERE pName=bt.tOwner) as cat_stime,
			(SELECT pCategory_etime FROM tPeopleInfo WHERE pName=bt.tOwner) as cat_etime,
			(SELECT pId FROM tPeopleInfo WHERE pName=bt.tOwner) as OwnerId,
			bt.tOwner,
			bt.tBankLoansDate,
			bt.tDate
		FROM 
			 tBankTrans AS bt
		LEFT JOIN 
			tContractScrivener AS cs ON cs.cCertifiedId=bt.tMemo		
		LEFT JOIN 
			tScrivener AS s ON cs.cScrivener=s.sId
		WHERE
			bt.tExport='1' ".$query_date.$query."  OR (tMemo IN ('000000000','000000008') ".$query_date.") 
			
		ORDER BY tBankLoansDate ASC";
// echo $sql;

$rs = $conn->Execute($sql);
$total=$rs->RecordCount();//計算總筆數

while (!$rs->EOF) {
	$date = substr($rs->fields['tDate'], 0,10);
	##利息出款
	if ($rs->fields['tMemo']=='000000000' || $rs->fields['tMemo']=='000000008') {//如果是利息出款則取建檔者
		
		$rs->fields['sUndertaker1']=$rs->fields['OwnerId'];
		$rs->fields['name']=$rs->fields['tOwner'];

	}
	##

	$sql = "SELECT uSubstituteStaff,uStaff FROM tUndertakerCalendar WHERE uDateTime <= '".$rs->fields['tDate']."' AND uDateTime2 >= '".$rs->fields['tDate']."' AND (uStaff = '".$rs->fields['sUndertaker1']."' OR uStaff = '".$rs->fields['OwnerId']."') AND uDel = 0";

	$rs2 = $conn->Execute($sql);
	$total = $rs2->RecordCount();

	##代理出款筆數(幫別人出款)
	if ($total > 0) {
		if (array_key_exists($rs2->fields['uSubstituteStaff'], $list_people)) {
			$list_people[$rs2->fields['uSubstituteStaff']]['banktrans']++;
		}
		
				
	}else if ($rs->fields['sUndertaker1'] != $rs->fields['OwnerId'] && $rs->fields['pTest'] != 1) { 

		if (strtotime($rs->fields['tDate']) >= strtotime($rs->fields['cat_stime']) && strtotime($rs->fields['tDate']) <= strtotime($rs->fields['cat_etime'])) {
			if (array_key_exists($rs->fields['sUndertaker1'], $list_people)) {
				$list_people[$rs->fields['sUndertaker1']]['banktrans']++;
			}

		}else{

			
			if (array_key_exists($rs->fields['OwnerId'], $list_people)) {
				$list_people[$rs->fields['OwnerId']]['banktrans']++;
			}
		
		}
		
	}else{

		if (array_key_exists($rs->fields['sUndertaker1'], $list_people)) {
			$list_people[$rs->fields['sUndertaker1']]['banktrans']++;
		}
		
	}

	unset($total);

	$rs->MoveNext();
}
##
##平均數
$list = array();
$days = round((strtotime($eDate)-strtotime($sDate))/3600/24)+1 ; //會少一天要+1
// echo $days."<bR>";
$sql = "SELECT rPId,rDate,rCaseCount,(SELECT pName FROM tPeopleInfo WHERE pId = rPId) AS uName  FROM tReportUndertakerCase WHERE rDate >= '".$sDate."' AND rDate <= '".$eDate."' AND rPId IN (".$pIdStr.") ORDER BY rDate,rPId ASC ";
	// echo $sql;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	
	$list_people[$rs->fields['rPId']]['caseCount'] += $rs->fields['rCaseCount'];


	
		
		$rs->MoveNext();
}


// 出款筆數	   獎勵金	案件量	    獎勵金
// 951~1099筆	1000元	301~349件	1000元
// 1100~1199筆	2000元	350~399件	2000元
// 1200~1299筆	3000元	400~449件	3000元
// 1300筆以上	4000元	450件以上	4000元


foreach ($list_people as $key => $value) {
	// echo $key."_".$value['caseCount']."_".$value['caseCount2']."<bR>";
	$list_people[$key]['avgcount'] = round($value['caseCount']/$days);//查詢範圍平均最高件數
	//出款筆數獎勵金
	if ($list_people[$key]['banktrans'] >= 951 && $list_people[$key]['banktrans'] <= 1099) {
		$list_people[$key]['banktransBonus'] = '1000';
	}elseif ($list_people[$key]['banktrans'] >= 1100 && $list_people[$key]['banktrans'] <= 1199) {
		$list_people[$key]['banktransBonus'] = '2000';
	}elseif ($list_people[$key]['banktrans'] >= 1200 && $list_people[$key]['banktrans'] <= 1299) {
		$list_people[$key]['banktransBonus'] = '3000';
	}elseif ($list_people[$key]['banktrans'] >= 1300) {
		$list_people[$key]['banktransBonus'] = '4000';
	}


	//案件量獎勵金
	if ($list_people[$key]['avgcount'] >= 301 && $list_people[$key]['avgcount'] <= 349) {
		$list_people[$key]['avgcountBonus'] = '1000';
	}elseif ($list_people[$key]['avgcount'] >= 350 && $list_people[$key]['avgcount'] <= 399) {
		$list_people[$key]['avgcountBonus'] = '2000';
	}elseif ($list_people[$key]['avgcount'] >= 400 && $list_people[$key]['avgcount'] <= 449) {
		$list_people[$key]['avgcountBonus'] = '3000';
	}elseif ($list_people[$key]['avgcount'] >= 450) {
		$list_people[$key]['avgcountBonus'] = '4000';
	}

	$list_people[$key]['totalBonus'] = $list_people[$key]['banktransBonus']+$list_people[$key]['avgcountBonus'];

	$list_people[$key]['realBonus'] = round($list_people[$key]['totalBonus']/2);
}

//$list_people[$rs2->fields['uSubstituteStaff']]['banktrans']++;
// $real_total = $list[$arr[$i]]['total']+$list[$arr[$i]]['extra'];
##



##
$smarty->assign('list',$list);
$smarty->assign('list_people',$list_people);
$smarty->assign('menu_y',$menu_y);
$smarty->assign('menu_m',$menu_m);
$smarty->assign('year',$year);
$smarty->assign('month',$month);
$smarty->assign('data',$data);
$smarty->assign('data_t',$total);
$smarty->display('bonusList.inc.tpl', '', 'banktrans') ;
?>