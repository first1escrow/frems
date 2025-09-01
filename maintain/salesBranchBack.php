<?php
require_once dirname(__DIR__) . '/openadodb.php';

$bid  = $_REQUEST['bid'];
$brid = $_REQUEST['brid'];
$act  = $_REQUEST['act'];

$return = '';

if ($act == 'y') {
    $act = '2';
} else if ($act == 'n') {
    $act = '1';
}

if ($bid && $brid && $act) {
    $sql = 'UPDATE tBranchSales SET bStage="' . $act . '" WHERE bId="' . $bid . '" AND bBranch="' . $brid . '";';
    if ($conn->Execute($sql)) {
        $return = 'ok';
    }

}

require_once __DIR__ . '/salesBranchList.php';
