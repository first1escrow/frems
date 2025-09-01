<?php
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';

//以簽約日期為主
$date_start = "2015-03-01 00:00:00";
$date_end   = "2015-08-31 23:59:59";
//活動期間 : 104年3月1日起至104年08月31日止，共計六個月。
//1.    台灣房屋及優美地產成交案件，每件計得1點。
// 2.    其他房仲品牌成交案件或代書自有非仲介成交案件每件計得2點。

//進行中、已結案
##
$sql = " SELECT
			cc.cCertifiedId AS cCertifiedId,
			cc.cSignDate AS cSignDate,
			cr.cBrand,
			cr.cBrand1,
			cr.cBrand2,
			cs.cScrivener

		 FROM
		 	tContractCase AS cc
		 LEFT JOIN
		 	tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		 LEFT JOIN
		 	tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId

		 WHERE
		 	cc.cSignDate >='" . $date_start . "'
		 	AND cc.cSignDate<='" . $date_end . "'
		 	AND cc.cCaseStatus IN (2,3)
		 ORDER BY cs.cScrivener ASC
		";

$rs = $conn->Execute($sql);
// $total=$rs->RecordCount();//計算總筆數
$i = 0;
while (!$rs->EOF) {

    $list[$i] = $rs->fields;

    $list[$i]['cBrand']  = point($list[$i]['cBrand']);
    $list[$i]['cBrand1'] = point($list[$i]['cBrand1']);
    $list[$i]['cBrand2'] = point($list[$i]['cBrand2']);

    // echo $list[$i]['cBrand']."-".$list[$i]['cBrand1']."-".$list[$i]['cBrand2']."<br>";

    if ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] != 0 && $list[$i]['cBrand2'] != 0) {

        $brand = array($list[$i]['cBrand'], $list[$i]['cBrand1'], $list[$i]['cBrand2']);

    } elseif ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] != 0 && $list[$i]['cBrand2'] == 0) {

        $brand = array($list[$i]['cBrand'], $list[$i]['cBrand1']);

    } elseif ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] == 0 && $list[$i]['cBrand2'] == 0) {

        $brand = array($list[$i]['cBrand']);
    }

    rsort($brand);

    $arr[$list[$i]['cScrivener']]['point']      = $arr[$list[$i]['cScrivener']]['point'] + $brand[0];
    $arr[$list[$i]['cScrivener']]['cScrivener'] = $list[$i]['cScrivener'];

    if ($brand[0] == 1) { //台屋

        $arr[$list[$i]['cScrivener']]['tw_point'] = $arr[$list[$i]['cScrivener']]['tw_point'] + $brand[0];
    } elseif ($brand[0] == 2) { //非台屋
        $arr[$list[$i]['cScrivener']]['untw_point'] = $arr[$list[$i]['cScrivener']]['untw_point'] + $brand[0];
    }

    unset($brand);
    $i++;
    $rs->MoveNext();
}
unset($list);
// echo "<pre>";
// print_r($arr);
// echo "</pre>";
// die;

##
// $sql = "SELECT
//     tra.tMoney AS  money,
//     cc.cCertifiedId AS cCertifiedId,
//     cc.cSignDate AS cSignDate,
//     cr.cBrand,
//     cr.cBrand1,
//     cr.cBrand2,
//     cs.cScrivener,
//     (SELECT cCertifiedMoney FROM tContractIncome AS ci WHERE ci.cCertifiedId=cc.cCertifiedId ) AS cCertifiedMoney,
//     cc.cCaseStatus AS status
// FROM
//     tBankTrans AS tra
// LEFT JOIN
//     tContractCase AS cc ON cc.cCertifiedId=tra.tMemo
// LEFT JOIN
//     tContractScrivener AS cs ON cs.cCertifiedId=tra.tMemo
// LEFT JOIN
//     tContractRealestate AS cr ON cr.cCertifyId=tra.tMemo
// WHERE
//     tra.tMemo <> '000000000' AND
//     cc.cSignDate >='".$date_start."'
//     AND cc.cSignDate<='".$date_end."'
//     AND tra.tAccount IN ('27110351738','10401810001889','20680100135997') AND tra.tPayOk='1'
//     AND cc.cCaseStatus = 4
//     ";

$sql = "SELECT
			cc.cCertifiedId AS cCertifiedId,
			cc.cSignDate AS cSignDate,
			cr.cBrand,
			cr.cBrand1,
			cr.cBrand2,
			cs.cScrivener,
			(SELECT cTotalMoney FROM tContractIncome AS ci WHERE ci.cCertifiedId =cc.cCertifiedId) AS totalMoney,
			(SELECT cCertifiedMoney FROM tContractIncome AS ci WHERE ci.cCertifiedId =cc.cCertifiedId) AS cCertifiedMoney

		 FROM
		 	tContractCase AS cc
		 LEFT JOIN
		 	tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		 LEFT JOIN
		 	tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId

		 WHERE
		 	cc.cSignDate >='" . $date_start . "'
		 	AND cc.cSignDate<='" . $date_end . "'
		 	AND cc.cCaseStatus = 4
		 ORDER BY cs.cScrivener ASC";
// echo $sql;
// die;
$rs = $conn->Execute($sql);
// $total=$rs->RecordCount();//計算總筆數
$i = 0;
while (!$rs->EOF) {

    $list[$i] = $rs->fields;

    $list[$i]['cBrand']  = point($list[$i]['cBrand']);
    $list[$i]['cBrand1'] = point($list[$i]['cBrand1']);
    $list[$i]['cBrand2'] = point($list[$i]['cBrand2']);

    // echo $list[$i]['cBrand']."-".$list[$i]['cBrand1']."-".$list[$i]['cBrand2']."<br>";

    if ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] != 0 && $list[$i]['cBrand2'] != 0) {

        $brand = array($list[$i]['cBrand'], $list[$i]['cBrand1'], $list[$i]['cBrand2']);

    } elseif ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] != 0 && $list[$i]['cBrand2'] == 0) {

        $brand = array($list[$i]['cBrand'], $list[$i]['cBrand1']);

    } elseif ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] == 0 && $list[$i]['cBrand2'] == 0) {

        $brand = array($list[$i]['cBrand']);
    }

    rsort($brand);

    //算點數的

    $totalMoney_cmoney = $list[$i]['totalMoney'] * 0.0006;

    if ($list[$i]['cCertifiedMoney'] == (string) $totalMoney_cmoney) {

        $arr[$list[$i]['cScrivener']]['point']      = $arr[$list[$i]['cScrivener']]['point'] + $brand[0];
        $arr[$list[$i]['cScrivener']]['cScrivener'] = $list[$i]['cScrivener'];

        if ($brand[0] == 1) { //台屋

            $arr[$list[$i]['cScrivener']]['tw_point'] = $arr[$list[$i]['cScrivener']]['tw_point'] + $brand[0];
        } elseif ($brand[0] == 2) { //非台屋
            $arr[$list[$i]['cScrivener']]['untw_point'] = $arr[$list[$i]['cScrivener']]['untw_point'] + $brand[0];
        }

    } else {

    }
    // echo '456<br>';
    unset($totalMoney_cmoney);

    ##
    unset($brand);
    $i++;
    $rs->MoveNext();
}
unset($list);

// echo "<pre>";
// print_r($arr);
// echo "</pre>";
// die;
// die;

##
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("羊羊得意 第一讚");
$objPHPExcel->getProperties()->setDescription("第一建經 羊羊得意 第一讚");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//寫入表頭資料
// $objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
$objPHPExcel->getActiveSheet()->setCellValue('A1', '編號');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '身分證字號');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '地政士事務所');

$objPHPExcel->getActiveSheet()->setCellValue('E1', '地址');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '台屋點數(台屋+優美)');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '非台屋點數');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '總點數');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '禮券金額');

//顏色
// $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFill()->getStartColor()->setARGB('FFEBEB');

//寬度
// $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
##

// $arr = explode(',', $people);
// sort($arr); //順序重排一下

// echo "<pre>";
// print_r($arr);
// echo "</pre>";
// die;
$row1 = 2;

$sql = "
		SELECT
			sName AS MainName,
			sOffice AS MainOffice,
			sId,
			sAddress,
			(SELECT zCity FROM tZipArea AS z WHERE z.zZip=sZip1) AS city,
			(SELECT zArea FROM tZipArea AS z WHERE z.zZip=sZip1) AS area,
			sIdentifyId
		FROM
			tScrivener
		WHERE
			sStatus=1
			ORDER BY sId ASC";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {

    $list[] = $rs->fields;

    $rs->MoveNext();
}
// echo "<pre>";
// print_r($list);
// echo "</pre>";
// die;
for ($i = 0; $i < count($list); $i++) {

    $sql = "SELECT sName,sMobile FROM tScrivenerSms WHERE sScrivener ='" . $list[$i]['sId'] . "'";

    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        $sms[] = $rs->fields['sName'] . "(" . $rs->fields['sMobile'] . ")";
        $rs->MoveNext();
    }

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row1, 'SC' . str_pad($list[$i]['sId'], '4', '0', STR_PAD_LEFT));
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $row1, $list[$i]['MainName']);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $row1, $list[$i]['sIdentifyId']);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $row1, $list[$i]['MainOffice']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $row1, $list[$i]['city'] . $list[$i]['area'] . $list[$i]['sAddress']);

    if ($arr[$list[$i]['sId']]['point'] == '') {
        $arr[$list[$i]['sId']]['point'] = 0;
    }

    if ($arr[$list[$i]['sId']]['tw_point'] == '') {
        $arr[$list[$i]['sId']]['tw_point'] = 0;
    }

    if ($arr[$list[$i]['sId']]['untw_point'] == '') {
        $arr[$list[$i]['sId']]['untw_point'] = 0;
    }

    if ($arr[$list[$i]['sId']]['point'] >= 150) {
        $money = "60000";

    } elseif ($arr[$list[$i]['sId']]['point'] >= 110 && $arr[$list[$i]['sId']]['point'] < 150) {
        $money = "30000";
    } elseif ($arr[$list[$i]['sId']]['point'] >= 80 && $arr[$list[$i]['sId']]['point'] < 110) {
        $money = "20000";
    } elseif ($arr[$list[$i]['sId']]['point'] >= 50 && $arr[$list[$i]['sId']]['point'] < 80) {
        $money = "12000";
    } elseif ($arr[$list[$i]['sId']]['point'] >= 25 && $arr[$list[$i]['sId']]['point'] < 50) {
        $money = "6000";
    } elseif ($arr[$list[$i]['sId']]['point'] >= 15 && $arr[$list[$i]['sId']]['point'] < 25) {
        $money = "3000";
    }

    if ($money == '') {
        $money = 0;
    }

    $objPHPExcel->getActiveSheet()->setCellValue('F' . $row1, $arr[$list[$i]['sId']]['tw_point']);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $row1, $arr[$list[$i]['sId']]['untw_point']);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $row1, $arr[$list[$i]['sId']]['point']);

    $objPHPExcel->getActiveSheet()->setCellValue('I' . $row1, $money);
    // $objPHPExcel->getActiveSheet()->setCellValue('E'.$row1,@implode(',', $sms));

    // $tw_point = $tw_point+$arr[$list[$i]['sId']]['tw_point'];

    // $untw_point = $untw_point+$arr[$list[$i]['sId']]['untw_point'];

    $row1++;

    unset($sms);unset($money);
}

// $objPHPExcel->getActiveSheet()->setCellValue('I1','台屋點數');
// $objPHPExcel->getActiveSheet()->setCellValue('J1','非台屋點數');
// $objPHPExcel->getActiveSheet()->setCellValue('K1','總點數');

// //顏色
// $objPHPExcel->getActiveSheet()->getStyle('I1:K2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle('I1:K2')->getFill()->getStartColor()->setARGB('FFEBEB');
// ##
// $objPHPExcel->getActiveSheet()->setCellValue('I2',$tw_point);
// $objPHPExcel->getActiveSheet()->setCellValue('J2',$untw_point);

// $tmp = $tw_point+$untw_point;

// $objPHPExcel->getActiveSheet()->setCellValue('K2',$tmp);

// unset($tmp);

$_file = '2015sheep.xlsx';

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

function point($brand)
{
    if ($brand == 1 || $brand == 49) {

        $point = 1;
    } else if ($brand != 0) {

        $point = 2;
    } else {
        $point = 0; //
    }

    return $point;
}
