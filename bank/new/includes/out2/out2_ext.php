<?php

$sql = 'SELECT
            a.cCertifiedId,
            a.cCaseMoney,
            b.cBankKey2 as o_bk1,
            b.cBankBranch2 as o_bk2,
            b.cBankAccName as o_bkname,
            b.cBankAccNumber as o_bknumber,
            b.cName as owner,
            b.cChecklistBank as o_ChecklistBank,
            b.cBankMoney as o_cBankMoney,
            c.cBankKey2 as b_bk1,
            c.cBankBranch2 as b_bk2,
            c.cBankAccName as b_bkname,
            c.cBankAccNumber as b_bknumber,
            c.cName as buyer,
            c.cChecklistBank as b_ChecklistBank,
            d.cBranchNum as cBranchNum,
            d.cBranchNum1 as cBranchNum1,
            d.cBranchNum2 as cBranchNum2,
            d.cBranchNum3 as cBranchNum3,
            d.cServiceTarget as cServiceTarget,
            d.cServiceTarget1 as cServiceTarget1,
            d.cServiceTarget2 as cServiceTarget2,
            d.cServiceTarget3 as cServiceTarget3,
            f.sAccountNum1,
            f.sAccountNum2,
            f.sAccount3,
            f.sAccount4,
            f.sAccountNum11,
            f.sAccountNum21,
            f.sAccount31,
            f.sAccount41,
            f.sAccountNum12,
            f.sAccountNum22,
            f.sAccount32,
            f.sAccount42,
            f.sEmail,
            f.sAccountUnused,
            f.sAccountUnused1,
            f.sAccountUnused2,
            (SELECT bName FROM tBrand WHERE bId = g.bBrand) AS bBrand,
            g.bName,
            g.bStore,
            g.bAccountNum1,
            g.bAccountNum2,
            g.bAccount3,
            g.bAccount4,
            g.bAccountNum11,
            g.bAccountNum21,
            g.bAccount31,
            g.bAccount41,
            g.bAccountNum12,
            g.bAccountNum22,
            g.bAccount32,
            g.bAccount42,
            g.bAccountNum13,
            g.bAccountNum23,
            g.bAccount33,
            g.bAccount43,
            g.bAccountUnused,
            g.bAccountUnused1,
            g.bAccountUnused2,
            g.bAccountUnused3,
            CONCAT(g.bFaxArea,g.bFaxMain) as store_fax,
            g.bEmail as store_email,
            g.bFaxDefault,
            (SELECT bName FROM tBrand WHERE bId = h.bBrand) AS bBrandA,
            h.bName AS bNameA,
            h.bStore as bStoreA,
            h.bAccountNum1 as bAccountNum1A,
            h.bAccountNum2 as bAccountNum2A,
            h.bAccount3 as bAccount3A,
            h.bAccount4 as bAccount4A,
            h.bAccountNum11 as bAccountNum11A,
            h.bAccountNum21 as bAccountNum21A,
            h.bAccount31 as bAccount31A,
            h.bAccount41 as bAccount41A,
            h.bAccountNum12 as bAccountNum12A,
            h.bAccountNum22 as bAccountNum22A,
            h.bAccount32 as bAccount32A,
            h.bAccount42 as bAccount42A,
            h.bAccountNum13 as bAccountNum13A,
            h.bAccountNum23 as bAccountNum23A,
            h.bAccount33 as bAccount33A,
            h.bAccount43 as bAccount43A,
            h.bAccountUnused as bAccountUnusedA,
            h.bAccountUnused1 as bAccountUnused1A,
            h.bAccountUnused2 as bAccountUnused2A,
            h.bAccountUnused3 as bAccountUnused3A,
            CONCAT(h.bFaxArea,h.bFaxMain) as store_faxA,
            h.bEmail as store_emailA,
            h.bFaxDefault as bFaxDefaultA,
            (SELECT bName FROM tBrand WHERE bId = i.bBrand) AS bBrandB,
            i.bName AS bNameB,
            i.bStore as bStoreB,
            i.bAccountNum1 as bAccountNum1B,
            i.bAccountNum2 as bAccountNum2B,
            i.bAccount3 as bAccount3B,
            i.bAccount4 as bAccount4B,
            i.bAccountNum11 as bAccountNum11B,
            i.bAccountNum21 as bAccountNum21B,
            i.bAccount31 as bAccount31B,
            i.bAccount41 as bAccount41B,
            i.bAccountNum12 as bAccountNum12B,
            i.bAccountNum22 as bAccountNum22B,
            i.bAccount32 as bAccount32B,
            i.bAccount42 as bAccount42B,
            i.bAccountNum13 as bAccountNum13B,
            i.bAccountNum23 as bAccountNum23B,
            i.bAccount33 as bAccount33B,
            i.bAccount43 as bAccount43B,
            i.bAccountUnused as bAccountUnusedB,
            i.bAccountUnused1 as bAccountUnused1B,
            i.bAccountUnused2 as bAccountUnused2B,
            i.bAccountUnused3 as bAccountUnused3B,
            CONCAT(i.bFaxArea,i.bFaxMain) as store_faxB,
            i.bEmail as store_emailB,
            i.bFaxDefault as bFaxDefaultB,
            (SELECT bName FROM tBrand WHERE bId = j.bBrand) AS bBrandC,
            j.bName AS bNameC,
            j.bStore as bStoreC,
            j.bAccountNum1 as bAccountNum1C,
            j.bAccountNum2 as bAccountNum2C,
            j.bAccount3 as bAccount3C,
            j.bAccount4 as bAccount4C,
            j.bAccountNum11 as bAccountNum11C,
            j.bAccountNum21 as bAccountNum21C,
            j.bAccount31 as bAccount31C,
            j.bAccount41 as bAccount41C,
            j.bAccountNum12 as bAccountNum12C,
            j.bAccountNum22 as bAccountNum22C,
            j.bAccount32 as bAccount32C,
            j.bAccount42 as bAccount42C,
            j.bAccountNum13 as bAccountNum13C,
            j.bAccountNum23 as bAccountNum23C,
            j.bAccount33 as bAccount33C,
            j.bAccount43 as bAccount43C,
            j.bAccountUnused as bAccountUnusedC,
            j.bAccountUnused1 as bAccountUnused1C,
            j.bAccountUnused2 as bAccountUnused2C,
            j.bAccountUnused3 as bAccountUnused3C,
            CONCAT(j.bFaxArea,j.bFaxMain) as store_faxC,
            j.bEmail as store_emailC,
            j.bFaxDefault as bFaxDefaultC,
            ci.cCertifiedMoney,
            ci.cTotalMoney,
            ci.cCommitmentMoney,
            e.cScrivener
        FROM
            tContractCase AS a
        LEFT JOIN
            tContractOwner AS b ON a.cCertifiedId = b.cCertifiedId
        LEFT JOIN
            tContractBuyer AS c ON a.cCertifiedId = c.cCertifiedId
        LEFT JOIN
            tContractRealestate AS d ON a.cCertifiedId = d.cCertifyId
        LEFT JOIN
            tContractScrivener AS e ON a.cCertifiedId = e.cCertifiedId
        LEFT JOIN
            tScrivener AS f ON e.cScrivener = f.sId
        LEFT JOIN
            tBranch AS g ON d.cBranchNum = g.bId
        LEFT JOIN
            tBranch AS h ON d.cBranchNum1 = h.bId
        LEFT JOIN
            tBranch AS i ON d.cBranchNum2 = i.bId
        LEFT JOIN
            tBranch AS j ON d.cBranchNum3 = j.bId
        LEFT JOIN
            tContractIncome AS ci ON a.cCertifiedId=ci.cCertifiedId
        WHERE
            a.cCertifiedId="' . $_vr_code . '";';
$rs = $conn->Execute($sql);

$total_money     = $rs->fields['cTotalMoney'];
$CertifiedMoney  = $rs->fields['cCertifiedMoney'];
$CommitmentMoney = $rs->fields['cCommitmentMoney'];

##賣方
//主賣方
$owner      = mb_substr(n_to_w(trim($rs->fields['owner'])), 0, 9);
$ownerArr[] = $rs->fields['owner']; //比對身分用

$ownerBankCount = 0;
if ($rs->fields["o_ChecklistBank"] == 0) {
    $ownerBankNameArr[] = trim($rs->fields["o_bkname"]); //比對戶名用

    $ownerBank[$ownerBankCount]['bank']        = trim($rs->fields["o_bk1"]);
    $ownerBank[$ownerBankCount]['bankBranch']  = trim($rs->fields["o_bk2"]);
    $ownerBank[$ownerBankCount]['bankAccName'] = trim($rs->fields["o_bkname"]);
    $ownerBank[$ownerBankCount]['bankAccNum']  = trim($rs->fields["o_bknumber"]);
    $ownerBank[$ownerBankCount]['bankMoney']   = trim($rs->fields["o_cBankMoney"]);
    $ownerBankCount++;
}
##

##買方
//主買方
$buyer          = mb_substr(n_to_w(trim($rs->fields['buyer'])), 0, 9);
$buyerBankCount = 0;
if ($rs->fields["b_ChecklistBank"] == 0) {
    $buyerBank[$buyerBankCount]['bank']        = trim($rs->fields["b_bk1"]);
    $buyerBank[$buyerBankCount]['bankBranch']  = trim($rs->fields["b_bk2"]);
    $buyerBank[$buyerBankCount]['bankAccName'] = trim($rs->fields["b_bkname"]);
    $buyerBank[$buyerBankCount]['bankAccNum']  = trim($rs->fields["b_bknumber"]);
    $buyerBankCount++;
}

//其他買賣方
$sql = 'SELECT
            cName,
            cIdentity,
            cBankAccName,
            cBankAccNum,
            cBankMain,
            cBankBranch,
            cChecklistBank,
            cBankMoney
        FROM
            tContractOthers
        WHERE
            cCertifiedId="' . $_vr_code . '"
            AND cChecklistBank = 0
            AND (cIdentity="1" OR cIdentity="2")
        ORDER BY
            cId
        ASC;';
$rsB = $conn->Execute($sql);

while (!$rsB->EOF) {
    if ($rsB->fields['cIdentity'] == 2) {
        $ownerArr[]                                = $rsB->fields['cName']; //比對身分用
        $ownerBank[$ownerBankCount]['bank']        = trim($rsB->fields["cBankMain"]);
        $ownerBank[$ownerBankCount]['bankBranch']  = trim($rsB->fields["cBankBranch"]);
        $ownerBank[$ownerBankCount]['bankAccName'] = trim($rsB->fields["cBankAccName"]);
        $ownerBank[$ownerBankCount]['bankAccNum']  = trim($rsB->fields["cBankAccNum"]);
        $ownerBank[$ownerBankCount]['bankMoney']   = trim($rsB->fields["cBankMoney"]);
        $ownerBankCount++;
    } else if ($rsB->fields['cIdentity'] == 1) {
        $buyerBank[$buyerBankCount]['bank']        = trim($rsB->fields["cBankMain"]);
        $buyerBank[$buyerBankCount]['bankBranch']  = trim($rsB->fields["cBankBranch"]);
        $buyerBank[$buyerBankCount]['bankAccName'] = trim($rsB->fields["cBankAccName"]);
        $buyerBank[$buyerBankCount]['bankAccNum']  = trim($rsB->fields["cBankAccNum"]);
        $buyerBankCount++;
    }

    $rsB->MoveNext();
}
##

//代書
$scrivenerBankCount = 0;
$_s_email           = trim($rs->fields["sEmail"]);

if ($rs->fields['sAccountUnused'] != 1 && $rs->fields["sAccountNum1"]) {
    $scrivenerBank[$scrivenerBankCount]['bank']        = trim($rs->fields["sAccountNum1"]);
    $scrivenerBank[$scrivenerBankCount]['bankBranch']  = trim($rs->fields["sAccountNum2"]);
    $scrivenerBank[$scrivenerBankCount]['bankAccName'] = trim($rs->fields["sAccount4"]);
    $scrivenerBank[$scrivenerBankCount]['bankAccNum']  = trim($rs->fields["sAccount3"]);
    $scrivenerBankCount++;
}

if ($rs->fields['sAccountUnused1'] != 1 && $rs->fields["sAccountNum11"]) {
    $scrivenerBank[$scrivenerBankCount]['bank']        = trim($rs->fields["sAccountNum11"]);
    $scrivenerBank[$scrivenerBankCount]['bankBranch']  = trim($rs->fields["sAccountNum21"]);
    $scrivenerBank[$scrivenerBankCount]['bankAccName'] = trim($rs->fields["sAccount41"]);
    $scrivenerBank[$scrivenerBankCount]['bankAccNum']  = trim($rs->fields["sAccount31"]);
    $scrivenerBankCount++;
}

if ($rs->fields['sAccountUnused2'] != 1 && $rs->fields["sAccountNum12"]) {
    $scrivenerBank[$scrivenerBankCount]['bank']        = trim($rs->fields["sAccountNum12"]);
    $scrivenerBank[$scrivenerBankCount]['bankBranch']  = trim($rs->fields["sAccountNum22"]);
    $scrivenerBank[$scrivenerBankCount]['bankAccName'] = trim($rs->fields["sAccount42"]);
    $scrivenerBank[$scrivenerBankCount]['bankAccNum']  = trim($rs->fields["sAccount32"]);
    $scrivenerBankCount++;
}

$sql   = "SELECT * FROM tScrivenerBank WHERE sUnUsed  = 0 AND sScrivener ='" . $rs->fields['cScrivener'] . "'";
$rs_ss = $conn->Execute($sql);

while (!$rs_ss->EOF) {
    $scrivenerBank[$scrivenerBankCount]['bank']        = trim($rs_ss->fields["sBankMain"]);
    $scrivenerBank[$scrivenerBankCount]['bankBranch']  = trim($rs_ss->fields["sBankBranch"]);
    $scrivenerBank[$scrivenerBankCount]['bankAccName'] = trim($rs_ss->fields["sBankAccountName"]);
    $scrivenerBank[$scrivenerBankCount]['bankAccNum']  = trim($rs_ss->fields["sBankAccountNo"]);
    $scrivenerBankCount++;

    $rs_ss->MoveNext();
}
##

//第一家仲介
$storeName     = $rs->fields['bBrand'] . "_" . $rs->fields['bStore'] . "_" . $rs->fields['bName']; //店名
$storeId       = $rs->fields['cBranchNum'];
$_store_fax    = ($rs->fields['bFaxDefault'] == 1) ? '' : trim($rs->fields["store_fax"]);
$_store_email  = trim($rs->fields["store_email"]);
$_store_target = '';
if ($rs->fields['cBranchNum'] > 0) {
    $_store_target = trim($rs->fields['cServiceTarget']);
}
$branchBankCount = 0;

if ($rs->fields['bAccountUnused'] != 1 && $rs->fields["bAccountNum1"]) {
    $branchBank[$branchBankCount]['bank']        = trim($rs->fields["bAccountNum1"]); // 店家 (第一家總行)
    $branchBank[$branchBankCount]['bankBranch']  = trim($rs->fields["bAccountNum2"]); // (第一家分行)
    $branchBank[$branchBankCount]['bankAccName'] = trim($rs->fields["bAccount4"]); // (第一家戶名)
    $branchBank[$branchBankCount]['bankAccNum']  = trim($rs->fields["bAccount3"]); // (第一家帳號)
    $branchBankCount++;
}

if ($rs->fields['bAccountUnused1'] != 1 && $rs->fields["bAccountNum11"]) {
    $branchBank[$branchBankCount]['bank']        = trim($rs->fields["bAccountNum11"]); // 店家 (第一家總行)
    $branchBank[$branchBankCount]['bankBranch']  = trim($rs->fields["bAccountNum21"]); // (第一家分行)
    $branchBank[$branchBankCount]['bankAccName'] = trim($rs->fields["bAccount41"]); // (第一家戶名)
    $branchBank[$branchBankCount]['bankAccNum']  = trim($rs->fields["bAccount31"]); // (第一家帳號)
    $branchBankCount++;
}

if ($rs->fields['bAccountUnused2'] != 1 && $rs->fields["bAccountNum12"]) {
    $branchBank[$branchBankCount]['bank']        = trim($rs->fields["bAccountNum12"]); // 店家 (第一家總行)
    $branchBank[$branchBankCount]['bankBranch']  = trim($rs->fields["bAccountNum22"]); // (第一家分行)
    $branchBank[$branchBankCount]['bankAccName'] = trim($rs->fields["bAccount42"]); // (第一家戶名)
    $branchBank[$branchBankCount]['bankAccNum']  = trim($rs->fields["bAccount32"]); // (第一家帳號)
    $branchBankCount++;
}

if ($rs->fields['bAccountUnused3'] != 1 && $rs->fields["bAccountNum13"]) {
    $branchBank[$branchBankCount]['bank']        = trim($rs->fields["bAccountNum13"]); // 店家 (第一家總行)
    $branchBank[$branchBankCount]['bankBranch']  = trim($rs->fields["bAccountNum23"]); // (第一家分行)
    $branchBank[$branchBankCount]['bankAccName'] = trim($rs->fields["bAccount43"]); // (第一家戶名)
    $branchBank[$branchBankCount]['bankAccNum']  = trim($rs->fields["bAccount33"]); // (第一家帳號)
    $branchBankCount++;
}

##

//第二家房仲介(A)
$storeName1       = $rs->fields['bBrandA'] . "_" . $rs->fields['bStoreA'] . "_" . $rs->fields['bNameA']; //店名
$storeId1         = $rs->fields['cBranchNum1'];
$_store_faxA      = ($rs->fields['bFaxDefaultA'] == 1) ? '' : trim($rs->fields["store_faxA"]);
$_store_emailA    = trim($rs->fields["store_emailA"]);
$branchBankCount1 = 0;
$_store_target1   = '';

if ($rs->fields['cBranchNum1'] > 0) {
    $_store_target1 = trim($rs->fields['cServiceTarget1']);
}

if ($rs->fields['bAccountUnusedA'] != 1 && $rs->fields["bAccountNum1A"]) {
    $branchBank1[$branchBankCount1]['bank']        = trim($rs->fields["bAccountNum1A"]); // 店家 (第二家總行)
    $branchBank1[$branchBankCount1]['bankBranch']  = trim($rs->fields["bAccountNum2A"]); // (第二家分行)
    $branchBank1[$branchBankCount1]['bankAccName'] = trim($rs->fields["bAccount4A"]); // (第二家戶名)
    $branchBank1[$branchBankCount1]['bankAccNum']  = trim($rs->fields["bAccount3A"]); // (第二家帳號)
    $branchBankCount1++;
}

if ($rs->fields['bAccountUnused1A'] != 1 && $rs->fields["bAccountNum11A"]) {
    $branchBank1[$branchBankCount1]['bank']        = trim($rs->fields["bAccountNum11A"]); // 店家 (第二家總行)
    $branchBank1[$branchBankCount1]['bankBranch']  = trim($rs->fields["bAccountNum21A"]); // (第二家分行)
    $branchBank1[$branchBankCount1]['bankAccName'] = trim($rs->fields["bAccount41A"]); // (第二家戶名)
    $branchBank1[$branchBankCount1]['bankAccNum']  = trim($rs->fields["bAccount31A"]); // (第二家帳號)
    $branchBankCount1++;
}

if ($rs->fields['bAccountUnused2A'] != 1 && $rs->fields["bAccountNum12A"]) {
    $branchBank1[$branchBankCount1]['bank']        = trim($rs->fields["bAccountNum12A"]); // 店家 (第二家總行)
    $branchBank1[$branchBankCount1]['bankBranch']  = trim($rs->fields["bAccountNum22A"]); // (第二家分行)
    $branchBank1[$branchBankCount1]['bankAccName'] = trim($rs->fields["bAccount42A"]); // (第二家戶名)
    $branchBank1[$branchBankCount1]['bankAccNum']  = trim($rs->fields["bAccount32A"]); // (第二家帳號)
    $branchBankCount1++;
}

if ($rs->fields['bAccountUnused3A'] != 1 && $rs->fields["bAccountNum13A"]) {
    $branchBank1[$branchBankCount1]['bank']        = trim($rs->fields["bAccountNum13A"]); // 店家 (第二家總行)
    $branchBank1[$branchBankCount1]['bankBranch']  = trim($rs->fields["bAccountNum23A"]); // (第二家分行)
    $branchBank1[$branchBankCount1]['bankAccName'] = trim($rs->fields["bAccount43A"]); // (第二家戶名)
    $branchBank1[$branchBankCount1]['bankAccNum']  = trim($rs->fields["bAccount33A"]); // (第二家帳號)
    $branchBankCount1++;
}
##

//第三家房仲介(B)
$storeName2 = $rs->fields['bBrandB'] . "_" . $rs->fields['bStoreB'] . "_" . $rs->fields['bNameB']; //店名
$storeId2   = $rs->fields['cBranchNum2'];

$_store_faxB   = ($rs->fields['bFaxDefaultB'] == 1) ? '' : trim($rs->fields["store_faxB"]);
$_store_emailB = trim($rs->fields["store_emailB"]);

$_store_target2 = '';
if ($rs->fields['cBranchNum2'] > 0) {
    $_store_target2 = trim($rs->fields['cServiceTarget2']);
}

$branchBankCount2 = 0;
if ($rs->fields['bAccountUnusedB'] != 1 && $rs->fields["bAccountNum1B"]) {
    $branchBank2[$branchBankCount2]['bank']        = trim($rs->fields["bAccountNum1B"]); // 店家 (第二家總行)
    $branchBank2[$branchBankCount2]['bankBranch']  = trim($rs->fields["bAccountNum2B"]); // (第二家分行)
    $branchBank2[$branchBankCount2]['bankAccName'] = trim($rs->fields["bAccount4B"]); // (第二家戶名)
    $branchBank2[$branchBankCount2]['bankAccNum']  = trim($rs->fields["bAccount3B"]); // (第二家帳號)
    $branchBankCount2++;
}

if ($rs->fields['bAccountUnused1B'] != 1 && $rs->fields["bAccountNum11B"]) {
    $branchBank2[$branchBankCount2]['bank']        = trim($rs->fields["bAccountNum11B"]); // 店家 (第二家總行)
    $branchBank2[$branchBankCount2]['bankBranch']  = trim($rs->fields["bAccountNum21B"]); // (第二家分行)
    $branchBank2[$branchBankCount2]['bankAccName'] = trim($rs->fields["bAccount41B"]); // (第二家戶名)
    $branchBank2[$branchBankCount2]['bankAccNum']  = trim($rs->fields["bAccount31B"]); // (第二家帳號)
    $branchBankCount2++;
}

if ($rs->fields['bAccountUnused2B'] != 1 && $rs->fields["bAccountNum12B"]) {
    $branchBank2[$branchBankCount2]['bank']        = trim($rs->fields["bAccountNum12B"]); // 店家 (第二家總行)
    $branchBank2[$branchBankCount2]['bankBranch']  = trim($rs->fields["bAccountNum22B"]); // (第二家分行)
    $branchBank2[$branchBankCount2]['bankAccName'] = trim($rs->fields["bAccount42B"]); // (第二家戶名)
    $branchBank2[$branchBankCount2]['bankAccNum']  = trim($rs->fields["bAccount32B"]); // (第二家帳號)
    $branchBankCount2++;
}

if ($rs->fields['bAccountUnused3B'] != 1 && $rs->fields["bAccountNum13B"]) {
    $branchBank2[$branchBankCount2]['bank']        = trim($rs->fields["bAccountNum13B"]); // 店家 (第二家總行)
    $branchBank2[$branchBankCount2]['bankBranch']  = trim($rs->fields["bAccountNum23B"]); // (第二家分行)
    $branchBank2[$branchBankCount2]['bankAccName'] = trim($rs->fields["bAccount43B"]); // (第二家戶名)
    $branchBank2[$branchBankCount2]['bankAccNum']  = trim($rs->fields["bAccount33B"]); // (第二家帳號)
    $branchBankCount2++;
}

//第四家房仲介(C)
$storeName3       = $rs->fields['bBrandC'] . "_" . $rs->fields['bStoreC'] . "_" . $rs->fields['bNameC']; //店名
$storeId3         = $rs->fields['cBranchNum3'];
$branchBankCount3 = 0;

$_store_faxC   = ($rs->fields['bFaxDefault'] == 1) ? '' : trim($rs->fields["store_faxC"]);
$_store_emailC = trim($rs->fields["store_emailC"]);

$_store_target3 = '';
if ($rs->fields['cBranchNum3'] > 0) {
    $_store_target3 = trim($rs->fields['cServiceTarget3']);
}

if ($rs->fields['bAccountUnusedC'] != 1 && $rs->fields["bAccountNum1C"]) {
    $branchBank3[$branchBankCount3]['bank']        = trim($rs->fields["bAccountNum1C"]); // 店家 (第二家總行)
    $branchBank3[$branchBankCount3]['bankBranch']  = trim($rs->fields["bAccountNum2C"]); // (第二家分行)
    $branchBank3[$branchBankCount3]['bankAccName'] = trim($rs->fields["bAccount4C"]); // (第二家戶名)
    $branchBank3[$branchBankCount3]['bankAccNum']  = trim($rs->fields["bAccount3C"]); // (第二家帳號)
    $branchBankCount3++;
}

if ($rs->fields['bAccountUnused1C'] != 1 && $rs->fields["bAccountNum11C"]) {
    $branchBank3[$branchBankCount3]['bank']        = trim($rs->fields["bAccountNum11C"]); // 店家 (第二家總行)
    $branchBank3[$branchBankCount3]['bankBranch']  = trim($rs->fields["bAccountNum21C"]); // (第二家分行)
    $branchBank3[$branchBankCount3]['bankAccName'] = trim($rs->fields["bAccount41C"]); // (第二家戶名)
    $branchBank3[$branchBankCount3]['bankAccNum']  = trim($rs->fields["bAccount31C"]); // (第二家帳號)
    $branchBankCount3++;
}

if ($rs->fields['bAccountUnused2C'] != 1 && $rs->fields["bAccountNum12C"]) {
    $branchBank3[$branchBankCount3]['bank']        = trim($rs->fields["bAccountNum12C"]); // 店家 (第二家總行)
    $branchBank3[$branchBankCount3]['bankBranch']  = trim($rs->fields["bAccountNum22C"]); // (第二家分行)
    $branchBank3[$branchBankCount3]['bankAccName'] = trim($rs->fields["bAccount42C"]); // (第二家戶名)
    $branchBank3[$branchBankCount3]['bankAccNum']  = trim($rs->fields["bAccount32C"]); // (第二家帳號)
    $branchBankCount3++;
}

if ($rs->fields['bAccountUnused3C'] != 1 && $rs->fields["bAccountNum13C"]) {
    $branchBank3[$branchBankCount3]['bank']        = trim($rs->fields["bAccountNum13C"]); // 店家 (第二家總行)
    $branchBank3[$branchBankCount3]['bankBranch']  = trim($rs->fields["bAccountNum23C"]); // (第二家分行)
    $branchBank3[$branchBankCount3]['bankAccName'] = trim($rs->fields["bAccount43C"]); // (第二家戶名)
    $branchBank3[$branchBankCount3]['bankAccNum']  = trim($rs->fields["bAccount33C"]); // (第二家帳號)
    $branchBankCount3++;
}

$sql   = "SELECT * FROM tBranchBank WHERE bBranch ='" . $rs->fields["cBranchNum"] . "' OR bBranch = '" . $rs->fields["cBranchNum1"] . "' OR bBranch = '" . $rs->fields["cBranchNum2"] . "' OR bBranch = '" . $rs->fields["cBranchNum3"] . "'";
$rs_bb = $conn->Execute($sql);

while (!$rs_bb->EOF) {
    if ($rs_bb->fields['bUnUsed'] == 0) {
        if ($rs_bb->fields['bBranch'] == $rs->fields['cBranchNum']) {
            $branchBank[$branchBankCount]['bank']        = trim($rs_bb->fields["bBankMain"]); // 店家 (第一家總行)
            $branchBank[$branchBankCount]['bankBranch']  = trim($rs_bb->fields["bBankBranch"]); // (第一家分行)
            $branchBank[$branchBankCount]['bankAccName'] = trim($rs_bb->fields["bBankAccountName"]); // (第一家戶名)
            $branchBank[$branchBankCount]['bankAccNum']  = trim($rs_bb->fields["bBankAccountNo"]); // (第一家帳號)
            $branchBankCount++;
        } else if ($rs_bb->fields['bBranch'] == $rs->fields['cBranchNum1']) {
            $branchBank1[$branchBankCount1]['bank']        = trim($rs_bb->fields["bBankMain"]); // 店家 (第二家總行)
            $branchBank1[$branchBankCount1]['bankBranch']  = trim($rs_bb->fields["bBankBranch"]); // (第二家分行)
            $branchBank1[$branchBankCount1]['bankAccName'] = trim($rs_bb->fields["bBankAccountName"]); // (第二家戶名)
            $branchBank1[$branchBankCount1]['bankAccNum']  = trim($rs_bb->fields["bBankAccountNo"]); // (第二家帳號)
            $branchBankCount1++;
        } else if ($rs_bb->fields['bBranch'] == $rs->fields['cBranchNum2']) {
            $branchBank2[$branchBankCount2]['bank']        = trim($rs_bb->fields["bBankMain"]); // 店家 (第二家總行)
            $branchBank2[$branchBankCount2]['bankBranch']  = trim($rs_bb->fields["bBankBranch"]); // (第二家分行)
            $branchBank2[$branchBankCount2]['bankAccName'] = trim($rs_bb->fields["bBankAccountName"]); // (第二家戶名)
            $branchBank2[$branchBankCount2]['bankAccNum']  = trim($rs_bb->fields["bBankAccountNo"]); // (第二家帳號)
            $branchBankCount2++;
        } else if ($rs_bb->fields['bBranch'] == $rs->fields['cBranchNum3']) {
            $branchBank3[$branchBankCount3]['bank']        = trim($rs_bb->fields["bBankMain"]); // 店家 (第二家總行)
            $branchBank3[$branchBankCount3]['bankBranch']  = trim($rs_bb->fields["bBankBranch"]); // (第二家分行)
            $branchBank3[$branchBankCount3]['bankAccName'] = trim($rs_bb->fields["bBankAccountName"]); // (第二家戶名)
            $branchBank3[$branchBankCount3]['bankAccNum']  = trim($rs_bb->fields["bBankAccountNo"]); // (第二家帳號)
            $branchBankCount3++;
        }
    }

    $rs_bb->MoveNext();
}
##

//其他新增的帳戶
$sql = "SELECT * FROM tContractCustomerBank WHERE cCertifiedId='" . $_vr_code . "' AND cChecklistBank = 0 ORDER BY cIdentity ASC";
$rsb = $conn->Execute($sql);

while (!$rsb->EOF) {
    if ($rsb->fields['cIdentity'] == 2 || $rsb->fields['cIdentity'] == 52) {
        if ($rsb->fields['cIdentity'] == 2) { //賣方
            $ownerBankNameArr[] = trim($rsb->fields["cBankAccountName"]); //比對戶名用
        }

        $ownerBank[$ownerBankCount]['bank']        = trim($rsb->fields["cBankMain"]);
        $ownerBank[$ownerBankCount]['bankBranch']  = trim($rsb->fields["cBankBranch"]);
        $ownerBank[$ownerBankCount]['bankAccName'] = trim($rsb->fields["cBankAccountName"]);
        $ownerBank[$ownerBankCount]['bankAccNum']  = trim($rsb->fields["cBankAccountNo"]);
        $ownerBank[$ownerBankCount]['bankMoney']   = trim($rsb->fields["cBankMoney"]);
        $ownerBankCount++;
    } else if ($rsb->fields['cIdentity'] == 1 || $rsb->fields['cIdentity'] == 53) {
        $buyerBank[$buyerBankCount]['bank']        = trim($rsb->fields["cBankMain"]);
        $buyerBank[$buyerBankCount]['bankBranch']  = trim($rsb->fields["cBankBranch"]);
        $buyerBank[$buyerBankCount]['bankAccName'] = trim($rsb->fields["cBankAccountName"]);
        $buyerBank[$buyerBankCount]['bankAccNum']  = trim($rsb->fields["cBankAccountNo"]);
        $buyerBankCount++;
    } elseif ($rsb->fields['cIdentity'] == 3) {
        ##非仲介成交可能會有仲介人需要出服務費給他
        $branchBank[$branchBankCount]['bank']        = trim($rsb->fields["cBankMain"]); // 店家 ( 總行)
        $branchBank[$branchBankCount]['bankBranch']  = trim($rsb->fields["cBankBranch"]); // ( 分行)
        $branchBank[$branchBankCount]['bankAccName'] = trim($rsb->fields["cBankAccountName"]); // ( 戶名)
        $branchBank[$branchBankCount]['bankAccNum']  = trim($rsb->fields["cBankAccountNo"]); // ( 帳號)
        $branchBankCount++;
    }

    $rsb->MoveNext();
}
##

###

//查詢是否曾出款"仲介服務費"
$sql           = 'SELECT * FROM tBankTrans WHERE tVR_Code="' . $vr_code . '" AND tObjKind="仲介服務費";';
$_rs           = $conn->Execute($sql);
$realty_charge = 0;
if ($_rs->RecordCount() > 0) {
    $realty_charge = 1;
}
$_rs = null;unset($_rs);
##

//查詢利息
$sql  = "SELECT cInterest,bInterest, cTax, bTax, cNHITax, bNHITax FROM tChecklist WHERE cCertifiedId ='" . $_vr_code . "'";
$rsCK = $conn->Execute($sql);

//代扣二代健保 利息稅額
$Int                = $rsCK->fields['cInterest'] + $rsCK->fields['bInterest'];
$NHITax             = $rsCK->fields['cNHITax'] + $rsCK->fields['bNHITax'];
$InterestTax        = $rsCK->fields['cTax'] + $rsCK->fields['bTax'];
$realCertifiedMoney = $CertifiedMoney - $Int + $NHITax + $InterestTax;
$realCertifiedMoney = ($realCertifiedMoney > 0) ? $realCertifiedMoney : '';

//查詢是否開過發票
$sql           = 'SELECT tInvoice FROM tBankTrans WHERE tVR_Code="' . $vr_code . '" AND tInvoice is not null LIMIT 1;';
$_rs           = $conn->Execute($sql);

if ($_rs->RecordCount() > 0) {
    $invoice = 1;
}
$_rs = null;unset($_rs);
##
