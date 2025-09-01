<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$brand                  = new Brand();
$list_categorybank_twhg = $brand->GetCategoryBank(array(8, 77));
$menu_categorybank_twhg = $brand->ConvertOption($list_categorybank_twhg, 'cId', 'cBankName');
$menu_scrivener         = array('1' => '有');

$data['bBank']      = explode(",", $data['bBank']);
$data['bScrivener'] = explode(",", $data['bScrivener']);

//$subject = $sms->GetSmsSubject($sms->mKindBranch);
//$list = $sms->GetBranchList($_POST["id"]);
//$data_sms = $sms->CombineSmsList($subject, $list, SMS::CATEGORY_NUM_BRANCH);

$smarty->assign('country', listCity($conn)); //縣市
$smarty->assign('area', listArea($conn)); //區域
$smarty->assign('is_edit', 0);
$smarty->assign('menu_categorybank_twhg', $menu_categorybank_twhg);
$smarty->assign('menu_scrivener', $menu_scrivener);
//$smarty->assign('data_sms', $data_sms);
$smarty->display('formbrand.inc.tpl', '', 'maintain');
