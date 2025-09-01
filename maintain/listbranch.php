<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/tracelog.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_GET), '查詢仲介店列表');

if ($_GET['sBrand']) {
    $brand2 = trim($_GET['sBrand']);
}

if ($_GET['sZip']) {
    $zip = trim($_GET['sZip']);
}

$sales = $_GET['salesman'];

$salesman = '<option value="">請選擇業務身分</option>';
$sql      = 'SELECT * FROM tPeopleInfo WHERE pDep IN (4,7) AND pJob = 1 ORDER BY pId ASC;';
$rs       = $conn->Execute($sql);
while (!$rs->EOF) {
    $salesman .= '<option value="' . $rs->fields['pId'] . '"';
    if ($sales == $rs->fields['pId']) {
        $salesman .= ' selected="selected"';
    }

    $salesman .= '>' . $rs->fields['pName'] . "</option>\n";

    $rs->MoveNext();
}

##品牌選單
$brand = new Brand();

$list_brand = $brand->GetBrandList(array(8, 77));

$menu_brand    = $brand->ConvertOption($list_brand, 'bId', 'bName');
$menu_brand[0] = "請選擇";

ksort($menu_brand);
// array_unshift($menu_brand,'請選擇');
##

// if ($_GET['city']) {
//     $sql = "SELECT zZip FROM tZipArea WHERE zCity = '".$_GET['city']."'";
//     // echo $sql;
//     $rs = $conn->Execute($sql);
//     $zip_c = $rs->fields['zZip'];
// }else{
//     $zip_c = $zip;
//     $sql = "SELECT * FROM tZipArea WHERE zCity = '".$_GET['city']."'";
//     $rs = $conn->Execute($sql);
// }

if ($_GET['city'] || $zip) {
    $sql      = "SELECT * FROM tZipArea WHERE zCity = '" . urldecode($_GET['city']) . "'";
    $rs       = $conn->Execute($sql);
    $zip_c    = (empty($zip)) ? $rs->fields['zZip'] : $zip;
    $tmp_city = (empty($_GET['city'])) ? $rs->fields['zCity'] : urldecode($_GET['city']);
    // $country =  listCity($conn,$zip_c);

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
    // $country =  listCity($conn,'');
    $area = "<option value=''>全區</option>";
}

if ($zip_c) {
    $str = 'WHERE zZip = "' . $zip_c . '"';
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
// print_r($country);
// echo $zip_c;

##
$smarty->assign('menu_brand', $menu_brand);
$smarty->assign('country', $country); //縣市

$smarty->assign('area', $area); //鄉鎮區域

$smarty->assign('salesman', $salesman);

$smarty->assign('search_brand', $brand2);
$smarty->assign('search_zip', $zip);
$smarty->assign('city', $city);

$smarty->display('listbranch.inc.tpl', '', 'maintain');
