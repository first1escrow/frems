<?php

//取得其他對象利息資訊
function getOthersWithInterest(&$conn, &$data, &$certifiedIds, $table, $debug = false)
{
    $target_table = preg_replace("/B$/iu", "", $table);        //濾除其他買方 table 後綴的 B 字
    $target_table = preg_replace("/O$/iu", "", $target_table); //濾除其他賣方 table 後綴的 O 字

    $target_field                      = ($target_table == 'tContractScrivener') ? 'cSmsTargetName' : 'cName';
    list($target_table, $target_field) = realtyNameRegulation($target_table, $target_field);

    $sql = 'SELECT
                a.cId,
                a.cCertifiedId,
                a.cTBId,
                a.cName,
                a.cIdentifyId,
                a.cInterestMoney,
                b.' . $target_field . ' as target
            FROM
                tContractInterestExt AS a
            JOIN
                ' . $target_table . ' AS b ON a.cTBId = b.cId
            WHERE
                a.cCertifiedId IN ("' . implode('","', $certifiedIds) . '")
                AND a.cDBName = "' . $table . '";';
    $rs = $conn->all($sql);

    if ($debug) {
        exit($conn->debug());
    }

    if (! empty($rs)) {
        foreach ($rs as $v) {
            if ($v['cInterestMoney'] > 0) { //當利息金額大於 0 的時候紀錄
                $v['table']                                                      = $table;
                $data[$v['cCertifiedId']][$v['cTBId']]['interestExt'][$v['cId']] = $v;
            }
        }
    }
}

//取得其他對象發票資訊
function getOthersWithInvoice(&$conn, &$data, &$certifiedIds, $table, $debug = false)
{
    $target_table = preg_replace("/B$/iu", "", $table);        //濾除其他買方 table 後綴的 B 字
    $target_table = preg_replace("/O$/iu", "", $target_table); //濾除其他賣方 table 後綴的 O 字

    $target_field                      = ($target_table == 'tContractScrivener') ? 'cSmsTargetName' : 'cName';
    list($target_table, $target_field) = realtyNameRegulation($target_table, $target_field);

    $sql = 'SELECT
                a.cId,
                a.cCertifiedId,
                a.cTBId,
                a.cName,
                a.cIdentifyId,
                a.cInvoiceMoney,
                b.' . $target_field . ' as target
            FROM
                tContractInvoiceExt AS a
            JOIN
                ' . $target_table . ' AS b ON a.cTBId = b.cId
            WHERE
                a.cCertifiedId IN ("' . implode('","', $certifiedIds) . '")
                AND a.cDBName = "' . $table . '";';
    $rs = $conn->all($sql);

    if ($debug) {
        exit($conn->debug());
    }

    if (! empty($rs)) {
        foreach ($rs as $v) {
            if ($v['cInvoiceMoney'] > 0) { //當發票金額大於 0 的時候紀錄
                $v['table']                                                     = $table;
                $data[$v['cCertifiedId']][$v['cTBId']]['invoiceExt'][$v['cId']] = $v;
            }
        }
    }
}

//取得仲介利息與發票資訊
function getRealtyInterestInvoice(&$conn, &$branches, &$data, $record, $field = '')
{
    if (! empty($record['cBranchNum' . $field]) && (! empty($record['cInterestMoney' . $field]) || ! empty($record['cInvoiceMoney' . $field]))) {
        $realty = [
            'cId'            => $record['cId'],
            'cCertifiedId'   => $record['cCertifiedId'],
            'cBranchNum'     => $record['cBranchNum' . $field],
            'cName'          => $branches[$record['cBranchNum' . $field]]['bStore'],
            'cInterestMoney' => $record['cInterestMoney' . $field],
            'cInvoiceMoney'  => $record['cInvoiceMoney' . $field],
            'cInvoiceDonate' => $record['cInvoiceDonate' . $field],
            'cInvoicePrint'  => $record['cInvoicePrint' . $field],
            'table'          => 'tContractRealestate' . $field,
        ];

        $index                                 = empty($field) ? 0 : $field;
        $data[$record['cCertifiedId']][$index] = $realty;
    }
}

//取得其他仲介利息資訊
function getRealtyOthersWithInterest(&$conn, &$branches, &$data, &$certifiedIds, $table)
{
    $other = [];
    getOthersWithInterest($conn, $data, $certifiedIds, $table);

    if (! empty($other)) {
        foreach ($other as $k => $v) {
            foreach ($v as $ka => $va) {
                foreach ($va['interestExt'] as $kb => $vb) {
                    $order        = '';
                    $order        = ($table == 'tContractRealestate1') ? '1' : $order;
                    $order        = ($table == 'tContractRealestate2') ? '2' : $order;
                    $order        = ($table == 'tContractRealestate3') ? '3' : $order;
                    $vb['target'] = getRealtyName($conn, $branches, $k, $order);

                    $data[$k][$ka]['interestExt'][$kb] = $vb;
                }
            }
        }
    }
}

//取得其他仲介發票資訊
function getRealtyOthersWithInvoice(&$conn, &$branches, &$data, &$certifiedIds, $table)
{
    $other = [];
    getOthersWithInvoice($conn, $other, $certifiedIds, $table);

    if (! empty($other)) {
        foreach ($other as $k => $v) {
            foreach ($v as $ka => $va) {
                foreach ($va['invoiceExt'] as $kb => $vb) {
                    $order        = '';
                    $order        = ($table == 'tContractRealestate1') ? '1' : $order;
                    $order        = ($table == 'tContractRealestate2') ? '2' : $order;
                    $order        = ($table == 'tContractRealestate3') ? '3' : $order;
                    $vb['target'] = getRealtyName($conn, $branches, $k, $order);

                    $data[$k][$ka]['invoiceExt'][$kb] = $vb;
                }
            }
        }
    }
}

//取得指定仲介的父店名
function getRealtyName(&$conn, &$branches, $certifiedId, $order)
{
    $sql = 'SELECT cBranchNum, cBranchNum1, cBranchNum2, cBranchNum3 FROM tContractRealestate WHERE cCertifyId = :cId;';
    $rs  = $conn->one($sql, ['cId' => $certifiedId]);

    return empty($rs['cBranchNum' . $order]) ? '' : $branches[$rs['cBranchNum' . $order]]['bStore'];
}

//仲介店名與欄位正規化
function realtyNameRegulation($target_table, $target_field)
{
    if (preg_match("/^tContractRealestate[1-3]{1}$/", $target_table)) {
        $target_field = 'cName' . substr($target_table, -1);
        $target_table = substr($target_table, 0, -1);

        return [$target_table, $target_field];
    }

    return [$target_table, $target_field];
}

/** 買方 */
//取得主買方
$buyer_main = [];

$sql = 'SELECT cId, cCertifiedId, cName, cInterestMoney, cInvoiceMoney, cInvoiceDonate, cInvoicePrint FROM tContractBuyer WHERE cCertifiedId IN ("' . implode('","', $certifiedIds) . '");';
$rs  = $conn->all($sql);

if (! empty($rs)) {
    foreach ($rs as $v) {
        if (! empty($v['cInterestMoney']) || ! empty($v['cInvoiceMoney'])) { //當利息或發票金額大於 0 的時候紀錄
            $buyer_main[$v['cCertifiedId']][$v['cId']] = $v;
        }
    }
}

//主買方指定利息對象
getOthersWithInterest($conn, $buyer_main, $certifiedIds, 'tContractBuyer');

//主買方指定發票對象
getOthersWithInvoice($conn, $buyer_main, $certifiedIds, 'tContractBuyer');

// echo '<pre>';
// print_r($buyer_main);exit('buyer_main');

//取得其他買方
$buyer_others = [];

$sql = 'SELECT cId, cCertifiedId, cName, cInterestMoney, cInvoiceMoney, cInvoiceDonate, cInvoicePrint FROM tContractOthers WHERE cCertifiedId IN ("' . implode('","', $certifiedIds) . '") AND cIdentity = 1;';
$rs  = $conn->all($sql);

if (! empty($rs)) {
    foreach ($rs as $v) {
        if (! empty($v['cInterestMoney']) || ! empty($v['cInvoiceMoney'])) { //當利息或發票金額大於 0 的時候紀錄
            $buyer_others[$v['cCertifiedId']][$v['cId']] = $v;
        }
    }
}

//其他買方指定利息對象
getOthersWithInterest($conn, $buyer_others, $certifiedIds, 'tContractOthersB');

//其他買方指定發票對象
getOthersWithInvoice($conn, $buyer_others, $certifiedIds, 'tContractOthersB');

// echo '<pre>';
// print_r($buyer_others);exit('buyer_others');

/** 賣方 */
//取得主賣方
$owner_main = [];

$sql = 'SELECT cId, cCertifiedId, cName, cInterestMoney, cInvoiceMoney, cInvoiceDonate, cInvoicePrint FROM tContractOwner WHERE cCertifiedId IN ("' . implode('","', $certifiedIds) . '");';
$rs  = $conn->all($sql);

if (! empty($rs)) {
    foreach ($rs as $v) {
        if (! empty($v['cInterestMoney']) || ! empty($v['cInvoiceMoney'])) { //當利息或發票金額大於 0 的時候紀錄
            $owner_main[$v['cCertifiedId']][$v['cId']] = $v;
        }
    }
}

//主賣方指定利息對象
getOthersWithInterest($conn, $owner_main, $certifiedIds, 'tContractOwner');

//主賣方指定發票對象
getOthersWithInvoice($conn, $owner_main, $certifiedIds, 'tContractOwner');

// echo '<pre>';
// print_r($owner_main);exit('owner_main');

//取得其他賣方
$owner_others = [];

$sql = 'SELECT cId, cCertifiedId, cName, cInterestMoney, cInvoiceMoney, cInvoiceDonate, cInvoicePrint FROM tContractOthers WHERE cCertifiedId IN ("' . implode('","', $certifiedIds) . '") AND cIdentity = 2;';
$rs  = $conn->all($sql);

if (! empty($rs)) {
    foreach ($rs as $v) {
        if (! empty($v['cInterestMoney']) || ! empty($v['cInvoiceMoney'])) { //當利息或發票金額大於 0 的時候紀錄
            $owner_others[$v['cCertifiedId']][$v['cId']] = $v;
        }
    }
}

//其他買方指定利息對象
getOthersWithInterest($conn, $owner_others, $certifiedIds, 'tContractOthersO');

//其他買方指定發票對象
getOthersWithInvoice($conn, $owner_others, $certifiedIds, 'tContractOthersO');

// echo '<pre>';
// print_r($owner_others);exit('owner_others');

/** 仲介 */
//取得仲介
$realty_main = [];

$sql = 'SELECT
            cId,
            cCertifyId as cCertifiedId,
            cBranchNum,
            cBranchNum1,
            cBranchNum2,
            cBranchNum3,
            cInterestMoney,
            cInterestMoney1,
            cInterestMoney2,
            cInterestMoney3,
            cInvoiceMoney,
            cInvoiceMoney1,
            cInvoiceMoney2,
            cInvoiceMoney3,
            cInvoiceDonate,
            cInvoiceDonate1,
            cInvoiceDonate2,
            cInvoiceDonate3,
            cInvoicePrint,
            cInvoicePrint1,
            cInvoicePrint2,
            cInvoicePrint3
        FROM
            tContractRealestate AS a
        WHERE
            cCertifyId IN ("' . implode('","', $certifiedIds) . '");';
$rs = $conn->all($sql);

if (! empty($rs)) {
    foreach ($rs as $v) { //判斷各仲介是否存在且發票或利息金額是否大於 0，若是、則記錄
        getRealtyInterestInvoice($conn, $branches, $realty_main, $v, '');
        getRealtyInterestInvoice($conn, $branches, $realty_main, $v, '1');
        getRealtyInterestInvoice($conn, $branches, $realty_main, $v, '2');
        getRealtyInterestInvoice($conn, $branches, $realty_main, $v, '3');
    }
}
// echo '<pre>';
// print_r($realty_main);exit('realty_main1');

//仲介指定利息對象
getRealtyOthersWithInterest($conn, $branches, $realty_main, $certifiedIds, 'tContractRealestate');
getRealtyOthersWithInterest($conn, $branches, $realty_main, $certifiedIds, 'tContractRealestate1');
getRealtyOthersWithInterest($conn, $branches, $realty_main, $certifiedIds, 'tContractRealestate2');
getRealtyOthersWithInterest($conn, $branches, $realty_main, $certifiedIds, 'tContractRealestate3');

//仲介指定發票對象
getRealtyOthersWithInvoice($conn, $branches, $realty_main, $certifiedIds, 'tContractRealestate');
getRealtyOthersWithInvoice($conn, $branches, $realty_main, $certifiedIds, 'tContractRealestate1');
getRealtyOthersWithInvoice($conn, $branches, $realty_main, $certifiedIds, 'tContractRealestate2');
getRealtyOthersWithInvoice($conn, $branches, $realty_main, $certifiedIds, 'tContractRealestate3');

// echo '<pre>';
// print_r($realty_main);exit('realty_main');

/** 地政士 */
//取得地政士
$scrivener_main = [];

$sql = 'SELECT cId, cCertifiedId, cInterestMoney, cInvoiceMoney, cInvoiceDonate, cInvoicePrint FROM tContractScrivener WHERE cCertifiedId IN ("' . implode('","', $certifiedIds) . '");';
$rs  = $conn->all($sql);

if (! empty($rs)) {
    foreach ($rs as $v) {
        if (! empty($v['cInterestMoney']) || ! empty($v['cInvoiceMoney'])) { //當利息或發票金額大於 0 的時候紀錄
            $scrivener_main[$v['cCertifiedId']][$v['cId']] = $v;
        }
    }
}

//主賣方指定利息對象
getOthersWithInterest($conn, $scrivener_main, $certifiedIds, 'tContractScrivener');

//主賣方指定發票對象
getOthersWithInvoice($conn, $scrivener_main, $certifiedIds, 'tContractScrivener');

// echo '<pre>';
// print_r($scrivener_main);exit('scrivener_main');

//將上列查詢到的清單($owner_main、$owner_others、$buyer_main、$buyer_others、$realty_main、$scrivener_main)整合寫入 list 中
$list = [];
foreach ($detail as $k => $v) {
    if (! empty($owner_main[$k])) {
        foreach ($owner_main[$k] as $va) {
            $row           = array_merge($detail[$k], $va);
            $row['target'] = $va['cName'];

            $list[] = $row;

            $row = null;unset($row);
        }
    }

    if (! empty($owner_others[$k])) {
        foreach ($owner_others[$k] as $va) {
            $row           = array_merge($detail[$k], $va);
            $row['target'] = $va['cName'];

            $list[] = $row;

            $row = null;unset($row);
        }
    }

    if (! empty($buyer_main[$k])) {
        foreach ($buyer_main[$k] as $va) {
            $row           = array_merge($detail[$k], $va);
            $row['target'] = $va['cName'] ?? '';

            $list[] = $row;

            $row = null;unset($row);
        }
    }

    if (! empty($buyer_others[$k])) {
        foreach ($buyer_others[$k] as $va) {
            $row           = array_merge($detail[$k], $va);
            $row['target'] = $va['cName'];

            $list[] = $row;

            $row = null;unset($row);
        }
    }

    if (! empty($realty_main[$k])) {
        foreach ($realty_main[$k] as $va) {
            $row           = array_merge($detail[$k], $va);
            $row['target'] = $va['cName'];

            $list[] = $row;

            $row = null;unset($row);
        }
    }

    if (! empty($scrivener_main[$k])) {
        foreach ($scrivener_main[$k] as $va) {
            $row = array_merge($detail[$k], $va);
            // $row['target'] = $va['cName'];
            $row['target'] = '地政士';

            $list[] = $row;

            $row = null;unset($row);
        }
    }
}

$detail = $list; //將 $list 資料回寫到 $detail 內

$buyer_main = $buyer_others = $owner_main = $owner_others = $realty_main = $scrivener_main = $list = null;
unset($buyer_main, $buyer_others, $owner_main, $owner_others, $realty_main, $scrivener_main, $list);
