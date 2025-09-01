<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

// $_POST = escapeStr($_POST);
// $env['public']

$conn = new first1DB;

//
$sql = 'SELECT
            a.sId,
            a.sDate,
            a.sIdentity,
            a.sConfirmed,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.sSales) as sales,
            c.sOffice,
            c.sName,
            d.bStore,
            (SELECT bName FROM tBrand WHERE bId = d.bBrand) as brand
        FROM
            tSalesReportPromo AS a
        JOIN
            tPeopleInfo AS b ON a.sSales = b.pId
        LEFT JOIN
            tScrivener AS c ON a.sStore = c.sId
        LEFT JOIN
            tBranch AS d ON a.sStore = d.bId;';
$rs = $conn->all($sql);
// echo '<pre>';
// print_r($rs);exit;
$data = [];
if (!empty($rs)) {
    foreach ($rs as $v) {
        if ($v['sIdentity'] == 'S') {
            $store = $v['sOffice'];
        } else {
            $store = $v['brand'] . $v['bStore'];
        }

        $confirm = '待確認';
        if ($v['sConfirmed'] == 'Y') {
            $confirm = '已確認';
        } else if ($v['sConfirmed'] == 'R') {
            $confirm = '已駁回';
        }
        $data[] = [
            'date'     => $v['sDate'],
            'sales'    => $v['sales'],
            'identity' => ($v['sIdentity'] == 'S') ? '地政士' : '仲介',
            'store'    => $store,
            'confirm'  => $confirm,
            'detail'   => '<a href="Javascript:void(0);" onclick="detail(\'' . $v['sId'] . '\')">點我查看</a>',
        ];

        $store = $confirm = null;
        unset($store, $confirm);
    }
}
##
// echo '<pre>';
// print_r($data);exit;

$conn = $rs = $sql = null;
unset($conn, $rs, $sql);

$smarty->assign('data', $data);

$smarty->display('salesReportPromo.inc.tpl', '', 'report2');
