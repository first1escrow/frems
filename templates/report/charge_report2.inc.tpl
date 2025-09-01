<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<meta http-equiv="X-UA-Compatible" content="IE=9"/>

		<{include file='meta.inc.tpl'}> 		
		<script type="text/javascript">

		var count =0;
		var tmp='';

		$(document).ready(function() {
			$('#add').hide();

			$( "#dialog" ).dialog({
				autoOpen: false,
				modal: true,
				minHeight:50,
				show: {
					effect: "blind",
					duration: 3000
				},
				hide: {
					effect: "explode",
					duration: 3000
				}
			});
			$('#formList').on('click', function() {
				var sales=$("[name='sales']").val();
				var val= $('[name="report_type"]:checked').val();
				
				if (sales=='0'&&val==1) {
					alert('請選擇業務');
					return false;
				}else
				{
					 var url = 'charge_report_excel2.php';
				
					$('[name="form1"]').attr('action',url);
				
					$('[name="form1"]').submit();
				}
			});

			
			

			$('#add').on('click', function() {
				count=count+1;
				var sales=$("[name='sales']").val();

				var url='charge_search2.php';
				// if (sales=='0') {
				// 	alert('請選擇業務');
				// 	return false;
				// }
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
			$('[name="report_type"]').on('click', function() {
				var val= $('[name="report_type"]:checked').val();
				
				if (val==1) 
				{
					$('#add').hide();
					
						$('#show_sales span').remove();
						tmp='';
						
					
				}else
				{
					$('#add').show();
				}
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
									<div id="menu-lv2">
                                                        
										</div>
									<form name='form1'  method="POST">
										<table width="100%" border="0" cellpadding="4" cellspacing="0" align="center" style="margin-top:50px;margin-right:100px;">
										<tr style="background-color:#F8ECE9;"><td width="25%" ></td><td style="padding:10px;">
												<input type="radio" name='date_type' value="1" checked>簽約時間
												<input type="radio" name='date_type' value="2">履保費出款日
												<input type="radio" name='date_type' value="3">結案日期
											</td><td width="25%"></td></tr>
										<tr style="background-color:#F8ECE9;"><td></td><td style="padding:10px;">民國<select name="start_y"><{$y}></select>年<select name="start_m"><{$m}></select>月&nbsp;~&nbsp;<select name="end_y"><{$y}></select>年<select name="end_m"><{$m}></select>月</td><td></td></tr>	
										<tr style="background-color:#F8ECE9;"><td></td><td style="padding:10px;">案件狀態：<{html_options name="cas_status" options=$menu_status }></td><td></td></tr>
										<tr style="background-color:#F8ECE9;"><td></td>
											<td style="padding:10px;">
												仲介類別：<select name="brand"><{$menu_caregory}></select>
															
												<{if $smarty.session.member_id == 6}>
													 <{html_checkboxes name=category options=$menu_category2 }>
												<{/if}>

											</td>
											<td>

											</td>
										</tr>
										<tr style="background-color:#F8ECE9;"><td></td><td style="padding:10px;"><input type='radio' name='report_type' value='1' checked>單一業務比較&nbsp;<input type="radio" name='report_type' value="2">多個業務比較</td><td></td></tr>
										<tr style="background-color:#F8ECE9;"><td></td><td style="padding:10px;"><div  id="show_sales">業務： <{html_options name=sales options=$menu_sales}><input type="button" value="加入比較" id="add">&nbsp;&nbsp;</div></td><td></td></tr>
										<tr style="background-color:#F8ECE9;"><td></td><td style="padding:10px;" align="center"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" id="formList" role="button" aria-disabled="false"><span class="ui-button-icon-primary ui-icon ui-icon-document"></span><span class="ui-button-text">輸出報表</span></button></td><td></td></tr>
										</table>
											
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