<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/class/contract.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/class/member.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/bank/report/calTax.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '查看合約書土地前次移轉詳細資料');

$_POST = escapeStr($_POST);
$_GET  = escapeStr($_GET);

$contract = new Contract();

$certified_id = ($_POST['id']) ? $_POST['id'] : $_GET['id'];
$item         = $_GET['item'];
$type         = isset($_GET['type']) ? $_GET['type'] : 1; // 確保 type 有默認值
$file         = ($type == 2) ? 'formland2edit.php' : 'formbuyowneredit.php';

if (! empty($_POST['delId'])) {
    $sql = "UPDATE tContractLandPrice SET cDel = 1 WHERE cId = '" . $_POST['delId'] . "'";

    $conn->Execute($sql);
}

if (! empty($_POST['ok'])) {

    $sql       = "SELECT cItem FROM tContractLandPrice WHERE cCertifiedId = '" . $certified_id . "' AND cLandItem = '" . $item . "' ORDER BY cItem DESC LIMIT 1";
    $rs        = $conn->Execute($sql);
    $dataCount = (! empty($rs->fields['cItem'])) ? ($rs->fields['cItem'] + 1) : 0;

    for ($i = 0; $i < count($_POST['new_land_movedate']); $i++) {
        if (! empty($_POST['new_land_movedate'][$i]) || ! empty($_POST['new_land_landprice'][$i]) || ! empty($_POST['new_land_power1'][$i]) || ! empty($_POST['new_land_power2'][$i])) {

            $data                 = [];
            $data['cCertifiedId'] = $certified_id;
            $data['cLandItem']    = $item;
            $data['cItem']        = $dataCount + $i;
            $data['cMoveDate']    = date_convert($_POST['new_land_movedate'][$i]) . "-00";
            $data['cLandPrice']   = str_replace(',', '', $_POST['new_land_landprice'][$i]);
            $data['cPower1']      = $_POST['new_land_power1'][$i];
            $data['cPower2']      = $_POST['new_land_power2'][$i];

            $contract->addLandPrice($data);
        }
    }
    unset($data);
    unset($dataCount);

    if (! empty($_POST['land_movedate'])) {
        for ($i = 0; $i < count($_POST['land_movedate']); $i++) {
            if (! empty($_POST['land_movedate'][$i]) || ! empty($_POST['land_landprice'][$i]) || ! empty($_POST['land_power1'][$i]) || ! empty($_POST['land_power2'][$i])) {

                $data = [];

                $data['cMoveDate']  = date_convert($_POST['land_movedate'][$i]) . "-00";
                $data['cLandPrice'] = str_replace(',', '', $_POST['land_landprice'][$i]);
                $data['cPower1']    = $_POST['land_power1'][$i];
                $data['cPower2']    = $_POST['land_power2'][$i];
                $data['cId']        = $_POST['land_id'][$i];

                $contract->saveLandPrice($data);

            }
        }
    }
    unset($data);

    //土增稅

    $cal = calCase($certified_id);
    $sql = "UPDATE tContractIncome SET cAddedTaxMoney = '" . $cal . "' WHERE cCertifiedId = '" . $certified_id . "'";
    $conn->Execute($sql);

    $tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '編輯合約書土地前次移轉詳細資料');

    if ($type == 2) {
        header("Location: formland2edit.php?id=" . $certified_id);
    } else {
        header("Location: formbuyowneredit.php?id=" . $certified_id);
    }

}
##
$sql = "SELECT * FROM tContractLandPrice WHERE cCertifiedId = '" . $certified_id . "' AND cLandItem = '" . $item . "' AND cItem >= 2 AND cDel = 0 ORDER BY cItem ASC ";
// echo $sql."<br>";
$rs   = $conn->Execute($sql);
$data = [];
while (! $rs->EOF) {
    $rs->fields['cMoveDate'] = substr($rs->fields['cMoveDate'], 0, 7);

    $rs->fields['cMoveDate'] = (substr($rs->fields['cMoveDate'], 0, 4) - 1911) . substr($rs->fields['cMoveDate'], 4);

    array_push($data, $rs->fields);

    $rs->MoveNext();
}

##
$smarty->assign('file', $file);
$smarty->assign('cSignCategory', $_GET['cSignCategory']);
$smarty->assign('data', $data);
$smarty->assign('certifiedid', $certified_id);
$smarty->display('formlandprice.inc.tpl', '', 'escrow');
