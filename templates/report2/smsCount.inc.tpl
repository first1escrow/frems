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
                });


                $('#cls').live('click', function(event) {
                    window.location = 'brandCalculate.php' ;
                });
				
                $('#cls').button( {
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
            padding-top: 10px;
            padding-bottom: 10px;


        }
        .tb{
           
            border: solid 1px #ccc;

        }
        .tb th{
           
            /*background-color:#F8ECE9;*/
            padding-top: 5px;
            padding-bottom: 5px;
            /**/

        }
        .tb td{
           
            /*background-color:#F8ECE9;*/
            padding-top: 5px;
            padding-bottom: 5px;
            /**/

        }
		#mainArea td {
			padding: 5px;
			padding-top:10px;
		}
		#mainArea th {
			padding: 5px;
			padding-top:10px;
		}
		#showArea td {
			padding: 5px;
			border-bottom-width: 1px;
			border-bottom-style: dotted;
			border-bottom-color: #CCC;
			width: 200px;
			text-align: center;
		}
		#showArea th {
			padding: 5px;
			border-bottom-width: 1px;
			border-bottom-style: dotted;
			border-bottom-color: #CCC;
			width: 100px;
			text-align: center;
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
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
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
                                        <h1>簡訊數量</h1>
                                        <div id="container">
                                            <center>
                                            <form name="form_search" method="post">
												
                                                <table border="0" cellspacing="0" cellpadding="0" class="tb" id="mainArea">
                                             
                                                    <tr>
                                                        <th style="">查詢時間：</th>
                                                        <td>
															年度：
															<select name="yearDate" style="width:60px;">
															<{$yearOption}>
															</select>　
															月份：
															<select name="monthDate" style="width:60px;">
															<{$monthOption}>
															</select>
                                                        </td>
                                                       
                                                    </tr>
                                                   <tr>
                                                       <td colspan="2"><{$total}></td>
                                                   </tr>
                                                    <tr>
                                                        <td colspan="2" align="center" style="padding-bottom:15px;">

                                                            <input type="button" id="export" value="查詢">
                                                            <input type="button" id="cls" value="清除">
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