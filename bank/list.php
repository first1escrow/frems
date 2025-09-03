<?php
    error_reporting(E_ALL & ~E_WARNING);
    require_once dirname(__DIR__) . '/openadodb.php';
    require_once dirname(__DIR__) . '/session_check.php';
    require_once dirname(__DIR__) . '/tracelog.php';
    require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';

    use First1\V1\PayByCase\PayByCase;

    function getCase($certifyId, $objKind)
    {
        global $conn;

        $sql             = 'SELECT `cCaseMoney`, `cBankRelay` FROM `tContractCase` WHERE `cEscrowBankAccount` = "' . $certifyId . '";';
        $rs              = $conn->Execute($sql);
        $total           = $rs->fields['cCaseMoney'];
        $bankRelay       = $rs->fields['cBankRelay'];
        $invoiceFinished = 'Y';
        if ($objKind == '履保費先收(結案回饋)') {
            $sql = 'SELECT SUM(cInvoiceBuyer + cInvoiceOwner + cInvoiceRealestate + cInvoiceScrivener + cInvoiceOther) as invoiceTotal , I.cCertifiedMoney
                FROM tContractInvoice as V LEFT JOIN tContractIncome as I ON V.cCertifiedId = I.cCertifiedId  WHERE V.cCertifiedId = "' . substr($certifyId, -9) . '";';
            $res = $conn->Execute($sql);

            $invoiceFinished = $res->fields['invoiceTotal'] == $res->fields['cCertifiedMoney'] ? 'Y' : 'N';
        }
        $res = [
            'total'           => $total,
            'bankRelay'       => $bankRelay,
            'invoiceFinished' => $invoiceFinished,
        ];
        return $res;
    }

    //驗證出款金額是否足夠
    function checkCaseMoneyBalance($total, $money)
    {
        $total -= $money;

        return ($total >= 0) ? '' : '<span style="color:red;font-weight:bold;" title="餘額不足">(' . $total . ')</span>';
    }
    ##
    function checkCaseMoneyPayOkBalance($caseMoneyTotal, $payOkTotal, $isInvoice, $certifyId)
    {
        global $conn;
        global $paybycase;

        $total = $caseMoneyTotal - $payOkTotal;
        if ($isInvoice === null) {

            //之前是否開過發票
            $sql = 'SELECT tVR_Code FROM `tBankTrans` WHERE tInvoice IS NOT NULL AND tOk = 1 AND `tVR_Code` = "' . $certifyId . '";';
            $rs  = $conn->Execute($sql);
            if ($rs->RecordCount() <= 0) {
                return ' <span style="color:red;font-weight:bold;" title="未選結案">(' . $total . ')</span> <input type="button" value="結案" id="i_' . $certifyId . '"  onclick="invoice(' . $certifyId . ')">';
            }
        }
        return '';
    }

    //找出所有申請代墊利息的專屬帳號
    function bankRelayVRCode()
    {
        global $conn;

        $sql     = 'SELECT tAccount FROM `tBankTrans` WHERE tVR_Code = 55006110050011;';
        $rs      = $conn->Execute($sql);
        $VRCodes = [];
        while (! $rs->EOF) {

            $VRCodes[] = $rs->fields['tAccount'];
            $rs->MoveNext();
        }

        return $VRCodes;
    }
    $VRCodes = bankRelayVRCode(); //有代墊利息的帳戶

    $tlog = new TraceLog();
    $tlog->selectWrite($_SESSION['member_id'], ' ', '查看出款未審核列表');
    $paybycase = new PayByCase;

    $_GET = escapeStr($_GET);

    if (isset($_GET['cat']) && $_GET['cat'] == 1) {
        $btnClass  = "";
        $btnClass1 = "focus_end";
    } else {
        $btnClass  = "focus_end";
        $btnClass1 = "";
    }

    if ($_SESSION["member_bankcheck"] != '1') {
        echo '
	<script>
	alert("您無此功能使用權限!!") ;
	</script>
	';

        exit;
    }

    //合約銀行資料
    $sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cOrder ASC;';
    $rs  = $conn->Execute($sql);
    while (! $rs->EOF) {
        $conBank[] = $rs->fields;
        $rs->MoveNext();
    }
    unset($rs);
    ##
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>出帳建檔列表</title>
    <link rel="stylesheet" href="../css/colorbox.css" />
    <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
    <script src="../js/jquery.colorbox.js"></script>

    <script>
    $(document).ready(function() {
        var bankcheck = "<?php echo $_SESSION['member_bankcheck'] ?>";
        var member_id = "<?php echo $_SESSION['member_id'] ?>";
        if (bankcheck == 1 && member_id == 1) {
            SmsRemind();
            setInterval("SmsRemind()", 60000); //出款簡訊提醒
        }

        $(".iframe").colorbox({
            iframe: true,
            width: "1100",
            height: "500",
            onClosed: function() {
                reload_page();
            }
        });
    });

    function SmsRemind() {
        $.ajax({
                url: '/includes/sms_remind.php',
                type: 'POST',
                dataType: 'html',
            })
            .done(function(txt) {
                //
                if (txt != '') {
                    alert(txt);
                }
            });
    }

    function invoice(VR_Code) {
        $.ajax({
            url: '/includes/invoice.php',
            type: 'POST',
            dataType: 'html',
            data: {"vr_code": VR_Code},
        })
        .done(function(txt) {
            if (txt != '') {

                alert(txt);
            }
        });
        $("#i_" + VR_Code).hide();
    }

    function open_w(url) {
        window.open(url, 'newwindow',
            'height=500, width=1100, top=250, left=250, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no'
        )
    }

    function reload_page() {
        location.reload();
    }

    function showChild(id) {
        var cls = $(".c" + id).attr('class');
        var arr = new Array();
        arr = cls.split(' ');

        //
        if (arr[1] == 'cClose') { //child cClose c99986006113464
            $(".c" + id).attr('class', cls.replace("cClose", "cOpen"));
            $("[name='s" + id + "']").val("縮合");

        } else {
            $(".c" + id).attr('class', cls.replace("cOpen", "cClose"));
            $("[name='s" + id + "']").val("展開");
        }
    }

    function checkALL(id) {
        var ck = $("[name='a" + id + "']").prop('checked');

        $(".c" + id + " [name='check[]']").each(function() {
            if (ck == true) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);

            }
        });
    }

    function auth() {
        var input = $('input');
        var arr_input = new Array();
        var reg = /.*\[]$/;

        $.each(input, function(key, item) {
            if ($(item).is(':checkbox')) {
                if ($(item).is(':checked')) {
                    if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                        arr_input[$(item).attr("name")] = new Array();
                    }
                    arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                }
            } else if ($(item).is(':radio')) {
                if ($(item).is(':checked')) {
                    arr_input[$(item).attr("name")] = $(item).val();
                }
            } else {
                if (reg.test($(item).attr("name"))) {
                    if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                        arr_input[$(item).attr("name")] = new Array();
                    }

                    arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                } else {
                    arr_input[$(item).attr("name")] = $(item).attr("value");
                }
            }
        });

        var obj_input = $.extend({}, arr_input);

        var request = $.ajax({
            url: "check_out3.php",
            type: "POST",
            data: obj_input,
            dataType: "html"
        });
        request.done(function(msg) {
            alert(msg);
            reload_page();
        });
    }

    function link(cat) {
        $(".btn").prop('class', 'btn');

        if (cat == 1) {
            location.href = "list.php?cat=1";
            $("#btn" + cat).prop('class', "btn focus_end");
        } else {
            location.href = "list.php";
            $("#btn" + cat).prop('class', "btn focus_end");
        }
    }
    </script>
    <style>
    .both {
        background-color: #FFC863;
        width: 1016px;
    }

    .btn {
        color: #B80000;
        font-family: Verdana;
        font-size: 14px;
        font-weight: bold;
        line-height: 14px;
        background-color: #fff;
        text-align: center;
        display: inline-block;
        padding: 8px 12px;
        border: 1px solid #DDDDDD;
        /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
    }

    .btn:hover {
        color: #fff;
        font-size: 14px;
        background-color: #B80000;
        border: 1px solid #000;
    }

    .btn.focus_end {
        color: #fff;
        font-family: Verdana;
        font-size: 14px;
        font-weight: bold;
        line-height: 14px;
        background-color: #B80000;
        text-align: center;
        display: inline-block;
        padding: 8px 12px;
        border: 1px solid #fff;
        /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
    }

    .owner {
        background-color: #FFE8E8;
        width: 1016px;
    }

    .main {
        width: 1016px;
    }

    .exp {
        width: 1016px;
        /*border:1px solid #999;*/
    }

    .child {
        padding-left: 50px;
        width: 1016px;
        font-size: 10px;

    }

    .child table {
        padding: 5px;
        margin-bottom: 5px;
        background-color: #E8FFE8;
        /*border: 1px solid #999;*/
    }

    .cvalue {
        /*background-color:#FFF;*/
        padding-left: 1px;
    }

    .cClose {
        display: none;
    }

    .cOpen {
        display: '';
    }

    .xxx-button {
        color: #FFFFFF;
        font-size: 12px;
        font-weight: normal;

        text-align: center;
        white-space: nowrap;
        height: 20px;

        background-color: #a63c38;
        border: 1px solid #a63c38;
        border-radius: 0.35em;
        font-weight: bold;
        padding: 0 20px;
        margin: 5px auto 5px auto;
    }

    .xxx-button:hover {
        background-color: #333333;
        border: 1px solid #333333;
    }
    </style>
</head>

<body>
    <div style="width:1024px; margin-bottom:5px; height:22px; background-color: #CCC">
        <div style="float:left;margin-left: 10px;"> <a href="instructions/IBookList.php">指示書</a> </div>
        <div style="float:left;margin-left: 10px;"> <a href="/bank/list2.php">待修改資料</a> </div>
        <?php
            if ($_SESSION["member_id"] == '6' || in_array($_SESSION["member_pDep"], [5, 6])) { //個別權限顯示
            ?>
        <div style="float:left;margin-left: 10px;"> <a href="/bank/BankTransProcess.php">出款進度</a> </div>
        <?php
        }?>
<?php
    if ($_SESSION["member_bankcheck"] == '1') { //個別權限顯示
    ?>
        <div style="float:left; margin-left: 10px;">
            <font color=red><strong>未審核列表</strong></font>
        </div>
        <div style="float:left; margin-left: 10px;"> <a href="/bank/list_ok.php">已審核列表</a></div>
        <div style="float:left; margin-left: 10px;"> <a href="/bank/bankRelay.php">中繼帳戶出款</a></div>
        <?php
        }?>
    </div>
    <div>
        <div>待出帳(審核)列表</div>

        <div>
            <input type="button" value="經辦" id="btn0" onclick="link(0)" class="btn                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 <?php echo $btnClass ?>">
            <input type="button" value="出款項目" id="btn1" onclick="link(1)" class="btn                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 <?php echo $btnClass1 ?>">
        </div>
        <?php

            for ($i = 0; $i < count($conBank); $i++) {
                $data = [];
                if ($_GET['cat'] == 1) { //以出款項目為主
                    $data2 = [];             //要放在後面的
                    $sql   = 'select * from tBankTrans where tOK<>"1" AND tVR_Code LIKE "' . $conBank[$i]['cBankVR'] . '%" ORDER BY tObjKind,tOwner ASC';
                    $rs    = $conn->Execute($sql);

                    while (! $rs->EOF) {
                        //20240618 Project S只有特定人員可以查看
                        if (! in_array($_SESSION['member_id'], [1, 3, 6, 12, 13, 36, 84, 90]) && ($rs->fields["tMemo"] == '130119712')) {
                            continue;
                        }

                        if (empty($data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['tObjKind'])) { //紀錄類別(只取第一筆)
                            $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['tObjKind'] = $rs->fields['tObjKind'];
                        }
                        $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['tOwner']   = $rs->fields['tOwner']; //tOwner
                        $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['tVR_Code'] = $rs->fields["tVR_Code"];
                        $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['Total'] += $rs->fields["tMoney"];
                        $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['count']++;
                        $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['tDate']    = $rs->fields["tDate"];
                        $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['tVR_Code'] = $rs->fields["tVR_Code"];
                        $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['objKindCount'][$rs->fields["tObjKind"]]++; //計算是否有不同項目出款
                        $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['tLegalAllow'] = $rs->fields["tLegalAllow"];
                        $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['data'][]      = $rs->fields;
                        if ($rs->fields['tInvoice'] != null) {
                            $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['isInvoice']++;
                        }
                        if (in_array($rs->fields['tObjKind'], ['點交(結案)', '解除契約', '建經發函終止', '預售屋'])) {
                            $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['close']++;
                        }
                        if ($rs->fields["tPayOk"] == '2') {
                            $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['payOkTotal'] += $rs->fields["tMoney"];
                        }

                        if (empty($data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['legalAllow'])) {
                            $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['legalAllow'] = $rs->fields["tLegalAllow"];
                        }
                        $data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['legalAllow'] = ($data[$conBank[$i]['cBankVR']][$rs->fields["tVR_Code"]]['legalAllow'] == '1') ? '1' : $rs->fields["tLegalAllow"];

                        $rs->MoveNext();
                    }

                    $bank = $conBank[$i]['cBankFullName'] . '(' . $conBank[$i]['cBranchName'] . ')';

                    echo "<div style=\"font-weight:bold;color:#002060;\">～～～&nbsp;" . $bank . "案件&nbsp;～～～</div>";

                    if (is_array($data[$conBank[$i]['cBankVR']])) {
                        $checkKind = '';

                        foreach ($data[$conBank[$i]['cBankVR']] as $v) {
                            if (count($v['objKindCount']) > 1) {
                                array_push($data2, $v);
                                continue;
                            }

                            if ($v['tObjKind'] != $checkKind) {
                                $checkKind = $v['tObjKind'];
                                echo "<div class=\"owner\">" . $v['tObjKind'] . "</div>";
                            }

                            //20241129 法務關注案件未同意前不可出款
                            $legalAllow  = (! empty($v['legalAllow']) && ($v['legalAllow'] == '1')) ? ' disabled' : '';
                            $legalNotice = (! empty($v['legalAllow']) && ($v['legalAllow'] == '1')) ? '<span style="color:red;font-weight:bold;font-size:10pt;"> (列管)</span>' : '';

                            echo "<div class=\"exp\">";
                            echo "<div class=\"main\">";
                            echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" class=\"font12\"><tr>";
                            echo "<td width=\"258\">";
                            $tVR_Code = isset($v["tVR_Code"]) ? $v["tVR_Code"] : '';
                            echo "<input type=\"checkbox\" name=\"a" . $tVR_Code . "\"  onclick=\"checkALL('" . $tVR_Code . "')\" " . $legalAllow . ">
										專屬帳號 <font color=\"blue\"><strong>" . $tVR_Code . "</strong></font>" . $legalNotice;
                            if ($tVR_Code !== '' && preg_match("/96988000000008/", $tVR_Code)) {
                                echo '(利息)'; //台新利息
                            } else if ($tVR_Code !== '' && preg_match("/99985000000000/", $tVR_Code)) {
                                echo '(利息)'; //永豐西門利息
                            } else if ($tVR_Code !== '' && preg_match("/99986000000000/", $tVR_Code)) {
                                echo '(利息)'; //永豐城中利息
                            }
                            echo "</td>";
                            $contractCase = getCase($v["tVR_Code"], $v["tObjKind"]);

                            $_balance = checkCaseMoneyBalance($contractCase['total'], $v["Total"]);

                            $certifiedMoneyArrived = '';
                            if ($contractCase['bankRelay'] == 'C' and $v['close'] > 0) {
                                $certifiedMoneyArrived = '<span style="color:red;font-weight:bold;" title="履保費已先扣款">履保費已先扣款</span>';
                            }
                            //發票分配未完成
                            $invoiceFinished = '';
                            if ($contractCase['invoiceFinished'] == 'N') {
                                $invoiceFinished = '<span style="color:red;font-weight:bold;" title="發票分配未完成">發票分配未完成</span>';
                            }

                            echo "<td width=\"251\">待出帳總金額 " . $v["Total"] . " 元{$_balance}{$certifiedMoneyArrived}{$invoiceFinished}</td>";

                            $_payOkBalance = ''; //代墊利息or履保費先收 點交 有出代書回饋金 未開發票
                            if (in_array($contractCase['bankRelay'], ['Y', 'C']) and $v['close'] > 0) {
                                $_payOkBalance = checkCaseMoneyPayOkBalance($contractCase['total'], $v["payOkTotal"], $v["isInvoice"], $v["tVR_Code"]);
                            }

                            $applyBankRelay = '';
                            if (is_array($v['data'])) {
                                foreach ($v['data'] as $kk => $vv) {
                                    $sql  = "select * from tBank where bBank3='" . substr($vv['tBankCode'], 0, 3) . "' and bBank4='' limit 1";
                                    $rs1  = $conn->Execute($sql);
                                    $bank = $rs1->fields["bBank4_name"];

                                    //
                                    $sql        = "select * from tBank where bBank3='" . substr($vv['tBankCode'], 0, 3) . "' and bBank4='" . substr($vv['tBankCode'], 3) . "' limit 1";
                                    $rs2        = $conn->Execute($sql);
                                    $bankBranch = $rs2->fields["bBank4_name"];

                                    switch ($vv["tCode"]) {
                                        case "01":
                                            $_title = "聯行轉帳";
                                            if ($vv["tCode2"] == '一銀內轉') {
                                                $_title = "一銀內轉";
                                            }

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
                                            if ($rs->fields['tCode2'] == '臨櫃領現') {
                                                $_title = '臨櫃領現';
                                            }

                                            break;
                                        case "06":
                                            $_title = "利息";
                                            break;
                                    }

                                    //20241128 法務關注案件未同意前不可出款
                                    $subLegalAllow = (! empty($vv['tLegalAllow']) && ($vv['tLegalAllow'] == '1')) ? ' disabled' : '';

                                    echo "<tr>";
                                    echo "<td><input type=\"checkbox\" name=\"check[]\" value=\"" . $vv["tId"] . "\" " . $subLegalAllow . "></td>";
                                    echo "<td><span class=\"ctitle\">類別</span> <span class=\"cvalue\">" . $vv["tKind"] . "</span><br><span class=\"ctitle\">交易類別</span><span class=\"cvalue\">&nbsp;" . $_title . "</span></td>";
                                    echo "<td><span class=\"ctitle\">解匯行</span> <span class=\"cvalue\">" . $bank . "</span><br><span class=\"ctitle\">分行別</span><span class=\"cvalue\">&nbsp;" . $bankBranch . "</span></td>";
                                    echo "<td><span class=\"ctitle\">戶名</span> <span class=\"cvalue\">" . $vv["tAccountName"] . "</span><br><span class=\"ctitle\">證號</span><span class=\"cvalue\">" . $vv["tAccountId"] . "</span></td>";
                                    echo "<td><span class=\"ctitle\">帳號</span><span class=\"cvalue\">" . $vv["tAccount"] . "</span></td>";
                                    echo "<td><span class=\"ctitle\">金額</span><span class=\"cvalue\">NT$ " . $vv["tMoney"] . "元(不含匯費)</span></td>";
                                    echo "<td>";
                                    if ($vv['pdf'] != '') {
                                        echo "<input type=\"button\" value=\"指示書\" onclick=\"book(" . $vv['bId'] . ",'" . $vv['pdf'] . "')\" class=\"xxx-button\"/>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                    echo "<td>&nbsp;</td>";
                                    echo "<td><span class=\"ctitle\">附言(備註)</span></td>";
                                    echo "<td cosplan=\"2\"><span class=\"cvalue\">" . $vv["tTxt"] . "</sapn>";

                                    if ($vv["tObjKind"] == '解除契約') {
                                        echo "<td ><span class=\"cvalue\">解約/終止履保</sapn>";
                                    } else {
                                        echo "<td ><span class=\"cvalue\">" . $vv["tObjKind"] . "</sapn>";
                                    }

                                    if ($vv['tObjKind2'] == '01') {
                                        $vv['tObjKind2'] = '申請公司代墊';
                                    } else if ($vv['tObjKind2'] == '02') {
                                        $vv['tObjKind2'] = '返還公司代墊';
                                    } else if ($vv['tObjKind2'] == '03') {
                                        $vv['tObjKind2'] = '不用代墊';
                                    } else if ($vv['tObjKind2'] == '04') {
                                        $vv['tObjKind2'] = '申請代理出款';
                                    } else if ($vv['tObjKind2'] == '05') {
                                        $vv['tObjKind2'] = '公司代理出款';
                                    }

                                    echo "<td><span style='color:red'>" . $vv['tObjKind2'] . "</span></td>
							      <td>&nbsp;</td>
							     ";

                                    echo "</tr>";
                                    echo "<tr><td colspan=\"7\"><hr /></td></tr>";
                                }
                            }

                            if (isset($v["tVR_Code"]) && in_array($v["tVR_Code"], $VRCodes)) {
                                $applyBankRelay = '<span style="color:red;font-weight:bold;">(申請代墊利息)</span>';
                            }

                            echo "<td width=\"34\"><input type=\"button\" name=\"s" . (isset($v["tVR_Code"]) ? $v["tVR_Code"] : '') . "\" value=\"展開\" onclick=\"showChild('" . (isset($v["tVR_Code"]) ? $v["tVR_Code"] : '') . "')\"></td>";

                            echo "<td>建檔時間 " . (isset($v["tDate"]) ? $v["tDate"] : '未知') . "</td>";
                            echo "<div class=\"child cClose c" . (isset($v["tVR_Code"]) ? $v["tVR_Code"] : '') . "\"><table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" width=\"966\">";
                        }
                    }

                    //保證號碼同時出款兩個項目
                    if (is_array($data2)) {
                        foreach ($data2 as $v) {
                            $legalAllow  = (! empty($v['legalAllow']) && ($v['legalAllow'] == '1')) ? ' disabled' : '';
                            $legalNotice = (! empty($v['legalAllow']) && ($v['legalAllow'] == '1')) ? '<span style="color:red;font-weight:bold;font-size:10pt;"> (列管)</span>' : '';

                            echo "<div class=\"both\">多筆出款項目</div>";
                            echo "<div class=\"exp\">";
                            echo "<div class=\"main\">";
                            echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" class=\"font12\"><tr>";
                            echo "<td width=\"258\">";
                            $tVR_Code = isset($v["tVR_Code"]) ? $v["tVR_Code"] : '';
                            echo "<input type=\"checkbox\" name=\"a" . $tVR_Code . "\"  onclick=\"checkALL('" . $tVR_Code . "')\" " . $legalAllow . ">
										專屬帳號 <font color=\"blue\"><strong>" . $tVR_Code . "</strong></font>" . $legalNotice;
                            if ($tVR_Code !== '' && preg_match("/96988000000008/", $tVR_Code)) {
                                echo '(利息)'; //台新利息
                            } else if ($tVR_Code !== '' && preg_match("/99985000000000/", $tVR_Code)) {
                                echo '(利息)'; //永豐西門利息
                            } else if ($tVR_Code !== '' && preg_match("/99986000000000/", $tVR_Code)) {
                                echo '(利息)'; //永豐城中利息
                            }

                            echo "</td>";
                            $caseObjKind = '';
                            foreach ($v['data'] as $onecase) {
                                if ($onecase['tObjKind'] == '履保費先收(結案回饋)') {
                                    $caseObjKind = '履保費先收(結案回饋)';
                                }
                            }
                            $contractCase = getCase($v["tVR_Code"], $caseObjKind);
                            $_balance     = checkCaseMoneyBalance($contractCase['total'], $v["Total"]);

                            $certifiedMoneyArrived = '';
                            if ($contractCase['bankRelay'] == 'C' and $v['close'] > 0) {
                                $certifiedMoneyArrived = '<span style="color:red;font-weight:bold;" title="履保費已先扣款">履保費已先扣款</span>';
                            }

                            //發票分配未完成
                            $invoiceFinished = '';
                            if ($contractCase['invoiceFinished'] == 'N') {
                                $invoiceFinished = '<span style="color:red;font-weight:bold;" title="發票分配未完成">發票分配未完成</span>';
                            }

                            echo "<td width=\"251\">待出帳總金額 " . $v["Total"] . " 元{$_balance}{$certifiedMoneyArrived}{$invoiceFinished}</td>";

                            $_payOkBalance = ''; //代墊利息 點交 有出代書回饋金 未開發票
                            if (in_array($contractCase['bankRelay'], ['Y', 'C']) and $v['close'] > 0) {
                                $_payOkBalance = checkCaseMoneyPayOkBalance($contractCase['total'], $v["payOkTotal"], $v["isInvoice"], $v["tVR_Code"]);
                            }
                            $applyBankRelay = '';
                            if (is_array($v['data'])) {
                                foreach ($v['data'] as $kk => $vv) {
                                    $sql  = "select * from tBank where bBank3='" . substr($vv['tBankCode'], 0, 3) . "' and bBank4='' limit 1";
                                    $rs1  = $conn->Execute($sql);
                                    $bank = $rs1->fields["bBank4_name"];

                                    //
                                    $sql        = "select * from tBank where bBank3='" . substr($vv['tBankCode'], 0, 3) . "' and bBank4='" . substr($vv['tBankCode'], 3) . "' limit 1";
                                    $rs2        = $conn->Execute($sql);
                                    $bankBranch = $rs2->fields["bBank4_name"];

                                    switch ($vv["tCode"]) {
                                        case "01":
                                            $_title = "聯行轉帳";
                                            if ($vv["tCode2"] == '一銀內轉') {
                                                $_title = "一銀內轉";
                                            }

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
                                            if ($rs->fields['tCode2'] == '臨櫃領現') {
                                                $_title = '臨櫃領現';
                                            }

                                            break;
                                        case "06":
                                            $_title = "利息";
                                            break;
                                    }

                                    //20241128 法務關注案件未同意前不可出款
                                    $subLegalAllow = (! empty($vv['tLegalAllow']) && ($vv['tLegalAllow'] == '1')) ? ' disabled' : '';

                                    echo "<tr>";
                                    echo "<td><input type=\"checkbox\" name=\"check[]\" value=\"" . $vv["tId"] . "\" " . $subLegalAllow . "></td>";
                                    echo "<td><span class=\"ctitle\">類別</span> <span class=\"cvalue\">" . $vv["tKind"] . "</span><br><span class=\"ctitle\">交易類別</span><span class=\"cvalue\">&nbsp;" . $_title . "</span></td>";
                                    echo "<td><span class=\"ctitle\">解匯行</span> <span class=\"cvalue\">" . $bank . "</span><br><span class=\"ctitle\">分行別</span><span class=\"cvalue\">&nbsp;" . $bankBranch . "</span></td>";
                                    echo "<td><span class=\"ctitle\">戶名</span> <span class=\"cvalue\">" . $vv["tAccountName"] . "</span><br><span class=\"ctitle\">證號</span><span class=\"cvalue\">" . $vv["tAccountId"] . "</span></td>";
                                    echo "<td><span class=\"ctitle\">帳號</span><span class=\"cvalue\">" . $vv["tAccount"] . "</span></td>";
                                    echo "<td><span class=\"ctitle\">金額</span><span class=\"cvalue\">NT$ " . $vv["tMoney"] . "元(不含匯費)</span></td>";
                                    echo "<td>";
                                    if ($vv['pdf'] != '') {
                                        echo "<input type=\"button\" value=\"指示書\" onclick=\"book(" . $vv['bId'] . ",'" . $vv['pdf'] . "')\" class=\"xxx-button\"/>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                    echo "<td>&nbsp;</td>";
                                    echo "<td><span class=\"ctitle\">附言(備註)</span></td>";
                                    echo "<td cosplan=\"2\"><span class=\"cvalue\">" . $vv["tTxt"] . "</sapn>";

                                    if ($vv["tObjKind"] == '解除契約') {
                                        echo "<td ><span class=\"cvalue\">解約/終止履保</sapn>";
                                    } else {
                                        echo "<td ><span class=\"cvalue\">" . $vv["tObjKind"] . "</sapn>";
                                    }

                                    if ($vv['tObjKind2'] == '01') {
                                        $vv['tObjKind2'] = '申請公司代墊';
                                    } else if ($vv['tObjKind2'] == '02') {
                                        $vv['tObjKind2'] = '返還公司代墊';
                                    } else if ($vv['tObjKind2'] == '03') {
                                        $vv['tObjKind2'] = '不用代墊';
                                    } else if ($vv['tObjKind2'] == '04') {
                                        $vv['tObjKind2'] = '申請代理出款';
                                    } else if ($vv['tObjKind2'] == '05') {
                                        $vv['tObjKind2'] = '公司代理出款';
                                    }

                                    echo "<td><span style='color:red'>" . $vv['tObjKind2'] . "</span></td>
							      <td>&nbsp;</td>
							     ";

                                    echo "</tr>";
                                    echo "<tr><td colspan=\"7\"><hr /></td></tr>";
                                }
                            }
                            echo "</table></div>";
                            echo "</div>";
                            echo "<div style=\"width:1016px;\"><hr></div>";
                        }
                    }
                } else {
                    $sql = 'select * from tBankTrans where tOK<>"1" AND tVR_Code LIKE "' . $conBank[$i]['cBankVR'] . '%" ORDER BY tOwner ASC';
                    $rs  = $conn->Execute($sql);

                    while (! $rs->EOF) {
                        //20240618 Project S只有特定人員可以查看
                        if (! in_array($_SESSION['member_id'], [1, 3, 6, 12, 13, 36, 84, 90]) && ($rs->fields["tMemo"] == '130119712')) {
                            continue;
                        }

                        if (! isset($data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['Total'])) {
                            $data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['Total'] = 0;
                        }
                        $data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['Total'] += $rs->fields["tMoney"];

                        if (! isset($data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['count'])) {
                            $data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['count'] = 0;
                        }
                        $data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['count']++;

                        if (! isset($data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['payOkTotal'])) {
                            $data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['payOkTotal'] = 0;
                        }
                        $data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['payOkTotal'] += $rs->fields["tMoney"];

                        $rs->MoveNext();
                    }

                    $bank = $conBank[$i]['cBankFullName'] . '(' . $conBank[$i]['cBranchName'] . ')';

                    echo "<div style=\"font-weight:bold;color:#002060;\">～～～&nbsp;" . $bank . "案件&nbsp;～～～</div>";

                    if (is_array($data[$conBank[$i]['cBankVR']])) {
                        foreach ($data[$conBank[$i]['cBankVR']] as $key => $value) {
                            echo "<div class=\"owner\">" . $key . "</div>";

                            foreach ($value as $k => $v) {
                                //20241128 法務關注案件未同意前不可出款
                                // $legalAllow  = (!empty($v['tLegalAllow']) && ($v['tLegalAllow'] == '1')) ? ' disabled' : '';
                                // $legalNotice = (!empty($v['tLegalAllow']) && ($v['tLegalAllow'] == '1')) ? '<span style="color:red;font-weight:bold;font-size:10pt;"> (列管)</span>' : '';

                                $legalAllow  = (! empty($v['legalAllow']) && ($v['legalAllow'] == '1')) ? ' disabled' : '';
                                $legalNotice = (! empty($v['legalAllow']) && ($v['legalAllow'] == '1')) ? '<span style="color:red;font-weight:bold;font-size:10pt;"> (列管)</span>' : '';

                                echo "<div class=\"exp\">";
                                echo "<div class=\"main\">";
                                echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" class=\"font12\"><tr>";
                                echo "<td width=\"258\">";
                                $tVR_Code = isset($v["tVR_Code"]) ? $v["tVR_Code"] : '';
                                echo "<input type=\"checkbox\" name=\"a" . $tVR_Code . "\"  onclick=\"checkALL('" . $tVR_Code . "')\" " . $legalAllow . ">
										專屬帳號 <font color=\"blue\"><strong>" . $tVR_Code . "</strong></font>" . $legalNotice;
                                if ($tVR_Code !== '' && preg_match("/96988000000008/", $tVR_Code)) {
                                    echo '(利息)'; //台新利息
                                } else if ($tVR_Code !== '' && preg_match("/99985000000000/", $tVR_Code)) {
                                    echo '(利息)'; //永豐西門利息
                                } else if ($tVR_Code !== '' && preg_match("/99986000000000/", $tVR_Code)) {
                                    echo '(利息)'; //永豐城中利息
                                }

                                echo "</td>";
                                $caseObjKind = '';
                                foreach ($v['data'] as $onecase) {
                                    if ($onecase['tObjKind'] == '履保費先收(結案回饋)') {
                                        $caseObjKind = '履保費先收(結案回饋)';
                                    }
                                }
                                $contractCase          = getCase($v["tVR_Code"], $caseObjKind);
                                $_balance              = checkCaseMoneyBalance($contractCase['total'], $v["Total"]);
                                $certifiedMoneyArrived = '';
                                if ($contractCase['bankRelay'] == 'C' and $v['close'] > 0) {
                                    $certifiedMoneyArrived = '<span style="color:red;font-weight:bold;" title="履保費已先扣款">履保費已先扣款</span>';
                                }
                                //發票分配未完成
                                $invoiceFinished = '';
                                if ($contractCase['invoiceFinished'] == 'N') {
                                    $invoiceFinished = '<span style="color:red;font-weight:bold;" title="發票分配未完成">發票分配未完成</span>';
                                }
                                echo "<td width=\"251\">待出帳總金額 " . $v["Total"] . " 元{$_balance}{$certifiedMoneyArrived}{$invoiceFinished}</td>";

                                $_payOkBalance = ''; //代墊利息 點交 有出代書回饋金 未開發票
                                if (in_array($contractCase['bankRelay'], ['Y', 'C']) and $v['close'] > 0) {
                                    $_payOkBalance = checkCaseMoneyPayOkBalance($contractCase['total'], $v["payOkTotal"], $v["isInvoice"], $v["tVR_Code"]);
                                }
                                $applyBankRelay = '';
                                if (is_array($v['data'])) {
                                    foreach ($v['data'] as $kk => $vv) {
                                        $sql  = "select * from tBank where bBank3='" . substr($vv['tBankCode'], 0, 3) . "' and bBank4='' limit 1";
                                        $rs1  = $conn->Execute($sql);
                                        $bank = $rs1->fields["bBank4_name"];

                                        //
                                        $sql        = "select * from tBank where bBank3='" . substr($vv['tBankCode'], 0, 3) . "' and bBank4='" . substr($vv['tBankCode'], 3) . "' limit 1";
                                        $rs2        = $conn->Execute($sql);
                                        $bankBranch = $rs2->fields["bBank4_name"];

                                        switch ($vv["tCode"]) {
                                            case "01":
                                                $_title = "聯行轉帳";
                                                if ($vv["tCode2"] == '一銀內轉') {
                                                    $_title = "一銀內轉";
                                                }

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
                                                if ($rs->fields['tCode2'] == '臨櫃領現') {
                                                    $_title = '臨櫃領現';
                                                }

                                                break;
                                            case "06":
                                                $_title = "利息";
                                                break;
                                        }

                                        //20241128 法務關注案件未同意前不可出款
                                        $subLegalAllow = (! empty($vv['tLegalAllow']) && ($vv['tLegalAllow'] == '1')) ? ' disabled' : '';

                                        echo "<tr>";
                                        echo "<td><input type=\"checkbox\" name=\"check[]\" value=\"" . $vv["tId"] . "\" " . $subLegalAllow . "></td>";
                                        echo "<td><span class=\"ctitle\">類別</span> <span class=\"cvalue\">" . $vv["tKind"] . "</span><br><span class=\"ctitle\">交易類別</span><span class=\"cvalue\">&nbsp;" . $_title . "</span></td>";
                                        echo "<td><span class=\"ctitle\">解匯行</span> <span class=\"cvalue\">" . $bank . "</span><br><span class=\"ctitle\">分行別</span><span class=\"cvalue\">&nbsp;" . $bankBranch . "</span></td>";
                                        echo "<td><span class=\"ctitle\">戶名</span> <span class=\"cvalue\">" . $vv["tAccountName"] . "</span><br><span class=\"ctitle\">證號</span><span class=\"cvalue\">" . $vv["tAccountId"] . "</span></td>";
                                        echo "<td><span class=\"ctitle\">帳號</span><span class=\"cvalue\">" . $vv["tAccount"] . "</span></td>";
                                        echo "<td><span class=\"ctitle\">金額</span><span class=\"cvalue\">NT$ " . $vv["tMoney"] . "元(不含匯費)</span></td>";
                                        echo "<td>";
                                        if ($vv['pdf'] != '') {
                                            echo "<input type=\"button\" value=\"指示書\" onclick=\"book(" . $vv['bId'] . ",'" . $vv['pdf'] . "')\" class=\"xxx-button\"/>";
                                        }
                                        echo "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                        echo "<td>&nbsp;</td>";
                                        echo "<td><span class=\"ctitle\">附言(備註)</span></td>";
                                        echo "<td cosplan=\"2\"><span class=\"cvalue\">" . $vv["tTxt"] . "</sapn>";

                                        if ($vv["tObjKind"] == '解除契約') {
                                            echo "<td ><span class=\"cvalue\">解約/終止履保</sapn>";
                                        } else {
                                            echo "<td ><span class=\"cvalue\">" . $vv["tObjKind"] . "</sapn>";
                                        }

                                        if ($vv['tObjKind2'] == '01') {
                                            $vv['tObjKind2'] = '申請公司代墊';
                                        } else if ($vv['tObjKind2'] == '02') {
                                            $vv['tObjKind2'] = '返還公司代墊';
                                        } else if ($vv['tObjKind2'] == '03') {
                                            $vv['tObjKind2'] = '不用代墊';
                                        } else if ($vv['tObjKind2'] == '04') {
                                            $vv['tObjKind2'] = '申請代理出款';
                                        } else if ($vv['tObjKind2'] == '05') {
                                            $vv['tObjKind2'] = '公司代理出款';
                                        }

                                        echo "<td><span style='color:red'>" . $vv['tObjKind2'] . "</span></td>
							      <td>&nbsp;</td>
							     ";

                                        echo "</tr>";
                                        echo "<tr><td colspan=\"7\"><hr /></td></tr>";
                                    }
                                }
                                echo "</table>";
                                echo "</div>";
                                echo "<div style=\"width:1016px;\"><hr></div>";
                            }
                        }
                    }
                }
            }

        ?>

        <div>
            <input type="button" value="審核通過" onclick="auth()" />
        </div>
</body>

</html>