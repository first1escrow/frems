<?php
namespace First1\V1\HR;

require_once dirname(__DIR__) . '/first1DB.php';
require_once __DIR__ . '/leaveHourCount.class.php';
require_once __DIR__ . '/traits/CheckIn.trait.php';
require_once __DIR__ . '/traits/Leave.trait.php';
require_once __DIR__ . '/traits/Department.trait.php';

use First1\V1\Staff\LeaveHourCount;

class HR
{
    use \CheckIn, \Leave, \Department;

    private $conn;
    private static $instance;

    private function __construct()
    {
        $this->conn = new \first1DB;
    }

    public function __destruct()
    {
    }

    /**
     * 單例模式
     * @return HR
     */
    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * 取得員工資料
     * @param string $date 日期
     * @param int|array $dept 部門編號
     * @return array 員工資料
     */
    public function dumpStaffData($date = null, $dept = null)
    {
        $date = empty($date) ? date('Y-m-d') : $date;

        if (is_array($dept)) {
            $dept = implode(',', $dept);
        }
        $dept = empty($dept) ? ' pDep <> 12 ' : ' pDep IN (' . $dept . ') ';

        $sql   = 'SELECT pId, pName, pDep, pOnBoard FROM tPeopleInfo WHERE pJob = 1 AND pId NOT IN (2, 6, 8, 66) AND ' . $dept . ' AND ((pOnBoard = "0000-00-00") OR (pOnBoard <= :date)) ORDER BY pOnBoard, pId ASC;';
        $staff = $this->conn->all($sql, ['date' => $date]);

        if (empty($staff)) {
            return [];
        }

        $data = [];
        foreach ($staff as $v) {
            $data[$v['pId']] = $v;
        }

        return $data;
    }

    /**
     * 依據取得員工打卡資料規格化以便寫入資料庫
     * @param array $data 打卡資料
     * @return array 規格化後的打卡資料
     */
    public function getCheckInSummary($data)
    {
        if (empty($data)) {
            return [];
        }

        $result = [];
        foreach ($data as $v) {
            $workingHours = $this->getWorkingHours($v['sDate'], $v['sIn'], $v['sOut']);
            $lateHours    = $this->getLateHours($v['sStaffId'], $v['sDate'], $v['sIn']);
            $earlyLeave   = $this->getEarlyLeaveHours($v['sStaffId'], $v['sDate'], $v['sOut']);

            $result[] = [
                $v['sDate'],
                $v['sStaffId'],
                $v['sIn'],
                $v['sOut'],
                $workingHours,
                $lateHours,
                $earlyLeave,
                date('Y-m-d H:i:s'),
            ];
        }

        return $result;
    }

    /**
     * 取得員工單日工作時數
     * @param string $date 日期
     * @param string $in 上班打卡時間
     * @param string $out 下班打卡時間
     * @return float 工作時數
     */
    public function getWorkingHours($date, $in, $out)
    {
        if (empty($in) || empty($out)) {
            return 0;
        }

        return LeaveHourCount::getLeaveHours($date . ' ' . $in, $date . ' ' . $out, 'S');
    }

    /**
     * 取得員工遲到時數
     * @param int $staffId 員工編號
     * @param string $date 上班打卡日期
     * @param string $in 上班打卡時間
     * @return float 遲到時數
     */
    public function getLateHours($staffId, $date, $in)
    {
        if (empty($in)) {
            return 0;
        }

        $in = strtotime($in);

        $from = date('Y-m-d 00:00:00', strtotime($date));
        $to   = date('Y-m-d 23:59:59', strtotime($date));

        $standrad = $this->getCheckInTimeByDate($staffId, $this->getHolidays($from, $to), $this->getLeaves($from, $to), $date);
        if (empty($standrad)) {
            return 0;
        }

        $standrad = strtotime($standrad);
        if ($in > $standrad) {
            $diff = $in - $standrad;
            return ($diff / 3600);
        }

        return 0;
    }

    /**
     * 取得員工早退時數
     * @param int $staffId 員工編號
     * @param string $date 下班打卡日期
     * @param string $out 下班打卡時間
     * @return float 早退時數
     */
    public function getEarlyLeaveHours($staffId, $date, $out)
    {
        if (empty($out)) {
            return 0;
        }

        $out = strtotime($out);

        $from = date('Y-m-d 00:00:00', strtotime($date));
        $to   = date('Y-m-d 23:59:59', strtotime($date));

        $standard = $this->getCheckOutTimeByDate($staffId, $this->getHolidays($from, $to), $this->getLeaves($from, $to), $date);
        if (empty($standard)) {
            return 0;
        }

        $standard = strtotime($standard);
        if ($out < $standard) {
            $diff = $standard - $out;
            return ($diff / 3600);
        }

        return 0;
    }

    /**
     * 寫入打卡資料
     * @param array $data 打卡資料
     * @return bool
     */
    public function insertCheckInData($data)
    {
        if (empty($data)) {
            return false;
        }

        $values = [];
        foreach ($data as $v) {
            $values[] = '(UUID(), "' . implode('","', $v) . '")';
        }

        $sql = 'INSERT INTO
                    tHRSummary
                (
                    hId,
                    hDate,
                    hStaffId,
                    hCheckInTime,
                    hCheckOutTime,
                    hWorkingHours,
                    hLateHours,
                    hEarlyLeaveHours,
                    hCreatedAt
                ) VALUES ' . implode(',', $values) . '
                ON DUPLICATE KEY UPDATE
                    hCheckInTime = VALUES(hCheckInTime),
                    hCheckOutTime = VALUES(hCheckOutTime),
                    hWorkingHours = VALUES(hWorkingHours),
                    hLateHours = VALUES(hLateHours),
                    hEarlyLeaveHours = VALUES(hEarlyLeaveHours);';
        return $this->conn->exeSql($sql);
    }

    /**
     * 請假資料格式整理
     * @param array $data 請假資料
     * @return array 請假申請資料
     */
    public function getLeaveSummary($data)
    {
        if (empty($data)) {
            return [];
        }

        $result = [];
        foreach ($data as $v) {
            $from = new \DateTime($v['sLeaveFromDateTime']);
            $to   = new \DateTime($v['sLeaveToDateTime']);

            do {
                $date    = $from->format('Y-m-d');
                $staffId = $v['sApplicant'];

                $begin = $from->format('Y-m-d 09:00:00');
                if ($from->format('Y-m-d') == substr($v['sLeaveFromDateTime'], 0, 10)) {
                    $begin = $v['sLeaveFromDateTime'];
                }

                $end = $from->format('Y-m-d 18:00:00');
                if ($from->format('Y-m-d') == substr($v['sLeaveToDateTime'], 0, 10)) {
                    $end = $v['sLeaveToDateTime'];
                }

                $hour_count = LeaveHourCount::getOvertimeHours($begin, $end); //計算當日上班時數
                $hour_count = round($hour_count, 2);

                $json = json_encode([
                    'sId'        => $v['sId'],
                    'staffId'    => $v['sApplicant'],
                    'staffName'  => $v['sApplicantName'],
                    'leaveId'    => $v['sLeaveId'],
                    'leaveName'  => $v['sLeaveName'],
                    'leaveFrom'  => $v['sLeaveFromDateTime'],
                    'leaveTo'    => $v['sLeaveToDateTime'],
                    'totalHours' => $hour_count,
                    'attachment' => $v['sLeaveAttachment'],
                ], JSON_UNESCAPED_UNICODE);

                $result[] = [
                    $date,
                    $staffId,
                    $json,
                ];

                $from->modify('+1 day');
            } while ($from->format('Y-m-d') <= $to->format('Y-m-d'));

        }

        return $result;
    }

    /**
     * 寫入請假資料
     * @param array $data 請假資料
     * @return bool
     */
    public function insertLeaveData($data)
    {
        if (empty($data)) {
            return false;
        }

        $values = [];
        foreach ($data as $v) {
            $values[] = '(UUID(), "' . $v[0] . '", "' . $v[1] . '", "' . addslashes($v[2]) . '")';
        }

        $sql = 'INSERT INTO
                    tHRSummary
                (
                    hId,
                    hDate,
                    hStaffId,
                    hLeaveData
                ) VALUES ' . implode(',', $values) . '
                ON DUPLICATE KEY UPDATE
                    hLeaveData = VALUES(hLeaveData);';
        return $this->conn->exeSql($sql);
    }

    /**
     * 確認打卡資料是否異常(依據備註文字判斷)
     * @param string $remark 備註
     * @return bool
     */
    public static function isAbnormal($remark)
    {
        if (empty($remark)) {
            return false;
        }

        $abnormal = ['遲到', '早退', '未打卡', '未簽到', '未簽退'];
        foreach ($abnormal as $v) {
            $v = strip_tags($v);
            if (strpos($remark, $v) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 寫入打卡異常資料
     * @param array $data 打卡異常資料
     * @return bool
     */
    public function insertCheckInAlert($data)
    {
        if (empty($data)) {
            return false;
        }

        $values = [];
        foreach ($data as $v) {
            $values[] = '(UUID(), "' . $v['sStaffId'] . '", "' . $v['sDate'] . '", "' . strip_tags($v['remark']) . '", "' . date('Y-m-d H:i:s') . '")';
        }

        $sql = 'INSERT INTO
                    tStaffCheckInAlert
                (
                    sId,
                    sStaffId,
                    sDate,
                    sReason,
                    sCreatedAt
                ) VALUES ' . implode(',', $values) . ' ON DUPLICATE KEY UPDATE sReason = VALUES(sReason);';
        return $this->conn->exeSql($sql);
    }

    /**
     * 刪除之前異常資料
     * @param int $staffId 員工編號
     * @return bool
     */
    public function removeCheckInAlert($staffId)
    {
        $sql = 'DELETE FROM tStaffCheckInAlert WHERE sStaffId = ' . $staffId;
        return $this->conn->exeSql($sql);
    }

    /**
     * 取得所有請假類別
     * @return array 請假類別
     */
    public function getLeaveType()
    {
        $sql = 'SELECT sId, sType, CASE WHEN sMemo is null OR sMemo = "" THEN sLeaveName ELSE sMemo END as leaveName, sLimit FROM tStaffLeaveType;';
        return $this->conn->all($sql);
    }
}
