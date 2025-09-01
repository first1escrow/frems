<?php
require_once '../configs/config.class.php';
require_once '../class/SmartyMain.class.php';
require_once '../openadodb.php' ;
require_once '../report/getBranchType.php' ;

// print_r($_POST) ; exit ;

if ($_POST['query'] == 'ok') $query = '' ;
else $query = 'none' ;

if (preg_match("/^[0-9]{2,3}$/",$_POST['yearDate'])) $yearDate = $_POST['yearDate'] ;
else {
	$yearDate = (date("Y", strtotime("-1 month")) - 1911) ;
	// echo "請指定查詢年度!!<br>\n" ;
	// exit ;
}

if (preg_match("/^[0-9]{2}$/",$_POST['monthDate'])) $monthDate = $_POST['monthDate'] ;
else {
	$monthDate = date("m", strtotime("-1 month")) ;
	// echo "請指定查詢月份!!<br>\n" ;
	// exit ;
}	
// echo date("Y", strtotime("-1 month")).', '.date("m", strtotime("-1 month"))."<br>\n" ; exit ;

$FDate = ($yearDate + 1911).'-'.$monthDate.'-01' ;
$TDate = ($yearDate + 1911).'-'.$monthDate.'-31' ;
// echo $FDate.', '.$TDate."<br>\n" ; exit ;
//進案件數(簽約)
$sql = '
	SELECT
		cas.cCertifiedId,
		cas.cApplyDate,
		cas.cSignDate,
		cas.cEndDate,
		rea.cBrand as brand,
		rea.cBrand1 as brand1,
		rea.cBrand2 as brand2,
		rea.cBranchNum as branch,
		rea.cBranchNum1 as branch1,
		rea.cBranchNum2 as branch2,
		inc.cCertifiedMoney
	FROM
		tContractCase AS cas
	LEFT JOIN
		tContractRealestate AS rea ON rea.cCertifyId = cas.cCertifiedId
	LEFT JOIN
		tContractIncome AS inc ON inc.cCertifiedId = cas.cCertifiedId
	WHERE
		cas.cSignDate >= "'.$FDate.' 00:00:00"
		AND cas.cSignDate <= "'.$TDate.' 23:59:59"
		AND cas.cCaseStatus <> "8"
		AND cas.cCertifiedId !="005030342"
		AND cas.cCertifiedId<>""
	ORDER BY
		cas.cSignDate
	ASC
;' ;

// echo $sql ; exit ;
$rs = $conn->Execute($sql) ;

$list1 = array('2' => 0, 'T' => 0, 'U' => 0, 'F' => 0, 'O' => 0, '3' => 0) ;
while (!$rs->EOF) {
	$v = array() ;
	$v = $rs->fields ;
	
	$type = branch_type($conn,$v) ;

	if ($type == 'F') {
		$type = 'O';
	}
	// $v['type'] = $type ;
	// $list[] = $v ;
	$list1['count'][$type] ++ ;
	$list1['money'][$type] += $v['cCertifiedMoney'] ;
	$list1['data'][$type][] = $v ;

	// if ($type == 'N') {
		// print_r($v) ; exit ;
	// }
	if ($type != 'N') {
		$list1['total'] ++ ;
		$list1['total2'] += $v['cCertifiedMoney'] ;
	}
	
	
	unset($v) ;
	$rs->MoveNext() ;
}
// if ($_SESSION['member_id'] == 6) {
// 	echo "<pre>";
// 	print_r($list1) ;
// 	die;
// }
// print_r($list1) ; // exit ;
// $list1['2'] = 台屋直營
// $list1['T'] = 台屋加盟
// $list1['U'] = 優美
// $list1['F'] = 永春
// $list1['O'] = 其他品牌
// $list1['3'] = 代書個人(非仲介成交)
##

//結案件數
$sql = '
	SELECT
		cas.cCertifiedId,
		cas.cApplyDate,
		cas.cSignDate,
		cas.cEndDate,
		rea.cBrand as brand,
		rea.cBrand1 as brand1,
		rea.cBrand2 as brand2,
		rea.cBranchNum as branch,
		rea.cBranchNum1 as branch1,
		rea.cBranchNum2 as branch2,
		inc.cCertifiedMoney
	FROM
		tContractCase AS cas
	LEFT JOIN
		tContractRealestate AS rea ON rea.cCertifyId = cas.cCertifiedId
	LEFT JOIN
		tContractIncome AS inc ON inc.cCertifiedId = cas.cCertifiedId
	WHERE
		cas.cEndDate >= "'.$FDate.' 00:00:00"
		AND cas.cEndDate <= "'.$TDate.' 23:59:59"
		AND cas.cCaseStatus <> "8"
	ORDER BY
		cas.cEndDate
	ASC
;' ;
// echo $sql ; exit ;
$rs = $conn->Execute($sql) ;

$list2 = array('2' => 0, 'T' => 0, 'U' => 0, 'F' => 0, 'O' => 0, '3' => 0) ;
while (!$rs->EOF) {
	$v = array() ;
	$v = $rs->fields ;
	
	$type = branch_type($conn,$v) ;
	if ($type == 'F') {
		$type = 'O';
	}
	// $v['type'] = $type ;
	// $list[] = $v ;
	$list2['count'][$type] ++ ;
	$list2['money'][$type] += $v['cCertifiedMoney'] ;
	$list2['data'][$type][] = $v ;
	// if ($type == 'N') {
		// print_r($v) ; exit ;
	// }

	if ($type != 'N') {
		$list2['total'] ++ ;
		$list2['total2'] += $v['cCertifiedMoney'] ;
	}
	
	
	unset($v) ;
	$rs->MoveNext() ;
}

// print_r($list2) ; exit ;
##

//
$yearOption = '' ;
for ($i = (date("Y") - 1911) ; $i > 100 ; $i --) {
	$yearOption .= '<option value="'.$i.'"' ;
	if ($i == $yearDate) $yearOption .= ' selected="selected"' ;
	$yearOption .= '>'.$i."</option>\n" ;
}

$monthOption = array() ;
for ($i = 1 ; $i <= 12 ; $i ++) {
	$monthOption .= '<option value="'.str_pad($i,2,'0',STR_PAD_LEFT).'"' ;
	if (str_pad($i,2,'0',STR_PAD_LEFT) == $monthDate) $monthOption .= ' selected="selected"' ;
	$monthOption .= '>'.str_pad($i,2,'0',STR_PAD_LEFT)."</option>\n" ;
}
##

$smarty->assign('query',$query) ;
$smarty->assign('m',preg_replace("/^0/",'',$monthDate)) ;
$smarty->assign('yearOption',$yearOption) ;
$smarty->assign('monthOption',$monthOption) ;
$smarty->assign('list1', $list1) ;
$smarty->assign('list2', $list2) ;
$smarty->display('brandCalculate.inc.tpl', '', 'report2') ;
?>