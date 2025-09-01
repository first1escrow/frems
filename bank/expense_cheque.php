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
	if ($rs->fields['cBankMain'] == '807') {
		$rs->fields['cBankName'] .= $rs->fields['cBranchName'] ;
	}
	$conBank[$rs->fields['cBankTrustAccount']] = $rs->fields ;
	$rs->MoveNext() ;
}
unset($rs) ;
##
$_end_date = (date("Y")-1911).date("md") ;
$_start_date = date("Y-m-d",mktime(0,0,0,date("m"),(date("d")-6),date("Y"))) ;		// 設定(6+1)天的顯示日期範圍
$tmp = explode('-',$_start_date) ;
$_start_date = ($tmp[0] - 1911).$tmp[1].$tmp[2] ;
unset($tmp) ;

if ($_SESSION['member_income'] == '1'){
	$str = '1=1';
}else{
	$str = 'scr.sUndertaker1 = "'.$_SESSION['member_id'].'"';
}


if ($textfield) {
	$str .= ' AND ec.eDepAccount LIKE "%'.$textfield.'"';
}else{
	$str .= ' AND ec.eTradeDate>="'.$_start_date.'" AND ec.eTradeDate<="'.$_end_date.'"';
}

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
			ec.eBankBranch,
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
			'.$str.'
			AND 
			IF( eDepAccount LIKE "0096988%" ,eTicketStatus IN ("S","P","D","OK","T","B"),eTicketStatus ="")  
		ORDER BY 
			ec.eSms,ec.eTradeDate 
		ASC
	';
	
	$rs = $conn->Execute($sql) ;
	$max = $rs->RecordCount() ;
	$i = 0 ;
	while(!$rs->EOF) {
		$list[$i] = $rs->fields ;


		$tmp = explode(' ', $list[$i]['eRegistTime']);
		$_y = substr($list[$i]["eTradeDate"],0,3) + 1911;
		$_m = substr($list[$i]["eTradeDate"],3,2);
		$_d = substr($list[$i]["eTradeDate"],5,2);
		$list[$i]["eTradeDate"] = $_y ."-" .$_m . "-" . $_d." ".substr($tmp[1],0,5);


		unset($tmp);
		if (preg_match("/60001/",$list[$i]['eDepAccount'])) {
			$list[$i]['eBankBranch'] = substr($list[$i]['ePayTitle'], 0,3);
		}else{

		}

		// elseif(preg_match("/99985/",$list[$i]['eAccount']) || preg_match("/99986/",$list[$i]['eAccount'])){

		// }else if (preg_match("/98988/",$list[$i]['eAccount'])) {
		// 	# code...
		// }

		if($list[$i]['eSms'] > 0){
			$list[$i]['color']='#CCCCCC';
		}else{
			$list[$i]['color']='#ffffff';
		}


		if ((preg_match("/10401810001999/",$list[$i]['eAccount'])) || (preg_match("/12601800015999/",$list[$i]['eAccount'])) ) {

			$list[$i]['chequeType'] = '託收票';
						//若信託帳戶為永豐
			if ($list[$i]['eCheckDate'] == '0000000') {
				$list[$i]['chequeType'] = '次交票' ;
			}
			##
			
		}elseif ($list[$i]['eExpenseDate']!='') { //一銀託收票
			$list[$i]['chequeType'] = '託收票';
		}elseif($list[$i]['eTicketStatus'] != ''){ //台新
			if ($rs->fields['eTicketStatus'] =='S') { //T:在途
				$list[$i]['chequeType'] = '託收票';
			}elseif ($rs->fields['eTicketStatus'] == 'P') {
				$list[$i]['chequeType'] = '次交票' ;
			}elseif($rs->fields['eTicketStatus'] == 'D'){
				$list[$i]['chequeType'] = '退票' ;
			}elseif ($rs->fields['eTicketStatus'] == 'B') {
				$list[$i]['chequeType'] = $list[$i]['ePayTitle'];
			}
		}else {
			$list[$i]['chequeType'] = '次交票' ;
		}

		$list[$i]['sUndertaker1'] = ($list[$i]['sUndertaker1'])?$list[$i]['sUndertaker1']:'';

		
		if ($list[$i]['eSms'] != 1) {

  			if ($list[$i]['eSms'] == 2) {
  				$list[$i]['SmsChecked'] = "checked=checked";
  			}
  		}

  		##
  		// $_status = $list[$i]['eTradeStatus'] ;


			switch ($list[$i]['eTradeStatus']) {
				case '1':
					$list[$i]['eTradeStatus'] = '沖正交易' ;
					break;
				case '9':
					$list[$i]['eTradeStatus'] = '被沖正交易' ;
					break;
				case '10':
					$list[$i]['eTradeStatus'] = '入庫' ;
					break;
				case '11':
					$list[$i]['eTradeStatus'] = '入庫-延期提示' ;
					break;
				case '12':
					$list[$i]['eTradeStatus'] = '入庫-領回' ;
					break;
				case '20':
					$list[$i]['eTradeStatus'] = '出庫' ;
					break;
				case '22':
					$list[$i]['eTradeStatus'] = '出庫-領回' ;
					break;
				case '30':
					$list[$i]['eTradeStatus'] = '退票' ;
					break;
				case '31':
					$list[$i]['eTradeStatus'] = '本埠退票' ;
					break;
				case '40':
					$list[$i]['eTradeStatus'] ='銷帳';
					break;
				case '48':
					$list[$i]['eTradeStatus'] ='退票通知沖正';
					break;
				case '49':
					$list[$i]['eTradeStatus'] ='即時銷入帳';
					break;

				default:
					$list[$i]['eTradeStatus'] = '正常交易' ;
					break;
			}

  		##

		$i++;
		$rs->MoveNext() ;
	}


// $_start_date = date("Y-m-d",mktime(0,0,0,date("m"),(date("d")-6),date("Y"))) ;		// 設定(6+1)天的顯示日期範圍



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
		<td colspan="10">
		日期範圍：
<?php

echo substr($_start_date,0,3).'/'.substr($_start_date,3,2).'/'.substr($_start_date,5,2) ;
echo ' ~ ' ;
echo substr($_end_date,0,3).'/'.substr($_end_date,3,2).'/'.substr($_end_date,5,2) ;

?>
		</td>
	</tr>

	<tr>
		<td width="12%">通知日期</td>
		<td width="10%">保證帳號</td>
		<td width="10%">支票金額</td>
		<td width="10%">交易狀態</td>
		<td width="10%">票據種類</td>
		<td width="10%">地政士</td>
		<td width="10%">經辦</td>
		<td width="10%">簡訊通知</td>
		<td width="10%">不寄送簡訊</td>
		<td width="8%">受理行<br />代碼</td>
	</tr>

	<?php foreach ($list as $k => $v): ?>
		<tr style="background-color:<?=$v['color']; ?>">
				<td><?=$v['eTradeDate']?>(<?=$conBank[$v['eAccount']]['cBankName']?>)</td>
				<td><?=substr($v['eDepAccount'],7)?></td>
				<td><?=(substr($v['eLender'],0,13)+1-1)?></td>
				<td><?=$v['eTradeDate']?></td>
				<td><?=$v['eTradeStatus']?></td>
				<td><?=$v['sName']?></td>
				<td><?=$v['sUndertaker1']?></td>
				<td>
					<a href="expense_cheque_sms.php?id=<?=$v['id']?>&sid=<?=$v['bSID'];?>"><img src="images/sms.png" border="0" width="50px" height="50px"></a>
				</td>
				<td align="center">
					<?php if ($v['eSms'] != 1): ?>
						<input type="checkbox" name="no" onclick="UnSend(<?=$v['id']?>)" <?=$v['SmsChecked']?>/>
					<?php endif ?>
					
				</td>
				<td><?=$v['eBankBranch']?></td>
		</tr>
	<?php endforeach ?>
	

</table>
</div>
</form>
</body>
</html>
