<?php
require_once dirname(dirname(dirname(__DIR__))) . '/openadodb.php';

/* 日期範圍 */
require_once __DIR__ . '/dateRange.php';
/***********/

$sql = 'SELECT
            cas.cCertifiedId as cCertifiedId,
            cas.cSignDate as cSignDate,
            cas.cApplyDate as cApplyDate,
            rea.cBrand,
            rea.cBrand1,
            rea.cBrand2,
            rea.cBrand3,
            rea.cBranchNum as cBranchNum,
            rea.cBranchNum1 as cBranchNum1,
            rea.cBranchNum2 as cBranchNum2,
            rea.cBranchNum3 as cBranchNum3,
            (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum) AS bCategory,
            (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum1) AS bCategory1,
            (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum2) AS bCategory2,
            (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum3) AS bCategory3,
            (SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brand,
            (SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brand1,
            (SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brand2,
            (SELECT bName FROM tBrand WHERE bId = rea.cBrand3) AS brand3,
            (SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum) AS branch,
            (SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum1) AS branch1,
            (SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum2) AS branch2,
            (SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum3) AS branch3,
            rea.cServiceTarget as cServiceTarget,
            rea.cServiceTarget1 as cServiceTarget1,
            rea.cServiceTarget2 as cServiceTarget2,
            rea.cServiceTarget3 as cServiceTarget3,
            own.cIdentifyId as ownerId,
            own.cName as ownerName,
            buy.cIdentifyId as buyerId,
            buy.cMobileNum AS buymobile,
            own.cMobileNum AS ownmobile,
            buy.cName as buyerName,
            sts.sName as caseStatus
        FROM
            tContractCase AS cas
        JOIN
            tContractRealestate AS rea ON rea.cCertifyId = cas.cCertifiedId
        LEFT JOIN
            tContractOwner AS own ON own.cCertifiedId = cas.cCertifiedId
        LEFT JOIN
            tContractBuyer AS buy ON buy.cCertifiedId = cas.cCertifiedId
        JOIN
            tStatusCase AS sts ON cas.cCaseStatus = sts.sId
        WHERE
            cas.cSignDate >= "' . $fromDate . '"
            AND cas.cSignDate <= "' . $toDate . '"
            AND cas.cApplyDate >= "' . $creatingFrom . '"
            AND cas.cApplyDate <= "' . $creatingTo . '"
            AND (rea.cBrand = 1 OR rea.cBrand1 = 1 OR rea.cBrand2 = 1 OR rea.cBrand3 = 1)
        GROUP BY
            cas.cCertifiedId
        ORDER BY
            cas.cSignDate,cas.cDealId
        ASC;';
$rs = $conn->Execute($sql);

$i    = 0;
$data = [];

//保證號碼+服務費>0+買方or賣方
//服務對象：1.買賣方、2.賣方、3.買方
while (!$rs->EOF) {
    echo 'case: ' . $rs->fields['cCertifiedId'] . "\n";

    //第一間店
    if ($rs->fields['cBrand'] == 1) {
        $case = [
            'cBrand'         => $rs->fields['cBrand'],
            'bCategory'      => $rs->fields['bCategory'],
            'cBranchNum'     => $rs->fields['cBranchNum'],
            'cServiceTarget' => $rs->fields['cServiceTarget'],
            'cCertifiedId'   => $rs->fields['cCertifiedId'],
        ];

        realty($data, $case);
        $case = null;unset($case);
    }

    //第二間店
    if ($rs->fields['cBrand1'] == 1) {
        $case = [
            'cBrand'         => $rs->fields['cBrand1'],
            'bCategory'      => $rs->fields['bCategory1'],
            'cBranchNum'     => $rs->fields['cBranchNum1'],
            'cServiceTarget' => $rs->fields['cServiceTarget1'],
            'cCertifiedId'   => $rs->fields['cCertifiedId'],
        ];

        realty($data, $case);
        $case = null;unset($case);
    }

    //第三間店
    if ($rs->fields['cBrand2'] == 1) {
        $case = [
            'cBrand'         => $rs->fields['cBrand2'],
            'bCategory'      => $rs->fields['bCategory2'],
            'cBranchNum'     => $rs->fields['cBranchNum2'],
            'cServiceTarget' => $rs->fields['cServiceTarget2'],
            'cCertifiedId'   => $rs->fields['cCertifiedId'],
        ];

        realty($data, $case);
        $case = null;unset($case);
    }

    //第四間店
    if ($rs->fields['cBrand3'] == 1) {
        $case = [
            'cBrand'         => $rs->fields['cBrand3'],
            'bCategory'      => $rs->fields['bCategory3'],
            'cBranchNum'     => $rs->fields['cBranchNum3'],
            'cServiceTarget' => $rs->fields['cServiceTarget3'],
            'cCertifiedId'   => $rs->fields['cCertifiedId'],
        ];

        realty($data, $case);
        $case = null;unset($case);
    }

    $rs->MoveNext();
}

$dir = __DIR__ . '/data';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
$fh = $dir . '/R' . date("Ymd") . '.csv';

file_put_contents($fh, "\xEF\xBB\xBF");
file_put_contents($fh, '保證號碼,仲介店類型,是否有服務費,身分' . "\r\n", FILE_APPEND);
if (!empty($data)) {
    foreach ($data as $v) {
        $txt = $v['cCertifiedId'] . '_,' . $v['Category'] . ',' . $v['Charge'] . ',' . $v['Target'];
        file_put_contents($fh, $txt . "\r\n", FILE_APPEND);
    }
}

exit('Done!!(' . date("Y-m-d G:i:s") . ')' . "\n");

//確認仲介店類型
function convertBranch($category)
{
    if ($category == 1) {
        return '加盟';
    }

    if ($category == 2) {
        return '直營';
    }

    return '';
}

//確認買賣方服務費
function checkServiecFee($branch, $cId, $target)
{
    global $conn;

    $check = '';

    $sql = "SELECT tMoney, tTxt FROM tBankTrans WHERE tMemo = '" . $cId . "' AND tStoreId = '" . $branch . "'";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        if (preg_match("/服務費/", $rs->fields['tTxt']) && preg_match("/" . $target . "/", $rs->fields['tTxt'])) {
            $check = $rs->fields['tMoney'];
        }

        $rs->MoveNext();
    }

    return $check;
}

//仲介店紀錄
function realty(&$data, $case)
{
    $i = count($data);

    $case['Category'] = convertBranch($case['bCategory']);
    if ($case['cServiceTarget'] == 1) { //買賣方
        $case['Target'] = '買方';
        $case['Charge'] = checkServiecFee($case['cBranchNum'], $case['cCertifiedId'], "買方");
        // $case['Charge'] = '';
        setData($data, $case);

        $case['Target'] = '賣方';
        $case['Charge'] = checkServiecFee($case['cBranchNum'], $case['cCertifiedId'], "賣方");
        setData($data, $case);
    } elseif ($case['cServiceTarget'] == 2) { //賣方
        $case['Target'] = '賣方';
        $case['Charge'] = checkServiecFee($case['cBranchNum'], $case['cCertifiedId'], "賣方");
        setData($data, $case);
    } elseif ($case['cServiceTarget'] == 3) { //買方
        $case['Target'] = '買方';
        $case['Charge'] = checkServiecFee($case['cBranchNum'], $case['cCertifiedId'], "買方");
        // $case['Charge'] = '';
        setData($data, $case);
    }
}

//建立資料
function setData(&$data, $case)
{
    $data[count($data)] = [
        'cCertifiedId' => $case['cCertifiedId'],
        'Category'     => $case['Category'],
        'Target'       => $case['Target'],
        'Charge'       => $case['Charge'],
    ];
}
