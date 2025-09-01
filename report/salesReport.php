<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/report/getBranchType.php';
require_once dirname(__DIR__) . '/includes/sales/getSalesInfo.php'; //function 都在這
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';

$_POST = escapeStr($_POST);

//取得業務人員清單
$sql = 'SELECT * FROM tPeopleInfo WHERE pDep IN ("7","8") AND pJob =1 AND pId != 3 ORDER BY pId ASC;';
$rs  = $conn->Execute($sql);

if ($_SESSION['member_id'] == 6) {
    $menu_sales['a'] = '全部';
}

while (! $rs->EOF) {
    $menu_sales[$rs->fields['pId']] = $rs->fields['pName'];

    if ($rs->fields['pJob'] == 2) {
        $menu_sales[$rs->fields['pId']] .= '(離)';
    }

    $rs->MoveNext();
}

if (empty($_POST['sales'])) {
    if ($_SESSION['member_id'] && $_SESSION['member_pDep'] == 7) {
        $sales = $_SESSION['member_id'];
    }
} else {
    $sales = $_POST['sales'];
}

//時間下拉
$yr    = trim($_POST['dateYear']);
$mn    = trim($_POST['dateMonth']);
$ok    = trim($_POST['ex']);
$trace = trim($_POST['traceXls']);

if ($trace == 'trace') {
    require_once __DIR__ . '/traceXls.php';
}

if (! $yr) {
    $yr = date("Y") - 1911;
}

if (! $mn) {
    $mn = date("m", mktime(0, 0, 0, (date("m"))));
}

$grade = 0;
##

//年度顯示
$y = '';
for ($i = 0; $i < 100; $i++) {
    $patt = $i + 100;
    $sl   = ($patt == $yr) ? " selected='selected'" : '';
    $y .= "<option value='" . $patt . "'" . $sl . ">" . $patt . "</option>\n";
}
##

//月份顯示
$m = '';
for ($i = 0; $i < 12; $i++) {
    $patt = $i + 1;
    $sl   = ($patt == $mn) ? " selected='selected'" : '';
    $m .= "<option value='" . $patt . "'" . $sl . ">" . $patt . "</option>\n";
}
##

//
$sql     = "SELECT * FROM tSalesReportPercent WHERE pYear = '" . ($yr + 1911) . "' AND pMonth = '" . intval($mn) . "' AND pSalesId = '" . $sales . "' ";
$rs      = $conn->Execute($sql);
$percent = $rs->fields;
##

if ($yr <= 104) {
    require_once dirname(__DIR__) . '/includes/sales/salesReportFor105.php';
} else if (is_file(dirname(__DIR__) . '/includes/sales/salesReportFor' . $yr . '.php')) {
    require_once dirname(__DIR__) . '/includes/sales/salesReportFor' . $yr . '.php';
} else {
    throw new Exception('找不到對應年度的檔案');
}

if (sprintf("%d", date('Y')) > $yr) {
    $now_check = '1';
}

$now_month = sprintf("%d", date('m'));
###########

if ($ok == 'ok') {
    $sql        = "SELECT pName FROM tPeopleInfo WHERE pId = '" . $sales . "'";
    $rs         = $conn->Execute($sql);
    $sales_name = $rs->fields['pName'];

    require_once __DIR__ . '/salesReportExcel.php';
}
$tmp_use = null;unset($tmp_use);
##

$smarty->assign('dataTaoyuan', $dataTaoyuan);
$smarty->assign('totalData', $totalData);
$smarty->assign('salesGroupListShow', $salesGroupListShow);
$smarty->assign('salesGroupList', $salesGroupList);
$smarty->assign('gradeNotice', $gradeNotice);
$smarty->assign('gradecolor', $gradecolor);
$smarty->assign('now_check', $now_check);
$smarty->assign('sess', $sess);
$smarty->assign('script', $script);
$smarty->assign('menu_sales', $menu_sales);
$smarty->assign('sales', $sales);
$smarty->assign("y", $y);
$smarty->assign("m", $m);
$smarty->assign("mn", $mn);
$smarty->assign("yr", $yr);
$smarty->assign("now_month", $now_month);
$smarty->assign('season1', $season1);
$smarty->assign('season2', $season2);
$smarty->assign('now_year', ($yr));
$smarty->assign('summary1Table', $summary1Table);
$smarty->assign('summary1Table', $summary1Table);
$smarty->assign('BranchCount', $BranchCount);
$smarty->assign('Branch', $Branch);
$smarty->assign('ScrivenerCount', $ScrivenerCount);
$smarty->assign('Scrivener', $Scrivener);
$smarty->assign('target', $target);
$smarty->assign('group', $group);
$smarty->assign('use', $use);
$smarty->assign('grade', $grade);
$smarty->assign('summary1', $summary1);
$smarty->assign('contribution', $contribution);
$smarty->assign('seasontarget', $seasontarget);
$smarty->assign('seasongroup', $seasongroup);
$smarty->assign('seasonuse', $seasonuse);
$smarty->assign('showseason', $showseason);
$smarty->assign('seasoncontribution', $seasoncontribution);
$smarty->assign('oseasontarget', $oseasontarget);
$smarty->assign('oseasongroup', $oseasongroup);
$smarty->assign('oseasonuse', $oseasonuse);
$smarty->assign('oshowseason', $oshowseason);
$smarty->assign('oseasoncontribution', $oseasoncontribution);
$smarty->assign('effect_type', $effect_type);
$smarty->assign('eff1', $eff1);
$smarty->assign('eff2', $eff2);
$smarty->assign('groupTW', $groupTW);
$smarty->assign('groupUnTW', $groupUnTW);
$smarty->assign('groupTW38', $groupTW38);
$smarty->assign('groupUnTW38', $groupUnTW38);
$smarty->assign('seasongroupTW', $seasongroupTW);
$smarty->assign('seasongroupUnTW', $seasongroupUnTW);
$smarty->assign('percent', $percent);
$smarty->assign('seasongroupALL', $seasongroupALL);
$smarty->assign('sales_weight', $sales_weight);
$smarty->assign('calendar_score', $calendar_score);
$smarty->assign('econtract', $econtract);

if ($yr <= 106) {
    $smarty->display('salesReport.inc.tpl', '', 'report');
} else if ($yr >= 107) {
    $smarty->display('salesReport_107.inc.tpl', '', 'report');
}
