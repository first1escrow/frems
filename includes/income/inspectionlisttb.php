<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/opendb2.php';

/* Database connection information */
$_REQUEST = escapeStr($_REQUEST);

$_f = $_REQUEST['f'];
$_t = $_REQUEST['t'];

$usrId = $_SESSION['member_id'];

$income_sql = " (scr.sUndertaker1 = '" . $usrId . "' OR a.eDepAccount LIKE '%050189756%') AND IF( SUBSTRING( eDepAccount, -9 ) = '000000000', ePayTitle LIKE  '%利息%' OR ePayTitle LIKE 'INT',(a.eTradeStatus = '1' OR a.eTradeStatus = '9' OR a.ePayTitle NOT LIKE '%網路整批%'))
    AND ((eTradeDate >= '" . $_f . "0101'
            AND eTradeDate <= '" . $_t . "1231%') OR eStatusIncome = 0 OR eStatusIncome = 1)";

if ($_SESSION['member_income'] == '1' || $_SESSION['member_test'] == 1) {
    $income_sql = "IF( SUBSTRING( eDepAccount, -9 ) = '000000000', ePayTitle LIKE  '%利息%' OR ePayTitle LIKE 'INT',(a.eTradeStatus = '1' OR a.eTradeStatus = '9' OR a.ePayTitle NOT LIKE '%網路整批%'))
    AND ((eTradeDate >= '" . $_f . "0101'
            AND eTradeDate <= '" . $_t . "1231%') OR eStatusIncome = 0 OR eStatusIncome = 1)";
}

if ($_POST['sSearch']) {
    $income_sql = "IF( SUBSTRING( eDepAccount, -9 ) = '000000000', ePayTitle LIKE  '%利息%' OR ePayTitle LIKE 'INT',(a.eTradeStatus = '1' OR a.eTradeStatus = '9' OR a.ePayTitle NOT LIKE '%網路整批%'))
    AND ((eTradeDate >= '" . $_f . "0101'
            AND eTradeDate <= '" . $_t . "1231%') OR eStatusIncome = 0 OR eStatusIncome = 1)";
}
/* Database parameter */
$aColumns = ['eTradeDate',
    'eRegistTime',
    'CertifiedId',
    'ePayTitle',
    'eLender',
    'eTradeStatusName',
    'cScrivener',
    'StatusIncome',
    'id',
    'sUndertaker1',
    'bFrom'];
$sIndexColumn = "id";

$sTable = "
(
  SELECT
    id,
    eStatusIncome,
    eAccount,
    CASE eTradeStatus
    WHEN 0 THEN '正常'
    WHEN 1 THEN '沖正'
    WHEN 9 THEN '被沖正'
    END eTradeStatusName,
    scr.sName AS cScrivener,
    eTradeDate,
    eTradeNum,
    CASE
    WHEN eTradeCode='1560' THEN CONCAT('-',CONVERT(LEFT( eDebit, 13 ), SIGNED))
    WHEN eTradeCode='1920' THEN CONCAT('-',CONVERT(LEFT( eDebit, 13 ), SIGNED))
    WHEN ePayTitle='票據退票' THEN CONCAT('-',CONVERT(LEFT( eDebit, 13 ), SIGNED))
    WHEN ePayTitle=' 退　票' THEN CONCAT('-',CONVERT(LEFT( eDebit, 13 ), SIGNED))
    ELSE CONCAT(CONVERT(LEFT( eLender, 13 ), SIGNED) - CONVERT(LEFT( eDebit, 13 ), SIGNED))
    END eLender,
    eDepAccount,
    SUBSTRING(eDepAccount, -9) CertifiedId,
    CASE eExportCode
    WHEN '9999999'
    THEN CONCAT(ePayTitle, '(', '本交票', ')')
    ELSE ePayTitle
    END ePayTitle,
    b.sName AS StatusIncome,
    SUBSTRING(a.eRegistTime,12,5) eRegistTime,
    scr.sUndertaker1,
    b.sSort,
    bco.bFrom
  FROM
    `tExpense` a
  LEFT JOIN
    tStatusIncome AS b ON a.eStatusIncome = b.sId
  LEFT JOIN
    tBankCode AS bco ON bco.bAccount=SUBSTRING(a.eDepAccount,-14)
  LEFT JOIN
    tScrivener AS scr ON scr.sId=bco.bSID
  WHERE
    " . $income_sql . "


) tb  ";

// WHERE  IF(条件,  true执行条件, false执行条件 )
/* injection */
$_POST['sSearch'] = $_POST['sSearch'];

/* Paging */
$sLimit = "";
if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . $_POST['iDisplayStart'] . ", " .
        $_POST['iDisplayLength'];
}

/* Ordering */
$sOrder = "";
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
        $sOrder = "   ";
    }
}
// else {
$sOrder = " ORDER BY sSort , eTradeDate desc,eRegistTime desc, eDepAccount, eTradeNum ";
// }

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
$sQuery = "
    SELECT SQL_CALC_FOUND_ROWS eAccount, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
    FROM   $sTable
    $sWhere
    $sOrder
    $sLimit
  ";

$rResult = mysqli_query($link, $sQuery) or die(mysqli_error());

/* Data set length after filtering */
$sQuery = "
    SELECT FOUND_ROWS()
  ";
$rResultFilterTotal = mysqli_query($link, $sQuery) or die(mysqli_error());
$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
$iFilteredTotal     = $aResultFilterTotal[0];

/* Total data set length */
$sQuery = "
    SELECT COUNT(" . $sIndexColumn . ")
    FROM   $sTable
  ";
$rResultTotal = mysqli_query($link, $sQuery) or die(mysqli_error());
$aResultTotal = mysqli_fetch_array($rResultTotal);
$iTotal       = $aResultTotal[0];

/* Output */
$output = [
    "sEcho"                => intval($_POST['sEcho']),
    "iTotalRecords"        => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData"               => [],
];

$minus = 0; //20240618 Project S只有特定人員可以查看用
while ($aRow = mysqli_fetch_array($rResult)) {
    $row = [];

    // Add the row ID and class to the object
    $row['DT_RowId']    = 'row_' . $aRow['CertifiedId'] . '_' . $aRow['id'];
    $row['DT_RowClass'] = 'grade' . ($aRow['grade'] ?? 'default');

    for ($i = 0; $i < count($aColumns); $i++) {
        //20240618 Project S只有特定人員可以查看
        if (($aColumns[$i] == "CertifiedId") && ($aRow[$aColumns[$i]] == '130119712') && ! in_array($_SESSION['member_id'], [1, 3, 6, 12, 13, 36, 84, 90])) {
            $minus++;
            continue;
        }

        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];

        } elseif ($aColumns[$i] == "StatusIncome") {
            //$aRow[$aColumns[$i]] .="(進行中)";

            if ($aRow[$aColumns[$i]] == "待確認" || $aRow[$aColumns[$i]] == "己確認") {

                if (preg_match("/本交票/", $aRow['ePayTitle'])) {

                    $dd    = tDate_check($aRow['eTradeDate'], 'ymd', 'r', '', 1, 0);
                    $today = (date('Y') - 1911) . date('m') . date('d');

                    if ($dd > $today) {
                        $aRow[$aColumns[$i]] .= '(隔日帳)';

                    }

                }
            }
            $row[] = $aRow[$aColumns[$i]];

        } else if ($aColumns[$i] == "eTradeStatusName") { //加入案件狀態 20150514(為了美觀把時間秒拿掉了)

            $sql = "SELECT cCaseStatus FROM tContractCase  WHERE cCertifiedId='" . str_replace('(電)', '', $aRow['CertifiedId']) . "'";

            $tmp_rel = mysqli_query($link, $sql);

            $tmp_row = mysqli_fetch_array($tmp_rel);
            if ($tmp_row && isset($tmp_row['cCaseStatus'])) {
                switch ($tmp_row['cCaseStatus']) {
                    case '2':
                        $aRow[$aColumns[$i]] .= "(進行中)";
                        break;
                    case '3':
                        $aRow[$aColumns[$i]] .= "(已結案)";
                        break;
                    case '4':
                        $aRow[$aColumns[$i]] .= "(解除契約)";
                        break;
                    case '6':
                        $aRow[$aColumns[$i]] .= "(異常)";
                        break;
                    case '8':
                        $aRow[$aColumns[$i]] .= "(作廢)";
                        break;
                    case '10':
                        $aRow[$aColumns[$i]] .= "已結案有保留款";
                        break;
                    case '9':
                        $aRow[$aColumns[$i]] .= "(發函終止)";
                        break;
                    default:
                        $aRow[$aColumns[$i]] .= "(未建檔)";
                        break;
                }
            }

            $row[] = $aRow[$aColumns[$i]];
            unset($tmp_row);
            // echo $aRow[$aColumns[$i]];
            // die;
        } elseif ($aColumns[$i] == "CertifiedId") {
            //
            if ($aRow['bFrom'] == 2) {
                $aRow[$aColumns[$i]] .= '(電)';
            }
            $row[] = $aRow[$aColumns[$i]];

        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }

        if ($_SESSION['member_id'] == 1) {
            if ($aColumns[$i] == 'sUndertaker1') {
                if ($aRow[$aColumns[$i]] == 1) {
                    $row['DT_RowClass'] = 'only';
                }
            }
        }

    }
    $output['aaData'][] = $row;

}
$output['iTotalDisplayRecords'] -= $minus;

echo json_encode($output);

//檢查票據是否兌現(日期檢查)
//$_date=>原始日期, $_dateType=>回覆日期格式('ymd','y','m','d','ym','md'), $_dateForm=>民國('r')、西元('b'), $_delimiter=>分隔符號, $_minus=>加減日數, $_sat=>是否過六日
function tDate_check($_date, $_dateForm = 'ymd', $_dateType = 'r', $_delimiter = '', $_minus = 0, $_sat = 0)
{
    $_aDate[0] = (substr($_date, 0, 3) + 1911);
    $_aDate[1] = substr($_date, 3, 2);
    $_aDate[2] = substr($_date, 5);

                  //是否遇六日要延後(六延兩天、日延一天)
    $weekend = 0; // 初始化 $weekend，避免未定義錯誤

    if ($_sat == '1') {
        $_ss = 0;
        $_ss = date("w", mktime(0, 0, 0, $_aDate[1], ($_aDate[2] + $_minus), $_aDate[0]));
        if ($_ss == '0') { // 如果是星期日的話，則延後一天
            $weekend = 1;
        } else if ($_ss == '6') { // 如果是星期六的話，則延後兩天
            $weekend = 2;
        }
    }
                                                                                              ##
    $_minus = $_minus + $weekend;                                                             //傳進來的日期必須加上遇到加日延後的日期
    $_t     = date("Y-m-d", mktime(0, 0, 0, $_aDate[1], ($_aDate[2] + $_minus), $_aDate[0])); //設定日期為 t+1 天
    unset($_aDate);

    $_aDate = explode('-', $_t);

    if ($_dateType == 'r') { //若要回覆日期格式為"民國"
        $_aDate[0] = $_aDate[0] - 1911;
    } else { //若要回覆日期格式為"西元"

    }

    //決定回覆日期格式
    switch ($_dateForm) {
        case 'y': //年
            return $_aDate[0];
            break;
        case 'm': //月
            return $_aDate[1];
            break;
        case 'd': //日
            return $_aDate[2];
            break;
        case 'ym': //年月
            return $_aDate[0] . $_delimiter . $_aDate[1];
            break;
        case 'md': //月日
            return $_aDate[1] . $_delimiter . $_aDate[2];
            break;
        case 'ymd': //年月日
            return $_aDate[0] . $_delimiter . $_aDate[1] . $_delimiter . $_aDate[2];
            break;
        default:
            break;
    }
    ##
}
