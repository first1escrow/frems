<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST      = escapeStr($_POST) ;

$exports    = $_POST['exp'] ;
$date_start = $_POST['date_start'];
$date_end   = $_POST['date_end'];
$People     = $_POST['people'];
$page       = ($_POST['page'])?$_POST['page']:1;

$row     = 31;
$limit_s = ($page - 1) * $row;
$limit_e = $limit_s + $row; //結束筆數

if ($People) {
	$str2 = " AND rPId = '".$People."'";
} else {
    $str2 = '';
}
##

$sql  = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(5,6) AND pBankTrans IN(1,2) AND pId!=6 AND pJob = 1  ORDER BY pId ASC ";
$rs = $conn->Execute($sql);
$menuPeople[0] = '全部';
while (!$rs->EOF) {
	if ($People > 0) {
		if ($People == $rs->fields['pId']) {
			$list_People[$rs->fields['pId']]['name'] = $rs->fields['pName']; //選項
		}
	} else {
		$list_People[$rs->fields['pId']]['name'] = $rs->fields['pName']; //選項
	}
	
	// $data_People[]=$rs->fields['pId']; //被選取的
	$menuPeople[$rs->fields['pId']] = $rs->fields['pName']; //選項

	$rs->MoveNext();
}

if ($_POST) {
	$tmp         = explode('-', $date_start);
	$sdate_start = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];

	$tmp         = explode('-', $date_end);
	$sdate_end   = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];

    $tmp = null;
	unset($tmp);

	$days = round((strtotime($sdate_end) - strtotime($sdate_start)) / 3600 / 24) + 1;	//會少一天要+1

	$sql = "SELECT rPId,rDate,rCaseCount,(SELECT pName FROM tPeopleInfo WHERE pId = rPId) AS uName  FROM tReportUndertakerCase WHERE rDate >= '".$sdate_start."' AND rDate <= '".$sdate_end."' ".$str2." ORDER BY rDate,rPId ASC ";
	// echo $sql;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		if (array_key_exists($rs->fields['rPId'],$list_People)) {
			$list_People[$rs->fields['rPId']]['count'] += $rs->fields['rCaseCount'];
			$data[] = $rs->fields;
		}
		
		$rs->MoveNext();
	}

	foreach ($list_People as $key => $value) {
		$list_People[$key]['avgcount'] = round($value['count']/$days);//查詢範圍平均最高件數
	}
}

##
$total = count($data); //總數
// echo $total;
if ($total<=$limit_e) { //如果總筆數小於顯示數
	$limit_e = $total;
}
for ($i=$limit_s; $i <$limit_e; $i++) { 
	$data_list[] = $data[$i];
}
$total_page= ceil($total/$row);//總頁數
##頁數下拉
$page_option = '';
for ($i=1; $i <=$total_page ; $i++) { 

	if ($page==$i) {
		$page_option .= '<option value="'.$i.'" selected>'.$i.'</option>';
	}else
	{
		$page_option .= '<option value="'.$i.'">'.$i.'</option>';
	}
}
if ($limit_s==0) {
	$limit_s=1;
}

	
##
##
$smarty->assign('total',$total);
$smarty->assign('limit_e',$limit_e);
$smarty->assign('limit_s',$limit_s);
$smarty->assign('total_page',$total_page);
$smarty->assign('page_option',$page_option);
$smarty->assign('data_list',$data_list);
$smarty->assign('days',$days);
$smarty->assign('date_start',$date_start); //預設時間(今天)
$smarty->assign('date_end',$date_end); //預設時間(今天)
$smarty->assign('list_People',$list_People); //數量
$smarty->assign('menuPeople',$menuPeople);
$smarty->assign('people',$People);

$smarty->display('caseProcessingCount2.inc.tpl', '', 'banktrans');
?>