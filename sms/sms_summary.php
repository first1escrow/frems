<?php
include_once '../configs/config.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

if (session_status() != 2) {
    session_start();
}

$member_id = $_SESSION['member_id'] ;

$member_power = $_SESSION['member_banktrans'];


//今日簡訊總數
$sql = '
	SELECT
		*
	FROM
		tSMS_Log_View
	WHERE
		sSend_Time >= "'.date("Y-m-d").' 00:00:00"
		AND tTID != "" 
		AND tTID != "error" ;
' ;
##

//echo "SQL=".$sql ;
$rs = $conn->Execute($sql) ;
$t_count = $rs->RecordCount() ;
$t_count += 0 ;
unset($rs) ;
##

//簡訊失敗
$sql = '
	SELECT
		a.*,
		b.sSend_Time 
	FROM
		tSMS_Check_View AS a
	JOIN
		tSMS_Log_View AS b ON b.tTID = a.tTaskID
	WHERE
		a.tCode NOT IN ("0","1","77","999999999","00000","99999")
		AND a.tChecked = "y"
		AND b.sSend_Time >= "'.date("Y-m-d").' 00:00:00" ;
' ;

//echo "SQL=".$sql ;
$rs = $conn->Execute($sql) ;
$f_count = $rs->RecordCount() ;
$f_count += 0 ;
unset($rs) ;
##

//簡訊傳送中
$sql = '
	SELECT
		a.*,
		b.*,
		a.id as tId 
	FROM
		tSMS_Check_View AS a
	JOIN
		tSMS_Log_View AS b ON b.tTID = a.tTaskID
	WHERE
		a.tChecked = "n"
		AND b.sSend_Time >= "'.date("Y-m-d").' 00:00:00" ;
' ;

//echo "SQL=".$sql ;
$rs = $conn->Execute($sql) ;
$s_count = $rs->RecordCount() ;
$s_count += 0 ;
unset($rs) ;
##

//伺服器端失敗
$sql = '
	SELECT
		*
	FROM
		tSMS_Check_View AS a
	JOIN
		tSMS_Log_View AS b ON b.tTID = a.tTaskID
	WHERE
		a.tCode IN ("77","999999999","99999")
		AND a.tChecked = "y"
		AND b.sSend_Time >= "'.date("Y-m-d").' 00:00:00" ;
' ;

//echo "SQL=".$sql ;
$rs = $conn->Execute($sql) ;
$b_count = $rs->RecordCount() ;
$b_count += 0 ;
unset($rs) ;
##

//簡訊成功
$sql = '
	SELECT
		*
	FROM
		tSMS_Check_View AS a
	JOIN
		tSMS_Log_View AS b ON b.tTID = a.tTaskID
	WHERE
		a.tCode IN ("0","00000") 
		AND a.tChecked = "y"
		AND b.sSend_Time >= "'.date("Y-m-d").' 00:00:00" ;
' ;

//
$ok_count = $t_count - $f_count - $s_count - $b_count ;
##
if ($member_power==2) {
	$query = " AND scr.sUndertaker1 ='".$member_id."'";
}

$case1 =0;
$case2 = 0;
$case3 = 0;
$case_all1 =0;
$case_all2=0;
$case_all3 = 0;
$today = date("Y-m-d");
$month_range = 2;//2個月

//案件資訊1(案件進行中&未入帳&簽約日過七天)

 //所有符合條件案件
 $sql = "
 		SELECT 
 			cc.cCertifiedId,
 			scr.sUndertaker1
 		FROM 
 			tContractCase AS cc
 		JOIN 
 			 tContractScrivener AS cs  ON cc.cCertifiedId = cs.cCertifiedId
 		JOIN 
 			tScrivener AS scr ON scr.sId = cs.cScrivener
 			
 		WHERE
 			cc.cCaseStatus=2 
 			AND cc.cSignDate != '00-00-00 00:00:00'
 			AND cc.cSignDate <= '".date('Y-m-d',strtotime('-7 day'))." 00:00:00'
 			".$query."";
 	// echo $sql."<br>"	;
 	// die;
$rs2 = $conn->Execute($sql);

$count_all = $rs2->RecordCount() ;

while (!$rs2->EOF) {
		
		if ($rs2->fields['sUndertaker1']==$member_id) {
			$list[$rs2->fields['sUndertaker1']]['all']++;
		}

	$rs2->MoveNext();
}

//有入帳
 $sql = "
 		SELECT 
 			cc.cCertifiedId,
 			scr.sUndertaker1
 			
 		FROM 
 			tExpense AS ex
 		
 		
 		LEFT JOIN 
 			tContractCase AS cc	 ON cc.cEscrowBankAccount=SUBSTRING(ex.eDepAccount,-14)

 		JOIN 
 			 tContractScrivener AS cs  ON cc.cCertifiedId = cs.cCertifiedId
 		JOIN 
 			tScrivener AS scr ON scr.sId = cs.cScrivener
 			
 		WHERE
 			cc.cCaseStatus=2 
 			AND cc.cSignDate != '00-00-00 00:00:00'
 			AND cc.cSignDate <= '".date('Y-m-d',strtotime('-7 day'))." 00:00:00'
 			".$query."
 			GROUP BY cc.cCertifiedId
 		";
// echo $sql."<br>";

$rs = $conn->Execute($sql);

$count_import = $rs->RecordCount() ;
while (!$rs->EOF) {
		

		if ($rs->fields['sUndertaker1']==$member_id) {
			$list[$rs->fields['sUndertaker1']]['import']++;
		}

	$rs->MoveNext();
}


//符合條件案件-有入帳=未入帳
$case_all1 = $count_all-$count_import; 
$case1 = $list[$member_id]['all']-$list[$member_id]['import'];

##
//案件資訊2(進行中&&簽約日>2個月) 20180123 只算無交屋日的

 $sql = "
 		SELECT 
 			cc.cCertifiedId,
 			cc.cSignDate,
 			cc.cFinishDate2,
			cc.cNoClosing as remark,
 			scr.sName AS scrivener,
 			(SELECT bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum) AS store,
 			(SELECT bName FROM tBrand AS b WHERE b.bId=cr.cBrand) AS brand,
 			cb.cName AS buyer,
 			co.cName AS owner,
 			scr.sUndertaker1,
			pr.cClosingDay 
 		FROM 
 			tContractCase AS cc
 		LEFT JOIN 
 			tContractRealestate AS cr ON cc.cCertifiedId = cr.cCertifyId 
 		LEFT JOIN 
 			tContractScrivener AS cs  ON cc.cCertifiedId = cs.cCertifiedId
 		LEFT JOIN 
 			tScrivener AS scr ON scr.sId = cs.cScrivener
 		LEFT JOIN 
 			tContractBuyer AS cb  ON cb.cCertifiedId = cs.cCertifiedId 
 		LEFT JOIN 
 			tContractOwner AS co  ON co.cCertifiedId = cs.cCertifiedId 
		LEFT JOIN
			tContractProperty AS pr ON cc.cCertifiedId=pr.cCertifiedId
 		WHERE
 			cc.cCaseStatus=2 
 			AND cc.cSignDate != '0000-00-00 00:00:00'
 			AND cc.cSignDate <= '".date('Y-m-d',strtotime('-2 month'))." 00:00:00'
 			".$query."
 			GROUP BY cc.cCertifiedId 
 			ORDER BY  cc.cSignDate DESC
 		";
 		// echo $sql."<bR>";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {

	

		$rs->fields['cSignDate'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['cSignDate']);	
		$tmp1 = explode('-',$rs->fields['cSignDate']) ;
		$valid_date = date("Y-m-d",mktime(0,0,0,($tmp1[1]+$month_range),$tmp1[2],$tmp1[0])) ; //+月的
		$valid_date2 = strtotime($valid_date) ;
		unset($tmp1);

		$rs->fields['cClosingDay'] = substr($rs->fields['cClosingDay'],0,10) ;
		
		$finishDate2 = strtotime($rs->fields['cClosingDay']) ;//預計點交日
		
		if ($rs->fields['cClosingDay'] == '0000-00-00') $rs->fields['cClosingDay'] = '' ;

		
	
		##
		//簽約日
		
		$SignDate = strtotime($rs->fields['cSignDate']) ;
		##
		
		// if (($today > $rs->fields['cClosingDay']) && ($rs->fields['cClosingDay'] != '')) { // 預計點交日不為空且今日大於預計點交日
		// 		if ($member_power==2) {
		// 			$case2 ++;
		// 		}elseif ($member_power==1) {
						
		// 			if ($member_id==$rs->fields['sUndertaker1']) {
		// 				$case2 ++;
		// 			}

		// 			$case_all2++;
		// 		}
			
		// }
		// elseif (($rs->fields['cClosingDay'] == '') && ($today > $valid_date)) {
		// 	if ($member_power==2) {
		// 			$case2 ++;
		// 		}elseif ($member_power==1) {
						
		// 			if ($member_id==$rs->fields['sUndertaker1']) {
		// 				$case2 ++;
		// 			}

		// 			$case_all2++;
		// 		}
		// }

		if (($rs->fields['cClosingDay'] == '') && ($today > $valid_date)) {
			if ($member_power==2) {
						$case2 ++;
			}elseif ($member_power==1) {
							
				if ($member_id==$rs->fields['sUndertaker1']) {
					$case2 ++;
				}

				$case_all2++;
			}
		}


	$rs->MoveNext();
}
##
##
//案件資訊3(超過點交日尚未結案) (沒有預計點交日的話以兩個月來算)[作廢] //20180123 只算有交屋日的


$sql = "SELECT 
 			cc.cCertifiedId,
 			cc.cSignDate,
			cc.cNoClosing as remark,
			cc.cFinishDate2,
 			(SELECT pName FROM tPeopleInfo WHERE pId=scr.sUndertaker1) AS sUndertaker1,
 			scr.sUndertaker1 AS sUndertakerId,
 			scr.sName AS scrivener,
 			(SELECT bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum) AS store,
 			(SELECT bName FROM tBrand AS b WHERE b.bId=cr.cBrand) AS brand,
 			cb.cName AS buyer,
 			co.cName AS owner,
			pr.cClosingDay 
 		FROM 
 			tContractCase AS cc
 		LEFT JOIN 
 			tContractRealestate AS cr ON cc.cCertifiedId = cr.cCertifyId 
 		LEFT JOIN 
 			tContractScrivener AS cs  ON cc.cCertifiedId = cs.cCertifiedId
 		LEFT JOIN 
 			tScrivener AS scr ON scr.sId = cs.cScrivener
 		LEFT JOIN 
 			tContractBuyer AS cb  ON cb.cCertifiedId = cs.cCertifiedId 
 		LEFT JOIN 
 			tContractOwner AS co  ON co.cCertifiedId = cs.cCertifiedId 
		LEFT JOIN
			tContractProperty AS pr ON cc.cCertifiedId=pr.cCertifiedId
 		WHERE
 			cc.cCaseStatus=2 AND cc.cSignDate != '0000-00-00 00:00:00' ".$query."
 			GROUP BY cc.cCertifiedId ORDER BY  cc.cSignDate DESC";
 		
 		
$rs = $conn->Execute($sql);

while (!$rs->EOF) {


	$rs->fields['cSignDate'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['cSignDate']);	
		$tmp1 = explode('-',$rs->fields['cSignDate']) ;
		$valid_date = date("Y-m-d",mktime(0,0,0,($tmp1[1]+$month_range),$tmp1[2],$tmp1[0])) ; //+月的
		$valid_date2 = strtotime($valid_date) ;
		unset($tmp1);

		$rs->fields['cClosingDay'] = substr($rs->fields['cClosingDay'],0,10) ;
		$finishDate2 = strtotime($rs->fields['cClosingDay']) ;//預計點交日
		if ($rs->fields['cClosingDay'] == '0000-00-00') $rs->fields['cClosingDay'] = '' ;

		##
		//簽約日
		
		$SignDate = strtotime($rs->fields['cSignDate']) ;
		
		
		##
		
		// echo $member_id."_".$rs->fields['sUndertakerId'];
		if (($today > $rs->fields['cClosingDay']) && ($rs->fields['cClosingDay'] != '')) { // 預計點交日不為空且今日大於預計點交日
				

			$case_all3++;

			if ($member_id == $rs->fields['sUndertakerId']) {
				$case3++;
			}
			
		}

		// elseif (($rs->fields['cClosingDay'] == '') && ($today > $valid_date)) {
		// 	$case_all3++;

		// 	if ($member_id == $rs->fields['sUndertakerId']) {
		// 		$case3++;
		// 	}
		// }
		


	
	$rs->MoveNext();
}



//地政士OR 仲介過期
if ($_SESSION['member_id'] == 6 || $_SESSION['member_id'] == 1) {
	
	$branch_over = $branch_non = 0;
	//大於開始日期且超過結束時間(暫停且逾期的店家數)
	$sql = "SELECT bId,bStatusDateEnd,bStatusDateStart FROM tBranch AS b WHERE b.bStatus = 3 "; //AND b.bStatusDateEnd < '".$today."' AND b.bStatusDateEnd !='0000-00-00'
	
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		
		if ($rs->fields['bStatusDateEnd'] < $today && $rs->fields['bStatusDateEnd'] !='0000-00-00') {
			$branch_over++;
		}elseif ($rs->fields['bStatusDateEnd'] == '0000-00-00' && $rs->fields['bStatusDateStart'] == '0000-00-00') {
			$branch_non++;
		}

		$rs->MoveNext();
	}
	// $branch = $rs->RecordCount();
}

// echo "<pre>";
// print_r($list);
// echo "</pre>";
$conn->close();
##
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="180" />
<title>資訊視窗</title>
<script src="../js/jquery-1.7.2.min.js"></script>
<script>
window.resizeTo('460','750') ;

$(document).ready(function() {

var check = "<?=$member_power?>";


	if (check==2) {
		$("#show_all").hide();
	}

<?php
if (($_SESSION['member_id'] == '6') || ($_SESSION['member_id'] == '1') || ($_SESSION['member_id'] == '18')) {
?>


	//check_Hifax() ;
	



<?php
}
?>
}) ;
function sms_list_win(k) {
	$('[name="ch"]').val(k) ;
	$('[name="new_sms_list"]').submit() ;
}

function new_case_list(a) {
	$('[name="check"]').val(a) ;
	$('[name="case_list"]').submit() ;
}

function store(t)
{
	$('[name="check"]').val(t) ;
	$('[name="store_list"]').submit() ;

	// window.open('store_list.php?check='+t,'store',"height=60px,width=300px,status=no");
}


function check_Hifax() {
	var url = '/hifax/checkMail.php' ;
	$.post(url,function(txt) {
		
		if (txt != '') {
			alert('收到新的傳真資料共 ' + txt + ' 筆，請至https://www.hibox.hinet.net查詢') ;
		}
	}) ;
}
</script>
<style>
a {
	color: blue ; 
	text-decoration: none ;
	font-size: 12pt ;
	font-weight: bold;
}
a:hover {
	color: red ;
	text-decoration: underline ;
}
</style>
</head>

<body>
<div>
	<!--<a href="../others/document/ctbc_contract.xlsx">中信銀行房貸代書服務窗口名單</a><br>-->
	<!--<a href="../others/document/ctbc_noservice.xlsx">中信銀行房貸不承做區域</a>-->
</div>
<p></p>
<div style="width:300px;height:100px;">
<a href="#" onclick="sms_list_win('')">展開簡訊明細</a><br>
<div style="height:10px;"></div>
<span style="font-size:12px;">今日簡訊摘要</span>
=============================================
<div>
今日已發送之簡訊總數：<span style="font-weight:bold;color:#000080;"><a href="#" onclick="sms_list_win('a')"><?=$t_count?>&nbsp;則</a></span>
</div>

<div>
今日傳送成功簡訊數量：<span style="font-weight:bold;color:#000080;"><a href="#" onclick="sms_list_win('s')"><?=$ok_count?>&nbsp;則</a></span>
</div>

<div>
今日傳送失敗簡訊數量：<span style="font-weight:bold;color:#000080;"><a href="#" onclick="sms_list_win('f')"><?=$f_count?>&nbsp;則</a></span>
</div>

<div>
今日傳送中之簡訊數量：<span style="font-weight:bold;color:#000080;"><a href="#" onclick="sms_list_win('c')"><?=$s_count?>&nbsp;則</a></span>
</div>

<div>
今日伺服器端錯誤簡訊數量：<span style="font-weight:bold;color:#000080;"><a href="#" onclick="sms_list_win('b')"><?=$b_count?>&nbsp;則</a></span>
</div>
=============================================<br><br>
<?php
if ($_SESSION['member_id'] != 10)  {?>
案件資訊
<div style="height:10px;"></div>
<span style="font-size:12px;"></span>
=============================================
<div>
	七日未入帳案件：<span style="font-weight:bold;color:#000080;"><a href="#" onclick="new_case_list('o1')"><?=$case1?>&nbsp;件</a></span>
</div>
<div>
	2個月未結案之案件：
	<span style="font-weight:bold;color:#000080;"><a href="#" onclick="new_case_list('o2')"><?=$case2?>&nbsp;件</a></span>
	<font color="red" size="2">[不含有交屋日的案件]</font>
</div>
<div>
	超過點交日尚未結案：
	<span style="font-weight:bold;color:#000080;"><a href="#" onclick="new_case_list('u1')"><?=($case3)?>&nbsp;件</a></span>
	<font color="red" size="2">[以有交屋日計算]</font>
</div>	
=============================================<br><br>
<div style="height:20px;text-align:center;">
</div>
<?php } ?>

<div id="show_all" >
	全部案件資訊
	<div style="height:10px;"></div>
	<span style="font-size:12px;"></span>
	=============================================
	<div>
		七日未入帳案件：<span style="font-weight:bold;color:#000080;"><a href="#" onclick="new_case_list('a1')"><?=$case_all1?>&nbsp;件</a></span>
	</div>
	<div>
		2個月未結案之案件：
		<span style="font-weight:bold;color:#000080;"><a href="#" onclick="new_case_list('a2')"><?=$case_all2?>&nbsp;件</a></span>
		<font color="red" size="2">[不含有交屋日的案件]</font>
	</div>
	
	<div style="width:500px;">
	 
		超過點交日尚未結案：
		<span style="font-weight:bold;color:#000080;"><a href="#" onclick="new_case_list('u2')"><?=($case_all3)?>&nbsp;件</a></span>
		<font color="red" size="2">[以有交屋日計算]</font>
	</div>	
	
	

	=============================================<br><br>
</div>

<?php
if ($_SESSION['member_id'] == 6 || $_SESSION['member_id'] == 1)  {?>
	<div>
		仲介暫停且過期
		=============================================<br>
		<div>
		仲介：<span style="font-weight:bold;color:#000080;"><a href="#" onclick="store('b1')"><?=$branch_over?>&nbsp;家</a></span>
		</div>
		=============================================<br><br>
	</div>
	<div>
		仲介暫停且未填寫期間
		=============================================<br>
		<div>
		仲介：<span style="font-weight:bold;color:#000080;"><a href="#" onclick="store('b2')"><?=$branch_non?>&nbsp;家</a></span>
		</div>
		=============================================<br><br>
	</div>
<?php }

?>



<div align="center" style="width:400px;">
<input type="button" onclick="window.close()" value="關閉視窗">
</div>
</div>
<form name="new_sms_list" method="POST" action="sms_list.php" target="_blank">
<input type="hidden" name="ch" value="">
</form>

<form name="case_list" method="POST" action="case_list.php" target="_blank">
<input type="hidden" name="check" value="">
</form>
<form name="store_list" method="POST"  action="store_list.php" target="foo" onsubmit="window.open('','foo','height=600px,width=300px,status=no,scrollbars=yes')">
	<input type="hidden" name="check" value="">
</form>
</body>
</html>
