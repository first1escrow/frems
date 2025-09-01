<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;





if ($_POST['year'] == '' && $_POST['s_year'] =='') {
	$s_year = date('Y')-1911;
	$s_month = date('m');
	$search_date = date('Y')."-".$s_month."-01";

}elseif ($_POST['year'] == '') {
	$s_year = $_POST['s_year'];
	$s_month = $_POST['m_year'];
	$search_date = ($_POST['s_year']+1911)."-".$_POST['m_year']."-01";
}else{
	$s_month = $_POST['month'];
	$s_year = $_POST['year'];
	$search_date = ($_POST['year']+1911)."-".$_POST['month']."-01";
}



if ($_POST['ok']) {
	include_once 'caseEnd_recordSave.php';//
}

//預設帶今天月
// if (!$_POST['month']) { $s_month = $today_month;}
// if (!$_POST['year']) { $s_year = $today_year;}

$menu_y = array();
for ($i=105; $i <= (date('Y')-1911) ; $i++) { 
	$menu_y[$i] =$i;
}

for ($i=1; $i <=12 ; $i++) { 
	$menu_m[str_pad($i, '2','0',STR_PAD_LEFT)] =  str_pad($i, '2','0',STR_PAD_LEFT);
}

//經辦列表
$sql  = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(5,6) AND pBankTrans IN(1,2) AND pId!=6 AND pJob = 1 ORDER BY pId ASC ";
$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {
	$list_people[$i] = $rs->fields; //選項
	if ($i%2 == 0) {
		$list_people[$i]['class'] = 'tb';
	}else{
		$list_people[$i]['class'] = 'tb1';
	}
	
	$i++;
	$rs->MoveNext();
}
//錯誤資料
$sql = "SELECT * FROM tPeopleCaseEndError WHERE pDate = '".$search_date."'";
// echo $sql;
$rs = $conn->Execute($sql);
$total = $rs->RecordCount();

while (!$rs->EOF) {
	
	$data[$rs->fields['pMid']] = $rs->fields;

	$rs->MoveNext();
}

###
$smarty->assign('list_people',$list_people);
$smarty->assign('menu_y',$menu_y);
$smarty->assign('menu_m',$menu_m);
$smarty->assign('s_year',$s_year);
$smarty->assign('s_month',$s_month);
$smarty->assign('data',$data);
$smarty->assign('data_t',$total);
$smarty->display('caseEnd_record.inc.tpl', '', 'banktrans') ;
?>