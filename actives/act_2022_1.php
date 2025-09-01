<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

//取得身分詳細資料
function getStore($identity, $store)
{
    global $conn;

    if (($identity != 'R') && ($identity != 'S')) {
        return '';
    }

    if ($identity == 'R') {
        $sql = 'SELECT
                    a.bId,
                    a.bStore,
                    a.bName,
                    b.bCode,
                    b.bName as bBrand
                FROM
                    tBranch AS a
                JOIN
                    tBrand AS b ON a.bBrand = b.bId
                WHERE
                    a.bId = ' . $store . ';';
    } else {
        $sql = 'SELECT
                    a.sId,
                    a.sName,
                    a.sOffice
                FROM
                    tScrivener AS a
                WHERE
                    a.sId = ' . $store . ';';
    }

    return $conn->one($sql);
}
##

//取得案件
function getCertifiedCase($identity, $store, $start, $end)
{
    global $conn;

    if (($identity != 'R') && ($identity != 'S')) {
        return ['count' => 0, 'detail' => []];
    }

    $sql = 'SELECT
                a.cCertifiedId,
                a.cEscrowBankAccount,
                (SELECT cName FROM tContractBuyer WHERE cCertifiedId = a.cCertifiedId) as buyer,
                (SELECT cName FROM tContractOwner WHERE cCertifiedId = a.cCertifiedId) as owner,
                a.cApplyDate,
                a.cSignDate,
                a.cFinishDate,
                (SELECT sName FROM tStatusCase WHERE sId = a.cCaseStatus) as caseStatus,
                b.cBranchNum as branch,
                b.cBranchNum1 as branch1,
                b.cBranchNum2 as branch2,
                b.cBranchNum3 as branch3,
    			(SELECT bName FROM tBrand WHERE bId = b.cBrand) AS brandname,
	    		(SELECT bName FROM tBrand WHERE bId = b.cBrand1) AS brandname1,
		    	(SELECT bName FROM tBrand WHERE bId = b.cBrand2) AS brandname2,
			    (SELECT bName FROM tBrand WHERE bId = b.cBrand3) AS brandname3,
			    (SELECT bCode FROM tBrand WHERE bId = b.cBrand) AS brandCode,
			    (SELECT bCode FROM tBrand WHERE bId = b.cBrand1) AS brandCode1,
			    (SELECT bCode FROM tBrand WHERE bId = b.cBrand2) AS brandCode2,
			    (SELECT bCode FROM tBrand WHERE bId = b.cBrand3) AS brandCode3,
			    b.cBrand as brand,
			    b.cBrand1 as brand1,
			    b.cBrand2 as brand2,
			    b.cBrand2 as brand3,
                (SELECT bName FROM tBranch WHERE bId = b.cBranchNum) AS branchName,
                (SELECT bName FROM tBranch WHERE bId = b.cBranchNum1) AS branchName1,
                (SELECT bName FROM tBranch WHERE bId = b.cBranchNum2) AS branchName2,
                (SELECT bName FROM tBranch WHERE bId = b.cBranchNum3) AS branchName3,
                c.cTotalMoney,
                c.cCertifiedMoney,
                d.cScrivener as sId,
                (SELECT sName FROM tScrivenerSms WHERE sId = d.cManage) as scrivener,
                e.cZip,
                e.cAddr,
                f.zCity,
                f.zArea
            FROM
                tContractCase AS a
            JOIN
                tContractRealestate AS b ON a.cCertifiedId = b.cCertifyId
            JOIN
                tContractIncome AS c ON a.cCertifiedId = c.cCertifiedId
            JOIN
                tContractScrivener AS d ON a.cCertifiedId = d.cCertifiedId
            LEFT JOIN
                tContractProperty AS e ON a.cCertifiedId = e.cCertifiedId
            LEFT JOIN
                tZipArea AS f ON e.cZip = f.zZip
            WHERE
                a.cSignDate >= "' . $start . ' 00:00:00" AND a.cSignDate <= "' . $end . ' 23:59:59"
                AND a.cCaseStatus <> 4 AND a.cCaseStatus <> 8 ';

    if ($identity == 'R') {
        $sql .= ' AND (
                        b.cBranchNum = ' . $store . '
                        OR b.cBranchNum1 = ' . $store . '
                        OR b.cBranchNum2 = ' . $store . '
                        OR b.cBranchNum3 = ' . $store . '
                    );';
    } else {
        $sql .= ' AND d.cScrivener = ' . $store . ';';
    }

    $rs = $conn->all($sql);
    if (empty($rs)) {
        return ['count' => 0, 'detail' => []];
    }

    $_dealMoney = 5000000;

    $_cases      = []; //所有有效案件
    $_totalCount = 0; //件數
    foreach ($rs as $k => $v) {
        $v['address'] = $v['cZip'] . ' ' . $v['zCity'] . $v['zArea'] . $v['cAddr'];

        $v['realtyCount'] = 0;
        $v['realtyCount'] += ($v['branch'] > 0) ? 1 : 0;
        $v['realtyCount'] += ($v['branch1'] > 0) ? 1 : 0;
        $v['realtyCount'] += ($v['branch2'] > 0) ? 1 : 0;
        $v['realtyCount'] += ($v['branch3'] > 0) ? 1 : 0;
        $v['realtyCount'] = round((1 / $v['realtyCount']), 1); //一組案件除以配件店數

        $_money = preg_match("/預售/iu", $v['cAddr']) ? getIncoming($v['cEscrowBankAccount']) : $v['cTotalMoney'];
        if (($_money >= $_dealMoney) && ($v['cCertifiedMoney'] >= ($_money / 10000 * 6))) {
            $v['scrivenerSales'] = getScrivenerSales($v['sId']);
            $v['realtySales']    = getRealtySales([$v['branch'], $v['branch1'], $v['branch2'], $v['branch3']]);

            $_totalCount += $v['realtyCount'];
            $_cases[] = $v;
        }
    }

    return ['count' => $_totalCount, 'detail' => $_cases];
}
##

//預售屋取得入款金額
function getIncoming($cId)
{
    global $conn;

    $sql = 'SELECT eDebit, eLender FROM tExpense WHERE eDepAccount = "00' . $cId . '"';
    $rs  = $conn->all($sql);

    if (empty($rs)) {
        return 0;
    }

    $_money = 0;
    foreach ($rs as $v) {
        $_money += (int) $v['eLender'] - (int) $v['eDebit'];
    }

    return $_money;
}
##

//取得地政士服務業務
function getScrivenerSales($sId)
{
    global $conn;

    $sql = 'SELECT (SELECT pName FROM tPeopleInfo WHERE pId = a.sSales) AS sales FROM tScrivenerSales AS a WHERE sScrivener = ' . $sId . ';';
    $rs  = $conn->all($sql);

    return empty($rs) ? '' : implode('_', array_column($rs, 'sales'));

}
##

//取得仲介服務業務
function getRealtySales($bIds)
{
    global $conn;

    if (empty($bIds)) {
        return '';
    }

    $sales = '';

    foreach ($bIds as $bId) {
        if ($bId > 0) {
            $sales .= empty($sales) ? getRSales($bId) : '_' . getRSales($bId);
        }
    }

    return $sales;
}

function getRSales($bId)
{
    global $conn;

    $sql = 'SELECT (SELECT pName FROM tPeopleInfo WHERE pId = a.bSales) AS sales FROM tBranchSales AS a WHERE bBranch = ' . $bId . ';';
    $rs  = $conn->all($sql);

    return empty($rs) ? '' : implode('_', array_column($rs, 'sales'));
}
##

//確認禮券面額
function ticketAmount($count)
{
    if ($count >= 12) {
        return 1000;
    }

    if ($count >= 7) {
        return 800;
    }

    if ($count >= 3) {
        return 600;
    }

    return 0;
}
##

if ($_POST['act'] == 'excel') {
    $conn = new first1DB;

    //活動代號
    $activity_id = 1;
    ##

    //有參加的店家
    $sql = 'SELECT
            a.aIdentity,
            a.aStoreId,
            b.aYear,
            b.aTarget,
            b.aStartDate,
            b.aEndDate,
            c.aTitle,
            d.aName
        FROM
            tActivityRecords AS a
        JOIN
            tActivities AS b ON a.aActivityId = b.aId
        JOIN
            tActivityRules AS c ON a.aActivityId = c.aId
        JOIN
            tActivityGifts AS d ON a.aGift = d.aId
        WHERE
            a.aActivityId = ' . $activity_id . ';';
    $stores = $conn->all($sql);

    if (empty($stores)) {
        exit('無參與店家資料');
    }
    ##

    //by 店家找出時間內案件
    foreach ($stores as $k => $v) {
        $v['storeName'] = getStore($v['aIdentity'], $v['aStoreId']);
        $v['case']      = getCertifiedCase($v['aIdentity'], $v['aStoreId'], $v['aStartDate'], $v['aEndDate']);

        $stores[$k] = $v;

    }
    ##

    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

    $objPHPExcel = new PHPExcel();
    //Set properties 設置文件屬性
    $objPHPExcel->getProperties()->setCreator("第一建經");
    $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
    $objPHPExcel->getProperties()->setTitle("第一建經");
    $objPHPExcel->getProperties()->setSubject("第一建經 2022活動");
    $objPHPExcel->getProperties()->setDescription("第一建經 2022活動");

    //指定目前工作頁
    $objPHPExcel->setActiveSheetIndex(0);

    //
    $row = 1;

    foreach ($stores as $k => $v) {
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row . '');
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '店名');
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '辦法');
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, '禮券類型');
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, '件數');
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, '禮券金額');
        $row++;

        $_storeName = ($v['aIdentity'] == 'R') ? $v['storeName']['bBrand'] . $v['storeName']['bStore'] . '(' . $v['storeName']['bName'] . ')' : $v['storeName']['sName'] . '(' . $v['storeName']['sOffice'] . ')';
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row . '');
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $_storeName);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $v['aTitle']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $v['aName']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $v['case']['count']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, ticketAmount($v['case']['count']));
        $row++;
        $row++;

        $_storeName = null;unset($_storeName);

        $col = 65;
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '序號');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '保證號碼');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介店編號');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介店名');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '賣方');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '買方');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '總價金');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '合約保證費');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '進案日期');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '簽約日期');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '實際點交日期');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '地政士姓名');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '標的物座落');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '狀態');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介業務');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '地政士業務');
        $row++;

        //
        if (is_array($v['case']['detail'])) {
            foreach ($v['case']['detail'] as $key => $value) {
                $col        = 65;
                $branchCode = array();
                $branchName = array();
                $applyDate  = (substr($value['cApplyDate'], 0, 10) != '0000-00-00') ? (substr($value['cApplyDate'], 0, 4) - 1911) . substr($value['cApplyDate'], 4, 6) : '000-00-00';
                $signDate   = (substr($value['cSignDate'], 0, 10) != '0000-00-00') ? (substr($value['cSignDate'], 0, 4) - 1911) . substr($value['cSignDate'], 4, 6) : '000-00-00';
                $endDate    = (substr($value['cFinishDate'], 0, 10) != '0000-00-00') ? (substr($value['cFinishDate'], 0, 4) - 1911) . substr($value['cFinishDate'], 4, 6) : '000-00-00';

                if ($value['branch'] > 0) {
                    array_push($branchCode, $value['brandCode'] . str_pad($value['branch'], 5, 0, STR_PAD_LEFT));
                    array_push($branchName, $value['branchName']);
                }

                if ($value['branch1'] > 0) {
                    array_push($branchCode, $value['brandCode1'] . str_pad($value['branch1'], 5, 0, STR_PAD_LEFT));
                    array_push($branchName, $value['branchName1']);
                }

                if ($value['branch2'] > 0) {
                    array_push($branchCode, $value['brandCode2'] . str_pad($value['branch2'], 5, 0, STR_PAD_LEFT));
                    array_push($branchName, $value['branchName2']);
                }

                if ($value['branch3'] > 0) {
                    array_push($branchCode, $value['brandCode3'] . str_pad($value['branch3'], 5, 0, STR_PAD_LEFT));
                    array_push($branchName, $value['branchName3']);
                }

                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, ($key + 1));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $value['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, @implode('_', $branchCode));
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, @implode('_', $branchName));
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['owner']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['buyer']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['cTotalMoney']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['cCertifiedMoney']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $applyDate);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $signDate);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $endDate);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['scrivener']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['address']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['caseStatus']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['realtySales']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['scrivenerSales']);

                unset($applyDate, $signDate, $endDate);

                $row++;
            }
        }

        $row++;
        $row++;
    }

    // $_file = '2022Act.xlsx';
    $_file = 'Act_' . $stores[0]['aYear'] . '_' . $activity_id . '_' . uniqid() . '.xlsx';

    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Content-type:application/force-download');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=' . $_file);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save("php://output");
    exit;
}
##

$smarty->assign('sDate', $sDate);
$smarty->assign('eDate', $eDate);
$smarty->display('act_2022_1.inc.tpl', '', 'actives');
