<?php
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$export =  $_GET["export"];

if ($export == "") { $export=2;}
//if ($b == "") { $b=1;}

//合約銀行資料
$conBank = array();
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cOrder ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	// $conBank[] = $rs->fields ;
	array_push($conBank, $rs->fields);
	$rs->MoveNext() ;
}
unset($rs) ;
##
for ($i = 0 ; $i < count($conBank) ; $i ++) {
	$bank = $conBank[$i]['cBankFullName'] ;
	if ($conBank[$i]['cBankMain']=='807') {
		$bank .= '('.$conBank[$i]['cBranchName'].')' ;
	}

	// export =1 已匯出,2 未匯出
	if ($export == "1") {
		$_cond = " and (tExport = '$export') and tPayOk=2"; 
	}else if ($export != "") { 
		$_cond = " and (tExport = '$export') and tPayOk=2"; 
	}

	$_url = ($export == "1")?"/bank/check_out2.php":"/bank/check_out.php";


	//合約銀行案件列表
	$sql = '
		SELECT 
			tVR_Code,
			SUM(tMoney) as Total,
			COUNT(tVR_Code) as C, 
			tDate,
			tBank_kind,
			tExport_time,
			tCode2,
			tCode,
			tKind,
			tObjKind,
			tObjKind2,
			(SELECT cEndDate FROM tContractCase WHERE cEscrowBankAccount = tVR_Code) AS endDate,
			tOwner
		FROM 
			tBankTrans 
		WHERE 
			tOK="1" 
			AND tBank_kind="'.$conBank[$i]['cBankName'].'"
			AND tVR_Code LIKE "'.$conBank[$i]['cBankVR'].'%"
			AND
			IF( tBank_kind = "台新",tCode2 NOT IN("虛轉虛","大額繳稅","臨櫃開票","臨櫃領現","聯行代清償") AND (tObjKind2 != "01" AND tObjKind2 != "02" AND tObjKind2 != "04" AND tObjKind2 != "05"),tCode2 NOT IN("虛轉虛","大額繳稅","臨櫃開票","臨櫃領現")) 
			AND tKind != "利息"
			'.$_cond.' 
		GROUP BY 
			tVR_Code
		ORDER BY 
			tDate 
		DESC ;
	' ;
	// if ($_SESSION['member_id'] == 6) {
	// 	header("Content-Type:text/html; charset=utf-8");
	// 	echo $sql."<br>";
	// }
	
	$rs = $conn->Execute($sql);
	$_total = $rs->RecordCount();	
	$_error =0;
	
	$count = 1 ;
	while( !$rs->EOF ) {
		$_tVR = $rs->fields['tVR_Code'] ;
		$rs->fields['endDate'] = ($rs->fields['endDate'] != '0000-00-00 00:00:00')? (substr($rs->fields['endDate'], 0,4)-1911).substr($rs->fields['endDate'], 4,6):'000-00-00';
		
		if (preg_match("/96988000000008/",$_tVR)) {
			$rs->fields["tBank_kind"] = '利息' ;		//台新利息
		}
		else if (preg_match("/99985000000000/",$_tVR)) {
			$rs->fields["tBank_kind"] = '利息' ;		//永豐西門利息
		}
		else if (preg_match("/99986000000000/",$_tVR)) {
			$rs->fields["tBank_kind"] = '利息' ;		//永豐城中利息
		}
		$rs->fields['bk'] = $conBank[$i]['cId'];

		// $list[$bank][] = $rs->fields;
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]] = $rs->fields;
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['tVR_Code'] = $rs->fields["tVR_Code"];
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['Total'] = $rs->fields["Total"];
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['count'] = $rs->fields['C'];
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['tDate'] = $rs->fields["tDate"];
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['tVR_Code'] = $rs->fields["tVR_Code"];
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields['tVR_Code']]['endDate'] = $rs->fields['endDate'];
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields['tVR_Code']]['tExport_time'] = $rs->fields['tExport_time'];
	
	
		$rs->MoveNext();
	}

	//合約銀行案件列表
	$sql = '
		SELECT
			tId, 
			tVR_Code,
			SUM(tMoney) as Total,
			COUNT(tVR_Code) as C, 
			tDate,
			tBank_kind,
			tExport_time,
			tCode2,
			tCode,
			tKind,
			tObjKind,
			tObjKind2,
			(SELECT cEndDate FROM tContractCase WHERE cEscrowBankAccount = tVR_Code) AS endDate,
			tOwner

		FROM 
			tBankTrans 
		WHERE 
			tOK="1" 
			AND tBank_kind="'.$conBank[$i]['cBankName'].'"
			AND tVR_Code LIKE "'.$conBank[$i]['cBankVR'].'%"
			AND ((tCode2 IN("虛轉虛","大額繳稅","臨櫃開票","臨櫃領現") OR (tBank_kind = "台新" AND (tCode = "03" OR tObjKind2 = "01" OR tObjKind2 = "02" OR tObjKind2 = "04" OR tObjKind2 = "05"))) OR  tKind = "利息" )
			'.$_cond.' 
		GROUP BY 
			tVR_Code,tCode2,tObjKind2
		ORDER BY 
			tDate 
		DESC ;
	' ;
	
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);
	$_total = $rs->RecordCount();	
	$_error =0;
	
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
		$rs->fields['bk'] = $conBank[$i]['cId'];

		$rs->fields['endDate'] = (substr($rs->fields['endDate'], 0,4)-1911).substr($rs->fields['endDate'], 4,10);

		// $list2[$bank][] = $rs->fields;
		$data2[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]] = $rs->fields;
		$data2[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['tVR_Code'] = $rs->fields["tVR_Code"];
		$data2[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['Total'] = $rs->fields["Total"];
		$data2[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['count'] = $rs->fields['C'];
		$data2[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['tDate'] = $rs->fields["tDate"];
		$data2[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields['tVR_Code']]['tExport_time'] = $rs->fields['tExport_time'];
	
		
	
		$rs->MoveNext();
	}
}
// echo "<pre>";
// print_r($list);
//五點後的打包 日期可以預設成次一個工作日(佩琦提的需求20170912)
$tmpD = date("Y-m-d H:i:s");
$checkTime = date("Y-m-d")." 17:00:00";
// echo $tmpD."_".$checkTime;
if ($tmpD >= $checkTime) {
	$eDay = date('Y-m-d',strtotime("+1 day"));
	$eDay = tDate_check(date("Ymd"),'ymd','b','-',1,1) ;
}else{
	$eDay = date('Y-m-d');
}
function tDate_check($_date,$_dateForm='ymd',$_dateType='r',$_delimiter='',$_minus=0,$_sat=0) {     
  $_aDate[0] = substr($_date,0,4) ;
  $_aDate[1] = substr($_date,4,2) ;
  $_aDate[2] = substr($_date,6) ;

 

  //$_cheque_date = implode('-',$_tDate) ;
  
  //是否遇六日要延後(六延兩天、日延一天)
  if ($_sat == '1') {
    $_ss = 0 ;
    $_ss = date("w",mktime(0,0,0,$_aDate[1],($_aDate[2]+$_minus),$_aDate[0])) ;
    if ($_ss == '0') {      //如果是星期日的話，則延後一天
      // if ($_minus < 0) {
      //  $_minus =  $_minus + $_minus + $_minus ;
      // }
      // else {
      //  $_minus = $_minus + $_minus ;
      // }  
      $weekend = 1;     
    }
    else if ($_ss == '6') {   //如果是星期六的話，則延後兩天
      // if ($_minus < 0) {
      //  $_minus = $_minus + $_minus ;
      // }
      // else {
      //  $_minus =  $_minus + $_minus + $_minus ;
      // }
      $weekend = 2;
    }
  }
  ##
  $_minus = $_minus+$weekend;//傳進來的日期必須加上遇到加日延後的日期
  $_t = date("Y-m-d",mktime(0,0,0,$_aDate[1],($_aDate[2]+$_minus),$_aDate[0])) ;    //設定日期為 t+1 天
  unset($_aDate) ;

  $_aDate = explode('-',$_t) ;
  
  if ($_dateType=='r') {    //若要回覆日期格式為"民國"
    $_aDate[0] = $_aDate[0] - 1911 ;
  }
  else {            //若要回覆日期格式為"西元"
  
  }

  //決定回覆日期格式
  switch ($_dateForm) {
    case 'y':       //年
          return $_aDate[0] ;
          break ;
    case 'm':       //月
          return $_aDate[1] ;
          break ;
    case 'd':       //日
          return $_aDate[2] ;
          break ;
    case 'ym':        //年月
          return $_aDate[0].$_delimiter.$_aDate[1] ;
          break ;
    case 'md':        //月日
          return $_aDate[1].$_delimiter.$_aDate[2] ;
          break ;
    case 'ymd':       //年月日
          return $_aDate[0].$_delimiter.$_aDate[1].$_delimiter.$_aDate[2] ;
          break ;
    default:
          break ;
  }
  ##
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
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
function exportFile(cId,cat){
	if (cat != 'all') {
		var loansdate = $('[name="datepicker_'+cat+'"]').val() ;
	}else{
		var loansdate = $('[name="datepicker'+cId+'"]').val() ;
	}
	
	$.colorbox({	
		iframe:true, width:"1100", height:"500",						
		href: "/bank/_export_all.php?x="+cId+"&y=<?=$export?>&l=" + loansdate+'&cat='+cat,
		onClosed:function(){ reload_page(); }
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
	.owner{
		background-color:#FFE8E8;
		width:1016px;
		
	}
</style>
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



<?php if ($_SESSION["member_bankcheck"] == '1'): ?>
	<div style="float:left; margin-left: 10px;"> <a href="/bank/list.php">未審核列表</a></div>
	<div style="float:left; margin-left: 10px;"> <font color=red><strong>已審核列表</strong></font></div>
	<div style="float:left; margin-left: 10px;"> <a href="/bank/pay_check.php">銀行出款確認</a></div>
	<?php if ($_SESSION["pBankBook"] != 0 || $_SESSION["member_id"] == 1 || $_SESSION["member_id"] == 13 || $_SESSION["member_id"] == 36): ?>
		<div style="float:left;margin-left: 10px;"> <a href="instructions/IBookManagerList.php">指示書列表</a> </div>
	<?php endif ?>
	<div style="float:left; margin-left: 10px;"><a href="/bank/returnMoneyList.php">返還代墊列表</a></div>
	<div style="float:left; margin-left: 10px;"> 【<a class='iframe2' href="/bank/report/report.php">銀行對帳單</a>】</div>
<?php endif ?>



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
	?>
		<tr>
			<td colspan="6" style="font-weight:bold;color:#002060;">～～～&nbsp;<?=$bank?>案件&nbsp;～～～
				
			</td>
		</tr>
		<?php 
		if (is_array($data[$conBank[$i]['cBankVR']])) { $no =1;
		foreach ($data[$conBank[$i]['cBankVR']] as $key => $value) : 
			// echo "<div class=\"owner\">".$key."</div>";

			?>
			<tr>
			<td colspan="6" class="owner">&nbsp;&nbsp;<?=$key?>
				
			</td>
		</tr>
			<?php foreach ($value as $k => $v):  ?>
				<tr>
					<td width="3%"><?=($no++)?></td>
					<td width="25%">專屬帳號
						<?php if ($export=="1"): 
							echo $v["tVR_Code"] . "(".$v["tBank_kind"].")";
						 else: 
							echo '<a class="iframe" href="'.$_url.'?ok=1&vr_code='.$v["tVR_Code"].'&ch=2&cat=all"><strong>'.$v["tVR_Code"].'</strong></a>' ;
					 endif ?>

					</td>
					<td width="25%">待出帳總金額 <?=$v["Total"]?> 元</td>
					<td width="7%">筆數 <?=$v["count"]?></td>
					<td width="20%">匯出時間 <?=$v["tExport_time"]?></td>
					<td width="20%">
						<?php if ($v['tObjKind'] == '點交(結案)' || $v['tObjKind'] == '解除契約' || $v['tObjKind'] == '建經發函終止'): ?>
							結案時間 <?=$v['endDate']?>
						<?php endif ?>
					</td>
				</tr>  
				<tr>
					<td height="19" colspan="6"><hr /></td>
				</tr>
			<?php endforeach ?>
			      
			
			<?php endforeach ?>
			<tr>
			<td colspan="6">
				<?php 
				
				if (count($data[$conBank[$i]['cBankVR']]) >= 1 and $export != '1' ) { 
					$bh = '' ; 
					if ($conBank[$i]['cBankMain'] == '807') {
						$bh = $conBank[$i]['cBranchName'] ;
					}
					
					echo '
						<input type="button" id="export_'.$conBank[$i]['cBankAlias'].$conBank[$i]['cId'].'" style="width:200px;" value="產生'.$conBank[$i]['cBankName'].$bh.'媒體檔" onclick="exportFile('.$conBank[$i]['cId'].',\'all\')"/>
						&nbsp;銀行放款日：
						<input type="text" name="datepicker'.$conBank[$i]['cId'].'" class="dt" style="width:100px;" value="'.$eDay.'" readonly />
						※(過了當天17點自動為次一個工作日)
					' ;
				} ?>
		
			</td>
			</tr>
				
			<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
		<?php
		} ?>

		<?php 
		if (is_array($data2[$conBank[$i]['cBankVR']])) {
			foreach ($data2[$conBank[$i]['cBankVR']] as $key => $value) : 
			?>
			<tr>
				<td colspan="6" class="owner">&nbsp;&nbsp;<?=$key?>
					
				</td>
			</tr>
			<?php foreach ($value as $k => $v):  ?>
				
			

			<tr>
					<td><?=($no++)?></td>
					<td>專屬帳號
						<?php if ($export=="1"): 

							echo $v["tVR_Code"] . "(".$v["tBank_kind"].")";
						 else: 
							echo '<a class="iframe" href="'.$_url.'?ok=1&vr_code='.$v["tVR_Code"].'&ch=2&cat='.$v['tId'].'&bk='.$v["bk"].'"><strong>'.$v["tVR_Code"].'</strong></a>' ;

							if ($v['tKind'] == '利息') {
								echo "(".$v['tKind'].")";
							}else{
								if ($v['tObjKind2'] == '01') {
									$tObjKind2 = '申請公司代墊';
									echo "(".$tObjKind2.")";
								}elseif ($v['tObjKind2'] == '02') {
									$tObjKind2 = '返還公司代墊';
									echo "(".$tObjKind2.")";
								}elseif ($v['tObjKind2'] == '04') {
									$tObjKind2 = '申請代理出款';
									echo "(".$tObjKind2.")";
								}elseif ($v['tObjKind2'] == '05') {
									$tObjKind2 = '公司代理出款';
									echo "(".$tObjKind2.")";
								}else{
									echo "(".$v['tCode2'].")";
								}
								
								
							}
							
					 endif ?>

					</td>
					<td width="251">待出帳總金額 <?=$v["Total"]?> 元</td>
					<td width="163">筆數 <?=$v["C"]?></td>
					<td>匯出時間 <?=$v["tExport_time"]?></td>
					<td width="34">&nbsp;</td>
			</tr>        
			<tr>
				<td height="19" colspan="5"><hr /></td>
			</tr>
			<tr>
			<td colspan="6">
				<?php 
				if (count($data2[$conBank[$i]['cBankVR']]) > 0 and $export != '1' ) { 
					$bh = '' ; 
					if ($conBank[$i]['cBankMain'] == '807') {
						$bh = $conBank[$i]['cBranchName'] ;
					}
					
					echo '
						<input type="button" id="export_'.$conBank[$i]['cBankAlias'].$conBank[$i]['cId'].'" style="width:200px;" value="產生'.$conBank[$i]['cBankName'].$bh.'媒體檔" onclick="exportFile('.$conBank[$i]['cId'].','.$v['tId'].')"/>
						&nbsp;銀行放款日：
						<input type="text" name="datepicker_'.$v['tId'].'" class="dt" style="width:100px;" value="'.$eDay.'" readonly />
						※(過了當天17點自動為次一個工作日)
					' ;
				} ?>
		
			</td>
			</tr>
			<?php endforeach ?>
			<?php endforeach ?>
			
				
			<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
		<?php
		} ?>
		
	<?php 
		}
	?>
	
	
  </table>
</div>
</body>
</html>
