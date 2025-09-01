<?php
require_once dirname(__DIR__) . '/bank/report/calTax.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/openadodb.php';

$cId                = $_POST['certifiedid'];
$checkaddedtaxmoney = $_POST['checkaddedtaxmoney'];
$data               = $_POST;

if (empty($data['land_item'])) {
    throw new Exception('No Land Item Founded!!');
}

//過濾取消未指定地號陣列紀錄
foreach ($data['land_item'] as $k => $v) {
    if (empty($data['land_land3'][$k])) {
        $data['land_item'][$k]    = $data['land_zip'][$k]    = null;
        $data['land_land1'][$k]   = $data['land_land2'][$k]   = $data['land_land3'][$k]   = null;
        $data['land_measure'][$k] = $data['land_category'][$k] = $data['land_money'][$k] = null;
        $data['land_power1'][$k]  = $data['land_power2'][$k]  = $data['land_price_' . $v]  = null;

        unset($data['land_item'][$k], $data['land_zip'][$k]);
        unset($data['land_land1'][$k], $data['land_land2'][$k], $data['land_land3'][$k]);
        unset($data['land_measure'][$k], $data['land_category'][$k], $data['land_money'][$k]);
        unset($data['land_power1'][$k], $data['land_power2'][$k], $data['land_price_' . $v]);

        $data['land_price_movedate'][$k]  = null;unset($data['land_price_movedate'][$k]);
        $data['land_price_landprice'][$k] = null;unset($data['land_price_landprice'][$k]);
        $data['land_price_power1'][$k]    = null;unset($data['land_price_power1'][$k]);
        $data['land_price_power2'][$k]    = null;unset($data['land_price_power2'][$k]);

    } else {
        if (! empty($data['land_price_movedate'][$k]) || ! empty($data['land_price_landprice'][$k])
            || ! empty($data['land_price_power1'][$k]) || ! empty($data['land_price_power2'][$k])) {

            $data['land_price_movedate'][$k]  = json_decode($data['land_price_movedate'][$k], true);
            $data['land_price_landprice'][$k] = json_decode($data['land_price_landprice'][$k], true);
            $data['land_price_power1'][$k]    = json_decode($data['land_price_power1'][$k], true);
            $data['land_price_power2'][$k]    = json_decode($data['land_price_power2'][$k], true);
        }
    }
}
##

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '編修合約書土地詳細資料');

$sql = 'DELETE FROM tContractLand WHERE cCertifiedId = "' . $cId . '" AND cItem > 0;'; //除了第一筆以外的都刪除
$conn->Execute($sql);

$sql = 'DELETE FROM tContractLandPrice WHERE cCertifiedId = "' . $cId . '" AND cLandItem > 0;'; //除了第一筆以外的都刪除
$conn->Execute($sql);

$cLandItem = 1;
foreach ($data['land_item'] as $k => $v) {
    $sql = 'INSERT INTO
                tContractLand
            (
                cCertifiedId, cItem, cZip,
                cLand1, cLand2, cLand3,
                cMeasure, cCategory, cMoney,
                cPower1, cPower2
            ) VALUES (
                "' . $cId . '", "' . $cLandItem . '", "' . $data['land_zip'][$k] . '",
                "' . $data['land_land1'][$k] . '", "' . $data['land_land2'][$k] . '", "' . $data['land_land3'][$k] . '",
                "' . $data['land_measure'][$k] . '", "' . $data['land_category'][$k] . '", "' . $data['land_money'][$k] . '",
                "' . $data['land_power1'][$k] . '", "' . $data['land_power2'][$k] . '"
            );';
    if ($conn->Execute($sql)) {
        if (! empty($data['land_price_movedate'][$k])) {
            $item_index = 0;
            foreach ($data['land_price_movedate'][$k] as $ka => $va) {
                if (! empty($data['land_price_movedate'][$k][$ka])
                    || ! empty($data['land_price_landprice'][$k][$ka])
                    || ! empty($data['land_price_power1'][$k][$ka])
                    || ! empty($data['land_price_power2'][$k][$ka])) {

                    if (! empty($data['land_price_movedate'][$k][$ka])) {
                        $arr                                  = explode('-', $data['land_price_movedate'][$k][$ka]);
                        $data['land_price_movedate'][$k][$ka] = ($arr[0] + 1911) . '-' . str_pad($arr[1], 2, '0', STR_PAD_LEFT) . '-00';
                        $arr                                  = null;unset($arr);
                    }

                    $tmp[] = '("' . $cId . '", "' . $cLandItem . '", "' . $item_index . '", "' . $data['land_price_movedate'][$k][$ka] . '", "' . $data['land_price_landprice'][$k][$ka] . '", "' . $data['land_price_power1'][$k][$ka] . '", "' . $data['land_price_power2'][$k][$ka] . '")';
                    $item_index++;
                }
            }

            if (! is_array($tmp)) {
                $tmp = [];
            }
            $sql = 'INSERT INTO
                        tContractLandPrice
                    (
                        cCertifiedId, cLandItem, cItem,
                        cMoveDate, cLandPrice,
                        cPower1, cPower2
                    ) VALUES ' . implode(',', $tmp) . ';';
            $conn->Execute($sql);

            $item_index = $tmp = null;
            unset($item_index, $tmp);
        }
    }

    $cLandItem++;
}

//重新計算增值稅
$money = calCase($_POST["certifiedid"]);

$sql = "UPDATE tContractIncome SET cAddedTaxMoney ='" . $money . "' WHERE cCertifiedId = '" . $_POST["certifiedid"] . "'"; //cAddedTaxMoney
$conn->Execute($sql);

if ($_GET['act'] == 'price') {
    header("Location: formlandprice.php?item=" . $_GET["no"] . "&type=2&cSignCategory=" . $_GET['cSignCategory'] . "&id=" . $_POST["certifiedid"]);
} else {
    header("Location: formbuyowneredit.php?id=" . $_POST["certifiedid"]);
}

exit;
