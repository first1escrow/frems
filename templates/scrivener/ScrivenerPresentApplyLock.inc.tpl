<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" href="../css/colorbox.css" />		
<{include file='meta2.inc.tpl'}>
<script src="/js/IDCheck.js"></script>
<script type="text/javascript">
$(document).ready(function() {
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
	$( "#dialog" ).dialog("close") ;
});
function lock(act){
	// $('[name="act"]').val(act);
	// $('[name="form"]').submit();
	// alert("處理中，請稍後");
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
	$.ajax({
		url: 'ScrivenerPresentApplySetLock.php',
		type: 'POST',
		dataType: 'html',
		data: {act: act,year:$("[name='year']").val(),month:$("[name='month']").val(),scrivener:$("[name='scrivener']").val()},
	})
	.done(function(msg) {
		// console.log(msg);
		
		$( "#dialog" ).dialog("close") ;
		alert("已更新完成");
	});
	
}
</script>
<style type="text/css">  
	#dialog{
		background-image:url("/images/animated-overlay.gif") ;
		background-repeat: repeat-x;
		margin: 0px auto;
		width: 300px;
		height: 30px;
	}    
	input {
		padding:5px;
		border:1px solid #CCC;
	}
	textarea{
		padding:10px;
		border:1px solid #CCC;
	}
	#year,#month{
		width: 100px;
	}
	.btn {
		color: #000;
		font-family: Verdana;
		font-size: 14px;
		font-weight: bold;
		line-height: 14px;
		background-color: #CCCCCC;
		text-align:center;
		display:inline-block;
		padding: 8px 12px;
		border: 1px solid #DDDDDD;
		/*border-radius:0.5em 0.5em 0.5em 0.5em;*/
	}
	.btn:hover {
		color: #000;
		font-size:12px;
		background-color: #999999;
		border: 1px solid #CCCCCC;
	}
	.btn.focus_end{
		    color: #000;
		    font-family: Verdana;
		    font-size: 14px;
		    font-weight: bold;
		    line-height: 14px;
		    background-color: #CCCCCC;
		    text-align:center;
		    display:inline-block;
		    padding: 8px 12px;
		    border: 1px solid #FFFF96;
		    /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
	}
		
	.l2,.l3,.l4,.l21{
		width: 300px;
	}
		
	.input-color {	
		background-color:#e8e8e8 ;
	}
	.tb-title {
        font-size: 18px;
        padding-left:15px; 
        padding-top:10px; 
        padding-bottom:10px; 
        background: #D1927C;

    }
    .input-text-sml{
        width:36px;

    }
    .cb1 {
		padding:0px 10px;
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
		background:url(../images/check_radio_sheet2.png) left top no-repeat;
		cursor:pointer;
		background-size:80px 20px;
		transition: none;
		-webkit-transition:none;
	}
	.cb1 input[type="checkbox"]:checked + label span {
		background:url(../images/check_radio_sheet2.png)  -20px top no-repeat;
		background-size:80px 20px;
		transition: none;
		-webkit-transition:none;
	}
	.cb1 label {
		cursor:pointer;
		display: inline-block;
		white-space: nowrap;
		margin-right: 10px;
		font-weight: bold;
		/*-webkit-appearance: push-button;
		-moz-appearance: button;*/
	}
	/*input*/
	.xxx-input {
		color:#666666;
		font-size:16px;
		font-weight:normal;
		/*background-color:#FFFFFF;*/
		text-align:left;
		height:34px;
		padding:0 5px;
		border:1px solid #CCCCCC;
		border-radius: 0.35em;
	}
	.xxx-input:focus {
		border-color: rgba(82, 168, 236, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
		-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
		outline: 0 none;
	}
	/*textarea*/
	.xxx-textarea {
		color:#666666;
		font-size:16px;
		font-weight:normal;
		line-height:normal;
		/*background-color:#FFFFFF;*/
		text-align:left;
		height:100px;
		padding:5px 5px;
		border:1px solid #CCCCCC;
		border-radius: 0.35em;
	}
	.xxx-textarea:focus {
		border-color: rgba(82, 168, 236, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
		-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
		outline: 0 none;
	}
	.xxx-select {
		color:#666666;
		font-size:16px;
		font-weight:normal;
		/*background-color:#FFFFFF;*/
		text-align:left;
		height:34px;
		padding:0 0px 0 5px;
		border:1px solid #CCCCCC;
		border-radius: 0em;
	}
</style>
</head>
<body  style="background-color:#F8ECE9">
	<div id="dialog" ></div>
    <div style="font-size: 16px;">申請對象鎖住</div>
	<div style="margin-top: 10px;">
		<form action="" method="POST" id="form" name="form">
	               		
	        <{html_options name=year id=year options=$menuYear class="xxx-select l2" selected=$year }>年
	        <{html_options name=month id=month options=$menuMonth class="xxx-select l2" selected=$month }>月
	        <select name="scrivener" id="" class="easyui-combobox" style="width: 200px;">
	        	<option value=""></option>
	        	<{foreach from=$menuScrivener key=key item=item}>
	        	<option value="<{$key}>"><{$item}></option>
	        	<{/foreach}>
	        </select>
	               		
	        <input type="button" value="鎖住" class="btn" onclick="lock(1)">&nbsp;&nbsp;&nbsp;&nbsp;
	        <input type="button" value="解鎖" class="btn" onclick="lock(2)">
	        
		</form>
		
	</div>
       
	        
		
   
</body>
</html>










