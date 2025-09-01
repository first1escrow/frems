<?php
namespace First1\V1\Staff;

require_once dirname(__DIR__) . '/first1DB.php';

class Staff
{
    private $conn;
    private $from_date, $to_date;
    public $holidayData    = [];
    public $leaveApplyData = [];
    public $WEEKEND        = [1 => '週一', 2 => '週二', 3 => '週三', 4 => '週四', 5 => '週五', 6 => '週六', 7 => '週日'];
    public $BEGINDATE      = '2025-01-01 00:00:00';

    public function __construct()
    {
        $this->conn = new \first1DB;
    }

    public function __destruct()
    {
    }

    /**
     * 設定查詢日期區間
     * @param string $from  日期區間起始
     * @param string $to   日期區間結束
     * @return void
     */
    public function setDateTimePeriod($from, $to)
    {
        $this->from_date = preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/", $from) ? $from : $from . ' 00:00:00';
        $this->to_date   = preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/", $to) ? $to : $to . ' 23:59:59';

        $this->getHolidayData($this->from_date, $this->to_date);
        $this->getLeaveApplyData($this->from_date, $this->to_date);
    }

    /**
     * 取得假日資料
     * @param string $from  日期區間起始
     * @param string $to   日期區間結束
     * @return void
     */
    public function getHolidayData($from, $to)
    {
        $from = strtotime($from);
        $to   = strtotime($to);

        $sql = 'SELECT
                    hId,
                    hName,
                    hMakeUpWorkday,
                    hFromDate,
                    hToDate,
                    hFromTime,
                    hToTime,
                    hFromTimestamp,
                    hToTimestamp
                FROM
                    tHoliday
                WHERE
                    hFromTimestamp >= :from
                    AND hToTimestamp <= :to
                    AND hMakeUpWorkday IN ("Y", "N");';
        $this->holidayData = $this->conn->all($sql, ['from' => $from, 'to' => $to]);
    }

    /**
     * 取得請假資料
     * @param string $from  日期區間起始
     * @param string $to   日期區間結束
     * @return void
     */
    public function getLeaveApplyData($from, $to)
    {
        $from = strtotime($from);
        $to   = strtotime($to);

        $sql = 'SELECT
                    sId,
                    sApplicant,
                    sLeaveId,
                    (SELECT sLeaveName FROM tStaffLeaveType WHERE sId = a.sLeaveId) as leaveName,
                    sLeaveFromDateTime,
                    sLeaveToDateTime,
                    sLeaveFromTmestamp,
                    sLeaveToTimestamp,
                    sTotalHoursOfLeave,
                    sLeaveAttachment,
                    sAgentApproval,
                    sAgentApprovalDateTime,
                    sUnitApproval,
                    sUnitApprovalDateTime,
                    sManagerApproval,
                    sManagerApprovalDateTime,
                    sProcessing
                FROM
                    tStaffLeaveApply AS a
                WHERE
                    sLeaveFromTmestamp <= :to
                    AND sLeaveToTimestamp >= :from
                    AND sStatus = "Y";';
        $this->leaveApplyData = $this->conn->all($sql, ['from' => $from, 'to' => $to]);
    }

    /**
     * 判斷是否為假日
     * @param string $date 日期
     * @param string $time 時間
     * @param string $makeUpWorkday 是否補班  Y: 是, N: 否
     * @return string|bool
     */
    public function isHoliday($date, $time = ' 09:00:00', $makeUpWorkday = 'N')
    {
        $timestamp = strtotime($date . ' ' . $time);

        if (! empty($this->holidayData)) {
            foreach ($this->holidayData as $holiday) {
                if ($holiday['hFromTimestamp'] <= $timestamp && $holiday['hToTimestamp'] >= $timestamp && $holiday['hMakeUpWorkday'] == $makeUpWorkday) {
                    return $holiday['hName'];
                }
            }
        }

        return false;
    }

    /**
     * 取得假日資料
     * @param string $date 日期
     * @param string $time 時間
     * @param string $makeUpWorkday 是否補班  Y: 是, N: 否
     */
    public function getHoliday($date, $time = null, $makeUpWorkday = 'N')
    {
        if (! empty($time)) {
            $timestamp = strtotime($date . ' ' . $time);

            if (! empty($this->holidayData)) {
                foreach ($this->holidayData as $holiday) {
                    if ($holiday['hFromTimestamp'] <= $timestamp && $holiday['hToTimestamp'] >= $timestamp && $holiday['hMakeUpWorkday'] == $makeUpWorkday) {
                        return $holiday;
                    }
                }
            }

            return false;
        }

        if (! empty($this->holidayData)) {
            foreach ($this->holidayData as $holiday) {
                if ($holiday['hFromDate'] == $date && $holiday['hToDate'] == $date && $holiday['hMakeUpWorkday'] == $makeUpWorkday) {
                    return $holiday;
                }
            }
        }

        return false;
    }

    /**
     * 取得請假資料
     * @param string $datetime 日期時間
     * @param string $staffId 員工編號
     * @return array|bool
     */
    public function getLeaveApply($datetime, $staffId)
    {
        if (! empty($this->leaveApplyData)) {
            foreach ($this->leaveApplyData as $leave) {
                if ($leave['sLeaveFromDateTime'] <= $datetime && $leave['sLeaveToDateTime'] >= $datetime && $leave['sApplicant'] == $staffId) {
                    return $leave;
                }
            }
        }

        return false;
    }

    /**
     * 取得本日請假資料
     * @param string $date 日期時間
     * @param string $staffId 員工編號
     * @return array|bool
     */
    public function getTodaysLeaveData($date, $staffId)
    {
        $date = date('Y-m-d', strtotime($date));

        $data = [];
        if (! empty($this->leaveApplyData)) {
            foreach ($this->leaveApplyData as $leave) {
                $from = date('Y-m-d', strtotime($leave['sLeaveFromDateTime']));
                $to   = date('Y-m-d', strtotime($leave['sLeaveToDateTime']));
                if ((($from >= $date) && ($to <= $date)) && ($leave['sApplicant'] == $staffId)) {
                    $data[] = $leave;
                }
            }
        }

        return $data;
    }

    /**
     * 取得多筆請假資料
     * @param array $remark 備註顯示資料
     * @param array $leave 請假資料
     * @param bool $isHtml 是否顯示 html tag
     * @return array 備註顯示資料
     */
    private function showLeaveRemark($remark, $leave = [], $isHtml = false)
    {
        if (empty($leave)) {
            return $remark;
        }

        foreach ($leave as $key => $value) {
            $_remark = empty($value['leaveName']) ? '' : $value['leaveName'];
            if (! empty($isHtml) && ! empty($_remark)) {
                $_remark = '<a href="Javascript:void(0);" style="font-size:9pt;" onclick="showLeave(' . $value['sId'] . ')">' . $value['leaveName'] . '</a>';
            }

            $remark[] = $_remark;

            $_remark = null;unset($_remark);
        }

        return $remark;
    }

    /**
     * 判斷是否為週末
     * @param string $date 日期
     * @return bool
     */
    public function isWeekend($pDate)
    {
        $week           = date('N', strtotime($pDate));
        $hMakeUpWorkday = $this->getHoliday($pDate, null, 'Y');

        if (in_array($week, [6, 7]) && empty($hMakeUpWorkday)) {
            return true;
        }

        return false;
    }

    /**
     * 判斷是否為今日
     * @param string $pDate 日期
     * @param string $today 今日日期
     * @return bool
     */
    public function isToday($pDate, $today = null)
    {
        if (empty($today)) {
            $today = date('Y-m-d');
        }

        return $pDate == $today;
    }

    /**
     * 判斷是否遲到
     * @param string $date 日期
     * @param string $time 時間
     * @param string $staffId 員工編號
     * @return bool
     */
    public function isLate($date, $time, $staffId, $in = '09:00:59')
    {
        $leave = $this->getLeaveApply($date . ' ' . $time, $staffId);
        if (! empty($leave) && ($leave['sLeaveFromDateTime'] >= $date . ' ' . $in)) {
            return false;
        }

        $time = strtotime($time);
        if ($date <= date('Y-m-d') && $time > strtotime($in)) {
            return true;
        }

        return false;
    }

    /**
     * 判斷是否早退
     * @param string $date 日期
     * @param string $time 時間
     * @param string $staffId 員工編號
     * @param string $out 應下班時間
     * @return bool
     */
    public function isEarly($date, $time, $staffId, $out = '17:30:00')
    {
        $leave = $this->getLeaveApply($date . ' ' . $time, $staffId);
        if (! empty($leave) && ($leave['sLeaveToDateTime'] >= $date . ' ' . $out)) {
            return false;
        }

        $time = strtotime($time);
        if ($date <= date('Y-m-d') && $time < strtotime($out)) {
            return true;
        }

        return false;
    }

    /**
     * 判斷是否請假
     * @param string $dateTime 日期時間
     * @param string $staffId 員工編號
     * @return array|bool
     */
    public function isLeave($dateTime, $staffId)
    {
        $leave = $this->getLeaveApply($dateTime, $staffId);
        return ! empty($leave) ? $leave : false;
    }

    /**
     * 判斷是否為主管
     * @param string $staffId 員工編號
     * @return bool|array
     */
    public function isSupervisor($staffId)
    {
        $sql = 'SELECT sStaffId, sDepartment FROM tSupervisor WHERE sStatus = "Y";';
        $rs  = $this->conn->all($sql);

        $departments = [];
        foreach ($rs as $row) {
            if ($row['sStaffId'] == $staffId) {
                $departments[] = $row['sDepartment'];
            }
        }

        return empty($departments) ? false : $departments;
    }

    /**
     * 取得主管id
     * @param string $staffId 員工編號
     * @return string|null
     */
    public function getSupervisor($staffId)
    {
        $sql = 'SELECT
                    b.sStaffId
                FROM
                    tPeopleInfo AS a
                JOIN
                    tSupervisor as b ON a.pDep = b.sDepartment
                WHERE
                    a.pId = :staff AND b.sStatus = "Y";';
        $rs = $this->conn->one($sql, ['staff' => $staffId]);
        return empty($rs['sStaffId']) ? null : $rs['sStaffId'];
    }

    /**
     * 取得備註
     * @param string $pDate 日期
     * @param string $pIn 簽到時間
     * @param string $pOut 簽退時間
     * @param string $staffId 員工編號
     * @param array $allowLateData 允許遲到資料
     * @param string $in 應簽到時間
     * @param string $out 應簽退時間
     * @param array $workingHolidays 補班資料
     * @param array $overtimes 加班資料
     * @param bool $isHtml 是否顯示 html tag
     * @return array 備註
     */
    public function getRemark($pDate, $pIn, $pOut, $staffId, $allowLateData = [], $in = '09:00:59', $out = '17:30:00', $workingHolidays = [], $overtimes = [], $isHtml = true)
    {
        $remark = [];

        if (! empty($pIn) && $this->isLate($pDate, $pIn, $staffId, $in)) {
            $time = $pIn;
            if ($time > '09:00:59' && $time < '12:00:00') {
                $time = '09:00:59';
            }

            $leave = $this->isLeave($pDate . ' ' . $in, $staffId);
            if (empty($leave)) {
                $holiday = $this->isHoliday($pDate, $pIn);
                $_remark = empty($holiday) ? '遲到' : $holiday;
                if ($_remark == '遲到') {
                    $_remark = empty($isHtml) ? $_remark : '<span style="color:red;font-weight:bold;">' . $_remark . '</span>';

                    if ($order = $this->isAllowLate($allowLateData, $pDate, $pIn)) {
                        $_remark = '遲' . $order;
                    }
                }

                if (! empty($_remark)) {
                    $remark[] = $_remark;
                }

                $_remark = null;unset($_remark);
            } else {
                $leave  = $this->getTodaysLeaveData($pDate, $staffId);
                $remark = $this->showLeaveRemark($remark, $leave, $isHtml);
            }
        }

        if (! empty($pOut) && $this->isEarly($pDate, $pOut, $staffId, $out)) {
            $time = $pOut;
            if ($time > '12:00:00' && $time < '13:00:00') {
                $time = '13:00:00';
            }

            if ($time > '17:30:00') {
                $time = '17:30:00';
            }

            $leave = $this->isLeave($pDate . ' ' . $out, $staffId);
            if (empty($leave) || ($leave['sLeaveToDateTime'] < $pDate . ' 17:30:00')) {
                $_remark = empty($isHtml) ? '早退' : '<span style="color:red;font-weight:bold;">早退</span>';

                $holiday  = $this->isHoliday($pDate, $pOut);
                $remark[] = empty($holiday) ? $_remark : $holiday;
            } else {
                $leave  = $this->getTodaysLeaveData($pDate, $staffId);
                $remark = $this->showLeaveRemark($remark, $leave, $isHtml);
            }
        }

        if ($this->isToday($pDate) && ! $this->isWeekend($pDate) && empty($this->isHoliday($pDate))) {
            $_leaveIn  = $this->isLeave($pDate . ' ' . $pIn, $staffId);
            $_leaveOut = $this->isLeave($pDate . ' ' . $pOut, $staffId);

            if (empty($pIn) && (date('H:i:s') > '09:00:59') && empty($_leaveIn)) {
                $_remark  = empty($isHtml) ? '未簽到' : '<span style="color:red;font-weight:bold;">未簽到</span>';
                $remark[] = $_remark;
            }

            if (empty($pOut) && (date('H:i:s') > '17:30:00') && empty($_leaveOut)) {
                $_remark  = empty($isHtml) ? '未簽退' : '<span style="color:red;font-weight:bold;">未簽退</span>';
                $remark[] = $_remark;
            }

            $_leave = $this->getTodaysLeaveData($pDate, $staffId);
            $remark = $this->showLeaveRemark($remark, $_leave, $isHtml);
        }

        if (! $this->isToday($pDate) && ! $this->isWeekend($pDate) && empty($this->isHoliday($pDate))) {
            $_leaveIn  = $this->isLeave($pDate . ' 09:00:59', $staffId);
            $_leaveOut = $this->isLeave($pDate . ' 17:30:00', $staffId);

            // if (empty($pIn) && empty($_leaveIn)) {
            if (empty($pIn)) {
                $_remark  = empty($isHtml) ? '未簽到' : '<span style="color:red;font-weight:bold;">未簽到</span>';
                $remark[] = $_remark;
            }

            // if (empty($pOut) && empty($_leaveOut)) {
            if (empty($pOut)) {
                $_remark  = empty($isHtml) ? '未簽退' : '<span style="color:red;font-weight:bold;">未簽退</span>';
                $remark[] = $_remark;
            }

            $_leave = $this->getTodaysLeaveData($pDate, $staffId);
            $remark = $this->showLeaveRemark($remark, $_leave, $isHtml);
        }

        if (! empty($overtimes[$staffId]) && empty($isHtml)) {
            foreach ($overtimes[$staffId] as $overtime) {
                if (substr($overtime['sOvertimeFromDateTime'], 0, 10) == $pDate) {
                    // 取得補班日期
                    $workingHolidays = array_column($workingHolidays, 'hFromDate');

                    //補班日或平日
                    if (in_array($pDate, $workingHolidays) || (empty($this->isWeekend($pDate)) && empty($this->isHoliday($pDate)))) {
                        if ($pOut > '18:30:00') {
                            $_remark  = '加班';
                            $remark[] = $_remark;
                        }
                    }

                    //假日
                    if (($this->isHoliday($pDate) || $this->isWeekend($pDate)) && ! in_array($pDate, $workingHolidays)) {
                        $_remark  = '加班';
                        $remark[] = $_remark;
                    }
                }
            }
        }

        return array_unique($remark);
    }

    /**
     * 判斷是否允許遲到
     * @param array $allowLateData 允許遲到資料
     * @param string $pDate 日期
     * @param string $pIn 簽到時間
     * @return bool or string 遲到次序
     */
    public function isAllowLate($allowLateData, $pDate, $pIn)
    {
        if (empty($allowLateData)) {
            return false;
        }

        foreach ($allowLateData as $data) {
            if ($data['sLateDate'] == $pDate && $data['sCheckInTime'] == $pIn) {
                return $data['sOrder'];
            }

        }

        return false;
    }
}
