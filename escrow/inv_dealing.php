<?php
// PHP 8+ 兼容性初始化
$arr          = [];
$owner_total  = 0;
$buyer_total  = 0;
$realty_total = 0;
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';

$tlog = new TraceLog();

//取得仲介資料
function get_realty($DBlink, $no)
{
    if ($no > 0) {
        $sub_sql = 'SELECT * FROM tBranch WHERE	bId="' . $no . '";';
        $real    = $DBlink->Execute($sub_sql);

        return $real->fields;
    }

    return null;
}

function another($conn, $cCertifiedId, $type, $tbl, $id)
{
    $sql = "SELECT * FROM tContractInvoiceExt WHERE cCertifiedId='" . $cCertifiedId . "' AND cDBName ='" . $tbl . "' AND cTBId='" . $id . "'";
    $rs  = $conn->Execute($sql);

    $i   = 0;
    $arr = [];
    while (! $rs->EOF) {
        $arr[$i]         = $rs->fields;
        $arr[$i]['type'] = $type;
        $arr[$i]['tbl']  = $tbl;
        $i++;
        $rs->MoveNext();
    }
    return $arr;
}

// ##
$cSignCategory = $_REQUEST['cSignCategory']; //判斷合約書位置

$cId                  = $_REQUEST['cCertifiedId'];
$latestCertifiedMoney = $_REQUEST['latestCertifiedMoney'];
$save                 = $_REQUEST['save'] ?? '';

$cCertifiedId = substr($cId, 5);

$_res    = '';
$records = '';

//地址下拉
$inv_another_country = listCity($conn, '');
$inv_another_area    = listArea($conn, '');
##

//處理更新資料
if ($save == 'ok') {
              //更新大項金額(tContractInvoice、包含地政士與捐款小項)
    $cIO = 0; //分配給賣方
    if ($_POST['cInvoiceOwner'] != '0') {$cIO = 1;}

    $cIB = 0; //分配給買方
    if ($_POST['cInvoiceBuyer'] != '0') {$cIB = 1;}

    $cIR = 0; //分配給仲介
    if ($_POST['cInvoiceRealestate'] != '0') {$cIR = 1;}

    $cIS = 0; //分配給地政士
    if (isset($_POST['cInvoiceScrivener']) && $_POST['cInvoiceScrivener'] != '0') {$cIS = 1;}

    $cI = 0; //分配給其他(創世)
    if (isset($_POST['cInvoiceOther']) && $_POST['cInvoiceOther'] != '0') {$cI = 1;}

    $sql = 'UPDATE
                tContractInvoice
            SET
                cSplitOwner="' . $cIO . '",
                cInvoiceOwner="' . (isset($_POST['cInvoiceOwner']) ? $_POST['cInvoiceOwner'] : '0') . '",
                cSplitBuyer="' . $cIB . '",
                cInvoiceBuyer="' . (isset($_POST['cInvoiceBuyer']) ? $_POST['cInvoiceBuyer'] : '0') . '",
                cSplitRealestate="' . $cIR . '",
                cInvoiceRealestate="' . (isset($_POST['cInvoiceRealestate']) ? $_POST['cInvoiceRealestate'] : '0') . '",
                cSplitScrivener="' . $cIS . '",
                cInvoiceScrivener="' . (isset($_POST['cInvoiceScrivener']) ? $_POST['cInvoiceScrivener'] : '0') . '",
                cSplitOther="' . $cI . '",
                cInvoiceOther="' . (isset($_POST['cInvoiceOther']) ? $_POST['cInvoiceOther'] : '0') . '"
            WHERE
                cCertifiedId="' . $cCertifiedId . '" ;';
    $conn->Execute($sql);
    $tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '案件開發票作業編修');
    ##

    //更新賣方資料
    if (isset($_POST['owner_cId']) && is_array($_POST['owner_cId'])) {
        for ($i = 0; $i < count($_POST['owner_cId']); $i++) {
            if (! isset($_POST['owner_donate']) || ! is_array($_POST['owner_donate']) ||
                ! isset($_POST['owner_donate'][$_POST['owner_cId'][$i]]) ||
                $_POST['owner_donate'][$_POST['owner_cId'][$i]] == '') {
                if (! isset($_POST['owner_donate'])) {
                    $_POST['owner_donate'] = [];
                }

                $_POST['owner_donate'][$_POST['owner_cId'][$i]] = 0;
            }

            if (! isset($_POST['owner_print']) || ! is_array($_POST['owner_print']) ||
                ! isset($_POST['owner_print'][$_POST['owner_cId'][$i]]) ||
                $_POST['owner_print'][$_POST['owner_cId'][$i]] == '') {
                if (! isset($_POST['owner_print'])) {
                    $_POST['owner_print'] = [];
                }

                $_POST['owner_print'][$_POST['owner_cId'][$i]] = 'N';
            }

            if ($_POST['owner_first'][$i] == '1') { //主賣方
                $sql = 'UPDATE
                        tContractOwner
                    SET
                        cInvoiceMoney="' . $_POST['owner_inv'][$i] . '",
                        cInvoiceDonate ="' . $_POST['owner_donate'][$_POST['owner_cId'][$i]] . '",
                        cInvoicePrint = "' . $_POST['owner_print'][$_POST['owner_cId'][$i]] . '"
                    WHERE
                        cCertifiedId="' . $cCertifiedId . '"
                        AND cId="' . $_POST['owner_cId'][$i] . '";';
            } else { //其他賣方
                $sql = 'UPDATE
                        tContractOthers
                    SET
                        cInvoiceMoney="' . $_POST['owner_inv'][$i] . '",
                        cInvoiceDonate ="' . $_POST['owner_donate'][$_POST['owner_cId'][$i]] . '",
                        cInvoicePrint = "' . $_POST['owner_print'][$_POST['owner_cId'][$i]] . '"
                    WHERE
                        cCertifiedId="' . $cCertifiedId . '"
                        AND cId="' . $_POST['owner_cId'][$i] . '";';
            }

            $conn->Execute($sql);
        }
    }
    ##

    //更新買方資料
    if (isset($_POST['buyer_cId']) && is_array($_POST['buyer_cId'])) {
        for ($i = 0; $i < count($_POST['buyer_cId']); $i++) {
            if (! isset($_POST['buyer_donate']) || ! is_array($_POST['buyer_donate']) ||
                ! isset($_POST['buyer_donate'][$_POST['buyer_cId'][$i]]) ||
                $_POST['buyer_donate'][$_POST['buyer_cId'][$i]] == '') {
                if (! isset($_POST['buyer_donate'])) {
                    $_POST['buyer_donate'] = [];
                }

                $_POST['buyer_donate'][$_POST['buyer_cId'][$i]] = 0;
            }

            if (! isset($_POST['buyer_print']) || ! is_array($_POST['buyer_print']) ||
                ! isset($_POST['buyer_print'][$_POST['buyer_cId'][$i]]) ||
                $_POST['buyer_print'][$_POST['buyer_cId'][$i]] == '') {
                if (! isset($_POST['buyer_print'])) {
                    $_POST['buyer_print'] = [];
                }

                $_POST['buyer_print'][$_POST['buyer_cId'][$i]] = 'N';
            }

            if ($_POST['buyer_first'][$i] == '1') { //主買方
                $sql = 'UPDATE
                        tContractBuyer
                    SET
                        cInvoiceMoney="' . $_POST['buyer_inv'][$i] . '",
                        cInvoiceDonate ="' . $_POST['buyer_donate'][$_POST['buyer_cId'][$i]] . '",
                        cInvoicePrint = "' . $_POST['buyer_print'][$_POST['buyer_cId'][$i]] . '"
                    WHERE
                        cCertifiedId="' . $cCertifiedId . '"
                        AND cId="' . $_POST['buyer_cId'][$i] . '";';
            } else { //其他買方
                $sql = 'UPDATE
                        tContractOthers
                    SET
                        cInvoiceMoney="' . $_POST['buyer_inv'][$i] . '",
                        cInvoiceDonate ="' . $_POST['buyer_donate'][$_POST['buyer_cId'][$i]] . '",
                        cInvoicePrint = "' . $_POST['buyer_print'][$_POST['buyer_cId'][$i]] . '"
                    WHERE
                        cCertifiedId="' . $cCertifiedId . '"
                        AND cId="' . $_POST['buyer_cId'][$i] . '";';
            }

            $conn->Execute($sql);
        }
    }
    ##

    //更新仲介資料
    if (isset($_POST['realty_bId']) && is_array($_POST['realty_bId'])) {
        for ($i = 0; $i < count($_POST['realty_bId']); $i++) {
            if (! isset($_POST['branch_donate']) || ! is_array($_POST['branch_donate']) ||
                ! isset($_POST['branch_donate'][$_POST['realty_bId'][$i]]) ||
                $_POST['branch_donate'][$_POST['realty_bId'][$i]] == '') {
                if (! isset($_POST['branch_donate'])) {
                    $_POST['branch_donate'] = [];
                }

                $_POST['branch_donate'][$_POST['realty_bId'][$i]] = 0;
            }

            if (! isset($_POST['branch_print']) || ! is_array($_POST['branch_print']) ||
                ! isset($_POST['branch_print'][$_POST['realty_bId'][$i]])) {
                if (! isset($_POST['branch_print'])) {
                    $_POST['branch_print'] = [];
                }

                $_POST['branch_print'][$_POST['realty_bId'][$i]] = 'N';
            } elseif ($_POST['branch_print'][$_POST['realty_bId'][$i]] == 'N') {
                $_POST['branch_print'][$_POST['realty_bId'][$i]] = 'N';
            }

            if ($_POST['realty_first'][$i] == '1') { //第一組
                $sql = 'UPDATE
                        tContractRealestate
                    SET
                        cInvoiceMoney="' . $_POST['realty_inv'][$i] . '",
                        cInvoiceDonate ="' . $_POST['branch_donate'][$_POST['realty_bId'][$i]] . '",
                        cInvoicePrint ="' . $_POST['branch_print'][$_POST['realty_bId'][$i]] . '"
                    WHERE
                        cCertifyId="' . $cCertifiedId . '";';
            } else if ($_POST['realty_first'][$i] == '2') { //第二組
                $sql = 'UPDATE
                        tContractRealestate
                    SET
                        cInvoiceMoney1="' . $_POST['realty_inv'][$i] . '",
                        cInvoiceDonate1 ="' . $_POST['branch_donate'][$_POST['realty_bId'][$i]] . '",
                        cInvoicePrint1 ="' . $_POST['branch_print'][$_POST['realty_bId'][$i]] . '"
                    WHERE
                        cCertifyId="' . $cCertifiedId . '";';
            } else if ($_POST['realty_first'][$i] == '3') { //第三組
                $sql = 'UPDATE
                        tContractRealestate
                    SET
                        cInvoiceMoney2="' . $_POST['realty_inv'][$i] . '",
                        cInvoiceDonate2 ="' . $_POST['branch_donate'][$_POST['realty_bId'][$i]] . '",
                        cInvoicePrint2 ="' . $_POST['branch_print'][$_POST['realty_bId'][$i]] . '"
                    WHERE
                        cCertifyId="' . $cCertifiedId . '";';
            } else { //第四組
                $sql = 'UPDATE
                        tContractRealestate
                    SET
                        cInvoiceMoney3="' . $_POST['realty_inv'][$i] . '",
                        cInvoiceDonate3 ="' . $_POST['branch_donate'][$_POST['realty_bId'][$i]] . '",
                        cInvoicePrint3 ="' . $_POST['branch_print'][$_POST['realty_bId'][$i]] . '"
                    WHERE
                        cCertifyId="' . $cCertifiedId . '";';
            }
            $conn->Execute($sql);
        }
    }

    ##地政士
    //更新
    if (isset($_POST['scrivener_sId']) && is_array($_POST['scrivener_sId'])) {
        for ($i = 0; $i < count($_POST['scrivener_sId']); $i++) {
            if (! isset($_POST['scrivener_donate'][$i]) || $_POST['scrivener_donate'][$i] == '') {
                $_POST['scrivener_donate'][$i] = 0;
            }

            if (! isset($_POST['scrivener_print'][$i]) || $_POST['scrivener_print'][$i] == '') {
                $_POST['scrivener_print'][$i] = 'N';
            }

            $sql = "UPDATE
                    tContractScrivener
                SET
                    cInvoiceDonate = '" . $_POST['scrivener_donate'][$i] . "',
                    cInvoiceTo ='" . $_POST['scrivener_personal'][$i] . "',
                    cInvoiceMoney = '" . $_POST['scrivener_inv'][$i] . "',
                    cInvoicePrint = '" . $_POST['scrivener_print'][$i] . "'
                WHERE
                    cCertifiedId='" . $cCertifiedId . "'";
            $conn->Execute($sql);
        }
    }
    ##

    //指定
    if (isset($_POST['another_cId']) && is_array($_POST['another_cId'])) {
        for ($i = 0; $i < count($_POST['another_cId']); $i++) {
            if (! isset($_POST['another_donate']) || ! is_array($_POST['another_donate']) ||
                ! isset($_POST['another_donate'][$_POST['another_cId'][$i]]) ||
                $_POST['another_donate'][$_POST['another_cId'][$i]] == '') {
                if (! isset($_POST['another_donate'])) {
                    $_POST['another_donate'] = [];
                }

                $_POST['another_donate'][$_POST['another_cId'][$i]] = 0;
            }

            if (! isset($_POST['another_print']) || ! is_array($_POST['another_print']) ||
                ! isset($_POST['another_print'][$_POST['another_cId'][$i]]) ||
                $_POST['another_print'][$_POST['another_cId'][$i]] == '') {
                if (! isset($_POST['another_print'])) {
                    $_POST['another_print'] = [];
                }

                $_POST['another_print'][$_POST['another_cId'][$i]] = 'N';
            }

            $sql = "UPDATE
                    tContractInvoiceExt
                SET
                    cInvoiceDonate = '" . $_POST['another_donate'][$_POST['another_cId'][$i]] . "',
                    cInvoiceMoney  ='" . $_POST['another_inv'][$i] . "',
                    cInvoicePrint = '" . $_POST['another_print'][$_POST['another_cId'][$i]] . "'
                WHERE
                    cId = '" . $_POST['another_cId'][$i] . "';";
            $conn->Execute($sql);
        }
    }
    ##

    $sql = "UPDATE tContractCase SET `cLastEditor` =  '" . $_SESSION['member_id'] . "', `cLastTime` =  now() WHERE cCertifiedId ='" . $cCertifiedId . "'";
    $conn->Execute($sql);
}
##

$sql = 'SELECT cCertifiedMoney, cTotalMoney FROM tContractIncome WHERE cCertifiedId="' . $cCertifiedId . '";';
$rs  = $conn->Execute($sql);

$cCertifiedMoney = (int) $rs->fields['cCertifiedMoney'];
$cTotalMoney     = (int) $rs->fields['cTotalMoney'];

$rs = null;unset($rs);

//取得各大類對象發票金額
$sql = 'SELECT * FROM tContractInvoice WHERE cCertifiedId="' . $cCertifiedId . '";';
$tmp = $conn->Execute($sql);

$cInvoiceOwner      = $tmp->fields['cInvoiceOwner'];      //賣方
$cInvoiceBuyer      = $tmp->fields['cInvoiceBuyer'];      //買方
$cInvoiceRealestate = $tmp->fields['cInvoiceRealestate']; //仲介
$cInvoiceScrivener  = $tmp->fields['cInvoiceScrivener'];  //代書
$cInvoiceOther      = $tmp->fields['cInvoiceOther'];      //捐創世
##
##其他的身分別(select)
$iden_count = 0;
##
##
$owner_another = [];
$buyer_another = [];

//取得賣方對象清單
$sql = 'SELECT * FROM tContractOwner WHERE cCertifiedId="' . $cCertifiedId . '";';
$tmp = $conn->Execute($sql); //首位賣方

##如果是公司則無法捐贈發票
if (! isset($owner[0]['donate'])) {
    $owner[0]['donate'] = '';
}

if (mb_strlen($tmp->fields['cIdentifyId']) == 8) {
    $owner[0]['donate'] = 'disabled=disabled';
}
##
##身分別
$owner[0]['type'] = '賣方1';
$owner[0]['tbl']  = 'tContractOwner';
##
$owner[0]['cId']            = $tmp->fields['cId'];                       //索引編號
$owner[0]['first']          = '1';                                       //是否為第一位?
$owner[0]['cName']          = $tmp->fields['cName'];                     //姓名
$owner[0]['cInvoiceMoney']  = $tmp->fields['cInvoiceMoney'];             //發票金額
$owner[0]['cInvoiceDonate'] = $tmp->fields['cInvoiceDonate'];            //是否捐贈發票
$owner[0]['cInvoicePrint']  = $tmp->fields['cInvoicePrint'];             //是否捐贈發票
$owner_total                = $owner_total + $owner[0]['cInvoiceMoney']; //賣方發票總額(個別加總)

$arr = another($conn, $cCertifiedId, '指定1', $owner[0]['tbl'], $owner[0]['cId']);
if (is_array($arr)) {
    $owner_another = array_merge($owner_another, $arr);
}

for ($i = 0; $i < count($owner_another); $i++) {
    $owner_total = $owner_total + $owner_another[$i]['cInvoiceMoney']; //賣方發票總額(個別加總)
}

unset($tmp, $arr);
##

$sql = 'SELECT * FROM tContractOthers WHERE cCertifiedId="' . $cCertifiedId . '" AND cIdentity="2";';
$tmp = $conn->Execute($sql); //其他賣方

$index = 1;
$j     = 2;
while (! $tmp->EOF) {
                                                       ##如果是公司則無法捐贈發票
    if (mb_strlen($tmp->fields['cIdentifyId']) == 8) { //
        $owner[$index]['donate'] = 'disabled=disabled';
    }
    ##
    ##身分別
    $owner[$index]['type'] = '賣方' . $j;
    $owner[$index]['tbl']  = 'tContractOthersO';
    ##

    $owner[$index]['cId']            = $tmp->fields['cId'];
    $owner[$index]['first']          = '2';
    $owner[$index]['cName']          = $tmp->fields['cName'];
    $owner[$index]['cInvoiceMoney']  = $tmp->fields['cInvoiceMoney'];
    $owner[$index]['cInvoiceDonate'] = $tmp->fields['cInvoiceDonate'];                 //是否捐贈發票
    $owner[$index]['cInvoicePrint']  = $tmp->fields['cInvoicePrint'];                  //是否列印發票
    $owner_total                     = $owner_total + $owner[$index]['cInvoiceMoney']; //賣方發票總額(個別加總)
    if (! isset($owner[$index]['donate'])) {
        $owner[$index]['donate'] = '';
    }

    $arr = another($conn, $cCertifiedId, '指定' . $j, $owner[$index]['tbl'], $owner[$index]['cId']);

    if (is_array($arr)) {
        for ($i = 0; $i < count($arr); $i++) {
            $owner_total = $owner_total + $arr[$i]['cInvoiceMoney']; //賣方發票總額(個別加總)
        }

        $owner_another = array_merge($owner_another, $arr);
    }

    $arr = null;unset($arr);

    $index++;
    $j++;

    $tmp->MoveNext();
}

$tmp = null;unset($tmp);
##

//取得買方對象清單
$sql = 'SELECT * FROM tContractBuyer WHERE cCertifiedId="' . $cCertifiedId . '";';
$tmp = $conn->Execute($sql); //首位買方

##如果是公司則無法捐贈發票
if (mb_strlen($tmp->fields['cIdentifyId']) == 8) { //\
    $buyer[0]['donate'] = 'disabled=disabled';
}
##

$buyer[0]['type'] = '買方1';
$buyer[0]['tbl']  = 'tContractBuyer';
##

$buyer[0]['cId']            = $tmp->fields['cId'];                       //保證號碼
$buyer[0]['first']          = '1';                                       //是否為第一位?
$buyer[0]['cName']          = $tmp->fields['cName'];                     //姓名
$buyer[0]['cInvoiceMoney']  = $tmp->fields['cInvoiceMoney'];             //發票金額
$buyer[0]['cInvoiceDonate'] = $tmp->fields['cInvoiceDonate'];            //是否捐贈發票
$buyer[0]['cInvoicePrint']  = $tmp->fields['cInvoicePrint'];             //是否列印發票
$buyer_total                = $buyer_total + $buyer[0]['cInvoiceMoney']; //買方發票總額(個別加總)
$tmp                        = null;unset($tmp);

$arr = another($conn, $cCertifiedId, '指定1', $buyer[0]['tbl'], $buyer[0]['cId']);

if (is_array($arr)) {
    $buyer_another = array_merge($buyer_another, $arr);
}

for ($i = 0; $i < count($buyer_another); $i++) {
    $buyer_total = $buyer_total + $buyer_another[$i]['cInvoiceMoney']; //買方發票總額(個別加總)
}

$arr = null;unset($arr);

$index = 1;

$sql = 'SELECT * FROM tContractOthers WHERE cCertifiedId="' . $cCertifiedId . '" AND cIdentity="1";';
$tmp = $conn->Execute($sql); //其他買方

$index = count($buyer);
$j     = 2;

while (! $tmp->EOF) {
                                                       ##如果是公司則無法捐贈發票
    if (mb_strlen($tmp->fields['cIdentifyId']) == 8) { //
        $buyer[$index]['donate'] = 'disabled=disabled';
    }
    ##

    ##其他的身分別(select)
    $buyer[$index]['type'] = '買方' . $j;
    $buyer[$index]['tbl']  = 'tContractOthersB';

    ##
    $buyer[$index]['cId']            = $tmp->fields['cId'];
    $buyer[$index]['first']          = '2';
    $buyer[$index]['cName']          = $tmp->fields['cName'];
    $buyer[$index]['cInvoiceMoney']  = $tmp->fields['cInvoiceMoney'];
    $buyer[$index]['cInvoiceDonate'] = $tmp->fields['cInvoiceDonate'];                 //是否捐贈發票
    $buyer[$index]['cInvoicePrint']  = $tmp->fields['cInvoicePrint'];                  //是否列印發票
    $buyer_total                     = $buyer_total + $buyer[$index]['cInvoiceMoney']; //買方發票總額(個別加總)

    $arr = another($conn, $cCertifiedId, '指定' . $j, $buyer[$index]['tbl'], $buyer[$index]['cId']);

    if (is_array($arr)) {
        for ($i = 0; $i < count($arr); $i++) {
            $buyer_total = $buyer_total + $arr[$i]['cInvoiceMoney']; //買方發票總額(個別加總)
        }

        $buyer_another = array_merge($buyer_another, $arr);
    }

    $arr = null;unset($arr);

    $index++;
    $j++;

    $tmp->MoveNext();
}

$tmp = null;unset($tmp);
##

//取得仲介對象清單
$sql = 'SELECT * FROM tContractRealestate WHERE	cCertifyId="' . $cCertifiedId . '";';
$tmp = $conn->Execute($sql);

$realty1        = $realty2        = $realty3        = [];
$realty_another = [];

$ri = 0;
if ($tmp->fields['cBranchNum'] != '0') {
    $realty1 = get_realty($conn, $tmp->fields['cBranchNum']);
    if ($realty1) {
        ##
        $realty1['type'] = '仲介1';
        $realty1['tbl']  = 'tContractRealestate';
        $realty1['cId']  = $tmp->fields['cId'];

        ##
        $realty1['first']          = '1';
        $realty1['bInvoiceMoney']  = $tmp->fields['cInvoiceMoney'];
        $realty1['cInvoiceDonate'] = $tmp->fields['cInvoiceDonate'];
        $realty1['cInvoicePrint']  = $tmp->fields['cInvoicePrint'];
        $realty[$ri++]             = $realty1;
        $realty_total              = $realty_total + $realty1['bInvoiceMoney'];

        $arr = another($conn, $cCertifiedId, '指定1', $realty1['tbl'], $realty1['cId']);
        if (is_array($arr)) {
            $realty_another = $arr;
        }

        for ($i = 0; $i < count($realty_another); $i++) {
            $realty_total = $realty_total + $realty_another[$i]['cInvoiceMoney'];
        }
    }
}
$realty1 = $arr = null;
unset($realty1, $arr);

if ($tmp->fields['cBranchNum1'] != '0') {
    $realty2 = get_realty($conn, $tmp->fields['cBranchNum1']);
    if ($realty2) {
        ##
        $realty2['type'] = '仲介2';
        $realty2['tbl']  = 'tContractRealestate1';
        $realty2['cId']  = $tmp->fields['cId'];

        ##
        $realty2['first']          = '2';
        $realty2['bInvoiceMoney']  = $tmp->fields['cInvoiceMoney1'];
        $realty2['cInvoiceDonate'] = $tmp->fields['cInvoiceDonate1'];
        $realty2['cInvoicePrint']  = $tmp->fields['cInvoicePrint1'];
        $realty[$ri++]             = $realty2;
        $realty_total              = $realty_total + $realty2['bInvoiceMoney'];

        ##
        $arr = another($conn, $cCertifiedId, '指定2', $realty2['tbl'], $realty2['cId']);

        if (is_array($arr)) {
            for ($i = 0; $i < count($arr); $i++) {
                $realty_total = $realty_total + $arr[$i]['cInvoiceMoney'];
            }
            $realty_another = array_merge($realty_another, $arr);
        }
        $arr = null;unset($arr);
    }
}
$realty2 = null;unset($realty2);

if ($tmp->fields['cBranchNum2'] != '0') {
    $realty3 = get_realty($conn, $tmp->fields['cBranchNum2']);
    if ($realty3) {
        ##
        $realty3['type'] = '仲介3';
        $realty3['tbl']  = 'tContractRealestate2';
        $realty3['cId']  = $tmp->fields['cId'];

        ##
        $realty3['first']          = '3';
        $realty3['bInvoiceMoney']  = $tmp->fields['cInvoiceMoney2'];
        $realty3['cInvoiceDonate'] = $tmp->fields['cInvoiceDonate2'];
        $realty3['cInvoicePrint']  = $tmp->fields['cInvoicePrint2'];
        $realty[$ri++]             = $realty3;
        $realty_total              = $realty_total + $realty3['bInvoiceMoney'];

        ##
        $arr = another($conn, $cCertifiedId, '指定3', $realty3['tbl'], $realty3['cId']);
        if (is_array($arr)) {
            for ($i = 0; $i < count($arr); $i++) {
                $realty_total = $realty_total + $arr[$i]['cInvoiceMoney'];
            }
            $realty_another = array_merge($realty_another, $arr);
        }
        $arr = null;unset($arr);
    }
}
$realty3 = null;unset($realty3);

if ($tmp->fields['cBranchNum3'] != '0') {
    $realty4 = get_realty($conn, $tmp->fields['cBranchNum3']);
    if ($realty4) {
        ##
        $realty4['type'] = '仲介4';
        $realty4['tbl']  = 'tContractRealestate3';
        $realty4['cId']  = $tmp->fields['cId'];

        ##
        $realty4['first']          = '4';
        $realty4['bInvoiceMoney']  = $tmp->fields['cInvoiceMoney3'];
        $realty4['cInvoiceDonate'] = $tmp->fields['cInvoiceDonate3'];
        $realty4['cInvoicePrint']  = $tmp->fields['cInvoicePrint3'];
        $realty[$ri++]             = $realty4;
        $realty_total              = $realty_total + $realty4['bInvoiceMoney'];
        ##

        $arr = another($conn, $cCertifiedId, '指定4', $realty4['tbl'], $realty4['cId']);
        if (is_array($arr)) {
            for ($i = 0; $i < count($arr); $i++) {
                $realty_total = $realty_total + $arr[$i]['cInvoiceMoney'];
            }
            $realty_another = array_merge($realty_another, $arr);
        }
        $arr = null;unset($arr);
    }
}

$realty4 = null;unset($realty4);
##

//取得代書對象清單
$sql = 'SELECT
            scr.sId,
            scr.sName,
            scr.sOffice,
            csc.cInvoiceDonate,
            csc.cInvoiceMoney,
            csc.cId,
            csc.cInvoiceTo,
            csc.cInvoicePrint
        FROM
            tContractScrivener AS csc
        JOIN
            tScrivener AS scr ON csc.cScrivener=scr.sId
        WHERE
            csc.cCertifiedId="' . $cCertifiedId . '";';
$tmp = $conn->Execute($sql);

##
$scrivener         = $tmp->fields;
$scrivener['name'] = '地政士';
$scrivener['tbl']  = 'tContractScrivener';
$scrivener['cId']  = $tmp->fields['cId'];
$scrivener_total   = $tmp->fields['cInvoiceMoney'];

$scrivener_another = another($conn, $cCertifiedId, '地政士', $scrivener['tbl'], $scrivener['cId']);

for ($i = 0; $i < count($scrivener_another ?? []); $i++) {
    $scrivener_total = $scrivener_total + $scrivener_another[$i]['cInvoiceMoney'];
}

if ($scrivener_total == '') {
    $scrivener_total = 0;
}
$tmp = null;unset($tmp);

//是否匯出進銷檔，匯出就不能改 20150918
$sql = "SELECT cInvoiceClose FROM  tContractCase WHERE cCertifiedId = '" . $cCertifiedId . "'";
$rs  = $conn->Execute($sql);

$close = $rs->fields['cInvoiceClose'];

//取得創世基金會對象清單
$fundament = '創世基金會';

##
$smarty->assign('InvoiceClose', $close);
$smarty->assign('cCertifiedId', $cCertifiedId);
$smarty->assign('cId', $cId);
$smarty->assign('cCertifiedMoney', $cCertifiedMoney);
$smarty->assign('latestCertifiedMoney', $latestCertifiedMoney);
$smarty->assign('cSignCategory', $cSignCategory);

// 統一為所有相關陣列補齊 'donate' 欄位，避免模板警告
// 處理 owner 陣列
if (is_array($owner)) {
    foreach ($owner as $k => $v) {
        if (is_array($v) && ! isset($owner[$k]['donate'])) {
            $owner[$k]['donate'] = '';
        }
    }
}

// 處理 buyer 陣列
if (is_array($buyer)) {
    foreach ($buyer as $k => $v) {
        if (is_array($v) && ! isset($buyer[$k]['donate'])) {
            $buyer[$k]['donate'] = '';
        }
    }
}

// 處理 realty 陣列
if (is_array($realty)) {
    foreach ($realty as $k => $v) {
        if (is_array($v) && ! isset($realty[$k]['donate'])) {
            $realty[$k]['donate'] = '';
        }
    }
}

// 處理 scrivener 陣列 (可能是單一記錄)
if (is_array($scrivener) && ! isset($scrivener['donate'])) {
    $scrivener['donate'] = '';
}

// 處理 owner_another 陣列
if (is_array($owner_another)) {
    foreach ($owner_another as $k => $v) {
        if (is_array($v) && ! isset($owner_another[$k]['donate'])) {
            $owner_another[$k]['donate'] = '';
        }
    }
}

// 處理 buyer_another 陣列
if (is_array($buyer_another)) {
    foreach ($buyer_another as $k => $v) {
        if (is_array($v) && ! isset($buyer_another[$k]['donate'])) {
            $buyer_another[$k]['donate'] = '';
        }
    }
}

// 處理 realty_another 陣列
if (is_array($realty_another)) {
    foreach ($realty_another as $k => $v) {
        if (is_array($v) && ! isset($realty_another[$k]['donate'])) {
            $realty_another[$k]['donate'] = '';
        }
    }
}

// 處理 scrivener_another 陣列
if (is_array($scrivener_another)) {
    foreach ($scrivener_another as $k => $v) {
        if (is_array($v) && ! isset($scrivener_another[$k]['donate'])) {
            $scrivener_another[$k]['donate'] = '';
        }
    }
}

//賣方
$smarty->assign('cInvoiceOwner', $cInvoiceOwner);    //賣方發票總額
$smarty->assign('owner_count', count($owner ?? [])); //賣方數量
$smarty->assign('data_owner', $owner);               //賣方資料
$smarty->assign('owner_total', $owner_total);        //賣方發票總額(個別)

//買方
$smarty->assign('cInvoiceBuyer', $cInvoiceBuyer);    //買方發票總額
$smarty->assign('buyer_count', count($buyer ?? [])); //買方數量
$smarty->assign('data_buyer', $buyer);               //買方資料
$smarty->assign('buyer_total', $buyer_total);        //買方發票總額(個別)

//仲介
$smarty->assign('cInvoiceRealestate', $cInvoiceRealestate); //仲介發票總額
$smarty->assign('realty_count', count($realty ?? []));      //仲介數量
$smarty->assign('data_realty', $realty);                    //仲介資料
$smarty->assign('realty_total', $realty_total);             //仲介

//地政士
$smarty->assign('cInvoiceScrivener', $cInvoiceScrivener); //發票總額
$smarty->assign('scrivener_total', $scrivener_total);     //發票總額(個別)
$smarty->assign('data_scrivener', $scrivener);

//創世
$smarty->assign('cInvoiceOther', $cInvoiceOther); //發票總額

//指定

// 簡單直接地為所有相關陣列補齊 'donate' 欄位
// 處理 owner 陣列
if (is_array($owner)) {
    foreach ($owner as $k => $v) {
        if (is_array($v) && ! isset($owner[$k]['donate'])) {
            $owner[$k]['donate'] = '';
        }
    }
}

// 處理 owner_another 陣列
if (is_array($owner_another)) {
    foreach ($owner_another as $k => $v) {
        if (is_array($v) && ! isset($owner_another[$k]['donate'])) {
            $owner_another[$k]['donate'] = '';
        }
    }
}

// 處理 buyer 陣列
if (is_array($buyer)) {
    foreach ($buyer as $k => $v) {
        if (is_array($v) && ! isset($buyer[$k]['donate'])) {
            $buyer[$k]['donate'] = '';
        }
    }
}

// 處理 buyer_another 陣列
if (is_array($buyer_another)) {
    foreach ($buyer_another as $k => $v) {
        if (is_array($v) && ! isset($buyer_another[$k]['donate'])) {
            $buyer_another[$k]['donate'] = '';
        }
    }
}

// 處理 realty 陣列
if (is_array($realty)) {
    foreach ($realty as $k => $v) {
        if (is_array($v) && ! isset($realty[$k]['donate'])) {
            $realty[$k]['donate'] = '';
        }
    }
}

// 處理 realty_another 陣列
if (is_array($realty_another)) {
    foreach ($realty_another as $k => $v) {
        if (is_array($v) && ! isset($realty_another[$k]['donate'])) {
            $realty_another[$k]['donate'] = '';
        }
    }
}

// 處理 scrivener 陣列 (可能是單一記錄)
if (is_array($scrivener) && ! isset($scrivener['donate'])) {
    $scrivener['donate'] = '';
}

// 處理 scrivener_another 陣列
if (is_array($scrivener_another)) {
    foreach ($scrivener_another as $k => $v) {
        if (is_array($v) && ! isset($scrivener_another[$k]['donate'])) {
            $scrivener_another[$k]['donate'] = '';
        }
    }
}

$smarty->assign('data_owner_another', $owner_another);         //賣方指定人資料
$smarty->assign('data_buyer_another', $buyer_another);         //買方指定人資料
$smarty->assign('data_realty_another', $realty_another);       //仲介指定人資料
$smarty->assign('data_scrivener_another', $scrivener_another); //地政士指定人資料

if ($cCertifiedMoney == 0) {
    $smarty->assign('inv_dealing_non', '無履保費金額或尚未儲存合約書!!!'); //賣方指定人資料
}

//賣方5人以上(含)，且總價金額低於1000萬(含)時，顯示警告訊息 "請確認合約版本第六條履保費收費方式!"
$certify_money_notice = '';
if ((count($owner) >= 5) && ($cTotalMoney <= 10000000)) {
    $fh                   = dirname(__DIR__) . '/includes/certifyRuleNotice.txt';
    $certify_money_notice = file_get_contents($fh);
}
$smarty->assign('certify_money_notice', $certify_money_notice);

// 補齊所有 owner 陣列元素的 donate 欄位，避免模板警告
if (is_array($owner)) {
    foreach ($owner as $k => $v) {
        if (! isset($owner[$k]['donate'])) {
            $owner[$k]['donate'] = '';
        }

    }
}

$smarty->display('inv_dealing.inc.tpl', '', 'escrow');
