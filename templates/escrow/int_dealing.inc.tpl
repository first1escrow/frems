<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>利息分配</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="/js/rocCal.js"></script>
<script type="text/javascript">
$(document).ready(function() {

     var close = "<{$close}>";
    var dep = "<{$smarty.session.member_pDep}>";
// alert(close);alert(dep);
    //已匯出進銷檔，所以禁止修改
    if (close =='Y' && ( dep != 9 && dep != 10 && dep != 1)) {
         var array = "input,select,textarea";
                   
        $("#result_area").find(array).each(function() {
            $(this).attr('disabled', true);
           
        }); 

    }

    if (!$('#int_list').prop('checked')) {
        $('#BankCheckList').hide() ;
    }
    $(document).keypress(function(e) {
        if (e.keyCode == 13) {
            go() ;
            //alert("Enter") ;
        }
    }) ;

    var save ="<{$save}>";

    if (save==1) {
        $('#int_total',window.parent.document).html('NT$<{$interest_total}>元') ;
        $('[name="int_total"]',window.parent.document).val("<{$interest_total}>") ;
        $('#int_money',window.parent.document).html("(已分配：<{$_bal}>元)") ;
        $('[name="int_money"]',window.parent.document).val("<{$_bal}>") ;    

    
        $('#int_show_scrivener', window.parent.document).html($("[name='scrivener_cInterestMoney']").val());

        //賣方
        var i =0;
        $(".owner_show_money").each( function() {


            var id= $(this).attr('id');

            var val = $("#"+id).val();

            $('#int_show_owner'+i, window.parent.document).html(val);

            i++;
        });
        //買方
        var i =0;
        $(".buyer_show_money").each( function() {


            var id= $(this).attr('id');

            var val = $("#"+id).val();

            $('#int_show_buyer'+i, window.parent.document).html(val);

            i++;
        });
    
        //仲介
        var i =0;
        $(".branch_show_money").each( function() {


            var id= $(this).attr('id');

            var val = $("#"+id).val();

            $('#int_show_branch'+i, window.parent.document).html(val);

            i++;
        });

        ///456789
        
        $('#dialog_save').text('利息分配資料已更新!!') ;
        $('#dialog_save').dialog({
            modal: true,
            buttons: {
                "確認": function() {
                    $(this).dialog("close") ;
                }
            }
        }) ;
    }
}) ;


 function checkInvoiceClose()
{
    var close = "<{$close}>";
    var check = 1;

    $.ajax({
        async: false, //同步處理
        url: '/includes/escrow/check_other.php',
        type: 'POST',
        dataType: 'html',
        data: {'cid': "<{$cCertifiedId}>",'type':'invoiceclose','close':close},
    })
    .done(function(txt) {
        check = txt ;
                   // 
    });
                    // alert(check);
   if (check == 'error') {
        return false;
    }else{
        return true;
    }
               

}
//儲存利息分配結果
function go() {

    if (!checkInvoiceClose()) {

         alert('此案件發票已在開立階段，頁面資料已過期請重新整理');
        return false;
        
   }

    var total_money = parseInt($('[name="total_balance"]').val()) ;
    var _already = parseInt($('#int_already').html()) ;
    if (total_money == _already) {
        $('[name="save"]').val('ok') ;
        $('[name="idealing"]').submit() ;
    }
    else {
        $('#dialog_save').text('尚有未分配利息金額!!') ;
        $('#dialog_save').dialog({
            modal: true,
            buttons: {
                "確認": function() {
                    $(this).dialog("close") ;
                }
            }
        }) ;
    }
    var interest_total = <{$interest_total}>;

    if( interest_total > 20000) {
        alert('因分配後利息超過兩萬 請重開點交單');
    }

}

//重新載入本頁
function cls() {
    $('[name="refresh_new"]').submit() ;
}

//重新計算利息分配(均分)
function redefineM(index) {
    var total_money = parseInt($('[name="total_balance"]').val()) ;
    var used_m = 0 ;        //商數
    var used_n = 0 ;        //餘數
    var _checked = 0 ;
    
    $('[name="c_checked[]"]').each(function() {
        if ($(this).prop("checked")) {
            _checked ++ ;
        }
    }) ;
    
    used_n = total_money % _checked ;
    used_m = Math.floor(total_money / _checked) ;
    var index_id = 0 ;
    $('[name="c_checked[]"]').each(function() {
        var _id = $(this).val() ;
        if ($(this).prop("checked")) {
            index_id ++ ;
            if (index_id == 1) {
                $('#Iindex'+_id).val(used_n + used_m) ;
            }
            else {
                $('#Iindex'+_id).val(used_m) ;
            }
        }
        else {
            $('#Iindex'+_id).val(0) ;
        }
        recalAll() ;
    }) ;

    ShowEmail(index);
}

function ShowEmail(index){
    //Cindex2
   


    if ($("#Cindex"+index).prop('checked') && $("#ckm"+index).val()==2) {
        $("#m"+index).removeAttr('readonly');
        $("#m"+index).attr('class', 'show');
        // $("#m"+index).css('background-color', 'orange');
        $("#m"+index).attr('placeholder', '請輸入電子信箱');

        //
    }else{
        $("#m"+index).val('');
        $("#m"+index).attr('readonly','readonly');
        $("#m"+index).attr('class', 'show1');
        $("#m"+index).attr('placeholder', '');
    }
}

//先行計算分配餘額
function recal(no) {
    var total_money = parseInt($('[name="total_balance"]').val()) ;
    var used_m = 0 ;
    
    $('input[name="c_checked[]"]').each(function() {
        var myId = $(this).val() ;
        var str = $('#Iindex'+myId).val() ; ;
        var regex = /^0+/gi ;
        str = str.replace(regex,"") ;
        
        if(str) { 
            used_m += parseInt(str) ;
            $('#Cindex'+no).prop("checked",true) ;
        }

    }) ;
    
    $('#int_already').html(used_m) ;
    $('#int_notyet').html(total_money-used_m) ;
}

//計算分配餘額
function recalAll() {
    var total_money = parseInt($('[name="total_balance"]').val()) ;
    var used_m = 0 ;
    
    $('input[name="c_checked[]"]').each(function() {
        var myId = $(this).val() ;
        var str = $('#Iindex'+myId).val() ; ;
        var regex = /^0+/gi ;
        str = str.replace(regex,"") ;
        
        if(str) { 
            used_m += parseInt(str) ;
        }

    }) ;
    
    $('#int_already').html(used_m) ;
    $('#int_notyet').html(total_money-used_m) ;
}

//是否強制產出銀行點交列表紀錄
function show_calendar() {
    $('#BankCheckList').toggle() ;
    if ($('[name="int_list"]').prop('checked')) {
        $('[name="BankCheckList"]').val("") ;
    }
    else {
        $('[name="BankCheckList"]').val('') ;
    }
}

function another_link(str,type,id)
{
    $("[name='iden']").val(str);
    $("[name='id']").val(id);
    $("[name='type']").val(type);
    $("[name='another_form']").attr('action', 'int_dealing_another.php');
    $("[name='another_form']").submit();
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
fieldset {
    border-radius: 6px;
}

.show1{
    background-color: #EBEBE4;
    
    /*
    border-color:#EBEBE4;*/
}
.show2{
    background-color: white;
}
</style>
</head>

<body style="background-color:#F8ECE9;">
<center>
    <form name="another_form" method="POST">
        <input type='hidden' name="type">
        <input type="hidden" name="iden" />
        <input type="hidden" name="id">
        <input type="hidden" name="CertifiedId" value="<{$cCertifiedId}>">
        <input type="hidden" name="cSignCategory" value="<{$cSignCategory}>">
        <input type="hidden" name="cId" value="<{$cId}>">

    </form>
<div id="result_area">

    <form name="idealing" method="POST">
    <div id="summary_area" style="width:730px;height:20px;border:1px solid #ccc;background-color:#F8ECE9;padding:20px;">
        <div>
            <div style="float:left;width:185px;text-align:right;">保證號碼：</div>
            <div style="float:left;width:180px;"><{$cCertifiedId}></div>
            <div style="float:left;width:185px;text-align:right;">總利息：</div>
            <div style="font-weight:bold;color:#000080;"><input type="hidden" name="total_balance" value="<{$interest_total}>"><span id="total_balance"><{$interest_total}></span></div>
        </div>
        <div style="float:left;width:700px;text-align:center;">
            <{if $cCertifiedId ==''}>
                <span style="font-weight:bold;color:red;">尚未產生利息!!</span>
            <{else}>
                已分利息配金額：<span id="int_already" style="font-weight:bold;color:#000080;"><{$_bal}></span>元、剩餘未分配利息金額：<span id="int_notyet" style="font-weight:bold;color:red;"><{$interest_total-$_bal}></span>元
            <{/if}>
        </div>
    </div>
    
    <div style="height:20px;font-size:9pt;width:730px;">
        <div style="float:left;width:120px;text-align:right;padding-top:18px;display: none;">
            <input id="int_list" name="int_list" type="checkbox" onclick="" <{$BankListChk}> >
            <label for="int_list">須代墊利息</label>
        </div>
        <div id="BankCheckList" style="float:left;width:160px;text-align:right;padding-top:18px;">
        代墊日期：<input type="text" name="BankCheckList" style="width:80px;height:10px;font-size:9pt;" onclick="" value="<{$bankList}>" readonly>
        </div>
        <input type="hidden" name="cCertifiedId" value="<{$cId}>">
        <input type="hidden" name="save" value="">
    </div>
<{if $_show == 1}>
        <{assign var='index' value='0'}>
        <div style="background-color:#F8ECE9;border:0px groove #ccc;padding:20px;">
            <!-- <{$records}> -->
            <fieldset style="width:80%">
                <div style="float:left;width:10%;font-size:9pt;">身份別</div>
                <div style="float:left;width:6%;font-size:9pt;">指定對象</div>
                <div style="float:left;width:20%;font-size:9pt;">姓名</div>
                <div style="float:left;width:16%;font-size:9pt;" >所得利息金額分配</div>
                <div style="float:left;width:6%;font-size:9pt;" >所得稅</div>
                <div style="float:left;width:6%;font-size:9pt;" >二代健保</div>
                <div style="font-size:9pt;">電子信箱(限公司)</div>
                <hr>
                
                <!--賣方-->
                <{assign var='o' value='1'}>
                <{assign var='iTaxTotal' value='0'}>
                <{assign var='iNHITaxTotal' value='0'}>
                <{foreach from=$data_o key=key item=item}>

                    <{if $item.cInterestMoney > 0 and $item.cInterestEdit == 'Y'}>
                        <{assign var='ck' value='checked=checked'}>
                        <{assign var='intMoney' value=$item.cInterestMoney}>
                    <{else}>
                        <{assign var='ck' value=''}>    
                        <{assign var='intMoney' value='0'}>
                    <{/if}>

                    <div style="float:left;width:10%;" ><input type="hidden" value="<{$index++}>" />賣方<{$o}></div>
                    <div style="float:left;width:6%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cId}>','賣方<{$o++}>','')">
                            <img src="../images/add.png" alt="編輯" width="18x" height="18px"  border="0">
                        </a>
                    </div>
                    <div style="float:left;width:20%;"><{$item.cName}></div>
                    <div style="float:left;width:16%;">
                        <input type="checkbox" id="Cindex<{$index}>" onclick="redefineM(<{$index}>)" name="c_checked[]"  value="<{$index}>" <{$ck}> >
                        <input type="text" class="owner_show_money" id="Iindex<{$index}>" name="owner_cInterestMoney" style="width:60px;text-align:right;" onKeyUp="recal(<{$index}>)" value="<{$intMoney}>">

                        <input type="hidden" name="owner_cId" value="<{$item.cId}>">
                    </div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iTax}></div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iNHITax}></div>
                    <div style="padding:1px;">
                        <{if $item.cEmail == ''}>
                            <{assign var='ck' value='readonly=readonly'}>
                        <{else}>
                            <{assign var='ck' value=''}>
                        <{/if}>

                        <{if $item.checkIden ==2 && $item.cEmail != ''}>
                            <{assign var='class' value='show2'}>
                        <{else}>
                            <{assign var='class' value='show1'}>
                        <{/if}>


                        <input type="text" name="owner_mail" id="m<{$index}>" value="<{$item.cEmail}>" class="<{$class}>" <{$ck}>/>
                        <input type="hidden" id="ckm<{$index}>" value="<{$item.checkIden}>"/>
                      
                    </div>
                <div style="clear: both;"></div>
                <{$iTaxTotal = $iTaxTotal + $item.iTax}>
                <{$iNHITaxTotal = $iNHITaxTotal + $item.iNHITax}>
                <{/foreach}>

                <{foreach from=$data_o2 key=key item=item}>

                <{if $item.cInterestMoney > 0 }>
                    <{assign var='ck' value='checked=checked'}>
                <{else}>
                    <{assign var='ck' value=''}>    
                <{/if}>

                <div style="float:left;width:10%;"><input type="hidden" value="<{$index++}>" />賣方<{$o}></div>
                <div style="float:left;width:6%;text-align:center;">
                    <a href="javascript:another_link('<{$item.tbl}>_<{$item.cId}>','賣方<{$o++}>','')">
                        <img src="../images/add.png" alt="編輯" width="18x" height="18px"  border="0">
                    </a>
                </div>
                <div style="float:left;width:20%;"><{$item.cName}></div>
                <div style="float:left;width:16%;">
                    <input type="checkbox" id="Cindex<{$index}>" onclick="redefineM(<{$index}>)" name="c_checked[]" value="<{$index}>" <{$ck}>>
                    <input type="text" id="Iindex<{$index}>" class="owner_show_money" name="cInterestMoney[]" style="width:60px;text-align:right;" onKeyUp="recal(<{$index}>)" value="<{$item.cInterestMoney}>">
                    <input type="hidden" name="int_cId[]" value="<{$item.cId}>">
                </div>
                <div style="float:left;width:6%;font-size:9pt;" ><{$item.iTax}></div>
                <div style="float:left;width:6%;font-size:9pt;" ><{$item.iNHITax}></div>
                <div style="padding:1px;">
                     <{if $item.cEmail == ''}>
                        <{assign var='ck' value='readonly=readonly'}>
                    <{else}>
                        <{assign var='ck' value=''}>
                    <{/if}>
                    <{if $item.checkIden ==2 && $item.cEmail != ''}>
                        <{assign var='class' value='show2'}>
                    <{else}>
                        <{assign var='class' value='show1'}>
                    <{/if}>
                        <input type="text" name="int_mail[]" id="m<{$index}>" value="<{$item.cEmail}>" class="<{$class}>" <{$ck}>/>
                        <input type="hidden" id="ckm<{$index}>" value="<{$item.checkIden}>"/>
                </div>
                <div style="clear: both;"></div>
                <{$iTaxTotal = $iTaxTotal + $item.iTax}>
                <{$iNHITaxTotal = $iNHITaxTotal + $item.iNHITax}>
                <{/foreach}>
                 <!--指定對象賣方-->
                <{assign var='o' value='1'}>
                <{foreach from=$owner_another key=key item=item}>

                    <{if $item.cInterestMoney > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>    
                    <{/if}>

                    <div style="float:left;width:10%;clear: both;" ><input type="hidden" value="<{$index++}>" /><{$item.type}></div>
                    <div style="float:left;width:6%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cTBId}>','<{$item.type}>',<{$item.cId}>)">
                            <img src="../images/pen.png" alt="編輯" width="18x" height="18px"  border="0"/>
                        </a> 
                    </div>
                    <div style="float:left;width:20%;"><{$item.cName}></div>
                    <div style="float:left;width:16%;">
                        <input type="checkbox" id="Cindex<{$index}>" onclick="redefineM(<{$index}>)" name="c_checked[]"  value="<{$index}>" <{$ck}> >
                        <input type="text" class="owner_show_money" id="Iindex<{$index}>" name="another_cInterestMoney[]" style="width:60px;text-align:right;" onKeyUp="recal(<{$index}>)" value="<{$item.cInterestMoney}>">
                        <input type="hidden" name="another_cId[]" value="<{$item.cId}>">
                    </div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iTax}></div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iNHITax}></div>
                    <div style="padding:1px;">
                        <{if $item.cEmail == ''}>
                            <{assign var='ck' value='readonly=readonly'}>
                        <{else}>
                            <{assign var='ck' value=''}>
                        <{/if}>
                        <{if $item.checkIden ==2 && $item.cEmail != ''}>
                            <{assign var='class' value='show2'}>
                        <{else}>
                            <{assign var='class' value='show1'}>
                        <{/if}>
                        <input type="text" name="another_mail[]" id="m<{$index}>" value="<{$item.cEmail}>" class="<{$class}>" <{$ck}>/>
                        <input type="hidden" id="ckm<{$index}>" value="<{$item.checkIden}>"/>

                    </div>
                <div style="clear: both;"></div>
                <{$iTaxTotal = $iTaxTotal + $item.iTax}>
                <{$iNHITaxTotal = $iNHITaxTotal + $item.iNHITax}>
                <{/foreach}>
                <!--小計-->
                <div style="float:left;width:50%;font-size:9pt;text-align:right;margin-right: 18px;color: red;" >小計</div>
                <div style="float:left;width:6%;font-size:9pt;color: red;" ><{$iTaxTotal}></div>
                <div style="float:left;width:6%;font-size:9pt;color: red;" ><{$iNHITaxTotal}></div>

                <!--指定對象賣方END-->
            </fieldset>
            <div style="height:30px;"></div>
            <!--賣方END-->
           
            <!--買方-->
            <{$iTaxTotal = 0}>
            <{$iNHITaxTotal = 0}>
            <fieldset style="width:80%;">
                <div style="float:left;width:10%;font-size:9pt;">身份別</div>
                <div style="float:left;width:6%;font-size:9pt;">指定對象</div>
                <div style="float:left;width:20%;font-size:9pt;">姓名</div>
                <div style="float:left;width:16%;font-size:9pt;">所得利息金額分配</div>
                <div style="float:left;width:6%;font-size:9pt;" >所得稅</div>
                <div style="float:left;width:6%;font-size:9pt;" >二代健保</div>
                <div style="font-size:9pt;">&nbsp;</div>
                <hr>
                <{assign var='b' value='1'}>
                <{foreach from=$data_b key=key item=item}>

                    <{if $item.cInterestMoney > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>    
                    <{/if}>
                    <div style="float:left;width:10%;"><input type="hidden" value="<{$index++}>" />買方<{$b}></div>
                    <div style="float:left;width:6%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cId}>','買方<{$b++}>','')">
                            <img src="../images/add.png" alt="編輯" width="18x" height="18px"  border="0">
                        </a>
                    </div>
                    <div style="float:left;width:20%;"><{$item.cName}></div>
                    <div style="float:left;width:16%;">
                        <input type="checkbox" id="Cindex<{$index}>" onclick="redefineM(<{$index}>)" name="c_checked[]"  value="<{$index}>" <{$ck}>>
                        <input type="text" id="Iindex<{$index}>" class="buyer_show_money" name="buyer_cInterestMoney" style="width:60px;text-align:right;" onKeyUp="recal(<{$index}>)" value="<{$item.cInterestMoney}>">
                        <input type="hidden" name="buyer_cId" value="<{$item.cId}>">
                    </div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iTax}></div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iNHITax}></div>
                    <div style="padding:1px;">
                        <{if $item.cEmail == ''}>
                            <{assign var='ck' value='readonly=readonly'}>
                        <{else}>
                            <{assign var='ck' value=''}>
                        <{/if}>
                        <{if $item.checkIden ==2 && $item.cEmail != ''}>
                            <{assign var='class' value='show2'}>
                        <{else}>
                            <{assign var='class' value='show1'}>
                        <{/if}>
                        <input type="text" name="buyer_mail" id="m<{$index}>" value="<{$item.cEmail}>" class="<{$class}>" <{$ck}>/>
                        <input type="hidden" id="ckm<{$index}>" value="<{$item.checkIden}>"/>
                    </div>
                <div style="clear: both;"></div>
                <{$iTaxTotal = $iTaxTotal + $item.iTax}>
                <{$iNHITaxTotal = $iNHITaxTotal + $item.iNHITax}>
                <{/foreach}>

                <{foreach from=$data_b2 key=key item=item}>
                    <{if $item.cInterestMoney > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>    
                    <{/if}>

                    <div style="float:left;width:10%;"><input type="hidden" value="<{$index++}>" />買方<{$b}></div>
                    <div style="float:left;width:6%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cId}>','買方<{$b++}>','')">
                            <img src="../images/add.png" alt="編輯" width="18x" height="18px"  border="0">
                        </a>
                    </div>
                    <div style="float:left;width:20%;"><{$item.cName}></div>
                    <div style="float:left;width:16%;">
                        <input type="checkbox" id="Cindex<{$index}>" onclick="redefineM(<{$index}>)" name="c_checked[]" value="<{$index}>" <{$ck}>>
                        <input type="text" id="Iindex<{$index}>" class="buyer_show_money" name="cInterestMoney[]" style="width:60px;text-align:right;" onKeyUp="recal(<{$index}>)" value="<{$item.cInterestMoney}>">
                        <input type="hidden" name="int_cId[]" value="<{$item.cId}>">
                    </div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iTax}></div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iNHITax}></div>
                    <div style="padding:1px;">
                        <{if $item.cEmail == ''}>
                            <{assign var='ck' value='readonly=readonly'}>
                        <{else}>
                            <{assign var='ck' value=''}>
                        <{/if}>
                        <{if $item.checkIden ==2 && $item.cEmail != ''}>
                            <{assign var='class' value='show2'}>
                        <{else}>
                            <{assign var='class' value='show1'}>
                        <{/if}>
                        <input type="text" name="int_email[]" id="m<{$index}>" value="<{$item.cEmail}>" class="<{$class}>" <{$ck}>/>
                        <input type="hidden" id="ckm<{$index}>" value="<{$item.checkIden}>"/>
                    </div>
                <div style="clear: both;"></div>
                <{$iTaxTotal = $iTaxTotal + $item.iTax}>
                <{$iNHITaxTotal = $iNHITaxTotal + $item.iNHITax}>
                <{/foreach}>
                 <!--指定對象買方-->
                
                <{foreach from=$buyer_another key=key item=item}>

                    <{if $item.cInterestMoney > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>    
                    <{/if}>

                    <div style="float:left;width:10%;clear: both;" ><input type="hidden" value="<{$index++}>" /><{$item.type}></div>
                    <div style="float:left;width:6%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cTBId}>','<{$item.type}>',<{$item.cId}>)">
                            <img src="../images/pen.png" alt="編輯" width="18x" height="18px"  border="0"/>
                        </a> 
                    </div>
                    <div style="float:left;width:20%;"><{$item.cName}></div>
                    <div style="float:left;width:16%;">
                        <input type="checkbox" id="Cindex<{$index}>" onclick="redefineM(<{$index}>)" name="c_checked[]"  value="<{$index}>" <{$ck}> >
                        <input type="text" class="owner_show_money" id="Iindex<{$index}>" name="another_cInterestMoney[]" style="width:60px;text-align:right;" onKeyUp="recal(<{$index}>)" value="<{$item.cInterestMoney}>">
                        <input type="hidden" name="another_cId[]" value="<{$item.cId}>">
                    </div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iTax}></div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iNHITax}></div>
                    <div style="padding:1px;">
                        <{if $item.cEmail == ''}>
                            <{assign var='ck' value='readonly=readonly'}>
                        <{else}>
                            <{assign var='ck' value=''}>
                        <{/if}>
                        <{if $item.checkIden ==2 && $item.cEmail != ''}>
                            <{assign var='class' value='show2'}>
                        <{else}>
                            <{assign var='class' value='show1'}>
                        <{/if}>
                        <input type="text" name="another_mail[]" id="m<{$index}>" value="<{$item.cEmail}>" class="<{$class}>" <{$ck}>/>
                        <input type="hidden" id="ckm<{$index}>" value="<{$item.checkIden}>"/>
                    </div>
                <div style="clear: both;"></div>
                <{$iTaxTotal = $iTaxTotal + $item.iTax}>
                <{$iNHITaxTotal = $iNHITaxTotal + $item.iNHITax}>
                <{/foreach}>
                <!--小計-->
                <div style="float:left;width:50%;font-size:9pt;text-align:right;margin-right: 18px;color: red;" >小計</div>
                <div style="float:left;width:6%;font-size:9pt;color: red;" ><{$iTaxTotal}></div>
                <div style="float:left;width:6%;font-size:9pt;color: red;" ><{$iNHITaxTotal}></div>
                <!--指定對象買方END-->
            </fieldset>
            <div style="height:30px;"></div>
            <!--買方END-->
            <!--仲介-->
            <{$iTaxTotal = 0}>
            <{$iNHITaxTotal = 0}>
            <fieldset style="width:80%;">
                <div style="float:left;width:10%;font-size:9pt;">身份別</div>
                <div style="float:left;width:6%;font-size:9pt;">指定對象</div>
                <div style="float:left;width:20%;font-size:9pt;">姓名</div>
                <div style="float:left;width:16%;font-size:9pt;">所得利息金額分配</div>
                <div style="float:left;width:6%;font-size:9pt;" >所得稅</div>
                <div style="float:left;width:6%;font-size:9pt;" >二代健保</div>
                <div style="font-size:9pt;">&nbsp;</div>
                <hr>
                <{assign var='r' value='1'}>
                <{assign var='r2' value='0'}>
                <{foreach from=$data_r key=key item=item}>
                    
                    <div style="float:left;width:10%;"><input type="hidden" value="<{$index++}>" />仲介<{$r}></div>
                    <div style="float:left;width:6%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cId}>','仲介<{$r++}>','')">
                            <img src="../images/add.png" alt="編輯" width="18x" height="18px"  border="0">
                        </a>
                    </div>
                    <div style="float:left;width:20%;"><{$item.bStore}></div>
                    <div style="float:left;width:16%;">
                        <input type="checkbox" id="Cindex<{$index}>" onclick="redefineM(<{$index}>)" name="c_checked[]" value="<{$index}>"<{$item.ck}> >
                        <input type="text" id="Iindex<{$index}>" class="branch_show_money" name="realty_cInterestMoney<{$r2}>" style="width:60px;text-align:right;" onKeyUp="recal(<{$index}>)" value="<{$item.cInterestMoney}>">
                        <input type="hidden" name="realty_cId" value="<{$item.cId}>">
                    </div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iTax}></div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iNHITax}></div>
                    <div style="padding:1px;">
                         <{if $item.cEmail == ''}>
                            <{assign var='ck' value='readonly=readonly'}>
                        <{else}>
                            <{assign var='ck' value=''}>
                        <{/if}>
                        <{if $item.checkIden ==2 && $item.cEmail != ''}>
                            <{assign var='class' value='show2'}>
                        <{else}>
                            <{assign var='class' value='show1'}>
                        <{/if}>
                        <input type="text" name="realty_email<{$r2++}>" id="m<{$index}>" value="<{$item.cEmail}>" class="<{$class}>" <{$ck}>/>
                        <input type="hidden" id="ckm<{$index}>" value="<{$item.checkIden}>"/>
                    </div>
                <div style="clear: both;"></div>
                <{$iTaxTotal = $iTaxTotal + $item.iTax}>
                <{$iNHITaxTotal = $iNHITaxTotal + $item.iNHITax}>
                <{/foreach}>
                 <!--指定對象仲介-->
               
                <{foreach from=$realty_another key=key item=item}>

                    <{if $item.cInterestMoney > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>    
                    <{/if}>

                    <div style="float:left;width:10%;" ><input type="hidden" value="<{$index++}>" /><{$item.type}></div>
                    <div style="float:left;width:6%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cTBId}>','<{$item.type}>',<{$item.cId}>)">
                            <img src="../images/pen.png" alt="編輯" width="18x" height="18px"  border="0"/>
                        </a> 
                    </div>
                    <div style="float:left;width:20%;"><{$item.cName}></div>
                    <div style="float:left;width:16%;">
                        <input type="checkbox" id="Cindex<{$index}>" onclick="redefineM(<{$index}>)" name="c_checked[]"  value="<{$index}>" <{$ck}> >
                        <input type="text" class="owner_show_money" id="Iindex<{$index}>" name="another_cInterestMoney[]" style="width:60px;text-align:right;" onKeyUp="recal(<{$index}>)" value="<{$item.cInterestMoney}>">
                        <input type="hidden" name="another_cId[]" value="<{$item.cId}>">
                    </div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iTax}></div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iNHITax}></div>
                    <div style="padding:1px;">
                        <{if $item.cEmail == ''}>
                            <{assign var='ck' value='readonly=readonly'}>
                        <{else}>
                            <{assign var='ck' value=''}>
                        <{/if}>
                        <{if $item.checkIden ==2 && $item.cEmail != ''}>
                            <{assign var='class' value='show2'}>
                        <{else}>
                            <{assign var='class' value='show1'}>
                        <{/if}>
                        <input type="text" name="another_mail[]" id="m<{$index}>" value="<{$item.cEmail}>" class="<{$class}>" <{$ck}>/>
                        <input type="hidden" id="ckm<{$index}>" value="<{$item.checkIden}>"/>
                    </div>
                <div style="clear: both;"></div>
                <{$iTaxTotal = $iTaxTotal + $item.iTax}>
                <{$iNHITaxTotal = $iNHITaxTotal + $item.iNHITax}>
                <{/foreach}>
                <!--小計-->
                <div style="float:left;width:50%;font-size:9pt;text-align:right;margin-right: 18px;color: red;" >小計</div>
                <div style="float:left;width:6%;font-size:9pt;color: red;" ><{$iTaxTotal}></div>
                <div style="float:left;width:6%;font-size:9pt;color: red;" ><{$iNHITaxTotal}></div>
                <!--指定對象仲介END-->
            </fieldset>
             <div style="height:30px;"></div>
            <!--仲介END-->
            <!--地政士-->
            <{$iTaxTotal = 0}>
            <{$iNHITaxTotal = 0}>
            <fieldset style="width:80%;">
                <div style="float:left;width:10%;font-size:9pt;">身份別</div>
                <div style="float:left;width:6%;font-size:9pt;">指定對象</div>
                <div style="float:left;width:20%;font-size:9pt;">姓名</div>
                <div style="float:left;width:16%;font-size:9pt;">所得利息金額分配</div>
                <div style="float:left;width:6%;font-size:9pt;" >所得稅</div>
                <div style="float:left;width:6%;font-size:9pt;" >二代健保</div>
                <div style="font-size:9pt;">&nbsp;</div>
                <hr>
                <{assign var='s' value='1'}>
                <{foreach from=$data_s key=key item=item}>
                    <div style="float:left;width:10%;"><input type="hidden" value="<{$index++}>" />地政士<{$s}></div>
                    <div style="float:left;width:6%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cId}>','地政士<{$s++}>','')">
                            <img src="../images/add.png" alt="編輯" width="18x" height="18px"  border="0">
                        </a>
                    </div>
                    <div style="float:left;width:20%;"><{$item.sName}></div>
                    <div style="float:left;width:16%;">
                        <input type="checkbox" id="Cindex<{$index}>" onclick="redefineM(<{$index}>)" name="c_checked[]" value="<{$index}>" <{$item.ck}>>
                        <input type="text" id="Iindex<{$index}>"  name="scrivener_cInterestMoney" style="width:60px;text-align:right;" onKeyUp="recal(<{$index}>)" value="<{$item.cInterestMoney}>">
                        <input type="hidden" name="scrivener_cId" value="<{$item.cId}>">
                    </div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iTax}></div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iNHITax}></div>
                    <div style="padding:1px;">
                       <{if $item.cEmail == ''}>
                            <{assign var='ck' value='readonly=readonly'}>
                        <{else}>
                            <{assign var='ck' value=''}>
                        <{/if}>
                        <{if $item.checkIden ==2 && $item.cEmail != ''}>
                            <{assign var='class' value='show2'}>
                        <{else}>
                            <{assign var='class' value='show1'}>
                        <{/if}>
                        <input type="text" name="scrivener_mail" id="m<{$index}>" value="<{$item.cEmail}>" class="<{$class}>" <{$ck}>/>
                        <input type="hidden" id="ckm<{$index}>" value="<{$item.checkIden}>"/>
                    </div>
                <div style="clear: both;"></div>
                <{$iTaxTotal = $iTaxTotal + $item.iTax}>
                <{$iNHITaxTotal = $iNHITaxTotal + $item.iNHITax}>
                <{/foreach}>
                 <!--指定對象地政士-->
                <{assign var='o' value='1'}>
                <{foreach from=$scr_another key=key item=item}>

                    <{if $item.cInterestMoney > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>    
                    <{/if}>

                    <div style="float:left;width:10%;" ><input type="hidden" value="<{$index++}>" /><{$item.type}></div>
                    <div style="float:left;width:6%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cTBId}>','<{$item.type}>',<{$item.cId}>)">
                            <img src="../images/pen.png" alt="編輯" width="18x" height="18px"  border="0"/>
                        </a> 
                    </div>
                    <div style="float:left;width:20%;"><{$item.cName}></div>
                    <div style="float:left;width:16%;">
                        <input type="checkbox" id="Cindex<{$index}>" onclick="redefineM(<{$index}>)" name="c_checked[]"  value="<{$index}>" <{$ck}> >
                        <input type="text" class="owner_show_money" id="Iindex<{$index}>" name="another_cInterestMoney[]" style="width:60px;text-align:right;" onKeyUp="recal(<{$index}>)" value="<{$item.cInterestMoney}>">
                        <input type="hidden" name="another_cId[]" value="<{$item.cId}>">
                    </div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iTax}></div>
                    <div style="float:left;width:6%;font-size:9pt;" ><{$item.iNHITax}></div>
                    <div style="padding:1px;">
                        <{if $item.cEmail == ''}>
                            <{assign var='ck' value='readonly=readonly'}>
                        <{else}>
                            <{assign var='ck' value=''}>
                        <{/if}>
                        <{if $item.checkIden ==2 && $item.cEmail != ''}>
                            <{assign var='class' value='show2'}>
                        <{else}>
                            <{assign var='class' value='show1'}>
                        <{/if}>
                        <input type="text" name="another_mail[]" id="m<{$index}>" value="<{$item.cEmail}>" class="<{$class}>" <{$ck}>/>
                        <input type="hidden" id="ckm<{$index}>" value="<{$item.checkIden}>"/>
                    </div>
                <div style="clear: both;"></div>
                <{$iTaxTotal = $iTaxTotal + $item.iTax}>
                <{$iNHITaxTotal = $iNHITaxTotal + $item.iNHITax}>
                <{/foreach}>
                <!--小計-->
                <div style="float:left;width:50%;font-size:9pt;text-align:right;margin-right: 18px;color: red;" >小計</div>
                <div style="float:left;width:6%;font-size:9pt;color: red;" ><{$iTaxTotal}></div>
                <div style="float:left;width:6%;font-size:9pt;color: red;" ><{$iNHITaxTotal}></div>
                <!--指定對象地政士END-->
                </fieldset>
            <div style="height:30px;"></div>
            <!--地政士END-->
            <div style="height:30px;"></div>
           
            <div style="width:80%;padding:20 40 20 40;text-align:right;">
                <{if $cSignCategory==1}>
                        <input type="button" onclick="go()" value="　更新　">
                        <input type="button" onclick="cls()" value="　重填　">
                <{/if}>
                    
            </div>
        </div>
<{/if}>
    </form>
    <form name="refresh_new" method="POST">
        <input type="hidden" name="cCertifiedId" value="<{$cId}>">
    </form>
</div>
<div id="dialog_save">
</div>
</center>
</body>
</html>