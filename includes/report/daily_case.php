<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/class/advance.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';

$advance  = new Advance();

$_POST = escapeStr($_POST);

$cCertifiedId   = $_POST['cCertifiedId'];
$cEndDate       = $_POST['cEndDate'];
$cCertifyDate   = $_POST['cCertifyDate'];

$aColumns = array('cCertifiedId', 'cEndDate', 'cBankList', 'tExportTime');

/* Filtering */
$sWhere = 'WHERE cas.cCertifiedId != "" AND cas.cCertifiedId !="005030342" AND cas.cCaseStatus != 8 ';
#搜尋條件-保證號碼
if (isset($cCertifiedId) && $cCertifiedId != "") {
    if ($sWhere) {$sWhere .= " AND ";}
    $sWhere .= ' cas.cCertifiedId="' . $cCertifiedId . '" ';
}

#實際點交日
if (isset($cEndDate) && $cEndDate != "") {
    $cEndDate = $advance->ConvertDateToAD($cEndDate, Base::DATE_FORMAT_NUM_DATE);

    if ($sWhere) {$sWhere .= " AND ";}
    $sWhere .= ' cas.cEndDate="' . $cEndDate . '" ';
}

$sQuery = "
            SELECT
                cas.cId, cas.cCertifiedId, cas.cEndDate, cas.cBankList
            FROM 
                tContractCase AS cas
                " . $sWhere ."
        ";
$res = $conn->Execute($sQuery);

#全部履保號
$certifiedId = '';
while (!$res->EOF) {
    $certifiedId .= $res->fields['cCertifiedId'].',';
    $res->MoveNext();
}
$res->moveFirst();

$certifiedId = substr($certifiedId, 0, -1);


#全部的履保費出款日
$sql = "SELECT
            tMemo, tBankLoansDate
        FROM
            tBankTrans
        WHERE
            tMemo in (" . $certifiedId . ")
          AND tKind = '保證費'
        ";
$tBankTransList = $conn->Execute($sql);

$output['data'] = array();
$listCertifiedId = array();

while (!$res->EOF) {
    $tBankTransList->moveFirst();
    while (!$tBankTransList->EOF) {
        if($res->fields['cCertifiedId'] == $tBankTransList->fields['tMemo']) {
            $res->fields['tExportTime'] = $tBankTransList->fields['tBankLoansDate'];

            #檢查重複
            $key = array_search($res->fields['cCertifiedId'], $listCertifiedId);
            #有重複=>比較日期
            if(false !== $key) {
                if($output['data'][$key]['tExportTime'] < $res->fields['tExportTime']) {
                    $output['data'][$key]['tExportTime'] = $res->fields['tExportTime'];
                }
            } else {
                #有搜尋履保費出款日
                if ($cCertifyDate) {
                    $cCertifyDate = $advance->ConvertDateToAD($cCertifyDate, Base::DATE_FORMAT_NUM_DATE);

                    if($cCertifyDate == $res->fields['tExportTime']) {
                        array_push($output['data'], $res->fields);
                        array_push($listCertifiedId, $res->fields['cCertifiedId']);
                    }
                } else {
                    array_push($output['data'], $res->fields);
                    array_push($listCertifiedId, $res->fields['cCertifiedId']);
                }
            }
        }
        $tBankTransList->MoveNext();
    }
    #如果tBankTrans沒找到履保費
    if(NULL == $res->fields['tExportTime']) {
        #有搜尋履保費出款日
        if ($cCertifyDate) {
            $cCertifyDate = $advance->ConvertDateToAD($cCertifyDate, Base::DATE_FORMAT_NUM_DATE);

            if($cCertifyDate == $res->fields['cBankList']) {
                $res->fields['tExportTime'] = NULL;
                array_push($output['data'], $res->fields);
                array_push($listCertifiedId, $res->fields['cCertifiedId']);
            }
        } else {
            array_push($output['data'], $res->fields);
            array_push($listCertifiedId, $res->fields['cCertifiedId']);
        }

    }
    $res->MoveNext();
}


echo json_encode($output);
exit();


