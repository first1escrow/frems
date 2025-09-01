<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<{include file='meta2.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {
    
});
function search(cat){

    $('[name="xls"]').val(cat);
    $("[name='form']").submit();

}
</script>
<style>
.tb{
    border-color: #CCC 1px solid;
    margin-top: 20px;
}
.tb th{
    color:#FFF;
    background-color: #a63c38;
    padding: 5px;
    border: 1px solid #a63c38;
}
.tb td{
    color:#000;
    background-color: #FFF;
    padding: 5px;
    border: 1px solid #FFF;
}
.tb2{
    width:80%;
    border-color: #CCC 1px solid;
    margin-bottom: 20px;
}
.tb2 th{
    color:#FFF;
    background-color: #a63c38;
    padding: 5px;
    /*border: 1px solid #CCC;*/
}
.tb2 td{
    color:#000;
    background-color: #FFF;
    padding: 5px;
    border: 1px solid #CCC;
}        
.total td{
    background-color: orange;
}

.cb1 {
    padding:0px 0px;
}
.cb1 input[type="checkbox"] {/*隱藏原生*/
    /*display:none;*/
    position: absolute;
    left: -9999px;
}
.cb1 input[type="checkbox"] + label span {
    display:inline-block;
    width:20px;
    height:20px;
    margin:-3px 4px 0 0;
    vertical-align:middle;
    background:url("../images/check_radio_sheet2.png") left top no-repeat;
    cursor:pointer;
    background-size:80px 20px;
    transition: none;
    -webkit-transition:none;
}
.cb1 input[type="checkbox"]:checked + label span {
    background:url("../images/check_radio_sheet2.png") -20px top no-repeat;
    background-size:80px 20px;
    transition: none;
    -webkit-transition:none;
}
.cb1 label {
    cursor:pointer;
    display: inline-block;
    margin-right: 10px;
    /*-webkit-appearance: push-button;
    -moz-appearance: button;*/
}

/*button*/
.xxx-button {
color:#FFFFFF;
    font-size:12px;
    font-weight:normal;
    
    text-align: center;
    white-space:nowrap;
    height:20px;
    
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
.xxx-select {
    color: #666666;
    font-size: 16px;
    font-weight: normal;
    background-color: #FFFFFF;
    text-align: left;
    height: 24px;
    padding: 0 0px 0 5px;
    border: 1px solid #CCCCCC;
    border-radius: 0em;
    font-family: "微軟正黑體", serif;

}

/*input*/
.xxx-input {
    color:#666666;
    font-size:14px;
    font-weight:normal;
    background-color:#FFFFFF;
    text-align:left;
    height:24px;
    padding:0 5px;
    border:1px solid #CCCCCC;
    border-radius: 0.35em;
}
.xxx-input:focus {
    border-color: rgba(82, 168, 236, 0.8);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
    outline: 0 none;
}
</style>
    </head>
    <body id="dt_example">
      
        <div id="wrapper">
            <div id="header">
                <table width="1000" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="233" height="72">&nbsp;</td>
                        <td width="753"><table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            <{include file='menu1.inc.tpl'}>
                <table width="1000" border="0" cellpadding="4" cellspacing="0">
                    <tr>
                        <td bgcolor="#DBDBDB">
                            <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                <tr>
                                    <td height="17" bgcolor="#FFFFFF">
                                        <div id="menu-lv2">
                                                        
                                        </div>
                                        <br/> 
                                        <h3>&nbsp;</h3>
                                        <h1>問卷統計</h1>
                                        <div id="container">
                                        
<center>
<div style="width:100%;padding:4px;">

    <form action="" method="POST" name="form">
        <table cellpadding="0" cellpadding="0" border="0" width="50%">
            <tr>
                <tr>
                    <th>經辦</th>
                    <td><{html_options name=undertaker options=$menuPeople selected=$undertaker class="xxx-select"}></td>
                </tr>
                <th>
                    <input type="hidden" name="id" value="1">
                    日期
                </th>
                <td><input type="text" name="sDate" style="width:100px;" class="xxx-input datepickerROC" value="<{$sDate}>">(起)至
                    <input type="text" name="eDate" style="width:100px;" class="xxx-input datepickerROC" value="<{$eDate}>">(迄)
                </td>
               
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="button" value="查詢" onclick="search('')" class="xxx-button" style="display:;width:100px;height:35px;font-size:16px;">
                    
                        <input type="button" value="下載EXCEL" onclick="search('xls');" class="xxx-button" style="display:;height:35px;font-size:16px;"> 
                        <input type="hidden" name="xls">
                </td>
            </tr>
            
          
        </table>
       
    	<br>
   
        
    </form>
 
</div>
<hr>

<div class="tb">
        <table cellpadding="0" cellspacing="0" border="0" align="center">
            <tr>
                <th>問卷總數</th>
                <th>問卷有效數</th>
                <th>問卷無效數</th>
                <th>分數總計</th>
                <th>平均分數</th>
    
            </tr>
            <tr>
                <td><{$data['total']}></td>
                <td><{$data['vaild']}></td>
                <td><{$data['invalid']}></td>
                <td><{$data['score']}></td>
                <td><{$data['avgScore']}></td>
            </tr>
        </table>
    </div>
    <hr>
    <div >
        <{foreach from=$data['count'] key=key item=item}>
        <table cellpadding="0" cellspacing="0" border="0"  align="center" class="tb2">
            
            <tr>
                <th colspan="<{($item.item|count)+1}>"><{$item.title}></th>
            </tr>
            <tr>
                <td>選項名稱</td>
                <td>選擇數</td>
                <td>分數</td>
            </tr>
            <{foreach from=$item.item key=k item=value}>
            <tr>    
                <td><span title="<{','|implode:$value.nogood}>"><{$value.item}></span></td>
                <td><{$value.value}></td>
                <td><{$value.score}></td>
            </tr>
            <{/foreach}>
            <tr class="total">
                <td>合計</td>
                <td><{$item.value}></td>
                <td><{$item.score}></td>
            </tr>
            <tr>
                <th colspan="<{($item.item|count)+1}>">意見回饋</th>
            </tr>
            <tr>
                <td colspan="<{($item.item|count)+1}>">
                <{foreach from=$item.text key=k item=value}>
                    <{$value}><br>
                <{/foreach}>
                </td>
            </tr>
        </table>
       
        <{/foreach}>
    </div>

</center>



    
 






<!-- <table cellspacing="0" cellpadding="0" class="tb" >
    <tr>
        <th width="20%">寄送日期</th>
        <th width="20%">寄送數量</th>
        <th width="60%">寄送對象</th>
    </tr>
    <{foreach from=$list key=key item=item}>
    <tr>
        <td><{$key}></td>
        <td><{$item.count}></td>
        <td><{$item.send}></td>
    </tr>
    <{/foreach}>
</table> -->
<div id="footer" style="height:50px;">
<p>2012 第一建築經理股份有限公司 版權所有</p>
</div>
                </td>
            </tr>
        </table>
        </td>
    </tr>
</table>
</div>


</body>
</html>