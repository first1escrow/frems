<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                
                $("[name='send']").on('click',  function() {

                   $("[name='save']").val('ok');
                    $("[name='form_search']").submit();


                });

                /*-----------------------------------------*/

                $("[name='send']").button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
            } );
			
			
        </script>
        <{$script}>
		<style>
		#container table{
            border: solid #CCC 1px;
        }
        #container  th{
            background-color:#E4BEB1;
             /*border: solid #CCC 1px;*/
            padding: 10px;
        }
       
        #container td{
            padding: 10px;
             border: solid #CCC 1px;
        }

        .ui-autocomplete-input {
                width:250px;
                height: 20px;
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
                                        <h1>建經小幫手</h1>
                                        <div id="container">
                                            
                                            <center>
                                                <font color="red">※限傳送給經辦</font>
                                                <form name="form_search" method="POST">
                                                <table width="80%">
                                                    <!-- <tr>
                                                        <th>對象</th>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <{html_checkboxes name=member options=$member selected=0}>
                                                        </td>
                                                    </tr> -->
                                                    <tr>
                                                        <th>文字內容</th>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <textarea name="txt" id="" cols="30" rows="10"></textarea>
                                                            <input type="hidden" name="save">
                                                        </td>
                                                    </tr>
                                                </table>
                                                <div style="padding:10px;"><input type="button" name="send" value="傳送"></div>
                                                
                                                </form>
                                                <!-- <div id="show"></div> -->
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