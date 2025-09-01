<?php
include('../web_addr.php') ;
include('../openadodb.php') ;
include_once '../session_check.php' ;
 
//if ($_SESSION["member_id"] != '1' and $_SESSION["member_id"] != '5' ) { 
if ($_SESSION["member_bankcheck"] != '1') {
	echo '
	<script>
	alert("您無此功能使用權限!!") ;
	</script>
	' ;
	//header('Location: /bank/new/out.php') ;
	exit ;
}

//合約銀行資料
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId DESC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$conBank[] = $rs->fields ;
	$rs->MoveNext() ;
}
unset($rs) ;
##
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>出帳建檔列表</title>
<link rel="stylesheet" href="/css/colorbox.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="/js/jquery.colorbox.js"></script>

<script>

$(document).ready(function(){
				//Examples of how to assign the ColorBox event to elements
				$(".group1").colorbox({rel:'group1'});
				$(".group2").colorbox({rel:'group2', transition:"fade"});
				$(".group3").colorbox({rel:'group3', transition:"none", width:"75%", height:"75%"});
				$(".group4").colorbox({rel:'group4', slideshow:true});
				$(".ajax").colorbox();
				$(".youtube").colorbox({iframe:true, innerWidth:425, innerHeight:344});
				$(".iframe").colorbox({
					iframe:true, width:"1100", height:"500",					
					onClosed:function(){ reload_page(); }
				});
				$(".inline").colorbox({inline:true, width:"50%"});
				$(".callbacks").colorbox({
					onOpen:function(){ alert('onOpen: colorbox is about to open'); },
					onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
					onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
					onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
					onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
				});
				
				//Example of preserving a JavaScript event for inline calls.
				$("#click").click(function(){ 
				$('#click').css({"background-color":"#f00", "color":"#fff", "cursor":"inherit"}).text("Open this window again and this message will still be here.");
					return false;
				});
				$("#export_file").click(function() {
					$.colorbox({	
						iframe:true, width:"1100", height:"500",						
						href: "http://first.twhg.com.tw/bank/_export_all.php?x=<?php echo $b;?>&y=<?php echo $export;?>",
						onClosed:function(){ reload_page(); }
					});
				})
});
function open_w(url){	
	window.open (url , 'newwindow', 'height=500, width=1100, top=250, left=250, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')	
}
function reload_page(){
	location.reload();
}
</script>
</head>

<body>
<div style="width:1024px; margin-bottom:5px; height:22px; background-color: #CCC">
<?php
//<div style="float:left;margin-left: 10px;"> <a href="http://first.twhg.com.tw/bank/new/out.php">建檔</a> </div>
?>
<div style="float:left;margin-left: 10px;"> <a href="<?=$web_addr?>/bank/list2.php">待修改資料</a> </div>
<?php
//if ($_SESSION["member_id"] != '1' and $_SESSION["member_id"] != '5' ) { 
if ($_SESSION["member_bankcheck"] == '1') { //個別權限顯示
?>
<div style="float:left; margin-left: 10px;"> <font color=red><strong>未審核列表</strong></font></div>
<div style="float:left; margin-left: 10px;"> <a href="<?=$web_addr?>/bank/list_ok.php">已審核列表</a></div>
<?php } ?>
</div>
<div>
<table width="1016" border="0" cellpadding="1" cellspacing="1" class="font12" id="ttt">
    <tr>
      <td colspan="5">待出帳(審核)列表</td>
    </tr>   
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
<?php
for ($i = 0 ; $i < count($conBank) ; $i ++) {
	$bank = $conBank[$i]['cBankFullName'] ;
	if ($conBank[$i]['cBankMain']=='807') {
		$bank .= '('.$conBank[$i]['cBranchName'].')' ;
	}
	
	echo '
	<tr>
		<td colspan="5" style="font-weight:bold;color:#002060;">～～～&nbsp;'.$bank.'案件&nbsp;～～～</td>
	</tr>
	' ;
	
	//合約銀行案件列表
	$sql = '
		SELECT 
			tVR_Code,
			SUM(tMoney) as Total,
			COUNT(tVR_Code) as C, 
			tDate,
			tBank_kind 
		FROM 
			tBankTrans 
		WHERE 
			tOK<>"1" 
			AND tVR_Code LIKE "'.$conBank[$i]['cBankVR'].'%"
		GROUP BY 
			tVR_Code
	' ;
	echo $sql."<br>";
	$rs = $conn->Execute($sql);	
	$_error =0;
	while( !$rs->EOF ) {
		echo '
		<tr>
			<td>
			專屬帳號 <a class="iframe" href="'.$web_addr.'/bank/check_out.php?vr_code='.$rs->fields["tVR_Code"].'"><strong>'.$rs->fields["tVR_Code"].'</strong></a>
			
		' ;
	
		$_tVR = $rs->fields['tVR_Code'] ;
		
		if (preg_match("/96988000000008/",$_tVR)) {
			echo '(利息)' ;		//台新利息
		}
		else if (preg_match("/99985000000000/",$_tVR)) {
			echo '(利息)' ;		//永豐西門利息
		}
		else if (preg_match("/99986000000000/",$_tVR)) {
			echo '(利息)' ;		//永豐城中利息
		}
		//else {
		//	echo $rs->fields["tBank_kind"] ;
		//}
		
		echo '
			
			</td>
			<td width="251">待出帳總金額 '.$rs->fields["Total"].' 元</td>
			<td width="163">筆數 '.$rs->fields["C"].'</td>
			<td>建檔時間 '.$rs->fields["tDate"].'</td>
			<td width="34">&nbsp;</td>
		</tr>        
		<tr>
			<td height="19" colspan="5"><hr /></td>
		</tr>
		' ;
    
		$rs->MoveNext();
	} 
	?>
    <tr>
      <td colspan="5">&nbsp;</td>
    </tr>
<?php
}
?>

  </table>
</div>
</body>
</html>
