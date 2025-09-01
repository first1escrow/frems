<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	<meta charset="UTF-8">
	<title>地政士負責經辦更改</title>
	<script src="../js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
	<link rel="stylesheet" href="../../css/datepickerROC.css" />
	<script type="text/javascript" src="../../js/datepickerRoc.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			
		});

		/* 取得縣市區域資料 */
			function cityChange() {
				var url = '../report/zipArea.php' ;
				var _city = $('#citys :selected').val() ;
				$.post(url,{'c':_city,'op':'1'},function(txt) {
					$('#areas').html(txt) ;
				}) ;
			}
			////

			/* 取得區域郵遞區號 */
			function areaChange() {
				var _area = $('#areas :selected').val() ;
				$('#zip').val(_area) ;
			}

		
	</script>
	<style>
		body{
			background-color: #F8ECE9
		}
		h1{
			transition: all 0.3s ease 0s;
			-webkit-transition: all 0.3s ease 0s;
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
		.title{
			text-align: center;
			font-size: 18px;
		}
		.undertaker{
			margin: 10px 10px 10px 10px ;
		}
		.undertaker-title, .undertaker-textbox, .undertaker-ctrl{
			margin-top: 5px;
			margin-bottom: 5px;
		}
		
		
		.undertaker-q{
			border-bottom:1px solid #CCCCCC;
			width: 100%;
			padding: 2px 2px 2px 2px;
			margin-bottom: 5px;
		}
		
		/*input*/
		placeholder {
			color: #999999;
			opacity: 1.0;
		}
		:-moz-placeholder {
			color: #999999;
			opacity: 1.0;
		}
		::-moz-placeholder {
			color: #999999;
			opacity: 1.0;
		}
		::-webkit-input-placeholder {
			color: #999999;
			opacity: 1.0;
		}
		:-ms-input-placeholder {
		   color: #999999;
		   opacity: 1.0;
		}
		.xxx-input {
			color:black;
			font-size:16px;
			font-weight:normal;
			background-color:#FFFFFF;
			text-align:left;
			height:34px;
			padding:0 5px;
			border:1px solid #CCCCCC;
			
		}
		.xxx-input:focus {
			border-color: rgba(82, 168, 236, 0.8) !important;
			box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
			-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
			outline: 0 none;
		}
		.tb {
			padding:2px;
			border :1px solid #a63c38;
			width: 100%;
		}
		.tb th{
			padding: 6px;
			color: rgb(255, 255, 255);
		    font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
		    font-size: 1em;
		    font-weight: bold;
		    background-color: rgb(156, 40, 33);
		}
		.tb td{
			padding: 6px;
		    border: 1px solid #CCCCCC;
		    text-align: left;
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
		}
		.xxx-select:focus {
			border-color: rgba(82, 168, 236, 0.8) !important;
			box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
			-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
			outline: 0 none;
		}
	</style>
</head>
<body>
<h3>地政士承辦經辦更改</h3>
<div class="undertaker">
	
		<div class="undertaker-title">
			<div class="undertaker-textbox"></div>
			<div class="undertaker-textbox">
				<form action="" method="POST" id="NewsForm" >
					區域:
					 <{html_options name=city id=citys options=$city onchange="cityChange()" class="xxx-select" style="width:80px;"}>
					<select name="area" id="areas" class="xxx-select" style="width:80px;"> <option value="">全區</option></select>
					&nbsp;&nbsp;&nbsp;&nbsp;
					經辦:
					<{html_options name=o_undertaker id=o_undertaker options=$undertaker class="xxx-select" style="width:80px;"}>轉給

					<{html_options name=undertaker id=undertaker options=$undertaker class="xxx-select" style="width:80px;"}>
					&nbsp;&nbsp;&nbsp;&nbsp;
					

					<input type="submit" value="送出" class="btn">
				</form>
			</div>
			
		</div>
		<div style="padding-left:30px;float:center;display:inline;text-align: center;width: 100%">
				
				
		</div>
		<div class="undertaker-textbox"> <hr> </div>
		<div>
			<table cellspacing="0" cellpadding="0" border="0" class="tb">
				<tr>
					<th>內容</th>
					<th>時間</th>
					<th>修改人</th>
				</tr>
				<{foreach from=$note key=key item=item}>
				<tr>
					<td><{$item.nContent}></td>
					<td><{$item.nModifyTime}></td>
					<td><{$item.Name}></td>
				</tr>
				<{/foreach}>
			</table>
		</div>
		
		<br>
		<center>
		<div>
			
			
		</div>
		</center>
	
</div>
	

	
</body>
</html>
