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
                                        <h1>品牌案件統計</h1>
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
                                                       <td colspan="2">&nbsp;</td>
                                                   </tr>
                                                    <tr>
                                                        <td colspan="2" align="center" style="padding-bottom:15px;">

                                                            <input type="button" id="export" value="查詢">
                                                            <input type="button" id="cls" value="清除">
                                                            <input type="hidden" name="query" value="ok">
                                                        </td>
                                                    </tr>
                                                   
                                                </table>
                                            </form>
											
											<div id="showArea" style="margin-top:30px;display:<{$query}>;">
												<table border=0>
													<tr style="font-weight:bold;">
														<th style="font-size:14pt;color:#000080;">仲介品牌</th>
														<th style="font-size:14pt;color:#000080;"><{$m}> 月份進案統計</th>
														<th style="font-size:14pt;color:#000080;">進案履保費統計</th>
														<th style="font-size:14pt;color:#000080;"><{$m}> 月份結案統計</th>
														<th style="font-size:14pt;color:#000080;">結案履保費統計</th>
													</tr>
													<tr>
														<th>台屋直營</th><td><{$list1.count.2}></td><td><{$list1.money.2}></td><td><{$list2.count.2}></td><td><{$list2.money.2}></td>
													</tr>
													<tr>
														<th>台屋加盟</th><td><{$list1.count.T}></td><td><{$list1.money.T}></td><td><{$list2.count.T}></td><td><{$list2.money.T}></td>
													</tr>
                                                    <!-- <tr>
														<th>優美地產</th>
                                                        <td><{if $list1.count.U == ''}>0<{else}><{$list1.count.U}><{/if}></td>
                                                        <td><{if $list1.money.U == ''}>0<{else}><{$list1.money.U}><{/if}></td>
                                                        <td><{if $list2.count.U == ''}>0<{else}><{$list2.count.U}><{/if}></td>
                                                        <td><{if $list2.money.U == ''}>0<{else}><{$list2.money.U}><{/if}></td>
													</tr> -->
                                                    <!-- <{if $smarty.session.member_id == 6}>
													<tr>
														<th>永春不動產</th><td><{$list1.count.F}></td><td><{$list1.money.F}></td><td><{$list2.count.F}></td><td><{$list2.money.F}></td>
													</tr>
                                                    <{/if}> -->
													<tr>
														<th>其他品牌</th><td><{$list1.count.O}></td><td><{$list1.money.O}></td><td><{$list2.count.O}></td><td><{$list2.money.O}></td>
													</tr>
													<tr>
														<th>代書個人</th><td><{$list1.count.3}></td><td><{$list1.money.3}></td><td><{$list2.count.3}></td><td><{$list2.money.3}></td>
													</tr>
													<tr style="font-weight:bold;color:red;">
														<th>總計</th><td><{$list1.total}></td><td><{$list1.total2}></td><td><{$list2.total}></td><td><{$list2.total2}></td>
												</table>
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