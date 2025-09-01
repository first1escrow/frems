<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

$_POST = escapeStr($_POST);

$id          = $_POST['id'];
$CertifiedId = $_POST['cId'];
$iden        = $_POST['iden'];

if ($id == '') {
    $data['msg'] = 'error';
    echo json_encode($data);
    die;
}

if (!checkUID($id)) {
    $data['msg'] = 'error';
    echo json_encode($data);
    die;
}

//買方
$sql = "SELECT
			cId AS id,
			(SELECT cSignDate FROM tContractCase AS cc WHERE cc.cCertifiedId=c.cCertifiedId) AS SignDate,
			cCertifiedId AS certifiedId,
			cIdentifyId AS identifyid,
			cName AS name,
			cBirthdayDay AS birthday,
			cMobileNum AS mobile,
			cRegistZip AS zip,
			(SELECT zCity FROM tZipArea WHERE zZip = cRegistZip) AS city,
			(SELECT zArea FROM tZipArea WHERE zZip = cRegistZip) AS area,
			cRegistAddr AS addr,
			cBankKey2 AS bankcode,
			cBankBranch2 AS bankbranch,
			cBankAccName AS bankaccname,
			cBankAccNumber AS bankaccnumber
		FROM
			tContractBuyer AS c
		WHERE
			cIdentifyId = '" . $id . "' AND c.cCertifiedId != '" . $CertifiedId . "'";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $rs->fields['birthday'] = dateformate($rs->fields['birthday']);
    $rs->fields['SignDate'] = str_replace('-', '', dateformate($rs->fields['SignDate']));

    $tmp[$rs->fields['SignDate']] = $rs->fields;
    $tmpC[0]['bankcode']          = $rs->fields['bankcode'];
    $tmpC[0]['bankbranch']        = $rs->fields['bankbranch'];
    $tmpC[0]['bankaccname']       = $rs->fields['bankaccname'];
    $tmpC[0]['bankaccnumber']     = $rs->fields['bankaccnumber'];

    $tmpB = getCustomerBank($rs->fields['certifiedId'], 1);

    if (is_array($tmpB)) {
        $tmp[$rs->fields['SignDate']]['bank'] = array_merge($tmpC, $tmpB);
    } else {
        $tmp[$rs->fields['SignDate']]['bank'] = $tmpC;
    }

    $rs->MoveNext();
}
$tmpB = $tmpC = null;
unset($tmpB, $tmpC);

//賣方
$sql = "SELECT
			cId AS id,
			cCertifiedId AS certifiedId,
			cIdentifyId AS identifyid,
			(SELECT cSignDate FROM tContractCase AS cc WHERE cc.cCertifiedId=c.cCertifiedId) AS SignDate,
			cName AS name,
			cBirthdayDay AS birthday,
			cMobileNum AS mobile,
			cRegistZip AS zip,
			(SELECT zCity FROM tZipArea WHERE zZip = cRegistZip) AS city,
			(SELECT zArea FROM tZipArea WHERE zZip = cRegistZip) AS area,
			cRegistAddr AS addr,
			cBankKey2 AS bankcode,
			cBankBranch2 AS bankbranch,
			cBankAccName AS bankaccname,
			cBankAccNumber AS bankaccnumber
		FROM
			tContractOwner AS c WHERE cIdentifyId = '" . $id . "'  AND c.cCertifiedId != '" . $CertifiedId . "'";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $rs->fields['birthday'] = dateformate($rs->fields['birthday']);
    $rs->fields['SignDate'] = str_replace('-', '', dateformate($rs->fields['SignDate']));

    $tmp[$rs->fields['SignDate']] = $rs->fields;
    $tmpC[0]['bankcode']          = $rs->fields['bankcode'];
    $tmpC[0]['bankbranch']        = $rs->fields['bankbranch'];
    $tmpC[0]['bankaccname']       = $rs->fields['bankaccname'];
    $tmpC[0]['bankaccnumber']     = $rs->fields['bankaccnumber'];

    $tmpB = getCustomerBank($rs->fields['certifiedId'], 2);

    if (is_array($tmpB)) {
        $tmp[$rs->fields['SignDate']]['bank'] = array_merge($tmpC, $tmpB);
    } else {
        $tmp[$rs->fields['SignDate']]['bank'] = $tmpC;
    }

    $rs->MoveNext();
}

$tmpB = $tmpC = null;
unset($tmpB, $tmpC);

##其他買賣方
$sql = "SELECT
			cId AS id,
			cCertifiedId AS certifiedId,
			(SELECT cSignDate FROM tContractCase AS cc WHERE cc.cCertifiedId=c.cCertifiedId) AS SignDate,
			cIdentifyId AS identifyid,
			cName AS name,
			cBirthdayDay AS birthday,
			cMobileNum AS mobile,
			cRegistZip AS zip,
			(SELECT zCity FROM tZipArea WHERE zZip = cRegistZip) AS city,
			(SELECT zArea FROM tZipArea WHERE zZip = cRegistZip) AS area,
			cRegistAddr AS addr,
			cBankMain AS bankcode,
			cBankBranch AS branchcode,
			cBankAccNum AS bankaccnumber,
			cBankAccName AS bankaccname
		FROM
			tContractOthers AS c WHERE cIdentifyId = '" . $id . "' AND cIdentity IN (1, 2, 5, 6, 7)  AND c.cCertifiedId != '" . $CertifiedId . "'";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $rs->fields['birthday'] = dateformate($rs->fields['birthday']);
    $rs->fields['SignDate'] = str_replace('-', '', dateformate($rs->fields['SignDate']));

    $tmp[$rs->fields['SignDate']] = $rs->fields;
    $tmpC[0]['bankcode']          = $rs->fields['bankcode'];
    $tmpC[0]['bankbranch']        = $rs->fields['branchcode'];
    $tmpC[0]['bankaccname']       = $rs->fields['bankaccname'];
    $tmpC[0]['bankaccnumber']     = $rs->fields['bankaccnumber'];

    $tmpB = getCustomerBank($rs->fields['certifiedId'], $rs->fields['id']);

    if (is_array($tmpB)) {
        $tmp[$rs->fields['SignDate']]['bank'] = array_merge($tmpC, $tmpB);
    } else {
        $tmp[$rs->fields['SignDate']]['bank'] = $tmpC;
    }

    $rs->MoveNext();
}
$tmpB = $tmpC = null;
unset($tmpB, $tmpC);

if (is_array($tmp)) {
    krsort($tmp);
    $data = array_shift($tmp);

    $data['msg'] = 'ok';
    echo json_encode($data);
} else {
    $data['msg'] = 'error';
    echo json_encode($data);
}

exit;

function dateformate($val)
{
    $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", "", $val));
    $tmp = explode('-', $val);

    if (preg_match("/0000/", $tmp[0])) {
        $tmp[0] = '000';
    } else {
        $tmp[0] -= 1911;
    }

    $val = $tmp[0] . "-" . $tmp[1] . "-" . $tmp[2];
    $tmp = null;unset($tmp);

    return $val;
}

function getCustomerBank($cId, $iden)
{
    global $conn;

    if ($iden != 1 && $iden != 2) {
        $str = " AND cOtherId = '" . $iden . "'";
    } else {
        $str = " AND cIdentity = '" . $iden . "'";
    }

    $sql = "SELECT
				cBankMain AS bankcode,
				cBankBranch AS bankbranch,
				cBankAccountNo AS bankaccnumber,
				cBankAccountName AS bankaccname
			FROM
				tContractCustomerBank
			WHERE
				cCertifiedId = '" . $cId . "'" . $str;
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        $tmp[] = $rs->fields;
        $rs->MoveNext();
    }

    return $tmp;
}

function checkUID($sn)
{
    $result = false;

    if (mb_strlen($sn) == 8) { //檢查統一編號
        $result = UNID($sn);
    } else if (mb_strlen($sn) == 10) {
        $sn = strtoupper($sn); //將英文字母設定為大寫

        $reg  = "/^[A-Z]{1}[A-D]{1}[0-9]{8}$/";
        $reg1 = "/^[0-9]{8}[A-Z]{1}[A-Z]{1}$/";

        if (preg_match($reg, $sn)) { //檢查居留證字號
            $result = RID($sn);
        } else if (preg_match($reg1, $sn)) { //未住滿183天的非大陸民眾(第一~八碼採護照西元年出生日;第九~十碼彩護照內英文姓名第一個字之前2個字母)
            $result = true;
        } else { //檢查身分證字號
            $result = PID($sn);
        }
    } else if (mb_strlen($sn) == 7) { //未住滿183天的大陸民眾(第一碼為9;第二到七碼為西元出生年之後2位及月日各2位)
        $reg2 = "/^[0-9]{7}$/";
        if (preg_match($reg2, $sn)) {
            $result = true;
        }
    }

    return $result;
}

/* 統一編號檢核 */
function UNID($sn)
{
    $cx  = array(1, 2, 1, 2, 1, 2, 4, 1); //驗算基數
    $sum = 0;
    if (mb_strlen($sn) != 8) {
        return false; //統編錯誤，要有 8 個數字
    }

    $cnum[0] = substr($sn, 0, 1);
    $cnum[1] = substr($sn, 1, 1);
    $cnum[2] = substr($sn, 2, 1);
    $cnum[3] = substr($sn, 3, 1);
    $cnum[4] = substr($sn, 4, 1);
    $cnum[5] = substr($sn, 5, 1);
    $cnum[6] = substr($sn, 6, 1);
    $cnum[7] = substr($sn, 7, 1);
    for ($i = 0; $i <= 7; $i++) {
        if (ord($cnum[$i]) < 48 || ord($cnum[$i]) > 57) {
            return false; //統編錯誤，要有 8 個 0-9 數字組合
        }

        $sum += cc($cnum[$i] * $cx[$i]); //加總運算碼結果
    }

    if ($sum % 10 == 0) {
        return true; //統一編號正確!
    } else if ($cnum[6] == 7 && ($sum + 1) % 10 == 0) {
        return true; //統一編號正確!
    } else {
        return false; //統一編號錯誤!
    }
}

/* 計算數字大於 10 之處理 */
function cc($n)
{
    if ($n > 9) {
        $s = $n+"";

        $n1 = substr($s, 0, 1) * 1;
        $n2 = substr($s, 1, 2) * 1;
        $n  = $n1 + $n2;
    }

    return $n;
}

function PID($sn)
{
    /* 定義字母對應的數字 */
    $a = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $b = array('10', '11', '12', '13', '14', '15', '16', '17', '34', '18', '19', '20', '21', '22', '35', '23', '24', '25', '26', '27', '28', '29', '32', '30', '31', '33');

    $max      = count($a);
    $alphabet = array();
    for ($i = 0; $i < $max; $i++) {
        $alphabet[$i] = array($a[$i], $b[$i]);
    }

    $sn    = strtoupper($sn); //將英文字母設定為大寫;
    $snLen = mb_strlen($sn); //計算字數長度

    /* 若號碼長度不等於10，代表輸入長度不合格式 */
    if ($snLen != 10) {
        return false; //輸入字號長度不正確!!
    }

    /* 取出第一個英文字母 */
    $ch    = substr($sn, 0, 1);
    $chVal = '';
    for ($i = 0; $i < $max; $i++) {
        if ($alphabet[$i][0] == $ch) {
            $chVal = $alphabet[$i][1];
            break;
        }
    }

    /* 取出檢查碼 */
    $lastch = substr($sn, -1, 1);

    $ch1 = substr($chVal, 0, 1); //十位數
    $ch2 = substr($chVal, 1, 1); //個位數

    $_val = ($ch2 * 9) + $ch1; //個位數 x 9 再加上十位數
    $_val = $_val % 10; //除以10取餘數

    /* 計算檢核碼 */
    $t1 = $_val * 1;
    $t2 = substr($sn, 1, 1) * 8;
    $t3 = substr($sn, 2, 1) * 7;
    $t4 = substr($sn, 3, 1) * 6;
    $t5 = substr($sn, 4, 1) * 5;
    $t6 = substr($sn, 5, 1) * 4;
    $t7 = substr($sn, 6, 1) * 3;
    $t8 = substr($sn, 7, 1) * 2;
    $t9 = substr($sn, 8, 1) * 1;

    $checkCode = ($t1 + $t2 + $t3 + $t4 + $t5 + $t6 + $t7 + $t8 + $t9) % 10;
    $checkCode = ($checkCode == 0) ? 0 : (10 - $checkCode); //檢查碼

    /* 比對檢核碼是否相符 */
    return ($checkCode == $lastch) ? true : false;
}

/* 居留證字號檢核 */
function RID($sn)
{
    /* 定義字母對應的數字 */
    $a = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $b = array('10', '11', '12', '13', '14', '15', '16', '17', '34', '18', '19', '20', '21', '22', '35', '23', '24', '25', '26', '27', '28', '29', '32', '30', '31', '33');

    $max      = count($a);
    $alphabet = array();
    for ($i = 0; $i < $max; $i++) {
        $alphabet[$i] = array($a[$i], $b[$i]);
    }

    $sn    = strtoupper($sn); //將英文字母設定為大寫
    $snLen = mb_strlen($sn); //計算字數長度

    /* 若號碼長度不等於10，代表輸入長度不合格式 */
    if ($snLen != 10) {
        return false; //輸入字號長度不正確!!
    }

    /* 取出英文字母 */
    $ch1    = substr($sn, 0, 1); //
    $ch2    = substr($sn, 1, 1); //
    $chVal1 = '';
    $chVal2 = '';
    for ($i = 0; $i < $max; $i++) {
        /* 取出第一個英文字母對應的數值 */
        if ($alphabet[$i][0] == $ch1) {
            $chVal1 = $alphabet[$i][1];
        }

        /* 取出第二個英文字母對應的數值 */
        if ($alphabet[$i][0] == $ch2) {
            $chVal2 = $alphabet[$i][1];
        }
    }

    /* 取出檢查碼 */
    $lastch = substr($sn, -1, 1);

    /* 第一碼英文字的轉換 */
    $ch1 = substr($chVal1, 0, 1); //十位數
    $ch2 = substr($chVal1, 1, 1); //個位數
    $t0  = ($ch2 * 9 + $ch1) % 10;

    /* 第二碼英文字的轉換 */
    $_val = substr($chVal2, -1, 1) * 1; //個位數

    /* 計算檢核碼 */
    $t1 = $_val * 8;
    $t2 = substr($sn, 2, 1) * 7;
    $t3 = substr($sn, 3, 1) * 6;
    $t4 = substr($sn, 4, 1) * 5;
    $t5 = substr($sn, 5, 1) * 4;
    $t6 = substr($sn, 6, 1) * 3;
    $t7 = substr($sn, 7, 1) * 2;
    $t8 = substr($sn, 8, 1) * 1;

    $checkCode = $t0 + $t1 + $t2 + $t3 + $t4 + $t5 + $t6 + $t7 + $t8 + 1 - 1;
    $checkCode = 10 - ($checkCode % 10);

    if (($checkCode % 10) == 0) {
        $checkCode = 0;
    }

    /* 比對檢核碼是否相符 */
    return ($checkCode == $lastch) ? true : false;
}
