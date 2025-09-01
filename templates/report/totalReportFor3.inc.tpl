<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<{include file='meta2.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {
    
});
function search(){
    $("[name='form']").submit();
}
</script>
<style>
.block{
    margin-bottom: 10px;
   
}


.tb{
    width: 100%
       
}
        
.tb th{
    background-color: #E4BEB1;
    padding: 10px;
    width: 40%
}
.tb td{
    padding: 10px;
    border:solid #CCC 1px;
}

.yeartb{
    /*padding: 10px;*/
    border:solid #CCC 1px;
}

.yeartb th{
    color: #FFF;
    background-color: #a63c38;
    padding: 5px;
    border: 1px solid #fff;
}

.yeartb td{
   color: #000;
    background-color: #FFF;
    padding: 5px;
    border: 1px solid #CCC;
}

.tb2{
    /*padding: 10px;*/
    border:solid #CCC 1px;
}

.tb2 th{
    color: #FFF;
    background-color: #FFB01C;
    padding: 5px;
    border: 1px solid #fff;
}

.tb2 td{
   color: #000;
    background-color: #FFF;
    padding: 5px;
    border: 1px solid #CCC;
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
    font-size: 14px;
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
        <form name="form_edit" id="form_edit" method="POST">
            <input type="hidden" name="id" id="id" value='3' />
        </form>
        <form name="form_add" id="form_add" method="POST">
        </form>
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
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
                                        <h1>案件統計總表</h1>
                                        <div id="container">
    <br>
    <div class="block">
        <form action="" method="POST" name="form">
        <table cellspacing="0" cellpadding="0" class="tb" align="center" width="100%">
            <tr>
                <td>
                    ※案件統計表(依品牌)因計算上以四捨五入方式做計算可能會跟案件統計表會有誤差，
                </td>
            </tr>
            <tr>
                <td  align="center">
                    進案日 ：
                    比對一 <{html_options name=year options=$menuYear selected=$year}> 
                    比對二<{html_options name=year2 options=$menuYear selected=$year2}> 
                   業務：
                        <{html_options name=sales options=$menuSales selected=$sales}> 
                    
                    <input type="submit" value="送出">

                    
                </td>
            </tr>
        
           
        </table>
        </form>
    </div>
   
    <div class="block">
        <table cellpadding="0" cellspacing="0" width="100%" class="tb2"> 
        <tr>
            <th colspan="6">案件統計表</th>
        </tr>
        <tr>
            <th>年度</th>
            <th>案件總筆數</th>
            <th>買賣總價金額</th>
            <th>合約總保證費金額</th>
            <th>回饋總金額</th>
            <th>收入</th> 
        </tr>
        <tr>
            <td><{$year}></td>
            <td><{$originalDataTotal.count|number_format}></td>
            <td><{$originalDataTotal.totalMoney|number_format}></td>
            <td><{$originalDataTotal.certifiedMoney|number_format}></td>
            <td><{$originalDataTotal.feedbackMoney|number_format}></td>
            <td><{($originalDataTotal.certifiedMoney-$originalDataTotal.feedbackMoney)|number_format}></td> 
        </tr>
        <tr>
            <td><{$year2}></td>
            <td><{$originalDataTotal1.count|number_format}></td>
            <td><{$originalDataTotal1.totalMoney|number_format}></td>
            <td><{$originalDataTotal1.certifiedMoney|number_format}></td>
            <td><{$originalDataTotal1.feedbackMoney|number_format}></td>
            <td><{($originalDataTotal1.certifiedMoney-$originalDataTotal1.feedbackMoney)|number_format}></td> 
        </tr>
        </table>
    </div>
    <div class="block">
        
            <table cellpadding="0" cellspacing="0" class="yeartb" width="100%">
               
                <tr>
                    <th colspan="6"><{$year}>案件統計表(依品牌)</th>
                </tr>
                <tr>
                    <th>類別</th>
                    <th>案件總筆數</th>
                    <th>買賣總價金額</th>
                    <th>合約總保證費金額</th>
                    <th>回饋總金額</th>
                    <th>收入</th> 
                </tr>
                <{foreach from=$data key=key item=item}>
                <tr>
                    <td><{$item.name}></td>
                    <td><{$item.count|number_format}></td>
                    <td><{$item.totalMoney|number_format}></td>
                    <td><{$item.certifiedMoney|number_format}></td>
                    <td><{$item.feedbackMoney|number_format}></td>
                    <td><{($item.certifiedMoney-$item.feedbackMoney)|number_format}></td> 
                </tr>
                <{/foreach}>
                <tr>
                    <td> 總計</td>
                    <td><{$dataTotal.count|number_format}></td>
                    <td><{$dataTotal.totalMoney|number_format}></td>
                    <td><{$dataTotal.certifiedMoney|number_format}></td>
                    <td><{$dataTotal.feedbackMoney|number_format}></td>
                    <td><{($dataTotal.certifiedMoney-$dataTotal.feedbackMoney)|number_format}></td> 
                </tr>
            </table>
        
       
    </div>
    <div class="block">
            <table cellpadding="0" cellspacing="0" class="yeartb"  width="100%">
                <tr>
                    <th colspan="6"><{$year2}>案件統計表(依品牌)</th>
                </tr>
                <tr>
                    <th>類別</th>
                    <th>案件總筆數</th>
                    <th>買賣總價金額</th>
                    <th>合約總保證費金額</th>
                    <th>回饋總金額</th>
                    <th>收入</th> 
                </tr>
                <{foreach from=$data1 key=key item=item}>
                <tr>
                    <td><{$item.name}></td>
                    <td><{$item.count|number_format}></td>
                    <td><{$item.totalMoney|number_format}></td>
                    <td><{$item.certifiedMoney|number_format}></td>
                    <td><{$item.feedbackMoney|number_format}></td>
                    <td><{($item.certifiedMoney-$item.feedbackMoney)|number_format}></td> 
                </tr>
                <{/foreach}>
                 <tr>
                    <td> 總計</td>
                    <td><{$dataTotal1.count|number_format}></td>
                    <td><{$dataTotal1.totalMoney|number_format}></td>
                    <td><{$dataTotal1.certifiedMoney|number_format}></td>
                    <td><{$dataTotal1.feedbackMoney|number_format}></td>
                    <td><{($dataTotal1.certifiedMoney-$dataTotal1.feedbackMoney)|number_format}></td> 
                </tr>
            </table>
        </div>
    <div style="clear:both;"></div>   
   
   





    
 






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