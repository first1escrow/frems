<?php
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

// print_r($_SESSION);
// $_SESSION["member_id"] 會員編號
// $_SESSION["member_name"] 姓名
// $_SESSION["member_job"] 
//$b = $_GET["b"];
$export =  $_GET["export"];

if ($export == "") { $export=2;}
//if ($b == "") { $b=1;}

//合約銀行資料
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cOrder ASC;' ;
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
<link rel="stylesheet" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css">
<script src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script src="/js/jquery.colorbox.js"></script>
<script src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>

<script>
$(document).ready(function(){
				$(".dt" ).datepicker({ dateFormat: "yy-mm-dd" }) ;
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
<?php
for ($i = 0 ; $i < count($conBank) ; $i ++) {
	echo '
				$("#export_'.$conBank[$i]['cBankAlias'].$conBank[$i]['cId'].'").click(function() {
					var loansdate = $(\'[name="datepicker'.$conBank[$i]['cId'].'"]\').val() ;
					$.colorbox({	
						iframe:true, width:"1100", height:"500",						
						href: "/bank/_export_all.php?x='.$conBank[$i]['cId'].'&y='.$export.'&l=" + loansdate,
						onClosed:function(){ reload_page(); }
					});
				})

	' ;
}
?>
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
</head>

<body>
<div style="width:1024px; margin-bottom:5px; height:22px; background-color: #CCC">
<?php
//<div style="float:left;margin-left: 10px;"> <a href="http://first.twhg.com.tw/bank/new/out.php">建檔</a> </div>
?>
<div style="float:left;margin-left: 10px;"> <a href="instructions/IBookList.php">指示書</a> </div>
<div style="float:left;margin-left: 10px;"> <a href="/bank/list2.php">待修改資料</a> </div>
<?php
if ($_SESSION["member_id"] == '6' || $_SESSION["member_pDep"] == 5) { //個別權限顯示
?>
<div style="float:left;margin-left: 10px;"> <a href="/bank/BankTransProcess.php">出款進度</a> </div>
<?php } ?>

<?php
//if ($_SESSION["member_id"] != '1' and $_SESSION["member_id"] != '5' ) { 
if ($_SESSION["member_bankcheck"] == '1') { //個別權限顯示
?>
<div style="float:left; margin-left: 10px;"> <a href="/bank/list.php">未審核列表</a></div>
<div style="float:left; margin-left: 10px;"> <font color=red><strong>已審核列表</strong></font></div>
<div style="float:left; margin-left: 10px;"> <a href="/bank/pay_check.php">銀行出款確認</a></div>
<?php
if ($_SESSION["pBankBook"] != 0) {?>
	<div style="float:left;margin-left: 10px;"> <a href="instructions/IBookManagerList.php">指示書列表</a> </div>
<?php }
?>


<div style="float:left; margin-left: 10px;"> 【<a class='iframe2' href="/bank/report/report.php">銀行對帳單</a>】</div>
<?php } ?>
</div>
<div>
<table width="1016" border="0" cellpadding="1" cellspacing="1" class="font12" id="ttt">
    <tr>
      <td colspan="5"> 
        <form name="form" id="form">
          待出帳(匯出)列表 狀態:
            <select name="jumpMenu2" id="jumpMenu2" onchange="MM_jumpMenu('parent',this,0)">
              <option value="list_ok.php?b=<?php echo $b;?>&export=2" <?php if ($export == "2") { echo 'selected="selected"';}?>>未匯出</option>
              <option value="list_ok.php?b=<?php echo $b;?>&export=1" <?php if ($export == "1") { echo 'selected="selected"';}?>>已匯出(未確認)</option>
            </select>
        </form></td>
    </tr>   
	<tr>
		<td colspan="5">
		&nbsp;
		</td>
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
	
	// export =1 已匯出,2 未匯出
	if ($export == "1") {
		$_cond = " and (tExport = '$export') and tPayOk=2"; 
	} 
	else if ($export != "") { 
		$_cond = " and (tExport = '$export') and tPayOk=2"; 
	}
	
	//合約銀行案件列表
	$sql = '
		SELECT 
			tVR_Code,
			SUM(tMoney) as Total,
			COUNT(tVR_Code) as C, 
			tDate,
			tBank_kind,
			tExport_time 
		FROM 
			tBankTrans 
		WHERE 
			tOK="1" 
			AND tBank_kind="'.$conBank[$i]['cBankName'].'"
			AND tVR_Code LIKE "'.$conBank[$i]['cBankVR'].'%"
			'.$_cond.' 
		GROUP BY 
			tVR_Code 
		ORDER BY 
			tDate 
		DESC ;
	' ;

	// echo $sql;
	$rs = $conn->Execute($sql);
	$_total = $rs->RecordCount();	
	$_error =0;
	if ($export == "1") {
		$_url = "/bank/check_out2.php";
	} 
	else {
		$_url = "/bank/check_out.php";
	}
	
	$count = 1 ;
	while( !$rs->EOF ) {
		$_tVR = $rs->fields['tVR_Code'] ;
		
		if (preg_match("/96988000000008/",$_tVR)) {
			$rs->fields["tBank_kind"] = '利息' ;		//台新利息
		}
		else if (preg_match("/99985000000000/",$_tVR)) {
			$rs->fields["tBank_kind"] = '利息' ;		//永豐西門利息
		}
		else if (preg_match("/99986000000000/",$_tVR)) {
			$rs->fields["tBank_kind"] = '利息' ;		//永豐城中利息
		}
		
		echo '
		<tr>
			<td>'.$count ++.'.</td>
			<td>專屬帳號 ' ;
		if ($export=="1") { 
			echo $rs->fields["tVR_Code"] . "(".$rs->fields["tBank_kind"].")";
		} 
		else { 
			echo '<a class="iframe" href="'.$_url.'?ok=1&vr_code='.$rs->fields["tVR_Code"].'&ch=2"><strong>'.$rs->fields["tVR_Code"].'</strong></a>' ;
		}
		echo '</td>
			<td width="251">待出帳總金額 '.$rs->fields["Total"].' 元</td>
			<td width="163">筆數 '.$rs->fields["C"].'</td>
			<td>匯出時間 '.$rs->fields["tExport_time"].'</td>
			<td width="34">&nbsp;</td>
		</tr>        
		<tr>
			<td height="19" colspan="5"><hr /></td>
		</tr>
		' ;
		
		$rs->MoveNext();
	}
	
	echo '
		<tr>
			<td colspan="6">
	' ;
	
	if ($_total > 0 and $export != '1' ) { 
		$bh = '' ; 
		if ($conBank[$i]['cBankMain'] == '807') {
			$bh = $conBank[$i]['cBranchName'] ;
		}
		//五點後的打包 日期可以預設成次一個工作日(BY佩琦20170912)
		$tmpD = date("Y-m-d H:i:s");
		$checkTime = date("Y-m-d")." 17:00:00";

		if ($tmpD >= $checkTime) {
			$eDay = date('Y-m-d',strtotime("+1 day"));
		}else{
			$eDay = date('Y-m-d');
		}

		echo '
			<input type="button" id="export_'.$conBank[$i]['cBankAlias'].$conBank[$i]['cId'].'" style="width:200px;" value="產生'.$conBank[$i]['cBankName'].$bh.'媒體檔" />
			&nbsp;銀行放款日：
			<input type="text" name="datepicker'.$conBank[$i]['cId'].'" class="dt" style="width:100px;" value="'.$eDay.'" readonly />
			※(過了當天17點自動為次一個工作日)
		' ;
	}
	
	echo '
			</td>
		</tr>
		
		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
	' ;
} 
?>
  </table>
</div>
</body>
</html>
