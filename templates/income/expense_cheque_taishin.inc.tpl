<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>支票存入通知區</title>
<script src="../js/jquery-1.12.4.min.js"></script>

<style>
.btn {
    color: #000;
    font-family: Verdana;
    font-size: 14px;
    font-weight: bold;
    line-height: 14px;
    background-color: #CCCCCC;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #DDDDDD;
            /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
.btn:hover {
    color: #000;
    font-size: 14px;
    background-color: #999999;
    border: 1px solid #CCCCCC;
}
.btn.focus_end{
    color: #000;
    font-family: Verdana;
    font-size: 14px;
    font-weight: bold;
    line-height: 14px;
    background-color: #CCCCCC;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #FFFF96;
    /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}

        
/*input*/
.xxx-input {
    color:black;
    font-size:16px;
    font-weight:normal;
    background-color:#FFFFFF;
    text-align:left;
    height:20px;
    padding:0 2px;
    border:1px solid #999;          
}
.xxx-input:focus{
    border-color: rgba(82, 168, 236, 0.8) !important;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
    outline: 0 none;
}


input[type="checkbox"]{
    width:15px;
    height:15px;
    margin:0px 4px 0 0;
    vertical-align:-4px;
}
.tb{
       
    border: solid 1px #ccc;
    width: 100%
}
.tb th{
    background-color:#E4BEB1;
    padding-bottom: 2px;
    padding-top:2px;
    line-height: 30px;
    border: solid 1px #999;
    text-align: center;
}
.tb td{
    padding: 5px;
    text-align: center;
    border: solid 1px #999;
    
}
</style>
<script>

function UnSend(id){

// console.log(id);

$.ajax({
    url: 'expense_cheque_ajax_taishin.php',
    type: 'POST',
    dataType: 'html',
    data: {id: id},
})
.done(function(txt) {
    if (txt==1) {
        alert('更改成功');

    }else{
        alert('更改失敗');
    }

    location.href='expense_cheque_taishin.php';
    
});

}
</script>
</head>

<body>
<form name="myform" method="POST">
<table width="500" cellpadding="0" cellspacing="0" class="tb">
   <!--  <tr>
        <td>※僅供通知客戶用，資料不列入收支明細(收到通知後隔天，詳細資料會寫入)</td>
    </tr> -->
    <tr>
        <td>帳號搜尋:
          <label for="textfield"></label>
        <input type="text" name="textfield" id="textfield" class="xxx-input" />
        <input type="submit" value="搜尋" class="btn" />
        </td>
    </tr>
</table>
<div style="height:10px;"></div>
<div id="highlight-plugin">
<table width="800" cellpadding="0" cellspacing="0" class="tb">
    <tr>
        <td colspan="9">
        日期範圍：<{$start_date|substr:0:3}>年<{$start_date|substr:3:2}>月<{$start_date|substr:5:2}>日    ~ 
        <{$end_date|substr:0:3}>年<{$start_date|substr:3:2}>月<{$end_date|substr:5:2}>日
        

        </td>
    </tr>

    <tr>
        <th width="20%">通知日期</th>
        <th width="10%">保證帳號</th>
        <th width="10%">支票金額</th>
        <th width="10%">票據種類</th>
        <th width="10%">受理行代碼</th>
        <th width="10%">地政士</th>
        <!-- <th width="10%">經辦</th> -->
       <!--  <th width="10%">簡訊通知</th>
        <th width="10%">不寄送簡訊</th> -->
    </tr>
    <{foreach from=$list key=key item=item}>
    <tr style="background-color:<{$item.color}>">
        <td><{$item.eCreatTime}></td>
        <td><{$item.eDepAccount|substr:-9}></td>
        <td><{$item.eLender}></td>
        <td><{$item.eTicket}><br>(數量<{$item.eTicketCount}>)</td>
        <td><{$item.eBankBranch}></td>
        <td><{$item.sName}></td>
        <!-- <td><{$item.sUndertaker1}></td> -->
        <!-- <td>
            <a href="expense_cheque_sms_taishin.php?id=<{$item.eId}>&sid=<{$item.bSID}>"><img src="../bank/images/sms.png" border="0" width="50px" height="50px"></a>
        </td>
        <td align="center">
            <input type="checkbox" name="no" onclick="UnSend(<{$item.eId}>)" <{$item.check}>/>
        </td> -->
    </tr>
    <{/foreach}>
</table>
</div>
</form>
</body>
</html>