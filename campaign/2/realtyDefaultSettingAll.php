<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

/**
 * 基隆區回饋對象：仲介
 * 宜花區回饋對象：仲介
 */

//feedback data
function FeedBackData($id, $type)
{
    global $conn;

    $sql = "SELECT * FROM tFeedBackData WHERE fType ='" . $type . "' AND fStoreId ='" . $id . "' AND fStatus = 0 AND fStop = 0;";
    return $conn->all($sql);
}
##

//排除仲介店家
function checkException($bId)
{
    $except_stores = [982, 985, 986, 987, 988, 989, 4699, 926, 1732, 924, 928, 5483]; //排除店家名單(12家)

    return in_array($bId, $except_stores) ? false : true;
}
##

//取得所屬業務
function getSales($bId)
{
    global $conn;

    $sql = 'SELECT
                b.pName
            FROM
                tBranchSalesForPerformance AS a
            JOIN
                tPeopleInfo AS b ON a.bSales = b.pId
            WHERE
                a.bBranch = "' . $bId . '"
            ';
    $rs = $conn->all($sql);

    $rs = array_column($rs, 'pName');

    return empty($rs) ? '' : implode('_', $rs);
}
##

$conn = new first1DB;

$sql = 'SELECT zZip, zCity FROM tZipArea WHERE zCity IN ("基隆市", "宜蘭縣", "花蓮縣");';
$rs  = $conn->all($sql);

$zips = $all_zips = [];
foreach ($rs as $v) {
    $zips[$v['zCity']][] = $v['zZip']; //by city
    $all_zips[]          = $v['zZip']; //all
}

//get realty by zip
$sql = 'SELECT
            a.bId,
            a.bStore,
            b.bCode,
            b.bName
        FROM
            tBranch as a
        JOIN
            tBrand as b on a.bBrand = b.bId
        WHERE
            a.bZip IN (' . implode(',', $all_zips) . ')
            AND a.bBrand <> 1
            AND a.bBrand <> 49
            AND a.bStatus = 1
        UNION
        SELECT
            a.bId,
            a.bStore,
            b.bCode,
            b.bName
        FROM
            tBranch as a
        JOIN
            tBrand as b on a.bBrand = b.bId
        WHERE
            a.bId IN (4416, 3345, 3324, 5754, 3381, 3122, 5841, 5650, 2333, 5758, 1554, 1613, 2574, 1759, 1574, 3164, 5707, 3088, 3089, 4579, 4580, 3081, 5061, 5597, 5327, 5677, 5658, 2086, 1348, 1410, 3039, 4227, 3482, 1241, 2476, 5408, 5843, 2536, 5412, 4004, 5531, 2549, 3978, 3979, 3406, 3838, 3443, 1920, 3839, 2282, 1570, 2484, 4738, 3751, 4739, 3545, 3564, 3565, 4869, 4364, 3605, 4360)
            AND a.bBrand <> 1
            AND a.bBrand <> 49
            AND a.bStatus = 1;';
$stores = $conn->all($sql); //加62家
// print_r($stores);exit;
echo 'total: ' . count($stores) . "\n";
##

$_fh_time = date("YmdHis");
$fh       = __DIR__ . '/realtyDefaultAppend_' . $_fh_time . '.txt';
file_put_contents($fh, '');

$all = [];
foreach ($stores as $k => $store) {
    if (empty(checkException($store['bId']))) {
        continue;
    }

    $store['bank'] = FeedBackData($store['bId'], 2);

    file_put_contents($fh, json_encode($store, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
    $all[] = $store;
}
$fh = null;unset($fh);

// print_r($all);
echo 'total: ' . count($all) . "\n";

$fh = __DIR__ . '/no_bank_for_realty_append_' . $_fh_time . '.log';
foreach ($all as $v) {
    try {
        $conn->beginTransaction();

        //adding main record
        $sql = 'INSERT INTO tActivityRecords (aActivityId, aIdentity, aStoreId, aRule, aGift) VALUES (2, "R", ' . $v['bId'] . ', 2, 0);';
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
            file_put_contents($fh, $v['bId'] . "\n", FILE_APPEND);

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

        $sql = 'INSERT INTO tActivityRecordsExt (aActivityId, aIdentity, aStoreId, aContent) VALUES (2, "R", ' . $v['bId'] . ', "' . addslashes($json) . '");';
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
