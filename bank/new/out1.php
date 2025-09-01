<?php
    header('Content-Type: text/html; charset=utf-8');
    require_once dirname(dirname(__DIR__)) . '/openadodb.php';
    require_once dirname(dirname(__DIR__)) . '/web_addr.php';
    require_once dirname(dirname(__DIR__)) . '/session_check.php';
    require_once dirname(dirname(__DIR__)) . '/class/checkFeedbackMoneyReview.php';

    $alert = '';

    $save = $_POST["save"] ?? '';

    $vr = empty($_POST['vr']) ? ($_GET["vr"] ?? '') : $_POST["vr"];

    $checkList = checkFeedbackMoneyReview($conn, substr($vr, -9));
    if (is_array($checkList) && ! empty($checkList)) {
        echo '<font color="red">審核後的回饋金與原回饋金資料不一致，請先確認資料。</font><br>';
    }

    if (! empty($_SESSION['alert'])) {
        $alert = 'alert("' . $_SESSION['alert'] . '");';

        $_SESSION['alert'] = null;unset($_SESSION['alert']);
    }

    $relatedCase = '';
    $tax_hint    = '';
    $tLegalAllow = '';
    if (preg_match("/^\d{14}$/", $vr)) {
        $sql = "SELECT c.cRelatedCase, c.cCaseHandler, c.cBankRelay, i.cCertifiedMoney
            FROM tContractCase AS c LEFT JOIN `tContractIncome` AS i ON c.cCertifiedId = i.cCertifiedId
            WHERE c.cEscrowBankAccount = '" . $vr . "'";
        $rs = $conn->Execute($sql);

        $relatedCase    = $rs->fields['cRelatedCase'];
        $bankRelay      = $rs->fields['cBankRelay'];      #代墊利息
        $certifiedMoney = $rs->fields['cCertifiedMoney']; #履保費

        //20241128新增法務案件判斷
        $tLegalAllow = (! empty($rs->fields['cCaseHandler']) && ($rs->fields['cCaseHandler'] == 1)) ? '<input type="hidden" name="tLegalAllow" value="1">' : '';
        if ($tLegalAllow) {
            $alert .= 'alert("此案件為法務案件，請留意出款！！");';
        }

        //確認是否有代扣所得稅或二代健保
        $sql = 'SELECT cTax, cNHITax, cInterest, bInterest  FROM tChecklist WHERE cCertifiedId = "' . substr($vr, -9) . '";';
        $rs  = $conn->Execute($sql);

        $_tax = [];
        if (! $rs->EOF) {
            if (! empty($rs->fields['cTax'])) {
                $_tax[] = '10%所得稅';
            }

            if (! empty($rs->fields['cNHITax'])) {
                $_tax[] = '2.11%二代健保';
            }
            $interest = $rs->fields['cInterest'] + $rs->fields['bInterest']; //利息
            if ($interest == $certifiedMoney) {
                echo '<font color="red">此案利息金額等於履保費，請提醒審核人員(佩琦、雅雯)需出款回饋金。</font><br>';
            }
        }

        if (! empty($_tax)) {
            $tax_hint = implode('跟', $_tax);
            $tax_hint = '<span style="margin-left: 10px;color: red;font-size: 10pt;font-weight:bold;">（此案件有' . $tax_hint . '，請留意出款！！）</span>';
        }
        $_tax = null;unset($_tax);
        ##
    }
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>出帳建檔作業 v2.01</title>
    <link type="text/css" href="css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
    <script type="text/javascript" src='codebase/message.js'></script>
    <link rel="stylesheet" type="text/css" href="codebase/themes/message_default.css">
    <style>
    .tb th {
        background-color: #E4BEB1;
    }

    .tb td {
        background-color: #F8ECE9;
        padding: 5px;
    }

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
    }

    .ui-combobox-input {
        margin: 0;
        padding: 0.1em;
    }

    .ui-autocomplete {
        width: 150px;
        max-height: 150px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        /* add padding to account for vertical scrollbar */
        padding-right: 20px;
    }

    /* IE 6 doesn't support max-height
    * we use height instead, but this forces the menu to always be this tall
    */
    * html .ui-autocomplete {
        height: 150px;
    }
    </style>
    <script>
    <?php echo $alert?>

    $(function() {

    });

    function showSms() {
        var radiokind = $("[name='radiokind']:checked").val();
        var vr = $("[name='vr']").html();
        var smsSend = $("[name='smsSend']:checked").val();

        if (radiokind == '' || radiokind == undefined) {
            alert("請選擇出款項目");
            return false;
        }

        if (smsSend == 2) {
            $.ajax({
                    url: 'getBankTranSms.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        radiokind: radiokind,
                        vr: <?php echo $vr?>
                    },
                })
                .done(function(msg) {
                    $("#smsShow").html(msg);
                });
        } else {
            $("#smsShow").html('');
        }
    }

    function checkALL() {
        var all = $('[name="all"]').prop('checked');

        if (all == true) {
            $('[name="allForm[]"]').prop('checked', true);
        } else {
            $('[name="allForm[]"]').prop('checked', false);
        }
    }

    function checkSms() {
        var smsSend = $("[name='smsSend']:checked").val();
        var checkCount = 0;
        if (smsSend == 2) {
            $("[name='allForm[]']").each(function() {
                if ($(this).prop('checked')) {
                    checkCount++;
                }
            });

            if (checkCount == 0) {
                alert("請選擇寄送對象");
                return false;
            }
        }

        $('[name="form1"]').submit();
    }

    function noSms() {
        $('#sms_no').click();
    }
    </script>
</head>

<body>
    <div style="width:1290px; margin-bottom:5px; height:22px; background-color: #CCC">

        <div style="float:left;margin-left: 10px;"> <a href="<?php echo $web_addr?>/bank/list2.php">待修改資料</a> </div>
        <?php
            if ($_SESSION["member_bankcheck"] == '1') { //個別權限顯示
            ?>
        <div style="float:left; margin-left: 10px;"> <a href="<?php echo $web_addr?>/bank/list.php">未審核列表</a></div>
        <?php
        }?>
    </div>
    <form id="form1" name="form1" method="post" action="out2.php">
        <?php echo $tLegalAllow?>

        <table width="682" border="0">
            <tr>
                <td colspan="4">專屬帳號:
                    <input type="text" value="<?php echo $vr?>" disabled="disabled">
                    <input type="hidden" name="vr_code" value="<?php echo $vr?>">
                    <input type="hidden" name="saveX" id="saveX" value="ok" />
                    <input type="hidden" name="bankRelay" value="<?php echo $bankRelay?>">
                    <?php if ($relatedCase): ?>
                    <span style="color:red;border:1px solid #CCC;">連件:<?php echo $relatedCase?></span>
                    <?php endif?>
<?php echo $tax_hint ?>
                </td>
            </tr>
            <tr>
                <td colspan="4">項目選擇: </td>
            </tr>
            <tr>
                <td width="77">&nbsp;</td>
                <td width="264"><input type="radio" name="radiokind" id="radio" value="點交" onclick="showSms()" />
                    <label for="radiokind"></label>
                    <label for="objKind[]">點交</label>
                </td>
                <td width="77">&nbsp;</td>
                <td width="264">
                    <input type="radio" name="radiokind" id="radio9" value="賣方仲介服務費" onclick="showSms()" />賣方仲介服務費
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="radio" name="radiokind" id="radio2" value="賣方先動撥" onclick="showSms()" />
                    賣方先動撥</td>
                <td>&nbsp;</td>
                <td><input type="radio" name="radiokind" id="radio4" value="買方仲介服務費" onclick="showSms()" />買方仲介服務費</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="radio" name="radiokind" id="radio3" value="扣繳稅款" onclick="showSms()" />
                    扣繳稅款</td>
                <td>&nbsp;</td>
                <td><input type="radio" name="radiokind" id="radio11" value="代墊利息" onclick="noSms()" />代墊利息</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="radio" name="radiokind" id="radio5" value="代清償" onclick="showSms()" />
                    代清償</td>
                <td>&nbsp;</td>
                <td><input type="radio" name="radiokind" id="radio12" value="履保費先收(結案回饋)" onclick="noSms()" />履保費先收(結案回饋)</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="radio" name="radiokind" id="radio10" value="調帳" onclick="showSms()" />
                    調帳</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="radio" name="radiokind" id="radio6" value="解除契約" onclick="showSms()" />
                    解約/終止履保</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="radio" name="radiokind" id="radio7" value="保留款撥付" onclick="showSms()" />
                    保留款撥付</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="radio" name="radiokind" id="radio8" value="建經發函終止" onclick="showSms()" />
                    建經發函終止</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="radio" name="radiokind" id="radio9" value="預售屋" onclick="showSms()" />預售屋</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4">簡訊發送：</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3"><input type="radio" name="smsSend" id="sms_default" value="0" checked=""
                        onclick="showSms()" />預設
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">
                    <input type="radio" name="smsSend" id="sms_select" value="2" onclick="showSms()" />自選對象
                    <span id="smsShow">

                    </span>
                </td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td colspan="3"><input type="radio" name="smsSend" id="sms_no" value="1" onclick="showSms()" />全不寄送</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><input type="button" name="button" id="button" value="下一步" onclick="checkSms()" />
                </td>
            </tr>
        </table>
    </form>
    <script type="text/javascript">
    <?php if (($_REQUEST["ok"] ?? '') == "1") {?>
    dhtmlx.alert({
        type: "alert-error",
        text: "新增成功"
    });
    <?php }?>
    </script>
</body>

</html>