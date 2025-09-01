<?php

require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$sql = "SELECT
			la.lId,
			la.lNickName,
			la.lCaseMobile,
			(SELECT bName FROM tBrand WHERE bId = b.bBrand) AS brand,
			b.bName,
			b.bStore,
			(SELECT zCity FROM tZipArea WHERE zZip = b.bZip) AS city,
			(SELECT zArea FROM tZipArea WHERE zZip = b.bZip) AS area,
			b.bAddress,
			la.lTargetCode
		FROM
			`tLineAccount` AS la
			LEFT JOIN tBranch AS b ON b.bId = SUBSTR(lTargetCode,3)
		WHERE
			la.`lStage2Auth` LIKE 'Y'
			AND la.lIdentity = 'R'
			AND la.lLineId  != 'U86db6edf9dd39e60f2615c1eede11617'
			AND la.lLineId  != 'U4b14569b842b0d5d4613b77b94af02b6'
		ORDER BY lTargetCode ASC";

// $sql = "SELECT * FROM `tLineAccount` WHERE `lStage2Auth` LIKE 'Y' AND `lTargetCode` != 'SC0224' AND `lTargetCode` != 'SC0632'";
$rs = $conn->Execute($sql);
$i  = 0;
while (!$rs->EOF) {
    $list[$i] = $rs->fields;

    $list[$i]['ssName'] = getBB($rs->fields['lCaseMobile']);
    $list[$i]['sales']  = getBranchSales(substr($rs->fields['lTargetCode'], 2));

    // if ($list[$i]['ssName'] == '') { //sMobileNum
    //     if ($list[$i]['sMobileNum']== $list[$i]['lCaseMobile']) {
    //         $list[$i]['ssName'] = $list[$i]['sName'];
    //     }
    // }

    // echo $list[$i]['lId']."_".$list[$i]['lTargetCode']."_".$list[$i]['ssName']."_".$list[$i]['lCaseMobile']."_".$list[$i]['sOffice']."_".$list[$i]['city'].$list[$i]['area'].$list[$i]['sCpAddress']."_".$list[$i]['lNickName']."_".$list[$i]['Sales']."<br>";

    $i++;
    $rs->MoveNext();
}
//排序/代書名字/事務所名稱/地址

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("LINE地政士名單");
$objPHPExcel->getProperties()->setDescription("LINE仲介名單");
##

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('LINE仲介名單');
##

//設定欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

//地政士編號    姓名    LIN暱稱    電話    事務所    地址

//寫入title資料
$objPHPExcel->getActiveSheet()->setCellValue('A1', '仲介編號'); // A 仲介
$objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'LINE暱稱');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '電話');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '品牌');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '店名');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '公司名稱');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '地址');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '業務');

//寫入各店家資料
$index = 2;

foreach ($list as $k => $v) {
    // echo $list[$i]['lId']."_".$list[$i]['lTargetCode']."_".$list[$i]['ssName']."_".$list[$i]['lCaseMobile']."_".$list[$i]['sOffice']."_".$list[$i]['city'].$list[$i]['area'].$list[$i]['sCpAddress']."_".$list[$i]['lNickName']."_".$list[$i]['Sales']."<br>";

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $index, $v['lTargetCode']); // A 仲介
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $index, $v['ssName']);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $index, $v['lNickName'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $index, $v['lCaseMobile'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $index, $v['brand']);
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $index, $v['bName']);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $index, $v['bStore']);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $index, $v['city'] . $v['area'] . $v['bAddress']);
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $index, $v['Sales']);
    $index++;
}

##

$_file = iconv('UTF-8', 'BIG5', 'LINE仲介名單');
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file . '.xlsx');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit;
function getBB($id)
{
    global $conn;
    $sql = "SELECT bName FROM tBranchSms WHERE bMobile = '" . $id . "' AND bCheck_id = 0 AND bDel = 0";

    $rs = $conn->Execute($sql);

    if ($rs->fields['bName']) {
        return $rs->fields['bName'];
    } else {
        return '';
    }
}

function getBranchSales($Id)
{

    global $conn;
    $sales = array();
    $sql   = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bs.bSales) AS Name FROM tBranchSales AS bs WHERE bBranch = '" . substr($Id, 2) . "'";

    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $sales[] = $rs->fields['Name'];

        $rs->MoveNext();
    }
    return implode(',', $sales);
}
