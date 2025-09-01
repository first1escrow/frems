<?php
require_once dirname(__DIR__) . '/session_check.php';

$cIds = explode('_', $_POST['cIds']);
$fTargetIds = explode('_', $_POST['fTargetIds']);

require_once dirname(__DIR__) . '/includes/sales/payByCasePDFReceiptAll.php';
