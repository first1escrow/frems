<?php
require_once dirname(dirname(__DIR__)) . '/.env.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/sms/SMS.class.php';

use First1\V1\SMS\SMS;

$log = dirname(dirname(__DIR__)) . '/log/sms/v1/shell/income';
if (! is_dir($log)) {
    mkdir($log, 0777, true);
}
$log .= '/incomeSMS_' . date('Ymd') . '.log';

// $argv[1] = 'eyJjSWQiOiIxMzAxNjA1MDQiLCJleHBlbnNlSWQiOiIxMzQ5NTI0IiwidGFyZ2V0IjoiaW5jb21lMiIsInRpdGxlIjoi5bqX6ZW3IiwibmFtZSI6Iue+heiJtyIsIm1vYmlsZSI6IjA5ODI2Mjg1MjQiLCJjb250ZW50Ijoi56ys5LiA5bu657aT5L+h6KiX5bGl57SE5L+d6K2J5bCI5oi25bey5pa8MuaciDE35pel5pS25Yiw5L+d6K2J57eo6JmfMTMwMTYwNTA077yI6LK35pa5546L5L+K6ZqG6LOj5pa56Zmz6I6b56mO77yJ5a2Y5YWlMzY2MzM05YWD77yI6KqN5YiXOuWwvuasvuW3rumhjTQwMDAwK+WNsOiKseeohTI1MDAr5aWR56iFNTQyOTQr6LK35pa56aCQ5pS25qy+6aCFMzAwMDAr6LK35pa55bGl5L+d6LK7MzU0MCvosrfmlrnku7Lku4vmnI3li5nosrsyMzYwMDDlhYMpO+WPsOS4reW4guWMl+Wxr+WNgOeSsOWkquadsei3rzU2NuiZn+WNgeS4ieaok+S5izEifQ==';
$data = base64_decode($argv[1]);
$data = json_decode($data, true);

file_put_contents($log, date('Y-m-d H:i:s') . PHP_EOL . ' argv: ' . print_r($argv, true) . PHP_EOL . 'data: ' . print_r($data, true) . PHP_EOL, FILE_APPEND);

/** 簡訊發送清單 */
$sms      = new SMS(new First1DB);
$response = $sms->send([
    'cId'       => $data['cId'],
    'expenseId' => $data['expenseId'],
    'target'    => $data['target'],
    'title'     => $data['title'],
    'name'      => $data['name'],
    'mobile'    => $data['mobile'],
    'content'   => $data['content'],
]);

header('Content-Type: application/json');
exit($response);
