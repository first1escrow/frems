<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

$cId = $_GET['cId'];

$in    = $out    = $records    = [];
$alert = '';
if (preg_match("/^\d{9}$/", $cId)) {
    $conn = new first1DB;

    //存檔
    if ($_POST['save'] == 'OK') {
        $sql = 'DELETE FROM tBuyerOwnerWebShow WHERE bCertifiedId = :cId;';
        if ($conn->exeSql($sql, ['cId' => $cId])) {
            if (!empty($_POST['buyerWebShow'])) {
                $b = [];
                foreach ($_POST['buyerWebShow'] as $k => $v) {
                    $detail             = json_decode(urldecode($_POST['detail'][$k]), true);
                    $detail['income']   = str_replace(',', '', $detail['income']);
                    $detail['outgoing'] = str_replace(',', '', $detail['outgoing']);
                    $b[]                = '(UUID(), "' . $cId . '", "B", "' . $detail['date'] . '","' . $detail['income'] . '","' . $detail['outgoing'] . '","' . $detail['detail'] . '","' . $detail['remark'] . '","' . $detail['hash'] . '", NOW())';
                    $detail             = null;unset($detail);
                }

                $sql = 'INSERT INTO
                            tBuyerOwnerWebShow
                        (
                            bId, bCertifiedId, bTarget, bDate, bIncome, bOutgoing, bDetail, bRemark, bHashId, bCreatedAt
                        )
                        VALUES ' . implode(',', $b) . ';';
                $conn->exeSql($sql);

                $b = null;unset($b);
            }

            if (!empty($_POST['ownerWebShow'])) {
                $o = [];
                foreach ($_POST['ownerWebShow'] as $k => $v) {
                    $detail             = json_decode(urldecode($_POST['detail'][$k]), true);
                    $detail['income']   = str_replace(',', '', $detail['income']);
                    $detail['outgoing'] = str_replace(',', '', $detail['outgoing']);

                    $o[]    = '(UUID(), "' . $cId . '", "O", "' . $detail['date'] . '","' . $detail['income'] . '","' . $detail['outgoing'] . '","' . $detail['detail'] . '","' . $detail['remark'] . '","' . $detail['hash'] . '", NOW())';
                    $detail = null;unset($detail);
                }

                $sql = 'INSERT INTO
                            tBuyerOwnerWebShow
                        (
                            bId, bCertifiedId, bTarget, bDate, bIncome, bOutgoing, bDetail, bRemark, bHashId, bCreatedAt
                        )
                        VALUES ' . implode(',', $o) . ';';
                $conn->exeSql($sql);

                $o = null;unset($o);
            }

            $alert = '<script>alert("已更新");</script>';
        } else {
            $alert = '<script>alert("更新失敗");</script>';
        }
    }
    ##

    //有勾選的項目
    $sql = 'SELECT bId, bTarget, bTransId, bExpId, bHashId FROM tBuyerOwnerWebShow WHERE bCertifiedId = :cId;';
    $rs  = $conn->all($sql, ['cId' => $cId]);

    $owner = $ownerExp = [];
    $buyer = $buyerExp = [];
    if (!empty($rs)) {
        foreach ($rs as $v) {
            switch ($v['bTarget']) {
                case 'B':
                    // $buyer[]    = $v['bTransId'];
                    // $buyerExp[] = $v['bExpId'];
                    $buyer[] = $v['bHashId'];
                    // $buyerExp[] = $v['bHashId'];
                    break;
                case 'O':
                    // $owner[]    = $v['bTransId'];
                    // $ownerExp[] = $v['bExpId'];
                    $owner[] = $v['bHashId'];
                    // $ownerExp[] = $v['bHashId'];
                    break;
            }
        }
    }
    ##

    //所有出款紀錄
    $sql = 'SELECT
                tBankLoansDate as tExport_time,
                tObjKind,
                tKind,
                tMoney,
                tTxt,
                tId,
                tShow,
                tObjKind2Item,
                tBank_kind,
                tObjKind2,
                tAccountName,
                tPayOk
            FROM
                tBankTrans
            WHERE
                tMemo = "' . $cId . '" AND tObjKind2 != "02"
            ORDER BY
                tExport_time
            ASC ;';
    $rs = $conn->all($sql);

    if (!empty($rs)) {
        foreach ($rs as $k => $v) {
            if($v['tExport_time'] >= '2025-01-10') {
                $hash = hash('sha256', substr($v['tExport_time'], 0, 10) . $v['tObjKind'] . '0' . $v['tMoney'] . $v['tTxt'] .  $v['tId']);
            } else {
                $hash = hash('sha256', substr($v['tExport_time'], 0, 10) . $v['tObjKind'] . '0' . $v['tMoney'] . $v['tTxt']);
            }

            $out[$k] = [
                'date'     => substr($v['tExport_time'], 0, 10),
                'detail'   => $v['tObjKind'],
                'income'   => 0,
                'outgoing' => number_format($v['tMoney']),
                'remark'   => $v['tTxt'],
                'obj'      => '2', // 2 表示為支出
                'tran_id'  => $v['tId'], //出款ID
                'show'     => $v['tShow'],
                'hash'     => $hash,
            ];

            if ($v['tBank_kind'] == '台新' && $v['tObjKind2'] == '01') {
                $out[$k]['taishinSp'] = ($v['tObjKind2Item'] != '') ? '已返還代墊款' : '未返還代墊款';
            }
            $out[$k]['json'] = urlencode(json_encode($out[$k]));
        }
    }
    ##

    //所有入款資訊

    // 明細(存入)
    $sql = '
		SELECT
			id,
			eTradeDate,
			eDebit,
			eLender,
			eChangeMoney,
			eStatusIncome,
			eBuyerMoney,
			eExtraMoney,
			eDepAccount,
			(SELECT sName FROM tCategoryIncome WHERE sId=a.eStatusRemark) as sName,
			eRemarkContent
		FROM
			tExpense AS a
		WHERE
			eDepAccount LIKE "%' . $cId . '"
			AND eTradeStatus="0"
			AND ePayTitle<>"網路整批"
		ORDER BY
			eLastTime
		ASC;
	';
    $result = $conn->all($sql);

    foreach ($result as $rs) {
        $_money1   = (int) substr($rs["eLender"], 0, 13); // 存入
        $_money2   = (int) substr($rs["eDebit"], 0, 13); // 支出
        $_buyer    = (int) substr($rs["eBuyerMoney"], 0, 13); // 扣除買方服務費
        $_buyer2   = (int) $rs['eExtraMoney'];
        $tmp_check = 0; //1 買方服務費  2買方溢入款
        if ($_buyer > 0) {$_money1 = $_money1 - $_buyer;
            $tmp_check += 1;} //
        if ($_buyer2 > 0) {$_money1 = $_money1 - $_buyer2;
            $tmp_check += 2;}

        $_total = $_money1 - $_money2;
        $_y     = substr($rs["eTradeDate"], 0, 3) + 1911;
        $_m     = substr($rs["eTradeDate"], 3, 2);
        $_d     = substr($rs["eTradeDate"], 5, 2);
        $_date  = $_y . '-' . $_m . '-' . $_d;

        if ($rs["eStatusIncome"] != "3" && $rs["eStatusIncome"] != "4") { // 調帳交易不顯示
            $arr[] = array(
                'date'   => $_date,
                'money1' => $_money1,
                'money2' => $_money2,
                'total'  => $_total,
                'kind'   => $rs['sName'],
                'txt'    => $rs['eRemarkContent'],
                'expId'  => $rs['id'],
                'check'  => $tmp_check,
                'hash'   => hash('sha256', $_date . $rs['sName'] . $_money1 . $_money2 . $rs['eRemarkContent']),
            );
        }
    }

    //設定 tExpenseDetail 變更出款日期
    $sql = 'SELECT tExport_time FROM tBankTrans WHERE tVR_Code="' . $tVR_Code . '" AND tObjKind="扣繳稅款";';
    $rs  = $conn->one($sql);

    $tmp_date = explode("-", substr($rs['tExport_time'], 0, 10));
    if (count($tmp_date) > 0) {
        $exp_date = implode('-', $tmp_date);
    }
    unset($tmp_date);
    ##

    foreach ($arr as $k => $v) {
        //取得明細部分買方分配總金額並將賣方入帳金額扣除買方支出
        $sql = 'SELECT SUM(eMoney) as M FROM tExpenseDetail WHERE eExpenseId="' . $v['expId'] . '" AND eTarget="3"; ';
        $rs  = $conn->one($sql);
        $v['money1'] -= (int) $rs['M']; //扣除買方明細加總金額
        $tmp = null;unset($tmp);
        ##

        //取出賣方明細部分出款
        $sql = 'SELECT
                    *,
                    (SELECT cName FROM tCategoryExpense WHERE cId=a.eItem) as kind,
                    (SELECT tBankLoansDate FROM  tBankTrans WHERE tId=a.eOK) AS tBankLoansDate
                FROM
                    tExpenseDetail AS a
                WHERE
                    eExpenseId="' . $v['expId'] . '"
                    AND eOK != ""';
        $result = $conn->all($sql);

        foreach ($result as $rs) {
            if (!empty($rs['tBankLoansDate'])) {
                $money2 = (int) $rs['eMoney'];
                if (!$exp_date) {
                    $exp_date = $v['date'];
                }

                $tmp_date             = explode("-", substr($rs['tBankLoansDate'], 0, 10));
                $rs['tBankLoansDate'] = $tmp_date[0] . '-' . $tmp_date[1] . '-' . $tmp_date[2];
                unset($tmp_date);

                $a[] = array(
                    'date'     => $rs['tBankLoansDate'],
                    'income'   => 0,
                    'outgoing' => $money2,
                    'detail'   => $rs['kind'],
                    'expId'    => $v['eExpenseId'],
                    'hash'     => hash('sha256', $rs['tBankLoansDate'] . $rs['kind'] . 0 . $money2 . $v['txt'] . $v['eExpenseId'])
                );

                //20230119 當有對應到出款檔案時，則將出款紀錄刪除
                foreach ($out as $_k => $_v) {
                    if ($_v['tran_id'] == $rs['eOK']) {
                        unset($out[$_k]);
                    }
                }
                ##
            }
        }
        ##

        //主要入款紀錄
        $sql = "SELECT * FROM tExpenseDetailSms WHERE eExpenseId = '" . $v['expId'] . "'";
        $rs  = $conn->one($sql);

        $in_check = 0;
        if ($rs['eSignMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'     => $v['date'],
                'income'   => $rs['eSignMoney'],
                'outgoing' => 0,
                'detail'   => '簽約款',
                'remark'   => '',
                'expId'    => $v['expId'],
                'hash'     => hash('sha256', $v['date'] . '簽約款' . $rs['eSignMoney'] . '0' . ''),
            );
        }

        if ($rs['eAffixMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'     => $v['date'],
                'income'   => $rs['eAffixMoney'],
                'outgoing' => 0,
                'detail'   => '用印款',
                'remark'   => '',
                'expId'    => $v['expId'],
                'hash'     => hash('sha256', $v['date'] . '用印款' . $rs['eAffixMoney'] . '0' . ''),
            );
        }

        if ($rs['eDutyMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'     => $v['date'],
                'income'   => $rs['eDutyMoney'],
                'outgoing' => 0,
                'detail'   => '完稅款',
                'remark'   => '',
                'expId'    => $v['expId'],
                'hash'     => hash('sha256', $v['date'] . '完稅款' . $rs['eDutyMoney'] . '0' . ''),
            );
        }

        if ($rs['eEstimatedMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'     => $v['date'],
                'income'   => $rs['eEstimatedMoney'],
                'outgoing' => 0,
                'detail'   => '尾款',
                'remark'   => '',
                'expId'    => $v['expId'],
                'hash'     => hash('sha256', $v['date'] . '尾款' . $rs['eEstimatedMoney'] . '0' . ''),
            );
        }

        if ($rs['eEstimatedMoney2'] > 0) {
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'     => $v['date'],
                'income'   => $rs['eEstimatedMoney2'],
                'outgoing' => 0,
                'detail'   => '尾款差額',
                'remark'   => '',
                'expId'    => $v['expId'],
                'hash'     => hash('sha256', $v['date'] . '尾款差額' . $rs['eEstimatedMoney2'] . '0' . ''),
            );
        }

        if ($rs['eServiceFee'] > 0) { //
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'     => $v['date'],
                'income'   => $rs['eServiceFee'],
                'outgoing' => 0,
                'detail'   => '買方仲介服務費',
                'remark'   => '',
                'expId'    => $v['expId'],
                'hash'     => hash('sha256', $v['date'] . '買方仲介服務費' . $rs['eServiceFee'] . '0' . ''),
            );
        }

        if ($rs['eCompensationMoney'] > 0) { //
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'     => $v['date'],
                'income'   => $rs['eCompensationMoney'],
                'outgoing' => 0,
                'detail'   => '代償後餘額',
                'remark'   => '',
                'expId'    => $v['expId'],
                'hash'     => hash('sha256', $v['date'] . '代償後餘額' . $rs['eCompensationMoney'] . '0' . ''),
            );
        }

        $sql    = "SELECT * FROM tExpenseDetailSmsOther WHERE eExpenseId = '" . $v['expId'] . "' AND eDel = 0";
        $result = $conn->all($sql);

        foreach ($result as $rs) {
            if ($rs['eMoney'] > 0) {
                $in_check = 1; //有輸入金額

                $a[] = array(
                    'date'     => $v['date'],
                    'income'   => $rs['eMoney'],
                    'outgoing' => 0,
                    'detail'   => $rs['eTitle'],
                    'remark'   => '',
                    'expId'    => $v['expId'],
                    'hash'     => hash('sha256', $v['date'] . $rs['eTitle'] . $rs['eMoney'] . '0' . ''),
                );
            }
        }
        $tmp = $tmp2 = null;
        unset($tmp, $tmp2);

        if ($in_check == 0) {
            $tmp = explode('+', $v['txt']);

            for ($i = 0; $i < count($tmp); $i++) {
                if ($v['check'] == 1) { //1 買方服務費  2買方溢入款
                    if (preg_match("/買方/", $tmp[$i]) && preg_match("/服務費/", $tmp[$i])) {
                        unset($tmp[$i]);
                    }
                } else if ($v['check'] == 2) {
                    if (preg_match("/買方溢入款/", $tmp[$i])) {
                        unset($tmp[$i]);
                    }
                } else if ($v['check'] == 3) {
                    if (preg_match("/買方/", $tmp[$i]) && preg_match("/服務費/", $tmp[$i])) {
                        unset($tmp[$i]);
                    } else if (preg_match("/買方溢入款/", $tmp[$i])) {
                        unset($tmp[$i]);
                    }
                }
            }

            if ($v['txt'] != '') {
                $v['txt'] = @implode('+', $tmp);
            }
            unset($tmp);

            $a[] = array(
                'date'     => $v['date'],
                'detail'   => $v['kind'],
                'income'   => $v['money1'],
                'outgoing' => $v['money2'],
                'remark'   => $v['txt'],
                'expId'    => $v['expId'],
                'hash'     => hash('sha256', $v['date'] . $v['kind'] . $v['money1'] . $v['money2'] . $v['txt']),
            );
        }
        $in_check = null;unset($in_check);
        ##
    }
    $arr = null;unset($arr);
    ##

    //收款
    $in = [];
    if (!empty($a)) {
        foreach ($a as $v) {
            if (($v['income'] > 0) || ($v['outgoing'] > 0)) {
                $v['json']     = urlencode(json_encode($v));
                $v['income']   = number_format($v['income']);
                $v['outgoing'] = number_format($v['outgoing']);

                $in[] = $v;
            }
        }
    }
    ##

    $records = array_merge($in, $out);
    usort($records, function ($a, $b) {
        if ($a['date'] == $b['date']) {
            return 0;
        }

        return ($a['date'] < $b['date']) ? -1 : 1;
    });
    ##
}

$smarty->assign('buyer', $buyer);
$smarty->assign('buyerExp', $buyerExp);
$smarty->assign('owner', $owner);
$smarty->assign('ownerExp', $ownerExp);
$smarty->assign('records', $records);
$smarty->assign('alert', $alert);

$smarty->display("buyerOwnerWebDetail.inc.tpl", "", "escrow");