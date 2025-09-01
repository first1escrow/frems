<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<!-- <script src="../js/1.7.1/jquery.min.js"></script> -->
<{include file='meta.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {

	
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



function contract(cid) {
	$('[name="id"]').val(cid) ;
	//alert($("#id").val()) ;
	$('[name="form_edit"]').submit() ;
}

function search()
{
	// alert('----');
	$("[name='search_form']").attr('action', 'invoicePower.php');

	$("[name='search_form']").submit();
}

function contract_invstatus(cid,st)
{
	// $("[name='cid']").val(cid);
	$.ajax({
		url: 'invoicePowerStaus.php',
		type: 'post',
		dataType: 'html',
		data: {'cid': cid,'status':st},
	})
	.done(function(txt) {
		
		if (txt == 'ok') {
			// $("form1[name='cid']").val(cid);
			$("[name='form1']").attr('action', 'invoicePower.php');

			$("[name='form1']").submit();


		}
	});
	

	

	// alert($("[name='cid']").val());
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
										<form name="sfrom" method="POST"></form>

										
										<form name="search_form" method="POST">
											
											<div id="show"></div>
											<table cellspacing="0" cellpadding="0" style="margin-left:-50px;width:990px;">
												
												<tr>
													<th style="background-color:#E4BeB1;text-align:center;height:40px;">保證號碼</th>
													<td style="background-color:#F8ECE9;text-align:left;height:40px;">
														<input type="text" name="cid" value="">&nbsp;&nbsp;
														<input type="button" value="查詢" onclick="search()" name="btn">※只能查詢發票修改已關閉過之保證號碼 
													</td>
												</tr>
												<tr><td colspan="2">&nbsp;</td></tr>
											</table>
										</form>
										<form name="form1" method="POST">
											<input type="hidden" name="cid" value="<{$cid}>">
											<table cellspacing="0" cellpadding="0" style="margin-left:-50px;width:990px;" id="loc">
												<tr style="background-color:#E4BeB1;text-align:center;height:40px;">
													<th width="15%">保證號碼</th>
													
													<th width="30%">目前狀態</th>
													<th width="40%">功能</th>
													<!-- <th width="40%">合約書位置</th> -->
													
												</tr>
												
												<{foreach from=$data key=key item=item}>
												<tr style="text-align:center;background-color:<{$item.color}>;height:40px;">
													<td><a href="#" onclick="contract('<{$item.CertifiedId}>')"><{$item.CertifiedId}></a><input type="hidden" name="CertifiedId" value=""></td>
													<td><{$item.cInvoiceText}></td>
													
													<td>
														<{if $item.cInvoiceClose =='Y'}>
														<input type="button" onclick="contract_invstatus('<{$item.CertifiedId}>','S')" value="開啟" >
														<{else}>
															<input type="button" onclick="contract_invstatus('<{$item.CertifiedId}>','Y')" value="關閉" >
														<{/if}>
													</td>
													
												</tr>
												<{/foreach}>
												<tr>
													<td colspan="4">&nbsp;</td>
												</tr>
												

											</table>
										</form>
										
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