<?php

if ($sales) {
                         //確認是否滿一年
    if ($sales == 'a') { //全業務加總
        $pOnBoard = '0000-00-00';
    } else {
        $sql = "SELECT pOnBoard FROM  tPeopleInfo WHERE pId = '" . $sales . "'";
        $rs  = $conn->Execute($sql);

        $pOnBoard = date('Y-m-d', strtotime("+12 month", strtotime($rs->fields['pOnBoard'])));
    }

    $tmpD  = ($yr + 1911) . "-" . str_pad($mn, 2, '0', STR_PAD_LEFT) . "-31";
    $check = ($pOnBoard > $tmpD) ? false : true;
    $check = (is_numeric($sales) && in_array($sales, [90, 118, 123])) ? true : $check; //2024-0628 偉哲、俊智、和毅的計算不用滿一年

    /**
     * 去年度數據
     */
    $last_start = ($yr + 1910) . '-01-01'; //去年起始
    $last_end   = ($yr + 1910) . '-12-31'; //去年結束

    //20210803廷尉改桃園，所以只算桃園計算部分數據
    $i = 1;
    if (is_numeric($sales)) { //指定業務
        if (in_array($sales, [38, 72])) {
            $sql = "
            SELECT * ,
            (SELECT SUM(CAST(sCaseTwQuantityTaichung AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseTwQuantityTaichung,
            (SELECT SUM(CAST(sCaseTwQuantityNantou AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseTwQuantityNantou,
            (SELECT SUM(CAST(sCaseTwQuantityChanghua AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseTwQuantityChanghua,
            (SELECT SUM(CAST(sCaseOtherQuantityTaichung AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseOtherQuantityTaichung,
            (SELECT SUM(CAST(sCaseOtherQuantityNantou AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseOtherQuantityNantou,
            (SELECT SUM(CAST(sCaseOtherQuantityChanghua AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseOtherQuantityChanghua
            FROM tSalesReport_2023  AS b
            WHERE sDate >= '" . $last_start . "'
              AND sDate <= '" . $last_end . "'
              AND sSales ='" . $sales . "'
            ORDER BY sDate ASC";

        } else {
            $sql = "SELECT * FROM tSalesReport_2023 WHERE sDate >= '" . $last_start . "' AND sDate <= '" . $last_end . "'  AND sSales ='" . $sales . "' ORDER BY sDate ASC";
        }
    } else { //全業務加總
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
                    tSalesReport_2023 WHERE sDate >= '" . $last_start . "' AND sDate <= '" . $last_end . "' GROUP BY sDate ORDER BY sDate ASC";
    }
    $rs      = $conn->Execute($sql);
    $sql_str = null;unset($sql_str);

    $summary2  = [];
    $totalData = []; //總計

    //計算升降級考核評分
    $seasonLast = [];
    //偉哲、俊智、和毅改用 sTaipeiQuantityThisYear(JSON)取代
    if (is_numeric($sales) && in_array($sales, [90, 118, 123])) {
        //2024-0628 偉哲、俊智、和毅的計算以計算年度記錄內的 sTaipeiQuantityLastYear column 為主
        //2025-0424 偉哲、俊智、和毅的計算，去年值以[去年]計算年度記錄內的 sTaipeiQuantityThisYear column 為主(解決跨月分數降低與年度開關店資料落差)
        $_date = ($yr + 1911) . date('-m-d', strtotime($rs->fields['sDate']));

//        $sql = "SELECT sDate, sTaipeiQuantityLastYear FROM tSalesReport_2023 WHERE sDate >= '" . ($yr + 1911) . "-01-01' AND sDate <= '" . ($yr + 1911) . "-12-31' AND sSales ='" . $sales . "' ORDER BY sDate ASC";
        if($yr.date("m", mktime(0, 0, 0, $mn)) >= "11404"){
            $sql = "SELECT sDate, sTaipeiQuantityLastYear FROM tSalesReport_2023 WHERE sDate >= '" . ($yr + 1911) . "-01-01' AND sDate <= '" . ($yr + 1911) . "-03-31' AND sSales ='" . $sales . "'";
            $sql .= " UNION ALL SELECT sDate, sTaipeiQuantityThisYear as sTaipeiQuantityLastYear FROM tSalesReport_2023 WHERE sDate >= '" . ($yr + 1910) . "-04-01' AND sDate <= '" . ($yr + 1910) . "-12-31' AND sSales ='" . $sales . "' ORDER BY sDate ASC";
        } else {
            $sql = "SELECT sDate, sTaipeiQuantityLastYear FROM tSalesReport_2023 WHERE sDate >= '" . ($yr + 1911) . "-01-01' AND sDate <= '" . ($yr + 1911) . "-12-31' AND sSales ='" . $sales . "' ORDER BY sDate ASC";
        }

        $rs2 = $conn->Execute($sql);
        while (! $rs2->EOF) {
            $i            = substr($rs2->fields['sDate'], 5, 2);
            $json_summary = json_decode($rs2->fields['sTaipeiQuantityLastYear'], true);

            $summary2[$i]['twcount']    = $json_summary['sCaseTwQuantity'];                       //台屋
            $summary2[$i]['othercount'] = $json_summary['sCaseUnTwQuantity'];                     // 他牌+非仲介(非台屋)
            $summary2[$i]['groupcount'] = $summary2[$i]['twcount'] + $summary2[$i]['othercount']; //加總

            $totalData['lasttwcount'] += $summary2[$i]['twcount'];       //去年度台屋件數加總
            $totalData['lastothercount'] += $summary2[$i]['othercount']; //去年度非台屋案件加總

            $rs2->MoveNext();
        }

        for ($x = 1; $x <= 12; $x++) {
            $i            = str_pad($x, 2, '0', STR_PAD_LEFT);
            $summary2[$i] = empty($summary2[$i]) ? ['twcount' => 0, 'othercount' => 0, 'groupcount' => 0] : $summary2[$i];

            //季
            $sess = ceil($x / 3);

                                                                               //季簽約數(達成率)
            $seasonLast[$sess]['targetcount'] += $summary2[$i]['targetcount']; //簽約數
                                                                               ##

            //季進件量(成長率)
            $seasonLast[$sess]['twcount']    = empty($seasonLast[$sess]['twcount']) ? 0 : $seasonLast[$sess]['twcount'];
            $seasonLast[$sess]['othercount'] = empty($seasonLast[$sess]['othercount']) ? 0 : $seasonLast[$sess]['othercount'];
            $seasonLast[$sess]['groupcount'] = empty($seasonLast[$sess]['groupcount']) ? 0 : $seasonLast[$sess]['groupcount'];

            $seasonLast[$sess]['twcount'] += $summary2[$i]['twcount'];       //進件量(台屋)
            $seasonLast[$sess]['othercount'] += $summary2[$i]['othercount']; //進件量(非台屋)
            $seasonLast[$sess]['groupcount'] += ($summary2[$i]['twcount'] + $summary2[$i]['othercount']);
            ##
        }
    } else {
        while (! $rs->EOF) {

            //2025-05-29 周榮德122 非台屋案件從2025年濾掉幸福家案件統計
            if($sales == 122 && $yr == 114){
                $CaseUnTwQuantity = getCaseUnTwQuantityDataBy122();

                if(isset($CaseUnTwQuantity[substr($rs->fields['sDate'],0,7)])){
                    $rs->fields['sCaseUnTwQuantity'] = $CaseUnTwQuantity[substr($rs->fields['sDate'],0,7)];
                }
            }

            $i = substr($rs->fields['sDate'], 5, 2);

                                                                         //月簽約數(達成率)
            $summary2[$i]['targetcount'] = $rs->fields['sSignQuantity']; //簽約數
                                                                         ##

            //月進件量(成長率)

            $summary2[$i]['twcount']    = $rs->fields['sCaseTwQuantity'];                         //台屋
            $summary2[$i]['othercount'] = $rs->fields['sCaseUnTwQuantity'];                       // 他牌+非仲介(非台屋)
            $summary2[$i]['groupcount'] = $summary2[$i]['twcount'] + $summary2[$i]['othercount']; //加總

            $totalData['lasttwcount'] += $summary2[$i]['twcount'];       //去年度台屋件數加總
            $totalData['lastothercount'] += $summary2[$i]['othercount']; //去年度非台屋案件加總

            $summary2[$i]['twcount38']    = $rs->fields['sCaseTwQuantityTaichung'] + $rs->fields['sCaseTwQuantityNantou'] + $rs->fields['sCaseTwQuantityChanghua'];          //台屋 中部(立寰)
            $summary2[$i]['othercount38'] = $rs->fields['sCaseOtherQuantityTaichung'] + $rs->fields['sCaseOtherQuantityNantou'] + $rs->fields['sCaseOtherQuantityChanghua']; //非台屋 中部(立寰)

            $totalData['lasttwcount38'] += $summary2[$i]['twcount38'];       //去年度台屋(中部、立寰)件數加總
            $totalData['lastothercount38'] += $summary2[$i]['othercount38']; //去年度非台屋(中部、立寰)案件加總
                                                                             ##

            //季
            $sess = ceil($i / 3);

                                                                               //季簽約數(達成率)
            $seasonLast[$sess]['targetcount'] += $summary2[$i]['targetcount']; //簽約數
                                                                               ##

                                                                             //季進件量(成長率)
            $seasonLast[$sess]['twcount'] += $summary2[$i]['twcount'];       //進件量(台屋)
            $seasonLast[$sess]['othercount'] += $summary2[$i]['othercount']; //進件量(非台屋)
            $seasonLast[$sess]['groupcount'] += ($summary2[$i]['twcount'] + $summary2[$i]['othercount']);
            $seasonLast[$sess]['twcount38'] += $summary2[$i]['twcount38'];       //台屋 中部
            $seasonLast[$sess]['othercount38'] += $summary2[$i]['othercount38']; //台屋 中部
                                                                                 ##

            $rs->MoveNext();
        }
        unset($sess);
    }
    ##

    /**
     * 今年度數據
     */

    //取得農曆年所在月份
    $chinese_new_year = [
        'month' => 2, //預設2月份
    ];

    $sql = 'SELECT cMonth FROM tSalesReportChineseNewYear WHERE cTwYear = ' . $yr . ';';
    $rs  = $conn->Execute($sql);
    if (! $rs->EOF) {
        $chinese_new_year = [
            'month' => $rs->fields['cMonth'],
        ];
    }
    ##

    $date_start = ($yr + 1911) . '-01-01'; //今年起始
    $date_end   = ($yr + 1911) . '-12-31'; //今年結束

    $summary1 = [
        '01' => [],
        '02' => [],
        '03' => [],
        '04' => [],
        '05' => [],
        '06' => [],
        '07' => [],
        '08' => [],
        '09' => [],
        '10' => [],
        '11' => [],
        '12' => [],
    ];
    $season1 = [
        '1' => [],
        '2' => [],
        '3' => [],
        '4' => [],
    ];

                              //
    if (is_numeric($sales)) { //指定業務
        if (in_array($sales, [38, 72])) {
            $sql = "
            SELECT * ,
            (SELECT SUM(CAST(sCaseTwQuantityTaichung AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseTwQuantityTaichung,
            (SELECT SUM(CAST(sCaseTwQuantityNantou AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseTwQuantityNantou,
            (SELECT SUM(CAST(sCaseTwQuantityChanghua AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseTwQuantityChanghua,
            (SELECT SUM(CAST(sCaseOtherQuantityTaichung AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseOtherQuantityTaichung,
            (SELECT SUM(CAST(sCaseOtherQuantityNantou AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseOtherQuantityNantou,
            (SELECT SUM(CAST(sCaseOtherQuantityChanghua AS DECIMAL(18, 2))) FROM tSalesReport_2023 AS a WHERE sSales in (38, 72) AND a.sDate = b.sDate) AS sCaseOtherQuantityChanghua
            FROM tSalesReport_2023  AS b
            WHERE sDate >= '" . $date_start . "'
              AND sDate <= '" . $date_end . "'
              AND sSales ='" . $sales . "'
            ORDER BY sDate ASC";
        } else {
            $sql = "SELECT * FROM tSalesReport_2023 WHERE sDate >= '" . $date_start . "' AND sDate <= '" . $date_end . "'  AND sSales ='" . $sales . "' ORDER BY sDate ASC";
        }

    } else { //全業務加總
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
                    tSalesReport_2023 WHERE sDate >= '" . $date_start . "' AND sDate <= '" . $date_end . "' GROUP BY sDate ORDER BY sDate ASC";
    }
    $rs = $conn->Execute($sql);

    $CheckMonth = (int) date('m');
    while (! $rs->EOF) {
        $i = substr($rs->fields['sDate'], 5, 2);

        /****** 月簽約數(達成率) ******/
        $summary1[$i]['targetcount'] = $rs->fields['sSignQuantity']; //簽約數

        $quota_case = getRatioCaseQuota(getSalesCaseRatio($sales, ($yr + 1911) . '-' . getCoveryRationMonth($i) . '-01')); //取得當月業務涵蓋率對應的案件數

        if ($i == $chinese_new_year['month']) { //當年度農曆年月份
            $quota_case = floor($quota_case * 0.6); //遇到農曆新年月份時，案件額度打六折、小數無條件捨去
        }

        $summary1[$i]['target'] = getOwnStoreTarget($summary1[$i]['targetcount'], $yr, $i, $sales, $quota_case); //達成率

                                                                      //月進件量(成長率)
        if (is_numeric($sales) && in_array($sales, [90, 118, 123])) { //偉哲、俊智、和毅改用 sTaipeiQuantityThisYear(JSON)取代
            $json_summary = json_decode($rs->fields['sTaipeiQuantityThisYear'], true);

            $summary1[$i]['twcount']    = $json_summary['sCaseTwQuantity'];   //台屋
            $summary1[$i]['othercount'] = $json_summary['sCaseUnTwQuantity']; // 他牌+非仲介
            $summary1[$i]['groupcount'] = $summary1[$i]['twcount'] + $summary1[$i]['othercount'];

            $totalData['twcount'] += $summary1[$i]['twcount'];
            $totalData['othercount'] += $summary1[$i]['othercount'];

                                                                                          //保證費
            $summary1[$i]['certifiedMoneyTw']    = $json_summary['sCertifiedMoneyTw'];    //台屋
            $summary1[$i]['certifiedMoneyOther'] = $json_summary['sCertifiedMoneyOther']; // 他牌+非仲介
            $totalData['certifiedMoneyTw'] += $summary1[$i]['certifiedMoneyTw'];
            $totalData['certifiedMoneyOther'] += $summary1[$i]['certifiedMoneyOther'];

                                                                                                //回饋
            $summary1[$i]['caseFeedBackMoneyTw']    = $json_summary['sCaseFeedBackMoneyTw'];    //台屋
            $summary1[$i]['caseFeedBackMoneyOther'] = $json_summary['sCaseFeedBackMoneyOther']; // 他牌+非仲介
            $totalData['caseFeedBackMoneyTw'] += $summary1[$i]['caseFeedBackMoneyTw'];
            $totalData['caseFeedBackMoneyOther'] += $summary1[$i]['caseFeedBackMoneyOther'];

            //淨收
            $summary1[$i]['caseIncomeTw']    = ($summary1[$i]['certifiedMoneyTw'] - $summary1[$i]['caseFeedBackMoneyTw']);
            $summary1[$i]['caseIncomeOther'] = ($summary1[$i]['certifiedMoneyOther'] - $summary1[$i]['caseFeedBackMoneyOther']);

            $totalData['caseIncomeTw'] += $summary1[$i]['caseIncomeTw'];
            $totalData['caseIncomeOther'] += $summary1[$i]['caseIncomeOther'];
        } else {
            $summary1[$i]['twcount']    = $rs->fields['sCaseTwQuantity'];                         //台屋
            $summary1[$i]['othercount'] = $rs->fields['sCaseUnTwQuantity'];                       // 他牌+非仲介
            $summary1[$i]['groupcount'] = $summary1[$i]['twcount'] + $summary1[$i]['othercount']; //20250116 廷蔚也算台屋

            $totalData['twcount'] += $summary1[$i]['twcount'];
            $totalData['othercount'] += $summary1[$i]['othercount'];

                                                                                        //保證費
            $summary1[$i]['certifiedMoneyTw']    = $rs->fields['sCertifiedMoneyTw'];    //台屋
            $summary1[$i]['certifiedMoneyOther'] = $rs->fields['sCertifiedMoneyOther']; // 他牌+非仲介
            $totalData['certifiedMoneyTw'] += $summary1[$i]['certifiedMoneyTw'];
            $totalData['certifiedMoneyOther'] += $summary1[$i]['certifiedMoneyOther'];

                                                                                              //回饋
            $summary1[$i]['caseFeedBackMoneyTw']    = $rs->fields['sCaseFeedBackMoneyTw'];    //台屋
            $summary1[$i]['caseFeedBackMoneyOther'] = $rs->fields['sCaseFeedBackMoneyOther']; // 他牌+非仲介
            $totalData['caseFeedBackMoneyTw'] += $summary1[$i]['caseFeedBackMoneyTw'];
            $totalData['caseFeedBackMoneyOther'] += $summary1[$i]['caseFeedBackMoneyOther'];

            //淨收
            $summary1[$i]['caseIncomeTw']    = ($summary1[$i]['certifiedMoneyTw'] - $summary1[$i]['caseFeedBackMoneyTw']);
            $summary1[$i]['caseIncomeOther'] = ($summary1[$i]['certifiedMoneyOther'] - $summary1[$i]['caseFeedBackMoneyOther']);

            $totalData['caseIncomeTw'] += $summary1[$i]['caseIncomeTw'];
            $totalData['caseIncomeOther'] += $summary1[$i]['caseIncomeOther'];

                                                                                       //中部區域顯示
            $summary1[$i]['twcountTaichung'] = $rs->fields['sCaseTwQuantityTaichung']; //台屋台中
            $summary1[$i]['twcountNantou']   = $rs->fields['sCaseTwQuantityNantou'];   //台屋南投
            $summary1[$i]['twcountChanghua'] = $rs->fields['sCaseTwQuantityChanghua']; //台屋彰化

            $summary1[$i]['othercountTaichung'] = $rs->fields['sCaseOtherQuantityTaichung']; //台屋台中
            $summary1[$i]['othercountNantou']   = $rs->fields['sCaseOtherQuantityNantou'];   //台屋南投
            $summary1[$i]['othercountChanghua'] = $rs->fields['sCaseOtherQuantityChanghua']; //台屋彰化

            $summary1[$i]['twcount38']    = $summary1[$i]['twcountTaichung'] + $summary1[$i]['twcountNantou'] + $summary1[$i]['twcountChanghua'];          //台屋 中部
            $summary1[$i]['othercount38'] = $summary1[$i]['othercountTaichung'] + $summary1[$i]['othercountNantou'] + $summary1[$i]['othercountChanghua']; //台屋 中部
        }

        //檢查計算區間是否滿一年(業務)
        $tmpD  = ($yr + 1911) . "-" . str_pad($i, 2, '0', STR_PAD_LEFT) . "-31";
        $check = ($pOnBoard > $tmpD) ? false : true;
        $check = (is_numeric($sales) && in_array($sales, [90, 118, 123])) ? true : $check; //2024-0628 偉哲、俊智、和毅的計算不用滿一年

        /****** 月進案件數成長率 ******/
        if (in_array($sales, [57, 68, 97])) {                                                                                                                         //永鑫(58)、廷蔚(38)、孟璋 他們台屋跟非台合併
            $summary1[$i]['groupAllshow'] = getPercentMonth107($sales, ($yr + 1911), $i, $summary2[$i]['othercount'], $summary1[$i]['othercount'], 'g', $check, 'show');  //成長率
            $summary1[$i]['groupAll']     = getPercentMonth107($sales, ($yr + 1911), $i, $summary2[$i]['othercount'], $summary1[$i]['othercount'], 'g', $check);          //成長率
        } else {                                                                                                                                                      //其他業務
            if ($check) {                                                                                                                                                 //滿一年
                $summary1[$i]['groupTWshow']   = getPercentMonth107($sales, ($yr + 1911), $i, $summary2[$i]['twcount'], $summary1[$i]['twcount'], 'g', $check, 'show');       //成長率
                $summary1[$i]['groupUnTWshow'] = getPercentMonth107($sales, ($yr + 1911), $i, $summary2[$i]['othercount'], $summary1[$i]['othercount'], 'g', $check, 'show'); //成長率
                $summary1[$i]['groupTW']       = getPercentMonth107($sales, ($yr + 1911), $i, $summary2[$i]['twcount'], $summary1[$i]['twcount'], 'g', $check);               //成長率
                $summary1[$i]['groupUnTW']     = getPercentMonth107($sales, ($yr + 1911), $i, $summary2[$i]['othercount'], $summary1[$i]['othercount'], 'g', $check);         //成長率

                //中部(立寰等)
                $summary1[$i]['groupTWshow38']   = getPercentMonth107($sales, ($yr + 1911), $i, $summary2[$i]['twcount38'], $summary1[$i]['twcount38'], 'g', $check, 'show');
                $summary1[$i]['groupUnTWshow38'] = getPercentMonth107($sales, ($yr + 1911), $i, $summary2[$i]['othercount38'], $summary1[$i]['othercount38'], 'g', $check, 'show');
                $summary1[$i]['groupTW38']       = getPercentMonth107($sales, ($yr + 1911), $i, $summary2[$i]['twcount38'], $summary1[$i]['twcount38'], 'g', $check);                   //成長率
                $summary1[$i]['groupUnTW38']     = getPercentMonth107($sales, ($yr + 1911), $i, $summary2[$i]['othercount38'], $summary1[$i]['othercount38'], 'g', $check);             //成長率
            } else {                                                                                                                                                                //未滿一年(跟上個月相比)
                $summary1[$i]['groupTWshow']   = getPercentMonth107($sales, ($yr + 1911), $i, $summary1[($i - 1)]['twcount'], $summary1[$i]['twcount'], 'gTw', $check, 'show');         //成長率
                $summary1[$i]['groupUnTWshow'] = getPercentMonth107($sales, ($yr + 1911), $i, $summary1[($i - 1)]['othercount'], $summary1[$i]['othercount'], 'gUnTw', $check, 'show'); //成長率
                $summary1[$i]['groupTW']       = getPercentMonth107($sales, ($yr + 1911), $i, $summary1[($i - 1)]['twcount'], $summary1[$i]['twcount'], 'gTw', $check);                 //成長率
                $summary1[$i]['groupUnTW']     = getPercentMonth107($sales, ($yr + 1911), $i, $summary1[($i - 1)]['othercount'], $summary1[$i]['othercount'], 'gUnTw', $check);         //成長率
            }
        }

        if ($i == $mn) { //店家/地政士明細(當月份資訊)
            $date_start = ($yr + 1911) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01 00:00:00';
            $date_end   = ($yr + 1911) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-31 23:59:59';

            $Branch    = getOwnBranch($sales, $date_start, $date_end);    //該月新進仲介店數
            $Scrivener = getOwnScrivener($sales, $date_start, $date_end); //該月新進地政士數

            $BranchCount    = count($Branch);
            $ScrivenerCount = count($Scrivener);

            $groupTW   = $summary1[$i]['groupTW'];   //查詢月成長率
            $groupUnTW = $summary1[$i]['groupUnTW']; //查詢月成長率
            $group     = $summary1[$i]['groupAll'];

            $groupTW38   = $summary1[$i]['groupTW38'];
            $groupUnTW38 = $summary1[$i]['groupUnTW38']; //查詢月成長率

            $tmp_cut2 = getUnApplyLine($sales, $Scrivener); //1/4有簽約地政士有加LINE才算
            $tmp_cut  = getSameStore($sales, $Branch, $Scrivener, $tmp_cut2['scrivener']);

            $summary1[$i]['targetcount'] = $BranchCount + $ScrivenerCount - $tmp_cut - $tmp_cut2['score'];
            unset($tmp_cut);

            $target                = $summary1[$i]['target']; //查詢月達成率
            $summary1[$i]['class'] = "show";

            //組長的成員簽約店家(只能看共區)
            $salesGroupList     = [];
            $salesGroupListShow = 0; //不可以看
            $sql                = "SELECT sMember,sCity FROM tSalesGroup WHERE sManager = '" . $sales . "' AND sSalesReport = 1";
            $rs2                = $conn->Execute($sql);
            while (! $rs2->EOF) {
                $salesGroupListShow = 0;
                $expArr             = explode(',', $rs2->fields['sMember']);

                foreach ($expArr as $key => $value) {
                    //簽約店
                    $sql                            = "SELECT pName FROM  tPeopleInfo WHERE pId = '" . $value . "'";
                    $rs3                            = $conn->Execute($sql);
                    $salesGroupList[$value]['name'] = $rs3->fields['pName'];
                    if (! is_array($salesGroupList[$value]['branch'])) {
                        $salesGroupList[$value]['branch'] = [];
                    }

                    if (! is_array($salesGroupList[$value]['scrivener'])) {
                        $salesGroupList[$value]['scrivener'] = [];
                    }

                    if (getOwnBranch($value, $date_start, $date_end, '', '', $rs2->fields['sCity'])) {
                        $salesGroupList[$value]['branch'] = array_merge($salesGroupList[$value]['branch'], getOwnBranch($value, $date_start, $date_end, '', '', $rs2->fields['sCity']));
                    }

                    if (getOwnScrivener($value, $date_start, $date_end, '', '', $rs2->fields['sCity'])) {
                        $salesGroupList[$value]['scrivener'] = array_merge($salesGroupList[$value]['scrivener'], getOwnScrivener($value, $date_start, $date_end, '', '', $rs2->fields['sCity']));
                    }

                    //行程
                    if (! is_array($salesGroupList[$value]['calendar'])) {
                        $salesGroupList[$value]['calendar'] = [];
                    }

                    if (getCalendar($value, $yr, $mn, $rs2->fields['sCity'])) {
                        $salesGroupList[$value]['calendar'] = array_merge($salesGroupList[$value]['calendar'], getCalendar($value, $yr, $mn, $rs2->fields['sCity']));
                    }
                }

                unset($expArr);
                $rs2->MoveNext();
            }

            foreach ($salesGroupList as $k => $v) {
                $sortArray = [];

                foreach ($salesGroupList[$k]['calendar'] as $key => $value) {
                    $sortArray[$value['date']] = $value;
                }

                $tmp_cut2 = getUnApplyLine($sales, $v['scrivener']); //1/4有簽約地政士有加LINE才算
                $tmp_cut  = getSameStore($sales, $v['branch'], $v['scrivener'], $tmp_cut2['scrivener']);

                $salesGroupList[$k]['targetcount'] = count($v['branch']) + count($v['scrivener']) - $tmp_cut - $tmp_cut2['score'];
                ksort($sortArray);

                $salesGroupList[$k]['calendar'] = [];
                $salesGroupList[$k]['calendar'] = array_merge($salesGroupList[$k]['calendar'], $sortArray);

                unset($tmp_cut2, $tmp_cut, $sortArray, $d);
            }
        }

        //季
        $sess = 0;

        //使用量有排除的問題，所以單獨拉出來算

        if ($i <= 3) { //第一季
            $sess = 1;
        } else if ($i > 3 && $i <= 6) { //第二季
            $sess = 2;
        } else if ($i > 6 && $i <= 9) { //第三季
            $sess = 3;
        } else if ($i > 9 && $i <= 12) { //第四季
            $sess = 4;
        }

                                                                        //簽約數(達成率)
        $season1[$sess]['targetcount'] += $summary1[$i]['targetcount']; //簽約數

                                                                      //進件量(成長率)
        $season1[$sess]['twcount'] += $summary1[$i]['twcount'];       //進件量(台屋)
        $season1[$sess]['othercount'] += $summary1[$i]['othercount']; //進件量(非台屋)
        $season1[$sess]['groupcount'] += $summary1[$i]['groupcount']; //所有案件
                                                                      ##

                                                                          //中部
                                                                          //進件量(成長率)
        $season1[$sess]['twcount38'] += $summary1[$i]['twcount38'];       //進件量(台屋)
        $season1[$sess]['othercount38'] += $summary1[$i]['othercount38']; //進件量(非台屋)

        $rs->MoveNext();
    }
    unset($sess, $CheckMonth);
    ##

    //算出季的平均數字
    for ($i = 1; $i <= 4; $i++) {
        $_date = ($yr + 1911) . '-' . str_pad(($i * 3 - 2), 2, 0, STR_PAD_LEFT) . '-01';
        if ($i == 1) {                                                                     //第一季(農曆新年通常在第一季的月分中)
            $_targetQuota = floor(getRatioCaseQuota(getSalesCaseRatio($sales, $_date)) * 0.6); //農曆新年月份，案件額度打六折、小數無條件捨去
            $_targetQuota += getRatioCaseQuota(getSalesCaseRatio($sales, $_date)) * 2;         //其他兩個月份
        } else {
            $_targetQuota = getRatioCaseQuota(getSalesCaseRatio($sales, $_date)) * 3;
        }
        $season1[$i]['target'] = round(($season1[$i]['targetcount'] / $_targetQuota) * 100); //達成率

        $_date = $_targetQuota = null;
        unset($_date, $_targetQuota);

        //檢查計算區間是否任職滿一年
        if ($i == 1) {
            $tmpD = ($yr + 1911) . "-03-01";
        } elseif ($i == 2) {
            $tmpD = ($yr + 1911) . "-06-01";
        } elseif ($i == 3) {
            $tmpD = ($yr + 1911) . "-09-01";
        } elseif ($i == 4) {
            $tmpD = ($yr + 1911) . "-12-01";
        }

        $check = ($pOnBoard > $tmpD) ? false : true;
        $check = (is_numeric($sales) && in_array($sales, [90, 118, 123])) ? true : $check; //2024-0628 偉哲、俊智、和毅的計算不用滿一年

        if (in_array($sales, [57, 97, 68])) { //永鑫 孟璋 廷蔚 他們台屋跟非台合併
            if ($check) {                         //滿一年(比較去年同期季)
                $season1[$i]['groupAllshow'] = getPercent107($sales, ($yr + 1911), $i, $seasonLast[$i]['groupcount'], $season1[$i]['groupcount'], 'g', $check, 'show');
                $season1[$i]['groupAll']     = getPercent107($sales, ($yr + 1911), $i, $seasonLast[$i]['groupcount'], $season1[$i]['groupcount'], 'g', $check);
            } else {
                $season1[$i]['groupAllshow'] = getPercent107($sales, ($yr + 1911), $i, $season1[($i - 1)]['groupcount'], $season1[$i]['groupcount'], 'g', $check, 'show');
                $season1[$i]['groupAll']     = getPercent107($sales, ($yr + 1911), $i, $season1[($i - 1)]['groupcount'], $season1[$i]['groupcount'], 'g', $check);
            }
        } else {
            if ($check) { //滿一年(比較去年同期季)
                $season1[$i]['groupTW']   = getPercent107($sales, ($yr + 1911), $i, $seasonLast[$i]['twcount'], $season1[$i]['twcount'], 'gTw', $check);
                $season1[$i]['groupUnTW'] = getPercent107($sales, ($yr + 1911), $i, $seasonLast[$i]['othercount'], $season1[$i]['othercount'], 'gUnTw', $check);

                $season1[$i]['groupTWshow']   = getPercent107($sales, ($yr + 1911), $i, $seasonLast[$i]['twcount'], $season1[$i]['twcount'], 'gTw', $check, 'show');
                $season1[$i]['groupUnTWshow'] = getPercent107($sales, ($yr + 1911), $i, $seasonLast[$i]['othercount'], $season1[$i]['othercount'], 'gUnTw', $check, 'show');

                //中部
                $season1[$i]['groupTW38']   = getPercent107($sales, ($yr + 1911), $i, $seasonLast[$i]['twcount38'], $season1[$i]['twcount38'], 'gTw', $check);
                $season1[$i]['groupUnTW38'] = getPercent107($sales, ($yr + 1911), $i, $seasonLast[$i]['othercount38'], $season1[$i]['othercount38'], 'gUnTw', $check);

                $season1[$i]['groupTWshow38']   = getPercent107($sales, ($yr + 1911), $i, $seasonLast[$i]['twcount38'], $season1[$i]['twcount38'], 'gTw', $check, 'show');
                $season1[$i]['groupUnTWshow38'] = getPercent107($sales, ($yr + 1911), $i, $seasonLast[$i]['othercount38'], $season1[$i]['othercount38'], 'gUnTw', $check, 'show');
            } else {
                $season1[$i]['groupTW']   = getPercent107($sales, ($yr + 1911), $i, $season1[($i - 1)]['twcount'], $season1[$i]['twcount'], 'gTw', $check);
                $season1[$i]['groupUnTW'] = getPercent107($sales, ($yr + 1911), $i, $season1[($i - 1)]['othercount'], $season1[$i]['othercount'], 'gUnTw', $check);

                $season1[$i]['groupTWshow']   = getPercent107($sales, ($yr + 1911), $i, $season1[($i - 1)]['twcount'], $season1[$i]['twcount'], 'gTw', $check, 'show');
                $season1[$i]['groupUnTWshow'] = getPercent107($sales, ($yr + 1911), $i, $season1[($i - 1)]['othercount'], $season1[$i]['othercount'], 'gUnTw', $check, 'show');
            }
        }
    }
    unset($tmpD);
    ##

                    //本季考核
    if ($mn <= 3) { //第一季
        $sess = 1;
    } elseif ($mn > 3 && $mn <= 6) { //第二季
        $sess = 2;
    } elseif ($mn > 6 && $mn <= 9) { //第三季
        $sess = 3;
    } elseif ($mn > 9 && $mn <= 12) { //第四季
        $sess = 4;
    }

    $season1[$sess]['class'] = "show";

    $showseason['targetcount'] = $season1[$sess]['targetcount'];
    $showseason['target']      = $season1[$sess]['target'];
    $showseason['groupTW']     = $season1[$sess]['groupTW'];
    $showseason['groupUnTW']   = $season1[$sess]['groupUnTW'];
    $showseason['groupAll']    = $season1[$sess]['groupAll'];
    ##

    //判斷季別
    $tmp[0] = $yr + 1911;
    $tmp[1] = $mn;
    if ($tmp[1] <= 3) { //第一季
                            //本季
        $sDate = $tmp[0] . '-01-01 00:00:00';
        $eDate = $tmp[0] . '-03-31 23:59:59';
        ##

        //本季分母用
        $eDateDiv = ($tmp[0] - 1) . '-12-31 23:59:59';
                                                       ##
    } else if (($tmp[1] >= 4) && ($tmp[1] <= 6)) { //第二季
                                                       //本季
        $sDate = $tmp[0] . '-04-01 00:00:00';
        $eDate = $tmp[0] . '-06-30 23:59:59';
        ##

        //本季分母用
        $eDateDiv = $tmp[0] . '-03-31 23:59:59';
                                                       ##
    } else if (($tmp[1] >= 7) && ($tmp[1] <= 9)) { //第三季
                                                       //本季
        $sDate = $tmp[0] . '-07-01 00:00:00';
        $eDate = $tmp[0] . '-09-30 23:59:59';
        ##

        //本季分母用
        $eDateDiv = $tmp[0] . '-06-30 23:59:59';
                 ##
    } else { //第四季
                 //本季
        $sDate = $tmp[0] . '-10-01 00:00:00';
        $eDate = $tmp[0] . '-12-31 23:59:59';
        ##

        //本季分母用
        $eDateDiv = $tmp[0] . '-09-30 23:59:59';
        ##
    }
    ##

    /****** 有效使用率 ******/
    $eff1 = [];

    $eff1['range_start2'] = DateChange($sDate);
    $eff1['range_end2']   = DateChange($eDate);

    $eff1['range_start'] = '000年00月00日';
    $eff1['range_end']   = DateChange($eDateDiv);

    $sql = 'SELECT * FROM tSalesReportStore WHERE sSales = "' . $sales . '" AND sDate >= "' . $sDate . '" AND sDate <= "' . $eDate . '";';
    $rs  = $conn->Execute($sql);

    //分子 (從以前到前一季的店家)有進案的店家
    $A          = $rs->fields['sScrivener'] + $rs->fields['sRealty'];
    $eff1['no'] = $A;

    //分母 (從以前到前一季的店家)
    $B             = $rs->fields['sScrTotal'] + $rs->fields['sRealTotal'];
    $eff1['total'] = $B;

    $eff1['effective'] = ($B > 0) ? round(($A / $B), 2) * 100 : 0;

    $json = json_decode($rs->fields['sStore'], true);

    $eff1['data']['scrcase']   = getScrivener107($json['sc_yes']);
    $eff1['data']['scrnocase'] = getScrivener107($json['sc_no']);

    $eff1['data']['branchcase']   = getBranch107($json['br_yes']);
    $eff1['data']['branchnocase'] = getBranch107($json['br_no']);

    /**
     * 114 年度指標比率
     * 2024-12-24
     */
    $percentTarget = $percent['pSign'] / 100; //通路新簽約數 2023-12-29(15%)

    $percentGroupTw   = $percent['pGroupTW'] / 100;          //進案件成長率(台屋) 2023-12-29(50%)
    $percentGroupUnTw = $percent['pGroupUnTW'] / 100;        //進案件成長率(非台屋) 2023-12-29(15%)
    $percentGroupALL  = $percentGroupTw + $percentGroupUnTw; //進案件成長率(全) 台屋 + 非台屋

    $percentUse = $percent['pPercentUse'] / 100; //有效使用率 2023-12-29(20%)

    /**
     * 有效率基準數據 2023-12-29(45% ~ 35%)
     *
     * 區域涵蓋率為 50% 之同仁所得之比例數字以 45% 為基準
     * 區域涵蓋率為 50% ~ 40% 間之同仁所得之比例數字以 40% 為基準
     * 區域涵蓋率為 40% 以下之同仁所得之比例數字以 35% 為基準
     */
    $EffectiveGoal = 40; //有效率基準趴數 2023-12-29(45% ~ 35%)
    if (! empty($sales)) {
        $_date  = ($yr + 1911) . '-' . str_pad(($sess * 3 - 2), 2, 0, STR_PAD_LEFT) . '-01';
        $_ratio = getSalesCaseRatio($sales, $_date);

        $EffectiveGoal = $percent['pEffectiveGoal1']; //涵蓋率 40% 以下
        if ($_ratio >= 50) {                          //涵蓋率 50% 以上
            $EffectiveGoal = $percent['pEffectiveGoal3'];
        } else if ($_ratio < 50 && $_ratio >= 40) { //涵蓋率 50% ~ 40% 之間
            $EffectiveGoal = $percent['pEffectiveGoal2'];
        }

        $_date = $_ratio = null;
        unset($_date, $_ratio);
    }

    $EffectiveBaseScore = $percent['pEffectiveBaseScore']; //基準趴數的基準分 2023-12-29(20分)

    $EffectivePlus  = $percent['pEffectivePlus'];  //有效率變動分數(基準趴數增減多少?趴) 2023-12-29(1%)
    $EffectivePlus2 = $percent['pEffectivePlus2']; //有效率變動分數(基準趴數增減多少$EffectivePlus趴就扣?分) 2023-12-29(1分)

                                                                  //統計分數
    $seasontarget = ($season1[$sess]['target'] * $percentTarget); //達成分數

    $seasongroupTW   = $season1[$sess]['groupTW'] * $percentGroupTw;     //成長分數(台屋)
    $seasongroupUnTW = $season1[$sess]['groupUnTW'] * $percentGroupUnTw; //成長分數(非台屋)
    $seasongroupALL  = $season1[$sess]['groupAll'] * $percentGroupALL;   //成長分數(全部)

    $eff1['score'] = 0; //扣到沒有分數就是0
    if ($eff1['effective'] > ($EffectiveGoal - $EffectiveBaseScore)) {
        $tmpScore = $eff1['effective'] - $EffectiveGoal; //相減後取得差數
        $tmpScore = (($tmpScore * $EffectivePlus2) / $EffectivePlus);

        $eff1['score'] = $EffectiveBaseScore + $tmpScore; //
        $tmpScore      = null;unset($tmpScore);
    }

    /****** 業績一覽表 ******/
    if ($sales == 57 || $sales == 68 || $sales == 97) { //永鑫 廷蔚 孟璋
        $grade = $seasontarget + $seasongroupALL + $eff1['score'];
    } else {
        $grade = $seasontarget + $seasongroupTW + $seasongroupUnTW + $eff1['score'];
    }

    $gradecolor  = "#000088";
    $gradeNotice = ''; //未達標提醒文字

    //當季簽約案件額度計算
    $targetQuota = 0;

    $_date = ($yr + 1911) . '-' . str_pad(($sess * 3 - 2), 2, 0, STR_PAD_LEFT) . '-01';
    if ($sess == 1) {                                                                 //第一季(農曆新年通常在第一季的月分中)
        $targetQuota = floor(getRatioCaseQuota(getSalesCaseRatio($sales, $_date)) * 0.6); //農曆新年月份，案件額度打六折、小數無條件捨去
        $targetQuota += getRatioCaseQuota(getSalesCaseRatio($sales, $_date)) * 2;         //其他兩個月份

        // if ($sales == 34) {
        //     $targetQuota = floor(7 * 0.6) + (7 * 2); //農曆新年月份，案件額度打六折、小數無條件捨去 + 其他兩個月份
        // }

        if ($sales == 57) {
            $targetQuota = floor(5 * 0.6) + (5 * 2); //農曆新年月份，案件額度打六折、小數無條件捨去 + 其他兩個月份
        }
    } else {
                                                                                                         // $targetQuota = ($sales == 34) ? 21 : (getRatioCaseQuota(getSalesCaseRatio($sales, $_date)) * 3); //第二季以後(政棋)
        $targetQuota = ($sales == 57) ? 15 : (getRatioCaseQuota(getSalesCaseRatio($sales, $_date)) * 3); //第二季以後(永鑫)
    }

    $targetQuota -= $showseason['targetcount'];
    ##

    $gradeNotice = '';
    if ($targetQuota > 0) {
        $gradeNotice .= '[本季涵蓋率為：' . getSalesCaseRatio($sales, $_date) . '%]<br>';
        $gradeNotice .= '[本季尚缺簽約數：' . $targetQuota . ']';
    }

    if ($showseason['target'] >= 80) {
        $gradeNotice .= empty($gradeNotice) ? '' : '<br>';
        $gradeNotice .= '<span style="font-weight:bold;color:green;font-size:14pt;">[本季開發總組數比率：' . $showseason['target'] . '%] </span>';
    } else if (($showseason['target'] < 80) && ($showseason['target'] >= 70)) {
        $gradeNotice .= empty($gradeNotice) ? '' : '<br>';
        $gradeNotice .= '<span style="font-weight:bold;color:#FFA54E;font-size:14pt;">[本季開發總組數比率：' . $showseason['target'] . '%] </span>';
    } else if ($showseason['target'] < 70) {
        $gradeNotice .= empty($gradeNotice) ? '' : '<br>';
        $gradeNotice .= ' [本季開發總組數比率：' . $showseason['target'] . '%]';
    }

    $gradecolor = empty($gradeNotice) ? '' : '#FF0000';

    $_date = $targetQuota = null;
    unset($_date, $targetQuota);

    //20221123 B級通路進案件數加成計算
    $sales_weight = [
        'add'   => 0,
        'minus' => 0,
        'bId'   => [],
    ];

    if (is_numeric($sales)) {
        $sql = 'SELECT sDate, sBid, sTwAdding, sTwMinus, sUnTwAdding, sUnTwMinus, s505Adding, s505Minus FROM tSalesReportWeighted WHERE sSales = "' . $sales . '" AND sDate >= "' . substr($sDate, 0, 10) . '" AND sDate <= "' . substr($eDate, 0, 10) . '";';
    } else {
        $sql = 'SELECT sDate, sBid, sTwAdding, sTwMinus, sUnTwAdding, sUnTwMinus, s505Adding, s505Minus FROM tSalesReportWeighted WHERE sDate >= "' . substr($sDate, 0, 10) . '" AND sDate <= "' . substr($eDate, 0, 10) . '";';
    }
    $rs = $conn->Execute($sql);

    while (! $rs->EOF) {
        $sales_weight['add'] += $rs->fields['sTwAdding'] + $rs->fields['sUnTwAdding'] + $rs->fields['s505Adding'];
        $sales_weight['minus'] += $rs->fields['sTwMinus'] + $rs->fields['sUnTwMinus'] + $rs->fields['s505Minus'];
        $sales_weight['bId'][substr($rs->fields['sDate'], 0, 7)] = $rs->fields['sBid'];

        $rs->MoveNext();
    }

    $grade += $sales_weight['add'] - $sales_weight['minus'];
    ##

    $weightedType = [
        'TwAdding'   => '加分',
        'TwMinus'    => '扣分',
        'unTwAdding' => '加分',
        'unTwMinus'  => '扣分',
        'm505Adding' => '加分',
        'm505Minus'  => '扣分',
    ];
    foreach ($sales_weight['bId'] as $key => $bIdJason) {
        $bIds = json_decode($bIdJason);

        $sales_weight['bId'][$key] = [];
        foreach ($bIds as $type => $bId) {

            $para      = implode(',', $bId);
            $storeName = convertToStoreName($para, $bId);
            $storeName = str_replace(",", "<br/>", $storeName);
            $sales_weight['bId'][$key][$weightedType[$type]] .= $storeName;
        }
    }

    //20221208 課程推廣加分
    $sales_weight['promo'] = 0;

    if (is_numeric($sales)) {
        $sql = 'SELECT COUNT(*) as points FROM tSalesReportPromo WHERE sSales = "' . $sales . '" AND sDate >= "' . substr($sDate, 0, 10) . '" AND sDate <= "' . substr($eDate, 0, 10) . '" AND sConfirmed = "Y";';
    } else {
        $sql = 'SELECT COUNT(*) as points FROM tSalesReportPromo WHERE sDate >= "' . substr($sDate, 0, 10) . '" AND sDate <= "' . substr($eDate, 0, 10) . '" AND sConfirmed = "Y";';
    }
    $rs = $conn->Execute($sql);

    while (! $rs->EOF) {
        $sales_weight['promo'] += $rs->fields['points'];
        $rs->MoveNext();
    }

                                                                                          // $sales_weight['promo'] = ($sales_weight['promo'] > 10) ? 10 : $sales_weight['promo']; //分數上限：10分
    $sales_weight['promo'] = ($sales_weight['promo'] > 20) ? 20 : $sales_weight['promo']; //分數上限：20分 20241217 副總指示上限分數調高至20分

    $grade += $sales_weight['promo'];
    ##

    $grade = round($grade, 2);

    //20250107 行程記錄加權分數
    $tHoliday     = [];
    $sql_tHoliday = "SELECT hFromDate FROM tHoliday WHERE hFromDate >= '" . substr($sDate, 0, 10) . "' AND hToDate <= '" . substr($eDate, 0, 10) . "' AND hMakeUpWorkday IN ('Y', 'N')";
    $rs_tHoliday  = $conn->Execute($sql_tHoliday);
    while (! $rs_tHoliday->EOF) {
        $tHoliday[] = $rs_tHoliday->fields['hFromDate'];
        $rs_tHoliday->MoveNext();
    }

    // 20250708 特定業務假日
    if(in_array($sales, [72])){ //富閔
        $tHoliday = array_merge($tHoliday, ["2025-07-01", "2025-07-02", "2025-07-03"]);
    }

    //業務行程
    $tCalendar = [];
//    $sql_tCalendar = "SELECT cStartDateTime,cEndDateTime FROM tCalendar WHERE cCreator = '".$sales."' AND cCreateDateTime >= '" . substr($sDate, 0, 10) . " 00:00:00' AND cCreateDateTime <= '" . substr($eDate, 0, 10) . " 23:59:59'";
    $sql_tCalendar = "SELECT cStartDateTime,cEndDateTime FROM tCalendar WHERE cCreator = '" . $sales . "' AND cErease = '1'
        AND ( (cStartDateTime >= '" . substr($sDate, 0, 10) . " 00:00:00' AND cStartDateTime <= '" . substr($eDate, 0, 10) . " 23:59:59')
        OR (cEndDateTime >= '" . substr($sDate, 0, 10) . " 00:00:00' AND cEndDateTime <= '" . substr($eDate, 0, 10) . " 23:59:59') )";
    if ($sDate >= '2025-04-01 00:00:00') {
        //2025第一季允許補填行程
        $sql_tCalendar .= " AND DATE(cCreateDateTime) <= DATE(cStartDateTime)";
    }
    $rs_tCalendar = $conn->Execute($sql_tCalendar);
    while (! $rs_tCalendar->EOF) {
        $tCalendar[] = [$rs_tCalendar->fields['cStartDateTime'], $rs_tCalendar->fields['cEndDateTime']];
        $rs_tCalendar->MoveNext();
    }

    //業務休假
    $leave                = [];
    $sql_tStaffLeaveApply = "SELECT sLeaveFromDateTime,sLeaveToDateTime FROM tStaffLeaveApply WHERE sApplicant = '" . $sales . "' AND sLeaveFromDateTime >= '" . substr($sDate, 0, 10) . " 00:00:00' AND sLeaveToDateTime <= '" . substr($eDate, 0, 10) . " 23:59:59'";
    $rs_tStaffLeaveApply  = $conn->Execute($sql_tStaffLeaveApply);
    while (! $rs_tStaffLeaveApply->EOF) {
        $leave[] = [$rs_tStaffLeaveApply->fields['sLeaveFromDateTime'], $rs_tStaffLeaveApply->fields['sLeaveToDateTime']];
        $rs_tStaffLeaveApply->MoveNext();
    }
    $leave = splitMultiDaySchedules($leave);
    foreach ($leave as $key => $value) {
        $tCalendar[] = [$value[0], $value[1]];
    }

    $calendar_score = getCalendarScore($tCalendar, $tHoliday, $sDate, $eDate);
    $grade += $calendar_score['score'];
    //=========行程記錄加權分數 end

    //2025-04-11 電子合約書加權分數
    $_date = ($yr + 1911) . '-' . str_pad(($sess * 3 - 2), 2, 0, STR_PAD_LEFT) . '-01';
    if ($_date >= '2025-04-01') {
        //取得時間範圍內入款的電子合約書保證號碼
        require_once __DIR__ . '/salesEcontract.php';

        $grade += $econtract['score'];
    }
    $_date = null;unset($_date);
} else {
    $script = '$("[name=\'excel\']").hide();';
}
##

//成長率((該季)/去年該季)*100%[BY季] (本季與去年同季相比)
function getPercent107($sales, $year, $s, $last, $now, $type, $check, $show = '')
{
    $val = 0;

    if ($s == 1 && ! $check) { //第一季要求出上一季
        $date_s = ($year - 1) . '-10-01';
        $date_e = ($year - 1) . '-12-31';

        //上一年的第三季
        $tmp = getLastData($sales, $date_s, $date_e, $type);

        if ($type == 'gTw') {
            $last = $tmp['tw']; //成長率
        } elseif ($type == 'gUnTw') {
            $last = $tmp['other'];
        } else if ($type == 'g') {
            $last = $tmp['all'];
        }
    }

    if ($last > 0) {
        $val = ($show) ? round((($now / $last) - 1) * 100) : round(($now / $last) * 100);
    }

    return $val;
}
##

//成長率((該月)/去年該月)*100%[BY月]
function getPercentMonth107($sales, $year, $month, $last, $now, $type, $check, $show = '')
{
    $val = 0;

    if ($month == 1 && ! $check) { //一月份且未滿一年
        $date_s = ($year - 1) . '-12-01';
        $date_e = ($year - 1) . '-12-31';

        $tmp = getLastData($sales, $date_s, $date_e, $type);

        if ($type == 'gTw') {
            $last = $tmp['tw']; //成長率
        } else if ($type == 'gUnTw') {
            $last = $tmp['other'];
        } else if ($type == 'g') {
            $last = $tmp['all'];
        }
    }

    if ($last > 0) {
        $val = ($show) ? round((($now / $last) - 1) * 100) : round((($now / $last)) * 100);
    }

    return $val;
}
##

//未滿一年撈取查詢年前一季用(限查詢年第一季使用)
function getLastData($sales, $sDate, $eDate, $type)
{
    global $conn;
    global $yr;

    $sql = "SELECT SUM(sCaseTwQuantity) AS sCaseTwQuantity,SUM(sCaseUnTwQuantity) AS sCaseUnTwQuantity FROM tSalesReport_2023 WHERE sSales = '" . $sales . "' AND sDate >= '" . $sDate . "' AND sDate <= '" . $eDate . "'";
    $rs  = $conn->Execute($sql);

    $tmp['tw']    = $rs->fields['sCaseTwQuantity'];
    $tmp['other'] = $rs->fields['sCaseUnTwQuantity'];
    $tmp['all']   = $rs->fields['sCaseTwQuantity'] + $rs->fields['sCaseUnTwQuantity'];

    if ($yr == 114 && $sales == 122) {
        $otherPass69  = getCaseUnTwQuantityBy122($sDate, $eDate);
        $tmp['other'] = $otherPass69;
    }

    return $tmp;
}

function getScrivener107($id)
{
    global $conn;

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
				s.sId IN(" . @implode(',', $id) . ")
				ORDER BY s.sId ASC";
        $rs = $conn->Execute($sql);

        while (! $rs->EOF) {
            $rs->fields['sCreat_time'] = DateChange($rs->fields['sCreat_time']);
            $tmp[]                     = $rs->fields;

            $rs->MoveNext();
        }
    }

    return $tmp;
}

function getBranch107($id)
{
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
				b.bId IN(" . @implode(',', $id) . ")
			ORDER BY b.bId ASC";

        $rs = $conn->Execute($sql);

        while (! $rs->EOF) {
            $rs->fields['bCreat_time'] = DateChange($rs->fields['bCreat_time']);
            $tmp[]                     = $rs->fields;

            $rs->MoveNext();
        }
    }

    return $tmp;
}

//確認地政士是否尚未加入Line
function getUnApplyLine($sales, $scrivener)
{
    global $conn;

    $data = ['score' => 0, 'scrivener' => []];
    if (is_array($scrivener)) {
        foreach ($scrivener as $k => $v) {
            if ($v['sSignDate2'] >= '2018-01-04') { //20180109 佩琪說1/4號簽進來的才要算LINE的部分  1/4前是以之前的算法
                $sql = "SELECT * FROM tLineAccount WHERE lStatus = 'Y' AND lTargetCode = 'SC" . str_pad($v['sId'], 4, '0', STR_PAD_LEFT) . "'";
                $rs  = $conn->Execute($sql);

                if ($rs->RecordCount() == 0 && $v['sSignCount'] != 1) {
                    $data['score']++;
                    $data['scrivener'][] = $v['sId'];
                }
            }
        }
    }

    return $data;
}
##

//因為換地區所以互換
function getOtherSalesData($sales, $sDate, $eDate)
{

    global $conn;

    if ($sales == 38) {
        $sales = 42;
    } else if ($sales == 42) {
        $sales = 38;
    } else {
        return false;
    }

    $sql = "SELECT * FROM tSalesReport_2023 WHERE sDate >= '" . $sDate . "' AND sDate <= '" . $eDate . "' AND sSales ='" . $sales . "' ORDER BY sDate ASC";

    $rs = $conn->Execute($sql);

    while (! $rs->EOF) {
        $data['twcount'] += $rs->fields['sCaseTwQuantity'];      //台屋
        $data['othercount'] += $rs->fields['sCaseUnTwQuantity']; // 他牌+非仲介(非台屋)

        $rs->MoveNext();
    }

    return $data;

}

function getCalendar($value, $yr, $mn, $city)
{
    global $conn;
    $sql = '
			SELECT
				*
			FROM
				tCalendar
			WHERE
				YEAR(cStartDateTime) = "' . ($yr + 1911) . '"
				AND MONTH(cStartDateTime) = "' . $mn . '"
				AND cCreator = "' . $value . '"
				AND cCity = "' . $city . '"
			ORDER BY
				cStartDateTime
			ASC;
		';

    $rs = $conn->Execute($sql);
    while (! $rs->EOF) {
        $sub  = $rs->fields['cSubject']; //目的：1=例行拜訪、2=開發拜訪、3=案件處理討論、4=其他
        $from = str_replace('-', '/', substr($rs->fields['cStartDateTime'], 5, 11));
        $to   = str_replace('-', '/', substr($rs->fields['cEndDateTime'], 5, 11));
        $desc = nl2br($rs->fields['cDescription']);

        $date = str_replace('-', '', substr($rs->fields['cStartDateTime'], 5, 11));
        $date = str_replace(':', '', $date);
        $date = str_replace(' ', '', $date);

        if ($sub == 1) {
            $sub = '例行拜訪';
        } else if ($sub == 2) {
            $sub = '開發拜訪';
        } else if ($sub == 3) {
            $sub = '案件處理討論';
        } else {
            $sub = '其他';
        }

        if ($rs->fields['cClass'] == 1) { //拜訪店家
            $brand   = $rs->fields['cBrand'];
            $catName = $rs->fields['cStore']; //店名

            if (($brand == 2) || empty($brand)) {
                $brand = '';
            }
            //2=非仲介成交
            else {
                $sql   = 'SELECT * FROM tBrand WHERE bId = "' . $brand . '";';
                $rel   = $conn->Execute($sql);
                $brand = $rel->fields['bName'];
                if (preg_match("/^自有品牌\(*/isu", $brand)) {
                    $brand = '自有品牌';
                }

            }

            $list[] = [
                'from'    => $from,
                'to'      => $to,
                'class'   => '拜訪店家',
                'subject' => $sub,
                'target'  => $brand . '/' . $catName,
                'city'    => $rs->fields['cCity'],
                'desc'    => $desc,
                'date'    => $date,
            ];

        } else if ($rs->fields['cClass'] == 2) { //拜訪代書
            $list[] = [
                'from'    => $from,
                'to'      => $to,
                'class'   => '拜訪代書',
                'subject' => $sub,
                'target'  => $rs->fields['cScrivener'],
                'city'    => $rs->fields['cCity'],
                'desc'    => $desc,
                'date'    => $date,
            ];

        } else { //其他
            $list[] = [
                'from'    => $from,
                'to'      => $to,
                'class'   => '其他',
                'subject' => $sub,
                'target'  => '',
                'city'    => $rs->fields['cCity'],
                'desc'    => $desc,
                'date'    => $date,
            ];

        }

        $rs->MoveNext();
    }

    return $list;
}

/**
 * 2022-11-15 adding above
 */

function getSalesCaseRatio($sales, $date)
{
    global $conn;

    //非指定業務時，回覆1%(預設10件)
    if (! is_numeric($sales)) {
        return 1;
    }

    // if ($sales == 34) { //政棋指定 20221115:耀哥確認政棋案件固定在7件
    //     return 49; //7件 => 49%
    // }

    if ($sales == 57) { //永鑫指定 20240108:耀哥確認永鑫案件固定在5件
        return 50;          //5件 => 50%
    }

    $sql = 'SELECT cFirst1, cMoi FROM tCoveryRatioBySales WHERE cIdentity = "R" AND cDate = "' . $date . '" AND cSales = ' . $sales . '
            UNION ALL
            SELECT cFirst1, cMoi FROM tCoveryRatioBySales WHERE cIdentity = "S" AND cDate = "' . $date . '" AND cSales = ' . $sales . ';';
    $rs = $conn->Execute($sql);

    $first1 = 0;
    $moi    = 0;
    while (! $rs->EOF) {
        $first1 += $rs->fields['cFirst1'];
        $moi += $rs->fields['cMoi'];

        $rs->MoveNext();
    }

    return round(($first1 / $moi * 100), 2);
}

function getCoveryRationMonth($month)
{
    $month = (int) $month;

    if ($month >= 1 && $month <= 3) {
        return '01';
    }

    if ($month >= 4 && $month <= 6) {
        return '04';
    }

    if ($month >= 7 && $month <= 9) {
        return '07';
    }

    return '10';
}

function getRatioCaseQuota($ratio)
{
    if ($ratio >= 50) {
        return 5;
    }

    if (($ratio < 50) && ($ratio >= 40)) {
        return 7;
    }

    return 10;
}

function convertToStoreName($bIdsStr, $bIdsArr)
{
    global $conn;

    $sql = '
            SELECT GROUP_CONCAT(CONCAT(bStore, ":", cast(bId AS char))) bStoreName
            FROM tBranch
            WHERE bId IN(' . $bIdsStr . ')
            ';

    $rs = $conn->Execute($sql);

    $countScore = [];

    foreach ($bIdsArr as $id) {
        $countScore[$id] = (int) $countScore[$id] + 1;
    }

    foreach ($countScore as $k => $v) {
        $rs->fields['bStoreName'] = str_replace($k, $v . '分', $rs->fields['bStoreName']);
    }

    return $rs->fields['bStoreName'];
}

//計算行程分數
function getCalendarScore($tCalendar, $tHoliday, $sDate, $eDate)
{
    // 將假日轉換為查找表格式
    $holidayLookup = array_flip($tHoliday);

    // 將行程按日期分組
    $schedulesByDate = [];
    foreach ($tCalendar as $schedule) {
        $date = substr($schedule[0], 0, 10);

        if (! isset($schedulesByDate[$date])) {
            $schedulesByDate[$date] = [
                'morning'   => false,
                'afternoon' => false,
            ];
        }

        // 檢查行程時間是否落在早上或下午時段
        $startTime = new DateTime($schedule[0]);
        $endTime   = new DateTime($schedule[1]);
        $startHour = (int) $startTime->format('H');
        $endHour   = (int) $endTime->format('H');

        // 橫跨早上到下午
        if (($startHour >= 9 && $startHour < 12) &&
            ($endHour > 13 && $endHour <= 24)) {
            $schedulesByDate[$date]['morning']   = true;
            $schedulesByDate[$date]['afternoon'] = true;
        }

        // 只在早上時段
        if (($startHour >= 9 && $startHour < 12) &&
            ($endHour > 9 && $endHour <= 13)) {
            $schedulesByDate[$date]['morning'] = true;
        }

        // 只在下午時段
        if (($startHour >= 13 && $startHour < 17) &&
            ($endHour > 13 && $endHour <= 24)) {
            $schedulesByDate[$date]['afternoon'] = true;
        }
    }

    $sDate_format = new DateTime($sDate);
    $eDate_format = new DateTime($eDate);
    $year         = $sDate_format->format('Y');
    $month        = $sDate_format->format('m');
    $month2       = $eDate_format->format('m');

    // 獲取該月的所有日期
    $results = [];
    $today   = new DateTime(date('Y-m-d'));
    $date    = new DateTime("$year-$month-01");
    $lastDay = new DateTime("$year-$month2-01");
    $lastDay->modify('last day of this month');
    if ($today >= $date && $today <= $lastDay) {
        $lastDay = $today;
        $lastDay->modify('-1 day');
    }

    while ($date <= $lastDay) {
        $currentDate = $date->format('Y-m-d');
        $dayOfWeek   = $date->format('N'); // 1 (週一) 到 7 (週日)

        // 判斷是否為工作日（週一到週五且不是假日）
        $isWorkday = ($dayOfWeek >= 1 && $dayOfWeek <= 5) && ! isset($holidayLookup[$currentDate]);

        if ($isWorkday) {
            $daySchedule = isset($schedulesByDate[$currentDate])
            ? $schedulesByDate[$currentDate]
            : ['morning' => false, 'afternoon' => false];

            if (! $daySchedule['morning'] || ! $daySchedule['afternoon']) {
                $results[$currentDate] = [
                    'date'               => $currentDate,
//                    'weekday' => $date->format('l'),
                    'morning_has_data'   => $daySchedule['morning'],
                    'afternoon_has_data' => $daySchedule['afternoon'],
                    'fully_complete'     => $daySchedule['morning'] && $daySchedule['afternoon'],
//                    'has_records' => $daySchedule['morning'] || $daySchedule['afternoon']
                ];
            }
        }

        $date->modify('+1 day');
    }

    $output['score'] = (empty($results)) ? 10 : 0;
    $output['error'] = $results;

    return $output;
}

//切割跨天行程或休假
function splitMultiDaySchedules($schedules)
{
    $splitSchedules = [];

    foreach ($schedules as $schedule) {
        $startDateTime = new DateTime($schedule[0]);
        $endDateTime   = new DateTime($schedule[1]);

        // 如果開始和結束日期相同，直接加入
        if ($startDateTime->format('Y-m-d') === $endDateTime->format('Y-m-d')) {
            $splitSchedules[] = $schedule;
            continue;
        }

        // 處理跨天的情況
        $currentDate = clone $startDateTime;
        while ($currentDate <= $endDateTime) {
            $nextDate = clone $currentDate;
            $nextDate->modify('+1 day');

            if ($currentDate->format('Y-m-d') === $startDateTime->format('Y-m-d')) {
                // 第一天：從開始時間到 18:00
                $splitSchedules[] = [
                    $currentDate->format('Y-m-d H:i:s'),
                    $currentDate->format('Y-m-d') . ' 18:00:00',
                ];
            } else if ($currentDate->format('Y-m-d') === $endDateTime->format('Y-m-d')) {
                // 最後一天：從 09:00 到結束時間
                $dayStart         = new DateTime($currentDate->format('Y-m-d') . ' 09:00:00');
                $splitSchedules[] = [
                    $dayStart->format('Y-m-d H:i:s'),
                    $endDateTime->format('Y-m-d H:i:s'),
                ];
            } else {
                // 中間的天：從 09:00 到 18:00
                $splitSchedules[] = [
                    $currentDate->format('Y-m-d') . ' 09:00:00',
                    $currentDate->format('Y-m-d') . ' 18:00:00',
                ];
            }

            $currentDate->modify('+1 day');
        }
    }

    return $splitSchedules;
}

//2025-01-10 周榮德122 非台屋案件從2025年開始濾掉幸福家案件統計
function getCaseUnTwQuantityBy122($sDate, $eDate)
{
    $sDate = substr($sDate, 0, 7);
    $eDate = substr($eDate, 0, 7);
    $ouput = 0;

    $CaseUnTwQuantity = getCaseUnTwQuantityDataBy122();

    foreach ($CaseUnTwQuantity as $k => $v) {
        if ($k >= $sDate && $k <= $eDate) {
            $ouput += $v;
        }
    }

    return $ouput;
}

function getCaseUnTwQuantityDataBy122(){
    return [
        "2024-12" => 84.5,
        "2024-11" => 62.83,
        "2024-10" => 63.5,
        "2024-09" => 71.66,
        "2024-08" => 61.5,
        "2024-07" => 82.82,
        "2024-06" => 91,
        "2024-05" => 86.33,
    ];
}
