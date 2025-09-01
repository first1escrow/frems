<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';


if($_SESSION['member_pDep'] == 7) {
    $_POST['sales_download'] = $_SESSION['member_id'];
}
//年度選擇
$year       = empty($year) ? (date("Y") - 1911) : $year;
$gov_period = [];

for ($i = date("Y"); $i >= 2023; $i--) {
    $year_option[($i - 1911)] = ($i - 1911) . '年度';
}
##
if($_POST['submit_download'] == 'Y') {
    $conn = new first1DB;

//取得比率定義
    $sql  = 'SELECT sRate as rate FROM tSalesReportAchievementRatio ORDER BY sDate DESC LIMIT 1;';
    $rs   = $conn->one($sql);
    $rate = empty($rs['rate']) ? 0.1 : $rs['rate'];
    $rate = $rate + 1;
##

    $adYear = $_POST['year_download'] + 1911;
//計算目標單月
    $lastYear = $adYear - 1;

    $sql = '
        SELECT 
            (SUM(sCertifiedMoney)-SUM(sFeedBackMoney))*(' . $rate . ' )/12 as Total
         FROM 
            tSalesReportAchievement AS a
         WHERE 
            a.sSales = :sSales 
           AND YEAR(sDate) = :adyear ;
       ';
    $rs = $conn->one($sql, ['sSales' => $_POST['sales_download'], 'adyear' => $lastYear]);

    $monthTotal = round($rs['Total']);
##
//報表
    $sql = "SELECT
            s.sDate,
            i.pName,
            s.mon,
            (@SUM2 := @SUM2 + s.mon) AS total,
            (s.sCertifiedMoney - s.sFeedBackMoney) AS sCertifiedMoney,
            (@sum := @sum + s.sCertifiedMoney - s.sFeedBackMoney) AS sCertifiedMoneyTotal,
            s.lastsCertifiedMoney,
            (@SUM3 := @SUM3 + s.lastsCertifiedMoney) AS lastsCertifiedMoneyTotal
        FROM
            (
                SELECT 
                    DATE_ADD( a.sDate , INTERVAL 1 YEAR ) AS sDate,
                    (SELECT sCertifiedMoney FROM tSalesReportAchievement WHERE sSales = :sSales and sDate =  DATE_ADD( a.sDate, INTERVAL 1 YEAR )) AS sCertifiedMoney,
                    (SELECT sFeedBackMoney FROM tSalesReportAchievement WHERE sSales = :sSales and sDate =  DATE_ADD( a.sDate, INTERVAL 1 YEAR )) AS sFeedBackMoney,
                    :monthTotal AS mon,
                    (sCertifiedMoney - sFeedBackMoney) AS lastsCertifiedMoney,
                    sSales
                FROM 
                    tSalesReportAchievement AS a , (select @sum :=0) b, (select @SUM2 :=0) c, (select @SUM3 :=0) d
                WHERE 
                    a.sSales = :sSales AND YEAR(sDate) = :lastYear
            ) AS s
        LEFT JOIN 
            tPeopleInfo AS i ON s.sSales = i.pId 
        ORDER BY 
            s.sDate ASC;";

    $data = $conn->all($sql, ['sSales' => $_POST['sales_download'], 'lastYear' => $lastYear, 'monthTotal' => $monthTotal]);

    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

    $objPHPExcel = new PHPExcel();
    //Set properties 設置文件屬性
    $objPHPExcel->getProperties()->setCreator("第一建經");
    $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
    $objPHPExcel->getProperties()->setTitle("第一建經");
    $objPHPExcel->getProperties()->setSubject("第一建經 業務績效目標與達成率");
    $objPHPExcel->getProperties()->setDescription("第一建經 業務績效目標與達成率");

    //指定目前工作頁
    $objPHPExcel->setActiveSheetIndex(0);

    //欄寬
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('12');
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('12');
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('12');
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('12');
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('12');
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('12');
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('12');
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('12');
    //邊框線
    $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);
    $objPHPExcel->getActiveSheet()->getStyle('A2:L2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);
    $objPHPExcel->getActiveSheet()->getStyle('C1:L1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
    $objPHPExcel->getActiveSheet()->getStyle('A2:L2')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);


    $objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '月份');
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->mergeCells('B1:B2');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '業務');
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->mergeCells('C1:D1');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '目標');
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->mergeCells('E1:F1');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '實際');
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->mergeCells('G1:H1');
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('G1', '與目標比較 ±值(%)');
    $objPHPExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->mergeCells('I1:J1');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '去年');
    $objPHPExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->mergeCells('K1:L1');
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('K1', '與去年實際比較 ±值(%)');
    $objPHPExcel->getActiveSheet()->getStyle('K1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('C2', '單月');
    $objPHPExcel->getActiveSheet()->getStyle('C2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('D2', '累計');
    $objPHPExcel->getActiveSheet()->getStyle('D2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('E2', '單月');
    $objPHPExcel->getActiveSheet()->getStyle('E2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('F2', '累計');
    $objPHPExcel->getActiveSheet()->getStyle('F2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('G2', '單月');
    $objPHPExcel->getActiveSheet()->getStyle('G2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('H2', '累計');
    $objPHPExcel->getActiveSheet()->getStyle('H2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('I2', '單月');
    $objPHPExcel->getActiveSheet()->getStyle('I2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('J2', '累計');
    $objPHPExcel->getActiveSheet()->getStyle('J2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('K2', '單月');
    $objPHPExcel->getActiveSheet()->getStyle('K2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('L2', '累計');
    $objPHPExcel->getActiveSheet()->getStyle('L2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->getStyle('A:L')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $row = 3;
    foreach ($data as $key => $value) {
        $timestamp = strtotime($value['sDate']);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, date('m', $timestamp) . ' 月'); //月份
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $value['pName']); //業務
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, number_format($value['mon'])); //單月
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, number_format($value['total'])); //累計
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, number_format($value['sCertifiedMoney'])); //單月
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, number_format($value['sCertifiedMoneyTotal'])); //累計

        $growthMonth = ($value['sCertifiedMoney'] - $value['mon']) / $value['mon'] * 100;
        $growthRate = ($value['sCertifiedMoneyTotal'] - $value['total']) / $value['total'] * 100;

        if($value['sCertifiedMoney'] == 0) {
            $growthMonth = 0;
            $growthRate = 0;
        }

        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, round($growthMonth, 2)); //單月
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, round($growthRate, 2)); //累計

        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, number_format($value['lastsCertifiedMoney'])); //單月
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, number_format($value['lastsCertifiedMoneyTotal'])); //累計

        $compareMonth = ($value['sCertifiedMoney'] - $value['lastsCertifiedMoney']) / $value['lastsCertifiedMoney'] * 100;
        $realGrowthRate = ($value['sCertifiedMoneyTotal'] - $value['lastsCertifiedMoneyTotal']) / $value['lastsCertifiedMoneyTotal'] * 100;
        if($value['sCertifiedMoney'] == 0) {
            $compareMonth = 0;
            $realGrowthRate = 0;
        }
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, round($compareMonth, 2)); //單月
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, round($realGrowthRate, 2)); //累計

        //背景顏色
        if($row % 2 == 1) {
            $objPHPExcel->getActiveSheet()->getStyle('A'. $row .':L'. $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f2f2f2');
        }

        $row++;
    }


    $_file = date('Y-m-d').$value['pName']. '.xlsx';

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

##
}


//取得業務列表
function getSales()
{
    $conn = new first1DB;

    $sales = [];
    if ($_SESSION['member_pDep'] == 7) {
        $sql = 'SELECT pId, pName FROM tPeopleInfo WHERE pId = :pId;';
        $rs  = $conn->all($sql, ['pId' => $_SESSION['member_id']]);
    } else {
        // $sql = 'SELECT pId, pName FROM tPeopleInfo WHERE pDep IN (4, 7) AND pJob = 1 AND pId <> 66;';
        $sql = 'SELECT pId, pName FROM tPeopleInfo WHERE pDep IN (7) AND pJob = 1 AND pId <> 66;';
        $rs  = $conn->all($sql);
        // $sales = [0 => '全部'];
    }

    if (!empty($rs)) {
        foreach ($rs as $v) {
            $sales[$v['pId']] = $v['pName'];
        }
    }

    return $sales;
}

$sales_option = getSales();

##

$smarty->assign('year', $year);
$smarty->assign('year_option', $year_option);
$smarty->assign('sales', $sales);
$smarty->assign('sales_option', $sales_option);
$smarty->display('salesAchievement.inc.tpl', '', 'report');
