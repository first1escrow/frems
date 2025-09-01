<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>指示書</title>
<!-- <link rel="stylesheet" href="/css/colorbox.css" /> -->
<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="../../js/lib/comboboxNormal.js"></script>
<link rel="stylesheet" type="text/css" href="../../libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<link rel="stylesheet" href="../../css/datepickerROC.css" />
<script type="text/javascript" src="../../js/datepickerRoc.js"></script>
<!-- <script type="text/javascript" src="../../js/ROCcalender_limit.js"></script> -->
<!-- <script src="/js/jquery.colorbox.js"></script> -->

<script type="text/javascript">
$(document).ready(function() {
	var status = "<{$data.bStatus}>";
	var ck = "<{$ck}>";
	var exp = "<{$exp}>";
	var array = "input,textarea";
  var pbook = "<{$smarty.session.pBankBook}>";

    if (status != '2') {
        setComboboxNormal('oBank', 'name');
        setComboboxNormal('oBank2', 'name');
        setComboboxNormal('cBank', 'name');
        setComboboxNormal('cBank2', 'name');
    }
  <{if $data.bBank != 5}>
    $(".taishin").hide();
  <{else}>
    $(".sin").hide();
  <{/if}>

  <{if $type =='modify' || $Mod == 1}>
    Cate();
  <{/if}>

   	if (exp == '1') {
   		
   		// $(".step1").each(function() {
	    //     $(this).attr('disabled', true);
	          
	    // }); 
   	}else{ //step2 指示日期 指示單編號  到指示書列表才可填寫
         $(".step2").each(function() {
           $(this).attr('disabled', true);
                          
       });
    }

      if (status == 1 && pbook  < 1) {
         $(".step1").each(function() {
           $(this).attr('disabled', true);

                          
       }); 
      }

   	if (status == '2') {
	             
	    $(".block").find(array).each(function() {
	        $(this).attr('disabled', true);
	                       
	    }); 
      $("#AccountNum").attr('disabled', true);
      $("#AccountNumB").attr('disabled', true);
      $("[name='bank']").attr('disabled', true);
      $("#NewAccountNum").attr('disabled', true);
      $("#NewAccountNumB").attr('disabled', true);
   	};

      $("[name='bank']").on('change', function() {

        // $(".first8").hide();


         if ($(this).val() == 1) {
            // $(".first8").show();
             
         }
      });

      
});
function AddTicket(){

  var row = parseInt($("[name='Addcount']").val());
  var row2 = row+1;
  $('#copy').clone().insertAfter('.copy:last');

  $('.copy:last input' ).val('');

  $("[name='Addcount']").val(row2);
  $('.copy:last ').attr('class', 'copy n'+row2); //copy n0

  // console.log($(".copy").length);

  if ($(".copy").length > 1) {
    $(".copy:last .delbtn").html("<input type=\"button\" value=\"刪除\" name=\"dd\" onclick=\"delRow('n"+row2+"')\" />");
   
  }

 
  
} 
function delRow(name){
  //bDel
  
  var t = name.substr(0,1);
  var id = name.substr(1);

  if (t == 'o') {
    $('#'+name).remove();
    $.ajax({
      url: 'setDetail.php',
      type: 'POST',
      dataType: 'html',
      data: {"id": id},
    })
    .done(function(txt) {
     alert('刪除成功');
    });
  }else{
    $("."+name).remove();
    alert('刪除成功');
  }
  
  

}
function save(id){
 
  var input = $('input');
   var select = $('select');
   var textarea = $('textarea');
  var arr_input = new Array();
  var mod = "<{$Mod}>";
  var url = '';

   $.each(select, function(key,item) {
      
         arr_input[$(item).attr("name")] = $(item).attr("value");
       
   });
  
  $.each(input, function(key,item) { //did

    var reg = /\[\]/ ;

      if ($(item).attr("name") == 'item[]') {
                        
        if ($(item).is(':checked')) {
               if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                  arr_input[$(item).attr("name")] = new Array();
               }
                            
               arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
         }
                        
      }else if (reg.test($(item).attr("name"))) {
                        
        if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
            arr_input[$(item).attr("name")] = new Array();
        }
                            
        arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        
      }else if ($(item).is(':radio')) {
         if ($(item).is(':checked')) {
            arr_input[$(item).attr("name")] = $(item).val();
         }
      }else{
         arr_input[$(item).attr("name")] = $(item).attr("value");
      }

      

      
                                        
    });
    $.each(textarea, function(key,item) {
         arr_input[$(item).attr("name")] = $(item).attr("value");
    });

   var obj_input = $.extend({}, arr_input);

   // console.log(mod);
   if (mod == 1) {
    url = 'saveIBook.php';
   }else{
    url = 'saveIBook3.php';
   }
   // console.log(url);
   $.ajax({
      url: url,
      type: 'POST',
      dataType: 'html',
      data: obj_input,
   })
   .done(function(txt) {
      var t = "<{$type}>";
      // console.log(txt);
      if (txt) {
         alert('儲存成功');
         // 
         if (txt !='OK') {
             $("#reload input[name='id']").val(txt);
         }

         $('[name="reload"]').submit();
        
      }  
     
   });
}

function saveBook(id){
  $.ajax({
    url: 'checkBook.php',
    type: 'POST',
    dataType: 'html',
    data: {'id': id},
  })
  .done(function(txt) {

    if (txt == 'OK') {
      save(id);
    }else{
      alert(txt);

      $('[name="reload"]').submit();
    }
  });
  
	
   

}

function pdfBook(id){
   $("[name='pdf']").submit();
}
function bookEnd()
{
   var s = $('[name="stauts"]').val();
   var id = "<{$data.bId}>";

   $.ajax({
      url: 'statusIBook.php',
      type: 'POST',
      dataType: 'html',
      data: {'id': id,'s':s},
   })
   .done(function(txt) {
      if (txt) {
         alert('狀態更改成功');
         $('[name="reload"]').submit();
      }
   });
   
}

function Cate(){

   var b = $("[name='bank']").val();
   var id = $("[name='Category']:checked").val();

   // console.log('gg');

   $(".s1").hide();
   $(".s2").hide();
   $(".s3").hide();
   $(".s4").hide();
   $("#TD").hide();
   $("#first78").show();
   $(".sp").show();
   
   if (b == 1 || b == 7) {
      
      if (id == 6) { 
        $(".taishin").hide();
        $(".s2").show();
        $(".s3").show();
        
         
      }else if (id == 7 || id==8 || id == 9) { //退票領回專用
         $(".s1").show();
        
      }else if(id == 11 || id==12){
        $("#TD").show();
        $("#TDTitle").text('取消日期:');
        $(".s1").show();
        $("#first78").hide();
        $(".sp").hide();
         // $(".first8").show();
      }
   }else if(b==4 || b==6 ){ //永豐
      $("#TDTitle").text('延後支票發票日');
      $(".taishin").hide();
      if (id == 7 || id==8 || id == 9) { //退票領回專用
         $(".s1").show();
         if(id == 9){
            $("#TD").show();
         }
      }else if(id == 6){
        
         $(".s3").show();
         $(".s4").show();
         
      }
                  
   }else if(b==5 ){ //台新
      // $(".first8").hide();
      $(".taishin").show();
      $(".sin").hide();
      if (id == 7 || id==8) { //退票領回專用
         $(".s1").show();
         if (id == 7 || id==8) {
            $("#TD").hide();
         }
      }else if(id == 6){
         
         $(".s3").show();
         $(".s4").show();
         
      }else if(id == 11){
        $(".taishin").hide();
        $(".s4").show();
        <{if $data.bId == ''}>
          $.ajax({
            url: 'IBook04-11.html',
            dataType: 'html',
          })
          .done(function(html) {
            $("#taishin11_12").html(html);
          });
        <{/if}>
        
       
      }else if (id==12) {
        $(".taishin").hide();
        $(".s4").show();
       <{if $data.bId == ''}>
          $.ajax({
            url: 'IBook04-12.html',
            dataType: 'html',
          })
          .done(function(html) {
            $("#taishin11_12").html(html);
          });
        <{/if}>
      }
                  
   }

}
function Bankchange(account) {
    if(account == 'old') {
      GetBankBranchList($('#AccountNum'),$('#AccountNumB'),null);
    }
    if(account == 'new') {
      GetBankBranchList($('#NewAccountNum'),$('#NewAccountNumB'),null);
    }


}
function GetBankBranchList(bank, branch, sc) {
                $(branch).prop('disabled',true) ;
            
            var request = $.ajax({  
                    url: "../../includes/maintain/bankbranchsearch.php",
                    type: "POST",
                    data: {
                        bankcode: $(bank).val()
                    },
                    dataType: "json"
                });
                request.done(function( data ) {
                    $(branch).children().remove().end();
                    $(branch).append('<option value="0">------</option>')
                    $.each(data, function (key, item) {
                        if (key == sc ) {
                            $(branch).append('<option value="'+key+'" selected>'+item+'</option>');
                        } else {
                            $(branch).append('<option value="'+key+'">'+item+'</option>');
                        }
                        
                    });
                setComboboxNormal('oBank2', 'name');
                setComboboxNormal('cBank2', 'name');
                });
            
            $(branch).prop('disabled',false) ;
}

function AddRow(cat){
  var count = parseInt($("[name='"+cat+"rowCount']").val());
  $.ajax({
    url: 'IBook04_Ajax.php',
    type: 'POST',
    dataType: 'html',
    data: {"cat": cat,"cou":count},
  })
  .done(function(txt) {
    $(txt).insertAfter(".loc"+cat+":last")
    $("[name='"+cat+"rowCount']").val((count+1));
  });


  
}

function del(id){
   $.ajax({
      url: 'IBook04_Del.php',
      type: 'POST',
      dataType: 'html',
      data: {'id': id},
   })
   .done(function(txt) {
      if (txt) {
         alert('刪除成功');
      }else{
        alert('失敗');
      }
      $('[name="reload"]').submit();
   });
  
}
</script>
<style>
   .tb1{
      /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
     padding: 10px;
     background-color: #FFF;
      
   }
   .tb1 td{
        padding: 5px;
   }
   .tb2{
      /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
      border:solid #000;
      background-color: #FFF;

      
   }
   .tb2 td{
      padding: 5px;
   }
   .title{
      font-size: 26px;
      text-align: center;
   }
   .title2{
      font-size: 16px;
      text-align: center;
   }
   .block{
      background-color: #f8ece9;
      width: 80%;
      height: 100%;
      padding-top: 10px;
   }
   .border_left{
      border-color:#000;
     
      border-right-style: double ;
      border-bottom-style: solid;
      border-bottom-width: 1px;
    
   }
   
   .border_left2{
      border-color:#000;
      border-right-style: double; 
      border-bottom-style: solid;      
      border-bottom-width: 1px;
   }
   .border_left3{
      border-color:#000;
      border-right-style: double; 
      border-bottom-style: solid; 
   }
   .border_left4{
      border-color:#000;
      border-right-style: double; 
      
   }
   .border_right{
      border-color:#000;  
      border-bottom-style: solid;
      border-bottom-width: 1px;
   }
    .border_right2{
      border-color:#000;  
      border-bottom-style: solid;
     
   }
   .bt1{
      width: 100px;
      height: 40px;
      background-color: #fb6363;
      font-size: 14px;
      color: #FFF;
      font-family: "微軟正黑體", serif;
   }
   .cat1{
      display: none;
   }
   .ui-button{
       padding-top: 4px;
       padding-bottom: 3px;
       top: 4px;
   }

   
</style>
</head>

<body>
    								
<center>
<form action="<{$pdf}>" method="POST" name="pdf" target="_blank">
   <input type="hidden" name="id" value="<{$data.bId}>"/>
</form>
<form name="reload" method="POST" id="reload">
     <input type="hidden" name="id" value="<{$data.bId}>" />
</form>

<div class="block">
   <span><font size="5px" color="red">狀態:<{$data.bStatusName}></font></span>
     <{if $smarty.session.pBankBook > 1}>
         <{html_options name=stauts options=$opStaus selected=$data.bStatus onChange="bookEnd()"}>
      <{/if}>
      <{if $smarty.session.pBankBook == 1 && ($data.bCategory == 6 || $data.bCategory == 12 || $data.bCategory == 11 || $data.bCategory == 7 || $data.bCategory == 8 )}>
         <{html_options name=stauts options=$opStaus selected=$data.bStatus onChange="bookEnd()"}>
      <{/if}>

   <form  method="POST" name="form_save" id="form_save">
      <input type="hidden" name="bId"  value="<{$data.bId}>" />
      <input type="hidden" name="type" value="<{$type}>" />
      <!-- <input type="hidden" name="Category"  value="<{$data.bCategory}>" /> -->
      <table width="100%" border="0" cellpadding="1" cellspacing="1" class="tb1">
            <tr>
             <td  class="title">不動產買賣價金第一建經履約保證信託指示通知書</td>
            </tr>
             <tr>
               <td class="title">銀行別: <{html_options name=bank options=$menu_bank selected=$data.bBank class="step1" onchange="Cate()"}>
                  
               </td>
            </tr>
            <tr>
               <td class="title2">
                  <{if $data.bCategory == 6}>
                     <{assign var='ck6' value='checked=checked'}> 
                  <{else if $data.bCategory == 7}>
                     <{assign var='ck7' value='checked=checked'}>
                  <{else if $data.bCategory == 8}>
                     <{assign var='ck8' value='checked=checked'}>
                  <{else if $data.bCategory == 9}>
                     <{assign var='ck9' value='checked=checked'}>
                  <{else if $data.bCategory == 11}>
                     <{assign var='ck11' value='checked=checked'}>
                  <{else if $data.bCategory == 12}>
                     <{assign var='ck12' value='checked=checked'}>
                  <{/if}>



                  <input type="radio" name="Category" id="" value="8" onCLick="Cate()" class="step1" <{$ck8}> />代收票據領回
                  <input type="radio" name="Category" id="" value="7" onCLick="Cate()" class="step1" <{$ck7}>/>退票領回專用
                  <input type="radio" name="Category" id="" value="9" onCLick="Cate()" class="step1" <{$ck9}>/>代收票據延期提示(限永豐)
                  <input type="radio" name="Category" id="" value="6" onCLick="Cate()" class="step1" <{$ck6}>/>匯款更正專用
                 
               </td>
            </tr>
            <tr>
              <td class="title2">
                 <input type="radio" name="Category" id="" value="11" onCLick="Cate()" class="step1" <{$ck11}> />開票未辦理(限一銀、台新)
                  <input type="radio" name="Category" id="" value="12" onCLick="Cate()" class="step1" <{$ck12}> />大額繳稅未辦理(限一銀、台新)
              </td>
            </tr>
           

      </table>
      <table width="100%" border="0" cellpadding="1" cellspacing="1" class="tb2">
      	<{if $smarty.session.pBankBook != 0}>
         <tr>
            <td colspan="4" class="border_left">指示日期：<input type="text" name="Date"  value="<{$data.bDate}>" class="datepickerROC step2"/></td>
            <td class="border_right">指示單編號：<input type="text" name="BookId"  value="<{$data.bBookId}>" class="step2"/></td>

         </tr>
         
        <{/if}>
         <tr>
            <td colspan="5">指示內容：</td>
         </tr>

         <tr>
            <td colspan="5" class="border_left2" width="50%">               
               保證號碼：<input type="text" name="CertifiedId" class="step1" maxlength="9" value="<{$data.CertifiedId_9}>"/>(9碼)
            </td>
            
         </tr>
          <tr class="taishin">
            <td>契約編號：<input type="text" name="ContractId" style="width:100px;" class="step2" value="<{$data.bContractID}>"/></td>
        </tr>
         <{if $smarty.session.pBankBook != 0}>
            <{if $data.bCategory!= 6}>
             <tr class="s4 <{$data.show4}>">
                <td colspan="5">一、本指示單取款總金額：
                <{if $data.bMoney ==0 }>
                   <input type="text" name="money"  style="width:250px" value="<{$data.expMoney}>" />
                <{else}>
                   <input type="text" name="money"  style="width:250px" value="<{$data.bMoney}>" />
                <{/if}>
                元整。<!-- (<{$data.expMoney}>) -->(請輸入阿拉伯數字)</td>
                
             </tr>
            <{/if}>
          <{/if}>
      </table>
    
      <table width="100%" border="0" cellpadding="1" cellspacing="1" class="tb2 <{$data.show1}> s1">  
        <!-- <tr class="first8">
           <td colspan="5">
              分行台照：
             <input type="text" name="bReBank2" id="" value="<{$data.bReBank2}>" class="step1"/>
           </td>
         </tr> -->
        <tr>
         	<input type="hidden" name="did"  value="<{$data_detail[0]['bId']}>"/>

         	<td id="first78">
               支票號碼：<input type="text" name="ticketNo" id="" value="<{$data_detail[0]['bTicketNo']}>" class="step1"/>
           
             
          </td>

          <td>金額：<input type="text" name="dMoney" id="" value="<{$data_detail[0]['bMoney']}>" class="step1"/></td>
        </tr>
         
        <tr id="TD">
          <td><span id="TDTitle">延後支票發票日:</span><input type="text" name="TicketDelay" class="datepickerROC" class="step1" value="<{$data_detail[0]['bTicketDelay']}>"></td>
          <td></td>
        </tr>
       
         
         <tr>
         	<td>領票人/繳款人/取款人： <input type="text" name="reName" id="" value="<{$data.breName}>" class="step1"/>
				  </td>
          <td>
            身分證字號：<input type="text" name="reIdentifyId" id="" value="<{$data.breIdentifyId}>" maxlength="10" class="step1"/>
            
			     </td>
         </tr>
         <tr>
         	<td colspan="2">
            <span class="sin">請依上列指示事項通知 貴行</span>
            <span class="taishin">分行：</span>
            

            <input type="text" name="reBank" id="" value="<{$data.bReBank}>" class="step1" />分行</td>
         	
         </tr>
          
         <tr class="sp"> 
          <td colspan="2">
              特殊事項<br />
              <textarea name="SpNote1" cols="100" rows="5" class="step1"><{$data.bSpNote1}></textarea>
          </td>
         </tr>
         <tr class="sp">
            <td colspan="2">
              特殊事項<br />
             <textarea name="SpNote2" cols="100" rows="5" class="step1"><{$data.bSpNote2}></textarea>
            </td>
         </tr>
  
      </table>
    

      <table width="100%" border="0" cellpadding="1" cellspacing="1" class="tb2 <{$data.show3}> s3">
        <tr class="taishin">
            <td>
               原匯款日期：
                <input type="text" name="oDate" class="datepickerROC" style="width:100px;" class="step1" value="<{$data.bODate}>" />
            </td>
        </tr>
        <tr class="taishin">
          <td>原指示單編號：<input type="text" name="oBookId" style="width:100px;" class="step2" value="<{$data.bOBookId}>"/></td>
        </tr>
       
         <tr>
            <td>一、解 款 行：
               <{html_options name="oBank" id="AccountNum" options=$menu_bank2 onchange="Bankchange('old')" selected=$data.AccountNum class="step1"}>
               分行
               <select name="oBank2" id="AccountNumB" class="step1" >
                  <{$menu_branch}>
               </select>
               <!-- <input type="text" name="oBank" value="<{$data.bObank}>" class="step1"/> -->
            </td>
         </tr>
         <tr>
            <td>二、錯誤資料：</td>
         </tr>
         <tr>
            <td>戶名(1)：<input type="text" name="EaccountName" value="<{$data.bEaccountName}>" class="step1" style="width:300px;"/></td>
         </tr>
         <tr>
            <td>帳號(1)：<input type="text" name="Eaccount" value="<{$data.bEaccount}>" class="step1" style="width:300px;"/></td>
         </tr>
         <tr class="">
            <td>金額(1)：<input type="text" name="Emoney" value="<{$data.bEmoney}>" class="step1 " style="width:300px;"/></td>
         </tr>
         <{foreach from=$data_Error key=key item=item}>
            <tr><td style="border:1px solid #CCC;backgroud-color:f8ece9;"><input type="button" value="刪除" onclick="del(<{$item.bId}>)" /></td></tr>
            <tr>
              <td>
                戶名(<{($key+2)}>)：<input type="text" name="MEaccountName[]" value="<{$item.bEaccountName}>" class="step1"/>
                <input type="hidden" name="eId[]" value="<{$item.bId}>" style="width:300px;" />
              </td>
           </tr>
           <tr>
              <td>帳號(<{($key+2)}>)：<input type="text" name="MEaccount[]" value="<{$item.bEaccount}>" class="step1" style="width:300px;"/></td>
           </tr>
           <tr class="">
              <td>金額(<{($key+2)}>)：<input type="text" name="MEmoney[]" value="<{$item.bEmoney}>" class="step1" style="width:300px;"/></td>
           </tr>
         <{/foreach}>
        
          <tr class="locE">
            <td><input type="button" value="新增一列" onclick="AddRow('E')" /><input type="hidden" name="ErowCount" value="<{$ErowCount}>" /></td>
          </tr>
          <tr>
            <td><hr /></td>
          </tr>
          <tr>
              <!-- 新增解款行 -->
              <td>一、解 款 行：
                  <{html_options name="cBank" id="NewAccountNum" options=$menu_bank2 onchange="Bankchange('new')" selected=$data.NewAccountNum class="step1"}>
                  分行
                  <select name="cBank2" id="NewAccountNumB" class="step1" >
                      <{$menu_branch_new}>
                  </select>
              </td>
          </tr>
          <tr>
            <td>二、更正資料：</td>
         </tr>
         <tr>
            <td> 戶名(1)：<input type="text" name="CaccountName" value="<{$data.bCaccountName}>" class="step1" style="width:300px;"/></td>
         </tr>
         <tr>
            <td>帳號(1)：<input type="text" name="Caccount" value="<{$data.bCaccount}>" class="step1" style="width:300px;"/></td>
         </tr>
         <tr class="">
            <td>金額(1)：<input type="text" name="Cmoney" value="<{$data.bCmoney}>" class="step1" style="width:300px;"/></td>
         </tr>
         <{foreach from=$data_Correct key=key item=item}>
            <tr><td style="border:1px solid #CCC;backgroud-color:f8ece9;"><input type="button" value="刪除" onclick="del(<{$item.bId}>)" /></td></tr>
            <tr>
              <td>
                戶名(<{($key+2)}>)：<input type="text" name="MCaccountName[]" value="<{$item.bEaccountName}>" class="step1"/>
                <input type="hidden" name="cId[]" value="<{$item.bId}>" style="width:300px;"/>
              </td>
           </tr>
           <tr>
              <td>帳號(<{($key+2)}>)：<input type="text" name="MCaccount[]" value="<{$item.bEaccount}>" class="step1" style="width:300px;"/></td>
           </tr>
           <tr class="">
              <td>金額(<{($key+2)}>)：<input type="text" name="MCmoney[]" value="<{$item.bEmoney}>" class="step1" style="width:300px;"/></td>
           </tr>
         <{/foreach}>
         <tr class="locC">
           <td><input type="button" value="新增一列" onclick="AddRow('C')" /><input type="hidden" name="CrowCount" value="<{$ErowCount}>" /></td>
         </tr>
          
          <tr>
            <td><hr /></td>
          </tr>
          <tr class="s3 <{$data.show3}>">
            <td>四、其他：<input type="text" name="Other" id="" value="<{$data.bOther}>" class="step1" style="width: 80%;"/></td>
          </tr>
      </table>

      <div id="taishin11_12">
       
      </div>

      <{if $data.bCategory == 11 && $data.bBank == 5}>
      <table width="100%" border="0" cellpadding="1" cellspacing="1" class="tb2" id="taishin11">
        
        <tr><td><input type="button" value="增加" class="step1" onclick="AddTicket()" /><input type="hidden" name="Addcount" value="0" /></td></tr>
           <{foreach from=$data_detail key=key item=item}>
          <tr class="copy" id="o<{$item.bId}>">
                <td colspan="5">
                    <input type="hidden" name="did[]" value="<{$item.bId}>" />
                    ■開立本行支票，抬頭：<input type="text" name="dName[]" class="step1" value="<{$item.bName}>" style="width:80px" />
                     ，金額：<input type="text" name="dMoney[]" class="step1" value="<{$item.bMoney}>" style="width:100px"/>元。
                     <input type="button" value="刪除" onclick="delRow('o<{$item.bId}>')" />
                <!-- stopStatus -->
                </td>
          </tr>
          <{/foreach}>
          <{if $data.bExport_nu == ''}>
               <tr class="copy n0" id="copy">
                <td colspan="5">
                    <input type="hidden" name="did[]"  />
                    
                    ■開立本行支票，抬頭：<input type="text" name="dName[]" class="step1" style="width:80px" />，金額：<input type="text" name="dMoney[]" class="step1" style="width:100px"/>元。<span class="delbtn"></span>
                
                </td>
               </tr>
          <{/if}>
         
           <tr>
            <td>領票人/繳款人： <input type="text" name="reName" id="" value="<{$data.breName}>"/>
            </td>
            <td>
              身分證字號：<input type="text" name="reIdentifyId" id="" value="<{$data.breIdentifyId}>" maxlength="10"/> 
            </td>
         </tr>
      </table>
      <{/if}>
      <{if $data.bCategory == 12 && $data.bBank == 5}>
      <table width="100%" border="0" cellpadding="1" cellspacing="1" class="tb2" id="taishin12">
     
        <tr>

          
          <td colspan="5"><input type="hidden" name="did[]"  value="<{$data_detail[0]['bId']}>" class="step1"/>
            ■繳交稅款金額：<input type="text" name="dMoney[]" class="step1" value="<{$data_detail[0]['bMoney']}>" id="" value="" />元。</td>
        </tr>
        <tr>
            <td>領票人/繳款人： <input type="text" name="reName" id="" value="<{$data.breName}>"/>
            </td>
            <td>
              身分證字號：<input type="text" name="reIdentifyId" id="" value="<{$data.breIdentifyId}>" maxlength="10"/> 
            </td>
         </tr>
      </table> 
      <{/if}>
      

    
   </form>  
   <div style="padding:20px;">
      &nbsp;
   </div>
         
</div>
 <div style="padding:20px;">
            <{if $data.bStatus != 2 }>
              <{if ($data.bStatus == 1 && $smarty.session.pBankBook > 0) || $data.bStatus ==0}>
              <input type="button" value="儲存" name="save" class="bt1" onclick="saveBook(<{$data.bId}>)"/>
             
              <{/if}>
            <{/if}>
            &nbsp;&nbsp;
            <input type="button" value="預覽" name="PDF" class="bt1" onclick="pdfBook(<{$data.bId}>)"  />
            &nbsp;&nbsp;
            
          
      </div>
</center>
</div>
</body>
</html>
