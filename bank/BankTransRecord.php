<?php
error_reporting(E_ALL & ~E_WARNING);

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';

$_POST = escapeStr($_POST);
##

if ($_POST) {
    //帳號查詢
    $account = [];
    $sId     = 142; //目前只有這個代書需要
    $sql     = "SELECT sAccount3 ,sAccount31,sAccount32,sAccount4,sAccount41,sAccount42 FROM tScrivener WHERE sId = '" . $sId . "'";
    $rs      = $conn->Execute($sql);

    if (! empty($rs->fields['sAccount3']) && ! empty($rs->fields['sAccount4'])) {
        $tmp                = [];
        $tmp['account']     = $rs->fields['sAccount3'];
        $tmp['accountName'] = $rs->fields['sAccount4'];
        array_push($account, $tmp);

        unset($tmp);
    }

    if (! empty($rs->fields['sAccount31']) && ! empty($rs->fields['sAccount41'])) {
        $tmp                = [];
        $tmp['account']     = $rs->fields['sAccount31'];
        $tmp['accountName'] = $rs->fields['sAccount41'];
        array_push($account, $tmp);

        unset($tmp);
    }

    if (! empty($rs->fields['sAccount32']) && ! empty($rs->fields['sAccount42'])) {
        $tmp                = [];
        $tmp['account']     = $rs->fields['sAccount32'];
        $tmp['accountName'] = $rs->fields['sAccount42'];
        array_push($account, $tmp);

        unset($tmp);
    }

    $sql = "SELECT sBankAccountNo AS account,sBankAccountName AS accountName FROM tScrivenerBank WHERE sScrivener = '" . $sId . "'";
    $rs  = $conn->Execute($sql);
    while (! $rs->EOF) {
        array_push($account, $rs->fields);

        $rs->MoveNext();
    }

    $str = [];
    foreach ($account as $val) {

        array_push($str, " (tAccount = '" . $val['account'] . "' AND tAccountName = '" . $val['accountName'] . "') ");
    }

    //日期
    if ($_POST['date']) {
        $str[] = " tBankLoansDate = '" . (substr($_POST['date'], 0, 3) + 1911) . "-" . substr($_POST['date'], 4, 2) . "-" . substr($_POST['date'], 7, 2) . "'";
    }
    //金額
    if ($_POST['money']) {
        $str[] = "tMoney = '" . $_POST['money'] . "'";
    }
    ##查詢出款
    $list = [];
    $sql  = "SELECT
				tBankLoansDate,
				(SELECT (SELECT sName FROM tScrivener WHERE sId=bSID) FROM tBankCode WHERE bAccount=tVR_Code) AS scrivnerName,
				tMemo,
				tBank_kind,
				tObjKind,
				tMoney,
				tTxt
			FROM
				tBankTrans
			WHERE
				" . join(' AND ', $str) . "
				AND
					tPayOk = 1
			ORDER BY tBankLoansDate DESC"
    ;
    $rs = $conn->Execute($sql);
    $i  = 0;
    while (! $rs->EOF) {
        $tmp          = $rs->fields;
        $tmp['color'] = ($i % 2 == 0) ? '#F8ECE9' : '#FFF';
        array_push($list, $tmp);

        $i++;
        $rs->MoveNext();
    }
    ##

}

##
$smarty->assign('list', $list);
$smarty->assign('menuScrivener', $menuScrivener);
$smarty->display('BankTransRecord.inc.tpl', '', 'bank');
