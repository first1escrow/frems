<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
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
                });

           
           

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
           /* padding-top: 10px;
            padding-bottom: 10px;*/


        }
        .tb{
            text-align: center;
            border: solid 1px #ccc;

        }
        .tb th{
           
            background-color:#E4BEB1;
           
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
                                        <h1>業務簽約店家統計</h1>
                                        <div id="container">
                                            <center>
                                            <form name="form_search" method="post">

                                                <table border="0" cellspacing="5" cellpadding="5" class="tb">
                                                    <!-- <tr>
                                                        <th colspan="2" align="left">
                                                            查詢時間︰
                                                            <input type="radio" name="cat" value="1" checked="checked">進案日期
                                                            <input type="radio" name="cat" value="2">簽約日期
                                                            <input type="radio" name="cat" value="3">結案日期
                                                        </th>
                                                    </tr> -->
                                                    <tr>
                                                        <th>業務：</th>
                                                        <td>
                                                            <{if $smarty.session.member_pDep != 7}>
                                                             <{html_options name=sales options=$menu_sales selected=$sales}>
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                     <tr>
                                                        <th>開始時間</th>
                                                        <td>
                                                            
                                                           
                                                                <select name="sdateYear" style="width:50px;"><{$y}></select>年
                                                            
                                                                <select name="sdateMonth" style="width:50px;"><{$m}></select>月
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>結束時間</th>
                                                        <td>
                                                            
                                                            
                                                            
                                                           
                                                                <select name="edateYear" style="width:50px;"><{$y}></select>年
                                                            
                                                                <select name="edateMonth" style="width:50px;"><{$m}></select>月
                                                           
                                                            
                                                        </td>
                                                       <!--  <td>時間：</td>
                                                        <td><input type="text" name="StartDate" class="datepickerROC" style="width:100px;"> (起)&nbsp;～&nbsp;<input type="text" name="EndDate" class="datepickerROC" style="width:100px;"> (迄)
                                                            
                                                        </td> -->
                                                       
                                                    </tr>
                                                   
                                                    <tr>
                                                        <td colspan="2">

                                                            <input type="submit" id="export" value="匯出EXCEL">
                                                            <input type="hidden" name="ck" value="1">
                                                        </td>

                                                    </tr>
                                                   
                                                </table>
                                            </form>
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