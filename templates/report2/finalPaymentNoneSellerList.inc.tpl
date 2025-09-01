<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<{include file='meta.inc.tpl'}>

        <script type="text/javascript">
            $(document).ready(function() {
			
            });
        </script>
		<style>
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('../bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table> 
            </div>
            <{include file='menu1.inc.tpl'}>
			<ul id="menu">
			<div id="dialog"></div>
			</ul>
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
                                        <h1>尾款非賣方名單</h1>
                                        <div id="container">
                                            <form name="form_search" method="POST">
                                                分行：
                                                <{html_options name=bankBranch options=$bank_option selected=$bank_selected}>
                                                年份：
                                                <select name="year" id="">
                                                    <{for $i =$smarty.now|date_format:"%Y" to 2012 step -1}>
                                                            <{$year = ($i - 1911)}>
                                                            <option value="<{$year+1911}>"<{if $year == $smarty.now|date_format:"%Y"}> selected="selected"<{/if}>><{$year}></option>
                                                    <{/for}>

                                                </select>
                                                月份：
                                                <select name="month" id="">
                                                    <{for $i = 1 to 12 }>
                                                        <{$month = $i|string_format:"%02d"}>
                                                        <option value="<{$month}>"<{if $month == $smarty.now|date_format:"%m"}> selected="selected"<{/if}>><{$month}></option>
                                                    <{/for}>

                                                </select>
                                                <input type="submit" value="下載EXCEL">
                                            </form>
                                            </center>
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