<?php

require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$sql = "SELECT
			la.lId,
			la.lNickName,
			la.lCaseMobile,
			s.sName,
			s.sOffice,
			s.sMobileNum,
			(SELECT zCity FROM tZipArea WHERE zZip = s.sCpZip1) AS city,
			(SELECT zArea FROM tZipArea WHERE zZip = s.sCpZip1) AS area,
			s.sCpAddress,
			lTargetCode,

			(SELECT pName FROM tPeopleInfo WHERE pId =s.sUndertaker1) AS sUndertaker
		FROM
			`tLineAccount` AS la
			LEFT JOIN tScrivener AS s ON s.sId = SUBSTR(lTargetCode,3)
		WHERE
			la.`lStage2Auth` = 'Y'
			AND la.lIdentity = 'S'
			AND la.`lTargetCode` != 'SC0224'
			AND la.`lTargetCode` != 'SC0632'
		ORDER BY lTargetCode ASC";

// $sql = "SELECT * FROM `tLineAccount` WHERE `lStage2Auth` LIKE 'Y' AND `lTargetCode` != 'SC0224' AND `lTargetCode` != 'SC0632'";
$rs = $conn->Execute($sql);
$i  = 0;
while (!$rs->EOF) {
    $list[$i] = $rs->fields;

    $list[$i]['ssName'] = getSS($rs->fields['lCaseMobile']);

    if ($list[$i]['ssName'] == '') { //sMobileNum
        if ($list[$i]['sMobileNum'] == $list[$i]['lCaseMobile']) {
            $list[$i]['ssName'] = $list[$i]['sName'];
        }
    }

    $list[$i]['sales'] = getScrivenerSales(substr($list[$i]['lTargetCode'], 2));

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
$objPHPExcel->getProperties()->setDescription("LINE地政士名單");
##

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('LINE地政士名單');
##

//設定欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

//地政士編號    姓名    LIN暱稱    電話    事務所    地址

//寫入title資料
$objPHPExcel->getActiveSheet()->setCellValue('A1', '地政士編號'); // A 仲介
$objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'LINE暱稱');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '電話');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '事務所');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '地址');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '業務');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '經辦');
//寫入各店家資料
$index = 2;

foreach ($list as $k => $v) {
    // echo $list[$i]['lId']."_".$list[$i]['lTargetCode']."_".$list[$i]['ssName']."_".$list[$i]['lCaseMobile']."_".$list[$i]['sOffice']."_".$list[$i]['city'].$list[$i]['area'].$list[$i]['sCpAddress']."_".$list[$i]['lNickName']."_".$list[$i]['Sales']."<br>";

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $index, $v['lTargetCode']); // A 仲介
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $index, $v['ssName']);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $index, $v['lNickName'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $index, $v['lCaseMobile'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $index, $v['sOffice']);
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $index, $v['city'] . $v['area'] . $v['sCpAddress']);

    $objPHPExcel->getActiveSheet()->setCellValue('G' . $index, implode(',', $v['aales']));

    $objPHPExcel->getActiveSheet()->setCellValue('H' . $index, $v['sUndertaker']);

    $index++;
}

##

$_file = iconv('UTF-8', 'BIG5', 'LINE地政士名單');
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

function getScrivenerSales($sId)
{

    global $conn;
    $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = ss.sSales) AS Name FROM tScrivenerSales AS ss WHERE sScrivener = '" . substr($sId, 2) . "'";

    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $sales[] = $rs->fields['Name'];

        $rs->MoveNext();
    }
    return implode(',', $sales);
}
function getSS($id)
{
    global $conn;
    $sales = array();
    $sql   = "SELECT sName FROM tScrivenerSms WHERE sMobile = '" . $id . "' AND sDel = 0";

    $rs = $conn->Execute($sql);

    if ($rs->fields['sName']) {
        return $rs->fields['sName'];
    } else {
        return '';
    }
}
