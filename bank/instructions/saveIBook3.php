<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

// $_POST = escapeStr($_POST) ;

if ($_POST['Date'] != '000-00-00' && $_POST['Date'] != '') {
    $tmp = explode('-', $_POST['oDate']);

    $_POST['Date'] = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
}

if ($_POST['oDate'] != '000-00-00' && $_POST['oDate'] != '') {
    $tmp = explode('-', $_POST['oDate']);

    $_POST['oDate'] = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
}

$item = 0;

for ($i = 0; $i < count($_POST['item']); $i++) {
    # code...
    $item = $item + $_POST['item'][$i];
}

//20230131 取得銀行虛擬帳號代碼
$sql = 'SELECT cBankVR FROM tContractBank WHERE cId = ' . $_POST['bank'] . ';';
$rs  = $conn->Execute($sql);
if (!$rs->EOF) {
    $code = $rs->fields['cBankVR'];
} else {
    exit('Cant find VR code!!');
}
##

$_POST['CertifiedId'] = $code . $_POST['CertifiedId'];
$_POST['oBank']       = $_POST['oBank'] . $_POST['oBank2'];
$_POST['cBank']       = $_POST['cBank'] . $_POST['cBank2'];

if ($_POST['type'] == 'add') {
    if ($_POST['Category'] == 8 || $_POST['Category'] == 7 || ($_POST['Category'] == 11 && $_POST['bank'] != 5) || ($_POST['Category'] == 12 && $_POST['bank'] != 5)) {
        $_POST['money'] = $_POST['dMoney'];
    }

    $sql = "INSERT INTO
				tBankTrankBook
			(
				bCertifiedId,
				bBank,
				bCategory,
				bMoney,
				breName,
				breIdentifyId,
				bReBank,
				bItem,
				bODate,
				bOBookId,
				bObank,
				bContractID,
				bEaccountName,
				bEaccount,
				bEmoney,
                bCbank,
				bCaccountName,
				bCaccount,
				bCmoney,
				bOther,
				bSpNote1,
				bSpNote2,
				bCreatTime,
				bCreatName,
				bCreatorId,
				bReBank2
			)VALUES(
				'" . $_POST['CertifiedId'] . "',
				'" . $_POST['bank'] . "',
				'" . $_POST['Category'] . "',
				'" . $_POST['money'] . "',
				'" . $_POST['reName'] . "',
				'" . $_POST['reIdentifyId'] . "',
				'" . $_POST['reBank'] . "',
				'" . $item . "',
				'" . $_POST['oDate'] . "',
				'" . $_POST['oBookId'] . "',
				'" . $_POST['oBank'] . "',
				'" . $_POST['ContractId'] . "',
				'" . $_POST['EaccountName'] . "',
				'" . $_POST['Eaccount'] . "',
				'" . $_POST['Emoney'] . "',
                '" . $_POST['cBank'] . "',
				'" . $_POST['CaccountName'] . "',
				'" . $_POST['Caccount'] . "',
				'" . $_POST['Cmoney'] . "',
				'" . $_POST['Other'] . "',
				'" . $_POST['SpNote1'] . "',
				'" . $_POST['SpNote2'] . "',
				'" . date('Y-m-d H:i:s') . "',
				'" . $_SESSION['member_name'] . "',
				'" . $_SESSION['member_id'] . "',
				'" . $_POST['bReBank2'] . "'
			)";
    if ($conn->Execute($sql)) {
        $lastId = $conn->Insert_ID();
        echo $lastId; //轉頁
    }
} else {
    if ($_POST['Category'] == 8 || $_POST['Category'] == 7 || ($_POST['Category'] == 11 && $_POST['bank'] != 5) || ($_POST['Category'] == 12 && $_POST['bank'] != 5)) {
        $_POST['money'] = $_POST['dMoney'];
    }

    $sql = "UPDATE
			tBankTrankBook
		SET
			bCertifiedId = '" . $_POST['CertifiedId'] . "',
			bBank = '" . $_POST['bank'] . "',
			bCategory = '" . $_POST['Category'] . "',
			bMoney = '" . $_POST['money'] . "',
			breName ='" . $_POST['reName'] . "',
			breIdentifyId ='" . $_POST['reIdentifyId'] . "',
			bModifyName ='" . $_SESSION['member_name'] . "',
			bModifyDate ='" . date('Y-m-d H:i:s') . "',
			bReBank ='" . $_POST['reBank'] . "',
			bReBank2 = '" . $_POST['bReBank2'] . "',
			bItem ='" . $item . "',
			bODate = '" . $_POST['oDate'] . "',
			bOBookId = '" . $_POST['oBookId'] . "',
			bContractID = '" . $_POST['ContractId'] . "',
			bObank = '" . $_POST['oBank'] . "',
			bEaccountName = '" . $_POST['EaccountName'] . "',
			bEaccount = '" . $_POST['Eaccount'] . "',
			bEmoney = '" . $_POST['Emoney'] . "',
            bCbank = '" . $_POST['cBank'] . "',
			bCaccountName = '" . $_POST['CaccountName'] . "',
			bCaccount = '" . $_POST['Caccount'] . "',
			bCmoney = '" . $_POST['Cmoney'] . "',
			bOther = '" . $_POST['Other'] . "',
			bSpNote1 = '" . $_POST['SpNote1'] . "',
			bSpNote2 = '" . $_POST['SpNote2'] . "'
		WHERE
			bId ='" . $_POST['bId'] . "'
		";
    if ($conn->Execute($sql)) {
        echo 'OK';
    }
}

//台新撤銷--大額繳稅 撤銷--開立本行支票
if ($_POST['bank'] == 5 && ($_POST['Category'] == 11 || $_POST['Category'] == 12)) {
    if ($lastId == '') {
        $lastId = $_POST['bId'];
    }

    $totalMoney = 0;
    for ($i = 0; $i < count($_POST['did']); $i++) {
        $totalMoney += $_POST['dMoney'][$i];

        if ($_POST['did'][$i] == '' && ($_POST['dName'][$i] != '' || $_POST['dMoney'][$i] != '')) {
            $sql = "INSERT INTO
						tBankTrankBookDetail (
							bTrankBookId,
							bName,
							bMoney,
							bStop,
							bCreatTime
						)VALUES(
							'" . $lastId . "',
							'" . $_POST['dName'][$i] . "',
							'" . $_POST['dMoney'][$i] . "',
							'" . $_POST['dStop'][$i] . "',
							'" . date('Y-m-d H:i:s') . "'
						)";
            $conn->Execute($sql);
        } else {
            $sql = "UPDATE
						tBankTrankBookDetail
					SET
						bName ='" . $_POST['dName'][$i] . "',
						bMoney ='" . $_POST['dMoney'][$i] . "',
						bStop = '" . $_POST['dStop'][$i] . "'
					WHERE
						bId ='" . $_POST['did'][$i] . "'";
            $conn->Execute($sql);
        }
    }

    if ($_POST['money'] == '') {
        //加總金額
        $sql = "UPDATE tBankTrankBook SET bMoney = '" . $totalMoney . "' WHERE bId ='" . $lastId . "'";
        $conn->Execute($sql);
    }
} else {
    if ($_POST['did'] == '' && ($_POST['dName'] != '' || $_POST['dMoney'] != '')) {
        if ($lastId == '') {
            $lastId = $_POST['bId'];
        }

        $sql = "INSERT INTO
						tBankTrankBookDetail (
							bTrankBookId,
							bTicketNo,
							bTicketDelay,
							bMoney,
							bCreatTime
						)VALUES(
							'" . $lastId . "',
							'" . $_POST['ticketNo'] . "',
							'" . $_POST['TicketDelay'] . "',
							'" . $_POST['dMoney'] . "',
							'" . date('Y-m-d H:i:s') . "'
						)";
        $conn->Execute($sql);
    } else {
        $sql = "UPDATE
						tBankTrankBookDetail
					SET
						bTicketNo = '" . $_POST['ticketNo'] . "',
						bName ='" . $_POST['dName'] . "',
						bMoney ='" . $_POST['dMoney'] . "',
						bTicketDelay = '" . $_POST['TicketDelay'] . "'
					WHERE
						bId ='" . $_POST['did'] . "'";
        $conn->Execute($sql);
    }
}

if (is_array($_POST['NEaccountName'])) { //錯誤帳戶新增
    if ($lastId == '') {
        $lastId = $_POST['bId'];
    }

    for ($i = 0; $i < count($_POST['NEaccountName']); $i++) {
        if ($_POST['NEaccountName'][$i] != '') {
            $sql = "INSERT INTO
					tBankTrankBookDetail
				(
					bTrankBookId,
					bCat,
					bEaccountName,
					bEaccount,
					bEmoney,
					bCreatTime
				)  VALUES(
					'" . $lastId . "',
					1,
					'" . $_POST['NEaccountName'][$i] . "',
					'" . $_POST['NEaccount'][$i] . "',
					'" . $_POST['NEmoney'][$i] . "',
					'" . date("Y-m-d H:i:s") . "'
				) ";
            $conn->Execute($sql);
        }
    }
}

if (is_array($_POST['MEaccountName'])) {
    for ($i = 0; $i < count($_POST['MEaccountName']); $i++) {
        $sql = "UPDATE
				tBankTrankBookDetail
			SET
				bEaccountName = '" . $_POST['MEaccountName'][$i] . "',
				bEaccount  = '" . $_POST['MEaccount'][$i] . "',
				bEmoney  = '" . $_POST['MEmoney'][$i] . "'
			WHERE
				bId = '" . $_POST['eId'][$i] . "'
			";
        $conn->Execute($sql);
    }
}

if (is_array($_POST['NCaccountName'])) { //正確帳戶新增
    if ($lastId == '') {
        $lastId = $_POST['bId'];
    }

    for ($i = 0; $i < count($_POST['NCaccountName']); $i++) {
        if ($_POST['NCaccountName'][$i] != '') {
            $sql = "INSERT INTO
					tBankTrankBookDetail
				(
					bTrankBookId,
					bCat,
					bEaccountName,
					bEaccount,
					bEmoney,
					bCreatTime
				)  VALUES(
					'" . $lastId . "',
					2,
					'" . $_POST['NCaccountName'][$i] . "',
					'" . $_POST['NCaccount'][$i] . "',
					'" . $_POST['NCmoney'][$i] . "',
					'" . date("Y-m-d H:i:s") . "'
				) ";
            $conn->Execute($sql);
        }
    }
}

if (is_array($_POST['MCaccountName'])) {
    for ($i = 0; $i < count($_POST['MCaccountName']); $i++) {
        $sql = "UPDATE
				tBankTrankBookDetail
			SET
				bEaccountName = '" . $_POST['MCaccountName'][$i] . "',
				bEaccount  = '" . $_POST['MCaccount'][$i] . "',
				bEmoney  = '" . $_POST['MCmoney'][$i] . "'
			WHERE
				bId = '" . $_POST['cId'][$i] . "'
			";
        $conn->Execute($sql);
    }
}
