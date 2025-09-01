<?php
require_once dirname(__DIR__) . '/openadodb.php';

$sDate = '110-03-01';
$eDate = '110-08-31';
$cat   = 1;

$storeData     = array();
$storeId       = array();
$storeCaseData = array();

$sql = "SELECT
				sId,
				sName AS name,
				sOffice AS store,
				sCategory as category
		FROM
			tScrivener
		WHERE
			sCategory = " . $cat . " AND sId NOT IN(1084,170,224) AND sName NOT LIKE '%業務專用%' AND sMobileNum !='' ORDER BY sId ASC";
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

            if (!(($rs->fields['brand'] == 1 && $rs->fields['branchCategory'] == 2) || ($rs->fields['brand1'] == 1 && $rs->fields['branchCategory1'] == 2) || ($rs->fields['brand2'] == 1 && $rs->fields['branchCategory2'] == 2))) { //直營案件
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

// print_r($storeData);

// die;

// $objPHPExcel = new PHPExcel();
// //Set properties 設置文件屬性
// $objPHPExcel->getProperties()->setCreator("第一建經");
// $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
// $objPHPExcel->getProperties()->setTitle("第一建經");
// $objPHPExcel->getProperties()->setSubject("第一建經 2021活動");
// $objPHPExcel->getProperties()->setDescription("第一建經 2021活動");

// //指定目前工作頁
// $objPHPExcel->setActiveSheetIndex(0);

//寫入表頭資料

$row = 1;
// $objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
$csv_txt = ($cat == 2) ? "直營" : "加盟";
$csv_txt .= "特約地政士贈獎活動\n";
foreach ($storeData as $k => $v) {
    $money = 0;

    if ($v['count'] > 0) {
        // $csv_txt .= "名稱,數量,金額\n";
        $csv_txt .= $v['storeName'] . "," . $v['count'] . "," . $money . "\n";
    }

    $row++;
}

$fw = fopen('/var/www/html/first.twhg.com.tw/actives/excel/TEST.log', 'A+');
fwrite($fw, $csv_txt);
fclose($fw);

die;
echo "\xEF\xBB\xBF";
echo $csv_txt;

exit;
// die;
// $_file = '2021ScrivenerAct.xlsx' ;

// header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
// header("Cache-Control: no-store, no-cache, must-revalidate");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
// header('Content-type:application/force-download');
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename='.$_file);

// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// $objWriter->save("php://output");
exit;

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

$smarty->assign('menuCategory', array(1 => '加盟', 2 => '直營'));
$smarty->assign('cat', $cat);
$smarty->assign('sDate', $sDate);
$smarty->assign('eDate', $eDate);
$smarty->display('act_202103.inc.tpl', '', 'actives');
