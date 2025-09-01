<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<{include file='meta.inc.tpl'}>

<script type="text/javascript">
// $("link[href='/libs/datatables/media/css/demo_table.css']").remove();

$(document).ready(function() {
    $('#download-exc').click(function () {
        let year_download = $('[name="year"]').val();
        $('[name="year_download"]').val(year_download);
        <{if $smarty.session.member_pDep == 7}>
            let sales_download = <{$smarty.session.member_id}>;
        <{else}>
            let sales_download = $('[name="sales"]').val();
        <{/if}>

        $('[name="sales_download"]').val(sales_download);
        $('[name="repo_download"]').submit();
    });
});

function query() {
    let _year = $('[name="year"] option:selected').val();
    <{if $smarty.session.member_pDep == 7}>
        let _sales = <{$smarty.session.member_id}>;
    <{else}>
        let _sales = $('[name="sales"] option:selected').val();
    <{/if}>

    // alert('date = ' + _date + ', sales = ' + _sales);
    let _url = '/includes/report/salesAchievement.php';
    $.post(_url, {'sales': _sales, 'year': _year}, function(response) {
        $('#result').empty();
        let _index = 1;
        let _pName;
        let _mon = 0;
        let _next = 0;
        let _color_index = 0;

        let _summary = response.summary;
        $('#certify-money').empty().html(parseInt(_summary.sCertifiedMoney).toLocaleString());
        $('#feedback-money').empty().html(parseInt(_summary.sFeedBackMoney).toLocaleString());
        $('#balance').empty().html((parseInt(_summary.sCertifiedMoney)-parseInt(_summary.sFeedBackMoney)).toLocaleString());

        let _data = response.data;
        $.each(_data, function(index, obj) {
            //與目標比較 單月
            let growth_month = (obj.sCertifiedMoney - obj.mon)/obj.mon;
            growth_month = (growth_month * 10 / 10 * 100).toFixed(2);
            if (obj.sCertifiedMoney == null) {
                growth_month = '-';
            }
            //與目標比較 累計
            let growth = (obj.sCertifiedMoneyTotal - obj.total)/obj.total;
            let growthRate = (growth * 10 / 10 * 100).toFixed(2);
            if (obj.sCertifiedMoneyTotal == null) {
                growthRate = '-';
            }
            //與去年實際比較 單月
            let compare_month = (obj.sCertifiedMoney - obj.lastsCertifiedMoney) / obj.lastsCertifiedMoney;
            compare_month = (compare_month * 10 / 10 * 100).toFixed(2);
            if (obj.sCertifiedMoney == null) {
                compare_month = '-';
            }
            //與去年實際比較 累計
            let realGrouwth = (obj.sCertifiedMoneyTotal - obj.lastsCertifiedMoneyTotal) / obj.lastsCertifiedMoneyTotal;
            let realGrowthRate = (realGrouwth * 10 / 10 * 100).toFixed(2);
            if (obj.sCertifiedMoneyTotal == null) {
                realGrowthRate = '-';
            }

            let _color = 'background-color: #F2F2F2;';
            if ((_index % 2) == 0) {
                _color = '';
            }

            let el = '<tr style="' + _color + '"><td style="padding: 5px;">' + parseInt(obj.sDate.substr(5,2)) + '&nbsp;月'
                    + '</td><td style="padding: 5px;">' + obj.pName
                    + '</td><td class="no-align">' + parseInt(obj.mon).toLocaleString()
                    + '</td><td class="no-align">' + parseInt(obj.total).toLocaleString()
                    + '</td><td class="no-align">' + (parseInt(obj.sCertifiedMoney).toLocaleString() == '非數值' ? '-': parseInt(obj.sCertifiedMoney).toLocaleString())
                    + '</td><td class="no-align">' + (parseInt(obj.sCertifiedMoneyTotal).toLocaleString() == '非數值' ? '-': parseInt(obj.sCertifiedMoneyTotal).toLocaleString())
                    + '</td><td class="no-align">' + growth_month
                    + '</td><td class="no-align">' + growthRate
                    + '</td><td class="no-align">' + parseInt(obj.lastsCertifiedMoney).toLocaleString()
                    + '</td><td class="no-align">' + parseInt(obj.lastsCertifiedMoneyTotal).toLocaleString()
                    + '</td><td class="no-align">' + compare_month
                    + '</td><td class="no-align">' + realGrowthRate
                                                        + '</td></tr>'
            ;
            
            _pName = obj.pName;
            _mon = obj.mon;
            _next += parseInt(_mon);

            $('#result').append(el);
            _index ++;
        });

        let _total = 0;
        for (let i = _index; i <= 12 ; i ++) {
            if (i == _index) {
                _next = parseInt(_next);
            } else {
                _next = 0;
            }

            let _color = 'background-color: #F2F2F2;';
            if ((i % 2) == 0) {
                _color = '';
            }

            _total += parseInt(_mon) + parseInt(_next);
            let el = '<tr style="' + _color + '"><td style="padding: 5px;">' + i + '&nbsp;月'
                    + '</td><td style="padding: 5px;">' + _pName
                    + '</td><td class="no-align">' + parseInt(_mon).toLocaleString()
                    + '</td><td class="no-align">' + parseInt(_total).toLocaleString()
                    + '</td><td class="no-align"> - '
                    + '</td><td class="no-align"> - '
                    + '</td><td class="no-align"> - '
                    + '</td><td class="no-align"> - </td></tr>';

            $('#result').append(el);
        }

        $('#result-area').show();
    }, 'json');
}
</script>
<style>
.css-year {
    width: 100px;
}

.align-center {
    vertical-align:middle;
    text-align:center;
    border-bottom: 1px solid #CCC;
    padding: 5px;
}

.no-align {
    text-align: right;
    padding-right: 2px;
    height: 50px;
}

.summary-style {
    width: 200px;
    padding: 5px;
}

.table-td-width {
    width: 200px;
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
                                        <h1>業務績效目標與達成率</h1>
                                        <div style="border: 1px solid #CCC;width:400px;margin: auto;">
                                            <div style="padding: 20px;" id="condition">
                                                <form method="POST" id="form1">
                                                    <input type="hidden" name="xls">
                                                    <div>    
                                                        <span>
                                                        查詢年度：
                                                        <{html_options name="year" class="css-year" options=$year_option selected=$year}>
                                                        </span>
                                                        <span>
                                                        <{if $smarty.session.member_pDep != 7}>
                                                        業務：<{html_options name="sales" class="css-year" options=$sales_option selected=$sales}>
                                                        <{/if}>
                                                        </span>
                                                    </div>
                                                    <div style="padding-top: 20px;text-align:center;">
                                                        <input type="button" style="width: 25%;padding: 2px;" value="查詢" onclick="query()">
                                                        <input type="button" id="download-exc" style="width: 25%;padding: 2px;" value="下載" >
                                                    </div>
                                                </form>
                                                <form name="repo_download" method="POST" >
                                                    <input type="hidden" name="submit_download" value="Y">
                                                    <input type="hidden" name="year_download" >
                                                    <input type="hidden" name="sales_download" >
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div id="result-area" style="display:none;">
                                            <div style="matgin 0px auto;padding-bottom: 20px;">
                                                <table style="text-align: center;margin: 0px auto;padding: 5px;">
                                                    <tr style="background-color: #CCC;">
                                                        <th class="summary-style">履保費金額</th>
                                                        <th class="summary-style">回饋金金額</th>
                                                        <th class="summary-style">實收履保費金額</th>
                                                    </tr>
                                                    <tr>
                                                        <td class="summary-style" id="certify-money"></td>
                                                        <td class="summary-style" id="feedback-money"></td>
                                                        <td class="summary-style" id="balance"></td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <table class="table table-striped" style="margin: 0px auto;">
                                                <thead>
                                                    <tr>
                                                        <th rowspan=2 class="align-center" style="vertical-align:middle;width: 20px;">月份</th>

                                                        <th rowspan=2 class="align-center" style="vertical-align:middle;width: 50px;">業務</th>
                                                        
                                                        <th colspan=2 class="align-center table-td-width">目標</th>
                                                        <th colspan=2 class="align-center table-td-width">實際</th>
                                                        <th colspan=2 class="align-center table-td-width" style="vertical-align:middle;">與目標比較&plusmn;值(%)</th>
                                                        <th colspan=2 class="align-center table-td-width">去年</th>
                                                        <th colspan=2 class="align-center table-td-width" style="vertical-align:middle;">與去年實際比較&plusmn;值(%)</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="align-center">單月</th>
                                                        <th class="align-center">累計</th>
                                                        <th class="align-center">單月</th>
                                                        <th class="align-center">累計</th>
                                                        <th class="align-center">單月</th>
                                                        <th class="align-center">累計</th>
                                                        <th class="align-center">單月</th>
                                                        <th class="align-center">累計</th>
                                                        <th class="align-center">單月</th>
                                                        <th class="align-center">累計</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="result">
                                                    
                                                </tbody>
                                            </table>
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