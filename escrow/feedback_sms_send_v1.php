<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

//確認輸入
$msg  = trim($_POST['msg']);
$bId  = trim($_POST['branch']);
$send = trim($_POST['send']);
$cat  = trim($_POST['cat']);
$uuid = trim($_POST['uuid']);
##

//1:寄送、2:顯示寄送者
if ($send == 1) {
    require_once dirname(__DIR__) . '/sms/sms_function.php';
    $sms = new SMS_Gateway();

    $mag = [];
    if ($cat != 1) {
        // $mag = $sms->send('', '', $bId, '回饋金', '', 'y', 0, $msg);
    }

    if (count($mag) <= 0) {
        echo true;
    }
} else {
    $target = ($cat == 1) ? '回饋金2' : '回饋金';

    if ($cat == 0) {
        require_once dirname(__DIR__) . '/sms/sms_function.php';

        $sms = new SMS_Gateway();
        $tmp = $sms->send('', '', $bId, '回饋金', '', 'n', 0, $msg);

        for ($i = 0; $i < count($tmp); $i++) {
            $tbl .= '<tr name="b' . $bId . '">';
            $tbl .= '<td>' . $tmp[$i]["brand"] . $tmp[$i]["bStore"] . '</td>';
            $tbl .= '<td>' . $tmp[$i]['title'] . '</td>';
            $tbl .= '<td>' . $tmp[$i]["mName"] . '</td>';
            $tbl .= '<td>' . $tmp[$i]["mMobile"] . '</td>';
            $tbl .= '</tr>';

            if ($cat == 1) {
                $tbl .= '<tr name="b' . $bId . '">';
                $tbl .= '<td colspan="4" style="word-wrap:break-word; ">' . $tmp[$i]["smsTxt"] . '</td>';
                $tbl .= '</tr>';
            }
        }

        exit($tbl);
    }

}

exit;
