<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/excel_cs2022.class.php';
require_once dirname(__DIR__) . '/class/excel_pu2022.class.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/session_check.php';

if (empty($_POST['action']) || !in_array($_POST['action'], ['date', 'certifiedId'])) {
    throw new Exception('Invalid request action');
}

ob_clean();

$excel    = null;
$prefix   = '';
$filename = '';
$rule     = array();

$logs = new Intolog();

switch ($_POST['report']) {
    case 5: //2022客供商檔
        $excel  = new ExcelCs($_POST);
        $prefix = 'CS';
        $logs->writelog('CSExcel');
        break;
    case 6: //2022進銷檔
        $excel  = new ExcelPu($_POST);
        $prefix = 'PU';
        $logs->writelog('PUExcel');
        break;
}

if ($_POST['action'] === 'date') {
    if (empty($_POST['fds']) || empty($_POST['fde']) || !preg_match('/^\d{3}-\d{2}-\d{2}$/', $_POST['fds']) || !preg_match('/^\d{3}-\d{2}-\d{2}$/', $_POST['fde'])) {
        throw new Exception('Invalid request date format');
    }

    $excel->GenerateMeta();
    $excel->GenerateTitle();
    $excel->GenerateField();

    $excel->PutTitleInto();
    $excel->PutDataInto();
    $excel->PutBgInto();
    $filename = $excel->GenerateFileName($prefix, $_POST['action']);
    $excel->OutPutBrower($filename);

    exit;

}

if ($_POST['action'] === 'certifiedId') {
    if (empty($_POST['certifiedId'])) {
        throw new Exception('Invalid request certifiedId');
    }

    $cIds = $_POST['certifiedId'];
    $cIds = explode(',', $cIds);

    $excel->GenerateMeta();
    $excel->GenerateTitle();
    $excel->GenerateField($cIds);

    $excel->PutTitleInto();
    $excel->PutDataInto();
    $excel->PutBgInto();
    $filename = $excel->GenerateFileName($prefix, $_POST['certifiedId']);
    $excel->OutPutBrower($filename);

    exit;
}
