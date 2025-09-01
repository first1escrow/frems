<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/writelog.php';
require_once dirname(__DIR__) . '/first1DB.php';

$_POST = escapeStr($_POST);

$sql  = "SELECT sLock FROM tScrivenerSms WHERE sId = '" . $_POST['id'] . "'";
$rs   = $conn->Execute($sql);
$lock = ($rs->fields['sLock'] == 0) ? 1 : 0;
// echo $sql."\r\n";
// echo $rs->fields['sLock']."\r\n";

$sql = "UPDATE tScrivenerSms SET sLock = " . $lock . " WHERE sId = '" . $_POST['id'] . "'";
// echo $sql."\r\n";
if ($conn->Execute($sql)) {
    echo 'OK';
}
write_log($_POST['sId'] . ',鎖住對象,' . $_POST['id'], 'scrivenersms');
// echo $sql;

if ($_POST['same'] == 1) {
    $sql = "SELECT sMobile,sName FROM tScrivenerSms WHERE sId = '" . $_POST['id'] . "' ";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $delArr['sMobile'] = $rs->fields['sMobile'];
        $delArr['sName']   = $rs->fields['sName'];
        $rs->MoveNext();
        # code...
    }

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
					AND cs.cScrivener = '" . $_POST['sId'] . "'";

    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        write_log('異動前案件(所住),' . json_encode($rs->fields) . "\r\n", 'scrivenersms');
        changeCaseMobile($rs->fields['cCertifiedId'], $delArr, $rs->fields);

        $rs->MoveNext();
    }
}

function changeCaseMobile($cId, $delArr, $target)
{
    global $conn;

    //簡訊對象
    $tmp      = explode(',', $target['cSmsTarget']);
    $tmp2     = explode(',', $target['cSmsTargetName']);
    $tmpCount = count($tmp);
    for ($i = 0; $i < count($tmp); $i++) {
        // echo $tmp[$i]."_".$delArr['sMobile']."\r\n";
        if (preg_match('/' . $tmp[$i] . '/', $delArr['sMobile'])) {
            unset($tmp[$i]);unset($tmp2[$i]);
        }
    }

    $cSmsTarget     = implode(',', $tmp);
    $cSmsTargetName = implode(',', $tmp2);
    unset($tmp);unset($tmp2);unset($tmpCount);
    //服務費簡訊
    $tmp      = explode(',', $target['cSend2']);
    $tmp2     = explode(',', $target['cSendName2']);
    $tmpCount = count($tmp);
    for ($i = 0; $i < count($tmp); $i++) {
        if (preg_match('/' . $tmp[$i] . '/', $delArr['sMobile'])) {
            unset($tmp[$i]);unset($tmp2[$i]);
        }
    }
    $send  = implode(',', $tmp);
    $name2 = implode(',', $tmp2);
    unset($tmp);unset($tmp2);unset($tmpCount);

    $sql = 'UPDATE tContractScrivener SET cSmsTarget="' . $cSmsTarget . '",cSmsTargetName="' . $cSmsTargetName . '",cSend2 = "' . $send . '",cSendName2="' . $name2 . '" WHERE cCertifiedId="' . $cId . '";';
    write_log('異動後案件(刪除),' . $sql . "\r\n", 'scrivenersms');
    echo $sql . "\r\n";
    $conn->Execute($sql);

}

function changeCaseMobile2($sId)
{
    global $conn;

    $str = '';

    $sql = "SELECT
					cc.cCertifiedId,
					cs.cScrivener,
					cs.cSmsTarget
				FROM
					tContractCase AS cc
				LEFT JOIN
					tContractScrivener AS cs ON cs.cCertifiedId =cc.cCertifiedId
				WHERE
					cc.cCaseStatus = 2
					AND cs.cScrivener = '" . $sId . "'";

    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {

        // delSmsDefault($sId,$rs->fields['cCertifiedId']);

        $rs->MoveNext();
    }

}

function delSmsDefault($sId, $cId)
{
    global $conn;
    global $link;

    $sql = 'SELECT sMobile,sDefault,sSend,sName FROM tScrivenerSms WHERE sScrivener="' . $sId . '" AND sDel = 0  ORDER BY sNID,sId ASC;'; //cSmsTarget
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        if ($rs->fields['sDefault'] == 1) {
            $smsTarget[] = $rs->fields['sMobile'];
            $name[]      = $rs->fields['sName'];

        }

        if ($rs->fields['sSend'] == 1) {
            $send[]  = $rs->fields['sMobile'];
            $name2[] = $rs->fields['sName'];
        }

        $rs->MoveNext();
    }

    //複製到案件的預設簡訊對象
    if (count($smsTarget) > 0) {
        $sql = 'UPDATE tContractScrivener SET cSmsTarget="' . @implode(',', $smsTarget) . '",cSmsTargetName="' . @implode(',', $name) . '",cSend2 = "' . @implode(',', $send) . '",cSendName2="' . @implode(',', $name2) . '" WHERE cCertifiedId="' . $cId . '" AND cScrivener="' . $sId . '";';

        $_conn = new first1DB;
        $_conn->exeSql($sql);
        $_conn = null;unset($_conn);
    }

}
