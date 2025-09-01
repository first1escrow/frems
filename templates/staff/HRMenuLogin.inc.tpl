<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        
        <style>
        
        .button {
            padding: 5px;
            margin: 5px;
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('http://first.twhg.com.tw/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
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

                                    <div id="container">
                                        <h1 style="text-align:left;">請輸入存取碼</h1>
                                        <br>
                                        <div>
                                            <form id="form1" method="post">
                                                <table class="table-box" border="0" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <th>存取碼：</td><td><input type="password" name="access-code" placeholder="請輸入存取碼"></td>
                                                        <td align="center"><input type="button" value="確定" class="button" onclick="access()"></td>
                                                    <tr>
                                                </table>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
        </div>
    </body>
</html>
<script type="text/javascript">
<{$alert}>

$(document).ready(function() {     
    $('[name="access-code"]').focus();
});

function access() {
    let accessCode = $('input[name="access-code"]').val();
    if (accessCode == '') {
        alert('請輸入存取碼');
        return;
    }

    $('#form1').submit();
}
</script>