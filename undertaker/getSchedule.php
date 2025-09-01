<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$sql = '
	SELECT
		uId,
		uDateTime,
		uDateTime2,
		(SELECT pName FROM tPeopleInfo WHERE pId=uStaff) as Staff,
		(SELECT pName FROM tPeopleInfo WHERE pId=uSubstituteStaff) as SubstituteStaff,
		(SELECT pName FROM tPeopleInfo WHERE pId=uCreator) as Creator,
		(SELECT pName FROM tPeopleInfo WHERE pId=uEditor) as uEditor
	FROM
		tUndertakerCalendar
	WHERE
		uDel = 0
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

$eventsArr = array();
$i         = 0;
foreach ($list as $k => $v) {
    $eventsArr[$i]['id']        = $v['uId'];
    $eventsArr[$i]['title']     = '經辦:' . $v['SubstituteStaff'] . '，代理人:' . $v['Staff'];
    $eventsArr[$i]['start']     = str_replace(" ", "T", trim($v['uDateTime']));
    $eventsArr[$i]['end']       = str_replace(" ", "T", trim($v['uDateTime2']));
    $eventsArr[$i]['className'] = 'user';

    $i++;
}

echo json_encode($eventsArr);