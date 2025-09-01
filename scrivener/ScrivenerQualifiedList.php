<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/tracelog.php';

//取得地政士所屬業務
function getScrivenerSales($scrivener)
{
    global $conn;

    $sql = 'SELECT d.pName FROM tScrivenerSales AS c JOIN tPeopleInfo AS d ON c.sSales = d.pId WHERE c.sScrivener = ' . $scrivener;
    $rs  = $conn->all($sql);

    return empty($rs) ? '' : implode(',', array_column($rs, 'pName'));
}
##

// $tlog = new TraceLog();
// $tlog->selectWrite($_SESSION['member_id'], json_encode($_GET), '檢視地政士生日禮達標名單');

//年度選單
$yearOptions = [];
for ($i = (date("Y", strtotime("+1 year")) - 1911); $i > 106; $i--) {
    $yearOptions[$i] = $i . '年度';
}
##

//月份選單
$monthOptions = [];
for ($i = 1; $i <= 12; $i++) {
    $monthOptions[str_pad($i, 2, '0', STR_PAD_LEFT)] = $i . ' 月份';
}
##

//
$month = preg_match("/^\d{2}$/", $_POST['month']) ? $_POST['month'] : date('m', strtotime('+1 month', strtotime(date('Y-m-01')))); //預設當月份的下個月
$year  = preg_match("/^\d{3}$/", $_POST['year']) ? ($_POST['year'] + 1911) : date('Y', strtotime('+1 month', strtotime(date('Y-m-01')))); //預設當年度
$level = preg_match("/^\d{1}$/", $_POST['level']) ? $_POST['level'] : 3;
##

$exception_scrivener = [436]; //排除名單

//
$conn = new first1DB;

$sql = 'SELECT
            a.sScrivener as code,
            a.sLevel as level,
            b.sName as name,
            SUBSTRING(b.sBirthday, 6, 5) as birthday
        FROM
            tScrivenerLevel AS a
        JOIN
            tScrivener AS b ON a.sScrivener = b.sId
        WHERE a.sYear = "' . $year . '" AND a.sStatus = 0 AND a.sLevel = "' . $level . '" AND MONTH(b.sBirthday) = "' . $month . '";';
$rs = $conn->all($sql);

$data = [];
foreach ($rs as $v) {
    if (!in_array($v['code'], $exception_scrivener)) {
        $data[] = [
            'code'     => 'SC' . str_pad($v['code'], 4, '0', STR_PAD_LEFT),
            'name'     => $v['name'],
            'birthday' => str_replace('-', '/', $v['birthday']),
            'level'    => $v['level'] . '級',
            'sales'    => getScrivenerSales($v['code']),
        ];
    }
}
##

$smarty->assign('yearOptions', $yearOptions);
$smarty->assign('selectedYear', ($year - 1911));
$smarty->assign('monthOptions', $monthOptions);
$smarty->assign('selectedMonth', $month);
$smarty->assign('data', $data);
$smarty->assign('levelOptions', ['1' => '一級地政士', '2' => '二級地政士', '3' => '三級地政士']);
$smarty->assign('selectedLevel', $level);

$smarty->display('ScrivenerQualifiedList.inc.tpl', '', 'scrivener');