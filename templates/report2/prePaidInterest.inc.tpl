<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<{include file='meta.inc.tpl'}>

<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    // $('.cmc_overlay').hide();
    // $('.cmc_overlay').show();

});

function colorbx(url) {
	$.colorbox({href:url});
}

function go() {
    let _account = $('[name="account"]:checked').val();
    let _from_date = $('[name="fromDate"]').val();
    let _to_date = $('[name="toDate"]').val();

    if (!_from_date) {
        alert('請指定日期範圍(起)');
        return;
    }
    
    if (!_to_date) {
        alert('請指定日期範圍(迄)');
        return;
    }

    $('#form1').submit();
}
</script>
<style>
.line-min {
    padding: 5px;
    text-align:left;
}

.btn {
    padding: 5px;
    width: 100px;
}
</style>
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
                    <td width="753">
                        <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                            <tr>
                                <td colspan="3" align="right">
                                    <div id="abgne_marquee" style="display:none;">
                                        <ul>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" align="right">
                                    <h1><{include file='welcome.inc.tpl'}></h1>
                                </td>
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
                                <h3>&nbsp;</h3>
                                <div id="container">
                                    <form method="post" id="form1">
                                        <div style="margin: 0px auto;width: 420px;">
                                            <{* <div class="line-min">
                                                選擇帳戶：
                                                <label for="account1">
                                                    <input type="radio" id="account1" name="account" value="60001110019411" checked="checked">&nbsp;60001-110019411
                                                </label>
                                                <label for="account2">
                                                    <input type="radio" id="account2" name="account" value="55006110050011">&nbsp;55006-110050011
                                                </label>
                                            </div> *}>
                                            <div class="line-min">
                                                日期範圍：
                                                <input type="date" name="fromDate" value="<{$fromDate}>"> ~ <input type="date" name="toDate" value="<{$toDate}>">
                                            </div>
                                            <div style="margin-top: 20px;text-align:center;">
                                                <button class="btn" onclick="go()"><i class="fa fa-file-excel-o"></i> 產出</button>
                                            </div>
                                        </div>
                                    </form>
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