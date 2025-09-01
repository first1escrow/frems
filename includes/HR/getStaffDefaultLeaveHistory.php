<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

require_once dirname(dirname(__DIR__)) . '/class/staffDefaultLeave.class.php';

use First1\V1\Staff\StaffDefaultLeave;

header('Content-Type: application/json');

$pId = empty($_POST['pId']) ? null : $_POST['pId'];

$staffDefaultLeave = StaffDefaultLeave::getInstance();
$detail            = $staffDefaultLeave->getStaffDefaultLeaveHistory($pId);

$data = [];
if (! empty($detail)) {
    foreach ($detail as $key => $value) {
        if (! empty($value['current'])) {
            foreach ($value['current'] as $k => $v) {
                $row[$k]['staffId']        = $v['sStaffId'];
                $row[$k]['staffName']      = $value['name'];
                $row[$k]['staffDept']      = $value['dept'];
                $row[$k]['leaveId']        = $v['sLeaveId'];
                $row[$k]['leaveName']      = $v['leaveName'];
                $row[$k]['currentDate']    = $v['sDate'];
                $row[$k]['currentDefault'] = $v['sLeaveDefault'];
                $row[$k]['currentBalance'] = $v['sLeaveBalance'];
                $row[$k]['lastDate']       = '-';
                $row[$k]['lastDefault']    = '-';
                $row[$k]['lastBalance']    = '-';
            }
        }

        if (! empty($value['last'])) {
            foreach ($value['last'] as $k => $v) {
                $row[$k]['lastDate']    = $v['sDate'];
                $row[$k]['lastDefault'] = $v['sLeaveDefault'];
                $row[$k]['lastBalance'] = $v['sLeaveBalance'];
            }
        }

        $data = array_merge($data, $row);
    }
}

exit(json_encode(['data' => $data], JSON_UNESCAPED_UNICODE));
