<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="UTF-8">
	<title>禮物編輯</title>
	<script src="../js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
	<script src="/js/combobox.js"></script>
	<link href="/css/combobox.css" rel="stylesheet">
	<script type="text/javascript">
		$(document).ready(function() {
			// $('[name="scrivener"]').combobox();
			if ("<{$disabled == 1}>") {
				$(".l2").attr('disabled', 'disabled');
				$(".l2").attr('class', $(".l2").attr('class')+' input-color');

				$(".l21").attr('readonly', 'readonly');
				$(".l21").attr('class', $(".l21").attr('class')+' input-color');
				
			}

			if ("<{$disabled2 == 1}>") {
				$(".l3").attr('disabled', 'disabled');
				$(".l3").attr('class', $(".l3").attr('class')+' input-color');	
				
			}

			if ("<{$disabled3 == 1}>") {
				$(".l4").attr('disabled', 'disabled');
				$(".l4").attr('class', $(".l4").attr('class')+' input-color');
			}

			if ("<{$disabledStatus == 1}>") {
				$("[name='status']").attr('disabled', 'disabled');
				$("[name='status']").attr('class', $("[name='status']").attr('class')+' input-color');
			}

			// if ("<{$data.sStatus}>" == 2) {
			// 	$(".l2").attr('disabled', 'disabled');
			// }
		});
		function Del(id){
			if (confirm("確定要刪除嗎?")) {
				$('[name="id"]').val(id);
				$("#formDel").submit();
			}
			
		}
		

		function apply(ss){
			if (ss == 'save') {
				$("[name='ok']").val('ok');
				alert("成功");	
			}
			
			$("#NewsForm").submit();

		}
	</script>
	<style>
		body{
			background-color: #F8ECE9
		}
		table th{
			width: 20%;
			text-align: center;
			padding: 5px;
			
		}
		table td{
			
			text-align: center;
			padding: 5px;
			
		}
		input {
			padding:5px;
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
		
		.l2,.l3,.l4{
			width: 300px;
		}
		.xxx-select-2 {
			width: 180px;
			color:#666666;
			font-size:16px;
			font-weight:normal;
			background-color:#FFFFFF;
			background-image:url("../images/select_icon1.png");
			background-repeat:no-repeat;
			background-position:center right;
			background-size: 18px auto;
			text-align:left;
			height:34px;
			padding:0 23px 0 5px;
			border:1px solid #CCCCCC;
			border-radius: 0.35em;
			appearance: none;
			-webkit-appearance: none;
			-moz-appearance: none;
			-ms-appearance: none;
			-o-appearance: none;
		}
		.xxx-select-2::-ms-expand {
		    display: none;
		}

		}
		.xxx-select-2:focus {
		    border-color: rgba(82, 168, 236, 0.8) !important;
		    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
			-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
		    outline: 0 none;
		}
		.xxx-textarea {
			width: 300px;
			color:#666666;
			font-size:16px;
			font-weight:normal;
			line-height:normal;
			background-color:#FFFFFF;
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
		.input-color {	
			background-color:#e8e8e8 ;
		}
	</style>
</head>
<body>
<center>
	<h1>生日禮品</h1>
	<form action="" method="POST" enctype="multipart/form-data" id="NewsForm" >
	<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<th>編號</th>
			<td><input type="text" name="code" value="<{$data.gCode}>"></td>
		</tr>
		<tr>
			<th>名稱</th>
			<td><input type="text" name="name" value="<{$data.gName}>"></td>
		</tr>
		<tr>
			<th>金額</th>
			<td><input type="text" name="money" value="<{$data.gMoney}>"></td>
		</tr>
		<tr>
			<th>是否為助理負責</th>
			<td><{html_options name=top options=$menu_option selected=$data.sTop class="xxx-select-2"}></td>
		</tr>
		
		
		<tr>	
			<td colspan="2">
				<input type="hidden" name="ok">
				<input type="hidden" name="cat" value="<{$cat}>">
				<input type="button" value="送出" onclick="apply('save')">
			</td>
		</tr>
	</table>
		
	</form>
	
		
	
</center>
	
</body>
</html>
