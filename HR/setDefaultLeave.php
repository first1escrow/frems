<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/staffDefaultLeave.class.php';

use First1\V1\Staff\StaffDefaultLeave;

$log = new TraceLog();

$id    = empty($_GET['id']) ? null : $_GET['id'];
$alert = '';

$conn = new First1DB;

if (! empty($_POST['sStaffId']) && is_numeric($_POST['sStaffId'])) {
    $log->log($_SESSION['member_id'], json_encode($_POST, JSON_UNESCAPED_UNICODE), '設定預設假別', 'insert/update');

    $values = [];
    if (! empty($_POST['sLeaveId'])) {
        foreach ($_POST['sLeaveId'] as $key => $value) {
            $_POST['sLeaveDefault'][$key] = empty($_POST['sLeaveDefault'][$key]) ? 0 : $_POST['sLeaveDefault'][$key];
            $_POST['sLeaveBalance'][$key] = empty($_POST['sLeaveBalance'][$key]) ? 0 : $_POST['sLeaveBalance'][$key];
            $_POST['sLeaveRemark'][$key]  = empty($_POST['sLeaveRemark'][$key]) ? null : $_POST['sLeaveRemark'][$key];

            $values[] = '(' . $_POST['sStaffId'] . ', ' . $value . ', ' . $_POST['sLeaveDefault'][$key] . ', ' . $_POST['sLeaveBalance'][$key] . ', "' . addslashes($_POST['sLeaveRemark'][$key]) . '")';
        }
    }

    if (! empty($values)) {
        $sql = 'INSERT INTO
                    tStaffLeaveDefault
                (
                    sStaffId,
                    sLeaveId,
                    sLeaveDefault,
                    sLeaveBalance,
                    sLeaveRemark
                ) VALUES ' . implode(', ', $values) . '
                ON DUPLICATE KEY UPDATE
                    sLeaveDefault = VALUES(sLeaveDefault),
                    sLeaveBalance = VALUES(sLeaveBalance),
                    sLeaveRemark = VALUES(sLeaveRemark);';
        if ($conn->exeSql($sql)) {
            $leaveData = $_POST;
            require_once dirname(__DIR__) . '/includes/HR/recordDefaultLeaveHistory.php';
            $leaveData = null;

            $alert = 'alert("儲存完成");';
        } else {
            $alert = 'alert("儲存失敗");';
        }
    }
}

$log->log($_SESSION['member_id'], json_encode($_GET, JSON_UNESCAPED_UNICODE), '查詢預設假別-指定對象', 'select');

$data = [];
if (! empty($id) && is_numeric($id)) {
    $sql = 'SELECT
                sId,
                sStaffId,
                (SELECT pName FROM tPeopleInfo WHERE pId = a.sStaffId) AS sStaffName,
                sLeaveId,
                (SELECT CASE WHEN sMemo IS NULL OR sMemo = "" THEN sLeaveName ELSE sMemo END AS leaveName FROM tStaffLeaveType WHERE sId = a.sLeaveId) AS sLeaveName,
                sLeaveDefault,
                sLeaveBalance,
                sLeaveRemark
            FROM
                tStaffLeaveDefault AS a
            WHERE
                sStaffId = :id';
    $data = $conn->all($sql, ['id' => $id]);
}

$leaves = StaffDefaultLeave::getInstance()->getDefaultLeaveDetail();
$log->log($_SESSION['member_id'], json_encode($leaves, JSON_UNESCAPED_UNICODE), '查詢預設假別', 'select');

$rows = [];
foreach ($leaves as $leave) {
    $leave['sLeaveName'] = (in_array($leave['sId'], [1, 2])) ? $leave['sMemo'] : $leave['sLeaveName'];

    $row = [
        'sStaffId'      => $id,
        'sStaffName'    => null,
        'sLeaveId'      => $leave['sId'],
        'sLeaveName'    => $leave['sLeaveName'],
        'sLeaveDefault' => 0,
        'sLeaveBalance' => 0,
        'sLeaveRemark'  => null,
    ];

    if (! empty($data)) {
        foreach ($data as $v) {
            if ($v['sLeaveId'] == $leave['sId']) {
                $row['sStaffName']    = $v['sStaffName'];
                $row['sLeaveId']      = $v['sLeaveId'];
                $row['sLeaveName']    = $v['sLeaveName'];
                $row['sLeaveDefault'] = $v['sLeaveDefault'];
                $row['sLeaveBalance'] = $v['sLeaveBalance'];
                $row['sLeaveRemark']  = $v['sLeaveRemark'];
                break;
            }
        }
    }

    $rows[] = $row;
}

$staffs = StaffDefaultLeave::getInstance()->getStaffIds();

$smarty->assign('staffs', $staffs);
$smarty->assign('alert', $alert);
$smarty->assign('data', $rows);
$smarty->display('setDefaultLeave.inc.tpl', '', 'HR');
