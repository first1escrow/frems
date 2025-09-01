<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

function getRevokeStatus(&$conn, $id)
{
    $sql = 'SELECT sStatus FROM tStaffOvertimeApplyRevoke WHERE sOvertimeApplyId = :sOvertimeApplyId AND sStatus = "N";';

    $bind = [
        'sOvertimeApplyId' => $id,
    ];

    $rs = $conn->one($sql, $bind);
    return empty($rs) ? false : $rs['sStatus'];
}

function isHoliday($date, &$holidays)
{
    foreach ($holidays as $holiday) {
        if ($date == $holiday['hFromDate']) {
            return true;
        }
    }

    return false;
}

$year      = empty($_POST['year']) ? date('Y') : $_POST['year'];
$month     = empty($_POST['month']) ? date('m') : $_POST['month'];
$member_id = $_SESSION['member_id'];

/* Database parameter */
$aColumns = array('sId',
    'sApplicant',
    'applicantName',
    'sOvertimeFromDateTime',
    'sOvertimeToDateTime',
    'overtimeDateTime',
    'reason',
    'processing',
    'status',
    'sStatus',
    'sCreatedAt');
$sIndexColumn = 'sId';

$sTable = '
(
    SELECT
        a.sId,
        a.sApplicant,
        (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) as applicantName,
        a.sOvertimeFromDateTime,
        a.sOvertimeToDateTime,
        CONCAT(SUBSTRING(a.sOvertimeFromDateTime, 6, 11), " ", SUBSTRING(a.sOvertimeToDateTime, 6, 11)) as overtimeDateTime,
        a.sApplyReason as reason,
        a.sUnitApprovalDateTime,
        a.sProcessing,
        CASE
            WHEN a.sProcessing = "U" THEN "待主管簽核"
            WHEN a.sProcessing = "F" THEN "已完成"
            ELSE ""
        END as processing,
        a.sStatus,
        CASE
            WHEN a.sStatus = "Y" THEN "已簽核"
            WHEN a.sStatus = "N" THEN "未簽核"
            WHEN a.sStatus = "D" THEN "已駁回"
            WHEN a.sStatus = "R" THEN "已撤銷"
            WHEN a.sStatus = "C" THEN "已取消"
            ELSE ""
        END as status,
        a.sCreatedAt
    FROM
        tStaffOvertimeApply AS a
    WHERE
        a.sApplicant = ' . $member_id . ' AND (a.sOvertimeFromDateTime >= "' . date("Y-m-d H:i:s", strtotime('-30 day')) . '" OR a.sStatus = "N")
) tb  ';

/* Paging */
$sLimit = "";
if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . $_POST['iDisplayStart'] . ", " .
        $_POST['iDisplayLength'];
}

/* Ordering */
$sOrder = '';
if (isset($_POST['order']) && count($_POST['order'])) {
    $orderBy = [];
    for ($i = 0; $i < count($_POST['order']); $i++) {
        if ($_POST['columns'][$_POST['order'][$i]['column']]['orderable'] == "true") {
            $dir       = $_POST['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC';
            $orderBy[] = $aColumns[$_POST['order'][$i]['column']] . " " . $dir;
        }
    }

    if (count($orderBy)) {
        $sOrder = 'ORDER BY ' . implode(', ', $orderBy);
    }
}

/* Filtering */
$sWhere = "";
if (isset($_POST['sSearch']) && $_POST['sSearch'] != "") {
    $sWhere = "WHERE (";
    for ($i = 0; $i < count($aColumns); $i++) {
        $sWhere .= $aColumns[$i] . " LIKE '%" . $_POST['sSearch'] . "%' OR ";
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}

/* Individual column filtering */
for ($i = 0; $i < count($aColumns); $i++) {
    if (isset($_POST['bSearchable_' . $i]) && $_POST['bSearchable_' . $i] == "true" && $_POST['sSearch_' . $i] != '') {
        if ($sWhere == "") {
            $sWhere = "WHERE  ";
        } else {
            $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i] . " LIKE '%" . $_POST['sSearch_' . $i] . "%' ";
    }
}

/*
 * SQL queries
 * Get data to display
 */
$conn = new first1DB;

$sQuery = "
    SELECT SQL_CALC_FOUND_ROWS sId, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
    FROM   $sTable
    $sWhere
    $sOrder
    $sLimit
  ";
$rResult = $conn->all($sQuery);

/* Data set length after filtering */
$iFilteredTotal = $conn->found_rows();

/* Total data set length */
$sQuery = "
    SELECT COUNT(" . $sIndexColumn . ") as total
    FROM   $sTable
  ";
$iTotal = $conn->one($sQuery)['total'];

/* Output */
$output = [
    'recordsTotal'    => intval($iTotal),
    'recordsFiltered' => intval($iFilteredTotal),
    'data'            => [],
];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        if (!empty($aColumns[$i])) {
            $row[$aColumns[$i]] = $aRow[$aColumns[$i]];
        }
    }

    $row['overtimeDateTime'] = date('m/d H:i', strtotime($aRow['sOvertimeFromDateTime'])) . '(起)<br>';
    $row['overtimeDateTime'] .= date('m/d H:i', strtotime($aRow['sOvertimeToDateTime'])) . '(迄)';

    //是否可撤銷請假
    $today           = date("Y-m-d");
    $today_timestamp = strtotime($today);
    $revoke_allow_ts = $today_timestamp;

    do {
        $revoke_allow_ts  = strtotime('-1 day', $revoke_allow_ts);
        $revoke_allow_day = date('Y-m-d', $revoke_allow_ts);
        $week             = date('w', $revoke_allow_ts);
    } while (in_array($week, [0, 6]) || isHoliday($revoke_allow_day, $holidays));

    $revoke        = date("Y-m-d", strtotime($row['sOvertimeFromDateTime'])); //2024-12-18 可撤銷時間延長至當天亦可撤銷
    $row['revoke'] = ($revoke_allow_day <= $revoke) ? 'Y' : 'N'; //2024-12-19 與家津討論後決定撤銷時間延長至請假開始後一天上班日仍可撤銷 2024-12-31 修正為前一個工作日仍可撤銷

    $revoke_status = getRevokeStatus($conn, $row['sId']);
    if ($revoke_status === 'Y') {
        $row['revoke'] = '已撤銷';
    }

    if ($revoke_status === 'N') {
        $row['revoke'] = '撤銷申請中';
    }

    $output['data'][] = $row;
}

exit(json_encode($output));