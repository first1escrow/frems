<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta2.inc.tpl'}>
<script type="text/javascript">
	$(document).ready(function() {
	});
	function save(){
		$("[name='form']").submit();
	}
	function cancel(){
		location.href = "legalCaseList.php";
	}
	function importEvent(){
		var url = 'importCaseEvent.php' ;
        $.colorbox({iframe:true, width:"70%", height:"90%", href:url}) ;
	}
	function add(){
		var clone = $(".row_new:first").clone();
		var no = $(".row_new").length;
		// console.log(no);
		var day = $("#newday").val();
		clone.find('[name="newday[]"]').val(day);

		clone.find('[name="newday[]"]').attr({
			id: 'newday'+no,
			onchange: "setEndDate('new','"+no+"')"
		});
		clone.find('[name="newnote[]"]').attr('id', 'newnote'+no);
		clone.find('[name="newendDay[]"]').attr('id','newendDay'+no);
		clone.find('#newendDayShow').attr('id', 'newendDayShow'+no);
		clone.insertAfter('.row:last');

		$(".row_new:last").attr({
			class: 'row',
			id: ''
		});
		$('.row:last #btn_import').remove();

		$("#newday").val(0);
		$("#newnote").val('');
		$("#newendDayShow").text('');
		$("#newendDay").val('')

		
	}

	function setEndDate(cat,id){

		
		// var day = parseInt($("[name='"+cat+"day"+id+"']").val());

		var day = parseInt($("#"+cat+"day"+id).val());
		var today=new Date();

		if (day > 0) {
			today.setDate(today.getDate() + day);
			$("#"+cat+"endDay"+id+"").val(today.getFullYear()+"-"+(today.getMonth()+1)+'-'+today.getDate())
			$("#"+cat+"endDayShow"+id).text((today.getFullYear()-1911)+"-"+(today.getMonth()+1)+'-'+today.getDate());
		}

		
		
	}

	function del(id){
		$.ajax({
			url: 'setCaseDetailStatus.php',
			type: 'POST',
			dataType: 'html',
			data: {id: id,cat:"del"},
		}).done(function(msg) {
			// console.log(msg);
			location.href = 'legalCaseEdit.php?id=<{$id}>';
		});
		
	}

</script>
<style>
ul.tabs {
    width: 100%;
    height: auto;
    border-left: 0px solid #999;
    border-bottom: 1px solid #D99888;
           
}  
ul.tabs li {
    margin: 0;
    padding: 0;
    border: 0;
    font-size: 100%;
    font: inherit;
    vertical-align: baseline;
    height: auto;
}

#subTabs-1,#subTabs-2{
            background-color: #FFF;
        }
.memberTB {
	border: 1px solid #ccc;
	padding: 5px;
	font-size: 10pt;
	font-weight: bold;
	text-align: center;
	height: auto;
	width: 100%;
	float: center;
	
}
.memberCell {
	padding: 5px;
	font-size: 9pt;
	text-align: center;
	border: 1px solid #ccc;
}

#table tbody td{
	padding: 5px;
	font-size: 9pt;
	text-align: center;
	border: 1px solid #ccc;
}
.row{
	border: 1px solid #999;
	width: 100%;
	float:center;
}
.row_title{
	background-color: #EEE0E5 ;
	padding: 5px;
	font-size: 9pt;
	text-align: center;
	border: 1px solid #CCC;
	width: 20%;
	
}
.row_content{
	font-size: 9pt;
	text-align: left;
	border: 1px solid #CCC;
	width: 80%;
}
.xxx-select {
	color:#666666;
	font-size:16px;
	font-weight:normal;
	background-color:#FFFFFF;
	text-align:left;
	height:34px;
	padding:0 0px 0 5px;
	border:1px solid #CCCCCC;
	border-radius: 0em;
	width: 100%
}
.endDayShow{
	background-color: #FF8C8C;
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
						<h3></h3>
						<div id="container">
							<h1>保證號碼<{$id}>編輯</h1>
							<br>

							<form name="form" method="POST">
								<div id="tt" style="">
									<div style="padding:20px;">
										
										<input type="hidden" name="id" value="<{$id}>">
										<center>
										<h1>新增事項</h1>
										<table cellspacing="0" width="80%" class="memberTB">
											<tr>
												<th class="row_title" width="30%">天數</th>
												<th class="row_title" width="60%">事項</th>
												<th class="row_title" width="10%">&nbsp;</th>
											</tr>
											<tr class="row_new">
												<td class="row_content">
													<{html_options name="newday[]" id="newday" options=$menu_day class="xxx-select" onchange="setEndDate('new','')"}>
													到期日
													<span id="newendDayShow" class="endDayShow"></span>
													<input type="hidden" name="newendDay[]" id="newendDay">
												</td>
												<td class="row_content">
													<!-- <select name="" id=""></select> -->
													<textarea name="newnote[]" id="newnote" cols="60" rows="3"></textarea>
												</td>
												<td class="row_content">
													<input type="button" id="btn_import" value="帶入預設事項" onclick="importEvent()" >
												</td>
											</tr>
										</table>
										<div style="margin-top:5px;margin-bottom: 10px;">
											<input type="button" value="新增至下方" onclick="add()">
											※新增至下方後請記得按下「儲存」
										</div>
										<hr>
										<h1>已建立事項</h1>
										<table cellspacing="0" width="80%" class="memberTB">
											<tr class="row">
												<th class="row_title" width="30%">天數</th>
												<th class="row_title" width="60%">事項</th>
												<th class="row_title" width="10%">&nbsp;</th>
											</tr>
										
											<{foreach from=$caseDetail key=key item=item}>
											<tr style="background-color:#FFF0F5;" class="row" id="row_<{$item.lId}>">
												
												<td class="memberCell" style="text-align:left;" width="30%">
													<input type="hidden" name="lId[]" value="<{$item.lId}>">
													<{html_options name="day[]" id="day<{$item.lId}>" options=$menu_day class="xxx-select" onchange="setEndDate('',<{$item.lId}>)" selected="<{$item.lDay}>"}>
													到期日
													<span id="endDayShow<{$item.lId}>" class="endDayShow"><{$item.lEndDay}></span>
													 <input type="hidden" name="endDay[]" id="endDay<{$item.lId}>" value="<{$item.lEndDay}>">
												</td>
												<td class="memberCell" style="text-align:left;" width="60%">
													<textarea name="note[]" id="note<{$item.lId}>" cols="60" rows="3"><{$item.lNote}></textarea>
												</td>
												<td width="10%">
													<input type="button" value="刪除" onclick="del(<{$item.lId}>)">
													&nbsp;
												</td>
												
											</tr>

											
											<{/foreach}>
											
										</table>
										</center>
										</div>
									</div>
									
								</div>
								
								<div>&nbsp;</div>

								<div style="text-align: center;width: 100%">
									<input type="button" style="width:100px;" value="儲存" onclick="save()">
									<input type="button" style="width:100px;" value="返回" onclick="cancel()">
								</div>
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