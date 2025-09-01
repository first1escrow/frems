<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="UTF-8">
	<title>最新消息修改</title>
	<link rel="stylesheet" type="text/css" href="../libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
	<script src="../js/jquery-1.7.2.min.js"></script>

	<script type="text/javascript" src="../libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	<script src="../js/datepickerRoc.js"></script>
	<link rel="stylesheet" href="/css/datepickerROC.css" />

	<script type="text/javascript">
		$(document).ready(function() {
			
		});
		function Del(){
			if (confirm("確定要刪除嗎?")) {
				$('[name="cat"]').val(3);
				$("#NewsForm").submit();
			}
			
		}
		
	</script>
	<style>
		body{
			background-color: #F8ECE9
		}
		table th{
			width: 20%;
			text-align: right;
			padding: 5px;
			border: 1px solid #999;
		}
		table td{
			
			text-align: left;
			padding: 5px;
			border: 1px solid #999;
		}
		input {
			padding:10px;
			border:1px solid #CCC;
		}
		.sl{
			padding:10px;
			border:1px solid #CCC;
		}
		textarea{
			padding:10px;
			border:1px solid #CCC;
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
<center>
	<h1>編輯</h1>
	<form action="" method="POST" enctype="multipart/form-data" id="NewsForm" >
		<table cellpadding="0" cellspacing="0" width="80%">
			<tr>
				<td colspan="2">※如果當天整天放假，開始、結束時間不需更改</td>
			</tr>
			<tr>
				<th>日期</th>
				<td><input type="text" name="DateStart" class="datepickerROC" value="<{$data.sDate}>" readonly ></td>
			</tr>
			<tr>
				<th>開始時間</th>
				<td>
					
					<{html_options name=DateStartHour options=$menuHour selected=$data.sHour class="sl"}>時
					
					<{html_options name=DateStartMin options=$menuMinutes selected=$data.sMinutes class="sl"}>分
						
					
				</td>
			</tr>
			<tr>
				<th>結束時間<input type="hidden" name="id" value="<{$data.oId}>"></th>
				<td colspan="2">
					
					<{html_options name=DateEndHour options=$menuHour selected=$data.oHour class="sl"}>時
					
					<{html_options name=DateEndMin options=$menuMinutes selected=$data.oMinutes class="sl"}>分

				</td>
			</tr>

			
		</table>
		<br>
		<div>
			<div style="padding-left:30px;float:center;display:inline">
				<input type="submit" value="送出" class="btn">
			</div>
			<div style="padding-left:30px;float:center;display:inline">
				<input type="button" value="刪除" onclick="Del()" class="btn">
			</div>
			
			<input type="hidden" name="cat" value="<{$cat}>">

		</div>
		
	</form>
</center>
	
</body>
</html>
