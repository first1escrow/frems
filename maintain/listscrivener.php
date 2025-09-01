<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/tracelog.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_GET), '檢視地政士資料維護列表');

if ($_GET['sZip']) {
    $zip = addslashes(trim($_GET['sZip']));
}

if ($_GET['sSearch']) {
    $sSearch = addslashes(trim($_GET['sSearch']));
}

$sales = $_GET['salesman'];

$salesman = '<option value="">請選擇業務身分</option>';
$sql      = 'SELECT * FROM tPeopleInfo WHERE pDep IN (4,7)  AND pJob = 1  ORDER BY pId ASC;';
$rs       = $conn->Execute($sql);
while (!$rs->EOF) {
    $salesman .= '<option value="' . $rs->fields['pId'] . '"';
    if ($sales == $rs->fields['pId']) {
        $salesman .= ' selected="selected"';
    }

    $salesman .= '>' . $rs->fields['pName'] . "</option>\n";

    $rs->MoveNext();
}

if ($_GET['city'] || $zip) {
    $sql      = "SELECT * FROM tZipArea WHERE zCity = '" . urldecode($_GET['city']) . "'";
    $rs       = $conn->Execute($sql);
    $zip_c    = (empty($zip)) ? $rs->fields['zZip'] : $zip;
    $tmp_city = (empty($_GET['city'])) ? $rs->fields['zCity'] : urldecode($_GET['city']);

    $area = "<option value=''>全區</option>";
    while (!$rs->EOF) {
        $selected = '';
        if ($zip == $rs->fields['zZip']) {
            $selected = "selected=selected";
        }
        $area .= "<option value='" . $rs->fields['zZip'] . "' " . $selected . ">" . $rs->fields['zArea'] . "</option>";

        $rs->MoveNext();
    }
} else {
    $area = "<option value=''>全區</option>";
}

$sql     = "SELECT * FROM tZipArea  GROUP BY zCity ORDER BY nid";
$rs      = $conn->Execute($sql);
$country = "<option value=''>全區</option>";
while (!$rs->EOF) {
    $selected = '';

    if ($tmp_city == $rs->fields['zCity']) {
        $selected = "selected=selected";
    }

    $country .= "<option value='" . $rs->fields['zCity'] . "' " . $selected . " >" . $rs->fields['zCity'] . "</option>";

    $rs->MoveNext();
}

//回饋條件
$feedDateCat = '<option value=""';
$feedDateCat .= (!isset($_GET['feedDateCat']) || $_GET['feedDateCat'] == '') ? ' selected="selected"' : '';
$feedDateCat .= '>全部</option>' . "\n";
$feedDateCat .= '<option value="0"';
$feedDateCat .= ($_GET['feedDateCat'] == '0') ? ' selected="selected"' : '';
$feedDateCat .= '>季結</option>' . "\n";
$feedDateCat .= '<option value="1"';
$feedDateCat .= ($_GET['feedDateCat'] == '1') ? ' selected="selected"' : '';
$feedDateCat .= '>月結</option>' . "\n";
$feedDateCat .= '<option value="2"';
$feedDateCat .= ($_GET['feedDateCat'] == '2') ? ' selected="selected"' : '';
$feedDateCat .= '>隨案結</option>' . "\n";

$smarty->assign('country', $country); //縣市
$smarty->assign('area', $area); //鄉鎮區域

$smarty->assign('feedDateCat', $feedDateCat);
$smarty->assign('salesman', $salesman);
$smarty->assign('sSearch', $sSearch);
$smarty->assign('search_zip', $zip);
$smarty->display('listscrivener.inc.tpl', '', 'maintain');
