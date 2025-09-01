<?php
require_once dirname(__DIR__) . '/staff.class.php';

use First1\V1\Staff\Staff;

trait CheckIn
{
    public $WEEKEND   = [1 => '週一', 2 => '週二', 3 => '週三', 4 => '週四', 5 => '週五', 6 => '週六', 7 => '週日'];
    public $BEGINDATE = '2025-01-01 00:00:00';

    /**
     * 取得員工指定月份打卡資料
     * @param string $from 起始日期
     * @param string $to 結束日期
     * @param int $member_id 員工編號
     * @param bool $isDaily 是否為單日
     */
    public function getCheckInOutList($from, $to, $member_id, $isDaily = false, $isHtml = true)
    {
        $member = $this->getMemberData($member_id);
        if (empty($member)) {
            return [];
        }

        $data = $this->verifyCheckInData($from, $to, $member);

        $startDate  = new DateTime($from);
        $endDate    = new DateTime($to);
        $periodData = $this->getPeriodData($startDate, $endDate, $member, $data, $isDaily, $isHtml);

        return [
            'member'     => $member,
            'periodData' => $periodData,
        ];
    }

    /**
     * 取得員工姓名
     * @param int $member_id 員工編號
     * @return string 員工資料
     */
    private function getMemberData($member_id)
    {
        $sql = 'SELECT pId, pDep, pName FROM tPeopleInfo WHERE pId = ' . $member_id;
        return $this->conn->one($sql);
    }

    /**
     * 驗證打卡資料
     * @param array $from 起始日期
     * @param array $to 結束日期
     * @param array $member 員工資料
     * @return array 驗證後的打卡資料
     */
    private function verifyCheckInData($from, $to, $member)
    {
        $result = $this->getCheckInData($from, $to, $member['pId']);
        if (empty($result)) {
            return [];
        }

        $data = [];
        foreach ($result as $aRow) {
            $in          = '';
            $inLatitude  = '';
            $inLongitude = '';
            $inFrom      = '';
            $inRemark    = '';
            if (! empty($aRow['IN'])) {
                $dateTimes = array_column($aRow['IN'], 'sDateTime');
                $in        = min($dateTimes);
                $index     = array_search($in, $dateTimes);

                $inLatitude  = $aRow['IN'][$index]['sLatitude'];
                $inLongitude = $aRow['IN'][$index]['sLongitude'];
                $inFrom      = $aRow['IN'][$index]['sFrom'];
                $inRemark    = $aRow['IN'][$index]['sRemark'];
                $in          = date('H:i:s', $in);
            }

            $out          = '';
            $outLatitude  = '';
            $outLongitude = '';
            $outFrom      = '';
            $outRemark    = '';
            if (! empty($aRow['OUT'])) {
                $dateTimes = array_column($aRow['OUT'], 'sDateTime');
                $out       = max($dateTimes);
                $index     = array_search($out, $dateTimes);

                $outLatitude  = $aRow['OUT'][$index]['sLatitude'];
                $outLongitude = $aRow['OUT'][$index]['sLongitude'];
                $outFrom      = $aRow['OUT'][$index]['sFrom'];
                $outRemark    = $aRow['OUT'][$index]['sRemark'];
                $out          = date('H:i:s', $out);
            }

            $row = [
                'DT_RowId'     => 'row_' . $aRow['sStaffId'] . '_' . $aRow['sDate'],
                'sId'          => $aRow['sId'],
                'sStaffId'     => $aRow['sStaffId'],
                'pName'        => $aRow['pName'],
                'sDate'        => $aRow['sDate'],
                'sIn'          => $in,
                'sOut'         => $out,
                'inFrom'       => $inFrom,
                'outFrom'      => $outFrom,
                'inLatitude'   => $inLatitude,
                'inLongitude'  => $inLongitude,
                'outLatitude'  => $outLatitude,
                'outLongitude' => $outLongitude,
                'sRemark'      => $inRemark . ' ' . $outRemark,
            ];

            $data[$aRow['sDate']] = $row;
        }

        return $data;
    }

    /**
     * 整合單日打卡資料
     * @param array $from 起始日期
     * @param array $to 結束日期
     * @param int $member_id 員工編號
     * @return array 單日打卡資料
     */
    private function getCheckInData($from, $to, $member_id)
    {
        $rResult = $this->getCheckInRecords($from, $to, $member_id);
        if (empty($rResult)) {
            return [];
        }

        $result = [];
        foreach ($rResult as $aRow) {
            $key   = $aRow['sStaffId'] . '_' . $aRow['sDate'];
            $inOut = $aRow['sInOut'];

            $result[$key]['sId']      = $aRow['sId'];
            $result[$key]['sStaffId'] = $aRow['sStaffId'];
            $result[$key]['pName']    = $aRow['pName'];
            $result[$key]['sDate']    = $aRow['sDate'];

            $result[$key][$aRow['sInOut']][] = [
                'sDateTime'  => strtotime($aRow['sDateTime']),
                'sFrom'      => $aRow['sFrom'],
                'sLatitude'  => $aRow['sLatitude'],
                'sLongitude' => $aRow['sLongitude'],
                'sRemark'    => $aRow['sRemark'],
            ];
        }

        return $result;
    }

    /**
     * 取得員工打卡紀錄
     * @param string $from 起始日期
     * @param string $to 結束日期
     * @param int $member_id 員工編號
     * @return array 打卡紀錄
     */
    private function getCheckInRecords($from, $to, $member_id)
    {
        $aColumns = [
            'sId',
            'sStaffId',
            'pName',
            'sDate',
            'sDateTime',
            'sInOut',
            'sFrom',
            'sLatitude',
            'sLongitude',
            'sRemark',
        ];

        $sTable = '
                (
                    SELECT
                        a.sId,
                        a.sStaffId,
                        b.pName,
                        a.sDateTime,
                        DATE(a.sDateTime) as sDate,
                        a.sInOut,
                        a.sFrom,
                        a.sLatitude,
                        a.sLongitude,
                        a.sRemark
                    FROM
                        tStaffCheckIn AS a
                    JOIN
                        tPeopleInfo AS b ON a.sStaffId = b.pId
                    WHERE
                        a.sDateTime >= "' . $from . '"
                        AND a.sDateTime <= "' . $to . '"
                        AND a.sStaffId = ' . $member_id . '
                ) tb  ';

        $sOrder = 'ORDER BY sDateTime DESC';

        $sQuery = "
                    SELECT SQL_CALC_FOUND_ROWS sId, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
                    FROM   $sTable
                    $sOrder
                ";

        return $this->conn->all($sQuery);
    }

    /**
     * 取得打卡資訊
     * @param DateTime $startDate 起始日期
     * @param DateTime $endDate 結束日期
     * @param array $member 員工資料
     * @param array $data 打卡資料
     * @param bool $isDaily 是否為單日
     * @param bool $isHtml 是否顯示 html tag
     * @return array 打卡資訊
     */
    private function getPeriodData($startDate, $endDate, $member, $data, $isDaily = false, $isHtml = true)
    {
        //取得補打卡資訊
        $sql = 'SELECT sApplyDate, sApplyType, sSupervisor, sApproval, sApprovalDateTime, sStatus FROM tStaffCheckInApply WHERE sStaffId = ' . $member['pId'] . ' AND sApplyDate BETWEEN "' . $startDate->format('Y-m-d') . '" AND "' . $endDate->format('Y-m-d') . '";';
        $rs  = $this->conn->all($sql);

        $checkInOut       = []; //補打卡申請中紀錄
        $checkInOutReject = []; //補打卡申請被拒紀錄
        $checkInOutOk     = []; //補打卡申請完成紀錄

        if (! empty($rs)) {
            foreach ($rs as $v) {
                $key = $v['sApplyDate'];
                if ($v['sStatus'] == 'N') {
                    $checkInOut[$key][] = $v;
                }

                if ($v['sStatus'] == 'R') {
                    $checkInOutReject[$key][] = $v;
                }

                if ($v['sStatus'] == 'Y') {
                    $checkInOutOk[$key][] = $v;
                }
            }
        }

        // 迴圈顯示每一天的日期
        $from = $startDate->format('Y-m-d H:i:s');
        $to   = $endDate->format('Y-m-d H:i:s');

        $staff = new Staff;
        $staff->setDateTimePeriod($startDate->format('Y-m-d 00:00:00'), $endDate->format('Y-m-d 23:59:59'));
        if ($isDaily) {
            $staff->leaveApplyData = $this->getDailyLeave($startDate->format('Y-m-d 09:00:00'), $endDate->format('Y-m-d 17:30:00'));
        }

        $allowLateData   = $this->getAllowLate($from, $to, $member['pId']); //取得每月允許遲到時間
        $holidays        = $this->getHolidays($from, $to);                  //取得假日
        $workingHolidays = $this->getHolidays($from, $to, true);            //取得補班日
        $leaves          = $this->getLeaves($from, $to);                    //取得請假
        $overtimes       = $this->getOvertimes($from, $to);                 //取得加班時間
        $lockDate        = $this->getLastLockDate();                        //取得最後鎖定日期

        while (($endDate >= $startDate) && ($endDate->format('Y-m-d H:i:s') >= $this->BEGINDATE)) {
            $today        = $endDate->format('Y-m-d');
            $remark       = [];
            $noRemarkData = true;
            $lock         = ($today <= $lockDate) ? 'Y' : 'N';

            $row = [
                'DT_RowId'     => 'row_' . $member['pId'] . '_' . $today,
                'sId'          => '',
                'sStaffId'     => $member['pId'],
                'pName'        => $member['pName'],
                'sDate'        => $today,
                'sIn'          => '',
                'sOut'         => '',
                'sInHoliday'   => '',
                'sOutHoliday'  => '',
                'inFrom'       => '',
                'outFrom'      => '',
                'inLatitude'   => '',
                'inLongitude'  => '',
                'outLatitude'  => '',
                'outLongitude' => '',
                'css'          => '',
                'week'         => $endDate->format('N'),
                'weekName'     => $this->WEEKEND[$endDate->format('N')],
                'apply'        => empty($checkInOut[$today]) ? false : $checkInOut[$today],
                'checkIn'      => '',
                'checkOut'     => '',
                'leaveAm'      => '',
                'leavePm'      => '',
            ];

            //是否周末
            if ($staff->isWeekend($endDate->format('Y-m-d'))) {
                $row['pName'] = '';
                $row['css']   = ($endDate->format('N') == 6) ? '#C0C0C0' : '#C0B8BD';
                $noRemarkData = false;
            }

            //是否節假日
            $holiday = '';
            if ($staff->isHoliday($today, '09:00:00') && $staff->isHoliday($today, '17:30:00')) {
                $holiday = $staff->isHoliday($today);

                $row['pName']   = '';
                $row['css']     = '#F08784';
                $row['holiday'] = true;
                $remark[]       = empty($holiday) ? '' : $holiday;
                $noRemarkData   = false;
            }

            //今日有打卡紀錄
            if (! empty($data[$today])) {
                $row = $data[$today];

                $row['css']         = empty($row['css']) ? '' : $row['css'];
                $row['week']        = $endDate->format('N');
                $row['weekName']    = $this->WEEKEND[$endDate->format('N')];
                $row['apply']       = empty($checkInOut[$today]) ? false : $checkInOut[$today];
                $row['sInHoliday']  = '';
                $row['sOutHoliday'] = '';
                $row['checkIn']     = $this->getCheckInTimeByDate($member['pId'], $holidays, $workingHolidays, $leaves, $today, $overtimes, $row['sIn']);
                $row['checkOut']    = $this->getCheckOutTimeByDate($member['pId'], $holidays, $workingHolidays, $leaves, $today, $overtimes, $row['sOut']);

                //是否節假日
                if ($holiday) {
                    $row['holiday'] = true;
                }

                //取得註解
                $remark_status = $staff->getRemark($row['sDate'], $row['sIn'], $row['sOut'], $member['pId'], $allowLateData, $row['checkIn'], $row['checkOut'], $workingHolidays, $overtimes, $isHtml);

                //2024-12-23 今日是否有期間內的請假
                $_leaveDuringData = $this->getLeaveDuringData($today . ' 00:00:00', $today . ' 23:59:59', $leaves, $member['pId']);

                $leaveInDay = [];
                if (! empty($_leaveDuringData)) {
                    foreach ($_leaveDuringData as $_duringData) {
                        if ($_duringData['sLeaveFromDateTime'] < $today . ' 12:00:00') {
                            $leaveInDay['in'] = $_duringData;
                        }

                        if ($_duringData['sLeaveFromDateTime'] >= $today . ' 12:00:00') {
                            $leaveInDay['out'] = $_duringData;
                        }
                    }
                }

                //2024-12-17 簽到時間延後是否因為請假
                $_sId = [];
                if ($row['checkIn'] > '09:00:59') {
                    if ($_leaveData = $this->getLeaveData($today . ' ' . $row['sIn'], $leaves, $member['pId'])) {
                        $_sId[] = $_leaveData['sId'];
                        if (! empty($isHtml)) {
                            $row['leaveAm'] .= '<a href="Javascript:void(0);" style="font-size:9pt;" onclick="showLeave(' . $_leaveData['sId'] . ')">(假)</a>';
                        }
                    }
                }

                if (! empty($leaveInDay['in']) && ! in_array($leaveInDay['in']['sId'], $_sId)) {
                    $_in = $today . ' ' . $row['sIn'];
                    if (($_in >= $leaveInDay['in']['sLeaveFromDateTime']) && ($_in <= $leaveInDay['in']['sLeaveToDateTime'])) {
                        $_sId[] = $leaveInDay['in']['sId'];

                        if (! empty($isHtml)) {
                            $row['leaveAm'] .= '<a href="Javascript:void(0);" style="font-size:9pt;" onclick="showLeave(' . $leaveInDay['in']['sId'] . ')">(假)</a>';
                        }
                    }
                    $_in = null;unset($_in);
                }

                //2024-12-17 簽退時間提前是否因為請假
                if ($row['checkOut'] < '17:30:00') {
                    if ($_leaveData = $this->getLeaveData($today . ' ' . $row['sOut'], $leaves, $member['pId'])) {
                        if (! in_array($_leaveData['sId'], $_sId)) {
                            $_sId[] = $_leaveData['sId'];
                            if (! empty($isHtml)) {
                                $row['leavePm'] .= '<a href="Javascript:void(0);" style="font-size:9pt;" onclick="showLeave(' . $_leaveData['sId'] . ')">(假)</a>';
                            }
                        }
                    }
                }

                if (! empty($leaveInDay['out']) && ! in_array($leaveInDay['out']['sId'], $_sId) && ! empty($isHtml)) {
                    $row['leavePm'] .= '<a href="Javascript:void(0);" style="font-size:9pt;" onclick="showLeave(' . $leaveInDay['out']['sId'] . ')">(假)</a>';
                }

                if (! empty($remark_status)) {
                    $noRemarkData = false;

                    foreach ($remark_status as $v) {
                        if (preg_match('/遲到/iu', $v)) {
                            if (! empty($isHtml) && ($lock != 'Y')) {
                                $row['leaveAm'] = '<a href="Javascript:void(0);" onclick="leaveApply(\'' . $today . '\', \'' . $today . '\')">' . $row['sIn'] . '</a>';
                            }
                        }

                        if (preg_match('/早退/iu', $v)) {
                            if (! empty($isHtml) && ($lock != 'Y')) {
                                $row['leavePm'] = '<a href="Javascript:void(0);" onclick="leaveApply(\'' . $today . '\', \'' . $today . '\')">' . $row['sOut'] . '</a>';
                            }
                        }
                    }

                    $remark = array_merge($remark, $remark_status);
                }
                $noRemarkData = false;

                if ($staff->isWeekend($endDate->format('Y-m-d'))) {
                    $row['css'] = ($endDate->format('N') == 6) ? '#C0C0C0' : '#C0B8BD';
                }
            }

            if ($noRemarkData && ($endDate->format('Y-m-d') <= date('Y-m-d'))) {
                $leaveAM = $staff->isLeave($endDate->format('Y-m-d') . ' 09:00:59', $member['pId']);
                $leavePM = $staff->isLeave($endDate->format('Y-m-d') . ' 17:30:00', $member['pId']);

                if ($leaveAM && $leavePM) {
                    $row['leave'] = $leaveAM;
                    $row['css']   = '#0FFFFF';

                    $_remark1 = $leaveAM['leaveName'];
                    $_remark2 = $leavePM['leaveName'];
                    if (! empty($isHtml)) {
                        $_remark1 = '<a href="Javascript:void(0);" style="font-size:9pt;" onclick="showLeave(' . $leaveAM['sId'] . ')">' . $leaveAM['leaveName'] . '</a>';
                        $_remark2 = '<a href="Javascript:void(0);" style="font-size:9pt;" onclick="showLeave(' . $leavePM['sId'] . ')">' . $leavePM['leaveName'] . '</a>';
                    }

                    $remark[] = empty($leaveAM['leaveName']) ? '請假' : $_remark1;
                    $remark[] = empty($leavePM['leaveName']) ? '請假' : $_remark2;
                }

                if ($leaveAM && ! $leavePM) {
                    $row['leaveAM'] = $leaveAM;

                    $_remark  = empty($isHtml) ? $leaveAM['leaveName'] : '<a href="Javascript:void(0);" style="font-size:9pt;" onclick="showLeave(' . $leaveAM['sId'] . ')">' . $leaveAM['leaveName'] . '</a>';
                    $remark[] = empty($leaveAM['leaveName']) ? '請假' : $_remark;
                }

                if ($leavePM && ! $leaveAM) {
                    $row['leavePM'] = $leavePM;

                    $_remark  = empty($isHtml) ? $leavePM['leaveName'] : '<a href="Javascript:void(0);" style="font-size:9pt;" onclick="showLeave(' . $leavePM['sId'] . ')">' . $leavePM['leaveName'] . '</a>';
                    $remark[] = empty($leavePM['leaveName']) ? '請假' : $_remark;
                }

                if (empty($leaveAM) && empty($leavePM)) {
                    $remark[] = '未打卡';
                }
            }

            $row['remark'] = implode(', ', array_filter(array_unique($remark)));

            //補上班卡、補下班卡
            $this->appliedCheckInOut($row, $checkInOutOk, $today, $isHtml);

            //補打卡申請中
            $this->applingCheckInOut($row, $checkInOut, $today, $isHtml);

            //申請補打卡
            $countIn  = 0;
            $countOut = 0;
            if (! empty($checkInOutReject[$today])) {
                foreach ($checkInOutReject[$today] as $v) {
                    if ($v['sApplyType'] == 'IN') {
                        $countIn++;
                    }

                    if ($v['sApplyType'] == 'OUT') {
                        $countOut++;
                    }
                }
            }

            //未打上班卡時處理
            $this->applyCheckIn($row, $endDate, $today, $countIn, $isHtml);

            //未打下班卡時處理
            $this->applyCheckOut($row, $endDate, $today, $countOut, $isHtml);

            //避免今日未打卡、未簽到、未簽退顯示
            $this->noCheckInOut($row, $endDate, $today);

            if (! empty($row['leaveAm'])) {
                $_sIn = strip_tags($row['leaveAm']);

                if ($row['sIn'] == $_sIn) {
                    $row['sIn'] = $row['leaveAm'];
                } else {
                    $row['sIn'] .= $row['leaveAm'];
                }

                $_sIn = null;unset($_sIn);
            }

            if (! empty($row['leavePm'])) {
                $_sOut = strip_tags($row['leavePm']);

                if ($row['sOut'] == $_sOut) {
                    $row['sOut'] = $row['leavePm'];
                } else {
                    $row['sOut'] .= $row['leavePm'];
                }

                $_sOut = null;unset($_sOut);
            }

            //2025-03-28 鎖定禁止申請補打卡或請假
            if ($lock == 'Y') {
                $row['sIn']  = strip_tags($row['sIn']);
                $row['sOut'] = strip_tags($row['sOut']);
            }

            $output['data'][] = $row;
            $endDate->modify('-1 day'); // 減少一天
        }

        return empty($output['data']) ? ['data' => []] : $output;
    }

    /**
     * 取得打卡簽到時間
     * @param int $staffId 員工編號
     * @param array $holidays 假日資料
     * @param array $workingHolidays 補班資料
     * @param array $leaves 請假資料
     * @param string $date 日期
     * @param array $overtimes 加班資料
     * @param string $checkTime 簽到時間
     * @return string 簽到時間
     */
    public function getCheckInTimeByDate($staffId, $holidays, $workingHolidays, $leaves, $date, $overtimes = [], $checkTime = null)
    {
        $checkInTime = '09:00:59';

        $modified = [];
        if (! empty($holidays)) {
            foreach ($holidays as $v) {
                if ($date >= $v['hFromDate'] && $date <= $v['hToDate'] && ($v['hFromTime'] != '00:00:00') && ($v['hFromTime'] <= '09:00:00')) {
                    $checkInTime = substr($v['hToTime'], 0, 5) . ':59';
                    $checkInTime = (($checkInTime > '12:00:00') && ($checkInTime <= '13:00:00')) ? '13:00:59' : $checkInTime;
                    $modified[]  = 'H';

                    break;
                }
            }
        }

        if (! empty($workingHolidays)) {
            foreach ($workingHolidays as $v) {
                if ($date >= $v['hFromDate'] && $date <= $v['hToDate'] && ($v['hFromTime'] != '00:00:00') && ($v['hFromTime'] > '09:00:00')) {
                    $checkInTime = substr($v['hToTime'], 0, 5) . ':59';
                    $checkInTime = (($checkInTime > '12:00:00') && ($checkInTime <= '13:00:00')) ? '13:00:59' : $checkInTime;
                    $modified[]  = 'W';

                    break;
                }
            }
        }

        if (! empty($leaves[$staffId])) {
            foreach ($leaves[$staffId] as $v) {
                $leaveFromDate = date('Y-m-d', $v['sLeaveFromTmestamp']);
                $leaveFromTime = date('H:i:s', $v['sLeaveFromTmestamp']);
                $leaveToDate   = date('Y-m-d', $v['sLeaveToTimestamp']);
                $leaveToTime   = date('H:i:s', $v['sLeaveToTimestamp']);

                //同一天
                if (($date == $leaveFromDate) && ($date == $leaveToDate)) {
                    if (($leaveFromTime != '00:00:00') && ($leaveFromTime <= '09:00:00')) { //請假開始時間小於等於09:00
                        $checkInTime = substr($leaveToTime, 0, 5) . ':59';                      //以請假結束時間為打卡時間
                        $checkInTime = (($checkInTime >= '12:00:00') && ($checkInTime <= '13:00:00')) ? '13:00:59' : $checkInTime;
                        $modified[]  = 'L';

                        break;
                    }
                }

                //請假開始日
                if (($date == $leaveFromDate) && ($date < $leaveToDate)) {
                    $checkInTime = '09:00:59';
                    $modified[]  = 'L';

                    break;
                }

                //請假中間日
                if (($date > $leaveFromDate) && ($date < $leaveToDate)) {
                    $checkInTime = '09:00:59';
                    $modified[]  = 'L';

                    break;
                }

                //請假結束日
                if (($date == $leaveToDate) && ($date > $leaveFromDate)) {
                    if (($leaveToTime != '23:59:59') && ($leaveToTime < '17:30:00')) {
                        $checkInTime = substr($leaveToTime, 0, 5) . ':59';
                        $checkInTime = (($checkInTime >= '12:00:00') && ($checkInTime <= '13:00:00')) ? '13:00:59' : $checkInTime;
                        $modified[]  = 'L';

                        break;
                    }
                }
            }
        }

        if (! empty($overtimes) && (in_array($modified, ['H', 'L']) || in_array(date('N', strtotime($date)), [6, 7]))) {
            if (isset($overtimes[$staffId]) && ! empty($checkInTime)) {
                // 2024-12-23 修正加班的上下班打卡時間
                $checkInTime = substr($checkTime, 0, 5) . ':59';
            }
        }

        return $checkInTime;
    }

    /**
     * 取得打卡簽退時間
     * @param int $staffId 員工編號
     * @param array $holidays 假日資料
     * @param array $workingHolidays 補班資料
     * @param array $leaves 請假資料
     * @param string $date 日期
     * @param array $overtimes 加班資料
     * @param string $checkTime 簽退時間
     * @return string 簽退時間
     */
    public function getCheckOutTimeByDate($staffId, $holidays, $workingHolidays, $leaves, $date, $overtimes = [], $checkTime = null)
    {
        $checkInTime = '17:30:00';

        $modified = [];
        if (! empty($holidays)) {
            foreach ($holidays as $v) {
                if ($date >= $v['hFromDate'] && $date <= $v['hToDate'] && ($v['hToTime'] != '23:59:59') && ($v['hToTime'] >= '17:30:00')) {
                    $checkInTime = $v['hFromTime'];
                    $checkInTime = (($checkInTime > '12:00:00') && ($checkInTime <= '13:00:00')) ? '12:00:00' : $checkInTime;
                    $modified[]  = 'H';

                    break;
                }
            }
        }

        if (! empty($workingHolidays)) {
            foreach ($workingHolidays as $v) {
                if ($date >= $v['hFromDate'] && $date <= $v['hToDate'] && ($v['hToTime'] != '23:59:59') && ($v['hToTime'] < '17:30:00')) {
                    $checkInTime = $v['hToTime'];
                    $checkInTime = (($checkInTime > '12:00:00') && ($checkInTime <= '13:00:00')) ? '12:00:00' : $checkInTime;
                    $modified[]  = 'W';

                    break;
                }
            }
        }

        if (! empty($leaves[$staffId])) {
            foreach ($leaves[$staffId] as $v) {
                $leaveFromDate = date('Y-m-d', $v['sLeaveFromTmestamp']);
                $leaveFromTime = date('H:i:s', $v['sLeaveFromTmestamp']);
                $leaveToDate   = date('Y-m-d', $v['sLeaveToTimestamp']);
                $leaveToTime   = date('H:i:s', $v['sLeaveToTimestamp']);

                //同一天
                if (($date == $leaveFromDate) && ($date == $leaveToDate)) {
                    if (($leaveToTime != '23:59:59') && ($leaveToTime >= '17:30:00')) {
                        $checkInTime = $leaveFromTime;
                        $checkInTime = (($checkInTime >= '12:00:00') && ($checkInTime <= '13:00:00')) ? '12:00:00' : $checkInTime;
                        $modified[]  = 'L';

                        break;
                    }
                }

                //請假開始日
                if (($date == $leaveFromDate) && ($date < $leaveToDate)) {
                    if (($leaveFromTime != '00:00:00') && ($leaveFromTime > '09:00:00')) {
                        $checkInTime = $leaveFromTime;
                        $checkInTime = (($checkInTime > '12:00:00') && ($checkInTime <= '13:00:00')) ? '12:00:00' : $checkInTime;
                        $modified[]  = 'L';

                        break;
                    }
                }

                //請假中間日
                if (($date > $leaveFromDate) && ($date < $leaveToDate)) {
                    $checkInTime = '17:30:00';
                    $modified[]  = 'L';

                    break;
                }

                //請假結束日
                if (($date == $leaveToDate) && ($date > $leaveFromDate)) {
                    $checkInTime = '17:30:00';
                    $modified[]  = 'L';

                    break;
                }
            }
        }

        if (! empty($overtimes) && (in_array($modified, ['H', 'L']) || in_array(date('N', strtotime($date)), [6, 7]))) {
            if (isset($overtimes[$staffId]) && ! empty($checkInTime)) {
                $checkInTime = $checkTime;
            }
        }

        return $checkInTime;
    }

    /**
     * 取得假日資料
     * @param string $from 開始日期時間
     * @param string $to 結束日期時間
     * @param bool $makeUpWork 是否為補班日 (true: 是, false: 否)
     * @return array 假日資料
     */
    public function getHolidays($from, $to, $makeUpWork = false)
    {
        $from       = strtotime($from);
        $to         = strtotime($to);
        $makeUpWork = $makeUpWork ? 'Y' : 'N';

        $sql = 'SELECT hName, hFromDate, hToDate, hFromTime, hToTime, hMakeUpWorkday FROM tHoliday WHERE hFromTimestamp >= :from AND hToTimestamp <= :to AND hMakeUpWorkday = :makeUpWork;';
        return $this->conn->all($sql, ['from' => $from, 'to' => $to, 'makeUpWork' => $makeUpWork]);
    }

    /**
     * 取得請假資料
     * @param string $from 開始日期時間
     * @param string $to 結束日期時間
     * @return array 請假資料
     */
    public function getLeaves($from, $to)
    {
        $from = strtotime($from);
        $to   = strtotime($to);

        $sql = 'SELECT sId, sApplicant, sLeaveId, sLeaveFromDateTime, sLeaveToDateTime, sLeaveFromTmestamp, sLeaveToTimestamp FROM tStaffLeaveApply WHERE sLeaveFromTmestamp <= :to AND sLeaveToTimestamp >= :from AND sStatus = "Y";';
        $rs  = $this->conn->all($sql, ['from' => $from, 'to' => $to]);

        if (empty($rs)) {
            return [];
        }

        $data = [];
        foreach ($rs as $v) {
            $data[$v['sApplicant']][] = $v;
        }

        return $data;
    }

    /**
     * 取得允許遲到紀錄
     * @param string $from_date 起始日期
     * @param string $to_date 結束日期
     * @param string $staffId 員工編號
     * @return array 紀錄 or 空陣列
     */
    public function getAllowLate($from_date, $to_date, $staffId)
    {
        $sql = 'SELECT sStaffId, sLateDate, sCheckInTime, sCheckInOutIndex, sOrder FROM tStaffMonthlyLateAllow WHERE sStaffId = :staff AND sLateDate >= :from_date AND sLateDate <= :to_date;';
        $rs  = $this->conn->all($sql, ['staff' => $staffId, 'from_date' => date('Y-m-d', strtotime($from_date)), 'to_date' => date('Y-m-d', strtotime($to_date))]);

        return empty($rs) ? [] : $rs;
    }

    /**
     * 取得指定日期的休假資料
     * @param string $from 開始日期
     * @param string $to 結束日期
     * @return array 休假資料
     */
    public function getDailyLeave($from, $to)
    {
        $from = preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}/", $from) ? strtotime($from) : $from;
        $to   = preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}/", $to) ? strtotime($to) : $to;

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
        return $this->conn->all($sql, ['from' => $from, 'to' => $to]);
    }

    /**
     * 檢查員工是否已打卡
     * @param int $staff 員工編號
     * @param string $date 日期 (Y-m-d)
     * @param string $inOut 上下班 (IN: 上班, OUT: 下班)
     * @param int $exceptId 例外編號
     * @param bool 是否已打卡
     */
    public function isCheckIn($staff, $date, $inOut, $exceptId = null)
    {
        $date  = date('Y-m-d', strtotime($date));
        $inOut = strtoupper($inOut);
        $bind  = ['staff' => $staff, 'date' => $date, 'inOut' => $inOut];

        $sql = 'SELECT sId FROM tStaffCheckIn WHERE sStaffId = :staff AND DATE(sDateTime) = Date(:date) AND sInOut = :inOut';
        if (! empty($exceptId)) {
            $sql .= ' AND sId != :sId';
            $bind['sId'] = $exceptId;
        }

        $rs = $this->conn->one($sql, $bind);

        return empty($rs) ? false : true;
    }

    /**
     * 取得請假資料
     * @param string $datetime 日期時間
     * @param array $leaves 請假資料
     * @param int $staffId 員工編號
     * @param string|null $to 結束日期時間
     * @return array|bool 請假資料
     */
    public function getLeaveData($datetime, $leaves, $staffId, $to = null)
    {
        $timestamp = strtotime($datetime);
        $to        = empty($to) ? null : strtotime($to);

        if (! empty($leaves[$staffId])) {
            foreach ($leaves[$staffId] as $v) {
                if (empty($to)) {
                    if ($timestamp >= $v['sLeaveFromTmestamp'] && $timestamp <= $v['sLeaveToTimestamp']) {
                        return $v;
                    }
                }

                if (! empty($to)) {
                    if ($v['sLeaveFromTmestamp'] >= $timestamp && $v['sLeaveToTimestamp'] <= $to) {
                        return $v;
                    }
                }
            }
        }

        return false;
    }

    /**
     * 取得指定日期期間的請假資料
     * @param string $from 開始日期
     * @param string $to 結束日期
     * @param array $leaves 請假資料
     * @param int $staffId 員工編號
     * @return array 請假資料
     */
    public function getLeaveDuringData($from, $to, $leaves, $staffId)
    {
        $from = strtotime($from);
        $to   = strtotime($to);

        $leaveData = [];
        if (! empty($leaves[$staffId])) {
            foreach ($leaves[$staffId] as $v) {
                if ($v['sLeaveFromTmestamp'] >= $from && $v['sLeaveToTimestamp'] <= $to) {
                    $leaveData[] = $v;
                }
            }
        }

        return empty($leaveData) ? [] : $leaveData;
    }

    /**
     * 取得加班資料
     * @param string $from 開始日期(yyyy-mm-dd hh:mm:ss)
     * @param string $to 結束日期(yyyy-mm-dd hh:mm:ss)
     * @return array 加班資料
     */
    public function getOvertimes($from, $to)
    {
        $sql = 'SELECT sId, sApplicant, sOvertimeFromDateTime, sOvertimeToDateTime, sTotalHoursOfOvertime, sApplyReason, sUnitApproval, sUnitApprovalDateTime, sProcessing, sStatus, sCreatedAt FROM tStaffOvertimeApply WHERE sOvertimeFromDateTime >= :from AND sOvertimeToDateTime <= :to AND sStatus = "Y";';
        $rs  = $this->conn->all($sql, ['from' => $from, 'to' => $to]);

        if (empty($rs)) {
            return [];
        }

        $data = [];
        foreach ($rs as $v) {
            $data[$v['sApplicant']][] = $v;
        }

        return $data;
    }

    /**
     * 加班時間檢查
     * @param string $date 日期
     * @param string $type 類型 (IN: 簽到, OUT: 簽退)
     * @param string $checkInTime 簽到時間
     * @param array $overtimes 加班資料
     * @return string 加班時間
     */
    public function overtimeCheck($date, $type, $checkInTime, $overtimes)
    {
        if (empty($overtimes)) {
            return $checkInTime;
        }

        foreach ($overtimes as $v) {
            $dateTime = ($type == 'IN') ? new DateTime($v['sOvertimeFromDateTime']) : new DateTime($v['sOvertimeToDateTime']);
            if ($date == $dateTime->format('Y-m-d')) {
                $checkInTime = $dateTime->format('H:i:s');

                return $checkInTime;
                break;
            }
        }

        return $checkInTime;
    }

    /**
     * 確認是否補打卡過
     * @param array $row 資料
     * @param array $checkInOutOk 補打卡資料
     * @param string $today 今日日期
     * @param bool $isHtml 是否為HTML
     * @return void
     */
    private function appliedCheckInOut(&$row, &$checkInOutOk, &$today, &$isHtml)
    {
        if (! empty($checkInOutOk[$today])) {
            foreach ($checkInOutOk[$today] as $v) {
                if ($v['sApplyType'] == 'IN') {
                    $row['sIn'] = empty($isHtml) ? '*' . $row['sIn'] : '<span style="cursor:help;" title="補打卡">*</span>' . $row['sIn'];
                }

                if ($v['sApplyType'] == 'OUT') {
                    $row['sOut'] = empty($isHtml) ? '*' . $row['sOut'] : '<span style="cursor:help;" title="補打卡">*</span>' . $row['sOut'];
                }
            }
        }
    }

    /**
     * 確認是否補打卡申請中
     * @param array $row 資料
     * @param array $checkInOut 補打卡資料
     * @param string $today 今日日期
     * @param bool $isHtml 是否為HTML
     * @return void
     */
    private function applingCheckInOut(&$row, &$checkInOut, &$today, &$isHtml)
    {
        if (! empty($checkInOut[$today])) {
            foreach ($checkInOut[$today] as $v) {
                if ($v['sApplyType'] == 'IN') {
                    $row['sIn'] = empty($isHtml) ? '補打卡申請中' : '<span style="font-size:10pt;">補打卡申請中</span>';
                }

                if ($v['sApplyType'] == 'OUT') {
                    $row['sOut'] = empty($isHtml) ? '補打卡申請中' : '<span style="font-size:10pt;">補打卡申請中</span>';
                }
            }
        }
    }

    /**
     * 申請補打上班卡
     * @param array $row 資料
     * @param DateTime $endDate 結束日期時間物件
     * @param string $today 今日日期
     * @param int $countIn 上班補打卡次數
     * @param bool $isHtml 是否為HTML
     * @return void
     */
    private function applyCheckIn(&$row, &$endDate, &$today, &$countIn, &$isHtml)
    {
        if (empty($row['sIn']) && empty($row['holiday']) && (empty($row['css']) || ! in_array($row['week'], [6, 7]) || preg_match('/加班/iu', $row['remark']))) {
            if (($endDate->format('Y-m-d') != date('Y-m-d') || date('H:i:s') > '09:00:59')) {
                $row['sIn'] .= empty($isHtml) ? '' : '<a href="Javascript:void(0);" onclick="apply(\'' . $row['sDate'] . '\', \'IN\')">申請補打卡</a>';
                if ($countIn >= 3) {
                    $row['sIn'] .= empty($isHtml) ? '' : '<a href="Javascript:void(0);" onclick="leaveApply(\'' . $today . '\', \'' . $today . '\')">請假</a>';
                }
            }
        }
    }

    /**
     * 申請補打下班卡
     * @param array $row 資料
     * @param DateTime $endDate 結束日期時間物件
     * @param string $today 今日日期
     * @param int $countOut 下班補打卡次數
     * @param bool $isHtml 是否為HTML
     * @return void
     */
    private function applyCheckOut(&$row, &$endDate, &$today, &$countOut, &$isHtml)
    {
        if (empty($row['sOut']) && empty($row['holiday']) && (empty($row['css']) || ! in_array($row['week'], [6, 7]) || preg_match('/加班/iu', $row['remark']))) {
            if (($endDate->format('Y-m-d') != date('Y-m-d')) || (date('H:i:s') >= '20:10:00')) {
                $row['sOut'] .= empty($isHtml) ? '' : '<a href="Javascript:void(0);" onclick="apply(\'' . $row['sDate'] . '\', \'OUT\')">申請補打卡</a>';
                if ($countOut >= 3) {
                    $row['sOut'] .= empty($isHtml) ? '' : '<a href="Javascript:void(0);" onclick="leaveApply(\'' . $today . '\', \'' . $today . '\')">請假</a>';
                }
            }
        }
    }

    /**
     * 今日未打卡、未簽到、未簽退顯示
     * @param array $row 資料
     * @param DateTime $endDate 結束日期時間物件
     * @param string $today 今日日期
     * @return void
     */
    private function noCheckInOut(&$row, &$endDate, &$today)
    {
        if ($endDate->format('Y-m-d') == date('Y-m-d')) {
            $row['remark'] = preg_replace("/未打卡/iu", '', $row['remark']);

            if (date('H:i:s') < '20:10:00') {
                $row['remark'] = preg_replace("/未簽退/iu", '', $row['remark']);
            }

            if (date('H:i:s') <= '09:00:59') {
                $row['remark'] = preg_replace("/未簽到/iu", '', $row['remark']);
            }
        }
    }

    /**
     * 取得最後鎖定日期
     * @return string 鎖定日期
     */
    private function getLastLockDate()
    {
        $sql = 'SELECT sDate FROM tStaffLockDate WHERE 1 ORDER BY sDate DESC LIMIT 1;';
        return $this->conn->one($sql)['sDate'];
    }

}
