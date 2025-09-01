<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCase.class.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/web_addr.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once dirname(dirname(__DIR__)) . '/class/slack.class.php';


use First1\V1\Notify\Slack;
use First1\V1\PayByCase\PayByCase;

$_SESSION['alert'] = null;unset($_SESSION['alert']);

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '查看/編修待出款案件明細');

$radiokind   = $_POST["radiokind"];
$vr_code     = $_POST["vr_code"];
$bankRelay   = $_POST["bankRelay"];
$_target_len = count($radiokind);
$_vr_code    = substr($vr_code, 5);
$smsSend     = $_POST['smsSend'];
$tLegalAllow = (!empty($_POST['tLegalAllow']) && in_array($_POST['tLegalAllow'], ['0', '1', '2'])) ? $_POST['tLegalAllow'] : '0';
$invoice = 0; //是否開過發票

//20230420 確認隨案支付案件是否已確認完成
//20241231 增加 再確認隨案支付案件是否已確認完成(ex:隨案代書被異動調整後取消)
if (in_array($radiokind, ['點交', '解除契約', '建經發函終止', '預售屋'])) {
    $paybycase = new PayByCase;

    $pay_by_case = $paybycase->getPayByCase($_vr_code);
    if (!empty($pay_by_case)) {
        if($pay_by_case['fMultipleFeedback'] == 1) {
            Slack::channelSend('保證號碼：'.$_vr_code .'雙代書點交');
        }
        $_alert = [];

        if (empty($pay_by_case['fSalesConfirmDate'])) {
            $_alert[] = '業務';
        }

        if (empty($pay_by_case['fAccountantConfirmDate'])) {
            $_alert[] = '會計';
        }

        if (!empty($_alert)) {
            $_SESSION['alert'] = implode('與', $_alert) . '人員尚未確認完成';
            $_alert            = null;unset($_alert);

            header('Location: /bank/new/out1.php?vr=' . $vr_code);
            exit;
        }
    }

    $pay_by_case_log = $paybycase->getPayByCaseLogNotConfirm($_vr_code);
    if (!empty($pay_by_case_log)) {
        $_alert = [];

        if (empty($pay_by_case_log['fSalesConfirmDate'])) {
            $_alert[] = '業務';
        }

        if (!empty($_alert)) {
            $_SESSION['alert'] = implode('與', $_alert) . '人員尚未確認完成';
            $_alert            = null;unset($_alert);

            header('Location: /bank/new/out1.php?vr=' . $vr_code);
            exit;
        }
    }
}
##

//仲介服務費處理
if ($radiokind == '買方仲介服務費') {
    $checkIden = 3;
    $radiokind = '仲介服務費';
} else if ($radiokind == '賣方仲介服務費') {
    $checkIden = 2;
    $radiokind = '仲介服務費';
}
##

//儲存自訂對象
if ($smsSend == 2 && $_POST["save"] != 'ok') {
    $allForm = $_POST['allForm'];

    $ra = ($radiokind == '點交') ? '點交(結案)' : $radiokind;

    //清除之前設定對象
    $sql = "UPDATE tBankTranSms SET bDel = 1 WHERE bVR_Code = '" . $_POST["vr_code"] . "' AND bObjKind = '" . $ra . "' AND bBankTranId = ''";
    $conn->Execute($sql);

    for ($i = 0; $i < count($allForm); $i++) {
        $tmp = explode('_', $allForm[$i]);

        $sql = "INSERT INTO
					tBankTranSms
				SET
					bVR_Code = '" . $_POST["vr_code"] . "',
					bObjKind = '" . $ra . "',
					bIden = '" . $tmp[0] . "',
					bName = '" . $tmp[1] . "',
					bMobile = '" . $tmp[2] . "',
					bCreatTime = '" . date("Y-m-d H:i:s") . "',
					bStoreId = '" . $tmp[3] . "'
					";
        $conn->Execute($sql);

        $tmp = null;unset($tmp);
    }

    $ra = null;unset($ra);
}

$realtyTarget = array(1 => '(買賣方)', 2 => '(賣方)', 3 => '(買方)');
##

// 取得合約銀行
$_vr_bank = substr($vr_code, 0, 5);

$sql = 'SELECT * FROM tContractBank WHERE cShow="1" AND cBankVR LIKE "' . $_vr_bank . '%" ORDER BY cId ASC;';
$rs  = $conn->Execute($sql);

$conBank = $rs->fields;

$_vr_bank    = $conBank['cBankName']; //銀行簡稱
$bank_no     = $conBank['cBankMain']; //銀行總行代碼
$main_bank   = $conBank['cBankMain']; //銀行總行代碼
$branch_bank = $conBank['cBankBranch']; //銀行分行代碼

$branch = '';
$branch = $conBank['cBranchName'];

if ($conBank['cBankMain'] == '807') {
    $branch      = $conBank['cBranchFullName'];
    $branch_bank = '1044'; //銀行分行代碼(永豐共用相同活儲帳戶)
}

$_account_name = $conBank['cAccountName']; //銀行活儲帳戶
$_account_no   = $conBank['cBankAccount']; //銀行活儲帳號

if ($conBank['cId'] == '1') {
    $branch_bank = '1440';
    $_account_no = '14410536688';
    //27110351738 似乎有調整過
}
##

$save    = $_POST["save"];
$moneyOK = true;

//取得調帳
$dates = date("Ymd", mktime(0, 0, 0, date("m"), (date("d")), (date("Y") - 1))); //顯示十日內的紀錄
$dates = (substr($dates, 0, 4) - 1911) . substr($dates, 4, 2) . substr($dates, 6, 2);

$sql = "SELECT * FROM tExpense WHERE eTradeDate >= '" . $dates . "' AND eLender <> '000000000000000' AND eStatusIncome = 1 AND eTradeStatus = 0 AND ePayTitle NOT LIKE '%網路整批%' ORDER BY eTradeDate,eTradeNum";
$rsx = $conn->Execute($sql);

$reconciliation_option = array();
while (!$rsx->EOF) {
    $reconciliation_option[$rsx->fields["id"]] = substr($rsx->fields["eDepAccount"], 2) . " / " . (int) substr($rsx->fields["eLender"], 0, -2) . "元";
    $rsx->MoveNext();
}

$dates = null;unset($dates);

if ($save == 'ok') {
    $bk = $_POST["bk"];

    $bank_kind      = $_POST["bank_kind"];
    $target         = $_POST["target"];
    $export         = $_POST["export"];
    $code2          = $_POST['code2'];
    $bank3          = $_POST["bank3"];
    $bank4          = $_POST["bank4"];
    $t_name         = $_POST["t_name"];
    $t_buyer        = $_POST["t_buyer"];
    $t_seller       = $_POST["t_seller"];
    $t_account      = $_POST["t_account"];
    $t_cost         = $_POST["t_cost"];
    $t_money        = $_POST["t_money"];
    $t_txt          = $_POST["t_txt"];
    $pid            = $_POST["pid"];
    $objKind        = $_POST["objKind"];
    $objKind2       = $_POST['taxScrivener'];
    $email          = $_POST["email"];
    $fax            = $_POST["fax"];
    $change_s       = $_POST["change_s"]; //調整專用 - 記錄入帳表之id
    $replace_patt   = array("\r\n", "\n", "\r", " ", "　");
    $send           = $_POST["tSend"];
    $showTxt        = $_POST['bankshowtxt']; //ALTER TABLE  `tBankTrans` ADD  `tBankShowTxt` VARCHAR( 100 ) NOT NULL COMMENT  '存摺顯示文字' AFTER  `tPayTxt`
    $taxPayId       = $_POST['taxPayId'];
    $storeId        = $_POST['storeId'];
    $taxReturnPayId = $_POST['taxReturnPayId'];
    $invoice        = $_POST['tInvoice'];

    //檢查出款金額是否超過餘額
    for ($i = 0; $i < count($_POST["t_money"]); $i++) {
        $checkTranMoney += $_POST["t_money"][$i];
    }

    $_total = count($export);

    $idArr  = array();
    $fw_exp = fopen(dirname(dirname(__DIR__)) . "/log2/expense_" . date("Ymd") . ".txt", 'a+');
    $feedBackScrivenerClose = 0;
    for ($i = 0; $i < $_total; $i++) { //tStoreId
        if ($_SESSION["member_name"] != "") {
            $record["tOwner"] = $_SESSION["member_name"];
        }

        $record["tVR_Code"]   = $vr_code;
        $record["tBank_kind"] = $bank_kind;
        $record["tCode"]      = ($export[$i] == 'x') ? '' : $export[$i];
        $record['tCode2']     = ($code2[$i] == 'x') ? '' : $code2[$i];
        $record["tKind"]      = ($target[$i] == 'x') ? '' : $target[$i];
        $record["tObjKind"]   = ($objKind[$i] == 'x') ? '' : $objKind[$i];

        if ($objKind2[$i] != '') {
            $record["tObjKind2"] = $objKind2[$i];
        } else {
            if ($record["tBank_kind"] == '台新' && $record["tCode"] == '03') {
                $record["tObjKind2"] = '03';
            }
        }
        if ($invoice == 'Y') {
            $record["tInvoice"] = date('Y-m-d H:i:s');
            $feedBackScrivenerClose = 1;
        }

        $bank                   = $bank3[$i] . $bank4[$i];
        $record["tBankCode"]    = $bank;
        $record["tBuyer"]       = $t_buyer[$i];
        $record["tSeller"]      = $t_seller[$i];
        $record["tAccount"]     = trim($t_account[$i]);
        $record["tAccountName"] = trim($t_name[$i]);
        $record["tAccountId"]   = $pid[$i];
        $record["tStoreId"]     = $storeId[$i];

        $money            = $t_cost[$i] + $t_money[$i];
        $record["tMoney"] = $money;

        $record["tEmail"] = $email[$i];
        $record["tFax"]   = $fax[$i];

        $record["tMemo"] = $_vr_code;

        $record["tChangeExpense"] = ($change_s[$i] == 'x') ? '' : $change_s[$i];
        $record['tBankShowTxt']   = $showTxt[$i];

        if ($send[$i] == '') {
            $send[$i] = 0;
        }

        $record['tSend'] = $send[$i];

        //帶入契約書用印店
        if ($t_seller[$i] > 0) {
            $fw = fopen(dirname(dirname(__DIR__)) . "/log2/service69_" . date("Ymd") . ".txt", 'a+');

            //幸福家
            $sql = "SELECT cBrand,cBrand1,cBrand2,cBrand3,cBranchNum,cBranchNum1,cBranchNum2,cBranchNum3 FROM tContractRealestate WHERE cCertifyId = '" . $_vr_code . "'";
            $rs  = $conn->Execute($sql);

            if ($rs->fields['cBrand'] == 69 || $rs->fields['cBrand1'] == 69 || $rs->fields['cBrand2'] == 69 || $rs->fields['cBrand3'] == 69) {
                $update_str = '';
                if ($storeId[$i] == $rs->fields['cBranchNum']) { //符合就是用印店
                    $update_str = 'cAffixBranch = 1,cAffixBranch1 = 0,cAffixBranch2 = 0,cAffixBranch3 = 0';
                } else if ($storeId[$i] == $rs->fields['cBranchNum1']) {
                    $update_str = 'cAffixBranch = 0,cAffixBranch1 = 1,cAffixBranch2 = 0,cAffixBranch3 = 0';
                } else if ($storeId[$i] == $rs->fields['cBranchNum2']) {
                    $update_str = 'cAffixBranch = 0,cAffixBranch1 = 0,cAffixBranch2 = 1,cAffixBranch3 = 0';
                } else if ($storeId[$i] == $rs->fields['cBranchNum3']) {
                    $update_str = 'cAffixBranch = 0,cAffixBranch1 = 0,cAffixBranch2 = 0,cAffixBranch3 = 1';
                }

                if ($update_str != '') {
                    $sql = "UPDATE tContractRealestate SET " . $update_str . " WHERE cCertifyId = '" . $_vr_code . "'";
                    $conn->Execute($sql);

                    fwrite($fw, $sql . "\r\n");
                }

                $update_str = null;unset($update_str);
            }

            fclose($fw);
        }

        $t_txt[$i]      = str_replace($replace_patt, "", $t_txt[$i]);
        $record["tTxt"] = $t_txt[$i];

        if ($record['tBankShowTxt'] == null) {
            $record['tBankShowTxt'] = '';
        }

        if ($_POST['datepicker' . $i]) {
            $record['tObjKind2Date'] = $_POST['datepicker' . $i];
        }

        //20241128 紀錄是否為法務許可案件
        $record["tLegalAllow"] = $tLegalAllow;

        if ($record["tKind"] == '保證費') {
            $feedBackScrivenerClose = 1;
            if($radiokind == '履保費先收(結案回饋)') { //履保費先收 若業務還未確認 先不鎖定
                $paybycase = new PayByCase;
                $pay_by_case = $paybycase->getPayByCase($_vr_code);
                if (!empty($pay_by_case)) {
                    if (empty($pay_by_case['fSalesConfirmDate'])) {
                        $feedBackScrivenerClose = 0;
                    }
                }
            }
        }

        $order_id = '';
        if ($t_money[$i] != '' && $moneyOK) {
            $result   = $conn->AutoExecute("tBankTrans", $record, 'INSERT');
            $order_id = $conn->Insert_ID();
            $idArr[]  = $order_id;

            fwrite($fw_exp, $order_id . "_" . $taxPayId . "\r\n");

            //更新明細紀錄, 變更為已出款(由於扣繳稅款只會單筆出款，所以忽略迴圈次數，當作單次處理)
            if ($taxPayId) {
                $taxArr = explode('_', $taxPayId);
                foreach ($taxArr as $k => $v) {
                    $sql = 'UPDATE tExpenseDetail SET eOK="' . $order_id . '" WHERE eId="' . $v . '";';
                    $conn->Execute($sql);

                    fwrite($fw_exp, $sql . "\r\n");
                }
            }

            ##
            //回寫申請代墊
            if ($taxReturnPayId) {
                $taxR = explode('_', $taxReturnPayId);
                foreach ($taxR as $k => $v) {
                    $sql = "UPDATE tBankTrans SET tObjKind2Item = '" . $order_id . "',tShow =0 WHERE tId = '" . $v . "'";
                    $conn->Execute($sql);
                }
            }
            //02返還公司代墊 05需帳出款至公司
            if ($record["tObjKind2"] == '02' || $record["tObjKind2"] == '04') {
                $sql = "UPDATE tBankTrans SET tShow = 0 WHERE tId = '" . $order_id . "'";
                $conn->Execute($sql);
            }

            //履保費先收要標記
            if($record["tObjKind"] == '履保費先收(結案回饋)') {
                $sql = "UPDATE tContractCase SET cBankRelay = 'C' WHERE cCertifiedId = '" . $_vr_code . "'";
                $conn->Execute($sql);
            }
        }

        $ok     = 1;
        $record = null;unset($record);
    }
    #要開結案或出款保證費
    if($feedBackScrivenerClose == 1) {
        $sql = "UPDATE `tContractCase` SET cFeedBackScrivenerClose = 1 WHERE cEscrowBankAccount = '" . $vr_code . "'";
        $conn->Execute($sql);
        _writeLog($vr_code, $sql, '回饋金鎖定代書欄位');
        $feedBackScrivenerClose = 0;
    }

    fclose($fw_exp);

    if ($moneyOK) {
        if ($_POST['smsSend'] == 2) {
            $sql = "UPDATE tBankTranSms SET bBankTranId = '" . @implode(',', $idArr) . "' WHERE bVR_Code = '" . $vr_code . "' AND bObjKind = '" . $objKind[0] . "' AND bDel = 0 AND bBankTranId = ''";
            $conn->Execute($sql);
        }

        header("Location: ../list2.php?ok=1");
        exit;
    }
}

if ($radiokind == '代墊利息') {
    //代墊利息出款建檔
    require_once __DIR__ . '/includes/out2/out2_paybyinterest.php';
    ##
} else {
    //其他出款關係人的詳細資料
    require_once __DIR__ . '/includes/out2/out2_ext.php';
    ##
}

//
$sql            = "SELECT SUM(tMoney) AS total FROM tBankTrans WHERE tBank_kind = '台新' AND tObjKind2 = '01' AND tObjKind2Item = '' AND tVR_Code='" . $vr_code . "'";
$rsTT           = $conn->Execute($sql);
$taishinSPMoney = $rsTT->fields['total'];
##

//半形<=>全形
function n_to_w($strs, $types = '0')
{ // narrow to wide , or wide to narrow
    $nt = array(
        "(", ")", "[", "]", "{", "}", ".", ",", ";", ":",
        "-", "?", "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~", "`", "'", "\"", "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " ",
    );
    $wt = array(
        "（", "）", "〔", "〕", "｛", "｝", "﹒", "，", "；", "：",
        "－", "？", "！", "＠", "＃", "＄", "％", "＆", "｜", "＼",
        "／", "＋", "＝", "＊", "～", "、", "、", "＂", "＜", "＞",
        "︿", "＿",
        "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
        "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
        "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
        "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
        "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
        "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
        "　",
    );

    if ($types == '0') { //半形轉全形
        // narrow to wide
        $strtmp = str_replace($nt, $wt, $strs);
    } else { //全形轉半形
        // wide to narrow
        $strtmp = str_replace($wt, $nt, $strs);
    }
    return $strtmp;
}

function _writeLog($cId, $pattern, $reason)
{
    $txt = "===========================\r\n";
    $txt .= "Date: " . date("Y-m-d H:i:s") . "\r\n";
    $txt .= "Cid: " . $cId . "\r\n";
    $txt .= "Reason: " . $reason . "\r\n";

    $txt .= "Pattern: " . $pattern . "\r\n";
    $txt .= "===========================\r\n";

    $path = dirname(dirname(__DIR__)) . '/log/feedback';

    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
    $fw = fopen($path . '/' . date("Ymd") . '.log', 'a+');
    fwrite($fw, $txt . "\r\n");
    fclose($fw);
}
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>出帳建檔作業 v2.0</title>
    <link type="text/css" href="../../js2/jquery-ui/jquery-ui.css" rel="stylesheet" />
    <link type="text/css" href="../../js2/combobox/combobox.css" rel="stylesheet" />
    <link type="text/css" href="../../js2/colorbox/colorbox.css" rel="stylesheet" />
    <script type="text/javascript" src="../../js2/jquery.js"></script>
    <script type="text/javascript" src="../../js2/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../../js2/combobox/combobox.js"></script>
    <script type="text/javascript" src="../../js2/colorbox/jquery.colorbox.js"></script>
    <script type="text/javascript" src="../../js2/colorbox/jquery.colorbox-zh-TW.js"></script>
    <script type="text/javascript" src="../../js2/lib/banktrans.js"></script>
    <script>
    var showOrHide = false;
    var radiokind = $('[name="radiokind"]').val();
    $(function() {
        $(".dt").datepicker({
            dateFormat: "yy-mm-dd"
        });
        $("#toggle_other").on("click", function() {
            show_other();
        });

        bank_check();
        Lock();

        recal('o', 0);
    });

    /* 檢查銀行是否為聯行或跨行 */
    function bank_check(n) {
        var index = 0;

        if ($("#export" + index).val() == "04" || $("#export" + index).val() == "05") { //大額繳稅跟臨櫃領現不用檢查
            return false;
        }

        var mainBank = $('[name="contractBank"]').val(); //合約銀行代碼
        $('[name="bank3[]"]').each(function(index) {
            var _export = '02';
            var bk_no = $(this).val();
            var _export_name = '跨行代清償';
            var checkCode = $("#export" + index).val();

            if (bk_no) {
                if (bk_no == mainBank) {
                    _export = item_check(index, n);
                    if (_export == '03') {
                        _export_name = '聯行代清償';
                    } else if (_export == '01') {
                        _export_name = '聯行轉帳';
                    }
                }
            }

            if ($("#export" + index).val() != "04" && $("#export" + index).val() != "05") {
                set_item(index, _export, _export_name);
            }

            index = index + 1;
        });
    }

    function bank_select_index(x, kind, y, index, w, b2) {
        var _target = $('#bk').val();
        if (_target == 'first') {
            x = '007';
        }
        var _number = Math.random();
        var url = "../_bank_select.php?i=" + _number + "&bank3=" + x + "&b4=" + b2;

        $.ajax({
            url: url,
            error: function(xhr) {
                alert("error!!");
            },
            success: function(response) {
                $("." + kind).empty();
                $("." + kind).append(response);
                setCombobox($("." + kind));
            }
        });

        if (w == 'n') {
            bank_check('n');
        } else {
            bank_check();
        }

    }

    //大額繳稅帳戶
    function bankAccountAuto(id, cat) {
        $.ajax({
            url: '../getMainBankAccount.php',
            type: 'POST',
            dataType: 'html',
            data: {
                id: $("[name='vr_code']").val(),
                cat: cat
            },
        }).done(function(msg) {
            var obj = jQuery.parseJSON(msg);
            if (obj.msg != 1) {
                $(".b3_" + id).combobox('destroy');
                $(".b3_" + id).val(obj.Bank);
                setCombobox($(".b3_" + id));

                $(".b4_" + id).combobox('destroy');
                bank_select_index(obj.Bank, "b4_" + id, "branch_" + id, id, '', obj.BankBranch);

                $("#t_name" + id).val(obj.AccName);
                $(".bb_" + id).val(obj.Acc);
            }
        });
    }

    /* 確認交易項目是否為代清償或扣繳稅款 */
    function item_check(id, n) {
        var index = 0;
        var _export = '01';
        var code = '';

        if (n) {
            if ($(".ta_" + id).val() != '' && $(".ta_" + id).val() != undefined) {
                code = $(".ta_" + id).val().substr(3, 3); //055 050 是還款帳戶所以是聯行代清償(03) 永豐限定
            }
        } else {
            if ($(".bb_" + id).val() != '' && $(".bb_" + id).val() != undefined) {
                code = $(".bb_" + id).val().substr(3, 3); //055 050 是還款帳戶所以是聯行代清償(03) 永豐限定
            }
        }

        var mainBank = $('[name="contractBank"]').val(); //合約銀行代碼
        $('[name="objKind[]"]').each(function() {
            if (index == id) {
                var objKind = $(this).val();

                if (objKind == '代清償' && mainBank == "807" && (code == '055' || code == '050')) {
                    _export = '03';
                }

                if (objKind == '代清償' && mainBank != "807") {
                    _export = '03';
                }
            }

            index = index + 1;
        });

        return _export;
    }

    function bankphone(v, type) {
        //type 1 原有的 2額外新增的
        var obj = $("#objKind" + v).val();

        if (obj == '代清償') {
            if (type == 1) {
                var bank = $(".b3_" + v).val();
                var bankB = $(".b4_" + v).val();
            } else {
                var bank = $(".b3n_" + v).val();
                var bankB = $(".b4n_" + v).val();
            }

            $("#bankp" + v).html('');

            $.ajax({
                    url: '../getBankPhone.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'bank': bank,
                        'branch': bankB
                    },
                })
                .done(function(txt) {

                    $("#bankp" + v).html('電話：' + txt);
                });
        }
    }

    function show_service(x, y, index, n) {
        _tg = '#' + y;

        if (x == '調帳' || x == '仲介服務費') {
            _obj1 = "#t_name" + n + index;
            _obj2 = "#t_account" + n + index;
            _obj3 = ".b3" + n + "_" + index;
            _obj4 = ".b4" + n + "_" + index;
            if (x == '仲介服務費') {
                $(_tg).show();
            }
        } else if (x == '代清償') {
            if (n == '') {
                bankphone(index, 1);
            } else {
                bankphone(index, '');
            }

        } else {
            $(_tg).hide();
            _obj1 = ".tn_" + index;
            _obj2 = ".ta_" + index;
            _obj3 = ".b4n_" + index;
            _obj4 = ".b3n_" + index;
        }

        if (n) {
            bank_check(n);
        } else {
            bank_check();
        }
    }

    //
    function serviceMoney(type, id) { //判斷是那一方的仲介服務費
        var target = $("#" + type + "serviceTarget" + id).val();
        var money = $("#" + type + "t_money" + id).val();
        var obj = $("#objKind" + id).val();
        $("." + type + "t_buyer" + id).val(0);
        $("." + type + "t_seller" + id).val(0);

        if (obj == '仲介服務費') {
            if (target == 'buyer') {
                $("." + type + "t_buyer" + id).val(money);
                $("." + type + "t_seller" + id).val(0);
            } else {
                $("." + type + "t_seller" + id).val(money);
                $("." + type + "t_buyer" + id).val(0);
            }

            check_money();
        }
    }

    //計算總價金*6%>(買+賣)服務費
    function check_money() {
        var buy_tmp = 0;
        var owner_tmp = 0;
        var sum = 0;
        var total = <?=$total_money?> * 0.06;

        var buy_money = new Array();
        var owner_money = new Array();

        $('.service_fee_buyer').each(function(i) {
            if ($(this).val() != '') {
                buy_money[i] = $(this).val();
            }
        });

        $('.service_fee_seller').each(function(i) {
            if ($(this).val() != '') {
                owner_money[i] = $(this).val();
            }
        });

        for (var i = 0; i < buy_money.length; i++) {
            buy_tmp = buy_tmp + parseInt(buy_money[i]);
        }

        for (var i = 0; i < owner_money.length; i++) {
            owner_tmp = owner_tmp + parseInt(owner_money[i]);
        }

        sum = buy_tmp + owner_tmp;
        if (total < sum) {
            alert('服務費大於總價金的6%');
        }
    }

    function recal(type, id) {
        var balanceMoney = "<?=trim($rs->fields["cCaseMoney"])?>";
        var _balance = parseInt(balanceMoney);

        $('input[name*="t_money"]').each(function() {
            var str = $(this).val();
            if (str) {
                _balance = _balance - parseInt(str);
            }
        });

        $('#caseMoney').html(_balance);

        serviceMoney(type, id);
    }

    function rel_words(name) { //半形轉全形
        var val = $("#" + name).val();

        $.ajax({
                url: 'replace_words.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    'txt': val
                },
            })
            .done(function(txt) {
                $("#" + name).val(txt);
            });
    }

    function go() {
        <?php if (in_array($bankRelay, ['Y', 'C']) and $invoice == 0 and in_array($radiokind, ['點交', '解除契約', '建經發函終止', '預售屋'])): ?>
            if ($('input[name="tInvoice"]:checked').length == 0) {
                alert('請選擇是否結案');
                return false;
            }
            if($('input[name="tInvoice"]:checked').val() == 'Y') {
                <?php
                    $_SESSION['refresh'] = 1;
                ?>
            }
        <?php endif?>

        $("#sub").attr('disabled', 'disabled');

        var radiokind = "<?=$radiokind?>";
        var realtyCharge = <?=$realty_charge?>;

        $('[name="objKind[]"]').each(function(index) {

            var str = $(this).val();
            if (str == '仲介服務費') {
                let tMoney = $('[name="t_money[]"]').eq(index).val();
                let tSeller = $('[name="t_seller[]"]').eq(index).val();
                let tBuyer = $('[name="t_buyer[]"]').eq(index).val();
                if (tMoney > 0) {
                    if (tSeller != 0 && tMoney != tSeller) {
                        $('[name="t_seller[]"]').eq(index).val(tMoney);
                    }
                    if (tBuyer != 0 && tMoney != tBuyer) {
                        $('[name="t_buyer[]"]').eq(index).val(tMoney);
                    }
                }
                realtyCharge = realtyCharge + 1;
            }
        });

        if (radiokind == '點交') {
            if (realtyCharge <= 0) {
                $('#realtyC').html(
                    '<h1 style="font-weight:bold;color:red;text-align:center;width:100%;">"仲介服務費"</h1><h3 style="text-align:center;width:100%;">尚未出款!!</h3>'
                );
                $('#realtyC').dialog({
                    modal: true,
                    buttons: {
                        "繼續出款": function() {
                            $(this).dialog("close");
                            $('#form1').submit();
                        },
                        "取消返回": function() {
                            $("#sub").prop('disabled', false);
                            $(this).dialog("close");
                        }
                    }
                });
            } else {
                $('#form1').submit();
            }
        } else {
            $('#form1').submit();
        }
    }
    </script>
    <style>
    .font9 {
        font-size: 9pt;
    }

    .section .custom-combobox-input {
        width: 80px;
    }
    </style>
</head>

<body>
    <div style="width:1600px; margin-bottom:5px; height:22px; background-color: #CCC">
        <div style="float:left;margin-left: 10px;">
            <font color=red><strong>建檔</strong></font>
        </div>
        <div style="float:left;margin-left: 10px;"> <a href="/bank/list2.php">待修改資料</a> </div>
        <?php if ($_SESSION["member_bankcheck"] == '1') {?>
        <div style="float:left; margin-left: 10px;"> <a href="/bank/list.php">未審核列表</a></div>
        <?php }?>
    </div>
    <form id="form1" name="form1" method="post" action="">
        <input type="hidden" name="tLegalAllow" value="<?=$tLegalAllow?>">

        <table width="1600" border="0">
            <tr>
                <td >*保證號碼 <?php echo ($radiokind == '代墊利息') ? $pay_case['vr_code'] : $vr_code; ?>
                    <input name="vr_code" type="hidden" id="vr_code" value="<?php echo ($radiokind == '代墊利息') ? $pay_case['vr_code'] : $vr_code; ?>" />
                    <input type="hidden" name="radiokind" value="<?=$radiokind?>" />
                    <input type="hidden" name="realCertifiedMoney" value="<?=$realCertifiedMoney?>">
                    <input id="certifiedId" type="hidden" value="<?php echo n_to_w($_vr_code); ?>" />
                    <input type="hidden" id="customer" value="<?php echo mb_substr($owner, 0, 3, 'UTF-8') . mb_substr($buyer, 0, 3, 'UTF-8'); ?>">
                    <input name="save" type="hidden" id="save" value="ok" />
                    <input type="hidden" name="legalCase" value="<?=$legalCase?>" />
                </td>
                <?php if (in_array($bankRelay, ['Y', 'C']) and $invoice == 0 and in_array($radiokind, ['點交', '解除契約', '建經發函終止', '預售屋'])): ?>
                    <td style="color: #5eb95e;font-weight: bold">結案：<input type="radio"  name="tInvoice" value="Y"> 結 <input type="radio" name="tInvoice" value="N">不結</td>
                <?php endif?>
                <td>
                    <?php
if ($radiokind == '代墊利息') {
    echo '配合銀行 【' . $pay_case['_vr_bank'] . $pay_case['branch'] . '】' . "\n";
} else {
    echo '配合銀行 【' . $_vr_bank . $branch . '】' . "\n";
}
?>
                    <input type="hidden" name="bank_kind"
                        value="<?php echo ($radiokind == '代墊利息') ? $pay_case['_vr_bank'] : $_vr_bank; ?>">
                    <input type="hidden" name="contractBank"
                        value="<?php echo ($radiokind == '代墊利息') ? $pay_case['bank_no'] : $bank_no; ?>">
                </td>
                <td align="center">
                    目前帳戶餘額：<?=trim($rs->fields["cCaseMoney"])?>
                    <?php if ($taishinSPMoney > 0): ?>
                    <font color="red">(<?=$taishinSPMoney?>元未返還)</font>
                    <?php endif?>
                    <?php if ($CommitmentMoney > 0): ?>
                    <font color="red">(承諾書金額：<?=$CommitmentMoney?>元)</font>
                    <?php endif?>
                    <input type="hidden" name="Balance" value="<?=($taishinSPMoney + $rs->fields["cCaseMoney"])?>" />
                    預計出帳後餘額：<span id="caseMoney"><?php echo trim($rs->fields["cCaseMoney"]); ?></span>
                </td>
            </tr>
        </table>
        <?php

//依據出款項目儲存至陣列中，以便顯示輸出
require_once __DIR__ . '/includes/out2/out2_ext1.php';
##

$paybycase = $pay_by_case = null;
unset($paybycase, $pay_by_case);
?>

        <?php for ($j = 0; $j < $i; $j++) {?>
        <!-- 點交出款
            地政士跟買賣方拉開距離
            仲介服務費拉到保證費下方 -->
        <?php if ($_a[$j] == '地政士' && in_array($radiokind, ['點交', '解除契約', '保留款撥付', '建經發函終止', '預售屋'])): ?>
        <div style="margin-top: 5px;width:1600px;">&nbsp;</div>
        <?php endif?>

        <div style="border:1px dotted #999999; width:1600px; margin:3px;">
            <?php
if ($scrivenerBankCount > 1 && $_a[$j] == '地政士') {
    $color = 'rgb(218,242,142)';
} else if (($branchBankCount + $branchBankCount1 + $branchBankCount2 + $branchBankCount2) > 1 && preg_match("/^仲介/", $_a[$j])) {
    $color = 'rgb(255,255,170)';
} else {
    $color = 'rgb(255,255,255)';
}

    $lock = ($_st[$j] == $checkIden || $_st[$j] == 1) ? '' : "$j";
    ?>
            <div style="display:block;">
                <?=$_sn[$j]?>
                <?php
if ($radiokind == '仲介服務費') {
        if ($lock != '') {
            ?>
                <input type="button" value="解鎖" onclick="unLock(<?=$j?>)" />
                <?php
}
        ?>
                <input type="hidden" value="<?=$lock?>" id="service<?=$j?>" class="lock" />
                <?php
}
    ?>
            </div>

            <table width="1600" border="0" class="font12" id="tb" cellpadding="0" cellspacing="0">
                <tr id="tr_pos" style='background-color:<?=$color?>' class="lock<?=$j?>">
                    <td width="132" class="section">
                        <input type="hidden" name="storeId[]" value="<?=$_si[$j]?>" />
                        <input type="hidden" name="smsSend" value="<?=$smsSend?>" />
                        <label for="target<?=$j?>"></label>
                        *

                        <select name="target[]" id="target<?=$j?>" class="combobox"
                            onchange="setTxt('<?=$_vr_code?>',<?=$j?>,this.value,'t_txt','objKind','ot_money')">
                            <option value="x">角色選擇</option>
                            <option value="賣方" <?php echo ($_a[$j] == '賣方') ? 'selected="selected"' : ''; ?>>賣方</option>
                            <option value="買方" <?php echo ($_a[$j] == '買方') ? 'selected="selected"' : ''; ?>>買方</option>
                            <option value="地政士" <?php echo ($_a[$j] == '地政士') ? 'selected="selected"' : ''; ?>>地政士
                            </option>
                            <option value="仲介" style="background-color:yellow;"
                                <?php echo preg_match("/^仲介/", $_a[$j]) ? 'selected="selected"' : ''; ?>>
                                仲介<?php echo preg_match("/^仲介/", $_a[$j]) ? mb_substr($_a[$j], 2, 100, "utf-8") : ''; ?>
                            </option>
                            <option value="保證費" <?php echo ($_a[$j] == '保證費') ? 'selected="selected"' : ''; ?>>保證費
                            </option>
                            <option value="地政士回饋金" <?php echo ($_a[$j] == '地政士回饋金') ? 'selected="selected"' : ''; ?>>
                                地政士回饋金</option>
                        </select>
                        <br />

                        *

                        <input type="hidden" name="code2[]" id="code2<?=$j?>" value="" />
                        <select name="export[]" id="export<?=$j?>" onchange="setCode2('export<?=$j?>',<?=$j?>)"
                            class="combobox export">
                            <option value="x" selected="selected">交易類別</option>
                            <option value="01">聯行轉帳</option>
                            <option value="01">一銀內轉</option>
                            <option value="02">跨行代清償</option>
                            <option value="03">聯行代清償</option>
                            <option value="04">大額繳稅</option>
                            <option value="05">臨櫃開票</option>
                            <option value="05">臨櫃領現</option>
                            <option value="06">利息</option>
                        </select>
                        <br />
                        *
                        <select name="objKind[]" id="objKind<?=$j?>" class="combobox objKind<?=$j?>"
                            onchange="show_service(this.value,'s_service_<?php echo $j; ?>','<?=$j?>','')">
                            <option value="x" selected="selected">項目</option>
                            <?php
if ($_a[$j] != '保證費') {
        ?>
                            <option value="賣方先動撥" <?php echo ($radiokind == '賣方先動撥') ? 'selected="selected"' : ''; ?>>
                                賣方先動撥</option>
                            <option value="仲介服務費" <?php echo ($radiokind == '仲介服務費') ? 'selected="selected"' : ''; ?>>
                                仲介服務費</option>
                            <?php
if ($radiokind == '扣繳稅款') {
            ?>
                            <option value="扣繳稅款" <?php echo ($radiokind == '扣繳稅款') ? 'selected="selected"' : ''; ?>>扣繳稅款
                            </option>
                            <?php
}
        ?>
                            <option value="代清償" <?php echo ($radiokind == '代清償') ? 'selected="selected"' : ''; ?>>代清償
                            </option>
                            <option value="其他">其他</option>
                            <option value="調帳" <?php echo ($radiokind == '調帳') ? 'selected="selected"' : ''; ?>>調帳
                            </option>
                            <option value="保留款撥付" <?php echo ($radiokind == '保留款撥付') ? 'selected="selected"' : ''; ?>>
                                保留款撥付</option>
                            <?php
}
    ?>
<?php
if ($radiokind != '履保費先收(結案回饋)') {
?>
                            <option value="點交(結案)" <?php echo ($radiokind == '點交') ? 'selected="selected"' : ''; ?>>
                                點交(結案)</option>
                            <option value="解除契約" <?php echo ($radiokind == '解除契約') ? 'selected="selected"' : ''; ?>>
                                解約/終止履保</option>
                            <option value="建經發函終止" <?php echo ($radiokind == '建經發函終止') ? 'selected="selected"' : ''; ?>>
                                建經發函終止</option>
                            <option value="預售屋" <?php echo ($radiokind == '預售屋') ? 'selected="selected"' : ''; ?>>預售屋
                            </option>
                            <option value="代墊利息" <?php echo ($radiokind == '代墊利息') ? 'selected="selected"' : ''; ?>>代墊利息
                            </option>
<?php
}
?>
<?php
    if ($radiokind == '履保費先收(結案回饋)') {
?>
                            <option value="履保費先收(結案回饋)" <?php echo ($radiokind == '履保費先收(結案回饋)') ? 'selected="selected"' : ''; ?>>履保費先收(結案回饋)
                            </option>
<?php
    }
?>
                        </select>

                        <?php if (in_array($radiokind, ['扣繳稅款', '賣方先動撥', '代清償']) && $_vr_bank == '台新'): ?>

                        <br />*
                        <select name="taxScrivener[]" id="taxScrivener<?=$j?>" class="combobox taxScrivener"
                            onchange="checkScrivenerTax(<?=$j?>)">
                            <option value="x">特殊項目</option>
                            <option value="01">申請公司代墊</option>
                            <option value="02">返還公司代墊</option>
                            <option value="03" selected="selected">不用代墊</option>
                            <option value="04">申請代理出款</option>
                            <option value="05">公司代裡出款</option>
                        </select>
                        <?php endif?>
                    </td>
                    <td width="231">
                        *解匯行
                        <label for="bank3[]"></label>
                        <?php
$sql = "SELECT * FROM tBank WHERE bBank4 = '' AND bBank3 !='000' AND bOK = 0 ORDER BY bBank3 ASC;";
    $rs2 = $conn->CacheExecute(1, $sql);
    ?>
                        <select name="bank3[]" id="bank3[]" class="combobox b3_<?php echo $j; ?>"
                            onchange="bank_select_index(this.value,'b4_<?php echo $j; ?>','branch_<?php echo $j; ?>','<?php echo $j; ?>','')"
                            style=" width:110px;">
                            <option value="x">選擇銀行</option>
                            <?php
while (!$rs2->EOF) {
        ?>
                            <option value="<?php echo $rs2->fields["bBank3"]; ?>" <?php
echo (trim($rs2->fields["bBank3"]) == $_ab3[$j]) ? 'selected="selected">' : '>';
        echo '(' . $rs2->fields["bBank3"] . ')' . trim($rs2->fields["bBank4_name"]) . '</option>' . "\n";

        $rs2->MoveNext();
    }
    ?> </select>

                                <?php
$sql = "SELECT * FROM tBank WHERE bBank4 != '' AND bBank3='" . $_ab3[$j] . "'  ORDER BY bCodeTitle , bBank3 ASC;";
    $rs3 = $conn->CacheExecute(1, $sql);
    ?>
                                <label for="bank4[]"><br />
                                    *分行別</label>
                                <select name="bank4[]" id="bank4[]" style="width:130px;"
                                    class="combobox b4_<?php echo $j; ?>" onchange="bankphone(<?php echo $j; ?>,1)">
                                    <option value="x">選擇分行</option>
                                    <?php while (!$rs3->EOF) {?>
                                    <option value="<?php echo $rs3->fields["bBank4"]; ?>" <?php
echo (trim($rs3->fields["bBank4"]) == $_ab4[$j]) ? 'selected="selected">' : '>';
        echo '(' . $rs3->fields['bBank4'] . ')' . trim($rs3->fields["bBank4_name"]) . '</option>' . "\n";

        $rs3->MoveNext();}
    ?> </select>
                                        <br />
                                        <span id="bankp<?php echo $j; ?>" style="color:#FF0000;"></span>
                    </td>
                    <td width="197">

                        *戶名
                        <label for="t_name[]"></label>
                        <?php
$str = ($_a[$j] == '賣方' && $_vr_bank != '台新' && in_array($radiokind, ['點交', '解除契約'])) ? "onkeyup=\"checkowner('" . $j . "','" . $_vr_code . "')\"" : "";
    ?>
                        <input name="t_name[]" type="text" id="t_name<?php echo $j; ?>" size="14" <?=$str?>
                            onblur="rel_words('t_name<?php echo $j; ?>')" value="<?php echo n_to_w($_an[$j]); ?>" />

                        <br />
                        *帳號
                        <label for="t_account<?php echo $j; ?>"></label>
                        <input name="t_account[]" type="text" id="t_account<?php echo $j; ?>"
                            class="bb_<?php echo $j; ?>" size="14" maxlength="14" onkeyup="bank_check()"
                            value="<?php echo $_ac[$j]; ?>" />
                    </td>
                    <td width="285">
                        <div id="s_change" style="display:block;">調帳選擇<select name="change_s[]" id="change_s[]"
                                class="combobox">
                                <option value="x">請選擇入帳記錄</option>
                                <?php foreach ($reconciliation_option as $k => $v): ?>
                                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                <?php endforeach?>
                            </select>
                            <br />
                        </div>*金額NT$
                        <label for="t_money[]"></label>
                        <input name="t_money[]" type="text" <?php
if ($radiokind == '扣繳稅款') {
        echo 'class="taxM" readonly="readonly" ';
    }

    if ($_a[$j] == '仲介') {
        echo 'style="background-color:yellow;text-align:right;" ';
    } else {
        echo 'style="text-align:right;" ';
    }
    ?> id="ot_money<?php echo $j; ?>" onKeyUp="recal('o',<?php echo $j; ?>)" size="10" title="tm<?php echo $j; ?>"
                            value="<?php
if ($_a[$j] == '保證費' && in_array($radiokind, ['點交', '解除契約', '建經發函終止', '預售屋'])) {
        echo $realCertifiedMoney;
    } elseif ($_a[$j] == '保證費' && in_array($radiokind, ['履保費先收(結案回饋)'])) {
        echo $CertifiedMoney;
    }else {
        echo (!empty($_ab5[$j]) && in_array($radiokind, ['點交', '解除契約', '建經發函終止', '預售屋'])) ? $_ab5[$j] : '';
    }
    ?>" <?php if ($_a[$j] == '保證費') { echo 'readonly=readonly'; }?> />
                        元
                        <input name="t_cost[]" type="hidden" id="t_cost[]" value="0" />
                        <?php if ($radiokind == '扣繳稅款'): ?>
                        <span id='exportTax'>
                            <a href='#' id='taxM<?=$j?>' style='font-size:9pt;'
                                onclick="checkChoiceDetail('<?=$_vr_code?>',<?php echo $j; ?>,1)">選擇出款</a>
                            <input type='hidden' class='taxRemark' name='taxPayId' value='' title='taxM<?=$j?>'
                                class="lock">
                        </span>
                        <span id='returnTax' style="display:none;">
                            <a href='#' id='taxM<?=$j?>' style='font-size:9pt;'
                                onclick="checkChoiceDetail('<?=$_vr_code?>',<?php echo $j; ?>,2)">選擇出款</a>
                            <input type='hidden' class='taxRemark' name='taxReturnPayId' value='' title='taxM<?=$j?>'
                                class="lock">
                            <br />繳稅日期
                            <input type="text" name="datepicker<?php echo $j; ?>"
                                value="<?=$rs->fields["tObjKind2Date"]?>" class="dt" style="width:100px;" />
                        </span>

                        <?php endif?>

                        <br />
                        <div id="s_service_<?php echo $j; ?>"
                            style="display:<?php if ($radiokind == '仲介服務費') {echo 'block';} else {echo 'none';}?>;">

                            對象:
                            <select name="serviceTarget[]" id="oserviceTarget<?php echo $j; ?>" class="combobox"
                                onchange="serviceMoney('o',<?php echo $j; ?>)">
                                <?php
//出款建檔-對象改鎖定買or賣方服務費，保留原設定
    if ($checkIden == 2) {
        $selected  = 'selected=selected';
        $selected1 = '';
    } else if ($checkIden == 3) {
        $selected  = '';
        $selected1 = 'selected=selected';
    } else if ($_st[$j] == 2) {
        $selected  = 'selected=selected';
        $selected1 = '';

    } elseif ($_st[$j] == 3) {
        $selected  = '';
        $selected1 = 'selected=selected';
    }
    ?>
                                <option value="owner" <?=$selected?>>賣方服務費</option>
                                <option value="buyer" <?=$selected1?>>買方服務費</option>

                            </select><br />

                            買方服務費:
                            <input type="text" style="text-align:right;"
                                class="ot_buyer<?php echo $j; ?> lbuyer<?php echo $j; ?>" size="10" disabled /><br />
                            <input type="hidden" name="t_buyer[]" class="ot_buyer<?php echo $j; ?> service_fee_buyer" />
                            賣方服務費:
                            <input type="text" style="text-align:right;"
                                class="ot_seller<?php echo $j; ?> lseller<?php echo $j; ?>" size="10" disabled /><br />
                            <input type="hidden" name="t_seller[]"
                                class="ot_seller<?php echo $j; ?> service_fee_seller" />
                        </div>
                    </td>

                    <td width="182">
                        證號
                        <label for="pid[]"></label>
                        <input name="pid[]" type="text" id="pid[]" size="10" />

                        <br />
                        EMail
                        <label for="email[]"></label>
                        <input name="email[]" type="text" id="email[]" size="13" value="<?php echo $_ae[$j]; ?>" />
                        <br />
                        FAX
                        <label for="fax[]"></label>
                        <input name="fax[]" type="text" id="fax[]" size="15" value="<?php echo $_af[$j]; ?>" />
                    </td>

                    <td width="167">*附言(勿按ENTER換行)<br />
                        <label for="t_cost[]"></label>
                        <label for="t_txt[]"></label>
                        <textarea name="t_txt[]" id="t_txt<?=$j?>" cols="20" class="t_txt" rows="5"
                            onblur="rel_words('t_txt<?=$j?>')"><?php
if (in_array($_a[$j], ['保證費', '地政士回饋金'])) {
        echo n_to_w($_vr_code);
        if ($_a[$j] === '地政士回饋金') {
            echo '回饋金';
        }
    }

    if ($radiokind == '仲介服務費') {
        $tmpTxtArr    = explode('_', $_sn[$j]);
        $tmpTxtArr[1] = str_replace('直營店', '', $tmpTxtArr[1]);
        $tmpTxtArr[1] = str_replace('特許加盟店', '', $tmpTxtArr[1]);
        $tmpTxtArr[1] = str_replace('加盟店', '', $tmpTxtArr[1]);
        echo $tmpTxtArr[1];
        $tmpTxtArr = null;unset($tmpTxtArr);
    } else if ($radiokind == '代墊利息') {
        echo '轉入' . n_to_w($vr_code);
    }

    ?></textarea>
                    </td>
                    <td width="130" align="center">
                        <input type="hidden" name="tSend[]" value="<?php echo ($smsSend == 1) ? 1 : 0; ?>" />
                    </td>
                    <?php
if (in_array($_vr_bank, ['永豐', '台新'])) {
        ?>
                    <td width="130">
                        <div id="Note<?=$j?>">存摺備註欄<br>(限聯行轉帳且字數為六個字)</div>
                        <input type="text" name="bankshowtxt[]" id="bankshowtxt<?=$j?>" maxlength="6"
                            value="<?php echo $owner . $buyer; ?>" />
                    </td>
                    <?php
}
    ?>
                    <td>

                    </td>
                </tr>
            </table>
        </div>
        <?php
}
?>
        <div id="toggle_other">
            <div style="float:left;">新增</div>
            <div id="pp" class="ui-icon ui-icon-triangle-1-e"></div>
        </div>
        <div id="other_all" style="display: none">
            <?php
$index = $i - 1;
for ($j = 1; $j <= 3; $j++) {
    ?>
            <div style="border:1px dotted #900; width:1600px; margin:3px;" id="o_<?php echo $j; ?>">
                <table width="1600" border="0" class="font12" id="ttt">
                    <tr id="tr_pos">
                        <input type="hidden" name="storeId[]" value="" />
                        <td width="132" class="section"><label for="target[]"></label>
                            *
                            <select name="target[]" id="target<?=($j + $index)?>" class="combobox"
                                onchange="setTxt('<?=$_vr_code?>',<?=($j + $index)?>,this.value,'t_txt','objKind','nt_money')">
                                <option value="">角色選擇</option>
                                <option value="賣方"
                                    <?php echo ($target[$i] == 'seller') ? 'selected="selected"' : ''; ?>>賣方
                                </option>
                                <option value="買方" <?php echo ($target[$i] == 'buyer') ? 'selected="selected"' : ''; ?>>
                                    買方
                                </option>
                                <option value="地政士"
                                    <?php echo ($target[$i] == 'scrivener') ? 'selected="selected"' : ''; ?>>地政士
                                </option>
                                <option value="仲介" style="background-color:yellow;"
                                    <?php echo ($target[$i] == 'realestate') ? 'selected="selected"' : ''; ?>>仲介
                                </option>
                                <option value="保證費"
                                    <?php echo ($target[$i] == 'guarantee') ? 'selected="selected"' : ''; ?>>保證費
                                </option>
                                <option value="地政士回饋金">地政士回饋金</option>
                            </select>
                            <br />

                            *
                            <input type="hidden" name="code2[]" id="code2<?=($j + $index)?>" value="" />
                            <select name="export[]" id="export<?=($j + $index)?>" class="combobox export"
                                onchange="setCode2('export<?=($j + $index)?>',<?=($j + $index)?>)">
                                <option value="" selected="selected">交易類別</option>
                                <option value="01">聯行轉帳</option>
                                <option value="01">一銀內轉</option>
                                <option value="02">跨行代清償</option>
                                <option value="03">聯行代清償</option>
                                <option value="04">大額繳稅</option>
                                <option value="05">臨櫃開票</option>
                                <option value="05">臨櫃領現</option>
                                <option value="06">利息</option>
                            </select>
                            <br />
                            *
                            <select name="objKind[]" id="objKind<?=($j + $index)?>"
                                class="combobox objKind<?php echo $j; ?>"
                                onchange="show_service(this.value,'s_serviceN_<?php echo $j; ?>','<?php echo $j; ?>','n')">
                                <option value="" selected="selected">項目</option>
                                <option value="賣方先動撥">賣方先動撥</option>
                                <option value="仲介服務費">仲介服務費</option>
                                <option value="代清償">代清償</option>
                                <option value="點交(結案)">點交(結案)</option>
                                <option value="其他">其他</option>
                                <option value="調帳">調帳</option>
                                <option value="解除契約">解約/終止履保</option>
                                <option value="保留款撥付">保留款撥付</option>
                                <option value="建經發函終止">建經發函終止</option>
                                <option value="預售屋">預售屋</option>
                                <option value="代墊利息">代墊利息
                            </select>
                        </td>
                        <td width="228">*解匯行
                            <label for="bank3[]"></label>
                            <?php
$sql = "SELECT * FROM tBank WHERE bBank4 = '' ORDER BY bCodeTitle, bBank3 ASC;";
    $rs2 = $conn->CacheExecute(1, $sql);
    ?>
                            <select name="bank3[]" id="bank3[]" class="combobox b3n_<?php echo $j; ?>"
                                onchange="bank_select_index(this.value,'b4n_<?php echo $j; ?>','branchn_<?php echo $j; ?>','<?php echo $j; ?>','n')"
                                style=" width:110px;">
                                <option value="">選擇銀行</option>
                                <?php while (!$rs2->EOF) {?>
                                <option value="<?php echo $rs2->fields["bBank3"]; ?>"><?php echo '(' . $rs2->fields['bBank3'] . ')' . trim($rs2->fields["bBank4_name"]); ?></option>
                                <?php
$rs2->MoveNext();}
    ?>
                            </select>

                            <?php
$sql = "SELECT * FROM tBank WHERE bBank4 != '' AND bBank3='$bank3' AND bOK = 0 ORDER BY bCodeTitle, bBank3 ASC;";
    $rs3 = $conn->CacheExecute(1, $sql);
    ?>
                            <br /><label for="bank4[]"></label>*分行別
                            <select name="bank4[]" id="bank4[]" style="width:130px;"
                                class="combobox b4n_<?php echo $j; ?>" onchange="bankphone(<?php echo $j; ?>,2)">
                                <option value="">選擇分行</option>
                                <?php while (!$rs3->EOF) {?>
                                <option value="<?php echo $rs3->fields["bBank4"]; ?>">
                                    <?php echo '(' . $rs3->fields['bBank4'] . ')' . trim($rs3->fields["bBank4_name"]); ?>
                                </option>
                                <?php
$rs3->MoveNext();}
    ?>
                            </select>
                            <br />
                            <span id="bankp<?php echo $j; ?>" style="color:#FF0000;"></span>
                        </td>
                        <td width="200">*戶名
                            <label for="t_name[]"></label>
                            <input name="t_name[]" type="text" id="t_namen<?php echo $j; ?>" size="14"
                                class="tn_<?php echo $j; ?>" onblur="rel_words('t_namen<?php echo $j; ?>')" />

                            <br />
                            *帳號
                            <label for="t_account[]"></label>
                            <input name="t_account[]" type="text" id="t_accountn<?php echo $j; ?>" size="14"
                                maxlength="14" class="ta_<?php echo $j; ?>" onkeyup="bank_check('n')" />
                        </td>
                        <td width="285">
                            <div id="s_change" style="display:block;">調帳選擇

                                <select name="change_s[]" id="change_s[]" class="combobox">
                                    <option value="x">請選擇入帳記錄</option>
                                    <?php foreach ($reconciliation_option as $k => $v): ?>
                                    <option value="<?php echo $k; ?>">$v</option>
                                    <?php endforeach?>
                                </select>
                                <br />
                            </div>
                            *金額NT$
                            <label for="t_money[]"></label>
                            <input name="t_money[]" type="text" id="nt_money<?php echo $j; ?>" style="text-align:right;"
                                onKeyUp="recal('n',<?php echo $j; ?>)" size="10" />元
                            <input name="t_cost[]" type="hidden" id="t_cost[]" value="0" />
                            <br />
                            <div id="s_serviceN_<?php echo $j; ?>" style="display:none;">
                                對象:<select name="serviceTarget[]" id="nserviceTarget<?php echo $j; ?>" class="combobox"
                                    onchange="serviceMoney('n',<?php echo $j; ?>)">
                                    <option value="owner" selected>賣方服務費</option>
                                    <option value="buyer">買方服務費</option>
                                </select><br />
                                買方服務費:
                                <input type="text" style="text-align:right;" class="nt_buyer<?php echo $j; ?>" size="10"
                                    disabled /><br />
                                <input type="hidden" name="t_buyer[]" class="nt_buyer<?php echo $j; ?>"
                                    class="service_fee_buyer" />
                                賣方服務費:
                                <input type="text" style="text-align:right;" class="nt_seller<?php echo $j; ?>"
                                    size="10" disabled /><br />
                                <input type="hidden" name="t_seller[]" class="nt_seller<?php echo $j; ?>"
                                    class="service_fee_seller" />
                            </div>
                        </td>

                        <td width="182">
                            證號
                            <label for="pid[]"></label>
                            <input name="pid[]" type="text" id="pid[]" size="10" />

                            <br />
                            EMail
                            <label for="email[]"></label>
                            <input name="email[]" type="text" id="email[]" size="13" />
                            <br />
                            FAX
                            <label for="fax[]"></label>
                            <input name="fax[]" type="text" id="fax[]" size="15" />
                        </td>
                        <td width="167">*附言(勿按ENTER換行)<br />
                            <label for="t_cost[]"></label>
                            <label for="t_txt[]"></label>
                            <textarea name="t_txt[]" id="t_txt<?=($j + $index)?>" cols="20" rows="5"
                                onblur="rel_words('t_txt<?=($j + $index)?>')"></textarea>

                        </td>
                        <td width="130" align="center">
                            <?php if ($smsSend == 1): ?>
                            <input type="hidden" name="tSend[]" value="1" />
                            <?php else: ?>
                            <input type="hidden" name="tSend[]" value="0" />
                            <?php endif?>

                        </td>
                        <td width="130">
                            <div id="Note<?=($j + $index)?>">存摺備註欄<br>(限聯行轉帳且字數為六個字)</div>

                            <input type="text" name="bankshowtxt[]" id="bankshowtxt<?=($j + $index)?>" maxlength="6"
                                value="<?=$owner . $buyer?>" />
                        </td>
                        <td>
                        </td>
                    </tr>

                </table>
            </div>
            <?php
}?>
        </div>
        <p>
            <input type="button" value="送出" onclick="go()" id="sub" />
        </p>
    </form>
    <p>
        注意: <br />
        1 角色選擇,交易類別,解匯行,分行別,戶名,帳號,金額,附言皆為必填欄位. <br />
        2 出額金額,跨行上限為 500萬,一銀則不限.<br />
        3 帳號,金額一律以半形數字輸入.<br />
        4 附言中,一律以全形中文來輸入. <br />
        5 傳真號碼一律以半形數字輸入,不需輸入其他符號. ex: 0227522793<br />
        6 一銀虛擬轉虛擬 ,交易類別選聯行轉帳 , 戶名寫:第一商業銀行受託信託財產專戶－第一建經<br />
        7 交易類別:臨櫃開票 戶名留一格空白,銀行帳號填入14碼0 (00000000000000).<br />
        8 台銀開票帳戶：台銀/忠孝 &nbsp;&nbsp;帳號：053001144289&nbsp;&nbsp;戶名：第一建築經理股份有限公司
    </p>
    <div id="realtyC"></div>
</body>

</html>