<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	<meta charset="UTF-8">
	<title>問卷編輯</title>
	<script src="../js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
	<link rel="stylesheet" href="../../css/datepickerROC.css" />
	<script type="text/javascript" src="../../js/datepickerRoc.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			
		});

		function add(){
			var count = parseInt($(".question-q").length);
			var no = count+1;
			var clonedRow = $(".question-q:last").clone(true).attr("id",'que'+no); 

			clonedRow.find('[type*="text"]').val('');
			clonedRow.find('#delQ').attr({
		   		onclick: 'del('+no+')',
		   		style: ''
		   	});
		    // clonedRow.find('[name*="question[]"]').attr('placeholder', '問題'+no);
		    clonedRow.find('[name*="itmeQ'+count+'[]"]').attr('name', 'itmeQ'+no+'[]');
		    clonedRow.find('[name*="itemQ'+count+'Score[]"]').attr('name', 'itmeQ'+no+'Score[]');
		    clonedRow.find('#aws').attr('onclick', 'add2('+no+')');
		    // console.log('qItem'+no+'1');
		    clonedRow.find('.qItem'+count).each(function() {
		    	if ($(this).attr('id') != 'qItem'+count+'1') {
		    		$(this).remove();
		    	}
		    });

		    clonedRow.find('#delA').attr({
				onclick: 'del2('+no+',1)'
			});

		    clonedRow.find('.qItem'+count).attr({
		    	id: 'qItem'+no+'1',
		    	class:'qItem'+no,
		    });
			

		   	clonedRow.insertAfter('.question-q:last');
		}
		function add2(id) {
			//qitem1
			var count = parseInt($(".qItem"+id).length);
			var no = count+1;
			var clonedRow = $(".qItem"+id+":last").clone(true).attr({
				id: 'qItem'+id+no,
				class: 'qItem'+id
			}); 

			clonedRow.find('[type*="text"]').val('');
    
			// clonedRow
			clonedRow.find('#delA').attr({
				onclick: 'del2('+id+','+no+')',
				style: ''
			});

			clonedRow.insertAfter(".qItem"+id+":last");
		}
		function del(id){
			if (id != 1) {
				if (confirm("確定要刪除嗎?")) {
					$("#que"+id).remove();
				}
			}else{
				alert("已經是最後一個，禁止刪除");
			}	
		}
		function del2(id,id2){
			// console.log("#qItme"+id+id2);
					
			if (id2 != 1 ) {
				if (confirm("確定要刪除嗎?")) {
					$("#qItme"+id+id2).remove();
				}
			}else{

			}
		}
		function view(str){
			window.open("http://www.first1.com.tw/line/question/Questionaire.php?v="+str+"&cat=view");
		}
		function send(id){
			$.ajax({
				url: 'QustionSend.php',
				type: 'POST',
				dataType: 'html',
				data: {id: id},
			})
			.done(function(msg) {
				console.log(msg);
			});
			
		}
		function analysis(id){
			$('[name="form"]').attr('action', 'QuestionAnalysis.php');
			$('[name="form"]').submit();
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
		.question{
			margin: 10px 10px 10px 10px ;
		}
		.question-title, .question-textbox, .question-ctrl{
			margin-top: 5px;
			margin-bottom: 5px;
		}
		
		.question-content{
			margin-top: 5px;
			margin-bottom: 5px;
		}
		.question-q{
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

		input[type="radio"]{
			width:20px;
			height:20px;
			margin:0px 4px 0 0;
			vertical-align:-4px;
		}
		input[type="checkbox"]{
			width:20px;
			height:20px;
			margin:0px 4px 0 0;
			vertical-align:-4px;
		}
		
	</style>
</head>
<body>
<h1>問卷編輯</h1>
<div class="question">
	<form action="" name="form" method="POST">
		<input type="hidden" name="id" value="<{$data.qId}>">
	</form>
	<form action="" method="POST" id="NewsForm" >
		<div class="question-title">
			<div class="question-textbox">問卷名稱:<input type="text" name="name" value="<{$data.qName}>" placeholder="問卷名稱" class="xxx-input" style="width: 50%"></div>
			<div class="question-textbox">
				調查時間:
				<input type="text" name="sDate" value="<{$data.qDateStart}>" placeholder="" class="xxx-input datepickerROC">至
				<input type="text" name="eDate" value="<{$data.qDateEnd}>" placeholder="" class="xxx-input datepickerROC"> 
			</div>
			<div class="question-textbox">
				發送時間: 
				<{html_radios name=sendMethod options=$sendMenu selected=$data.qSend}>
			</div>
			<div class="question-textbox">
				發送對象:
				<{html_checkboxes name=sendIden options=$sendIdenMenu selected=$data.qSendIden}>
			</div>
			
			<div  class="question-textbox"> <hr> </div>
		</div>
		<div class="question-content">
			<div class="question-ctrl"><input type="button" value="新增問題" onclick="add()"></div>
			 <{assign var='index' value='0'}> 
			<{foreach from=$data.qContent key=key item=item}>
			<div class="question-q" id="que<{$index++}>">
				<div>
					<input type="text" name="question[]" value="<{$item.question}>" placeholder="問題描述" class="xxx-input" style="width: 80%">

					<select name="qCategory[]" id="" class="xxx-input"><option value="1" selected="selected">單選</option></select>

					<input type="button" value="刪除" id="delQ" onclick="del(<{$index}>)" style="display: none">
				</div>
				<div class="question-ctrl">
					<input type="button" id="aws" value="新增選項" onclick="add2(<{$index}>)">
				</div>
				<div>
					<{foreach from=$item.item key=k item=detail}>
					<div class="qItem<{$index}>" id="qItem<{$index}><{$k}>" style="margin-bottom: 2px;">
						<!-- <input type="radio" id="radio11" disabled="disabled"> -->
						<input type="text" name="itmeQ<{$index}>[]" class="xxx-input" placeholder="選項描述" value="<{$detail}>" style="width: 40%">
						<input type="text" name="itemQ<{$index}>Score[]" id="" placeholder="分數" class="xxx-input" value="<{$item.itemScore[$k]}>">
						&nbsp;&nbsp;<input type="button" value="刪除" id="delA" onclick="del2(<{$index}>,<{$k}>)"  style="display: none">
					</div>
					<{/foreach}>	
				</div>
			</div>
			<{/foreach}>
			<div class="question-q" id="que<{($data.qContent|count)+1}>">
				<div>
					<input type="text" name="question[]" value="" placeholder="問題描述" class="xxx-input" style="width: 80%">

					<select name="qCategory[]" id="" class="xxx-input"><option value="1" selected="selected">單選</option></select>

					<input type="button" value="刪除" id="delQ" onclick="del(<{($data.qContent|count)+1}>)" style="display: none">
				</div>
				<div class="question-ctrl">
					<input type="button" id="aws" value="新增選項" onclick="add2(<{($data.qContent|count)+1}>)">
				</div>
				<div>
					<div class="qItem<{($data.qContent|count)+1}>" id="qItem<{($data.qContent|count)+1}>1" style="margin-bottom: 2px;">
						<input type="text" name="itmeQ1[]" class="xxx-input" placeholder="選項描述" value="" style="width: 40%">
						<input type="text" name="itemQ1Score[]" id="" placeholder="分數" class="xxx-input">
						&nbsp;&nbsp;<input type="button" value="刪除" id="delA" onclick="del2(<{($data.qContent|count)+1}>,1)"  style="display: none">
						
					</div>
				</div>
			</div>
		</div>
		
		<br>
		<center>
		<div>
			<div style="padding-left:30px;float:center;display:inline;text-align: center;width: 100%">
				<input type="submit" value="送出" class="btn">
				<{if $cat == 2}>
				<input type="button" value="瀏覽" onclick="view('<{$data.token}>')" class="btn">
				<input type="button" value="統計" onclick="analysis('<{$data.qId}>')" class="btn">
				<input type="button" value="發送問卷" class="btn" onclick="send('<{$data.qId}>')">
				<{/if}>
			</div>
			
			<input type="hidden" name="id" value="<{$data.qId}>">
			<input type="hidden" name="cat" value="<{$cat}>">

		</div>
		</center>
	</form>
</div>
	

	
</body>
</html>
