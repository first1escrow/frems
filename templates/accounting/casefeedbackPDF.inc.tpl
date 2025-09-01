<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>

<{include file='meta2.inc.tpl'}>


<script type="text/javascript">
$(document).ready(function() {
    var aSelected = [];
    $('#dialog').dialog('close');
	$(".ajax").colorbox({width:"400",height:"100"});
	$(".iframe").colorbox({iframe:true, width:"1200px", height:"90%"}) ;

	// $( "[name='scrivener']" ).combobox();
	// get_branch() ;
	
	// $('[name="storeSearch"]').on('click', function() {
		
	// 	if ($(this).prop('checked') == true) {
	// 		$('.addbt').show();
			
	// 	}else{
	// 		$('.addbt').hide();
	// 		$("#showBrach").empty();
	// 		$("#showSctivener").empty();
	// 	}
	// });
	
	$("[name='storeSearch']").on('click', function() {
		
		if ($("[name='storeSearch']").prop('checked') == true) {
			
			$('[name="bCategory"]').removeAttr('checked');
			$("[name='bCategory']").attr('disabled', 'disabled');
		}else{
			$("[name='bCategory']").removeAttr('disabled');
			$("[name='bCategory']").prop('checked', 'checked')
		}


	});


	$(".panel-header").hide() ;
	
	
	
});
			
function get_branch() {
	var url = "/includes/report/get_branch.php" ;
	var cl = $('[name="bStoreClass"]').val() ;
	var bc = $('[name="bCategory"]').val() ;
	
	$.post(url,{'bStoreClass':cl,'bCategory':bc},function(txt) {
		var str = '&nbsp;店名稱&nbsp;<select id="branch" name="branch" class="easyui-combobox">'+txt+'</select>' ;
		
		$('#branch1').html(str) ;

		$( "#branch" ).combobox();
		
	}) ;
}

function save() {
	var url = 'casefeedbackPDF_result.php' ;

	var bk = $('[name="bank"]').val() ;
	var sc = $('[name="bStoreClass"]').val() ;
	
	var sy = $('[name="sales_year"]').val() ;
	var se = $('[name="sales_season"]').val() ;
	var cd = $('[name="certifiedid"]').val() ;
	// var bc = $('[name="bCategory[]"]').val() ;
	var ir = $('[name="invert_result"]:checked').val() ;
	var bck = $('[name="storeSearch"]:checked').val();
	
	
	var tmp = new Array();
	$('.bStore').each(function(i) { tmp[i] = this.id; });
	var br = tmp.join(',');

	var tmp = new Array();
	$('.sStore').each(function(i) { tmp[i] = this.id; });
	var scr = tmp.join(',');

	var tmp = new Array();
	$('[name="bCategory"]:checked').each(function(i) { 
		
				tmp[i] = this.value; 
		
		
	});
	var bc = tmp.join(',');

	
	
	$( "#dialog" ).dialog("open") ;
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
	$.post(url,
		{'bank':bk,'bStoreClass':sc,'branch':br,'bCategory':bc,'invert_result':ir, 
		'sales_year':sy,'sales_season':se,'certifiedid':cd,'scrivener':scr,'bck':bck},
		function(txt) {
			$('#container').html(txt) ;
			$( "#dialog" ).dialog("close") ;
	}) ;
	
	//alert('儲存') ;
	//alert(url) ;
}
function add(cat){

	if (cat == 'b') {
		var val = $('[name="branch"]').val();
		
		var text = $('#branch option[value="'+val+'"]').text(); 
		
		
		$("#showBrach").append('<div id="'+val+'" class="addStore bStore"><a href="#" onClick="del('+val+')" >(刪除)</a>'+text+'</div>');

		  var filter = /直營/;
		  var filter2 = /^TH/;//
        if (filter.test(text)&&filter2.test(text)) {
           $('input:checkbox[name="bCategory"]').filter('[value="2"]').attr('checked',false) ;
        }else{
        	$('input:checkbox[name="bCategory"]').filter('[value="1"]').attr('checked',false) ;
        }


		
	}else if(cat == 's'){
		var val = $('[name="scrivener"]').val();
		var text = $('#scrivener option[value="'+val+'"]').text(); 
		
		$("#showSctivener").append('<div id="'+val+'" class="addStore sStore"><a href="#" onClick="del('+val+')">(刪除)</a>'+text+'</div>');
		$('input:checkbox[name="bCategory"]').filter('[value="3"]').attr('checked',false) ;
		
	}
}
function del(id){
	$("#"+id).remove();
}
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
	width: 300px;
	height: 30px;
}
.store{
	/*border: 1px solid #999;*/
	background-color:#F8ECE9;
	padding-bottom: 20px;
	width:900px;

}
.addStore{
	background-color: white;
	padding-top: 5px;
	padding-bottom: 5px;
	width:400px;
	border-bottom: 1px #CCC solid;
}

.cb1 {
	padding:0px 0px;
}
.cb1 input[type="checkbox"] {/*隱藏原生*/
    /*display:none;*/
    position: absolute;
    left: -9999px;
}
.cb1 input[type="checkbox"] + label span {
    display:inline-block;
    width:20px;
    height:20px;
    margin:-3px 4px 0 0;
    vertical-align:middle;
    background:url("../images/check_radio_sheet2.png") left top no-repeat;
    cursor:pointer;
	background-size:80px 20px;
	transition: none;
	-webkit-transition:none;
}
.cb1 input[type="checkbox"]:checked + label span {
    background:url("../images/check_radio_sheet2.png") -20px top no-repeat;
	background-size:80px 20px;
	transition: none;
	-webkit-transition:none;
}
.cb1 label {
    cursor:pointer;
	display: inline-block;
	margin-right: 10px;
    /*-webkit-appearance: push-button;
    -moz-appearance: button;*/
}

/*button*/
.xxx-button {
color:#FFFFFF;
	font-size:12px;
	font-weight:normal;
	
	text-align: center;
	white-space:nowrap;
	height:20px;
	
	background-color: #a63c38;
    border: 1px solid #a63c38;
    border-radius: 0.35em;
    font-weight: bold;
    padding: 0 20px;
    margin: 5px auto 5px auto;
}
.xxx-button:hover {
	background-color:#333333;
	border:1px solid #333333;
}
.xxx-select {
	color: #666666;
	font-size: 14px;
	font-weight: normal;
	background-color: #FFFFFF;
	text-align: left;
	height: 24px;
	padding: 0 0px 0 5px;
	border: 1px solid #CCCCCC;
	border-radius: 0em;
	font-family: "微軟正黑體", serif;
}

/*input*/
.xxx-input {
	color:#666666;
	font-size:14px;
	font-weight:normal;
	background-color:#FFFFFF;
	text-align:left;
	height:24px;
	padding:0 5px;
	border:1px solid #CCCCCC;
	border-radius: 0.35em;
}
.xxx-input:focus {
    border-color: rgba(82, 168, 236, 0.8);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
	-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
    outline: 0 none;
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
										<h1>店家回饋付款通知書</h1>
										<div id="container">
										<div id="dialog" class="easyui-dialog" style="display:none"></div>


<table cellspacing="0" cellpadding="0" style="width:900px;padding-top:20px;">
<tr>
	<td style="width:300px;background-color:#E4BEB1;padding:4px;">
	金流系統

	<select name="bank" size="1" style="width:165px;" class="xxx-select">
		<{$contract_bank}>
	</select>
	</td>
	<td style="width:200px;background-color:#E4BEB1;padding:4px;">
	&nbsp;&nbsp;身分別&nbsp;
	<select name="bStoreClass" size="1" style="width:120px;" onchange="get_branch()" class="xxx-select">
		<option value="2" selected="selected">單店</option>
		<option value="1">總店</option>
	</select>
	</td>
	<td style="width:200px;background-color:#E4BEB1;padding:4px;">
		保證號碼
	<input type="text" name="certifiedid" style="width:100px;" maxlength="9" class="xxx-input">
	</td>
	<td style="width:200px;background-color:#E4BEB1;padding:4px;">
	
	</td>
</tr>
<tr >
	<td colspan="4" style="width:200px;background-color:#E4BEB1;padding:4px;">
		仲介商類型
		<!-- <select name="bCategory" size="1" style="width:100px;" onchange="get_branch()">
			<option value="" selected="selected">全部</option>
			<option value="1">加盟</option>
			<option value="2">直營</option>
			<option value="3">非仲介成交</option>
		</select> -->
		<!-- <input type="checkbox" name="bCategory[]" id="" value="1">加盟
		<input type="checkbox" name="bCategory[]" id="" value="2">直營
		<input type="checkbox" name="bCategory[]" id="" value="3">非仲介成交 -->
		
        <span class="cb1"><input type="checkbox" name="bCategory" value="1" id="Category1" checked><label for="Category1"><span></span>加盟</label></span>

        <span class="cb1"><input type="checkbox" name="bCategory" value="2" id="Category2" checked><label for="Category2"><span></span>直營</label></span>
        <span class="cb1"><input type="checkbox" name="bCategory" value ="3" id="Category3" checked><label for="Category3"><span></span>地政士</label></span>

	</td>
</tr>

<tr>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	年度季別
	<select name="sales_year" style="width:60px;" class="xxx-select">
	<{$menu_year}>
	</select>
	年度
	<{html_options name="sales_season" style="width:80px;" options=$menu_season selected=$seasons class="xxx-select"}>
	</td>
	<td colspan="2" style="width:400px;background-color:#F8ECE9;padding:4px;">
		&nbsp;
	</td>
	<td style="width:200px;background-color:#F8ECE9;padding:4px;font-size:10pt;">
		
	</td>
</tr>


<tr>
	<td colspan="4" style="background-color:#F8ECE9;padding:4px;">
		
		<input type="checkbox" name="storeSearch" value="1">只查詢店家或地政士
		<span style='color:red'>※請選擇完後按下增加</span>
	</td>
</tr>
</table>
<div class="store">
	<div id="branch1" style="display:inline">
		&nbsp;店名稱&nbsp;
		<select id="branch" name="branch" class="easyui-combobox" data-options="
                    valueField: 'id',
                    textField: 'text'
                    " style="width:300px;">
		<{foreach from=$menu_branch key=key item=item}>
			<option value="<{$key}>"><{$item}></option>
		<{/foreach}>
		</select> 
		
		<!-- <input id="branch" class="easyui-combobox" name="branch" style="width:100%;" > -->
	</div>
	<div style="display:inline;margin:50px;line-height:30px;"><input type="button" value="增加" onclick="add('b')" class="xxx-button"></div>
	<div id="showBrach" style="padding-left:20px">
		
	</div>
</div>
<div class="store">
	<div  style="display:inline">
		&nbsp;地政士&nbsp;
		<select name="scrivener" id="scrivener"  class="easyui-combobox" style="width:300px;">
			<{foreach from=$menu_scr key=key item=item}>
			<option value="<{$key}>"><{$item}></option>
			<{/foreach}>
		</select>
		
	</div>
	<div style="display:inline;margin:50px;line-height:30px;"><input type="button" value="增加" onclick="add('s')" class="xxx-button"></div>

	<div id="showSctivener" style="padding-left:20px;">
		
	</div>
</div>


<div style="padding:20px;text-align:center;">
<input type="button" value="查詢" onclick="save()" class="xxx-button" style="display:;width:100px;height:35px;font-size:16px;">
</div>
<div id="dwn"></div>
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