<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';

/* injection */
$_POST = escapeStr($_POST);
$_GET  = escapeStr($_GET);

$_POST['sSearch'] = $_POST['sSearch'];
$year             = ($_GET['year']) ? $_GET['year'] : date('Y');

$str = " AND  ((bDate BETWEEN '" . $year . "-01-01' AND '" . $year . "-12-31') OR bDate ='0000-00-00' )";
if ($_SESSION['pBankBook'] == 2) {
    $str .= " AND (bStatus = 1 OR bStatus = 2)";
    $str .= "AND IF(bCategory != '1',bBookId != '',bBookId != '' OR bBookId = '')";
}

$today = date('Y-m-d');
$day7  = date('Y-m-d', strtotime("-7days"));

$str = " AND IF( bDate='0000-00-00', bCreatTime BETWEEN '" . $day7 . " 00:00:00' AND '" . $today . " 23:59:59', bCreatTime != '') " . $str;

/* Database parameter */
$aColumns = array('bDate',
    'cBankName',
    'bBookId',
    'CategoryName',
    'bMoney',
    'bStatusName',
    'bCreatName',
    'bModifyName2',
    'bModifyName3',
    'bBank2',
    'bBank',
    'bCategory',
    'cBranchName',
    'bStatus',
);
$sIndexColumn = "bId";

$sTable = "
(
    SELECT
        *,
        bBank AS bBank2,
        (SELECT cBankName FROM tContractBank WHERE cId=bBank) AS cBankName,
        (SELECT cBranchName FROM tContractBank WHERE cId=bBank) AS cBranchName,
        (SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName,
        case bStatus when 0 then '待確認' when 1 then '待審核' when 2 then '已審核'  else '未知' END as bStatusName

FROM
    `tBankTrankBook`
WHERE bDel = 0  " . $str . "
) tb
";

/* Paging */
$sLimit = "";
if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . $_POST['iDisplayStart'] . ", " .
        $_POST['iDisplayLength'];
}

/* Ordering */
$sOrder = "  ";
if (isset($_POST['iSortCol_0'])) {
    $sOrder = "ORDER BY  ";
    for ($i = 0; $i < intval($_POST['iSortingCols']); $i++) {
        if ($_POST['bSortable_' . intval($_POST['iSortCol_' . $i])] == "true") {
            $sOrder .= $aColumns[intval($_POST['iSortCol_' . $i])] . "
                    " . $_POST['sSortDir_' . $i] . ", ";
        }
    }
    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == "ORDER BY") {
        $sOrder = "";
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
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }

        $sWhere .= $aColumns[$i] . " LIKE '%" . $_POST['sSearch_' . $i] . "%' ";
    }
}

$sOrder = " Order by bStatus ASC,bCreatTime desc,bDate DESC";

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
        SELECT SQL_CALC_FOUND_ROWS bId, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
    ";

// $rResult = mysqli_query($link,$sQuery) or die(mysqli_error());
$rResult = $conn->Execute($sQuery);
//echo $sQuery ; exit ;
/* Data set length after filtering */
$sQuery = "
        SELECT FOUND_ROWS() AS FOUND_ROWS
    ";
// $rResultFilterTotal = mysqli_query($link,$sQuery) or die(mysqli_error());
$rResultFilterTotal = $conn->Execute($sQuery);

$iFilteredTotal = $rResultFilterTotal->fields['FOUND_ROWS'];

/* Total data set length */
$sQuery = "
        SELECT COUNT(" . $sIndexColumn . ") AS totalCount
        FROM   $sTable
    ";
// $rResultTotal = mysqli_query($link,$sQuery) or die(mysqli_error());
$rResultTotal = $conn->Execute($sQuery);
$iTotal       = $rResultTotal->fields['totalCount'];

/* Output */
$output = array(
    "sEcho"                => intval($_POST['sEcho']),
    "iTotalRecords"        => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData"               => array(),
);

while (!$rResult->EOF) {
    $aRow           = $rResult->fields;
    $aRow['bMoney'] = number_format($aRow['bMoney']);

    $row = array();

    // Add the row ID and class to the object
    $row['DT_RowId']    = 'row_' . $aRow['bId'];
    $row['DT_RowClass'] = 'grade' . $aRow['grade'];
    $row['pdf']         = '';

    for ($i = 0; $i < count($aColumns); $i++) {
        $bank = 0;
        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
        } else if ($aColumns[$i] != ' ') {
            /* General output */

            if ($aColumns[$i] == 'cBankName') {
                // echo $aRow[$aColumns[$i]];
                $aRow[$aColumns[$i]] .= $aRow['cBranchName'];
            }

            if ($aColumns[$i] == 'bBank2') {
                $link = '<a href="javascript:void(0)" onclick="bModify(' . $aRow['bId'] . ')">編輯</a>';
                $file = '';

                if ($aRow[$aColumns[$i]] == 4 || $aRow[$aColumns[$i]] == 6) {
                    if ($aRow['bCategory'] == 1) {
                        $file = "sinopac01_pdf.php";
                    } elseif ($aRow['bCategory'] == 2) {
                        $file = 'sinopac02_pdf.php';
                    } elseif ($aRow['bCategory'] == 3 || $aRow['bCategory'] == 4 || $aRow['bCategory'] == 5) {
                        $file = 'sinopac03_pdf.php';
                    } elseif ($aRow['bCategory'] == 6) {
                        $file = 'sinopac05_pdf.php';
                    } elseif ($aRow['bCategory'] == 7 || $aRow['bCategory'] == 8 || $aRow['bCategory'] == 9) {
                        $file = 'sinopac04_pdf.php';
                    }
                } elseif ($aRow[$aColumns[$i]] == 1 || $aRow[$aColumns[$i]] == 7) {
                    if ($aRow['bCategory'] == 1) {
                        $file = 'firstInform2.php';
                    } elseif ($aRow['bCategory'] == 6) {
                        $file = 'firstInform3.php';
                    } elseif ($aRow['bCategory'] == 3 || $aRow['bCategory'] == 4 || $aRow['bCategory'] == 5) {
                        $file = 'firstInform1.php';
                    } elseif ($aRow['bCategory'] == 7 || $aRow['bCategory'] == 8) {
                        $file = 'firstInform4.php';
                    } elseif ($aRow['bCategory'] == 11) {
                        $file = 'firstInform11.php';
                    } elseif ($aRow['bCategory'] == 12) {
                        $file = 'firstInform12.php';
                    } elseif ($aRow['bCategory'] == 13) {
                        $file = 'firstInform13.php';
                    } elseif ($aRow['bCategory'] == 14) {
                        $file = 'firstInform14.php';
                    }
                } elseif ($aRow[$aColumns[$i]] == 5) {
                    if ($aRow['bCategory'] == 1) {
                        $file = 'taishin01_pdf.php';
                    } elseif ($aRow['bCategory'] == 3 || $aRow['bCategory'] == 4 || $aRow['bCategory'] == 5) {
                        $file = 'taishin03_pdf.php';
                    } elseif ($aRow['bCategory'] == 6) {
                        $file = 'taishin06_pdf.php';
                    } elseif ($aRow['bCategory'] == 7 || $aRow['bCategory'] == 8) {
                        $file = 'taishin07_pdf.php';
                    } elseif ($aRow['bCategory'] == 10) {
                        $file = 'taishin10_pdf.php';
                    } elseif ($aRow['bCategory'] == 11) {
                        $file = 'taishin11_pdf.php';
                    } elseif ($aRow['bCategory'] == 12) {
                        $file = 'taishin12_pdf.php';
                    }
                }

                if (!empty($file)) {
                    if ($_SESSION['pBankBook'] == 2 || $_SESSION['pBankBook'] == 3) {
                        $link .= "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"Audit(" . $aRow['bId'] . ",'" . $file . "')\">審核預覽</a>";
                    }
                    $aRow['bBank2'] = $link . "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(" . $aRow['bId'] . ",'" . $file . "')\">預覽</a>";

                } else {
                    $aRow['bBank2'] = '異常請刪除後再試';
                }
                $aRow['bBank2'] .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0)" onclick="bDel(' . $aRow['bId'] . ')">刪除</a>';

            }

            $row[] = $aRow[$aColumns[$i]];
        }

        if ($aColumns[$i] == 'bStatusName') {
            if ($aRow[$aColumns[$i]] == '已審核') {
                $row['DT_RowClass'] = 'close';
            }
        }

    }

    $output['aaData'][] = $row;

    $rResult->MoveNext();
}

$spData = array();
$data   = array();
foreach ($output['aaData'] as $key => $value) {

    if ($value[0] == '0000-00-00') {
        array_push($spData, $value);
    } else {
        array_push($data, $value);
    }
}

// echo "<pre>";
// print_r($spData);

//     die;

unset($output['aaData']);

$output['aaData'] = array_merge($spData, $data);

echo json_encode($output);
