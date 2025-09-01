<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;
$tlog->selectWrite($_SESSION['member_id'], json_encode($_REQUEST), '查看銀行存入支票列表') ;

$textfield = $_REQUEST['textfield'] ;

//取得合約銀行資料
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$conBank[] = $rs->fields ;
	$rs->MoveNext() ;
}
unset($rs) ;
##

if ($textfield) {
	// $sql = 'SELECT * FROM tExpense_cheque WHERE eDepAccount LIKE "%'.$textfield.'" ORDER BY eTradeDate ASC ;' ;
	
	$sql='
		SELECT 
			ec.eTradeDate,
			ec.eTradeNum,
			ec.eDepAccount,
			ec.eLender,
			ec.eDebit,
			ec.eTradeStatus,
			ec.eAccount,
			ec.eCheckDate,
			ec.eExpenseDate,
			ec.id,
			ec.ePayTitle,
			ec.eSms,
			ec.eRegistTime,
			ec.eTicketStatus,
			scr.sName,
			bco.bSID,
			(SELECT pName FROM tPeopleInfo WHERE pId=scr.sUndertaker1) AS sUndertaker1

		FROM 
			tExpense_cheque AS ec  
		LEFT JOIN 
			tBankCode AS bco 
		ON 
			bco.bAccount=SUBSTRING(ec.eDepAccount,-14)
		LEFT JOIN
			tScrivener AS scr ON scr.sId=bco.bSID
		WHERE
			ec.eDepAccount LIKE "%'.$textfield.'"
			AND 
			IF( eDepAccount LIKE "0096988%" ,eTicketStatus IN ("S","P","OK","T","B"),eTicketStatus ="") 
		ORDER BY 
			ec.eSms,ec.eTradeDate 
		ASC
	';

	$rs = $conn->Execute($sql) ;
	$max = $rs->RecordCount() ;
	$i = 0 ;
	for ($i = 0 ; $i < $max ; $i ++) {
		$list[$i] = $rs->fields ;
		$rs->MoveNext() ;
	}
}

$_end_date = (date("Y")-1911).date("md") ;
$_start_date = date("Y-m-d",mktime(0,0,0,date("m"),(date("d")-6),date("Y"))) ;		// 設定(6+1)天的顯示日期範圍

// $_start_date = date("Y-m-d",mktime(0,0,0,date("m"),(date("d")-6),date("Y"))) ;		// 設定(6+1)天的顯示日期範圍


$tmp = explode('-',$_start_date) ;
$_start_date = ($tmp[0] - 1911).$tmp[1].$tmp[2] ;
unset($tmp) ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>支票存入通知區</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<script type="text/javascript" src="js/jquery.highlight-3.js"></script>
<style>
.highlight { background-color: yellow }
</style>
<script>
function search(){
	_key = $('#textfield').val();
	//alert(_key);
	$('#highlight-plugin').removeHighlight().highlight(_key);
	window.location.hash = _key; 
}
function UnSend(id){

// console.log(id);

$.ajax({
	url: 'expense_cheque_ajax.php',
	type: 'POST',
	dataType: 'html',
	data: {id: id},
})
.done(function(txt) {
	if (txt==1) {
		alert('更改成功');

	}else{
		alert('更改失敗');
	}

	location.href='expense_cheque.php';
	
});

}
</script>
</head>

<body>
<form name="myform" method="POST">
<table width="500" border="1">
  <tr>
    <td>帳號搜尋:
      <label for="textfield"></label>
    <input type="text" name="textfield" id="textfield" />
    <input type="submit" value="搜尋" /></td>
  </tr>
</table>
<div style="height:10px;"></div>
<div id="highlight-plugin">
<table width="800" border="1">
	<tr>
		<td colspan="9">
		日期範圍：
<?php

echo substr($_start_date,0,3).'/'.substr($_start_date,3,2).'/'.substr($_start_date,5,2) ;
echo ' ~ ' ;
echo substr($_end_date,0,3).'/'.substr($_end_date,3,2).'/'.substr($_end_date,5,2) ;

?>
		</td>
	</tr>

	<tr>
		<td width="20%">通知日期</td>
		<td width="10%">保證帳號</td>
		<td width="10%">支票金額</td>
		<td width="10%">交易狀態</td>
		<td width="10%">票據種類</td>
		<td width="10%">地政士</td>
		<td width="10%">經辦</td>
		<td width="10%">簡訊通知</td>
		<td width="10%">不寄送簡訊</td>
	</tr>
<?php
if ($textfield) {
	if (!empty($list)) {
			foreach ($list as $k => $v) {


			//調整通知日期顯示(20180209 增加時間顯示)
			$tmp = explode(' ', $v['eRegistTime']);
			

			$_t1 = substr($v['eTradeDate'],0,3) ;
			$_t2 = substr($v['eTradeDate'],3,2) ;
			$_t3 = substr($v['eTradeDate'],5) ;
			$_date = $_t1.'-'.$_t2.'-'.$_t3." ".substr($tmp[1],0,5) ;
			unset($tmp);
			##
			
			//調整交易狀態顯示
			$_status = $v['eTradeStatus'] ;


			switch ($_status) {
				case '1':
					$_status = '沖正交易' ;
					break;
				case '9':
					$_status = '被沖正交易' ;
					break;
				case '10':
					$_status = '入庫' ;
					break;
				case '11':
					$_status = '入庫-延期提示' ;
					break;
				case '12':
					$_status = '入庫-領回' ;
					break;
				case '20':
					$_status = '出庫' ;
					break;
				case '22':
					$_status = '出庫-領回' ;
					break;
				case '30':
					$_status = '退票' ;
					break;
				case '31':
					$_status = '本埠退票' ;
					break;
				case '40':
					$_status ='銷帳';
					break;
				case '48':
					$_status ='退票通知沖正';
					break;
				case '49':
					$_status ='即時銷入帳';
					break;

				default:
					$_status = '正常交易' ;
					break;
			}
/*
			if ($_status=='1') {
				$_status = '沖正交易' ;
			}
			else if ($_status=='9') {
				$_status = '被沖正交易' ;
			}
			else {
				$_status = '正常交易' ;
			}*/
			##

		if($v['eSms'] > 0)
		{
			$color='#CCCCCC';
		}else
		{
			$color='#ffffff';
		}
	?>
			<tr style="background-color:<?php echo $color; ?>">
				<td><?=$_date?></td>
				<td><?=substr($v['eDepAccount'],7)?></td>
				<td><?=(substr($v['eLender'],0,13)+1-1)?></td>
				<td><?=$_status?></td>
				<td>
				<?php
					if ((preg_match("/10401810001999/",$v['eAccount'])) || (preg_match("/12601800015999/",$v['eAccount'])) ) {
						//若信託帳戶為永豐
						$chequeType = '託收票' ;
						if ($v['eCheckDate'] == '0000000') {
							$chequeType = '次交票' ;
						}
						##
						echo $chequeType ;
					}elseif ($v['eExpenseDate']!='') { //一銀託收票
						echo '託收票' ;
					}elseif($rs->fields['eTicketStatus'] != ''){ //台新
						if ($rs->fields['eTicketStatus'] =='S') { //T:在途
							echo '託收票' ;
						}elseif ($rs->fields['eTicketStatus'] == 'P') {
							echo '次交票' ;
						}elseif($rs->fields['eTicketStatus'] == 'D'){
							echo '退票' ;
						}elseif ($rs->fields['eTicketStatus'] == 'B') {
							echo $rs->fields['ePayTitle'];
						}
					}else {
						echo '次交票' ;
					}
				?>
				</td>
				<td><?php echo $v['sName']; ?></td>
				<td><?php
					if ($v['sUndertaker1'] == '') {
						echo '吳佩琦';
					}else{
						
					 echo $v['sUndertaker1'];
					}
					?></td>
				<td><a href="expense_cheque_sms.php?id=<?php echo $v['id']; ?>&sid=<?php echo $v['bSID']; ?>"><img src="images/sms.png" border="0" width="50px" height="50px"></a></td>
				<td align="center">
  		<?php

  			if ($v['eSms'] != 1) {

  				if ($v['eSms'] == 2) {
  					$check = "checked=checked";
  				}

  			?>
  				<input type="checkbox" name="no" onclick="UnSend(<?php echo $v['id']; ?>)" <?php echo $check; ?>/>

  		<?php  unset($check);	
  			} ?>
  		

  		
  	</td>
			</tr>
	<?php
		}
	}
	
}
else {
	//$sql = "select * from tExpense_cheque order by eTradeDate desc,eTradeNum";
	// $sql = '
	// 	SELECT 
	// 		* 
	// 	FROM 
	// 		tExpense_cheque 
	// 	WHERE
	// 		eTradeDate>="'.$_start_date.'"
	// 		AND eTradeDate<="'.$_end_date.'"
	// 	ORDER BY 
	// 		eTradeDate 
	// 	DESC ;
	// ' ;
//
	$sql='
		SELECT 
			ec.eTradeDate,
			ec.eTradeNum,
			ec.eDepAccount,
			ec.eLender,
			ec.eDebit,
			ec.eTradeStatus,
			ec.eAccount,
			ec.eCheckDate,
			ec.eExpenseDate,
			ec.id,
			ec.eSms,
			ec.eRegistTime,
			ec.eTicketStatus,
			ec.ePayTitle,
			scr.sName,
			bco.bSID,
			(SELECT pName FROM tPeopleInfo WHERE pId=scr.sUndertaker1) AS sUndertaker1

		FROM 
			tExpense_cheque AS ec  
		LEFT JOIN 
			tBankCode AS bco 
		ON 
			bco.bAccount=SUBSTRING(ec.eDepAccount,-14)
		LEFT JOIN
			tScrivener AS scr ON scr.sId=bco.bSID
		WHERE
			ec.eTradeDate>="'.$_start_date.'"
		AND 
			ec.eTradeDate<="'.$_end_date.'"
		AND 
			IF( eDepAccount LIKE "0096988%" ,eTicketStatus IN ("S","P","D","OK","T","B"),eTicketStatus ="") 
		ORDER BY 
			ec.eSms ASC,ec.eTradeDate
		DESC
		';	
		// echo $sql;
	$rs = $conn->Execute($sql);	
	$_error =0;
	while( !$rs->EOF ) {
		$tmp = explode(' ', $rs->fields['eRegistTime']);
		$_y = substr($rs->fields["eTradeDate"],0,3) + 1911;
		$_m = substr($rs->fields["eTradeDate"],3,2);
		$_d = substr($rs->fields["eTradeDate"],5,2);
		$_date = $_y ."-" .$_m . "-" . $_d." ".substr($tmp[1],0,5);

		unset($tmp);
		
		for ($i = 0 ; $i < count($conBank) ; $i ++) {
			$tAccount = $conBank[$i]['cBankTrustAccount'] ; 
			if (preg_match("/$tAccount/",$rs->fields['eAccount'])) {
				$rs->fields['eTradeNum'] = $conBank[$i]['cBankName'] ;
				if ($conBank[$i]['cBankMain'] == '807') {
					$rs->fields['eTradeNum'] .= $conBank[$i]['cBranchName'] ;
				}
				break ;
			}
		}

		if($rs->fields['eSms'] > 0)
		{
			$color='#CCCCCC';
		}else
		{
			$color='#ffffff';
		}
?>
	<tr style="background-color:<?php echo $color; ?>">
    <td><?php echo $_date ."(".$rs->fields["eTradeNum"].")";?><a name="<?php echo substr($rs->fields["eDepAccount"],7);?>" id="<?php echo substr($rs->fields["eDepAccount"],7);?>"></a></td>
    <td><?php echo substr($rs->fields["eDepAccount"],7);?></td>
    <td><?php 
	$_money = (int)substr($rs->fields["eLender"],0,-2);
	if ($_money <= 0) {
		echo "-".(int)substr($rs->fields["eDebit"],0,-2);
	} 
	else {
		echo $_money;
	}
	?></td>
    <td>
	<?php

		switch ($rs->fields["eTradeStatus"]) {
				case '1':
					$_status = '沖正交易' ;
					break;
				case '9':
					$_status = '被沖正交易' ;
					break;
				case '10':
					$_status = '入庫' ;
					break;
				case '11':
					$_status = '入庫-延期提示' ;
					break;
				case '12':
					$_status = '入庫-領回' ;
					break;
				case '20':
					$_status = '出庫' ;
					break;
				case '22':
					$_status = '出庫-領回' ;
					break;
				case '30':
					$_status = '退票' ;
					break;
				case '31':
					$_status = '本埠退票' ;
					break;
				case '40':		
						$_status ='銷帳';
					break;
				case '48':
					$_status ='退票通知沖正';
					break;
				case '49':
					$_status ='即時銷入帳';
					break;

				default:
					$_status = '正常交易' ;
					break;
			} 

			echo $_status;
		/*if ($rs->fields["eTradeStatus"] == "1") { 
			echo "沖正交易";
		} else if ($rs->fields["eTradeStatus"] == "9") { 
			echo "被沖正交易";
		} else {
			echo "正常交易";
		}*/
	?></td>
	<td>
	<?php
	if ((preg_match("/10401810001999/",$rs->fields['eAccount'])) || (preg_match("/12601800015999/",$rs->fields['eAccount']))) {
		//若信託帳戶為永豐
		$chequeType = '託收票' ;
		if ($rs->fields['eCheckDate'] == '0000000') {
			$chequeType = '次交票' ;
		}
		##
		echo $chequeType ;
	}elseif ($rs->fields['eExpenseDate']!='') { //一銀託收票
		echo '託收票' ;
	}elseif($rs->fields['eTicketStatus'] != ''){ //台新
		if ($rs->fields['eTicketStatus'] =='S') {
			echo '託收票' ;
		}elseif ($rs->fields['eTicketStatus'] == 'P') {
			echo '次交票' ;
		}elseif($rs->fields['eTicketStatus'] == 'D'){
			echo '退票' ;
		}elseif ($rs->fields['eTicketStatus'] == 'B') {
							echo $rs->fields['ePayTitle'];
						}
	}else {
		echo '次交票' ;
	}
	?>
	</td>
	<td>
	<?php 
		echo $rs->fields['sName'];
	?>
	</td>
	<td><?php 
		if ($rs->fields['sUndertaker1'] == '') {
			echo '吳佩琦';
		}else{
			echo $rs->fields['sUndertaker1'];
		}
		
	?></td>
	<td><a href="expense_cheque_sms.php?id=<?php echo $rs->fields['id']; ?>&sid=<?php echo $rs->fields['bSID']; ?>"><img src="images/sms.png" border="0" width="50px" height="50px"></a></td>
  	<td align="center">
  		<?php

  			if ($rs->fields['eSms'] != 1) {

  				if ($rs->fields['eSms'] == 2) {
  					$check = "checked=checked";
  				}

  			?>
  				<input type="checkbox" name="no" onclick="UnSend(<?php echo $rs->fields['id']; ?>)" <?php echo $check; ?>/>

  		<?php  unset($check);	
  			} ?>
  		

  		
  	</td>
  </tr>
<?php
		$rs->MoveNext();
	} 
}
?>
</table>
</div>
</form>
</body>
</html>
