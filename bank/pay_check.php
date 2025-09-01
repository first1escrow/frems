<?php
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$today = date("Y-m-d") ;

//合約銀行基本資料
$_all_balance = 0 ;
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cBankMain,cId DESC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$conBank[] = $rs->fields ;
	$_all_balance += $rs->fields['cBankBalance'] + 1 - 1 ;				//所有銀行調帳金額加總
	$rs->MoveNext() ;
}
unset($rs) ;
##


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
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

				var bankcheck = "<?=$_SESSION['member_bankcheck']?>";
		var member_id = "<?=$_SESSION['member_id']?>";
		if (bankcheck == 1 && member_id == 1) {
			SmsRemind();
	        setInterval("SmsRemind()", 60000) ; //出款簡訊提醒
		}
});

function SmsRemind(){
	// console.log('SSSSS');
            $.ajax({
                url: '/includes/sms_remind.php',
                type: 'POST',
                dataType: 'html',
            })
            .done(function(txt) {
                // 
                if (txt != '') {
                    alert(txt);   
                }
  
            });
            
        }
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
	vertical-align: center;
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
<div style="float:left; margin-left: 10px;"> <font color=red><strong>銀行出款確認</strong></font></div>
<?php
if ($_SESSION["pBankBook"] != 0 && $_SESSION["member_id"] != 69 ) {?>
	<div style="float:left;margin-left: 10px;"> <a href="instructions/IBookManagerList.php">指示書列表</a> </div>
<?php }
?>
<div style="float:left; margin-left: 10px;"><a href="/bank/returnMoneyList.php">返還代墊列表</a></div>
<div style="float:left; margin-left: 10px;"> <a href="/bank/sms_check.php">簡訊發送</a></div>
<div style="float:left; margin-left: 10px;"> 【<a class='iframe2' href="/bank/report/report.php">銀行對帳單</a>】</div>
<?php } ?>
</div>
<div>
<div id="bank_c">
<div style="text-align:center;">資料計算中...</div>
	
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
		tBank_kind,
		tObjKind2
	FROM 
		tBankTrans 
	WHERE 
		tExport="1" 
		AND tBankLoansDate>="'.date("Y-m-d",strtotime('-60 day')).'"
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
	
	for ($i = 0 ; $i < count($conBank) ; $i ++) {
		$vr = $conBank[$i]['cBankVR'] ;
		if (preg_match("/^$vr/",$rs->fields['tVR_Code'])) {
			$str = $conBank[$i]['cBankName'].$conBank[$i]['cBranchName'] ;
			
			
			
			break ;
		}
	}
	$tObjKind2 = '';
	if ($rs->fields['tObjKind2'] == '01') {
		$tObjKind2 = '[申請公司代墊]';
	}elseif ($rs->fields['tObjKind2'] == '02') {
		$tObjKind2 = '[返還公司代墊]';
	}elseif ($rs->fields['tObjKind2'] == '04') {
		$tObjKind2 = '[申請代理出款]';
	}elseif ($rs->fields['tObjKind2'] == '05') {
		$tObjKind2 = '[公司代裡出款]';
	}
	
	echo '
	<li>
		<a class="iframe" href="_detail_pay.php?sn='.$rs->fields["tExport_nu"].'&ts='.$rs->fields["tExport_time"].'&tm='.$rs->fields["M"].'">
		<strong>媒體檔匯出時間: </strong>'.$rs->fields["tExport_time"].'  <strong>出帳金額:</strong> '.number_format($rs->fields["M"]).' 元&nbsp;
		('.$str.$rs->fields['tPayOk'].')<span>'.$tObjKind2.'<span>.</a>
	</li>
	' ;
	
	$rs->MoveNext() ; 
}
?>
</ul>
</div>
</div>

</body>
</html>
