<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>更多對象</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="/js/rocCal.js"></script>
<link href="/css/combobox.css" rel="stylesheet">
<link href="/css/transferArea.css?v=20230816" rel="stylesheet">
<script src="/js/IDCheck.js"></script>
<script src="/js/lib/comboboxNormal.js"></script>
<script src="/js/transferArea.js?v=20230818"></script>
<link rel="stylesheet" href="/css/colorbox.css" />
<script src="/js/jquery.colorbox.js"></script>
<script type="text/javascript">
var parentId = [];

$(document).ready(function() {
    var ck = "<{$InvoiceClose}>";
    var dep ="<{$smarty.session.member_pDep}>";

    if (ck == 'Y' && ( dep != 9 && dep != 10 && dep != 1)) {
        $(".invoice").each(function() {
            $(this).attr('disabled', true);
        });
    }
   
   $('.date_pick').each(function() {
        $(this).datepicker({
            dateFormat: "yy-mm-dd"
        }) ;
    }) ;

    $('#addnew').button({
        icons:{
            primary: "ui-icon-plus"
        }
    }) ;

    setComboboxNormal('bank select','class');

    let sn = $('[name="buy_identifyid"]', window.parent.document).val();
    if ((sn != '') && (sn != undefined) && (sn != null)) {
        parentId.push(sn);
    }

    sn = $('[name="owner_identifyid"]', window.parent.document).val();
    if ((sn != '') && (sn != undefined) && (sn != null)) {
        parentId.push(sn);
    }

    InitialIdDoubleCheck();
});

var timeout;
var delay = 500;   //間隔 0.5 秒再取輸入值
function checkID(cat, id) {
    if(timeout) {
        clearTimeout(timeout);
    }

    timeout = setTimeout(function() {
        checkIDDelay(cat, id);
    }, delay);
}

function checkIDDelay(cat, id){
    let _val = $('#'+cat+"IdentifyId_"+id).val().toUpperCase(); //newIdentifyId_0
    $('#' + cat + "IdentifyId_" + id).val(_val);

    let _id =  $('#'+cat+"Idc_"+ id +'_img'); //newIdc_0_img

    let id_array = new Array();
    $('.idc').each(function(index) {
        if (($(this).val() != '') && ($(this).val() != undefined) && ($(this).val() != null)) {
            if ($(this).attr('id') != cat + "IdentifyId_" + id) {
                id_array.push($(this).val());
            }
        }
    });

    $('#save_btn').show();
    $("#" + cat + "Guardian_" + id).attr('class', 'display');
    if (checkUID(_val)) {
        _id.html('<img src="/images/ok.png">') ;
        if (_val.length == 8) {
            $("#"+cat+"Guardian_"+id).attr('class', 'show');
        }
    } else {
        _id.html('<img src="/images/ng.png">') ;
    }

    checkIdDouble(cat, id);
}

function getCustomer(cat,id){ 
    let val = $('#'+cat+"IdentifyId_"+id).val();
    let check = 0;
    let iden = ("<{$_iden}>" == 'o') ? 'owner' : 'buyer';

    $.ajax({
        url: '/includes/escrow/getCustomer.php',
        type: 'POST',
        dataType: 'html',
        async:false,
        data: {id: val, cId: "<{$cCertifiedId}>", iden: iden},
    }).done(function(msg) {
        let obj = JSON.parse(msg);

        if (obj.msg == 'ok') {
            let check = 0;
            if (checkUID(val)) {
                if ($("[name='"+cat+"Name_"+id+"']").val() != '') {
                    if (!confirm("已有資料存在，是否要取代?")) {
                       check = 1;
                    }
                }
            }

            if (check == 0) {
                $("[name='"+cat+"Name_"+id+"']").val(obj.name);
                $("[name='"+cat+"BirthdayDay_"+id+"']").val(obj.birthday);
                $("[name='"+cat+"RegistZip_"+id+"']").val(obj.zip);
                $("[name='"+cat+"RegistCity_"+id+"']").val(obj.city);
                $("[name='"+cat+"RegistAddr_"+id+"']").val(obj.addr);

                $("[name='"+cat+"RegistArea_"+id+"'] option").remove() ; //
                $.post('listArea.php',{"city":obj.city},function(txt) {    
                    $("[name='"+cat+"RegistArea_"+id+"']").append(txt) ;
                    $("[name='"+cat+"RegistArea_"+id+"']").val(obj.zip);
                }) ;

                $("[name='"+cat+"Checklist_"+id+"']").click();//newChecklist_0
            }
        }
    });
}

function GetBankBranchList(bank, branch, sc) {
    $(branch).prop('disabled',true) ;

    let request = $.ajax({  
        url: "/includes/maintain/bankbranchsearch.php",
        type: "POST",
        data: {
            bankcode: $(bank).val()
        },
        dataType: "json"
    });

    request.done(function( data ) {
        $(branch).combobox("destroy");
        $(branch).children().remove().end();
        $(branch).append('<option value="">------</option>')
        $.each(data, function (key, item) {
            if (key == sc ) {
                $(branch).append('<option value="'+key+'" selected>'+item+'</option>');
            } else {
                $(branch).append('<option value="'+key+'">'+item+'</option>');
            }
        });

        setComboboxNormal($(branch).attr("id"),'id');//再把combobox加回來
    });
    
    $(branch).prop('disabled',false) ;
}

/* 身份證字號查核以決定是否顯示非本國籍選單 */
function checkForeign(cat,id) {
    let pat = /[a-zA-Z]{2}/ ;
    let pat2 = /[a-zA-z]{1}[8|9]{1}[0-9]{8}/;//2021新證號
    let pat3 = /^[0-9]{7}$/;//大陸人民證號
    let _val = $('#'+cat+"IdentifyId_"+id).val();//newIdentifyId_0
   
    if (pat.test(_val) || pat2.test(_val) || pat3.test(_val)) { //newForeign_0
        $('#' + cat + 'Foreign_' + id).attr('class', 'show'); 
    } else {
        $('#' + cat + 'Foreign_' + id).attr('class', 'display'); 
    }
}

function bank_change(Bmain,Bbranch) {
    let url = 'bankConvert.php' ;
    let _bank = $('#' + Bmain).val() ;
    $.post(url,{'bk': _bank},function(txt) {
        $('#' + Bbranch).html(txt) ;
    }) ;
}

//依據縣市選擇改變鄉鎮市選項
function zip_area(city,area,zips) {
    let url = 'zipConvert.php' ;
    let _city = $('#' + city).val() ;
    
    $.post(url,{'ct': _city},function(txt) {
        $('#' + area).html(txt) ;
        $('#' + zips).val('') ;
    }) ;    
}

//依據鄉鎮市選擇改變郵遞區號顯示
function zip_change(area,zip) {
    let _zip = $('#' + area).val() ;
    $('#' + zip).val(_zip) ;
}

function showCountryCode(cat,id){
    let val = $("#" + cat + "Code_" + id).val();
    $("#" + cat + '_' + id).val(val);
}

//newCopybank
function addRow(){
    let no = parseInt($("[name='newRowCount']").val());
    let no2 = no + 1;

    $("#newBankMain_" + no + "_0").combobox("destroy");//要先移掉原本的不然複製出來的會有問題
    $("#newBankMain_" + no + "_0 option").show();//option 沒有顯示出來只好強制顯示
    $("#newcBankBranch_" + no + "_0").combobox("destroy"); //要先移掉原本的不然複製出來的會有問題

    let reg = /.*\[]$/ ;
    let clonedRow = $(".row:last").clone(true);

    clonedRow.find('[type*="text"]').val('');
    clonedRow.find('select').val('');

    clonedRow.find('#newCountryCode_'+no).attr('onchange', 'showCountryCode("newCountry",'+no2+')');
    clonedRow.find('#newRegistCity_'+no).attr('onchange', 'zip_area("newRegistCity_'+no2+'","newRegistArea_'+no2+'","newRegistZip_'+no2+'")');
    clonedRow.find('#newRegistArea_'+no).attr('onchange', 'zip_change("newRegistArea_'+no2+'","newRegistZip_'+no2+'")');

    clonedRow.find('#newBaseCity_'+no).attr('onchange', 'zip_area("newBaseCity_'+no2+'","newBaseArea_'+no2+'","newBaseZip_'+no2+'")');
    clonedRow.find('#newBaseArea_'+no).attr('onchange', 'zip_change("newBaseArea_'+no2+'","newBaseZip_'+no2+'")');
    clonedRow.find('[name*="newPaymentDate_'+no+'"]').datepicker( "destroy" );
    clonedRow.find('[name*="newPaymentDate_'+no+'"]').removeClass("hasDatepicker").attr("id",'');
    clonedRow.find('[name*="newPaymentDate_'+no+'"]').datepicker({dateFormat: "yy-mm-dd"}) ;
    clonedRow.find('#newIdentifyId_'+no).attr('onkeyup', 'checkID("new","'+no2+'")');
    
    clonedRow.find('#newIdc_'+no+'_img').attr('id', 'newIdc_'+no2+'_img');
    clonedRow.find('#newForeign_'+no).attr('id', 'newForeign_'+no2);
    clonedRow.find('#newGuardian_'+no).attr('id', 'newGuardian_'+no2);
    clonedRow.find('#newAddBank_'+no).val('新增銀行').attr('onclick', 'addBank("new","'+no2+'")'); //newAddBank_1

    clonedRow.find('#newSame_'+no).attr('onclick', 'addr("new",'+no2+');');
    clonedRow.find('#newBirthdayDay_'+no).attr('onclick', 'showdate(myform.newBirthdayDay_'+no2+')');
    clonedRow.find('#newBankMain_'+no+'_0').attr('onchange', "bank_change('newBankMain_"+no2+"_0','newcBankBranch_"+no2+"_0')");
    clonedRow.find('#newIndex_'+no).val(0);
    clonedRow.find("input").each(function(index, el) {
        if ($(this).attr("name") != undefined) {
            if (!reg.test($(this).attr("name"))) {
                let string = $(this).attr("name").split("_");
                let name = string[0]+'_'+no2;
                $(this).attr("name",name);//+

                if (string[0] != 'newPaymentDate') { //會跟datepicker衝突所以不能有id
                    $(this).attr("id",name);
                }
            }else{
               let name = $(this).attr("name").split("[]"); 
               let name2 = name[0].split("_"); 
               let id = $(this).attr("id").split("_"); 
              
               $(this).attr("name",name2[0]+'_'+no2+'[]');//+
               $(this).attr("id",id[0]+'_'+no2+'_'+id[2]); 
            }
        }
    });

    clonedRow.find("select").each(function(index, el) {
        if ($(this).attr("name") != undefined) {
            if (!reg.test($(this).attr("name"))) {
                let string = $(this).attr("name").split("_");
                let name = string[0]+'_'+no2;
                $(this).attr("name",name);//+
                $(this).attr("id",name);
            }else{
               let name = $(this).attr("name").split("[]"); 
               let name2 = name[0].split("_"); 
               let id = $(this).attr("id").split("_"); 
              
               $(this).attr("name",name2[0]+'_'+no2+'[]');//+
               $(this).attr("id",id[0]+'_'+no2+'_'+id[2]); 
            }
        }
    });

    clonedRow.find('.newCopybank_'+no).each(function(index, el) {
        let txt = 'newCopybank_'+no;
        if ($(this).attr('id') != txt) {
            $(this).remove();
        }
    });

    clonedRow.find('#newCopybank_'+no).attr('class', 'newCopybank_'+no2);
    clonedRow.find('#newCopybank_'+no).attr({
        class: 'newCopybank_'+no2,
        id: 'newCopybank_'+no2
    });

    clonedRow.find('#newChecklist_'+no).attr({
        class: 'newChecklist_'+no2,
        id: 'newChecklist_'+no2
    });

    clonedRow.insertAfter('.row:last');

    $("[name='newRowCount']").val(no2);
    setComboboxNormal("newBankMain_"+no+"_0","id");//套用combobox
    setComboboxNormal("newcBankBranch_"+no+"_0","id");//套用combobox
    setComboboxNormal("newBankMain_"+no2+"_0","id");//套用combobox
    setComboboxNormal("newcBankBranch_"+no2+"_0","id");//套用combobox
}

function addBank(cat,id){
    let count = parseInt($("#"+cat+'Index_'+id).val());//new_ChecklistBank_0 newIndex_0
    count++;

    $("#"+cat+"BankMain_"+id+"_0").combobox("destroy");
    $("#"+cat+"cBankBranch_"+id+"_0").combobox("destroy"); //newcBankBranch_0_0
    $("#"+cat+"BankMain_"+id+"_0 option").show();//option 沒有顯示出來只好強制顯示

    let clonedRow = $("#"+cat+"Copybank_"+id).clone(true);

    clonedRow.attr('id', 'newCopybank'+id+'_'+count);
    clonedRow.find('input').val('');
    clonedRow.find('select').val('');
    clonedRow.find('[name*="'+cat+'BankMain_'+id+'[]"]').attr({
        id: cat+'BankMain_'+id+'_'+count,
        name: cat+'BankMain_'+id+'[]',
        onchange:"bank_change('"+cat+"BankMain_"+id+"_"+count+"','"+cat+"cBankBranch_"+id+"_"+count+"')"
    });

    clonedRow.find('[name*="'+cat+'cBankBranch_'+id+'[]"] option').remove();
    clonedRow.find('[name*="'+cat+'cBankBranch_'+id+'[]"]').attr({
        id: cat+'cBankBranch_'+id+'_'+count,
        name: cat+'cBankBranch_'+id+'[]'
    });

    clonedRow.find('[name*="'+cat+'BankAccNum_'+id+'[]"]').attr({
        id: cat+'BankAccNum_'+id+'_'+count,
        name: cat+'BankAccNum_'+id+'[]'
    });

    clonedRow.find('[name*="'+cat+'BankAccName_'+id+'[]"]').attr({
        id: cat+'BankAccName_'+id+'_'+count,
        name: cat+'BankAccName_'+id+'[]'
    });

    clonedRow.find('[name*="'+cat+'BankAccMoney_'+id+'[]"]').attr({
        id: cat+'BankAccMoney_'+id+'_'+count,
        name: cat+'BankAccMoney_'+id+'[]'
    });

    //oldChecklistBank_0
    clonedRow.find('[name*="'+cat+'ChecklistBank_'+id+'"]').attr({
        id: cat+'ChecklistBank_'+id+'_'+count,
        name: cat+'ChecklistBank_'+id+'_'+count
    }).prop('checked', '');

    clonedRow.insertAfter('.'+cat+'Copybank_'+id+':last');

    $("#"+cat+'Index_'+id).val(count);
    setComboboxNormal(cat+"BankMain_"+id+"_0","id");
    setComboboxNormal(cat+"cBankBranch_"+id+"_0","id");

    setComboboxNormal(cat+'BankMain_'+id+'_'+count,"id");
    setComboboxNormal(cat+'cBankBranch_'+id+'_'+count,"id");
}

function addr(cat, id) {
    //newSame_0
     if ($('#'+cat+'Same_'+id).prop('checked')) {
        let url = 'zipConvert.php' ;
        $.post(url,{'ct':$('#'+cat+'RegistCity_'+id).val()},function(txt) {
                $('#'+cat+'BaseArea_'+id).html(txt) ;
                $('#'+cat+'BaseArea_'+id).val($('#'+cat+'RegistArea_'+id).val()) ;
        }) ;

        //newBaseZip_0_F
        $('#'+cat+'BaseZip_'+id).val($('#'+cat+'RegistZip_'+id).val());//郵遞區號複製
        
        $('#'+cat+'BaseCity_'+id).val($('#'+cat+'RegistCity_'+id).val());
        $('#'+cat+'BaseAddr_'+id).val($('#'+cat+'RegistAddr_'+id).val()) ;
    } else {
        $('#'+cat+'BaseAddr_'+id).val('') ;
        $('#'+cat+'BaseCity_'+id).val('') ;
        $('#'+cat+'BaseArea_'+id).empty().html('<option value="">區域</option>') ;
        $('#'+cat+'BaseZip_'+id).val('') ;
    }
}

//刪除資料
function del(no) {
    $('#dialog_save').html('確認是否刪除本筆資料？') ;
    $('#dialog_save').prop('title','ID = ' + no + ', 刪除？') ;
    
    $('#dialog_save').dialog({
        resizable: false,
        height: 140,
        modal: true,
        buttons: {
            "確認": function() {
                $('form[name="del_form"] input[name="del"]').val('ok') ;
                $('form[name="del_form"] input[name="del_no"]').val(no) ;
                $('form[name="del_form"]').submit() ;
            },
            "取消": function() {
                $(this).dialog("close") ;
                $('[name="myform"]').submit() ;
            }
        }
    }) ;
}

function clickChecklist(cat,index){
    $("."+cat+"Checklist_"+index).each(function() {
        if ($("#"+cat+"Checklist_"+index).prop("checked")) {
            $(this).prop('checked', 'checked');
        } else {
            $(this).prop('checked', '');
        }
    });
}

function checkChecklist(cat,index){
    let count = $("."+cat+"Checklist"+index).length;
    let count2 = 0;

    $("."+cat+"Checklist"+index).each(function() {
        if ($(this).prop('checked')) {
            count2++;
        }
    });

    if (count == count2) { //oldChecklist0
        $("[name='"+cat+"Checklist"+index+"']").prop('checked', 'checked');
    } else {
        $("[name='"+cat+"Checklist"+index+"']").prop('checked', '');
    }
}

function print(cid,iden) {
    window.open('buyerownerlist_print.php?cid='+cid+'&iden='+iden);
}

function catchData(){
    let input = $('input');
    let textarea = $('textarea');
    let select = $('select');
    let arr_input = new Array();
    let reg = /.*\[]$/ ;
    let breakOut = false;

    if(<{$cCaseStatus}> == 1) {
        //通訊地址檢查old
        $.each($("input[name^='oldBaseZip_']"), function (index, value){
            if($(value).val() == '') {
                alert('請確認通訊地址');
                breakOut = true;
                return false;
            }
            if($("input[name='oldBaseAddr_" + index + "']").val() == '') {
                alert('請確認通訊地址!');
                breakOut = true;
                return false;
            }
            //alert( "Index #" + index + ": " + $(value).val() );
        });
        if(breakOut) return false;

        //通訊地址檢查new
        $.each($("input[name^='newIdentifyId_']"), function (index, value){
            if($(value).val() != '') { //有填寫身分證號/統編
                if($("input[name='newBaseZip_" + index + "']").val() == '') {
                    alert('請確認通訊地址');
                    breakOut = true;
                    return false;
                }

                if($("input[name='newBaseAddr_" + index + "']").val() == '' || $("input[name='newBaseAddr_" + index + "']").val() == undefined) {
                    alert('請確認通訊地址!');
                    breakOut = true;
                    return false;
                }
            }
        });
        if(breakOut) return false;
    }



    $.each(select, function(key,item) {
        if (reg.test($(item).attr("name"))) {        
            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                arr_input[$(item).attr("name")] = new Array();            
            }
                            
            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
        } else {
            arr_input[$(item).attr("name")] = $(item).attr("value");      
        }       
    });

    $.each(input, function(key,item) {
        if(reg.test($(item).attr("name"))){
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
        } else if ($(item).is(':checkbox')) {
            if ($(item).is(':checked')) {
                arr_input[$(item).attr("name")] = '1';
            } else {
                arr_input[$(item).attr("name")] = '0';
            }
        } else if ($(item).is(':radio')) {
            if ($(item).is(':checked')) {
                arr_input[$(item).attr("name")] = $(item).val();
            }
        } else {
            arr_input[$(item).attr("name")] = $(item).attr("value");
        }
    });

    let obj_input = $.extend({}, arr_input);
    let url_submit = "buyerownerlist_save.php?iden=<{$_iden}>&cCertifyId=<{$cCertifiedId}>&cSingCategory=<{$SignCategory}>";
    let request = $.ajax({
        url: url_submit,
        type: "POST",
        data: obj_input,
        dataType: "html"
    });

    request.done( function( msg ) {
        if (msg) {
            alert('儲存成功');
            location.href ="buyerownerlist.php?iden=<{$_iden}>&cCertifyId=<{$cCertifiedId}>&cSingCategory=<{$SignCategory}>";
        }
    });
}

/* 檢核身份證字號是否重複 */
function checkIdDouble(cat, id) {
    let el = $('#' + cat + "IdentifyId_" + id);
    let arr = new Array;

    let br_id =  $('[name="buy_identifyid"]', window.parent.document).val()
    if ((br_id != '') && (br_id != undefined) && (br_id != null)) {
        arr.push(br_id);
    }

    let ow_id =  $('[name="owner_identifyid"]', window.parent.document).val()
    if ((ow_id != '') && (ow_id != undefined) && (ow_id != null)) {
        arr.push(ow_id);
    }

    $('.js-select').each(function() {
        let sn = $(this).val();
        if ((sn != '') && (sn != null) && (sn != undefined)) {
            arr.push(sn);
        }
    })

    let _duplicated = hasDuplicates(arr);
    if (_duplicated != '') {
        alert('身分證號/統編已存在!!(' + _duplicated + ')');
        el.focus().select();
        $('#save_btn').hide();
    } else {
        checkForeign(cat, id);
        getCustomer(cat, id);
    }

    delete(arr);
}

function InitialIdDoubleCheck() {
    let arr = new Array;

    let br_id =  $('[name="buy_identifyid"]', window.parent.document).val()
    if ((br_id != '') && (br_id != undefined) && (br_id != null)) {
        arr.push(br_id);
    }

    let ow_id =  $('[name="owner_identifyid"]', window.parent.document).val()
    if ((ow_id != '') && (ow_id != undefined) && (ow_id != null)) {
        arr.push(ow_id);
    }

    $('.js-select').each(function() {
        let sn = $(this).val();
        if ((sn != '') && (sn != null) && (sn != undefined)) {
            arr.push(sn);
        }
    })
    
    if (hasDuplicates(arr)) {
        $('#save_btn').hide();
    } else {
        $('#save_btn').show();
    }

    delete(arr);
}

function hasDuplicates(arr) {
    let counts = [];

    for (let i = 0; i <= arr.length; i++) {
        if (counts[arr[i]] === undefined) {
            counts[arr[i]] = 1;
        } else {
            return arr[i];
        }
    }

    return '';
}

/* 其他買賣方更多行動電話號碼 */
function more_phone(others_id, identify_id) {
    let cId = $('[name="cCertifiedId"]').val();
    let cIdentity = $('[name="cIdentity"]').val();
    let url = 'formphonedit.php?t=' + cIdentity + '&cid=' + cId + '&cSignCategory=1&others_id=' + others_id;
    
    $.colorbox({iframe:true, width:"750px", height:"350px", href:url}) ;
}
////
</script>
<style>
    body{
         background-color:#F8ECE9; 
    }

    .display{
        display: none;
    }

    .show{
        display: '';
    }

    .Title{
        background-color:#E4BEB1;font-size:12pt;font-weight:bold;padding:5px;
        width: 100%;
    }

    .tb{

    }

    .tb th{
        text-align: left;
        padding-top: 5px;
    }

    .tb td{
        text-align: left;
        padding-top: 5px;
    }

    .sign-red {
        color:red;
    }

    .xxx-button {
        color:#FFFFFF;
        font-size:14px;
        font-weight:normal;
        
        text-align: center;
        white-space:nowrap;
        height:30px;
        
        background-color: #a63c38;
        border: 1px solid #a63c38;
        border-radius: 0.35em;
        font-weight: bold;
        padding: 0 20px;
        margin: 5px auto 5px auto;
    }

    .xxx-button:hover {
        background-color:#333333;
        border:1px solid #333333;
    }

    fieldset {
        border-radius: 6px;
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
</style>
</head>
<body>
    <div style="padding-bottom: 10px;">
        <input type="button" name="button" id="button" value="預覽列印" onclick="print('<{$cCertifiedId}>','<{$_iden}>');" />
    </div>

    <form method="post" name="del_form">
        <input type="hidden" name="del" value="">
        <input type="hidden" name="del_no" value="">
    </form>

    <form method="POST" name="myform" id="myform">
        <input type="hidden" name="_iden" value="<{$_iden}>" />
        <input type="hidden" name="cIdentity" value="<{$cIdentity}>" />
        <input type="hidden" name="cCertifiedId" value="<{$cCertifiedId}>" />
        <{foreach from=$list key=key item=item}>
        <table cellpadding="0" cellspacing="0" class="tb" width="100%" id="old<{$item.cId}>">
            <tr>
                <td class="Title" colspan="3">
                    [<span id="no"><{$item.no}></span>]<{$_ide}>方資料　(<{$cCertifiedId}>) 
                    <{if $checkSave == 1}>
                    <span style="font-size:10pt;"><a href="#old<{$item.cId}>" onclick="del('<{$item.cId}>')">刪除</a></span>
                    <{/if}>
                    <input type="hidden" name="oldId[]" value="<{$item.cId}>" />
                </td>
            </tr>
            <tr>
                <th><span class="sign-red">*</span>身分別：</th>
                <td><{$_ide}>方</td>
                <td rowspan="5">
                    <fieldset class="<{$item.checkforeign}>" style="font-size:9pt;" id="oldForeign_<{$key}>">  <!-- display: none -->
                        <legend style="font-size:9pt;">非本國籍身份資料</legend>
                        <table = border="0" style="padding-left:10px;">
                            <tr>
                                <td style="font-size:9pt; width:300px;">
                                    國籍代碼： <input type="text" name="oldCountry_<{$key}>" id="oldCountry_<{$key}>" style="width:35px" disabled value="<{$item.cCountryCode}>">
                                        <{html_options name="oldCountryCode_<{$key}>" id="oldCountryCode_<{$key}>" options=$menuCountry style="width: 100px;font-size:9pt;" onchange="showCountryCode('oldCountry','<{$key}>')" selected=$item.cCountryCode}>
                                    <br>租稅協定代碼：<input type="text" style="width:40px;" name="oldTaxTreatyCode_<{$key}>" id="oldTaxTreatyCode_<{$key}>" value="<{$item.cTaxTreatyCode}>">
                                    護照號碼：<input type="text" name="oldPassport_<{$key}>" id="oldPassport_<{$key}>" value="<{$item.cPassport}>" style="width:100px" class="passport invoice">
                                </td>
                                <td style="font-size:9pt;">
                                    <label style="font-size:9pt;">
                                        <table border="0" style="width:200px;">
                                            <tr>
                                                <td style="width:80px;font-size:9pt;">已住滿183天?<{$item.cResidentLimit}></td>
                                                <td>
                                                    <{html_radios name="oldResidentLimit_<{$key}>" id="oldResidentLimit_<{$key}>" options=$menuResident checked=$item.cResidentLimit  }>
                                                </td>
                                            </tr>
                                        </table>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size:9pt;">
                                    給付日期：<input type="text" name="oldPaymentDate_<{$key}>"  style="width:120px;" class="date_pick" value="<{$item.cPaymentDate}>">
                                </td>
                                <td style="font-size:9pt;">
                                    <label style="font-size:9pt;">
                                        <table border="0" style="width:200px;">
                                            <tr>
                                                <td style="width:80px;font-size:9pt;">已加入健保?</td>
                                                <td style="width:20px;">
                                                    <input type="checkbox" name="oldcNHITax_<{$key}>" id="oldcNHITax_<{$key}>" value="1" <{$item.cNHITaxChecked}>>
                                                </td>
                                                <td style="font-size:9pt;">是</td>
                                            </tr>
                                        </table>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><span class="sign-red">*</span>身分證號/統編︰</th>
                <td>
                    <input type="text" maxlength="10" style="width:120px;" class="idc invoice js-select" id="oldIdentifyId_<{$key}>" name="oldIdentifyId_<{$key}>" value="<{$item.cIdentifyId}>" onkeyup="checkID('old','<{$key}>')"/>
                    <span id="oldIdc_<{$key}>_img" style="padding-left:5px;" ><{$item.checkIDImg}></span>
                </td>
            </tr>
            <tr>
                <th><span class="sign-red">*</span><{$_ide}>方姓名</th>
                <td><input type="text" name="oldName_<{$key}>" id="oldName_<{$key}>" style="width:120px;" value="<{$item.cName}>" class="invoice"/></td>
            </tr>
            <tr id="oldGuardian_<{$key}>" class="<{$item.OtherNameShow}>">
                <th>&nbsp;&nbsp;法定代理人:</th>
                <td><input type="text" name="oldOtherName_<{$key}>" id="oldOtherName_<{$key}>" value="<{$item.cOtherName}>" /></td>
            </tr>
            <tr>
                <th><span class="sign-red">*</span>行動電話︰</th>
                <td>
                    <input type="text" maxlength="10" style="width:120px;" name="oldMobileNum_<{$key}>" id="oldMobileNum_<{$key}>" value="<{$item.cMobileNum}>" class="invoice" />
                    <a href="Javascript:void(0);" onclick="more_phone(<{$item.cId}>, '<{$item.cIdentifyId}>')"><span style="font-size:9pt;">更多...</span></a>
                </td>
            </tr>
            <tr>
                <th>&nbsp;&nbsp;出生日期︰</th>
                <td><input type="text" maxlength="10" name="oldBirthdayDay_<{$key}>" id="oldBirthdayDay_<{$key}>" style="width:120px;" class="calender input-text-big"  onclick="showdate(myform.oldBirthdayDay_<{$key}>)" value="<{$item.cBirthdayDay}>" /></td>
            </tr>
            <tr>
                <th>&nbsp;&nbsp;戶籍地址︰</th>
                <td colspan="2">
                    <input type="text" maxlength="6" name="oldRegistZip_<{$key}>" id="oldRegistZip_<{$key}>" readonly="readonly" value="<{$item.cRegistZip}>" style="background-color:#CCC;width:50px;"/>
                    <{html_options name="oldRegistCity_<{$key}>" options=$meunCity  style="width:80px;" onchange="zip_area('oldRegistCity_<{$key}>','oldRegistArea_<{$key}>','oldRegistZip_<{$key}>')" id="oldRegistCity_<{$key}>" class="invoice" selected=$item.cRegistCity}>
                    <{html_options name="oldRegistArea_<{$key}>" id="oldRegistArea_<{$key}>" style="width:80px;" class="invoice" onchange="zip_change('oldRegistArea_<{$key}>','oldRegistZip_<{$key}>')" options=$item.cRegistAreaMenu selected=$item.cRegistZip}>
                    <input type="text" name="oldRegistAddr_<{$key}>" value="<{$item.cRegistAddr}>" style="width:500px;" class="invoice" id="oldRegistAddr_<{$key}>"/>
                </td>
            </tr>
            <tr>
                <th><span class="sign-red">*</span>通訊地址︰</th>
                <td  colspan="2">
                    <input type="checkbox" id="oldSame_<{$key}>" name="oldSame_<{$key}>" class="invoice" onclick="addr('old',<{$key}>);" <{$item.sameAddr}> > 同上
                    <input type="text" maxlength="6" id="oldBaseZip_<{$key}>" name="oldBaseZip_<{$key}>" value="<{$item.cBaseZip}>" readonly="readonly" style="background-color:#CCC;width:50px;"/>
                    <{html_options name="oldBaseCity_<{$key}>" id="oldBaseCity_<{$key}>" style="width:80px;" class="invoice"  onchange="zip_area('oldBaseCity_<{$key}>','oldBaseArea_<{$key}>','oldBaseZip_<{$key}>')" options=$meunCity selected=$item.cBaseCity}>
                    <{html_options name="oldBaseArea_<{$key}>" id="oldBaseArea_<{$key}>" style="width:80px;" class="invoice" onchange="zip_change('oldBaseArea_<{$key}>','oldBaseZip_<{$key}>')" options=$item.cBaseAreaMenu selected=$item.cBaseZip}>
                    <input type="text" name="oldBaseAddr_<{$key}>" class="invoice" style="width:500px;" id="oldBaseAddr_<{$key}>" value="<{$item.cBaseAddr}>"/>
                </td>
            </tr>
            <tr>
                <th>&nbsp;&nbsp;E-MAIL︰</th>
                <td colspan="2"><input type="text" name="oldEmail_<{$key}>" id="oldmail_<{$key}>" value="<{$item.cEmail}>"></td>
            </tr>
            <tr>
                <th>&nbsp;&nbsp;移轉範圍︰</th>
                <td>
                    <div><input type="button" style="padding: 5px;" onclick="transferArea('<{$cCertifiedId}>', '<{if $_iden == 'o'}>2<{else}>1<{/if}>', '<{$item.cId}>')" value="設定"></div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <fieldset style="width:80%" id="oldbankeField_<{$key}>">
                        <input type="button" value="新增銀行" name="oldAddBank_<{$key}>" id="oldAddBank_<{$key}>"  onclick="addBank('old','<{$key}>')" > <input type="checkbox" name="oldChecklist<{$key}>" id="oldChecklist_<{$key}>" onclick="clickChecklist('old','<{$key}>')" <{$item.BankChecked}>>全部不帶入點交單和出款
                        <input type="hidden" name="oldIndex_<{$key}>" id="oldIndex_<{$key}>" value="<{$item.OtherBankCount}>" />

                        <table border="0"  id="oldCopybank_<{$key}>" class="oldCopybank_<{$key}>" width="100%">
                            <tr>
                                <th colspan="2"> <input type="checkbox" name="oldChecklistBank_<{$key}>" class="oldChecklist_<{$key}>" id="oldChecklistBank_<{$key}>_0" value="1" onclick="checkChecklist('old','<{$key}>')" <{$item.cChecklistBankChecked}> />不帶入點交單和出款</th>
                            </tr>
                            <tr>
                                <th width="30%" style="text-align: right;">指定解匯總行︰<input type="hidden" name="otherBankId_<{$key}>[]" /></th>
                                <td width="25%" class="bank">
                                    <{html_options name="oldBankMain_<{$key}>[]" id="oldBankMain_<{$key}>_0" class="invoice" style="width:300px;" onchange="bank_change('oldBankMain_<{$key}>_0','oldcBankBranch_<{$key}>_0')" options=$menuBank selected=$item.cBankMain}>
                                </td>
                                <th width="30%" style="text-align: right;">指定解匯分行︰</th>
                                <td width="25%" class="bank">
                                    <{html_options name="oldcBankBranch_<{$key}>[]" id="oldcBankBranch_<{$key}>_0" class="invoice" style="width:300px;" options=$item.cBankBranchMenu selected=$item.cBankBranch}>
                                </td>
                            </tr>
                            <tr>
                                <th style="text-align: right;">指定解匯帳號︰</th>
                                <td><input type="text" maxlength="14" id="oldBankAccNum_<{$key}>_0" name="oldBankAccNum_<{$key}>[]" style="width:300px;" class="invoice" value="<{$item.cBankAccNum}>" /></td>
                                <th style="text-align: right;">指定解匯帳戶︰</th>
                                <td><input type="text" name="oldBankAccName_<{$key}>[]" id="oldBankAccName_<{$key}>_0" style="width:300px;"class="invoice" value="<{$item.cBankAccName}>"/></td>
                            </tr>
                            <tr >
                                <th style="text-align: right;">金額︰</th>
                                <td colspan="3"><input type="text" name="oldBankAccMoney_<{$key}>[]" id="oldBankAccMoney_<{$key}>_0" style="width:300px;"class="invoice" value="<{$item.cBankMoney}>"/></td>
                            </tr>
                            <tr>
                                <td colspan="4"><hr /></td>
                            </tr>
                        </table>
                        <{foreach from=$item.OtherBank key=k item=data}>
                        <table border="0"  id="oldCopybank_<{$key}>" class="oldCopybank_<{$key}>" width="100%">
                            <tr>
                                <th colspan="2"> <input type="checkbox" class="oldChecklist<{$key}>" name="oldChecklistBank_<{$key}>_<{$data.index}>" id="oldChecklistBank_<{$key}>_<{$data.index}>" value="1" onclick="checkChecklist('old','<{$key}>')" <{$data.cChecklistBankChecked}>/>不帶入點交單和出款</th>
                            </tr>
                            <tr>
                                <th width="30%" style="text-align: right;">指定解匯總行︰<input type="hidden" name="otherBankId_<{$key}>[]" value="<{$data.cId}>" /></th>
                                <td width="25%" class="bank">
                                    <{html_options name="oldBankMain_<{$key}>[]" id="oldBankMain_<{$key}>_<{$data.index}>" class="invoice" style="width:300px;" onchange="bank_change('oldBankMain_<{$key}>_<{$data.index}>','oldcBankBranch_<{$key}>_<{$data.index}>')" options=$menuBank selected=$data.cBankMain}>
                                </td>
                                <th width="30%" style="text-align: right;">指定解匯分行︰</th>
                                <td width="25%" class="bank">
                                    <{html_options name="oldcBankBranch_<{$key}>[]" id="oldcBankBranch_<{$key}>_<{$data.index}>" class="invoice" style="width:300px;" options=$data.bankBranch selected=$data.cBankBranch}>
                                </td>
                            </tr>
                            <tr>
                                <th style="text-align: right;">指定解匯帳號︰</th>
                                <td><input type="text" maxlength="14" id="oldBankAccNum_<{$key}>_<{$data.index}>" name="oldBankAccNum_<{$key}>[]" style="width:300px;" class="invoice" value="<{$data.cBankAccountNo}>" /></td>
                                <th style="text-align: right;">指定解匯帳戶︰</th>
                                <td><input type="text" name="oldBankAccName_<{$key}>[]" id="oldBankAccName_<{$key}>_<{$data.index}>" style="width:300px;"class="invoice" value="<{$data.cBankAccountName}>"/></td>
                            </tr>
                            <tr >
                                <th style="text-align: right;">金額︰</th>
                                <td colspan="3"><input type="text" name="oldBankAccMoney_<{$key}>[]" id="oldBankAccMoney_<{$key}>_0" style="width:300px;"class="invoice" value="<{$data.cBankMoney}>"/></td>
                            </tr>
                                <td colspan="4"><hr /></td>
                            </tr>
                        </table>
                        <{/foreach}>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
        </table>
        <{/foreach}>
        <hr />
        <div style="float:right;padding-right:10px;">
            <input type="button" value="增加對象" onclick="addRow()" id="addnew" />
            <input type="hidden" name="newRowCount" value="0" />
            <input type="hidden" name="save" value="ok" />
        </div>
        <table cellpadding="0" cellspacing="0" class="tb row" width="100%" >
            <tr>
                <td class="Title" colspan="3">
                    <{$_ide}>方資料　(<{$cCertifiedId}>)
                </td>
            </tr>
            <tr>
                <th><span class="sign-red">*</span>身分別：</th>
                <td><{$_ide}>方</td>
                <td rowspan="5">
                    <fieldset class="display" style="font-size:9pt;" id="newForeign_0">  <!-- display: none -->
                        <legend style="font-size:9pt;">非本國籍身份資料</legend>
                        <table = border="0" style="padding-left:10px;">
                            <tr>
                                <td style="font-size:9pt; width:300px;">
                                    國籍代碼： <input type="text" name="newCountry_0" id="newCountry_0" style="width:35px" disabled value="">
                                        <{html_options name="newCountryCode_0" id="newCountryCode_0" options=$menuCountry style="width: 100px;font-size:9pt;" onchange="showCountryCode('newCountry','0')" class="test"}>
                                    <br>租稅協定代碼：<input type="text" style="width:40px;" name="newTaxTreatyCode_0" id="newTaxTreatyCode_0" value="">
                                    護照號碼：<input type="text" name="newPassport_0" id="newPassport_0" style="width:100px" class="passport invoice">
                                </td>
                                <td style="font-size:9pt;">
                                    <label style="font-size:9pt;">
                                        <table border="0" style="width:200px;">
                                            <tr>
                                                <td style="width:80px;font-size:9pt;">已住滿183天?</td>
                                                <td>
                                                    <{html_radios name="newResidentLimit_0" id="newResidentLimit_0" options=$menuResident checked=$Resident  }>
                                                </td>
                                            </tr>
                                        </table>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size:9pt;">
                                    給付日期：<input type="text" name="newPaymentDate_0"  style="width:120px;" class="date_pick">
                                </td>
                                <td style="font-size:9pt;">
                                    <label style="font-size:9pt;">
                                        <table border="0" style="width:200px;">
                                            <tr>
                                                <td style="width:80px;font-size:9pt;">已加入健保?</td>
                                                <td style="width:20px;">
                                                    <input type="checkbox" name="newcNHITax_0" id="newcNHITax_0" value="1">
                                                </td>
                                                <td style="font-size:9pt;">是</td>
                                            </tr>
                                        </table>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><span class="sign-red">*</span>身分證號/統編︰</th>
                <td>
                    <input type="text" maxlength="10" style="width:120px;" class="idc invoice js-select" id="newIdentifyId_0" name="newIdentifyId_0" value="" onkeyup="checkID('new','0')"/>
                    <span id="newIdc_0_img" style="padding-left:5px;" ></span>
                </td>
            </tr>
            <tr>
                <th><span class="sign-red">*</span><{$_ide}>方姓名︰</th>
                <td><input type="text" name="newName_0" id="newName_0" style="width:120px;" value="" class="invoice"/></td>
            </tr>
            <tr id="newGuardian_0" class="display">
                <th>&nbsp;&nbsp;法定代理人:</th>
                <td><input type="text" name="newOtherName_0" id="newOtherName_0" value="" /></td>
            </tr>
            <tr>
                <th><span class="sign-red">*</span>行動電話︰</th>
                <td><input type="text" maxlength="10" style="width:120px;" name="newMobileNum_0" id="newMobileNum_0" value="" class="invoice" /></td>
            </tr>
            <tr>
                <th>&nbsp;&nbsp;出生日期︰</th>
                <td><input type="text" maxlength="10" name="newBirthdayDay_0" id="newBirthdayDay_0" style="width:120px;" value=""  class="calender input-text-big"  onclick="showdate(myform.newBirthdayDay_0)"/></td>
            </tr>
            <tr>
                <th>&nbsp;&nbsp;戶籍地址︰</th>
                <td colspan="2">
                    <input type="text" maxlength="6" name="newRegistZip_0" id="newRegistZip_0" readonly="readonly" value="" style="background-color:#CCC;width:50px;"/>
                    <{html_options name="newRegistCity_0" options=$meunCity  style="width:80px;" onchange="zip_area('newRegistCity_0','newRegistArea_0','newRegistZip_0')" id="newRegistCity_0" class="invoice"}>
                    <{html_options name="newRegistArea_0" id="newRegistArea_0" style="width:80px;" class="invoice" onchange="zip_change('newRegistArea_0','newRegistZip_0')" options=$meunArea}>
                    <input type="text" name="newRegistAddr_0" value="" style="width:500px;" class="invoice" id="newRegistAddr_0"/>
                </td>
            </tr>
            <tr>
                <th><span class="sign-red">*</span>通訊地址︰</th>
                <td  colspan="2">
                    <input type="checkbox" id="newSame_0" name="newSame_0" class="invoice" onclick="addr('new',0);"> 同上
                    <input type="text" maxlength="6" id="newBaseZip_0" name="newBaseZip_0" readonly="readonly" style="background-color:#CCC;width:50px;"/>
                    <{html_options name="newBaseCity_0" id="newBaseCity_0" style="width:80px;" class="invoice"  onchange="zip_area('newBaseCity_0','newBaseArea_0','newBaseZip_0')" options=$meunCity}>
                    <{html_options name="newBaseArea_0" id="newBaseArea_0" style="width:80px;" class="invoice" onchange="zip_change('newBaseArea_0','newBaseZip_0')" options=$meunArea}>
                    <input type="text" name="newBaseAddr_0" class="invoice" style="width:500px;" id="newBaseAddr_0"/>
                </td>
            </tr>
            <tr>
                <th>&nbsp;&nbsp;E-MAIL︰</th>
                <td colspan="2"><input type="text" name="newEmail_0" id="newEmail_0" value=""></td>
            </tr>
            <tr>
                <td colspan="3">
                    <fieldset style="width:80%" id="newbankeField_0">
                        <input type="button" value="新增銀行" name="newAddBank_0" id="newAddBank_0"  onclick="addBank('new','0')" >
                        <input type="checkbox" name="newChecklist_0" id="newChecklist_0" onclick="clickChecklist('new','0')">全部不帶入點交單和出款

                        <input type="hidden" name="newIndex_0" id="newIndex_0" value="0" />
                        <table border="0"  id="newCopybank_0" class="newCopybank_0" width="100%">
                            <tr>
                                <th colspan="2">
                                    <input type="checkbox" name="newChecklistBank_0_0" id="newChecklistBank_0_0" class="newChecklist_0" value="1" />不帶入點交單和出款
                                </th>
                            </tr>
                            <tr>
                                <th width="30%" style="text-align: right;">指定解匯總行︰</th>
                                <td width="25%" class="bank">
                                    <{html_options name="newBankMain_0[]" id="newBankMain_0_0" class="invoice" style="width:300px;" onchange="bank_change('newBankMain_0_0','newcBankBranch_0_0')" options=$menuBank }>
                                </td>
                                <th width="30%" style="text-align: right;">指定解匯分行︰</th>
                                <td width="25%" class="bank">
                                    <{html_options name="newcBankBranch_0[]" id="newcBankBranch_0_0" class="invoice" style="width:300px;" options=$menuBankBranch}>
                                </td>
                            </tr>
                            <tr>
                                <th style="text-align: right;">指定解匯帳號︰</th>
                                <td><input type="text" maxlength="14" id="newBankAccNum_0_0" name="newBankAccNum_0[]" style="width:300px;" class="invoice"/></td>
                                <th style="text-align: right;">指定解匯帳戶︰</th>
                                <td><input type="text" name="newBankAccName_0[]" id="newBankAccName_0_0" style="width:300px;"class="invoice"/></td>
                            </tr>
                            <tr>
                                <th style="text-align: right;">金額︰</th>
                                <td colspan="3"><input type="text" name="newBankAccMoney_0[]" id="newBankAccMoney_0_0" style="width:300px;"class="invoice"/></td>
                            </tr>
                            <tr>
                                <td colspan="4"><hr /></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
            </tr>
        </table>
        <{if $checkSave == 1}>
        <div style="float:center;text-align:center"><input type="button" id="save_btn" value="儲存" class="xxx-button" onclick="catchData()" /></div>
        <{/if}>
    </form>

    <div id="dialog_save"></div>
    <div id="dialog"></div>
</body>
</html>