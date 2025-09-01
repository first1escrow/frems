<?php
header("Content-Type:text/html; charset=utf-8");

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once __DIR__ . '/finalPaymentNoneSellerListFunction.php';

$bank_option = [
    '60001' => '第一銀行桃園分行',
    '55006' => '第一銀行城東分行',
];
$bank_selected = '60001';

$ts = false;
if (!empty($_GET['bank']) && ($_GET['bank'] == 'taishin')) {
    if ($_SESSION['pTaishinReport'] != 1) {
        exit('Invalid Access!!(R)');
    }

    $bank_option = [
        '96988' => '台新銀行建北分行',
    ];
    $bank_selected = '96988';

    $ts = true; //卻認為台新登入
}

if (empty($ts) && ($_SESSION['pSellerNoteReport'] != 1)) { //非台新進入須確認是否有授權
    exit('Invalid Access!!(R)');
}

$_POST = escapeStr($_POST);

if ($_POST['year']) {
    $bankBranch = '';

    if ($_POST['bankBranch'] == '60001') { //一銀桃園
        $bankBranch = '60001';
    }

    if ($_POST['bankBranch'] == '55006') { //一銀城東
        $bankBranch = '55006';
    }

    if ($_POST['bankBranch'] == '96988') { //台新建北
        $bankBranch = '96988';
    }

    if (empty($bankBranch)) {
        exit('Invalid Access!!(B)');
    }

    $sDate = $_POST['year'] . "-" . $_POST['month'] . "-01";
    $eDate = $_POST['year'] . "-" . $_POST['month'] . "-31";

    //賣方備註選單
    // $Item = array(1 => '賣方匯第三人',2 => '多數賣方指定匯其中一人或數人',3 =>'代理人受領',4 =>'代理人指定匯第三人帳戶',5=>'其他:' );////1.賣方親自點交,指定滙給第三人 2.賣方等親自點交,同意全部滙給其中一人 3.有授權書(代理人可收受價金) 4.有授權書(代理人可指定滙給第三人) 5.其他:
    $sql = "SELECT * FROM tCategorySellerNote  ORDER BY cOrder ASC ";
    $rs  = $conn->Execute($sql);
    while (!$rs->EOF) {
        $Item[$rs->fields['cId']] = $rs->fields['cName'];
        $rs->MoveNext();
    }
    ##

    //取得結案號碼
    // $sql = "SELECT tMemo,tBankLoansDate,tAccountName FROM tBankTrans WHERE tBankLoansDate BETWEEN '" . $sDate . "' AND '" . $eDate . "' AND tPayOk = 1 AND tBank_kind = '一銀' AND tObjKind IN ('解除契約','點交(結案)','建經發函終止') AND tKind ='賣方'";
    $sql = 'SELECT
                tMemo,
                tBankLoansDate,
                tAccountName
            FROM
                tBankTrans
            WHERE
                tBankLoansDate BETWEEN "' . $sDate . '" AND "' . $eDate . '"
                AND tPayOk = 1
                AND tVR_Code LIKE "' . $bankBranch . '%"
                AND tObjKind IN ("解除契約","點交(結案)","建經發函終止")
                AND tKind = "賣方";';
    $rs = $conn->Execute($sql);

    $list_check     = array();
    $checkOwnerNote = true; //確認是否符合顯示賣方備註 false:要顯示填寫

    while (!$rs->EOF) {
        $data = array();
        $data = $rs->fields;

        if (empty($list_check[$data['tMemo']])) {
            $list_check[$data['tMemo']] = array();
        }

        if (empty($list_check[$data['tMemo']]['tAccountName'])) {
            $list_check[$data['tMemo']]['tAccountName'] = array();
        }

        $list_check[$data['tMemo']]['tMemo']          = $data['tMemo'];
        $list_check[$data['tMemo']]['tBankLoansDate'] = $data['tBankLoansDate'];
        $list_check[$data['tMemo']]['tAccountName'][] = $data['tAccountName'];

        $owner = $ownerArr = $data;
        unset($owner, $ownerArr, $data);

        $rs->MoveNext();
    }

    $list = array();
    foreach ($list_check as $key => $val) {
        $check = true;

        //賣方備註(沒賣方不用填寫;有非賣方帳戶要填寫;//結案有出賣方帳戶但其中有賣方未收錢(EX:賣1、賣2、賣3;出款只出給了賣1、賣2))
        $owner                     = getOwner($val['tMemo'], 'cName');
        $ownerArr                  = explode('_', $owner);
        $list_check[$key]['owner'] = $ownerArr;

        //帳戶名跟賣方姓名對不起來
        foreach ($val['tAccountName'] as $acc_name) {
            if (!in_array($acc_name, $ownerArr)) {
                $list_check[$key]['check'] = 0;
                $check                     = false;
            }
        }

        //結案有出賣方帳戶但其中有賣方未收錢(EX:賣1、賣2、賣3;出款只出給了賣1、賣2)
        foreach ($ownerArr as $name) {
            if (!in_array($name, $val['tAccountName'])) {
                $list_check[$key]['check'] = 0;
                $check                     = false;
            }
        }

        if (!$check) {
            array_push($list, $list_check[$key]);
        }

        $owner = $ownerArr = null;
        unset($owner, $ownerArr);
    }

    foreach ($list as $key => $val) {
        $sql = "SELECT * FROM tBankTransSellerNote WHERE tCertifiedId = '" . $val['tMemo'] . "'";
        $rs  = $conn->Execute($sql);

        $list[$key]['tAnother']     = $rs->fields['tAnother'];
        $list[$key]['tAnotherNote'] = $rs->fields['tAnotherNote'];
        $list[$key]['relation1'] = $rs->fields['relation1'];
        $list[$key]['relation3'] = $rs->fields['relation3'];
        $list[$key]['relation4'] = $rs->fields['relation4'];
    }

    require_once __DIR__ . '/finalPaymentNoneSellerListExcel.php';

    exit;
}

$smarty->assign('list', $list);
$smarty->assign('bank_option', $bank_option);
$smarty->assign('bank_selected', $bank_selected);

$smarty->display('finalPaymentNoneSellerList.inc.tpl', '', 'report2');
