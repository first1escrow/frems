<?php
require_once dirname(__DIR__) . '/configs/contract.setting.php';
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/contractBank.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/tracelog.php';

$log = new TraceLog();
$log->log($_SESSION['member_id'], print_r($_GET, true), '查看地政士合約申請', 'select');

$id = empty($_GET['id']) ? null : $_GET['id'];
if (empty($id)) {
    http_response_code(400);
    exit('No ID provided');
}

$conn = new first1DB();

$sql = 'SELECT
            a.aId,
            a.aScrivenerId,
            CONCAT("SC", LPAD(a.aScrivenerId, 4, "0")) AS scrivenereId,
            a.aFrom,
            a.aBrand,
            a.aCategory,
            a.aApplication,
            a.aEscrowBank,
            a.aQuantity,
            a.aApplyDateTime,
            a.aProcessed,
            b.sName AS scrivenerName,
            b.sOffice AS scrivenerOffice,
            c.cBankName AS escrowBankName,
            c.cBranchName AS escrowBankBranch,
            c.cBankFullName AS escrowBankFullName,
            c.cBranchFullName AS escrowBankBranchFullName
        FROM
            tApplyBankCode AS a
        JOIN
            tScrivener AS b ON a.aScrivenerId = b.sId
        JOIN
            tContractBank AS c ON a.aEscrowBank = c.cBankVR
        WHERE
            aId = :id;';
$data = $conn->one($sql, ['id' => $id]);

if (! empty($data)) {
    $data['brand']       = in_array($data['aBrand'], array_keys($contractSetting['brand'])) ? $contractSetting['brand'][$data['aBrand']] : '未知';
    $data['category']    = in_array($data['aCategory'], array_keys($contractSetting['category'])) ? $contractSetting['category'][$data['aCategory']] : '未知';
    $data['application'] = in_array($data['aApplication'], array_keys($contractSetting['appilcation'])) ? $contractSetting['appilcation'][$data['aApplication']] : '未知';
    $data['process']     = in_array($data['aProcessed'], array_keys($contractSetting['process'])) ? $contractSetting['process'][$data['aProcessed']] : '未知';

    $_SESSION['key'] = mt_rand(1, 1000);

    $Lnum = ($data['aApplication'] == '1') ? $data['aQuantity'] : 0;
    $Bnum = ($data['aApplication'] == '2') ? $data['aQuantity'] : 0;

    $banks    = new contractBank();
    $bankList = $banks->getContractBanks();

    $bank = null;
    foreach ($bankList as $item) {
        if ($item['cBankVR'] == $data['aEscrowBank']) {
            $bank = [
                'cId'             => $item['cId'],
                'cBankVR'         => $item['cBankVR'],
                'cBankMainName'   => $item['cBankFullName'],
                'cBankBranchName' => $item['cBranchFullName'],
            ];
            break;
        }
    }

    $data['apply'] = [
        'Lnum'   => $Lnum,
        'Bnum'   => $Bnum,
        'Snum'   => 0,
        'bank'   => empty($bank['cId']) ? '' : $bank['cId'],
        'man'    => $data['aScrivenerId'],
        'bBrand' => $data['aBrand'],
        'ver'    => 'A',
        'key'    => $_SESSION['key'],
        'save'   => 'ok',
    ];
}

$smarty->assign('data', $data);

$smarty->display('editBankCodeRecord.inc.tpl', '', 'bank');
