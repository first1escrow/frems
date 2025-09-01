<?php

ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);

include_once '../../configs/config.class.php';
include_once 'class/excel_cs.class.php';
include_once 'class/excel_pu.class.php';
include_once 'class/excel_cs1.class.php';
include_once 'class/excel_pu1.class.php';
include_once 'class/excel_cs2.class.php';
include_once 'class/excel_pu2.class.php';
include_once 'class/intolog.php' ;
include_once '../../session_check.php' ;

// error_reporting(E_ALL);


$excel = null;
$prefix = '';
$filename = '';
$rule = Array();

//�w��log����
$logs = new Intolog() ;
##

switch ($_GET['report']) {
    case 1:     //�ȨѰ���
        $excel = new ExcelCs($_GET);
        $prefix = 'CS';
        $logs->writelog('CSExcel') ;
        break;
    case 2:     //�i�P��
        $excel = new ExcelPu($_GET);
        $prefix = 'PU';
        $logs->writelog('PUExcel') ;
        break;
    case 3:     //�ȨѰ��� 2015-06-24 ��
        $excel = new ExcelCs1($_GET);
        $prefix = 'CS';
        $logs->writelog('CS1Excel') ;
        break;
    case 4:     //�i�P�� 2015-06-24 ��
        $excel = new ExcelPu1($_GET);
        $prefix = 'PU';
        $logs->writelog('PU1Excel') ;
        break;
     case 5:     //�ȨѰ���
        $excel = new ExcelCs2($_GET);
        $prefix = 'CS';
        $logs->writelog('CS2Excel') ;
        break;
     case 6:     //�i�P��
        $excel = new ExcelPU2($_GET);
        $prefix = 'PU';
        $logs->writelog('PU2Excel') ;
        break;
}

$excel->GenerateMeta();
$excel->GenerateTitle();        //CS�ȨѰ��ɡB�i�P��
$excel->GenerateField();        //CS�ȨѰ��ɡB�i�P��
$excel->PutTitleInto();
$excel->PutDataInto();
$excel->PutBgInto();
$filename = $excel->GenerateFileName($prefix);      //CS�ȨѰ��ɡB�i�P��
$excel->OutPutBrower($filename);


exit();
?>
