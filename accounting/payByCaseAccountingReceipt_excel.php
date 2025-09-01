<?php
require_once dirname(__DIR__) . '/openadodb.php' ;
require_once dirname(__DIR__) . '/session_check.php' ;

$_POST = escapeStr($_POST) ;

if ($_POST['allForm']) {
    $qstr = "sId IN(".@implode(',', $_POST['allForm']).")";
}

$exportTime = date('YmdHis');

$sql = "UPDATE 
            tFeedBackMoneyPayByCase 
        SET 
            fCaseCloseTime = '".date('Y-m-d')."',
            fExportTime = '".$exportTime."' 
        WHERE 
            ".$qstr;

$conn->Execute($sql);


