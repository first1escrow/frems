<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(__DIR__) . '/incomeFunction.php';

//發送簡訊
function sendSMS($targetType, $cId, $expenseId, $serials, $mobiles, $titles, $names, $contents)
{
    $path = dirname(dirname(__DIR__)) . '/log/sms/v1/shell/income';
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
    $selected_log = $path . '/selectedIncomeSMS_' . date('Ymd') . '.log';
    $log          = $path . '/incomeSMS_' . date('Ymd') . '.log';

    foreach ($serials as $serial) {
        if (empty($serial)) {
            continue;
        }

        $mobile = empty($mobiles[$serial][0]) ? '' : $mobiles[$serial][0];
        $title  = empty($titles[$serial][0]) ? '' : $titles[$serial][0];
        $name   = empty($names[$serial][0]) ? '' : $names[$serial][0];

        file_put_contents($selected_log, date('Y-m-d H:i:s') . ' serial: ' . $serial . ', mobile: ' . $mobile . ', title: ' . $title . ', name: ' . $name . PHP_EOL, FILE_APPEND);

        if (! empty($contents) && ! empty($mobile)) {
            foreach ($contents as $content) {
                if (empty($content)) {
                    continue;
                }

                $data = [
                    'cId'       => $cId,
                    'expenseId' => $expenseId,
                    'serial'    => $serial,
                    'target'    => $targetType,
                    'title'     => $title,
                    'name'      => $name,
                    'mobile'    => $mobile,
                    'content'   => $content,
                ];

                $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                $data = base64_encode($data);

                $cmd = '/usr/bin/php -f ' . dirname(dirname(__DIR__)) . '/shell/income/incomeSMS.php ' . $data . ' > /dev/null 2>&1 &';
                file_put_contents($log, date('Y-m-d H:i:s') . ' ' . $cmd . PHP_EOL, FILE_APPEND);

                shell_exec($cmd);
            }
        }
    }
}

$cId = empty($_POST['cId']) ? null : $_POST['cId'];
if (empty($cId)) {
    http_response_code(400);
    exit('無保證號碼資訊');
}

$expenseId = empty($_POST['expenseId']) ? null : $_POST['expenseId'];
if (empty($expenseId)) {
    http_response_code(400);
    exit('無入帳資訊');
}

$targetType = empty($_POST['targetType']) ? null : $_POST['targetType'];
if (empty($targetType)) {
    http_response_code(400);
    exit('無法確認入款簡訊類別(targetType)');
}

//賣方簡訊
if (! empty($_POST['owner_serial'])) {
    $serials  = $_POST['owner_serial'];
    $mobiles  = empty($_POST['owner_mobile']) ? [] : $_POST['owner_mobile'];
    $titles   = empty($_POST['owner_title']) ? [] : $_POST['owner_title'];
    $names    = empty($_POST['owner_name']) ? [] : $_POST['owner_name'];
    $contents = empty($_POST['owner_content']) ? [] : $_POST['owner_content'];

    sendSMS($targetType, $cId, $expenseId, $serials, $mobiles, $titles, $names, $contents);
}

//賣方店東簡訊
if (! empty($_POST['ownerBoss_serial'])) {
    $serials  = $_POST['ownerBoss_serial'];
    $mobiles  = empty($_POST['ownerBoss_mobile']) ? [] : $_POST['ownerBoss_mobile'];
    $titles   = empty($_POST['ownerBoss_title']) ? [] : $_POST['ownerBoss_title'];
    $names    = empty($_POST['ownerBoss_name']) ? [] : $_POST['ownerBoss_name'];
    $contents = empty($_POST['ownerBoss_content']) ? [] : $_POST['ownerBoss_content'];

    sendSMS($targetType, $cId, $expenseId, $serials, $mobiles, $titles, $names, $contents);
}

//買方簡訊
if (! empty($_POST['buyer_serial'])) {
    $serials  = $_POST['buyer_serial'];
    $mobiles  = empty($_POST['buyer_mobile']) ? [] : $_POST['buyer_mobile'];
    $titles   = empty($_POST['buyer_title']) ? [] : $_POST['buyer_title'];
    $names    = empty($_POST['buyer_name']) ? [] : $_POST['buyer_name'];
    $contents = empty($_POST['buyer_content']) ? [] : $_POST['buyer_content'];

    sendSMS($targetType, $cId, $expenseId, $serials, $mobiles, $titles, $names, $contents);
}

//買方店東簡訊
if (! empty($_POST['buyerBoss_serial'])) {
    $serials  = $_POST['buyerBoss_serial'];
    $mobiles  = empty($_POST['buyerBoss_mobile']) ? [] : $_POST['buyerBoss_mobile'];
    $titles   = empty($_POST['buyerBoss_title']) ? [] : $_POST['buyerBoss_title'];
    $names    = empty($_POST['buyerBoss_name']) ? [] : $_POST['buyerBoss_name'];
    $contents = empty($_POST['buyerBoss_content']) ? [] : $_POST['buyerBoss_content'];

    sendSMS($targetType, $cId, $expenseId, $serials, $mobiles, $titles, $names, $contents);
}

//地政士簡訊
if (! empty($_POST['scrivener_serial'])) {
    $serials  = $_POST['scrivener_serial'];
    $mobiles  = empty($_POST['scrivener_mobile']) ? [] : $_POST['scrivener_mobile'];
    $titles   = empty($_POST['scrivener_title']) ? [] : $_POST['scrivener_title'];
    $names    = empty($_POST['scrivener_name']) ? [] : $_POST['scrivener_name'];
    $contents = empty($_POST['scrivener_content']) ? [] : $_POST['scrivener_content'];

    sendSMS($targetType, $cId, $expenseId, $serials, $mobiles, $titles, $names, $contents);
}
