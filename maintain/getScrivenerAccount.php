<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST = escapeStr($_POST);

$sql = "SELECT
			sAccountNum1,
			sAccountNum2,
			sAccount3,
			sAccount4,
			sAccountUnused,
			sAccountNum11,
			sAccountNum21,
			sAccount31,
			sAccount41,
			sAccountUnused1,
			sAccountUnused2,
			sAccount32,
			sAccountNum12,
			sAccountNum22,
			sAccount32,
			sAccount42
		FROM
			tScrivener
		WHERE
			sId = '" . $_POST['id'] . "'";
// echo $sql;
$rs = $conn->Execute($sql);
$i  = 0;
if (!$rs->EOF) {
    if ($rs->fields['sAccountUnused'] == 0) {
        $bank[$i]['bank']       = $rs->fields['sAccountNum1'];
        $bank[$i]['bankBranch'] = $rs->fields['sAccountNum2'];
        $bank[$i]['Account3']   = $rs->fields['sAccount3'];
        $bank[$i]['Account4']   = $rs->fields['sAccount4'];
        // echo 'A';
        $i++;
    }

    if ($rs->fields['sAccountUnused1'] == 0 && $rs->fields['sAccount31'] != '') {
        $bank[$i]['bank']       = $rs->fields['sAccountNum11'];
        $bank[$i]['bankBranch'] = $rs->fields['sAccountNum21'];
        $bank[$i]['Account3']   = $rs->fields['sAccount31'];
        $bank[$i]['Account4']   = $rs->fields['sAccount41'];

        // echo 'B';

        $i++;
    }

    if ($rs->fields['sAccountUnused2'] == 0 && $rs->fields['sAccount32'] != '') {
        $bank[$i]['bank']       = $rs->fields['sAccountNum12'];
        $bank[$i]['bankBranch'] = $rs->fields['sAccountNum22'];
        $bank[$i]['Account3']   = $rs->fields['sAccount32'];
        $bank[$i]['Account4']   = $rs->fields['sAccount42'];

        // echo 'C';
        $i++;
    }

    if ($rs->fields['sAccountUnused3'] == 0 && $rs->fields['sAccount33'] != '') {
        $bank[$i]['bank']       = $rs->fields['sAccountNum13'];
        $bank[$i]['bankBranch'] = $rs->fields['sAccountNum23'];
        $bank[$i]['Account3']   = $rs->fields['sAccount33'];
        $bank[$i]['Account4']   = $rs->fields['sAccount43'];

        // echo 'D';
        $i++;
    }

    ##銀行
    // $dataBank = array();
    $sql = "SELECT * FROM tScrivenerBank WHERE sScrivener ='" . $_POST['id'] . "' AND sUnUsed = 0 AND sBankAccountName !=''";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {

        $bank[$i]['bank']       = $rs->fields['sBankMain'];
        $bank[$i]['bankBranch'] = $rs->fields['sBankBranch'];
        $bank[$i]['Account3']   = $rs->fields['sBankAccountNo'];
        $bank[$i]['Account4']   = $rs->fields['sBankAccountName'];
        // echo 'E';
        $i++;
        $rs->MoveNext();
    }

    // print_r($bank);

}

// krsort($bank);//最新一筆

// print_r($bank);

echo json_encode($bank[(count($bank) - 1)]);
