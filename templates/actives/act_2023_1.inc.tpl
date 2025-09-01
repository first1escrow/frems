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

                $('#brand_exc').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#group_exc').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#export').click(function () {
                    $('[name="form_search"]').submit();
                });

                $('#group_exc').click(function () {
                    $('[name="form_group"]').submit();
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
                                        <h1>2023 鴻兔大展 我要第一!!!</h1>
                                            <table style="margin-left: auto; margin-right: auto; height: 100px">
                                                <tr>
                                                    <td>
                                                        <!--各別店家-->
                                                        <form name="form_search" method="POST">
                                                            <center>
                                                                <input type="hidden" name="act" value="excel">
                                                                <button id="export">下載EXCEL(店家)</button>
                                                            </center>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <!--集團店家-->
                                                        <form name="form_group" method="POST" >
                                                            <center>
                                                                <input type="hidden" name="group_act" value="excel3">
                                                                <button id="group_exc">下載EXCEL(集團)</button>
                                                            </center>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <!--新簽約店家-->
                                                        <!--<form name="form_new" method="POST" >
                                                            <center>
                                                                <input type="hidden" name="new_act" value="excel4">
                                                                <button id="new_exc">新簽約店家(依業務分)</button>
                                                            </center>
                                                        </form>-->
                                                    </td>
                                                </tr>
                                            </table>
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