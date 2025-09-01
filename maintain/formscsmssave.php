<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/sms.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/writelog.php';
require_once dirname(__DIR__) . '/first1DB.php';

$sms = new SMS();

if (count($_POST['sSn']) > 0) {
    $sms->SaveScrivener($_POST);
    write_log('更新簡訊對象,' . json_encode($_POST) . '', 'scrivenersms');
}

// die('--');

if (! empty($_POST['sms_sMobile'])) {
    $sms->AddScrivener($_POST, $_POST['scid']);
    write_log('新增簡訊對象,' . json_encode($_POST) . '', 'scrivenersms');
}

$sms->SaveScrivenerDefault($_POST['sDefault'], $_POST['scid'], $_POST['sName'], $_POST['sSend']);

if ($_POST['same'] == 1) {
    changeCaseMobile($_POST['scid'], $_POST);
}

$sql = "UPDATE tScrivener SET sSmsLocationMark = '" . $_POST['smsLocationMark'] . "' WHERE sId = '" . $_POST['scid'] . "'";
$conn->Execute($sql);

function changeCaseMobile($sId, $data)
{
    global $conn;

    $str = '';

    $sql = "SELECT
					cc.cCertifiedId,
					cs.cScrivener,
					cs.cSmsTarget,
					cs.cSmsTargetName,
					cs.cSend2,
					cs.cSendName2
				FROM
					tContractCase AS cc
				LEFT JOIN
					tContractScrivener AS cs ON cs.cCertifiedId =cc.cCertifiedId
				WHERE
					cc.cCaseStatus = 2
					AND cs.cScrivener = '" . $sId . "'";

    $rs = $conn->Execute($sql);

    while (! $rs->EOF) {
        $tmpN = explode(',', $rs->fields['cSmsTargetName']);
        $tmpM = explode(',', $rs->fields['cSmsTarget']);

        for ($i = 0; $i < count($tmpN); $i++) {
            $smsArr[$i]['name']   = $tmpN[$i];
            $smsArr[$i]['mobile'] = $tmpM[$i];
            // echo $tmpN[$i]['name'];
        }
        unset($tmpN);unset($tmpM);
        //
        $tmpN = explode(',', $rs->fields['cSendName2']);
        $tmpM = explode(',', $rs->fields['cSend2']);

        for ($i = 0; $i < count($tmpN); $i++) {
            $Send2[$i]['name']   = $tmpN[$i];
            $Send2[$i]['mobile'] = $tmpM[$i];
            // echo $tmpN[$i]['name'];
        }
        unset($tmpN);unset($tmpM);
        if (is_array($smsArr)) {
            write_log('異動前案件,' . json_encode($smsArr) . "\r\n", 'scrivenersms');
            write_log('異動前案件,' . json_encode($Send2) . "\r\n", 'scrivenersms');
            updateSms($sId, $rs->fields['cCertifiedId'], $data, $smsArr, $Send2);
        }

        unset($tmpN);unset($tmpM);unset($smsArr);unset($Send2);

        $rs->MoveNext();
    }

    //$smsArr[$rs->fields['cSmsTargetName']] = $rs->fields['cSmsTarget'];

}

function updateSms($sId, $cId, $data, $smsArr, $Send2)
{
    global $conn;
    global $link;

    for ($i = 0; $i < count($smsArr); $i++) {
        for ($j = 0; $j < count($data["sMobile"]); $j++) {
            if ($smsArr[$i]['name'] == $data["sName"][$j] && $smsArr[$i]['mobile'] != $data["sMobile"][$j]) {
                // echo $smsArr[$j]['name'] ."=".$data["sName"][$i].";".$smsArr[$j]['mobile']."=".$data["sMobile"][$i]."<br>";
                $smsArr[$i]['mobile'] = $data["sMobile"][$j];
            }
        }
        $smsTarget[] = $smsArr[$i]["mobile"];
        // $name[] = $smsArr[$i]["name"];
    }

    for ($i = 0; $i < count($Send2); $i++) {
        for ($j = 0; $j < count($data["sMobile"]); $j++) {
            if ($Send2[$i]['name'] == $data["sName"][$j] && $Send2[$i]['mobile'] != $data["sMobile"][$j]) {
                // echo $Send2[$j]['name'] ."=".$data["sName"][$i].";".$Send2[$j]['mobile']."=".$data["sMobile"][$i]."<br>";
                $Send2[$i]['mobile'] = $data["sMobile"][$j];
            }
        }

        $smsTarget2[] = $Send2[$i]["mobile"];
        // $name2[] = $Send2[$i]["name"];
    }

    if (count($smsTarget) > 0) {
        $sql = 'UPDATE tContractScrivener SET cSmsTarget="' . @implode(',', $smsTarget) . '",cSend2 ="' . @implode(',', $smsTarget2) . '" WHERE cCertifiedId="' . $cId . '" AND cScrivener="' . $sId . '";';

        $_conn = new first1DB;
        $_conn->exeSql($sql);
        $_conn = null;unset($_conn);

        write_log('異動案件,' . $sql . "\r\n", 'scrivenersms');
    }

}
header("Location: formscriveneredit.php?id=" . $_POST['scid']);
