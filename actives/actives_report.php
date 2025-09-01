<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$cat = trim($_POST['cat']);

// echo $cat;
//輸出Excel檔案
if ($cat == '2015sheep') {

    // $logs->writelog('accChecklistExcel') ;
    include_once 'actives_2015_sheep_excel.php';
}
##

$smarty->display('actives_report.inc.tpl', '', 'actives');
