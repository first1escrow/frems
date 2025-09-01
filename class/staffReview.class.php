<?php
namespace First1\V1\Staff;

require_once dirname(__DIR__) . '/first1DB.php';

class StaffReview
{
    private static $instance;
    private $conn;

    private function __construct()
    {
        $this->conn = new \first1DB;
    }

    public function __destruct()
    {
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new StaffReview;
        }

        return self::$instance;
    }

    /**
     * 判斷是否有待審核的資料
     * @param  int $staff_id 員工編號
     * @return array|bool
     */
    public function isReview($staff_id)
    {
        $checkin_case         = $this->getCheckInReviewCase($staff_id);
        $leave_case           = $this->getLeaveReviewCase($staff_id);
        $leave_revoke_case    = $this->getLeaveRevokeReviewCase($staff_id);
        $overtime_case        = $this->getOvertimeReviewCase($staff_id);
        $overtime_revoke_case = $this->getOvertimeRevokeReviewCase($staff_id);

        if (!empty($checkin_case) || !empty($leave_case) || !empty($leave_revoke_case) || !empty($overtime_case) || !empty($overtime_revoke_case)) {
            return array_merge($checkin_case, $leave_case, $leave_revoke_case, $overtime_case, $overtime_revoke_case);
        }

        return false;
    }

    /**
     * 取得簽到簽退待審核資料
     * @param  int $staff_id 員工編號
     * @return array
     */
    public function getCheckInReviewCase($staff_id)
    {
        $sql = 'SELECT
                    sId,
                    sStaffId AS staffId,
                    (SELECT pName FROM tPeopleInfo WHERE pId = a.sStaffId) AS staffName,
                    sApplyDate AS applyDate,
                    sApplyType,
                    CASE
                        WHEN sApplyType = "IN" THEN "簽到"
                        WHEN sApplyType = "OUT" THEN "簽退"
                        ELSE ""
                    END AS applyType,
                    sReason AS reason,
                    sCreatedAt AS createdAt,
                    "checkIn" AS reviewType,
                    "補打卡" AS reviewTypeName,
                    CASE
                        WHEN sStatus = "N" THEN "未簽核"
                        WHEN sStatus = "Y" THEN "已簽核"
                        WHEN sStatus = "R" THEN "未通過"
                        ELSE ""
                    END AS statusName
                FROM
                    tStaffCheckInApply AS a
                WHERE
                    sSupervisor = :staff_id
                    AND sStatus = "N";';
        $rs = $this->conn->all($sql, ['staff_id' => $staff_id]);
        if (empty($rs)) {
            return [];
        }

        return array_map(function ($item) {
            return [
                'sId'            => $item['sId'],
                'staffId'        => $item['staffId'],
                'staffName'      => $item['staffName'],
                'reviewType'     => $item['reviewType'],
                'createdAt'      => substr($item['createdAt'], 0, 16),
                'description'    => '申請項目：' . $item['applyType'] . "<br>\n" . '事由：' . $item['reason'] . "<br>\n" . '日期：' . $item['applyDate'],
                'status'         => $item['statusName'],
                'reviewTypeName' => $item['reviewTypeName'],
            ];
            return $case;
        }, $rs);
    }

    /**
     * 取得請假待審核資料
     * @param  int $staff_id 員工編號
     * @return array
     */
    public function getLeaveReviewCase($staff_id)
    {
        $sql = 'SELECT
                    sId,
                    sApplicant AS staffId,
                    (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) AS staffName,
                    sLeaveId AS leaveId,
                    (SELECT CASE WHEN sMemo = "" OR sMemo IS NULL THEN sLeaveName ELSE sMemo END AS leaveName FROM tStaffLeaveType WHERE sId = a.sLeaveId) AS leaveName,
                    CONCAT(SUBSTRING(a.sLeaveFromDateTime, 1, 16), " ~ ", SUBSTRING(a.sLeaveToDateTime, 1, 16)) AS leaveDateTime,
                    sCreatedAt AS createdAt,
                    "leave" AS reviewType,
                    "請假" AS reviewTypeName,
                    CASE
                        WHEN sStatus = "N" THEN "未簽核"
                        WHEN sStatus = "Y" THEN "已簽核"
                        WHEN sStatus = "D" THEN "已駁回"
                        WHEN sStatus = "R" THEN "已撤銷"
                        WHEN sStatus = "C" THEN "已取消"
                        ELSE ""
                    END AS statusName
                FROM
                    tStaffLeaveApply AS a
                WHERE
                    ((sAgentApproval = :staff_id AND sAgentApprovalDateTime IS NULL)
                    OR (
                        sUnitApproval = :staff_id
                        AND sUnitApprovalDateTime IS NULL
                        AND (
                            sAgentApproval IS NULL
                            OR sAgentApprovalDateTime IS NOT NULL
                        )
                    )
                    OR (sManagerApproval = :staff_id AND sManagerApprovalDateTime IS NULL AND sUnitApprovalDateTime IS NOT NULL))
                    AND sStatus = "N";';
        $rs = $this->conn->all($sql, ['staff_id' => $staff_id]);

        if (empty($rs)) {
            return [];
        }

        return array_map(function ($item) {
            return [
                'sId'            => $item['sId'],
                'staffId'        => $item['staffId'],
                'staffName'      => $item['staffName'],
                'reviewType'     => $item['reviewType'],
                'createdAt'      => substr($item['createdAt'], 0, 16),
                'description'    => '假別：' . $item['leaveName'] . "<br>\n" . '日期：' . $item['leaveDateTime'],
                'status'         => $item['statusName'],
                'reviewTypeName' => $item['reviewTypeName'],
            ];
        }, $rs);
    }

    /**
     * 取得請假撤銷待審核資料
     * @param  int $staff_id 員工編號
     * @return array
     */
    public function getLeaveRevokeReviewCase($staff_id)
    {
        $sql = 'SELECT
                    a.sId as revokeId,
                    a.sLeaveApplyId AS leaveApplyId,
                    a.sCreatedAt AS createdAt,
                    a.sProcessing,
                    (
                        CASE
                            WHEN a.sProcessing = "A" THEN "代理人"
                            WHEN a.sProcessing = "U" THEN "部門主管"
                            WHEN a.sProcessing = "M" THEN "總經理"
                            ELSE ""
                        END
                    ) as processingName,
                    a.sStatus,
                    (
                        CASE
                            WHEN a.sStatus = "N" THEN "未簽核"
                            WHEN a.sStatus = "Y" THEN "已簽核"
                            WHEN a.sStatus = "D" THEN "已駁回"
                            WHEN a.sStatus = "C" THEN "已取消"
                            ELSE ""
                        END
                    ) as statusName,
                    (SELECT pName FROM tPeopleInfo WHERE pId = b.sApplicant) AS staffName,
                    (SELECT CASE WHEN sMemo = "" THEN sLeaveName ELSE sMemo END AS leaveName FROM tStaffLeaveType WHERE sId = b.sLeaveId) AS leaveName,
                    CONCAT(SUBSTRING(b.sLeaveFromDateTime, 1, 16), " ~ ", SUBSTRING(b.sLeaveToDateTime, 1, 16)) AS leaveDateTime,
                    "leaveRevoke" AS reviewType,
                    "銷假" AS reviewTypeName
                FROM
                    tStaffLeaveApplyRevoke AS a
                JOIN
                    tStaffLeaveApply AS b ON a.sLeaveApplyId = b.sId
                WHERE
                    ((b.sAgentApproval = :staff_id AND b.sAgentApprovalDateTime IS NOT NULL)
                    OR (b.sUnitApproval = :staff_id AND b.sUnitApprovalDateTime IS NOT NULL)
                    OR (b.sManagerApproval = :staff_id AND b.sManagerApprovalDateTime IS NOT NULL))
                    AND a.sStatus = "N";';
        $rs = $this->conn->all($sql, ['staff_id' => $staff_id]);

        if (empty($rs)) {
            return [];
        }

        return array_map(function ($item) {
            return [
                'sId'            => $item['revokeId'],
                'staffName'      => $item['staffName'],
                'reviewType'     => $item['reviewType'],
                'createdAt'      => substr($item['createdAt'], 0, 16),
                'description'    => '申請撤銷假別：' . $item['leaveName'] . "<br>\n" . '日期：' . $item['leaveDateTime'] . "<br>\n" . '進度：' . $item['processingName'] . "<br>\n" . '狀態：' . $item['statusName'],
                'status'         => $item['statusName'],
                'reviewTypeName' => $item['reviewTypeName'],
            ];
        }, $rs);
    }

    /**
     * 取得加班待審核資料
     * @param  int $staff_id 員工編號
     * @return array
     */
    public function getOvertimeReviewCase($staff_id)
    {
        $sql = 'SELECT
                    sId,
                    sApplicant AS staffId,
                    (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) AS staffName,
                    CONCAT(SUBSTRING(a.sOvertimeFromDateTime, 1, 16), " ~ ", SUBSTRING(a.sOvertimeToDateTime, 1, 16)) AS overtimeDateTime,
                    sCreatedAt AS createdAt,
                    "overtime" AS reviewType,
                    "加班" AS reviewTypeName,
                    CASE
                        WHEN sStatus = "N" THEN "未簽核"
                        WHEN sStatus = "Y" THEN "已簽核"
                        WHEN sStatus = "D" THEN "已駁回"
                        WHEN sStatus = "R" THEN "已撤銷"
                        WHEN sStatus = "C" THEN "已取消"
                        ELSE ""
                    END AS statusName
                FROM
                    tStaffOvertimeApply AS a
                WHERE
                    sUnitApproval = :staff_id
                    AND sUnitApprovalDateTime IS NULL
                    AND sStatus = "N";';
        $rs = $this->conn->all($sql, ['staff_id' => $staff_id]);

        if (empty($rs)) {
            return [];
        }

        return array_map(function ($item) {
            return [
                'sId'            => $item['sId'],
                'staffId'        => $item['staffId'],
                'staffName'      => $item['staffName'],
                'reviewType'     => $item['reviewType'],
                'createdAt'      => substr($item['createdAt'], 0, 16),
                'description'    => '加班時間：' . $item['overtimeDateTime'],
                'status'         => $item['statusName'],
                'reviewTypeName' => $item['reviewTypeName'],
            ];
        }, $rs);
    }

    /**
     * 取得加班撤銷待審核資料
     * @param  int $staff_id 員工編號
     * @return array
     */
    public function getOvertimeRevokeReviewCase($staff_id)
    {
        $sql = 'SELECT
                    a.sId as revokeId,
                    a.sOvertimeApplyId AS overtimeApplyId,
                    a.sCreatedAt AS createdAt,
                    a.sProcessing,
                    "部門主管" as processingName,
                    a.sStatus,
                    (
                        CASE
                            WHEN a.sStatus = "N" THEN "未簽核"
                            WHEN a.sStatus = "Y" THEN "已簽核"
                            WHEN a.sStatus = "D" THEN "已駁回"
                            WHEN a.sStatus = "C" THEN "已取消"
                            ELSE ""
                        END
                    ) as statusName,
                    (SELECT pName FROM tPeopleInfo WHERE pId = b.sApplicant) AS staffName,
                    CONCAT(SUBSTRING(b.sOvertimeFromDateTime, 1, 16), " ~ ", SUBSTRING(b.sOvertimeToDateTime, 1, 16)) AS overtimeDateTime,
                    "overtimeRevoke" AS reviewType,
                    "撤銷加班" AS reviewTypeName
                FROM
                    tStaffOvertimeApplyRevoke AS a
                JOIN
                    tStaffOvertimeApply AS b ON a.sOvertimeApplyId = b.sId
                WHERE
                    b.sUnitApproval = :staff_id
                    AND b.sUnitApprovalDateTime IS NOT NULL
                    AND a.sStatus = "N";';
        $rs = $this->conn->all($sql, ['staff_id' => $staff_id]);

        if (empty($rs)) {
            return [];
        }

        return array_map(function ($item) {
            return [
                'sId'            => $item['revokeId'],
                'staffName'      => $item['staffName'],
                'reviewType'     => $item['reviewType'],
                'createdAt'      => substr($item['createdAt'], 0, 16),
                'description'    => '加班撤銷：' . $item['overtimeDateTime'],
                'status'         => $item['statusName'],
                'reviewTypeName' => $item['reviewTypeName'],
            ];
        }, $rs);
    }

    /**
     * 取得補卡已審核資料
     * @param  int $staff_id 員工編號
     * @return array
     */
    public function getCheckInReviewHistory($staff_id)
    {
        $sql = 'SELECT
                    sId,
                    sStaffId AS staffId,
                    (SELECT pName FROM tPeopleInfo WHERE pId = a.sStaffId) AS staffName,
                    sApplyDate AS applyDate,
                    sApplyType,
                    CASE
                        WHEN sApplyType = "IN" THEN "簽到"
                        WHEN sApplyType = "OUT" THEN "簽退"
                        ELSE ""
                    END AS applyType,
                    sReason AS reason,
                    sCreatedAt AS createdAt,
                    "checkIn" AS reviewType,
                    "補打卡" AS reviewTypeName,
                    sApprovalDateTime AS approvalDateTime,
                    CASE
                        WHEN sStatus = "N" THEN "未簽核"
                        WHEN sStatus = "Y" THEN "已簽核"
                        WHEN sStatus = "R" THEN "未通過"
                        ELSE ""
                    END AS statusName
                FROM
                    tStaffCheckInApply AS a
                WHERE
                    sSupervisor = :staff_id
                    AND sStatus = "Y";';
        $rs = $this->conn->all($sql, ['staff_id' => $staff_id]);
        if (empty($rs)) {
            return [];
        }

        return array_map(function ($item) {
            return [
                'sId'              => $item['sId'],
                'staffId'          => $item['staffId'],
                'staffName'        => $item['staffName'],
                'reviewType'       => $item['reviewType'],
                'createdAt'        => substr($item['createdAt'], 0, 16),
                'description'      => $item['reason'],
                'status'           => $item['statusName'],
                'reviewTypeName'   => '補' . $item['applyType'] . '卡',
                'approvalDateTime' => substr($item['approvalDateTime'], 0, 16),
                'applyDate'        => $item['applyDate'],
            ];
            return $case;
        }, $rs);
    }

    /**
     * 取得請假已審核資料
     * @param  int $staff_id 員工編號
     * @return array
     */
    public function getLeaveReviewHistory($staff_id)
    {
        $sql = 'SELECT
                    sId,
                    sApplicant AS staffId,
                    (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) AS staffName,
                    sLeaveId AS leaveId,
                    (SELECT CASE WHEN sMemo = "" OR sMemo IS NULL THEN sLeaveName ELSE sMemo END AS leaveName FROM tStaffLeaveType WHERE sId = a.sLeaveId) AS leaveName,
                    sLeaveFromDateTime AS leaveFromDateTime,
                    sLeaveToDateTime AS leaveToDateTime,
                    CONCAT(SUBSTRING(a.sLeaveFromDateTime, 1, 16), " ~ ", SUBSTRING(a.sLeaveToDateTime, 1, 16)) AS leaveDateTime,
                    sCreatedAt AS createdAt,
                    sUnitApprovalDateTime AS sUnitApprovalDateTime,
                    "leave" AS reviewType,
                    "請假" AS reviewTypeName,
                    CASE
                        WHEN sStatus = "N" THEN "未簽核"
                        WHEN sStatus = "Y" THEN "已簽核"
                        WHEN sStatus = "D" THEN "已駁回"
                        WHEN sStatus = "R" THEN "已撤銷"
                        WHEN sStatus = "C" THEN "已取消"
                        ELSE ""
                    END AS statusName
                FROM
                    tStaffLeaveApply AS a
                WHERE
                    (sAgentApproval = :staff_id AND sAgentApprovalDateTime IS NOT NULL)
                    OR (sUnitApproval = :staff_id AND sUnitApprovalDateTime IS NOT NULL)
                    OR (sManagerApproval = :staff_id AND sManagerApprovalDateTime IS NOT NULL);';
        $rs = $this->conn->all($sql, ['staff_id' => $staff_id]);

        if (empty($rs)) {
            return [];
        }

        return array_map(function ($item) {
            $description = substr($item['leaveFromDateTime'], 0, 16) . '(起)<br>' . substr($item['leaveToDateTime'], 0, 16) . '(迄)';
            return [
                'sId'              => $item['sId'],
                'staffId'          => $item['staffId'],
                'staffName'        => $item['staffName'],
                'reviewType'       => $item['reviewType'],
                'createdAt'        => substr($item['createdAt'], 0, 16),
                'description'      => $description,
                'status'           => $item['statusName'],
                'reviewTypeName'   => $item['leaveName'],
                'approvalDateTime' => substr($item['sUnitApprovalDateTime'], 0, 16),
                'applyDate'        => substr($item['leaveFromDateTime'], 0, 16),
            ];
        }, $rs);
    }

    /**
     * 取得加班已審核資料
     * @param  int $staff_id 員工編號
     * @return array
     */
    public function getOvertimeReviewHistory($staff_id)
    {
        $sql = 'SELECT
                    sId,
                    sApplicant AS staffId,
                    (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) AS staffName,
                    sOvertimeFromDateTime AS overtimeFromDateTime,
                    sOvertimeToDateTime AS overtimeToDateTime,
                    CONCAT(SUBSTRING(a.sOvertimeFromDateTime, 1, 16), " ~ ", SUBSTRING(a.sOvertimeToDateTime, 1, 16)) AS overDateTime,
                    sCreatedAt AS createdAt,
                    sUnitApprovalDateTime AS sUnitApprovalDateTime,
                    "overtime" AS reviewType,
                    "加班" AS reviewTypeName,
                    CASE
                        WHEN sStatus = "N" THEN "未簽核"
                        WHEN sStatus = "Y" THEN "已簽核"
                        WHEN sStatus = "D" THEN "已駁回"
                        WHEN sStatus = "R" THEN "已撤銷"
                        WHEN sStatus = "C" THEN "已取消"
                        ELSE ""
                    END AS statusName
                FROM
                    tStaffOvertimeApply AS a
                WHERE
                    sUnitApproval = :staff_id
                    AND sUnitApprovalDateTime IS NOT NULL;';
        $rs = $this->conn->all($sql, ['staff_id' => $staff_id]);

        if (empty($rs)) {
            return [];
        }

        return array_map(function ($item) {
            $description = substr($item['overtimeFromDateTime'], 0, 16) . '(起)<br>' . substr($item['overtimeToDateTime'], 0, 16) . '(迄)';
            return [
                'sId'              => $item['sId'],
                'staffId'          => $item['staffId'],
                'staffName'        => $item['staffName'],
                'reviewType'       => $item['reviewType'],
                'createdAt'        => substr($item['createdAt'], 0, 16),
                'description'      => $description,
                'status'           => $item['statusName'],
                'reviewTypeName'   => $item['reviewTypeName'],
                'approvalDateTime' => substr($item['sUnitApprovalDateTime'], 0, 16),
                'applyDate'        => substr($item['overtimeFromDateTime'], 0, 16),
            ];
        }, $rs);
    }

}