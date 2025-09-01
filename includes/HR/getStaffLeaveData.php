<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

function getRevokeStatus(&$conn, $id)
{
    $sql = 'SELECT sStatus FROM tStaffLeaveApplyRevoke WHERE sLeaveApplyId = :sLeaveApplyId AND sStatus = "N";';

    $bind = [
        'sLeaveApplyId' => $id,
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

$fromDate = empty($_POST['fromDate']) ? date('Y-m-d', strtotime('-2 days')) : $_POST['fromDate'];
$toDate   = empty($_POST['toDate']) ? date('Y-m-d', strtotime('+ 1 month')) : $_POST['toDate'];
$staffId  = empty($_POST['staffId']) ? 0 : $_POST['staffId'];

/* Database parameter */
$aColumns = ['sId',
    'sApplicant',
    'applicantName',
    'sLeaveId',
    'leaveName',
    'sLeaveFromDateTime',
    'sLeaveToDateTime',
    'leaveDateTime',
    'sTotalHoursOfLeave',
    'sAgentApprovalDateTime',
    'sUnitApprovalDateTime',
    'sManagerApprovalDateTime',
    'sProcessing',
    'processing',
    'sStatus',
    'status',
    'sCreatedAt'];
$sIndexColumn = 'sId';

$sTable = '
    SELECT
        a.sId,
        a.sApplicant,
        (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) as applicantName,
        a.sLeaveId,
        (SELECT sLeaveName FROM tStaffLeaveType WHERE sId = a.sLeaveId) as leaveName,
        a.sLeaveFromDateTime,
        a.sLeaveToDateTime,
        CONCAT(SUBSTRING(a.sLeaveFromDateTime, 6, 11), " ~ ", SUBSTRING(a.sLeaveToDateTime, 6, 11)) as leaveDateTime,
        a.sTotalHoursOfLeave,
        a.sAgentApprovalDateTime,
        a.sUnitApprovalDateTime,
        a.sManagerApprovalDateTime,
        a.sProcessing,
        CASE
            WHEN a.sProcessing = "A" THEN "待代理人簽核"
            WHEN a.sProcessing = "U" THEN "待主管簽核"
            WHEN a.sProcessing = "M" THEN "待總經理簽核"
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
        tStaffLeaveApply AS a
    WHERE
        a.sStatus IN ("Y", "N")
        AND a.sLeaveFromDateTime <= "' . $toDate . ' 23:59:59"
        AND a.sLeaveToDateTime >= "' . $fromDate . ' 00:00:00"
';

if (! empty($staffId)) {
    $sTable .= ' AND a.sApplicant = ' . $staffId;
}

$sTable = ' (' . $sTable . ') AS tb ';

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

//取得所有假日
$_start = strtotime($year . '-' . $month . '-01 00:00:00');
$_end   = strtotime(date('Y-m-t 23:59:59', strtotime($year . '-' . $month . '-31 23:59:59')));

$sql      = 'SELECT hId, hName, hMakeUpWorkday, hFromDate, hToDate, hFromTime, hToTime, hFromTimestamp, hToTimestamp FROM tHoliday WHERE hMakeUpWorkday = "N" AND hFromTimestamp >= :start AND hToTimestamp <=:end;';
$holidays = $conn->all($sql, ['start' => $_start, 'end' => $_end]);

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
        if (! empty($aColumns[$i])) {
            $row[$aColumns[$i]] = $aRow[$aColumns[$i]];
        }

    }

    /* 是否可撤銷請假 */
    $revoke          = date("Y-m-d", strtotime($row['sLeaveFromDateTime'])); //2024-12-18 可撤銷時間延長至當天亦可撤銷
    $revoke_allow_ts = strtotime($revoke);                                   //2024-12-19 與家津討論後決定撤銷時間延長至請假開始後一天上班日仍可撤銷 2024-12-31 修正為前一個工作日仍可撤銷

    do {
        $revoke_allow_ts  = strtotime('+1 day', $revoke_allow_ts);
        $revoke_allow_day = date('Y-m-d', $revoke_allow_ts);
        $week             = date('w', $revoke_allow_ts);
    } while (in_array($week, [0, 6]) || isHoliday($revoke_allow_day, $holidays));

    $row['revoke'] = ($revoke_allow_day >= date('Y-m-d')) ? 'Y' : 'N';

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
