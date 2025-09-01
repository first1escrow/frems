<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                var ck = "<{$smarty.session.member_id}>";
               
                
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
                $(".ui-dialog-titlebar").hide() ;
                
               

                $('#export').live('click', function(event) {

                    
                     show("open") ;
                   
                    
                    $("[name='form_search']").submit();
                     show('close');
                });


                $('#export').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );

           
           

            });
            
            function show(op) {
                $( "#dialog" ).dialog(op) ;
            }

          

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
            width:300px;
            background-color:#E4BEB1;
            padding:4px;
            /**/

        }
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
                                    <td colspan="3" align="right"></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"></td>
                                    <td width="14%" align="center"></td><td width="5%" height="30" colspan="2"></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            
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
                                        <h1>簽約案件統計表</h1>
                                        <div id="container">
                                            <form name="form_search">
                                                <table border="0" cellspacing="10" cellpadding="10">
                                                    <tr>
                                                        <td colspan="3">游明桐、燊氐集團(林映成)、<!-- 莊威玲、藍海集團、 -->葉寶桐、幸福家、飛鷹、群義</td>
                                                    </tr>
                                                    <tr>
                                                        <th align="left">查詢時間︰</th>
                                                        <td>
                                                            <select name="years">
                                                                <{$years}>
                                                            </select> 年度
                                                            <input type="hidden" name="ck" value="1">
                                                            <select name="month">
                                                                <{$menu_scrivener}>
                                                            </select> 月
                                                            <input type="hidden" name="ck" value="1">
                                                        </td>
                                                        <td><input type="button" id="export" value="匯出EXCEL"></td>
                                                        
                                                    </tr>
                                                   
                                                   
                                                </table>
                                            </form>
                                            
                                            
                                            <center>
                                              
                                                
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