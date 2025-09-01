<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/class/contract.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/class/income.class.php';
require_once dirname(__DIR__) . '/session_check.php';

$advance   = new Advance();
$contract  = new Contract();
$scrivener = new Scrivener();
$income    = new Income();

$list_categorybank_twhg = $contract->GetCategoryBank(array(8, 61));
$menu_categorybank_twhg = $contract->ConvertOption($list_categorybank_twhg, 'cId', 'cBankName');

$smarty->assign('menu_categorybank_twhg', $menu_categorybank_twhg);
$smarty->display('formbrand.inc.tpl', '', 'maintain');
