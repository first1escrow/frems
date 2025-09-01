<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once __DIR__ . '/getBranchType.php';

$_POST = escapeStr($_POST);

$sDate = date('Y-m') . "-01";
$eDate = date('Y-m-d');

//因有未結案先行出履保費所以增加條件[tra.tObjKind = "其他" AND tKind="保證費"]20151113
$sql = 'SELECT cBankAccount,cBankCode FROM tContractBank WHERE cShow = 1;';
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $bank_acc[]  = $rs->fields['cBankAccount'];
    $bank_code[] = $rs->fields['cBankCode'];

    $rs->MoveNext();
}

$bankAccStr .= '"' . implode('","', $bank_acc) . '"';
$bankCodeStr .= implode(',', $bank_code);

$bank_acc = $bank_code = null;
unset($bank_acc, $bank_code);

$data['sinopacCase'] = 0; //永豐
$data['firstCase']   = 0; //一銀
$data['taishinCase'] = 0; //台新

$data['otherCase']    = 0; //其他品牌+非仲介
$data['sinopacMoney'] = 0; //永豐餘額
$data['firstMoney']   = 0; //一銀餘額
$data['taishinMoney'] = 0; //台新餘額

$data['MonthTotalMoney'] = 0; //當月總合計
$data['MonthTotalCount'] = 0; //當月結案數
####

//簽約件數
$sql = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"'; //005030342 電子合約書測試用沒有刪的樣子
$sql .= ' AND cas.cCaseStatus <> "8" ';
$sql .= ' AND ( cas.cSignDate >= "' . $sDate . ' 00:00:00" AND cas.cSignDate <= "' . $eDate . ' 23:59:59")';

$sql = 'SELECT
            cas.cCertifiedId as CertifiedId,
            cas.cBank as bank,
            rea.cBrand as brand,
            rea.cBrand1 as brand1,
            rea.cBrand2 as brand2,
            rea.cBrand2 as brand3,
            rea.cBranchNum as branch,
            rea.cBranchNum1 as branch1,
            rea.cBranchNum2 as branch2,
            rea.cBranchNum3 as branch3
        FROM
            tContractCase AS cas
        LEFT JOIN
            tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
        WHERE
        ' . $sql . '
        GROUP BY
            cas.cCertifiedId;';
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $type = branch_type($conn, $rs->fields);

    if (in_array($type, ['O', '3'])) { //他牌+非仲
        $data['otherCase']++;
    }

    if (in_array($rs->fields['bank'], [8, 81])) {
        $data['firstCase']++;
    } else if (in_array($rs->fields['bank'], [77, 80])) {
        $data['sinopacCase']++;
    } else if (in_array($rs->fields['bank'], [68])) {
        $data['taishinCase']++;
    }

    $type = null;unset($type);

    $rs->MoveNext();
}
###

//買賣總價金
$sql = "SELECT SUM(cTotalMoney) AS totalMoney FROM tContractIncome";
$rs  = $conn->Execute($sql);

$data['totalMoney'] = $rs->fields['totalMoney'];
######

//餘額
//合約銀行基本資料
$_all_balance = 0;
$sql          = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cBankMain,cId DESC;';
$rs           = $conn->Execute($sql);

while (!$rs->EOF) {
    $conBank[] = $rs->fields;
    $rs->MoveNext();
}
$rs = null;unset($rs);

//總利息-總所得稅-利息出款之總金額
$sqlx = 'SELECT * FROM tExpense WHERE eTradeCode IN ("1912","1920","1560","1785");';
$rsx  = $conn->Execute($sqlx);
while (!$rsx->EOF) {
    $_eLender = (int) substr($rsx->fields["eLender"], 0, -2);
    $_eDebit  = (int) substr($rsx->fields["eDebit"], 0, -2);

    $bankInt[$rsx->fields['eAccount']] += ($_eLender - $_eDebit); //cBankTrustAccount

    $rsx->MoveNext();
}
##

//一銀利息與所得稅處理
$sqlx = 'SELECT * FROM tExpense WHERE eAccount IN ("27110352556", "14410531988") AND eTradeCode IN ("1793","1493") AND eDepAccount = "0000000000000000";';
$rsx  = $conn->Execute($sqlx);
while (!$rsx->EOF) {
    $_eLender = (int) substr($rsx->fields["eLender"], 0, -2);
    $_eDebit  = (int) substr($rsx->fields["eDebit"], 0, -2);
    $_t_money = $_t_money + $_eLender - $_eDebit;

    $bankInt[$rsx->fields['eAccount']] += ($_eLender - $_eDebit); //cBankTrustAccount

    $rsx->MoveNext();
}

$rsx = null;unset($rsx);
##

//各合約銀行銀行端與建經系統端餘額資料
$bank_total = 0;
for ($i = 0; $i < count($conBank); $i++) {
    $bMoney = 0; //計算銀行金額

    //各銀行(分行)相關帳務金額
    $sql = 'SELECT
                SUM(cCaseMoney) as total_money
            FROM
                tContractCase
            WHERE
                cBank="' . $conBank[$i]['cBankCode'] . '"
                AND cCaseMoney > 0 ;';
    $rs = $conn->Execute($sql);

    $bMoney = $rs->fields['total_money'] + $bankInt[$conBank[$i]['cBankTrustAccount']] + $conBank[$i]['cBankBalance']; //計算後台總金額

    if (in_array($conBank[$i]['cBankCode'], [8, 81])) {
        $data['firstMoney'] += $bMoney;
    } else if (in_array($conBank[$i]['cBankCode'], [77, 80])) {
        $data['sinopacMoney'] += $bMoney;
    } else if (in_array($conBank[$i]['cBankCode'], [68])) {
        $data['taishinMoney'] += $bMoney;
    }
}
######

//結案數 419464 ->代墊回存  //436979 -> 不能計入保證費
$sql = 'SELECT
            SUM(tra.tMoney)	AS total,
            COUNT(tra.tMoney) AS count
        FROM
            tBankTrans AS tra
        WHERE
            tra.tExport="1"
            AND tra.tPayOk="1"
            AND tra.tExport_nu NOT LIKE "aaaaaaaaaaaa_"
            AND tKind="保證費"
            AND tra.tAccount IN(' . $bankAccStr . ')
            AND (tra.tBankLoansDate>="' . $sDate . '" AND tra.tBankLoansDate<="' . $eDate . '") AND tra.tId NOT IN(419464,436979,451978,739171);';
$rs = $conn->Execute($sql);

// 當月總合計($$$)
$data['MonthTotalMoney'] = (int) $rs->fields['total'];
$data['MonthTotalCount'] = (int) $rs->fields['count'];
##

//無履保費出款但有出利息
$sql = 'SELECT
            cas.cCertifiedId,
            (SELECT cInterestMoney FROM tContractBuyer WHERE cCertifiedId = cas.cCertifiedId) AS bInterestMoney,
            (SELECT cInterestMoney FROM tContractOwner WHERE cCertifiedId = cas.cCertifiedId) AS oInterestMoney,
            (SELECT SUM(cInterestMoney) FROM tContractOthers WHERE cCertifiedId = cas.cCertifiedId GROUP BY cCertifiedId) AS otherInterestMoney,
            (SELECT SUM(cInterestMoney) FROM tContractInterestExt WHERE cCertifiedId = cas.cCertifiedId) AS exInterestMoney,
            (SELECT cCertifiedMoney FROM tContractIncome WHERE cCertifiedId = cas.cCertifiedId) AS cCertifiedMoney
        FROM
            tContractCase AS cas
        WHERE
            cas.cBankList>="' . $sDate . '"
            AND cas.cBankList<="' . $eDate . '"
            AND cas.cBankList<>""
            AND cas.cBank IN (' . $bankCodeStr . ')
            AND cas.cCertifiedId NOT IN("060316077");';
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $int = $rs->fields['bInterestMoney'] + $rs->fields['oInterestMoney'] + $rs->fields['otherInterestMoney'] + $rs->fields['exInterestMoney'];

    if ($rs->fields['cCertifiedMoney'] >= $int) { //保證費大於利息
        $data['MonthTotalCount']++;
        $data['MonthTotalMoney'] -= ($rs->fields['bInterestMoney'] + $rs->fields['oInterestMoney'] + $rs->fields['otherInterestMoney'] + $rs->fields['exInterestMoney']);
    } else {
        $data['MonthTotalMoney'] -= ($int - $rs->fields['cCertifiedMoney']);
    }

    $rs->MoveNext();
}
##

if (date('Y-m') == '2022-03') { //101494851 原本只收64475 但是收到*2  所以又退回去
    $data['MonthTotalMoney'] -= 64475;
}

if (date('Y-m') == '2022-03') { //090037779 溢收退回6000
    $data['MonthTotalMoney'] -= 6000;
}

if (date('Y-m') == '2022-03') { //101030466這件退3060給客戶
    $data['MonthTotalMoney'] -= 3060;
}

if (date('Y-m') == '2022-04') { //3/31 10116467這件履保費後來退一半 所以4/1的後台要扣掉22014
    $data['MonthTotalMoney'] -= 22014;
}

$extraMoney = $data['MonthTotalMoney']; //誤入金額
##

$smarty->assign("sDate", $sDate);
$smarty->assign("eDate", $eDate);
$smarty->assign("data", $data);
$smarty->display('totalReport.inc.tpl', '', 'report');
