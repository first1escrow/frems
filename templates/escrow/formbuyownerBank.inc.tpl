<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>其他出款帳號</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="/js/rocCal.js"></script>
<script src="/js/IDCheck.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    // 初始化所有銀行下拉選單的 Select2
    $('select[name="oldBankMain[]"]').select2({
        placeholder: "請選擇總行",
        allowClear: true,
        width: '300px'
    });
    
    $('select[name="oldcBankBranch[]"]').select2({
        placeholder: "請選擇分行", 
        allowClear: true,
        width: '300px'
    });
    
    $('select[name="newBankMain[]"]').select2({
        placeholder: "請選擇總行",
        allowClear: true,
        width: '300px'
    });
    
    $('select[name="newcBankBranch[]"]').select2({
        placeholder: "請選擇分行",
        allowClear: true,
        width: '300px'
    });

});

function bank_change(Bmain,Bbranch) {
    var url = 'bankConvert.php' ;
    var _bank = $('#'+Bmain).val() ;
    $.post(url,{'bk':_bank},function(txt) {
        // 銷毀舊的 Select2 實例
        $('#'+Bbranch).select2('destroy');
        $('#'+Bbranch).html(txt);
        // 重新初始化 Select2
        $('#'+Bbranch).select2({
            placeholder: "請選擇分行",
            allowClear: true,
            width: '300px'
        });
    }) ;
}

function addBank(cat,id){
    $('select[name="newBankMain[]"]').select2('destroy');
    $('select[name="newcBankBranch[]"]').select2('destroy');
    // return ;

    var count = parseInt($("#"+cat+'Index_'+id).val());//new_ChecklistBank_0 newIndex_0
    
    count++;
    
    var clonedRow = $("#"+cat+"Copybank_"+id).clone(true);
    clonedRow.attr('id', 'newCopybank'+count);
    clonedRow.find('input').val('');
    clonedRow.find('select').val('');
    
    // 銷毀克隆元素中的 Select2 實例
    // clonedRow.find('select').select2('destroy');
    
    clonedRow.find('[name*="'+cat+'BankMain[]"]').attr({
        id: cat+'BankMain_'+count,
        onchange:"bank_change('"+cat+"BankMain_"+count+"','"+cat+"cBankBranch_"+count+"')"
    });

    clonedRow.find('[name*="'+cat+'cBankBranch[]"] option').remove();

    clonedRow.find('[name*="'+cat+'cBankBranch[]"]').attr({
        id: cat+'cBankBranch_'+count
    });

    clonedRow.find('[name*="'+cat+'BankAccNum[]"]').attr({
        id: cat+'BankAccNum_'+count
    });
    clonedRow.find('[name*="'+cat+'BankAccName[]"]').attr({
        id: cat+'BankAccName_'+count
    });
    
    clonedRow.insertAfter('.'+cat+'Copybank_'+id+':last');
    
    // 重新初始化新元素的 Select2
    // $('#'+cat+'BankMain_'+count).select2({
    //     placeholder: "請選擇總行",
    //     allowClear: true,
    //     width: '300px'
    // });
    
    // $('#'+cat+'cBankBranch_'+count).select2({
    //     placeholder: "請選擇分行",
    //     allowClear: true,
    //     width: '300px'
    // });
    


    $("#"+cat+'Index_'+id).val(count);


    $('select[name="newBankMain[]"]').select2({
        placeholder: "請選擇總行",
        allowClear: true,
        width: '300px'
    });
    
    $('select[name="newcBankBranch[]"]').select2({
        placeholder: "請選擇分行",
        allowClear: true,
        width: '300px'
    });
}
function addr(cat,id){
    //newSame_0
     if ($('#'+cat+'Same_'+id).prop('checked')) {
        
        var url = 'zipConvert.php' ;

        $.post(url,{'ct':$('#'+cat+'RegistCity_'+id).val()},function(txt) {
                $('#'+cat+'BaseArea_'+id).html(txt) ;
                $('#'+cat+'BaseArea_'+id).val($('#'+cat+'RegistArea_'+id).val()) ;
        }) ;
        //newBaseZip_0_F
        $('#'+cat+'BaseZip_'+id).val($('#'+cat+'RegistZip_'+id).val());//郵遞區號複製
        // $('#'+cat+'BaseZip_'+id+'_F').val($('#'+cat+'RegistZip_'+id).val());//郵遞區號複製
        $('#'+cat+'BaseCity_'+id).val($('#'+cat+'RegistCity_'+id).val());
        $('#'+cat+'BaseAddr_'+id).val($('#'+cat+'RegistAddr_'+id).val()) ;
       
    }else {
            $('#'+cat+'BaseAddr_'+id).val('') ;
            $('#'+cat+'BaseCity_'+id).val('') ;
            $('#'+cat+'BaseArea_'+id).empty().html('<option value="">區域</option>') ;
            $('#'+cat+'BaseZip_'+id).val('') ;
    }
}

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
        /*border:1px solid;*/
      
    }
    .sign-red {
        color:red;
    }
    /*button*/
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

   /* table tr td {
        text-align:left;
    }
    */
fieldset {
    border-radius: 6px;
}

/* Select2 自定義樣式 */
.select2-container--default .select2-selection--single {
    height: 30px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px;
    color: #333;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 28px;
}

.select2-dropdown {
    border-radius: 4px;
}

.select2-container .select2-selection--single {
    height: 30px !important;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #999;
}

/* 統一輸入框樣式與 Select2 一致 */
input.invoice[type="text"] {
    height: 30px;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 0 8px;
    line-height: 28px;
    font-size: 14px;
    color: #333;
    background-color: #fff;
    box-sizing: border-box;
}

input.invoice[type="text"]:focus {
    border-color: #5897fb;
    outline: none;
    box-shadow: 0 0 0 1px rgba(88, 151, 251, 0.3);
}
</style>
</head>
<body>
<form action="" method="POST" name="form">
   
    <input type="checkbox" name="Checklist<{$key}>" id="Checklist<{$key}>" <{$ChecklistBank}>>全部不帶入點交單和出款
                   
    <input type="hidden" name="newIndex_0" id="newIndex_0" value="0" />
   
    <{foreach from=$list key=key item=data}>
    <table border="0"  id="oldCopybank" class="oldCopybank" width="100%">
        
        <tr>
            <th width="30%" style="text-align: right;">指定解匯總行︰<input type="hidden" name="otherBankId[]" value="<{$data.cId}>" /></th>
            <td width="25%">
                <{html_options name="oldBankMain[]" id="oldBankMain_<{$key}>" class="invoice" style="width:300px;" onchange="bank_change('oldBankMain_<{$key}>','oldcBankBranch_<{$key}>')" options=$menuBank selected=$data.cBankMain}>

            </td>
            <th width="30%" style="text-align: right;">指定解匯分行︰</th>
            <td width="25%">
                <{html_options name="oldcBankBranch[]" id="oldcBankBranch_<{$key}>" class="invoice" style="width:300px;" options=$data.cBankBranchMenu selected=$data.cBankBranch}>
            </td>
        </tr>
        <tr>
            <th style="text-align: right;">指定解匯帳號︰</th>
            <td><input type="text" maxlength="14" id="oldBankAccNum_<{$key}>" name="oldBankAccNum[]" style="width:300px;" class="invoice" value="<{$data.cBankAccountNo}>" /></td>
            <th style="text-align: right;">指定解匯帳戶︰</th>
            <td><input type="text" name="oldBankAccName[]" id="oldBankAccName_<{$key}>" style="width:300px;" class="invoice" value="<{$data.cBankAccountName}>"/></td>
        </tr>
        <tr>
            <td colspan="4"><hr /></td>
        </tr>
    </table>
    <{/foreach}>
    <hr />
     <input type="button" value="新增銀行" name="AddBank_0" id="AddBank_0"  onclick="addBank('new','0')" >
     <table border="0"  id="newCopybank_0" class="newCopybank_0" width="100%">
        <tr>
            <th width="30%" style="text-align: right;">指定解匯總行︰</th>
            <td width="25%">
                <{html_options name="newBankMain[]" id="newBankMain_0" class="newBankMain" style="width:300px;" onchange="bank_change('newBankMain_0','newcBankBranch_0')" options=$menuBank}>

            </td>
            <th width="30%" style="text-align: right;">指定解匯分行︰</th>
            <td width="25%">
                <{html_options name="newcBankBranch[]" id="newcBankBranch_0" class="newcBankBranch" style="width:300px;" options=$menuBankBranch}>

            </td>
        </tr>
        <tr>
            <th style="text-align: right;">指定解匯帳號︰</th>
            <td><input type="text" maxlength="14" id="newBankAccNum_0" name="newBankAccNum[]" style="width:300px;" class="invoice"/></td>
            <th style="text-align: right;">指定解匯帳戶︰</th>
            <td><input type="text" name="newBankAccName[]" id="newBankAccName_0" style="width:300px;" class="invoice"/></td>
        </tr>
        <tr>
            <td colspan="4"><hr /></td>
        </tr>
    </table>
    <div style="text-align: center">
        <input type="hidden" name="CertifiedId" value="<{$CertifiedId}>" />
        <input type="submit" value="送出" />
    </div>
</form>    
</body>
</html>