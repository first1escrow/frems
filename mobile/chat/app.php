<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
// require_once dirname(dirname(dirname(__FILE__))).'/openadodbAPI.php' ;
// require_once dirname(dirname(__FILE__)).'/api_function.php' ;
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;

$_REQUEST = escapeStr($_REQUEST) ;

$target = $_REQUEST['target'] ;
$from = $_REQUEST['from'] ;
if (empty($from)) $from = 'app' ;


$sql = 'SELECT * FROM tScrivener WHERE sUndertaker1 = "12" AND sStatus = "1" ORDER BY sId ASC;' ;

$rs = $conn->Execute($sql) ;

$scr = array() ;
while (!$rs->EOF) {
	$scr[] = $rs->fields ;
	$rs->MoveNext() ;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server"><meta http-equiv="cache-control" content="no-store"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>TEST</title>
	<link rel="stylesheet" href="../../css/jquery-ui.css">
	<script src="../../js/jquery.js"></script>
	<script src="../../js/jquery-ui.js"></script>
	<script src="masonry.js"></script>
	<script language="Javascript">
	var  i = 1 ;
	$(function() {
		$('[name="content"]').focus() ;
		getChat('');
		// $('#msg').masonry({
		//   // options
		//   itemSelector: '.now',
		//   columnWidth: 200
		// });

		// setInterval("getChat()", 1000) ;
		
		$("#msg").scroll(function() {
	        if ( $(this).scrollTop() == 0){
	            // $('#gotop').fadeIn("fast");
	            // var loc = $(".now:first").attr("id"); //$(selector).height()
	            
	            // var date = $(".pre:first").val();//class="pre"
	            // if (date == '') {
	            // 	date = 1;
	            // }
	            // console.log(date);
	            var date = $("[name='pday']").val();//class="pre"
	            console.log(date);
	            getChat(date);
	           
	            // // $('#'+loc).focus() ;
	            // // $(this).scrollTop(-100)scrollTop: 
	            // $("#"+loc).fadeIn( "slow" );
	             // var tmp = $("#"+loc).offset();
	             // console.log(tmp);
	             // $(this).scrollTop(tmp.top);
	        } else {
	            // $('#gotop').stop().fadeOut("fast");
	           
	        }
	    });


	}) ;
	
	function getChat(day) {
		var urls = 'checkMsg.php' ;
		$.ajax({
			url: urls,
			type: 'POST',
			dataType: 'text',
			data: 'acc='+$('[name="target"]').val()+'&ide='+$('[name="from"]').val()+'&day='+day,
			success: function(txt) {
				// console.log(txt);
				var ch = txt.substr(0,1) ;
				txt = txt.substr(1) ;
				//alert(ch) ;
				if (ch == '2') {
					//有新訊息
					i = 1 ;
				}
				if (day == '') {
					$('#msg').empty().html(txt) ;
				}else{
					$(txt).insertBefore('.now') ;
				}
				
				if (i == 1) {
					var elem = document.getElementById('msg');
					elem.scrollTop = elem.scrollHeight;
					i = 2 ;
				}
			},
			//error: function(xhr, ajaxOptions, thrownError) {
			//	alert(xhr.status) ;
			//	alert(thrownError) ;
			//}
		}) ;
	}
	
	function setIndex() {
		i = 1 ;
	}
	</script>
	<style>
		#msg{
			width:600px;height:250px;border:1px solid #ccc;padding:5px;overflow:scroll;
		}
	</style>
</head>
<body>
<div id="msg" >

</div>
<div style="width:600px;text-align:right;">
	<form method="POST" enctype="multipart/form-data" action="processAPP.php">
		<p>
		發訊身分：
		<select name="from">
			<option value="app"<?php echo $from == 'app' ? 'selected="selected"' : '' ;?>>APP</option>
			<option value="first1"<?php echo $from == 'first1' ? 'selected="selected"' : '' ;?>>後台</option>
		</select>
		　　　
		收訊對象：
		<select name="target" onchange="setIndex()">
	<?php
	foreach ($scr as $k => $v) {
		echo '<option value="SC'.str_pad($v['sId'],4,'0',STR_PAD_LEFT).'"' ;
		echo ('SC'.str_pad($v['sId'],4,'0',STR_PAD_LEFT) == $target) ? ' selected="selected"' : '' ;
		echo '>'.$v['sName']."</option>\n" ;
	}
	?>
		</select>
		</p>
		<p>請選擇上傳檔案：<input type="file" name="appfile"></p>
		<p>請輸入訊息：<input type="text" name="content" style="width:246px;"></p>
		<p>
			<input type="submit" value="Enter">
		</p>
	</form>
</div>
</body>
</html>
