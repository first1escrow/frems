<?php
require_once dirname(dirname(__DIR__)).'/configs/config.class.php';
require_once dirname(dirname(__DIR__)).'/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)).'/openadodb.php';
require_once dirname(dirname(__DIR__)).'/session_check.php';
require_once dirname(dirname(__DIR__)).'/tracelog.php';

$tmp = explode('-', $_POST['Date']);

if ($_POST['Date'] != '000-00-00') {
    $_POST['Date'] = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
}

if ($_POST['oDate'] != '000-00-00' && $_POST['oDate'] != '') {
    $tmp            = explode('-', $_POST['oDate']);
    $_POST['oDate'] = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
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
} else {
    if (is_array($_POST['did'])) {
        for ($i = 0; $i < count($_POST['did']); $i++) {
            if ($_POST['did'][$i] == '' && ($_POST['dName'][$i] != '' || $_POST['dMoney'][$i] != '')) {
                $sql = "INSERT INTO
							tBankTrankBookDetail (
								bTrankBookId,
								bName,
								bMoney,
								bStop,
								bCreatTime
							)VALUES(
								'" . $_POST['bId'] . "',
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
    } else {
        if ($_POST['did'] == '' && ($_POST['dName'] != '' || $_POST['dMoney'] != '')) {
            $sql = "INSERT INTO
                        tBankTrankBookDetail (
                            bTrankBookId,
                            bTicketNo,
                            bMoney,
                            bTicketDelay,
                            bCreatTime
                        )VALUES(
                            '" . $_POST['bId'] . "',
                            '" . $_POST['ticketNo'] . "',
                            '" . $_POST['dMoney'] . "',
                            '" . $_POST['TicketDelay'] . "',
                            '" . date('Y-m-d H:i:s') . "'
                        )";
            $conn->Execute($sql);
        } else {
            if (is_array($_POST['dMoney'])) { //大額
                for ($i = 0; $i < count($_POST['did']); $i++) {
                    $sql = "UPDATE
                            tBankTrankBookDetail
                        SET
                            bName ='" . $_POST['dName'][$i] . "',
                            bMoney ='" . $_POST['dMoney'][$i] . "'
                        WHERE
                            bId ='" . $_POST['did'][$i] . "'";
                    $conn->Execute($sql);
                }
            } else { //票據
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
    }
}

if ($_POST['BookId'] != '' && $_POST['Date'] != '000-00-00') {
    $str = "bStatus = '1',";
    $str .= "bModifyName2 = '" . $_SESSION['member_name'] . "',
			bModifyDate2 = '" . date('Y-m-d H:i:s') . "',";
}

if ($_POST['Category'] == 8 || $_POST['Category'] == 7 || ($_POST['Category'] == 11 && $_POST['bank'] != 5) || ($_POST['Category'] == 12 && $_POST['bank'] != 5)) {
    $_POST['money'] = $_POST['dMoney'];
}

if ($_POST['CertifiedId']) {
    //20230206 取得銀行虛擬帳號代碼
    $sql = 'SELECT cBankVR FROM tContractBank WHERE cId = ' . $_POST['bank'] . ';';
    $rs  = $conn->Execute($sql);
    if (!$rs->EOF) {
        $code = $rs->fields['cBankVR'];
        $code = substr($code, 0, 5);
    } else {
        exit('Cant find VR code!!');
    }

    // if ($_POST['bank'] == 1) {
    //     $code = '60001';
    // }elseif ($_POST['bank'] == 4) {
    //     $code = '99985';
    // }elseif ($_POST['bank'] == 6) {
    //     $code = '99986';
    // }elseif ($_POST['bank'] == 5) {
    //     $code = '96988';
    // }

    $str .= "bCertifiedId = '" . $code . $_POST['CertifiedId'] . "',";
}

$sql = "UPDATE
			tBankTrankBook
		SET
			bDate ='" . $_POST['Date'] . "',
			bMoney ='" . $_POST['money'] . "',
			bBookId='" . $_POST['BookId'] . "',
			bCount = '" . $_POST['count'] . "',
			bSpecificCount = '" . $_POST['specificCount'] . "',
			" . $str . "
			bModifyName = '" . $_SESSION['member_name'] . "',
			bModifyDate = '" . date('Y-m-d H:i:s') . "',
			breName ='" . $_POST['reName'] . "',
			breIdentifyId ='" . $_POST['reIdentifyId'] . "',
			bToCertifiedId = '" . $_POST['ToCertified'] . "',
			ToCertifiedFirst = '" . $_POST['ToCertifiedFirst'] . "',
			bReBank ='" . $_POST['reBank'] . "',
			bReBank2 = '" . $_POST['bReBank2'] . "',
			bItem ='" . $item . "',
			bODate = '" . $_POST['oDate'] . "',
			bOBookId = '" . $_POST['oBookId'] . "',
			bObank = '" . $_POST['oBank'] . $_POST['oBank2'] . "',
			bContractID = '" . $_POST['ContractId'] . "',
			bEaccountName = '" . $_POST['EaccountName'] . "',
			bEaccount = '" . $_POST['Eaccount'] . "',
			bEmoney = '" . $_POST['Emoney'] . "',
			bCbank = '" . $_POST['cBank'] . $_POST['cBank2'] . "',
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