<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

//確認輸入
$msg   = trim($_POST['msg']);
$bId   = trim($_POST['branch']);
$cat   = trim($_POST['cat']);
$batch = trim($_POST['uuid']);
##

//2022-07-04
if ($cat == 1) {
    $target = '回饋金2';

    $vars = base64_encode(json_encode(['member' => $_SESSION['member_id'], 'batch' => $batch, 'bid' => $bId, 'target' => $target, 'msg' => $msg]));
    $cmd  = 'nohup /usr/bin/php -f ' . __DIR__ . '/feedback_sms_generator.php ' . $vars . ' > /dev/null &';
    shell_exec($cmd);
}
exit;
