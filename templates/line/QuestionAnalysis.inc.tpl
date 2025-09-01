<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	<meta charset="UTF-8">
	<title>問卷統計</title>
	<script src="../js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
	<link rel="stylesheet" href="../../css/datepickerROC.css" />
	<script type="text/javascript" src="../../js/datepickerRoc.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			
		});

		
	</script>
	<style>
		body{
			background-color: #F8ECE9;
			text-align:center;
		}
		.tb{
			border-color: #CCC 1px solid;
			margin-top: 20px;
		}
		.tb th{
			color:#FFF;
			background-color: #a63c38;
			padding: 5px;
			border: 1px solid #a63c38;
		}
		.tb td{
			color:#000;
			background-color: #FFF;
			padding: 5px;
			border: 1px solid #FFF;
		}
		
		.tb2{
			width:80%;
			border-color: #CCC 1px solid;
			margin-bottom: 20px;
		}
		.tb2 th{
			color:#FFF;
			background-color: #a63c38;
			padding: 5px;
			/*border: 1px solid #CCC;*/
		}
		.tb2 td{
			color:#000;
			background-color: #FFF;
			padding: 5px;
			border: 1px solid #CCC;
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
	</style>
</head>
<body>
<h1>問卷統計</h1>
<div class="question">
	<!-- input: -->
	<div>
		
	
	<form action="" method="POST">
		<{html_options name=year options=$menuYear selected=$year class="xxx-select"}>年
		<{html_options name=month options=$menuMonth selected=$month class="xxx-select"}>月
		<input type="hidden" name="id" value="<{$id}>">
		<input type="submit" value="查詢" class="btn">
	</form>
	</div>
	<div class="tb">
		<table cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<th>問卷總數</th>
				<th>問卷有效數</th>
				<th>問卷無效數</th>
			</tr>
			<tr>
				<td><{$data['total']}></td>
				<td><{$data['vaild']}></td>
				<td><{$data['invalid']}></td>
			</tr>
		</table>
	</div>
	<hr>
	<div >
		<{foreach from=$data['count'] key=key item=item}>
		<table cellpadding="0" cellspacing="0" border="0"  align="center" class="tb2">
			
			<tr>
				<th colspan="<{($item.item|count)+1}>"><{$item.title}></th>
			</tr>
			<tr>
				<td>選項名稱</td>
				<td>選擇數</td>
			</tr>
			<{foreach from=$item.item key=k item=value}>
			<tr>	
				<td><{$k}></td>
				<td><{$value}></td>
			</tr>
			<{/foreach}>
			
		</table>
		<{/foreach}>
	</div>
</div>
	

	
</body>
</html>
