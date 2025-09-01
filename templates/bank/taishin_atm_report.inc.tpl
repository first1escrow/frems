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
                                        <h1>台新ATM企業送金存款人姓名</h1>
                                        <div id="container">
                                           <form action="report.php"  enctype="multipart/form-data" method="POST" form="form">
                                                <!-- <input type="file" name="upload_file" id="upload_file"> -->
                                                
                                                <select name="report" id="">
                                                    <option value="1">ATM企業送金存款人姓名</option>
                                                </select>
                                                <input type="file" name="upload_file">
                                                <input type="hidden" name="ok" id="" value="ok">
                                                <input type="submit" value="查詢">
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