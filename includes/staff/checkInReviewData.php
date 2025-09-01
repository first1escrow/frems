<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$year      = empty($_POST['year']) ? date('Y') : $_POST['year'];
$month     = empty($_POST['month']) ? date('m') : $_POST['month'];
$member_id = $_SESSION['member_id'];
// $member_id = 38;

/* Database parameter */
$aColumns = array('sId',
    'sStaffId',
    'staffName',
    'sApplyDate',
    'sApplyType',
    'applyType',
    'sReason',
    'sApproval',
    'approvalName',
    'sApprovalDateTime',
    'sStatus',
    'sCreatedAt');
$sIndexColumn = 'sId';

$sTable = '
(
    SELECT
        a.sId,
        a.sStaffId,
        (SELECT pName FROM tPeopleInfo WHERE pId = a.sStaffId) as staffName,
        a.sApplyDate,
        a.sApplyType,
        CASE
            WHEN a.sApplyType = "IN" THEN "簽到"
            WHEN a.sApplyType = "OUT" THEN "簽退"
            ELSE "未知"
        END as applyType,
        a.sReason,
        a.sApproval,
        (SELECT pName FROM tPeopleInfo WHERE pId = a.sApproval) as approvalName,
        a.sApprovalDateTime,
        a.sStatus,
        a.sCreatedAt
    FROM
        tStaffCheckInApply AS a
    WHERE
        a.sSupervisor = ' . $member_id . '
) tb  ';

/* injection */
// $_POST['sSearch'] = $_POST['sSearch'];

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

    $output['data'][] = $row;
}

exit(json_encode($output));