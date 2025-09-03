<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';

$tlog = new TraceLog();
if (isset($_SESSION['member_id'])) {
    $tlog->selectWrite($_SESSION['member_id'], json_encode($_REQUEST), '查看銀行資料列表');
}

$t = $_REQUEST['t'] ?? null; // 檢查是否存在
$f = $_REQUEST['f'] ?? null; // 檢查是否存在

// 初始化 $yr['f'] 和 $yr['t']
$yr = ['f' => '', 't' => ''];

for ($i = (date("Y") - 1911); $i >= 101; $i--) {
    $yr['f'] .= '<option value=' . $i;
    $yr['t'] .= '<option value=' . $i;

    if ($i == $f) {
        $yr['f'] .= ' selected="selected"';
    }

    if ($i == $t) {
        $yr['t'] .= ' selected="selected"';
    }

    $yr['f'] .= '>' . $i . "</option>\n";
    $yr['t'] .= '>' . $i . "</option>\n";
}

$smarty->assign('yr', $yr);
$smarty->assign('web_addr', $web_addr);
$smarty->display('listinspection.inc.tpl', '', 'income');
