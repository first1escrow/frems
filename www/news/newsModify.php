<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;

$op = $_REQUEST['op'] ;
$sn = $_REQUEST['sn'] ;
$v = $_POST ;

//設定檔案存放目錄位置
	$uploaddir = '/var/www/www.first1.com.tw/newWeb/upload/' ;
	$link = 'http://www.first1.com.tw/newWeb/upload/';

if ($v['act'] == 'del') {
	$sql = 'SELECT * FROM tNews WHERE nId="'.$v['sn'].'";' ;
	$rs = $conn->Execute($sql) ;
	
	//刪除圖片檔
	$fh = $uploaddir.'/pic/'.$rs->fields['nPicture'] ;
	if (is_file($fh)) unlink($fh) ;
	##
	
	//刪除文件檔
	$fh = $uploaddir.'/form/'.$rs->fields['nForm'] ;
	if (is_file($fh)) unlink($fh) ;
	##
	
	//刪除文章記錄
	$sql = 'DELETE FROM tNews WHERE nId="'.$v['sn'].'";' ;
	if ($conn->Execute($sql)) header('Location: newsList.php') ;
	else {
		echo "系統錯誤!!(錯誤代碼：0x000005)" ;
		exit ;
	}
	##
}

if ($v['nType']) {
	//print_r($v) ; exit ;
	
	//圖片檔處理
	$pic_name = '' ;
	
	if ($_FILES["img_file"]["name"]) {
		$pic_name = date("YmdHis").'.'.pathinfo($_FILES["img_file"]["name"],PATHINFO_EXTENSION) ;
		move_uploaded_file($_FILES["img_file"]["tmp_name"], $uploaddir."/pic/".$pic_name) ;
	}
	##
	
	//文件檔處理
	$form_name = '' ;
	
	if ($_FILES["form_file"]["name"]) {
		$form_name = date("YmdHis").'.'.pathinfo($_FILES["form_file"]["name"],PATHINFO_EXTENSION) ;
		move_uploaded_file($_FILES["form_file"]["tmp_name"], $uploaddir."/form/".$form_name) ;
	}
	##
	
	//日期修正
	if ($v['nStartDate']) {
		$tmp = explode('-',$v['nStartDate']) ;
		$v['nStartDate'] = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;
	}
	
	if ($v['nEndDate']) {
		$tmp = explode('-',$v['nEndDate']) ;
		$v['nEndDate'] = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;
	}
	##
	
	//是否採用線上報名方式
	if ($v['onLineRegist']) $v['onLineRegist'] = '1' ;
	##
	
	//修正是否保留選項
	if ($v['nShow']) $v['nShow'] = '1' ;
	##
	
	//
	if ($pic_name) $pic_name = 'nPicture="'.$pic_name.'",' ;
	if ($form_name) $form_name = 'nForm="'.$form_name.'",' ;
	
	$sql = '
		UPDATE
			tNews
		SET
			nType="'.$v['nType'].'",
			nSubject="'.$v['nSubject'].'",
			nURL="'.$v['nURL'].'",
			nContent="'.$v['nContent'].'",
			'.$pic_name.'
			'.$form_name.'
			nStartDate="'.$v['nStartDate'].'",
			nEndDate="'.$v['nEndDate'].'",
			nShow="'.$v['nShow'].'",
			nField="'.$v['nfield'].'",
			nOnlieRegist="'.$v['onLineRegist'].'"
		WHERE
			nId="'.$sn.'"
	;' ;
	
	if ($conn->Execute($sql)) {
		
	}
	##
}

//
$list = array() ;
$sql = 'SELECT * FROM tNews WHERE nId="'.$sn.'";' ;
$rs = $conn->Execute($sql) ;
$list = $rs->fields ;

if ($list['nStartDate'] != '0000-00-00 00:00:00') {
	$_arr = array() ;
	$_arr = explode('-',substr($list['nStartDate'],0,10)) ;
	$list['nStartDate'] = ($_arr[0] - 1911).'-'.$_arr[1].'-'.$_arr[2] ;
	unset($_arr) ;
}
else $list['nStartDate'] = '' ;

if ($list['nEndDate'] != '0000-00-00 00:00:00') {
	$_arr = array() ;
	$_arr = explode('-',substr($list['nEndDate'],0,10)) ;
	$list['nEndDate'] = ($_arr[0] - 1911).'-'.$_arr[1].'-'.$_arr[2] ;
	unset($_arr) ;
}
else $list['nEndDate'] = '' ;
##
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>增修新聞活動</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="/js/datepickerRoc.js"></script>
<script type="text/javascript" src="/js/ROCcalender.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#dialog').dialog({
		autoOpen: false,
		width: 260,
		height: 298
	}) ;
	
	$('#dialog1').dialog({
		autoOpen: false,
		width: 300,
		height: 280
	}) ;
	
	$('#c<?=$list['nType']?>').css('background-color','#00FF00') ;
	setrTitle(<?=$list['nType']?>) ;
	
	/*$('.mClass').click(function() {
		var cid = $(this).prop('id') ;
		var myid = cid.substr(1) ;
		
		setrTitle(myid) ;
		
		$('.mClass').each(function() {
			if ($(this).prop('id') == cid) {
				$(this).css('background-color','#00FF00') ;
				$('[name="nType"]').val(myid) ;
			}
			else {
				$(this).css('background-color','') ;
			}
		}) ;
	}) ;*/
	
	$('#nURL').keyup(function() {
		var str = $(this).val() ;
		
		if (str != '') {
			$('.lmt').each (function() {
				$(this).val('') ;
				$(this).prop('disabled',true) ;
			}) ;
		}
		else {
			$('.lmt').each (function() {
				$(this).prop('disabled',false) ;
			}) ;
		}
	}) ;
}) ;

function update() {
	$('#dialog').dialog('open') ;
	$('#myform').submit() ;
}

function del() {
	if (confirm('是否確定刪除本文章!?') == true) {
		$('[name="act"]').val('del') ;
		$('#myform').submit() ;
	}
	else {
		return false ;
	}
}

function goback() {
	var url = 'newsList.php' ;
	
	$('#myform').prop('action',url) ;
	$('#myform').submit() ;
}

function dela(fn, sn) {
	if (confirm('確定刪除本附件!?') == true) {
		$.post('newDelAttach.php',{'sn':sn, 'fn':fn}, function(txt) {
			if (txt == 'OK') {
				location = 'newsModify.php?op=m&sn=' + sn ;
			}
		}) ;
	}
}

function previewPic(pic) {
	$('#dialog1').html('<img style="width:280px;height:220px;" src="<?=$link?>pic/' + pic +'">') ;
	$('#dialog1').dialog('open') ;
}

function closePic() {
	$('#dialog1').dialog('close') ;
	$('#dialog1').html('') ;
}

function setrTitle(no) {
	if (no == '4') {
		$('#f1').html('課程名稱：') ;
		$('#f2').html('相關連結：') ;
		$('#f3').html('課程期間：') ;
		$('#f4').html('課程內容：') ;
		
		$('#onLineR').css('display','') ;
		$('#f5').css('display','') ;

	}
	else if (no == '3') {
		$('#f1').html('主題：') ;
		$('#f2').html('文章連結：') ;
		$('#f3').html('刊登期間：') ;
		$('#f4').html('內文：') ;
		
		$('#onLineR').css('display','none') ;
		$('#f5').css('display','none') ;
		$('#onLineRegist').prop('checked',false) ;
	}
	else if (no == '2') {
		$('#f1').html('問題：') ;
		$('#f2').html('文章連結：') ;
		$('#f3').html('刊登期間：') ;
		$('#f4').html('答覆：') ;
		
		$('#onLineR').css('display','none') ;
		$('#f5').css('display','none') ;
		$('#onLineRegist').prop('checked',false) ;
	}
	else {
		$('#f1').html('主題／問題：') ;
		$('#f2').html('文章連結：') ;
		$('#f3').html('刊登期間：') ;
		$('#f4').html('內文／答覆：') ;
		
		$('#onLineR').css('display','none') ;
		$('#f5').css('display','none') ;
		$('#onLineRegist').prop('checked',false) ;
	}
}
function joinField() {
	var newfield = $('#fieldAdd').val() ;
	var nfieldJson = $('[name="nfield"]').val() ;
	var nfieldCount = $('[name="fieldCount"]').val();
	
	if (nfieldJson != '') nfieldJson = nfieldJson + ',' + newfield + ':' +nfieldCount ;
	else nfieldJson = newfield + ':' +nfieldCount  ;
	
	$('[name="nfield"]').val(nfieldJson) ;
	transferField(nfieldJson) ;
	
	$('#fieldAdd').val('') ;
	$('[name="fieldCount"]').val('') ;
}

function transferField(nfieldJson) {
	var line = '' ;	
	var arr = nfieldJson.split(',') ;
	
	for (var i = 0 ; i < arr.length ; i ++) {
		if (line != '') line = line + '<br>' ;
		var myId = 'tf' + i ;
		line = line + '<span id="' + myId + '" alt="' + arr[i] +'" style="background-color:#CCC"><a href="#" onclick="removetf(' + i + ')" >Ｘ</a>' + arr[i] + '</span>' ;
	}
	
	$('#field2').html(line) ;
}

function removetf(id)
{
	var myId = 'tf' + id ;
	
	var nfieldJson = $('[name="nfield"]').val() ;
	var arr = nfieldJson.split(',') ;
	var val = $("#"+myId).attr('alt');
	var line = '';

	for (var i = 0; i < arr.length; i++) {
		if (arr[i] != val) {
			
			if (line != '') line = line + ',' + arr[i] ;
			else line = arr[i] ;
		}
		
	};

	$('[name="nfield"]').val(line);
	$("#"+myId).remove();

}
</script>
<style>
input {
	/* background-color: #FFFFFD ;*/
}

body {
	/*font-family: 標楷體;
	font-size: 11pt;*/
	font-family: "微軟正黑體", serif;
}

#tbl .tdH {
	text-align: center;
	font-weight: bold;
	background-color: #FFB6C1;
}

#tbl td {
	text-align: left;
	padding: 5px;
}

.mClass {
	float:left;
	margin:2px;
	border:1px solid #ccc;
	width:150px;
	height:30px;
	text-align:center;
	padding-top: 10px;
}
</style>
</head>
<body style="background-color:#F8ECE9;">

<form method="POST" name="myform" id="myform" enctype="multipart/form-data">
	<input type="hidden" name="sn" value="<?=$sn?>">
	<input type="hidden" name="act" value="">
	<div id="show"></div>
	<div id="dialog" title="檔案上傳中..."><img src="/images/wait_1.gif"></div>
	<div id="dialog1" title='圖片預覽'></div>

	<center>

	<div style="width:700px;text-align:left;">
		<div style="margin-top:50px;font-size:14pt;">
			<div style="float:left;width:120px;">主類別：<input type="hidden" name="nType" value="<?=$list['nType']?>"></div>
			
			<!-- <div class="mClass" id="c1">房事新聞</div> -->
			<div class="mClass" id="c2">Q & A</div>
			<div class="mClass" id="c3">文章活動</div>
			<div class="mClass" id="c4">課程報名</div>
			
			<div style="clear:both;"></div>
		</div>
		
		<hr>
		
		<div style="margin-top:20px;">
			<div style="float:left;width:120px;"><span id="f1"></span></div>
			
			<div style="float:left;">
				<input type="text" style="width:565px;" name="nSubject" value="<?=$list['nSubject']?>">
			</div>
			
			<div style="clear:both;"></div>
		</div>
		
		<div style="margin-top:20px;">
			<div style="float:left;width:120px;"><span id="f2"></span></div>
			
			<div style="float:left;">
				<input type="text" style="width:565px;" id="nURL" name="nURL" value="<?=$list['nURL']?>">
			</div>
			
			<div style="clear:both;"></div>
		</div>
		
		<div style="margin-top:10px;">
			<div style="float:left;width:120px;"><span id="f3"></span></div>
			
			<div style="float:left;">
				<input type="text" style="width:100px;" name="nStartDate" value="<?=$list['nStartDate']?>" class="datepickerROC">
			</div>
			
			<div style="float:left;margin:0 20px 0 20px;">
				<font style="font-size:10pt;">到</font>
			</div>
			
			<div style="float:left;">
				<input type="text" style="width:100px;" name="nEndDate" value="<?=$list['nEndDate']?>" class="datepickerROC">
			</div>
			
			<div style="margin-left:20px;float:left;">
				<input type="checkbox" name="nShow"<?php if ($list['nShow']) echo ' checked="checked"' ;?> id="nShow">
				<label for="nShow" style="font-size:10pt;">活動結束後是否保留</label>
			</div>
			
			<div style="clear:both;"></div>
		</div>
		
		<div style="margin-top:10px;">
			<div style="float:left;width:120px;">附件：</div>
			
			<div style="float:center;font-size: 10pt;">
				<div style="float:left;">圖片：</div>
<?php
if ($list['nPicture']) {
	echo '
		<div style="float:left;">
			<img src="/images/attach_3.png" title="'.$list['nPicture'].'" onmouseover="previewPic(\''.$list['nPicture'].'\');return true;" onmouseout="closePic()";return true;">
			<span style="cursor:pointer;" onclick="dela(\'p\',\''.$sn.'\')">X</span>
		</div>
	' ;
}
else {
?>
				<div style="float:left;"><input type="file" class="lmt" name="img_file"><span style="margin-left:10px;">(JPG, GIF, BMP 格式、280px * 220px、2MB 以內)</span></div>
<?php
}
?>
				<div style="clear:both;"></div>
			</div>
			
			<div style="clear:both;"></div>
		</div>
			
		<div style="margin-top:10px;">
			<div style="float:left;width:120px;">&nbsp;</div>
			
			<div style="float:left;font-size: 10pt;">
				<div style="float:left;">表單：</div>
<?php
if ($list['nForm']) {
	echo '
		<div style="float:left;">
			<img src="/images/attach_3.png" title="'.$list['nForm'].'">
			<span style="cursor:pointer;" onclick="dela(\'f\',\''.$sn.'\')">X</span>
		</div>
	' ;
}
else {
?>
				<div style="float:left;"><input type="file" class="lmt" name="form_file"><span style="margin-left:10px;">(文件檔、圖檔、2MB 以內)</span></div>
<?php
}
?>
				<div style="float:left;padding-left:20px;" id="onLineR">
				<input type="checkbox" name="onLineRegist" id="onLineRegist" <?php if ($list['nOnlieRegist']) echo ' checked="checked"' ;?>>
				<label for="onLineRegist" style="font-size:10pt;">採線上報名</label>
				</div>
				<div style="clear:both;"></div>
			</div>
			
			<div style="clear:both;"></div>
		</div>
		<div style="margin-top:10px;" id="f5">
			<div style="float:left;width:120px;">場次：</div>
			
			<div id="field1" style="float:left;font-size: 10pt;padding-right:10px;">
					名稱：<input type="text" id="fieldAdd" style="width:150px;">
					人數：<input type="text" name="fieldCount" style="width:100px;" />
					<input type="button" value="加入" onclick="joinField()">
					<input type="hidden" name="nfield" value="<?=$list['nField']?>" >
				
				<!-- <span id="' + myId + '" alt="' + arr[i] +'" style="background-color:#CCC"><a href="#" onclick="removetf(' + i + ')" >Ｘ</a>' + arr[i] + '</span> -->
			</div>

			<div style="clear:both;"></div>
		</div>
		<div style="margin-top:10px;" id="f5">

				<div id="field2" style="padding-left:120px;font-size: 10pt;">
				<?php
				if ($list['nField']) {
					$tmp = explode(',', $list['nField']);
					for ($i=0; $i < count($tmp); $i++) { 

						echo '<span id="tf'.$i.'" alt="'.$tmp[$i].'" style="background-color:#CCC"><a href="#" onclick="removetf('.$i.')" >Ｘ</a>'.$tmp[$i].'</span><br>' ;
				
					}
					unset($tmp);
				}
					
				?>
					
				</div>

		</div>
		
		<div style="margin-top:10px;">
			<div style="float:left;width:120px;"><span id="f4"></span></div>
			
			<div style="float:left;">
				<span style="margin-top:10px;font-size:9pt;color:#000080;">
				* 文章內如有需要引用超聯結，請以&lt;a&gt;xxx&lt;/a&gt;來表示。例：&lt;a&gt;http://www.first1.com.tw&lt;/a&gt;
				</span><br>
				<textarea class="lmt" name="nContent" rows="15" cols="60"><?=$list['nContent']?></textarea>
			</div>
			
			<div style="clear:both;"></div>
		</div>
		
		<div style="width:100%;margin-top:20px;">
			<center>
				<input type="button" value="修改" onclick="update()">
				<input type="button" value="刪除" onclick="del()">
				<input type="button" value="列表" onclick="goback()">
			</center>
		</div>
	</div>
</form>

</center>
</body>
</html>
