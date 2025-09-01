<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>
    <{include file='meta.inc.tpl'}> 		
    <script type="text/javascript">
    $(document).ready(function() {
        var aSelected = [];
        
        $( "#dialog" ).dialog({
            autoOpen: false,
            modal: true,
            minHeight:50,
            show: {
                effect: "blind",
                duration: 1000
            },
            hide: {
                effect: "explode",
                duration: 1000
            }
        });
        $(".ui-dialog-titlebar").hide() ;
        
        $('#loading').dialog('close');
        $(".ajax").colorbox({width:"400",height:"100"});
        
        $( "#branch_search" ).combobox() ;
        $( "#scrivener_search" ).combobox() ;
        
        $('#citys').change(function() {
            cityChange() ;
        }) ;
        
        $('#areas').change(function() {
            areaChange() ;
        }) ;
    });

    /* 取得縣市區域資料 */
    function cityChange() {
        let url = 'zipArea.php' ;
        let _city = $('#citys :selected').val() ;
        
        $.post(url,{'c':_city,'op':'1'},function(txt) {
            $('#areas').html(txt) ;
        }) ;
    }

    /* 取得區域郵遞區號 */
    function areaChange() {
        let _area = $('#areas :selected').val() ;
        $('#zip').val(_area) ;
    }

    function postData(cat) {
        let d1 = $("[name='sEndDate']").val();
        let d2 = $("[name='eEndDate']").val();

        if (cat == '1') { //報表
            $("[name='ok']").val('ok');
        } else {
            $("[name='ok']").val('ok');
        }
        
        $('[name="form"]').submit();
    }

    function Audit(){
        $("[name='ok']").val('ok');
        $('[name="form"]').submit();
    }

    function clearFrom(){
        $("[name='sEndDate']").val('');
        $("[name='eEndDate']").val('');
        $("[name='ok']").val('');
        $("[name='xls']").val('');
        $('[name="current_page"]').val('');
        $("[name='sales']").val('');
    }

    function checkedAll(){
        if ($("[name='all']").prop('checked') == true) {
            $(".checkCase").prop('checked', true);
        } else {
            $(".checkCase").prop('checked', false);
        }
    }
    </script>
    <style>
    .small_font {
        font-size: 9pt;
        line-height:1;
    }

    input.bt4 {
        padding:4px 4px 1px 4px;
        vertical-align: middle;
        background: #F8EDEB;border:1px #727272 outset;color:font-size:12px;margin-left:2px
    }

    input.bt4:hover {
        padding:4px 4px 1px 4px;
        vertical-align: middle;
        background:  #EBD1C8;border:1px #727272 outset;font-size:12px;margin-left:2px;cursor:pointer
    }

    .ui-autocomplete-input {
        width:300px;
    }

    #dialog {
        background-image:url("/images/animated-overlay.gif") ;
        background-repeat: repeat-x;
        margin: 0px auto;
    }

    .row{
        background-color:#FFFFFF;padding-top:5px;padding-left:5px;
    }

    .tb th{
        background-color:#E4BEB1;padding:4px;
    }

    .tb td{
        background-color:#F8ECE9;padding:4px;padding-left:5px;
    }

    .tb{
        border: 1px solid #CCC;
    }

    .cb1 {
        padding:0px 0px;
    }

    .cb1 input[type="checkbox"] {/*隱藏原生*/
        /*display:none;*/
        position: absolute;
        left: -9999px;
    }

    .cb1 input[type="checkbox"] + label span {
        display:inline-block;
        width:20px;
        height:20px;
        margin:-3px 4px 0 0;
        vertical-align:middle;
        background:url("../images/check_radio_sheet2.png") left top no-repeat;
        cursor:pointer;
        background-size:80px 20px;
        transition: none;
        -webkit-transition:none;
    }

    .cb1 input[type="checkbox"]:checked + label span {
        background:url("../images/check_radio_sheet2.png") -20px top no-repeat;
        background-size:80px 20px;
        transition: none;
        -webkit-transition:none;
    }

    .cb1 label {
        cursor:pointer;
        display: inline-block;
        margin-right: 10px;
    }

    .cbAll{
            width:20px;
        height:20px;
        margin:-3px 4px 0 0;
    }
    </style>
</head>
<body id="dt_example">
    <div id="wrapper">
        <div id="header">
            <table width="1000" border="0" cellpadding="2" cellspacing="2">
                <tr>
                    <td width="233" height="72">&nbsp;</td>
                    <td width="753">
                        <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                            <tr>
                                <td colspan="3" align="right">
                                    <h1><{include file='welcome.inc.tpl'}></h1>
                                </td>
                            </tr>
                            <tr>
                                <td width="81%" align="right"></td>
                                <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td>
                                <td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table> 
        </div>
        <{include file='menu1.inc.tpl'}>
        <table width="1000" border="0" cellpadding="4" cellspacing="0">
            <tr>
                <td bgcolor="#DBDBDB">
                    <table width="100%" border="0" cellpadding="4" cellspacing="1">
                        <tr>
                            <td height="17" bgcolor="#FFFFFF">
                                <div id="menu-lv2">

                                </div>
                                <br/> 
                                <h3>&nbsp;</h3>
                                <div id="container">
                                    <div id="dialog"></div>
                                    <div>
                                        <form name="form" method="POST">
                                            <input type="hidden" name="xls">
                                            <input type="hidden" name="ok">
                                            <h1>回饋金審核</h1>
                                            <center>
                                                <table cellspacing="0" cellpadding="0" style="width:50%;border:1px solid #CCC">
                                                    <tr>
                                                        <td style="width:20%;background-color:#E4BEB1;padding:4px;">類別</td>
                                                        <td style="background-color:#F8ECE9;padding:4px;padding-left:5px;">
                                                            <input type="radio" name="cat" id="" value="1" <{$checked1}>>未核可
                                                            <input type="radio" name="cat" id="" value="2" <{$checked2}>> 已核可
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:20%;background-color:#E4BEB1;padding:4px;">保證號碼</td>
                                                        <td style="background-color:#F8ECE9;padding:4px;padding-left:5px;">
                                                            <input type="text" name="cCertifiedId" id="">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:20%;background-color:#E4BEB1;padding:4px;">
                                                            履保費出款日
                                                        </td>
                                                        <td style="background-color:#F8ECE9;padding:4px;padding-left:5px;">
                                                            <input type="text" name="sEndDate" class="datepickerROC" style="width:100px;" value="<{$sEndDate}>">(起)~
                                                            <input type="text" name="eEndDate" class="datepickerROC" style="width:100px;" value="<{$eEndDate}>">(迄)
                                                        </td>
                                                    </tr>
                                                    <{if $smarty.session.member_id|in_array: [1, 3, 6, 12]}>
                                                    <tr>
                                                        <td style="width:20%;background-color:#E4BEB1;padding:4px;">
                                                            申請人
                                                        </td>
                                                        <td style="background-color:#F8ECE9;padding:4px;padding-left:5px;">
                                                            <select name="sales" size="1" style="width:130px;">
                                                                <option value="">全部</option>
                                                                <{$salesList}>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:20%;background-color:#E4BEB1;padding:4px;">
                                                            回饋金隨案出款
                                                        </td>
                                                        <td style="background-color:#F8ECE9;padding:4px;padding-left:5px;">
                                                            <select name="payByCase" style="width:130px;">
                                                                <option value="0">全部(預設)</option>
                                                                <option value="1" <{if $payByCase == 1}> selected <{/if}>>隨案回饋金</option>
                                                                <option value="2" <{if $payByCase == 2}> selected <{/if}>>標記案件</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <{/if}>
                                                </table>
                                            </center>

                                            <div style="padding:20px;text-align:center;">
                                                <input type="button" value="查詢" onclick="postData(0);" class="bt4" style="display:;width:100px;height:35px;">
                                                <input type="button" value="清除" class="bt4" style="display:;width:100px;height:35px;" onclick="clearFrom()">
                                            </div>

                                            <div style="font-size: 18px;color:red;padding: 10px;"><{$msg}></div>
                                            <div style="padding-bottom:5px;"><hr></div>

                                            <div id="data">
                                                <input type="checkbox" onclick="checkedAll()" name="all" class="cbAll">全選

                                                <{foreach from=$list key=key item=item}>
                                                <table width="100%" border="0" class="tb">
                                                    <tr>
                                                        <th colspan="5" align="left">
                                                            <{if $cat != 1 && $item.close != 1}>
                                                            <span class="cb1"><input type="checkbox" name="Case[]" id="case<{$item.ReviewId}>" value="<{$item.ReviewId}>" class="checkCase"><label for="case<{$item.ReviewId}>"><span></span></label></span>
                                                            <{/if}>
                                                            &nbsp;
                                                        </th>
                                                        <th>
                                                            <{if $item.checkSalesArea == 0}>
                                                            <font color="red">不同區業務</font>
                                                            <{/if}>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th width="15%">案號︰</th>
                                                        <td width="20%"><{$item.cCertifiedId}></td>
                                                        <th width="10%">地政士︰</th>
                                                        <td width="20%"><{$item.sOffice}></td>
                                                        <th width="10%">總價金︰</th>
                                                        <td width="20%">
                                                            <{if $item.fTotalMoney == 0}><{$item.cTotalMoney|number_format:0}>
                                                            <{else}><{$item.fTotalMoney|number_format:0}>
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th width="15%">保證費金額:</th>
                                                        <td colspan="4">
                                                            <{if $item.fCertifiedMoney == 0}><{$item.cCertifiedMoney|number_format:0}>
                                                            <{else}><{$item.fCertifiedMoney|number_format:0}>
                                                            <{/if}>
                                                        </td>
                                                        <td>
                                                            <{if $item.close == 1}>
                                                            <font color="blue">回饋金已鎖定</font>	
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="6">回饋對象</th>
                                                    </tr>
                                                    <tr>
                                                        <th width="15%">仲介店名︰</th>
                                                        <td colspan="5">
                                                            <span style="background-color: <{$item.BranchFeedBackStatusColor}>">
                                                            <{if $item.BrandName =='非仲介成交'}>
                                                                <{$item.BrandName}>
                                                            <{else}>
                                                                <{$item.BrandName}>&nbsp;&nbsp;<{$item.BranchName}>
                                                            <{/if}>
                                                            </span>
                                                            <{if $item.BranchFeedBackStatus == 1}>
                                                                <font color="blue"><b>[無合作契約書，有回饋]</b></font>
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th width="15%">案件回饋︰</th>
                                                        <td>
                                                            <span style="background-color:#CCC;<{$item.change}>">
                                                                <{if $item.aCaseFeedback == 0}>
                                                                    回饋，金額：<{$item.aFeedBackMoney|number_format:0}>元 (<{$item.feedbackRate}>%)
                                                                <{else}>
                                                                    不回饋
                                                                <{/if}>
                                                            </span>
                                                        </td>
                                                        <th>回饋對象︰</th>
                                                        <td colspan="3">
                                                            <{if $item.aFeedbackTarget == 1}>
                                                                仲介 回饋比率<{$item.bRecall}>% 代書回饋比率<{$item.bScrRecall}>%
                                                            <{else}>
                                                                地政士 回饋比率<{$item.sRecall}>% 特殊回饋比率<{$item.sSpRecall1}>%
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <{if $item.individualMoney != ''}>
                                                    <{foreach from=$item.individualMoney key=k item=money }>
                                                    <tr>
                                                        <th width="15%">個案回饋︰</th>
                                                        <td>
                                                            <span style="background-color:#CCC;<{$item.change}>">
                                                                金額：<{$item.individualMoney.$k|number_format:0}>元 (<{$item.individualRate.$k}>%)
                                                            </span>
                                                        </td>
                                                        <th>回饋名稱︰</th>
                                                        <td colspan="3">
                                                            <{$item.individualName.$k}>(<{$item.individualRecall.$k}>%)
                                                        </td>
                                                    </tr>
                                                    <{/foreach}>
                                                    <{/if}>
                                                    <{if $item.BranchName1 != ''}>
                                                    <tr >
                                                        <th>仲介店名︰</th>
                                                        <td colspan="5">
                                                            <span style="background-color: <{$item.BranchFeedBackStatusColor1}>">
                                                            <{$item.BrandName1}>&nbsp;&nbsp;<{$item.BranchName1}> (<{$item.feedbackRate1}>%)
                                                            </span>
                                                            <{if $item.BranchFeedBackStatus1 == 1}>
                                                                <font color="blue"><b>[無合作契約書，有回饋]</b></font>
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <tr >
                                                        <th>案件回饋︰</th>
                                                        <td>
                                                            <span style="background-color:#CCC;<{$item.change1}>">	
                                                                <{if $item.aCaseFeedback1 == 0}>
                                                                    回饋，金額：<{$item.aFeedBackMoney1|number_format:0}>元
                                                                <{else}>
                                                                    不回饋
                                                                <{/if}> 
                                                            </span>
                                                        </td>
                                                        <th>回饋對象︰</th>
                                                        <td colspan="3">
                                                            <{if $item.aFeedbackTarget1 == 1}>
                                                                仲介 回饋比率<{$item.bRecall1}>% 代書回饋比率<{$item.bScrRecall1}>%
                                                            <{else}>
                                                                地政士 回饋比率<{$item.sRecall}>% 特殊回饋比率<{$item.sSpRecall1}>%
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <{if $item.individualMoney1 != ''}>
                                                    <{foreach from=$item.individualMoney1 key=k item=money }>
                                                    <tr>
                                                        <th width="15%">個案回饋︰</th>
                                                        <td>
                                                            <span style="background-color:#CCC;<{$item.change1}>">
                                                                金額：<{$item.individualMoney1.$k|number_format:0}>元 (<{$item.individualRate1.$k}>%)
                                                            </span>
                                                        </td>
                                                        <th>回饋名稱︰</th>
                                                        <td colspan="3">
                                                            <{$item.individualName1.$k}>(<{$item.individualRecall1.$k}>%)
                                                        </td>
                                                    </tr>
                                                    <{/foreach}>
                                                    <{/if}>
                                                    <{/if}>
                                                    <{if $item.BranchName2 != ''}>
                                                    <tr >
                                                        <th>仲介店名︰</th>
                                                        <td colspan="5">
                                                            <span style="background-color: <{$item.BranchFeedBackStatusColor2}>">
                                                                <{$item.BrandName2}>&nbsp;&nbsp;<{$item.BranchName2}> (<{$item.feedbackRate2}>%)
                                                            </span>
                                                            <{if $item.BranchFeedBackStatus2 == 1}>
                                                                <font color="blue"><b>[無合作契約書，有回饋]</b></font>
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <tr >
                                                        <th>案件回饋︰</th>
                                                        <td>
                                                            <span style="background-color:#CCC;<{$item.change2}>">
                                                                <{if $item.aCaseFeedback2 == 0}>
                                                                    回饋，金額：<{$item.aFeedBackMoney2|number_format:0}>元
                                                                <{else}>
                                                                    不回饋
                                                                <{/if}>
                                                            </span>
                                                        </td>
                                                        <th>回饋對象︰</th>
                                                        <td colspan="3">
                                                            <{if $item.aFeedbackTarget2 == 1}>
                                                                仲介 回饋比率<{$item.bRecall2}>% 代書回饋比率<{$item.bScrRecall2}>%
                                                            <{else}>
                                                                地政士 回饋比率<{$item.sRecall}>% 特殊回饋比率<{$item.sSpRecall1}>%
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <{if $item.individualMoney2 != ''}>
                                                    <{foreach from=$item.individualMoney2 key=k item=money }>
                                                    <tr>
                                                        <th width="15%">個案回饋︰</th>
                                                        <td>
                                                            <span style="background-color:#CCC;<{$item.change2}>">
                                                                金額：<{$item.individualMoney2.$k|number_format:0}>元 (<{$item.individualRate2.$k}>%)
                                                            </span>
                                                        </td>
                                                        <th>回饋名稱︰</th>
                                                        <td colspan="3">
                                                            <{$item.individualName2.$k}>(<{$item.individualRecall2.$k}>%)
                                                        </td>
                                                    </tr>
                                                    <{/foreach}>
                                                    <{/if}>
                                                    <{/if}>
                                                    <{if $item.BranchName3 != ''}>
                                                    <tr >
                                                        <th>仲介店名︰</th>
                                                        <td colspan="5">
                                                            <span style="background-color: <{$item.BranchFeedBackStatusColor3}>">
                                                            <{$item.BrandName3}>&nbsp;&nbsp;<{$item.BranchName3}> (<{$item.feedbackRate3}>%)
                                                            </span>
                                                            <{if $item.BranchFeedBackStatus3 == 1}>
                                                                <font color="blue"><b>[無合作契約書，有回饋]</b></font>
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <tr >
                                                        <th>案件回饋︰</th>
                                                        <td>
                                                            <span style="background-color:#CCC;<{$item.change3}>">
                                                                <{if $item.aCaseFeedback3 == 0}>
                                                                    回饋，金額：<{$item.aFeedBackMoney3|number_format:0}>元
                                                                <{else}>
                                                                    不回饋
                                                                <{/if}>
                                                            </span>
                                                        </td>

                                                        <th>回饋對象︰</th>
                                                        <td colspan="3">
                                                            <{if $item.aFeedbackTarget3 == 1}>
                                                                仲介 回饋比率<{$item.bRecall3}>% 代書回饋比率<{$item.bScrRecall3}>%
                                                            <{else}>
                                                                地政士 回饋比率<{$item.sRecall}>% 特殊回饋比率<{$item.sSpRecall1}>%
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <{if $item.individualMoney3 != ''}>
                                                    <{foreach from=$item.individualMoney3 key=k item=money }>
                                                    <tr>
                                                        <th width="15%">個案回饋︰</th>
                                                        <td>
                                                            <span style="background-color:#CCC;<{$item.change3}>">
                                                                金額：<{$item.individualMoney3.$k|number_format:0}>元 (<{$item.individualRate3.$k}>%)
                                                            </span>
                                                        </td>
                                                        <th>回饋名稱︰</th>
                                                        <td colspan="3">
                                                            <{$item.individualName3.$k}>(<{$item.individualRecall3.$k}>%)
                                                        </td>
                                                    </tr>
                                                    <{/foreach}>
                                                    <{/if}>
                                                    <{/if}>

                                                    <tr id="sp_show_mpney" style="display:<{$item.sSpRecall}>;"> 
                                                        <th>地政士事務所</th>
                                                        <td colspan="2"><{$item.sOffice}> (<{$item.feedbackRateSP}>%)</td>
                                                        <th>特殊回饋︰</td>
                                                        <td colspan="3">
                                                            <span style="background-color:#CCC;<{$item.changesp}>;">
                                                                <{$item.aScrivnerSpFeedBackMoney|number_format:0}>元
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <{if $item.otherFeedCount > 0}>
                                                    <tr>
                                                        <th colspan="6">其他回饋對象</th>                              
                                                    </tr>
                                                    <{foreach from=$item.otherFeed key=key item=item2}>
                                                    <tr>
                                                        <th>回饋對象：</th>
                                                        <td>
                                                            <{if $item2.fFeedbackTarget == 1}>
                                                                地政士 回饋比率<{$item.sRecall}>% 特殊回饋比率<{$item.sSpRecall1}>%
                                                            <{else}>
                                                                仲介 回饋比率<{$item.bRecall}>% 代書回饋比率<{$item.bScrRecall}>%
                                                            <{/if}>
                                                        </td>
                                                        <th>店名：</th>
                                                        <td><{$item2.Name}> </td>
                                                        <th>回饋金：</th>
                                                        <td>
                                                            <span style="background-color:#CCC;"><{$item2.fCaseFeedBackMoney|number_format:0}>元(<{$item2.feedbackRateOther}>%)</span>
                                                        </td>
                                                    </tr>
                                                    <{if $item2.fCaseFeedBackNote != ''}>
                                                    <tr>
                                                        <th>原因:</th>
                                                        <td colspan="5"><{$item2.fCaseFeedBackNote}></td>
                                                    </tr>
                                                    <{/if}>
                                                    <{/foreach}>
                                                    <{/if}>

                                                    <{if $item.fNote != '' || !empty($item.otherFeedDel)}>
                                                        <tr>
                                                            <th colspan="6">備註</th>                              
                                                        </tr>
                                                        <{if $item.fNote != ''}>
                                                        <tr>
                                                            <td colspan="6" style="padding-left: 10px;"><{$item.fNote}></td>
                                                        </tr>
                                                        <{/if}>
                                                        <{foreach from=$item.otherFeedDel key=key item=item2}>
                                                        <tr> 
                                                            <td style="border:2px solid red;" colspan="6">
                                                                店名：<{$item2.Code}><{$item2.Name}>；
                                                                回饋金：<{$item2.fCaseFeedBackMoney}>；
                                                                刪除原因:<{$item2.fCaseFeedBackNote}>
                                                            </td>
                                                        </tr>
                                                        <{/foreach}> 
                                                    <{/if}>
                                                    
                                                    <tr>
                                                        <th>申請者</th>
                                                        <td colspan="2"><{$item.fCreator}> (<{$item.fApplyTime}>)</td>
                                                        <th>審核者</th>
                                                        <td colspan="2">
                                                            <{if $item.fAuditor != ''}>
                                                                <{$item.fAuditor}> (<{$item.fAuditorTime}>)
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                </table>
                                                
                                                <div class="row">&nbsp;</div>
                                                <{/foreach}>

                                                <{if $max == 0}>
                                                <div style="text-align: center">查無資料</div>
                                                <{/if}>

                                                <br>
                                                <{if $max > 0}>
                                                <center>
                                                    <{if $cat == 0}>
                                                    <input type="button" value="核可" onclick="Audit()" class="bt4" style="display:;width:100px;height:35px;">
                                                    <{/if}>
                                                </center>
                                                <{/if}>
                                            </div>
                                        </form>
                                    <div>
                                    <div id="footer" style="height:50px;">
                                        <p>2012 第一建築經理股份有限公司 版權所有</p>
                                    </div>
                                </div>
				            </td>
                        </tr>
                    </table>
		        </td>
	        </tr>
        </table>
    </div>
</body>
</html>