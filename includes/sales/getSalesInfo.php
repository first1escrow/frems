<?php
##//使用率、成長率、貢獻率((該月)/前月)*100%[BY月]
function getPercent($sales,$year,$month,$last,$now,$type)
{
	if ($month == 1) {
		$date_s = ($year-1).'-12-01 00:00:00'; 
		$date_e = ($year-1).'-12-31 23:59:59'; 
		
		if ($sales == 25) {
			$tmp = getOwnCase($sales,$date_s,$date_e);
		}else{
			$tmp = getOwnCase($sales,$date_s,$date_e);
		}

		

		if ($type == 'u') {
			// $last = $tmp['use']; //使用率
			$utmp = getUseMonth($sales,$date_s,$date_e);
			if (is_array($utmp)) {
				foreach ($utmp as $k => $v) {
					$last += $v;
				}
			}
			unset($utmp);
		}
		else if ($type == 'g') {
			$last = $tmp['tw'] + $tmp['other'];//成長率
		}
		elseif ($type == 'c') { //貢獻率
			$last = $tmp['cCertifiedMoney']-$tmp['feedBackMoney'];
		}
	}
		
	if ($last > 0) {
		//$use = round((($now-$last)/$last)*100);
		$use = round((($now/$last)-1)*100);
	}
	else{
		$use = 0;
	}
	
	return $use;
}

##//使用率、成長率、貢獻率((該季)/該季)*100%[BY季]
function getPercent2($sales,$year,$s,$last,$now,$type)
{
	// echo $sales.",".$year.",".$s.",".$last.",".$now.",".$type."<br>";
	if ($s == 1) {
		$date_s = ($year-1).'-10-01 00:00:00'; 
		$date_e = ($year-1).'-12-31 23:59:59'; 
		
		if ($sales == 25) {
			$tmp = getOwnCase($sales,$date_s,$date_e);
		}else{
			$tmp = getOwnCase($sales,$date_s,$date_e);
		}

		if ($type == 'u') {
			$utmp = getUseMonth($sales,$date_s,$date_e);
			if (is_array($utmp)) {
				foreach ($utmp as $k => $v) {
					$last += $v;
				}
			}
			unset($utmp);
			
			// $last = $tmp['use']; //使用率
		}elseif ($type == 'g') {
			$last = $tmp['tw'] + $tmp['other'];//成長率
		}elseif ($type == 'c') { //貢獻率
			$last = $tmp['cCertifiedMoney']-$tmp['feedBackMoney'];
		}
	}
		
	if ($last > 0) {
		//$use = round((($now-$last)/$last)*100);
		
		$use = round(($now/$last)*100);

	}
	else{
		$use = 0;
	}
	
	return $use;
}


##//使用率、成長率、貢獻率((該季)/該季)*100%[BY季] (本季跟上一季相差)
function getPercent3($sales,$year,$s,$last,$now,$type)
{
	$use = 0;
	// echo $sales.",".$year.",".$s.",".$last.",".$now.",".$type."<br>";
	// echo $year;
	if ($s == 1) { //第一季要求出上一季的百分比
		$date_s = ($year-1).'-10-01 00:00:00'; 
		$date_e = ($year-1).'-12-31 23:59:59'; 
		// echo ($year-1);
		

		//上一年的第三季
		$date_s2 = ($year-1).'-07-01 00:00:00'; 
		$date_e2 = ($year-1).'-09-30 23:59:59'; 
		
		if ($sales == 25) {
			$tmp = getOwnCase($sales,$date_s,$date_e);
			$tmp2 = getOwnCase($sales,$date_s2,$date_e2);
		}else{
			$tmp = getOwnCase($sales,$date_s,$date_e);
			$tmp2 = getOwnCase($sales,$date_s2,$date_e2);
		}

		
		if ($type == 'u') {//使用率
			$tmp['use'] = 0;
			$utmp = getUseMonth($sales,$date_s,$date_e);
			if (is_array($utmp)) {
				foreach ($utmp as $k => $v) {
					$tmp['use'] += $v;
				}
			}
			unset($utmp);

			$tmp2['use'] = 0;
			$utmp = getUseMonth($sales,$date_s2,$date_e2);
			if (is_array($utmp)) {
				foreach ($utmp as $k => $v) {
					$tmp2['use'] += $v;
				}
			}
			unset($utmp);

			$last = round(($tmp['use']/$tmp2['use'])*100);
			// echo "last".$tmp['use']."/".$tmp2['use']."=".($tmp['use']/$tmp2['use'])."<br>";
			// echo '9:'.$tmp['use'];
			// // echo $now;
			// die;
			// // $last = $tmp['use']; 
		}elseif ($type == 'g') {

			$last = round((($tmp['tw'] + $tmp['other'])/($tmp2['tw'] + $tmp2['other']))*100);
			// echo $last.'g'.$now;
		}elseif ($type == 'c') { //貢獻率
			$last = round((($tmp['cCertifiedMoney']-$tmp['feedBackMoney'])/($tmp2['cCertifiedMoney']-$tmp2['feedBackMoney']))*100);
			// $last = $tmp['cCertifiedMoney']-$tmp['feedBackMoney'];
		}elseif ($type == 't') {
			
			$last = getTargetLast($sales,$year);
		}
	}
	
	// if ($type != 'u') {
		// if ($now > 100) { $now = 100;}
		// if ($last > 100) { $last = 100;}
	// }

	

	$y = date('Y');
	$m = date('m');

	if ($year==$y) {
		if ($today <= 3) {
			if ($s == 1) {
				$use = $now-$last;
			}
			
		}else if ($today > 3 && $today <= 6) {
			if ($s == 2) {
				$use = $now-$last;
			}
			
		}else if ($today >6 && $today <=9) {
			if ($s == 3) {
				$use = $now-$last;
			}
		}else if ($today >9 && $today <=12) {
			if ($s == 4) {
				$use = $now-$last;
			}
		}
	}else{
		$use = $now-$last;
	}
	


	
	
	
	return $use;
}

function getTargetLast($sales,$year)
{

	
	$j = 0;
	for ($i=10; $i <=12 ; $i++) { 
		$date_start = ($year-1).'-'.str_pad($i,2,'0',STR_PAD_LEFT).'-01 00:00:00';
		$date_end = ($year-1).'-'.str_pad($i,2,'0',STR_PAD_LEFT).'-31 23:59:59';

		
		//新進店總數 (該月新進仲介店數+該月新進地政士數)
		$summary1[$j]['targetcount'] = count(getOwnBranch($sales,$date_start,$date_end)) + count(getOwnScrivener($sales,$date_start,$date_end));
		$summary1[$j]['target'] = getOwnStoreTarget($summary1[$j]['targetcount']);
		$j++;
		
	}
	// 	echo "<pre>";
	// 	print_r($summary1);
	// echo "</pre>";
	for ($i=0; $i < count($summary1); $i++) { 
		$t = $summary1[$i]['targetcount'];

		if (($year-1911) <= 105) {
			
			#簽約數/達成率
			//後面大於10 則把多出來的加到前一個去(超過季度的月份不加3
			//[4不算在同一個季度] 6[7不算在同一個季度] 9[10不算在同一個季度] 12[1不算在同一個季度] )
			if (($i%3) != 0 && $summary1[$i]['targetcount'] < 10 && $summary1[($i+1)]['targetcount'] >10) { 
				$t = getMoreTargetCount($summary1[$i]['targetcount'],$summary1[($i+1)]['targetcount']);
				// echo $t."-";
			}
			
			//大於100%的算100%
			if ($t > 10) {$t = 10;} //數量

		 
			$target = $target+($t*10);
		}else{
			$target = $target +($t*10);
		}
		
		
	}

	$last = round(($target/3));//達成率
	// echo $last;

	return $last;
}




//取得該業務案件(依區域劃分)FRO 
function getOwnCase($sales,$date_start,$date_end)
{
	global $conn;

	

	$data['other'] = 0;
	$data['tw'] = 0;
	$data['unTW'] = 0;
	$data['Scrivener'] = 0;
	$data['cCertifiedMoney'] = 0;
	$data['feedBackMoney'] = 0;
	$data['use'] = 0;

	
	// if ($query) {
		$sql ='
			SELECT 
				cas.cCertifiedId as cCertifiedId,
				cas.cSignDate as cSignDate, 
				csc.cScrivener as cScrivener, 
				rea.cBrand as brand,
  				rea.cBrand1 as brand1,
  				rea.cBrand2 as brand2,
				rea.cBranchNum as branch,
				rea.cBranchNum1 as branch1,
				rea.cBranchNum2 as branch2,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ), LPAD(rea.cBranchNum,5,"0")) AS branchcode,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ), LPAD(rea.cBranchNum1,5,"0")) AS branchcode1,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ), LPAD(rea.cBranchNum2,5,"0")) AS branchcode2,
				CONCAT("SC", LPAD(csc.cScrivener,4,"0")) AS scrcode,
				ci.cCertifiedMoney,
				cas.cCaseFeedBackMoney,
				cas.cCaseFeedBackMoney1,
				cas.cCaseFeedBackMoney2,
				cas.cSpCaseFeedBackMoney,				
				(SELECT bSales FROM tBranchSales WHERE bBranch = rea.cBranchNum LIMIT 1) AS bSales1,
				(SELECT bSales FROM tBranchSales WHERE bBranch = rea.cBranchNum1 LIMIT 1) AS bSales2,
				(SELECT bSales FROM tBranchSales WHERE bBranch = rea.cBranchNum2 LIMIT 1) AS bSales3,
				(SELECT sSales FROM tScrivenerSales WHERE sScrivener = cScrivener LIMIT 1) AS sSales,
				(SELECT bSales FROM tBranch WHERE bId = rea.cBranchNum) AS bPreSales1,
				(SELECT bSales FROM tBranch WHERE bId = rea.cBranchNum1) AS bPreSales2,
				(SELECT bSales FROM tBranch WHERE bId = rea.cBranchNum2) AS bPreSales3,
				(SELECT sSales FROM tScrivener WHERE sId = cScrivener) AS sPreSales,
				(SELECT bSalesDate FROM tBranch WHERE bId = rea.cBranchNum) AS bPreSalesDate1,
				(SELECT bSalesDate FROM tBranch WHERE bId = rea.cBranchNum1) AS bPreSalesDate2,
				(SELECT bSalesDate FROM tBranch WHERE bId = rea.cBranchNum2) AS bPreSalesDate3,
				(SELECT sSalesDate FROM tScrivener WHERE sId = cScrivener) AS sPreSalesDate

			FROM 
				tContractCase AS cas 
			LEFT JOIN 
				tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
			LEFT JOIN 
				tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId 
			LEFT JOIN 
				tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId
			LEFT JOIN
				tContractIncome AS ci ON ci.cCertifiedId=cas.cCertifiedId
			WHERE
				cas.cSignDate >= "'.$date_start.'" AND cas.cSignDate <= "'.$date_end.'"
				AND cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"
				AND cas.cCaseStatus<>"8" 
				'.$query.' 
			GROUP BY
				cas.cCertifiedId
			ORDER BY 
				cas.cApplyDate,cas.cId,cas.cSignDate ASC;
		' ;
		// echo $sql."<br>";
		// if ($_SESSION['member_id'] == 6) {
		// 	echo $sql;
		// 	echo "<br>";
		// }
		
		$rs = $conn->Execute($sql);
		
		while (!$rs->EOF) {
			
			
			if (checkSales($rs->fields,$sales)) {
				$count = 0 ;
				$part = 0;

			 	if ($rs->fields['brand'] > 0 ) {$count++;}
			 	if ($rs->fields['brand1'] > 0 ) {$count++;}
			 	if ($rs->fields['brand2'] > 0 ) {$count++;}
	 					
	 			$part  = Round(1/$count,2);
	 			if ($rs->fields['brand'] == $rs->fields['brand1'] && $rs->fields['brand'] == $rs->fields['brand2']) {
					if ($rs->fields['brand'] == 1 || $rs->fields['brand'] == 49 || $rs->fields['brand'] == 56) {

					 $data['tw'] = $data['tw'] + 1 ;
					 // $data['data'][$rs->fields['cCertifiedId']]['tw'] = $part;

					}elseif($rs->fields['brand'] == 2){
						$data['scr'] = $data['scr'] + 1 ;
						$data['Scrivener'] =$data['Scrivener']+ $part;
						// $data['data'][$rs->fields['cCertifiedId']]['scr'] = $part;
					}else{
						 					
						$data['other'] = $data['other']+1;
						$data['unTW'] = $data['unTW']+$part;
						// $data['data'][$rs->fields['cCertifiedId']]['other'] = $part;
						 		
					}
					 		
				}else{
				 	if ($rs->fields['brand'] == 1 || $rs->fields['brand'] == 49 || $rs->fields['brand'] == 56) {
			 			$data['tw'] = $data['tw'] + $part ;
			 			// $tmp[$rs->fields['cCertifiedId']]['tw'] = $part;
			 		}else{
			 			$data['other'] = $data['other']+$part;
			 			// $tmp[$rs->fields['cCertifiedId']]['other'] = $part;
			 			if ($rs->fields['brand'] == 2) {
			 					
							$data['Scrivener'] =$data['Scrivener']+ $part;
			 			}else{
			 				$data['unTW'] = $data['unTW']+$part;
			 			}

			 		}

			 		if ($rs->fields['brand1'] > 0) {
			 			if (($rs->fields['brand1'] == 1 || $rs->fields['brand1'] == 49 || $rs->fields['brand1'] == 56) ) {
				 			$data['tw'] = $data['tw'] + $part ;
				 					// $tmp[$rs->fields['cCertifiedId']]['tw'] += $part;
				 		}else{
				 			$data['other'] = $data['other']+$part;
				 					// $tmp[$rs->fields['cCertifiedId']]['other'] += $part;
				 			if ($rs->fields['brand1'] == 2) {
			 					
								$data['Scrivener'] =$data['Scrivener']+ $part;
				 			}else{
				 				$data['unTW'] = $data['unTW']+$part;
				 			}

				 		}
			 		}

	 				
			 		if ($rs->fields['brand2']) {
			 			if (($rs->fields['brand2'] == 1 || $rs->fields['brand2'] == 49 || $rs->fields['brand2'] == 56) && $rs->fields['brand2'] > 0 ) {
				 			$data['tw'] = $data['tw'] + $part ;
				 			// $tmp[$rs->fields['cCertifiedId']]['tw'] += $part;
				 		}else{
				 			$data['other'] = $data['other']+$part;
				 			// $tmp[$rs->fields['cCertifiedId']]['other'] += $part;
				 			if ($rs->fields['brand2'] == 2) {
			 					
								$data['Scrivener'] =$data['Scrivener']+ $part;
				 			}else{
				 				$data['unTW'] = $data['unTW']+$part;
				 			}
				 		}
			 		}

				}

				//使用量 (++沒意義，最後以陣列數為主)
					$use[$rs->fields['scrcode']]++;//地政士
				//
				if ($rs->fields['branch'] > 0 && $rs->fields['branch'] != 505) //排除非仲介成交
				{
					$use[$rs->fields['branchcode']]++;
				}

				if ($rs->fields['branch1'] > 0 && $rs->fields['branch1'] != 505 )
				{
					$use[$rs->fields['branchcode1']]++;
				}

				if ($rs->fields['branch2'] > 0 && $rs->fields['branch2'] != 505)
				{
					$use[$rs->fields['branchcode2']]++;
				}
				
				
				//貢獻率
				$data['cCertifiedMoney'] = $data['cCertifiedMoney'] + $rs->fields['cCertifiedMoney'];//保證費
				$data['feedBackMoney'] = $data['feedBackMoney'] + $rs->fields['cCaseFeedBackMoney'] + $rs->fields['cCaseFeedBackMoney1'] +$rs->fields['cCaseFeedBackMoney2']+$rs->fields['cSpCaseFeedBackMoney'];//回饋金

				
				unset($type);
				
				
			}
			
			
		
			$rs->MoveNext();
		}



		$data['use'] = count($use);//使用量
		
	// }
	


	
	return $data;
}
// //使用率BY季
// function getUseSeason($sales,$yr)
// {
// 	global $conn;

// 	$month_s = 1;
// 	$month_e = 3;
// 	for ($i=1; $i <= 4; $i++) { 

// 		$start = $yr.'-'.str_pad($month_s,2,'0',STR_PAD_LEFT).'-01 00:00:00';
// 		$end = $yr.'-'.str_pad($month_e,2,'0',STR_PAD_LEFT).'-31 23:59:59';
// 		// echo $start."-".$end;
// 		if ($sales == 25) {
// 			$tmp = getOwnCase25($sales,$start,$end);
// 		}else{
// 			$tmp = getOwnCase($sales,$start,$end);
// 		} 
// 		// print_r($tmp);
// 		$arr['use'.$i] = $tmp['use'];
// 		// die;
// 		$month_s = $month_s+3;
// 		$month_e = $month_e+3;


// 	}

// 	return $arr;
// }




//使用率
function getUseMonth($sales,$date_start,$date_end)
{
	global $conn;

	$zip = getSalesArea($sales);
	$query .= ' AND pro.cZip IN ('.$zip.') ' ;
	
	
	$data['use'] = 0;

	
	if ($query) {
		$sql ='
			SELECT 
				cas.cCertifiedId as cCertifiedId,
				cas.cSignDate as cSignDate, 
				csc.cScrivener as cScrivener, 
				rea.cBrand as brand,
  				rea.cBrand1 as brand1,
  				rea.cBrand2 as brand2,
				rea.cBranchNum as branch,
				rea.cBranchNum1 as branch1,
				rea.cBranchNum2 as branch2,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ), LPAD(rea.cBranchNum,5,"0")) AS branchcode,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ), LPAD(rea.cBranchNum1,5,"0")) AS branchcode1,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ), LPAD(rea.cBranchNum2,5,"0")) AS branchcode2,
				CONCAT("SC", LPAD(csc.cScrivener,4,"0")) AS scrcode,
				ci.cCertifiedMoney,
				cas.cCaseFeedBackMoney,
				cas.cCaseFeedBackMoney1,
				cas.cCaseFeedBackMoney2,
				cas.cSpCaseFeedBackMoney,
				(SELECT bSales FROM tBranchSales WHERE bBranch = rea.cBranchNum LIMIT 1) AS bSales1,
				(SELECT bSales FROM tBranchSales WHERE bBranch = rea.cBranchNum1 LIMIT 1) AS bSales2,
				(SELECT bSales FROM tBranchSales WHERE bBranch = rea.cBranchNum2 LIMIT 1) AS bSales3,
				(SELECT sSales FROM tScrivenerSales WHERE sScrivener = cScrivener LIMIT 1) AS sSales,
				(SELECT bSales FROM tBranch WHERE bId = rea.cBranchNum) AS bPreSales1,
				(SELECT bSales FROM tBranch WHERE bId = rea.cBranchNum1) AS bPreSales2,
				(SELECT bSales FROM tBranch WHERE bId = rea.cBranchNum2) AS bPreSales3,
				(SELECT sSales FROM tScrivener WHERE sId = cScrivener) AS sPreSales,
				(SELECT bSalesDate FROM tBranch WHERE bId = rea.cBranchNum) AS bPreSalesDate1,
				(SELECT bSalesDate FROM tBranch WHERE bId = rea.cBranchNum1) AS bPreSalesDate2,
				(SELECT bSalesDate FROM tBranch WHERE bId = rea.cBranchNum2) AS bPreSalesDate3,
				(SELECT sSalesDate FROM tScrivener WHERE sId = cScrivener) AS sPreSalesDate
			FROM 
				tContractCase AS cas 
			LEFT JOIN 
				tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
			LEFT JOIN 
				tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId 
			LEFT JOIN 
				tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId
			LEFT JOIN
				tContractIncome AS ci ON ci.cCertifiedId=cas.cCertifiedId
			WHERE
				cas.cSignDate >= "'.$date_start.'" AND cas.cSignDate <= "'.$date_end.'"
				'.$query.' 
			GROUP BY
				cas.cCertifiedId
			ORDER BY 
				cas.cApplyDate,cas.cId,cas.cSignDate ASC;
		' ;
			

		$rs = $conn->Execute($sql);
		
		while (!$rs->EOF) {
			// $type = branch_type($conn,$rs->fields);

			if (checkSales($rs->fields,$sales)) {
				$rs->fields['cSignDate'] = preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$rs->fields['cSignDate']);

				$tmp = explode('-', $rs->fields['cSignDate'] );

				//使用量 (++沒意義，最後以陣列數為主)
				if ($use[$rs->fields['scrcode']] == '') {

					$use[$rs->fields['scrcode']] = $tmp[0].$tmp[1];//地政士
					$month[$tmp[0].$tmp[1]]++;
				}
					
				//
				if ($rs->fields['branch'] > 0 && $rs->fields['branch'] != 505 && $use[$rs->fields['branchcode']] == '') //排除非仲介成交
				{
					$use[$rs->fields['branchcode']] = $tmp[0].$tmp[1];
					$month[$tmp[0].$tmp[1]]++;
				}

				if ($rs->fields['branch1'] > 0 && $rs->fields['branch1'] != 505  && $use[$rs->fields['branchcode1']] == '')
				{
					$use[$rs->fields['branchcode1']] = $tmp[0].$tmp[1];
					$month[$tmp[0].$tmp[1]]++;
				}

				if ($rs->fields['branch2'] > 0 && $rs->fields['branch2'] != 505  && $use[$rs->fields['branchcode2']] == '')
				{
					$use[$rs->fields['branchcode2']] = $tmp[0].$tmp[1];
					$month[$tmp[0].$tmp[1]]++;
				}
			}

			
			
		
			// unset($type);
		
			$rs->MoveNext();
		}
	
	}
	

	
		

	
	return $month;
}

//達成率:((該月新進仲介+該月新進地政士)/10)*100%
function getOwnStoreTarget($total,$yr='',$m='',$sales,$targetCount=10) { 

	if ($yr == 106 && $m == 1) {
		// 二月不用加倍
		// 但是一月因為遇到過年
		// 7件100%
		// 少一件-14%
		// 多一件+14%
		$tmp = $total - 7;
		if ($tmp == 0) {
			$target = 100;
		}else{
			$target = 100+($tmp*14);
		}

		if ($total == 0) {
			$target = 0;
		}
		
	}elseif($yr == 108 && $m == 2){
		//2月達標店數是6組
		//每少一組-16
		//超過6組每一組維持加10分

		$tmp = $total - 6;

		if ($tmp > 0) {
			$target = 100+($tmp*10);
		}elseif($tmp == 0){
			$target = 100;
		}else {
			$target = 100+($tmp*16);
		}
	}else{
		$target = round(($total/$targetCount)*100,2);
	}
	

	return $target ;
}

function getOwnStoreTargetFor34($total,$yr='',$m='') { 

	if ($yr == 106 && $m == 1) {
		// 二月不用加倍
		// 但是一月因為遇到過年
		// 7件100%
		// 少一件-14%
		// 多一件+14%
		$tmp = $total - 7;
		if ($tmp == 0) {
			$target = 100;
		}else{
			$target = 100+($tmp*14);
		}

		if ($total == 0) {
			$target = 0;
		}
		
	}elseif($yr == 108 && $m == 2){
		//2月達標店數是6組
		//每少一組-16
		//超過6組每一組維持加10分

		$tmp = $total - 6;

		if ($tmp > 0) {
			$target = 100+($tmp*10);
		}elseif($tmp == 0){
			$target = 100;
		}else {
			$target = 100+($tmp*16);
		}
	}else{
		$target = round(($total/7)*100,2);
	}
	

	return $target ;
}
##
//取得新進地政士
function getOwnScrivener($sales,$date_start,$date_end,$all='',$status='',$city='') {
	global $conn ;
	
	$Scrivener = array();
	$ScrivenerCity = array();

	$tmp = explode(' ', $date_start);
	$date_start = $tmp[0];


	unset($tmp);
	$tmp = explode(' ', $date_end);
	$date_end = $tmp[0];

	if ($all == '') {
		$str = "(ss.sSignDate >='".$date_start."' AND ss.sSignDate <='".$date_end."') AND ";
	}
	// echo $status."s_";
	
	if ($status) {
		$str .= "s.sStatus = 1  AND ";
	}
    $sqlfor103 = '';

    if($date_start == '2024-12-01' and $sales == 103) {
        $sqlfor103 = 'OR (ss.sId = 5556)';
    }
		
	
	$sql = "SELECT
				(SELECT pName FROM tPeopleInfo AS p WHERE p.pId=ss.sSales ) AS sales,
				s.sName,
				s.sOffice,
				ss.sSignDate,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip = s.sZip1) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip = s.sZip1) AS area,
				sRemark4,
				s.sSales AS preSales,
				s.sId,
				ss.sSignCount
			FROM
				tSalesSign AS ss
			LEFT JOIN
				tScrivener AS s ON s.sId = ss.sStore
			WHERE
				".$str."
				ss.sType = 1 AND ss.sSales ='".$sales."' AND s.sStatus != 3 ".$sqlfor103."
				GROUP BY s.sId
				ORDER BY ss.sSignDate ASC
				";
		// echo $sql;

		$rs = $conn->Execute($sql);
		
		$i = 1;
		while (!$rs->EOF) {
			if ($rs->fields['preSales'] == 0) {
				$Scrivener[$i] = $rs->fields;
				$Scrivener[$i]['no'] = $i;
				
				$Scrivener[$i]['Line'] = checkLineApply($rs->fields['sId'],$Scrivener[$i]['sSignDate']);
				$Scrivener[$i]['sSignDate2'] =$Scrivener[$i]['sSignDate'];
				$Scrivener[$i]['sSignDate'] = DateChange($Scrivener[$i]['sSignDate']);

				//主管跟組員共同區域店家
				if ($city) {
					if ($city == $rs->fields['city']) {
						$ScrivenerCity[] = $Scrivener[$i];
					}
				}

				$i++;
			}
			
			$rs->MoveNext();
		}

	if (!$city) {
		return $Scrivener;
	}else{
		return $ScrivenerCity;
	}
	
}
##
function getbSameStore($status){
	global $conn;
	$store = array();
	$store2 = array();
	$sql = "SELECT bSameStore,bId FROM tBranch WHERE bSameStore != ''";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$exp = explode(';', $rs->fields['bSameStore']); 

		foreach ($exp as $k => $v) {
			$store[(int)substr($v, 2)] = $rs->fields['bId'];//同店對應的名單

			array_push($store2, (int)substr($v, 2));//不顯示的店家
		}



		$rs->MoveNext();
	}

	if ($status == 1) {
		return $store;
	}else{
		return $store2;
	}
	
}
function getSignStoreList(){
	global $conn;
	$store = array();
	$sql = "SELECT
				(SELECT pName FROM tPeopleInfo AS p WHERE p.pId=ss.sSales ) AS sales,
				(SELECT bName FROM tBrand AS br WHERE br.bId = b.bBrand) AS brand,
				ss.sSignDate,
				b.bOldStoreID,
				b.bSameStore,
				b.bStore,
				b.bSales AS preSales,
				bName,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip = b.bZip) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip = b.bZip) AS area,
				b.bCashierOrderMemo,
				b.bId,
				CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode
			FROM
				tSalesSign AS ss
			LEFT JOIN
				tBranch AS b ON b.bId = ss.sStore
			WHERE
				
				ss.sType = 2 AND bSameStore != ''
				
				GROUP BY b.bId
				ORDER BY ss.sSignDate ASC";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		// $store[$rs->fields['bId']] = $rs->fields;
		array_push($store, $rs->fields['bId']);
		$rs->MoveNext();
	}


	return $store;

}
//取得新進仲介店
function getOwnBranch($sales,$date_start,$date_end,$all='',$status='') {
	global $conn ;

	$Branch = array();
	$BranchCheck = getSignStoreList(); //取得所有簽約店家


	$date_start = substr($date_start , 0,10);
	$date_end = substr($date_end , 0,10);


	
	if ($all == '') {
		$str = " AND (ss.sSignDate >='".$date_start."' AND ss.sSignDate <='".$date_end."')";
	}

	
	if ($status) { //有效率要把關店的過濾掉，簽約店要顯示 ，但轉召的只要顯示一筆
		$str .= " AND b.bStatus = 1 ";
	}

	$close = getCgStore($sales,$date_start,$date_end,$all='');

	$exclude = getbSameStore(2);
	//
	// print_r($exclude);
	// die;
	if (is_array($exclude)) {
		$str .= " AND ss.sStore NOT IN(".implode(',', $exclude).")";
	}
	
	$sql = "SELECT
				(SELECT pName FROM tPeopleInfo AS p WHERE p.pId=ss.sSales ) AS sales,
				(SELECT bName FROM tBrand AS br WHERE br.bId = b.bBrand) AS brand,
				ss.sSignDate,
				b.bOldStoreID,
				b.bSameStore,
				b.bStore,
				b.bSales AS preSales,
				bName,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip = b.bZip) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip = b.bZip) AS area,
				b.bCashierOrderMemo,
				b.bId,
				CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode
			FROM
				tSalesSign AS ss
			LEFT JOIN
				tBranch AS b ON b.bId = ss.sStore
			WHERE
				
				ss.sType = 2 AND ss.sSales ='".$sales."'
				".$str."
				GROUP BY b.bId
				ORDER BY ss.sSignDate ASC
				";
		// echo $sql;

		// die;
	$rs = $conn->Execute($sql);
	
	$i = 1;
	while (!$rs->EOF) {
		if ($rs->fields['preSales'] == 0) {
			if ($status =='') {
				// $BranchCheck[$rs->fields['bId']] = $rs->fields;//紀錄簽約店家
				array_push($BranchCheck, $rs->fields['bId']);//紀錄簽約店家
				if (!@in_array($rs->fields['bId'], $close)) {
					$Branch[$i] = $rs->fields;
					
					$Branch[$i]['sSignDateSort'] = $Branch[$i]['sSignDate'];
					$Branch[$i]['sSignDate'] = DateChange($Branch[$i]['sSignDate']);
					
					if ($rs->fields['bOldStoreID'] > 0) {
						
						//算給新店
						$tmp = getCloseStore($rs->fields['bOldStoreID']);
						$Branch[$i]['oldStore'] = '原:'.$tmp['bCode'].$tmp['brand'].$tmp['bStore']."(".$tmp['bName'].')';
						unset($tmp);
					}
					$i++;
				}


			}else{ //舊的有效率
				$Branch[$i] = $rs->fields;
					
					$Branch[$i]['sSignDateSort'] = $Branch[$i]['sSignDate'];
					$Branch[$i]['sSignDate'] = DateChange($Branch[$i]['sSignDate']);
					
					if ($rs->fields['bOldStoreID'] > 0) {
						
						$tmp = getCloseStore($rs->fields['bOldStoreID']);
						// echo $rs->fields['bName']."_".$tmp['bName']."<br>";
						// $Branch[$i]['oldStore'] = '原'.$tmp['bCode'].$tmp['brand'].$tmp['bStore']."(".$tmp['bName'].')';
						if ($tmp['bName'] == $rs->fields['bName']) {
							
							$Branch[$i]['checkOK'] = 1 ; //檢查是否相同法人 相同法人就計算分子的案件

						}else{
							$Branch[$i]['checkOK'] = 0 ;
						}
					}
					$i++;
			}
		}
		
		
		
		$rs->MoveNext();
	}
	//檢查分店店家要算在本店
	// 	有效率跟簽約數的計算店家以設定店家為主要計算對象，如其他分店有近案都要算給該店家
	// EX: A、B、C為同店，A店有設定B、C為分身店，有效率跟簽約數都算在那間店上
	if ($status == '' ) {
		$str = " AND (ss.sSignDate >='".$date_start."' AND ss.sSignDate <='".$date_end."')";
		
		if (is_array($exclude)) {
			$str .= " AND ss.sStore IN(".implode(',', $exclude).")";
		}

		 $checkExClude = getbSameStore(1);
		 // print_r($BranchCheck);
		 // die;

		$sql = "SELECT
				(SELECT pName FROM tPeopleInfo AS p WHERE p.pId=ss.sSales ) AS sales,
				(SELECT bName FROM tBrand AS br WHERE br.bId = b.bBrand) AS brand,
				ss.sSignDate,
				b.bOldStoreID,
				b.bStore,
				b.bSales AS preSales,
				bName,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip = b.bZip) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip = b.bZip) AS area,
				b.bCashierOrderMemo,
				b.bId,
				CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode
			FROM
				tSalesSign AS ss
			LEFT JOIN
				tBranch AS b ON b.bId = ss.sStore
			WHERE
				
				ss.sType = 2 AND ss.sSales ='".$sales."'
				".$str."
				GROUP BY b.bId
				ORDER BY b.bId ASC
				";
			
				// print_r($BranchCheck);
		$rs = $conn->Execute($sql);
		// $BranchTT = array();
		while (!$rs->EOF) {
			// echo $checkExClude[$rs->fields['bId']]."//\r\n";  
			if (!in_array($checkExClude[$rs->fields['bId']],$BranchCheck)) { //比對是否已經有簽約資料 //本店未簽約
				array_push($BranchCheck, $rs->fields['bId']);
				$tmp_store = getCloseStore($checkExClude[$rs->fields['bId']]);
				
				if (strtotime($rs->fields['sSignDate']) >= strtotime($date_start) && strtotime($rs->fields['sSignDate']) <=strtotime($date_end) ) {
					$Branch[$i] = $rs->fields;
					//替換
					$Branch[$i]['brand'] = $tmp_store['brand'];
					$Branch[$i]['bStore'] = $tmp_store['bStore'];
					$Branch[$i]['bName'] = $tmp_store['bName'];
					$Branch[$i]['city']= $tmp_store['city'];
					$Branch[$i]['area']= $tmp_store['area'];
					$Branch[$i]['bId']= $tmp_store['bId'];
					
					$Branch[$i]['sSignDateSort'] = $Branch[$i]['sSignDate'];
					$Branch[$i]['sSignDate'] = DateChange($Branch[$i]['sSignDate']);


					// array_push($BranchTT, $Branch[$i]);

					// print_r($Branch[$i]);
					$i++;
					
				}
				
				unset($tmp_store);
				// print_r($Branch[$i]);
				// die;
			}
			


			$rs->MoveNext();
		}
	}
	// echo "<pre>";
	// print_r($Branch);
	// die('##############');


	$max = count($Branch);
	for ($i = 1 ; $i <= $max ; $i ++) {
		for ($j = 1 ; $j <= $max - 1 ; $j ++) {
			
			if ($Branch[$j]['sSignDateSort'] > $Branch[$j+1]['sSignDateSort']) {
				$tmp = $Branch[$j] ;
				$Branch[$j] = $Branch[$j+1] ;
				$Branch[$j+1] = $tmp ;
				unset($tmp) ;
			}
		}

	}

	

	return $Branch;
}


function getCloseStore($id){
	global $conn;
	$data = array();
	$sql = "SELECT *,(SELECT bName FROM tBrand AS b WHERE b.bId = bBrand) AS brand,(SELECT bCode FROM tBrand AS b WHERE b.bId = bBrand) AS code,(SELECT zCity FROM tZipArea AS z WHERE z.zZip =bZip) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip = bZip) AS area FROM tBranch WHERE bId = '".$id."'";
	// echo $sql;
	$rs = $conn->Execute($sql);
	$data = $rs->fields;
	// $data['txt'] = $rs->fields['code'].str_pad($rs->fields['bId'], '5','0',STR_PAD_LEFT).$rs->fields['brand'].$rs->fields['bStore']."(".$rs->fields['bName'].')';

	return $data;
}
//20180109 佩琪說1/4號簽進來的才要算LINE的部分  1/4前是以之前的算法
function checkLineApply($sId,$signDate){
	global $conn;

	$check = '';
	

	if ($signDate >= '2018-01-04') { //20180109 佩琪說1/4號簽進來的才要算LINE的部分  1/4前是以之前的算法
				$sql = "SELECT *  FROM tLineAccount WHERE lStatus = 'Y' AND lTargetCode = 'SC".str_pad($sId, 4,'0',STR_PAD_LEFT)."'";
				
				$rs = $conn->Execute($sql);
				

				if ($rs->RecordCount() > 0) {
					if ($rs->fields['lCaseMobileAuth2'] == '') {
						$check = 'LINE加入日期:'.DateChange($rs->fields['lCaseMobileAuthTime']);
					}else{
						$check = 'LINE加入日期:'.DateChange($rs->fields['lCaseMobileAuthTime2']);
						
					}
					
				}else{
					$check = '尚未加入LINE';
					$sql = "SELECT sSignCount FROM tSalesSign WHERE sType =1 AND sStore =".$sId."";
					$rs = $conn->Execute($sql);
					if ($rs->fields['sSignCount'] == 1) {
						$check .= '(要算簽約數)';
					}
					
					
				}
	}

	return $check;
}

//取得轉召的店編
function getCgStore($sales,$date_start,$date_end,$all='')
{
	global $conn;

	if ($all == '') {
		$str = " AND (ss.sSignDate >='".$date_start."' AND ss.sSignDate <='".$date_end."')";
	}

	$sql = "SELECT		
				b.bOldStoreID
			FROM
				tSalesSign AS ss
			LEFT JOIN
				tBranch AS b ON b.bId = ss.sStore
			WHERE
				b.bOldStoreID > 0 AND 
				ss.sType = 2 AND ss.sSales ='".$sales."'
				".$str."
				GROUP BY b.bId
				ORDER BY ss.sSignDate ASC
				";
	
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$arr[] = $rs->fields['bOldStoreID'];

		$rs->MoveNext();
	}

	return $arr;
}

function getSameStore($sales,$arr,$arr2,$lineArr){
	global $conn;

	$score = 0;

	if (is_array($lineArr)) {
		$scrivener = @implode(',', $lineArr);
		$str = " AND sStore NOT IN(".$scrivener.")";
		// print_r($lineArr);

	}


	if (is_array($arr)) {
		foreach ($arr as $k => $v) {
			$sql= "SELECT sStore,COUNT(sStore) AS storeCount FROM tSalesSign WHERE sType = 2 AND sStore = '".$v['bId']."' AND sTag = 0";
			$rs = $conn->Execute($sql);

			if ($rs->fields['storeCount'] > 1) {
				$score += Round(1/$rs->fields['storeCount'],2);
			}
			
		}
	}
	
	if (is_array($arr2)) {
		foreach ($arr2 as $k => $v) {
			$sql= "SELECT sStore,COUNT(sStore) AS storeCount FROM tSalesSign WHERE sType = 1 AND sStore = '".$v['sId']."' AND sTag = 0".$str;
			$rs = $conn->Execute($sql);

			if ($rs->fields['storeCount'] > 1) {
				$score += Round(1/$rs->fields['storeCount'],2);
			}
		}
	}
	
	// echo $score;
	return $score;
}
##
##
//日期轉換
function DateChange($date)
{
	$date = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$date)) ;
	$tmp = explode('-',$date) ;
	
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
	
	$date = $tmp[0].'年'.$tmp[1].'月'.$tmp[2].'日' ;
	unset($tmp) ;
	return $date;
}

//後面大於10 則把多出來的加到前一個去
function getMoreTargetCount($val1,$val2)
{
	if ($val2 >10) { 
		$add = $val2 -10;
	}

	$val1  = $val1 + $add;

	if ($val1 > 10) {
		$val1 = 10;
	}

	return $val1;
}
function getGrade($target,$group,$use,$contribution,$yr,$effective='')
{
	
	if ($yr <= 105) {
		if ($target > 100) {$target = 100;}//超過100只能是100
		if ($group > 100) {$group = 100;}//超過100只能是100
		if ($use > 100) {$use = 100;}//超過100只能是100
		if ($contribution > 100) {$contribution = 100;}//超過100只能是100
			
		
		$grade = ($target * 0.4)+($group*0.3)+($use*0.2)+($contribution*0.1);
	}else{
		$grade = ($target * 0.3)+($group*0.2)+($use*0.2)+$effective;
	}
	

	return $grade;
}
//取得業物歸屬區域
function getSalesArea($sales)
{
	global $conn;

	// $sql = "SELECT bSales FROM tBranchSales WHERE bBranch IN(".@implode(',', $branch).")";
	$sql = "SELECT
				b.bZip
			FROM
				tBranch AS b
			LEFT JOIN
				tBranchSales AS bs ON bs.bBranch = b.bId
			WHERE
				bs.bSales = '".$sales."'
			GROUP BY b.bZip";
			
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {

		$zip[$rs->fields['bZip']] = '"'.$rs->fields['bZip'].'"';

		$rs->MoveNext();
	}

	$sql = "SELECT
				s.sCpZip1
			FROM
				tScrivener AS s
			LEFT JOIN
				tScrivenerSales AS ss ON s.sId = ss.sScrivener
			WHERE
				ss.sSales  = '".$sales."'"; //sSales

	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {

		$zip[$rs->fields['sCpZip1']] = '"'.$rs->fields['sCpZip1'].'"';

		$rs->MoveNext();
	}
	

	$tmp = implode(',', $zip);
	

	return $tmp;

}
function checkSales($arr,$pId)
{
	global $conn;
	
	$check = 0;
	// $branch[] = '"'.$arr['branch'].'"';
	// if ($arr['branch1'] > 0) {$branch[] = $arr['branch1'];}
	// if ($arr['branch2'] > 0) {$branch[] = $arr['branch2'];}	
	
	if ($arr['sSales'] == $pId) {

		if ($arr['sPreSales'] > 0) {
			if ($arr['sPreSalesDate'] < $arr['cSignDate']) {
				$check++;
			}
		}else{
			$check++;
		}
	}

	if ($arr['bSales1'] == $pId) {
		if ($arr['bPreSales1'] > 0) {
			if ($arr['bPreSalesDate1'] < $arr['cSignDate']) {
				$check++;
			}
		}else{
			$check++;
		}

		
	}

	if ($arr['bSales2'] == $pId) {
		
		if ($arr['bPreSales2'] > 0) {
			if ($arr['bPreSalesDate2'] < $arr['cSignDate']) {
				$check++;
			}
		}else{
			$check++;
		}

	}

	if ($arr['bSales3'] == $pId) {
		if ($arr['bPreSales3'] > 0) {
			if ($arr['bPreSalesDate3'] < $arr['cSignDate']) {
				$check++;
			}
		}else{
			$check++;
		}
	}

	if ($check > 0) {
		return true;
	}else{
		return false;
	}
	
	// $sql = "SELECT bSales FROM tBranchSales WHERE bBranch IN(".@implode(',', $branch).")";

	// $rs = $conn->Execute($sql);

	// while (!$rs->EOF) {
	// 	$sales[] = $rs->fields['bSales'];

	// 	$rs->MoveNext();
	// }

	

	// $sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener =".$arr['cScrivener']." AND sSales='".$pId."'";

	// $rs = $conn->Execute($sql);

	// $max=$rs->RecordCount();
	
	
	// if (in_array($pId, $sales) || $max > 0) {

	// 	return true;
	// }
	// else{
	// 	return false;
	// }

	
}
function getSaleOnBoard($sales)
{
	global $conn;

	$sql = "SELECT pOnBoard FROM tPeopleInfo WHERE pId ='".$sales."'";

	$rs = $conn->Execute($sql);

	$tmp = explode('-', $rs->fields['pOnBoard']);

	$pOnBoard = $tmp[0]."-".$tmp[1]."-01";

	return $pOnBoard;
}
##有效率106年版##
function getEffective3($yr,$now_season,$sales){
	global $conn;
	if ($now_season  == 1) {
		$season_first_m = 1;
	}elseif ($now_season  == 2) {
		$season_first_m = 4;
		$status = 1;//只取還在開店的
	}elseif ($now_season  == 3) {
		$season_first_m = 7;
		$status = 1;//只取還在開店的
	}elseif ($now_season  == 4) {
		$season_first_m = 10;
		$status = 1;//只取還在開店的
	}
	

	
	$range_start = date('Y-m-d',strtotime("-12 month",strtotime(($yr+1911)."-".str_pad($season_first_m,2,'0',STR_PAD_LEFT))))." 00:00:00";
	
	$range_end = date('Y-m',strtotime("-1 month",strtotime(($yr+1911)."-".str_pad($season_first_m,2,'0',STR_PAD_LEFT))))."-".date('t',$season_first_m)." 23:59:59" ;
	
	####分母(由結算季度往前回推四季作為一整年度)####
	// echo $status;
	$scr = getOwnScrivener($sales,$range_start,$range_end,'',$status);//時間範圍內簽約的代書

	$branch = getOwnBranch($sales,$range_start,$range_end,'',$status);//時間範圍內簽約的仲介
	######

	

	//從第二季開始分子時間範圍改為(本季+前四季)

	if ($now_season  == 1) {
		$range_start2 = $range_start;
		$range_end2 = $range_end;

	}else{		
		$range_start2 = date('Y-m-d',strtotime("-12 month",strtotime(($yr+1911)."-".str_pad($season_first_m,2,'0',STR_PAD_LEFT))))." 00:00:00";	
		$range_end2 = date('Y-m',strtotime("+2 month",strtotime(($yr+1911)."-".str_pad($season_first_m,2,'0',STR_PAD_LEFT))))."-".date('t',$season_first_m)." 23:59:59" ;

	}

	$effcase_data = getEffectiveCase($scr,$branch,$range_start2,$range_end2); //分子

	$effcase = count($effcase_data);//分子
	$efftotal = count($scr)+count($branch); //分母
	
	if ($efftotal != '' && $efftotal != 0) {
		$effective = round($effcase/$efftotal,2)*100;
	}else{
		$effective = 0;
	}
	

	$data['range_start'] = DateChange($range_start);
	$data['range_end'] = DateChange($range_end);
	$data['range_start2'] = DateChange($range_start2);
	$data['range_end2'] = DateChange($range_end2);
	$data['efftotal'] = $efftotal ;
	$data['effcase_data'] = $effcase_data;
	$data['effcase'] = $effcase;
	$data['effective'] = $effective;
	$data['data'] = getEffectiveData($effcase_data,$scr,$branch);

	
	// print_r($data);

	return $data;
	
}
###


##有效率105年版##
//有效率顯示是否進案
function getEffectiveData($effcase_data,$scr,$branch)
{
	for ($i=1; $i <= count($scr); $i++) { 

		if ($effcase_data['S'.$scr[$i]['sId']] == 1) {
			$data['scrcase'][] = $scr[$i];
		}else{
			$data['scrnocase'][] = $scr[$i];
		}

	}

	for ($i=1; $i <= count($branch); $i++) { 
		if ($effcase_data['B'.$branch[$i]['bId']] == 1) {
			$data['branchcase'][] = $branch[$i];
		}else{
			$data['branchnocase'][] = $branch[$i];
		}
	}
	
	
	
	return $data;
}

function getEffective($yr,$mn,$day,$onBoard,$sales)//小於規定日
{
		global $conn;
		$now_season = floor($mn /3);
		$tmp = $mn % 3; // 是否有餘數
		if ($tmp != 0) { $now_season++;}
		
		if ($now_season  == 1) {
			$season_first_m = 1;
		}elseif ($now_season  == 2) {
			$season_first_m = 4;
		}elseif ($now_season  == 3) {
			$season_first_m = 7;
		}elseif ($now_season  == 4) {
			$season_first_m = 10;
		}
		unset($tmp)	;unset($now_season);
		

		//取得前前季的區間
		$range_start = date('Y-m-d',strtotime("-6 month",strtotime(($yr+1911)."-".str_pad($season_first_m,2,'0',STR_PAD_LEFT))))." 00:00:00";
		$range_end = date('Y-m',strtotime("-4 month",strtotime(($yr+1911)."-".str_pad($season_first_m,2,'0',STR_PAD_LEFT))))."-".date('t',$season_first_m)." 23:59:59" ;
		
		//前一季區間
		$range_start2 = date('Y-m-d',strtotime("-3 month",strtotime(($yr+1911)."-".str_pad($season_first_m,2,'0',STR_PAD_LEFT))))." 00:00:00";
		$range_end2 = date('Y-m',strtotime("-1 month",strtotime(($yr+1911)."-".str_pad($season_first_m,2,'0',STR_PAD_LEFT))))."-".date('t',$season_first_m)." 23:59:59" ;
		
		####分母(前前季)####
		$scr = getOwnScrivener($sales,$range_start,$range_end);//時間範圍內簽約的代書

		$branch = getOwnBranch($sales,$range_start,$range_end);//時間範圍內簽約的仲介
		#########分子(前前季+前一季)###########
		$effcase_data = getEffectiveCase2($scr,$branch,$range_start,$range_end,$range_start2,$range_end2); //有進案(前前季+前一季)

		#######################################
		$effcase = count($effcase_data);//分子
		$efftotal = count($scr)+count($branch); //分母


		if ($efftotal != 0) {

			$effective = round($effcase/$efftotal,4)*100;
		}else{
			$effective = 0;
		}

		$case['effective'] = $effective;
		$case['range_start'] = DateChange($range_start);
		$case['range_end'] = DateChange($range_end);
		$case['range_start2'] = DateChange($range_start2);
		$case['range_end2'] = DateChange($range_end2);
		$case['effcase'] = $effcase;
		$case['efftotal'] = $efftotal;

		$case['data'] = getEffectiveData($effcase_data,$scr,$branch);


		return $case;
}


function getEffective2($yr,$mn,$day,$onBoard,$sales)//大於等於規定日
{
	###分子區間###
		
	$range_start = date('Y-m',strtotime("-17 month",strtotime(($yr+1911)."-".str_pad($mn,2,'0',STR_PAD_LEFT))))."-01 00:00:00";
		
		
	$range_end =   date('Y-m',strtotime("-6 month",strtotime(($yr+1911)."-".str_pad($mn,2,'0',STR_PAD_LEFT))))."-".date('t',$mn)." 23:59:59" ;
		
	#########分子(前前季+前一季)###########
	$scr = getOwnScrivener($sales,'','','all');//全部簽約的代書
	$branch = getOwnBranch($sales,'','','all');//全部簽約的仲介

	$effcase_data = getEffectiveCase($scr,$branch,$range_start,$range_end); //有進案[一年半-6月]

		
	#######################################
	$effcase = count($effcase_data);//分子
	$efftotal = 120; //分母
	if ($efftotal != 0) {
		$effective = round($effcase/$efftotal,4)*100;
	}else{
			$effective = 0;
	}

	// $ecase = getEffectiveData($effcase_data,$scr,$branch);

	$case['effective'] = $effective;
	$case['range_start'] = DateChange($range_start);
	$case['range_end'] = DateChange($range_end);
	$case['effcase'] = $effcase;
	$case['efftotal'] = $efftotal;

	$case['data'] = getEffectiveData($effcase_data,$scr,$branch);


	return $case;
}

//有效率的分子(大於等於規定日)
function getEffectiveCase($scr,$branch,$start,$end)
{
	global $conn;
	 
	

	for ($i=1; $i <= count($scr); $i++) { 
		$sid[] = "'".$scr[$i]['sId']."'";
		$sid2[] = $scr[$i]['sId'];

	}

	for ($i=1; $i <= count($branch); $i++) { 
		if ($branch[$i]['bOldStoreID'] > 0 ) {
			// print_r($branch[$i]);
			
			if ($branch[$i]['checkOK'] == 1) { //要看法人名稱 如果轉的前後法人名稱都一樣 有效率就持續計算
				// echo $branch[$i]['bId'];
				$bid[] = "'".$branch[$i]['bOldStoreID']."'";
				$bid2[] = $branch[$i]['bOldStoreID'];
				$bid3[$branch[$i]['bOldStoreID']] = $branch[$i]['bId'];
			}
			$bid[] = "'".$branch[$i]['bId']."'";
			$bid2[] = $branch[$i]['bId'];
		}else{
			$bid[] = "'".$branch[$i]['bId']."'";
			$bid2[] = $branch[$i]['bId'];
		}
		

	}


	if (is_array($bid)) {
		$tmp = implode(',', $bid);
	}else{
		$tmp = 0;
	}

	if (is_array($sid)) {
		$tmp1 = implode(',', $sid);
	}else{
		$tmp1 = 0;
	}


	$sql = "
			SELECT
				cc.cCertifiedId,
				cr.cBranchNum,
				cr.cBranchNum1,
				cr.cBranchNum2,
				cs.cScrivener
			FROM
				tContractCase AS cc 
			LEFT JOIN 
				tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId 
			LEFT JOIN 
				tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
			WHERE
				(cc.cApplyDate >='".$start."' AND cc.cApplyDate <='".$end."') AND 
				(cr.cBranchNum IN (".$tmp.") OR cr.cBranchNum1 IN (".$tmp.") 
				OR cr.cBranchNum2 IN (".$tmp.") OR cs.cScrivener IN (".$tmp1."))
			GROUP BY cc.cCertifiedId
			";
			// echo $sql;
	$rs = $conn->Execute($sql);
	// print_r($bid2);

	while (!$rs->EOF) {
		
		// 因為是全部查詢，所以要判斷是否為簽約的店或地政士
		// 找尋是否為簽約的地政士
		//把編號存進陣列KEY
		if (@in_array($rs->fields['cScrivener'],$sid2)) {
			$data['S'.$rs->fields['cScrivener']] = 1;
		}

		if (@in_array($rs->fields['cBranchNum'],$bid2)) {
			
			if ($bid3[$rs->fields['cBranchNum']] > 0) { //確認是否要算到新的店
				
				$rs->fields['cBranchNum'] = $bid3[$rs->fields['cBranchNum']];
			}
			$data['B'.$rs->fields['cBranchNum']]  = 1;
		}

		if (@in_array($rs->fields['cBranchNum1'],$bid2)) {
			if ($bid3[$rs->fields['cBranchNum1']] > 0) { //確認是否要算到新的店
				// echo 'step2';
				$rs->fields['cBranchNum1'] = $bid3[$rs->fields['cBranchNum1']];
			}
			$data['B'.$rs->fields['cBranchNum1']]  = 1;
		}

		if (@in_array($rs->fields['cBranchNum2'],$bid2)) {
			if ($bid3[$rs->fields['cBranchNum2']] > 0) { //確認是否要算到新的店
				
				$rs->fields['cBranchNum2'] = $bid3[$rs->fields['cBranchNum2']];
			}
			$data['B'.$rs->fields['cBranchNum2']]  = 1;
		}
		$rs->MoveNext();
	}


	unset($tmp);unset($tmp1);
	unset($sid);unset($sid1);
	unset($bid);unset($bid1);

	return $data;
	

	// die;
	
	
}

//有效率的分子(小於規定日)
function getEffectiveCase2($scr,$branch,$start,$end,$start2,$end2)
{
	global $conn;
	 
	// $end = $end." 23:59:59";

	for ($i=1; $i <= count($scr); $i++) { 
		$sid[] = "'".$scr[$i]['sId']."'";
		$sid2[] = $scr[$i]['sId'];

	}

	for ($i=1; $i <= count($branch); $i++) { 
		$bid[] = "'".$branch[$i]['bId']."'";
		$bid2[] = $branch[$i]['bId'];

	}


	if (is_array($bid)) {
		$tmp = implode(',', $bid);
	}else{
		$tmp = 0;
	}

	if (is_array($sid)) {
		$tmp1 = implode(',', $sid);
	}else{
		$tmp1 = 0;
	}

	##前前季##
	$sql = "
			SELECT
				cc.cCertifiedId,
				cr.cBranchNum,
				cr.cBranchNum1,
				cr.cBranchNum2,
				cs.cScrivener
			FROM
				tContractCase AS cc 
			LEFT JOIN 
				tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId 
			LEFT JOIN 
				tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
			WHERE
				(cc.cApplyDate >='".$start."' AND cc.cApplyDate <='".$end."') AND 
				(cr.cBranchNum IN (".$tmp.") OR cr.cBranchNum1 IN (".$tmp.") 
				OR cr.cBranchNum2 IN (".$tmp.") OR cs.cScrivener IN (".$tmp1."))
			GROUP BY cc.cCertifiedId
			";
			// echo $sql;
	$rs = $conn->Execute($sql);
	// print_r($bid2);

	while (!$rs->EOF) {
		
		// 因為是全部查詢，所以要判斷是否為簽約的店或地政士
		// 找尋是否為簽約的地政士
		//把編號存進陣列KEY
		if (@in_array($rs->fields['cScrivener'],$sid2)) {
			$data['S'.$rs->fields['cScrivener']] = 1;
		}

		if (@in_array($rs->fields['cBranchNum'],$bid2)) {
			$data['B'.$rs->fields['cBranchNum']]  = 1;
		}

		if (@in_array($rs->fields['cBranchNum1'],$bid2)) {
			$data['B'.$rs->fields['cBranchNum1']]  = 1;
		}

		if (@in_array($rs->fields['cBranchNum2'],$bid2)) {
			$data['B'.$rs->fields['cBranchNum2']]  = 1;
		}
		$rs->MoveNext();
	}
	####################前一季###############

		$sql = "
			SELECT
				cc.cCertifiedId,
				cr.cBranchNum,
				cr.cBranchNum1,
				cr.cBranchNum2,
				cs.cScrivener
			FROM
				tContractCase AS cc 
			LEFT JOIN 
				tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId 
			LEFT JOIN 
				tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
			WHERE
				(cc.cApplyDate >='".$start2."' AND cc.cApplyDate <='".$end2."') AND 
				(cr.cBranchNum IN (".$tmp.") OR cr.cBranchNum1 IN (".$tmp.") 
				OR cr.cBranchNum2 IN (".$tmp.") OR cs.cScrivener IN (".$tmp1."))
			GROUP BY cc.cCertifiedId
			";
			// echo $sql;
		$rs = $conn->Execute($sql);
		// print_r($bid2);

		while (!$rs->EOF) {
			
			// 因為是全部查詢，所以要判斷是否為簽約的店或地政士
			// 找尋是否為簽約的地政士
			//把編號存進陣列KEY
			if (@in_array($rs->fields['cScrivener'],$sid2)) {
				$data['S'.$rs->fields['cScrivener']] = 1;
			}

			if (@in_array($rs->fields['cBranchNum'],$bid2)) {
				$data['B'.$rs->fields['cBranchNum']]  = 1;
			}

			if (@in_array($rs->fields['cBranchNum1'],$bid2)) {
				$data['B'.$rs->fields['cBranchNum1']]  = 1;
			}

			if (@in_array($rs->fields['cBranchNum2'],$bid2)) {
				$data['B'.$rs->fields['cBranchNum2']]  = 1;
			}
			$rs->MoveNext();
		}




	########################################

	unset($tmp);unset($tmp1);
	unset($sid);unset($sid1);
	unset($bid);unset($bid1);




	return $data;
	

	// die;
	
	
}
#######用不到#######
//取得簽約店家
function getSignStore($sales)
{
	global $conn;

	$sql = "SELECT sType,sStore,sSignDate FROM tSalesSign WHERE sSales ='".$sales."' AND sSignDate <= '".$date_end."'";

	// echo $sql;
	// die;
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		
		if ($rs->fields['sType'] == 1) {
			$tmp[1][] = $rs->fields['sStore']; //地政士
		}elseif ($rs->fields['sType'] == 2) {
			$tmp[2][] = $rs->fields['sStore'];//仲介店
		}

		$rs->MoveNext();
	}

	return $tmp;
}
//店家業務
function getBranchSales($bId)
{
	global $conn;
	$sql = "
			SELECT
				(SELECT pName FROM tPeopleInfo WHERE pId=bs.bSales) AS name,
				(SELECT pId FROM tPeopleInfo WHERE pId=bs.bSales) AS pId
			FROM 
				tBranchSales as bs
			WHERE 
				bs.bBranch=".$bId."
			";
	$rs = $conn->Execute($sql);

	
	return $rs->fields['pId'];
}

//地政士業務
##
function getScrivenerSales($sId)
{
	global $conn;
		$sql = "
			SELECT
				(SELECT pName FROM tPeopleInfo WHERE pId=ss.sSales) AS name,
				(SELECT pId FROM tPeopleInfo WHERE pId=ss.sSales) AS pId
			FROM 
				tScrivenerSales as ss
			WHERE 
				ss.sScrivener =".$sId."
			";
			// echo $sql;
	$rs = $conn->Execute($sql);

	// $tmp['name'] =$rs->fields['name'];
	
	return $rs->fields['pId'];
}

//25專用
function getOwnCase25($sales,$date_start,$date_end)
{
	global $conn;

	$data['other'] = 0;
	$data['tw'] = 0;
	$data['cCertifiedMoney'] = 0;
	$data['feedBackMoney'] = 0;
	$data['use'] = 0;

	$zip = getSalesArea($sales);
	$query .= ' AND pro.cZip IN ('.$zip.') ' ;

	$tZipArea = array(
	  100,103,104,105,106,108,110,111,
	  112,114,115,116,
	  207,208,220,221,
	  222,223,224,226,
	  227,228,231,232,
	  233,234,235,236,
	  237,238,239,241,
	  242,243,244,247,
	  248,249,251,252,
	  253); //雙北
  
	// echo "<pre>";
	// print_r($tZipArea);
	// echo "</pre>";

	$sql ='
			SELECT 
				cas.cCertifiedId as cCertifiedId,
				cas.cSignDate as cSignDate, 
				csc.cScrivener as cScrivener, 
				rea.cBrand as brand,
  				rea.cBrand1 as brand1,
  				rea.cBrand2 as brand2,
				rea.cBranchNum as branch,
				rea.cBranchNum1 as branch1,
				rea.cBranchNum2 as branch2,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ), LPAD(rea.cBranchNum,5,"0")) AS branchcode,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ), LPAD(rea.cBranchNum1,5,"0")) AS branchcode1,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ), LPAD(rea.cBranchNum2,5,"0")) AS branchcode2,
				CONCAT("SC", LPAD(csc.cScrivener,4,"0")) AS scrcode,
				ci.cCertifiedMoney,
				cas.cCaseFeedBackMoney,
				cas.cCaseFeedBackMoney1,
				cas.cCaseFeedBackMoney2,
				pro.cZip,
				cas.cSpCaseFeedBackMoney

			FROM 
				tContractCase AS cas 
			LEFT JOIN 
				tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
			LEFT JOIN 
				tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId 
			LEFT JOIN 
				tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId
			LEFT JOIN
				tContractIncome AS ci ON ci.cCertifiedId=cas.cCertifiedId
			WHERE
				cas.cSignDate >= "'.$date_start.'" AND cas.cSignDate <= "'.$date_end.'"
				'.$query.' 
			GROUP BY
				cas.cCertifiedId
			ORDER BY 
				cas.cApplyDate,cas.cId,cas.cSignDate ASC;
		' ;


		$rs = $conn->Execute($sql);
		
		while (!$rs->EOF) {

			$type = branch_type($conn,$rs->fields);

			if ($type == 'O') {//'加盟其他品牌' ;

				if (in_array($rs->fields['cZip'],$tZipArea)) { //判斷是否為雙北
					$sales_b[] = getBranchSales($rs->fields['branch']);

					if ($rs->fields['branch1'] > 0) {
						$sales_b[] = getBranchSales($rs->fields['branch1']);
					}
					
					if ($rs->fields['branch2'] > 0) {
						$sales_b[] = getBranchSales($rs->fields['branch2']);
					}
					

					if (in_array($sales, $sales_b)) { //仲介店業務如果有25才算案件
						$list[] = $rs->fields ;
						// $data['other']++;

					}

					unset($sales_b);
				}else{ //宜蘭跟基隆
					$list[] = $rs->fields ;
				}


				
			}elseif ($type == '3') {
				$sales_s = getScrivenerSales($rs->fields['cScrivener']);

				if ($sales_s == $sales) { //$sales = 25; 地政士業務是25才算案件
					$list[] = $rs->fields;
					// $data['other']++;
				}
			}

			unset($sales_b);unset($type);unset($sales_s);

			$rs->MoveNext();
		}

		$data['other'] = count($list);

		for ($i=0; $i < $data['other']; $i++) { 

		

			##地政士業務
			$sales_s = getScrivenerSales($list[$i]['cScrivener']);

			if ($sales_s == $sales) {
				$use[$list[$i]['scrcode']]++;//地政士
			}

			##仲介店業務
			//找出該案件的業務，(++沒意義，最後以陣列數為主)

			if ($sales == getBranchSales($list[$i]['branch'])) {
				$use[$list[$i]['branchcode']]++;
			}

			if ($sales == getBranchSales($list[$i]['branch1'])) {
				$use[$list[$i]['branchcode1']]++;
			}


			if ($sales == getBranchSales($list[$i]['branch2'])) {
				$use[$list[$i]['branchcode2']]++;
			}

			##

			//貢獻率
			$data['cCertifiedMoney'] = $data['cCertifiedMoney'] + $list[$i]['cCertifiedMoney'];//保證費
			$data['feedBackMoney'] = $data['feedBackMoney'] + $list[$i]['cCaseFeedBackMoney'] + $list[$i]['cCaseFeedBackMoney1'] +$list[$i]['cCaseFeedBackMoney2'];//回饋金
			if ($sales_s == $sales) {
				$data['feedBackMoney'] = $data['feedBackMoney'] +$list[$i]['cSpCaseFeedBackMoney'];
			}
		}

		$data['use'] = count($use);//使用量

		//貢獻率
			

		return $data;
}
//25專用 使用率
function getUseMonth25($sales,$date_start,$date_end)
{
	global $conn;

		
	$zip = getSalesArea($sales);
	$query .= ' AND pro.cZip IN ('.$zip.') ' ;

	$tZipArea = array(
	  100,103,104,105,106,108,110,111,
	  112,114,115,116,
	  207,208,220,221,
	  222,223,224,226,
	  227,228,231,232,
	  233,234,235,236,
	  237,238,239,241,
	  242,243,244,247,
	  248,249,251,252,
	  253); //雙北
  
	
	$sql ='
			SELECT 
				cas.cCertifiedId as cCertifiedId,
				cas.cSignDate as cSignDate, 
				csc.cScrivener as cScrivener, 
				rea.cBrand as brand,
  				rea.cBrand1 as brand1,
  				rea.cBrand2 as brand2,
				rea.cBranchNum as branch,
				rea.cBranchNum1 as branch1,
				rea.cBranchNum2 as branch2,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ), LPAD(rea.cBranchNum,5,"0")) AS branchcode,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ), LPAD(rea.cBranchNum1,5,"0")) AS branchcode1,
				CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ), LPAD(rea.cBranchNum2,5,"0")) AS branchcode2,
				CONCAT("SC", LPAD(csc.cScrivener,4,"0")) AS scrcode,
				ci.cCertifiedMoney,
				cas.cCaseFeedBackMoney,
				cas.cCaseFeedBackMoney1,
				cas.cCaseFeedBackMoney2,
				pro.cZip,
				cas.cSpCaseFeedBackMoney

			FROM 
				tContractCase AS cas 
			LEFT JOIN 
				tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
			LEFT JOIN 
				tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId 
			LEFT JOIN 
				tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId
			LEFT JOIN
				tContractIncome AS ci ON ci.cCertifiedId=cas.cCertifiedId
			WHERE
				cas.cSignDate >= "'.$date_start.'" AND cas.cSignDate <= "'.$date_end.'"
				'.$query.' 
			GROUP BY
				cas.cCertifiedId
			ORDER BY 
				cas.cApplyDate,cas.cId,cas.cSignDate ASC;
		' ;

		
		$rs = $conn->Execute($sql);
		
		while (!$rs->EOF) {

			$type = branch_type($conn,$rs->fields);

			if ($type == 'O') {//'加盟其他品牌' ;

				if (in_array($rs->fields['cZip'],$tZipArea)) { //判斷是否為雙北
					$sales_b[] = getBranchSales($rs->fields['branch']);

					if ($rs->fields['branch1'] > 0) {
						$sales_b[] = getBranchSales($rs->fields['branch1']);
					}
					
					if ($rs->fields['branch2'] > 0) {
						$sales_b[] = getBranchSales($rs->fields['branch2']);
					}
					

					if (in_array($sales, $sales_b)) { //仲介店業務如果有25才算案件
						$list[] = $rs->fields ;
						// $data['other']++;

					}

					unset($sales_b);
				}else{ //宜蘭跟基隆
					$list[] = $rs->fields ;
				}


				
			}elseif ($type == '3') {
				$sales_s = getScrivenerSales($rs->fields['cScrivener']);

				if ($sales_s == $sales) { //$sales = 25; 地政士業務是25才算案件
					$list[] = $rs->fields;
					// $data['other']++;
				}
			}

			unset($sales_b);unset($type);unset($sales_s);

			$rs->MoveNext();
		}
		

		for ($i=0; $i < count($list); $i++) { 

			$list[$i]['cSignDate'] = preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$list[$i]['cSignDate']);

			$tmp = explode('-', $list[$i]['cSignDate'] );

			##地政士業務
			$sales_s = getScrivenerSales($list[$i]['cScrivener']);

			if ($sales_s == $sales && $use[$list[$i]['scrcode']] == '') {
				$use[$list[$i]['scrcode']] = $tmp[0].$tmp[1];//地政士
				$month[$tmp[0].$tmp[1]]++;
			}

			##仲介店業務
			//找出該案件的業務，(++沒意義，最後以陣列數為主)

			if ($sales == getBranchSales($list[$i]['branch']) && $use[$list[$i]['branchcode']] == '') {
				$use[$list[$i]['branchcode']] = $tmp[0].$tmp[1];
				$month[$tmp[0].$tmp[1]]++;
			}

			if ($sales == getBranchSales($list[$i]['branch1']) && $use[$list[$i]['branchcode1']] == '') {
				$use[$list[$i]['branchcode1']] = $tmp[0].$tmp[1];
				$month[$tmp[0].$tmp[1]]++;
			}


			if ($sales == getBranchSales($list[$i]['branch2']) && $use[$list[$i]['branchcode2']] == '') {
				$use[$list[$i]['branchcode2']] = $tmp[0].$tmp[1];
				$month[$tmp[0].$tmp[1]]++;
			}

			##

		}


		//貢獻率
			

		return $month;
}
##################


#################舊版###############
//有效率顯示是否進案


###########################################
?>