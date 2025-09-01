<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/sms/sms_function_manually.php';
require_once dirname(__DIR__) . '/class/LineBotRequest.php';

$message = '親愛的第一建經特約地政士～您好，第一建經與第一銀行合作推出「房貸即時通」服務，凡由第一建經承做「價金信託履約保證」之房屋買賣案件（土地除外），皆會於案件建檔完成後，即時透過第一建經AI機器人推播line訊息通知房貸評估結果，方便您更有效率協助客戶評估房貸申請，歡迎多加利用！！';

$conn = new first1DB;

//取得代書名單
$sql = 'SELECT DISTINCT a.sMobile, a.sScrivener, a.sName FROM tScrivenerSms AS a JOIN tTitle_SMS AS b ON a.sNID = b.id WHERE sMobile <> "" AND sNID = 1 AND sDel = 0 order by sMobile asc;';
$rs  = $conn->all($sql);
// print_r($scrivener);exit;
##

//確認是否有Line帳號
$scrivener = [];
$sms       = [];
$line      = [];
foreach ($rs as $v) {
    if (preg_match("/^09\d{8}$/", $v['sMobile'])) {
        echo $v['sMobile'] . ' ... ';
        $scrivener[] = $v;

        $sId = 'SC' . str_pad($v['sScrivener'], 4, '0', STR_PAD_LEFT);
        $tk  = isLineAccount($sId, $v['sMobile']);

        if (empty($tk)) {
            echo "adding to sms list\n";
            $sms[] = $v['sMobile'];
        } else {
            echo "adding to line list\n";

            // $line[] = array_merge($v, ['userId' => $tk]);
            foreach ($tk as $va) {
                $line[] = $va;
            }
        }

        $sId = $tk = null;
        unset($sId, $tk);
    }
}
echo 'scrivener = ' . count($scrivener) . "\n";
##

//簡訊號碼唯一性
echo 'sms = ' . count($sms) . "\n";
$sms = array_values(array_unique($sms));
// print_r($sms);exit;
echo 'sms1 = ' . count($sms) . "\n";
// exit;
##

//Line 唯一性
echo 'line = ' . count($line) . "\n";
// file_put_contents('aa.csv', implode("\n", $line));
$line = array_values(array_unique($line));
// print_r($line);exit;
echo 'line1 = ' . count($line) . "\n";
print_r($line);
exit;
##

//發送簡訊

$sms   = null;
$sms[] = '0922785490';

print_r($sms);exit;
$sender = new SMS_Gateway;
foreach ($sms as $v) {
    echo $k . ' ... ';

    $result = $sender->manual_send($v, $message, "n", '第一建經', '公告', '');
    file_put_contents(__DIR__ . '/notice_sms_' . date("Ymd") . '.log', date("Y-m-d H:i:s") . "\nRequest:\n" . print_r($v, true) . "Response:\n" . print_r($result, true) . "\n", FILE_APPEND);

    echo "done\n";

    $result = null;unset($result);
}
$sender = $sms = null;
unset($sender, $sms);

##

//發送Line
if (!is_dir(dirname(__DIR__) . '/log')) {
    mkdir(dirname(__DIR__) . '/log', 0777, true);
}
$config = [
    'id'          => '1522216771',
    'secret'      => '50864b42986c00ab1ce293d61ffcece5',
    'accessToken' => 'Ua1yf4L4nNHJxICNoGYNSgc0N8lkLoyDaHsdsXDVjCqY7tJgWaGSZODFK+aNYpK7v4KDjFDVpcc3KV3tC71GZE6fiu4Z+7MOAADjvdhvKc9+0H4kfIr6EDPjrxoZd1TleM4ExlAEgelDE6N4KxfumAdB04t89/1O/w1cDnyilFU=',
    'log'         => dirname(__DIR__) . '/log/bot',
];

$line   = null;
$line[] = 'U86db6edf9dd39e60f2615c1eede11617';

$bot = new LineBotRequest($config['id'], $config['secret'], $config['accessToken'], $config['log']);
foreach ($line as $v) {
    echo $v . ' ... ';

    $response['userId']                             = $v;
    $response['messages'][0]['actionType']          = 'image';
    $response['messages'][0]['image']['imageUrl']   = 'https://www.first1.com.tw/images/EDM_page-0001.jpg';
    $response['messages'][0]['image']['previewUrl'] = 'https://www.first1.com.tw/images/EDM_page-0001.jpg';

    $result = $bot->send($response);
    file_put_contents(dirname(__DIR__) . '/log/notice_line_' . date("Ymd") . '.log', date("Y-m-d H:i:s") . "\nRequest:\n" . print_r($v, true) . "Response:\n" . print_r($result, true) . "\n", FILE_APPEND);

    echo "done\n";

    $result = null;unset($result);
}
$sender = $sms = null;
unset($sender, $sms);
##

//確認是否開通Line帳號
function isLineAccount($sId, $mobile)
{
    global $conn;

    if (empty($sId) || empty($mobile)) {
        return false;
    }

    $sql = 'SELECT lLineId FROM tLineAccount WHERE lIdentity = "S" AND lTargetCode = :sId AND lCaseMobile = :mobile AND lStatus = "Y"';
    $rs  = $conn->all($sql, ['sId' => $sId, 'mobile' => $mobile]);

    if (empty($rs)) {
        return false;
    }

    $rs = array_column($rs, 'lLineId');

    $list = [];
    foreach ($rs as $v) {
        if (preg_match("/^U\w{32}$/i", $v)) {
            $list[] = $v;
        }

    }

    return empty($list) ? false : $list;
}
##
