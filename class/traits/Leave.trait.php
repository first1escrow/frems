<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

trait Leave
{
    public function getLeaveApplyData($year, $month, $staffId)
    {
        $from = strtotime($year . '-' . $month . '-01 00:00:00');
        $to   = strtotime($year . '-' . $month . '-' . date('t', $from) . ' 23:59:59');

        $sql = 'SELECT
                    sId,
                    sApplicant,
                    (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) AS sApplicantName,
                    sLeaveId,
                    (SELECT sLeaveName FROM tStaffLeaveType WHERE sId = a.sLeaveId) AS sLeaveName,
                    sLeaveFromDateTime,
                    sLeaveToDateTime,
                    sTotalHoursOfLeave,
                    sLeaveAttachment
                FROM
                    tStaffLeaveApply AS a
                WHERE
                    sApplicant = :staffId
                    AND sLeaveFromTmestamp <= :to
                    AND sLeaveToTimestamp >= :from;';
        return $this->conn->all($sql, ['staffId' => $staffId, 'from' => $from, 'to' => $to]);
    }

}
