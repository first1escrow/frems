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

                $('#search').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#sms').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });
                $('.bt').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });


            });
           
        </script>
        <style>
        #dialog {
            background-image:url("../images/animated-overlay.gif") ;
            background-repeat: repeat-x;
            margin: 0px auto;
        }

        #search{
            width: 80px;
            height: 50px;
            font-size: 15px;
        }
        #b{
            padding-top: 10px;
            padding-bottom: 10px;


        }
        .tb{
            text-align: center;
            border: solid 1px #ccc;

        }
        .tb th{
            background-color:#E4BEB1;
            padding:4px;
            /**/

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
                                        <h1>案件移轉量</h1>
                                        <div id="container">
                                            <div>※建經資料以店家區域為區分條件，配件會根據店家做平分</div>
                                            <div>※政府上個月的資料不會很快在下個月顯示，目前排成每10天抓一次</div>

                                            <form name="form_search" method="POST">
                                            <center>
                                            <table border="0" cellspacing="2" cellpadding="2" class="tb">
                                                <tr>
                                                    <th width="20%">時間</th>
                                                    <td>
                                                        <{html_options name="startYear" options=$menu_Year selected=$startYear}>年
                                                        <select name="startMonth" style="width:80px;">
                                                            <option value="">請選擇</option>
                                                            <option value="01">1月份</option>
                                                            <option value="02">2月份</option>
                                                            <option value="03">3月份</option>
                                                            <option value="04">4月份</option>
                                                            <option value="05">5月份</option>
                                                            <option value="06">6月份</option>
                                                            <option value="07">7月份</option>
                                                            <option value="08">8月份</option>
                                                            <option value="09">9月份</option>
                                                            <option value="10">10月份</option>
                                                            <option value="11">11月份</option>
                                                            <option value="12">12月份</option>
                                                        </select>
                                                        月
                                                        至
                                                        <{html_options name="endYear" options=$menu_Year selected=$endYear}>年
                                                        <select name="endMonth" style="width:80px;">
                                                            <option value="">請選擇</option>
                                                            <option value="01">1月份</option>
                                                            <option value="02">2月份</option>
                                                            <option value="03">3月份</option>
                                                            <option value="04">4月份</option>
                                                            <option value="05">5月份</option>
                                                            <option value="06">6月份</option>
                                                            <option value="07">7月份</option>
                                                            <option value="08">8月份</option>
                                                            <option value="09">9月份</option>
                                                            <option value="10">10月份</option>
                                                            <option value="11">11月份</option>
                                                            <option value="12">12月份</option>
                                                        </select>
                                                        月
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"><input type="submit" value="下載EXCEL"></td>
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