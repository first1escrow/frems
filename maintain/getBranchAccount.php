<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST = escapeStr($_POST);

$sql = "SELECT
			bAccountNum1,
			bAccountNum2,
			bAccount3,
			bAccount4,
			bAccountUnused,
			bAccountNum11,
			bAccountNum21,
			bAccount31,
			bAccount41,
			bAccountUnused1,
			bAccountUnused2,
			bAccount32,
			bAccountNum12,
			bAccountNum22,
			bAccount32,
			bAccount42,
			bAccountNum13,
			bAccountNum23,
			bAccount33,
			bAccount43,
			bAccountUnused3
		FROM
			tBranch
		WHERE
			bId = '" . $_POST['id'] . "'";

$rs = $conn->Execute($sql);
$i  = 0;
if (!$rs->EOF) {
    if ($rs->fields['bAccountUnused'] == 0) {
        $bank[$i]['bank']       = $rs->fields['bAccountNum1'];
        $bank[$i]['bankBranch'] = $rs->fields['bAccountNum2'];
        $bank[$i]['Account3']   = $rs->fields['bAccount3'];
        $bank[$i]['Account4']   = $rs->fields['bAccount4'];
        // echo 'A';
        $i++;
    }

    if ($rs->fields['bAccountUnused1'] == 0 && $rs->fields['bAccount31'] != '') {
        $bank[$i]['bank']       = $rs->fields['bAccountNum11'];
        $bank[$i]['bankBranch'] = $rs->fields['bAccountNum21'];
        $bank[$i]['Account3']   = $rs->fields['bAccount31'];
        $bank[$i]['Account4']   = $rs->fields['bAccount41'];

        // echo 'B';

        $i++;
    }

    if ($rs->fields['bAccountUnused2'] == 0 && $rs->fields['bAccount32'] != '') {
        $bank[$i]['bank']       = $rs->fields['bAccountNum12'];
        $bank[$i]['bankBranch'] = $rs->fields['bAccountNum22'];
        $bank[$i]['Account3']   = $rs->fields['bAccount32'];
        $bank[$i]['Account4']   = $rs->fields['bAccount42'];

        // echo 'C';
        $i++;
    }

    if ($rs->fields['bAccountUnused3'] == 0 && $rs->fields['bAccount33'] != '') {
        $bank[$i]['bank']       = $rs->fields['bAccountNum13'];
        $bank[$i]['bankBranch'] = $rs->fields['bAccountNum23'];
        $bank[$i]['Account3']   = $rs->fields['bAccount33'];
        $bank[$i]['Account4']   = $rs->fields['bAccount43'];

        // echo 'D';
        $i++;
    }

    ##銀行
    // $dataBank = array();
    $sql = "SELECT * FROM tBranchBank WHERE bBranch ='" . $_POST['id'] . "' AND bUnUsed = 0";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {

        $bank[$i]['bank']       = $rs->fields['bBankMain'];
        $bank[$i]['bankBranch'] = $rs->fields['bBankBranch'];
        $bank[$i]['Account3']   = $rs->fields['bBankAccountNo'];
        $bank[$i]['Account4']   = $rs->fields['bBankAccountName'];
        // echo 'E';
        $i++;
        $rs->MoveNext();
    }

    // print_r($bank);

}

// krsort($bank);//最新一筆

// print_r($bank);

echo json_encode($bank[(count($bank) - 1)]);
