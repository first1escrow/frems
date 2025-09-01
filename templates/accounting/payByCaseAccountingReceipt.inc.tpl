<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <{include file='meta2.inc.tpl'}>
    <script type="text/javascript">
        $(document).ready(function() {
            searchData();
        } );

        function dia(op) {
            $( "#dialog" ).dialog(op) ;
        }
        function checkALL(){
            var all = $('[name="all"]').prop('checked');

            // console.log(all);
            if (all == true) {
                $('[name="allForm[]"]').prop('checked', true);
            }else{
                $('[name="allForm[]"]').prop('checked', false);
            }
            showcount();
            // allForm
        }
        function searchData(){
            var tranStatus = $('[name="banktranStatus"]').val();
            if ($('[name="banktranStatus"]').val() == 1) {
                $('[name="sDate"]').val('');
                $('[name="sDate2"]').val('');
            }

            $.ajax({
                url: 'payByCaseAccountingReceipt_result.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    banktranStatus: tranStatus,
                    exp: $('[name="exp"]').val(),
                    endCaseStart: $('[name="eDate"]').val(),
                    endCaseEnd: $('[name="eDate2"]').val(),
                    exportStart: $('[name="sDate"]').val(),
                    exportEnd: $('[name="sDate2"]').val(),
                },
            })
            .done(function(html) {
                $("#result").html(html);
                showcount();
            });
        }
        function excel(){
            $("#exportform").submit();
        }
        function showcount(){
            var checkedCount = 0;
            var feedbackMoneyTotal = 0;
            $('[name="allForm[]"]').each(function() {
                if ($(this).prop('checked') == true) {
                    // console.log($('[name="sFeedBackMoneyTotal_'+$(this).val()+'"]').val());
                    feedbackMoneyTotal += parseInt($('[name="sFeedBackMoneyTotal_'+$(this).val()+'"]').val())
                    checkedCount++;
                }
            });
            $("#count").html(checkedCount);
            $("#feedBackMoney").html(feedbackMoneyTotal);
        }

        function setStatus(id){
            $.ajax({
                url: 'payByCaseAccountingReceipt_status.php',
                type: 'POST',
                dataType: 'html',
                data: {id: id},
            })
            .done(function(code) {
                searchData();
                if (code == 1) {
                    alert("已恢復未匯出");
                }
            });
        }

        function caseReceipt(cId) {
            let _receipt = 'N';

            if ($('#receipt_'+cId).is(':checked')) {
                _receipt = 'Y';
            }
            accountPayByCaseConfirm(cId, 'receipt', _receipt);
        }
        function accountPayByCaseConfirm(cId, target, action) {
            $('.cmc_overlay').show();

            let url = '/includes/accounting/accountPayByCaseConfirm.php';
            $.post(url, {'cId': cId, 'target': target, 'action': action}, function(response) {
                if (response.status == 200) {
                    alert('已變更完成');
                    if(action == 'Y') {
                        $('input#receipt_' + cId).after('已收');
                    }
                } else {
                    alert(response.message);
                }
                $('.cmc_overlay').hide();
            }, 'json').fail(function (xhr, status, error) {
                alert(xhr.responseText);
                $('.cmc_overlay').hide();
            });
        }
    </script>
    <style>
        #dialog {
            background-image:url("../images/animated-overlay.gif") ;
            background-repeat: repeat-x;
            margin: 0px auto;
        }
        .tb_main{
            border: 1px solid #999;

        }
        .tb{
            border: 1px solid #FFFFFF;
            width: 100%;
            /*background-color: #FCEEEE;*/

        }
        .tb td{
            border: 1px solid #999;
            padding: 5px;
        }
        .tb th{
            border: 1px solid #999;
            background: #FCEEEE;
            padding: 5px;
        }
        .div-inline{
            display:inline;
            /* width: 90%;
             float: center;
             padding-bottom: 50px;
             */


            /*padding-right: 20px;*/
        }
        .div-inline th{
            text-align: left;
        }
        .div-inline td{
            padding-left: 20px;
        }
        #show {
            padding: 50px;

        }
        .div-inline2{
            display:inline;
            width: 100%;
            float: center;
            padding-bottom: 50px;

            /*padding-right: 20px;*/
        }
        .xxx-button {
            color:#FFFFFF;
            font-size:12px;
            font-weight:normal;

            text-align: center;
            white-space:nowrap;

            background-color: #a63c38;
            border: 1px solid #a63c38;
            border-radius: 0.35em;
            font-weight: bold;
            padding: 0 20px;
            margin: 5px auto 5px auto;

            width:auto;
            height:20px;
            font-size:16px;
        }
        .xxx-button:hover {
            background-color:#333333;
            border:1px solid #333333;
        }
    </style>
</head>
<body id="dt_example">
<div class="cmc_overlay" style="display:none;">
    <div class="cmc_overlay__inner">
        <div class="cmc_overlay__content"><span class="cmc_spinner"></span></div>
    </div>
</div>
<div id="wrapper">
    <div id="header">
        <table width="1000" border="0" cellpadding="2" cellspacing="2">
            <tr>
                <td width="233" height="72">&nbsp;</td>
                <td width="753">
                    <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                        <tr>
                            <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                        </tr>
                        <tr>
                            <td width="81%" align="right"></td>
                            <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <{include file='menu1.inc.tpl'}>
    <ul id="menu">
        <div id="dialog"></div>
    </ul>
    <table width="1000" border="0" cellpadding="4" cellspacing="0">
        <tr>
            <td bgcolor="#DBDBDB">
                <table width="100%" border="0" cellpadding="4" cellspacing="1">
                    <tr>
                        <td height="17" bgcolor="#FFFFFF">
                            <div id="container">
                                <center>
                                    <h1>收據繳回確認表</h1>
                                    <br>
                                    <form name="myform" id="myform" method="POST"  action="" >
                                        <table  align="center" class="tb_main" cellpadding="10" cellspacing="10">
                                            <tr>
                                                <td align="center">狀態
                                                    <select name="banktranStatus" id="" >
                                                        <option value="1" selected>未匯出</option>
                                                        <option value="2">已匯出</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    匯出批次
                                                    <select name="exp" id="exp" class="easyui-combobox">
                                                        <{foreach from=$menu_exp key=key item=item}>
                                                        <option value="<{$key}>"><{$item}></option>
                                                        <{/foreach}>
                                                    </select>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    履保費日期
                                                    <input type="text" name="eDate" class="datepickerROC" style="width:100px;">
                                                    ~
                                                    <input type="text" name="eDate2" class="datepickerROC" style="width:100px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    匯出表單時間
                                                    <input type="text" name="sDate" class="datepickerROC" style="width:100px;">
                                                    ~
                                                    <input type="text" name="sDate2" class="datepickerROC" style="width:100px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="button" value="查詢" onclick="searchData()">
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                        <br>
                                    <form name="exportform" id="exportform" method="POST"  action="" >
                                        <div id="result">

                                        </div>
                                        <div><input type="button" value="匯出" onclick="excel()" class="xxx-button"></div>
                                        <input type="hidden" name="export" value="1">
                                        <div>店家勾選數量: <span id="count"></span></div>
                                        <div>店家勾選總金額: <span id="feedBackMoney"></span></div>
                                    </form>
                                    <br>

                                    <div id="show">
                                        <{$show}>
                                    </div>
                                </center>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div id="footer">
        <p>2012 第一建築經理股份有限公司 版權所有</p>
    </div>
</body>
</html>