<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

/* Database parameter */
$aColumns = [
    'pId',
    'pName',
    'pDep',
    'pDepName',
    'pGender',
    'pOnBoard',
    'pSerialNo',
    'pRegisterZip',
    'pRegisterAddress',
    'registerCity',
    'registerArea',
    'pMailingZip',
    'pMailingAddress',
    'mailingCity',
    'mailingArea',
    'pBirthday',
];
$sIndexColumn = 'pId';

$sTable = '
(
    SELECT
        a.pId,
        a.pName,
        a.pDep,
        (SELECT dDep FROM tDepartment WHERE dId = a.pDep) AS pDepName,
        CASE WHEN a.pGender = "M" THEN "男" ELSE "女" END AS pGender,
        a.pOnBoard,
        b.pSerialNo,
        b.pRegisterZip,
        b.pRegisterAddress,
        (SELECT zCity FROM tZipArea WHERE zZip = b.pRegisterZip) AS registerCity,
        (SELECT zArea FROM tZipArea WHERE zZip = b.pRegisterZip) AS registerArea,
        b.pMailingZip,
        b.pMailingAddress,
        (SELECT zCity FROM tZipArea WHERE zZip = b.pMailingZip) AS mailingCity,
        (SELECT zArea FROM tZipArea WHERE zZip = b.pMailingZip) AS mailingArea,
        b.pBirthday
    FROM
        tPeopleInfo AS a
    JOIN
        tPeopleInfoDetail AS b ON a.pId = b.pStaffId
    WHERE
        a.pJob = 1
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
    SELECT SQL_CALC_FOUND_ROWS " . $sIndexColumn . ", " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
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

    $onBoard = $aRow['pOnBoard'];
    if (!empty($onBoard)) {
        $onBoard = new DateTime($onBoard);
        $onBoard = ($onBoard->format('Y') - 1911) . $onBoard->format('/m/d');
    }
    $row['onBoard'] = $onBoard;

    $row['registerAddress'] = $aRow['registerCity'] . $aRow['registerArea'] . $aRow['pRegisterAddress'];
    $row['mailingAddress']  = $aRow['mailingCity'] . $aRow['mailingArea'] . $aRow['pMailingAddress'];

    $output['data'][] = $row;
}

exit(json_encode($output));