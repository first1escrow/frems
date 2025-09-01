<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

header('Content-Type: application/json');

if (empty($_POST['branch'])) {
    exit;
}

$branches = explode(',', $_POST['branch']);

$conn = new first1DB;

$list = [];
foreach ($branches as $branch) {
    $sql = 'SELECT sId, sOffice FROM tScrivener WHERE sId = :sId;';
    $rs  = $conn->one($sql, ['sId' => $branch]);
    if (!empty($rs)) {
        $list[] = $rs;
    }

    $sql = $rs = null;
    unset($sql, $rs);
}

exit(json_encode($list, JSON_UNESCAPED_UNICODE));
