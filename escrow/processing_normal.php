<?php
//可能從前台或後台來
if (!$pdo) {$pdo = $pdo62;}
//14 碼保證號碼
$tVR_Code = $data_case['cEscrowBankAccount'];
##
$_cheque = array();
//取得一銀保證號碼之所有交換票據資料
if (preg_match("/^60001/", $tVR_Code) || preg_match("/^55006/", $tVR_Code)) { //若為一銀的案件，則加入票據資料
    $sql = 'SELECT * FROM tExpense_cheque WHERE eDepAccount = "00' . $tVR_Code . '" AND eTradeStatus = "0" ORDER BY eTradeDate ASC;';

    $rs = $pdo->prepare($sql);
    $rs->bindValue(1, $tVR_Code, PDO::PARAM_STR);
    $rs->execute();
    $x = 0;
    while ($tmp = $rs->fetch()) {
        $_cheque[$x]          = $tmp;
        $_cheque[$x]['match'] = 'x';
        $x++;
        unset($tmp);
    }

} else if (preg_match("/^9998[56]0/", $tVR_Code)) { //若為永豐的案件，則加入票據資料
    //取得次交票紀錄(Time to Pay tickets)
    $Time2Pay = array();

    $sql = '
        SELECT
            DISTINCT eCheckNo
        FROM
            tExpense_cheque
        WHERE
            eDepAccount = ?
            AND eTradeStatus = "0"
            AND eCheckDate = "0000000"
        ORDER BY
            eDepAccount
        ASC;
    ';
    $rs = $pdo->prepare($sql);
    $rs->bindValue(1, '00' . $tVR_Code, PDO::PARAM_STR);
    $rs->execute();

    $tmp2 = array();
    while ($tmp = $rs->fetch()) {
        array_push($tmp2, $tmp);

        unset($tmp);
    }
    $y = 0;
    for ($i = 0; $i < count($tmp2); $i++) {
        $sql = 'SELECT * FROM tExpense_cheque WHERE eDepAccount = ? AND eTradeStatus = "0" AND eCheckDate = "0000000" AND eCheckNo = ? ORDER BY eTradeDate DESC LIMIT 1';

        $rs = $pdo->prepare($sql);
        $rs->bindValue(1, '00' . $tVR_Code, PDO::PARAM_STR);
        $rs->bindValue(2, $tmp2[$i]['eCheckNo'], PDO::PARAM_STR);
        $rs->execute();
        // $total = $rs->rowCount();
        while ($tmp = $rs->fetch()) {
            if ($buyerowner == 2 && $tmp['eDepAccount'] == '0099986007014557' && $tmp['id'] == '18151') { //不顯示
                # code...
            } else {
                $Time2Pay[$y]               = $tmp;
                $Time2Pay[$y]['match']      = 'x';
                $Time2Pay[$y++]['Time2Pay'] = '1'; //保留、顯示
                unset($tmp);
            }

        }

    }
    //取得託收票紀錄(Bills for Collection)
    $B4C = array();

    $sql = '
        SELECT
          *
        FROM
          tExpense_cheque
        WHERE
          eDepAccount = ?
          AND eTradeStatus = "0"
          AND eCheckDate <> "0000000"
        ORDER BY
          eDepAccount,eCheckDate
        ASC;
    ';
    $rs = $pdo->prepare($sql);
    $rs->bindValue(1, '00' . $tVR_Code, PDO::PARAM_STR);

    $rs->execute();
    $x = 0;
    while ($tmp = $rs->fetch()) {
        $B4C[$x]             = $tmp;
        $B4C[$x]['match']    = 'x';
        $B4C[$x]['Time2Pay'] = '1'; //保留、顯示
        $x++;
        unset($tmp);
    }

    ##

    //比對當相同紀錄出現時，剔除託收票紀錄
    for ($x = 0; $x < count($Time2Pay); $x++) {
        for ($y = 0; $y < count($B4C); $y++) {
            if ($Time2Pay[$x]['eDepAccount'] == $B4C[$y]['eDepAccount']
                && $Time2Pay[$x]['eCheckNo'] == $B4C[$y]['eCheckNo']
                && $Time2Pay[$x]['eDebit'] == $B4C[$y]['eDebit']
                && $Time2Pay[$x]['eLender'] == $B4C[$y]['eLender']) {

                $B4C[$y]['Time2Pay'] = '2'; //剔除、不顯示
            }
        }
    }

    $B4C = array_merge($B4C, $Time2Pay);
    unset($Time2Pay);

    $y = 0;
    for ($x = 0; $x < count($B4C); $x++) {
        if ($B4C[$x]['Time2Pay'] == '1') { //僅取出保留的票據資料
            $_cheque[$y++] = $B4C[$x];
        }
    }
    unset($B4C);
    ##
} else if (preg_match("/^96988/", $tVR_Code)) { //若為台新的案件，則加入票據資料
    $sql = 'SELECT * FROM tExpense_cheque WHERE eDepAccount = ? AND eTradeStatus = "0" ORDER BY eTradeDate ASC; ';
    $rs  = $pdo->prepare($sql);
    $rs->bindValue(1, '00' . $tVR_Code, PDO::PARAM_STR);
    $rs->execute();
    $x = 0;

    while ($tmp = $rs->fetch()) {
        $_cheque[$x]          = $tmp;
        $_cheque[$x]['match'] = 'x';
        $x++;
        unset($tmp);
    }

}
##
//取得銷帳檔入帳紀錄資料
$query = '
        SELECT
            *,
            (SELECT sName FROM tCategoryIncome WHERE sId = exp.eStatusRemark) eStatusRemarkName,
            eRemarkContent
        FROM
            tExpense AS exp
        JOIN
            tCategoryIncome AS inc ON exp.eStatusRemark = inc.sId
        WHERE
            eDepAccount = ?
            AND exp.eStatusIncome = "2"
            AND exp.eTradeStatus IN ("0" ,"8")
        ORDER BY
           eTradeDate
        ASC ;
    ';

$rs = $pdo->prepare($query);
$rs->bindValue(1, '00' . $tVR_Code, PDO::PARAM_STR);
$rs->execute();

$_income = array();
while ($tmp = $rs->fetch()) {
    $_income[] = $tmp;
    unset($tmp);
}

$income_max = count($_income);
// echo count($tmp);
for ($j = 0; $j < $income_max; $j++) {

    $_income[$j]['match'] = 'x';

    //取得對應調帳之出款檔金額
    $sql = 'SELECT tMoney FROM tBankTrans WHERE tChangeExpense="' . $_income[$j]['id'] . '" AND tVR_Code="' . $tVR_Code . '";';

    $rs = $pdo->prepare($sql);

    $rs->execute();
    $tmpCE   = $rs->fetchAll();
    $_tMoney = 0;

    for ($n = 0; $n < count($tmpCE); $n++) {
        $_tMoney += $tmpCE[$n]['tMoney'] + 1 - 1;
    }

    $_tMoney *= 100;
    $_income[$j]['eLender'] -= $_tMoney;
    ##
}
##
//print_r($_income) ; print_r($_cheque) ; exit ;

//檢核票據是否已兌現 &&($_income[$j]['eTradeStatus'] == '8')             //交易狀態為票據交易
if (preg_match("/^60001/", $tVR_Code) || preg_match("/^55006/", $tVR_Code)) { //一銀的保證號碼帳號
    for ($x = 0; $x < count($_cheque); $x++) { //票據交易(8)
        for ($j = 0; $j < count($_income); $j++) {
            if (($_cheque[$x]['eDepAccount'] == $_income[$j]['eDepAccount']) //保證號碼相同
                && ($_income[$j]['eTradeCode'] == '1793') //交易代碼為1950
                && ($_cheque[$x]['eDebit'] == $_income[$j]['eDebit']) //支出金額相符
                && ($_cheque[$x]['eLender'] == $_income[$j]['eLender']) //收入金額相符

                && ($_cheque[$x]['eTradeDate'] < $_income[$j]['eTradeDate']) //票據日期須小於銷帳日期
                && ($_income[$j]['match'] == 'x')) { //銷帳紀錄須未被配對

                $_income[$j]['match']  = '1'; //在銷帳紀錄中找到支票紀錄
                $_cheque[$x]['match']  = '1'; //在支票紀錄中找到銷帳紀錄
                $_income[$j]['remark'] = ' 本款項由' . tDate_check($_cheque[$x]['eTradeDate'], 'md', 'b', '/', 0, 0) . '支票兌現';
                break;
            }
        }
        //print_r($_income) ; print_r($_cheque) ; exit ;
    }
    //echo 'AAA' ; exit ;
    for ($x = 0; $x < count($_cheque); $x++) { //正常交易(0)
        for ($j = 0; $j < count($_income); $j++) {
            if (($_cheque[$x]['eDepAccount'] == $_income[$j]['eDepAccount']) //保證號碼相同
                && ($_income[$j]['eTradeCode'] == '1950') //交易代碼為1950
                && ($_cheque[$x]['eDebit'] == $_income[$j]['eDebit']) //支出金額相符
                && ($_cheque[$x]['eLender'] == $_income[$j]['eLender']) //收入金額相符
                && ($_income[$j]['eTradeStatus'] == '0') //交易狀態為票據交易
                && ($_cheque[$x]['eTradeDate'] < $_income[$j]['eTradeDate']) //票據日期須小於銷帳日期
                && ($_income[$j]['match'] == 'x')) { //銷帳紀錄須未被配對(重要)

                $_income[$j]['match']  = '1'; //在銷帳紀錄中找到支票紀錄
                $_cheque[$x]['match']  = '1'; //在支票紀錄中找到銷帳紀錄
                $_income[$j]['remark'] = ' 本款項由' . tDate_check($_cheque[$x]['eTradeDate'], 'md', 'b', '/', 0, 0) . '支票兌現';
                break;
            }
        }
    }
} else if (preg_match("/^9998[56]0/", $tVR_Code)) { //永豐的保證號碼帳號
    for ($x = 0; $x < count($_cheque); $x++) { //票據交易
        for ($j = 0; $j < count($_income); $j++) {
            if (($_cheque[$x]['eDepAccount'] == $_income[$j]['eDepAccount']) //保證號碼相同
                && ($_cheque[$x]['eDebit'] == $_income[$j]['eDebit']) //支出金額相符
                && ($_cheque[$x]['eLender'] == $_income[$j]['eLender']) //收入金額相符
                && ($_income[$j]['eSummary'] == '票據轉入') //交易摘要為票據轉入
                && ($_cheque[$x]['eCheckNo'] == $_income[$j]['eCheckNo']) //支票號碼相同
                && ($_income[$j]['match'] == 'x')) { //銷帳紀錄須未被配對

                $_income[$j]['match']  = '1'; //在銷帳紀錄中找到支票紀錄
                $_cheque[$x]['match']  = '1'; //在支票紀錄中找到銷帳紀錄
                $_income[$j]['remark'] = ' 本款項由' . tDate_check($_cheque[$x]['eTradeDate'], 'md', 'b', '/', 0, 0) . '支票兌現';
                break;
            }
            //這筆是票據入帳，調帳沒有條全部金額 所以強制用寫死的方式 入帳狀態用2 非3
            if ($tVR_Code == '99986006034599') {
                $_income[$j]['match']  = '1'; //在銷帳紀錄中找到支票紀錄
                $_cheque[$x]['match']  = '1'; //在支票紀錄中找到銷帳紀錄
                $_income[$j]['remark'] = ' 本款項由' . tDate_check($_cheque[$x]['eTradeDate'], 'md', 'b', '/', 0, 0) . '支票兌現';
                break;
            }
        }
    }
} else if (preg_match("/^96988/", $tVR_Code)) { //台新的保證號碼帳號
    for ($x = 0; $x < count($_cheque); $x++) { //正常交易(0)
        for ($j = 0; $j < count($_income); $j++) {
            if (($_cheque[$x]['eDepAccount'] == $_income[$j]['eDepAccount']) //保證號碼相同
                && ($_income[$j]['eTradeCode'] == 'PDC') //交易代碼為 PDC 票據交易
                && ($_cheque[$x]['eDebit'] == $_income[$j]['eDebit']) //支出金額相符
                && ($_cheque[$x]['eLender'] == $_income[$j]['eLender']) //收入金額相符
                && ($_income[$j]['eTradeStatus'] == '0') //交易狀態為票據交易
                && ($_cheque[$x]['eTradeDate'] < $_income[$j]['eTradeDate']) //票據日期須小於銷帳日期
                && ($_income[$j]['match'] == 'x')) { //銷帳紀錄須未被配對(重要)

                $_income[$j]['match']  = '1'; //在銷帳紀錄中找到支票紀錄
                $_cheque[$x]['match']  = '1'; //在支票紀錄中找到銷帳紀錄
                $_income[$j]['remark'] = ' 本款項由' . tDate_check($_cheque[$x]['eTradeDate'], 'md', 'b', '/', 0, 0) . '支票兌現';
                break;
            }
        }
    }
}
##
//取出未兌現支票據資料
$j      = 0;
$cheque = array();
for ($x = 0; $x < count($_cheque); $x++) { //將未標記之票據紀錄取出
    //echo "Cheque=".$_cheque[$x]['eLender']."<br>\n" ;
    if ($_cheque[$x]['id'] != '16500' || ($buyerowner == 1 && $_cheque[$x]['id'] == '16500')) { ////20171023 佩琦 006055907 有一張票據待兌現70萬 請不要顯示在賣方官網 幫拉掉
        if ($_cheque[$x]['eTipDate'] != '' || ($_cheque[$x]['eCheckDate'] != '0000000' && $_cheque[$x]['eCheckDate'] != '')) { //如果是託收票  以到期日加一日為兌現日

            $_expire_date = tDate_check($_cheque[$x]['eCheckDate'], 'ymd', 'b', '-', 1, 1); //

        } else {
            $_expire_date = tDate_check($_cheque[$x]['eTradeDate'], 'ymd', 'b', '-', 3, 1); //票據(預計)兌現時間

        }
        if ($_expire_date <= date("Y-m-d")) { //若今日超過兌現時間，則不顯示
            $_cheque[$x]['match'] = '1';
        }

        if ($_cheque[$x]['match'] == 'x') {
            $cheque[$j]           = $_cheque[$x];
            $cheque[$j]['cheque'] = '1';

            $j++;
        }
    }

}
unset($_cheque);
##

//print_r($_income) ; print_r($_cheque) ; exit ;
//合併顯示
$income_arr = array_merge($_income, $cheque);
// echo "<pre>";
//  print_r($_income);
//  echo "</pre>";

unset($_income);unset($cheque);
##
//20160921佩琪要求應部分調帳關係，金額沒辦法顯示，這一筆入帳日期2016-09-13 其他 NT$65600(僅顯示給買方)
if ($member['cCertifiedId'] == '004047873' && $acc == 'R100261188') {
    $cc                                = count($income_arr);
    $income_arr[$cc]['eTradeDate']     = '1050913';
    $income_arr[$cc]['eLender']        = '6560000';
    $income_arr[$cc]['eRemarkContent'] = '其他';
    $income_arr[$cc]['match']          = 'x';
    unset($cc);
}

//排序
for ($j = 0; $j < count($income_arr); $j++) {
    for ($x = 0; $x < (count($income_arr) - 1); $x++) {
        if ($income_arr[$x]['eTradeDate'] > $income_arr[($x + 1)]['eTradeDate']) {
            $tmp                  = $income_arr[$x];
            $income_arr[$x]       = $income_arr[($x + 1)];
            $income_arr[($x + 1)] = $tmp;
            unset($tmp);
        }
    }
}
##

//20221130 惠婷要求針對 111640211 進行入賬調整
if ($member['cCertifiedId'] == '111640211') {
    for ($x = 0; $x < count($income_arr); $x++) {

        if ($income_arr[$x]['id'] == '898997') {
            $income_arr[$x]['eLender'] = '000000500000000';
        }

        if ($income_arr[$x]['id'] == '899027') {
            $income_arr[$x] = null;
            unset($income_arr[$x]);
        }

        if ($income_arr[$x]['id'] == '899030') {
            $income_arr[$x] = null;
            unset($income_arr[$x]);
        }
    }
    $_cnt       = null;unset($_cnt);
    $income_arr = array_values($income_arr);
}
##
?>


<table class="ct_table">
  	<tbody>
        <tr class="odd-row-b">
  			<th colspan="3"></th>
  			<th><span src="../images/loader.gif" popup_block style="display:none;" id="loader"></span></th>
  		</tr>
  		<tr class="odd-row">
  			<th width="30%" height="35" class="first">入帳日期</th>
  			<th width="30%">帳款摘要</th>
  			<th width="20%">收入</th>
  			<th width="20%" class="last">備註</th>
  		</tr>
  		<?php
//印出
for ($j = 0; $j < count($income_arr); $j++) {
    if ($income_arr[$j]['eStatusRemark'] == '0') {
        $income_arr[$j]['eStatusRemarkName'] = '';
    }

    if ($income_arr[$j]['eStatusIncome'] == '3') { //調帳時之顯示
        //if ($income_arr[$j]['eChangeMoney'] > 0) {    //若調帳後仍有餘額時顯示
        if (($income_arr[$j]['eChangeMoney'] > 0) && ($income_arr[$j]['eChangeMoney'] != $income_arr[$j]['eLender'])) { //若調帳後仍有餘額時顯示
            if ($income_arr[$j]['cheque'] == '1') {
                $color = '#000080';
            } else {
                $color = '#000000';
            }

            echo '<tr class="intable">' . "\n";

            //顯示日期
            echo '<td >';
            echo tDate_check($income_arr[$j]['eTradeDate'], 'ymd', 'b', '-', 0, 0);
            echo "</td>\n";
            ##

            //顯示摘要、收入與備註
            if ($income_arr[$j]['cheque'] == '1') {
                echo '<td >支票</td>' . "\n";
                echo '<td style="text-align:right;">(NT$' . number_format($income_arr[$j]['eLender']) . ')</td>' . "\n";

                if (($income_arr[$j]['eCheckDate'] != '') && ($income_arr[$j]['eCheckDate'] != '0000000')) {
                    $_tDate = tDate_check($income_arr[$j]['eCheckDate'], 'md', 'b', '/', 1, 0);
                    echo '<td >未兌現、預計' . $_tDate . "兌現</td>\n";
                } else {
                    echo '<td >未兌現、預計二日後兌現</td>' . "\n";
                }
            } else {
                if ($buyerowner == 2 || $tVR_Code == '60001081235074') {
                    //過濾買方應付款
                    $sql = "SELECT eTitle,eMoney FROM tExpenseDetailSmsOther WHERE eExpenseId = '" . $income_arr[$j]['id'] . "' AND eDel = 0";

                    $rs = $pdo->prepare($sql);

                    $rs->execute();
                    $tmpMoney  = 0;
                    $checkItem = 0;
                    while ($tmpDetail = $rs->fetch()) {
                        // if ($tmpDetail['eTitle'] == '買方應付款項') {
                        $tmpMoney += $tmpDetail['eMoney'];
                        $checkItem = 1; //有買方應付款項細項
                        // }
                        unset($tmpDetail);
                    }

                    $tmp  = explode('+', $income_arr[$j]['eRemarkContent']);
                    $text = '';
                    for ($i = 0; $i < count($tmp); $i++) {
                        if ($tmp[$i] != '' && (!preg_match("/買方/", $tmp[$i]) && !preg_match("/契稅/", $tmp[$i]) && !preg_match("/印花稅/", $tmp[$i]))) {
                            $text .= '+' . $tmp[$i];
                        }

                    }

                    $income_arr[$j]['eRemarkContent'] = $text; //implode('+', $tmp)
                    unset($tmp);
                }
                $income_arr[$j]['eExplain'] = str_replace('買方服務費', '', $income_arr[$j]['eExplain']);

                echo '<td>' . $income_arr[$j]['eStatusRemarkName'] . $income_arr[$j]['eRemarkContent'] . "</td>\n";
                echo "<td >" . 'NT$';
                if ($buyerowner == '2') { //賣方
                    if ($tVR_Code == '60001081235074') {
                        echo number_format($income_arr[$j]['eChangeMoney']);
                    } else {
                        echo number_format($income_arr[$j]['eChangeMoney'] - $income_arr[$j]['eBuyerMoney'] - $income_arr[$j]['eExtraMoney'] - $tmpMoney + 1 - 1);
                    }

                } else { //買方
                    echo number_format($income_arr[$j]['eChangeMoney']);
                }

                echo "</td>\n";
                echo "<td>" . $income_arr[$j]['eExplain'] . "</td>\n";
            }
            ##

            echo "</tr>\n";
        }
    } else { //正常入帳時之顯示
        $income_arr[$j]['eLender'] = substr($income_arr[$j]['eLender'], 0, -2);

        if ($income_arr[$j]['cheque'] == '1') {
            $color = '#000080';
        } else {
            $color = '#000000';
        }

        if (($income_arr[$j]['eLender'] - $income_arr[$j]['eBuyerMoney'] + 1 - 1 != 0) || $buyerowner == 1) { //如果賣方的金額為0就不顯示
            //顯示摘要、收入與備註
            if ($income_arr[$j]['cheque'] == '1') {
                echo '<tr >' . "\n";

                //顯示日期
                echo '<td >';
                echo tDate_check($income_arr[$j]['eTradeDate'], 'ymd', 'b', '-', 0, 0);
                echo "</td>\n";
                ##
                echo '<td >支票</td>' . "\n";
                echo '<td style="text-align:right;">(NT$' . number_format($income_arr[$j]['eLender']) . ')</td>' . "\n";

                if (($income_arr[$j]['eCheckDate'] != '') && ($income_arr[$j]['eCheckDate'] != '0000000')) {
                    $_tDate = tDate_check($income_arr[$j]['eCheckDate'], 'md', 'b', '/', 1, 0);
                    echo '<td >未兌現、預計' . $_tDate . "兌現</td>\n";
                } else {
                    echo '<td >未兌現、預計二日後兌現</td>' . "\n";
                }
                echo "</tr>\n";
            } else {
                if ($buyerowner == 2 || $tVR_Code == '60001081235074') {
                    $sql = "SELECT eTitle,eMoney FROM tExpenseDetailSmsOther WHERE eExpenseId = '" . $income_arr[$j]['id'] . "' AND eDel = 0";

                    $rs = $pdo->prepare($sql);

                    $rs->execute();
                    $tmpMoney = 0;

                    $checkItem = 0;
                    while ($tmpDetail = $rs->fetch()) {
                        // if ($tmpDetail['eTitle'] == '買方應付款項') {
                        $tmpMoney += $tmpDetail['eMoney'];
                        $checkItem = 1; //有買方應付款項細項
                        // }
                        unset($tmpDetail);
                    }

                    $tmp  = explode('+', $income_arr[$j]['eRemarkContent']);
                    $text = '';
                    for ($i = 0; $i < count($tmp); $i++) {
                        if ($tmp[$i] != '' && (!preg_match("/買方/", $tmp[$i]) && !preg_match("/契稅/", $tmp[$i]) && !preg_match("/印花稅/", $tmp[$i]))) {
                            $text .= '+' . $tmp[$i];
                        }

                    }

                    $income_arr[$j]['eRemarkContent'] = $text; //implode('+', $tmp)
                    unset($tmp);
                }

                $income_arr[$j]['eExplain'] = str_replace('買方服務費', '', $income_arr[$j]['eExplain']);

                if ($buyerowner == '2') { //賣方
                    if ($tVR_Code == '60001081235074') {
                        $tmpM = $income_arr[$j]['eLender'];
                    } else {
                        $tmpM = $income_arr[$j]['eLender'] - $income_arr[$j]['eBuyerMoney'] - $income_arr[$j]['eExtraMoney'] - $tmpMoney + 1 - 1;

                    }
                } else { //買方
                    $tmpM = $income_arr[$j]['eLender'];

                }
                #針對55006121465825 調整官網顯示
                if($tVR_Code == '55006121465825' and $income_arr[$j]['eTradeDate'] == '1130229') {
                    $tmpM = '27500';
                }

                // if (($tmpM > 0) && ($income_arr[$j]['id'] != 899121)) {
                if ($tmpM > 0) {
                    echo '<tr >' . "\n";

                    //顯示日期
                    echo '<td >';
                    echo tDate_check($income_arr[$j]['eTradeDate'], 'ymd', 'b', '-', 0, 0);
                    echo "</td>\n";
                    ##
                    echo '<td>' . $income_arr[$j]['eStatusRemarkName'] . $income_arr[$j]['eRemarkContent'] . "</td>\n";
                    echo "<td style='text-align:right;'>" . 'NT$';
                    echo number_format($tmpM);
                    echo "</td>\n";
                    echo "<td>" . $income_arr[$j]['eExplain'] . "</td>\n";
                    echo "</tr>\n";

                }
                unset($tmpM);
            }
            ##

        }

    }
}
##

?>
  	</tbody>
</table>