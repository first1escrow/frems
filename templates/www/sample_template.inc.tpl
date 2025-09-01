<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <{include file='meta.inc.tpl'}>
    <link rel="stylesheet" href="/css/colorbox.css" />
    <link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />
    <script src="/js/jquery.colorbox.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        // $('.cmc_overlay').show();
        // $('.cmc_overlay').hide();
    });

    </script>
</head>
<body id="dt_example">
    <div class="cmc_overlay" style="display:none;">
        <div class="cmc_overlay__inner">
            <div class="cmc_overlay__content"><span class="cmc_spinner"></span></div>
        </div>
    </div>

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
                        </table>
                    </td>
                </tr>
            </table> 
        </div>
        <div id="mainNav">
            <table width="1000" border="0" cellpadding="0" cellspacing="0">
                <tr>

                </tr>
            </table>
        </div>
        <div id="content">
            <div class="abgne_tab">
                <{include file='menu1.inc.tpl'}>
                <div class="tab_container">
                    <div id="menu-lv2"></div>

                    <br/>

                    <div id="tab" class="tab_content">
                        <table width="980" border="0" cellpadding="4" cellspacing="1">
                            <tr>
                                <td bgcolor="#DBDBDB">
                                    <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                        <tr>
                                            <td height="17" bgcolor="#FFFFFF">
                                                <div id="container">
                                                    Working Area ....
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div id="footer">
            <p>2012 第一建築經理股份有限公司 版權所有</p>
        </div>
    <div>
</body>
</html>