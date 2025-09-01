<?php
ini_set('memory_limit', '256M');
set_time_limit(1200);

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/openadodbSlave.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';

use First1\V1\PayByCase\PayByCase;

$paybycase = new PayByCase;

//判定身份別(法人、自然人'其他)
function obj_id($iden)
{
    $_ide = '';

    if (preg_match("/^[0-9]{8}$/", $iden)) { //若身分為法人(八碼、公司)
        $_ide = '法人';
    } else if (preg_match("/^\w{10}$/", $iden)) { //若為自然人(十碼、個人)
        $_ide = '其他';

        if (preg_match("/[a-zA-Z]{2}/", $iden) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-9]{8}/", $iden)) { //若證號有兩碼英文字，則為外國人
            $_ide = '非本國人';
        } else if (preg_match("/^[a-zA-Z]{1}[0-9]{9}$/", $iden)) { //符合1+9碼、則為本國人
            $_ide = '自然人';
        }
    } else if (preg_match("/^9[0-9]{6}$/", $iden)) {
        $_ide = '非本國人';
    }

    return $_ide;
}

//計算 10 % 所得稅額
function payTax($_id, $_int = 0)
{
    $_len = strlen($_id); // 個人10碼 公司8碼

    if ($_len == '10') { // 個人10碼
        $_o   = 2; // 本國籍自然人(一般民眾)
        $_tax = 0.1; // 稅率：10%

        if (preg_match("/[A-Za-z]{2}/", $_id) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-9]{8}/", $_id)) { //判別是否為外國人(兩碼英文字母者)
            $_o   = 1; // 外國籍自然人(一般民眾)
            $_tax = 0.2; // 稅率：20%
        }
    } else if ($_len == '8') { // 公司8碼
        $_o   = 2; // 本國籍自然人(一般民眾)                        // 本國籍法人(公司)
        $_tax = 0.1; // 稅率：10%
    } else if ($_len == '7') {
        if (preg_match("/^9[0-9]{6}$/", $_id)) { // 判別是否為外國人
            $_o   = 1; // 外國籍自然人(一般民眾)
            $_tax = 0.2; // 稅率：20%
        }
    }

    if ($_o == "1") {
        $cTax = round($_int * $_tax);
    } else if ($_o == "2") {
        $cTax = 0;

        if ($_int > 20000) {
            $cTax = round($_int * $_tax);
        }
    }

    return $cTax;
}

//計算2%補充保費 2016/01/15改1.91%(0.0191)  //2021/01/01 調整為2.11%(0.0211)
function payNHITax($_id, $_ide = 0, $_int = 0)
{
    $NHI = 0;

    if (preg_match("/\w{10}/", $_id)) { // 若為自然人身分(10碼)則需要代扣 NHI2 稅額
        if (preg_match("/[A-Za-z]{2}/", $_id) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-9]{8}/", $_id)) { // 若為外國人
            if ($_ide == '1') { // 若有加保健保者
                if ($_int >= 20000) { // 若餘額大於等於 5000
                    $NHI = round($_int * 0.0211); // 則代扣 2% 保費 20160115改1.91%(0.0191)  //2021/01/01 調整為2.11%(0.0211)
                }
            }
        } else {
            if ($_int >= 20000) { // 若利息大於等於 20,000 元(105-01-01起額度調為2萬元)
                $NHI = round($_int * 0.0211); // 則代扣 2% 保費 2016/01/15改1.91%(0.0191) //2021/01/01 調整為2.11%(0.0211)
            }
        }
    }

    return $NHI;
}

//仲介店型態
function realtyCat($id)
{
    global $conn;

    $_sql = 'SELECT bBrand,bCategory FROM tBranch WHERE bId="' . $id . '";';
    $rs   = $conn->Execute($_sql);

    return array($rs->fields['bBrand'], $rs->fields['bCategory']);
}

//計算品牌數量
function countBrand($bId, &$o, &$t, &$u, &$s, &$n)
{
    $arrTmp = realtyCat($bId);
    if ($arrTmp[0] == '1') { //台灣房屋
        if ($arrTmp[1] == '2') { //直營
            $s++;
        } else { //加盟
            $t++;
        }
    } else if ($arrTmp[0] == '2') { //非仲介成交
        $n++;
    } else if ($arrTmp[0] == '49') { //優美
        $u++;
    } else { //其他品牌
        $o++;
    }
}

//起始日期
if ($fds) {
    $tmp = explode('-', $fds);
    $tmp[0] += 1911;
    $fds = implode('-', $tmp);
    $tmp = null;unset($tmp);
}

//結束日期
if ($fde) {
    $tmp = explode('-', $fde);
    $tmp[0] += 1911;
    $fde = implode('-', $tmp);
    $tmp = null;unset($tmp);
}

//取得合約銀行活儲帳號
$bank_name = '';
$bank_vr   = '';
$cBankCode = '';
if ($bank_option) {
    $sql = 'SELECT cBankVR, cBankAccount, cBankCode FROM tContractBank WHERE cBankCode IN (' . $bank_option . ');';
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        //銀行代號
        //2家第一銀行 永豐銀行都要算
        if (in_array($rs->fields['cBankCode'], [77, 80])) {
            $cBankCode .= "77,80,";
            $bank_vr .= '999850|999860|'; //活儲帳號
        } else if (in_array($rs->fields['cBankCode'], [8, 81])) {
            $cBankCode .= "8,81,";
            $bank_vr .= '60001|55006|'; //活儲帳號
        } else {
            $cBankCode .= $rs->fields['cBankCode'] . ',';
            $bank_vr .= $rs->fields['cBankVR'] . '|'; //活儲帳號
        }

        $rs->MoveNext();
    }
}

//活儲帳號
$bank_vr   = substr($bank_vr, 0, -1);
$cBankCode = substr($cBankCode, 0, -1);

//因有未結案先行出履保費所以增加條件[tra.tObjKind = "其他" AND tKind="保證費"]20151113
$sql = 'SELECT
            tra.tBankLoansDate as tDate,
            cas.cEndDate AS cEndDate,
            cas.cFeedbackDate AS cFeedbackDate,
            tra.tMemo as tCertifiedId,
            tra.tVR_Code as VR_Code,
            tra.tMoney,
            (SELECT cCertifiedMoney FROM tContractIncome WHERE cCertifiedId=tra.tMemo) as cCertifiedMoney,
            own.cIdentifyId as ownerId,
            own.cNHITax as ownerNHI,
            buy.cIdentifyId as buyerId,
            buy.cNHITax as buyerNHI,
            rea.cBranchNum as cBranchNum,
            rea.cBranchNum1 as cBranchNum1,
            rea.cBranchNum2 as cBranchNum2,
            rea.cBranchNum3 as cBranchNum3,
            cas.cCaseStatus AS cCaseStatus,
            tra.tAccount AS tAccount,
            tra.tObjKind,
            cas.cSpCaseFeedBackMoney,
            cas.cCaseFeedBackMoney,
            cas.cCaseFeedBackMoney1,
            cas.cCaseFeedBackMoney2,
            cas.cCaseFeedBackMoney3,
            buy.cInterestMoney AS buyer_cInterestMoney,
            buy.cInvoiceMoney AS buyer_cInvoiceMoney,
            own.cInterestMoney AS owner_cInterestMoney,
            own.cInvoiceMoney AS owner_cInvoiceMoney,
            rea.cInterestMoney AS branch_cInterestMoney,
            rea.cInterestMoney1 AS branch_cInterestMoney1,
            rea.cInterestMoney2 AS branch_cInterestMoney2,
            rea.cInterestMoney3 AS branch_cInterestMoney3,
            rea.cInvoiceMoney AS branch_cInvoiceMoney,
            rea.cInvoiceMoney1 AS branch_cInvoiceMoney1,
            rea.cInvoiceMoney2 AS branch_cInvoiceMoney2,
            rea.cInvoiceMoney3 AS branch_cInvoiceMoney3,
            (SELECT cInterestMoney FROM tContractScrivener AS csc WHERE csc.cCertifiedId=tra.tMemo) AS scr_cInterestMoney,
            (SELECT pName FROM tPeopleInfo WHERE pId=cas.cLastEditor) as lastmodify,
            (SELECT sName FROM tStatusCase WHERE sId =cas.cCaseStatus) AS status,
            (SELECT cInvoiceScrivener FROM tContractInvoice AS a WHERE a.cCertifiedId = tra.tMemo) AS scr_cInvoiceMoney,
            (SELECT cInvoiceOther FROM tContractInvoice AS a WHERE a.cCertifiedId = tra.tMemo) AS cInvoiceOther,
            (SELECT cBankFullName FROM tContractBank AS bank WHERE bank.cBankCode = cas.cBank) AS bankName,
            (SELECT bMoney FROM tBankTransRelay AS relay WHERE relay.bKind = "保證費" AND relay.bCertifiedId = tra.tMemo LIMIT 1) AS relayMoney,
            (SELECT bMoney FROM tBankTransRelay AS relay WHERE relay.bKind = "地政士回饋金" AND relay.bCertifiedId = tra.tMemo LIMIT 1) AS relayFeedBackMoney,
            (SELECT b.sFeedDateCat FROM tContractScrivener AS a LEFT JOIN tScrivener AS b ON a.cScrivener = b.sId WHERE a.cCertifiedId = cas.cCertifiedId) AS feedDateCat,
            (SELECT 0) as certifiedMoneyPaid,
            cas.cBankRelay AS cBankRelay,
            tra.tExport_time
        FROM
            tBankTrans AS tra
        JOIN
            tContractBuyer AS buy ON buy.cCertifiedId=tra.tMemo
        JOIN
            tContractOwner AS own ON own.cCertifiedId=tra.tMemo
        JOIN
            tContractRealestate AS rea ON rea.cCertifyId=tra.tMemo
        JOIN
            tContractCase AS cas ON cas.cCertifiedId=tra.tMemo
        WHERE
            tra.tExport="1"
            AND tra.tPayOk="1"
            AND tra.tExport_nu NOT LIKE "aaaaaaaaaaaa_"
            AND tKind="保證費"
            AND tra.tVR_Code REGEXP "^(' . $bank_vr . ')"
            AND (tra.tBankLoansDate>="' . $fds . '" AND tra.tBankLoansDate<="' . $fde . '")
        ORDER BY
            tra.tExport_time,tra.tMemo
        ASC ;';
$rs = $conn->Execute($sql);

$list = array();
$feedbakCid = []; //用來分辨有沒有出款回饋金
while (!$rs->EOF) {
    array_push($list, $rs->fields);
    $feedbakCid[] = $rs->fields['tCertifiedId'];
    $rs->MoveNext();
}

//無履保費出款但有出利息
$sql = 'SELECT
            cas.cBankList as tDate,
            cas.cEndDate as cEndDate,
            cas.cFeedbackDate as cFeedbackDate,
            cas.cCertifiedId as tCertifiedId,
            cas.cEscrowBankAccount as VR_Code,
            cas.cCaseStatus AS cCaseStatus,
            cas.cSpCaseFeedBackMoney,
            cas.cCaseFeedBackMoney,
            cas.cCaseFeedBackMoney1,
            cas.cCaseFeedBackMoney2,
            cas.cCaseFeedBackMoney3,
            buy.cIdentifyId as buyerId,
            buy.cNHITax as buyerNHI,
            buy.cInterestMoney AS buyer_cInterestMoney,
            buy.cInvoiceMoney AS buyer_cInvoiceMoney,
            own.cInterestMoney AS owner_cInterestMoney,
            own.cInvoiceMoney AS owner_cInvoiceMoney,
            own.cIdentifyId as ownerId,
            own.cNHITax as ownerNHI,
            rea.cBranchNum as cBranchNum,
            rea.cBranchNum1 as cBranchNum1,
            rea.cBranchNum2 as cBranchNum2,
            rea.cBranchNum3 as cBranchNum3,
            rea.cInterestMoney AS branch_cInterestMoney,
            rea.cInterestMoney1 AS branch_cInterestMoney1,
            rea.cInterestMoney2 AS branch_cInterestMoney2,
            rea.cInterestMoney3 AS branch_cInterestMoney3,
            rea.cInvoiceMoney AS branch_cInvoiceMoney,
            rea.cInvoiceMoney1 AS branch_cInvoiceMoney1,
            rea.cInvoiceMoney2 AS branch_cInvoiceMoney2,
            rea.cInvoiceMoney3 AS branch_cInvoiceMoney3,
            (SELECT cCertifiedMoney FROM tContractIncome AS a WHERE a.cCertifiedId = cas.cCertifiedId) as cCertifiedMoney,
            (SELECT pName FROM tPeopleInfo WHERE pId=cas.cLastEditor) as lastmodify,
            (SELECT sName FROM tStatusCase WHERE sId =cas.cCaseStatus) AS status,
            (SELECT cInterestMoney FROM tContractScrivener AS a WHERE a.cCertifiedId=cas.cCertifiedId) as scr_cInterestMoney,
            (SELECT cInvoiceScrivener FROM tContractInvoice AS a WHERE a.cCertifiedId = cas.cCertifiedId) as scr_cInvoiceMoney,
            (SELECT cInvoiceOther FROM tContractInvoice AS a WHERE a.cCertifiedId = cas.cCertifiedId) as cInvoiceOther,
            (SELECT cBankFullName FROM tContractBank AS bank WHERE bank.cBankCode = cas.cBank) AS bankName,
            (SELECT bMoney FROM tBankTransRelay AS relay WHERE relay.bKind = "保證費" AND relay.bCertifiedId = cas.cCertifiedId LIMIT 1) AS relayMoney,
            (SELECT bMoney FROM tBankTransRelay AS relay WHERE relay.bKind = "地政士回饋金" AND relay.bCertifiedId = cas.cCertifiedId LIMIT 1) AS relayFeedBackMoney,
            (SELECT b.sFeedDateCat FROM tContractScrivener AS a LEFT JOIN tScrivener AS b ON a.cScrivener = b.sId WHERE a.cCertifiedId = cas.cCertifiedId) AS feedDateCat,
            (SELECT 0) as certifiedMoneyPaid,
            cas.cBankRelay AS cBankRelay
        FROM
            tContractCase AS cas
        JOIN
            tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId
        JOIN
            tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
        JOIN
            tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
        WHERE
            cas.cBankList>="' . $fds . '"
            AND cas.cBankList<="' . $fde . '"
            AND cas.cBankList<>""
            AND cas.cBank IN (' . $cBankCode . ')
        ORDER BY
            cas.cBankList, cas.cCertifiedId
        ASC ;';
$rs = $conn->Execute($sql);

$arr = array();
$i   = 0;
$cId = [];

while (!$rs->EOF) {
    $arr[$i]           = $rs->fields;
    $arr[$i]['tMoney'] = '0'; //因未出履保費，所以金額設為 0
    $cId[] = $rs->fields['tCertifiedId'];
    $feedbakCid[] = $rs->fields['tCertifiedId'];
    $i++;

    $rs->MoveNext();
}

$queryStr = '';
if(!empty($feedbakCid)) {
    $queryStr = ' AND cas.cCertifiedId NOT IN (' . implode(",", $feedbakCid) . ')';
}
//當天出回饋金 保證金已收
$sql = 'SELECT
        cas.cBankList as tDate,
        cas.cEndDate as cEndDate,
        cas.cFeedbackDate as cFeedbackDate,
        cas.cCertifiedId as tCertifiedId,
        cas.cEscrowBankAccount as VR_Code,
        cas.cCaseStatus AS cCaseStatus,
        cas.cSpCaseFeedBackMoney,
        cas.cCaseFeedBackMoney,
        cas.cCaseFeedBackMoney1,
        cas.cCaseFeedBackMoney2,
        cas.cCaseFeedBackMoney3,
        buy.cIdentifyId as buyerId,
        buy.cNHITax as buyerNHI,
        buy.cInterestMoney AS buyer_cInterestMoney,
        buy.cInvoiceMoney AS buyer_cInvoiceMoney,
        own.cInterestMoney AS owner_cInterestMoney,
        own.cInvoiceMoney AS owner_cInvoiceMoney,
        own.cIdentifyId as ownerId,
        own.cNHITax as ownerNHI,
        rea.cBranchNum as cBranchNum,
        rea.cBranchNum1 as cBranchNum1,
        rea.cBranchNum2 as cBranchNum2,
        rea.cBranchNum3 as cBranchNum3,
        rea.cInterestMoney AS branch_cInterestMoney,
        rea.cInterestMoney1 AS branch_cInterestMoney1,
        rea.cInterestMoney2 AS branch_cInterestMoney2,
        rea.cInterestMoney3 AS branch_cInterestMoney3,
        rea.cInvoiceMoney AS branch_cInvoiceMoney,
        rea.cInvoiceMoney1 AS branch_cInvoiceMoney1,
        rea.cInvoiceMoney2 AS branch_cInvoiceMoney2,
        rea.cInvoiceMoney3 AS branch_cInvoiceMoney3,
        (SELECT 0) as cCertifiedMoney,
        (SELECT pName FROM tPeopleInfo WHERE pId=cas.cLastEditor) as lastmodify,
        (SELECT sName FROM tStatusCase WHERE sId =cas.cCaseStatus) AS status,
        (SELECT cInterestMoney FROM tContractScrivener AS a WHERE a.cCertifiedId=cas.cCertifiedId) as scr_cInterestMoney,
        (SELECT cInvoiceScrivener FROM tContractInvoice AS a WHERE a.cCertifiedId = cas.cCertifiedId) as scr_cInvoiceMoney,
        (SELECT cInvoiceOther FROM tContractInvoice AS a WHERE a.cCertifiedId = cas.cCertifiedId) as cInvoiceOther,
        (SELECT cBankFullName FROM tContractBank AS bank WHERE bank.cBankCode = cas.cBank) AS bankName,
        (SELECT bMoney FROM tBankTransRelay AS relay WHERE relay.bKind = "保證費" AND relay.bCertifiedId = cas.cCertifiedId LIMIT 1) AS relayMoney,
        (SELECT bMoney FROM tBankTransRelay AS relay WHERE relay.bKind = "地政士回饋金" AND relay.bCertifiedId = cas.cCertifiedId LIMIT 1) AS relayFeedBackMoney,
        (SELECT b.sFeedDateCat FROM tContractScrivener AS a LEFT JOIN tScrivener AS b ON a.cScrivener = b.sId WHERE a.cCertifiedId = cas.cCertifiedId) AS feedDateCat,
        (SELECT 1) as certifiedMoneyPaid,
        cas.cBankRelay AS cBankRelay,
        (SELECT cCertifiedMoney FROM tContractIncome AS a WHERE a.cCertifiedId = cas.cCertifiedId) as paidMoney,
        (SELECT tObjKind FROM `tBankTrans` AS tra WHERE tra.tKind = "保證費" AND tra.tVR_Code = cas.cEscrowBankAccount LIMIT 1) AS tObjKind
    FROM
        tContractCase AS cas
    JOIN
        tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId
    JOIN
        tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
    JOIN
        tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
    WHERE
        cas.cFeedbackDate>="' . $fds . '"
        AND cas.cFeedbackDate<="' . $fde . '"
        AND cas.cFeedbackDate<>""
        AND cas.cBank IN (' . $cBankCode . ')' . $queryStr . '
    ORDER BY
        cas.cBankList, cas.cCertifiedId
    ASC ;';

$rs = $conn->Execute($sql);
while (!$rs->EOF) {
    $arr[$i]           = $rs->fields;
    $arr[$i]['tMoney'] = '0'; //因未出履保費，所以金額設為 0
    $cId[] = $rs->fields['tCertifiedId'];
    $i++;

    $rs->MoveNext();
}


if(!empty($cId)) {
    /* 找出所有 tExport_time */
    $cId = implode(",", $cId);
    $sql = 'SELECT a.tMemo, a.tExport_time FROM tBankTrans AS a WHERE a.tMemo IN(' . $cId . ') AND tObjKind = "點交(結案)" GROUP BY tMemo DESC';
    $res = $conn->Execute($sql);

    $exportTime = [];
    while (!$res->EOF) {
        $exportTime[$res->fields['tMemo']] = $res->fields['tExport_time'];
        $res->MoveNext();
    }

    foreach ($arr as $key => $value) {
        $arr[$key]['tExport_time'] = $exportTime[$value['tCertifiedId']];
    }
}



$list = array_merge($list, $arr); //合併上面撈取的兩種資料
$arr  = null;unset($arr);

//計算利息
$paybycase_data = [];

$max = count($list);
for ($i = 0; $i < $max; $i++) {
    $list[$i]['expDate'] = $list[$i]['tDate'];
    $tmp                 = explode('-', $list[$i]['tDate']);
    $list[$i]['tDate']   = $tmp[1] . '/' . $tmp[2];
    $tmp                 = null;unset($tmp);

    //取得保證號碼、利息
    $tInterest = 0;

    //發票數量統計歸零
    $invoiceNo = 0;

    //買方人數
    $buyerNo = 0;

    //賣方人數
    $ownerNo = 0;

    //回饋金
    $feedbackmoney = 0;
    $feedbackmoney = $list[$i]['cCaseFeedBackMoney'] + $list[$i]['cCaseFeedBackMoney1'] + $list[$i]['cCaseFeedBackMoney2'] + $list[$i]['cCaseFeedBackMoney3'] + $list[$i]['cSpCaseFeedBackMoney'];

    $tmp = getFeedBackMoney($list[$i]['tCertifiedId']);
    if (is_array($tmp)) {
        foreach ($tmp as $k => $v) {
            $feedbackmoney += $v['fMoney'];
        }
    }
    $list[$i]['Feed'] = $feedbackmoney;

    $tInterest += (int) $list[$i]['buyer_cInterestMoney'];
    if ($list[$i]['buyer_cInvoiceMoney'] != 0) {
        $invoiceNo += 1;
    }

    //其他買方
    $sql = 'SELECT cInterestMoney,cInvoiceMoney FROM tContractOthers WHERE cCertifiedId="' . $list[$i]['tCertifiedId'] . '" AND cIdentity="1";';
    $rs  = $conn->Execute($sql);
    while (!$rs->EOF) {
        $tInterest += (int) $rs->fields['cInterestMoney'];
        if ($rs->fields['cInvoiceMoney'] != '0') { //若開發票金額大於0，則累加
            $invoiceNo += 1;
        }
        $buyerNo++;

        $rs->MoveNext();
    }

    if ($buyerNo > 0) {
        $list[$i]['buyerNo'] = '(' . ($buyerNo + 1) . '人)';
    }

    $tInterest += (int) $list[$i]['owner_cInterestMoney'];
    if ($list[$i]['owner_cInvoiceMoney'] != '0') { //若開發票金額大於0，則累加
        $invoiceNo += 1;
    }

    //其他賣方
    $sql = 'SELECT cInterestMoney,cInvoiceMoney FROM tContractOthers WHERE cCertifiedId="' . $list[$i]['tCertifiedId'] . '" AND cIdentity="2";';
    $rs  = $conn->Execute($sql);
    while (!$rs->EOF) {
        $tInterest += (int) $rs->fields['cInterestMoney'];
        if ($rs->fields['cInvoiceMoney'] != '0') { //若開發票金額大於0，則累加
            $invoiceNo += 1;
        }
        $ownerNo++;

        $rs->MoveNext();
    }

    if ($ownerNo > 0) {
        $list[$i]['ownerNo'] = '(' . ($ownerNo + 1) . '人)';
    }

    $tInterest += (int) $list[$i]['branch_cInterestMoney'];
    $tInterest += (int) $list[$i]['branch_cInterestMoney1'];
    $tInterest += (int) $list[$i]['branch_cInterestMoney2'];
    $tInterest += (int) $list[$i]['branch_cInterestMoney3'];
    if ($list[$i]['branch_cInvoiceMoney'] != '0') { //若開發票金額大於0，則累加
        $invoiceNo += 1;
    }

    if ($list[$i]['branch_cInvoiceMoney1'] != '0') { //若開發票金額大於0，則累加
        $invoiceNo += 1;
    }

    if ($list[$i]['branch_cInvoiceMoney2'] != '0') { //若開發票金額大於0，則累加
        $invoiceNo += 1;
    }

    if ($list[$i]['branch_cInvoiceMoney3'] != '0') { //若開發票金額大於0，則累加
        $invoiceNo += 1;
    }

    $tInterest += (int) $list[$i]['scr_cInterestMoney'];
    if ($list[$i]['scr_cInvoiceMoney'] != '0') { //若開發票金額大於0，則累加
        $invoiceNo += 1;
    }

    if ($list[$i]['cInvoiceOther'] != '0') {
        $invoiceNo += 1;
    }

    $sql = 'SELECT cInvoiceMoney FROM tContractInvoiceExt WHERE cCertifiedId = "' . $list[$i]['tCertifiedId'] . '"';
    $rs  = $conn->Execute($sql);
    while (!$rs->EOF) {
        if ($rs->fields['cInvoiceMoney'] > 0) {
            $invoiceNo += 1;
        }

        $rs->MoveNext();
    }

    //指定對象的利息
    $sql = 'SELECT cInterestMoney FROM tContractInterestExt WHERE cCertifiedId = "' . $list[$i]['tCertifiedId'] . '"';
    $rs  = $conn->Execute($sql);
    while (!$rs->EOF) {
        $tInterest += (int) $rs->fields['cInterestMoney'];
        $rs->MoveNext();
    }

    //利息總和
    $list[$i]['tInterest'] = $tInterest;

    //發票數總和
    $list[$i]['invoiceNo'] = $invoiceNo;

    //代書回饋金
    $pay_by_case = $paybycase->getPayByCase($list[$i]['tCertifiedId']);
    if (!empty($pay_by_case)) {
        if($list[$i]['cFeedbackDate'] >= $fds and $list[$i]['cFeedbackDate'] <= $fde) {
            $paybycase_data[] = array_merge($pay_by_case, ['tDate' => $list[$i]['cFeedbackDate'], 'bankName' => $list[$i]['bankName']]);
        }
    }

    $list[$i]['scrivenerFeedBackMoney'] = empty($pay_by_case['detail']['total']) ? 0 : $pay_by_case['detail']['total']; //金額
    $list[$i]['feedbackIncomeTax']      = empty($pay_by_case['fTax']) ? 0 : $pay_by_case['fTax']; //代扣10%稅款
    $list[$i]['feedbackNHITax']         = empty($pay_by_case['fNHI']) ? 0 : $pay_by_case['fNHI']; //代扣2%保費
    if($list[$i]['tObjKind'] == '履保費先收(結案回饋)') {
        $list[$i]['scrivenerFeedBackMoney'] = 0;
        $list[$i]['feedbackIncomeTax'] = 0;
        $list[$i]['feedbackNHITax'] = 0;
    }
    $list[$i]['branchFeedBackMoney']    = $list[$i]['Feed'] - $list[$i]['scrivenerFeedBackMoney'];
}

//剔除退款回存重複金額 20230927:保證號碼同金額同日期僅能退款一次，超過一次就可能會有問題
$k   = 0;
$max = count($list);
for ($i = 0; $i < $max; $i++) {
    $tmp   = explode('-', $list[$i]['expDate']);
    $tDate = ($tmp[0] - 1911) . $tmp[1] . $tmp[2];
    $tmp   = null;unset($tmp);

    $tMoney = str_pad($list[$i]['tMoney'], 13, '0', STR_PAD_LEFT) . '00';

    $sql = 'SELECT
                id
            FROM
                tExpense
            WHERE
                eTradeCode="178Y"
                AND eExportCode="8888888"
                AND eDepAccount="00' . $list[$i]['VR_Code'] . '"
                AND (ePayTitle LIKE "%退款回存%" OR ePayTitle LIKE "退匯存入")
                AND eTradeDate="' . $tDate . '"
                AND eLender="' . $tMoney . '";';
    $rs = $conn->Execute($sql);

    $fg = 0;
    while (!$rs->EOF) {
        $arr[$list[$i]['tCertifiedId']] = empty($arr[$list[$i]['tCertifiedId']]) ? 1 : ($arr[$list[$i]['tCertifiedId']] + 1);
        if ($arr[$list[$i]['tCertifiedId']] > 1) {
            $detail[$k] = $list[$i];
            $k++;
        }
        $fg++;

        $rs->MoveNext();
    }

    if (!$fg) {
        $detail[$k] = $list[$i];
        $k++;
    }
}

$list = null;unset($list);

//依據銀行分組
$first = [];
$sinopac = [];
$taishin = [];
foreach ($detail as $key => $item) {
    if($item['bankName'] == '第一銀行') {
        $first[] = $item;
    }
    if($item['bankName'] == '永豐銀行') {
        $sinopac[] = $item;
    }
    if($item['bankName'] == '台新銀行') {
        $taishin[] = $item;
    }
}
$detail = null;unset($detail);
usort($first, function ($a, $b){
    return $a['tExport_time'] > $b['tExport_time'];
});
usort($sinopac, function ($a, $b){
    return $a['tExport_time'] > $b['tExport_time'];
});
usort($taishin, function ($a, $b){
    return $a['tExport_time'] > $b['tExport_time'];
});

$detail = array_merge($first, $sinopac);
$detail = array_merge($detail, $taishin);

$paybycaseDataTmp = [];
$cId = [];
//隨案結成本拋轉表的順序要同履保費出款日
foreach ($detail as $value) {
    foreach ($paybycase_data as $data) {
        if($value['tCertifiedId'] == $data['fCertifiedId']) {
            if(!in_array($value['tCertifiedId'], $cId)) {
                $paybycaseDataTmp[] = $data;
                $cId[] = $value['tCertifiedId'];
            }
        }
    }
}
$paybycase_data = $paybycaseDataTmp;

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("查詢明細");
$objPHPExcel->getProperties()->setDescription("第一建經案件資料查詢明細結果");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(16);

//調整欄位高度
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(42);

//繪製框線
$objPHPExcel->getActiveSheet()->getStyle('A2:AE2')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('A2:AE2')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);
$objPHPExcel->getActiveSheet()->getStyle('A2:AE2')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);
$objPHPExcel->getActiveSheet()->getStyle('A2:AE2')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);

//總表標題列填色
$objPHPExcel->getActiveSheet()->getStyle('D2:G2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('D2:G2')->getFill()->getStartColor()->setARGB('00DBDCF2');

$objPHPExcel->getActiveSheet()->getStyle('U2:V2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('U2:V2')->getFill()->getStartColor()->setARGB('00DBDCF2');

//設定總表文字置中
$objPHPExcel->getActiveSheet()->getStyle('A:Y')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G1:AE1')->getAlignment()->setWrapText(true);

//設定總表所有案件金額千分位符號
//$objPHPExcel->getActiveSheet()->getStyle('D2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

//設定字型大小
$objPHPExcel->getActiveSheet()->getStyle('A:AE')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A1:AC1')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('AC1')->getFont()->setSize(9);

//設定字型顏色
$objPHPExcel->getActiveSheet()->getStyle('H1:I1')->getFont()->getColor()->setARGB('000070C0');
$objPHPExcel->getActiveSheet()->getStyle('L1')->getFont()->getColor()->setARGB('000070C0');
$objPHPExcel->getActiveSheet()->getStyle('R1:T1')->getFont()->getColor()->setARGB('000070C0');
$objPHPExcel->getActiveSheet()->getStyle('D1:G1')->getFont()->getColor()->setARGB('00FF0000');
$objPHPExcel->getActiveSheet()->getStyle('J1:K1')->getFont()->getColor()->setARGB('00FF0000');
$objPHPExcel->getActiveSheet()->getStyle('N1:P1')->getFont()->getColor()->setARGB('00FF0000');
$objPHPExcel->getActiveSheet()->getStyle('AC1')->getFont()->getColor()->setARGB('00FF0000');

//寫入表頭資料
$objPHPExcel->getActiveSheet()->setCellValue('E1', '銀行入帳金額=A-B-代扣利息'); //應收金額
$objPHPExcel->getActiveSheet()->setCellValue('F1', '利息=B'); //利息出
$objPHPExcel->getActiveSheet()->setCellValue('G1', '應付履約保證費額=A'); //履保費收入總額
$objPHPExcel->getActiveSheet()->setCellValue('H1', '公式算出'); //收入未稅
$objPHPExcel->getActiveSheet()->setCellValue('I1', '公式算出'); //收入稅額
$objPHPExcel->getActiveSheet()->setCellValue('J1', '代扣利息所得稅'); //代扣10%稅款
$objPHPExcel->getActiveSheet()->setCellValue('K1', '代扣利息所得稅'); //代扣2%保費
$objPHPExcel->getActiveSheet()->setCellValue('L1', '公式算出'); //差異數
$objPHPExcel->getActiveSheet()->setCellValue('O1', '代扣佣金所得稅'); //代扣10%稅款
$objPHPExcel->getActiveSheet()->setCellValue('P1', '代扣佣金所得稅'); //代扣2%保費
$objPHPExcel->getActiveSheet()->setCellValue('R1', '公式算出'); //一銀/台新/永豐
$objPHPExcel->getActiveSheet()->setCellValue('S1', '公式算出'); //預付費用(回饋金)
$objPHPExcel->getActiveSheet()->setCellValue('T1', '公式算出'); //差異數

$objPHPExcel->getActiveSheet()->setCellValue('AC1', '金額 = G 欄(履保費收入總額) - AB 欄(回饋成本)'); //差異數

$objPHPExcel->getActiveSheet()->setCellValue('A2', '交易日期');
$objPHPExcel->getActiveSheet()->setCellValue('B2', '序號');
$objPHPExcel->getActiveSheet()->setCellValue('C2', '存入金額');
$objPHPExcel->getActiveSheet()->setCellValue('D2', '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue('E2', '應收金額');
$objPHPExcel->getActiveSheet()->setCellValue('F2', '利息支出');
$objPHPExcel->getActiveSheet()->setCellValue('G2', '履保費收入總額');
$objPHPExcel->getActiveSheet()->setCellValue('H2', '收入未稅');
$objPHPExcel->getActiveSheet()->setCellValue('I2', '收入稅額');
$objPHPExcel->getActiveSheet()->setCellValue('J2', '代扣10%稅款');
$objPHPExcel->getActiveSheet()->setCellValue('K2', '代扣2.11%保費');
$objPHPExcel->getActiveSheet()->setCellValue('L2', '差異數');
$objPHPExcel->getActiveSheet()->setCellValue('M2', '備註');
$objPHPExcel->getActiveSheet()->setCellValue('N2', '代書回饋');
$objPHPExcel->getActiveSheet()->setCellValue('O2', '代扣10%稅款');
$objPHPExcel->getActiveSheet()->setCellValue('P2', '代扣2.11%保費');
$objPHPExcel->getActiveSheet()->setCellValue('Q2', '實付回饋金');
$objPHPExcel->getActiveSheet()->setCellValue('R2', '一銀/台新/永豐');
$objPHPExcel->getActiveSheet()->setCellValue('S2', '預付費用(回饋金)');
$objPHPExcel->getActiveSheet()->setCellValue('T2', '差異數');
$objPHPExcel->getActiveSheet()->setCellValue('U2', '買方身份');
$objPHPExcel->getActiveSheet()->setCellValue('V2', '賣方身份');
$objPHPExcel->getActiveSheet()->setCellValue('W2', '應開發票數');
$objPHPExcel->getActiveSheet()->setCellValue('X2', '仲介類型');
$objPHPExcel->getActiveSheet()->setCellValue('Y2', '最後修改者');
$objPHPExcel->getActiveSheet()->setCellValue('Z2', '案件狀態');
$objPHPExcel->getActiveSheet()->setCellValue('AA2', '尚未匯出回饋金');
$objPHPExcel->getActiveSheet()->setCellValue('AB2', '總回饋成本');
$objPHPExcel->getActiveSheet()->setCellValue('AC2', '淨收入');
$objPHPExcel->getActiveSheet()->setCellValue('AD2', '銀行別');
$objPHPExcel->getActiveSheet()->setCellValue('AE2', '隨案結');

$last_first_count   = 0;
$last_taishin_count = 0;
$last_sinopac_count = 0;

//寫入查詢資料
$max              = count($detail);
$k                = 3; // 起始位置
$no               = 1; //序號
$j                = $k;
for ($i = 0; $i < $max; $i++) {
    if($detail[$i]['certifiedMoneyPaid'] == 1) continue; //只有回饋金的跳過
    //計算10%稅額
    $detail[$i]['paytax'] = $paytax = 0;
    $detail[$i]['paytax'] = payTax($detail[$i]['ownerId'], $detail[$i]['tInterest']);
    $detail[$i]['paytax'] += $paytax;

    //計算2%補充保費
    $detail[$i]['NHITax'] = $NHITax = 0;
    $detail[$i]['NHITax'] = payNHITax($detail[$i]['ownerId'], $detail[$i]['ownerNHI'], $detail[$i]['tInterest']);
    $detail[$i]['NHITax'] += $NHITax;

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, $detail[$i]['tDate']); //交易日期
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $no); //序號
    $no++;
    $amountReceivable = $detail[$i]['tMoney'];
    //正常案件
    if(is_null($detail[$i]['relayFeedBackMoney'])) {
        $deposits = $amountReceivable;
    } else {
        //隨案案件
        $deposits = ($detail[$i]['relayMoney'])?$detail[$i]['relayMoney'] : 0 ;
    }
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $deposits); //存入金額
    $objPHPExcel->getActiveSheet()->getCell('D' . $j)->setValueExplicit($detail[$i]['tCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING); //保證號碼
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $j, $amountReceivable); //應收金額
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $j, $detail[$i]['tInterest']); //利息支出
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $j, $detail[$i]['cCertifiedMoney']); //履保費收入總額

    $money1 = round(($detail[$i]['cCertifiedMoney'] / 1.05), 0);
    $money2 = ($detail[$i]['cCertifiedMoney'] - $money1);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $j, $money1); //公式算出1 = 履保費/1.05
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $j, $money2); //公式算出2 = 履保費-公式算出1
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $j, $detail[$i]['paytax']); //代扣10%稅款
    $objPHPExcel->getActiveSheet()->setCellValue('K' . $j, $detail[$i]['NHITax']); //代扣2%保費

    //差異數(L) = 應收金額(C) + 利息支出(F) - 收入未稅(H) - 收入稅額(I) - 代扣10%稅款(J) - 代扣2%保費(K)
    $money3 = $amountReceivable + $detail[$i]['tInterest'] - $money1 - $money2 - $detail[$i]['paytax'] - $detail[$i]['NHITax'];
    $objPHPExcel->getActiveSheet()->setCellValue('L' . $j, $money3); //差異數

    if($detail[$i]['tObjKind'] == '履保費先收(結案回饋)') {
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $j, '履保費先收未結案'); //備註
    }

    $objPHPExcel->getActiveSheet()->setCellValue('N' . $j, $detail[$i]['scrivenerFeedBackMoney']); //代書回饋
    $objPHPExcel->getActiveSheet()->setCellValue('O' . $j, $detail[$i]['feedbackIncomeTax']); //代扣10%稅款
    $objPHPExcel->getActiveSheet()->setCellValue('P' . $j, $detail[$i]['feedbackNHITax']); //代扣2%保費

    //應收金額 - 存入金額
    $bank = $amountReceivable - $detail[$i]['relayMoney'];
    if ($detail[$i]['scrivenerFeedBackMoney'] == 0) {
        $detail[$i]['relayFeedBackMoney'] = 0;
        $bank                             = 0;
    }
    $objPHPExcel->getActiveSheet()->setCellValue('R' . $j, $bank); //一銀/台新/永豐
    $objPHPExcel->getActiveSheet()->setCellValue('Q' . $j, $detail[$i]['relayFeedBackMoney']); //實付回饋金

    if ($detail[$i]['bankName'] == '第一銀行' and 0 != $detail[$i]['relayFeedBackMoney']) {
        $last_first_count++;
    }
    if ($detail[$i]['bankName'] == '台新銀行' and 0 != $detail[$i]['relayFeedBackMoney']) {
        $last_taishin_count++;
    }
    if ($detail[$i]['bankName'] == '永豐銀行' and 0 != $detail[$i]['relayFeedBackMoney']) {
        $last_sinopac_count++;
    }

    //預付費用(回饋金) = 實付回饋金 + 存入金額 - 應收金額
    $prePayMoney = $detail[$i]['relayFeedBackMoney'] + $detail[$i]['relayMoney'] - $amountReceivable;
    if ($detail[$i]['scrivenerFeedBackMoney'] == 0) {
        $prePayMoney = 0;
    }

    $objPHPExcel->getActiveSheet()->setCellValue('S' . $j, $prePayMoney); //預付費用(回饋金)

    //差異數 = 實付回饋金 - 一銀/台新/永豐 - 預付費用(回饋金)
    $diffMoney = $detail[$i]['relayFeedBackMoney'] - $bank - $prePayMoney;
    $objPHPExcel->getActiveSheet()->setCellValue('T' . $j, $diffMoney); //差異數

    $objPHPExcel->getActiveSheet()->getCell('U' . $j)->setValueExplicit(obj_id($detail[$i]['buyerId']) . $detail[$i]['buyerNo']); //買方
    $objPHPExcel->getActiveSheet()->getCell('V' . $j)->setValueExplicit(obj_id($detail[$i]['ownerId']) . $detail[$i]['ownerNo']); //賣方
    $objPHPExcel->getActiveSheet()->setCellValue('W' . $j, $detail[$i]['invoiceNo']); //應開發票數

    //配件依據 "1.加盟(其他品牌)、2.加盟(台灣房屋)、3.優美、4.直營、5.非仲介成交" 順序掛帳
    $cBrand = '';
    $o      = 0; //加盟--其他品牌
    $t      = 0; //加盟--台灣房屋
    $u      = 0; //優美
    $s      = 0; //直營
    $n      = 0; //非仲介成交

    $bId = $detail[$i]['cBranchNum'];
    if ($bId > 0) { //第一組仲介品牌代號
        countBrand($bId, $o, $t, $u, $s, $n);
    }

    $bId = $detail[$i]['cBranchNum1'];
    if ($bId > 0) { //第二組仲介是否存在
        countBrand($bId, $o, $t, $u, $s, $n);
    }

    $bId = $detail[$i]['cBranchNum2'];
    if ($bId > 0) { //第三組仲介是否存在
        countBrand($bId, $o, $t, $u, $s, $n);
    }

    $bId = $detail[$i]['cBranchNum3'];
    if ($bId > 0) { //第四組仲介是否存在
        countBrand($bId, $o, $t, $u, $s, $n);
    }

    if ($o > 0) {
        $cBrand = '加盟(其他品牌)';
    } else if ($t > 0) {
        $cBrand = '加盟(台灣房屋)';
    } else if ($u > 0) {
        $cBrand = '加盟(優美地產)';
    } else if ($s > 0) {
        $cBrand = '直營';
    } else {
        $cBrand = '非仲介成交';
    }
    ##

    //仲介類型
    $objPHPExcel->getActiveSheet()->setCellValue('X' . $j, $cBrand);

    //最後修改人
    $objPHPExcel->getActiveSheet()->setCellValue('Y' . $j, $detail[$i]['lastmodify']);
    $objPHPExcel->getActiveSheet()->setCellValue('Z' . $j, $detail[$i]['status']);

    //仲介回饋
    $objPHPExcel->getActiveSheet()->setCellValue('AA' . $j, $detail[$i]['branchFeedBackMoney']);

    //總回饋成本
    $objPHPExcel->getActiveSheet()->setCellValue('AB' . $j, $detail[$i]['Feed']);

    //淨收入
    $objPHPExcel->getActiveSheet()->setCellValue('AC' . $j, ($detail[$i]['cCertifiedMoney'] - $detail[$i]['Feed']));

    //銀行別
    $objPHPExcel->getActiveSheet()->setCellValue('AD' . $j, $detail[$i]['bankName']);

    //隨案結
    $isPayBycase = '';
    if($detail[$i]['feedDateCat'] == 2) { $isPayBycase = 'V'; }
    $objPHPExcel->getActiveSheet()->setCellValue('AE' . $j, $isPayBycase);

    if($detail[$i]['tObjKind'] == '履保費先收(結案回饋)') {
        $objPHPExcel->getActiveSheet()->getStyle("A".$j.":AE".$j."")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle("A".$j.":AE".$j."")->getFill()->getStartColor()->setARGB('FFFF00');
    }
    $j++;
}

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('履保費出款日');

require_once dirname(__DIR__) . '/includes/accounting/acc_checklist_feedback_excel.php';
require_once dirname(__DIR__) . '/includes/accounting/acc_checklist_paybycase_excel.php';

$objPHPExcel->setActiveSheetIndex(0);

$_file = 'bankChecklist.xlsx';

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit;
