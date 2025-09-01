<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {	

	// $('[name="add"]').on('click', function() {
	// 	var url = 'BannerEdit.php?cat=add';
	// 	$.colorbox({iframe:true, width:"800px", height:"80%",href:url,onClosed:function (){
	// 		location.href='banner.php';
	// 	}}) ;
	// });

	$('[name="addBank"]').on('click', function() {
		var url = 'BannerBank.php';
		$.colorbox({iframe:true, width:"800px", height:"80%",href:url,onClosed:function (){
			location.href='banner.php';
		}}) ;
	});

	//addBank BannerBank

	// $(".iframe").colorbox({iframe:true, width:"800px", height:"80%",onClosed:function (){
	// 	location.href='banner.php';
	// }}) ;
	
	/* 設定 UI dialog 屬性 */
	$('#msg').dialog({
		modal: true,
		buttons: {
			OK: function() {
				$(this).dialog("close") ;
			}
		}
	}) ;
}) ;
function add(){
	location.href = "contractEdit.php?cat=add";
}
function mod(id){
	var url = 'BannerEdit.php?cat=mod&id='+id;
	$.colorbox({iframe:true, width:"800px", height:"80%",href:url,onClosed:function(){
		location.href='banner.php';
	}}) ;
}
function del(id){
	if (confirm("確定要刪除嗎?")) {
		$.ajax({
			url: 'status.php',
			type: 'POST',
			dataType: 'html',
			data: {cat: 'del',id:id},
		})
		.done(function(txt) {
			
			if (txt) {
				alert('刪除成功');
				location.href='banner.php';
			};
					
		});
				
	}
			
}
function statusOK(id,cat){
	if (cat==1) {
		cat = 'ok';
	}else{
		cat = 'ok2';
	}
	if (confirm("確定要上架嗎?")) {
		$.ajax({
			url: 'status.php',
			type: 'POST',
			dataType: 'html',
			data: {cat: cat,id:id},
		}).done(function(txt) {
			if (txt) {
				alert('上架成功');
				location.href='banner.php';
			}
							
		});
						
	}
			
}

function statusNO(id,cat){
	if (cat==1) {
		cat = 'no';
	}else{
		cat = 'no2';
	}

	if (confirm("確定要下架嗎?")) {
		$.ajax({
			url: 'status.php',
			type: 'POST',
			dataType: 'html',
			data: {cat: cat,id:id},
		}).done(function(txt) {
			if (txt) {
				alert('下架成功');
				location.href='banner.php';
			}
							
		});
						
	}
}

function statusUp(id,s){

	var sort = parseInt(s);
	if (sort > 0) {
		sort = sort-1 ;
	}

	$.ajax({
		url: 'status.php',
		type: 'POST',
		dataType: 'html',
		data: {cat: 'up',id:id,sort:sort},
	}).done(function(txt) {
		location.href='banner.php';
		// console.log(txt);
							
	});
						
	
}
function statusDown(id,s){

	var sort = parseInt(s);
	sort = sort+1 ;
	
	$.ajax({
		url: 'status.php',
		type: 'POST',
		dataType: 'html',
		data: {cat: 'down',id:id,sort:sort},
	}).done(function(txt) {
		// console.log(txt);
		location.href='banner.php';	
							
	});
						
	
}
function edit(id){
	location.href = "contractEdit.php?cat=edit&id="+id;
	// $('[name="form"]').attr('action', 'contractEdit.php');
	// $('[name="id"]').val(id);
	// $('[name="cat"]').val('edit');
	// $('[name="form"]').submit();
}
</script>
<style>

.bank-all-1 {
	text-align:center;
	margin-bottom:50px;
}
.bank-all-1 ul {
	display:inline-block;
	width: 100%;
	padding:0;
	margin: 0;
}
.bank-all-1 li {	
	float: left;
	list-style-type: none;
	width:30.33%;
	padding:15px 10px;
	margin: 0 1.5% 0 1.5%;	
}

.tb th{
	background-color:#E4BEB1;
    padding:4px;
}

.tb td{
	text-align: center;
    border: solid 1px #ccc;
}

.btn {
	color: #000;
	font-family: Verdana;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
	background-color: #FFF;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #DDDDDD;
	/*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
.btn:hover {
	color: #000;
	font-size:14px;
	background-color: #CCC;
	border: 1px solid #CCCCCC;
}
.btn.focus_end{
	color: #000;
	font-family: Verdana;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
	background-color: #FFF;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #FFFF96;
	  /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}

.btn1 {
	color: #008F00;
	font-family: Verdana;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
	background-color: #FFBFBF;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #DDDDDD;
	/*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
.btn1:hover {
	color: #FFBFBF;
	font-size:14px;
	background-color: #008F00;
	border: 1px solid #CCCCCC;
}
.btn1.focus_end{
	color: #008F00;
	font-family: Verdana;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
	background-color: #FFBFBF;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #FFFF96;
	  /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}

.btn2 {
	color: #000;
	font-family: Verdana;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
	background-color: #CCC;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #DDDDDD;
	/*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
.btn2:hover {
	color: #CCC;
	font-size:14px;
	background-color: #000;
	border: 1px solid #CCCCCC;
}
.btn2.focus_end{
	color: #000;
	font-family: Verdana;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
	background-color: #CCC;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #FFFF96;
	  /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
</style>
</head>
<body id="dt_example">
<div id="wrapper">
<div id="header">
		<table width="1000" border="0" cellpadding="2" cellspacing="2">
			<tr>
				<td width="233" height="72">&nbsp;</td>
				<td width="753">
					<table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
						<tr>
							<td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
						</tr>
						<tr>
							<td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
							<td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
						</tr>
					</table>
				</td>
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
						<h1>電子合約書管理</h1>
						
							<div style="padding-bottom:10px">
								<input type="button" value="新增" name="add" class="btn" onclick="add()">
								
							</div>
							<center>
							
							<div style="border:1px solid #CCC;">
								<table cellspacing="0" cellpadding="0" width="100%" class="tb">
									<tr>
										<th width="40%">名稱</th>
										<th width="10%">類別</th>
										<th width="10%">是否上架</th>
										<th width="25%">修改時間</th>
										<th width="25%">功能</th>
									
									</tr>
									<{foreach from=$list key=key item=item}>
										
											
										<tr>
											<td><{$item.eName}></td>
											
											<td><{$item.eApplication}></td>
											<td><{$item.eSendIden}></td>
											<td><{$item.eModifyTime}></td>
											<td>
												<input type="button" value="編輯" onclick="edit(<{$item.eId}>)" class="btn">
												<input type="button" value="刪除" onclick="del(<{$item.eId}>)" class="btn">
												
											</td>
											
										</tr>
									<{/foreach}>
									
								</table>
								
							</div>
							
							</center>
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