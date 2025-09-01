<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="/js/jquery-1.7.2.min.js"></script>
<title>銀行付款明細</title>
<script>
$(document).ready(function(){

});
function payBack(){
  $.ajax({
    url: "quit_pay.php?sn=<{$sn}>&ts=<{$ts}>&tm=<{$tm}>&p=ok",
    type: 'POST',
    dataType: 'html',
  }).done(function(json) {
    // console.log(json);
    var obj =  jQuery.parseJSON(json);

    alert(obj.msg);

    if (obj.code != 0) {
       parent.$.fn.colorbox.close(); 
    }
    
  });
  
}
</script>
<style type="text/css">
.tb td {
  font-size:12px;
  border: 1px solid #CCC;
  padding: 5px;
}
.tb th {
  font-size: 14px;
  text-align: left;
  border: 1px solid #CCC;
  padding: 5px;
}
</style>
<script>

</script>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <table width="100%" border="0" cellpadding="1" cellspacing="1" class="font12" id="ttt">
    <tr>
      <td colspan="2"> <strong> 媒體檔匯出時間:</strong> <font color=red><{$ts}></font> , 金額共 <font color=red><{$tm}></font> 元整.(<{$sn}>)<input name="save" type="hidden" id="save" value="ok" /></td>
      <td >&nbsp;</td>
      <td >[<a href="paymentBookEdit.php?sn=<{$sn}>" target="_blank">指示書</a>]</td>
      <td >&nbsp;</td>
      <td >[<a href="#" onclick="payBack()">媒體檔退回</a>]</td>
      <td >&nbsp;</td>
    </tr>
  </table>
     <{foreach from=$data key=key item=item}>
        <table border="0"  width="100%" class="tb" cellpadding="0" cellspacing="0" class="tb">
            <tbody>
                <tr>
                  <th>類別</th>
                  <td colspan="9"><{$item.tKind}></td>
                </tr>
                <tr>
                    
                    <th>交易類別</th>
                    <td> <{$item.tCode2}></td>
                   
                    <th>解匯行</th>
                    <td><{$item.bank}></td>
                   
                    <th>戶名</th>
                    <td><{$item.tAccountName}></td>
                    
                    <th>帳號</th>
                    <td><{$item.tAccount}></td>
                    <th>金額</th>
                    <td>NT$ <font color="red"><{$item.tMoney}></font>元(不含匯費)</td>
                </tr>
                <tr>
                     <th>項目</th>
                    <td>
                       <{$item.tObjKind}>
                    </td>
                     <th>分行別</th>
                    <td><{$item.bankbranch}></td>
                    <th>證號</th>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <th>附言(備註)</th>
                    <td colspan="7"><{$item.tPayTxt}></td>
                    <th>保證號碼</th>
                    <td><font color="red"><{$item.tVR_Code}></font></td>

                   
                </tr>

               
            </tbody>
        </table>
        <hr style="width: 100%;float: left;">
    <{/foreach}>

   
  </table>
</form>
</body>
</html>
