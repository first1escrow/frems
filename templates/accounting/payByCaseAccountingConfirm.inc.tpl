<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />


<{include file='meta.inc.tpl'}>


<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $( "#tabs" ).tabs({
        selected: <{$_tab}>
    });

    $("#example").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST", 
        "sAjaxSource": "/includes/accounting/payByCaseConfirmed.php",
    });
    $("#payok_list").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST",
        "sAjaxSource": "/includes/accounting/payByCasePayok.php",
    });
});

function colorbx(url) {
	$.colorbox({href:url});
}

function detail(cId) {
    $('#id').val(cId);
    $('[name="_tabs"]').val(9);
    $('#form1').submit();
}

function confirm(cId) {
    $('.cmc_overlay').show();
    let fNHIpay = $("input[name='fNHIpay['"+cId+"']']").is(':checked');

    if(fNHIpay == true) {
        fNHIpay = 'Y';
    } else {
        fNHIpay = 'N';
    }
    let url = '/includes/accounting/payByCaseConfirm.php';
    $.post(url, {  cId: cId, fNHIpay: fNHIpay }, function(response) {
        if (response.status == 200) {
            alert('已確認');
        } else {
            alert(response.message);
        }

        window.location.reload();
        $('.cmc_overlay').hide();
    }, 'json').fail(function (xhr, status, error) {
        alert(xhr.responseText);
        $('.cmc_overlay').hide();
    });
}

function caseReceipt(cId) {
    let _receipt = 'N';

    if ($('#receipt_'+cId).is(':checked')) {
        _receipt = 'Y';
    }

    accountPayByCaseConfirm(cId, 'receipt', _receipt);
}

function caseClose(cId) {
    let _close = 'N';

    if ($('#close_'+cId).is(':checked')) {
        _close = 'Y';
    }

    accountPayByCaseConfirm(cId, 'close', _close);
}

function accountPayByCaseConfirm(cId, target, action) {
    $('.cmc_overlay').show();

    let url = '/includes/accounting/accountPayByCaseConfirm.php';
    $.post(url, {'cId': cId, 'target': target, 'action': action}, function(response) {
        if (response.status == 200) {
            alert('已變更完成');
            $('[name="_tab_id"]').val(2);
            $('[name="tab"]').submit();
        } else {
            alert(response.message);
        }

        $('.cmc_overlay').hide();
    }, 'json').fail(function (xhr, status, error) {
        alert(xhr.responseText);
        $('.cmc_overlay').hide();
    });
}

function showPDF(cId, targetId) {
    $('[name="pdfId"]').val(cId);
    $('[name="targetId"]').val(targetId);
    $('#form2').submit();
}

</script>
<style>
.main {
    width: 900px;margin:0px auto;
}

.rowWidth td {
    width: 20%;
}

.title-head {
    font-weight: bold;
    margin-top: 5px;
}

.hr-style {
    background-color: #4ddfd1;
    margin: 5px 0 5px 0;
}

#account-list th {
    text-align: center;
}

#account-list td:nth-child(2), td:nth-child(3), td:nth-child(5), td:nth-child(6) {
    text-align: center;
}
</style>
</head>
<body id="dt_example">
    <div class="cmc_overlay" style="display:none;">
        <div class="cmc_overlay__inner">
            <div class="cmc_overlay__content"><span class="cmc_spinner"></span></div>
        </div>
    </div>

    <form method="POST" id="form1" action="/escrow/formbuyowneredit.php" target="_blank">
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="_tabs" value="">
    </form>

    <form method="POST" name="tab">
        <input type="hidden" name="_tab_id">
    </form>

    <form method="POST" id="form2" action="/includes/sales/payByCasePDFReceipt.php" target="_blank">
        <input type="hidden" name="pdfId">
        <input type="hidden" name="targetId">
    </form>

    <div id="wrapper">
        <div id="header">
            <table width="1000" border="0" cellpadding="2" cellspacing="2">
                <tr>
                    <td width="233" height="72">&nbsp;</td>
                    <td width="753">
                        <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                            <tr>
                                <td colspan="3" align="right">
                                    <div id="abgne_marquee" style="display:none;">
                                        <ul>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" align="right">
                                    <h1><{include file='welcome.inc.tpl'}></h1>
                                </td>
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

        <div id="mainNav">
            <table width="1000" border="0" cellpadding="0" cellspacing="0">
                <tr>

                </tr>
            </table>
        </div>

        <div id="content">
            <div class="abgne_tab">
                <{include file='menu1.inc.tpl'}>
                <div class="tab_container">
                    <div id="menu-lv2"></div>
                    <br/>
                    <div id="tab" class="tab_content">
                        
                        <table width="100%" border="0" cellpadding="4" cellspacing="0">
                            <tr>
                                <td bgcolor="#DBDBDB">
                                    <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                        <tr>
                                            <td height="17" bgcolor="#FFFFFF">

                                                <div id="container">
                                                    <div style="font-size:14pt;color: #4E6CA3;padding-bottom: 20px;">
                                                        回饋金隨案出款確認清單
                                                    </div>

                                                    <div id="tabs">
                                                        <ul>
                                                            <li><a href="#tabs-unconfirm">待確認</a></li>
                                                            <li><a href="#tabs-confirmed">已確認</a></li>
                                                            <li><a href="#tabs-payok">已出款</a></li>
                                                        </ul>

                                                        <div id="tabs-unconfirm">
                                                            <{if $list|count > 0}>
                                                            <{foreach from=$list key=key item=item}>
                                                            <div style="margin-bottom: 5px; padding:5px; background-color:<{if $key % 2 == 0}>#DDDDDD;<{/if}>">
                                                                <div style="padding: 5px;">
                                                                    <div class="title-head" style="float:left;text-align:left;">
                                                                        <div style="padding-bottom: 5px;">保證號碼：<{$item.fCertifiedId}>、負責業務：<{$item.sales}></div>
                                                                    </div>
                                                                    <div style="float:right;text-align:right;">
                                                                        <input type="checkbox" name="fNHIpay['<{$item.fCertifiedId}>']" id="fNHIpay">代扣二代健保費
                                                                        <button style="padding: 5px;" onclick="detail('<{$item.fCertifiedId}>')">案件明細</button>
                                                                        <button style="padding: 5px;" onclick="confirm('<{$item.fCertifiedId}>')">　確認　</button>
                                                                    </div>
                                                                    <div style="clear:both;"></div>
                                                                </div>

                                                                <div style="padding: 5px;">
                                                                    <div>
                                                                        <div class="title-head">仲介：</div>
                                                                        <hr class="hr-style"></hr>

                                                                        <{foreach from=$item.detail.realty key=k item=v}>
                                                                        <div style="float:left;"><{$v}></div>
                                                                        <div style="clear:both;"></div>
                                                                        <{/foreach}>
                                                                    </div>
                                                                </div>

                                                                <div style="margin-top: 20px;padding: 5px;">
                                                                    <div>
                                                                        <div class="title-head">回饋金：</div>
                                                                        <hr class="hr-style"></hr>

                                                                        <div style="float:left;width: 100px;">買賣總價金：</div>
                                                                        <div style="float:left;width: 150px;"><{$item.detail.cTotalMoney}></div>
                                                                        <div style="float:left;width: 60px;">履保費：</div>
                                                                        <div style="float:left;">
                                                                            <{$item.detail.cCertifiedMoney}>
                                                                            <{if $item.detail.deficiency == "Y"}>
                                                                            <span class="checkCertifiedFee" style="display: ;border:0px;"><font color="red" style="font-weight:bold;">(未收足)</font></span>
                                                                            <{/if}>
                                                                        </div>
                                                                        <div style="float:right;"><font color="red" id="showRatio"><{$item.detail.ratio}>%</font></div>
                                                                        <div style="float:right;">案件回饋比例：</div>
                                                                        <div style="clear:both;padding-bottom: 10px;"></div>

                                                                        <div style="float:left;">回饋對象：</div>
                                                                        <div style="float:left;"><{$item.scrivenerId}></div>
                                                                        <div style="float:left;">（回饋金額：</div>
                                                                        <div style="float:left;"><{$item.detail.total}>）</div>
                                                                        <div style="clear:both;"></div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <br>

                                                                <fieldset style="margin:10px 0 10px 0;;padding: 10px;">
                                                                    <legend class="title-head">回饋金帳戶</legend>
                                                                    
                                                                    <div style="padding: 5px;">
                                                                        <div style="padding-bottom: 5px;">
                                                                            銀行：<{$item.bankMain}><{$item.bankBranch}>、帳號：<{$item.bankAccount}>、戶名：<{$item.bankAccountName}>
                                                                        </div>
                                                                        <div>
                                                                            身分類別：<{$item.identity}>、證號：<{$item.fIdentityIdNumber}>
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <{/foreach}>
                                                            <{else}>
                                                            <div style="text-align:center;margin-bottom: 5px;">無未確認案件</div>
                                                            <{/if}>
                                                        </div>
                                                        <!--已確認-->
                                                        <div id="tabs-confirmed">
                                                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:40px;">保證號碼</th>
                                                                        <th style="width:30px;">負責業務</th>
                                                                        <th style="width:30px;">回饋對象</th>
                                                                        <th style="width:30px;">金額</th>
                                                                        <th style="width:;">回饋金帳戶</th>
                                                                        <th style="width:30px;">代扣二代健保費</th>
                                                                        <th style="width:30px;">實際金額</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="5">　</td>
                                                                    </tr>
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <th>保證號碼</th>
                                                                        <th>負責業務</th>
                                                                        <th>回饋對象</th>
                                                                        <th>金額</th>
                                                                        <th>回饋金帳戶</th>
                                                                        <th>代扣二代健保費</th>
                                                                        <th>實際金額</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                        <!--已出款-->
                                                        <div id="tabs-payok">
                                                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="payok_list">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width:40px;">保證號碼</th>
                                                                    <th style="width:30px;">負責業務</th>
                                                                    <th style="width:30px;">回饋對象</th>
                                                                    <th style="width:30px;">金額</th>
                                                                    <th style="width:;">回饋金帳戶</th>
                                                                    <th style="width:30px;">代扣二代健保費</th>
                                                                    <th style="width:30px;">實際金額</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr>
                                                                    <td colspan="5">　</td>
                                                                </tr>
                                                                </tbody>
                                                                <tfoot>
                                                                <tr>
                                                                    <th>保證號碼</th>
                                                                    <th>負責業務</th>
                                                                    <th>回饋對象</th>
                                                                    <th>金額</th>
                                                                    <th>回饋金帳戶</th>
                                                                    <th>代扣二代健保費</th>
                                                                    <th>實際金額</th>
                                                                </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div id="footer" style="height:50px;">
            <p>2012 第一建築經理股份有限公司 版權所有</p>
        </div>
    </div>
</body>
</html>