<?php
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$today = date("Y-m-d") ;

//合約銀行基本資料

$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cBankMain,cId DESC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$str = $rs->fields['cBankName'].$rs->fields['cBranchName'] ;
			
			
	$conBank[substr($rs->fields['cBankVR'],0,5)] = $str ;
	$rs->MoveNext() ;	
}


unset($rs) ;unset($str);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>銀行付款確認</title>
<link rel="stylesheet" href="/css/colorbox.css" />
<script src="/js/jquery-1.7.2.min.js"></script>
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
				$(".iframe2").colorbox({
					iframe:true, width:"1000", height:"900"					

				});
				$(".iframe3").colorbox({
					iframe:true, width:"450", height:"500"					

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
						href: "/bank/_export_all.php?x=<?php echo $b;?>&y=<?php echo $export;?>",
						onClosed:function(){ reload_page(); }
					});
				});


				$.ajax({
					url: 'bank_check.php',
					dataType: 'html'
				})
				.done(function(txt) {
					$("#bank_c").html(txt);
				});
				
});
function reload_page(){
	location.reload();
}
function open_w(url){	
	window.open (url , 'newwindow', 'height=500, width=1100, top=250, left=250, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')	
}
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
function open_w_all(x,y){	
	// x -> bank , y -> export
	url = '/bank/_export_all.php?x=' + x  + "&y=" + y;
	window.open (url , 'newwindow2', 'height=600, width=1100, top=150, left=350, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')	
}
</script>
<style>
li {
	height:20px;
}
#bank_c{
	height: 170px;
	/*border: 1px solid #999;*/
	width: 1024px;
}
</style>
</head>

<body>
<div style="width:1024px; margin-bottom:5px; height:22px; background-color: #CCC">
<div style="float:left;margin-left: 10px;"> <a href="instructions/IBookList.php">指示書</a> </div>
<div style="float:left;margin-left: 10px;"> <a href="/bank/list2.php">待修改資料</a> </div>
<?php
if ($_SESSION["member_id"] == '6' || $_SESSION["member_pDep"] == 5) { //個別權限顯示
?>
<div style="float:left;margin-left: 10px;"> <a href="/bank/BankTransProcess.php">出款進度</a> </div>
<?php } ?>
<?php
if ($_SESSION["member_bankcheck"] == '1') { //個別權限顯示
?>
<div style="float:left; margin-left: 10px;"> <a href="/bank/list.php">未審核列表</a></div>
<div style="float:left; margin-left: 10px;"> <a href="/bank/list_ok.php">已審核列表</a></div>
<div style="float:left; margin-left: 10px;"> <a href="/bank/pay_check.php">銀行出款確認</a></div>

<?php
if ($_SESSION["pBankBook"] != 0 && $_SESSION["member_id"] != 69) {?>
	<div style="float:left;margin-left: 10px;"> <a href="instructions/IBookManagerList.php">指示書列表</a> </div>
<?php }
?>
<div style="float:left; margin-left: 10px;"><a href="/bank/returnMoneyList.php">返還代墊列表</a></div>
<div style="float:left; margin-left: 10px;"> <font color=red><strong>簡訊發送</strong></font></div>
<div style="float:left; margin-left: 10px;"> 【<a class='iframe2' href="/bank/report/report.php">銀行對帳單</a>】</div>
<?php } ?>
</div>
<div>
<div id="bank_c">
<div style="text-align:center;line-height:100px">資料計算中...</div>
	
</div>
<div style="overflow:auto; height:500px;border: 1px solid #666;background-color: #CCC;padding: 8px; width:1024px">
<ul>
<?php
$sql = '
	SELECT 
		tPayOk,
		tExport_nu,
		SUM(tMoney) as M,
		tExport_time,
		tVR_Code,
		tStep1Name,
		tStep2Name

	FROM 
		tBankTrans 
	WHERE 
		tExport="1"
		AND tSend !=1 
		AND (tObjKind2 != "02" AND tObjKind2 != "04")
		AND tBankLoansDate>="'.date("Y-m-d",strtotime('-30 day')).'"
	GROUP BY 
		tExport_nu 
	ORDER BY 
		tExport_time 
	DESC ;
' ;
$rs = $conn->Execute($sql);
$_total = $rs->RecordCount();

while(!$rs->EOF) { 
	if($rs->fields['tPayOk']=='1') {
		$rs->fields['tPayOk'] = '' ;
	}
	else {
		$rs->fields['tPayOk'] = '、尚有未出款確認案件' ;
	}
	
	// for ($i = 0 ; $i < count($conBank) ; $i ++) {
	// 	$vr = $conBank[$i]['cBankVR'] ;
	// 	if (preg_match("/^$vr/",$rs->fields['tVR_Code'])) {
	// 		$str = $conBank[$i]['cBankName'] ;
			
	// 		if ($conBank[$i]['cBankMain'] == '807') {
	// 			$str .= $conBank[$i]['cBranchName'] ;
	// 		}
			
	// 		break ;
	// 	}
	// }
	
	

	if ($rs->fields['tStep1Name'] > 0 && $rs->fields['tStep2Name'] == 0) {//第一審核 但是尚未第二審核
		echo '
		<li style="background-color:white;">
			<a class="iframe" href="_sms_send.php?sn='.$rs->fields["tExport_nu"].'&ts='.$rs->fields["tExport_time"].'&tm='.$rs->fields["M"].'">
			<strong>媒體檔匯出時間: </strong>'.$rs->fields["tExport_time"].'  <strong>出帳金額:</strong> '.number_format($rs->fields["M"]).' 元&nbsp;
			('.$conBank[substr($rs->fields['tVR_Code'], 0,5)].$rs->fields['tPayOk'].')&nbsp;指示書編號:'.getBookId($rs->fields["tExport_nu"]).'.</a>
		</li>
		' ;
	}else{
		echo '
		<li>
			<a class="iframe" href="_sms_send.php?sn='.$rs->fields["tExport_nu"].'&ts='.$rs->fields["tExport_time"].'&tm='.$rs->fields["M"].'">
			<strong>媒體檔匯出時間: </strong>'.$rs->fields["tExport_time"].'  <strong>出帳金額:</strong> '.number_format($rs->fields["M"]).' 元&nbsp;
			('.$conBank[substr($rs->fields['tVR_Code'], 0,5)].$rs->fields['tPayOk'].')&nbsp;指示書編號:'.getBookId($rs->fields["tExport_nu"]).'.</a>
		</li>
		' ;
	}
	
	
	$rs->MoveNext() ; 
}

function getBookId($nu){
   global $conn;

   $sql= "SELECT bBookId FROM tBankTrankBook WHERE bExport_nu = '".$nu."' AND bDel = 0";
   $rs = $conn->Execute($sql);

   return $rs->fields['bBookId'];
}
?>
</ul>
</div>
</div>

</body>
</html>
