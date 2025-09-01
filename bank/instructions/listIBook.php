<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';

/* injection */
$_POST            = escapeStr($_POST);
$_POST['sSearch'] = $_POST['sSearch'];

if ($_SESSION['pBankBook'] == 0) {
    $str .= " AND bCreatorId ='" . $_SESSION['member_id'] . "'";
}

if ($_SESSION['pBankBook'] == 2) {
    $str = " AND (bStatus = 1 OR bStatus = 2)";
}

/* Database parameter */
$aColumns = array('bDate',
    'cBankName',
    'bBookId',
    'CategoryName',
    'bMoney',
    'bStatusName',
    'bCreatName',
    'bBank2',
    'bBank',
    'bCategory',
    'cBranchName',
);
$sIndexColumn = "bId";

//1一般2虛轉虛3開票4繳稅5臨櫃6補通訊7退票
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
WHERE bDel = 0	AND (bCategory = 6 OR bCategory =7 OR bCategory =8 OR bCategory =9 OR bCategory =0 OR bCategory =11 OR bCategory =12) " . $str . "
) tb
";

/* Paging */
$sLimit = "";
if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . $_POST['iDisplayStart'] . ", " . $_POST['iDisplayLength'];
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

$sOrder = " Order by bId desc,bDate ASC ";

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
$rResult = $conn->Execute($sQuery);

/* Data set length after filtering */
$sQuery             = "SELECT FOUND_ROWS() AS FOUND_ROWS";
$rResultFilterTotal = $conn->Execute($sQuery);
$iFilteredTotal     = $rResultFilterTotal->fields['FOUND_ROWS'];

/* Total data set length */
$sQuery       = "SELECT COUNT(" . $sIndexColumn . ") AS totalCount FROM $sTable";
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
    $aRow = $rResult->fields;

    $row = array();

    // Add the row ID and class to the object
    $row['DT_RowId'] = 'row_' . $aRow['bId'];
    $row['pdf']      = '';

    for ($i = 0; $i < count($aColumns); $i++) {
        $bank = 0;
        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            if ($aColumns[$i] == 'cBankName') {
                $aRow[$aColumns[$i]] .= $aRow['cBranchName'];
            }

            if ($aColumns[$i] == 'bBank2') {
                $link = '<a href="javascript:void(0)" onclick="bModify(' . $aRow['bId'] . ')">編輯</a>';

                if (in_array($aRow[$aColumns[$i]], [4, 6])) { //永豐
                    $pdf = '';

                    if ($aRow['bCategory'] == 1) {
                        $pdf = 'sinopac01_pdf.php';
                    } elseif ($aRow['bCategory'] == 2) {
                        $pdf = 'sinopac02_pdf.php';
                    } elseif ($aRow['bCategory'] == 3 || $aRow['bCategory'] == 4 || $aRow['bCategory'] == 5) {
                        $pdf = 'sinopac03_pdf.php';
                    } elseif ($aRow['bCategory'] == 6) {
                        $pdf = 'sinopac05_pdf.php';
                    } elseif ($aRow['bCategory'] == 7 || $aRow['bCategory'] == 8 || $aRow['bCategory'] == 9) {
                        $pdf = 'sinopac04_pdf.php';
                    }

                    if ($pdf == '') {
                        $aRow['bBank2'] = '<a href="javascript:void(0)" onclick="bDel(' . $aRow['bId'] . ')">刪除</a>';
                    } else {
                        $aRow['bBank2'] = $link . "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(" . $aRow['bId'] . ",'" . $pdf . "')\">預覽</a>";
                    }

                } elseif (in_array($aRow[$aColumns[$i]], [1, 7])) { //一銀
                    $pdf = '';

                    if ($aRow['bCategory'] == 1) {
                        $pdf = 'firstInform2.php';
                    } elseif ($aRow['bCategory'] == 2) {
                        $pdf = 'sinopac02_pdf.php';
                    } elseif (in_array($aRow['bCategory'], [3, 4, 5])) {
                        $pdf = 'firstInform1.php';
                    } elseif ($aRow['bCategory'] == 6) {
                        $pdf = 'firstInform3.php';
                    } elseif (in_array($aRow['bCategory'], [7, 8])) {
                        $pdf = 'firstInform4.php';
                    } elseif ($aRow['bCategory'] == 11) {
                        $pdf = 'firstInform11.php';
                    } elseif ($aRow['bCategory'] == 12) {
                        $pdf = 'firstInform12.php';
                    }

                    if ($pdf == '') {
                        $aRow['bBank2'] = '<a href="javascript:void(0)" onclick="bDel(' . $aRow['bId'] . ')">刪除</a>';
                    } else {
                        $aRow['bBank2'] = $link . "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(" . $aRow['bId'] . ",'" . $pdf . "')\">預覽</a>";
                    }
                } elseif ($aRow[$aColumns[$i]] == 5) { //台新
                    $pdf = '';

                    if ($aRow['bCategory'] == 1) {
                        $pdf = 'taishin01_pdf.php';
                    } elseif ($aRow['bCategory'] == 2) {
                        $pdf = 'sinopac02_pdf.php';
                    } elseif (in_array($aRow['bCategory'], [3, 4])) {
                        $pdf = 'taishin03_pdf.php';
                    } elseif ($aRow['bCategory'] == 6) {
                        $pdf = 'taishin06_pdf.php';
                    } elseif (in_array($aRow['bCategory'], [7, 8])) {
                        $pdf = 'taishin07_pdf.php';
                    } elseif ($aRow['bCategory'] == 11) {
                        $pdf = 'taishin11_pdf.php';
                    } elseif ($aRow['bCategory'] == 12) {
                        $pdf = 'taishin12_pdf.php';
                    }

                    if ($pdf == '') {
                        $aRow['bBank2'] = '<a href="javascript:void(0)" onclick="bDel(' . $aRow['bId'] . ')">刪除</a>';
                    } else {
                        $aRow['bBank2'] = $link . "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(" . $aRow['bId'] . ",'" . $pdf . "')\">預覽</a>";
                    }
                } else {
                    $aRow['bBank2'] = '<a href="javascript:void(0)" onclick="bDel(' . $aRow['bId'] . ')">刪除</a>';
                }
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

echo json_encode($output);
