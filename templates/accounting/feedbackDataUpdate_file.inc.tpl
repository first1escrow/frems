<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {

                $("#import").live('click', function() {
                  
                    $("#myform").submit();
                });

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
                $('#import').button({
                    icons:{
                        primary: "ui-icon-folder-open"
                    }
                }) ;
				
               
            } );
			
			function dia(op) {
				$( "#dialog" ).dialog(op) ;
			}
        </script>
		<style>
		#dialog {
			background-image:url("../images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
         .tb_main{
            border: 1px solid #999;

        }
        .tb{
            border: 1px solid #FFFFFF;
            width: 50%;
            /*background-color: #FCEEEE;*/

        }
        .tb td{
            border: 1px solid #999;
            padding: 5px;
        }
        .tb th{
            border: 1px solid #999;
            background: #FCEEEE;
            padding: 5px;
        }

        .div-inline{ 
            display:inline;
           /* width: 90%;
            float: center;
            padding-bottom: 50px;
*/


            /*padding-right: 20px;*/
        } 
        .div-inline th{
          text-align: left;
        }
        .div-inline td{
            padding-left: 20px;
        }
        #show {
            padding: 50px;
           
        }
        .div-inline2{ 
            display:inline;
            width: 100%;
            float: center;
            padding-bottom: 50px;
           
            /*padding-right: 20px;*/
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
                                        <h3>&nbsp;</h3>
                                        <div id="container">
                                            <center>
                                                <h1>回饋金資料匯入</h1>
                                                <br>
                                                <div style="paddin:20px;text-align:right;"><a href="excel/example/feedbackExample.xlsx" target="_blank">範例檔格式下載</a></div>
                                                <br>
                                                <form name="myform" id="myform" method="POST" enctype="multipart/form-data" >
                                                <table  align="center" class="tb_main" cellpadding="10" cellspacing="10">
                                                    
                                                    <tr>
                                                        <th align="center">上傳檔案<input type="hidden" value='1' name='check'></th>
                                                        <td align="center"><input name="upload_file" type="file"  /></td>
                                                        <td align="center"> <input type="button" id="import" value="匯入"></td>    
                                                        <td>※限EXCEL2007以上格式(.xlsx)</td>
                                                    </tr>
                                                   
                                                </table>
                                                </form>
                                                <br>

                                                <div id="show">
                                                    <{$show}>
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