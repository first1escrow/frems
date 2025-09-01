<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

$conn = new first1DB;

if (!empty($_POST) && in_array($_POST['actions'], ['excel', 'restore'])) {
    $post  = $_POST;
    $_POST = null;unset($_POST);

    if ($post['actions'] == 'restore') {
        require_once dirname(__DIR__) . '/includes/bank/restoreRelayData.php';

        header('Location: /bank/bankRelay.php');
        exit;
    }
}

$sql = 'SELECT bExport_nu FROM tBankTransRelay WHERE bExport_nu IS NOT NULL GROUP BY bExport_nu;';
$rs  = $conn->all($sql);

$menu_export = array_column($rs, 'bExport_nu');
rsort($menu_export);
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
    <title>中繼帳戶出帳列表</title>
    <link rel="stylesheet" type="text/css" href="/css/colorbox.css" />
    <link rel="stylesheet" type="text/css" href="/css/datepickerROC.css" />
    <link rel="stylesheet" type="text/css" href="/js/datepicker/jquery-ui-datepicker.css" />
    <link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />

    <script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/jquery.colorbox.js"></script>
    <script type="text/javascript" src="/js/datepicker/jquery-ui-datepicker.js"></script>
    <script type="text/javascript" src="/js/datepickerRoc.js"></script>
    <script type="text/javascript" src="/js/jquery.number.js"></script>
    <script>
    $(document).ready(function() {
        $('.sms-show').hide();

        setTimeout(function() {
            queryData();
        }, 500);

        $(".iframe").colorbox({
            iframe: true,
            width: "1100",
            height: "500",
            onClosed: function() {
                // reload_page();
            }
        });
    });

    function queryData() {
        resetCalculatedInfo();
        let banktranStatus = $('[name="banktranStatus"] option:selected').val();
        let exp = $('#exp option:selected').val();
        let sDate = $('[name="sDate"]').val();
        let eDate = $('[name="eDate"]').val();

        let url = '/includes/bank/getRelayTransData.php';
        $.post(url, {
            'export': banktranStatus,
            'export_nu': exp,
            'start_date': sDate,
            'end_date': eDate
        }, function(response) {
            if (response.status == 'OK') {
                $('#check_all').prop('checked', false);
                $('#sms_check_all').prop('checked', false);

                $('#result').empty().html(response.data);
                $('#check_all').prop('checked', false);

                $('.sms-show').hide();
                if (banktranStatus == 1) {
                    $('.sms-show').show();
                }
            } else {
                alert('操作異常');
            }
        }, 'json');
    }

    function getCheckedValues() {
        let data = [];
        $('.js-check').each(function() {
            if ($(this).is(':checked')) {
                data.push($(this).val())
            }
        });

        return data;
    }

    function exportList() {
        let data = getCheckedValues();

        if (data.length == 0) {
            alert('請至少選取一筆紀錄');
            return;
        }

        $('[name="actions"]').val('excel');
        $('#myform').attr('action', '/includes/bank/exportRelayData.php').attr('target', '_blank').submit();
        location.reload();
    }

    function checkAll() {
        let checked = false;
        if ($('#check_all').is(':checked')) {
            checked = true;
        }

        $('.js-check').each(function() {
            $(this).prop('checked', checked);
        }).promise().done(function() {
            calculateChecked();
        });
    }

    function smsCheckAll() {
        let checked = false;
        if ($('#sms_check_all').is(':checked')) {
            checked = true;
        }

        $('.js-sms-check').each(function() {
            $(this).prop('checked', checked);
        }).promise().done(function() {
            smsChecked();
        });
    }

    function smsChecked() {

    }

    function restoreList() {
        let data = getCheckedValues();

        if (data.length == 0) {
            alert('請至少選取一筆紀錄');
            return;
        }

        if (confirm('確認是否要恢復案件為未匯款?')) {
            $('[name="actions"]').val('restore');
            $('#myform').submit();
        }
    }

    function calculateChecked() {
        let total_store = 0;
        let total_feedback = 0;
        let total_nhi = 0;
        let total_tax = 0;
        let total_balance = 0;
        let total_incoming = 0;

        $('.js-check').each(function() {
            if ($(this).prop('checked') === true) {
                let el = $(this).parents('tr').children('td');

                let _kind = el.eq(2).text(); //類別
                let _feedback = parseInt(el.eq(7).text().replace(/\,/g, '')); //回饋金
                let _tax = parseInt(el.eq(8).text().replace(/\,/g, '')); //稅款
                let _nhi = parseInt(el.eq(9).text().replace(/\,/g, '')); //保費
                let _total = parseInt(el.eq(10).text().replace(/\,/g, '')); //實際金額
                let _incoming = parseInt(el.eq(11).text().replace(/\,/g, '')); //實際金額

                total_store += 1;
                total_feedback += _feedback;
                total_nhi += _nhi;
                total_tax += _tax;
                total_balance += _total;
                total_incoming += _incoming;
            }
        }).promise().done(function() {
            setCalculatedInfo({
                store: total_store,
                feedback: total_feedback,
                nhi: total_nhi,
                tax: total_tax,
                balance: total_balance,
                incoming: total_incoming
            });
        });
    }

    function setCalculatedInfo(obj) {
        $('#total-store').empty().html($.number(obj.store));
        $('#total-feedback').empty().html($.number(obj.feedback));
        $('#total-nhi').empty().html($.number(obj.nhi));
        $('#total-tax').empty().html($.number(obj.tax));
        $('#total-balance').empty().html($.number(obj.balance));
        $('#total-incoming').empty().html($.number(obj.incoming));
    }

    function resetCalculatedInfo() {
        setCalculatedInfo({
            store: 0,
            feedback: 0,
            nhi: 0,
            tax: 0,
            balance: 0,
            incoming: 0
        });
    }


    function sendSMS() {
        let data = getSmsCheckedValues();
        if (data.length == 0) {
            alert('請至少選取一筆紀錄');
            return;
        }

        if (confirm('確認是否要發送簡訊?')) {
            $('.cmc_overlay').show();

            $.post('/includes/bank/sendSMS.php', {
                'sms-uid': data
            }, function(response) {
                alert(response);
                queryData();
                $('.cmc_overlay').hide();
            }).fail(function(xhr, status, error) {
                xhr.responseText ? alert(xhr.responseText) : alert('發送簡訊失敗');
                $('.cmc_overlay').hide();
            });
        }
    }

    function getCheckedValues() {
        let data = [];
        $('.js-check').each(function() {
            if ($(this).is(':checked')) {
                data.push($(this).val())
            }
        });

        return data;
    }

    function getSmsCheckedValues() {
        let data = [];
        $('.js-sms-check').each(function() {
            if ($(this).is(':checked')) {
                data.push($(this).val())
            }
        });

        return data;
    }
    </script>
    <style>
    table {
        border: 1px solid #CCC;
    }

    th {
        background: #CCC;
        /* height: 50px; */
        padding: 3px;
    }

    .width-50 {
        width: 50px;
    }

    .width-80 {
        width: 80px;
    }

    .width-120 {
        width: 120px;
    }

    .width-200 {
        width: 200px;
    }

    td {
        text-align: center;
        /* font-size: 10pt; */
        padding: 2px;
    }

    #result td {
        border-bottom: 1px solid #CCC;
    }

    tr:nth-child(even) {
        /* background: #FFE8E8; */
    }

    .feedback {
        background: #ffe8e8;
        color: #000000;
    }

    .text-right {
        text-align: right;
    }

    #chart {
        font-size: 9pt;
    }
    </style>
</head>

<body>
    <div class="cmc_overlay" style="display:none;">
        <div class="cmc_overlay__inner">
            <div class="cmc_overlay__content"><span class="cmc_spinner"></span></div>
        </div>
    </div>

    <div style="width:1024px; margin-bottom:5px; height:22px; background-color: #CCC">
        <div style="float:left;margin-left: 10px;"> <a href="instructions/IBookList.php">指示書</a> </div>
        <div style="float:left;margin-left: 10px;"> <a href="/bank/list2.php">待修改資料</a> </div>
        <?php
if (in_array($_SESSION["member_id"], [6, 13, 36]) || in_array($_SESSION["member_pDep"], [5, 6])) { //個別權限顯示
    ?>
        <div style="float:left;margin-left: 10px;"> <a href="/bank/BankTransProcess.php">出款進度</a> </div>
        <?php
}

if ($_SESSION["member_bankcheck"] == '1') { //個別權限顯示
    ?>
        <div style="float:left; margin-left: 10px;"> <a href="/bank/list.php">未審核列表</a></div>
        <div style="float:left; margin-left: 10px;"> <a href="/bank/list_ok.php">已審核列表</a></div>
        <div style="float:left; margin-left: 10px;">
            <font color=red><strong>中繼帳戶出款</strong></font>
        </div>
        <?php
}
?>
    </div>

    <div id="chart">
        <div>
            <center>
                <form name="myform" id="myform" method="POST">
                    <input type="hidden" name="actions">
                    <table class="tb_main" cellpadding="10" cellspacing="10">
                        <tr>
                            <td align="center">狀態
                                <select name="banktranStatus" id="">
                                    <option value="2" selected>未匯款</option>
                                    <option value="1">已匯款</option>
                                </select>
                            </td>
                            <td>
                                匯出批次
                                <select name="exp" id="exp" class="easyui-combobox">
                                    <option value="">　　</option>
                                    <?php
foreach ($menu_export as $k => $v) {
    echo '<option value="' . $v . '">' . $v . '</option>' . "\n";
}
?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                匯出時間
                                <input type="text" name="sDate" class="datepickerROC" style="width:100px;">
                                ~
                                <input type="text" name="eDate" class="datepickerROC" style="width:100px;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                                <input type="button" value="查詢" onclick="queryData()">
                            </td>
                        </tr>
                        </tr>
                    </table>
                    <br>
                    <div style="margin-top: 20px;">
                        <table>
                            <thead>
                                <tr>
                                    <th class="width-50">
                                        <input type="checkbox" id="check_all" value="Y" onclick="checkAll()">
                                    </th>
                                    <th class="width-80 sms-show">
                                        簡訊<br>
                                        <input type="checkbox" id="sms_check_all" value="Y" onclick="smsCheckAll()">
                                    </th>
                                    <th class="width-80">類別</th>
                                    <th class="width-80">保證號碼</th>
                                    <th class="width-80">店編號</th>
                                    <th class="width-200">戶名</th>
                                    <th class="width-120">證件號碼</th>
                                    <th class="width-80">回饋金</th>
                                    <th class="width-80">代扣稅款<br>(10%)</th>
                                    <th class="width-80">代扣保費<br>(2.11%)</th>
                                    <th class="width-120">實際金額</th>
                                    <th class="width-120">經辦匯款金額</th>
                                    <th class="width-80">匯出時間</th>
                                    <th class="width-80">批號</th>
                                </tr>
                            </thead>
                            <tbody id="result"></tbody>
                        </table>
                    </div>
                </form>

                <div style="width: 500px;margin-top: 30px;margin-bottom: 30px;font-size:18px;">
                    <fieldset>
                        <legend>已勾選統計</legend>
                        <div>
                            <div style="float:left;width:250px;text-align:right;">已勾選店家數：</div>
                            <div style="float:left;text-align:left;" id="total-store">0</div>
                            <div style="clear:both;"></div>
                        </div>
                        <div>
                            <div style="float:left;width:250px;text-align:right;">回饋金加總：</div>
                            <div style="float:left;text-align:left;" id="total-feedback">0</div>
                            <div style="clear:both;"></div>
                        </div>
                        <div>
                            <div style="float:left;width:250px;text-align:right;">代扣二代健保加總：</div>
                            <div style="float:left;text-align:left;" id="total-nhi">0</div>
                            <div style="clear:both;"></div>
                        </div>
                        <div>
                            <div style="float:left;width:250px;text-align:right;">代扣所得稅加總：</div>
                            <div style="float:left;text-align:left;" id="total-tax">0</div>
                            <div style="clear:both;"></div>
                        </div>
                        <div>
                            <div style="float:left;width:250px;text-align:right;">實際金額加總：</div>
                            <div style="float:left;text-align:left;" id="total-balance">0</div>
                            <div style="clear:both;"></div>
                        </div>
                        <div>
                            <div style="float:left;width:250px;text-align:right;">經辦匯款金額加總：</div>
                            <div style="float:left;text-align:left;" id="total-incoming">0</div>
                            <div style="clear:both;"></div>
                        </div>
                    </fieldset>
                </div>
            </center>
        </div>
    </div>
</body>

</html>