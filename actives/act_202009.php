<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

$_POST = escapeStr($_POST);

if ($_POST['sDate']) {
    $sDate         = $_POST['sDate'];
    $eDate         = $_POST['eDate'];
    $storeData     = array();
    $storeId       = array();
    $storeCaseData = array();
    //查詢店家 0:未參加;1:辦法一;2:辦法二 0:未設定;1:7-11禮券;2全聯禮券
    $sql = "SELECT
				bId,
				bName AS name,
				bStore AS store,
				(SELECT bName FROM tBrand WHERE bId = bBrand) AS brand,
				(SELECT bCode FROM tBrand WHERE bId = bBrand) AS code,
				bAct_2020,
				bAct_2020_gift
			FROM
				tBranch WHERE bAct_2020 > 0";
    $rs = $conn->Execute($sql);
    // $i = 0;
    while (!$rs->EOF) {
        $storeData[$rs->fields['bId']]              = $rs->fields;
        $storeData[$rs->fields['bId']]['storeName'] = $storeData[$rs->fields['bId']]['brand'] . $storeData[$rs->fields['bId']]['store'] . "(" . $storeData[$rs->fields['bId']]['name'] . ")";
        $storeData[$rs->fields['bId']]['storeCode'] = $storeData[$rs->fields['bId']]['code'] . str_pad($storeData[$rs->fields['bId']]['bId'], 5, '0', STR_PAD_LEFT);
        $storeData[$rs->fields['bId']]['count']     = 0;

        if ($storeData[$rs->fields['bId']]['bAct_2020'] == 1) {
            $storeData[$rs->fields['bId']]['Act_2020'] = '辦法一';
        } elseif ($storeData[$rs->fields['bId']]['bAct_2020'] == 2) {
            $storeData[$rs->fields['bId']]['Act_2020'] = '辦法二';
        } elseif ($storeData[$rs->fields['bId']]['bAct_2020'] == 3) {
            $storeData[$rs->fields['bId']]['Act_2020'] = '辦法三';
        } elseif ($storeData[$rs->fields['bId']]['bAct_2020'] == 4) {
            $storeData[$rs->fields['bId']]['Act_2020'] = '辦法四';
        }

        if ($storeData[$rs->fields['bId']]['bAct_2020_gift'] == 1) {
            $storeData[$rs->fields['bId']]['Act_2020_gift'] = '7-11禮券';
        } elseif ($storeData[$rs->fields['bId']]['bAct_2020_gift'] == 2) {
            $storeData[$rs->fields['bId']]['Act_2020_gift'] = '全聯禮券';
        } else {
            $storeData[$rs->fields['bId']]['Act_2020_gift'] = '未設定';
        }

        array_push($storeId, $rs->fields['bId']);

        // $i++;
        $rs->MoveNext();
    }

    ##
    //總價400萬以上 履保費有收足萬分之六，解約不計入統計，跨店0.5件計算
    $query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342" AND cas.cCaseStatus IN(2,3) AND cTotalMoney >= 4000000'; //005030342 電子合約書測試用沒有刪的樣子

    if ($storeId) {
        if ($query) {$query .= " AND ";}

        $query .= ' (rea.cBranchNum IN (' . @implode(",", $storeId) . ') OR rea.cBranchNum1 IN (' . @implode(",", $storeId) . ') OR rea.cBranchNum2 IN (' . @implode(",", $storeId) . ') OR rea.cBranchNum3 IN (' . @implode(",", $storeId) . ')) ';
        // unset($storeId);
    }

    // 搜尋條件-簽約日期
    if ($sDate) {
        $tmp       = explode('-', $sDate);
        $sSignDate = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2];
        unset($tmp);

        if ($query) {$query .= " AND ";}
        $query .= ' cas.cSignDate>="' . $sSignDate . ' 00:00:00" ';
    }
    if ($eDate) {
        $tmp       = explode('-', $eDate);
        $eSignDate = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2];
        unset($tmp);

        if ($query) {$query .= " AND ";}
        $query .= ' cas.cSignDate<="' . $eSignDate . ' 23:59:59" ';
    }

    $sql = '
		SELECT
			cas.cCertifiedId as cCertifiedId,
			cas.cApplyDate as cApplyDate,
			cas.cSignDate as cSignDate,
			cas.cFinishDate as cFinishDate,
			cas.cEndDate as cEndDate,
			buy.cName as buyer,
			own.cName as owner,
			inc.cTotalMoney as cTotalMoney,
			inc.cCertifiedMoney as cCertifiedMoney,
			inc.cFirstMoney as cFirstMoney,
			csc.cScrivener as cScrivener,
			(SELECT b.sName FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivener,
			(SELECT b.sCategory FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivenerCategory,
			pro.cAddr as cAddr,
			pro.cZip as cZip,
			zip.zCity as zCity,
			zip.zArea as zArea,
			(SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brandname,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brandname1,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brandname2,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand3) AS brandname3,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand) AS brandCode,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand1) AS brandCode1,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand2) AS brandCode2,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand3) AS brandCode3,
			rea.cBrand as brand,
			rea.cBrand1 as brand1,
			rea.cBrand2 as brand2,
			rea.cBrand2 as brand3,
			rea.cBranchNum as branch,
			rea.cBranchNum1 as branch1,
			rea.cBranchNum2 as branch2,
			rea.cBranchNum3 as branch3,
			(SELECT bName FROM tBranch WHERE bId = rea.cBranchNum) AS branchName,
			(SELECT bName FROM tBranch WHERE bId = rea.cBranchNum1) AS branchName1,
			(SELECT bName FROM tBranch WHERE bId = rea.cBranchNum2) AS branchName2,
			(SELECT bName FROM tBranch WHERE bId = rea.cBranchNum3) AS branchName3


		FROM
			tContractCase AS cas
		LEFT JOIN
			tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId
		LEFT JOIN
			tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
		LEFT JOIN
			tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
		LEFT JOIN
			tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId
		LEFT JOIN
			tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId
		LEFT JOIN
			tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
		LEFT JOIN
			tZipArea AS zip ON zip.zZip=pro.cZip
		LEFT JOIN
			tScrivener AS scr ON scr.sId = csc.cScrivener
		WHERE
		' . $query . '
		GROUP BY
			cas.cCertifiedId
		ORDER BY
			cas.cApplyDate,cas.cId,cas.cSignDate ASC;
		';

    // echo $sql;

    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {

        //履保費收足萬分之六
        $realCertifyFee = round(($rs->fields['cTotalMoney'] - $rs->fields['cFirstMoney']) * 0.0006);
        //(cer_real + 10) < cer_title
        if (($rs->fields['cCertifiedMoney'] + 10) >= $realCertifyFee) {
            $count       = 0;
            $branchCount = 0;

            //先取得仲介數
            if ($rs->fields['branch'] > 0) {
                $branchCount++;
            }

            if ($rs->fields['branch1'] > 0) {
                $branchCount++;
            }

            if ($rs->fields['branch2'] > 0) {
                $branchCount++;
            }

            if ($rs->fields['branch3'] > 0) {
                $branchCount++;
            }

            // echo $rs->fields['cCertifiedId']."<bR>";
            // echo "branchCount".$branchCount."<br>";
            // echo "count".round(1/$branchCount,2)."<br>";
            //判斷該店是否有參加活動
            if (in_array($rs->fields['branch'], $storeId)) {
                $storeData[$rs->fields['branch']]['count'] += round(1 / $branchCount, 2);

                if (!is_array($storeData[$rs->fields['branch']]['caseData'])) {
                    $storeData[$rs->fields['branch']]['caseData'] = array();
                }
                $storeData[$rs->fields['branch']]['caseData'][] = $rs->fields;
            }

            if (in_array($rs->fields['branch1'], $storeId)) {
                $storeData[$rs->fields['branch1']]['count'] += round(1 / $branchCount, 2);

                if (!is_array($storeData[$rs->fields['branch1']]['caseData'])) {
                    $storeData[$rs->fields['branch1']]['caseData'] = array();
                }
                $storeData[$rs->fields['branch1']]['caseData'][] = $rs->fields;
            }

            if (in_array($rs->fields['branch2'], $storeId)) {
                $storeData[$rs->fields['branch2']]['count'] += round(1 / $branchCount, 2);

                if (!is_array($storeData[$rs->fields['branch2']]['caseData'])) {
                    $storeData[$rs->fields['branch2']]['caseData'] = array();
                }
                $storeData[$rs->fields['branch2']]['caseData'][] = $rs->fields;
            }

            if (in_array($rs->fields['branch3'], $storeId)) {
                $storeData[$rs->fields['branch3']]['count'] += round(1 / $branchCount, 2);

                if (!is_array($storeData[$rs->fields['branch3']]['caseData'])) {
                    $storeData[$rs->fields['branch3']]['caseData'] = array();
                }
                $storeData[$rs->fields['branch2']]['caseData'][] = $rs->fields;
            }
        }

        // $caseData[] = $rs->fields;

        $rs->MoveNext();
    }
    ##

    // echo "<pre>";
    // print_r($storeData);

    // die;

    $objPHPExcel = new PHPExcel();
    //Set properties 設置文件屬性
    $objPHPExcel->getProperties()->setCreator("第一建經");
    $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
    $objPHPExcel->getProperties()->setTitle("第一建經");
    $objPHPExcel->getProperties()->setSubject("第一建經 2020活動");
    $objPHPExcel->getProperties()->setDescription("第一建經 2020活動");

    //指定目前工作頁
    $objPHPExcel->setActiveSheetIndex(0);

    //寫入表頭資料

    $row = 1;
    // $objPHPExcel->getActiveSheet()->mergeCells("A1:D1");

    foreach ($storeData as $k => $v) {
        $money = 0;

        // 辦法一：
        // 單店每月送計2件(含)，每件400元禮券
        // 單店每月送計5件(含)，每件600元禮券
        // 辦法二：
        // 單店每月送計2件(含)，每件600元禮券
        // 單店每月送計5件(含)，每件700元禮券
        if ($v['bAct_2020'] == 1) { //辦法一
            if ($v['count'] >= 2 && $v['count'] < 5) {
                $money = 400;
            } elseif ($v['count'] >= 5) {
                $money = 600;
            }
        } else if ($v['bAct_2020'] == 2) {
            if ($v['count'] >= 2 && $v['count'] < 5) {
                $money = 600;
            } elseif ($v['count'] >= 5) {
                $money = 700;
            }
        } elseif ($v['bAct_2020'] == 3) {
            if ($v['count'] >= 2 && $v['count'] < 5) {
                $money = 600;
            } elseif ($v['count'] >= 5) {
                $money = 800;
            }
        } elseif ($v['bAct_2020'] == 4) {
            if ($v['count'] >= 10) {
                $money = 700;
            }
        }

        if ($money == 0) {
            continue;
        }

        // $col = 65;
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row . '');
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '店名');
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '辦法');
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, '禮券類型');
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, '件數');
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, '金額');
        $row++;

        $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row . '');
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $v['storeName']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $v['Act_2020']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $v['Act_2020_gift']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $v['count']);

        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, ($money * $v['count']));
        $row++;
        $row++;

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

        //     echo "<pre>";
        // print_r($v['caseData']);

        // die;

        //
        if (is_array($v['caseData'])) {
            foreach ($v['caseData'] as $key => $value) {
                $col            = 65;
                $branchCode     = array();
                $branchName     = array();
                $branchSales    = array();
                $scrivenerSales = array();
                $applyDate      = (substr($value['cApplyDate'], 0, 10) != '0000-00-00') ? (substr($value['cApplyDate'], 0, 4) - 1911) . substr($value['cApplyDate'], 4, 6) : '000-00-00';
                $signDate       = (substr($value['cSignDate'], 0, 10) != '0000-00-00') ? (substr($value['cSignDate'], 0, 4) - 1911) . substr($value['cSignDate'], 4, 6) : '000-00-00';

                $endDate = (substr($value['cEndDate'], 0, 10) != '0000-00-00') ? (substr($value['cEndDate'], 0, 4) - 1911) . substr($value['cEndDate'], 4, 6) : '000-00-00';

                if ($value['branch'] > 0) {
                    array_push($branchCode, $value['brandCode'] . str_pad($value['branch'], 5, 0, STR_PAD_LEFT));
                    array_push($branchName, $value['branchName']);
                    $branchSales = array_merge($branchSales, getBranchSales($value['branch']));
                }

                if ($value['branch1'] > 0) {
                    array_push($branchCode, $value['brandCode1'] . str_pad($value['branch1'], 5, 0, STR_PAD_LEFT));
                    array_push($branchName, $value['branchName1']);
                    $branchSales = array_merge($branchSales, getBranchSales($value['branch1']));
                }

                if ($value['branch2'] > 0) {
                    array_push($branchCode, $value['brandCode2'] . str_pad($value['branch2'], 5, 0, STR_PAD_LEFT));
                    array_push($branchName, $value['branchName2']);
                    $branchSales = array_merge($branchSales, getBranchSales($value['branch2']));
                }

                if ($value['branch3'] > 0) {
                    array_push($branchCode, $value['brandCode3'] . str_pad($value['branch3'], 5, 0, STR_PAD_LEFT));
                    array_push($branchName, $value['branchName3']);
                    $branchSales = array_merge($branchSales, getBranchSales($value['branch3']));
                }

                $scrivenerSales = getScrivenerSales($value['cScrivener']);

                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, ($key + 1));
                // $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['cCertifiedId']);
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
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['cZip'] . $value['zCity'] . $value['zArea'] . $value['zAddr']);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['status']);

                // $sql = "SELECT FROM tBranchSales WHERE bBranch = ''"
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, @implode('_', $branchSales));
                $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, @implode('_', $scrivenerSales));

                $row++;
            }
        }

        $row++;
    }

    // die;
    $_file = '2020BranchAct.xlsx';

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
function getBranchSales($id)
{
    global $conn;

    $sales = array();
    // $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
    $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS Name FROM tBranchSales WHERE bBranch = '" . $id . "'";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $sales[] = $rs->fields['Name'];

        $rs->MoveNext();
    }

    return $sales;
}

function getScrivenerSales($id)
{
    global $conn;
    $sales = array();

    // $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
    $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS Name FROM tScrivenerSales WHERE sScrivener = '" . $id . "'";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $sales[] = $rs->fields['Name'];

        $rs->MoveNext();
    }

    return $sales;
}
##
$smarty->assign('sDate', $sDate);
$smarty->assign('eDate', $eDate);
$smarty->display('act_202009.inc.tpl', '', 'actives');
