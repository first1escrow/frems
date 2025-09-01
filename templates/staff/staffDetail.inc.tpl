<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        
        <style>
        .table-box {
            margin: 0 auto;
            width:600px;
            text-align: left;
        }

        .table-box tr:nth-child(odd) {
            
            background-color: #c4e8ee;
        }

        .table-box th {
            padding: 8px;
            width: 75px;
            /* text-align: right; */
            /* border-bottom: 1px solid #000; */
        }

        .table-box td {
            padding: 8px;
            /* width: 200px; */
            /* border-bottom: 1px solid #000; */
        }

        .td-width-long {
            width: 180px;
        }

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
                                        <h1 style="text-align:left;">基本資料</h1>
                                        <br>
                                        <div>
                                            <table class="table-box" border="0" cellpadding="0" cellspacing="1">
                                                <tr>
                                                    <th>姓名：</td><td class="td-width-long"><{$staff['name']}></td>
                                                    <th>性別：</td><td><{$staff['gender']}></td>
                                                </tr>
                                                <tr>
                                                    <th>帳號：</td><td class="td-width-long"><{$staff['account']}></td>
                                                    <th>密碼：</td><td><a href="Javascript:void(0);" onclick="modify('password')">修改</a></td>
                                                </tr>
                                                <tr>
                                                    <th>部門：</td><td class="td-width-long"><{$staff['dep']}></td>
                                                    <th>分機：</td><td><{$staff['ext']}></td>
                                                </tr>
                                                <tr>
                                                    <th>到職日：</td><td><{$staff['onBoard']}></td>
                                                    <th>存取碼：</td><td><a href="Javascript:void(0);" onclick="modify('access')">修改</a></td>
                                                </tr>
                                                <tr>
                                                    <th>通訊地址：</td>
                                                    <td colspan="3">
                                                        <a href="Javascript:void(0);" onclick="modify('mailing')">修改</a>
                                                        <span id="mailing-address"><{$staff['mailingAddress']}></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>戶籍地址：</td>
                                                    <td colspan="3">
                                                        <a href="Javascript:void(0);" onclick="modify('register')">修改</a>
                                                        <span id="register-address"><{$staff['registerAddress']}></span>
                                                    </td>
                                                </tr>
                                            </table>
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
$(document).ready(function() {     

});

function modify(action) {
    if (action == 'password') {
        $.colorbox({
            href: '/includes/staff/setPassword.php',
            width: 600,
            height: 450,
            iframe: true
        });
    }
    if (action == 'access') {
        $.colorbox({
            href: '/includes/staff/setAccess.php',
            width: 600,
            height: 450,
            iframe: true
        });
    }

    if ((action == 'mailing') || (action == 'register')) {
        $.colorbox({
            href: '/includes/staff/setAddress.php?action=' + action,
            width: 600,
            height: 400,
            iframe: true
        });
    }
}

</script>