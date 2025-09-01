<?php
include_once dirname(__DIR__) . '/openadodb.php' ;
include_once dirname(__DIR__) . '/session_check.php' ;

$_POST = escapeStr($_POST) ;

$sql = "
        UPDATE 
            tFeedBackMoneyPayByCase 
        SET 
            fCaseCloseTime = '0000-00-00', fExportTime = null
        WHERE 
            fId = '".$_POST['id']."'";

if ($conn->Execute($sql)) {
    echo 1;
} else {
    echo 0;
}

?>
