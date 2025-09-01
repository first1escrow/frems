<?php
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_GET     = escapeStr($_GET);
$category = ($_POST['cat']) ? $_POST['cat'] : $_GET['cat'];
$id       = ($_POST['id']) ? $_POST['id'] : $_GET['id'];

$str = '';
foreach ($_POST as $k => $v) {
    if (preg_match("/authority_/", $k)) {
        $authArray[$k] = $v;
    }
}

$fax = $_POST['pFaxNumArea'] . '-' . $_POST['pFaxNum'];
$use = $_POST['use'];

$_POST['pOnBoard'] = ($_POST['pOnBoard'] != '000-00-00') ? (substr($_POST['pOnBoard'], 0, 3) + 1911) . substr($_POST['pOnBoard'], 3) : '0000-00-00';

if ($use == 1) {
    if ($category == 'add') {
        $str .= 'pTest = "' . $_POST['city'][0] .' ",';
    } else if ($category == 'mod') {
        if ($_POST['pDep'] == 7) {
            $sql = "UPDATE tPeopleInfo SET pTest = '" . $_POST['city'][0] . "' WHERE pId = '" . $id . "'";
            $conn->Execute($sql);
        }
    }
} else {
    $str .= 'pTest = "0",';
}

//20231012 業務單位且有指定日曆顏色
$_POST['pCalenderClass'] = (preg_match("/^\#\w{6}$/i", $_POST['pCalenderClass']) && ($_POST['pDep'] == 7)) ? strtoupper($_POST['pCalenderClass']) : '';

if ($category == 'mod') {
    if ($_POST['chageVal'] == 1) {
        $str .= "pAuthority = '" . json_encode($authArray) . "',";
    }

    $sql = "UPDATE
				tPeopleInfo
			SET
				pName = '" . $_POST['pName'] . "',
				pGender = '" . $_POST['pGender'] . "',
				pAccount = '" . $_POST['pAccount'] . "',
				pJob = '" . $_POST['pJob'] . "',
				pPassword = '" . $_POST['pwd'] . "',
				pDep = '" . $_POST['pDep'] . "',
				pExt = '" . $_POST['pExt'] . "',
				pOnBoard = '" . $_POST['pOnBoard'] . "',
                pCalenderClass = '" . $_POST['pCalenderClass'] . "',
				" . $str . "
				pFaxNum = '" . $fax . "',
				pBankTrans = '" . $authArray['authority_36'] . "'
			WHERE
				pId = '" . $id . "'";
    $conn->Execute($sql);
} else {
    if ($_POST['chageVal'] == 1) {
        $str = "pAuthority = '" . json_encode($authArray) . "'";
    }

    $sql = "INSERT
				tPeopleInfo
			SET
				pName = '" . $_POST['pName'] . "',
				pGender = '" . $_POST['pGender'] . "',
				pAccount = '" . $_POST['pAccount'] . "',
				pJob = '" . $_POST['pJob'] . "',
				pPassword = '" . $_POST['pwd'] . "',
				pOnBoard = '" . $_POST['pOnBoard'] . "',
                pCalenderClass = '" . $_POST['pCalenderClass'] . "',
				" . $str . "
				pDep = '" . $_POST['pDep'] . "',
				pExt = '" . $_POST['pExt'] . "',
				pFaxNum = '" . $fax . "',
				pBankTrans = '" . $authArray['authority_36'] . "'
			";
    $conn->Execute($sql);
}