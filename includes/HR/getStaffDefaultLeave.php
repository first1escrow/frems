<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

require_once dirname(dirname(__DIR__)) . '/class/staffDefaultLeave.class.php';

use First1\V1\Staff\StaffDefaultLeave;

header('Content-Type: application/json');

$conn = new First1DB;

$staffDefaultLeave = StaffDefaultLeave::getInstance();
$leaveData         = $staffDefaultLeave->getStaffDefaultLeave();

$data = [];
if (! empty($leaveData)) {
    foreach ($leaveData as $list) {
        $content = [];

        foreach ($list as $v) {
            if (! empty($v['sLeaveDefault'])) {
                $content[] = $v['sLeaveName'] . '：' . $v['sLeaveDefault'] . '小時、剩餘 ' . $v['sLeaveBalance'] . '小時';
            }
        }

        $data[] = [
            'id'        => $v['sStaffId'],
            'staffName' => $v['sStaffName'],
            'content'   => implode('<br>', $content),
        ];
    }
}

exit(json_encode(['data' => $data], JSON_UNESCAPED_UNICODE));
