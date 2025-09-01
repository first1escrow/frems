<?php
require_once dirname(__DIR__) . '/openadodb.php';

$sn  = $_REQUEST['sid'];
$sid = $_REQUEST['stid'];
$act = $_REQUEST['act'];

$return = '';

if ($act == 'y') {
    $act = '2';
} else if ($act == 'n') {
    $act = '1';
}

if ($sn && $sid && $act) {
    $sql = 'UPDATE tScrivenerSales SET sStage="' . $act . '" WHERE sId="' . $sn . '" AND sScrivener="' . $sid . '";';
    if ($conn->Execute($sql)) {
        $return = 'ok';
    }

}

require_once __DIR__ . '/salesScrivenerList.php';
