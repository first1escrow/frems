<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" href="colorbox.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
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
	
	$(".ajax").colorbox({width:"400",height:"100"});
});

function save() {
	var url = 'certified_result.php' ;

	var sd = $('[name="start_date"]').val() ;
	var ed = $('[name="end_date"]').val() ;
	var by = $('[name="buyer"]').val() ;
	var ow = $('[name="owner"]').val() ;
	var sc = $('[name="scrivener"]').val() ;
	var bh = $('[name="branch"]').val() ;
	var ct = $('[name="category"]').val() ;
	var cd = $('[name="certifiedid"]').val() ;
	var cs = $('[name="case_status"]').val() ;
	
	//alert('日期 起='+sd) ; alert('保證號碼='+cd) ; alert('日期 迄='+ed) ; 
	//alert('買方='+by) ; alert('賣方='+ow) ; alert('地政士='+sc) ; 
	//alert('類別='+ct) ; alert('仲介店='+bh) ; 
	//alert('狀態='+st) ; alert('狀態日='+ed) ; alert('區域='+zp) ; 
	$( "#dialog" ).dialog("open") ;
	
	$.post(url,
		{'start_date':sd,'end_date':ed,'buyer':by,'owner':ow,'case_status':cs,
		'scrivener':sc,'branch':bh,'category':ct,'certifiedid':cd},
		function(txt) {
			$('#container').html(txt) ;
			$( "#dialog" ).dialog("close") ;
	}) ;
	
	//alert('儲存') ;
	//alert(url) ;
}
function cancel() {
	location.reload() ;
}
</script>
<style>
.small_font {
	font-size: 9pt;
	line-height:1;
}
input.bt4 {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background: #F8EDEB;border:1px #727272 dotted;color:font-size:12px;margin-left:2px
}
input.bt4:hover {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background:  #EBD1C8;border:1px #727272 dotted;font-size:12px;margin-left:2px;cursor:pointer
}
.btn {
	padding:10px 20px 10px 20px ;
	color:#212121 ;
	background-color:#F8ECE9 ;
	margin:2px ;
	border:1px outset #F8ECE0 ;
	cursor:pointer ;
}
.btn:hover {
	padding:10px 20px 10px 20px ;
	color:#212121 ;
	background-color:#EBD1C8 ;
	margin:2px;
	border:1px outset #F8ECE0;
	cursor:pointer;
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
<form name="mycal">
<table cellspacing="0" cellpadding="0" style="width:900px;padding:20px;">
<tr>
	<td style="width:300px;background-color:#E4BEB1;padding:4px;">
	出款日期
	<input type="text" name="start_date"class="calender datepickerROC" style="width:160px;" readonly>
	(起)
	</td>
	<td style="width:300px;background-color:#E4BEB1;padding:4px;">
	出款日期
	<input type="text" name="end_date" class="calender datepickerROC" style="width:160px;" readonly>
	(迄)
	</td>
	<td style="width:300px;background-color:#E4BEB1;padding:4px;">
	案件狀態　
	<select name="case_status" style="width:164px;">
	<option></option>
	<{$status}>
	</select>
	</td>
</tr>
<tr>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	買方姓名
	<input type="text" name="buyer" style="width:160px;font-size:8pt;height:20px;" readonly>
	<a href="get_buyer.php" class="small_font ajax">選擇</a>
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	賣方姓名
	<input type="text" name="owner" style="width:160px;font-size:8pt;height:20px;" readonly>
	<a href="get_owner.php" class="small_font ajax">選擇</a>
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	地政士姓名
	<input type="text" name="scrivener" style="width:160px;font-size:8pt;height:20px;" readonly>
	<a href="get_scrivener.php" class="small_font ajax">選擇</a>
	</td>
</tr>
<tr>
	<td style="width:300px;background-color:#E4BEB1;padding:4px;">
	仲介店名
	<input type="text" name="branch" style="width:160px;font-size:8pt;height:20px;" value="" readonly>
	<a href="get_branch.php" class="small_font ajax">選擇</a>
	</td>
	<td style="width:300px;background-color:#E4BEB1;padding:4px;">
	仲介類型
	<select name="category" size="1" style="width:160px;">
	<option value="">全部</option>
	<{$category}>
	</select>
	</td>
	<td style="width:300px;background-color:#E4BEB1;padding:4px;">
	保證號碼　
	<input type="text" name="certifiedid" style="width:160px;" maxlength="9">
	</td>
</tr>

</table>
<div style="padding:20px;text-align:center;">

<center>
<span onclick="save()" class="btn">確定</span>
<span onclick="cancel()" class="btn" style="display:none;">取消</span>
</center>

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