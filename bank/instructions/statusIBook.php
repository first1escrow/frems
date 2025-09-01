<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

$_POST = escapeStr($_POST);
$_GET  = escapeStr($_GET);

$status = ($_GET['f'] == 'list') ? 2 : $_POST['s'];

$sql = "UPDATE
            tBankTrankBook
        SET
            bStatus = '" . $status . "',
            bModifyName3 = '" . $_SESSION['member_name'] . "',
            bModifyDate3 = '" . date('Y-m-d H:i:s') . "',
            bModifyName = '" . $_SESSION['member_name'] . "',
            bModifyDate = '" . date('Y-m-d H:i:s') . "'
        WHERE
            bId ='" . $_POST['id'] . "';";
if ($conn->Execute($sql)) {
    echo 'OK';

    //20240105
    require_once dirname(dirname(__DIR__)) . '/first1DB.php';

    $db_conn = new first1DB;

    //取得指示書ID對應出款資料的ID
    $sql = "SELECT b.tId FROM tBankTrankBook AS a JOIN tBankTrans AS b ON a.bExport_nu = b.tExport_nu WHERE a.bId = :id AND a.bCategory <> 6 AND a.bDel = 0 AND a.bExport_nu != '';";
    $rs  = $db_conn->all($sql, ['id' => $_POST['id']]);

    if (!empty($rs)) {
        $tIds = array_column($rs, 'tId');

        $cBankRelay = 'N';
        if ($status == 2) { //審核通過
            $cBankRelay = 'Y';
            require_once dirname(dirname(__DIR__)) . '/includes/bank/registerRelayBank.php';
        } else { //恢復待審核/待確認
            require_once dirname(dirname(__DIR__)) . '/includes/bank/removeRelayBank.php';
        }

        //20241112 代墊利息案件註記
        require_once dirname(dirname(__DIR__)) . '/includes/bank/payForBalance.php';

        $tIds = $db_conn = null;
        unset($tIds, $db_conn);
    }
}