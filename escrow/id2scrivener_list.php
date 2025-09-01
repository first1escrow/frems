<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../tracelog.php';
include_once "../openadodb.php";

// 取得變數值
$scr  = $_GET['scr']; //地政士
$bank = $_GET['bank']; //銀行
$ver  = $_GET['ver']; //版本
$cat  = $_GET['cat']; //1加盟 2直營
$app  = $_GET['app']; //1土地 2建物
$type = $_GET['type']; //1有查到 2:無法辨別

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_GET), '申請保證號碼查詢');
// echo $scr."-".$bank."-".$ver;
$_POST = escapeStr($_POST);
$_GET  = escapeStr($_GET);

if ($_POST) {
    # code...
    for ($i = 0; $i < count($_POST['certifiedId']); $i++) {
        $sql = "UPDATE tBankCode SET bNo72 ='" . $_POST['no'][$i] . "' WHERE bAccount = '" . $_POST['certifiedId'][$i] . "'";
        // echo $sql."<br>";
        $conn->Execute($sql);
    }
    $tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '申請保證號碼群義流水號');
}

if ($type == 1) {
    if (!empty($bank)) {
        $str .= ' AND bAccount LIKE "' . $bank . '%"';
    }

    if (!empty($ver)) {
        $str .= ' AND bBrand LIKE "' . $ver . '%"';
    }

    //剩餘保證號碼總數
    $sql = '
		SELECT
			bAccount,
			bVersion,
			bCreateDate,
			(SELECT pName FROM tPeopleInfo WHERE pId = bCreatePerson) AS pName,
			(SELECT bName FROM tBrand WHERE bId = bBrand) AS BrandName,
			bBrand,
			bCategory,
			bNo72
		FROM
			tBankCode
		WHERE
			bSID="' . $scr . '"
			AND bDel="n"
			AND bUsed="0"
			AND bApplication="' . $app . '"
			AND bCategory="' . $cat . '"
			' . $str . '
			 ;
	';

    $rs = $conn->Execute($sql);
    $i  = 0;
    while (!$rs->EOF) {

        $list[$i]                = $rs->fields;
        $list[$i]['certifiedId'] = substr($rs->fields['bAccount'], 5, 9);
        if ($i % 2 == 0) {
            $list[$i]['color'] = "#FFFFFF";
        } else {
            $list[$i]['color'] = "#F8ECE9";
        }

        $list[$i]['bCreateDate'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/", '', str_replace(' ', '', $rs->fields['bCreateDate']));
        //一銀
        if (preg_match("/^60001/", $rs->fields['bAccount'])) {
            $list[$i]['bank'] = '一銀桃園';
        } elseif (preg_match("/^99985/", $rs->fields['bAccount'])) { //永豐
            $list[$i]['bank'] = '永豐西門';
        } elseif (preg_match("/^99986/", $rs->fields['bAccount'])) { //永豐
            $list[$i]['bank'] = '永豐城中';
        } elseif (preg_match("/^96988/", $rs->fields['bAccount'])) {
            $list[$i]['bank'] = '台新';
        } elseif (preg_match("/^55006/", $rs->fields['bAccount'])) {
            $list[$i]['bank'] = '一銀城東';
        } else {

        }

        if ($list[$i]['bBrand'] == 1) {
            if ($list[$i]['bCategory'] == 1) { //仲介類型(1加盟2直營3非仲介)
                $list[$i]['BrandName'] .= '加盟';
            } elseif ($list[$i]['bCategory'] == 2) {
                $list[$i]['BrandName'] .= '直營';
            }
        }

        // if ($list[$i]['bCategory'] == 1) { //仲介類型(1加盟2直營3非仲介)
        //     $list[$i]['bCategory'] = '加盟';
        // }elseif ($list[$i]['bCategory'] == 2) {
        //     $list[$i]['bCategory'] = '直營';
        // }elseif ($list[$i]['bCategory'] == 3) {
        //     $list[$i]['bCategory'] = '非仲介成交';
        // }

        $rs->MoveNext();
        $i++;
    }
} else if ($type == 2) {
    //舊版無法辨識版本保證號碼餘額
    $sql = '
		SELECT
			bAccount,
			bVersion,
			bCreateDate,
			(SELECT pName FROM tPeopleInfo WHERE pId = bCreatePerson) AS pName
		FROM
			tBankCode
		WHERE
			bSID="' . $scr . '"
			AND (bBrand="" OR bCategory="" OR bApplication="")
			AND bDel="n"
			AND bUsed="0"
			AND bAccount LIKE "' . $bank . '%"
			;';
    $rs = $conn->Execute($sql);
    $i  = 0;
    while (!$rs->EOF) {
        $list[$i]                = $rs->fields;
        $list[$i]['certifiedId'] = substr($rs->fields['bAccount'], 5, 9);
        if ($i % 2 == 0) {
            $list[$i]['color'] = "#FFFFFF";
        } else {
            $list[$i]['color'] = "#F8ECE9";
        }

        $list[$i]['bCreateDate'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/", '', str_replace(' ', '', $rs->fields['bCreateDate']));
        //一銀
        if (preg_match("/^60001/", $rs->fields['bAccount'])) {
            $list[$i]['bank'] = '一銀桃園';
        } elseif (preg_match("/^99985/", $rs->fields['bAccount'])) { //永豐
            $list[$i]['bank'] = '永豐西門';
        } elseif (preg_match("/^99986/", $rs->fields['bAccount'])) { //永豐
            $list[$i]['bank'] = '永豐城中';
        } elseif (preg_match("/^96988/", $rs->fields['bAccount'])) {
            $list[$i]['bank'] = '台新';
        } elseif (preg_match("/^55006/", $rs->fields['bAccount'])) {
            $list[$i]['bank'] = '一銀城東';
        } else {

        }

        $rs->MoveNext();
        $i++;
    }
}

$smarty->assign('data', $list);

$smarty->display('id2scrivener_list.inc.tpl', '', 'escrow');
