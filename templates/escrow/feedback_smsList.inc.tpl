<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="UTF-8">
	<title>回饋金寄送名單確認</title>
	<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			 
			$("[name='date']").on('change', function() {
				$("#NewsForm").submit();
			});

		});
		function goSend(){
			var txt = '';
			// var cat = $().
			// alert("匯入中");
			$("[name='storeId[]']").each(function() {
				if ($(this).attr('checked')) {
					txt += '<option value="'+$(this).val()+'">'+$("#"+$(this).val()).text()+'</option>';
					$.ajax({
                            url: 'feedback_sms_send.php',
                            type: 'POST',
                            dataType: 'html',
                            async: false, //同步處理
                            data: {'branch':$(this).val(),'send':'2','cat':$('[name="cat"]').val(),'msg':$('[name="msg"]', window.parent.document).val()},
                        })
                        .done(function(txt2) {
                        	// console.log(txt2);
                            // alert(txt);
                            // $("#show", window.parent.document).html(txt);
                            // $(txt).insertAfter("#show", window.parent.document);
                            $("#show", window.parent.document).after(txt2);
                           
                        })      
				}
			});
			// console.log(txt);
			$('[name="branch"]', window.parent.document).html(txt);
    			parent.$.fn.colorbox.close();//關閉視窗
    			//branch
		}
	</script>
	<style>
		body{
			background-color: #F8ECE9
		}
		table th{
			
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
	<h1>回饋金寄送名單確認</h1>
	<form action="" method="POST"  id="NewsForm" > 
		<div> 
			 <{html_radios name=aa options=$menu_cat selected=$cat disabled="disabled"}>

			 <input type="hidden" name="cat" value="<{$cat}>">
		</div>

		 匯入時間:<{html_options name=date options=$menu selected=$date}> <font color="red">※預設帶最新匯入的資料</font>
		 <br>
		<div style="padding-top:15px">
			<table cellspacing="0" cellpadding="0" width="50%">
				<tr>
					<th></th>
					<th>店名稱</th>
				</tr>
				<{foreach from=$data key=key item=item}>
				<tr>
					<td><input type="checkbox" name="storeId[]" id="" value="<{$key}>" checked></td>
					<td><span id="<{$key}>"><{$item}></span></td>
				</tr>
				<{/foreach}>
			</table>
			

			
		</div>
		<br>
		<div>
			<div style="padding-left:30px;float:center;display:inline">
				<input type="button" value="確認" class="btn" onclick="goSend()">
			</div>
			<div style="padding-left:30px;float:center;display:inline">
				<!-- <input type="button" value="刪除" onclick="Del()" class="btn"> -->
			</div>
			
		</div>
		
	</form>
</center>
	
</body>
</html>
