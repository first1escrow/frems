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

   if (status == 2) {
       var array = "input";
                   
         $(".block").find(array).each(function() {
         $(this).attr('disabled', true);
                       
      }); 
   };
});

function saveBook(id)
{
  var date = $('[name="Date"]').val();
  var BookId = $('[name="BookId"]').val();
  var money = $('[name="money"]').val();
  var count = $('[name="count"]').val();
  var cat = $('[name="Category"]').val();
  var specificCount = $('[name="bSpecificCount"]').val();
   // console.log('');
   if (date == '' ) {
      alert('指示日期未填寫');
      return false;
   }else if(BookId == ''){
      alert('指示單編號未填寫');
      return false;
   }else if(money == ''){
      alert('金額未填寫');
      return false;
   }else if(count == ''){
      alert('數量未填寫');
      return false;
   }

   $.ajax({
      url: 'saveIBook.php',
      type: 'POST',
      dataType: 'html',
      data: {
         'Date': date,
         'BookId':BookId,
         'money':money,
         'count':count,
         'cat':cat,
         'bId':id,
         'ContractId':$('[name="ContractId"]').val(),
         'specificCount':specificCount
      },
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
<form action="<{$pdf}>" method="POST" name="pdf" target="_balnk">
   <input type="hidden" name="id" value="<{$data.bId}>"/>
</form>
<form name="reload">
     <input type="hidden" name="id" value="<{$data.bId}>" />
</form>
<!-- <form  method="POST" name="form_save">
   <input type="hidden" name="id" value="<{$id}>" />
   <input type="hidden" name="code" value="<{$code}>" />
   <input type="hidden" name="code2" value="<{$code2}>" />
</form> -->
<div class="block">
   <span><font size="5px" color="red">狀態:<{$data.bStatusName}></font></span>
     <{if $smarty.session.pBankBook > 1 }>
         <{html_options name=stauts options=$opStaus selected=$data.bStatus onChange="bookEnd()"}>
      <{/if}>

   <form  method="POST" name="form_save" id="form_save">
      
      <input type="hidden" name="Category"  value="<{$data.bCategory}>" />
     
      </table>
      <table width="80%" border="0" cellpadding="1" cellspacing="1" class="tb2">
         <tr>
            <td colspan="4" class="border_left">指示日期：<input type="text" name="Date" id="" value="<{$data.bDate}>" class="datepickerROC"/></td>
            <td class="border_right">指示單編號：<input type="text" name="BookId" id="" value="<{$data.bBookId}>"/></td>

         </tr>
         <{if $data.bBank == 1 || $data.bBank == 7}>
            <tr>
               <td colspan="5">筆數：<input type="text" name="bSpecificCount" id="" value="<{$data.bSpecificCount}>"/> </td>
            </tr>
         <{/if}>
         <{if $data.bBank == 4 || $data.bBank == 6}>
         <tr>
            <td colspan="4" class="border_left2">
            <{if $data.bBank == 4}>
               ■
            <{else}>
               □
            <{/if}>
            專戶帳號：104-018-1000199-9</td>
            <td class="border_right">保證號碼：99985</td>

         </tr>
         
         <tr>
            <td colspan="4" class="border_left3">
            <{if $data.bBank == 6}>
               ■
            <{else}>
               □
            <{/if}>
            專戶帳號：126-018-0001599-9</td>
            <td class="border_right2">保證號碼：99986</td>

         </tr>
         
            
         <{/if}>
         <tr>
            <td colspan="5">指示內容：</td>
         </tr>
         <{if $data.bBank != 5}>
         <tr>
            <td colspan="5" >玆請  貴行於接到本指示通知後，於 貴行<font color="red"><{$data.cBranchName}>分行「<{$data.cTrustAccountName}>」</font>中支付相關款項</td>
            
         </tr>
         <{/if}>
         <tr>
            <td colspan="5" >總金額&nbsp;新台幣
            <{if $data.bMoney ==0 }>
            <input type="text" name="money" id="" style="width:250px" value="<{$data.expMoney}>" />
            <{else}>
            <input type="text" name="money" id="" style="width:250px" value="<{$data.bMoney}>" />
            <{/if}>
            元整(請輸入阿拉伯數字)<!-- (<{$data.expMoney}>) --></td>
            
         </tr>
        
         <{if $data.bBank == 4 || $data.bBank == 6 || $data.bBank == 5}>
         <tr>

            <td colspan="5">二、總筆數：
               <{if $data.bCount == 0}>
               <input type="text" name="count" value="<{$data.expCount}>" />筆。<!-- (<{$data.expCount}>) --></td>
               <{else}>
               <input type="text" name="count" value="<{$data.bCount}>" />筆。
               <{/if}>
         </tr>
         <{/if}>
         
         <{if $data.bBank == 5}>
         <tr>
            <td colspan="5">
               契約編號：<input type="text" name="ContractId" style="width:100px;" value="<{$data.bContractID}>"/>
            </td>
         </tr>
         <{/if}>
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
