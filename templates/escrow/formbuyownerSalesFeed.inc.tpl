<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta charset="UTF-8">
<title></title>
<script type="text/javascript">
var rowCount = parseInt(<{count($otherFeed2)}>);
$(document).ready(function() {
    CalculationRatio();

    <{if $msg != ''}>
        alert("<{$msg}>");
    <{/if}>
    if ("<{$data_case.cFeedBackClose}>"  == 1) {
        var array = "input,select,textarea";
                   
        $("#content").find(array).each(function() {
            $(this).attr('disabled', true);
                       
        }); 
    }

    setCombobox('');
    $(".feedbackmoneysum").change(function(event) {
       CalculationRatio();
    });

    $('.jsFeedBackMoney').bind('input propertychange', function() {
        let arr = $(this).prop('name').split('_');

        let id = arr[1];
        let has = $('[name="cooperationHas_' + id + '"]').val();
        checkBranchCoorpMoney(id, has);
    });
});

function feedbackmoney(){
    $("[name='cat']").val('search');
    $("[name='form']").submit();     
}

function save(val){
    var check = true;
    var check2 = true;
    var scrivener = "<{$data_case.scrivenerId}>";
    let scrivenerAccountCount = "<{count($scrivenerAccount)}>";
    let isScrivener = 0;
    let chekcsp = "<{$chekcsp}>";

    $(".checkBranch").each( function() {
        if ($(this).val() == 505) {
            var tmp = $(this).attr('name').split('_');
            if ($("[name='cFeedbackTarget_"+tmp[1]+"']:checked").val() == 1) {
                
                check= false;
            }

            if ($("[name='cCaseFeedback_"+tmp[1]+"']:checked").val() == 1) {
                check2 = false;
            }
        }
    });

    if (!check) {
        alert("非仲介成交回饋對象請選擇地政士");
        return false;
    }

    if (!check2) {
        alert("非仲介成交請選回饋按鈕");
        return false;
    }

    check = true;
    check2 = true;
    check3 = true;

    $(".row").each(function(index, val) {
        if ($('[name="newotherFeedType'+index+'"]:checked').val() == 1 && $('[name="newotherFeedstoreId'+index+'"]').val() == scrivener) {
            check2 = false;
        }
        
        if ($('[name="change'+index+'"]').val() == 1 && $('[name="newotherFeedMoneyNote'+index+'"]').val() == '' && $("#row"+index).attr('style') == '') {
            $('[name="newotherFeedMoneyNote'+index+'"]').attr('style', 'background:yellow');
            check = false;
        }else{
            $('[name="newotherFeedMoneyNote'+index+'"]').attr('style', '');
        }
        if($('[name="newotherFeedstoreId'+index+'"]').val() == '') {
            check3 = false;
        }
    });

    //如果有回饋，就可以新增下方回饋
    if ($('[name="cCaseFeedBackMoney_1"]').val() > 0 && $('[name="cCaseFeedback_1"]:checked').val() == 0 ) {
        check2 = true;
    }else if($('[name="cCaseFeedBackMoney_2"]').val() > 0 && $('[name="cCaseFeedback_2"]:checked').val() == 0 ) {
        check2 = true;
    }else if($('[name="cCaseFeedBackMoney_3"]').val() > 0 && $('[name="cCaseFeedback_3"]:checked').val() == 0 ){
        check2 = true;
    }else if($('[name="cCaseFeedBackMoney_4"]').val() > 0 && $('[name="cCaseFeedback_4"]:checked').val() == 0 ){
        check2 = true;
    }
    
    if (!check2) {
        alert("回饋案件代書請直接更改回饋對象");
        return false;
    }

    if (!check) {
        alert("請填寫其他回饋金的原因");
        return false;
    }

    if(!check3) {
        alert("請選擇其他回饋對象店名");
        return false;
    }

    if(scrivenerAccountCount > 1) {
        $('[name^="cFeedbackTarget_"]:checked').each(function (key, value) {
            if($(value).val() == 2 || chekcsp == 1 )  {
                isScrivener = 1;
            }
        });
        if(isScrivener == 1 && $('[name="fFeedbackDataId"]:checked').val() == undefined) {
            alert("請選擇回饋帳戶");
            return false;
        }
    }

    if($('#otherScrivenerAcc').is(":visible") && $('[name="fOtherFeedbackDataId"]:checked').val() == undefined) {
        alert("請選擇其他回饋帳戶");
        return false;
    }

    $("[name='cat']").val(val);
    $('[name^="cFeedbackTarget_"]').attr("disabled", false);
    $('[name^="cCaseFeedback_"]').attr("disabled", false);
    $('[name^="newotherFeedType"]').attr("disabled", false);
    $('[name^="newotherFeedstoreId"]').attr("disabled", false);
    $('[name^="cCaseFeedBackMoney_"]').attr("disabled", false);
    $('[name^="newotherFeedMoney"]').attr("disabled", false);
    $('[name^="cSpCaseFeedBackMoney"]').attr("disabled", false);
    $("[name='form']").submit();
}

function ChangeFeedStore(selector,name){
    var type = selector.val();
    var scrivenerId = "<{$data_case.scrivenerId}>";
    var chekcsp = "<{$chekcsp}>";

    $.ajax({
        url: '../includes/escrow/feedBackMoneyAjax.php',
        type: 'POST',
        dataType: 'html',
        data: {'type': selector.val(), 'act':'st', 'chekcsp':chekcsp, 'scrivenerId':scrivenerId},
    }).done(function(txt) {
        $("#"+name+" option").remove();
        $("#"+name).html(txt); 
    });
}

function setCombobox(type){
    $.widget( "ui.combobox", {
        _create: function() {
            var input,
                self = this,
                select = this.element.hide(),
                selected = select.children( ":selected" ),
                value = selected.val() ? selected.text() : "",
                wrapper = this.wrapper = $( "<span>" )
                    .addClass( "ui-combobox" )
                    .insertAfter( select );
            input = $( "<input>" )
                .appendTo( wrapper )
                .val( value )
                .addClass( "ui-state-default ui-combobox-input" )
                .autocomplete({
                    delay: 0,
                    minLength: 0,
                    source: function( request, response ) {
                        // alert($.ui.autocomplete.escapeRegex(request.term));
                        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                        response( select.children( "option" ).map(function() {
                            var text = $( this ).text();
                            if ( this.value && ( !request.term || matcher.test(text) ) ){
                                return {
                                    label: text.replace(
                                        new RegExp(
                                            "(?![^&;]+;)(?!<[^<>]*)(" +
                                            $.ui.autocomplete.escapeRegex(request.term) +
                                            ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                        ), "<strong>$1</strong>" ),
                                    value: text,
                                    option: this
                                };
                            }
                        }));
                    },
                    select: function( event, ui ) {                                    
                        ui.item.option.selected = true;
                        self._trigger( "selected", event, {
                            item: ui.item.option
                        });

                        select.trigger("change");
                    },
                    change: function( event, ui ) {
                        if ( !ui.item ) {
                            var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                            valid = false;
                            select.children( "option" ).each(function() {
                                if ( $( this ).text().match( matcher ) ) {
                                    this.selected = valid = true;
                                    $("[name='']")

                                    return false;
                                }
                            });
                            if ( !valid ) {                                          // remove invalid value, as it didn't match anything
                                $( this ).val( "" );
                                select.val( "" );
                                input.data( "autocomplete" ).term = "";
                                return false;
                            }
                        }
                                                                    
                    }
                })
                .addClass( "ui-widget ui-widget-content ui-corner-left" );
            input.data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a>" + item.label + "</a>" )
                .appendTo( ul );
            };
            $( "<a>" )
                .attr( "tabIndex", -1 )
                .attr( "title", "Show All Items" )
                .appendTo( wrapper )
                .button({
                    icons: {
                        primary: "ui-icon-triangle-1-s"
                    },
                    text: false
                })
                .removeClass( "ui-corner-all" )
                .addClass( "ui-corner-right ui-combobox-toggle" )
                .click(function() {
                    // close if already visible
                    if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
                        input.autocomplete( "close" );
                        return;
                    }

                    // work around a bug (likely same cause as #5265)
                    $( this ).blur();
                      
                    // pass empty string as value to search for, displaying all results
                    input.autocomplete( "search", "" );
                    input.focus();

                });
        },
        destroy: function() {
            this.wrapper.remove();
            this.element.show();
            $.Widget.prototype.destroy.call( this );

        }
    });
   
    if (type == 'd') {
        $('.combox').combobox('destroy');
    }else{
        $('.combox').combobox('destroy');
        $('.combox').combobox();
    }
}

function otherFeedChangeMark(index){
    $("#change"+index).val(1);
}

function checkScrivenerAccount(target) {
    let isScrivener = 0;
    let chekcsp = "<{$chekcsp}>";
    if(target == 2) { //地政士
        <{if count($scrivenerAccount) > 1 }>
            $('#scrivenerAcc').show();
        <{/if}>
    }
    if(target == 1) { //仲介
        $('[name^="cFeedbackTarget_"]:checked').each(function (key, value) {
            if($(value).val() == 2 || chekcsp == 1 )  {
                isScrivener = 1;
            }
        });
        if(isScrivener == 0) {
            $('#scrivenerAcc').hide();
        }
    }
}

function showOtherFeedBackAcc(sId, otherFeedType) {
    let isScrivener = 0;
    //地政士才需要確認2個以上帳戶
    if(1 == $("[name="+otherFeedType+"]:checked").val()) {
        $.ajax({
            url: '../includes/escrow/feedBackDataAjax.php',
            type: 'POST',
            dataType: 'html',
            data: {'sId': sId},
        }).done(function (txt) {
            $("#otherScrivenerAcc").empty();
            $("#otherScrivenerAcc").html(txt);
            $("#otherScrivenerAcc").show();
            if(txt == '') { $("#otherScrivenerAcc").hide(); }
        });
    }
    //選仲介時 需要確認其他選項還有沒有地政士
    if(2 == $("[name="+otherFeedType+"]:checked").val()) {
        $('[name^="newotherFeedType"]:checked').each(function (key, value) {
            if($(value).val() == 1)  {
                isScrivener = 1;
            }
        });
        if(isScrivener == 0) {
            $("#otherScrivenerAcc").empty();
            $("#otherScrivenerAcc").hide();
        }
    }


}

function addOtherFeed(){
    if ($("#row0").attr('style') != '' && $("#row0").attr('style') != undefined) { //
        $("#row0").attr('style', '');
        $('[name="change0"]').val(1)
        if(<{$data_case.cFeedBackScrivenerClose}> == 1) {
            $("[name='newotherFeedType0']").filter('[value="2"]').attr('checked',true).click();
        } else{
            $("[name='newotherFeedType0']").filter('[value="1"]').attr('checked',true).click();
        }

        $("[name='newotherFeedstoreId0']").val('');
        $("[name='newotherFeedMoney0']").val('');
        $("[name='newotherFeedMoneyNote0']").val('');
        $("[name='oId0']").val('');

    }else{
        setCombobox('d');

        var clone = $(".row:last").clone().attr('id', 'row'+rowCount);

        $("[name='newOtherIndex']").val(rowCount);
        
        clone.find('input[name*="newotherFeedType'+(rowCount-1)+'"]').attr({
            name: 'newotherFeedType'+rowCount,
            onclick: 'ChangeFeedStore($(this),\'newotherFeedstoreId'+rowCount+'\')'
        });

        clone.find('#newotherFeedstoreId'+(rowCount-1)).attr({
            name: 'newotherFeedstoreId'+rowCount,
            id:'newotherFeedstoreId'+rowCount,
            value:'',
            onchange:'otherFeedChangeMark('+rowCount+');showOtherFeedBackAcc(this.value, "newotherFeedType'+rowCount+'")',
        });

        clone.find('input[name*="newotherFeedMoney'+(rowCount-1)+'"]').attr({
            name: 'newotherFeedMoney'+rowCount,
            value:'',
            onchange:'otherFeedChangeMark('+rowCount+')'
        });

        clone.find('#OtherFeedDel'+(rowCount-1)).attr({
            id: 'OtherFeedDel'+rowCount,
            onclick:'delfeedmoney('+rowCount+')'
        });

        clone.find('[name*="newotherFeedMoneyNote'+(rowCount-1)+'"]').attr({
            name: 'newotherFeedMoneyNote'+rowCount,
            style:''
        }).val('');

        clone.find('[name*="oId'+(rowCount-1)+'"]').attr({
            name: 'oId'+rowCount,
            value:''
        });

        clone.find('[name*="otherFeedId'+(rowCount-1)+'"]').attr({
            name: 'otherFeedId'+rowCount,
            value:''
        });

        clone.find('[name*="change'+(rowCount-1)+'"]').attr({
            name: 'change'+rowCount,
            value:1
        });

        clone.find('[name*="deleteMark'+(rowCount-1)+'"]').val('');

        clone.insertAfter(".row:last");


        if(<{$data_case.cFeedBackScrivenerClose}> == 1) {
            $("[name='newotherFeedType"+rowCount+"']").filter('[value="2"]').attr('checked',true).click();
        } else{
            $("[name='newotherFeedType"+rowCount+"']").filter('[value="1"]').attr('checked',true).click();
        }
        setCombobox('');
    }

    rowCount++;
}

function delfeedmoney(index){
    var name = 'row'+index;

    if (confirm('確認是否要刪除?')) {
        if ($("[name='newotherFeedMoneyNote"+index+"']").val() == '') {
            $('[name="newotherFeedMoneyNote'+index+'"]').attr('style', 'background:yellow');
            alert("請填寫原因");
            return false;
        }

        if ($("[name='otherFeedId"+index+"']").val() != '') {
            $.ajax({
                url: 'formbuyownereditSalesFeedDel.php',
                type: 'POST',
                dataType: 'html',
                data: {category:1,id:$("[name='otherFeedId"+index+"']").val(),note:$("[name='newotherFeedMoneyNote"+index+"']").val()},
            }).done(function(msg) {
                $("#"+name).remove();
            });

        }else{
            var input = $('#'+name+' input');
            var textarea = $('#'+name+' textarea');
            var select = $('#'+name+' select');
            var arr_input = new Array();
           
            $.each(select, function(key,item) {
                arr_input[$(item).attr("name")] = $(item).attr("value");
             
            });

            $.each(textarea, function(key,item) {
                arr_input[$(item).attr("name")] = $(item).attr("value"); 
            });

            $.each(input, function(key,item) {
                if ($(item).is(':radio')) {
                    if ($(item).is(':checked')) {
                        arr_input[$(item).attr("name")] = $(item).val();
                    }
                        
                }else {
                    arr_input[$(item).attr("name")] = $(item).attr("value");
                }
            });

            var obj_input = $.extend({}, arr_input);
            $.ajax({
                url: 'formbuyownereditSalesFeedDel.php',
                type: 'POST',
                dataType: 'html',
                data: {category:2,cId:"<{$id}>",index:index,data:obj_input},
            }).done(function(msg) {
                // console.log(msg);
            });
            if(<{$data_case.cFeedBackScrivenerClose}> == 1) {
                $("[name='newotherFeedType0']").filter('[value="2"]').attr('checked',true).click();
            } else{
                $("[name='newotherFeedType0']").filter('[value="1"]').attr('checked',true).click();
            }

            $("[name='newotherFeedstoreId0']").val('');
            $("[name='newotherFeedMoney0']").val('');
            $("[name='newotherFeedMoneyNote0']").val('');
            $("[name='oId0']").val('');

            $("#"+name).hide();
        }
    } 
}

function CalculationRatio(){
    var total = 0;
    var ratio = 0;
    if ($('[name="income_certifiedmoney"]').val() != '' ) {
        var certifiedmoney = parseInt($('[name="income_certifiedmoney"]').val().replace(/\,/g, ''));
            $(".feedbackmoneysum").each( function() {
            if ($(this).val() != '') {
                total += parseInt($(this).val());
            }
        });

        if (certifiedmoney > 0) {
            var ratio = ((total/certifiedmoney)*100).toFixed(2); //取二位 
        }
    }

    $("#showRatio").html(ratio+'%');
}

function checkBranchCoorpMoney(id, has) {
    let money = $('[name="cCaseFeedBackMoney_' + id + '"]').val();  //金額
    let target = $('[name="cFeedbackTarget_' + id + '"]:checked').val();  //對象；1=仲介、2=地政士
    let YN = $('[name="cCaseFeedback_' + id + '"]:checked').val();  //是否回饋：1=不回饋、0=回饋

    if ((has == 0) && (YN == 0) && (target == 1) && (money <= 0)) {
        $('#apply').hide();
    } else {
        $('#apply').show();
    }
}

function checkFeedBackMoney(id) {
    let money = $('[name="cCaseFeedBackMoney_' + id + '"]').val();  //金額
    if(money > 0) {
        alert('不回饋 金額須為0');
        event.returnValue = false
        return false;
    }
}

var timeout;
function storeCoorpVerify(id, has) {
    const delay = 500; //間隔 5 秒再取輸入值

    if(timeout) {
        clearTimeout(timeout);
    }

    timeout = setTimeout(function() {
        checkBranchCoorpMoney(id, has);
    }, delay);
}

</script>
<style>
th {
    text-align:right;
    background: #E4BEB1;
    padding-top:10px;
    padding-bottom:10px;
}
td{
    /*text-align:right;*/
    background: #F8ECE9;;
    padding-top:10px;
    padding-bottom:10px;
}
.tb-title {
    text-align:left;
    font-size: 18px;
    padding-left:15px; 
    padding-top:10px; 
    padding-bottom:10px; 
    background: #E4BEB1;
    font-weight:bold;
}
.btnD{
    color: #000;
    font-family: Verdana;
    font-size: 16px;
    font-weight: bold;
    line-height: 20px;
    background-color: #F8ECE9;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #DDDDDD;
}
.btnD:hover{
    color: #FFF;
    font-size:16px;
    background-color: #E4BEB1;
    border: 1px solid #FFFF96;
}
.dis{
    display:none; 
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
    width:160px;
}
.ui-autocomplete {
    width:160px;
    max-height: 300px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
    /* add padding to account for vertical scrollbar */
    padding-right: 20px;
}
.ui-autocomplete-input {
    width:200px;
}
textarea{
    width: 95%;
    height: 30%;
}
</style>
</head>
<body id="content">
    <h3>回饋金修改申請</h3>
    <form action="" method="POST" name="form">
        <input type="hidden" name="cat">
        <input type="hidden" name="rId" value="<{$review.fId}>">

        <div style="width: 100%;">

            <table width="100%" border="0" class="gridtable">
            <tr>
                <td colspan="6" class="tb-title">
                    <div style="float:right;padding-right:10px;">保證費金額:<font color="red"><{$data_income.cCertifiedMoney}></font> <input type="hidden" name="income_certifiedmoney" value="<{$data_income.cCertifiedMoney}>"> 案件回饋比例:<font color="red" id="showRatio"> </font> </div>
                </td>      
            </tr>
           
            <{foreach from=$store key=key item=item}>                                   
            <tr>
                <td  colspan="6" class="tb-title">回饋對象</td>
            </tr>
            <tr>
                <th width="10%">仲介店名︰</th>
            <td colspan="4"><label id='bt'><{$item.brand}><{$item.branch}> <{if $item.cooperationHas != 1}><span style="font-size:9pt;">(無合契)</span><{/if}></label></td>
                <td width="20%" >
                    <input type="hidden" name="cFeedbackStoreId_<{$key}>" value="<{$item.bId}>" class="checkBranch">
                    <input type="hidden" name="cooperationHas_<{$key}>" value="<{$item.cooperationHas}>">
                </td>
            </tr>
            <tr>
                <th width="10%">案件回饋︰</th>
                <td width="22%">
                    <input type="radio"  name="cCaseFeedback_<{$key}>" onclick="checkBranchCoorpMoney(<{$key}>, <{$item.cooperationHas}>);" value="0" <{$item.caseFeedback0}> <{if $item.caseFeedTarget == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;回饋
                    金額：<input type="text"  style="width:80px;text-align:right;" class="jsFeedBackMoney feedbackClose feedbackmoneysum" name="cCaseFeedBackMoney_<{$key}>" onkeyup="storeCoorpVerify(<{$key}>, <{$item.cooperationHas}>)" maxlength="8" value="<{$item.feedbackmoney}>" <{if $item.caseFeedTarget == 2 }> <{$scrivenerDisabled}><{/if}>>&nbsp;元
                </td>                                       
                <td width="10%">
                
                    <{if $item.cooperationHas == 1}>
                        <input type="radio" class="feedbackClose" onclick="checkBranchCoorpMoney(<{$key}>, <{$item.cooperationHas}>);checkFeedBackMoney(<{$key}>);" disabled="disabled" name="cCaseFeedback_<{$key}>" value="1" <{$item.caseFeedback1}>>&nbsp;不回饋
                    <{else}>
                        <input type="radio" class="feedbackClose" onclick="checkBranchCoorpMoney(<{$key}>, <{$item.cooperationHas}>);checkFeedBackMoney(<{$key}>);" <{$store_cooperation_disable}> name="cCaseFeedback_<{$key}>" value="1" <{$item.caseFeedback1}> <{if $item.caseFeedTarget == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;不回饋
                    <{/if}>
                </td>
                <th width="10%">回饋對象︰</th>
                <td width="15%">
                    <{html_radios name="cFeedbackTarget_<{$key}>" onclick="checkBranchCoorpMoney(<{$key}>, <{$item.cooperationHas}>);checkScrivenerAccount(this.value);" options=$menuTarget selected="<{$item.caseFeedTarget}>"}>
                </td>
                <td align="center" valign="center">
                  
                </td>                                          
            </tr>
                <{if $item.individual}>
                    <{foreach from=$item.individual key=individualkey item=individualitem}>
                        <tr>
                            <th>個案回饋︰</th>
                            <td colspan="2"><{$individualitem.individualName}></td>
                            <th>回饋金額︰</th>
                            <td colspan="3">
                                <input type="text" class="feedbackClose feedbackmoneysum" style="width:80px;text-align:right;" name="individualMoney[<{$item.bId}>][]" maxlength="8" value="<{$individualitem.individualMoney}>">&nbsp;元
                                <input type="hidden" name="individualId[<{$item.bId}>][]" value="<{$individualitem.individualId}>">
                                <input type="hidden" name="individualBranchId[<{$item.bId}>][]" value="<{$item.bId}>">
                            </td>
                        </tr>
                    <{/foreach}>
                <{/if}>
            <{/foreach}>
            
            
            <{if ($scrivenerDetail.cSpCaseFeedBackMoney|default:0) > 0 || $chekcsp == 1}>                                 
            <tr> 
                <th>地政士事務所</th>
                <td colspan="2"><{$scrivenerDetail.sOffice}></td>
                <th>特殊回饋︰</td>
                <td colspan="3"><input type="text" class="feedbackClose feedbackmoneysum" style="width:80px;text-align:right;" name="cSpCaseFeedBackMoney" maxlength="8" value="<{$scrivenerDetail.cSpCaseFeedBackMoney|default:0}>" <{if $item.caseFeedTarget == 2}><{$scrivenerDisabled}><{/if}> />&nbsp;元 <input type="hidden" name="scrivenerId" value="<{$scrivenerDetail.sId}>" /></td>
            </tr>
            <{/if}>                                   
        </table>
        <table width="100%" border="0" class="feedm">
            <tr id="location">
                <td colspan="6" class="tb-title">其他回饋對象
                    <div style="float:right;padding-right:10px;">
                        <{if $data_case.cFeedBackClose != 1}> 
                            <a href="#location" class="add-feedback"  onclick="addOtherFeed()">新增</a>
                            <input type="hidden" name="newOtherIndex" value="<{$otherFeed2Count}>">
                        <{/if}>
                    </div>
                </td>
            </tr>

            <{if count($otherFeed2) == 0}>
                <tbody class="row" id="row0" style="display:none;">
                    <tr> 
                        <th width="10%">回饋對象：
                            <input type="hidden" name="oId0" value="">
                            <input type="hidden" name="otherFeedId0" value="">
                            <input type="hidden" name="change0">
                            <input type="hidden" name="deleteMark">
                        </th>
                        <td><{html_radios name="newotherFeedType0" options=$menuOTarget onClick="ChangeFeedStore($(this),'newotherFeedstoreId0')" checked="1" }></td>
                        <th width="10%">店名：</th>
                        <td>
                        <select name="newotherFeedstoreId0" id="newotherFeedstoreId0" class=" checkStoreO combox" onchange="otherFeedChangeMark(0);showOtherFeedBackAcc(this.value, 'newotherFeedType0')">
                            <{foreach from=$menuotherFeedStore  key=k item=i}>
                            <option value="<{$k}>" <{$ck}>><{$i}></option>
                            <{/foreach}>
                        </select>
                        </td>
                        <th width="10%">回饋金：</th>
                        <td>
                            <input type="text" class="feedbackClose feedbackmoneysum" style="width:80px;text-align:right;" name="newotherFeedMoney0" value="" onchange="otherFeedChangeMark(0)">元
                            <input type="button" value="刪除" id="OtherFeedDel0" onclick="delfeedmoney(0)">
                        </td>
                    </tr>
                    <tr>
                        <th>原因：</th>
                        <td colspan="5"><textarea name="newotherFeedMoneyNote0"><{$item.fNote|default:""}></textarea></td>
                    </tr>
                </tbody>
            <{/if}>
            

            <{foreach from=$otherFeed2 key=key item=item}>

            <tbody class="row" id="row<{$key}>">
                    <th width="10%">回饋對象：

                        <input type="hidden" name="oId<{$key}>" value="<{$item.id}>">
                        <input type="hidden" name="otherFeedId<{$key}>" value="<{$item.fId ?? ''}>">
                        <input type="hidden" name="change<{$key}>">
                    </th>
                    <td><{html_radios name="newotherFeedType<{$key}>" options=$menuOTarget onClick="ChangeFeedStore($(this),'newotherFeedstoreId<{$key}>')" checked="<{$item.fType}>" }></td>
                    <th width="10%">店名：</th>
                    <td>
                    <select name="newotherFeedstoreId<{$key}>" id="newotherFeedstoreId<{$key}>" class=" checkStoreO  <{if !(' disabled' == $scrivenerDisabled and $item.fType == 1) }>combox<{/if}>" onchange="otherFeedChangeMark(<{$key}>);showOtherFeedBackAcc(this.value, 'newotherFeedType<{$key}>')" <{if $item.fType == 1}><{$scrivenerDisabled}><{/if}>>
                        <{foreach from=$item.store  key=k item=i}>
                                <{if $item.fStoreId == $k}>
                                    <{assign var='ck' value='selected=selected'}> 
                                <{else}>
                                    <{assign var='ck' value=''}> 
                                <{/if}>
                                <option value="<{$k}>" <{$ck}>><{$i}></option>
                        <{/foreach}>
                    </select>
                    </td>
                    <th width="10%">回饋金：</th>
                    <td>
                        <input type="text" class="feedbackClose feedbackmoneysum" style="width:80px;text-align:right;" name="newotherFeedMoney<{$key}>" value="<{$item.fMoney}>" onchange="otherFeedChangeMark(<{$key}>)" <{if $item.fType == 1}><{$scrivenerDisabled}><{/if}>>元
                        <input type="button" value="刪除" id="OtherFeedDel<{$key}>" onclick="delfeedmoney(<{$key}>)" <{if $item.fType == 1}><{$scrivenerDisabled}><{/if}>>
                    </td>
                </tr>
                <tr>
                    <th>原因：</th>
                    <td colspan="5"><textarea name="newotherFeedMoneyNote<{$key}>"><{$item.fNote|default:""}></textarea></td>
                </tr>
            </tbody>
            <{/foreach}> 
            <{foreach from=$delNote key=key item=item}>
            <tbody>
                <tr>
                    <th width="10%">回饋對象：</th>
                    <td><{$item.fType}></td>
                    <th>店名：</th>
                    <td><{$item.Code}><{$item.Name}></td>
                    <th>回饋金：</th>
                    <td><{$item.fCaseFeedBackMoney}></td>
                </tr>
                <tr>
                    <th>刪除原因:</th>
                    <td colspan="5"><{$item.fNote|default:""}></td>
                </tr>
            </tbody>
            <{/foreach}> 
            <tr>
                <td colspan="6" class="tb-title">備註</td>
            </tr>
            <tr>
                <td colspan="6"><textarea name="note" id="" cols="120" rows="5"><{$review.fNote|default:""}></textarea></td>
            </tr>
            <tr>
                <td colspan="6" id="delete"></td>
            </tr>
            <tbody id="scrivenerAcc" style="display: <{if count($scrivenerAccount) > 1 and $feedBackScrivener > 0}><{else}>none<{/if}>">
                <tr>
                    <td colspan="6" class="tb-title">選擇主要地政士回饋帳戶(若有2個以上回饋帳戶需選擇)</td>
                </tr>
                <{foreach from=$scrivenerAccount key=key item=item}>
                <tr>
                    <td colspan="6" ><input type="radio" name="fFeedbackDataId" value="<{$item.fId}>">戶名：<{$item.fAccountName}>、帳號：<{$item.fAccountNum}><{$item.fAccountNumB}>-<{$item.fAccount}></td>
                </tr>
                <{/foreach}>
            </tbody>
            <tbody id="otherScrivenerAcc" style="display: <{if count($otherScrivenerAccount) > 1 }><{else}>none<{/if}>">
                <tr>
                    <td colspan="6" class="tb-title">選擇其他地政士回饋帳戶(若有2個以上回饋帳戶需選擇) </td>

                </tr>
                <{foreach from=$otherScrivenerAccount key=key item=item}>
                <tr>
                    <td colspan="6" ><input type="radio" name="fOtherFeedbackDataId" value="<{$item.fId}>">戶名：<{$item.fAccountName}>、帳號：<{$item.fAccountNum}><{$item.fAccountNumB}>-<{$item.fAccount}></td>
                </tr>
                <{/foreach}>
            </tbody>
        </table>


        </div>
       

        <div style="text-align: center;">
            <{if $data_case.cFeedBackClose != 1}>
            <input type="button" id="apply" value="送出" class="btnD" onclick="save('<{if $review.fId != ''}>save<{else}>add<{/if}>')">
            <{/if}>
        </div>
    </form>
    <script>
        //回饋金隨案付款
        if(<{$data_case.cFeedBackScrivenerClose}> == 1) {
            if(<{$data_realstate.cBranchNum}> == 505) {
                $(".gridtable input").attr("disabled", true);
                $(".add-feedback").hide();
            } else {
                $('[name^="cFeedbackTarget_"]').attr("disabled", true);
                $('[name^="newotherFeedType"]').attr("disabled", true);
            }
        }

    </script>
</body>
</html>