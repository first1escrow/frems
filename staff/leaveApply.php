<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/includes/staff/leaveConfig.php';
require_once dirname(__DIR__) . '/includes/staff/leaveFunction.php';

//確認日期是否已被鎖定
function getLastLockDate($conn, $from, $to)
{
    $sql      = 'SELECT sDate FROM tStaffLockDate WHERE 1 ORDER BY sDate DESC LIMIT 1;';
    $lockDate = $conn->one($sql)['sDate'];

    return (($from <= $lockDate) || ($to <= $lockDate)) ? true : false;
}

if ($_POST) {
    //20250328 確認申請日期是否已被鎖定
    if (! empty($_POST['date-from']) && ! empty($_POST['date-to'])) {
        if (getLastLockDate(new first1DB, $_POST['date-from'], $_POST['date-to'])) {
            breakOut('日期已被鎖定，無法申請');
            exit;
        }
    }

    require_once dirname(__DIR__) . '/includes/staff/sendLeaveApply.php';

    echo '
        <script>
            alert("申請已送出");
            window.location.href = "myLeave.php";
            // parent.location.reload();
            parent.refresh("leave");
        </script>
    ';
    exit;
}

$conn = new first1DB;

$sql = 'SELECT
            sLeaveId,
            (SELECT IF(sMemo IS NULL, sLeaveName, sMemo) FROM tStaffLeaveType WHERE sId = a.sLeaveId) AS leaveName,
            sLeaveDefault,
            sLeaveBalance,
            sLeaveRemark
        FROM
            tStaffLeaveDefault AS a
        WHERE
            sStaffId = ' . $_SESSION['member_id'] . '
            AND (sLeaveDefault != 0 OR sLeaveBalance != 0);';
$leaveTypes = $conn->all($sql);

$sql = 'SELECT sId, sType, sLeaveName, sMemo FROM tStaffLeaveType WHERE 1 ORDER BY sId;';
$rs  = $conn->all($sql);

$leaveOptions = [0 => ''];
foreach ($rs as $row) {
    if (in_array($row['sId'], [1, 2])) {
        $leaveOptions[1] = '特休假';
        continue;
    }

    $leaveOptions[$row['sId']] = empty($row['sMemo']) ? $row['sLeaveName'] : $row['sMemo'];
}

$dept = $_SESSION['member_pDep'];

// $sql = 'SELECT pId, pName FROM tPeopleInfo WHERE pJob = 1 AND pDep IN (5, 6, 7, 8, 9, 10, 11, 13) AND pId <> 66 ORDER BY pDep, pName DESC;';
$sql = 'SELECT pId, pName FROM tPeopleInfo WHERE pJob = 1 AND pDep IN (' . $dept . ') AND pId NOT IN (' . $_SESSION['member_id'] . ', 66) ORDER BY pDep, pName DESC;';
$rs  = $conn->all($sql);

if ($dept == 5) {
    $rs = [
        ['pId' => 111, 'pName' => '林佳靜'],
        ['pId' => 115, 'pName' => '陳芃羽'],
        ['pId' => 120, 'pName' => '柯金伶'],
        ['pId' => 124, 'pName' => '吳佳玟'],
        ['pId' => 12, 'pName' => '劉雅雯'],
        ['pId' => 1, 'pName' => '吳佩琦'],
    ];

    if ($_SESSION['member_id'] == 1) {
        $rs[] = ['pId' => 22, 'pName' => '劉展宏'];
    }
}

if ($dept == 9) {
    $rs = [
        ['pId' => 36, 'pName' => '陳品彣'],
        ['pId' => 86, 'pName' => '葉芷伶'],
    ];
}

$agentOptions = [0 => '無'];
foreach ($rs as $row) {
    $agentOptions[$row['pId']] = $row['pName'];
}

$needAttachmentLeave = '["' . implode('","', $needAttachmentLeave) . '"]'; // 需要附件的假別: 3=婚假, 4=產檢假, 5=產假, 6=陪產假, 8=喪假

$from_date = (! empty($_GET['from_date']) && preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/iu", $_GET['from_date'])) ? $_GET['from_date'] : date('Y-m-d');
$to_date   = (! empty($_GET['to_date']) && preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/iu", $_GET['to_date'])) ? $_GET['to_date'] : date('Y-m-d');

//20250328 確認申請日期是否已被鎖定
if (getLastLockDate(new first1DB, $from_date, $to_date)) {
    breakOut('日期已被鎖定，無法申請');
    exit;
}
// echo '<pre>';
// print_r($leaveTypes);exit;
$smarty->assign('leaveTypes', $leaveTypes);
$smarty->assign('from_date', $from_date);
$smarty->assign('to_date', $to_date);
$smarty->assign('leaveOptions', $leaveOptions);
$smarty->assign('agentOptions', $agentOptions);
$smarty->assign('needAttachmentLeave', $needAttachmentLeave);

$smarty->display('leaveApply.inc.tpl', '', 'staff');
