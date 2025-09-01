<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="UTF-8">
	<title></title>
	<link rel="stylesheet" type="text/css" href="../../libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
	<script src="../../js/jquery-1.7.2.min.js"></script>

	<script type="text/javascript" src="../../libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	<script src="../../js/datepickerRoc.js"></script>
	<link rel="stylesheet" href="/css/datepickerROC.css" />
	<script type="text/javascript">
		$(document).ready(function() {
			
		});
		
		function Edit(cat,id){
			location.href='BannerBankEdit.php?cat='+cat+'&id='+id;
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
			border: 1px solid #999;
		}
		table td{
			
			padding: 5px;
			border: 1px solid #999;
		}
		input {
			padding:10px;
			border:1px solid #CCC;
		}
		textarea{
			padding:10px;
			border:1px solid #CCC;
		}
		.sp{
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
	<h1>Banner銀行</h1>
	<input type="button" value="新增" class="btn" onclick="Edit('add',<{$item.bId}>)">
	<form action="" method="POST" enctype="multipart/form-data" id="NewsForm" >
		<table cellpadding="0" cellspacing="0" width="80%">
			<tr>
				<th>銀行名稱</th>
				<th>代碼</th>
				<th>房貸名稱</th>
				<th>編輯</th>
				
			</tr>

			<{foreach from=$bank key=key item=item}>
			<tr>
				<td><{$item.bBankName}></td>
				<td><{$item.bBank}></td>
				<td><{$item.bBankName2}></td>
				<td align="center"><input type="button" value="編輯" class="btn" onclick="Edit('mod',<{$item.bId}>)"></td>
			</tr>
			<{/foreach}>
		</table>
		
		
	</form>
</center>
	
</body>
</html>
