<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$today = date('Y-m-d');
$year  = ($_POST['year']) ? $_POST['year'] : (date('Y') - 1911);
$month = ($_POST['month']) ? $_POST['month'] : (int) date('m');

$day1 = ($year + 1911) . "-" . $month . "-01 00:00:00";
$day2 = ($year + 1911) . "-" . $month . "-31 23:59:59";

$sql = '
	SELECT
		uId,
		uDateTime,
		uDateTime2,
		uNote,
		(SELECT pName FROM tPeopleInfo WHERE pId=uStaff) as Staff,
		(SELECT pName FROM tPeopleInfo WHERE pId=uSubstituteStaff) as SubstituteStaff,
		(SELECT pName FROM tPeopleInfo WHERE pId=uCreator) as Creator,
		(SELECT pName FROM tPeopleInfo WHERE pId=uEditor) as uEditor
	FROM
		tUndertakerCalendar
	WHERE
		uDel = 0 AND uDateTime >= "' . $day1 . '" AND uDateTime2 <="' . $day2 . '"
	ORDER BY
		uDateTime
	ASC
;';
$rs = $conn->Execute($sql);

$list = array();
while (!$rs->EOF) {
    $list[] = $rs->fields;
    $rs->MoveNext();
}

for ($i = 108; $i <= (date('Y') - 1910); $i++) {
    $menuYear[$i] = $i;
}

for ($i = 1; $i <= 12; $i++) {
    $menuMonth[$i] = $i;
}
// print_r($list);
##
$smarty->assign('menuYear', $menuYear);
$smarty->assign('menuMonth', $menuMonth);
$smarty->assign('today', $today);
$smarty->assign('month', $month);
$smarty->assign('year', $year);
$smarty->assign('list', $list);
$smarty->display('undertakerCalendar.inc.tpl', '', 'undertaker');