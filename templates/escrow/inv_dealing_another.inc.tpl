<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
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

    var row = "<{$index}>";
    var close = "<{$close}>";
    var dep = "<{$smarty.session.member_pDep}>";

    if (row > 0) {

        $(".copy").hide();
    }else
    {
        $(".copy [name='row[]']").val(0);
    }

        //已匯出進銷檔，所以禁止修改
    if (close =='Y' && ( dep != 9 && dep != 10 && dep != 1)) {
         var array = "input,select,textarea";
                   
        $("#result_area").find(array).each(function() {
            $(this).attr('disabled', true);
           
        }); 

    }
    //

    $("[name='add']").on('click', function() {
        
        $('.copy').clone().insertAfter('.loc:last').attr('class', 'loc');

        row++;
       
        $('.loc:last').show();
        $('.loc:last').attr('id','loc'+row);

        $(".loc .del").val('刪除');       
                
        $(".loc [name='inv_another_country[]']:last").attr('id', 'inv_another_country'+row);
        $(".loc [name='inv_another_country[]']:last").attr('onchange', "getArea('inv_another_country"+row+"','inv_another_area"+row+"','inv_another_zip"+row+"')");

        $(".loc [name='inv_another_area[]']:last").attr('id', 'inv_another_area'+row);
        $(".loc [name='inv_another_area[]']:last").attr('onchange', "getZip('inv_another_area"+row+"','inv_another_zip"+row+"')");

        $(".loc [name='inv_another_zip[]']:last").attr('id', 'inv_another_zip'+row);

        $(".loc #inv_another_areaR:last").attr('id', 'inv_another_area'+row+'R');
        $(".loc #inv_another_zipF:last").attr('id', 'inv_another_zip'+row+'F');

        //onkeyup="checkID($(this),$('#Idc_0_img'))"
        $(".loc [name='inv_another_IdentifyId[]']:last").attr('onkeyup', "checkID($(this),$('#Idc_img_"+row+"'))");
        $(".loc [name='Idc_img']:last").attr('id', 'Idc_img_'+row);

        $(".loc:last").val(row);

    });
     var  change = 0;

    $(document).on("change",".cg",function(){
        change = 1;
       
     });
    // $(".change").live('change', function(){
    //     alert('ck');
    // });

    $("[name='save']").live('click', function() {
        //發票指定對象地址檢查
        if($("[name='inv_another_zipF']").val() == '' || $("[name='inv_another_addr[]']").val() == '' ) {
            alert('地址填寫不完整');
            return;
        }
        var j;
        for(j=row; j >=0; j--) {
            if($('#inv_another_zip'+j+'F').val() == '') {
                alert('地址填寫不完整!');
                return;
            }
        }

       if (change==1) {
            if (confirm("修改指定對象會影響到「利息」的指定對象，是否要更改?")) {
                $("[name='form']").submit();
            }else{
                 $("[name='back']").submit();
            }
       }else{
         $("[name='form']").submit();
       }

        // $("[name='form']").submit();

          
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
function del(id){
    $("[name='id']").val(id);
    $("[name='form_del']").submit();

  
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

</style>
</head>
<body style="background-color:#F8ECE9;">
<center>
<h1>發票指定對象(<{$type}>)</h1>
<form action="inv_dealing_another_del.php" name="form_del" method="POST">
    <input type="hidden" name="id" />
    <input type="hidden" name="cCertifiedId" value="<{$cId}>" />
    <input type="hidden" name="cSignCategory" value="<{$cSignCategory}>" />
</form>
<form action="inv_dealing.php" method="POST" name="back">
    <input type="hidden" name="cCertifiedId" value="<{$cId}>" />
    <input type="hidden" name="cSignCategory" value="<{$cSignCategory}>" />
<!--     <input type="hidden" name="iden" value="<{$iden}>" />
    <input type="hidden" name="type" value="<{$type}>" /> -->
</form>
<div id="show"></div>
<div id="result_area">
    <!-- 其他-->
    <fieldset style="width:1000px;">
    
        <div style="float:left;width:100px;text-align:left;font-size:9pt;">姓名</div>
        <div style="float:left;width:150px;text-align:left;font-size:9pt;">統編/身分證</div>
        <div style="float:left;width:400px;text-align:left;font-size:9pt;">地址</div>
        <div style="float:left;width:100px;text-align:left;font-size:9pt;">電話</div>
                    
        <div style="">
            <{if $id == ''}>
                <input type="button" name="add" value="新增">
            <{/if}>
            &nbsp;
        </div>
        <form method="POST" name="form">
          
        <div class="loc" id="loc">
            <{foreach from=$data_another key=key item=item}>  
            
                    <{if $item.cInvoiceDonate==1}>
                       <{assign var='ck2' value='checked=checked'}>
                    <{else}>
                         <{assign var='ck2' value=''}>
                    <{/if}>
                    <hr>
                    <input type="hidden" name="row[]" value="<{$item.row}>">
                    <input type="hidden" name="another_id[]" value="<{$item.cId}>">
                    <input type="hidden" name="int_id[]" value="<{$item.cIntId}>">
                    <input type="hidden" name='inv_another_iden[]' value="<{$item.cDBName}>_<{$item.cTBId}>">
                   
                    <div style="float:left;width:100px;text-align:left;">
                        <input type="text" style="width:80px" value="<{$item.cName}>" name="inv_another_name[]" class="cg"/>
                    </div>
                    <div style="float:left;width:150px;text-align:left;">
                        <input type="text" style="width:80px" name="inv_another_IdentifyId[]" id="inv_another_IdentifyId<{$item.row}>" maxlength="10" value="<{$item.cIdentifyId}>" class="cg" onkeyup="checkID($(this),$('#Idc_<{$item.row}>_img'))"/>
                        <span id="Idc_<{$item.row}>_img" style="padding-left:5px;" ><{$item.checkIDImg}></span>
                    </div>
                    <div style="float:left;width:400px;text-align:left;">
                        <input type="hidden" name="inv_another_zip[]" id="inv_another_zip<{$item.row}>" value="<{$item.cInvoiceZip}>" />
                        <input type="text" maxlength="6" name="inv_another_zipF" id="inv_another_zip<{$item.row}>F" style="width:30px;" readonly="readonly" value="<{$item.cInvoiceZip}>" class="cg"/>
                        <select  name="inv_another_country[]" id="inv_another_country<{$item.row}>" onchange="getArea('inv_another_country<{$item.row}>','inv_another_area<{$item.row}>','inv_another_zip<{$item.row}>')" class="cg">
                                    <{$item.city}>
                        </select>
                        <span id="inv_another_area<{$item.row}>R">
                            <select  name="inv_another_area[]" id="inv_another_area<{$item.row}>" onchange="getZip('inv_another_area<{$item.row}>','inv_another_zip<{$item.row}>')" class="cg">
                                    <{$item.area}>
                            </select>
                        </span>
                        <input type="text" style="width:200px" name="inv_another_addr[]" value="<{$item.cInvoiceAddr}>" class="cg"/>
                    </div>
                    <div style="float:left;width:100px;">
                       <input type="text" name="inv_another_phone[]" value="<{$item.cPhone}>" maxlength="10" class="cg"/>
                    </div>

                    <div style=""><input type="button" class="del" onclick="del(<{$item.cId}>)" value="刪除"></div>
            <{/foreach}>
        </div>
        
            <input type="hidden" name="check" value="1" />
            <input type="hidden" name="CertifiedId" value="<{$cCertifiedId}>" />
            <input type="hidden" name="cSignCategory" value="<{$cSignCategory}>" />
            <input type="hidden" name="iden" value="<{$iden}>" />
            <input type="hidden" name="type" value="<{$type}>" />
            <input type="hidden" name="cId" value="<{$cId}>">
            <input type="hidden" name="id" value="<{$id}>">

            <div class="copy" >
                <hr>
                            
                <input type="hidden" name="row[]" value="">
                <input type="hidden" name='inv_another_iden[]' value="<{$iden}>">
                
                <div style="float:left;width:100px;text-align:left;">
                    <input type="text" style="width:80px" name="inv_another_name[]" />
                </div>
                <div style="float:left;width:150px;text-align:left;">
                    <input type="text" style="width:80px" name="inv_another_IdentifyId[]" id="inv_another_IdentifyId_0" maxlength="10" onkeyup="checkID($(this),$('#Idc_img_0'))"/>
                    <span id="Idc_img_0" style="padding-left:5px;" name="Idc_img" ></span>
                </div>
                <div style="float:left;width:400px;text-align:left;">
                    <input type="hidden" name="inv_another_zip[]" id="inv_another_zip" value="" />
                    <input type="text" maxlength="6" name="inv_another_zipF" id="inv_another_zipF" style="width:30px;" readonly="readonly" value="" />
                    <select  name="inv_another_country[]" id="inv_another_country" onchange="getArea('inv_another_country','inv_another_area','inv_another_zip')">
                            <{$inv_another_country}>
                    </select>
                    <span id="inv_another_areaR">
                    <select  name="inv_another_area[]" id="inv_another_area" onchange="getZip('inv_another_area','inv_another_zip')">
                            <{$inv_another_area}>
                    </select>
                    </span>
                    <input type="text" style="width:200px" name="inv_another_addr[]" value="" />
                </div>
                    <div style="float:left;width:100px;">
                        <input type="text" name="inv_another_phone[]"  maxlength="10"/>
                    </div>

              
                <div style=""><input type="button" class="del" onclick="del()" value="刪除"></div>
            </div>
        </form>
        
        

        <hr>
        <{if $cSignCategory==1}>
            <input type="button" name="save" value="　儲存　">
            <input type="button"  name="back" value="返回" />
        <{/if}>
    </fieldset>
</div>
    <div style="height:20px;"></div>
    <div style="width:740px;padding:20 40 20 40;text-align:right;height:600px;">
</center>
</body>
</html>
           


