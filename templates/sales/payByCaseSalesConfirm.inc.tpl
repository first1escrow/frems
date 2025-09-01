<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />


<{include file='meta.inc.tpl'}>


<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<script type="text/javascript">
    var confirmedTable = null;
$(document).ready(function() {
    $( "#tabs" ).tabs({
        selected: <{$_tab}>
    });

    <{if $toggle == '1_2'}>
        $("#toggle_1_2").click();
    <{/if}>

    confirmedTable = $("#example").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST", 
        "sAjaxSource": "/includes/sales/payByCaseConfirmed.php",
        "oLanguage": {
            "sEmptyTable": "沒有資料可以顯示",
            "sZeroRecords": "找不到符合的資料"
        }
    });

    $("#payok_list").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST",
        "sAjaxSource": "/includes/sales/payByCasePayok.php",
    });
});

function colorbx(url) {
	$.colorbox({href:url});
}

function apply(cId) {
    $('#id').val(cId);
    $('[name="_tabs"]').val(10);
    $('#form1').submit();
}

function detail(cId) {
    $('#id').val(cId);
    $('#form1').submit();
}

function confirm_case(cId, targetId, fId) {
    let radio = $('[name="' + cId + '"]:checked');

    if (!radio.val()) {
        alert('請確認選取回饋金帳戶資訊');
        return false;
    }
    
    $('.cmc_overlay').show();

    let url = '/includes/sales/payByCaseConfirm.php';
    $.post(url, {'cId': cId, 'bank': radio.val(), 'fId': fId}, function(response) {
        if (response.status == 200) {
            alert('已確認');
        } else {
            alert(response.message);
        }

        let _identity = radio.val().split('_');
        if (_identity[0] == 3) { //限定統編才顯示
            showPDF(cId, targetId);
        }

        setTimeout(function() {
            window.location = location.href;
        }, 500);
        
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

function checkAll() {
    if ($("[name='all']").prop('checked') == true) {
        $(".checkCase").prop('checked', true);
    } else {
        $(".checkCase").prop('checked', false);
    }
}

function checkAll2() {
    if ($("[name='all2']").prop('checked') == true) {
        $(".checkCase").prop('checked', true);
    } else {
        $(".checkCase").prop('checked', false);
    }
}

function confirmAll() {
    let checkedBoxes = $('input[name="case[]"]:checked');
    let checkedCount = checkedBoxes.length;

    let checkedValues = [];
    checkedBoxes.map(function() {
        checkedValues.push({ 'cid':this.value, 'bank': $('[name="' + this.value + '"]:checked').val(), 'fid':this.dataset.fid });
    });

    if(checkedCount > 0){
        if(window.confirm('共選取 ' + checkedCount + ' 筆保證號碼，是否都確認？')){
            $('.cmc_overlay').show();
            let url = '/includes/sales/payByCaseConfirmAll.php';

            $.post(url, JSON.stringify(checkedValues), function(response) {
                if (response.code == 1) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.error_detail);
                }

                $('.cmc_overlay').hide();
            }, 'json').fail(function (xhr, status, error) {
                alert(xhr.responseText);
                $('.cmc_overlay').hide();
            });
        }
    } else {
        alert('未勾選待確認的資料');
    }
}

var logIsLoaded = false;
function getLogList(){
    if(!logIsLoaded){
        var log_dataTable = $("#log_list").dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sServerMethod": "POST",
            "sAjaxSource": "/includes/sales/payByCaseLog.php",
            "aaSorting": [[6, "desc"]],
            "aoColumnDefs": [
                { "bSortable": false, "aTargets": [4] }
            ]
        });

        logIsLoaded = true;
    }
}

function getFeedbackConfirmed(btn){
    $(".toggle-btn2").removeClass("active").addClass("inactive");
    $(btn).addClass("active").removeClass("inactive");

    var tableWidth = $("#example").width();
    confirmedTable.fnDestroy();
    $("#example tbody").empty();
    $("#example").css("width", tableWidth);

    $("#example").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST",
        "sAjaxSource": "/includes/sales/feedbackConfirmed.php",
        "oLanguage": {
            "sEmptyTable": "沒有資料可以顯示",
            "sZeroRecords": "找不到符合的資料"
        }
    });
}

function getPayByCaseConfirmed(btn){
    $(".toggle-btn2").removeClass("active").addClass("inactive");
    $(btn).addClass("active").removeClass("inactive");

    var tableWidth = $("#example").width();
    confirmedTable.fnDestroy();
    $("#example tbody").empty();
    $("#example").css("width", tableWidth);

    $("#example").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST",
        "sAjaxSource": "/includes/sales/payByCaseConfirmed.php",
        "oLanguage": {
            "sEmptyTable": "沒有資料可以顯示",
            "sZeroRecords": "找不到符合的資料"
        }
    });
}

function getLogDetail(id){
    let url = 'payByCaseLogDetail.php?id=' + id;
    $.colorbox({iframe:true, width:"90%", height:"50%", href:url});
}

function confirm_case_log(cId, fId) {
    $('.cmc_overlay').show();

    let url = '/includes/sales/payByCaseConfirmLog.php';
    $.post(url, {'cId': cId, 'fId': fId}, function(response) {
        if (response.status == 200) {
            alert('已確認');
        } else {
            alert(response.message);
        }

        setTimeout(function() {
            window.location = location.href;
        }, 500);

    }, 'json').fail(function (xhr, status, error) {
        alert(xhr.responseText);
        $('.cmc_overlay').hide();
    });
}

function unconfirmed_toggle(v, btn){
    $(".toggle-btn").removeClass("active").addClass("inactive");
    $(btn).addClass("active").removeClass("inactive");

    $("#tabs-unconfirm-1").hide();
    $("#tabs-unconfirm-2").hide();
    $("#tabs-unconfirm-" + v).show();
}

function confirm_case2(cId, targetId, fId, fTarget) {

    $('.cmc_overlay').show();

    let url = '/includes/sales/feedbackConfirm.php';
    $.post(url, {'cId': cId, 'fId': fId, 'fTarget': fTarget}, function(response) {
        if (response.status == 200) {
            alert('已確認');
        } else {
            alert(response.message);
        }

        if (location.href.indexOf('toggle') === -1){
            window.location = location.href + '?toggle=1_2';
        } else {
            window.location = location.href
        }
    }, 'json').fail(function (xhr, status, error) {
        alert(xhr.responseText);
        $('.cmc_overlay').hide();
    });
}

function confirmAll2() {
    let checkedBoxes = $('input[name="case2[]"]:checked');
    let checkedCount = checkedBoxes.length;

    let checkedValues = [];
        checkedBoxes.map(function() {
        checkedValues.push({ 'cid':this.value, 'fid':this.dataset.fid });
    });

    if(checkedCount > 0){
        if(window.confirm('共選取 ' + checkedCount + ' 筆保證號碼，是否都確認？')){
            $('.cmc_overlay').show();
            let url = '/includes/sales/feedbackConfirmAll.php';

            $.post(url, JSON.stringify(checkedValues), function(response) {
                if (response.code == 1) {
                    alert(response.message);
                    if (location.href.indexOf('toggle') === -1){
                        window.location = location.href + '?toggle=1_2';
                    } else {
                        window.location = location.href
                    }
                } else {
                    alert(response.error_detail);
                }

                $('.cmc_overlay').hide();
            }, 'json').fail(function (xhr, status, error) {
                alert(xhr.responseText);
                $('.cmc_overlay').hide();
            });
        }
    } else {
        alert('未勾選待確認的資料');
    }
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

.tip-box{
    position: relative;
}
.tip-box:hover::before {
    position: absolute;
    top: -50px;
    left: 85px;
    content: '請先審核回饋金';
}

.toggle-container {
    margin: 20px;
}

.toggle-btn {
    padding: 10px 20px;
    margin-right: 10px;
    border: 1px solid #ccc;
    background-color: #f0f0f0;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.toggle-btn2 {
    padding: 10px 20px;
    margin-right: 10px;
    border: 1px solid #ccc;
    background-color: #f0f0f0;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.active {
    background-color: #990000;
    color: white;
    border-color: #990000;
}

.inactive {
    opacity: 0.6;
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
                                                    <div style="padding-bottom: 20px;" >
                                                        <a href="payByCaseSalesConfirm.php" style="font-size:14pt;color: #4E6CA3;padding-bottom: 20px;">回饋金隨案出款確認清單</a>
                                                    </div>

                                                    <div id="tabs">
                                                        <ul>
                                                            <li><a href="#tabs-unconfirm">待確認</a></li>
                                                            <li><a href="#tabs-confirmed">已確認</a></li>
                                                            <li><a href="#tabs-payok">已出款</a></li>
                                                            <{if $smarty.session.member_id == '6'}>
                                                            <li><a href="#tabs-log" onclick="getLogList()">異動紀錄</a></li>
                                                            <{/if}>
                                                        </ul>
                                                        <div id="tabs-unconfirm">
                                                            <button class="toggle-btn active" style="padding: 5px;" onclick="unconfirmed_toggle('1', this)">隨案回饋(<{$list|count}>)</button>
                                                            <button id="toggle_1_2" class="toggle-btn inactive" style="padding: 5px;" onclick="unconfirmed_toggle('2', this)">標記案件(<{$list2|count}>)</button>

                                                            <div id="tabs-unconfirm-1">
                                                                <!-- 業務再確認 -->
                                                                <{if $list_recheck|count > 0}>
                                                                <fieldset style="margin:10px 0 10px 0;;padding: 10px;">
                                                                    <legend class="title-head" style="color: red">異動再確認</legend>
                                                                <{foreach from=$list_recheck key=key item=item}>
                                                                <div style="margin-bottom: 5px; padding:5px; background-color:<{if $key % 2 == 0}>#DDDDDD;<{/if}>">
                                                                    <div style="padding: 5px;">
                                                                        <div class="title-head" style="float:left;text-align:left;">
                                                                            <div style="padding-bottom: 5px;">
                                                                                <label for="chk_<{$item.fCertifiedId}>">保證號碼：<{$item.fCertifiedId}>、負責業務：<{$item.sales}>、簽約日：<{$item.signDate|mb_substr:0:10}></label>
                                                                            </div>
                                                                        </div>
                                                                        <div style="float:right;text-align:right;">
                                                                            <button style="padding: 5px;" onclick="detail('<{$item.fCertifiedId}>')">案件明細</button>
                                                                            <button style="padding: 5px;" onclick="apply('<{$item.fCertifiedId}>')">修改申請</button>
                                                                            <button style="padding: 5px;" onclick="confirm_case_log('<{$item.fCertifiedId}>','<{$item.fId}>')" <{if $item.reviewStatus === '0'}> disabled class="" <{/if}> >確認
                                                                            </button>
                                                                            <{if $item.reviewStatus === '0'}> <br><strong style="font-size: 11px;">(請先審核回饋金)</strong> <{/if}>
                                                                        </div>
                                                                        <div style="clear:both;"></div>
                                                                    </div>

                                                                    <{if $item.detail.total > 0}>
                                                                    <div style="padding: 5px;">
                                                                        <div>
                                                                            <div class="title-head">仲介：</div>
                                                                            <hr class="hr-style"></hr>
                                                                            <div style="width: 79%;float:left;">
                                                                                <{foreach from=$item.detail.realty key=k item=v}>
                                                                                <div style="float:left;"><{$v}></div>
                                                                                <div style="clear:both;"></div>
                                                                                <{/foreach}>
                                                                            </div>
                                                                            <div style="width: 21%;float:right;">案件總回饋比例：<{$item.totalRatio}>%</div>
                                                                            <div style="clear:both;"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div style="margin-top: 20px;padding: 5px;">
                                                                        <div>
                                                                            <div class="title-head">回饋金：</div>
                                                                            <hr class="hr-style"></hr>

                                                                            <div style="float:left;width: 100px;">原回饋對象：</div>
                                                                            <div style="float:left;width: 150px;"><{$item.scrivenerId}></div>
                                                                            <div style="float:left;width: 60px;"></div>
                                                                            <div style="float:left;"></div>
                                                                            <div style="float:right;"></div>
                                                                            <div style="float:right;"></div>
                                                                            <div style="clear:both;padding-bottom: 10px;"></div>

                                                                            <div style="float:left;width: 100px;">買賣總價金：</div>
                                                                            <div style="float:left;width: 150px;"><{$item.detail.cTotalMoney}></div>
                                                                            <div style="float:left;width: 60px;">履保費：</div>
                                                                            <div style="float:left;">
                                                                                <{$item.detail.cCertifiedMoney}>
                                                                                <{if $item.detail.deficiency == "Y"}>
                                                                                <span class="checkCertifiedFee" style="display: ;border:0px;"><font color="red" style="font-weight:bold;">(未收足)</font></span>
                                                                                <{/if}>
                                                                            </div>
                                                                            <div style="float:right;"></div>
                                                                            <div style="float:right;"></div>
                                                                            <div style="clear:both;padding-bottom: 10px;"></div>

                                                                            <div style="float:left;">回饋對象：</div>
                                                                            <{foreach from=$item.detail.case key=k2 item=v2}>
                                                                            <div style="float:left;"><{$v2.cBranchName}></div>
                                                                            <div style="float:left;">（回饋金額：</div>
                                                                            <div style="float:left;"><{$v2.cCaseFeedBackMoney}>）</div>
                                                                            <div style="clear:both;"></div>
                                                                            <div style="clear:both;padding-bottom: 10px;"></div>
                                                                            <{/foreach}>
                                                                        </div>
                                                                    </div>
                                                                    <{else}>
                                                                    <div style="padding: 5px; color: red;">已非隨案回饋：</div>
                                                                    <div style="padding: 5px; "><{$item.memo}></div>
                                                                    <{/if}>

                                                                    <br>

                                                                </div>
                                                                <{/foreach}>
                                                                </fieldset>
                                                                <{/if}>

                                                                <{if $sales_counter|count > 0}>
                                                                <fieldset style="margin:10px 0 10px 0;;padding: 10px;">
                                                                    <legend class="title-head">待確認業務數量統計</legend>

                                                                    <{foreach from=$sales_counter key=key item=item}>
                                                                    <div style="padding: 5px;">
                                                                        <{if $key != ''}>
                                                                            <label>
                                                                                <{if $key == '曾政耀'}>
                                                                                    <a href="payByCaseSalesConfirm.php?id=3"><{$key}></a>
                                                                                <{else}>
                                                                                    <{$key}>
                                                                                <{/if}>：<{$item}> 件
                                                                            </label>
                                                                        <{/if}>
                                                                    </div>

                                                                    <{/foreach}>
                                                                </fieldset>
                                                                <{/if}>

                                                                <{if $list|count > 0}>
                                                                <div style="padding-bottom: 5px;"><input type="checkbox" onclick="checkAll()" name="all" class="cbAll" id="cbAll"><label for="cbAll">  全選</label></div>
                                                                <{foreach from=$list key=key item=item}>
                                                                <div style="margin-bottom: 5px; padding:5px; background-color:<{if $key % 2 == 0}>#DDDDDD;<{/if}>">
                                                                    <div style="padding: 5px;">
                                                                        <div class="title-head" style="float:left;text-align:left;">
                                                                            <div style="padding-bottom: 5px;">
                                                                                <{if $item.reviewStatus != '0'}><input type="checkbox" id="chk_<{$item.fCertifiedId}>" name="case[]" value="<{$item.fCertifiedId}>" data-fid="<{$item.fId}>" class="checkCase"/><{/if}>
                                                                                <label for="chk_<{$item.fCertifiedId}>">保證號碼：<{$item.fCertifiedId}>、負責業務：<{$item.sales}>、簽約日：<{$item.signDate|mb_substr:0:10}></label>
                                                                            </div>
                                                                        </div>
                                                                        <div style="float:right;text-align:right;">
                                                                            <button style="padding: 5px;" onclick="detail('<{$item.fCertifiedId}>')">案件明細</button>
                                                                            <button style="padding: 5px;" onclick="apply('<{$item.fCertifiedId}>')">修改申請</button>
                                                                            <button style="padding: 5px;" onclick="confirm_case('<{$item.fCertifiedId}>', '<{$item.fTargetId}>', '<{$item.fId}>')" <{if $item.reviewStatus === '0' or $item.bank.bank|count == 0 }> disabled class="" <{/if}> >確認
                                                                            </button>
                                                                            <{if $item.reviewStatus === '0'}> <br><strong style="font-size: 11px;">(請先審核回饋金)</strong> <{/if}>
                                                                            <{if $item.bank.bank|count == 0}> <br><strong style="font-size: 11px;">(請先建立回饋金帳戶)</strong> <{/if}>
                                                                        </div>
                                                                        <div style="clear:both;"></div>
                                                                    </div>

                                                                    <div style="padding: 5px;">
                                                                        <div>
                                                                            <div class="title-head">仲介：</div>
                                                                            <hr class="hr-style"></hr>
                                                                            <div style="width: 79%;float:left;">
                                                                                <{foreach from=$item.detail.realty key=k item=v}>
                                                                                <div style="float:left;"><{$v}></div>
                                                                                <div style="clear:both;"></div>
                                                                                <{/foreach}>
                                                                            </div>
                                                                            <div style="width: 21%;float:right;">案件總回饋比例：<{$item.totalRatio}>%</div>
                                                                            <div style="clear:both;"></div>
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
                                                                            <div style="float:right;">隨案回饋比例：</div>
                                                                            <div style="clear:both;padding-bottom: 10px;"></div>

                                                                            <div style="float:left;">回饋對象：</div>
                                                                            <div style="float:left;"><{$item.scrivenerId}></div>
                                                                            <div style="float:left;">（回饋金額：</div>
                                                                            <div style="float:left;"><{$item.detail.total}>）</div>
                                                                            <div style="clear:both;"></div>
                                                                            <div style="clear:both;padding-bottom: 10px;"></div>

                                                                            <div style="float:left;">回饋比率：</div>
                                                                            <div style="float:left;margin-right: 10px"><{$item.sRecall}></div>
                                                                            <div style="float:left;">特殊回饋比率：</div>
                                                                            <div style="float:left;"><{$item.sSpRecall}></div>
                                                                        </div>
                                                                    </div>

                                                                    <br>

                                                                    <fieldset style="margin:10px 0 10px 0;;padding: 10px;">
                                                                        <legend class="title-head">回饋金帳戶</legend>

                                                                        <{foreach from=$item.bank.bank key=ka item=va}>
                                                                        <div style="padding: 5px;">
                                                                            <input type="radio" id="<{$item.fCertifiedId}>" name="<{$item.fCertifiedId}>" value="<{$va.bank}>" <{$item.bank.checked}>>
                                                                            <label for="<{$item.fCertifiedId}>">銀行：<{$va.bankMain}><{$va.bankBranch}>、帳號：<{$va.fAccount}>、戶名：<{$va.fAccountName}></label>
                                                                        </div>

                                                                        <{/foreach}>
                                                                    </fieldset>
                                                                </div>
                                                                <{/foreach}>
                                                                <div style="text-align:center;margin-bottom: 5px;margin-top: 10px;"><button style="padding: 5px;" onclick="confirmAll()">批次確認</button></div>
                                                                <{else}>
                                                                <div style="text-align:center;margin-bottom: 5px;">無未確認案件</div>
                                                                <{/if}>
                                                            </div>

                                                            <!-- 標記案件 -->
                                                            <div id="tabs-unconfirm-2" style="display: none;">
                                                                <{if $list2|count > 0}>
                                                                <div style="padding-bottom: 5px;padding-top: 10px;"><input type="checkbox" onclick="checkAll2()" name="all2" class="cbAll" id="cbAll2"><label for="cbAll2">  全選</label></div>
                                                                <{foreach from=$list2 key=key item=item}>
                                                                <div style="margin-bottom: 5px; padding:5px; background-color:<{if $key % 2 == 0}>#DDDDDD;<{/if}>">
                                                                    <div style="padding: 5px;">
                                                                        <div class="title-head" style="float:left;text-align:left;">
                                                                            <div style="padding-bottom: 5px;">
                                                                                <{if $item.reviewStatus != '0'}><input type="checkbox" id="chk2_<{$item.fCertifiedId}>" name="case2[]" value="<{$item.fCertifiedId}>" data-fid="<{$item.fId}>" class="checkCase"/><{/if}>
                                                                                <label for="chk2_<{$item.fCertifiedId}>">保證號碼：<{$item.fCertifiedId}>、負責業務：<{$item.sales}>、簽約日：<{$item.signDate|mb_substr:0:10}></label>
                                                                            </div>
                                                                        </div>
                                                                        <div style="float:right;text-align:right;">
                                                                            <button style="padding: 5px;" onclick="detail('<{$item.fCertifiedId}>')">案件明細</button>
                                                                            <!--<button style="padding: 5px;" onclick="apply('<{$item.fCertifiedId}>')">修改申請</button>-->
                                                                            <button style="padding: 5px;" onclick="confirm_case2('<{$item.fCertifiedId}>', '<{$item.fTargetId}>', '<{$item.fId}>', '<{$item.fTarget}>')" <{if $item.reviewStatus === '0'}> disabled class="" <{/if}> >確認
                                                                            </button>
                                                                            <{if $item.reviewStatus === '0'}> <br><strong style="font-size: 11px;">(請先審核回饋金)</strong> <{/if}>
                                                                        </div>
                                                                        <div style="clear:both;"></div>
                                                                    </div>

                                                                    <div style="padding: 5px;">
                                                                        <div>
                                                                            <div class="title-head">代書：<{$item.scrivenerId}></div>
                                                                            <div class="title-head">仲介：</div>
                                                                            <hr class="hr-style"></hr>
                                                                            <div style="width: 79%;float:left;">
                                                                                <{foreach from=$item.detail.realty key=k item=v}>
                                                                                <div style="float:left;"><{$v}></div>
                                                                                <div style="clear:both;"></div>
                                                                                <{/foreach}>
                                                                            </div>
                                                                            <div style="width: 21%;float:right;">案件總回饋比例：<{$item.totalRatio}>%</div>
                                                                            <div style="clear:both;"></div>
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
                                                                            <div style="float:right;">回饋比例：</div>
                                                                            <div style="clear:both;padding-bottom: 10px;"></div>

                                                                            
                                                                            <{foreach from=$item.detail.case key=k item=v}>
                                                                                <{if $v.cBranchNum > 0}>
                                                                                    <div style="float:left;"><{$v.cBranchStore}>(金額：<{$v.cCaseFeedBackMoney}>)</div>
                                                                                    <div style="clear:both;"></div>
                                                                                <{/if}>
                                                                            <{/foreach}>
                                                                        </div>
                                                                    </div>

                                                                    <br>
                                                                </div>
                                                                <{/foreach}>
                                                                <div style="text-align:center;margin-bottom: 5px;margin-top: 10px;"><button style="padding: 5px;" onclick="confirmAll2()">批次確認</button></div>
                                                                <{else}>
                                                                <div style="text-align:center;margin-bottom: 5px;">無未確認案件</div>
                                                                <{/if}>
                                                            </div>
                                                        </div>
                                                        <!--已確認-->
                                                        <div id="tabs-confirmed">
                                                            <div>
                                                                <button class="toggle-btn2 active" style="padding: 5px;" onclick="getPayByCaseConfirmed(this)">隨案回饋</button>
                                                                <button id="toggle_2_2" class="toggle-btn2 inactive" style="padding: 5px;" onclick="getFeedbackConfirmed(this)">標記案件</button>
                                                            </div>
                                                            </br>
                                                            <span style="color: red;font-size: 10px;">*履保費未收足案件</span>
                                                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:50px;">保證號碼</th>
                                                                        <th style="width:30px;">負責業務</th>
                                                                        <th style="width:30px;">確認業務</th>
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
                                                                        <th>確認業務</th>
                                                                        <th>回饋對象</th>
                                                                        <th>金額</th>
                                                                        <th>回饋金帳戶</th>
                                                                        <th style="width:30px;">代扣二代健保費</th>
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
                                                                    <th style="width:50px;">保證號碼</th>
                                                                    <th style="width:30px;">負責業務</th>
                                                                    <th style="width:30px;">確認業務</th>
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
                                                                    <th>確認業務</th>
                                                                    <th>回饋對象</th>
                                                                    <th>金額</th>
                                                                    <th>回饋金帳戶</th>
                                                                    <th style="width:30px;">代扣二代健保費</th>
                                                                    <th>實際金額</th>
                                                                </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>

                                                        <!--log 紀錄-->
                                                        <div id="tabs-log">
                                                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="log_list">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width:50px;">保證號碼</th>
                                                                    <th style="width:30px;">負責業務</th>
                                                                    <th style="width:30px;">確認業務</th>
                                                                    <th style="width:30px;">回饋對象</th>
                                                                    <th style="width:30px;">金額</th>
                                                                    <th style="width:30px;">異動次數</th>
                                                                    <th style="width:30px;">異動時間</th>
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
                                                                    <th>確認業務</th>
                                                                    <th>回饋對象</th>
                                                                    <th>金額</th>
                                                                    <th>異動次數</th>
                                                                    <th>異動時間</th>
                                                                </tr>
                                                                </tfoot>
                                                            </table>
                                                            <div>&nbsp;</div>
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