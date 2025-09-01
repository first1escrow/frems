<?php
header('Content-Type: application/json');

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$staffId  = $_SESSION['member_id'];
$staffDep = $_SESSION['member_pDep'];

$from_date = preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $_GET['start']) ? $_GET['start'] . ' 00:00:00' : date("Y-m-d 00:00:00", strtotime("-2 years"));
$to_date   = preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $_GET['end']) ? $_GET['end'] . ' 23:59:59' : date("Y-m-d 23:59:59");

$sql_ext = ' AND cCreator="' . $staffId . '" ';
if (in_array($staffId, [2, 3, 6, 13, 129])) {
    $sql_ext = '';
}

$sql = 'SELECT
            *,
            (SELECT pName FROM tPeopleInfo WHERE pId=a.cCreator) as pName,
            (SELECT cName FROM tCalendarClass WHERE cId=a.cClass) as visit
        FROM
            tCalendar AS a
        WHERE
            cStartDateTime >= "' . $from_date . '" AND cStartDateTime <= "' . $to_date . '"
            ' . $sql_ext . '
            AND cErease = "1"
        ORDER BY
            cStartDateTime
        ASC;';
$rs = $conn->Execute($sql);

$list = [];
while (! $rs->EOF) {
    $list[] = $rs->fields;
    $rs->MoveNext();
}

$eventsArr = [];
$i         = 0;
foreach ($list as $k => $v) {
    $eventsArr[$i]['id'] = $v['cId'];

    $eventsArr[$i]['title'] = $v['pName'] . ' ' . $v['visit'];

    if ($v['cClass'] == '1') {
        $eventsArr[$i]['title'] .= '(' . $v['cStore'] . ')';
    }

    if ($v['cClass'] == '2') {
        $eventsArr[$i]['title'] .= '(' . $v['cScrivener'] . ')';
    }

    $eventsArr[$i]['start'] = str_replace(" ", "T", trim($v['cStartDateTime']));
    $eventsArr[$i]['end']   = str_replace(" ", "T", trim($v['cEndDateTime']));

    $color = empty($v['cCreator']) ? '' : 'user' . $v['cCreator'];

    if (! empty($color)) {
        $eventsArr[$i]['className'] = $color;
    }

    $i++;
}

//取得假勤系統資料
$sql = 'SELECT l.sId, l.sLeaveFromDateTime, l.sLeaveToDateTime, l.sLeaveId, p.pName FROM tStaffLeaveApply AS l
            LEFT JOIN tPeopleInfo AS p ON l.sApplicant=p.pId
                WHERE p.pDep = "7" AND l.sLeaveToDateTime >= "' . $from_date . '" AND l.sLeaveToDateTime <= "' . $to_date . '"';
$sql .= (! in_array($staffId, [2, 3, 6, 13, 129])) ? ' AND l.sApplicant = "' . $staffId . '" ' : '';
$rs = $conn->Execute($sql);
while (! $rs->EOF) {
    $eventsArr[$i]['id']    = 'l' . $rs->fields['sId'];
    $eventsArr[$i]['title'] = $rs->fields['pName'];
    if ($rs->fields['sLeaveId'] == 9) {
        $eventsArr[$i]['title'] .= ' 公差';
    } else {
        $eventsArr[$i]['title'] .= ($rs->fields['sLeaveId'] != 20) ? ' 休假' : '';
    }
    $eventsArr[$i]['start']     = str_replace(" ", "T", trim($rs->fields['sLeaveFromDateTime']));
    $eventsArr[$i]['end']       = str_replace(" ", "T", trim($rs->fields['sLeaveToDateTime']));
    $eventsArr[$i]['className'] = 'leave';

    $i++;
    $rs->MoveNext();
}

exit(json_encode($eventsArr));