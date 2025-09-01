<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

//計算上一季最後的一個月的日期
function lastSeasonMonth($year, $month)
{
    $this_season               = ceil($month / 3);
    $first_month_of_the_season = $this_season * 3 - 2;
    $_date                     = $year . '-' . str_pad($first_month_of_the_season, 2, '0', STR_PAD_LEFT) . '-01';

    return date("Y-m", strtotime('-1 month', strtotime($_date))) . '-31';
}
##

$script = '';

if (($_SESSION['member_sales'] != 1) && ($_SESSION['member_pDep'] == 7)) {
    $script = 'window.open("../calendar/calendar.php","_blank") ;';
    $script .= 'window.open("../sales/certifiedFee.php?s=1","cf","status=no") ;';
    $_SESSION['member_sales'] = 1;
}

//20230214 增加提示業務回饋金申請狀態未完成提示
$_endTime2 = lastSeasonMonth(date("Y"), date("m"));

$sql = 'SELECT
                COUNT(*) as total
            FROM (
                SELECT
                    sf.sId,
                    sf.sStoreCode,
                    sf.sStoreId,
                    CASE sf.sStoreCode
                        WHEN "SC"
                        THEN (SELECT sSales as sales FROM tScrivenerSales WHERE sScrivener = sf.sStoreId AND sSales = ' . $_SESSION['member_id'] . ')
                        ELSE (SELECT bSales as sales FROM tBranchSales WHERE bBranch = sf.sStoreId AND bSales = ' . $_SESSION['member_id'] . ')
                    END AS sales
                FROM
                    tStoreFeedBackMoneyFrom AS sf
                WHERE
                    sf.sDelete = 0
                    AND sf.sStatus IN (1, 2)
                    AND sf.sCategory IN (1,2,3)
                    AND sf.sEndTime2 <= "' . $_endTime2 . '"
                    AND sf.sStoreName NOT LIKE "%總管理%"
            ) as tb
            WHERE
                sales = ' . $_SESSION['member_id'] . ';';
$rs = $conn->Execute($sql);
if (!$rs->EOF) {
    if ($rs->fields['total'] > 0) {
        $script .= 'alert("尚有客戶回饋金未請款，請即時處理");';
    }
}

$_endTime2 = $rs = null;
unset($_endTime2, $rs);
##

$lastYear  = date("Y", strtotime("-1 month"));
$lastMonth = date("m", strtotime("-1 month"));

$last2Year  = date("Y", strtotime("-2 month"));
$last2Month = date("m", strtotime("-2 month"));

$smarty->assign('script', $script);
$smarty->assign('lastMonth', $lastMonth);
$smarty->assign('last2Month', $last2Month);

$smarty->display('salesTracking.inc.tpl', '', 'sales');
