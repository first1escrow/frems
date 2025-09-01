<?php
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

//取得所有業務
function getSales()
{
    global $conn;

    $sales = [];

    // $sql = "SELECT pId, pName FROM tPeopleInfo WHERE pDep IN (4, 7) AND pJob = 1 AND pId <> 66;"; //仍在職的業務代表
    $sql = "SELECT pId, pName FROM tPeopleInfo WHERE pDep IN (4, 7) AND pJob = 1;"; //仍在職的業務代表
    $rs  = $conn->Execute($sql);
    while (!$rs->EOF) {
        $sales[$rs->fields['pId']] = $rs->fields['pName'];
        $rs->MoveNext();
    }

    return $sales;
}
##

//取得地區業務代表
function getZipSales($city, $area, $sales)
{
    global $conn;

    $zip = [];

    $sql = 'SELECT * FROM tZipArea WHERE zCity = "' . $city . '" AND zArea = "' . $area . '";';
    $rs  = $conn->Execute($sql);

    if (!$rs->EOF) {
        $zip = [
            'zip'   => $rs->fields['zZip'],
            'id'    => $rs->fields['zSales'],
            'sales' => $sales[$rs->fields['zSales']],
        ];
    }

    return $zip;
}
##

//取得台中區業務代表
function getTaichungSales()
{
    global $conn;

    $sql = 'SELECT GROUP_CONCAT(zArea) AS allArea, zSales FROM tZipArea AS z WHERE z.zCity = \'台中市\' GROUP BY zSales;';
    $rs  = $conn->Execute($sql);

    $areas = array();
    while (!$rs->EOF) {
        $area = [
            'sales' => $rs->fields['zSales'],
            'area'   => $rs->fields['allArea'],
        ];
        
        array_push($areas, $area);
    
        $rs->MoveNext();
    }
    return $areas;
}
##

/**
 * excel
 */
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("各品牌店家名單");
$objPHPExcel->getProperties()->setDescription("第一建經各品牌店家名單");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('各品牌店家名單');
// $objPHPExcel->getActiveSheet()->getStyle("A1:Z24")->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

//寫入表頭資料

// $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);
// $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValue('A1', "各品牌店家名單");
$objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
$col = 65;
$row = 2;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '品牌');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '店家名稱');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '公司名稱');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '地址');
$row++;

$zipCount  = [];
$salesArea = [];
$_total = 0;
for ($i = 0; $i < count($list); $i++) {
    $col = 65;
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['brandName']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['sname']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['scompany']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['city'] . $list[$i]['area'] . $list[$i]['addr']);

    $zipCount[$list[$i]['city']]++;
    $salesArea[$list[$i]['city']][$list[$i]['area']]++;
    $_total += 1;
    $row++;
}
#####
$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setTitle('地區數量');

$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '地區');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '數量');
$row++;
$sql = "SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY nid ASC";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $col = 65;

    if($rs->fields['zCity'] == '台中市') {
        $allArea = getTaichungSales();
        $count38 = 0; //陳立寰
        $count72 = 0; //柯富閔
        foreach($allArea as $area) {
            foreach($salesArea[$rs->fields['zCity']] as $key => $value) {
                if(true ==  preg_match('/'.$key.'/i', $area['area'])) {
                    if($area['sales'] == 38) {
                        $count38 = $count38 + $value ;
                    }
                    if($area['sales'] == 72) {
                        $count72 = $count72 + $value ;
                    }
                }
            }
        }

        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '台中市_陳立寰');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $count38);
        $row++;
        $col = 65;
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '台中市_柯富閔');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $count72);
    } else {
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $rs->fields['zCity']);
        $count = ($zipCount[$rs->fields['zCity']] != '') ? $zipCount[$rs->fields['zCity']] : 0;
    
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $count);
    }

    $row++;
    $rs->MoveNext();
}

/**
 * 20230118 新增第3頁(業務服務品牌數量統計)
 */
$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(2);
$objPHPExcel->getActiveSheet()->setTitle('負責業務數量統計');

$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '業務');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '數量');
$row++;

$sales = getSales();

$salesCount = [];
$_totalA = $_totalB = 0;
foreach ($salesArea as $k => $v) {
    $city = $k;
    foreach ($v as $ka => $va) {
        $area = $ka;
        $zip  = getZipSales($city, $area, $sales);

        if (!empty($zip['id'])) {
            if (preg_match("/\,/iu", $zip['id'])) {
                $_tmp   = explode(',', $zip['id']);
                $_max   = count($_tmp);
                $_point = round(($va / $_max), 1);

                foreach ($_tmp as $_v) {
                    $salesCount[$_v] += $_point;
                }

                $_tmp = $_max = $_point = null;
                unset($_tmp, $_max, $_point);
            } else {
                $salesCount[$zip['id'] ]+= $va;
            }
        }

        $zips = $zip = null;
        unset($zips, $zip);
    }
}

$row = 1;
foreach ($sales as $k => $v) {
    $col = 65;
    $row ++;

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $salesCount[$k]);
}

######################################################################################3
$objPHPExcel->setActiveSheetIndex(0);

$_file = 'brandReport.xlsx';

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

function date_change($str)
{
    $tmp = explode('-', $str);

    $tmp[0] = 1911 + $tmp[0];

    $str = $tmp[0] . '-' . $tmp[1] . '-' . $tmp[2];

    return $str;
}