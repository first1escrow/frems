<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCaseScrivener.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/first1DB.php';

use First1\V1\PayByCase\PayByCaseScrivener;

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '查看/編修待出款案件明細');

$del = $_REQUEST["del"];
//是否有刪除
if ($del == 'ok') {
    $tid = $_REQUEST["tid"];

    $sql   = 'SELECT * FROM tBankTrans WHERE tId="' . $tid . '" AND tOk = 1;';
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    if ($total == 0) {
        //找到要被刪除的保證號碼
        $sql  = 'SELECT tMemo, tKind, tInvoice, tObjKind FROM tBankTrans WHERE tId = ' . $tid . ';';
        $_rs  = $conn->Execute($sql);
        $_cId = $_rs->fields['tMemo'];
        $kind = $_rs->fields['tKind']; //類別
        $objKind = $_rs->fields['tObjKind']; //項目
        $invoice = $_rs->fields['tInvoice']; //開發票

        $_rs = null;unset($_rs);
        ##

        //刪除出款紀錄
        $delx = 'DELETE FROM tBankTrans WHERE tId="' . $tid . '";';
        $conn->Execute($delx);
        ##

        //恢復稅款為未出款
        $sql = 'UPDATE tExpenseDetail SET eOK="" WHERE eOK="' . $tid . '";';
        $conn->Execute($sql);

        //返還稅款清除ID
        $sql = "UPDATE  tBankTrans SET tObjKind2Item = '' WHERE tObjKind2Item = '" . $tid . "'";
        $conn->Execute($sql);

        //20230421 回饋金隨案支付 20250306因為重審沒意義 所以先移除
//        $pay_by_case_scrivener = new PayByCaseScrivener(new first1DB);
//        $pay_by_case_scrivener->modifyAffectCaseBankAccountByCase($_cId);

        //如果刪除保證費 或 刪除所有代墊利息開發票 就解鎖代書回饋金欄位
        if($kind == '保證費' or $invoice != null) {
            $invoiceCount = 0; //要開發票的總筆數
            $certifiedMoneyCount = 0; //保證費的總筆數

            if($invoice != null) {
                $sql   = 'SELECT `tMemo` FROM tBankTrans WHERE tMemo="' . $_cId . '" AND tInvoice is not null;';
                $rs    = $conn->Execute($sql);
                $invoiceCount = $rs->RecordCount();
            }
            if($kind == '保證費') {
                $sql   = 'SELECT `tMemo` FROM tBankTrans WHERE tMemo="' . $_cId . '" AND tKind = "保證費";';
                $rs    = $conn->Execute($sql);
                $certifiedMoneyCount = $rs->RecordCount();
            }
            if($invoiceCount == 0 and $certifiedMoneyCount == 0) {
                $sql = 'UPDATE `tContractCase` SET cFeedBackScrivenerClose = 0 WHERE cCertifiedId = ' . $_cId;
                $conn->Execute($sql);
            }
            if($objKind == '履保費先收(結案回饋)') {
                $sql   = 'SELECT `tMemo` FROM tBankTrans WHERE tMemo="' . $_cId . '" AND tKind = "保證費" AND tObjKind = "履保費先收(結案回饋)";';
                $rs    = $conn->Execute($sql);
                if($rs->RecordCount() == 0) {
                    $sql = 'UPDATE `tContractCase` SET cBankRelay  = "N" WHERE cCertifiedId = ' . $_cId;
                    $conn->Execute($sql);
                }
            }
        }

        $_cId = $pay_by_case_scrivener = $kind = $objKind = null;
        unset($_cId, $pay_by_case_scrivener, $kind, $objKind);
        ##
    } else {
        echo "<script>alert(\"此筆已打包，頁面過期即將重整!!\") ;location.href='list2.php'</script>";
    }
}
##

$save = $_POST["save"];

//更新儲存
if ($save == 'ok') {
    $vr_code        = $_POST["vr_code"];
    $bid            = $_POST["bid"];
    $bank_kind      = $_POST["bank_kind"];
    $target         = $_POST["target"];
    $export         = $_POST["export"];
    $code2          = $_POST["code2"];
    $bank3          = $_POST["bank3"];
    $bank4          = $_POST["bank4"];
    $t_name         = $_POST["t_name"];
    $t_account      = $_POST["t_account"];
    $t_cost         = $_POST["t_cost"];
    $t_money        = $_POST["t_money"];
    $t_txt          = $_POST["t_txt"];
    $pid            = $_POST["pid"];
    $objKind        = $_POST["objKind"];
    $email          = $_POST["email"];
    $fax            = $_POST["fax"];
    $send           = $_POST["tSend"];
    $showTxt        = $_POST['bankshowtxt'];
    $tScrivenerNote = $_POST['scrivenerNote'];

    $replace_patt = array("\r\n", "\n", "\r", " ", "　");

    $_total = count($vr_code);

    for ($i = 0; $i < $_total; $i++) {
        $_tid                 = $bid[$i];
        $record["tVR_Code"]   = $vr_code[$i];
        $record["tBank_kind"] = $bank_kind[$i];
        $record["tCode"]      = $export[$i];
        $record['tCode2']     = $code2[$i];
        $record["tKind"]      = $target[$i];
        $record["tObjKind"]   = $objKind[$i];

        $bank                = $bank3[$i] . $bank4[$i];
        $record["tBankCode"] = $bank;

        $record["tAccount"]     = $t_account[$i];
        $record["tAccountName"] = $t_name[$i];
        $record["tAccountId"]   = $pid[$i];
        $record["tMoney"]       = $t_money[$i];

        $serial          = substr($vr_code[$i], 5);
        $record["tMemo"] = $serial;

        $t_txt[$i]                = str_replace($replace_patt, "", $t_txt[$i]);
        $record["tTxt"]           = $t_txt[$i];
        $record["tEmail"]         = $email[$i];
        $record["tFax"]           = $fax[$i];
        $record["tSend"]          = $send[$i];
        $record['tBankShowTxt']   = $showTxt[$i];
        $record['tScrivenerNote'] = $tScrivenerNote[$i];

        if ($record['tBankShowTxt'] == null) {
            $record['tBankShowTxt'] = '';
        }

        $sql   = 'SELECT * FROM tBankTrans WHERE tId="' . $_tid . '" AND tOk = 1;';
        $rs    = $conn->Execute($sql);
        $total = $rs->RecordCount();

        if ($total == 0) {
            $conn->AutoExecute("tBankTrans", $record, 'UPDATE', "tId=$_tid");
        } else {
            echo "<script>alert(\"此筆已打包，頁面過期即將重整!!\") ;location.href='list2.php'</script>";
        }

        $ok = 1;
    }
}
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

function getCount($kind, $cId, $objKind)
{
    global $conn;

    $sql   = "SELECT * FROM tBankTrans WHERE tMemo = '" . $cId . "' AND tKind = '" . $kind . "' AND tObjKind ='" . $objKind . "' AND tOk='2'";
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    return $total;
}
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
    <link type="text/css" href="css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
    <script type="text/javascript" src="/libs/jquery.colorbox-min.js"></script>
    <link rel="stylesheet" href="../css/colorbox.css" />

    <title>出帳檔審核匯出作業</title>
    <style type="text/css">
    .font12 {
        font-size: 12px;
    }

    .ui-combobox {
        position: relative;
        display: inline-block;
    }

    .ui-combobox-toggle {
        position: absolute;
        top: 0;
        bottom: 0;
        margin-left: -1px;
        padding: 0;
        /* adjust styles for IE 6/7 */
        *height: 1.5em;
        *top: 0.1em;
        width: 20px;
    }

    .ui-combobox-input {
        margin: 0;
        padding: 0.1em;
        width: 160px;
    }

    .ui-autocomplete {
        width: 160px;
        max-height: 300px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        /* add padding to account for vertical scrollbar */
        padding-right: 20px;
    }

    .ui-autocomplete {
        width: 160px;
        max-height: 300px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        /* add padding to account for vertical scrollbar */
        padding-right: 20px;
        font-size: 12px;
    }

    .ui-autocomplete-input {
        width: 120px;
        font-size: 12px;
    }
    </style>
    <script>
    $(document).ready(function() {
        <?php if ($_SESSION["refresh"] == "1") {?>
            window.opener.document.location.reload();
        <?php $_SESSION["refresh"] = 0 ?>
        <?php }?>

        //AJAX 重新跑合約書帳務收支明細(刪除)
        var ck = "<?=$_REQUEST['del']?>";
        if (ck == 'ok') {
            tran_table();
        }
        <?php if ($_REQUEST["ok"] == "1") {?>
        var id = $('[name="certifiedid"]', opener.document).val();
        $('form[name=form_edit] input[name=id]', opener.document).val(id);
        tran_table();
        <?php }?>

        $.widget("ui.combobox", {
            _create: function() {
                var input,
                    self = this,
                    select = this.element.hide(),
                    selected = select.children(":selected"),
                    value = selected.val() ? selected.text() : "",
                    wrapper = this.wrapper = $("<span>")
                    .addClass("ui-combobox")
                    .insertAfter(select);

                input = $("<input>")
                    .appendTo(wrapper)
                    .val(value)
                    .addClass("ui-state-default ui-combobox-input")
                    .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: function(request, response) {
                            var matcher = new RegExp($.ui.autocomplete.escapeRegex(request
                                .term), "i");
                            response(select.children("option").map(function() {
                                var text = $(this).text();
                                if (this.value && (!request.term || matcher
                                        .test(text)))
                                    return {
                                        label: text.replace(
                                            new RegExp(
                                                "(?![^&;]+;)(?!<[^<>]*)(" +
                                                $.ui.autocomplete
                                                .escapeRegex(request.term) +
                                                ")(?![^<>]*>)(?![^&;]+;)",
                                                "gi"
                                            ), "<strong>$1</strong>"),
                                        value: text,
                                        option: this
                                    };
                            }));
                        },
                        select: function(event, ui) {
                            ui.item.option.selected = true;
                            self._trigger("selected", event, {
                                item: ui.item.option
                            });
                            select.trigger("change");
                        },
                        change: function(event, ui) {
                            if (!ui.item) {
                                var matcher = new RegExp("^" + $.ui.autocomplete
                                        .escapeRegex($(this).val()) + "$", "i"),
                                    valid = false;
                                select.children("option").each(function() {
                                    if ($(this).text().match(matcher)) {
                                        this.selected = valid = true;
                                        $("[name='']")
                                        return false;
                                    }
                                });
                                if (!valid) {
                                    // remove invalid value, as it didn't match anything
                                    $(this).val("");
                                    select.val("");
                                    input.data("autocomplete").term = "";
                                    return false;
                                }
                            }
                        }
                    })
                    .addClass("ui-widget ui-widget-content ui-corner-left");

                input.data("autocomplete")._renderItem = function(ul, item) {
                    return $("<li></li>")
                        .data("item.autocomplete", item)
                        .append("<a>" + item.label + "</a>")
                        .appendTo(ul);
                };

                $("<a>")
                    .attr("tabIndex", -1)
                    .attr("title", "Show All Items")
                    .appendTo(wrapper)
                    .button({
                        icons: {
                            primary: "ui-icon-triangle-1-s"
                        },
                        text: false
                    })
                    .removeClass("ui-corner-all")
                    .addClass("ui-corner-right ui-combobox-toggle")
                    .click(function() {
                        // close if already visible
                        if (input.autocomplete("widget").is(":visible")) {
                            input.autocomplete("close");
                            return;
                        }

                        // work around a bug (likely same cause as #5265)
                        $(this).blur();

                        // pass empty string as value to search for, displaying all results
                        input.autocomplete("search", "");
                        input.focus();
                    });
            },

            destroy: function() {
                this.wrapper.remove();
                this.element.show();
                $.Widget.prototype.destroy.call(this);
            }
        });

        $('.bank').combobox();
    });

    function tran_table() {
        var id = $('[name="certifiedid"]', opener.document).val();

        $.ajax({
                url: '../../escrow/tran_table.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    "id": id
                },
            })
            .done(function(txt) {
                $('div #tran_show', opener.document).empty();
                $('div #tran_show', opener.document).html(txt);
            });
    }

    function bankphone(v, type) {
        //type 1 原有的 2額外新增的
        var obj = $(".objKind" + v).val();

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
                    url: 'getBankPhone.php',
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

    function export_file(x) {
        var _number = Math.random();
        var url = "_export_file.php?i=" + _number + "&id=" + x;

        $.ajax({
            url: url,
            error: function(xhr) {
                alert("error!!");
            },
            success: function(response) {

            }
        });
    }

    function bank_select_index(x, kind, index, b2) {
        var _number = Math.random();
        var url = "_bank_select.php?i=" + _number + "&bank3=" + x + "&b4=" + b2;

        $.ajax({
            url: url,
            error: function(xhr) {
                alert("error!!");
            },
            success: function(response) {
                $("." + kind).empty();
                $("." + kind).append(response);
                setBankAutoComplete(kind);
            }
        });

        check03(index);
    }

    function check03(id) {
        var code = $(".bb_" + id).val().substr(3, 3); //055 050 是還款帳戶所以是聯行代清償(03) 永豐限定
        var mainBank = "";
        var objKind = $("#objKind" + id).val();
        var bank = $(".b3_" + id).val();
        var _export = '01';

        if ($('#vr_code' + id).val().substr(0, 5) == "99985" || $('#vr_code' + id).val().substr(0, 5) == "99986") {
            mainBank = "807";
        } else if ($('#vr_code' + id).val().substr(0, 5) == "60001") {
            mainBank = "007";
        }

        if (objKind == '代清償') {
            if (mainBank == '807') {
                if (bank == mainBank) {
                    if ((code == '055' || code == '050')) {
                        _export = '03';
                    }
                }
            } else {
                if (bank != mainBank) {
                    _export = '02';
                } else {
                    _export = '03';
                }

            }

            $("#export" + id).val(_export);

            if (_export == '01') {
                $('#export' + id + ' option').each(function() {
                    if ($(this).text() == '聯行轉帳' && _export == '01') {
                        $(this).attr('selected', true);
                    } else {
                        $(this).attr('selected', false);
                    }
                });
            }
        }
    }

    function setBankAutoComplete(kind) {
        $.widget("ui.combobox", {
            _create: function() {
                var input,
                    self = this,
                    select = this.element.hide(),
                    selected = select.children(":selected"),
                    value = selected.val() ? selected.text() : "",
                    wrapper = this.wrapper = $("<span>")
                    .addClass("ui-combobox")
                    .insertAfter(select);
                input = $("<input>")
                    .appendTo(wrapper)
                    .val(value)
                    .addClass("ui-state-default ui-combobox-input")
                    .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: function(request, response) {
                            var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term),
                                "i");
                            response(select.children("option").map(function() {
                                var text = $(this).text();
                                if (this.value && (!request.term || matcher.test(text)))
                                    return {
                                        label: text.replace(
                                            new RegExp(
                                                "(?![^&;]+;)(?!<[^<>]*)(" +
                                                $.ui.autocomplete.escapeRegex(
                                                    request.term) +
                                                ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                            ), "<strong>$1</strong>"),
                                        value: text,
                                        option: this
                                    };
                            }));
                        },
                        select: function(event, ui) {
                            ui.item.option.selected = true;
                            self._trigger("selected", event, {
                                item: ui.item.option
                            });
                            select.trigger("change");
                        },
                        change: function(event, ui) {
                            if (!ui.item) {
                                var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex($(this)
                                        .val()) + "$", "i"),
                                    valid = false;
                                select.children("option").each(function() {
                                    if ($(this).text().match(matcher)) {
                                        this.selected = valid = true;
                                        $("[name='']")
                                        return false;
                                    }
                                });
                                if (!valid) {
                                    // remove invalid value, as it didn't match anything
                                    $(this).val("");
                                    select.val("");
                                    input.data("autocomplete").term = "";
                                    return false;
                                }
                            }
                        }
                    })
                    .addClass("ui-widget ui-widget-content ui-corner-left");

                input.data("autocomplete")._renderItem = function(ul, item) {
                    return $("<li></li>")
                        .data("item.autocomplete", item)
                        .append("<a>" + item.label + "</a>")
                        .appendTo(ul);
                };

                $("<a>")
                    .attr("tabIndex", -1)
                    .attr("title", "Show All Items")
                    .appendTo(wrapper)
                    .button({
                        icons: {
                            primary: "ui-icon-triangle-1-s"
                        },
                        text: false
                    })
                    .removeClass("ui-corner-all")
                    .addClass("ui-corner-right ui-combobox-toggle")
                    .click(function() {
                        // close if already visible
                        if (input.autocomplete("widget").is(":visible")) {
                            input.autocomplete("close");
                            return;
                        }

                        // work around a bug (likely same cause as #5265)
                        $(this).blur();

                        // pass empty string as value to search for, displaying all results
                        input.autocomplete("search", "");
                        input.focus();
                    });
            },

            destroy: function() {
                this.wrapper.remove();
                this.element.show();
                $.Widget.prototype.destroy.call(this);
            }
        });

        $("." + kind).combobox();
    }

    function waring_msg() {
        alert('請確認附言內容跟出款項目相符!');
    }

    function del(tt) {
        $('[name="tid"]').val(tt);
        $('[name="del"]').val('ok');

        $('[name="delform"]').submit();
    }

    function mod(nn) {
        $('[name="del"]').val('');
        $('[name="' + nn + '"]').submit();
    }

    function rel_words(name) //半形轉全形
    {
        var val = $("#" + name).val();

        $.ajax({
                url: 'new/replace_words.php',
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

    function setTxt(id, val, name, cid, name2) {
        var no = cid.substring(5);

        //如果是保證費要帶保號
        if (val == '保證費') {
            $.ajax({
                    url: 'new/replace_words.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'txt': no
                    },
                })
                .done(function(txt) {
                    $("#" + name + id).text(txt);
                });

            $("#" + name2 + id + " option").remove();
            $("#" + name2 + id).html(
                "<option value=\"\" selected=\"selected\">項目</option><option value=\"點交(結案)\" >點交(結案)</option><option value=\"解除契約\">解約/終止履保</option><option value=\"建經發函終止\">建經發函終止</option>"
            );
        } else {
            var tmp = $("#" + name2 + id).val();

            $("#" + name + id).text('');
            $("#" + name2 + id + " option").remove();

            let el = '<option value="" >項目</option>' +
                '<option value="賣方先動撥">賣方先動撥</option>' +
                '<option value="仲介服務費">仲介服務費</option>' +
                '<option value="代清償">代清償</option>' +
                '<option value="點交(結案)">點交(結案)</option>' +
                '<option value="其他">其他</option>' +
                '<option value="調帳">調帳</option>' +
                '<option value="解除契約">解約/終止履保</option>' +
                '<option value="保留款撥付">保留款撥付</option>' +
                '<option value="建經發函終止">建經發函終止</option>' +
                '<option value="預售屋">預售屋</option>' +
                '<option value="代墊利息">代墊利息</option>';
            $("#" + name2 + id).html(el);
            // $("#"+name2+id).html("<option value=\"\" selected=\"selected\">項目</option><option value=\"賣方先動撥\">賣方先動撥</option><option value=\"仲介服務費\">仲介服務費</option><option value=\"代清償\">代清償</option><option value=\"點交(結案)\">點交(結案)</option><option value=\"其他\">其他</option><option value=\"調帳\">調帳</option><option value=\"解除契約\">解約/終止履保</option><option value=\"保留款撥付\">保留款撥付</option><option value=\"建經發函終止\">建經發函終止</option>");
            $("#" + name2 + id + "").val(tmp);
        }

        setBankTxt(id, '');
    }

    function book(id) {
        $("[name='id']").val(id);
        $("[name='IBook']").submit();
    }

    /*取得交易名稱*/
    function setCode2(name, v) {
        var txt = $("#" + name).find(":selected").text();
        var val = $("#" + name).val();
        var id = $("#bid" + v).val();
        var code = $("#vr_code" + v).val();
        var txt2 = '';
        var bank = code.substr(0, 5);

        $("#code2" + v).val(txt);

        if (txt == '虛轉虛' || val == "05" || val == "04") {
            $("#showb" + v).html('<input type="button" value="指示書" onclick="book(' + id + ')" />');
            if (val == "04" || val == "05") {
                bankAccountAuto(v, code);
                if (txt == '大額繳稅' && bank == '60001') {
                    $("#t_txt" + v).text("大額繳稅詳第一建經指示書編號");
                }

                if (txt == '臨櫃開票' && bank == '60001') {
                    $("#t_txt" + v).text("臨櫃開票詳第一建經指示書編號");
                }
            }
        } else {
            $("#showb" + v).html('');
        }

        setBankTxt(v, 'code');
    }

    function bankAccountAuto(id, cId) {
        $.ajax({
            url: 'getMainBankAccount.php',
            type: 'POST',
            dataType: 'html',
            data: {
                id: cId
            },
        }).done(function(msg) {
            var obj = jQuery.parseJSON(msg);
            if (obj.msg != 1) {
                $(".b3_" + id).combobox('destroy');
                $(".b3_" + id).val(obj.Bank);
                setBankAutoComplete("b3_" + id);

                $(".b4_" + id).combobox('destroy');
                bank_select_index(obj.Bank, "b4_" + id, id, obj.BankBranch);

                $("#t_name" + id).val(obj.AccName);
                $(".bb_" + id).val(obj.Acc);
            }
        });
    }

    function setBankT(id) {
        var v = $("#bankshowtxt" + id).val();
        $("#bst" + id).val(v);
    }

    function setBankTxt(v, cat) {
        var code = $("#vr_code" + v).val();
        var bank = code.substr(0, 5);

        if (($("#export" + v).val() == '01' || $("#export" + v).val() == '02') && (bank == '99985' || bank == '99986' ||
                bank == '96988') && $("#target" + v).val() == '地政士') {
            $('#Note' + v).show();
            $('#Note' + v).html("存摺備註欄<br>(限聯行轉帳、跨行代清償且字數為六個字)");

            $("#bankshowtxt" + v + "").remove();
            $.ajax({
                    url: 'getCustomer.php',
                    type: 'POST',
                    dataType: 'html',
                    async: false,
                    data: {
                        id: code
                    },
                })
                .done(function(msg) {
                    $("#bs" + v).html("<input type=\"text\" id=\"bankshowtxt" + v + "\" maxlength=\"6\" value=\"" +
                        msg + "\">");
                });
        } else {
            $('#Note' + v).hide();
            $('#bankshowtxt' + v).val('');
            $("#bs" + v).html('<input type="hidden" name="bankshowtxt[]" id="bankshowtxt' + v + '" maxlength="6" >');
        }

        $("#bst" + v).val($("#bankshowtxt" + v).attr('value'));
        if (cat == '' || cat == null) {
            check03(v);
        }
    }

    function checkowner(index, id) {
        var val = $("#t_name" + index).val();
        $.ajax({
                url: 'includes/checkOwner.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id,
                    val: val
                },
            })
            .done(function(msg) {
                if (msg == 'fail') {
                    $("#anotherS1" + index).show();
                } else {
                    $("#anotherS1" + index).hide();
                }
            });
    }

    function openSellerNote() {
        var url = "/bank/sellerNote.php";
        $.colorbox({
            iframe: true,
            width: "60%",
            height: "60%",
            href: url,
            onClosed: function() {
                location.href = 'list2.php';
            }
        });
    }

    function checkFrom(val, cId) {
        var money = 0;
        var check = 0;

        $(".c" + cId).each(function(index, el) {
            money += parseInt($(this).val());
        });

        $.ajax({
            url: 'checkCommitmentMoney.php',
            type: 'POST',
            dataType: 'html',
            async: false,
            data: {
                cId: cId,
                money: money
            },
        }).done(function(json) {
            var obj = jQuery.parseJSON(json);
            if (obj.code != 200) {
                alert(obj.codeMsg);
                check = 1;
            }
        });

        if (check == 1) {
            return false;
        } else {
            $("#form" + val).submit();
        }
    }

    function legalUnLock(id) {
        $.ajax({
            url: 'legalUnLock.php',
            type: 'POST',
            dataType: 'html',
            data: {
                id: id
            },
        }).done(function(msg) {
            alert(msg);
            location.href = 'list2.php';
        }).fail(function(xhr, textStatus, errorThrown) {
            alert(textStatus);
        });
    }
    </script>
</head>

<body>
    <form action="instructions/AddIBook.php" name="IBook" method="POST" target="_blank">
        <input type="hidden" name="id" />
        <input type="hidden" name="code" />
        <input type="hidden" name="code2" />
    </form>
    <form method="POST" name="delform">
        <input type="hidden" name="tid" value="">
        <input type="hidden" name="del" value="">
    </form>

    <div style="width:1550px; margin-bottom:5px; height:22px; background-color: #CCC">
        <div style="float:left;margin-left: 10px;">
            <a href="instructions/IBookList.php">指示書</a>
        </div>
        <div style="float:left;margin-left: 10px;">
            <font color=red><strong>待修改資料</strong></font>
        </div>
        <?php
if ($_SESSION["member_id"] == '6' || in_array($_SESSION["member_pDep"], [5, 6])) { //個別權限顯示
    echo '<div style="float:left;margin-left: 10px;"> <a href="/bank/BankTransProcess.php">出款進度</a> </div>' . "\n";
}

if ($_SESSION["member_bankcheck"] == '1') { //個別權限顯示
    echo '<div style="float:left; margin-left: 10px;"> <a href="/bank/list.php">未審核列表</a></div>' . "\n";
}
?>
    </div>

    <div>
        &nbsp;&nbsp;
        <input type="button" name="button2" id="button2" value="批次列印"
            onclick="window.open ('/bank/report/export_select2.php', 'newwindow2', 'height=800, width=920, top=0, left=0, toolbar=no, menubar=no, scrollbars=yes, resizable=no,location=no, status=no')" />
        &nbsp;&nbsp;
        <table width="1550px" border="0" cellpadding="0" cellspacing="0" class="font12">
            <?php
if ($_SESSION["member_pDep"] == 5 && !in_array($_SESSION["member_id"], [1, 12])) {
    $str = " AND tOwner ='" . $_SESSION['member_name'] . "'";
} elseif (($_SESSION["member_bankcheck"] == '0')) {
    $str = " AND tOwner ='" . $_SESSION['member_name'] . "'";
    if (in_array($_SESSION["member_pDep"], [6])) {
        // if (in_array($_SESSION["member_id"], [22])) {
        $str = '';
    }
}

$sql = "SELECT * FROM tBankTrans WHERE tOk='2'" . $str;
$rs  = $conn->Execute($sql);

$legalAllow = '';
$_error     = 0;
$j          = 1;
while (!$rs->EOF) {
    //20240618 Project S只有特定人員可以查看
    if (!in_array($_SESSION['member_id'], [1, 3, 6, 12, 13, 36, 84, 90]) && ($rs->fields["tMemo"] == '130119712')) {
        continue;
    }

    $_target = $rs->fields["tBank_kind"];

    //20241128 非法務關注案件或法務放行案件不限制修改與出款
    $tLegalAllow = $rs->fields["tLegalAllow"];
    $legalAllow  = (!empty($tLegalAllow) && ($tLegalAllow == '1')) ? ' disabled' : '';
    $modifyAllow = (in_array($_SESSION["member_pDep"], [6]) && ($_SESSION["member_id"] != 22)) ? ' disabled' : '';

    if ($rs->fields["tOk"] != '1') {
        $_error++;
    }

    switch ($rs->fields["tCode"]) {
        case "01":
            $_title = "聯行轉帳";
            break;
        case "02":
            $_title = "跨行代清償";
            break;
        case "03":
            $_title = "聯行代清償";
            break;
        case "04":
            $_title = "大額繳稅";
            break;
        case "05":
            $_title = "臨櫃開票";
            break;
        case "06":
            $_title = "利息";
            break;
    }

    $bank3 = substr($rs->fields["tBankCode"], 0, 3);
    $bank4 = substr($rs->fields["tBankCode"], 3, 4);

    $sql         = "SELECT * FROM tBank WHERE bBank3='$bank3' AND bBank4='' LIMIT 1";
    $rs1         = $conn->Execute($sql);
    $_bank_title = $rs1->fields["bBank4_name"];

    $sql           = "SELECT * FROM tBank WHERE bBank4='$bank4' LIMIT 1";
    $rs2           = $conn->Execute($sql);
    $_bank_cotitle = $rs2->fields["bBank4_name"];

    ##是否要顯示顏色
    if ($rs->fields['tKind'] == '仲介' && getCount($rs->fields['tKind'], $rs->fields['tMemo'], $rs->fields['tObjKind']) > 1) {
        $color = 'rgb(255,255,170)';
    } elseif ($rs->fields['tKind'] == '地政士' && getCount($rs->fields['tKind'], $rs->fields['tMemo'], $rs->fields['tObjKind']) > 1) {
        $color = 'rgb(218,242,142)';
    } else {
        $color = 'rgb(255,255,255)';
    }

    $color = (!empty($tLegalAllow) && ($tLegalAllow == '1')) ? 'rgb(250,240,230)' : $color;
    ?>
            <form id="form<?=$j?>" name="form<?=$j?>" method="post" action="" style="margin:0px; padding:0px;">

                <tr style='background-color:<?=$color?>'>
                    <td colspan="2">專屬帳號 <strong><?php echo $rs->fields["tVR_Code"]; ?></strong>
                        <div style="display:block;">
                            <?php
if ($rs->fields['tStoreId'] > 0) {
        $sql = "SELECT bStore,bName,(SELECT bName FROM tBrand WHERE bId=bBrand) AS Brand FROM tBranch WHERE bId = '" . $rs->fields['tStoreId'] . "'";
        $rsS = $conn->Execute($sql);
        echo $rsS->fields["Brand"] . "_" . $rsS->fields["bStore"] . "_" . $rsS->fields["bName"];
    }
    ?>
                        </div>
                        <input name="save" type="hidden" id="save" value="ok" />
                        <label for="vr_code2">
                            <input name="vr_code[]" type="hidden" id="vr_code<?=$j?>"
                                value="<?php echo $rs->fields["tVR_Code"]; ?>" />

                            <input type="hidden" name="bid[]" id="bid<?=$j?>"
                                value="<?php echo $rs->fields["tId"]; ?>" />
                        </label>
                    </td>
                    <td width="172">
                        <select name="bank_kind[]" id="bank_kind[]">
                            <?php
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;';
    $rsb = $conn->Execute($sql);

    while (!$rsb->EOF) {
        $bk_tmp = $rsb->fields['cBankName'];

        echo '<option value="' . $bk_tmp . '"';
        if (preg_match("/$bk_tmp/", $rs->fields["tBank_kind"])) {
            echo ' selected="selected"';
        }
        echo '>' . $bk_tmp . "</option>\n";

        $rsb->MoveNext();
    }
    ?>
                        </select>
                    </td>
                    <td width="167">&nbsp;</td>
                    <td width="182"><?php if($rs->fields['tInvoice'] != null):?><span style="color: blue; ">代墊利息或履保費已收需開發票</span><?php endif?></td>
                    <td width="132" align="center">
                        <input type="button" name="button" id="button" value="修改"
                            onclick="checkFrom(<?=$j?>,'<?=$rs->fields["tMemo"]?>')" <?=$legalAllow?>
                            <?=$modifyAllow?> />
                        <input type="button" name="button3" id="button3" value="刪除"
                            onclick="location.href='list2.php?tid=<?php echo $rs->fields["tId"]; ?>&del=ok';"
                            <?=$legalAllow?> <?=$modifyAllow?> />
                        <?php
if (!empty($tLegalAllow) && ($tLegalAllow == '1') && ($_SESSION['member_pDep'] == '6' or $_SESSION['member_id'] == '6')) {
        ?>
                        <button type="button" onclick="legalUnLock(<?=$rs->fields["tId"]?>)">法務解鎖</button>
                        <?php
}
    ?>
                    </td>
                    <td width="100">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr id="tr_pos" style='background-color:<?=$color?>'>
                    <td width="148"><label for="target[]"></label>
                        *
                        <select name="target[]" id="target<?=$j?>"
                            onchange="setTxt(<?=$j?>,this.value,'t_txt','<?=$rs->fields["tVR_Code"]?>','objKind')">
                            <option value="">角色選擇</option>
                            <option value="賣方"
                                <?php echo ($rs->fields["tKind"] == "賣方") ? 'selected="selected"' : ''; ?>>賣方</option>
                            <option value="買方"
                                <?php echo ($rs->fields["tKind"] == "買方") ? 'selected="selected"' : ''; ?>>買方</option>
                            <option value="地政士"
                                <?php echo ($rs->fields["tKind"] == "地政士") ? 'selected="selected"' : ''; ?>>地政士</option>
                            <option value="仲介"
                                <?php echo ($rs->fields["tKind"] == "仲介") ? 'selected="selected"' : ''; ?>>仲介</option>
                            <option value="保證費"
                                <?php echo ($rs->fields["tKind"] == "保證費") ? 'selected="selected"' : ''; ?>>保證費</option>
                            <option value="地政士回饋金"
                                <?php echo ($rs->fields["tKind"] == "地政士回饋金") ? 'selected="selected"' : ''; ?>>地政士回饋金
                            </option>
                        </select>
                        <br />
                        <label for="export[]"></label>
                        *
                        <input type="hidden" name="code2[]" id="code2<?=$j?>" value="<?=$rs->fields["tCode2"]?>" />
                        <select name="export[]" id="export<?=$j?>" onchange="setCode2('export<?=$j?>',<?=$j?>)">
                            <option value="">交易類別</option>
                            <option value="01"
                                <?php echo ($rs->fields["tCode"] == "01" && $rs->fields["tCode2"] == '聯行轉帳') ? 'selected="selected"' : ''; ?>>
                                聯行轉帳</option>
                            <option value="01"
                                <?php echo ($rs->fields["tCode"] == "01" && $rs->fields["tCode2"] == '一銀內轉') ? 'selected="selected"' : ''; ?>>
                                一銀內轉</option>
                            <option value="02"
                                <?php echo ($rs->fields["tCode"] == "02") ? 'selected="selected"' : ''; ?>>跨行代清償
                            </option>
                            <option value="03"
                                <?php echo ($rs->fields["tCode"] == "03") ? 'selected="selected"' : ''; ?>>聯行代清償
                            </option>
                            <option value="04"
                                <?php echo ($rs->fields["tCode"] == "04") ? 'selected="selected"' : ''; ?>>大額繳稅</option>
                            <option value="05"
                                <?php echo ($rs->fields["tCode"] == "05" && $rs->fields["tCode2"] == '臨櫃開票') ? 'selected="selected"' : ''; ?>>
                                臨櫃開票</option>
                            <option value="05"
                                <?php echo ($rs->fields["tCode"] == "05" && $rs->fields["tCode2"] == '臨櫃領現') ? 'selected="selected"' : ''; ?>>
                                臨櫃領現</option>
                        </select><br />
                        * <select name="objKind[]" id="objKind<?=$j?>" class="objKind<?=$j?>"
                            onchange="setBankTxt(<?=$j?>,'')">
                            <option value="" selected="selected">項目</option>
                            <?php
if ($rs->fields["tKind"] != "保證費") {?>
                            <option value="賣方先動撥"
                                <?php echo ($rs->fields["tObjKind"] == "賣方先動撥") ? 'selected="selected"' : ''; ?>>賣方先動撥
                            </option>
                            <option value="仲介服務費"
                                <?php echo ($rs->fields["tObjKind"] == "仲介服務費") ? 'selected="selected"' : ''; ?>>仲介服務費
                            </option>
                            <?php if ($rs->fields["tObjKind"] == "扣繳稅款"): ?>
                            <option value="扣繳稅款"
                                <?php echo ($rs->fields["tObjKind"] == "扣繳稅款") ? 'selected="selected"' : ''; ?>>扣繳稅款
                            </option>
                            <?php endif?>
                            <option value="代清償"
                                <?php echo ($rs->fields["tObjKind"] == "代清償") ? 'selected="selected"' : ''; ?>>代清償
                            </option>
                            <?php
}
    ?>

                            <option value="點交(結案)"
                                <?php echo ($rs->fields["tObjKind"] == "點交(結案)") ? 'selected="selected"' : ''; ?>>點交(結案)
                            </option>
                            <?php
if ($rs->fields["tKind"] != "保證費") {?>
                            <option value="其他"
                                <?php echo ($rs->fields["tObjKind"] == "其他") ? 'selected="selected"' : ''; ?>>其他
                            </option>
                            <option value="調帳"
                                <?php echo ($rs->fields["tObjKind"] == "調帳") ? 'selected="selected"' : ''; ?>>調帳
                            </option>
                            <?php }
    ?>
                            <option value="解除契約"
                                <?php echo ($rs->fields["tObjKind"] == '解除契約') ? 'selected="selected"' : ''; ?>>解約/終止履保
                            </option>
                            <?php
if ($rs->fields["tKind"] != "保證費") {?>
                            <option value="保留款撥付"
                                <?php echo ($rs->fields["tObjKind"] == '保留款撥付') ? 'selected="selected"' : ''; ?>>保留款撥付
                            </option>
                            <?php }
    ?>
                            <option value="建經發函終止"
                                <?php echo ($rs->fields["tObjKind"] == '建經發函終止') ? 'selected="selected"' : ''; ?>>建經發函終止
                            </option>
                            <option value="預售屋"
                                <?php echo ($rs->fields["tObjKind"] == '預售屋') ? 'selected="selected"' : ''; ?>>預售屋
                            </option>
                            <option value="代墊利息"
                                <?php echo ($rs->fields["tObjKind"] == '代墊利息') ? 'selected="selected"' : ''; ?>>代墊利息
                            </option>
                            <?php
if ($rs->fields["tKind"] == "保證費" and $rs->fields["tObjKind"] == '履保費先收(結案回饋)') {?>
                            <option value="履保費先收(結案回饋)"
                                <?php echo ($rs->fields["tObjKind"] == '履保費先收(結案回饋)') ? 'selected="selected"' : ''; ?>>履保費先收(結)
                            </option>
<?php }
                            ?>
                        </select>
                        *
                        <?php if (($rs->fields["tObjKind"] == '扣繳稅款' || $rs->fields["tObjKind"] == '賣方先動撥' || $rs->fields["tObjKind"] == '代清償') && $rs->fields["tBank_kind"] == '台新' && $rs->fields["tCode"] != "04"): ?>
                        <br />*
                        <select name="taxScrivener[]" id="taxScrivener<?=$j?>" class="taxScrivener" disabled="disabled">
                            <option value="">特殊項目</option>
                            <option value="01"
                                <?php echo ($rs->fields["tObjKind2"] == '01') ? 'selected="selected"' : ''; ?>>申請公司代墊
                            </option>
                            <option value="02"
                                <?php echo ($rs->fields["tObjKind2"] == '02') ? 'selected="selected"' : ''; ?>>返還公司代墊
                            </option>
                            <option value="03"
                                <?php echo ($rs->fields["tObjKind2"] == '03') ? 'selected="selected"' : ''; ?>>不用代墊
                            </option>
                            <option value="04"
                                <?php echo ($rs->fields["tObjKind2"] == '04') ? 'selected="selected"' : ''; ?>>申請代理出款
                            </option>
                            <option value="05"
                                <?php echo ($rs->fields["tObjKind2"] == '05') ? 'selected="selected"' : ''; ?>>公司代理出款
                            </option>
                        </select>
                        <?php endif?>
                    </td>
                    <td width="214">*解匯行
                        <label for="bank3[]"></label>
                        <?php
$sql = "SELECT * FROM tBank WHERE bBank4 = '' ORDER BY bBank3 ASC";
    $rsb = $conn->Execute($sql);
    ?>
                        <select name="bank3[]" id="bank3[]" class="bank b3_<?php echo $j; ?>"
                            onchange="bank_select_index(this.value,'b4_<?php echo $j; ?>','<?php echo $j; ?>')"
                            style=" width:110px;">
                            <option value="">選擇銀行</option>
                            <?php
while (!$rsb->EOF) {
        echo '<option value="' . $rsb->fields["bBank3"] . '" ';
        echo (substr($rs->fields["tBankCode"], 0, 3) == $rsb->fields["bBank3"]) ? 'selected="selected"' : '';
        echo '>(' . $rsb->fields["bBank3"] . ')' . $rsb->fields["bBank4_name"] . '</option>' . "\n";
        $rsb->MoveNext();
    }
    ?>
                        </select>
                        <label for="bank4[]"><br />
                            *分行別</label>
                        <?php
$sql  = "SELECT * FROM tBank WHERE bBank4 <> '' AND bOK = 0 AND bBank3='" . substr($rs->fields["tBankCode"], 0, 3) . "' ORDER BY bBank3 ASC";
    $rsb2 = $conn->Execute($sql);
    ?>
                        <select name="bank4[]" id="bank4[]" style="width:110px;" class="bank b4_<?php echo $j; ?>"
                            onchange="bankphone(<?php echo $j; ?>,1)">
                            <?php
while (!$rsb2->EOF) {
        echo '<option value="' . $rsb2->fields["bBank4"] . '" ';
        echo (substr($rs->fields["tBankCode"], 3, 4) == $rsb2->fields["bBank4"]) ? 'selected="selected"' : '';
        echo '>(' . $rsb2->fields['bBank4'] . ')' . $rsb2->fields["bBank4_name"] . '</option>' . "\n";
        $rsb2->MoveNext();
    }
    ?>
                        </select><br />
                        <span id="bankp<?php echo $j; ?>" style="color:#FF0000;">
                            <?php
if ($rs->fields["tObjKind"] == "代清償") {
        $sql         = "SELECT bBank_area,bBank_tel FROM tBank WHERE bBank3 = '" . substr($rs->fields["tBankCode"], 0, 3) . "' AND bBank4 = '" . substr($rs->fields["tBankCode"], 3, 4) . "'";
        $bank_search = $conn->Execute($sql);

        echo "電話：" . $bank_search->fields['bBank_area'] . "-" . $bank_search->fields['bBank_tel'];
    }
    ?>
                        </span>
                    </td>
                    <td>*戶名
                        <label for="t_name[]"></label>
                        <input name="t_name[]" type="text" id="t_name<?php echo $j; ?>"
                            value="<?php echo n_to_w($rs->fields["tAccountName"]); ?>" size="14"
                            onblur="rel_words('t_name<?php echo $j; ?>')"
                            <?php if ($rs->fields["tKind"] == '賣方') {echo "onkeyup=\"checkowner('" . $j . "','" . $rs->fields['tMemo'] . "')\"";}?> />
                        <br />
                        證號
                        <label for="pid[]"></label>
                        <input name="pid[]" type="text" id="pid[]" size="10"
                            value="<?php echo $rs->fields["tAccountId"]; ?>" />
                        <br />
                        EMail
                        <label for="email[]"></label>
                        <input name="email[]" type="text" id="email[]" size="13"
                            value="<?php echo $rs->fields["tEmail"]; ?>" />
                        <br />
                        FAX
                        <label for="fax[]"></label>
                        <input name="fax[]" type="text" id="fax[]" size="15"
                            value="<?php echo $rs->fields["tFax"]; ?>" />
                    </td>
                    <td>*銀行帳號(14碼)<br />
                        <label for="t_account[]"></label>
                        <input name="t_account[]" class="bb_<?php echo $j; ?>" type="text" id="t_account[]" size="17"
                            value="<?php echo $rs->fields["tAccount"]; ?>" maxlength="14"
                            onkeyup="check03('<?php echo $j; ?>')" />
                    </td>
                    <td>*金額NT$<br />
                        <label for="t_money[]"></label>
                        <?php
$readonly = in_array($rs->fields["tObjKind"], ["扣繳稅款", "仲介服務費"]) ? 'readonly=readonly' : '';
if($readonly == '' and $rs->fields["tKind"] == '保證費') { $readonly = 'readonly=readonly'; };
    ?>
                        <input name="t_money[]" type="text" id="t_money[]" size="10"
                            value="<?php echo $rs->fields["tMoney"]; ?>" class="c<?=$rs->fields['tMemo']?>"
                            <?=$readonly?> />
                        元
                        <input name="t_cost[]" type="hidden" id="t_cost[]" value="0" />
                        <?php if ($rs->fields["tBank_kind"] == '台新' && $rs->fields["tObjKind2Date"] != ""): ?>
                        <br />繳稅日期
                        <input type="text" name="datepicker<?php echo $j; ?>" value="<?=$rs->fields["tObjKind2Date"]?>"
                            disabled="disabled" style="width:100px;" />
                        <?php endif?>
                    </td>
                    <td>*附言(勿按ENTER換行)<br />
                        <label for="t_cost[]"></label>
                        <label for="t_txt[]"></label>
                        <textarea name="t_txt[]" id="t_txt<?php echo $j; ?>" cols="20" rows="3"
                            onblur="rel_words('t_txt<?php echo $j; ?>')"><?php echo $rs->fields["tTxt"]; ?></textarea>
                    </td>

                    <td align="center">
                        不發送簡訊<br>
                        <input type="checkbox" name="tSend[]" id="" value="1"
                            <?php echo ($rs->fields["tSend"] == 1) ? 'checked' : ''; ?> />
                    </td>
                    <td>
                        <?php
if (in_array($rs->fields["tCode"], ["01", "02"]) && in_array($rs->fields["tBank_kind"], ['永豐', '台新']) && ($rs->fields["tKind"] == "地政士")) {
        ?>
                        <span id="Note<?=$j?>">存摺備註欄<br>(限聯行轉帳、跨行代清償且字數為六個字)</span>
                        <span id="bs<?=$j?>"><input type="text" id="bankshowtxt<?=$j?>" maxlength="6"
                                value="<?php echo $rs->fields["tBankShowTxt"]; ?>" onblur="setBankT(<?=$j?>)" />
                        </span>
                        <input type="hidden" name="bankshowtxt[]" id="bst<?=$j?>"
                            value="<?php echo $rs->fields["tBankShowTxt"]; ?>" />
                        <?php
} else {?>
                        <span id="Note<?=$j?>"></span>
                        <span id="bs<?=$j?>"></span>
                        <input type="hidden" name="bankshowtxt[]" id="bst<?=$j?>" />
                        <?php }
    ?>
                    </td>
                    <td>
                        官網代書用備註
                        <textarea name="scrivenerNote[]" id="" cols="20"
                            rows="5"><?php echo $rs->fields["tScrivenerNote"]; ?></textarea>
                    </td>
                    <td align="center" id="showb<?=$j?>">
                        <?php
if (($rs->fields["tCode2"] == '一銀內轉') || in_array($rs->fields["tCode"], ['05', '04'])) {
        echo '<input type="button" value="指示書" onclick="book(' . $rs->fields["tId"] . ')" />' . "\n";
    }
    ?>
                    </td>
                </tr>
                <tr>
                    <td height="19" colspan="10">
                        <hr />
                    </td>
                </tr>
            </form>
            <?php
$rs->MoveNext();
    $j++;
}

?>
        </table>
        <input type="button" name="button2" id="button2" value="批次列印"
            onclick="window.open ('/bank/report/export_select2.php', 'newwindow2', 'height=800, width=920, top=0, left=0, toolbar=no, menubar=no, scrollbars=yes, resizable=no,location=no, status=no')" />
    </div>
    <p>&nbsp;</p>
</body>

</html>