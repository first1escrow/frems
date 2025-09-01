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
            width: 90%;
            background-color: #FCEEEE;
        }
        .tb td{
            border-bottom: 1px solid #999;
        }

        .div-inline{ 
            display:inline;
            width: 70%;
            float: center;
            padding-bottom: 50px;


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
                                            <h1>回饋金簡訊對象匯入</h1>
                                            <center>
                                                <form name="myform" id="myform" method="POST" enctype="multipart/form-data" >
                                                    <div>※通知簡訊對象以基本資料維護簡訊對象的地政士、店東、店長為準</div>
                                                    <br>
                                                <table  align="center" class="tb_main" cellpadding="10" cellspacing="10" style="margin-bottom: 10px;" width="80%">
                                                    <tr>
                                                        <td width="10%"><b>上傳類別</b></td>
                                                        <td align="left" colspan="3">
                                                            <input type="radio" name="cat" value="1" checked> 通知簡訊
                                                            <input type="radio" name="cat" value="0" > 收款簡訊
                                                            
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table  align="center" class="tb_main" cellpadding="10" cellspacing="10" width="80%">
                                                    
                                                    <tr >
                                                        <th align="center">上傳檔案<input type="hidden" value='1' name='check'></th>
                                                        <td align="center"><input name="upload_file" type="file"  /></td>
                                                        <td align="center"> <input type="button" id="import" value="匯入"></td>    
                                                        <td>※限EXCEL2007以上格式(.xlsx)</td>
                                                    </tr>
                                                   
                                                </table>
                                                </form>
                                                <br>

                                               <a href="excel/example/feedsmsexample.xlsx">回饋金簡訊匯入格式範例</a>
                                           </center>
                                           <div id="show">
                                            <{if $show != ''}>
                                                <script type="text/javascript">
                                                    alert("<{$show}>");
                                                </script>
                                                
                                            <{/if}>
                                            </div>
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