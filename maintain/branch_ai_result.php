<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once __DIR__ . '/lib.php';

//取得仲介店資訊
function getBranchInformation($bId)
{
    global $conn;

    $sql = 'SELECT * FROM tBranch WHERE bId = "' . $bId . '";';
    $rs  = $conn->Execute($sql);

    return $rs->fields;
}
##

$sales_bank   = trim($_POST['sales_bank']);
$ge_id        = trim($_POST['ge_id']);
$lander_Sdate = trim($_POST['lander_Sdate']);
$lander_Edate = trim($_POST['lander_Edate']);
$total_page   = trim($_POST['total_page']) + 1 - 1;
$current_page = trim($_POST['current_page']) + 1 - 1;
$record_limit = trim($_POST['record_limit']) + 1 - 1;

$acc  = trim($_POST['bid']);
$acc2 = trim($_POST['brand']);

if (!$record_limit) {$record_limit = 5;}

$date_range = '';
$query      = '';

// 設定搜尋字串
#$sales_bank = 8 ;
//$sales_bank_VR_Code = '60001' ;

# 設定銀行別 #
if ($sales_bank) {
    $sql = '
	SELECT
		*
	FROM
		tContractBank
	WHERE
		cShow="1"
		AND cBankCode="' . $sales_bank . '"
	';

    $rs = $conn->Execute($sql);
    if ($query) {$query .= ' AND';}
    if ($sales_bank) {
        $query .= ' tBank_kind ="' . $rs->fields['cBankName'] . '"';
    }
}
##

# 設定起訖時間範圍 #
if ($lander_Sdate && $lander_Edate) {
    $tmp = explode('-', $lander_Sdate);
    //起始時間
    $tmp[0]       = $tmp[0] + 1911;
    $lander_Sdate = $tmp[0] . "-" . $tmp[1] . "-" . $tmp[2];
    unset($tmp);
    //訖
    $tmp          = explode('-', $lander_Edate);
    $tmp[0]       = $tmp[0] + 1911;
    $lander_Edate = $tmp[0] . "-" . $tmp[1] . "-" . $tmp[2];
    unset($tmp);

    if ($query) {$query .= ' AND';}
    $query .= ' btr.tBankLoansDate >="' . $lander_Sdate . '" AND btr.tBankLoansDate <= "' . $lander_Edate . ' 23:59:59"';
}
##

# 設定保證號碼 #
if ($ge_id) {
    if ($query) {$query .= ' AND';}
    $query .= ' btr.tMemo = "' . $ge_id . '"';
}
##

# 整理成 SQL 字串格式 #
if ($query) {$query = ' AND' . $query;}

$query = '
	SELECT
        btr.tAccount,
        btr.tAccountName,
		btr.tVR_Code,
		btr.tBank_kind,
		btr.tKind,
		btr.tObjKind,
		btr.tMoney,
		btr.tBuyer,
		btr.tSeller,
		btr.tTxt,
		btr.tExport_time,
		btr.tBankLoansDate,
		buy.cCertifiedId,
		buy.cName as buyer,
		buy.cIdentifyId as buyerId,
		own.cCertifiedId,
		own.cName as owner,
		own.cIdentifyId as ownerId,
		rea.cCertifyId,
		rea.cBrand,
		rea.cBranchNum,
		rea.cBranchNum1,
		rea.cBranchNum2,
		rea.cServiceTarget,
		rea.cServiceTarget1,
		rea.cServiceTarget2,
		cas.cId as detail_id
	FROM
		tBankTrans AS btr
	JOIN
		tContractRealestate AS rea ON rea.cCertifyId=btr.tMemo
	JOIN
		tContractOwner AS own ON own.cCertifiedId=rea.cCertifyId
	JOIN
		tContractBuyer AS buy ON buy.cCertifiedId=own.cCertifiedId
	JOIN
		tContractCase AS cas ON cas.cCertifiedId=rea.cCertifyId
	WHERE
		(
			(rea.cBranchNum="' . $acc . '" AND rea.cBrand="' . $acc2 . '" ) OR
			(rea.cBranchNum1="' . $acc . '" AND rea.cBrand1="' . $acc2 . '") OR
			(rea.cBranchNum2="' . $acc . '" AND rea.cBrand2="' . $acc2 . '")
		)
		AND btr.tKind="仲介" AND btr.tStoreId = ' . $acc . '
		' . $query . '
		AND btr.tMemo <> "020364045"
	ORDER BY
		btr.tBankLoansDate
	DESC ;
';

##
//////

$rs = $conn->Execute($query);

while (!$rs->EOF) {

    $type  = '';
    $check = 0;
    if ($rs->fields['cBranchNum'] > 0 && $rs->fields['cBranchNum'] == $acc) {
        $type = $rs->fields['cServiceTarget'];
    } elseif ($rs->fields['cBranchNum1'] > 0 && $rs->fields['cBranchNum1'] == $acc) {
        $type = $rs->fields['cServiceTarget1'];
    } elseif ($rs->fields['cBranchNum2'] > 0 && $rs->fields['cBranchNum2'] == $acc) {
        $type = $rs->fields['cServiceTarget2'];
    }

    if ($rs->fields['tObjKind'] == '仲介服務費') {

        if (($rs->fields['tBuyer'] && !$rs->fields['tSeller']) && $type == 3) { # 有買方金額、無賣方金額時
        $check = 1;
        } else if (!$rs->fields['tBuyer'] && $rs->fields['tSeller'] && $type == 2) { # 無買方金額、有賣方金額時
        $check = 1;
        } else if (($rs->fields['tBuyer'] || $rs->fields['tSeller']) && $type == 1) { # 有買方金額、有賣方金額時
        $check = 1;
        } else { # 無買方金額、無賣方金額時

        }

    } else {
        $check = 1;
    }

    if ($check == 1) {
        $arr[] = $rs->fields;
    }

    $rs->MoveNext();
}
$max = count($arr);

// 找出所有符合的資料並重組

if ($max > 0) {

    # 取得資料起始結束位置
    list($i_begin, $i_end, $current_page, $total_page) = show_range($max, $record_limit, $current_page, $total_page);

    if ($max > 0) {
        $msg = '
			<div style="height:10px;">
			</div>
			<table id="tb001" width="100%">
			<tr style="background-color:#eaeaea;">
				<td width="10%">保證號碼</td>
				<td width="10%">銀行別</td>
				<td width="10%">匯款日期</td>
				<td width="10%">買方</td>
				<td width="10%">賣方</td>
				<td width="10%">項目</td>
				<td width="10%">金額</td>
			</tr>
			';

        for ($i = $i_begin; $i < $i_end; $i++) {
            //修改姓名顯示
            if (strlen($arr[$i]['ownerId']) == 10) { //賣方
                //$arr[$i]['owner'] = newName($arr[$i]['owner']) ;
            }

            if (strlen($arr[$i]['buyerId']) == 10) { //買方
                //$arr[$i]['buyer'] = newName($arr[$i]['buyer']) ;
            }
            ##

            $msg .= '
				<tr style="background-color:';

            if ($i % 2 == 0) {$msg .= '#fff5ee;';} else { $msg .= '#fffafa;';}

            $msg .= '">
					<td rowspan="2" style="width:60pt;">' . substr($arr[$i]['tVR_Code'], 5, 9) . '</td>
					<td>' . $arr[$i]['tBank_kind'] . '</td>
					<td>' . $arr[$i]['tBankLoansDate'] . '</td>
					<td>' . $arr[$i]['buyer'] . '</td>
					<td>' . $arr[$i]['owner'] . '</td>
					';

            if ($arr[$i]['tObjKind'] == '仲介服務費') {
                if ($arr[$i]['tBuyer'] && !$arr[$i]['tSeller']) { # 有買方金額、無賣方金額時
                $msg .= '<td>仲介服務費（買方）</td>';
                    $arr[$i]['tMoney'] = $arr[$i]['tBuyer'] + 1 - 1;
                } else if (!$arr[$i]['tBuyer'] && $arr[$i]['tSeller']) { # 無買方金額、有賣方金額時
                $msg .= '<td>仲介服務費（賣方）</td>';
                    $arr[$i]['tMoney'] = $arr[$i]['tSeller'] + 1 - 1;
                } else if ($arr[$i]['tBuyer'] && $arr[$i]['tSeller']) { # 有買方金額、有賣方金額時
                $msg .= '<td>仲介服務費（買賣方）</td>';
                    $arr[$i]['tMoney'] = $arr[$i]['tBuyer'] + $arr[$i]['tSeller'];
                } else { # 無買方金額、無賣方金額時
                $msg .= '<td>仲介服務費</td>';
                }
            } else {
                $msg .= '<td>' . $arr[$i]['tObjKind'] . '&nbsp;</td>';
            }

            $msg .= '<td>' . number_format($arr[$i]['tMoney']) . '&nbsp;</td>
				</tr>
				<tr style="background-color:';

            if ($i % 2 == 0) {$msg .= '#fff5ee;';} else { $msg .= '#fffafa;';}

            $msg .= '">
					<td>備註</td>
					<td colspan="6" style="text-align:left;width:400px;">' . $arr[$i]['tTxt'] . '</td>
				</tr>
				';
        }

        $msg .= '
			</table>
			';
    } else {
        $msg = '
			<table id="tb001">
			<tr style="background-color:#eaeaea;">
				<td>日期</td>
				<td>保證號碼</td>
				<td>買方</td>
				<td>賣方</td>
				<td>項目</td>
				<td>金額</td>
				<td>備註</td>
			</tr>
			<tr style="background-color:#e0eee0;"><td colspan="7" style="text-align:left">目前尚無任何資料！！</td></tr>
			</table>
			';
    }
} else {
    $msg = '
	<table width="100%">
	<tr style="background-color:#eaeaea;">
		<td width="10%">保證號碼</td>
				<td width="8%">銀行別</td>
				<td width="10%">匯款日期</td>
				<td width="10%">買方</td>
				<td width="10%">賣方</td>
				<td width="10%">項目</td>
				<td width="10%">金額</td>
	</tr>
	<tr style="background-color:#e0eee0;"><td colspan="7" style="text-align:left">目前尚無任何資料！！</td></tr>
	</table>
	';
}

//////

if ($record_limit == 5) {$records_limit .= '<option value="5" selected="selected">5</option>' . "\n";} else { $records_limit .= '<option value="5">5</option>' . "\n";}
if ($record_limit == 10) {$records_limit .= '<option value="10" selected="selected">10</option>' . "\n";} else { $records_limit .= '<option value="10">10</option>' . "\n";}
if ($record_limit == 15) {$records_limit .= '<option value="15" selected="selected">15</option>' . "\n";} else { $records_limit .= '<option value="15">15</option>' . "\n";}
if ($record_limit == 20) {$records_limit .= '<option value="20" selected="selected">20</option>' . "\n";} else { $records_limit .= '<option value="20">20</option>' . "\n";}
if ($record_limit == 25) {$records_limit .= '<option value="25" selected="selected">25</option>' . "\n";} else { $records_limit .= '<option value="25">25</option>' . "\n";}

echo $msg;
$msg2 =
    '<div style="margin:0px auto;width:450px;height:20px;padding:20px;">
						<a href="#tabs-ai"><img src="/images/backward.jpg" style="border:0px;" onclick="back_pg01()"></a>
                        <span style="font-size:9pt;">
                        第&nbsp;<input type="text" size="1" name="current_page" onchange="direct_pg01()" value="' . $current_page . '" style="text-align:right;">&nbsp;頁
                        ／共&nbsp;' . $total_page . '&nbsp;頁
                        </span>
                        <a href="#tabs-ai"><img src="/images/forward.jpg" style="border:0px;" onclick="next_pg01()"></a>

                        <div style="padding:4px;font-size:9pt;">
                        每次顯示&nbsp;
                        <select name="record_limit" size="1" onchange="show_limit01()" style="font-size:9pt;width:48px;">
                        ' . $records_limit . '
                        </select>
                        &nbsp;筆資料
                        顯示第&nbsp;' . $i_begin . '&nbsp;筆到第&nbsp;' . $i_end . '&nbsp;筆的紀錄，共&nbsp;' . $max . '&nbsp;筆紀錄
                    </div>';
echo $msg2;
