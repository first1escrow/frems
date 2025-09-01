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

   <{if $data.bBank != 5}>
    $(".taishin").hide();
  <{else}>
    $(".other").hide();
  <{/if}>

	if (ck == '1') {
		alert("因更動交易類別，請重新製作指示書");
	}
   
   	if (exp == '1') { //除指示日期 指示單編號 以外的禁止修改
   		
   		// $(".step1").each(function() {
	    //     $(this).attr('disabled', true);
	                       
	    // }); 
   	}else{ //step2 指示日期 指示單編號 總金額  到指示書列表才可填寫
         $(".step2").each(function() {
           $(this).attr('disabled', true);
                          
       });
      }

   	if (status == '2') {
	             
	    $(".block").find(array).each(function() {
	        $(this).attr('disabled', true);
	                       
	    }); 
   	};
});

function saveBook(id)
{
	var input = $('input');
  var select = $('select');
  var textarea = $('textarea');
	var arr_input = new Array();
	var mod = "<{$Mod}>";
	var url = '';

  $.each(select, function(key,item) {
    if ($(item).attr("name") == 'dStop[]') {
                                            
            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                arr_input[$(item).attr("name")] = new Array();
            }
                                                
            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                                            
    }else{
      arr_input[$(item).attr("name")] = $(item).attr("value");
    }
    
  });
 	
	$.each(input, function(key,item) { //did

        if ($(item).attr("name") == 'dName[]') {
                                            
            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                arr_input[$(item).attr("name")] = new Array();
            }
                                                
            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                                            
        }else if($(item).attr("name") == 'dMoney[]'){
        	if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
	                arr_input[$(item).attr("name")] = new Array();
	            }
	                                                
	            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
	        }else if ($(item).is(':radio')) {
	            if ($(item).is(':checked')) {
	                arr_input[$(item).attr("name")] = $(item).val();
	        }
        }else if($(item).attr("name") == 'did[]'){
        	if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
	                arr_input[$(item).attr("name")] = new Array();
	            }
	                                                
	            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
	        }else if ($(item).is(':radio')) {
	            if ($(item).is(':checked')) {
	                arr_input[$(item).attr("name")] = $(item).val();
	        }
        }else {
            arr_input[$(item).attr("name")] = $(item).attr("value");
        }
                                        
    });

    $.each(textarea, function(key,item) {
         arr_input[$(item).attr("name")] = $(item).attr("value");
    });

	 var obj_input = $.extend({}, arr_input);

	 if (mod == 1) {
	 	url = 'saveIBook.php';
	 }else{
	 	url = 'saveIBook2.php';
	 }

   $.ajax({
      url: url,
      type: 'POST',
      dataType: 'html',
      data: obj_input,
   })
   .done(function(txt) {
      if (txt) {
         alert('儲存成功');
         // console.log(txt);
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
function AddTicket(){

  var row = parseInt($("[name='Addcount']").val());
	var row2 = row+1;
	$('#copy').clone().insertAfter('.copy:last');

	$('.copy:last input' ).val('');

  $("[name='Addcount']").val(row2);
  $('.copy:last ').attr('class', 'copy n'+row2); //copy n0

  // $('.n'+row2+' td').append('<input type="button" value="刪除" name="dd" onclick="delRow(\'n'+row2+'\')" />');

  // console.log('.copy:last [alt="n'+row+'"]');
  $('.copy:last [name="dd"]' ).val('刪除');
  $('.copy:last [name="dd"]' ).attr('onclick', "delRow('n"+row2+"')");
  // onclick="delRow('n')"
// <input type="button" value="刪除" name="dd" onclick="delRow('n<{$item.bId}>')" />
 

  
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
   .block{
      background-color: #f8ece9;
      width: 60%;
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
      font-size: 16px;
      color: #FFF;
      font-family: "微軟正黑體", serif;
   }
</style>
</head>

<body>
    								
<center>
<form action="<{$pdf}>" method="POST" name="pdf" target="_blank">
   <input type="hidden" name="id" value="<{$data.bId}>"/>
</form>
<form name="reload" method="POST">
     <input type="hidden" name="id" value="<{$data.bBankTranId}>" />
</form>

<div class="block">
   <span><font size="5px" color="red">狀態:<{$data.bStatusName}></font></span>
     <{if $smarty.session.pBankBook > 1}>
         <{html_options name=stauts options=$opStaus selected=$data.bStatus onChange="bookEnd()"}>
      <{/if}>

   <form  method="POST" name="form_save" id="form_save">
      <input type="hidden" name="bId"  value="<{$data.bId}>" />
      <input type="hidden" name="Category"  value="<{$data.bCategory}>" />
      <table width="80%" border="0" cellpadding="1" cellspacing="1" class="tb1">
            <tr>
             <td colspan ="6" class="title">不動產買賣價金第一建經履約保證信託指示通知書</td>
            </tr>
            <tr>
               <td colspan="6" class="title">(
               	<{if $data.bCategory == 3}>
               		開立票據
               	<{else if $data.bCategory == 4}>
					繳交稅款
               	<{else if $data.bCategory == 5}>
					臨櫃現金取款
               <{else if $data.bCategory == 2}>
               虛轉虛
               	<{/if}>
               	專用)</td>
            </tr>

      </table>
      <table width="80%" border="0" cellpadding="1" cellspacing="1" class="tb2">
      	<{if $smarty.session.pBankBook > 0 || $smarty.session.member_id == 1}>
         <tr>
            <td colspan="4" class="border_left">指示日期：<input type="text" name="Date"  value="<{$data.bDate}>" class="datepickerROC step2"/></td>
            <td class="border_right">指示單編號：<input type="text" name="BookId"  value="<{$data.bBookId}>" class="step2"/></td>

         </tr>
        <{/if}>
        <{if $data.bBank == 4 || $data.bBank == 6}>
         <tr>
            <td colspan="4" class="border_left2" width="50%">
            <{if $data.bBank == 4}>
               ■
                保證號碼：99985-<{$data.CertifiedId_9}>
            <{else}>
               □
               保證號碼：99985-
            <{/if}>
           
            </td>
            <td class="border_right" width="50%">專戶帳號：104-018-1000199-9</td>

         </tr>
         <tr>
            <td colspan="4" class="border_left3">
            <{if $data.bBank == 6}>
               ■
               保證號碼：99986-<{$data.CertifiedId_9}>
            <{else}>
               □
               保證號碼：99986-
            <{/if}>
            
            </td>
            <td class="border_right2">專戶帳號：126-018-0001599-9</td>

         </tr>
         <{/if}>
         <tr>
            <td colspan="5">指示內容：</td>
         </tr>
         <tr class="taishin">
            <td>契約編號：<input type="text" name="ContractId" style="width:100px;" class="step2" value="<{$data.bContractID}>"/></td>
        </tr>
         <tr>
            <td colspan="5">一、本指示單取款總金額：
            <{if $data.bMoney ==0 }>
               <input type="text" name="money"  style="width:250px" value="<{$data.expMoney}>"/>
            <{else}>
               <input type="text" name="money"  style="width:250px" value="<{$data.bMoney}>"/>
            <{/if}>
            元整。<!-- (<{$data.expMoney}>) -->(請輸入阿拉伯數字)</td>
            
         </tr>
         
         <{if $data.bCategory == 2}>
         <tr>
            <td colspan="5">

               匯至保證號碼：
               <{if $data.ToCertifiedFirst == ''}>
                <{if $data.bBank ==6}>
                  <input type="text" name="ToCertifiedFirst" id="" value="99986" maxlength="5" style="width:80px"/>
                <{else}>
                  <input type="text" name="ToCertifiedFirst" id="" value="99985" maxlength="5" style="width:80px"/>
                <{/if}>
               <{else}>
                <input type="text" name="ToCertifiedFirst" id="" value="<{$data.ToCertifiedFirst}>" maxlength="5" style="width:80px"/>
               <{/if}>
               -<input type="text" name="ToCertified" id="" value="<{$data.bToCertifiedId}>" maxlength="9"/>專戶之履保專戶。 <!-- 99985-004020709 -->
            </td>
         </tr>
         <{if $smarty.session.pBankBook != 0}>
         <tr>
            <td colspan="5">總筆數：<input type="text" name="count" value="<{$data.bCount}>" />筆。<!-- (<{$data.expCount}>) --></td>
           
         </tr>
          <{/if}>
         <{/if}>
         <{if $data.bCategory == 3}>
         
         <tr>
         	<td colspan="5">
				<table border="1" width="100%">
					<tr><td><input type="button" value="增加" class="step1" onclick="AddTicket()" /><input type="hidden" name="Addcount" value="0" /></td></tr>
					<{foreach from=$data_detail key=key item=item}>
					<tr class="copy" id="o<{$item.bId}>">
			         	<td colspan="5">
			         			<input type="hidden" name="did[]" value="<{$item.bId}>" />
			         			■開立本行支票，抬頭：<input type="text" name="dName[]" class="step1" value="<{$item.bName}>" style="width:80px" />，
                    <{html_options name="dStop[]" options=$stopStatus selected=$item.bStop}>
                    背書轉讓，金額：<input type="text" name="dMoney[]" class="step1" value="<{$item.bMoney}>" style="width:100px"/>元。<input type="button" value="刪除" onclick="delRow('o<{$item.bId}>')" />
			         	<!-- stopStatus -->
			         	</td>
			    </tr>
			    <{/foreach}>
                  <{if $data.bExport_nu == ''}>
			         <tr class="copy n0" id="copy">
			         	<td colspan="5">
			         			<input type="hidden" name="did[]"  />
                    
			         			■開立本行支票，抬頭：<input type="text" name="dName[]" class="step1" style="width:80px" />，<{html_options name="dStop[]" options=$stopStatus}>背書轉讓，金額：<input type="text" name="dMoney[]" class="step1" style="width:100px"/>元。<input type="button" value="刪除" name="dd" onclick="delRow('n0')" />
			         	
			         	</td>
			         </tr>
                  <{/if}>
				</table>
         	</td>
         </tr>
		
         <{/if}>
         <{if $data.bCategory == 4}>
        
         <tr>

         	<input type="hidden" name="did[]"  value="<{$data_detail[0]['bId']}>" class="step1"/>
         	<td colspan="5">■繳交稅款金額：<input type="text" name="dMoney[]" class="step1" value="<{$data_detail[0]['bMoney']}>" id="" value="" />元。</td>
         </tr>
        
         <{/if}>
         <{if $data.bCategory == 5}>
         
         <tr>
         	<input type="hidden" name="did[]"  value="<{$data_detail[0]['bId']}>"/>
         	<td colspan="5">■臨櫃現金取款金額：<input type="text" name="dMoney[]" id="" class="step1" value="<{$data_detail[0]['bMoney']}>" />元。</td>
         </tr>

         <{/if}>
         <{if $data.bCategory != 2}>
         <tr>
         	<td colspan="5">二、領票人/繳款人/取款人： <input type="text" name="reName" id="" value="<{$data.breName}>" class="step1"/><br>
				身分證字號：<input type="text" name="reIdentifyId" id="" value="<{$data.breIdentifyId}>" maxlength="10" class="step1"/>
			</td>
         </tr>
         <tr>
         	<td colspan="5">
            <span class="other">三、請依上列指示事項通知 貴行</span>
            <span class="taishin">轉入分行：</span>

            <input type="text" name="reBank" id="" value="<{$data.bReBank}>" class="step1" /></td>
         	
         </tr>
         <tr class="other">
         	<td colspan="5">四、開立本行支票/臨櫃領現/臨櫃繳稅取款交易，請協助回傳簽收單至<{$Fax}>。
			   </td>
         </tr>
         <{/if}>
         <tr>
           
           <td colspan="5">
              特殊事項<br />
              <textarea name="SpNote1" cols="60" rows="5" class="step1"><{$data.bSpNote1}></textarea>
           </td>
         </tr>
         <tr>
            <td colspan="5">
              特殊事項<br />
             <textarea name="SpNote2" cols="60" rows="5" class="step1"><{$data.bSpNote2}></textarea>
            </td>
         </tr>

         
      </table>
      

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
            <input type="button" value="預覽" name="PDF" class="bt1" onclick="pdfBook(<{$data.bId}>)" />
            &nbsp;&nbsp;
            
          
      </div>
</center>
</div>
</body>
</html>
