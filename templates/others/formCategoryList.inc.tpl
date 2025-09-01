<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<!-- <script src="../js/1.7.1/jquery.min.js"></script> -->
<{include file='meta.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {
	getMarguee(<{$smarty.session.member_id}>) ;
	setInterval(function() { getMarguee2(<{$smarty.session.member_id}>); }, 180000)
	
	 $('[name="btn"]').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

	 $("#loc input").button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });
});

function chk_status(cid)
{

	$("[name='CertifiedId']").val(cid);


	$("[name='form1']").attr('action', 'SignCategory.php');
	
	$("[name='form1']").submit();
}

function contract(cid) {
	$('[name="id"]').val(cid) ;
	//alert($("#id").val()) ;
	$('[name="form_edit"]').submit() ;
}

function search()
{
	// alert('----');
	$("[name='search_form']").attr('action', 'formCategoryList.php');

	$("[name='search_form']").submit();
}

function changePage()
{
	var page= $("[name='page']").val();

	$("[name='form1']").attr('action', 'formCategoryList.php');

	$("[name='form1']").submit();
}
function del(cid)
{
	$.ajax({
		url: 'DeleteCategory.php',
		type: 'POST',
		dataType: 'html',
		data: {'cid': cid},
	})
	.done(function(txt) {
		// alert(txt);
		location.href='formCategoryList.php';
	})

}

function contract_del(cid)
{
	if (confirm("確認是否要刪除"+cid+"?")) {
		$.ajax({
			url: 'DeleteContract.php',
			type: 'POST',
			dataType: 'html',
			data: {'cid': cid},
		})
		.done(function(txt) {
			// alert(txt);
			location.href='formCategoryList.php';
			// $("#loc").html(txt);
		})
	}


	
}
</script>
<style>

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
                                    <td colspan="3" align="right">
										<div id="abgne_marquee" style="display:none;">
											<ul>
											</ul>
										</div>
									</td>
                                </tr>
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

										<div id="container">
										<form name="form_edit" method="POST" action="/escrow/formbuyowneredit.php" target="_blank">
												<input type="hidden" name="id" value='' />
										</form>
										<{if $smarty.session.member_tEcontract !=0}>
										<form name="search_form" method="POST">
											<div id="show"></div>
											<table cellspacing="0" cellpadding="0" style="margin-left:-50px;width:990px;">
												
												<tr>
													<th style="background-color:#E4BeB1;text-align:center;height:40px;">保證號碼</th>
													<td style="background-color:#F8ECE9;text-align:left;height:40px;">
														<input type="text" name="cid">&nbsp;&nbsp;
														<input type="button" value="查詢" onclick="search()" name="btn">
													</td>
												</tr>
												<tr><td colspan="2">&nbsp;</td></tr>
											</table>
										</form>
										<form name="form1" method="POST">
											<table cellspacing="0" cellpadding="0" style="margin-left:-50px;width:990px;" id="loc">
												<tr style="background-color:#E4BeB1;text-align:center;height:40px;">
													<th width="15%">保證號碼</th>
													<th width="15%">地政士</th>
													<th width="30%">申請日期</th>
													<th width="40%">&nbsp;</th>
													<!-- <th width="40%">合約書位置</th> -->
													
												</tr>
												
												<{foreach from=$data key=key item=item}>
												<tr style="text-align:center;background-color:<{$item.color}>;height:40px;">
													<td>
														<{if $item.check == 1}>
														<a href="#" onclick="contract('<{$item.CertifiedId}>')"><{$item.CertifiedId}></a>
														<input type="hidden" name="CertifiedId" value="">
														<{else}>
															<{$item.CertifiedId}>
														<{/if}>
													</td>
													<td><{$item.ScrivenerName}></td>
													<td><{$item.CreatDate}></td>
													<td>
														
														<!-- <input type="button" onclick="contract_del('<{$item.CertifiedId}>')" value="刪除"> -->
													</td>
													<!--<td>
													

													 	 <button name="sign_category<{$item.CertifiedId}>" onClick="chk_status('<{$item.CertifiedId}>')" <{$item.disabled}>>ECS</button> -->

													 <!-- <a href="#" onclick="del('<{$item.CertifiedId}>')">清空</a> 
													
													</td>-->
												</tr>
												<{/foreach}>
												<tr>
													<td colspan="4">&nbsp;</td>
												</tr>
												<tr>
													<td colspan="4" align="center">
														 第 <{html_options name="page" options=$page_menu selected=$page onchange="changePage(<{$limit_s}>)" }> 頁 ／共<{$total_page}>頁    顯示第 <{$limit_s}> 筆到第 <{$limit_e}> 筆的紀錄，共 <{$total}> 筆紀錄
													</td>
												</tr>

											</table>
										</form>
										<{/if}>
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