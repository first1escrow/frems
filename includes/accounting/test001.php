<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/excel_cs2022.class.php';
require_once dirname(dirname(__DIR__)) . '/class/excel_pu2022.class.php';
require_once dirname(dirname(__DIR__)) . '/class/intolog.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

ob_clean();

$excel    = null;
$prefix   = '';
$filename = '';
$rule     = array();

$logs = new Intolog();

switch ($_GET['report']) {
    case 5: //2022客供商檔
        $excel  = new ExcelCs($_GET);
        $prefix = 'CS';
        $logs->writelog('CSExcel');
        break;
    case 6: //2022進銷檔
        $excel  = new ExcelPu($_GET);
        $prefix = 'PU';
        $logs->writelog('PUExcel');
        break;
}

$excel->GenerateMeta();
$excel->GenerateTitle();
$excel->GenerateField();

$excel->PutTitleInto();
$excel->PutDataInto();
$excel->PutBgInto();
$filename = $excel->GenerateFileName($prefix);
$excel->OutPutBrower($filename);

exit;
