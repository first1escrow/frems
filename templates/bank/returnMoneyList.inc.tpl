<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>出款進度</title>
<link rel="stylesheet" type="text/css" href="../libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
    <script src="../js/jquery-1.7.2.min.js"></script>

    <script type="text/javascript" src="../libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script src="../js/datepickerRoc.js"></script>
    <link rel="stylesheet" href="/css/datepickerROC.css" />


<script>

$(document).ready(function(){

   

              
         
});

</script>
<style>
    .tb{
        width: 700px;
        border:1px solid #CCC;
        padding: 2px;
    }
    .tb th{
        background-color: #E4BEB1;
        line-height: 30px;
         border:1px solid #CCC;
    }
    .tb td{
       
        line-height: 30px;
         border:1px solid #CCC;
    }

</style>
</head>

<body>
<div style="width:1300px; margin-bottom:5px; height:22px; background-color: #CCC">
<div style="float:left;margin-left: 10px;"> <a href="instructions/IBookList.php">指示書</a></div>
<div style="float:left;margin-left: 10px;"> <a href="/bank/list2.php">待修改資料</a> </div>
<{if $smarty.session.member_pDep == 5 || $smarty.session.member_id == 6 }>
<div style="float:left;margin-left: 10px;"> <a href="/bank/BankTransProcess.php">出款進度</a> </div>
<{/if}>
<{if $smarty.session.member_bankcheck ==1 }>

<div style="float:left; margin-left: 10px;"> <a href="/bank/list.php">未審核列表</a></div>
<div style="float:left; margin-left: 10px;"> <a href="/bank/list_ok.php">已審核列表</a></div>
<div style="float:left; margin-left: 10px;"> <a href="/bank/pay_check.php">銀行出款確認</a></div>
<div style="float:left;margin-left: 10px;"><a href="instructions/IBookManagerList.php">指示書列表</a></div>
<div style="float:left;margin-left: 10px;"><font color="red"><strong>返還代墊列表</strong></font></div>
<div style="float:left; margin-left: 10px;"> <a href="/bank/sms_check.php">簡訊發送</a></div>
<div style="float:left; margin-left: 10px;"> 【<a class='iframe2' href="/bank/report/report.php">銀行對帳單</a>】</div>
<{/if}>
</div>
<div>


<br>
<div style="padding-bottom:10px;padding-left:50px">
<!-- <font color="red">※預設為今天和昨天的資料</font> -->
<form action="" method="POST">
   <!--  日期:<input type="text" name="Date" class="datepickerROC" readonly style="width:100px">
    保證號碼:<input type="text" name="cId" style="width:100px" maxlength="9" />
    進度:<{html_options name=process options=$menuStatus}>
    <input type="submit" value="查詢" /> -->
</form>
</div>
<div style="width:1300px;padding-left:50px">

<table cellpadding="0" cellspacing="0" border="0" class="tb">
        <tr>
            <th width="30%">媒體檔匯出時間</th>
            <th width="30%">出帳金額</th>
            <th></th>
        </tr>
    
        <{foreach from=$list key=key item=item}>

        <tr style="background-color:<{$item.color}>">
            <td align="center"><{$item.tExport_time}> </td>
            <td align="right"><{$item.M}>元</td>

            <td align="center">
              <!--   <a class="iframe" href="returnMoneyExcel.php?sn=<{$item.tExport_nu}>">下載 </a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                <a class="iframe" href="returnMoneyPDF.php?sn=<{$item.tExport_nu}>" target="_blank">PDF下載 </a>
               
            </td>
        </tr>
        <{/foreach}>
</table>
</div>



</div>
</body>
</html>
