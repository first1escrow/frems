<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>開發票對象</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="/js/ROCcalender_limit.js"></script>
<script type="text/javascript" src="/js/rocCal.js"></script>
<script src="/js/jquery.colorbox.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var close = '<{$InvoiceClose|default:''}>';
    var dep = '<{$smarty.session.member_pDep|default:''}>';
// alert(close);alert(dep);
    //已匯出進銷檔，所以禁止修改
    if (close =='Y' && ( dep != 9 && dep != 10 )) { //&& dep != 1
         var array = "input,select,textarea";
                   
        $("#result_area").find(array).each(function() {
            $(this).attr('disabled', true);
           
        }); 

    }
    //  
    totalCount() ;
    $(document).keypress(function(e) {

        if (e.keyCode == 13) {
            go() ;
            //alert("Enter") ;
        }
    }) ;

    //賣方全選
    $("#select_owner").click(function (){
        if($('#ownerCheckbox').prop("checked") == false) {
            return false;
        }
        if($("#select_owner").prop("checked")) {
            $("input[name='Owner_chk[]']").each(function() {
                $(this).prop("checked", true);
                item_split();
            });
        } else {
            $("input[name='Owner_chk[]']").each(function() {
                $(this).prop("checked", false);
                item_split();
            });
        }
    });

    //買方全選
    $("#select_buyer").click(function (){
        if($('#buyerCheckbox').prop("checked") == false) {
            return false;
        }
        if($("#select_buyer").prop("checked")) {
            $("input[name='Buyer_chk[]']").each(function() {
                $(this).prop("checked", true);
                item_split();
            });
        } else {
            $("input[name='Buyer_chk[]']").each(function() {
                $(this).prop("checked", false);
                item_split();
            });
        }
    });
}) ;




//儲存開發票對象資料
function go() {
    var alreadyMoney = $("#alreadyMoney").text();
    var cCertifiedMoney = $("#cCertifiedMoney").text();

    if (!checkInvoiceClose()) {

        alert('此案件發票已在開立階段，頁面資料已過期請重新整理');
        return false;
        
   }

    if (alreadyMoney == cCertifiedMoney) {
        $('#dialog_save').text('開發票對象資料已更新!!') ;
        $('#dialog_save').dialog({
            modal: true,
            buttons: {
                "確認": function() {
                    $(this).dialog("close") ;
                    go2();

                }
            }
        }) ;

    }else{
        alert("無法儲存，已分配金額不等於履保費金額");
    }

   

}
function go2()
{
    if (!checkInvoiceClose()) {

         alert('此案件發票已在開立階段，頁面資料已過期請重新整理');
        return false;
   }

    $('[name="save"]').val('ok') ;
    $('[name="idealing"]').submit() ;



}

 function checkInvoiceClose()
{
    var close = '<{$close|default:''}>';
    var check = 1;

    $.ajax({
        async: false, //同步處理
        url: '/includes/escrow/check_other.php',
        type: 'POST',
        dataType: 'html',
        data: {'cid': '<{$cCertifiedId|default:''}>','type':'invoiceclose','close':close},
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

//重新分配大項
function category_split() {
    console.log('category_split function called'); // 調試日誌
    var cer = $('[name="total_cCertifiedMoney"]').val() ;           //保證費
    var arr = new Array() ;
    var index = 0 ;
    
    //計算大項分母
    $('[name="cInvChk[]"]').each(function() {
        var tag = $(this).val() ;
        
        if ($(this).prop('checked')==true) {
            arr[index] = tag ;
            index = index + 1 ;
        }
        
        $('#'+tag).html(0) ;
        $('[name="'+tag+'"]').val(0) ;
    }) ;
    
    var m = cer % index ;                   //餘數
    var n = Math.floor(cer / index) ;       //商數
    m = m + n ;                             //首位被選取的金額
    
    for (var i = 0 ; i < arr.length ; i ++) {
        var tag = arr[i] ;
        
        if (i == 0) { var x = m ; }
        else { var x = n ; }
        
        $('#'+tag).html(x) ;
        $('[name="'+tag+'"]').val(x) ;
    }
    
    //計算小項
    $('[name="cInvChk[]"]').each(function() {
        item_split() ;
    }) ;
}

//重新分配各小項
function item_split() { 
    item_dollar('cInvoiceOwner','Owner_chk','owner_donate') ;              //賣方金額分配
    item_dollar('cInvoiceBuyer','Buyer_chk','buyer_donate') ;              //買方金額分配
    item_dollar('cInvoiceRealestate','Realestate_chk','branch_donate') ;    //仲介金額分配

    var ck = '<{$data_scrivener_another.cId|default:''}>'.toString();
    
    if (ck !='') {
        if ($('[value="cInvoiceScrivener"]').prop('checked')) {
            $('#scr_ck').prop('checked',true) ;
        }
    }

    


    item_dollar('cInvoiceScrivener','Scrivener_chk','scr_invdonate') ;      //代書
    

    if ($('[value="cInvoiceOther"]').prop('checked')) {
        $('[name="Other_chk[]"]').prop('checked',true) ;
    }

    item_dollar('cInvoiceOther','Other_chk') ;              //捐款
}

//平均分配小項金額
function item_dollar(iMoney,iChk,donate) {
    var arr = new Array() ;
    var index = 0 ;
    var ow = $('[name="'+iMoney+'"]').val() ;           //單項分配到的保證費
    
     var c =1;
    
    $('[name="'+iChk+'[]"]').each(function() {
        var tag = $(this).val() ;

        
        if ($(this).prop('checked')==true) {
            arr[index] = tag ;
            index = index + 1 ;
            
        }else{
            $("#"+donate+c).removeAttr('checked');
        }
        $('#'+tag).val(0) ;

        c++;

    }) ;
    
    var m = ow % index ;
    var n = Math.floor(ow / index) ;
    m = m + n ;
    
    for (var i = 0 ; i < arr.length ; i ++) {
        var tag = arr[i] ;
        
        if (i == 0) { var x = m ; }
        else { var x = n ; }
        
        $('#'+tag).val(x) ;
    }
    money_count(iChk) ;
}

//計算小計金額
function money_count(str) {
    var total = 0 ;
    var tmp = str.split('_') ;
    var cInvoice = 'cInvoice' + tmp[0] ;
    //alert(cInvoice) ;
    
    $('[name="'+str+'[]"]').each(function() {
        var tag = $(this).val() ;
        //alert(cInvoice + '=' + $('[name="'+cInvoice+'"]').prop('checked')) ;
        if ($('[value="'+cInvoice+'"]').prop('checked')) {
            //若輸入金額為 "0" 則取消勾選
            if ($('#'+tag).val() == '0') {
                $(this).prop('checked',false) ;
            }
            else {
                $(this).prop('checked',true) ;
                total = total + parseInt($('#'+tag).val()) ;
            }
        }
        else {
            $(this).prop('checked',false) ;
            $('#'+tag).val(0) ;
        }

    }) ;
    $('#'+str+'_count').html(total) ;
    if (total > 0) {
        $('#'+cInvoice).html(total) ;
        $('[name="'+cInvoice+'"]').val(total) ;
    }
    totalCount() ;
}

//重新載入本頁
function cls() {
    $('[name="refresh_new"]').submit() ;
}

//合計小計金額
function totalCount() {
    var T = 0
    var o = parseInt($('#Owner_chk_count').html()) ;
    var b = parseInt($('#Buyer_chk_count').html()) ;
    var r = parseInt($('#Realestate_chk_count').html()) ;
    var s = parseInt($('#Scrivener_chk_count').html()) ;
    // var f = parseInt($('#Other_chk_count').html()) ;
    // alert($('#Other_chk_count').html());
    var f = 0 ;
    if ($('#Other_chk_count').html()) f = parseInt($('#Other_chk_count').html()) ;
    // alert('o='+o+',b='+b+',r='+r+',s='+s+',f='+f) ;
    T = T + o + b + r + s + f ;
    $('#alreadyMoney').html(T) ;
}


function another_link(str,type,id)
{
    $("[name='iden']").val(str);
    $("[name='id']").val(id);
    $("[name='type']").val(type);
    $("[name='another_form']").attr('action', 'inv_dealing_another.php');
    $("[name='another_form']").submit();
}

// 確認函數已定義
console.log('Functions defined:', {
    category_split: typeof category_split,
    item_split: typeof item_split,
    go: typeof go
});

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
input {
    text-align:right;
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

    <div id="summary_area" style="width:730px;border:1px solid #ccc;background-color:#F8ECE9;padding:20px;">
        <div>
            <div style="float:left;font-weight:bold;color:red;margin-top:-10px;padding-bottom:10px;"><{$certify_money_notice}></div>
            <div style="clear:both;"></div>

            <div style="float:left;width:175px;text-align:right;">保證號碼：</div>
            <div style="float:left;width:175px;"><{$cCertifiedId}></div>
            <div style="float:left;width:175px;text-align:right;">履保費金額：<br>已分配金額：</div>
            <div style="font-weight:bold;color:#000080;">
                <input type="hidden" name="total_cCertifiedMoney" value="<{$latestCertifiedMoney}>">
                <span id="cCertifiedMoney"><{$latestCertifiedMoney}></span><br>
                <span id="alreadyMoney">0</span>
            </div>
            <div style="clear:both;"></div>
            <!-- 
            <div style="float:left;width:175px;text-align:right;">&nbsp;</div>
            <div style="float:left;width:175px;">&nbsp;</div>
            <div style="float:left;width:175px;text-align:right;">已分配金額：</div>
            <div style="font-weight:bold;color:#006400;"><span id="alreadyMoney">0</span></div> -->
        </div>
    </div>
    
    <div style="height:20px;">
        <div style="font-weight:bold;color:red;">
        <{$inv_dealing_non}>
    </div>
        
    </div>
   
    <form name="idealing" method="POST">
        <input type="hidden" name="cCertifiedId" value="<{$cId}>">
        <input type="hidden" name="save" value="">
        <div style="background-color:#F8ECE9;padding:20px;">

        <!--賣方顯示-->

        <fieldset style="width:80%;">
                <legend align="left" style="">
                    <{if $cInvoiceOwner > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>
                    <{/if}>

                    <input name="cInvChk[]" type="checkbox" id="ownerCheckbox" onclick="category_split()" value="cInvoiceOwner" <{$ck}>>
                    賣方
                    (NT.$&nbsp;<span id="cInvoiceOwner"><{$cInvoiceOwner}></span>)
                    <input type="hidden" name="cInvoiceOwner" value="<{$cInvoiceOwner}>">
                </legend>
                <div style="float:left;width:10%;font-size:9pt;">身份別</div>
                <div style="float:left;width:45%;text-align:left;font-size:9pt;">姓名</div>
                <div style="float:left;width:10%;text-align:center;font-size:9pt;">指定發票對象</div>
                <div style="float:left;width:10%;text-align:center;font-size:9pt;">發票捐贈</div>
                <div style="float:left;width:10%;text-align:center;font-size:9pt;">列印發票</div>
                <div style="font-size:9pt;"><input type="checkbox" id="select_owner">開發票對象金額分配</div>
                <hr>

                <{assign var='i' value='1'}>
                <{foreach from=$data_owner key=key item=item}>

                    <{if $item.cInvoiceDonate == 1 }>
                        <{assign var='ck2' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck2' value=''}>
                    <{/if}>

                    <{if $item.cInvoicePrint == 'Y' }>
                        <{assign var='ck3' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck3' value=''}>
                    <{/if}>

                    <!-- {$variable|count_characters} -->
                <div style="float:left;width:10%;clear: both;">賣方&nbsp;<{$i}></div>
                <div style="float:left;width:45%;text-align:left;"><{$item.cName}>&nbsp;</div>
                <div style="float:left;width:10%;text-align:center;">
                    <a href="javascript:another_link('<{$item.tbl}>_<{$item.cId}>','<{$item.type}>','')"  >
                        <img src="../images/add.png" alt="編輯" width="18x" height="18px" border="0" />
                    </a> 
                </div>
                <div style="float:left;width:10%;text-align:center;">
                   
                    <input type="checkbox" name="owner_donate[<{$item.cId}>]"  id="owner_donate<{$i}>" value="1" <{$item.donate}> <{$ck2}> >
                </div>
                <div style="float:left;width:10%;text-align:center;">
                    <input type="checkbox" name="owner_print[<{$item.cId}>]"  id="owner_print<{$i}>" value="Y"  <{$ck3}> >
                </div>
                <div style="padding:0px;">
                     <{if $item.cInvoiceMoney > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>
                    <{/if}>

                    <input type="checkbox" name="Owner_chk[]" onclick="item_split()" value="owner<{$i}>" <{$ck}> >
                    <input type="text" class="owner_show_money" id="owner<{$i++}>" name="owner_inv[]" style="width:100px;" onKeyup="money_count('Owner_chk')" value="<{$item.cInvoiceMoney}>">
                    <input type="hidden" name="owner_cId[]" value="<{$item.cId}>">
                    <input type="hidden" name="owner_first[]" value="<{$item.first}>">
            
                </div>
                <hr>
                <{/foreach}>
                <!--賣方指定人-->
                 
                <{foreach from=$data_owner_another key=key item=item}>
                    
                   <{if $item.cInvoiceDonate == 1 }>
                        <{assign var='ck2' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck2' value=''}>
                    <{/if}>

                    <{if $item.cInvoicePrint == 'Y' }>
                        <{assign var='ck3' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck3' value=''}>
                    <{/if}>

                    <div style="float:left;width:10%;"><{$item.type}></div>
                    <div style="float:left;width:45%;text-align:left;"><{$item.cName}>&nbsp;</div>
                    <div style="float:left;width:10%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cTBId}>','<{$item.type}>',<{$item.cId}>)" >
                            <img src="../images/pen.png" alt="編輯" width="18x" height="18px" border="0"/>
                        </a> 
                    </div>
                    <div style="float:left;width:10%;text-align:center;">

                        <input type="checkbox" name="another_donate[<{$item.cId}>]" id="owner_donate<{$i}>" value="1" <{$ck2}>>
                    </div>

                    <div style="float:left;width:10%;text-align:center;">

                        <input type="checkbox" name="another_print[<{$item.cId}>]" id="owner_print<{$i}>" value="Y" <{$ck3}>> 
                    </div>

                    <div style="padding:0px;">
                         <{if $item.cInvoiceMoney > 0 }>
                            <{assign var='ck' value='checked=checked'}>
                        <{else}>
                            <{assign var='ck' value=''}>
                        <{/if}>

                        <input type="checkbox" name="Owner_chk[]" onclick="item_split()" value="owner<{$i}>"  <{$ck}> >
                        <input type="text" class="owner_show_money" id="owner<{$i++}>" name="another_inv[]" style="width:100px;" onKeyup="money_count('Owner_chk')" value="<{$item.cInvoiceMoney}>">
                        <input type="hidden" name="another_cId[]" value="<{$item.cId}>">
                        <!-- <input type="hidden" name="another_first[]" value="<{$item.first}>"> -->
                
                    </div>
                    <hr>
                <{/foreach}>


                <div style="float:left;text-align:right;width:85%;">小計</div>
                <div id='Owner_chk_count' style="text-align:right;"><{$owner_total}></div>
        </fieldset>
        <div style="height:30px;"></div>

            <fieldset style="width:80%;">
                <legend align="left" style="">
                    
                    <{if $cInvoiceBuyer > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>
                    <{/if}>
                    <input type="checkbox" name="cInvChk[]" id="buyerCheckbox" onclick="category_split()" value="cInvoiceBuyer" <{$ck}>>
                    買方
                    (NT.$&nbsp;<span id="cInvoiceBuyer"><{$cInvoiceBuyer}></span>)
                    <input type="hidden" name="cInvoiceBuyer" value="<{$cInvoiceBuyer}>">
                </legend>
                <div style="float:left;width:10%;font-size:9pt;">身份別</div>
                <div style="float:left;width:45%;text-align:left;font-size:9pt;">姓名</div>
                <div style="float:left;width:10%;text-align:cenrer;font-size:9pt;">指定發票對象</div>
                <div style="float:left;width:10%;text-align:cenrer;font-size:9pt;">發票捐贈</div>
                <div style="float:left;width:10%;text-align:center;font-size:9pt;">列印發票</div>
                <div style="font-size:9pt;"><input type="checkbox" id="select_buyer">開發票對象金額分配</div>
                <hr>
                 <{assign var='i' value='1'}>
                <{foreach from=$data_buyer key=key item=item}>
                   
                     <{if $item.cInvoiceDonate == 1 }>
                        <{assign var='ck2' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck2' value=''}>
                    <{/if}>

                    <{if $item.cInvoiceMoney > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>
                    <{/if}>

                    <{if $item.cInvoicePrint == 'Y' }>
                        <{assign var='ck3' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck3' value=''}>
                    <{/if}>
                <div style="float:left;width:10%;">買方&nbsp;<{$i}></div>
                <div style="float:left;width:45%;text-align:left;"><{$item.cName}>&nbsp;</div>
                 <div style="float:left;width:10%;text-align:center;">
                    <a href="javascript:another_link('<{$item.tbl}>_<{$item.cId}>','<{$item.type}>','')" >
                        <img src="../images/add.png" alt="編輯" width="18x" height="18px" border="0"/>
                    </a> 
                </div>
                <div style="float:left;width:10%;text-align:cenrer;">
                    <input type="checkbox"  name="buyer_donate[<{$item.cId}>]" id="buyer_donate<{$i}>" value="1" <{$ck2}> <{$item.donate}>>
                </div>
                <div style="float:left;width:10%;text-align:cenrer;">
                    <input type="checkbox"  name="buyer_print[<{$item.cId}>]" id="buyer_print<{$i}>" value="Y" <{$ck3}> >
                </div>
                <div style="padding:0px;">
                    <input type="checkbox" name="Buyer_chk[]" onclick="item_split()" value="buyer<{$i}>" <{$ck}>>
                    <input type="text" class="buyer_show_money" id="buyer<{$i++}>" name="buyer_inv[]" style="width:100px;" onKeyup="money_count('Buyer_chk')" value="<{$item.cInvoiceMoney}>">
                    <input type="hidden" name="buyer_cId[]" value="<{$item.cId}>">
                    <input type="hidden" name="buyer_first[]" value="<{$item.first}>">
                </div>
                <hr>
                <{/foreach}>
                 <{foreach from=$data_buyer_another key=key item=item}>
                    
                   <{if $item.cInvoiceDonate == 1 }>
                        <{assign var='ck2' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck2' value=''}>
                    <{/if}>

                    <{if $item.cInvoicePrint == 'Y' }>
                        <{assign var='ck3' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck3' value=''}>
                    <{/if}>

                    <div style="float:left;width:10%;"><{$item.type}></div>
                    <div style="float:left;width:45%;text-align:left;"><{$item.cName}>&nbsp;</div>
                    <div style="float:left;width:10%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cTBId}>','<{$item.type}>',<{$item.cId}>)" >
                            <img src="../images/pen.png" alt="編輯" width="18x" height="18px" border="0"/>
                        </a> 
                    </div>
                    <div style="float:left;width:10%;text-align:center;">

                        <input type="checkbox" name="another_donate[<{$item.cId}>]" value="1" id="buyer_donate<{$i}>" <{$ck2}>>
                    </div>
                    <div style="float:left;width:10%;text-align:center;">

                        <input type="checkbox" name="another_print[<{$item.cId}>]" value="Y"  <{$ck3}>>
                    </div>
                    <div style="padding:0px;">
                         <{if $item.cInvoiceMoney > 0 }>
                            <{assign var='ck' value='checked=checked'}>
                        <{else}>
                            <{assign var='ck' value=''}>
                        <{/if}>

                        <input type="checkbox" name="Buyer_chk[]" onclick="item_split()" value="buyer<{$i}>"  <{$ck}> >
                        <input type="text" class="buyer_show_money" id="buyer<{$i++}>" name="another_inv[]" style="width:100px;" onKeyup="money_count('Buyer_chk')" value="<{$item.cInvoiceMoney}>">
                        <input type="hidden" name="another_cId[]" value="<{$item.cId}>">
                        <!-- <input type="hidden" name="another_first[]" value="<{$item.first}>"> -->
                
                    </div>
                    <hr>
                <{/foreach}>
                <div style="float:left;width:85%;text-align:right;">小計</div>
                <div id='Buyer_chk_count' style="text-align:right;"><{$buyer_total}></div>
            </fieldset>
            <div style="height:30px;"></div>

            <fieldset style="width:80%;">
                <legend align="left" style="">

                    <{if $cInvoiceRealestate > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>
                    <{/if}>
                    <input type="checkbox" name="cInvChk[]" onclick="category_split()" value="cInvoiceRealestate" <{$ck}> >
                    仲介
                    (NT.$&nbsp;<span id="cInvoiceRealestate"><{$cInvoiceRealestate}></span>)
                    <input type="hidden" name="cInvoiceRealestate" value="<{$cInvoiceRealestate}>">
                </legend>
                <div style="float:left;width:10%;font-size:9pt;">身份別</div>
                <div style="float:left;width:45%;text-align:left;font-size:9pt;">姓名</div>
                <div style="float:left;width:10%;text-align:center;font-size:9pt;">指定發票對象</div>
                <div style="float:left;width:10%;text-align:center;font-size:9pt;">發票捐贈</div>
                <div style="float:left;width:10%;text-align:center;font-size:9pt;">列印發票</div>
                <div style="font-size:9pt;">開發票對象金額分配</div>
                <hr>
                <{assign var='i' value='1'}>
                <{foreach from=$data_realty key=key item=item}>
                    
                    <{if $item.cInvoiceDonate == 1 }>
                        <{assign var='ck2' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck2' value=''}>
                    <{/if}>
                    <{if $item.bInvoiceMoney > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>
                    <{/if}>
                    <{if $item.cInvoicePrint == 'Y' }>
                        <{assign var='ck3' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck3' value=''}>
                    <{/if}>
                <div style="float:left;width:10%;">仲介&nbsp;<{$i}></div>
                <div style="float:left;width:45%;text-align:left;"><{$item.bStore}>&nbsp;</div>
                <div style="float:left;width:10%;text-align:center;">
                    <a href="javascript:another_link('<{$item.tbl}>_<{$item.cId}>','<{$item.type}>','')">
                        <img src="../images/add.png" alt="編輯" width="18x" height="18px" border="0"/>
                    </a> 
                </div>
                <div style="float:left;width:10%;text-align:center;">
                    <input type="checkbox" id="branch_donate<{$i}>" name="branch_donate[<{$item.bId}>]" value="1" <{$ck2}> <{$item.donate}>> 
                </div>
                 <div style="float:left;width:10%;text-align:center;">
                    <input type="checkbox" id="branch_print<{$i}>" name="branch_print[<{$item.bId}>]" value="Y" <{$ck3}> > 
                </div>
                <div style="padding:0px;">
                    <input type="checkbox" name="Realestate_chk[]"  onclick="item_split()" value="realty<{$i}>" <{$ck}>>
                    <input type="text" class="branch_show_money" id="realty<{$i++}>" name="realty_inv[]" style="width:100px;" onKeyup="money_count('Realestate_chk')" value="<{$item.bInvoiceMoney}>">
                    <input type="hidden" name="realty_bId[]" value="<{$item.bId}>">
                    <input type="hidden" name="realty_first[]" value="<{$item.first}>">
                </div>
                <{/foreach}>

                 <{foreach from=$data_realty_another key=key item=item}>
                    
                    <{if $item.cInvoiceDonate == 1 }>
                        <{assign var='ck2' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck2' value=''}>
                    <{/if}>
                    <{if $item.cInvoiceMoney > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>
                    <{/if}>
                    <{if $item.cInvoicePrint == 'Y' }>
                        <{assign var='ck3' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck3' value=''}>
                    <{/if}>
                <div style="float:left;width:10%;"><{$item.type}></div>
                <div style="float:left;width:45%;text-align:left;"><{$item.cName}>&nbsp;</div>
                 <div style="float:left;width:10%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cTBId}>','<{$item.type}>',<{$item.cId}>)">
                            <img src="../images/pen.png" alt="編輯" width="18x" height="18px" border="0"/>
                        </a> 
                    </div>
                <div style="float:left;width:10%;text-align:center;">
                    <input type="checkbox" id="branch_donate<{$i}>" name="another_donate[<{$item.cId}>]" value="1" <{$ck2}> <{$item.donate}>> 
                </div>
                <div style="float:left;width:10%;text-align:center;">
                    <input type="checkbox" id="branch_print<{$i}>" name="another_print[<{$item.cId}>]" value="Y" <{$ck3}> > 
                </div>
                <div style="padding:0px;">
                    <input type="checkbox" name="Realestate_chk[]"  onclick="item_split()" value="realty<{$i}>" <{$ck}>>
                    <input type="text" class="branch_show_money" id="realty<{$i++}>" name="another_inv[]" style="width:100px;" onKeyup="money_count('Realestate_chk')" value="<{$item.cInvoiceMoney}>">
                    <input type="hidden" name="another_cId[]" value="<{$item.cId}>">
                   
                </div>
                <{/foreach}>
                <hr>
                <div style="float:left;width:85%;text-align:right;">小計</div>
                <div id='Realestate_chk_count' style="text-align:right"><{$realty_total}></div>
            </fieldset>

            <div style="height:30px;"></div>

            <fieldset style="width:80%;">

                     <{if $data_scrivener.cInvoiceDonate == 1 }>
                        <{assign var='ck2' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck2' value=''}>
                    <{/if}>

                    <{if $cInvoiceScrivener > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>
                    <{/if}>

                    <{if $data_scrivener.cInvoiceTo ==2 }>
                        <{assign var='ck4' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck3' value='checked=checked'}>
                    <{/if}>

                    <{if $data_scrivener.cInvoicePrint == 'Y' }>
                        <{assign var='ck5' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck5' value=''}>
                    <{/if}>

                     <{if $data_scrivener.cInvoiceMoney > 0 }>
                        <{assign var='ckinv' value='checked=checked'}>
                    <{else}>
                        <{assign var='ckinv' value=''}>
                    <{/if}>
                    

                <legend align="left" style="">
                    <input type="checkbox" name="cInvChk[]" onclick="category_split()" value="cInvoiceScrivener" <{$ck}>>
                    地政士
                    (NT.$&nbsp;<span id="cInvoiceScrivener"><{$cInvoiceScrivener}></span>)
                    <input type="hidden" name="cInvoiceScrivener" value="<{$cInvoiceScrivener}>">
                </legend>
                <div style="float:left;width:10%;font-size:9pt;">身份別</div>
                <div style="float:left;width:20%;text-align:left;font-size:9pt;">姓名</div>
                <div style="float:left;width:10%;text-align:center;font-size:9pt;">指定對象</div>
                <div style="float:left;width:20%;text-align:left;font-size:9pt;">發票對象</div>
                <div style="float:left;width:10%;text-align:center;font-size:9pt;">發票捐贈</div>
                <div style="float:left;width:10%;text-align:center;font-size:9pt;">列印發票</div>
                <div style="font-size:9pt;">開發票對象金額分配</div>
                <hr>

                <div style="float:left;width:10%;">地政士&nbsp;</div>
                <div style="float:left;width:20%;text-align:left;"><{$data_scrivener.sName}>&nbsp;</div>
                <div style="float:left;width:10%;text-align:center;">
                    <a href="javascript:another_link('<{$data_scrivener.tbl}>_<{$data_scrivener.cId}>','地政士','')">
                        <img src="../images/add.png" alt="編輯" width="18x" height="18px" border="0"/>
                    </a> 
                </div> 
                <div style="float:left;width:20%;text-align:left;">
                <input type="radio"name="scrivener_personal[]" value="1" <{$ck3}> >個人
                <input type="radio" name="scrivener_personal[]" value="2" <{$ck4}> />事務所
                  
                </div> 
                <div style="float:left;width:10%;text-align:center;">
                    <input type="checkbox" name="scrivener_donate[]" id="scr_invdonate1" value="1" <{$ck2}>>
                </div>
                <div style="float:left;width:10%;text-align:center;">
                    <input type="checkbox" name="scrivener_print[]" id="scr_print1" value="Y" <{$ck5}>>
                </div>
                <div style="padding:0px;">
                    <input type="checkbox" name="Scrivener_chk[]" onclick="item_split()" value="scr1" id="scr_ck"  <{$ckinv}>>
                    <input type="text" id="scr1" class="scrivener_show_money" name="scrivener_inv[]" style="width:100px;" onKeyup="money_count('Scrivener_chk')" value="<{$data_scrivener.cInvoiceMoney}>">
                    <input type="hidden" name="scrivener_sId" value="$data_scrivener.sId">
                </div>
                <{assign var='i' value='2'}>
                <{foreach from=$data_scrivener_another key=key item=item}>
                    
                    <{if $item.cInvoiceDonate == 1 }>
                        <{assign var='ck2' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck2' value=''}>
                    <{/if}>
                    <{if $item.cInvoiceMoney > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>
                    <{/if}>

                    <{if $item.cInvoicePrint == 'Y' }>
                        <{assign var='ck3' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck3' value=''}>
                    <{/if}>
                <div style="float:left;width:10%;"><{$item.type}></div>
                <div style="float:left;width:20%;text-align:left;"><{$item.cName}>&nbsp;</div>
                 <div style="float:left;width:10%;text-align:center;">
                        <a href="javascript:another_link('<{$item.tbl}>_<{$item.cTBId}>','<{$item.type}>',<{$item.cId}>)" border="0">
                            <img src="../images/pen.png" alt="編輯" width="18x" height="18px"  border="0"/>
                        </a> 
                    </div>
                <div style="float:left;width:20%;text-align:left;">&nbsp;</div>
                <div style="float:left;width:10%;text-align:center;">
                    <input type="checkbox"  name="another_donate[<{$item.cId}>]" id="scr_invdonate<{$i}>" value="1" <{$ck2}> <{$item.donate}>> 
                </div>
                <div style="float:left;width:10%;text-align:center;">
                    <input type="checkbox"  name="another_print[<{$item.cId}>]" id="scr_print<{$i}>" value="Y" <{$ck3}> > 
                </div>
                <div style="padding:0px;">
                    <input type="checkbox" name="Scrivener_chk[]"  onclick="item_split()" value="scr<{$i}>"  <{$ck}>>
                    <input type="text"  id="scr<{$i++}>" name="another_inv[]" class="scrivener_show_money" style="width:100px;" onKeyup="money_count('Scrivener_chk')" value="<{$item.cInvoiceMoney}>">
                    <input type="hidden" name="another_cId[]" value="<{$item.cId}>">
                   
                </div>
                <{/foreach}>
                <hr>
                <div style="float:left;width:85%;text-align:right;">小計</div>
                <div id='Scrivener_chk_count' style="text-align:right"><{$scrivener_total}></div>
            </fieldset>
             
                <hr>

            <div style="height:30px;"></div>
        <{if $cInvoiceOther > 0}>
            <fieldset style="width:800px;">
                 <{if $cInvoiceOther > 0 }>
                        <{assign var='ck' value='checked=checked'}>
                    <{else}>
                        <{assign var='ck' value=''}>
                    <{/if}>
                <legend align="left" style="">
                    <input type="checkbox" name="cInvChk[]" onclick="category_split()" value="cInvoiceOther" <{$ck}> >
                    創世基金會
                    (NT.$&nbsp;<span id="cInvoiceOther"><{$cInvoiceOther}></span>)
                    <input type="hidden" name="cInvoiceOther" value="<{$cInvoiceOther}>">
                </legend>
                <div style="float:left;width:240px;font-size:9pt;">身份別</div>
                <div style="float:left;width:200px;text-align:left;font-size:9pt;">姓名</div>
                <div style="font-size:9pt;">開發票對象金額分配</div>
                <hr>

                <div style="float:left;width:240px;">創世基金會&nbsp;</div>
                <div style="float:left;width:200px;text-align:left;">創世基金會</div>
                <div style="padding:0px;">
                    <input type="checkbox" name="Other_chk[]" onclick="item_split()" value="fund" disabled="disabled" <{$ck}>>
                    <input type="text" id="fund" name="fundament_inv[]" style="width:100px;" onKeyup="money_count('Other_chk')" value="<{$cInvoiceOther}>">
                </div>

                <hr>
                <div style="float:left;width:640px;text-align:right;">小計</div>
                <div id='Other_chk_count'><{$cInvoiceOther}></div>
            </fieldset>
        <{/if}>

            <{if $cSignCategory==1}>
                <input type="button" onclick="go()" value="　更新　">
                <input type="button" onclick="cls()" value="　重填　">
            <{/if}>
            
            </div>
        </div>
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
