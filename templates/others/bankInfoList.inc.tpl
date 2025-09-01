<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>


<script src="../js/jquery-1.7.2.min.js"></script>
<{include file='meta.inc.tpl'}> 
<script src="../js/jquery.colorbox.js"></script>		
<script type="text/javascript">
$(document).ready(function() {
	
                
    // $("[name='edit']").click(function() {
    // 	 Edit('add','');
    // });
	
	$('#searchBtn').button({
        icons:{
            primary: "ui-icon-document"
        }
    });
});

function Edit(type,id){

	if (type != 'del') {

		var url = "bankInfoEdit.php?cat="+type+"&id="+id;
	    $.colorbox({iframe:true, width:"40%", height:"90%", href:url,onClosed:function(){
		    location.href="bankInfoList.php";

		}}) ;
	}else{
		
		if (confirm("確認要刪除嗎?")) {
			$.ajax({
				url: 'bankInfoDel.php',
				type: 'POST',
				dataType: 'html',
				data: {"id": id},
			})
			.done(function() {
				location.href="bankInfoList.php";
			});
		}
		
		
	}

	
}

</script>
<style>
	.search1{		
	padding-left: 20px;
	}
	.search2{
		
		text-align: right;
		
		float: right;
		display:inline;
	}
	.btn {
	    color: #000;
	    font-family: Verdana;
	    font-size: 12px;
	    font-weight: bold;
	    line-height: 14px;
	    background-color: #FFFFFF;
	    text-align:center;
	    display:inline-block;
	    padding: 8px 12px;
	    border: 1px solid #DDDDDD;
	   
	}
	.btn:hover {
	    color: #00008F;
	    font-size:12px;
	    background-color: #FFFF96;
	    border: 1px solid #FFFF96;
	}
	.btn.focus_end{
	    color: #000;
	    font-family: Verdana;
	    font-size: 12px;
	    font-weight: bold;
	    line-height: 14px;
	    background-color: #FFFF96;
	    text-align:center;
	    display:inline-block;
	    padding: 8px 12px;
	    border: 1px solid #FFFF96;
	  
	}
	.tb1{
		width: 100%;
		border: 1px solid #DDDDDD;
	}
	.tb1 th{
		 line-height: 16px;
		 font-size: 14px;
		 background-color: #E4BEB1;
		 padding: 8px 12px;
		 border: 1px #DDDDDD solid;
	}
	.tb1 td{
		line-height: 16px;
		 font-size: 14px;
		 background-color: #FFFFFF;
		 padding: 8px 12px;
		border: 1px solid #DDDDDD;
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

									<h1>銀行資訊</h1>
									<br>

									<div style="width:100%">
										<div class="search1">
											<{if $smarty.session.pBankInfo == 1}>
											<input type="button" value="新增" class="btn" name="edit" onclick="Edit('add','')">
											<{/if}>
										</div>
										<div class="search2">
											
											<form name="search" method="POST"> 
												
												關鍵字搜尋：<input type="text" name="searchTxt" id="" placeholder="請輸入關鍵字">
												<input type="submit" value="搜尋" id="searchBtn">
											</form>
										</div>
									


										<table cellspacing="0" cellpadding="0" border="0"  class="tb1" align="left">
											<tr>
												<th width="20%">銀行名稱</th>
												<th width="20%">常用照會電話</th>
												<th width="42%">備註</th>
												
												<{if $smarty.session.pBankInfo == 1}>
													
													<th width="10%">最後修改者</th>
													<th width="8%">&nbsp;</th>
												<{else}>

													<th width="18%">最後修改者</th>
												<{/if}>

											</tr>	
											<{foreach from=$list key=key item=item}>
												<tr>
													<td>
														<{if $item.bUrl != ''}>
															<a href="<{$item.bUrl}>" target="_blank"><{$item.bankName}></a>
														<{else}>
															<{$item.bankName}>
														<{/if}>
													</td>
													<td>
														<{if $item.bPhoneArea != ''}>
															<{$item.bPhoneArea}>
														<{/if}>
														<{if $item.bPhone !=''}>
															-<{$item.bPhone}>
														<{/if}>
														<{if $item.bPhoneExt != ''}>
															#<{$item.bPhoneExt}>
														<{/if}>
													</td>
													
													<td><{$item.bNote}></td>
													<td><{$item.pName}></td>
													<{if $smarty.session.pBankInfo == 1}>
													<td>
														<div style="padding-bottom:10px;"><a href="#" onclick="Edit('mod',<{$item.bId}>)">修改</a></div>
													<hr><div style="padding-top:10px;"><a href="#" onclick="Edit('del',<{$item.bId}>)">刪除</a></div>
													</td>
													<{else}>

													<{/if}>
												</tr>
											<{/foreach}>	
											
												
												
										</table>
									</div>
		
									<br><br>
									<div style="padding:20px;text-align:center;">
										&nbsp;
									</div>

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