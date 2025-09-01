<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <{include file='meta.inc.tpl'}>
    <link rel="stylesheet" href="/css/colorbox.css" />
    <link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />
    <script src="/js/jquery.colorbox.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {

    });

    function colorbx(url) {
        $.colorbox({href:url});
    }

    function report() {
        $('.cmc_overlay').show();

        let _year = $('[name="year"] :selected');
        let _season = $('[name="season"] :selected');
        let _sales = $('[name="sales"] :selected');
        
        if (_sales.val() == '0') {
            alert('請指定業務人員!!');
            $('[name="sales"]').focus();
            $('.cmc_overlay').hide();

            return false;
        }

        $.post('salesTwhgNoCaseReportDetail.php', {'year': _year.val(), 'season': _season.val(), 'sales': _sales.val()}, function(response) {
            console.log(response);

            let _year_ = _year.text();
            let _season_ = _season.text();
            let _sales_ = _sales.text();
            let el = '<div style="padding:8px;background-color: #E4BEB1;text-align: center;">店名稱</div>';

            response.forEach(function(item, index) {
                let _color = (index % 2 == 0) ? '#F8ECE9' : '';
                el += '<div style="padding:8px;background-color: '+ _color + '">' + item.code + ' ' + item.bStore + '</div>';
            });

            $('#result').empty().html(el);

            $('.cmc_overlay').hide();
        }, 'json').fail(function() {
            alert('系統異常！請稍後在試');
            $('.cmc_overlay').hide();
        });
    }
</script>
<style>

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

                                                    <div style="padding-bottom:20px;margin-top:-10px;">
                                                        <h1 style="text-align:left;font-size:12pt;">台屋未進案季報表</h1>
                                                    </div>

                                                    <div style="padding: 10px;border: 1px solid #CCC;width: 450px;margin: 0px auto;height: 80px; text-align: center;">
                                                        <form method="POST" id="form1">
                                                            <input type="hidden" name="excel" value="OK">
                                                            時間：
                                                            <{html_options name=year options=$menu_year selected=$year}>
                                                            <{html_options name=season options=$menu_season selected=$season}>
                                                            負責業務：
                                                            <{html_options name=sales options=$menu_sales selected=$sales}>
                                                        </form>
                                                        <div style="margin-top: 20px;">
                                                            <button style="padding: 5px;" onclick="report()">產出報表</button>
                                                        </div>
                                                    </div>

                                                    <div id="result" style="margin-top:50px;padding: 10px;width: 450px;margin: 0px auto;">
                                                    </div>

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