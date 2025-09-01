<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/advance.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

$advance         = new Advance();
$menu_bankbranch = $advance->GetBankBranchList($_POST['bankcode']);
echo json_encode($menu_bankbranch);
