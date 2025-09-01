<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/includes/staff/leaveConfig.php';

$id = $_GET['id'];
if (empty($id) || !is_numeric($id)) {
    exit('Invalid access'); //非法存取
}

$conn = new first1DB;

$case = [];

$sql = 'SELECT
            sApplicant,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) as staffName,
            sLeaveId,
            (SELECT CASE WHEN sMemo = "" OR sMemo IS NULL THEN sLeaveName ELSE sMemo END AS name FROM tStaffLeaveType WHERE sId = a.sLeaveId) as leaveName,
            sLeaveFromDateTime,
            sLeaveToDateTime,
            sTotalHoursOfLeave,
            sApplyReason,
            sLeaveAttachment,
            sAgentApproval,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.sAgentApproval) as agent,
            sAgentApprovalDateTime,
            sUnitApproval,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.sUnitApproval) as supervisor,
            sUnitApprovalDateTime,
            sManagerApproval,
            sManagerApprovalDateTime,
            sProcessing,
            sStatus
        FROM
            tStaffLeaveApply AS a
        WHERE
            sId = ' . $id . ';';
$rs = $conn->one($sql);

if (!empty($rs)) {
    $rs['sLeaveFromDateTime'] = date('Y-m-d H:i', strtotime($rs['sLeaveFromDateTime']));
    $rs['sLeaveToDateTime']   = date('Y-m-d H:i', strtotime($rs['sLeaveToDateTime']));

    $rs['sLeaveAttachment']         = empty($rs['sLeaveAttachment']) ? '無' : '<a href="Javascript:void(0);" onclick="attachment(\'' . $id . '\')">附件</a>';
    $rs['agent']                    = empty($rs['agent']) ? '無' : $rs['agent'];
    $rs['sAgentApprovalDateTime']   = empty($rs['sAgentApprovalDateTime']) ? '-' : substr($rs['sAgentApprovalDateTime'], 0, -3);
    $rs['supervisor']               = empty($rs['supervisor']) ? '無' : $rs['supervisor'];
    $rs['sUnitApprovalDateTime']    = empty($rs['sUnitApprovalDateTime']) ? '-' : substr($rs['sUnitApprovalDateTime'], 0, -3);
    $rs['sManagerApprovalDateTime'] = empty($rs['sManagerApprovalDateTime']) ? '-' : $rs['sManagerApprovalDateTime'];
}

$smarty->assign('case', $rs);

$smarty->display('leaveList.inc.tpl', '', 'staff');
