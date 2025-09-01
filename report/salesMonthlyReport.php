<?php
include_once '../configs/config.class.php';
include_once dirname(__DIR__).'/class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
require_once dirname(__DIR__).'/includes/lib/contractBank.php';

/**
 * 2024-09-19
 * 業務業績月報表
 * 資料分類 1:簽約日 2:進案日(耀哥.雄哥) 3:出款日(會計)
 */

$sid = (empty($_GET['sid'])) ? 0 : $_GET['sid'];
$salesId = $sid;
$kind = (int)$_GET['kind'];
$dataKind = ($kind < 1 || $_GET['kind'] > 3) ? 2 : $_GET['kind'];

if(date('m') <= '03'){
    $year = date('Y') -1;
    $yearFront = date('Y') -2;
    $startYM1 = (int)(($year-1).'01');
    $endYM1 = (int)(($year-1).'12');
    $startYM2 = (int)(($year).'01');
    $endYM2 = (int)(($year).'12');
} else {
    $year = date('Y');
    $yearFront = date('Y') - 1;
    $startYM1 = (int)(($year-1).'01');
    $endYM1 = (int)(($year-1).'12');
    $startYM2 = (int)(($year).'01');
    $endYM2 = (int)(($year).'12');
}

$dataYmStart = $yearFront*100 + 1;
$dataYmEnd = $year*100 + 12;
$menu_sales[] = '總表';
$menu_sales[66] = '公司';
$menu_sales[2] = '台屋體系';

//取得業務人員清單
$sql = 'SELECT * FROM tPeopleInfo WHERE pDep IN ("7","8") AND pJob =1 AND pId != 3 AND pId != 66 ORDER BY pId ASC;';
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $menu_sales[$rs->fields['pId']] = $rs->fields['pName'];

    if ($rs->fields['pJob'] == 2) {
        $menu_sales[$rs->fields['pId']] .= '(離)';
    }

    $rs->MoveNext();
}

$sql = "SELECT * FROM www_first1_report.salesPerformanceReport where 
               dataYM >= ".$dataYmStart." AND dataYM <= ".$dataYmEnd." AND salesId = ". $sid." AND dataKind = ".$dataKind." ORDER BY dataYM ASC;";
$rs = $conn->Execute($sql) ;

$allSales = [];
$chartData = [];

if($rs){
    for($xx = $dataYmStart ;$xx<=$dataYmStart+11;$xx++){
        $allSales[$rs->fields['salesId']][$xx]['certifiedMoney'] = 0;
        $allSales[$rs->fields['salesId']][$xx]['certifiedMoneyAvg'] = 0;
        $allSales[$rs->fields['salesId']][$xx]['feedbackMoney'] = 0;
        $allSales[$rs->fields['salesId']][$xx]['performanceMoney'] = 0;
        $allSales[$rs->fields['salesId']][$xx]['contractCaseCountAvg'] = 0;
    }

    while (!$rs->EOF) {
        if (!isset($allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoney'])) {
            $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoney'] = 0;
        }
        if (!isset($allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoneyAvg'])) {
            $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoneyAvg'] = 0;
        }
        if (!isset($allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['feedbackMoney'])) {
            $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['feedbackMoney'] = 0;
        }
        if (!isset($allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['performanceMoney'])) {
            $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['performanceMoney'] = 0;
        }
        if (!isset($allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['contractCaseCountAvg'])) {
            $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['contractCaseCountAvg'] = 0;
        }

        $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoney'] += $rs->fields['certifiedMoney'];
        $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoneyAvg'] += $rs->fields['certifiedMoneyAvg'];
        $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['feedbackMoney'] += $rs->fields['feedbackMoney'];
        $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['performanceMoney'] += $rs->fields['performanceMoney'];
        $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['contractCaseCountAvg'] += $rs->fields['contractCaseCountAvg'];

        $rs->MoveNext();
    }
}

foreach($allSales[$sid] as $k=>$v){
    if($k >= ($yearFront*100 + 1) && $k <= ($yearFront*100 + 12)){
        $label[1] = $yearFront . '年 成交件數';
        $label[3] = $yearFront . '年 實收額';
        $chartData[1][] = $v['contractCaseCountAvg'];
        $chartData[3][] = $v['performanceMoney']/10000;
    } else if($k >= ($year*100 + 1) && $k <= ($year*100 + 12)){
        $label[2] = $year . '年 成交件數';
        $label[4] = $year . '年 實收額';
        $chartData[2][] = $v['contractCaseCountAvg'];
        $chartData[4][] = $v['performanceMoney']/10000;
    }
}

$allData = [];

$tbl = "";

for ($x = $startYM1; $x <= $endYM1; $x++) {
    $colorIndex = ($x % 2 == 0) ? '' : '#F8ECE9';

    $allData['certifiedMoney'] += $allSales[$salesId][$x]['certifiedMoney'];
    $allData['certifiedMoneyAvg'] += $allSales[$salesId][$x]['certifiedMoneyAvg'];
    $allData['feedbackMoney'] += $allSales[$salesId][$x]['feedbackMoney'];
    $allData['performanceMoney'] += $allSales[$salesId][$x]['performanceMoney'];
    $allData['contractCaseCountAvg'] += $allSales[$salesId][$x]['contractCaseCountAvg'];

    $tbl .= "<tr style='background-color:".$colorIndex.";'>";
    $tbl .= "<td>" . substr($x,0,4) .'/'. substr($x,4,2) . "</td>";
    $tbl .= "<td>" . number_format($allSales[$salesId][$x]['certifiedMoney']) . "</td>";
    $tbl .= "<td>" . number_format($allSales[$salesId][$x]['certifiedMoneyAvg']) . "</td>";
    $tbl .= "<td>" . number_format($allSales[$salesId][$x]['feedbackMoney']) . "</td>";
    $tbl .= "<td>" . number_format($allSales[$salesId][$x]['performanceMoney']) . "</td>";
    $tbl .= "<td>" . $allSales[$salesId][$x]['contractCaseCountAvg'] . "</td>";
    $tbl .= "</tr>";

    if (substr($x, -2, 2) == 12) {
        $x = (int)(substr($x, 0, 4) . '99') + 1;
    }
}

$tbl .= "<tr style='background-color:#F8ECE9;'><td>總計</td>
            <td>".number_format($allData['certifiedMoney'])."</td>
            <td>".number_format($allData['certifiedMoneyAvg'])."</td>
            <td>".number_format($allData['feedbackMoney'])."</td>
            <td>".number_format($allData['performanceMoney'])."</td>
            <td>".number_format($allData['contractCaseCountAvg'],2)."</td>
            </tr>";

$per_performanceMoney = $allData['performanceMoney'];
$per_contractCaseCountAvg = $allData['contractCaseCountAvg'];

$allData = [];

$tbl2 = "";

for ($x = $startYM2; $x <= $endYM2; $x++) {
    $colorIndex = ($x % 2 == 0) ? '' : '#F8ECE9';

    $performanceIncrease = 0;
    if(isset($allSales[$salesId][$x - 100]['performanceMoney']) && $allSales[$salesId][$x - 100]['performanceMoney'] > 0){
        $performanceIncrease = round((($allSales[$salesId][$x]['performanceMoney'] - $allSales[$salesId][$x - 100]['performanceMoney']) / $allSales[$salesId][$x - 100]['performanceMoney']) * 100);
    }
    $countIncrease = 0;
    if(isset($allSales[$salesId][$x - 100]['contractCaseCountAvg']) && $allSales[$salesId][$x - 100]['contractCaseCountAvg'] > 0){
        $countIncrease = round((($allSales[$salesId][$x]['contractCaseCountAvg'] - $allSales[$salesId][$x - 100]['contractCaseCountAvg']) / $allSales[$salesId][$x - 100]['contractCaseCountAvg']) * 100);
    }

    $allData['certifiedMoney'] += $allSales[$salesId][$x]['certifiedMoney'];
    $allData['certifiedMoneyAvg'] += $allSales[$salesId][$x]['certifiedMoneyAvg'];
    $allData['feedbackMoney'] += $allSales[$salesId][$x]['feedbackMoney'];
    $allData['performanceMoney'] += $allSales[$salesId][$x]['performanceMoney'];
    $allData['contractCaseCountAvg'] += $allSales[$salesId][$x]['contractCaseCountAvg'];

    $tbl2 .= "<tr style='background-color:".$colorIndex.";'>";
    $tbl2 .= "<td>" . substr($x,0,4) .'/'. substr($x,4,2) . "</td>";
    $tbl2 .= "<td>" . number_format($allSales[$salesId][$x]['certifiedMoney']) . "</td>";
    $tbl2 .= "<td>" . number_format($allSales[$salesId][$x]['certifiedMoneyAvg']) . "</td>";
    $tbl2 .= "<td>" . number_format($allSales[$salesId][$x]['feedbackMoney']) . "</td>";
    $tbl2 .= "<td>" . number_format($allSales[$salesId][$x]['performanceMoney']) . "</td>";
    $tbl2 .= "<td>" . number_format($allSales[$salesId][$x]['contractCaseCountAvg'],2) . "</td>";
    $tbl2 .= "<td>" . $performanceIncrease . "%</td>";
    $tbl2 .= "<td>" . $countIncrease . "%</td>";
    $tbl2 .= "</tr>";

    if (substr($x, -2, 2) == 12) {
        $x = (int)(substr($x, 0, 4) . '99') + 1;
    }
}

$tbl2 .= "<tr style='background-color:#F8ECE9;'><td>總計</td>
            <td>".number_format($allData['certifiedMoney'])."</td>
            <td>".number_format($allData['certifiedMoneyAvg'])."</td>
            <td>".number_format($allData['feedbackMoney'])."</td>
            <td>".number_format($allData['performanceMoney'])."</td>
            <td>".number_format($allData['contractCaseCountAvg'],2)."</td>
            <td>".number_format(round(($allData['performanceMoney'] - $per_performanceMoney)*100 / $per_performanceMoney))."%</td>
            <td>".number_format(round(($allData['contractCaseCountAvg'] - $per_contractCaseCountAvg)*100 / $per_contractCaseCountAvg))."%</td>
            </tr>";
##

$smarty->assign('tbl',$tbl) ;
$smarty->assign('tbl2',$tbl2) ;
$smarty->assign('menu_sales', $menu_sales);
$smarty->assign('sales', $salesId);
$smarty->assign('sales_name', $menu_sales[$salesId]);
$smarty->assign('label', $label);
$smarty->assign('chart_data', $chartData);
$smarty->assign('dataKind', $dataKind);
$smarty->display('salesMonthlyReport.tpl', '', 'report') ;
?>