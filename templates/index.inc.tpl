<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>第一建經後台</title>
        <link href="/css/layout.css" rel="stylesheet" type="text/css" />
		<script src="/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$(this).keydown(function(e) {
				if (e.keyCode == 13) {
					$('form[name="member_form"]').submit() ;
				}
			}) ;
            
			$('[name="account"]').select().focus() ;
		}) ;
		</script>
        <style type="text/css">
            body {
                background-color: #B91C23;
            }
            body,td,th {
                color: #000;
                font-family: "Times New Roman", Times, serif;
                font-size: 24px;
            }
        </style>
    </head>
    <body>
        <div id="wrapper">
            <div id="header"></div>
            <div id="content">
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <div id="content p">  
                    <form name="member_form" action="/includes/member/passwordcheck.php" method="POST">
                        <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td></td>
                                <td width="100" align="right">帳 號 :</td>
                                <td width="100">
                                    <input name="account" type="text" tabindex="1" maxlength="10" size="30" value="<{$act}>"/>
                                </td>
                                <td width="280" rowspan="2" align="left">
                                    <a href="#" onClick="document.forms['member_form'].submit();">
                                        <img src="/images/login.jpg" alt="" width="124" height="49" />
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td width="100"></td>
                                <td align="right">密 碼 :</td>
                                <td>
                                    <label for="textfield5"></label>
                                    <input name="password" type="password" tabindex="2" maxlength="12" size="31" value="<{$psd}>"/>
                                </td>
                            </tr>
                            <tr>
                                <td width="100"></td>
                                <td align="right"></td>
                                <td>
                                    <span style="font-size:10pt;"><input type="checkbox" name="remembered"<{$remembered}> value="1">&nbsp;記住帳號密碼</span>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br />
                </div>
            </div>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
        </div>
    </body>
</html>
