<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>開發票對象</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="/js/ROCcalender_limit.js"></script>
<script type="text/javascript" src="/js/rocCal.js"></script>
<script src="/js/IDCheck.js"></script>
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

    var row = "<{$index}>";

    $(".copy").hide();
    
    if (row > 0) {

        
    }else
    {
        $(".copy [name='row[]']").val(0);
    }
    
    $("[name='add']").live('click', function() {
        
        $('.copy').clone().insertAfter('.loc:last').attr('class', 'loc');

    
        $('.loc:last').show();
        $('.loc:last').attr('id','loc'+row);

        // $(".loc .del").val('刪除');       
        
        // //戶籍地址
        // $(".loc [name='int_another_country[]']:last").attr('id', 'int_another_country'+row);
        // $(".loc [name='int_another_country[]']:last").attr('onchange', "getArea('int_another_country"+row+"','int_another_area"+row+"','int_another_zip"+row+"')");

        // $(".loc [name='int_another_area[]']:last").attr('id', 'int_another_area'+row);
        // $(".loc [name='int_another_area[]']:last").attr('onchange', "getZip('int_another_area"+row+"','int_another_zip"+row+"')");

        // $(".loc [name='int_another_zip[]']:last").attr('id', 'int_another_zip'+row);

        // $(".loc #int_another_areaR:last").attr('id', 'int_another_area'+row+'R');
        // $(".loc #int_another_zipF:last").attr('id', 'int_another_zip'+row+'F');
        // // int_another_caddr
        // $(".loc [name='int_another_addr[]']:last").attr('id', 'int_another_addr'+row);
        //通訊
         $(".loc [name='int_another_ccountry[]']:last").attr('id', 'int_another_ccountry'+row);
        $(".loc [name='int_another_ccountry[]']:last").attr('onchange', "getArea('int_another_ccountry"+row+"','int_another_carea"+row+"','int_another_czip"+row+"')");

        $(".loc [name='int_another_carea[]']:last").attr('id', 'int_another_carea'+row);
        $(".loc [name='int_another_carea[]']:last").attr('onchange', "getZip('int_another_carea"+row+"','int_another_czip"+row+"')");

        $(".loc [name='int_another_czip[]']:last").attr('id', 'int_another_czip'+row);

        $(".loc #int_another_careaR:last").attr('id', 'int_another_carea'+row+'R');
        $(".loc #int_another_czipF:last").attr('id', 'int_another_czip'+row+'F');

        $(".loc [name='int_another_caddr[]']:last").attr('id', 'int_another_caddr'+row);

        $(".loc [name='int_another_IdentifyId[]']:last").attr('onkeyup', "checkID($(this),$('#Idc_img_"+row+"'))");
        $(".loc [name='Idc_img']:last").attr('id', 'Idc_img_'+row);

        // // int_same

        // $(".loc [name='int_same']:last").attr('id', 'int_same'+row);
        // $(".loc [name='int_same']:last").attr('onclick', "same_addr(int_same"+row+","+row+")");

        // //解匯銀行
        // $(".loc #int_another_bank:last").attr('id', 'int_another_bank'+row); //change_bank('int_another_bank','int_another_bankbranch')
        // $(".loc [name='int_another_bank[]']:last").attr('onchange', "change_bank('int_another_bank"+row+"','int_another_bankbranch"+row+"')");
        // //解匯分行
        //   $(".loc #int_another_bankbranch:last").attr('id', 'int_another_bankbranch'+row); //change_bank('int_another_bank','int_another_bankbranch')
        //國家代碼
            $(".loc #int_another_fcountry:last").attr('id', 'int_another_fcountry'+row);
            
            $(".loc [name='fcountry[]']:last").attr('onchange', "country_code('fcountry"+row+"','int_another_fcountry"+row+"')");
            $(".loc #fcountry:last").attr('id', 'fcountry'+row);
        //日曆
        $(".loc [name='int_another_pdate[]']:last").attr('onclick', "showdate(form.int_another_pdate"+row+")");
        $(".loc #int_another_pdate:last").attr('id', 'int_another_pdate'+row);
        //radio 
        $(".loc .int_another_rlimit").attr('name', 'int_another_rlimit'+row);
        $(".loc .int_another_NHITax").attr('name', 'int_another_NHITax'+row);
 
         $(".loc [name='del']:last").attr('onclick',"del2("+row+")");
        $(".loc [name='row[]']:last").val(row);
        row++;
    });


    var  change = 0;

     $(document).on("change",".ch",function(){
        change = 1;
       
     });

    $("[name='save']").live('click', function() {

        if (change==1) {
            if (confirm("修改指定對象會影響到「發票」的指定對象，是否要更改?")) {
                $("[name='form']").submit();
            }else{
                 $("[name='back']").submit();
            }
       }else{
         $("[name='form']").submit();
       }


          
    });

    $("[name='back']").live('click', function() {
        
        $("[name='back']").submit();
    });

   
});
function getArea(ct,ar,zp) {
    var url = 'listArea.php' ;
    var ct = $('#' + ct + ' :selected').val() ;
                
    $('#' + zp + '').val('') ;
    $('#' + zp + 'F').val('') ;
    $('#' + ar + 'R').empty() ;
                
    $.post(url,{"city":ct},function(txt) {
        var str = '<select class="input-text-big" name="' + ar + '" id="' + ar + '" onchange=getZip("' + ar + '","' + zp + '")>' ;
        str = str + txt + '</select>' ;
        $('#' + ar + 'R').html(str) ;
    }) ;
}
            
function getZip(ar,zp) {
    var zips = $('#' + ar + ' :selected').val() ;
    
    $('#' + zp + '').val(zips) ;
    $('#' + zp + 'F').val(zips.substr(0,3)) ;
}
function int_del(id)
{

    $("[name='id']").val(id);
    $("[name='form_del']").submit();

  
}
function change_bank(name,name2)
{
    GetBankBranchList($('#'+name),$('#'+name2), null);                             
}

function GetBankBranchList(bank, branch, sc) {
    $(branch).prop('disabled',true) ;
                
    var request = $.ajax({  
        url: "../includes/maintain/bankbranchsearch.php",
        type: "POST",
        data: {
            bankcode: $(bank).val()
        },
        dataType: "json"
    });
    request.done(function( data ) {
        $(branch).children().remove().end();
        $(branch).append('<option value="">------</option>')
        $.each(data, function (key, item) {
            if (key == sc ) {
                $(branch).append('<option value="'+key+'" selected>'+item+'</option>');
            } else {
                $(branch).append('<option value="'+key+'">'+item+'</option>');
            }
            
        });
    });
                
    $(branch).prop('disabled',false) ;
}

function country_code(name,name2)
{
    $("#"+name2).val($("#"+name).val());
}
function same_addr(name,row)
{
   
    var check = $(name).attr('checked');

    // alert(check);

    if (check!='checked') {
        $("#int_another_czip"+row).val('');
        $("#int_another_ccountry"+row).val('');
        $("#int_another_carea"+row).val('');
        $("#int_another_caddr"+row).val('');  
        $("#int_another_czip"+row+"F").val('');
         $("#int_another_carea"+row+" option").remove();

    }else{
        var zip = $("#int_another_zip"+row).val();
        var city = $("#int_another_country"+row).val();
        var area = $("#int_another_area"+row).val();
        var addr = $("#int_another_addr"+row).val();


        $("#int_another_czip"+row).val(zip);
        $("#int_another_czip"+row+"F").val(zip);
        $("#int_another_ccountry"+row).val(city);

        $("#int_another_carea"+row+" option").remove();
        $("#int_another_area"+row+" option").clone().appendTo("#int_another_carea"+row);

        $("#int_another_carea"+row).val(area);
        $("#int_another_caddr"+row).val(addr);  
    }
    

}

function del2(id)
{
    $("#loc"+id).remove();
}
function checkID(dom,domImg){
    
    //inv_another_IdentifyId_0
    var _val = dom.val();//newIdentifyId_0

    var _id =  domImg;//newIdc_0_img
    
    // $("#"+cat+"Guardian_"+id).attr('class', 'display'); 
    if (checkUID(_val)) {
        
        _id.html('<img src="/images/ok.png">') ;
        if (_val.length == 8) {
            // $("#"+cat+"Guardian_"+id).attr('class', 'show'); //'#guardian_'+no guardian_0 newGuardian_1
                // console.log("#"+cat+"Guardian_"+id);
        }
    }else {

        _id.html('<img src="/images/ng.png">') ;
        
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
fieldset {
    border-radius: 6px;
}
/*input {
    text-align:right;
}*/
.loc td{
    padding-left: 10px;

    border: 1px solid #FFF;
}
.loc th{
    text-align: right;
  
    background-color: #E4BEB1;
    border: 1px solid #FFF;
}
.copy td{
     padding-left: 10px;

    border: 1px solid #FFF;
}
.copy th{
     text-align: right;

    background-color: #E4BEB1;
    border: 1px solid #FFF;
}

.countrycode{
    width: 150px;
    font-size:9pt;

}
</style>
</head>
<body style="background-color:#F8ECE9;">
<center>
<div id="result_area">
<h1>指定對象(<{$type}>)</h1>
<form action="int_dealing_another_del.php" name="form_del" method="POST">
    <input type="hidden" name="id" />
    <input type="hidden" name="cCertifiedId" value="<{$cId}>" />
    <input type="hidden" name="cSignCategory" value="<{$cSignCategory}>" />
</form>
<form action="int_dealing.php" method="POST" name="back">
    <input type="hidden" name="cCertifiedId" value="<{$cId}>" />
    <input type="hidden" name="cSignCategory" value="<{$cSignCategory}>" />
<!--     <input type="hidden" name="iden" value="<{$iden}>" />
    <input type="hidden" name="type" value="<{$type}>" /> -->
</form>
    <!-- 其他-->
<form method="POST" name="form">
    <fieldset style="width:900px;">
        <div style="text-align:right"> 
            <{if $id == ''}>
                <input type="button" name="add" value="新增">
            <{/if}>
        </div>
        <div class="loc" id="loc">
        <{foreach from=$data_another key=key item=item}>
              <input type="hidden" name="row[]" value="<{$item.row}>">
                <input type="hidden" name="another_id[]" value="<{$item.cId}>">
                <input type="hidden" name="inv_id[]" value="<{$item.cInvId}>">
                <input type="hidden" name='int_another_iden[]' value="<{$item.cDBName}>_<{$item.cTBId}>">
            <table border="0" cellpadding="3" cellspacing="3"  width="900px">
                <tr>
                    <td colspan="4">
                        <input type="button" class="del" onclick="int_del(<{$item.cId}>)" value="刪除">
                    </td>
                </tr>
                <tr>
                    <th>姓名：</th>
                    <td>
                        <input type="text" style="width:80px" value="<{$item.cName}>" name="int_another_name[]" class="ch"/>
                    </td>
                    <th>統編/身分證：</th>
                    <td>
                        <input type="text" style="width:80px" name="int_another_IdentifyId[]" maxlength="10" value="<{$item.cIdentifyId}>" class="ch" onkeyup="checkID($(this),$('#Idc_<{$item.row}>_img'))"/>
                        <span id="Idc_<{$item.row}>_img" style="padding-left:5px;" ><{$item.checkIDImg}></span>
                    </td>
                </tr>
                <tr>
                    <th>電話：</th>
                    <td colspan="3"><input type="text" name="int_another_phone[]" value="<{$item.cMobileNum}>" maxlength="10" class="ch"/></td>
                </tr>
               <!--  <tr>
                    <th>戶籍地址：</th>
                    <td colspan="3">
                        <input type="hidden" name="int_another_zip[]" id="int_another_zip<{$item.row}>" value="<{$item.cRegistZip}>" />
                        <input type="text" maxlength="6" name="int_another_zipF" id="int_another_zip<{$item.row}>F" style="width:30px;" readonly="readonly" value="<{$item.cRegistZip}>"/>
                        <select  name="int_another_country[]" id="int_another_country<{$item.row}>" onchange="getArea('int_another_country<{$item.row}>','int_another_area<{$item.row}>','int_another_zip<{$item.row}>')">
                                    <{$item.city}>
                        </select>
                        <span id="int_another_area<{$item.row}>R">
                            <select  name="int_another_area[]" id="int_another_area<{$item.row}>" onchange="getZip('int_another_area<{$item.row}>','int_another_zip<{$item.row}>')">
                                    <{$item.area}>
                            </select>
                        </span>
                        <input type="text" style="width:200px" name="int_another_addr[]" id="int_another_addr<{$item.row}>" value="<{$item.cRegistAddr}>" />
                    </td>
                </tr> -->
                <tr>
                    <th>通訊地址：</th>
                    <td colspan="3">
                        <input type="hidden" name="int_another_czip[]" id="int_another_czip<{$item.row}>" value="<{$item.cBaseZip}>" />
                        <input type="text" maxlength="6" name="int_another_czipF" id="int_another_czip<{$item.row}>F" style="width:30px;" readonly="readonly" value="<{$item.cBaseZip}>"/>
                        <select  name="int_another_ccountry[]" id="int_another_ccountry<{$item.row}>" onchange="getArea('int_another_ccountry<{$item.row}>','int_another_carea<{$item.row}>','int_another_czip<{$item.row}>')" class="ch">
                                    <{$item.ccity}>
                        </select>
                        <span id="int_another_carea<{$item.row}>R">
                            <select  name="int_another_carea[]" id="int_another_carea<{$item.row}>" onchange="getZip('int_another_area<{$item.row}>','int_another_zip<{$item.row}>')" class="ch">
                                    <{$item.carea}>
                            </select>
                        </span>
                        <input type="text" style="width:200px" name="int_another_caddr[]" id="int_another_caddr<{$item.row}>" value="<{$item.cBaseAddr}>" class="ch"/>
                       <!--  <{if $item.cRegistZip==$item.cBaseZip && $item.cRegistAddr==$item.cBaseAddr}>
                            <input type="checkbox" name="int_same" id="int_same<{$item.row}>" onclick="same_addr(int_same<{$item.row}>,<{$item.row}>)" checked/>同上
                        <{else}>
                            <input type="checkbox" name="int_same" id="int_same<{$item.row}>" onclick="same_addr(int_same<{$item.row}>,<{$item.row}>)" />同上
                        <{/if}> -->
                    </td>
                </tr>
               <!--  <tr>
                    <th>解匯銀行：</th>
                    <td>
                        <{html_options name="int_another_bank[]" id="int_another_bank<{$item.row}>" options=$menu_bank onChange="change_bank('int_another_bank<{$item.row}>','int_another_bankbranch<{$item.row}>')" selected="<{$item.cBankMain}>"}>
                    </td>
                    <th>解匯分行：</th>
                    <td>
                        <{html_options name="int_another_bankbranch[]" id="int_another_bankbranch<{$item.row}>" class="input-text-per" options=$item.menu_branch  selected="<{$item.cBankBranch}>"}>
                    </td>
                </tr>
                <tr>
                    <th>解匯銀行戶名：</th>
                    <td><input type="text" name="int_another_bankaccname[]" value="<{$item.cBankAccName}>" /></td>
                    <th>解匯銀行帳號：</th>
                    <td><input type="text" name="int_another_bankaccnumber[]" value="<{$item.cBankAccNum}>" /></td>
                </tr> -->
                <tr>
                    <th>國家代碼：</th>
                    <td>
                        <input type="text" name="int_another_fcountry[]" id="int_another_fcountry<{$item.row}>" style="width:35px" value="<{$item.cCountryCode}>" disabled>
                        <{html_options name="fcountry[]" id="fcountry<{$item.row}>" options=$menu_countrycode  class="countrycode ch" onChange="country_code('fcountry<{$item.row}>','int_another_fcountry<{$item.row}>')" selected="<{$item.cCountryCode}>" }> 
                        
                    </td>
                    <th>租稅協定代碼：</th>
                    <td><input type="text" name="int_another_ftax[]" id="" style="width:50px" value="<{$item.cTaxTreatyCode}>" class="ch"/></td>
                </tr>
                <tr>
                    <th>是否住滿183天：</th>
                    <td>
                        <{if $item.cResidentLimit==1}>
                            <input type="radio" name="int_another_rlimit<{$item.row}>"  value="0"  class="ch"/>否
                            <input type="radio" name="int_another_rlimit<{$item.row}>"  value="1"  checked class="ch"/>是
                        <{else}>
                            <input type="radio" name="int_another_rlimit<{$item.row}>"  value="0" checked class="ch"/>否
                            <input type="radio" name="int_another_rlimit<{$item.row}>"  value="1"  class="ch"/>是
                        <{/if}>
                        
                     </td>
                    <th>給付日期：</th>
                    <td><input type="text" name="int_another_pdate[]" id="int_another_pdate<{$item.row}>" onclick="showdate(form.int_another_pdate<{$item.row}>)" value="<{$item.cPaymentDate}>" class="ch"/></td>
                </tr>
                <tr>
                    <th>是否已加入健保：</th>
                    <td>
                        <{if $item.cNHITax==1}>
                            <input type="radio" name="int_another_NHITax<{$item.row}>" value="0" class="ch">否
                            <input type="radio" name="int_another_NHITax<{$item.row}>" value="1" checked="checked" class="ch">是
                        <{else}>
                            <input type="radio" name="int_another_NHITax<{$item.row}>" value="0" checked="checked" class="ch">否
                            <input type="radio" name="int_another_NHITax<{$item.row}>" value="1" >是
                        <{/if}>
                    </td>
                    <th>護照號碼</th>
                    <td><input type="text" name="int_another_postid[]" value="<{$item.cPostId}>" /></td>
                </tr>
            </table>
            <{/foreach}>
        </div>
        
        <input type="hidden" name="check" value="1" />
        <input type="hidden" name="CertifiedId" value="<{$cCertifiedId}>" />
        <input type="hidden" name="cSignCategory" value="<{$cSignCategory}>" />
        <input type="hidden" name="iden" value="<{$iden}>" />
        <input type="hidden" name="type" value="<{$type}>" />
        <input type="hidden" name="cId" value="<{$cId}>">
        <input type="hidden" name="id" value="<{$id}>">

        
        <div class="copy">
            <hr>
            <table border="0" cellpadding="3" cellspacing="0"  width="900px">
                <tr>
                    <td colspan="4">
                        <input type="button" class="del" onclick="del2()" name="del" value="刪除">
                    </td>
                </tr>
                <tr>
                    <th>
                        <input type="hidden" name="row[]" value="">
                        <input type="hidden" name='int_another_iden[]' value="<{$iden}>">
                        姓名：
                    </th>
                    <td>
                        <input type="text" style="width:80px" name="int_another_name[]" />
                    </td>
                    <th>統編/身分證：</th>
                    <td>
                        <input type="text" style="width:80px" name="int_another_IdentifyId[]" maxlength="10" onkeyup="checkID($(this),$('#Idc_img_0'))"/>
                        <span id="Idc_img_0" style="padding-left:5px;" name="Idc_img" ></span>
                    </td>
                </tr>
                 <tr>
                    <th>電話：</th>
                    <td colspan="3"><input type="text" name="int_another_phone[]" value="<{$item.cPhone}>" maxlength="10"/></td>
                </tr>
                <!--  <tr>
                    <th>戶籍地址：</th>
                    <td colspan="3">
                         <input type="hidden" name="int_another_zip[]" id="int_another_zip" value="" />
                        <input type="text" maxlength="6" name="int_another_zipF" id="int_another_zipF" style="width:30px;" readonly="readonly" value="" />
                        <select  name="int_another_country[]" id="int_another_country" onchange="getArea('int_another_country','int_another_area','int_another_zip')">
                                <{$int_another_country}>
                        </select>
                        <span id="int_another_areaR">
                        <select  name="int_another_area[]" id="int_another_area" onchange="getZip('int_another_area','int_another_zip')">
                                <{$int_another_area}>
                        </select>
                        </span>
                        <input type="text" style="width:200px" name="int_another_addr[]" value="" id="int_another_addr"/>
                    </td>
                </tr> -->
                <tr>
                    <th>地址：</th>
                    <td colspan="3">
                         <input type="hidden" name="int_another_czip[]" id="int_another_czip" value="<{$item.row}>" />
                        <input type="text" maxlength="6" name="int_another_czipF" id="int_another_czipF" style="width:30px;" readonly="readonly" value="" />
                        <select  name="int_another_ccountry[]" id="int_another_ccountry" onchange="getArea('int_another_ccountry','int_another_carea','int_another_czip')">
                                <{$int_another_country}>
                        </select>
                        <span id="int_another_careaR">
                        <select  name="int_another_carea[]" id="int_another_carea" onchange="getZip('int_another_carea','int_another_czip')">
                                <{$int_another_area}>
                        </select>
                        </span>
                        <input type="text" style="width:200px" name="int_another_caddr[]"  value="" id="int_another_caddr" />
                        <!-- <input type="checkbox" name="int_same" id="int_same" onclick="same_addr(int_same,'')" />同上 -->
                    </td>
                </tr>
               <!--  <tr>
                    <th>解匯銀行：</th>
                    <td>
                        <{html_options name="int_another_bank[]" id="int_another_bank" options=$menu_bank onChange="change_bank('int_another_bank','int_another_bankbranch')"}>
                    </td>
                    <th>解匯分行：</th>
                    <td>
                        <select name="int_another_bankbranch[]" id="int_another_bankbranch" class="input-text-per" >
                           <option>請選擇</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>解匯銀行戶名：</th>
                    <td><input type="text" name="int_another_bankaccname[]" /></td>
                    <th>解匯銀行帳號：</th>
                    <td><input type="text" name="int_another_bankaccnumber[]" id="" /></td>
                </tr> -->
                <tr>
                    <th>國家代碼：</th>
                    <td>
                        <input type="text" name="int_another_fcountry[]" id="int_another_fcountry" style="width:35px" value="<{$data_owner.cCountryCode}>" disabled>
                        <{html_options name="fcountry[]" id="fcountry" options=$menu_countrycode  class=countrycode onChange="country_code('fcountry','int_another_fcountry')" }> 
                        
                    </td>
                    <th>租稅協定代碼：</th>
                    <td><input type="text" name="int_another_ftax[]" id="" style="width:50px" /></td>
                </tr>
                <tr>
                    <th>是否住滿183天：</th>
                    <td>
                        <input type="radio" name="int_another_rlimit" class="int_another_rlimit" value="0" checked />否
                        <input type="radio" name="int_another_rlimit" class="int_another_rlimit" value="1"  />是
                     </td>
                    <th>給付日期：</th>
                    <td><input type="text" name="int_another_pdate[]" id="int_another_pdate" onclick="showdate(form.int_another_pdate)" /></td>
                </tr>
                <tr>
                    <th>是否已加入健保：</th>
                    <td>
                        <input type="radio" name="int_another_NHITax" class="int_another_NHITax" value="0" checked="checked">否
                        <input type="radio" name="int_another_NHITax" class="int_another_NHITax" value="1">是
                    </td>
                    <th>護照號碼</th>
                    <td><input type="text" name="int_another_postid[]"  /></td>
                </tr>
                
            </table>
        </div>
        

            
</form>
        
        

        <hr>
        <{if $cSignCategory==1}>
            <input type="button" name="save" value="　儲存　">
            <input type="button"  name="back" value="返回" />
        <{/if}>
    </fieldset>

    <div style="height:20px;"></div>
    <div style="width:740px;padding:20 40 20 40;text-align:right;height:600px;">
</div>
</center>
</body>
</html>
           


