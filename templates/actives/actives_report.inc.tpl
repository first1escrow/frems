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
            
            function dia(op) {
                $( "#dialog" ).dialog(op) ;
            }

            function act (cat) {
                 $("[name='cat']").val(cat); //那 一個活動
                 $("[name='excel_out']").submit();
            }

            function act_sms(cat)
            {
                var url = "actives_2015_sheep_sms.php?cat="+cat;
                  $("#sms").colorbox({iframe:true, width:"1200px", height:"90%", href:url}) ;
                // $.colorbox({iframe:true, width:"1000px", height:"100%", href:url}) ;
            }
            function lineEx(){
                $("[name='line']").submit();
            }
            function lineEx2(){
                $("[name='line2']").submit();
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('../bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
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
                                        <!-- <h1>活動報表</h1> -->
                                        <div id="container">
                                            <form name="form_search">
                                            <center>
                                            <table border="0" cellspacing="2" cellpadding="2" class="tb">
                                                <tr>
                                                    <th>活動名稱</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                                <tr>
                                                    <td>「羊羊得意 第一讚！」履保進案贈獎活動</td>
                                                    <td>
                                                        <input type="button" id='export' value="匯出EXCEL" onclick="act('2015sheep')">&nbsp;&nbsp;&nbsp;
                                                        <{if $smarty.session.member_banktrans == 1}>
                                                            <input type="button" id='sms' value="寄送簡訊" onclick="act_sms('2015sheep')">
                                                        <{/if}>
                                                    </td>
                                                   
                                                </tr>
                                               
                                            </table>
                                            <br>
                                            <table  border="0" cellspacing="2" cellpadding="2" class="tb">
                                                <tr>
                                                    <th>名稱</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                                <tr>
                                                    <td>LINE地政士名單</td>
                                                    <td>
                                                   
                                                        <input type="button" value="匯出EXCEL" onclick="lineEx()" class="bt">
                                                    
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>LINE仲介名單</td>
                                                    <td>
                                                   
                                                        <input type="button" value="匯出EXCEL" onclick="lineEx2()" class="bt">
                                                    
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