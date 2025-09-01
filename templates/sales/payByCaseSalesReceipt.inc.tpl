<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <{include file='meta2.inc.tpl'}>
    <link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />
    <script type="text/javascript">
        $(document).ready(function() {
            searchData();
        });

        function dia(op) {
            $( "#dialog" ).dialog(op) ;
        }

        function searchData() {
            $('.cmc_overlay').show();

            let tranStatus = $('[name="banktranStatus"]').val();

            $.ajax({
                url: 'payByCaseSalesReceipt_result.php',
                type: 'POST',
                dataType: 'html'
            })
            .done(function(html) {
                $("#result").html(html);
                $('.cmc_overlay').hide();
            });
        }

    </script>
    <style>
        #dialog {
            background-image:url("../images/animated-overlay.gif") ;
            background-repeat: repeat-x;
            margin: 0px auto;
        }
        .tb_main{
            border: 1px solid #999;

        }
        .tb{
            border: 1px solid #FFFFFF;
            width: 100%;
            /*background-color: #FCEEEE;*/

        }
        .tb td{
            border: 1px solid #999;
            padding: 5px;
        }
        .tb th{
            border: 1px solid #999;
            background: #FCEEEE;
            padding: 5px;
        }
        .div-inline{
            display:inline;
        }
        .div-inline th{
            text-align: left;
        }
        .div-inline td{
            padding-left: 20px;
        }
        #show {
            padding: 50px;

        }
        .div-inline2{
            display:inline;
            width: 100%;
            float: center;
            padding-bottom: 50px;

            /*padding-right: 20px;*/
        }
        .xxx-button {
            color:#FFFFFF;
            font-size:12px;
            font-weight:normal;

            text-align: center;
            white-space:nowrap;

            background-color: #a63c38;
            border: 1px solid #a63c38;
            border-radius: 0.35em;
            font-weight: bold;
            padding: 0 20px;
            margin: 5px auto 5px auto;

            width:auto;
            height:20px;
            font-size:16px;
        }
        .xxx-button:hover {
            background-color:#333333;
            border:1px solid #333333;
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
                            <div id="container">
                                <center>
                                    <h1>收據繳回確認表</h1>
                                    <br>
                                    <div id="result">
                                    </div>
                                    <div id="show">
                                        <{$show}>
                                    </div>
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