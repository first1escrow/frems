<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/sms/sms_function_v2.php';
require_once dirname(dirname(__DIR__)) . '/includes/LineBotRequest.php';

/**
 * 北北區回饋對象：地政士
 * 桃園區回饋對象：地政士
 * 屏澎區回饋對象：地政士
 * 雲嘉南回饋對象：地政士
 * 中彰投回饋對象：地政士
 * 竹苗區回饋對象：地政士
 * 台東區回饋對象：地政士
 * 高雄區回饋對象：地政士
 *
 * no:
 * has:
 * total:
 */

//排除地政士店家
function checkException($sId)
{
    //共 122 家 (SC0350 改成 SC0850)
    $except_stores = [2538, 2295, 2245, 850, 363, 179, 169, 564, 994, 951, 945, 781, 1815, 83, 1589, 1196, 286, 1100, 1268, 272, 1463, 1614, 958, 593, 1426, 1028, 293, 2811, 2575, 2037, 1951, 610, 395, 2218, 1155, 1876, 464, 701, 2103, 137, 2190, 902, 558, 1111, 1891, 2738, 2573, 1555, 1147, 562, 376, 1501, 1868, 1634, 2254, 430, 115, 1325, 1494, 1557, 1297, 2183, 2544, 1328, 700, 549, 373, 1492, 1230, 134, 1511, 314, 2173, 1720, 1250, 634, 1013, 2833, 2589, 1837, 813, 476, 445, 1133, 1132, 1928, 2807, 2009, 1575, 1854, 288, 2203, 1924, 886, 2233, 1583, 1598, 688, 894, 883, 1579, 2054, 125, 2617, 578, 1587, 2789, 2656, 2144, 1170, 2293, 135, 742, 461, 1966, 478, 1437, 1686, 1811, 2029, 1636, 2778]; //排除店家名單

    //20230224 新增加 30 家
    $except_stores2 = [1, 2, 3, 4, 7, 8, 271, 337, 9, 2164, 10, 11, 13, 14, 15, 16, 17, 19, 232, 270, 283, 311, 12, 762, 21, 22, 23, 25, 26, 432];

    $except_stores = array_merge($except_stores, $except_stores2);
    return in_array($sId, $except_stores) ? false : true;
}
##

//確認地政士是否有綁定 Line 帳號
function hasLine($sId)
{
    global $conn;

    $sql = 'SELECT lLineId as userId FROM tLineAccount WHERE lIdentity = "S" AND lTargetCode = "' . $sId . '" AND lStage1Auth = "Y" AND lStage2Auth = "Y" AND lStatus = "Y";';
    $rs  = $conn->all($sql);

    return empty($rs) ? false : $rs;
}
##

//取得所屬業務
function getSales($sId)
{
    global $conn;

    $sql = 'SELECT
                b.pName
            FROM
                tScrivenerSalesForPerformance AS a
            JOIN
                tPeopleInfo AS b ON a.sSales = b.pId
            WHERE
                a.sScrivener = "' . $sId . '"
            ';
    $rs = $conn->all($sql);

    $rs = array_column($rs, 'pName');

    return empty($rs) ? '' : implode('_', $rs);
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
            AND a.sName NOT LIKE "%(未)%"
            AND a.sName NOT LIKE "%業務專用%"
            AND a.sId <> 228
            AND a.sId <> 2297;';
$stores = $conn->all($sql);
echo "total = " . count($stores) . "\n";
##

$no_phone = $phone = $all_phone_data = [];
$no_line  = $has_line  = $all_line_data  = [];

$max_line_limit = 1000;
$line_count     = 0;
$except_idx     = 0;
foreach ($stores as $k => $store) {
    if (empty(checkException($store['sId']))) {
        $except_idx++;
        continue;
    }

    $store['code']  = 'SC' . str_pad($store['sId'], 4, '0', STR_PAD_LEFT);
    $store['sales'] = getSales($store['sId']);

    //check line account usage
    $userId = hasLine($store['code']);

    if (!empty($userId) && ($line_count < $max_line_limit)) { //有綁定 Line 且待發送帳號數 < 1000
        $line_count += count($userId);

        $store['line'] = array_column($userId, 'userId');
        $has_line[]    = $store;

        $all_line_data = array_merge($all_line_data, $store['line']);
    } else { //沒有綁定
        $no_line[] = $store;

        if (preg_match("/^09\d{8}$/", $store['sMobileNum'])) {
            $phone[]                              = $store;
            $all_phone_data[$store['sMobileNum']] = $store; //重複號碼會覆蓋過去
        } else {
            $no_phone[] = $store;
        }
    }
    ##
}

echo 'no  line count: ' . count($no_line) . "\n";
echo 'has line count: ' . count($has_line) . "\n";
echo 'no  phone count: ' . count($no_phone) . "\n";
echo 'has phone count: ' . count($phone) . "\n";
echo 'except store count: ' . $except_idx . "\n";
echo 'line_count: ' . $line_count . "\n";

//line
sort($all_line_data);
echo 'all_line_data = ' . count($all_line_data) . "\n";

$all_line_data = array_unique($all_line_data);
echo 'all_line_data(filter) = ' . count($all_line_data) . "\n";
##

//phone
echo 'has phone count(filter): ' . count($all_phone_data) . "\n";
##
// exit;
//no phone number log
$fh = __DIR__ . '/scrivener_no_phone_number_' . date("Ymd") . '.csv';
file_put_contents($fh, "\xEF\xBB\xBF");
file_put_contents($fh, '代碼,地政士,負責業務' . "\n", FILE_APPEND);
foreach ($no_phone as $v) {
    file_put_contents($fh, $v['code'] . ',' . $v['sName'] . ',' . $v['sales'] . "\n", FILE_APPEND);
}
##

//targets log
$fh = __DIR__ . '/scrivener_targets_' . date("Ymd") . '.csv';
file_put_contents($fh, "\xEF\xBB\xBF");
file_put_contents($fh, '代碼,姓名,電話,Line' . "\n", FILE_APPEND);

foreach ($has_line as $v) { //LINE
    foreach ($v['line'] as $va) {
        file_put_contents($fh, $v['code'] . ',' . $v['sName'] . ',' . $v['sMobileNum'] . ',' . $va . "\n", FILE_APPEND);
    }
}

foreach ($phone as $v) { //SMS
    file_put_contents($fh, $v['code'] . ',' . $v['sName'] . ',' . $v['sMobileNum'] . ',' . "\n", FILE_APPEND);
}
##
exit;
//sending with line
// $all_line_data = ['U86db6edf9dd39e60f2615c1eede11617'];
// $all_line_data = ['U62072f7646730891bfefc9a26fbe850b']; //佩琦
// print_r($all_line_data);exit;

$channel_id           = '1522216771';
$channel_secret       = '50864b42986c00ab1ce293d61ffcece5';
$channel_access_token = 'Ua1yf4L4nNHJxICNoGYNSgc0N8lkLoyDaHsdsXDVjCqY7tJgWaGSZODFK+aNYpK7v4KDjFDVpcc3KV3tC71GZE6fiu4Z+7MOAADjvdhvKc9+0H4kfIr6EDPjrxoZd1TleM4ExlAEgelDE6N4KxfumAdB04t89/1O/w1cDnyilFU=';

// $bot = new LineBotRequest($channel_id, $channel_secret, $channel_access_token);
foreach ($all_line_data as $v) {
    $response           = [];
    $response['userId'] = $v;

    $response['messages'][0]['actionType'] = 'text';
    $response['messages'][0]['text']       = '第一建經通知:為感謝全體特約地政士的支持與肯定，特辦：「2023鴻兔大展、我要第一！」履保進案贈獎活動。';

    $response['messages'][1]['actionType']          = 'image';
    $response['messages'][1]['image']['imageUrl']   = 'https://www.first1.com.tw/images/s_page-0001.jpg';
    $response['messages'][1]['image']['previewUrl'] = 'https://www.first1.com.tw/images/s_page-0001.jpg';

    echo $v . ' ==> ';
    // print_r($bot->send($response));

    echo " done！\n";
}
##

//sending with sms
// $all_phone_data = [
//     ['sMobileNum' => '0922785490', 'sName' => '陳銘慶地政士'],
// ];
// $all_phone_data = [
//     ['sMobileNum' => '0919200247', 'sName' => '吳珮琦地政士'],
// ];
// print_r($all_phone_data);exit;

$sms_txt = '第一建經通知:為感謝全體特約地政士的支持與肯定，特辦：「2023鴻兔大展、我要第一！」履保進案贈獎活動，活動辦法短連結 https://www.first1.com.tw/images/s_page-0001.jpg ' . "\r\n";

// $sms = new SMS_Gateway_V2;
foreach ($all_phone_data as $v) {
    echo $v['sName'] . '(' . $v['sMobileNum'] . ') ==> ';

    // print_r($sms->sms_send($v['sMobileNum'], $v['sName'], $sms_txt, '2023鴻兔大展'));
    echo "done！\n";
}
##

echo "\nDone!!\n";
