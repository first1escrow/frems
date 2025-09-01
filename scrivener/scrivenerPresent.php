<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/tracelog.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_GET), '檢視地政士資料維護列表');
$_GET = escapeStr($_GET);

if ($_GET['sSearch']) {
    $sSearch = trim($_GET['sSearch']);
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
##

//
$menuYearOutput = [];

$year = (date('Y') - 1911);

$menuYear[0]       = '';
$menuYearOutput[0] = '全部';
for ($i = ($year + 1); $i >= 107; $i--) {
    $menuYear[$i]       = $i;
    $menuYearOutput[$i] = $i;
}

if (isset($_REQUEST['sYear'])) {
    $year = ($_REQUEST['sYear']) ? $_REQUEST['sYear'] : '';
} else {
    $year = ($_REQUEST['sYear']) ? $_REQUEST['sYear'] : (date('Y') - 1911);
}
##

$menuTarget = ($_GET['target'] == '') ? '<option value="">請選擇</option> ' : '<option value="">請選擇</option>';

$menuTarget .= ($_GET['target'] == 1) ? '<option value="1" selected="selected">未達標</option>' : '<option value="1">未達標</option>';
$menuTarget .= ($_GET['target'] == 2) ? '<option value="2" selected="selected">已達標</option>' : '<option value="2">已達標</option>';

$menuReceipt = ($_GET['receipt'] == '') ? '<option value="">請選擇</option> ' : '<option value="">請選擇</option>';
$menuReceipt .= ($_GET['receipt'] == '0') ? '<option value="0" selected="selected">未繳回</option>' : '<option value="0">未繳回</option>';
$menuReceipt .= ($_GET['receipt'] == '1') ? '<option value="1" selected="selected">已繳回</option>' : '<option value="1">已繳回</option>';

$menuStatus = "<option value=''>全部</option>";
$menuStatus .= ($_GET['status'] == 1) ? '<option value="1" selected="selected">申請中</option>' : '<option value="1">申請中</option>';
$menuStatus .= ($_GET['status'] == 2) ? '<option value="2" selected="selected">主管審核通過</option>' : '<option value="2">主管審核通過</option>';
$menuStatus .= ($_GET['status'] == 3) ? '<option value="3" selected="selected">主管審核不通過</option>' : '<option value="3">主管審核不通過</option>';
$menuStatus .= ($_GET['status'] == 4) ? '<option value="4" selected="selected">已處理</option>' : '<option value="4">已處理</option>';
$menuStatus .= ($_GET['status'] == 5) ? '<option value="5" selected="selected">取消申請</option>' : '<option value="5">取消申請</option>';
// print_r($menuYear);
$smarty->assign('menuReceipt', $menuReceipt);
$smarty->assign('menuYear', $menuYear);
$smarty->assign('menuYearOutput', $menuYearOutput);
$smarty->assign('year', $year);
$smarty->assign('salesman', $salesman);
$smarty->assign('menuTarget', $menuTarget);
$smarty->assign('menuStatus', $menuStatus);
$smarty->display('scrivenerPresent.inc.tpl', '', 'scrivener');
