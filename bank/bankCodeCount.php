<?php
include_once '../openadodb.php' ;

$_POST = escapeStr($_POST) ;

$id = $_POST['id'];
$bank = $_POST['bank'];

$sql = "SELECT cBankVR,cShowName FROM tContractBank WHERE cId ='".$bank."'";

$rs = $conn->Execute($sql);

$bankData = $rs->fields;
##

##
$sql = "SELECT * FROM tBankCode WHERE bSID ='".$id."' AND bAccount LIKE '".$bankData['cBankVR']."%' AND bDel = 'n' AND bUsed = 0";
// echo $sql;
$rs = $conn->Execute($sql);

while (!$rs->EOF) { //1台屋2非仲介49優美  1加盟2直營3非仲介
	// $brand = ($rs->fields['bBrand']==1)? '台屋':($rs->fields['bBrand']==2)? '非仲介成交':'優美';

	if ($rs->fields['bBrand']==1 && $rs->fields['bCategory'] == 1) {
		$brand = '台屋加盟';
	}elseif ($rs->fields['bBrand']==1 && $rs->fields['bCategory'] == 2) {
		$brand = '台屋直營';
	}elseif ($rs->fields['bBrand']== 49) {
		$brand = '優美地產';
	}else{
		$brand = '非仲介成交';
	}

	$app = '';
	//bApplication 1土地2建物3預售屋
	if ($rs->fields['bApplication'] == 1) {
		$app = '土地';
	}elseif ($rs->fields['bApplication'] == 2) {
		$app = '建物';
	}elseif ($rs->fields['bApplication'] == 3) {
		$app = '預售屋';
	}else{
		$app = '未知';
	}

	$bankCount[$brand][$app]++;

	$rs->MoveNext();
}
// echo $sql;
?>

<table cellpadding="0" cellspacing="0" border="1" width="45%">
	<tr>
		<th colspan="3"><?=$bankData['cShowName']?>-未使用保證號碼</th>
	</tr>
	<tr>
		<td align="center">品牌</td>
		<td align="center">合約書種類</td>
		<td align="center">數量</td>
	</tr>
	<tr>
		<th rowspan="5" width="20%">台屋加盟</th>	
	</tr>
	<tr>
		<td width="20%">土地</td>
		<td width="10%"><?=($bankCount['台屋加盟']['土地'] == '')? '0' : $bankCount['台屋加盟']['土地'] ?></td>
	</tr>
	<tr>
		<td>建物</td>
		<td><?=($bankCount['台屋加盟']['建物'] == '')? '0' : $bankCount['台屋加盟']['建物'] ?></td>			
	</tr>
	<tr>
		<td>預售屋</td>
		<td><?=($bankCount['台屋加盟']['預售屋'] == '')? '0' : $bankCount['台屋加盟']['預售屋'] ?></td>		
	</tr>
	<tr>
		<td>未知</td>
		<td><?=($bankCount['台屋加盟']['未知'] == '')? '0' : $bankCount['台屋加盟']['未知'] ?></td>
	</tr>
	<tr>
		<th rowspan="5">台屋直營</th>		
	</tr>
	<tr>
		<td>土地</td>
		<td><?=($bankCount['台屋直營']['土地'] == '')? '0' : $bankCount['台屋直營']['土地'] ?></td>			
	</tr>
	<tr>
		<td>建物</td>
		<td><?=($bankCount['台屋直營']['建物'] == '')? '0' : $bankCount['台屋直營']['建物'] ?></td>			
	</tr>
	<tr>
		<td>預售屋</td>
		<td><?=($bankCount['台屋直營']['預售屋'] == '')? '0' : $bankCount['台屋直營']['預售屋'] ?></td>		
	</tr>
	<tr>
		<td>未知</td>
		<td><?=($bankCount['台屋直營']['未知'] == '')? '0':$bankCount['台屋直營']['未知'] ?></td>	
	</tr>
	<tr>
		<th rowspan="5">優美地產</th>		
	</tr>
	<tr>
		<td>土地</td>
		<td><?=($bankCount['優美地產']['土地'] == '')? '0':$bankCount['優美地產']['土地'] ?></td>			
	</tr>
	<tr>
		<td>建物</td>
		<td><?=($bankCount['優美地產']['建物'] == '')? '0':$bankCount['優美地產']['建物'] ?></td>			
	</tr>
	<tr>
		<td>預售屋</td>
		<td><?=($bankCount['優美地產']['預售屋'] == '')? '0':$bankCount['優美地產']['預售屋'] ?></td>		
	</tr>
	<tr>
		<td>未知</td>
		<td><?=($bankCount['優美地產']['未知'] == '')?'0':$bankCount['優美地產']['預售屋']?></td>	
	</tr>
	<tr>
		<th rowspan="5">非仲介成交</th>		
	</tr>
	<tr>
		<td>土地</td>
		<td><?=($bankCount['非仲介成交']['土地'] == '')? '0':$bankCount['非仲介成交']['土地'] ?></td>			
	</tr>
	<tr>
		<td>建物</td>
		<td><?=($bankCount['非仲介成交']['建物'] =='')?'0':$bankCount['非仲介成交']['建物'] ?></td>			
	</tr>
	<tr>
		<td>預售屋</td>
		<td><?=($bankCount['非仲介成交']['預售屋'] =='')?'0':$bankCount['非仲介成交']['預售屋'] ?></td>		
	</tr>
	<tr>
		<td>未知</td>
		<td><?=($bankCount['非仲介成交']['未知']=='')?'0':$bankCount['非仲介成交']['未知'] ?></td>	
	</tr>
	
	
</table>