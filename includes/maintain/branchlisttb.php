<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';

$_POST = escapeStr($_POST);

$_zip      = $_REQUEST['sZip'];
$_brand    = $_REQUEST['sBrand'];
$_salesman = $_REQUEST['salesman'];
$_manager  = $_REQUEST['manager'];
$_city     = urldecode($_REQUEST['city']);

$str = '';

if ($_SESSION['member_branch'] == 1) { //是否可以看到停用店家
    $str .= " AND b.bStatus = '1'";
}

if ($_salesman) {
    $str .= ' AND a.bSales="' . $_salesman . '" ';
}

//特殊用
if ($_SESSION['member_pDep'] == 7 && ($_SESSION['member_test'] == 0)) {
    if ($_salesman != '' && ($_salesman != $_SESSION['member_id'])) {
        if (in_array($_SESSION['member_id'], [38, 72]) && in_array($_salesman, [38, 72])) {
            $str = ' AND a.bSales="' . $_salesman . '" ';
        } else {
            $str = ' AND a.bSales="-" '; //不給查
        }
    } else {
        $str = ' AND a.bSales="' . $_SESSION['member_id'] . '" ';
    }
}

if ($_SESSION['member_test'] != 0) {
    $str .= " AND ";

    $sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '" . $_SESSION['member_test'] . "'";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $test_tmp[] = "'" . $rs->fields['zZip'] . "'";
        $rs->MoveNext();
    }

    $str .= "b.bZip IN(" . implode(',', $test_tmp) . ")";
}

/* Database parameter */
$aColumns = array('bCode',
    'bBrand',
    'bStore',
    'bName',
    'bCooperationHas',
    'bServiceOrderHas',
    'bStatus',
    'bCategory',
    'bTelMain',
    'bFaxMain',
    'bManager',
    'bSerialnum',
    'bAccount4',
    'bAccount41',
    'bAccount42',
    'bAccount43',
    'bBankAccountName',
    'address',
);
$sIndexColumn = "bId";

$sTable = "
(
	SELECT
		b.bId,
		CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode,
		b.bStore,
		b.bZip,
		(Select bName From `tBrand` a Where a.bId = b.bBrand ) as bBrand,
		b.bName,
		b.bSerialnum as bIdentityNumber,
		case `bServiceOrderHas` when 1 then '有' Else '無' END as bServiceOrderHas,
		case `bStatus` when 1 then '啟用' when 3 then '暫停' else '停用' END as bStatus,
		case `bCategory` when 1 then '加盟' else '特許加盟店' END as bCategory,
        CONCAT(bFaxArea,b.bFaxMain) AS bFaxMain,
        CONCAT(bTelArea,b.bTelMain) AS bTelMain,
        b.bManager,
        b.bSerialnum,
        case `bCooperationHas` when 1 then '有' Else '無' END as bCooperationHas,
        b.bAccount4,
        b.bAccount41,
        b.bAccount42,
        b.bAccount43,
        bb.bBankAccountName,
        CONCAT(
            (SELECT zCity FROM tZipArea WHERE zZip = b.bZip),
            (SELECT zArea FROM tZipArea WHERE zZip = b.bZip),
            b.bAddress
        ) as address
    FROM
        `tBranch` AS b
    LEFT JOIN
    	tBranchSales AS a ON a.bBranch=b.bId
    LEFT JOIN
        tBranchBank AS bb ON bb.bBranch=b.bId
    WHERE
    	b.bId != 0 " . $str . " GROUP BY b.bId
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

if ($_brand) {
    if ($sWhere == '') {
        $sWhere .= "WHERE ";
    } else {
        $sWhere .= " AND ";
    }

    $sWhere .= "bStoreId=" . $_brand . ' ';
}

if ($_zip) {
    if ($sWhere == '') {
        $sWhere .= "WHERE ";
    } else {
        $sWhere .= " AND ";
    }

    $sWhere .= "bZip='" . $_zip . "' ";
} else if ($_city) {
    $sql = "SELECT * FROM tZipArea WHERE zCity = '" . $_city . "'";
    $rs  = $conn->Execute($sql);
    while (!$rs->EOF) {
        $tmp_zip[] = '"' . $rs->fields['zZip'] . '"';
        $rs->MoveNext();
    }

    if ($sWhere == '') {
        $sWhere .= "WHERE ";
    } else {
        $sWhere .= " AND ";
    }

    $sWhere .= "bZip IN (" . @implode(',', $tmp_zip) . ") ";
}

$sOrder = " Order by bId desc ";

/*
 * SQL queries
 * Get data to display
 */
//$sWhere = str_replace('bCode', "CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0'))", $sWhere);

$str = str_replace('a.bSales', 'b.bSales', $str);
$str = str_replace("AND b.bStatus = '1'", "AND b.bStatus = '啟用'", $str);

$str = (empty($sWhere)) ? "where b.bId != 0 " . $str : " AND b.bId != 0 " . $str;

$sQuery = "
SELECT SQL_CALC_FOUND_ROWS b.bId, b.bId, b.bCode, b.bStore, b.bStoreId, b.bZip, b.bBrand, b.bName, b.bIdentityNumber,
       b.bServiceOrderHas, b.bStatus, b.bCategory, b.bFaxMain, b.bTelMain, b.bManager, b.bSerialnum, b.bCooperationHas, b.bAccount4, b.bAccount41, b.bAccount42, b.bAccount43, b.bBankAccountName, address
FROM
(
        SELECT
                br.bId,
                CONCAT((Select bCode From `tBrand` c Where c.bId = br.bBrand ),LPAD(br.bId,5,'0')) as bCode,
                br.bStore,
                br.bBrand as bStoreId,
                br.bZip,
                (Select bName From `tBrand` a Where a.bId = br.bBrand ) as bBrand,
                br.bName,
                br.bSerialnum as bIdentityNumber,
                case `bServiceOrderHas` when 1 then '有' Else '無' END as bServiceOrderHas,
                case `bStatus` when 1 then '啟用' when 3 then '暫停' else '停用' END as bStatus,
                case `bCategory` when 1 then '加盟' else '特許加盟店' END as bCategory,
                CONCAT(bFaxArea,br.bFaxMain) AS bFaxMain,
                CONCAT(bTelArea,br.bTelMain) AS bTelMain,
                br.bManager,
                br.bSerialnum,
                case `bCooperationHas` when 1 then '有' Else '無' END as bCooperationHas,
                br.bAccount4,
                br.bAccount41,
                br.bAccount42,
                br.bAccount43,
                bb.bBankAccountName,
                a.bSales,
                CONCAT(
                    (SELECT zCity FROM tZipArea WHERE zZip = br.bZip),
                    (SELECT zArea FROM tZipArea WHERE zZip = br.bZip),
                    br.bAddress
                ) as address
            FROM
                `tBranch` AS br
            LEFT JOIN
                tBranchSales AS a ON a.bBranch=br.bId
            LEFT JOIN
                tBranchBank AS bb ON bb.bBranch=br.bId
            WHERE 1=1
) AS b "
    . $sWhere . $str . " GROUP BY b.bId" . $sOrder . $sLimit;

$rResult = $conn->Execute($sQuery);

/* Data set length after filtering */
$sQuery = "
        SELECT FOUND_ROWS() AS FOUND_ROWS
    ";
$rResultFilterTotal = $conn->Execute($sQuery);

$iFilteredTotal = $rResultFilterTotal->fields['FOUND_ROWS'];

/* Total data set length */
$sQuery = "
        SELECT COUNT(" . $sIndexColumn . ") AS totalCount
        FROM   $sTable
    ";
$rResultTotal = $conn->Execute($sQuery);

$iTotal = $rResultTotal->fields['totalCount'];

/* Output */
$output = array(
    "sEcho"                => intval($_POST['sEcho']),
    "iTotalRecords"        => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData"               => array(),
);

while (!$rResult->EOF) {
    $aRow               = $rResult->fields;
    $row                = array();
    $row['DT_RowId']    = 'row_' . $aRow['bId'];
    $row['DT_RowClass'] = 'grade' . $aRow['grade'];

    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }

        if ($aColumns[$i] == 'bStatus') {
            if ($aRow[$aColumns[$i]] == '停用') {
                $row['DT_RowClass'] = 'close';
            } elseif ($aRow[$aColumns[$i]] == '暫停') {
                $row['DT_RowClass'] = 'close';
            }
        }
    }

    $output['aaData'][] = $row;

    $rResult->MoveNext();
}

echo json_encode($output);
