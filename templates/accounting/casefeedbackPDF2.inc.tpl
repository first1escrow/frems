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
	
	$("[name='storeSearch']").on('click', function() {
		if ($("[name='storeSearch']").prop('checked') == true) {
			$('[name="bCategory"]').removeAttr('checked');
			$("[name='bCategory']").attr('disabled', 'disabled');
		} else {
			$("[name='bCategory']").removeAttr('disabled');
			$("[name='bCategory']").prop('checked', 'checked')
		}
	});

	$("[name='brand']").on('change', function() {
		$('[name="bCategory"]').removeAttr('checked');
		$("[name='bCategory']").attr('disabled', 'disabled');
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

function save(act) {
	var status = $('[name="caseStatus"]:checked').val();
	var bk = $('[name="bank"]').val() ;
	var sc = $('[name="bStoreClass"]').val() ;
	var sy = $('[name="sales_year"]').val() ;
	var se = $('[name="sales_season"]').val() ;
	var cd = $('[name="certifiedid"]').val() ;
	var ir = $('[name="invert_result"]:checked').val() ;
	var bck = $('[name="storeSearch"]:checked').val();

	if (status == 'a' && act == 's') {
		var url = 'casefeedbackPDF2_result_old.php' ;
	} else {
		var url = 'casefeedbackPDF2_result.php' ;
	}

	
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
	$.post(url,
		{'bank':bk,'bStoreClass':sc,'branch':br,'bCategory':bc,'invert_result':ir, 'sales_year_end':$('[name="sales_year_end"]').val(),'sales_season_end':$('[name="sales_season_end"]').val(),
		'sales_year':sy,'sales_season':se,'certifiedid':cd,'scrivener':scr,'bck':bck,'status':status,'act':act,'bd':$("[name='brand']").val(),"timeCategory":$("[name='timeCategory']:checked").val()},
		function(txt) {
			$('#container').html(txt) ;
			$( "#dialog" ).dialog("close") ;
	}).fail(function (jqXHR, textStatus, errorThrown) {
        /*打印jqXHR对象的信息*/
        // console.log(jqXHR.responseText); //必要的时候编码一下:encodeURIComponent(jqXHR.responseText);
        // console.log(jqXHR.status);
        // console.log(jqXHR.readyState);
        // console.log(jqXHR.statusText);
        /*打印其他两个参数的信息*/
        // console.log(textStatus);
        // console.log(errorThrown);
        
        $( "#dialog" ).dialog("close") ;
        alert(jqXHR.statusText);
    }) ;
}

function add(cat) {
	if (cat == 'b') {
		var val = $('[name="branch"]').val();
		var text = $('#branch option[value="'+val+'"]').text(); 

		$("#showBrach").append('<div id="'+val+'" class="addStore bStore"><a href="#" onClick="del('+val+')" >(刪除)</a>'+text+'</div>');
		var filter = /直營/;
		var filter2 = /^TH/;//
        
        if (filter.test(text)&&filter2.test(text)) {
            $('input:checkbox[name="bCategory"]').filter('[value="2"]').attr('checked',false) ;
        } else {
        	$('input:checkbox[name="bCategory"]').filter('[value="1"]').attr('checked',false) ;
        }
	} else if (cat == 's') {
		var val = $('[name="scrivener"]').val();
		var text = $('#scrivener option[value="'+val+'"]').text(); 
		
		$("#showSctivener").append('<div id="'+val+'" class="addStore sStore"><a href="#" onClick="del('+val+')">(刪除)</a>'+text+'</div>');
		$('input:checkbox[name="bCategory"]').filter('[value="3"]').attr('checked',false) ;
	}
}

function del(id) {
	$("#"+id).remove();
}

function WebRelease() {
	var tmp = new Array();
	$('[name="allForm[]"]:checked').each(function(i) { 
		tmp[i] = this.value; 
	});
	var form = tmp.join(',');

	$.ajax({
		url: 'casefeedbackPDF2_webRelease.php',
		type: 'POST',
		dataType: 'html',
		data: {form: form},
	})
	.done(function(msg) {
		alert(msg);
		searchResult('s');
	});
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
                                <h1>付款通知書查詢</h1>
                                <div id="dialog" class="easyui-dialog" style="display:none"></div>
                                <div id="container">

                                    <table cellspacing="0" cellpadding="0" style="width:900px;padding-top:20px;">
                                        <tr>
                                            <td colspan="4" style="width:300px;background-color:#E4BEB1;padding:4px;">&nbsp;
                                                
                                            </td>
                                        </tr>

                                        <tr >
                                            <td colspan="2" style="width:200px;background-color:#E4BEB1;padding:4px;">
                                                仲介商類型
                                                <span class="cb1"><input type="checkbox" name="bCategory" value="1" id="Category1" checked><label for="Category1"><span></span>加盟</label></span>
                                                <span class="cb1"><input type="checkbox" name="bCategory" value="2" id="Category2" checked><label for="Category2"><span></span>直營</label></span>
                                                <span class="cb1"><input type="checkbox" name="bCategory" value ="3" id="Category3" checked><label for="Category3"><span></span>地政士</label></span>
                                            </td>
                                            <td colspan="2" style="width:200px;background-color:#E4BEB1;padding:4px;">
                                                狀態查詢:
                                                <input type="radio" name="caseStatus" value="a" checked="">全部(未產出)
                                                <input type="radio" name="caseStatus" value="0"> 未發布
                                                <input type="radio" name="caseStatus" value="1"> 已發布
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="4"  style="width:300px;background-color:#F8ECE9;padding:4px;">
                                                <input type="radio" name="timeCategory" value="1" checked>單一
                                                <input type="radio" name="timeCategory" value="2">區間

                                                年度季別
                                                <select name="sales_year" style="width:60px;" class="xxx-select">
                                                <{$menu_year}>
                                                </select>
                                                年度
                                                <{html_options name="sales_season" style="width:80px;" options=$menu_season selected=$seasons class="xxx-select"}>(起)~
                                                年度季別
                                                <select name="sales_year_end" style="width:60px;" class="xxx-select">
                                                <{$menu_year}>
                                                </select>
                                                年度
                                                <{html_options name="sales_season_end" style="width:80px;" options=$menu_season selected=$seasons class="xxx-select"}>(迄)
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
                                        </div>
                                        <div style="display:inline;margin:50px;line-height:30px;"><input type="button" value="增加" onclick="add('b')" class="xxx-button">
                                        </div>
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
                                        <div style="display:inline;margin:50px;line-height:30px;"><input type="button" value="增加" onclick="add('s')" class="xxx-button">
                                        </div>
                                        <div id="showSctivener" style="padding-left:20px;">
                                        </div>
                                    </div>

                                    <div style="padding:20px;text-align:center;">
                                        <input type="button" value="查詢" onclick="save('s')" class="xxx-button" style="display:;width:100px;height:35px;font-size:16px;">
                                        <input type="button" value="產出PDF" onclick="save('pdf')" class="xxx-button" style="display:;width:100px;height:35px;font-size:16px;">
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