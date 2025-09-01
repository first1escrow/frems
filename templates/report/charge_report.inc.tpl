<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<meta http-equiv="X-UA-Compatible" content="IE=9"/>
		<link rel="stylesheet" href="colorbox.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script src="js/jquery.colorbox.js"></script>
		<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />
		<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
		<{include file='meta.inc.tpl'}> 		
		<script type="text/javascript">

		var count =0;
		var tmp='';

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

			$('#formList').click(function() {


			$( "#dialog" ).dialog("open") ;
				 var url = 'charge_report_excel.php';
				
			$('[name="form1"]').attr('action',url);
		
			$('[name="form1"]').submit();

				$( "#dialog" ).dialog("close") ;
			});

			$('#add').click(function() {
				count=count+1;
				var sales=$("[name='sales']").val();

				var url='charge_search.php';
				if (sales=='0') {
					alert('請選擇業務');
					return false;
				}
				array=tmp.split(',');
					if(count!=1)
					{
						for (var i = 0; i < array.length; i++) {
						
							if (array[i]==sales) {

								alert("此業務已新增過");
								return false;
							}
						}
					}
					tmp =tmp+sales+',';

				$.post(url,{'sales':sales,'act':'add','count':count},function(txt) {
							$('#show_sales').append(txt);
														
				}) ;
				
			});

					
		});

		function del_sales(sid)
			{
				
			    array=tmp.split(',');
			    tmp='';
			    for (var i = 0; i < array.length; i++) 
			    {
						
					if (array[i]!=sid) 
					{

						tmp =tmp+array[i]+',';	
					}
				}
				
				$('#sales'+sid).remove();

			}
		
		</script>
		<style>
					.ui-autocomplete-input {
							width:210px;
						}
					#center_line{
							margin:0px auto;width:500px;text-align:left;padding:10px;border:1px solid #ccc;height:300px; line-height:20px;
						}
						#dialog {
							background-image:url("/images/animated-overlay.gif") ;
							background-repeat: repeat-x;
							margin: 0px auto;
						}
		</style>
    </head>
    <body id="dt_example">
        <form name="form_edit" id="form_edit" method="POST">
            <input type="hidden" name="id" id="id" value='3' />
        </form>
        <form name="form_add" id="form_add" method="POST">
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            <{include file='menu1.inc.tpl'}>
				<div id="dialog"></div>
				<table width="1000" border="0" cellpadding="4" cellspacing="0">
					<tr>
						<td bgcolor="#DBDBDB">
							<table width="100%" border="0" cellpadding="4" cellspacing="1">
								<tr>
									<td height="17" bgcolor="#FFFFFF">
									
									<form name='form1'  method="POST">
										<div id="menu-lv2">
                                                        
										</div>
										
										<br/> 
										<h3>&nbsp;</h3>
										<div  id="center_line">
										<div style="height:30px;">
											
										</div>
										<div style="height:30px;border-bottom : 1px solid #ccc;">
											進案時間：民國<select name="start_y"><{$y}></select>年<select name="start_m"><{$m}></select>月&nbsp;~&nbsp;<select name="end_y"><{$y}></select>年<select name="end_m"><{$m}></select>月<br>
										</div>
										<div style="padding-top:20px;height:30px;" id="show_sales">
											業務： <{html_options name=sales options=$menu_sales}><input type="button" value="加入比較" id="add"><br>
											
										</div>
										
										
									</div>
									<div style="text-align:center;padding:20px;">
											<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" id="formList" role="button" aria-disabled="false"><span class="ui-button-icon-primary ui-icon ui-icon-document"></span><span class="ui-button-text">輸出報表</span></button>
										</div>
									</form>

<div id="footer" style="height:50px;">
<p>2012 第一建築經理股份有限公司 版權所有</p>
</div>
				</td>
			</tr>

		</table>
		</td>
	</tr>
</table>
</div>


</body>
</html>