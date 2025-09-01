<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <link rel="stylesheet" href="/css/colorbox.css" />
        <script src="/js/jquery.colorbox.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $( "#dialog" ).dialog({
                    autoOpen: false,
                    modal: true,
                    minHeight:50,
                    show: {
                        effect: "blind",
                        duration: 1000
                    },
                    hide: {
                        effect: "explode",
                        duration: 1000
                    }
                });

                $('#export').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#export').click(function () {
                    $('[name="form_search"]').submit();
                });
            });

        </script>
        <style>
        #dialog {
            background-image:url("../images/animated-overlay.gif") ;
            background-repeat: repeat-x;
            margin: 0px auto;
        }

        .tb{
            text-align: center;
            border: solid 1px #ccc;
        }

        .tb th{
            background-color:#E4BEB1;
            padding:4px;
        }
        </style>
    </head>
    <body id="dt_example">
        <form name="excel_out" method="POST">
           <input type="hidden" name="cat">
        </form>
        <form action="lineList.php" name="line" target="_blank">
            
        </form>
         <form action="lineList2.php" name="line2" target="_blank">
            
        </form>
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
                            </table></td>
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
                                        <h1>2022 履保案件送第一，年終禮券大放送!!!</h1>
                                        <div id="container">
                                            <form name="form_search" method="POST">
                                                <center>
                                                    <table border="0" cellspacing="2" cellpadding="2" class="tb">
                                                        <tr>
                                                            <th width="20%">活動方案</th>
                                                            <td>活動期間使用第一建經履保完成簽約並進案者</td>
                                                        </tr>
                                                        <tr>
                                                            <th width="20%">注意事項</th>
                                                            <td style="text-align:left;padding-left: 10px;">
                                                                <div>● 於活動結束次月底前完成統計並開始發送。</div>
                                                                <div>● 所有送件資格均需第一建經查驗合格為有效。</div>
                                                                <div>● 以成交總價 500 萬元(含)以上，且未特別折扣履約保證費之案件(預售屋換約以保管金額為成交總價計算)，納入活動統計。</div>
                                                                <div>● 跨店合作案件，其件數以 0.5 件計算。</div>
                                                                <div>● 中途解約案件，不計入活動。</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="padding: 10px;">
                                                                <input type="hidden" name="act" value="excel">
                                                                <button id="export">下載EXCEL</button>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </form>
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