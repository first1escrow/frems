<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$id  = $_POST['id'];
$cat = $_POST['cat'];

if ($cat == '') {
    if (preg_match("/99985/", $id)) {
        $sql = "SELECT * FROM tContractBank WHERE cId = 4";
        $rs  = $conn->Execute($sql);

        $data['Bank']       = '';
        $data['BankBranch'] = '';
        $data['Acc']        = $rs->fields['cBankTrustAccount'];
        $data['AccName']    = $rs->fields['cTrustAccountName'];
    } else if (preg_match("/99986/", $id)) {
        $sql = "SELECT * FROM tContractBank WHERE cId = 6";
        $rs  = $conn->Execute($sql);

        $data['Bank']       = '';
        $data['BankBranch'] = '';
        $data['Acc']        = $rs->fields['cBankTrustAccount'];
        $data['AccName']    = $rs->fields['cTrustAccountName'];
    } else if (preg_match("/60001/", $id)) {
        $sql = "SELECT * FROM tContractBank WHERE cId = 1";
        $rs  = $conn->Execute($sql);

        $data['Bank']       = $rs->fields['cBankMain'];
        $data['BankBranch'] = $rs->fields['cBankBranch'];
        $data['Acc']        = '00000000000000';
        $data['AccName']    = '　';
    } else if (preg_match("/55006/", $id)) {
        $sql = "SELECT * FROM tContractBank WHERE cId = 7";
        $rs  = $conn->Execute($sql);

        $data['Bank']       = $rs->fields['cBankMain'];
        $data['BankBranch'] = $rs->fields['cBankBranch'];
        $data['Acc']        = '00000000000000';
        $data['AccName']    = '　';
    } else if (preg_match("/96988/", $id)) {
        $sql = "SELECT * FROM tContractBank WHERE cId = 5";
        $rs  = $conn->Execute($sql);

        $data['Bank']       = $rs->fields['cBankMain3'];
        $data['BankBranch'] = $rs->fields['cBankBranch3'];
        $data['Acc']        = $rs->fields['cBankAccount3'];
        $data['AccName']    = $rs->fields['cAccountName3'];
    }
} else if (in_array($cat, [1, 3])) { //代墊申請(要帶代書的)
    $sql = "SELECT
				s.sAccountNum1,
				s.sAccountNum2,
				s.sAccount3,
				s.sAccount4
			FROM
				tContractScrivener AS cs
			LEFT JOIN
				tScrivener AS s ON s.sId = cs.cScrivener
			WHERE
				cs.cCertifiedId = '" . substr($id, -9) . "'";
    $rs = $conn->Execute($sql);

    if (preg_match("/96988/", $id)) {
        $data['Bank']       = $rs->fields['sAccountNum1'];
        $data['BankBranch'] = $rs->fields['sAccountNum2'];
        $data['Acc']        = $rs->fields['sAccount3'];
        $data['AccName']    = $rs->fields['sAccount4'];
    }
} else if (in_array($cat, [2, 4])) { //代墊返還
    $sql = "SELECT * FROM tContractBank WHERE cId = 5";
    $rs  = $conn->Execute($sql);

    if (preg_match("/96988/", $id)) {
        $data['Bank']       = $rs->fields['cBankMain'];
        $data['BankBranch'] = $rs->fields['cBankBranch'];
        $data['Acc']        = "98828" . substr($id, -9);
        $data['AccName']    = $rs->fields['cAccountName2'];
    }
}

exit(json_encode($data));
