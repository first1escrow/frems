<?php
include_once '../openadodb.php' ;


$People = trim(addslashes($_POST['People']));
$date_start = trim(addslashes($_POST['date_start']));
$date_end = trim(addslashes($_POST['date_end']));
$page = addslashes(trim($_POST['page'])); //目前頁數


if ($People=='') {
	die('無查詢對象');
}

// echo $page."<br>";

##頁數
if (!$page) {
	$page=1;
	
}

$row=31;
$limit_s = ($page-1)*$row;
$limit_e = $limit_s+$row; //結束筆數
##
##查詢字串
if (!empty($date_start)) {
	
	$date_start = date_change($date_start)."";

	$query_date = "AND tBankLoansDate >='".$date_start."'";
	
}

if (!empty($date_end)) {

	$date_end = date_change($date_end)."";
	$query_date .= " AND tBankLoansDate<='".$date_end."'";
	
}



	$sql  = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(5,6) AND pBankTrans IN(1,2) AND pId!=6 ORDER BY pId ASC ";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$tmp[] = $rs->fields['pId']; //選項
		// $data_People[]=$rs->fields['pId']; //被選取的

		$rs->MoveNext();
	}

	$str = implode(',',$tmp) ;
	
	$query = " AND s.sUndertaker1 IN (".$str.")";

unset($tmp);
##
//以各保號的承辦人計算和媒體檔‧SP:保號000000000(永豐)000000008(台新)利息出款
//s.sUndertaker1、name:地政士的經辦 ； OwnerId、bt.tOwner:出款的人
$sql = "
		SELECT 
			bt.tMemo,
			s.sUndertaker1,
			(SELECT pName FROM tPeopleInfo WHERE pId=s.sUndertaker1) as name,
			(SELECT pTest FROM tPeopleInfo WHERE pId=bt.tOwner) as pTest,
			(SELECT pCategory_stime FROM tPeopleInfo WHERE pName=bt.tOwner) as cat_stime,
			(SELECT pCategory_etime FROM tPeopleInfo WHERE pName=bt.tOwner) as cat_etime,
			(SELECT pId FROM tPeopleInfo WHERE pName=bt.tOwner) as OwnerId,
			bt.tOwner,
			bt.tBankLoansDate
		FROM 
		 	tBankTrans AS bt
		LEFT JOIN 
			tContractScrivener AS cs ON cs.cCertifiedId=bt.tMemo		
		LEFT JOIN 
			tScrivener AS s ON cs.cScrivener=s.sId
		WHERE
			bt.tExport='1' ".$query_date.$query."  OR (tMemo IN ('000000000','000000008') ".$query_date.") 
		
		ORDER BY tBankLoansDate ASC";

// echo $sql;

$rs = $conn->Execute($sql);
// $total=$rs->RecordCount();//計算總筆數
while (!$rs->EOF) {

	##利息出款
	if ($rs->fields['tMemo']=='000000000' || $rs->fields['tMemo']=='000000008') {//如果是利息出款則取建檔者
		
		$rs->fields['sUndertaker1']=$rs->fields['OwnerId'];
		$rs->fields['name']=$rs->fields['tOwner'];
	}
	##
	
	// $list = $rs->fields;
	$list[$rs->fields['sUndertaker1']]['name'] = $rs->fields['name']; //姓名

	$list[$rs->fields['sUndertaker1']][$rs->fields['tBankLoansDate']]['count'] = $list[$rs->fields['sUndertaker1']][$rs->fields['tBankLoansDate']]['count']+1; //單日出款數
	$list[$rs->fields['sUndertaker1']]['total'] = $list[$rs->fields['sUndertaker1']]['total']+1; //總出款數

	##代理出款筆數(幫別人出款)

	if ($rs->fields['sUndertaker1'] != $rs->fields['OwnerId'] && $rs->fields['pTest'] != 1) { //($rs->fields['tBankLoansDate'] >= $rs->fields['cat_etime'] && $rs->fields['cat_etime'] !='0000-00-00') || $rs->fields['cat_etime']=='0000-00-00' && ($rs->fields['tOwner'] !=33 ||$rs->fields['tOwner'] !=32)
		
		
		if (strtotime($rs->fields['tBankLoansDate']) >= strtotime($rs->fields['cat_stime']) && strtotime($rs->fields['tBankLoansDate']) <= strtotime($rs->fields['cat_etime'])) {
			//時間範圍內的不算
		}else{
			$list[$rs->fields['sUndertaker1']][$rs->fields['tBankLoansDate']]['unextra'] = $list[$rs->fields['sUndertaker1']][$rs->fields['tBankLoansDate']]['unextra']+1;//幫別人出的單日出款數

			$list[$rs->fields['OwnerId']][$rs->fields['tBankLoansDate']]['extra'] = $list[$rs->fields['OwnerId']][$rs->fields['tBankLoansDate']]['extra']+1;//被別人出的單日出款數


			$list[$rs->fields['sUndertaker1']]['unextra'] = $list[$rs->fields['sUndertaker1']]['unextra']+1;//幫別人出的總出款數

			$list[$rs->fields['OwnerId']]['extra'] = $list[$rs->fields['OwnerId']]['extra']+1;//被別人出的總出款數
		}
			
		
			
			
		// }
	}

	

	$rs->MoveNext();
}

// echo "<pre>";
// print_r($list);
// echo "</pre>";
// die;

//表一
$tb1 = '<table border="0" class="tb">
		<tr>
			<th>姓名</th>
			<th>本家出款筆數</th>
			<th>代理出款筆數</th>
			<th>被代理出款數</th>
			<th>合計總筆數</th>
		</tr>

		';
//表二
$tb2 = '<table border="0" class="tb">
			<tr>
	                                                    
	            <th>姓名</th>
	            <th>日期</th>
	            <th>本家出款筆數</th>
				<th>代理出款筆數</th>
				<th>被代理出款數</th>
				<th>合計總筆數</th>
	        </tr>';
        
$arr = explode(',', $People);
$j=0;
for ($i=0; $i < count($arr); $i++) { 


	if ($i%2==0) {
		$color = "#FFFFFF";
	}else
	{
		$color = "#F8ECE9";
	}

	$sql = "SELECT pName FROM tPeopleInfo WHERE pId='".$arr[$i]."'";

	$rs = $conn->Execute($sql);

	$name = $rs->fields['pName'];

	$real_total = $list[$arr[$i]]['total']+$list[$arr[$i]]['extra']-$list[$arr[$i]]['unextra'];

	
		$tb1 .= '<tr style="background-color:'.$color.'">
				<td>'.$name.'</td>
				<td>'.$list[$arr[$i]]['total'].'</td>
				<td>'.$list[$arr[$i]]['extra'].'</td>
				<td>'.$list[$arr[$i]]['unextra'].'</td>
				<td>'.$real_total.'</td>
			</tr>';
	
	
	
	
	if (is_array($list)) {
		foreach ($list[$arr[$i]] as $k => $v) {

			

			if ($k!='name' && $k!='total' && $k != 'extra' && $k != 'unextra' ) {

				$tmp[$j]['name']= $name;
				$tmp[$j]['date']=$k;	
				$tmp[$j]['count'] = $v['count'];
				$tmp[$j]['extra'] = $v['extra'];
				$tmp[$j]['unextra'] = $v['unextra'];
				$tmp[$j]['total'] = $v['count']+$v['extra']-$v['unextra'];

				
				$j++;
			}
			

			
		}
	}

	unset($real_total);
	
}
	

$total = count($tmp); //總數
// echo $total;
if ($total<=$limit_e) { //如果總筆數小於顯示數
	$limit_e = $total;
}
// echo $limit_s.$limit_e;

for ($i=$limit_s; $i <$limit_e; $i++) { 
	
	$tb2 .='<tr>
				<td style="border: 1px solid #CCC;">'.$tmp[$i]['name'].'</td>
				<td style="border: 1px solid #CCC;">'.$tmp[$i]['date'].'</td>
				<td style="border: 1px solid #CCC;">'.$tmp[$i]['count'].'</td>
				<td style="border: 1px solid #CCC;">'.$tmp[$i]['extra'].'</td>
				<td style="border: 1px solid #CCC;">'.$tmp[$i]['unextra'].'</td>
				<td style="border: 1px solid #CCC;">'.$tmp[$i]['total'].'</td>
			</tr>';
		
}

$tb1 .='</table>';

##
$total_page= ceil($total/$row);//總頁數
##

##頁數下拉

for ($i=1; $i <=$total_page ; $i++) { 

	if ($page==$i) {
		$op .= '<option value="'.$i.'" selected>'.$i.'</option>';
	}else
	{
		$op .= '<option value="'.$i.'">'.$i.'</option>';
	}

	

}
##
if ($limit_s==0) {
	$limit_s=1;
}

$tb2 .='<tr>
			<td colspan="4" align="center">
				第 <select name="page" onchange="changePage()">'.$op.'</select> 頁 ／共'.$total_page.'頁    顯示第 '.$limit_s.' 筆到第 '.$limit_e.' 筆的紀錄，共 '.$total.' 筆紀錄
			</td>
		</tr>';

$tb2 .='</table>';

echo $tb1."<br>";
echo $tb2;
##



function date_change($str)
{
	$tmp = explode('-', $str);

	$tmp[0] = 1911+$tmp[0];

	$str = $tmp[0].'-'.$tmp[1].'-'.$tmp[2];

	return $str;
}
?>