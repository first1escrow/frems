<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#export').live('click', function () {
					$('[name="form_search"]').submit() ;
                });
                $('#export').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
            } );
        </script>
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
                                        <div id="container">
                                            <form name="form_search" method="POST">
											<input type="hidden" name="save_ok" value="1">
                                            <table border="0" cellspacing="10" cellpadding="10">
                                                <tr>
                                                    <th>姓名︰</th>
                                                    <td><input type="text" value="<{$staff_name}>" disabled="disabled"></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <th>帳號︰</th>
                                                    <td><input type="text" value="<{$staff_account}>" disabled="disabled"></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <th>分機︰</th>
                                                    <td><input type="text" name="pExt" value="<{$staff_ext}>"></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <th>傳真︰</th>
                                                    <td>
														(<input type="text" name="FaxArea" value="<{$staff_faxarea}>" style="width:20px;">)
														-
														<input type="text" name="FaxNum1" value="<{$staff_fax1}>" style="width:55px;">
														-
														<input type="text" name="FaxNum2" value="<{$staff_fax2}>" style="width:55px;">
													</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">

                                                    </td>
                                                </tr>
                                            </table>
                                            </form>
                                            <center><button id="export">更新基本資料</button></center>
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
    </body>
</html>