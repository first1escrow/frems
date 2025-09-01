<?php

if ($sales) {
	
	##確認是否滿一年##
	$sql = "SELECT pOnBoard FROM  tPeopleInfo WHERE pId = '".$sales."'";
	$rs = $conn->Execute($sql);

	$pOnBoard = date('Y-m-d',strtotime("+12 month",strtotime($rs->fields['pOnBoard'])));
	// $pOnBoard = $rs->fields['pOnBoard'];
	if ($sales == 'a') { //全業務加總
		$pOnBoard = '0000-00-00';

	}
	##

	//
	$tmpD = ($yr+1911)."-".str_pad($mn,2,'0',STR_PAD_LEFT)."-31";
	$check = ($pOnBoard > $tmpD)? false:true;


	##
	
    //去年
    $last_start = ($yr + 1910).'-01-01' ;   //去年起始
    $last_end = ($yr + 1910).'-12-31' ;     //去年結束

    //20210803廷尉改桃園，所以只算桃園計算部分數據


   

    $i = 1 ;
    // $sql = "SELECT * FROM tSalesReport WHERE sDate >= '".$last_start."' AND sDate <= '".$last_end."' ".$sql_str." ORDER BY sDate ASC";
    if (is_numeric($sales)) { //全業務加總

    	$sql = "SELECT * FROM tSalesReport WHERE sDate >= '".$last_start."' AND sDate <= '".$last_end."'  AND sSales ='".$sales."' ORDER BY sDate ASC";
    	
	}else{
		$sql = "SELECT
					sDate,
					SUM(sSignQuantity) AS sSignQuantity,
					SUM(sCaseTwQuantity) AS sCaseTwQuantity,
					SUM(sCaseOtherQuantity) AS sCaseOtherQuantity,
					SUM(sCaseScrivenerQuantity) AS sCaseScrivenerQuantity,
					SUM(sCaseUnTwQuantity) AS sCaseUnTwQuantity,
					SUM(sCertifiedMoney) AS sCertifiedMoney,
					SUM(sCertifiedMoneyTw) AS sCertifiedMoneyTw,
					SUM(sCertifiedMoneyOther) AS sCertifiedMoneyOther,
					SUM(sCaseFeedBackMoney) AS sCaseFeedBackMoney,
					SUM(sCaseFeedBackMoneyTw) AS sCaseFeedBackMoneyTw,
					SUM(sCaseFeedBackMoneyOther) AS sCaseFeedBackMoneyOther

				FROM
					tSalesReport WHERE sDate >= '".$last_start."' AND sDate <= '".$last_end."' GROUP BY sDate ORDER BY sDate ASC";
	}
    // echo $sql;
    // die;
    unset($sql_str);
    // echo $sql."<br>\n" ; exit;
    $rs = $conn->Execute($sql) ;
    
    $summary2 = array() ;
    $totalData = array();//總計
    //計算升降級考核評分
    $seasonLast = array() ;
	while (!$rs->EOF) {
		//簽約數 達成率
		$summary2[$i]['targetcount'] = $rs->fields['sSignQuantity'];    //簽約數
		##
        
		//進件量 成長率
		
		$summary2[$i]['twcount'] = $rs->fields['sCaseTwQuantity']; //台屋
		$summary2[$i]['othercount'] = $rs->fields['sCaseUnTwQuantity']; // 他牌+非仲介(非台屋)		
		$summary2[$i]['groupcount'] = $summary2[$i]['twcount']+$summary2[$i]['othercount'];

		$totalData['lasttwcount'] +=$summary2[$i]['twcount'];
		$totalData['lastothercount'] += $summary2[$i]['othercount'];


		$summary2[$i]['twcount38'] = $rs->fields['sCaseTwQuantityTaichung']+$rs->fields['sCaseTwQuantityNantou']+$rs->fields['sCaseTwQuantityChanghua']; //台屋 中部
		$summary2[$i]['othercount38'] = $rs->fields['sCaseOtherQuantityTaichung']+$rs->fields['sCaseOtherQuantityNantou']+$rs->fields['sCaseOtherQuantityChanghua'];
		
		$totalData['lasttwcount38'] +=$summary2[$i]['twcount38'];
		$totalData['lastothercount38'] += $summary2[$i]['othercount38'];
		##

		//季
		$sess = 0;
		if ($i <= 3) {  //第一季
			$sess = 1;
		}else if ($i > 3 && $i <= 6) {
			$sess = 2;
		}else if ($i > 6 && $i <= 9) {
			$sess = 3;
		}else if ($i >9 && $i <=12) {
			$sess = 4;
		}

		//簽約數/達成率
		$seasonLast[$sess]['targetcount'] +=  $summary2[$i]['targetcount'] ;    //簽約數
		// $seasonLast[1]['target'] += $summary2[$i]['target'] ;   //達成率

		##
			
		//進件量/成長率

		$seasonLast[$sess]['twcount'] += $summary2[$i]['twcount'] ;  //進件量(台屋)
		$seasonLast[$sess]['othercount'] += $summary2[$i]['othercount'] ;     //進件量(非台屋)
		$seasonLast[$sess]['groupcount'] += ($summary2[$i]['twcount']+$summary2[$i]['othercount']);
		$seasonLast[$sess]['twcount38'] += $summary2[$i]['twcount38']; //台屋 中部
		$seasonLast[$sess]['othercount38'] += $summary2[$i]['othercount38'];//台屋 中部
		##
        $i++;

		$rs->MoveNext() ;
	}
	unset($sess);
    
   // print_r($summary2);
    ##
     
    //今年
	$date_start = ($yr + 1911).'-01-01' ;   //今年起始
	$date_end = ($yr + 1911).'-12-31' ;     //今年結束
	

	$i = 1;
	// 
	if (is_numeric($sales)) { 
		$sql = "SELECT * FROM tSalesReport WHERE sDate >= '".$date_start."' AND sDate <= '".$date_end."'  AND sSales ='".$sales."' ORDER BY sDate ASC";
		
		
	}else{ //全業務加總
		$sql = "SELECT
				sDate,
					SUM(sSignQuantity) AS sSignQuantity,
					SUM(sCaseTwQuantity) AS sCaseTwQuantity,
					SUM(sCaseOtherQuantity) AS sCaseOtherQuantity,
					SUM(sCaseScrivenerQuantity) AS sCaseScrivenerQuantity,
					SUM(sCaseUnTwQuantity) AS sCaseUnTwQuantity,
					SUM(sCertifiedMoney) AS sCertifiedMoney,
					SUM(sCertifiedMoneyTw) AS sCertifiedMoneyTw,
					SUM(sCertifiedMoneyOther) AS sCertifiedMoneyOther,
					SUM(sCaseFeedBackMoney) AS sCaseFeedBackMoney,
					SUM(sCaseFeedBackMoneyTw) AS sCaseFeedBackMoneyTw,
					SUM(sCaseFeedBackMoneyOther) AS sCaseFeedBackMoneyOther

				FROM
					tSalesReport WHERE sDate >= '".$date_start."' AND sDate <= '".$date_end."' GROUP BY sDate ORDER BY sDate ASC";
		// echo $sql."<br>";
	}

	$rs = $conn->Execute($sql) ;
    $CheckMonth = (int)date('m');
	while (!$rs->EOF) {
		// echo $rs->fields['sDate']."<bR>";
		//簽約數 達成率
		$summary1[$i]['targetcount'] = $rs->fields['sSignQuantity'];    //簽約數
		if ($sales == 34) {
			$summary1[$i]['target'] = getOwnStoreTarget($summary1[$i]['targetcount'],$yr,$i,$sales,7);   //達成率
		}else{
			$summary1[$i]['target'] = getOwnStoreTarget($summary1[$i]['targetcount'],$yr,$i,$sales,10);   //達成率
		}
		
		
		$summary1[$i]['twcount'] = $rs->fields['sCaseTwQuantity']; //台屋		
		$summary1[$i]['othercount'] = $rs->fields['sCaseUnTwQuantity']; // 他牌+非仲介
		$summary1[$i]['groupcount'] = $summary1[$i]['twcount']+$summary1[$i]['othercount'];
		$totalData['twcount'] +=$summary1[$i]['twcount'];
		$totalData['othercount'] += $summary1[$i]['othercount'];


		//保證費
		$summary1[$i]['certifiedMoneyTw'] = $rs->fields['sCertifiedMoneyTw'];//台屋
		$summary1[$i]['certifiedMoneyOther'] = $rs->fields['sCertifiedMoneyOther'];// 他牌+非仲介
		$totalData['certifiedMoneyTw'] += $summary1[$i]['certifiedMoneyTw'];
		$totalData['certifiedMoneyOther'] += $summary1[$i]['certifiedMoneyOther'];

		//回饋
		$summary1[$i]['caseFeedBackMoneyTw'] = $rs->fields['sCaseFeedBackMoneyTw'];//台屋
		$summary1[$i]['caseFeedBackMoneyOther'] = $rs->fields['sCaseFeedBackMoneyOther'];// 他牌+非仲介
		$totalData['caseFeedBackMoneyTw'] += $summary1[$i]['caseFeedBackMoneyTw'];
		$totalData['caseFeedBackMoneyOther'] += $summary1[$i]['caseFeedBackMoneyOther'];


		//淨收
		$summary1[$i]['caseIncomeTw'] = ($summary1[$i]['certifiedMoneyTw']-$summary1[$i]['caseFeedBackMoneyTw']);
		$summary1[$i]['caseIncomeOther'] = ($summary1[$i]['certifiedMoneyOther']-$summary1[$i]['caseFeedBackMoneyOther']);

		$totalData['caseIncomeTw'] += $summary1[$i]['caseIncomeTw'];
		$totalData['caseIncomeOther'] += $summary1[$i]['caseIncomeOther'];

		//中部區域顯示
		$summary1[$i]['twcountTaichung'] = $rs->fields['sCaseTwQuantityTaichung'];//台屋台中
		$summary1[$i]['twcountNantou'] = $rs->fields['sCaseTwQuantityNantou'];//台屋南投	
		$summary1[$i]['twcountChanghua'] = $rs->fields['sCaseTwQuantityChanghua'];//台屋彰化



		$summary1[$i]['othercountTaichung'] = $rs->fields['sCaseOtherQuantityTaichung'];//台屋台中
		$summary1[$i]['othercountNantou'] = $rs->fields['sCaseOtherQuantityNantou'];//台屋南投	
		$summary1[$i]['othercountChanghua'] = $rs->fields['sCaseOtherQuantityChanghua'];//台屋彰化

		$summary1[$i]['twcount38'] = $summary1[$i]['twcountTaichung']+$summary1[$i]['twcountNantou']+$summary1[$i]['twcountChanghua']; //台屋 中部
		$summary1[$i]['othercount38'] = $summary1[$i]['othercountTaichung']+$summary1[$i]['othercountNantou']+$summary1[$i]['othercountChanghua'];//台屋 中部


		// $summary1[$i]['Untw'] = $rs->fields['sCaseOtherQuantity'];//他牌
		// $summary1[$i]['scrivener'] = $rs->fields['sCaseScrivenerQuantity']; //非仲介
		
		//檢查計算區間是否滿一年
		$tmpD = ($yr+1911)."-".str_pad($i,2,'0',STR_PAD_LEFT)."-31";
		$check = ($pOnBoard > $tmpD)? false:true;

		if (($sales == 57 || $sales == 65)  ) { //他們台屋跟非台合併
			if ($check) { //滿一年(跟去年比)
				
				$summary1[$i]['groupAllshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['groupcount'],$summary1[$i]['groupcount'],'g',$check,'show');  //成長率
				$summary1[$i]['groupAll'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['groupcount'],$summary1[$i]['groupcount'],'g',$check);  //成長率

			
			}else{
				
				$summary1[$i]['groupAllshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['groupcount'],$summary1[$i]['groupcount'],'g',$check,'show');  //成長率
				$summary1[$i]['groupAll'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['groupcount'],$summary1[$i]['groupcount'],'g',$check);  //成長率

			
			}

		}elseif($sales == 68){
			$summary1[$i]['groupAllshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['othercount'],$summary1[$i]['othercount'],'g',$check,'show');  //成長率  //成長率
			$summary1[$i]['groupAll'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['othercount'],$summary1[$i]['othercount'],'g',$check);  //成長率
		}else{
			if ($check) { //滿一年
				
				// echo 'GO_';
				$summary1[$i]['groupTWshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['twcount'],$summary1[$i]['twcount'],'g',$check,'show');  //成長率
				$summary1[$i]['groupUnTWshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['othercount'],$summary1[$i]['othercount'],'g',$check,'show');  //成長率
				$summary1[$i]['groupTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['twcount'],$summary1[$i]['twcount'],'g',$check);  //成長率
				$summary1[$i]['groupUnTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['othercount'],$summary1[$i]['othercount'],'g',$check);  //成長率
			
				// $summary1[$i]['groupTWshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['twcount'],$summary1[$i]['twcount'],'g',$check,'show');  //
				//中部
				$summary1[$i]['groupTWshow38'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['twcount38'],$summary1[$i]['twcount38'],'g',$check,'show');
				$summary1[$i]['groupUnTWshow38'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['othercount38'],$summary1[$i]['othercount38'],'g',$check,'show');
				$summary1[$i]['groupTW38'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['twcount38'],$summary1[$i]['twcount38'],'g',$check);  //成長率
				$summary1[$i]['groupUnTW38'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['othercount38'],$summary1[$i]['othercount38'],'g',$check);  //成長率
			

			}else{

				// echo 'GO2_';
				$summary1[$i]['groupTWshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['twcount'],$summary1[$i]['twcount'],'gTw',$check,'show');  //成長率
				
				$summary1[$i]['groupUnTWshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['othercount'],$summary1[$i]['othercount'],'gUnTw',$check,'show');  //成長率
				$summary1[$i]['groupTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['twcount'],$summary1[$i]['twcount'],'gTw',$check);  //成長率
				$summary1[$i]['groupUnTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['othercount'],$summary1[$i]['othercount'],'gUnTw',$check);  //成長率
			
			}


		}

		

		if ($i == $mn ) { //店家/地政士明細(當月份資訊)
			$date_start = ($yr+1911).'-'.str_pad($i,2,'0',STR_PAD_LEFT).'-01 00:00:00';
			$date_end = ($yr+1911).'-'.str_pad($i,2,'0',STR_PAD_LEFT).'-31 23:59:59';
				
			$Branch = getOwnBranch($sales,$date_start,$date_end) ; //該月新進仲介店數
			$Scrivener = getOwnScrivener($sales,$date_start,$date_end);//該月新進地政士數
				
			$BranchCount = count($Branch);
			$ScrivenerCount = count($Scrivener);
			$target = $summary1[$i]['target'];//查詢月達成率
			$groupTW = $summary1[$i]['groupTW'];//查詢月成長率
			$groupUnTW = $summary1[$i]['groupUnTW'];//查詢月成長率
			$group = $summary1[$i]['groupAll']	;

			$groupTW38 = $summary1[$i]['groupTW38'];
			$groupUnTW38 = $summary1[$i]['groupUnTW38'];//查詢月成長率

				$tmp_cut2 = getUnApplyLine($sales,$Scrivener); //1/4有簽約地政士有加LINE才算
				$tmp_cut = getSameStore($sales,$Branch,$Scrivener,$tmp_cut2['scrivener']);
				
				
				
				$summary1[$i]['targetcount'] = $BranchCount+$ScrivenerCount-$tmp_cut-$tmp_cut2['score'];

				
				// $summary1[$i]['target'] = getOwnStoreTarget($summary1[$i]['targetcount'],$yr,$i);   //達成率
				if ($sales == 34) {
					$summary1[$i]['target'] = getOwnStoreTarget($summary1[$i]['targetcount'],$yr,$i,$sales,7);   //達成率
				}else{
					$summary1[$i]['target'] = getOwnStoreTarget($summary1[$i]['targetcount'],$yr,$i,$sales,10);   //達成率
				}
				unset($tmp_cut);
			


			$summary1[$i]['class'] = "show";

			//組長的成員簽約店家(只能看共區)
			
				$salesGroupList = array();
				$salesGroupListShow = 0;//不可以看
				$sql = "SELECT sMember,sCity FROM tSalesGroup WHERE sManager = '".$sales."' AND sSalesReport = 1";
				$rs2 = $conn->Execute($sql);
				while (!$rs2->EOF) {
					$salesGroupListShow = 1;
					$expArr = explode(',', $rs2->fields['sMember']);

					
					foreach ($expArr as $key => $value) {
						//簽約店
						$sql = "SELECT pName FROM  tPeopleInfo WHERE pId = '".$value."'";
						$rs3 = $conn->Execute($sql);
						$salesGroupList[$value]['name'] = $rs3->fields['pName'];
						if (!is_array($salesGroupList[$value]['branch'])) {
							$salesGroupList[$value]['branch'] = array();
						}
						if (!is_array($salesGroupList[$value]['scrivener'])) {
							$salesGroupList[$value]['scrivener'] = array();
						}

						if (getOwnBranch($value,$date_start,$date_end,'','',$rs2->fields['sCity'])) {
							$salesGroupList[$value]['branch'] = array_merge($salesGroupList[$value]['branch'],getOwnBranch($value,$date_start,$date_end,'','',$rs2->fields['sCity']));
						
						}

						if (getOwnScrivener($value,$date_start,$date_end,'','',$rs2->fields['sCity'])) {
							$salesGroupList[$value]['scrivener'] = array_merge($salesGroupList[$value]['scrivener'],getOwnScrivener($value,$date_start,$date_end,'','',$rs2->fields['sCity']));
						
						}
						//行程
						if (!is_array($salesGroupList[$value]['calendar'])) {

							$salesGroupList[$value]['calendar'] = array();
						}

						if (getCalendar($value,$yr,$mn,$rs2->fields['sCity'])) {
							$salesGroupList[$value]['calendar'] = array_merge($salesGroupList[$value]['calendar'],getCalendar($value,$yr,$mn,$rs2->fields['sCity']));

						}
						
						// if ($_SESSION['member_id'] == 6) {
						// 	echo $value;
						// 	header("Content-Type:text/html; charset=utf-8"); 
						// 	echo "<pre>";
						// 	print_r(getCalendar($value,$yr,$mn,$rs2->fields['sCity']));
						// }

						// if ($_SESSION['member_id'] == 6) {
						// 	echo $value;
						// 	header("Content-Type:text/html; charset=utf-8"); 
						// 	echo "<pre>";
						// 	print_r($salesGroupList[$value]);
						// }
					}
					

					unset($expArr);
					$rs2->MoveNext();
				}





				foreach ($salesGroupList as $k => $v) {

					$sortArray =  array();

					foreach ($salesGroupList[$k]['calendar'] as $key => $value) {
						
						$sortArray[$value['date']] = $value;
					}
					
					$tmp_cut2 = getUnApplyLine($sales,$v['scrivener']); //1/4有簽約地政士有加LINE才算
					$tmp_cut = getSameStore($sales,$v['branch'],$v['scrivener'],$tmp_cut2['scrivener']);
					
					$salesGroupList[$k]['targetcount'] = count($v['branch'])+count($v['scrivener'])-$tmp_cut-$tmp_cut2['score'];

					// $salesGroupList[$value]['calendar'] = array();
					ksort($sortArray);


					$salesGroupList[$k]['calendar'] = array();
					$salesGroupList[$k]['calendar'] = array_merge($salesGroupList[$k]['calendar'],$sortArray);

					// echo "<pre>";
					// print_r($sortArray);

					unset($tmp_cut2);unset($tmp_cut);unset($sortArray);unset($d);
				}


				// echo "<pre>";
				// print_r($salesGroupList[65]['calendar']);

			
			
			

		}

		//季
		$sess = 0;
		
		//使用量有排除的問題，所以單獨拉出來算
		if ($i <= 3) {  //第一季
			$sess = 1;
			
		}else if ($i > 3 && $i <= 6) {   //第二季
			$sess = 2;
		}
		else if ($i > 6 && $i <= 9) {   //第三季
			$sess = 3;

		}else if ($i >9 && $i <=12) {    //第四季
			$sess = 4;

		}

		//簽約數/達成率
			$season1[$sess]['targetcount'] += $summary1[$i]['targetcount'] ;    //簽約數
			if ($sess == 1) {
				$season1[$sess]['targetPart'] += $summary1[$i]['target'];
			}
			
			//進件量/成長率
			$season1[$sess]['twcount'] += $summary1[$i]['twcount'] ;  //進件量(台屋)
			$season1[$sess]['othercount'] += $summary1[$i]['othercount'] ;     //進件量(非台屋)
			$season1[$sess]['groupcount'] += $summary1[$i]['groupcount'];
			##

			//中部
			//進件量/成長率
			$season1[$sess]['twcount38'] += $summary1[$i]['twcount38'] ;  //進件量(台屋)
			$season1[$sess]['othercount38'] += $summary1[$i]['othercount38'] ;     //進件量(非台屋)
			

		// if (($sales == 42 ) &&  $mn >3 && $mn <= 6 && $i < 4) { //未滿一年
		// 	$summary1[$i]['twcount'] = $rs->fields['sCaseTwQuantity'];
		// 	$summary1[$i]['othercount'] = $rs->fields['sCaseUnTwQuantity']; // 他牌+非仲介

		// }

		$i++;

		$rs->MoveNext();
	}

	// echo "<prE>";
		// print_r($summary1);
	// 	die;
		##
	unset($sess);
	unset($CheckMonth);

	//算出季的平均數字
	for ($i=1; $i <= 4; $i++) { 
		// echo $season1[$i]['targetcount']."_";
		if ($i == 1 && $yr == 110) {
			// echo $season1[$i]['targetPart'];			
				$season1[$i]['target'] =round($season1[$i]['targetPart']/3);

		}else{
			

			if ($sales == 34) {
				// $season1[$i]['targetcount'] = 32;
				// echo $i."_".$season1[$i]['targetcount']."_";
				//以季來看簽約間數22~30 不能加分 ，31開始加分 計算多一間約多0.6
				if ($season1[$i]['targetcount'] <= 21) { 
					$season1[$i]['target'] = round(($season1[$i]['targetcount']/(7*3))*100);//一月簽7間滿分 
				}else if ($season1[$i]['targetcount'] > 21 && $season1[$i]['targetcount'] <= 30) { //維持滿分
					$season1[$i]['target'] = round((21/(7*3))*100);//一月簽7間滿分 
				}else{
					$target34 = round((21/(7*3))*100);//原本部分

					$normalTarget = 30;//一般業務標準
					$spTarget =round((($season1[$i]['targetcount'] - $normalTarget)/30)*100);//超過30恢復原標準
					
					$season1[$i]['target'] = $target34+$spTarget;
					unset($spTarget);unset($normalTarget);unset($target34);
					
				}

				// $season1[$i]['target'] = round(($season1[$i]['targetcount']/(7*3))*100);//一月簽7間滿分 
				
			}else{
				$season1[$i]['target'] = round((($season1[$i]['targetcount']*10)/3));//達成率
			}

			
		}
		

		//檢查計算區間是否任職滿一年
		if ($i == 1) {
			$tmpD = ($yr+1911)."-03-01";
		}elseif($i == 2){
			$tmpD = ($yr+1911)."-06-01";
		}elseif($i == 3){
			$tmpD = ($yr+1911)."-09-01";
		}elseif($i == 4){
			$tmpD = ($yr+1911)."-12-01";
		}

		
		$check = ($pOnBoard > $tmpD)? false:true;
		// echo $pOnBoard.">".$tmpD;


		if (($sales == 57 || $sales == 65) ) { //他們台屋跟非台合併
			if ($check) { //滿一年(比較去年同期季)
				// echo 'GO';
				// $season1[$i]['groupTW'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['twcount'], $season1[$i]['twcount'], 'gTw',$check);
				// $season1[$i]['groupUnTW'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check);
				
				// $season1[$i]['groupTWshow'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['twcount'], $season1[$i]['twcount'], 'gTw',$check,'show');
				// $season1[$i]['groupUnTWshow'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check,'show');


				$season1[$i]['groupAllshow'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['groupcount'], $season1[$i]['groupcount'], 'g',$check,'show');
				$season1[$i]['groupAll'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['groupcount'], $season1[$i]['groupcount'], 'g',$check);
			
			}else{
				// echo 'GO2';
				
				// $season1[$i]['groupTW'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['twcount'], $season1[$i]['twcount'], 'gTw',$check);
				// $season1[$i]['groupUnTW'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check);
				
				// $season1[$i]['groupTWshow'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['twcount'], $season1[$i]['twcount'], 'gTw',$check,'show');
				// $season1[$i]['groupUnTWshow'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check,'show');

				$season1[$i]['groupAllshow'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['groupcount'], $season1[$i]['groupcount'], 'g',$check,'show');
				$season1[$i]['groupAll'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['groupcount'], $season1[$i]['groupcount'], 'g',$check);
			
			}
		}elseif($sales == 68){
			$season1[$i]['groupAllshow'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['othercount'], $season1[$i]['othercount'], 'g',$check,'show');
			$season1[$i]['groupAll'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['othercount'], $season1[$i]['othercount'], 'g',$check);
		}else{
			if ($check) { //滿一年(比較去年同期季)
				// echo 'GO';
				$season1[$i]['groupTW'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['twcount'], $season1[$i]['twcount'], 'gTw',$check);
				$season1[$i]['groupUnTW'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check);
				
				$season1[$i]['groupTWshow'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['twcount'], $season1[$i]['twcount'], 'gTw',$check,'show');
				$season1[$i]['groupUnTWshow'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check,'show');
				//中部
				$season1[$i]['groupTW38'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['twcount38'], $season1[$i]['twcount38'], 'gTw',$check);
				$season1[$i]['groupUnTW38'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['othercount38'], $season1[$i]['othercount38'], 'gUnTw',$check);
				
				$season1[$i]['groupTWshow38'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['twcount38'], $season1[$i]['twcount38'], 'gTw',$check,'show');
				$season1[$i]['groupUnTWshow38'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['othercount38'], $season1[$i]['othercount38'], 'gUnTw',$check,'show');
			
			}else{
				// echo 'GO2';
				
				$season1[$i]['groupTW'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['twcount'], $season1[$i]['twcount'], 'gTw',$check);
				$season1[$i]['groupUnTW'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check);
				
				$season1[$i]['groupTWshow'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['twcount'], $season1[$i]['twcount'], 'gTw',$check,'show');
				$season1[$i]['groupUnTWshow'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check,'show');
			
			}

		}
		
		
		// if ($check) { //滿一年
		// 	$summary1[$i]['groupTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['twcount'],$summary1[$i]['twcount'],'g',$check);  //成長率
		// 	$summary1[$i]['groupUnTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['othercount'],$summary1[$i]['othercount'],'g',$check);  //成長率
		// }else{
			
		// 	$summary1[$i]['groupTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['twcount'],$summary1[$i]['twcount'],'gTw',$check);  //成長率
		// 	$summary1[$i]['groupUnTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['othercount'],$summary1[$i]['othercount'],'gUnTw',$check);  //成長率
		
		// }

		// $season1[$i]['group'] = getPercent107($sales,($yr+1911),$i,$season1[($i-1)]['g_count'],$season1[$i]['g_count'],'g');//成長率
		

		// $season1[$i]['use'] = getPercent107($sales,($yr+1911),$i,$season1[($i-1)]['usecount'],$season1[$i]['usecount'],'u');//使用率

		// echo $season1[$i]['use'].":";

		// $season1[$i]['contribution'] = getPercent107($sales,($yr+1911),$i,$season1[($i-1)]['realmoney'],$season1[$i]['realmoney'],'c');//貢獻率
		
		
		//季要顯示跟上一季相差多少
		// $season2[$i]['target'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['target'],$season1[$i]['target'],'t');
		// $season2[$i]['group'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['group'],$season1[$i]['group'],'g');//成長率
		// $season2[$i]['use'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['use'],$season1[$i]['use'],'u');//使用率
		// $season2[$i]['contribution'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['contribution'],$season1[$i]['contribution'],'c');//貢獻率
        ##
		

	}


	unset($tmpD);
	//本季考核
	if ($mn <= 3 ) {
		$sess = 1 ;
			
	}elseif ($mn > 3 && $mn <=6) {
	
		$sess = 2 ;
	}elseif ($mn >6 && $mn <=9) {
	
		$sess = 3 ;
	}elseif ($mn >9 && $mn <=12) {
	

		$sess = 4 ;
	}

	$season1[$sess]['class']  = "show";
	// $seasontarget = $season1[$sess]['target'];//達成率
	// $seasongroupTW = $season1[$sess]['groupTW'];
	// $seasongroupUnTW = $season1[$sess]['groupUnTW'];
	$showseason['targetcount'] = $season1[$sess]['targetcount'];		
	$showseason['target'] = $season1[$sess]['target'];
	$showseason['groupTW'] = $season1[$sess]['groupTW'];
	$showseason['groupUnTW'] = $season1[$sess]['groupUnTW'];
	$showseason['groupAll'] = $season1[$sess]['groupAll'];
	##
	// print_r($showseason);
		#############
	//有效率effective 
	// 用前一年度(由結算季度往前回推四季作為一整年度)所簽約代書及仲介通路之總和為分母，以分母的店家有進案之店家數為分子
	// 所得之比例數字以65%為基準 達此基準者，本指標所得之積分為20分，基準數字每增減一分，所得積分及增減一分，小於等於45% 就0分
	//(EX1:70%等於20分+5分=25分) (EX2:64%等於20分-1分=19分)
	//※已經停用的店 有效率分子 分母都要拉掉不計算 20170424
	//這季 $sess
	/*
		$eff1 = getEffective3($yr,$sess,$sales);

		if($yr == 106 && $sess == 1){ //第一季算法
			

			if ($eff1['effective'] > 45) {//低於45%就是0
				$tmp = $eff1['effective'] - 65;
				if ($tmp == 0) {
					$eff1['score'] = 20;
				}else{
					$eff1['score'] = 20+$tmp;
				}
			}else{
				$eff1['score'] = 0;
			}
		}else{
			//1.結算季度不算，往前推四個季度,所簽約代書及仲介通路之總合為分母，結算季度加上前四個季度為分子，  
	          // 所得之比例數字以70%為30分，基準數字每增減1%所得積分即增減0.4分。
	          // 

			
				$tmp = $eff1['effective'] - 70;
				$eff1['score'] = 30+($tmp * 0.4); 
				if ($eff1['effective'] == 0 ) { 
					$eff1['score'] = 0;
				}

			
		}
	*/
    //判斷季別
    $tmp[0] = $yr + 1911 ;
    $tmp[1] = $mn ;
    if ($tmp[1] <= 3) {     //第一季
        //本季
        $sDate = $tmp[0].'-01-01 00:00:00' ;
        $eDate = $tmp[0].'-03-31 23:59:59' ;
        ##
        
         //本季分母用
	    // $sDateDiv = ($tmp[0] - 1).'-10-01 00:00:00' ;
	    $eDateDiv = ($tmp[0] - 1).'-12-31 23:59:59' ;
        ##
    }
    else if (($tmp[1] >= 4) && ($tmp[1] <= 6)) {    //第二季
        //本季
        $sDate = $tmp[0].'-04-01 00:00:00' ;
        $eDate = $tmp[0].'-06-30 23:59:59' ;
        ##
        
        //本季分母用
    	// $sDateDiv = $tmp[0].'-01-01 00:00:00' ;
    	$eDateDiv = $tmp[0].'-03-31 23:59:59' ;
        ##
    }
    else if (($tmp[1] >= 7) && ($tmp[1] <= 9)) {    //第三季
        //本季
        $sDate = $tmp[0].'-07-01 00:00:00' ;
        $eDate = $tmp[0].'-09-30 23:59:59' ;
        ##
        
        //本季分母用
    	// $sDateDiv = $tmp[0].'-04-01 00:00:00' ;
    	$eDateDiv = $tmp[0].'-06-30 23:59:59' ;
        ##
    }
    else {      //第四季
        //本季
        $sDate = $tmp[0].'-10-01 00:00:00' ;
        $eDate = $tmp[0].'-12-31 23:59:59' ;
        ##
        
        //本季分母用
    	// $sDateDiv = $tmp[0].'-07-01 00:00:00' ;
    	$eDateDiv = $tmp[0].'-09-30 23:59:59' ;
        ##
    }
    ##

    $eff1 = array() ;
    
    $eff1['range_start2'] = DateChange($sDate) ;
    $eff1['range_end2'] = DateChange($eDate) ;
    
    $eff1['range_start'] = '000年00月00日' ;
    $eff1['range_end'] = DateChange($eDateDiv) ;
    
    
    $sql = 'SELECT * FROM tSalesReportStore WHERE sSales = "'.$sales.'" AND sDate >= "'.$sDate.'" AND sDate <= "'.$eDate.'";' ;

    // if ($_SESSION['member_id'] == 6) {
    // 	echo $sql;
    // }
  
    $rs = $conn->Execute($sql) ;
    // print_r($rs->fields) ;
    //分子 (從以前到前一季的店家)有進案的店家
    $A = $rs->fields['sScrivener'] + $rs->fields['sRealty'] ;
    $eff1['no'] = $A ;

     //分母 (從以前到前一季的店家)
    $B = $rs->fields['sScrTotal'] + $rs->fields['sRealTotal'] ;

    if ($sales == 42 && $yr == 108 && ($mn == 10 || $mn == 11 || $mn == 12)) {
    	$B = $B-80;//政耀說的-80給欣宜 20200102
    }

    $eff1['total'] = $B ;
    // print_r($rs->fields) ;
    // print_r($rs->fields) ; exit ;

    // echo $B;
    if ($B > 0) {
    	$eff1['effective'] = round(($A / $B ), 2)* 100 ;
    }else{
    	$eff1['effective'] = 0 ;
    }
    


    $json = json_decode($rs->fields['sStore'],true);

    // if (condition) {
    // 	# code...
    // }

    $eff1['data']['scrcase'] = getScrivener107($json['sc_yes']);
    $eff1['data']['scrnocase'] = getScrivener107($json['sc_no']);

    $eff1['data']['branchcase'] = getBranch107($json['br_yes']);
    $eff1['data']['branchnocase'] = getBranch107($json['br_no']);
    
   
   
    
   
	// echo $sql;
 //    echo "<pre>";
	// print_r($eff);
	// echo "</pre>";
   //$eff1.data['scrcase']



    ##各績效分數計算##
	// $percentTarget = Round((25/100),2);
	// $percentGroupTw = Round((25/100),2);
	// $percentGroupUnTw = Round((25/100),2);
	// $percentUse = 25;
	// $EffectiveGoal = 65;//基準趴數
	// $EffectiveBaseScore = 20;//基準趴數的基準分
	// $EffectivePlus = 1;//變動分數(基準趴數增減多少?趴)
	// $EffectivePlus2 = 1;//變動分數(基準趴數增減多少$EffectivePlus趴就扣?分)

	$percentTarget = Round(($percent['pSign']/100),2);//通路簽約數占比
	$percentGroupTw = Round(($percent['pGroupTW']/100),2);//成長率(台)
	$percentGroupUnTw = Round(($percent['pGroupUnTW']/100),2);//成長率(非台)
	$percentGroupALL = Round((($percent['pGroupUnTW']+$percent['pGroupTW'])/100),2);//成長率
	$percentUse = $percent['pPercentUse'];//有效使用率
	$EffectiveGoal = $percent['pEffectiveGoal'];//基準趴數
	$EffectiveBaseScore = $percent['pEffectiveBaseScore'];//基準趴數的基準分
	$EffectivePlus = $percent['pEffectivePlus'];//變動分數(基準趴數增減多少?趴)
	$EffectivePlus2 = $percent['pEffectivePlus2'];//變動分數(基準趴數增減多少$EffectivePlus趴就扣?分)


	$seasontarget = ($season1[$sess]['target']*$percentTarget);//達成率
	// $seasongroupTW = (($season1[$sess]['groupTW']*$percentGroupTw) > $percent['pGroupTW'])? $percent['pGroupTW']:($season1[$sess]['groupTW']*$percentGroupTw);//成長率(台)
	// $seasongroupUnTW = (($season1[$sess]['groupUnTW']*$percentGroupUnTw) > $percent['pGroupUnTW'])? $percent['pGroupUnTW']:($season1[$sess]['groupUnTW']*$percentGroupUnTw);//成長率(非台)
	
	$seasongroupTW = $season1[$sess]['groupTW']*$percentGroupTw;
	$seasongroupUnTW = $season1[$sess]['groupUnTW']*$percentGroupUnTw;
	$seasongroupALL = $season1[$sess]['groupAll']*$percentGroupALL;

	  	if ($eff1['effective'] > ($EffectiveGoal-$EffectiveBaseScore)) {//扣到沒有分數就是0
			$tmpScore = $eff1['effective'] - $EffectiveGoal;//相減後取得差數
			
			$tmpScore = (($tmpScore*$EffectivePlus2)/$EffectivePlus);
			$eff1['score'] = $EffectiveBaseScore+$tmpScore;//
		
		}else{
			$eff1['score'] = 0;
		}

		
		// if ($eff1['score'] > $percentUse) {
		// 	$eff1['score'] = $percentUse;
		// }
	##
		//第二季開始
		// 分數文字改黑色，未達標紅字並文字提醒
		// 本季考核分數110以上 季簽約數未達25間 為不及格
		// 本季考核分數90以下 簽約數未達22間 為不及格
		//政祺不適用
	//總分
	if ($sales == 57 || $sales == 65 || $sales = 68) {
		// echo $season1[$sess]['groupALL']."_".$percentGroupALL;
		$grade = $seasontarget+$seasongroupALL+$eff1['score'];

	}else{
		$grade = $seasontarget+$seasongroupTW+$seasongroupUnTW+$eff1['score'];
	}

	
	$gradecolor = "#000088";
	$gradeNotice = '';//未達標提醒文字
	if ($sales == 34) {
		if (($mn > 3 && $yr >= 109)) { 
			if ($showseason['targetcount'] < 21) {
				$gradecolor = "#FF0000";
				$gradeNotice = '[本季尚缺簽約數'.(21-$showseason['targetcount']).']';
			}
		}
	}else{
		if (($mn > 3 && $yr >= 109)) {
			if ($grade > 110 && $showseason['targetcount'] < 25) {
				$gradecolor = "#FF0000";
				$gradeNotice = '[本季尚缺簽約數'.(25-$showseason['targetcount']).']';
			}elseif ($grade < 90 && $showseason['targetcount'] < 22) {
				$gradecolor = "#FF0000";
				$gradeNotice = '[本季尚缺簽約數'.(22-$showseason['targetcount']).']';
			}
		}
	}

	
	
	
}else{
	$script = '$("[name=\'excel\']").hide();';
		
}

if ($_SESSION['member_id'] == 6) {
	// echo "<pre>";
	// print_r($summary1);

	// print_r($summary2);
}


##//成長率、有效使用率((該季)/去年該季)*100%[BY季] (本季與去年同季相比)
function getPercent107($sales, $year, $s, $last, $now, $type,$check,$show='') {
	$val = 0;
	// echo $sales.",".$year.",".$s.",".$last.",".$now.",".$type."<br>";
	// echo $year;
	if ($s == 1 && !$check) { //第一季要求出上一季
		$date_s = ($year-1).'-10-01'; 
		$date_e = ($year-1).'-12-31'; 
		// echo ($year-1);
		

		//上一年的第三季
		// $date_s2 = ($year-1).'-07-01 00:00:00'; 
		// $date_e2 = ($year-1).'-09-30 23:59:59'; 
		
		// $tmp = getOwnCase($sales,$date_s,$date_e);
		// $tmp2 = getOwnCase($sales,$date_s2,$date_e2);
		$tmp = getLastData($sales,$date_s,$date_e,$type);

		if($type == 'gTw'){
			$last = $tmp['tw'];//成長率

		}elseif($type == 'gUnTw'){
			$last = $tmp['other'];	
		}else if ($type == 'g') {
			$last = $tmp['all'];	
		}

		
	}

	if ($last == 0) {
		$val = 0;

	}else{
		if ($show) {
			$val = round((($now/$last)-1)*100);
		}else{
			$val = round(($now/$last)*100);
		}
	}

	
	
	


	
	
	
	return $val;
}
##//成長率、有效使用率((該月)/去年該月)*100%[BY月]  
function getPercentMonth107($sales,$year,$month,$last,$now,$type,$check,$show='') {
	
	// echo $month;
	// echo $check;
	
	if ($month == 1 && !$check) {
		
		$date_s = ($year-1).'-12-01'; 
		$date_e = ($year-1).'-12-31'; 
		
		// $tmp = getOwnCase($sales,$date_s,$date_e);
		$tmp = getLastData($sales,$date_s,$date_e,$type);


		if($type == 'gTw'){
			$last = $tmp['tw'];//成長率

		}elseif($type == 'gUnTw'){
			$last = $tmp['other'];
			
		}elseif ($type == 'g') {
			$last = $tmp['all'];
		}


	}
	
	

	if ($last == 0) {
		$val = 0;

	}else{
		if ($show) {
			$val = round((($now/$last)-1)*100);
		}else{
			$val = round((($now/$last))*100);
		}
		
	}

	
	return $val;
}
//未滿一年撈取查詢年前一季用(限查詢年第一季使用)
function getLastData($sales,$sDate,$eDate,$type){

	global $conn;

	$sql = "SELECT SUM(sCaseTwQuantity) AS sCaseTwQuantity,SUM(sCaseUnTwQuantity) AS sCaseUnTwQuantity FROM tSalesReport WHERE sSales = '".$sales."' AND sDate >= '".$sDate."' AND sDate <= '".$eDate."'";
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);

	$tmp['tw'] = $rs->fields['sCaseTwQuantity'];
	$tmp['other'] = $rs->fields['sCaseUnTwQuantity'];
	$tmp['all'] = $rs->fields['sCaseTwQuantity']+$rs->fields['sCaseUnTwQuantity'];
	return $tmp;

}

function getScrivener107($id){
	global $conn;

	// print_r($id);
	if ($id) {
		$sql = "SELECT
				s.sName,
				s.sOffice,
				s.sCreat_time,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip = s.sZip1) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip = s.sZip1) AS area,
				s.sSales AS preSales,
				s.sId
			FROM
				tScrivener AS s
			WHERE
				s.sId IN(".@implode(',', $id).")
				
				ORDER BY s.sId ASC";
	// echo $sql."<bR>";
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$rs->fields['sCreat_time'] = DateChange($rs->fields['sCreat_time']);
			$tmp[] = $rs->fields;

			$rs->MoveNext();
		}
	}

	

	return $tmp;
}

function getBranch107($id){
	global $conn;

	if ($id) {
		$sql = "SELECT
				
				(SELECT bName FROM tBrand AS br WHERE br.bId = b.bBrand) AS brand,
				b.bOldStoreID,
				b.bStore,
				bName,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip = b.bZip) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip = b.bZip) AS area,
				b.bCashierOrderMemo,
				b.bId,
				b.bCreat_time,
				CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode
			FROM
				tBranch AS b 
			WHERE
				b.bId IN(".@implode(',', $id).")
			ORDER BY b.bId ASC";

		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$rs->fields['bCreat_time'] = DateChange($rs->fields['bCreat_time']);
			$tmp[] = $rs->fields;


			$rs->MoveNext();
		}
	}
	
	
	return $tmp;			
}

function getUnApplyLine($sales,$scrivener){ 
	global $conn;
 	
	$data = array('score'=>0,'scrivener'=>array());

	if (is_array($scrivener)) {
		foreach ($scrivener as $k => $v) {

			
			if ($v['sSignDate2'] >= '2018-01-04') { //20180109 佩琪說1/4號簽進來的才要算LINE的部分  1/4前是以之前的算法
				$sql = "SELECT * FROM tLineAccount WHERE lStatus = 'Y' AND lTargetCode = 'SC".str_pad($v['sId'], 4,'0',STR_PAD_LEFT)."'";
				
				$rs = $conn->Execute($sql);
				

				if ($rs->RecordCount() == 0 && $v['sSignCount'] != 1) {
					$data['score']++;
					
					
					$data['scrivener'][] = $v['sId'];
					
					
				}
			}
			// $sql= "SELECT sStore,COUNT(sStore) AS storeCount FROM tSalesSign WHERE sType = 2 AND sStore = '".$v['bId']."'";
			// $rs = $conn->Execute($sql);

			// if ($rs->fields['storeCount'] > 1) {
			// 	
			// }
			
		}

		


	}

	return $data;
}
//因為換地區所以互換
function getOtherSalesData($sales,$sDate,$eDate){

	global $conn;

	if ($sales == 38) {
		$sales = 42;
	}else if($sales == 42) {
		$sales = 38;
	}else{
		return false;
	}


	$sql = "SELECT * FROM tSalesReport WHERE sDate >= '".$sDate."' AND sDate <= '".$eDate."' AND sSales ='".$sales."' ORDER BY sDate ASC";

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$data['twcount'] +=  $rs->fields['sCaseTwQuantity']; //台屋
		$data['othercount'] += $rs->fields['sCaseUnTwQuantity']; // 他牌+非仲介(非台屋)



		$rs->MoveNext();
	}


	return $data;

    
}

function getCalendar($value,$yr,$mn,$city){
	global $conn;
	$sql = '
			SELECT
				*
			FROM
				tCalendar
			WHERE
				YEAR(cStartDateTime) = "'.($yr+1911).'"
				AND MONTH(cStartDateTime) = "'.$mn.'"
				AND cCreator = "'.$value.'"
				AND cCity = "'.$city.'"
			ORDER BY
				cStartDateTime
			ASC;
		' ;
	
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$sub = $rs->fields['cSubject'] ;		//目的：1=例行拜訪、2=開發拜訪、3=案件處理討論、4=其他
		$from = str_replace('-', '/', substr($rs->fields['cStartDateTime'], 5,11));
		$to = str_replace('-', '/', substr($rs->fields['cEndDateTime'], 5,11));
		$desc = nl2br($rs->fields['cDescription']);

		$date = str_replace('-', '', substr($rs->fields['cStartDateTime'], 5,11));
		$date = str_replace(':', '', $date);
		$date = str_replace(' ', '', $date);
		

		if ($sub == 1) $sub = '例行拜訪' ;
		else if ($sub == 2) $sub = '開發拜訪' ;
		else if ($sub == 3) $sub = '案件處理討論' ;
		else $sub = '其他' ;
		
		if ($rs->fields['cClass'] == 1) {	//拜訪店家
			$brand = $rs->fields['cBrand'] ;
			$catName = $rs->fields['cStore'] ;	//店名
			
			if (($brand == 2) || empty($brand)) $brand = '' ;	//2=非仲介成交
				else {
					$sql = 'SELECT * FROM tBrand WHERE bId = "'.$brand.'";' ;
					$rel = $conn->Execute($sql) ;
					$brand = $rel->fields['bName'] ;
					if (preg_match("/^自有品牌\(*/isu", $brand)) $brand = '自有品牌' ;
				}
				
				$list[] = array(
					'from' => $from,
					'to' => $to,
					'class' => '拜訪店家',
					'subject' => $sub,
					'target' => $brand.'/'.$catName,
					'city' =>$rs->fields['cCity'],
					'desc' => $desc,
					'date'=>$date
				) ;
			
			}
			else if ($rs->fields['cClass'] == 2) {	//拜訪代書
				$list[] = array(
					'from' => $from,
					'to' => $to,
					'class' => '拜訪代書',
					'subject' => $sub,
					'target' => $rs->fields['cScrivener'],
					'city' =>$rs->fields['cCity'],
					'desc' => $desc,
					'date'=>$date
				) ;
				
			}
			else {		//其他
				$list[] = array(
					'from' => $from,
					'to' => $to,
					'class' => '其他',
					'subject' => $sub,
					'target' => '',
					'city' =>$rs->fields['cCity'],
					'desc' => $desc,
					'date'=>$date
				) ;
				
			}


			$rs->MoveNext();
	}

	
	return $list;
}
?>