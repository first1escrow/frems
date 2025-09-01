<?php
namespace First1\V1\Staff;

require_once dirname(__DIR__) . '/first1DB.php';

class StaffDefaultLeave
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
            self::$instance = new staffDefaultLeave;
        }

        return self::$instance;
    }

    /**
     * 需要取得預設假別名稱
     * @return array 各假期的資訊
     */
    public function getDefaultLeaveDetail()
    {
        $sql = 'SELECT sId, sType, sLeaveName, sMemo FROM tStaffLeaveType WHERE sLimit = "Y" ORDER BY sId ASC;';
        $rs  = $this->conn->all($sql);

        $defaultLeave = [];
        if (! empty($rs)) {
            foreach ($rs as $row) {
                $defaultLeave[$row['sId']] = $row;
            }
        }

        return $defaultLeave;
    }

    /**
     * 取得員工ID、員工姓名、員工部門
     * @param null $staffId 員工ID
     * @return array 員工ID、員工姓名、員工部門
     */
    public function getStaffIds($staffId = null)
    {
        $sql = 'SELECT pId, pName, pDep FROM tPeopleInfo WHERE pId = ' . $staffId;
        if (empty($staffId)) {
            $sql = 'SELECT pId, pName, pDep FROM tPeopleInfo WHERE pJob = 1 AND pId NOT IN (2, 6, 8, 66) ORDER BY pName ASC;';
        }

        $rs = $this->conn->all($sql);
        if (empty($rs)) {
            return [];
        }

        if (count($rs) == 1) {
            return $rs[0];
        }

        foreach ($rs as $row) {
            $staffIds[$row['pId']] = $row;
        }

        return $staffIds;
    }

    /**
     * 取得員工預設假別(包含沒有資料的員工)
     * @param array $defaultLeave 預設假別
     * @param int $staffId 員工ID
     * @return array 員工預設假別
     */
    public function getStaffDefaultLeaveWithStaff($defaultLeave, $staffId)
    {
        $leaveId = implode(',', array_column($defaultLeave, 'sId'));

        $sql = 'SELECT
                sId,
                sStaffId,
                sLeaveId,
                sLeaveDefault,
                sLeaveBalance,
                sLeaveRemark
            FROM
                tStaffLeaveDefault
            WHERE
                sStaffId = ' . $staffId . '
                AND sLeaveId IN (' . $leaveId . ');';
        $rs = $this->conn->all($sql);

        $leaves = [];
        if (empty($rs)) {
            foreach ($defaultLeave as $leaveId => $row) {
                $leaves[$leaveId] = [
                    'sStaffId'      => $staffId,
                    'sLeaveId'      => $leaveId,
                    'sLeaveName'    => empty($row['sMemo']) ? $row['sLeaveName'] : $row['sMemo'],
                    'sLeaveDefault' => 0,
                    'sLeaveBalance' => 0,
                    'sLeaveRemark'  => null,
                ];
            }

            return $leaves;
        }

        foreach ($defaultLeave as $leaveId => $row) {
            $leaves[$leaveId] = [
                'sStaffId'      => $staffId,
                'sLeaveId'      => $leaveId,
                'sLeaveName'    => empty($row['sMemo']) ? $row['sLeaveName'] : $row['sMemo'],
                'sLeaveDefault' => 0,
                'sLeaveBalance' => 0,
            ];

            foreach ($rs as $v) {
                $sLeaveDefault = 0;
                $sLeaveBalance = 0;

                if ($v['sLeaveId'] == $leaveId) {
                    $sLeaveDefault = $v['sLeaveDefault'];
                    $sLeaveBalance = $v['sLeaveBalance'];

                    $leaves[$leaveId] = [
                        'sStaffId'      => $v['sStaffId'],
                        'sLeaveId'      => $leaveId,
                        'sLeaveName'    => empty($row['sMemo']) ? $row['sLeaveName'] : $row['sMemo'],
                        'sLeaveDefault' => $sLeaveDefault,
                        'sLeaveBalance' => $sLeaveBalance,
                        'sLeaveRemark'  => empty($v['sLeaveRemark']) ? '' : $v['sLeaveRemark'],
                    ];
                }
            }
        }
        return $leaves;
    }

    /**
     * 取得員工預設假別(資料庫內)
     * @return array 員工預設假別
     */
    public function getStaffDefaultLeave()
    {
        $sql = 'SELECT
                    a.sId,
                    a.sStaffId,
                    (SELECT pName FROM tPeopleInfo WHERE pId = a.sStaffId) AS sStaffName,
                    (SELECT pDep FROM tPeopleInfo WHERE pId = a.sStaffId) AS sStaffDep,
                    (SELECT dDep FROM tDepartment WHERE dId = sStaffDep) AS depName,
                    a.sLeaveId,
                    (SELECT CASE WHEN sMemo IS NULL OR sMemo = "" THEN sLeaveName ELSE sMemo END AS leaveName FROM tStaffLeaveType WHERE sId = a.sLeaveId) AS sLeaveName,
                    a.sLeaveDefault,
                    a.sLeaveBalance,
                    a.sLeaveRemark,
                    b.pOnBoard
                FROM
                    tStaffLeaveDefault AS a
                JOIN
                    tPeopleInfo AS b ON a.sStaffId = b.pId AND b.pJob = 1
                ORDER BY
                    b.pOnBoard ASC
                ;';
        $rs = $this->conn->all($sql);

        if (empty($rs)) {
            return [];
        }

        $data = [];
        foreach ($rs as $leaveId => $row) {
            $data[$row['sStaffId']][] = [
                'sStaffId'      => $row['sStaffId'],
                'sStaffName'    => $row['sStaffName'],
                'sStaffDep'     => $row['sStaffDep'],
                'depName'       => $row['depName'],
                'sLeaveId'      => $row['sLeaveId'],
                'sLeaveName'    => $row['sLeaveName'],
                'sLeaveDefault' => $row['sLeaveDefault'],
                'sLeaveBalance' => $row['sLeaveBalance'],
                'sLeaveRemark'  => $row['sLeaveRemark'],
            ];
        }

        return $data;
    }

    /**
     * 設定員工預設假別
     * @param array $defaultLeave 預設假別
     * @param int $staffId 員工ID
     * @return bool
     */
    public function setStaffDefaultLeave($defaultLeave, $staffId)
    {
        $values = [];
        foreach ($defaultLeave as $leave) {
            $values[] = '(' . $leave['sStaffId'] . ', ' . $leave['sLeaveId'] . ', ' . $leave['sLeaveDefault'] . ', ' . $leave['sLeaveBalance'] . ', ' . $leave['sLeaveRemark'] . ', NOW())';

        }

        $sql = 'INSERT INTO
                    tStaffLeaveDefault
                (
                    sStaffId,
                    sLeaveId,
                    sLeaveDefault,
                    sLeaveBalance,
                    sLeaveRemark,
                    sCreatedAt
                ) VALUES ' . implode(',', $values) . '
                ON DUPLICATE KEY UPDATE
                    sLeaveDefault = VALUES(sLeaveDefault),
                    sLeaveBalance = VALUES(sLeaveBalance),
                    sLeaveRemark = VALUES(sLeaveRemark);';
        return $this->conn->exeSql($sql);
    }

    /**
     * 取得員工預設假別歷史紀錄
     * @param int|null $pId 員工ID
     * @return array 員工預設假別歷史紀錄
     */
    public function getStaffDefaultLeaveHistory($pId = null)
    {
        //員工資訊
        $staffs = $this->getStaffs($pId);

        if (empty($staffs)) {
            return [];
        }

        //假別資訊
        $leaveNames = $this->getDefaultLeaveDetail();

        $data = [];
        foreach ($staffs as $staffId => $staff) {
            list($current, $last)     = $this->getStaffDefaultLeaveDetail($staff['sStaffId'], $leaveNames);
            $data[$staff['sStaffId']] = ['name' => $staff['sStaffName'], 'dept' => $staff['depName'], 'current' => $current, 'last' => $last];
        }

        return $data;
    }

    /**
     * 取得員工資料
     * @param int|null $pId 員工ID
     * @return array 員工資料
     */
    public function getStaffs($pId = null)
    {
        $sql = empty($pId) ? 'AND pId NOT IN (2, 6, 8, 66)' : 'AND pId = ' . $pId;

        $sql = 'SELECT
                    pId as sStaffId,
                    pName as sStaffName,
                    pDep as sStaffDep,
                    (SELECT dDep FROM tDepartment WHERE dId = a.pDep) as depName,
                    pOnBoard
                FROM
                    tPeopleInfo AS a
                WHERE
                    pJob = 1
                    ' . $sql . '
                ORDER BY
                    pOnBoard
                ASC;';

        return $this->conn->all($sql);
    }

    /**
     * 取得員工預設假別歷史紀錄
     * @param int $staffId 員工ID
     * @param array $leaveNames 假別資訊
     * @return array 員工預設假別歷史紀錄 [最近一筆, 倒數第二筆]
     */
    private function getStaffDefaultLeaveDetail($staffId, $leaveNames)
    {
        //取得每個假別以日期排序的最近一筆
        $current = $this->getDefaultLeaveHistoryCurrent($staffId, $leaveNames);

        //取得每個假別以日期排序的倒數第二筆
        $last = $this->getDefaultLeaveHistoryLast($staffId, $current, $leaveNames);

        //20250225 依據家津要求，將 "年度特休假[1]" 的 "上次預設時數" 與 "上次剩餘時數" 設定為等同於 "前剩餘特休假[2]" 的 "本次預設時數" 與 "本次剩餘時數" 一致
        if ($current[2] && $last[1]) {
            // $last[1] = $current[2];
            $_last          = $current[2];
            $_last['sDate'] = $last[1]['sDate'];
            $last[1]        = $_last;

            $_last = null;unset($_last);
        }

        return [$current, $last];
    }

    /**
     * 取得員工預設假別歷史紀錄(最近一筆)
     * @param int $staffId 員工ID
     * @param array $leaveNames 假別資訊
     * @return array 員工預設假別歷史紀錄
     */
    private function getDefaultLeaveHistoryCurrent($staffId, $leaveNames)
    {
        foreach ($leaveNames as $leaveId => $row) {
            $sql = 'SELECT sStaffId, sDate, sLeaveId, sLeaveDefault, sLeaveBalance FROM tStaffLeaveDefaultHistory WHERE sStaffId = ' . $staffId . ' AND sLeaveId = ' . $leaveId . ' ORDER BY sDate DESC LIMIT 1;';
            $rs  = $this->conn->one($sql);

            if (! empty($rs)) {
                $leaveName = $leaveNames[$rs['sLeaveId']]['sLeaveName'];
                if (! empty($leaveNames[$rs['sLeaveId']]['sMemo'])) {
                    $leaveName = $leaveNames[$rs['sLeaveId']]['sMemo'];
                }

                $data[$rs['sLeaveId']] = array_merge(['leaveName' => $leaveName], $rs);
            }
        }

        return $data;
    }

    /**
     * 取得員工預設假別歷史紀錄(倒數第二筆)
     * @param int $staffId 員工ID
     * @param array $current 最近一筆
     * @param array $leaveNames 假別資訊
     * @return array 員工預設假別歷史紀錄
     */
    private function getDefaultLeaveHistoryLast($staffId, $current, $leaveNames)
    {
        if (empty($current)) {
            return [];
        }

        $data = [];
        foreach ($current as $v) {
            $sql = 'SELECT sStaffId, sDate, sLeaveId, sLeaveDefault, sLeaveBalance FROM tStaffLeaveDefaultHistory WHERE sStaffId = ' . $v['sStaffId'] . ' AND sLeaveId = ' . $v['sLeaveId'] . ' AND sDate < "' . $v['sDate'] . '" ORDER BY sDate DESC LIMIT 1;';
            $rs  = $this->conn->one($sql);

            if (! empty($rs)) {
                $leaveName = $leaveNames[$rs['sLeaveId']]['sLeaveName'];
                if (! empty($leaveNames[$rs['sLeaveId']]['sMemo'])) {
                    $leaveName = $leaveNames[$rs['sLeaveId']]['sMemo'];
                }

                $data[$rs['sLeaveId']] = array_merge(['leaveName' => $leaveName], $rs);
            }
        }

        return $data;
    }
}
