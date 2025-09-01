<?php
require_once dirname(dirname(__DIR__)).'/class/brand.class.php';
require_once dirname(dirname(__DIR__)).'/session_check.php';
require_once dirname(dirname(__DIR__)).'/tracelog.php';

$tlog = new TraceLog();
$tlog->insertWrite($_SESSION['member_id'], json_encode($_POST), '新增仲介品牌與明細');

$contract          = new Brand();
$_POST['signDate'] = (substr($_POST['signDate'], 0, 3) + 1911) . substr($_POST['signDate'], 3);

if (empty($contract->CheckBrandExist($_POST['code']))) {
    $contract->AddBrand($_POST);
    exit('儲存完成');
} else {
    exit('品牌代碼已存在');
}