<?php
    error_reporting(E_ALL & ~E_WARNING);

    require_once dirname(dirname(__DIR__)) . '/session_check.php';
    require_once dirname(dirname(__DIR__)) . '/openadodb.php';

    if ($_SESSION["member_pDep"] == 5 && $_SESSION["member_id"] != 1) {
        $str = " AND tOwner ='" . $_SESSION['member_name'] . "'";
    }

    $CertifiedId = [];

    //20241129 過濾法務關注案件
    $sql = 'SELECT tCode2, tMemo, tObjKind, tBank_kind FROM tBankTrans WHERE tOk="2" AND tLegalAllow <> "1" ' . $str . ' GROUP BY tVR_Code, tObjKind ORDER BY tVR_Code ASC;';
    $rs  = $conn->Execute($sql);
    $i   = 0;
    while (! $rs->EOF) {
        $kind = (in_array($rs->fields['tCode2'], ['大額繳稅', '臨櫃開票'])) ? '【' . $rs->fields['tCode2'] . '】' : '';

        $CertifiedId[$i]['text2'] = '';
        $CertifiedId[$i]['value'] = $rs->fields['tMemo'] . "_" . $rs->fields['tObjKind'];
        $CertifiedId[$i]['text']  = '0' . $rs->fields['tBank_kind'] . $rs->fields['tObjKind'] . $kind . "_" . $rs->fields['tMemo']; //
        if ($rs->fields['tObjKind'] == '賣方先動撥') {
            $CertifiedId[$i]['text2']       = "<a href='../../escrow/bankTransConfirmCall.php?action=banktrans&cid=" . $rs->fields['tMemo'] . "' class='iframe' style='font-size:9pt;'>(照會)</a>";
            $callFlag[$rs->fields['tMemo']] = checkConfirmCall($conn, $rs->fields['tMemo']);
        }
        $CertifiedId[$i]['certifiedId'] = $rs->fields['tMemo'];

        $i++;

        $rs->moveNext();
    }

    //確認出款照會規則
    function checkConfirmCall($conn, $id)
    {
        $output['flag']  = false; //預設需要確認照會
        $output['msg']   = [];
        $output['flag2'] = true;
        $output['msg2']  = '';
        $priceCondition  = 1500000; //總金額超過150就需要確認照會資料
        $priceBatch      = 0;
        $tBankTransIds   = [];

        // 此案件賣方
        $owners     = [];
        $sql_owners = "SELECT cName AS name FROM tContractOwner WHERE cCertifiedId = '" . $id . "'";
        $sql_owners .= " UNION ALL SELECT cName AS name FROM tContractOthers WHERE cCertifiedId = '" . $id . "' AND cIdentity = '2'";
        $rs_owners = $conn->Execute($sql_owners);
        while (! $rs_owners->EOF) {
            $owners[] = trim($rs_owners->fields['name']);
            $rs_owners->moveNext();
        }

        $ownerFlag      = false;
        $ownerOtherFlag = false;
        $checkOwners    = $owners;
        $sql_check      = "SELECT tId, tMoney, tAccountName FROM tBankTrans WHERE tMemo='" . $id . "' AND tOk='2' AND tLegalAllow <> '1' AND tObjKind ='賣方先動撥'";
        $rs_check       = $conn->Execute($sql_check);
        if ($rs_check && $rs_check->RecordCount() > 0) {
            $ownerFlag = true;
        }

        while (! $rs_check->EOF) {
            $tBankTransIds[] = $rs_check->fields['tId'];
            $priceBatch += $rs_check->fields['tMoney'];

            // 只要出款帳戶有一個是非此案賣方就不通過
            if (! in_array(trim($rs_check->fields['tAccountName']), $owners)) {
                if ($ownerFlag) {
                    $ownerFlag      = false;
                    $ownerOtherFlag = true;
                }
            }

            //比對每個賣方
            $checkOwners = array_diff($checkOwners, [$rs_check->fields['tAccountName']]);
            $rs_check->moveNext();
        }

        if ($ownerOtherFlag) {
            $output['flag']  = false;
            $output['msg'][] = '出款非賣方帳戶';
        } else if (! empty($checkOwners)) {
            $output['flag']  = false;
            $output['msg'][] = '出款未包含所有賣方';
        } else if ($ownerFlag) {
            $output['flag'] = true;
        } else {
            $output['flag']  = false;
            $output['msg'][] = '...';
        }

        //超過規定金額就需要照會
        if ($priceBatch >= $priceCondition) {
            $output['flag']  = false;
            $output['msg'][] = '出款總額超過' . $priceCondition . '元';
        }

        if (! $output['flag']) {
            $kindCalls[1]    = $kindCalls[2]    = 0;
            $output['flag2'] = false; //未過照會規則
            $output['msg2']  = '需照會買賣雙方';

            $sql_call = "SELECT bKind FROM tBankTransConfirmCall WHERE bBankTransId in (" . implode(",", $tBankTransIds) . ") AND bDeletedAt is null";
            $rs_call  = $conn->Execute($sql_call);
            while (! $rs_call->EOF) {
                $kindCalls[$rs_call->fields['bKind']]++;
                $rs_call->moveNext();
            }

                                     // 判斷是否填寫買賣雙方照會資料
            if ($kindCalls[3] > 0) { //只要有副總確認就直接過
                $output['flag2'] = true;
                $output['msg2']  = '';
            } else if ($kindCalls[1] > 0 && $kindCalls[2] > 0) {
                $output['flag2'] = true;
                $output['msg2']  = '';
            }
        }

        if ($rs_owners) {$rs_owners->Close();}
        if ($rs_check) {$rs_check->Close();}
        if ($rs_call) {$rs_call->Close();}

        return $output;
    }

    $sendFlag  = true;
    $sendMsg   = "";
    $callArray = [];

    foreach ($callFlag as $k => $v) {
        if (! $v['flag'] && ! $v['flag2']) {
            $sendFlag = false;
            $sendMsg .= $k . "：" . implode('、', $v['msg']) . "<br>";
            $callArray[] = (string) $k;
        }
    }
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">

    <title>出款確認單</title>
    <!--    <script type="text/javascript" src="../../js/jqueryery-1.12.4.min.js"></script>-->
    <!--    <script type="text/javascript" src="../../libs/jquery.colorbox-min.js"></script>-->
    <link rel="stylesheet" href="../../css/colorbox.css" />
    <script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="../../js/jquery.colorbox.js"></script>
    <script type="text/javascript">
        var callArray =                        <?php echo json_encode($callArray); ?>;

    $(document).ready(function() {
        $('body').on('click', '.accoont', function(e) {
            var currentAccount = e.currentTarget.value;
            var form2Element = '<input type="hidden" name="account[]" value="' + currentAccount + '">';
            $("#form2").append(form2Element);
        });

        $('body').on('click', '#CertifiedId110050011, #CertifiedId110019411', function(e) {
            if ($('#CertifiedId110050011 , #CertifiedId110019411').is(':checked')) {
                $("#chose_certified_id").empty();
                $("#form2").empty();

                var currentCertified = e.currentTarget.value;
                var form2Element = '<input type="hidden" name="CertifiedId[]" value=" ' +
                    currentCertified + ' ">';
                $("#form2").append(form2Element);

                //其他帳號鎖定
                $('#show input').each(function(index) {
                    if (e.currentTarget.id != (this.id)) {
                        $(this).prop('checked', false);
                        $(this).attr('disabled', true);
                    }
                });
                //找出所有代墊利息項目
                const Interest = ["CertifiedId110050011", "CertifiedId110019411"];
                url = '/bank/report/_interest_acc.php?sn=' + e.currentTarget.id;
                $.ajax({
                    url: url,
                    error: function(xhr) {
                        alert("請稍後再試！");
                    },
                    success: function(response) {
                        const obj = JSON.parse(response);
                        if (obj.status) {
                            var elementInput = '<ul id = "certified_list">';
                            $("#chose_certified_id").prepend(elementInput);
                            $.each(obj.account, function(key, value) {
                                elementInput =
                                    '<li><input type="checkbox" name="account[]" class="accoont" value="' +
                                    value + '" >' + value + '</li>';

                                $("#certified_list").append(elementInput);
                            });
                            elementInput = '</ul>';
                            $("#chose_certified_id").append(elementInput);

                            elementInput =
                                '<input type="button" name="button2" value="送出" class="xxx-button" onclick="sned2()" style="position:absolute; bottom:0px; left: 50%;" />';
                            $("#chose_certified_id").append(elementInput);
                            $.colorbox({
                                inline: true,
                                href: "#chose_certified_id",
                                width: "60%",
                                height: "60%",
                                onClosed: function() {
                                    //send();
                                }
                            });
                        }
                    }
                });
            } else {
                $('#show input').each(function(index) {
                    $(this).attr('disabled', false);
                });
            }
        });

        $(".iframe").colorbox({iframe:true, width:"90%", height:"90%",onClosed:function(){
                // getinv();
            }}) ;
    });

    function showCertified() {
        let input = $('input');
        let arr_input = new Array();
        let reg = /.*\[]$/;

        $.each(input, function(key, item) {
            if (reg.test($(item).attr("name"))) {
                if ($(item).is(':checkbox')) {
                    if ($(item).is(':checked')) {
                        if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                            arr_input[$(item).attr("name")] = new Array();
                        }
                        arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                    }
                } else {
                    if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                        arr_input[$(item).attr("name")] = new Array();
                    }
                    arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                }
            } else {
                arr_input[$(item).attr("name")] = $(item).attr("value");
            }
        });

        arr_input['id'] = $("[name='search']").val();
        let obj_input = $.extend({}, arr_input);

        $.ajax({
                url: 'export_select_Search.php',
                type: 'POST',
                dataType: 'html',
                data: obj_input,
            })
            .done(function(msg) {
                $("#show").html(msg);
            });
    }

    function send() {
        let input = $('input');
        let arr_input = new Array();
        let reg = /.*\[]$/;
        let call_flag = true;
        let checked_counter = 0;

        $.each(input, function(key, item) {
            if (reg.test($(item).attr("name"))) {
                if ($(item).is(':checkbox')) {
                    if ($(item).is(':checked')) {
                        if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                            arr_input[$(item).attr("name")] = new Array();
                        }
                        arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        //判斷是否要填寫照會資料
                        let cid = $(item).val().split('_')[0];
                        if (callArray.includes(cid)) {
                            alert(cid + " 需填寫照會資料！");
                            call_flag = false;
                        }
                        checked_counter++;
                    }
                } else {
                    if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                        arr_input[$(item).attr("name")] = new Array();
                    }
                    arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                }
            } else {
                arr_input[$(item).attr("name")] = $(item).attr("value");
            }
        });

        if (checked_counter === 0) {
            call_flag = false;
        }

        arr_input['cat'] = 'msg';
        let obj_input = $.extend({}, arr_input);

        if(call_flag){
            $.ajax({
                url: 'export_list_all_result.php',
                type: 'POST',
                dataType: 'html',
                data: obj_input
            })
                .done(function(msg) {
                    if (msg) {
                        alert(msg);
                    }

                    $("#form1").submit();
                });
        }
    }

    function sned2() {
        $("#form2").submit();
    }


    </script>
    <style type="text/css">
    body {
        font-family: "微軟正黑體", "Microsoft JhengHei", "黑體-繁", "Heiti TC", "華文黑體", "STHeiti", "儷黑 Pro", "LiHei Pro Medium", "新細明體", "PMingLiU", "細明體", "MingLiU", "serif";
        line-height: normal;
        font-size: 100%;
    }

    .cb1 {
        padding: 0px 0px;
    }

    .cb1 input[type="checkbox"] {
        position: absolute;
        left: -9999px;
    }

    .cb1 input[type="checkbox"]+label span {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin: -3px 4px 0 0;
        vertical-align: middle;
        background: url("images/check_radio_sheet2.png") left top no-repeat;
        cursor: pointer;
        background-size: 80px 20px;
        transition: none;
        -webkit-transition: none;
    }

    .cb1 input[type="checkbox"]:checked+label span {
        background: url("images/check_radio_sheet2.png") -20px top no-repeat;
        background-size: 80px 20px;
        transition: none;
        -webkit-transition: none;
    }

    .cb1 label {
        cursor: pointer;
        display: inline-block;
        margin-right: 10px;
        /*-webkit-appearance: push-button;
    -moz-appearance: button;*/
    }

    /*button*/
    .xxx-button {
        color: #FFFFFF;
        font-size: 16px;
        font-weight: normal;
        background-color: #a63c38;
        text-align: center;
        white-space: nowrap;
        height: 34px;
        padding: 0 10px;
        border: 1px solid #a63c38;
        border-radius: 0.35em;
    }

    .xxx-button:hover {
        background-color: #333333;
        border: 1px solid #333333;
    }

    /*input*/
    .xxx-input {
        color: #666666;
        font-size: 16px;
        font-weight: normal;
        background-color: #FFFFFF;
        text-align: left;
        height: 34px;
        padding: 0 5px;
        border: 1px solid #CCCCCC;
        border-radius: 0.35em;
    }

    .xxx-input:focus {
        border-color: rgba(82, 168, 236, 0.8) !important;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
        outline: 0 none;
    }
    </style>
</head>

<body>
    <center>
        保證號碼查詢：<input type="text" name="search" onkeyup="showCertified()" class="xxx-input"
            placeholder="保證號碼查詢" /><br />
        <div style="border:1px #CCC solid;width: 50% ;margin-top: 50px;padding:5px;">
            請勾選 專屬帳號:
            <form id="form1" name="form1" method="post" action="export_list_all_result.php">
                <div id="show">
                    <?php foreach ($CertifiedId as $value): ?>
                    <div style="padding: 5px; text-align: left;margin-left: 30%">
                        <span class=""><input type="checkbox" name="CertifiedId[]" value="<?php echo $value['value']?>"
                                id="CertifiedId<?php echo $value['certifiedId']?>"><label
                                for="CertifiedId<?php echo $value['certifiedId']?>"><span></span><?php echo $value['text']?></label><?php echo $value['text2']?></span>
                    </div>
                    <?php endforeach?>
                </div>
                <div style='display:none'>
                    <div id="chose_certified_id" style="height: 90%; position:relative;">
                    </div>

                </div>

                <input type="button" name="button" id="button" value="送出" class="xxx-button" onclick="send()" />
                <?php if (! $sendFlag) {?>
                    <div style="text-align: left; padding: 15px; color: red;">
                        <b>【需填寫買賣雙方照會紀錄】</b><br>
                        <?php echo $sendMsg; ?>
                    </div>
                <?php }?>
            </form>
        </div>
    </center>
    <div style="display: none;">
        <form id="form2" name="form2" method="post" action="export_list_all_result.php">

        </form>
    </div>
</body>

</html>