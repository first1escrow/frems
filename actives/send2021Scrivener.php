<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/sms/sms_function_manually.php';

$sDate         = '110-03-01';
$eDate         = '110-12-31';
$cat           = 1;
$sms           = new SMS_Gateway();
$storeData     = array();
$storeId       = array();
$storeCaseData = array();

$sql = "SELECT
				sId,
				sName AS name,
				sOffice AS store,
				sCategory as category,
				sMobileNum AS mobile
		FROM
			tScrivener
		WHERE
			sCategory = " . $cat . " AND sId NOT IN(1084,170,224,1182,1300,2297) AND sName NOT LIKE '%業務專用%' ORDER BY sId ASC";
// echo $sql;
// die;
$rs = $conn->Execute($sql);

// $i = 0;
while (!$rs->EOF) {
    $storeData[$rs->fields['sId']]              = $rs->fields;
    $storeData[$rs->fields['sId']]['storeName'] = $storeData[$rs->fields['sId']]['name'] . "(" . $storeData[$rs->fields['sId']]['store'] . ")";
    $storeData[$rs->fields['sId']]['storeCode'] = 'SC' . str_pad($rs->fields['sId'], 4, '0', STR_PAD_LEFT);
    $storeData[$rs->fields['sId']]['count']     = 0;

    array_push($storeId, $rs->fields['sId']);
    $rs->MoveNext();
}

##
//
$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342" AND cas.cCaseStatus<>"8"'; //005030342 電子合約書測試用沒有刪的樣子

if ($storeId) {
    $query .= " AND csc.cScrivener IN(" . @implode(',', $storeId) . ")";
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
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum) AS branchName,
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum1) AS branchName1,
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum2) AS branchName2,
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum3) AS branchName3,
			(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum) as branchCategory,
			(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum1) as branchCategory1,
			(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum2) as branchCategory2,
			(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum) as branchCategory3
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

    if (in_array($rs->fields['cScrivener'], $storeId)) {

        if ($storeData[$rs->fields['cScrivener']]['category'] == 1) { //加盟
            $storeData[$rs->fields['cScrivener']]['count']++;
            if (empty($storeData[$rs->fields['cScrivener']]['caseData'])) {
                $storeData[$rs->fields['cScrivener']]['caseData'] = array();
            }
            $storeData[$rs->fields['cScrivener']]['caseData'][] = $rs->fields;
        } elseif ($storeData[$rs->fields['cScrivener']]['category'] == 2) { //直營

            if (!(($rs->fields['brand'] == 1 && $rs->fields['branchCategory'] == 2) || ($rs->fields['brand1'] == 1 && $rs->fields['branchCategory1'] == 2) || ($rs->fields['brand2'] == 1 && $rs->fields['branchCategory2'] == 2))) { //非直營案件
                $storeData[$rs->fields['cScrivener']]['count']++;
                if (empty($storeData[$rs->fields['cScrivener']]['caseData'])) {
                    $storeData[$rs->fields['cScrivener']]['caseData'] = array();
                }
                $storeData[$rs->fields['cScrivener']]['caseData'][] = $rs->fields;
            }

        }

    }

    $rs->MoveNext();
}
##
$fwError       = fopen('excel/send2021ScrivenerError' . date('Ymd') . '.txt', 'a+');
$fw            = fopen('excel/send2021Scrivener' . date('Ymd') . '.txt', 'a+');
$row           = 1;
$checkOK       = array(70, 186, 408, 534, 564, 874, 1204, 1461, 1522, 1708, 2154, 489, 1127, 187, 144); //
$longTxtCount  = 0;
$shortTxtCount = 0;
foreach ($storeData as $k => $v) {
    $moneyTxt = '';

    if ($v['count'] >= 20 && $v['count'] < 35) {
        $moneyTxt = '贈獎禮券已達3000元等級,';
    } elseif ($v['count'] >= 35 && $v['count'] < 50) {
        $moneyTxt = '贈獎禮券已達6000元等級,';
    } elseif ($v['count'] >= 50 && $v['count'] < 70) {
        $moneyTxt = '贈獎禮券已達10000元等級,';
    } elseif ($v['count'] >= 70 && $v['count'] < 100) {
        $moneyTxt = '贈獎禮券已達15000元等級,';
    } elseif ($v['count'] >= 100 && $v['count'] < 150) {
        $moneyTxt = '贈獎禮券已達25000元等級,';
    } elseif ($v['count'] >= 150 && $v['count'] < 250) {
        $moneyTxt = '贈獎禮券已達50000元等級,';
    } elseif ($v['count'] >= 250) {
        $moneyTxt = '贈獎禮券已達100000元等級,';
    }

    if ($v['count'] > 0) {

        if ($moneyTxt != '') {
            $longTxtCount++;
        } else {
            $shortTxtCount++;
        }

        $FirstNameCheck = mb_strcut($v['name'], 0, 3, "UTF-8");

        $sql       = "SELECT sName FROM tScrivenerSms WHERE sScrivener = '" . $k . "' AND sMobile = '" . str_replace('-', '', $v['mobile']) . "' AND sCheck_id ='' AND sLock = 0";
        $rs        = $conn->Execute($sql);
        $FirstName = mb_strcut($rs->fields['sName'], 0, 3, "UTF-8"); //

        if ($k == 1754 || $k == 430 || $k == 166) {
            $FirstName = '温';
        }

        if ($k == 1991) {
            $FirstName = '陳';
        }

        if ($k == 148) {
            $FirstName = '陳';
        }

        if ($k == 213) {
            $FirstName = '林';
        }

        //親愛的李代書您好:第一建經通知目前代書贈獎活動您已累計送件170件,贈獎禮券已達50000元等級,活動至本月底截止，敬請集中火力送件至第一建經，衝高累績贈獎喔！
        //
        //親愛的王代書您好:第一建經通知目前代書贈獎活動您已累計送件4件,活動至本月底截止，敬請集中火力送件至第一建經，衝高累績贈獎喔！

        $message = '親愛的' . $FirstName . '代書您好:第一建經通知目前代書贈獎活動您已累計送件' . $v['count'] . '件,' . $moneyTxt . '活動至本月底截止，敬請集中火力送件至第一建經，衝高累績贈獎喔！';

        // echo "##".$v['storeCode'].$v['name']."##\r\n";
        // echo $message."\r\n";

        if ($FirstName == '' || (($FirstName != $FirstNameCheck) && !preg_match("/" . $FirstName . "/", $v['name']) && !in_array($v['sId'], $checkOK))) {

            fwrite($fwError, "##" . $v['storeCode'] . $v['name'] . $v['mobile'] . "##\r\n");
            fwrite($fwError, $message . "\r\n");

        } else {

            //SC0298李明遠 、SC1600王慈靜  SC1461正業長庚特區所   SC1662王乃佳 SC0223張瑄倫
            if ($v['sId'] == '223' || $v['sId'] == '1662') {
                //
                // $sms->manual_send('0937185661',$message,'y','');

                // die;
                // $sms->manual_send('0919200247',$message,'y','');
                //
            }

            $sms->manual_send($v['mobile'], $message, 'y', ''); //要y

        }

        fwrite($fw, "##" . $v['storeCode'] . $v['name'] . $v['mobile'] . "##\r\n");
        fwrite($fw, $message . "\r\n");

    }
    $row++;
}

echo "長簡訊:" . $longTxtCount . "、短簡訊:" . $shortTxtCount . "\r\n";
fwrite($fw, "長簡訊:" . $longTxtCount . "、短簡訊:" . $shortTxtCount . "\r\n");

fclose($fw);

fclose($fwError);

die;

$conn->close();

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
