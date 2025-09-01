<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<html>
<head>
<{include file='meta.inc.tpl'}> 		
<script type="text/javascript">
$(document).ready(function() {
	$( "#subTabs" ).tabs();
	searchprocess();
	getMarguee(<{$smarty.session.member_id}>) ;
	setInterval(function() { getMarguee2(<{$smarty.session.member_id}>); }, 180000)
	
	$("#show").colorbox({iframe:true, width:"1200px", height:"90%"}) ;

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
	
	$('[name="cid"]').focus() ;

	$('[name="cid"]').keyup(function() {
		conv_ajax() ;
	}) ;

	
	$("#more").hide();
	$("#add").hide();
	$("#status2").hide();
	$("#bankN2").hide();
	$("#scr").hide();
	$("#msg").hide();
	$("[name='app2']").hide();
	$('#reDel').hide() ;
	

	$("[name='cat']").click(function() {
		// console.log($(this).val());
		if ($(this).val() == 'one') {
			$("#one").show();
			$("#more").hide();

		}else{
			$("#more").show();
			$("#one").hide();
		}
	});
	
	$('[name="scr_option_replace"]').combobox() ;
	$('[name="scr_option_replace2"]').combobox();
	
	$('[name="scr_total"]').combobox() ;
	$('[name="bank_total"]').combobox() ;
	$('[name="ver_total"]').combobox() ;
	$('[name="searchShipScrivener"]').combobox();

	$('#status').prop('type','hidden') ;	
	$(".showact").hide();
	<{if $s == '1'}>
	alert('保證號碼已更新成功!!') ;
	<{/if}>
}) ;

function show_msg(cat,app,type){
	var scr = $("[name='s']").val();
	var bank = $("[name='b']").val();
	var ver = $("[name='v']").val();

	var url = 'id2scrivener_list.php?scr='+scr+'&bank='+bank+'&ver='+ver+'&cat='+cat+'&app='+app+'&type='+type+'' ;
    $.colorbox({
		iframe:true,
		width:"900px",
		height:"500px",
		href:url,
		onClosed: function() {
			search() ;
		}
	}) ;
}

function conv_ajax() {
		var url = 'id_conv_scr.php' ;
		var id = $('[name="cid"]').val() ;
		var check='<{$smarty.session.member_bankcheck}>';

		
		$.post(url,{'cid':id},function(txt) {
			// console.log(txt);
			var obj = jQuery.parseJSON(txt) ;
			// console.log(obj);
			$('#cnvt').prop('disabled',false) ;
			$('#status').prop('type','hidden') ;
			$(".showact").hide();
			$('#reDel').hide() ;

			$('#scr_name').html('') ;
			$('#cid_status').html('') ;
			$('#scr_cid_status').html('') ;
			$('#bankN').html('');

			if (obj.status == 'ng') {
				$('#scr_name').html(obj.scrivener) ;
				$('#cid_status').html(obj.statusMsg) ;
				$('#cnvt').prop('disabled',true) ;
				$('#status').prop('type','hidden') ;	
			}else{
				$('#scr_name').html('<span style="color:#000080;font-weight:bold;">'+obj.scrivener+'</span>') ;
				$('#cid_status').html(obj.statusMsg) ;
				$('#bankN').html('<span style="color:#FF0000;font-weight:bold;">'+obj.bank+'</span>') ;
				$(".showact").show();
				$("[name='app']").val(obj.app);
				$("[name='Brand']").val(obj.brand);
				$("[name='Category']").val(obj.category);

				if (obj.status == 'del' || obj.status == 'used') {
					if(check==1){
						$('#status').prop('type','button') ;
					}else{
						
						$('#status').prop('type','hidden') ;	
					}
					// console.log(obj.status);
					// console.log(check);
					// console.log(<{$smarty.session.member_id}>);
					// console.log(obj.statusMsg);

					if (obj.status == 'del'){
						$('#cnvt').prop('disabled',true) ;
						if(check==1){
							// $('#reDel').show() ;
							<{if $smarty.session.member_id == 6 || $smarty.session.member_id == 1 || $smarty.session.member_id == 12}>
							$('#cid_status').html(obj.statusMsg+"<input type=\"button\" value=\"取消刪除\" onclick=\"return_Del()\" id=\"reDel\">") ;
							<{/if}>
						}
						

						$('#status').prop('type','hidden') ;	

					}
				}else if(obj.status == 'ok'){
					if(check==1){
							
						$('#status').prop('type','hidden') ;
					}else{
							
						$('#status').prop('type','hidden') ;
					}
				}
			}


			// if (txt.match('ng')) {
				// $('#scr_name').html('') ;
				// $('#cid_status').html('') ;
				// $('#scr_cid_status').html('') ;
				// $('#bankN').html('');
				
				// var arr = txt.split('_') ;
				// $('#scr_name').html(arr[1]) ;

				// $('#cnvt').prop('disabled',true) ;
				// $('#status').prop('type','hidden') ;				
			// }
			// else {
			// 	$('#scr_name').html('') ;
			// 	$('#cid_status').html('') ;
			// 	$('#scr_cid_status').html('') ;
				
			// 	var arr = txt.split('_') ;
			// 	$('#scr_name').html('<span style="color:#000080;font-weight:bold;">'+arr[2]+'</span>') ;
			// 	$('#cid_status').html(arr[3]) ;
			// 	$('#bankN').html('<span style="color:#FF0000;font-weight:bold;">'+arr[4]+'</span>') ;
			// 	//
			// 	$(".showact").show();
			// 	$("[name='app']").val(arr[5]);
				
			// 	var str = arr[3] ;
			// 	if (str.match('刪除')||str.match('已使用')) {

					
					
			// 		if(check==1){
			// 			$('#status').prop('type','button') ;
			// 		}else{
						
			// 			$('#status').prop('type','hidden') ;	
			// 		}

			// 		if (str.match('刪除')){
			// 			$('#cnvt').prop('disabled',true) ;
			// 			if(check==1){
			// 				// $('#reDel').show() ;
			// 				<{if $smarty.session.member_id == 6 || $smarty.session.member_id ==1}>
			// 				$('#cid_status').html(arr[3]+"<input type=\"button\" value=\"取消刪除\" onclick=\"return_Del()\" id=\"reDel\">") ;
			// 				<{/if}>
			// 			}
						

			// 			$('#status').prop('type','hidden') ;	

			// 		}
					
					
			// 	}else if (str.match('未使用')){
			// 		if(check==1)
			// 		{
						
			// 			$('#status').prop('type','hidden') ;
			// 		}else
			// 		{
						
			// 			$('#status').prop('type','hidden') ;
			// 		}
					
			
			// 	};

			// }
			
		}) ;
		
}
function conv_ajax2(){
	

	var url = '' ;
	var id = $('[name="cid2"]').val() ;
	var check = '<{$smarty.session.member_bankcheck}>';
	
	if (id == '') {
		$("#bankN2").text('');
		$("#scr").text('');
		$("#msg").text('');

		$("[name='app2']").val('');
		return false;
	}
	$("#bankN2").show();
	$("#scr").show();
	$("#msg").show();
	$("[name='app2']").show();

	$.ajax({
		url: 'id_conv_scr.php',
		type: 'POST',
		dataType: 'html',
		data: {"cid": id},
	})
	.done(function(txt) {
		var obj = jQuery.parseJSON(txt);

		$("#bankN2").text(obj.bank);
		$("#scr").text(obj.scrivener);
		$("#msg").text(obj.statusMsg);
		$("[name='app2']").val(obj.app);
		$("[name='Brand2']").val(obj.brand);
		$("[name='Category2']").val(obj.category);
	
		// $("#bankN2").text(obj.bankname);
		// $("#scr").text(obj.scrivener);
		// $("#msg").text(obj.msg);

		// $("[name='app2']").val(obj.Application);

		//新增按鈕
		if (obj.cId != '') { 
			$("#add").show();
		}else{
			$("#add").hide();
		}
		// //恢復未使用
		if (obj.status== 'used') {
			$("#status2").show();
		}else{
			$("#status2").hide();
		}
		// console.log(obj);
		// console.log("success");
	});
	
	
}
function conv_scr() {
	var new_scr = $('[name="scr_option_replace"]').val() ;
	
	if (new_scr=='') {
		alert('請選擇欲轉換的代書姓名!!') ;
	}
	else {
		if (confirm('請再次確認是否要變更保證號碼所屬代書!?')) {
			$('[name="chg_scr"]').val('ok');
			$('[name="moreOk"]').val('');
			$('[name="myform"]').submit() ;
		}
	}
}

function conv_scr2() {
	var new_scr = $('[name="scr_option_replace2"]').val() ;
	var check = 0;

	$("#cgCid .certified").each(function() {
		if ($(this).val() != '') {
			check = 1;
		}
	});

	
	if (new_scr=='') {
		alert('請選擇欲轉換的代書姓名!!') ;
	}else if(check == 0){
		alert('請新增保證號碼');
	}else {
		if (confirm('請再次確認是否要變更保證號碼所屬代書!?')) {
			$('[name="chg_scr"]').val('');
			$('[name="moreOk"]').val(1);
			$('[name="mform"]').submit() ;
		}
	}
}

function search() {
	var scr = $('[name="scr_total"]').val() ;
	var bank = $('[name="bank_total"]').val() ;
	var ver = $('[name="ver_total"]').val() ;
	var date = $('[name="date"]').val();
	var url = 'id2scrivener.php' ;

	$('[name="s"]').val(scr);
	$('[name="b"]').val(bank);
	$('[name="v"]').val(ver);

	
	if (scr) {
		$.post(url,{'scr':scr,'ver':ver,'bank':bank,'date':date},function(txt) {
			$('#cid_no_status').html(txt) ;
		}) ;
	}
	else {
		alert('查詢保證號碼餘額條件不完整!! 請重新選擇...') ;
	}
}

function download(){
	var scr = $('[name="scr_total"]').val() ;

	if (scr) {
		$("[name='excel']").submit();
	}else{
		alert('查詢保證號碼餘額條件不完整!! 請重新選擇...') ;
	}
}

function return_status(){

	dia("open") ;
	var url='reworkcId.php';
	if($('[name="cat"]:checked').val() == 'one'){
		var id = $('[name="cid"]').val() ;
	}else{
		var id = $('[name="cid2"]').val() ;
	}
	
	 
	$.post(url,{'cid':id},function(txt) {
			
			setTimeout("dia('close')",2000) ;
			alert(txt);

			$('[name="excel_out"]').submit() ;
			conv_ajax() ;
		}) ;

	
}

function return_Del(){
	dia("open") ;
	var url = 'reworkcIdDel.php';
	var id = $('[name="cid"]').val() ;
	
	$.post(url,{'cid':id},function(txt) {
			
		setTimeout("dia('close')",2000) ;
		alert(txt);

			
		conv_ajax() ;
	}) ;

}

function dia(op) {
	
	$( "#dialog" ).dialog(op) ;
}
function ChangeVer(){


	if($('[name="cat"]:checked').val() == 'one'){
		var cid = $('[name="cid"]').val() ;
		var brand = $('[name="Brand"]').val();
		var category = $('[name="Category"]').val();
		var app = $('[name="app"]').val();

	}else{
		var cid = $('[name="cid2"]').val() ;
		var brand = $('[name="Brand2"]').val();
		var category = $('[name="Category2"]').val();
		var app = $('[name="app2"]').val();
	}
	
	if (confirm("確定要更改?")) {
		$.ajax({
			url: 'id_conv_app.php',
			type: 'POST',
			dataType: 'html',
			data: {cId:cid,br:brand,cat:category,app:app}, //"val": val,"cId":cid,cat:cat
		})
		.done(function(txt) {
			// console.log(txt);
			if (txt== 'ok') {
				alert('更新成功');

				location.href='searchscrivener.php';
			}
		});
	}
	
	
}
function addDown(){
	var cid = $('[name="cid2"]').val();
	var check = 0;


	$("#cgCid .certified").each(function() {
		if ($(this).val() == cid) {
			check = 1;
		}
	});

	if (check == 0) {
		$("#copy").clone().appendTo('#cgCid');
		$("#cgCid #copy #msg:last").append('<input type="button" value="刪除" onClick="del(\''+cid+'\')">');
		$("#cgCid #copy #msg:last").append('<input type="hidden" name="CertifiedId[]" value="'+cid+'" class="certified">');
		//
		$("#cgCid #copy:last").attr('id', 'copy'+cid);

		// $("#cgCid .certified:last").val(cid);
	}else{

		alert('已重複了');
		return false;
	}

	
	
	// var scr = $('#scr').text();

	// console.log(scr+'_'+cid);

	// $('#cgCid').append("<div>"+scr+'_'+cid+"</div>");

}
function del(cid){
	// console.log(cid);
	$("#copy"+cid).remove();
}
function searchprocess(){

	$.ajax({
		url: '../includes/escrow/bankCodeProcess.php',
		type: 'POST',
		dataType: 'html',
		data:{
			year:$("[name='year']").val(),
			month:$("[name='month']").val(),
			searchShipDateStart:$("[name='searchShipDateStart']").val(),
			searchShipDateEnd:$("[name='searchShipDateEnd']").val(),
			searchShipScrivener:$("[name='searchShipScrivener']").val(),
			searchShipApplicant:$("[name='searchShipApplicant']").val()
		}
	}).done(function(html) {
		$("#bancodeprocess").html("");
		$("#bancodeprocess").html(html);
	});
	
}
function saveProcess(){
	var arr_input = new Array();
	var reg = /.*\[]$/ ;
	var select = $("#bancodeprocess select");
	var input = $("#bancodeprocess input");
	var textarea = $('#bancodeprocess textarea');

	$.each(select, function(key, item) {
	 	if (reg.test($(item).attr("name"))) {
                        
            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                arr_input[$(item).attr("name")] = new Array();            
            }
                            
            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
          
        }else{
            arr_input[$(item).attr("name")] = $(item).attr("value");
                        
        }
	});

	$.each(input, function(key, item) {
	 	if (reg.test($(item).attr("name"))) {
                        
            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                arr_input[$(item).attr("name")] = new Array();            
            }
                            
            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
          
        }else{
            arr_input[$(item).attr("name")] = $(item).attr("value");
                        
        }
	});

	$.each(textarea, function(key,item) {
		if (reg.test($(item).attr("name"))) {
                        
            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                arr_input[$(item).attr("name")] = new Array();            
            }
                            
            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
          
        }else{
            arr_input[$(item).attr("name")] = $(item).attr("value");
                        
        }
    });

	arr_input['year'] = $('[name="year"]').val();
	arr_input['month'] = $('[name="month"]').val();
	// console.log(arr_input);
	var obj_input = $.extend({}, arr_input);

	$.ajax({
		url: '../includes/escrow/bankCodeProcessSave.php',
		type: 'POST',
		dataType: 'html',
		data: obj_input
	})
	.done(function(msg) {
		// console.log(msg);
		alert(msg);
		searchprocess();
	});
	
}

function showApplyFrom(no){
	window.open('/bank/form.php?no='+no, '_blank', config='height=700,width=650,scrollbars=yes');
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
	background: #F8EDEB;border:1px #727272 outset;color:font-size:12px;margin-left:2px
}
input.bt4:hover {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background:  #EBD1C8;border:1px #727272 outset;font-size:12px;margin-left:2px;cursor:pointer
}
.ui-autocomplete-input {
	width:200px;
}
#dialog {
			background-image:url("/images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
.button_style{
	padding: 5px 5px 5px 5px;
}

#add {
	color: #2626FF;
	font-family: Verdana;
	font-size: 16px;
	font-weight: bold;
	line-height: 20px;
	background-color: #FFD382;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #DDDDDD;
}
#add:hover {
	color: #FFFFFF;
	font-size:16px;
	background-color: #E69500;
	border: 1px solid #FFFF96;
}
.tb {
	width: 100%;
}
.tb th{
	padding: 5px;
    border: 1px solid #999;
    background-color: #CFDEFF;
    text-align: center;
}
.tb td{
	padding: 5px;
    border: 1px solid #999;
    background-color: #FFFFFF;
    text-align: center;
}
#bancodeprocess{
	width: 100%;
	margin-bottom: 20px;
    padding: 5px;
    background-color: #FFFFFF;
}
.showact{
	border: 1px solid #999;
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
						<div id="dialog"></div>	

						<div id="subTabs">
							<ul>
								<li><a href="#subTabs-1">保號->代書</a></li>
								<li><a href="#subTabs-2">查詢代書剩餘保證號碼</a></li>
								<li><a href="#subTabs-3">出貨進度</a></li>
							</ul>
							<div id="subTabs-1">
								<form name="myform" method="POST">
								<input type="hidden" name="chg_scr" value="">
								<div style="padding-bottom:20px;">
									<input type="radio" name="cat" id="" value="one" checked> 保證號碼單一轉換
									<{if $smarty.session.member_codeChange2 == 1 || $smarty.session.member_pDep == 5}>
										<input type="radio" name="cat" id="" value="more"> 保證號碼多個轉換
									<{/if}>
								</div>
								<div id="one">
									<table style="width:750px;">
										<tr>
											<td colspan="3">
												保證號碼：<input type="text" name="cid" maxlength="9" style="width:140px;font-size:10pt;">&nbsp;&nbsp;<span id="bankN" style="width:100px;" ></span>
												<input type="button" id="status" value="恢復未使用" onclick="return_status()" class="button_style">
												
											</td>
										</tr>
										<tr>
											<td colspan="3">
											<div class="showact">
												更改契約書版本
												<select name="app">
														<option value="1">土地</option>
														<option value="2">建物</option>
														<option value="3">預售屋</option>
												</select>
												&nbsp;&nbsp;
												合約品牌
												<select name="Brand">
													<{$ver_option_total}>
												</select>	
												&nbsp;&nbsp;
												仲介類型
												<select name="Category">
													<option value=""></option>
													<option value="1">加盟</option>
													<option value="2">直營</option>
													<option value="3">非仲介成交</option>
												</select>
												<input type="button" value="更改" onclick="ChangeVer()">
											</div>
											
												
											</td>
										</tr>
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td>
												<div style="border:1px groove;width:300px;height:200px;padding:10px;background-color: white">
													<div>原始地政士資料：<br><hr><br></div>
													<div id="scr_name"></div>
													<div id="cid_status"></div>
													<div id="scr_cid_status"></div>
												</div>
											</td>
											<td style="width:120px; text-align:center;">
												
												<input type="button" id="cnvt" value="  -- 轉換 -> " onclick="conv_scr()" class="button_style">
												
											</td>
											<td>
												<div style="border:1px groove;width:300px;height:200px;padding:10px;background-color: white;">
													<div>欲轉換地政士：<br><hr><br></div>
													<select name="scr_option_replace">
														<option></option>
														<{$scr_option_total2}>
													</select>
												</div>
											</td>
										</tr>
									</table>
								</div>
								</form>
								<form action="" method="POST" name="mform">
								<div id="more">
									<table style="width:750px;">
										<tr>
											<td colspan="3">
												保證號碼：<input type="text" name="cid2" maxlength="9" style="width:140px;font-size:10pt;" onkeyup="conv_ajax2()">&nbsp;&nbsp;
												<input type="button" value="新增至下方" onclick="addDown()" id="add">

												&nbsp;&nbsp;
												<input type="button" id="status2" value="恢復未使用" onclick="return_status()" class="button_style">
											</td>

										</tr>
										<tr>
											<td colspan="3">
											<div class="showact">
												更改契約書版本
												<select name="app2">

														<option value="1">土地</option>
														<option value="2">建物</option>
														<option value="3">預售屋</option>
												</select>
												&nbsp;&nbsp;
												合約品牌
												<select name="Brand2">
													<{$ver_option_total}>
												</select>	
												&nbsp;&nbsp;
												仲介類型
												<select name="Category2">
													<option value=""></option>
													<option value="1">加盟</option>
													<option value="2">直營</option>
													<option value="3">非仲介成交</option>
												</select>
												<input type="button" value="設定" onclick="ChangeVer()">
											</div>
												
											</td>
										</tr>
										<tr>
											<td colspan="3">
												銀行別:<span id="bankN2" style="width:100px;" ></span>
											</td>
										</tr>

										<tr>
											<td colspan="3">目前狀態:
												<div id="copy" style="padding-left:20px;">
													<span id="scr" style="color:#000080;font-weight:bold;"></span>
													<div id="msg" ></div>
												</div>		
											</td>
										</tr>
										
										<tr>
											<td colspan="3">
												
											</td>
										</tr>
										<tr>
											<td>
												<div style="border:1px groove;width:350px;height:200px;padding:10px;overflow:auto;background-color: white;">
													<div>保證號碼：<br><hr><br></div>
													<div id="cgCid" style="">
														
													</div>
												</div>
			
											</td>
											<td>
												
													<input type="button" id="cnvt" value="  -- 轉換 -> " onclick="conv_scr2()" class="button_style">
													<input type="hidden" name="moreOk" value="">
												
											</td>
											<td>
												<div style="border:1px groove;width:300px;height:200px;padding:10px;background-color: white;">
													<div>欲轉換地政士：<br><hr><br></div>
													<select name="scr_option_replace2">
														<option></option>
														<{$scr_option_total2}>
													</select>
												</div>
											</td>
										</tr>
									</table>
									

								</div>
								</form>
							</div>
							<div id="subTabs-2" >
								<div>
									<table style="width:750px;">
										<tr>
											<td valign="top" style="width:320px;">
												<input type="hidden" name="s" >
												<input type="hidden" name="b" >
												<input type="hidden" name="v" >
												<form action="id2scrivenerExcel.php" name="excel" method="POST" target="_blank">
												<div>
													查詢代書剩餘保證號碼數量：
												</div>
												<div style="height:10px;"></div>
												<div style="padding:1px;">
													<span style="color:red;">*</span>代&nbsp;&nbsp;&nbsp;&nbsp;書：
													<select name="scr_total" >
														<option></option>
														<{$scr_option_total}>
													</select>
												</div>
												<div style="padding:1px;">
													　銀行別：
													<select name="bank_total">
														<option></option>
														<{$bank_option_total}>
													</select>
												</div>
												<div style="padding:1px;">
													合約版本：
													<select name="ver_total">
														<option></option>
														<{$ver_option_total}>
													</select>
												</div>
												
												<div style="padding:1px;">
													申請日期：
													<input type="text" name="date"  class="datepickerROC" placeholder ="格式範例:000-00-00">
												</div>
												<div style="text-align:center; padding:5px;">
													<input type="button" style="padding:5px;" id="search_scr" onclick="search()" value=" 查詢">
													<input type="button" style="padding:5px;font-size:12px;" value="匯出EXCEL" onclick="download()">
												</div>
												</form>
											</td>
											<td style="border:1px groove;background-color: white;">
												<center>
												<span id="cid_no_status"></span>
												</center>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div id="subTabs-3" >
								<div style="margin-bottom: 10px;">
									時間:
									<{html_options name=year options=$menuYear selected=$year onchange="searchprocess()"}> 年
									<{html_options name=month options=$menuMonth selected=$month onchange="searchprocess()"}>月
									出貨日:
									<input type="text" name="searchShipDateStart" id="" class="datepickerROC2" style="width:100px;">至
									<input type="text" name="searchShipDateEnd" id="" class="datepickerROC2" style="width:100px;">
									地政士:
									<select name="searchShipScrivener" id="searchShipScrivener">
									<option></option>
									<{$menuSearchShipScrivener}>
									</select>
									<br>
									申請人:
									<{html_options name=searchShipApplicant options=$menuApplicant selected=$applicant}>

									
									<input type="button" value="搜尋" onclick="searchprocess()">
									<font color="red">(2020年09月後為新版介面)</font>
								</div>
								<div id="bancodeprocess">
									
								</div>
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