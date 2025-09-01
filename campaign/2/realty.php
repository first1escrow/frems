<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/sms/sms_function_v2.php';

/**
 * 基隆區回饋對象：仲介
 * 宜花區回饋對象：仲介
 *
 * no: 63
 * has: 303
 * total: 412
 */

//排除仲介店家
function checkException($bId)
{
    $except_stores = [982, 985, 986, 987, 988, 989, 4699, 926, 1732, 924, 928, 5483]; //排除店家名單

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
$zips = null;unset($zips);

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
$stores = $conn->all($sql);
echo 'total: ' . count($stores) . "\n";
##

$no_phone   = $no_phone_extra   = $phone   = $data   = $data1   = [];
$except_idx = 0;
foreach ($stores as $k => $store) {
    if (empty(checkException($store['bId']))) {
        $except_idx++;
        continue;
    }

    $store['sales'] = getSales($store['bId']);

    //
    $sql = 'SELECT
                a.bName,
                a.bMobile,
                b.tTitle
            FROM
                tBranchSms AS a
            JOIN
                tTitle_SMS AS b ON a.bNID = b.id
            WHERE
                a.bBranch = ' . $store['bId'] . ' AND a.bNID IN (12, 13, 26, 27) AND a.bDel = 0;';
    $rs = $conn->all($sql);

    if (empty($rs)) {
        $no_phone[] = [
            'bId'   => $store['bId'],
            'bCode' => $store['bCode'],
            'store' => $store['bName'] . $store['bStore'],
            'sales' => $store['sales'],
        ];
    } else {
        $_receiver = $_arr = [];
        foreach ($rs as $va) {
            if (preg_match("/^09\d{8}$/", $va['bMobile'])) {
                $_receiver[] = $va;
                // $_arr[$va['bMobile']] = $va; //重複號碼會覆蓋過去
                $data1[$va['bMobile']] = $va;
            }
        }

        if (empty($_receiver)) {
            $no_phone[] = [
                'bId'   => $store['bId'],
                'bCode' => $store['bCode'],
                'store' => $store['bName'] . $store['bStore'],
                'sales' => $store['sales'],
            ];

            $no_phone_extra[] = [
                'bId'   => $store['bId'],
                'bCode' => $store['bCode'],
                'store' => $store['bName'] . $store['bStore'],
                'sales' => $store['sales'],
            ];

        } else {
            $phone[] = [
                'bId'       => $store['bId'],
                'bCode'     => $store['bCode'],
                'store'     => $store['bName'] . $store['bStore'],
                'sales'     => $store['sales'],
                'receivers' => $_receiver,
            ];

            $data = array_merge($data, $_receiver);
            // $data1 = array_merge($data1, $_arr);
        }

        $_receiver = $_arr = null;
        unset($_receiver, $_arr);
    }
    ##
}

echo 'no  phone count: ' . count($no_phone) . "\n";
echo 'has phone count: ' . count($phone) . "\n";
echo 'except store count: ' . $except_idx . "\n";
echo 'ready for send: ' . count($data) . "\n";
echo 'ready for send(no duplication): ' . count($data1) . "\n";

//no phone no
$fh = __DIR__ . '/no_phone_number_' . date("Ymd") . '.csv';
file_put_contents($fh, "\xEF\xBB\xBF");
file_put_contents($fh, '店家,店名稱,負責業務' . "\n", FILE_APPEND);
foreach ($no_phone as $v) {
    file_put_contents($fh, $v['bCode'] . str_pad($v['bId'], 5, '0', STR_PAD_LEFT) . ',' . $v['store'] . ',' . $v['sales'] . "\n", FILE_APPEND);
}

//targets
$fh = __DIR__ . '/targets_' . date("Ymd") . '.csv';
file_put_contents($fh, "\xEF\xBB\xBF");
file_put_contents($fh, '店家,職稱,姓名,電話,' . "\n", FILE_APPEND);
foreach ($phone as $v) {
    $code = str_pad($v['bId'], 5, '0', STR_PAD_LEFT) . ',';
    foreach ($v['receivers'] as $va) {
        file_put_contents($fh, $v['bCode'] . str_pad($v['bId'], 5, '0', STR_PAD_LEFT) . ',' . $va['tTitle'] . ',' . $va['bName'] . ',' . $va['bMobile'] . "\n", FILE_APPEND);
    }
}
exit;
// $data1 = [
//     ['bMobile' => '0922785490', 'bName' => '陳銘慶', 'tTitle' => '店長'],
// ];
// $data1 = [
//     ['bMobile' => '0919200247', 'bName' => '吳珮琦', 'tTitle' => '店長'],
// ];
print_r(array_values($data1));exit;

$sms_txt = '第一建經通知:為感謝全體房仲夥伴的支持與肯定，特舉辦：「2023兔飛猛進、我要第一！」履保進案贈獎活動，活動辦法短連結 https://www.first1.com.tw/images/r_page-0001.jpg ' . "\r\n";

// $sms = new SMS_Gateway_V2;
foreach ($data1 as $v) {
    echo $v['bName'] . $v['tTitle'] . '(' . $v['bMobile'] . ') ==> ';
    // print_r($sms->sms_send($v['bMobile'], $v['bName'] . $v['tTitle'], $sms_txt, '2023鴻兔大展'));
    echo "done！\n";
}

echo "\nDone!!\n";
