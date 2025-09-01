<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/colorbox.css" />
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />
<script src="../js/jquery-1.7.2.min.js"></script>
<script src="../js/jquery.colorbox.js"></script>
<{include file='meta.inc.tpl'}>
<script type="text/javascript" src="../js/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	
});

function go(type) {
    $('[name="xls"]').val(type);
    $('#form1').submit();
}

</script>
<style>
.css-year {
    width: 100px;
}
</style>
</head>
    <body id="dt_example">
        <form action="/calendar/calendar.php" target="_blank"></form>
        <div id="wrapper">
            <div id="header">
                <table width="1000" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="233" height="72">&nbsp;</td>
                        <td width="753">
                            <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table>
                        </td>
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
                                    <h3></h3>
                                    <div id="container">

                                    <div style="padding-bottom:20px;">
                                        <h1>成長趨勢分析表</h1>
                                        <div style="border: 1px solid #CCC;width:400px;height:300px;margin: auto;">
                                            <div style="padding: 20px;">
                                                <form method="POST" id="form1">
                                                    <input type="hidden" name="xls">
                                                    <div>    
                                                        <span>
                                                        查詢年度：
                                                        <{html_options name="year" class="css-year" options=$year_option selected=$year}>
                                                        </span>
                                                        <span>
                                                        業務：
                                                        <{html_options name="sales" class="css-year" options=$sales_option selected=$sales}>
                                                        </span>
                                                    </div>
                                                    <div style="text-align: center;">
                                                        <span>
                                                            <button style="margin-top: 20px;padding: 5px;" type="button" onclick="go('A')">下載</button>
                                                        </span>
                                                    </div>
                                                    <div style="margin-top: 10px; text-align: center;">
                                                        <!-- <button type="button" style="padding: 5px;" onclick="go('S')">業績趨勢報表</button> -->
                                                        <!-- <button style="margin-left: 10px;padding: 5px;" type="button" onclick="go('A')">區域趨勢報表</button> -->
                                                    </div>
                                                </form>
                                            </div>

                                            <div style="padding: 20px;">
                                                <fieldset>
                                                    <legend style="padding-left: 5px;padding-right: 5px;">政府移轉棟數有效數據範圍</legend>
                                                    <ul style="padding: 20px;">
                                                    <{foreach from=$gov_period key=k item=v}>
                                                        <li style="font-size:10pt;border-bottom: 1px solid #CCC;"><{$k}>年度 <{$v[0]}>月份 ~ <{$v[1]}>月份</li>
                                                    <{/foreach}>
                                                    </ul>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>	


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