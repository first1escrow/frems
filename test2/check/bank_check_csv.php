<?php
include_once ('../openadodb.php') ;


$fp = fopen('../excel/AllBank20160630.csv', 'r');
$i = 0;
while(!feof($fp)){

	$line = fgets($fp);
			
	if ($i > 0) {
		$tmp = explode(',', $line);

		$data[$j]['bBank3'] =$tmp[0];

		$data[$j]['bBank4'] =$tmp[1];
		$data[$j]['bBank3_name'] =str_replace('　', '',$tmp[4]);
		$data[$j]['bBank4_name'] =str_replace('　', '',$tmp[3]);
		$data[$j]['bBank_address'] =str_replace('　', '',$tmp[7]);

		$tmp2 = explode(' ', $tmp[5]);

		$data[$j]['bBank_area'] =$tmp2[0];
		$data[$j]['bBank_tel'] =str_replace('　', '',$tmp2[1]);
		// $data[$j]['bBank3'] =$tmp[0];
		// $data[$j]['bBank3'] =$tmp[0];

		// echo 'read:'.$data[$j]['bBank3']."_".$data[$j]['bBank4']."_".$data[$j]['bBank3_name']."_".$data[$j]['bBank4_name']."_".$data[$j]['bBank_address'];
		// echo $data[$j]['bBank_area']."_".$data[$j]['bBank_tel]']."\r\n";
		$j++;
		unset($tmp);unset($tmp2);
	}

	$i++;
			
}

$j= 0;
//總代號,分支代號,總機構名稱,分支機構名稱,簡稱,聯絡電話,生效日期,地址,備註,所屬共用中心,異動原因
for ($i=0; $i < count($data); $i++) { 
	$sql = "SELECT * FROM tBank WHERE bBank3 = '".$data[$i]['bBank3']."' AND bBank4='".$data[$i]['bBank4']."'";
	// echo $sql;
	$rs = $conn->Execute($sql);
	
	if ($rs->fields['bId']) {
		if ($data[$i]['bBank4_name'] != $rs->fields['bBank4_name']|| $data[$i]['bBank3_name'] != $rs->fields['bBank3_name'] || $data[$i]['bBank_address'] != $rs->fields['bBank_address']) {
			
			$list[$j] = $data[$i];

			if ($data[$i]['bBank4_name'] != $rs->fields['bBank4_name']) {
				$list[$j]['error'] .= '分行錯誤_';
			// || 
			}

			if ($data[$i]['bBank3_name'] != $rs->fields['bBank3_name']) {
				$list[$j]['error'] .= '簡稱錯誤_';
			}

			if ($data[$i]['bBank_address'] != $rs->fields['bBank_address']) {
				$list[$j]['error'] .= '地址錯誤_';
			}

			if ($data[$i]['bBank_area']  != $rs->fields['bBank_area']) {
				$list[$j]['error'] .= '區碼錯誤_';
			}

			if ($data[$i]['bBank_tel']  != $rs->fields['bBank_tel']) {
				$list[$j]['error'] .= '電話錯誤_';
			}
			$j++;
		}


	}else{
		$list[$j] = $data[$i];
		$list[$j]['error']= '沒有該分行';
		

		$j++;
	}
	
}

$tbl = '<table cellpadding="0" cellspacing="0">';
$tbl .='<tr>
			<th>總代號</th>
			<th>分行代號</th>
			<th>分行名稱</th>
			<th>簡稱</th>
			<th>地址</th>
			<th>聯絡電話(區碼)</th>
			<th>聯絡電話</th>
			<th>錯誤原因</th>
		
		</tr>';
for ($i=0; $i < count($list); $i++) { 

	$tbl .='<tr>
				<td style="border:1px solid;">'.$list[$i]['bBank3'].'</td>
				<td style="border:1px solid;">'.$list[$i]['bBank4'].'</td>				
				<td style="border:1px solid;">'.$list[$i]['bBank4_name'].'</td>
				<td style="border:1px solid;">'.$list[$i]['bBank3_name'].'</td>
				<td style="border:1px solid;">'.$list[$i]['bBank_address'].'</td>
				<td style="border:1px solid;">'.$list[$i]['bBank_area'].'</td>
				<td style="border:1px solid;">'.$list[$i]['bBank_tel'].'</td>
				<td style="border:1px solid;">'.$list[$i]['error'].'</td>
			</tr>';

	
}

$tbl .= '<table>';

echo $tbl;
?>


<html>
	<table cellpadding="" cellspacing=""></table>
</html>