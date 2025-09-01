<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>

<script src="../js/jquery-1.7.2.min.js"></script>


<{include file='meta.inc.tpl'}> 		
<script type="text/javascript">
$(document).ready(function() {
	var aSelected = [];
	
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
	
	$('#loading').dialog('close');
	$(".ajax").colorbox({width:"400",height:"100"});
	
	
	
	
	$("#show_ss").hide();
	$("#show_se").hide();
	
	$('[name="excel"]').on('click', function() {
		
		$('[name="search"]').submit();
	});

	$('[name="time"]').on('click',function(){
		var val = $('[name="time"]:checked').val();

		if (val =='y') {
			$("#show_ss").hide();
			$("#show_se").hide();
			$("#show_ms").hide();
			$("#show_me").hide();
		}else if(val =='m')
		{
			$("#show_ss").hide();
			$("#show_se").hide();
			$("#show_ms").show();
			$("#show_me").show();
		}else if(val =='s')
		{
			$("#show_ss").show();
			$("#show_se").show();
			$("#show_ms").hide();
			$("#show_me").hide();
		}
	});
});




</script>
<style>

	input.bt4 {
		padding:4px 4px 1px 4px;
		vertical-align: middle;
		background: #F8EDEB;border:1px #727272 outset;color:font-size:12px;margin-left:2px
	}
	input.bt4:hover {
		padding:4px 4px 1px 4px;
		vertical-align: middle;
		background:  #EBD1C8;border:1px #727272 outset;font-size:12px;margin-left:2px;cursor:pointer
	}

	#dialog {
		background-image:url("/images/animated-overlay.gif") ;
		background-repeat: repeat-x;
		margin: 0px auto;
	}
		
	.tb1 {
		border: 1px solid #CCC;
	}
	.tb1 th{
		padding-top:5px;
		padding-bottom:5px;
		background: #F8EDEB;
		text-align: left;
		padding-left: 10px;
		border: 1px solid #CCC;
			
	}
	.tb1 td{
		padding-top:5px;
		padding-bottom:5px;
		background:  #E4BEB1;
		padding-left: 10px;
		border-bottom: 1px solid #CCC;

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
                                    <td width="81%" align="right"></td>
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
										<h3>&nbsp;</h3>
										<div id="container">
										<div id="dialog"></div>
<div>
<form name="search" method="POST">
<h1>人員出款錯誤紀錄表</h1>
<br>
	<table cellspacing="0" cellpadding="0" border="0" style="width:900px;" class="tb1" align="left">
		<tr>
			<th >
				期間選擇
			</th>
			<td >
				
				<!-- <input type="radio" name="time"  value="y" checked>年 &nbsp; -->
				<input type="hidden" name="time"  value="m"><!-- 月 &nbsp; -->
				<!-- <input type="radio" name="time"  value="s">季 &nbsp; -->
				
				
				(起)
				年度<{html_options name="s_year" options=$year_option}> 年
				
				<span id="show_ss">
					<!-- <select name="s_season" style="width:80px;">
						<option value="">請選擇</option>
						<option value="S1">第一季</option>
						<option value="S2">第二季</option>
						<option value="S3" >第三季</option>
						<option value="S4">第四季</option>
						
					</select>季 -->
					
				</span>
				<span id="show_ms">
					
					<select name="s_month" style="width:80px;">
						<option value="">請選擇</option>
						<option value="01">1月份</option>
						<option value="02">2月份</option>
						<option value="03">3月份</option>
						<option value="04">4月份</option>
						<option value="05">5月份</option>
						<option value="06">6月份</option>
						<option value="07">7月份</option>
						<option value="08">8月份</option>
						<option value="09">9月份</option>
						<option value="10">10月份</option>
						<option value="11">11月份</option>
						<option value="12">12月份</option>
					</select>
					月
				</span>
				～
				(迄)
				 年度<{html_options name="e_year" options=$year_option}>
				<span id="show_se">
					<!-- <select name="e_season" style="width:80px;">
						<option value="">請選擇</option>
						<option value="S1">第一季</option>
						<option value="S2">第二季</option>
						<option value="S3" >第三季</option>
						<option value="S4">第四季</option>
						
					</select>季 -->
					
				</span>
				
				
				<span id="show_me">
					
					<select name="e_month" style="width:80px;">
						<option value="">請選擇</option>
						<option value="01">1月份</option>
						<option value="02">2月份</option>
						<option value="03">3月份</option>
						<option value="04">4月份</option>
						<option value="05">5月份</option>
						<option value="06">6月份</option>
						<option value="07">7月份</option>
						<option value="08">8月份</option>
						<option value="09">9月份</option>
						<option value="10">10月份</option>
						<option value="11">11月份</option>
						<option value="12">12月份</option>
					</select>月
				</span>
			</td>
		</tr>
		
	
		
		
	</table>

	
<br><br>
<div style="padding:20px;text-align:center;">

<input type="button" value="匯出 excel 檔" name="excel" class="bt4" style="width:100px;height:35px;display:;">
</div>
</form>
</div>

				</div>
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