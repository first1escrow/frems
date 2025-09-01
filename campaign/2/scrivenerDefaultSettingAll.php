<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

/**
 * 北北區回饋對象：地政士
 * 桃園區回饋對象：地政士
 * 屏澎區回饋對象：地政士
 * 雲嘉南回饋對象：地政士
 * 中彰投回饋對象：地政士
 * 竹苗區回饋對象：地政士
 * 台東區回饋對象：地政士
 * 高雄區回饋對象：地政士
 */

//是否設定優先
function checkPriority($sId)
{
    //total priority: 114 20230224 SC0350 改 SC0850
    $priority = [2538, 2295, 2245, 850, 363, 179, 169, 1196, 286, 1100, 1268, 272, 1463, 1614, 958, 593, 1426, 1028, 293, 2811, 2575, 2037, 1951, 610, 395, 2218, 1155, 1876, 464, 701, 2103, 137, 2190, 902, 558, 1111, 1891, 2738, 2573, 1555, 1147, 562, 376, 1501, 1868, 1634, 2254, 430, 115, 1325, 1494, 1557, 1297, 2183, 2544, 1328, 700, 549, 373, 1492, 1230, 134, 1511, 314, 2173, 1720, 1250, 634, 1013, 2833, 2589, 1837, 813, 476, 445, 1133, 1132, 1928, 2807, 2009, 1575, 1854, 288, 2203, 1924, 886, 2233, 1583, 1598, 688, 894, 883, 1579, 2054, 125, 2617, 578, 1587, 2789, 2656, 2144, 1170, 2293, 135, 742, 461, 1966, 478, 1437, 1686, 1811, 2029, 1636, 2778];
    return in_array($sId, $priority) ? true : false;
}
##

//feedback data
function FeedBackData($id, $type)
{
    global $conn;

    $sql = "SELECT * FROM tFeedBackData WHERE fType ='" . $type . "' AND fStoreId ='" . $id . "' AND fStatus = 0 AND fStop = 0;";
    return $conn->all($sql);
}
##

//排除地政士店家
function checkException($sId)
{
    //total: 8
    $except_stores  = [564, 994, 951, 945, 781, 1815, 83, 1589]; //排除店家名單(共 8 家)
    $except_stores2 = [1, 2, 3, 4, 7, 8, 271, 337, 9, 2164, 10, 11, 13, 14, 15, 16, 17, 19, 232, 270, 283, 311, 12, 762, 21, 22, 23, 25, 26, 432]; //排除店家名單(共 30 家)

    $except_stores = array_merge($except_stores, $except_stores2);
    return in_array($sId, $except_stores) ? false : true;
}
##

$conn = new first1DB;

$sql = 'SELECT zZip, zCity FROM tZipArea WHERE zCity IN ("台北市", "新北市", "桃園市", "屏東縣", "澎湖縣", "雲林縣", "嘉義市", "嘉義縣", "台南市", "台中市", "彰化縣", "南投縣", "新竹市", "新竹縣", "苗栗縣", "台東縣", "高雄市");';
$rs  = $conn->all($sql);

$zips = $all_zips = [];
foreach ($rs as $v) {
    $zips[$v['zCity']][] = $v['zZip']; //by city
    $all_zips[]          = $v['zZip']; //all
}
$zips = null;unset($zips);

//get scriveners by zip
$sql = 'SELECT
            a.sId, a.sName, a.sOffice, a.sMobileNum
        FROM
            tScrivener as a
        WHERE
            a.sCpZip1 IN ("' . implode('","', $all_zips) . '")
            AND a.sStatus = 1
            AND a.sName NOT LIKE "%業務專用%"
            AND a.sId <> 228
            AND a.sId <> 2297;';
$stores = $conn->all($sql);
// print_r($stores);
echo "total = " . count($stores) . "\n";
// exit;
##

$_fh_time = date("YmdHis");
$fh       = __DIR__ . '/scrivenerDefaultAppend_' . $_fh_time . '.txt';
file_put_contents($fh, '');

$all = [];
foreach ($stores as $k => $store) {
    if (empty(checkException($store['sId']))) {
        continue;
    }

    $store['bank']     = FeedBackData($store['sId'], 1);
    $store['priority'] = empty(checkPriority($store['sId'])) ? 'N' : 'Y';

    file_put_contents($fh, json_encode($store, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
    $all[] = $store;
}
$fh = null;unset($fh);

// print_r($all);
echo 'total(except): ' . count($all) . "\n";
// exit;

$fh = __DIR__ . '/no_bank_for_scrivener_append_' . $_fh_time . '.log';
foreach ($all as $v) {
    try {
        $conn->beginTransaction();

        //adding main record
        $sql = 'INSERT INTO tActivityRecords (aActivityId, aIdentity, aStoreId, aRule, aGift, aPriority) VALUES (2, "S", ' . $v['sId'] . ', 2, 0, "' . $v['priority'] . '");';
        $conn->exeSql($sql);
        ##

        //adding extra record
        $_extRecord = [];
        foreach ($v['bank'] as $_v) {
            $_extRecord[] = [
                "idNo"             => $_v['fIdentityNumber'],
                "bankMain"         => $_v['fAccountNum'],
                "identity"         => $_v['fIdentity'],
                "bankBranch"       => $_v['fAccountNumB'],
                "bankAccount"      => $_v['fAccount'],
                "mailingAddr"      => $_v['fAddrC'],
                "residenceAddr"    => $_v['fAddrR'],
                "mailingAddrZip"   => $_v['fZipC'],
                "bankAccountName"  => $_v['fAccountName'],
                "residenceAddrZip" => $_v['fZipR'],
            ];
        }

        if (empty($_extRecord)) {
            file_put_contents($fh, $v['sId'] . "\n", FILE_APPEND);

            $_extRecord[] = [
                "idNo"             => '',
                "bankMain"         => '',
                "identity"         => '',
                "bankBranch"       => '',
                "bankAccount"      => '',
                "mailingAddr"      => '',
                "residenceAddr"    => '',
                "mailingAddrZip"   => '',
                "bankAccountName"  => '',
                "residenceAddrZip" => '',
            ];
        }

        $json = json_encode($_extRecord, JSON_UNESCAPED_UNICODE);

        $sql = 'INSERT INTO tActivityRecordsExt (aActivityId, aIdentity, aStoreId, aContent) VALUES (2, "S", ' . $v['sId'] . ', "' . addslashes($json) . '");';
        $conn->exeSql($sql);
        ##

        $conn->endTransaction();

        echo $sql . "\n";

        $_extRecord = $json = null;
        unset($_extRecord, $json);
    } catch (Exception $e) {
        $conn->cancelTransaction();

        print_r($v);
        echo $e->getMessage();
        exit;
    }
}
