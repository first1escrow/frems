<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';
require_once dirname(__DIR__) . '/session_check.php';

//預載log物件
$logs = new Intolog();
##

$_POST = escapeStr($_POST);

//仲介類型轉碼
function category_convert($str = '0', $code = '')
{
    switch ($str) {
        case '1':
            if ($code == 'TH') {
                $str = '加盟(台屋)';
            } else if ($code == 'UM') {
                $str = '加盟(優美)';
            } else {
                $str = '加盟(其他)';
            }

            break;
        case '2':
            $str = '直營';
            break;
        case '3':
            $str = '非仲介成交';
            break;
        default:
            $str = '未知';
            break;
    }
    return $str;
}
##

$bank          = trim($_POST['bank']); //查詢銀行系統
$bStoreClass   = trim($_POST['bStoreClass']); //查詢店身份 (總店:1、單店:2)
$sales_year    = trim($_POST['sales_year']); //查詢回饋年度
$sales_season  = trim($_POST['sales_season']); //查詢回饋季
$certifiedid   = trim($_POST['certifiedid']); //查詢保證號碼
$bCategory     = trim($_POST['bCategory']); //查詢仲介商類型 (加盟:1、直營:2)
$show_hide     = trim($_POST['show_hide']); //是否預設顯示明細區塊
$invert_result = trim($_POST['invert_result']); //顯示剔除資料或正常顯示
$exports       = trim($_POST['exports']); //是否輸出excel檔案

$total_page   = trim($_POST['total_page']) + 1 - 1; //總頁數
$current_page = trim($_POST['current_page']) + 1 - 1; //目前頁數
$record_limit = trim($_POST['record_limit']) + 1 - 1; //單頁顯示筆數
$next_page    = trim($_POST['next_page']); //接下來顯示是否要開啟明細部分

$branch    = $_POST['branch'];
$scrivener = $_POST['scrivener'];
$brand     = $_POST['bd'];
$bck       = $_POST['bck']; //是否撈取多店 1:有

if (!$record_limit) {
    $record_limit = 10;
}

$functions = '';

// 找出愈搜尋的店身份(總店:1、單店:2)
if ($bStoreClass == "1") { //搜尋總店
    //bStoreClass = 總店/單店、bCategory = 加盟/直營、branch = 店編號
    // 店名
    $_cond = '';

    if ($branch && ($scrivener == 0)) {
        if ($bck == 1) {
            $_cond .= ' AND a.bId IN (' . $branch . ')';
        } else {
            $_cond .= ' AND a.bId="' . $branch . '" ';
        }
    }
    ##

    //仲介類型
    if ($bCategory) {
        $_cond .= ' AND bCategory="' . $bCategory . '" ';
    }
    ##
    //品牌
    if ($brand) {
        $_cond .= 'AND bBrand = "' . $brand . '"';
    }

    //找出所有總店
    $bsql = '
	SELECT
		bId,
		(SELECT bCode FROM tBrand WHERE bId=a.bBrand) as bBrand,
		bStore,
		bCategory,
		bStoreClass,
		bClassBranch
	FROM
		tBranch AS a
	WHERE
		bStoreClass="1"
		AND bStatus="1"
		' . $_cond . '
	ORDER BY
		bId
	ASC;
	';

    $i  = 0;
    $rs = $conn->Execute($bsql);
    while (!$rs->EOF) {
        $realty[$i] = $rs->fields;
        $realty_arr = explode(';', $realty[$i]['bClassBranch']);
        for ($j = 0; $j < count($realty_arr); $j++) {
            $realty_arr[$j] = preg_replace("/^[a-zA-Z]+/", "", $realty_arr[$j]);
            $realty_arr[$j] = $realty_arr[$j] + 1 - 1;
        }

        foreach ($realty_arr as $k => $v) {
            $bsql = '
				SELECT
					bId,
					(SELECT bCode FROM tBrand WHERE bId=a.bBrand) as bBrand,
					bStore,
					bCategory,
					bStoreClass,
					bClassBranch,
					bFeedDateCat
				FROM
					tBranch AS a
				WHERE
					bId="' . $v . '"
					AND bStatus="1"
				ORDER BY
					bId
				ASC;
			';
            $rs = $conn->Execute($bsql);

            while (!$rs->EOF) {
                $realty[++$i] = $rs->fields;
                $rs->MoveNext();
            }
        }

        $i++;
        $rs->MoveNext();
    }

} else { //搜尋分店
    // 店名
    $_cond = '';

    if ($branch && ($scrivener == 0)) {

        if ($bck == 1) {
            $_cond .= ' AND a.bId IN (' . $branch . ')';
        } else {
            $_cond .= ' AND a.bId="' . $branch . '" ';
        }
    }
    ##

    //仲介類型
    if ($bCategory) {
        $_cond .= ' AND bCategory="' . $bCategory . '" ';
    }
    ##
    //品牌
    if ($brand) {
        $_cond .= 'AND bBrand = "' . $brand . '"';
    }

    $bsql = '
	SELECT
		bId,
		(SELECT bCode FROM tBrand WHERE bId=a.bBrand) as bBrand,
		bStore,
		bCategory,
		bStoreClass,
		bClassBranch,
		bFeedDateCat
	FROM
		tBranch AS a
	WHERE
		a.bId <> 0
		' . $_cond . '
	ORDER BY
		bId
	ASC;
	';
    $rs = $conn->Execute($bsql);

    while (!$rs->EOF) {
        $realty[] = $rs->fields;
        $rs->MoveNext();
    }
}
##

////建立搜尋條件
$_cond = '';
// 銀行
if ($bank) {
    $_cond .= ' AND cas.cBank="' . $bank . '"';

}
##

// 保證號碼
if ($certifiedid) {
    $_cond .= ' AND cas.cCertifiedId="' . $certifiedid . '"';
    $_cond2 = ' AND cCertifiedId="' . $certifiedid . '"';
}
##

// 年度季別
$date_range   = '';
$contractDate = '';
if ($sales_year && $sales_season) {
    switch ($sales_season) {
        case 'S1':
            $date_range    = ' cas.cFeedbackDate>="' . $sales_year . '-01-01" AND cas.cFeedbackDate<="' . $sales_year . '-03-31"';
            $sales_season1 = '第1季';
            break;
        case 'S2':
            $date_range    = ' cas.cFeedbackDate>="' . $sales_year . '-04-01" AND cas.cFeedbackDate<="' . $sales_year . '-06-30"';
            $sales_season1 = '第2季';
            break;
        case 'S3':
            $date_range    = ' cas.cFeedbackDate>="' . $sales_year . '-07-01" AND cas.cFeedbackDate<="' . $sales_year . '-09-30"';
            $sales_season1 = '第3季';
            break;
        case 'S4':
            $date_range    = ' cas.cFeedbackDate>="' . $sales_year . '-10-01" AND cas.cFeedbackDate<="' . $sales_year . '-12-31"';
            $sales_season1 = '第4季';
            break;
        default:
            $date_range    = ' cas.cFeedbackDate>="' . $sales_year . '-' . $sales_season . '-01" AND cas.cFeedbackDate<="' . $sales_year . '-' . $sales_season . '-31"';
            $sales_season1 = preg_replace("/^0/", "", $sales_season) . '月份';
            break;
    }
    $_cond .= ' AND ' . $date_range;
}
##

//取得合約銀行帳號
$_sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow="1" GROUP BY cBankAccount ORDER BY cId ASC;';
$rs   = $conn->Execute($_sql);

while (!$rs->EOF) {
    $conBank[] = $rs->fields['cBankAccount'];
    $rs->MoveNext();
}

$conBank_sql = implode('","', $conBank);
##

//tra.tObjKind IN ("點交(結案)","解除契約","建經發函終止") 不用了
//20180103 改成(保證費+銀行放款時間)
$_sql = '
	SELECT
		cas.cCertifiedId as cCertifiedId,
		c.bApplication
	FROM
		tContractCase AS cas JOIN tBankCode AS c ON cas.cEscrowBankAccount = c.bAccount
	WHERE 1
		' . $_cond .'
	ORDER BY
		cas.cEndDate
	ASC ;
';
$rs = $conn->Execute($_sql);

while (!$rs->EOF) {
    $cid_arr[] = $rs->fields;
    $rs->MoveNext();
}
##

$otherFeedbackAllItem = array();
$otherFeed = array();
$cid_max   = count($cid_arr);
for ($i = 0; $i < $cid_max; $i++) {
    //--依據保證號碼找出買賣方、店編號1、店編號2、買賣總價金、是否回饋、回饋金1、回饋金2、結案日期、銀行別--
    $Dsql = '
		SELECT
			rea.cCertifyId as cCertifiedId,
			buy.cName as buyer,
			own.cName as owner,
			rea.cBranchNum as cBranchNum,
			rea.cBranchNum1 as cBranchNum1,
			rea.cBranchNum2 as cBranchNum2,
			rea.cBrand as cBrand,
			rea.cBrand1 as cBrand1,
			rea.cBrand2 as cBrand2,
			inc.cTotalMoney as cTotalMoney,
			inc.cCertifiedMoney as cCertifiedMoney,
			inc.cFirstMoney as cFirstMoney,
			cas.cSpCaseFeedBackMoney as cSpCaseFeedBackMoney,
			cas.cCaseFeedback as cCaseFeedback,
			cas.cCaseFeedback1 as cCaseFeedback1,
			cas.cCaseFeedback2 as cCaseFeedback2,
			cas.cCaseFeedBackMoney as cCaseFeedBackMoney,
			cas.cCaseFeedBackMoney1 as cCaseFeedBackMoney1,
			cas.cCaseFeedBackMoney2 as cCaseFeedBackMoney2,
			cas.cEndDate as cEndDate,
			cas.cSignDate as cSignDate,
			cas.cFeedbackTarget as cFeedbackTarget,
			cas.cFeedbackTarget1 as cFeedbackTarget1,
			cas.cFeedbackTarget2 as cFeedbackTarget2,
			(
				SELECT
					(
						SELECT
							sName
						FROM
							tScrivener AS b
						WHERE
							b.sId=a.cScrivener
					)
				FROM
					tContractScrivener AS a
				WHERE
					a.cCertifiedId=cas.cCertifiedId
			) as cScrivener,

			(SELECT cBankFullName FROM tContractBank WHERE cBankCode=cas.cBank) as cBank,
			(
				SELECT
					(
						SELECT
							sId
						FROM
							tScrivener AS b
						WHERE
							b.sId=a.cScrivener
					)
				FROM
					tContractScrivener AS a
				WHERE
					a.cCertifiedId=cas.cCertifiedId
			) as sId,
			(
				SELECT
					(
						SELECT
							sOffice
						FROM
							tScrivener AS b
						WHERE
							b.sId=a.cScrivener
					)
				FROM
					tContractScrivener AS a
				WHERE
					a.cCertifiedId=cas.cCertifiedId
			) as sOffice,
			(
				SELECT
					(
						SELECT
							sFeedDateCat
						FROM
							tScrivener AS b
						WHERE
							b.sId=a.cScrivener
					)
				FROM
					tContractScrivener AS a
				WHERE
					a.cCertifiedId=cas.cCertifiedId
			) as sFeedDateCat,
			(SELECT CONCAT("SC", LPAD(a.cScrivener,4,"0"))  FROM tContractScrivener AS a WHERE a.cCertifiedId=cas.cCertifiedId) AS sCode2,
            (SELECT SUBSTR(relay.bExport_time, 1, 10) FROM tBankTransRelay AS relay WHERE relay.bCertifiedId = rea.cCertifyId AND relay.bKind= "地政士回饋金" LIMIT 1) AS bExport_time
		FROM
			tContractRealestate AS rea
		JOIN
			tContractBuyer AS buy ON buy.cCertifiedId=rea.cCertifyId
		JOIN
			tContractOwner AS own ON own.cCertifiedId=rea.cCertifyId
		JOIN
			tContractIncome AS inc ON inc.cCertifiedId=rea.cCertifyId
		JOIN
			tContractCase AS cas ON cas.cCertifiedId=rea.cCertifyId 
		WHERE
			rea.cCertifyId="' . $cid_arr[$i]['cCertifiedId'] . '"
	';

    $rs                          = $conn->Execute($Dsql);
    $app                         = $cid_arr[$i]['bApplication'];
    $cid_arr[$i]                 = $rs->fields;
    $cid_arr[$i]['bApplication'] = $app;

    //撈取其他回饋對象
    $tmp = getOtherFeed_case($cid_arr[$i]['cCertifiedId'], $cid_arr[$i], $branch, $scrivener, $brand);

    if (is_array($tmp)) {
        $otherFeed = array_merge($otherFeed, $tmp);
        $otherFeedbackAllItem[$cid_arr[$i]['cCertifiedId']] = 0;
        //判斷其他回饋的屬性 1:加盟,2:直營,3:非仲介成交
        if(count($tmp) > 0){
            if(!in_array($cid_arr[$i]['cBranchNum'],array('','505')) || !in_array($cid_arr[$i]['cBranchNum1'],array('','505')) || !in_array($cid_arr[$i]['cBranchNum2'],array('','505'))){
                $otherFeedbackAllItem[$cid_arr[$i]['cCertifiedId']] = 1;
            } else if($cid_arr[$i]['cBranchNum'] == "505" || $cid_arr[$i]['cBranchNum1'] == "505" || $cid_arr[$i]['cBranchNum2'] == "505"){
                $otherFeedbackAllItem[$cid_arr[$i]['cCertifiedId']] = 3;
            }
        }
    }

    $app = null;unset($app);
}

//所有店家
for ($i = 0; $i < count($realty); $i++) {
    //辨識店家身分
    if ($realty[$i]['bStoreClass'] == '1') {
        $realty[$i]['bStoreClass'] = '總店';
    } else {
        $realty[$i]['bStoreClass'] = '單店';
    }
    ##

    //檢核是否有屬於該店之保證號碼
    $index = 0;

    for ($j = 0; $j < $cid_max; $j++) {
        if ($cid_arr[$j]['cFeedbackTarget'] == '1') { //第一家回饋對象為仲介
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum'] && $cid_arr[$j]['cBranchNum'] > 0) { //第一家仲介
                $realty[$i]['cId'][$index]['buyer']              = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']              = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']         = $cid_arr[$j]['cBranchNum'];
                $realty[$i]['cId'][$index]['cTotalMoney']        = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney']    = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']      = $cid_arr[$j]['cCaseFeedback'];
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney'];
                $realty[$i]['cId'][$index]['cEndDate']           = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cSignDate']          = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget']    = '';
                $realty[$i]['cId'][$index]['cBank']              = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cCertifiedId']       = $cid_arr[$j]['cCertifiedId'];
                $realty[$i]['cId'][$index]['bApplication']       = $cid_arr[$j]['bApplication'];

                $index++;

                if ($cid_arr[$j]['cSpCaseFeedBackMoney'] != 0 && (($cid_arr[$j]['cBrand'] != 2 && $cid_arr[$j]['cBrand'] != 49 && $cid_arr[$j]['cBrand'] != 1) || ($cid_arr[$j]['cBrand1'] != 2 && $cid_arr[$j]['cBrand1'] != 49 && $cid_arr[$j]['cBrand1'] != 1) || ($cid_arr[$j]['cBrand2'] != 2 && $cid_arr[$j]['cBrand2'] != 49 && $cid_arr[$j]['cBrand2'] != 1))) {
                    $realty[$i]['cId'][$index]['buyer']                = $cid_arr[$j]['buyer'];
                    $realty[$i]['cId'][$index]['owner']                = $cid_arr[$j]['owner'];
                    $realty[$i]['cId'][$index]['cBranchNum']           = $cid_arr[$j]['cBranchNum'];
                    $realty[$i]['cId'][$index]['cTotalMoney']          = $cid_arr[$j]['cTotalMoney'];
                    $realty[$i]['cId'][$index]['cCertifiedMoney']      = $cid_arr[$j]['cCertifiedMoney'];
                    $realty[$i]['cId'][$index]['cCaseFeedback']        = 0;
                    $realty[$i]['cId'][$index]['cCaseFeedBackMoney']   = $cid_arr[$j]['cSpCaseFeedBackMoney'];
                    $realty[$i]['cId'][$index]['cSpCaseFeedBackMoney'] = $cid_arr[$j]['cSpCaseFeedBackMoney'];
                    $realty[$i]['cId'][$index]['bcode']                = $cid_arr[$j]['bcode'];
                    $realty[$i]['cId'][$index]['cEndDate']             = $cid_arr[$j]['cEndDate'];
                    $realty[$i]['cId'][$index]['cSignDate']            = $cid_arr[$j]['cSignDate'];
                    $realty[$i]['cId'][$index]['cFeedbackTarget']      = $cid_arr[$j]['sCode2'];
                    $realty[$i]['cId'][$index]['ck']                   = '地政士';
                    $realty[$i]['cId'][$index]['cBank']                = $cid_arr[$j]['cBank'];
                    $realty[$i]['cId'][$index]['cScrivener']           = $cid_arr[$j]['cScrivener'];
                    $realty[$i]['cId'][$index]['sId']                  = $cid_arr[$j]['sId'];
                    $realty[$i]['cId'][$index]['sOffice']              = $cid_arr[$j]['sOffice'];
                    $realty[$i]['cId'][$index]['cCertifiedId']         = $cid_arr[$j]['cCertifiedId'];
                    $realty[$i]['cId'][$index]['bApplication']         = $cid_arr[$j]['bApplication'];
                    $realty[$i]['cId'][$index]['sFeedDateCat']         = $cid_arr[$j]['sFeedDateCat'];
                    $realty[$i]['cId'][$index]['bCategory2']           = '特殊回饋(地政士)';
                    $realty[$i]['cId'][$index]['exportTime']           = $cid_arr[$j]['bExport_time'];

                    $index++;
                }
            }
        } else if ($cid_arr[$j]['cFeedbackTarget'] == '2') { //回饋對象為地政士(一)
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum'] && $cid_arr[$j]['cBranchNum'] > 0) { //第一家仲介(代表)
                $realty[$i]['cId'][$index]['buyer']              = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']              = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']         = '地政士';
                $realty[$i]['cId'][$index]['cTotalMoney']        = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney']    = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']      = $cid_arr[$j]['cCaseFeedback'];
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney'];
                $realty[$i]['cId'][$index]['cEndDate']           = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cSignDate']          = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget']    = $cid_arr[$j]['sCode2'];
                $realty[$i]['cId'][$index]['ck']                 = '地政士';
                $realty[$i]['cId'][$index]['cBank']              = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cScrivener']         = $cid_arr[$j]['cScrivener'];
                $realty[$i]['cId'][$index]['cCertifiedId']       = $cid_arr[$j]['cCertifiedId'];
                $realty[$i]['cId'][$index]['bApplication']       = $cid_arr[$j]['bApplication'];
                $realty[$i]['cId'][$index]['sFeedDateCat']       = $cid_arr[$j]['sFeedDateCat'];
                $realty[$i]['cId'][$index]['sOffice']            = $cid_arr[$j]['sOffice'];
                $realty[$i]['cId'][$index]['sId']                = $cid_arr[$j]['sId'];
                $realty[$i]['cId'][$index]['exportTime']         = $cid_arr[$j]['bExport_time'];


                $index++;

                if ($cid_arr[$j]['cSpCaseFeedBackMoney'] != 0 && (($cid_arr[$j]['cBrand'] != 2 && $cid_arr[$j]['cBrand'] != 49 && $cid_arr[$j]['cBrand'] != 1) || ($cid_arr[$j]['cBrand1'] != 2 && $cid_arr[$j]['cBrand1'] != 49 && $cid_arr[$j]['cBrand1'] != 1) || ($cid_arr[$j]['cBrand2'] != 2 && $cid_arr[$j]['cBrand2'] != 49 && $cid_arr[$j]['cBrand2'] != 1))) {
                    $realty[$i]['cId'][$index]['buyer']                = $cid_arr[$j]['buyer'];
                    $realty[$i]['cId'][$index]['owner']                = $cid_arr[$j]['owner'];
                    $realty[$i]['cId'][$index]['cBranchNum']           = $cid_arr[$j]['cBranchNum'];
                    $realty[$i]['cId'][$index]['cTotalMoney']          = $cid_arr[$j]['cTotalMoney'];
                    $realty[$i]['cId'][$index]['cCertifiedMoney']      = $cid_arr[$j]['cCertifiedMoney'];
                    $realty[$i]['cId'][$index]['cCaseFeedback']        = 0;
                    $realty[$i]['cId'][$index]['cCaseFeedBackMoney']   = $cid_arr[$j]['cSpCaseFeedBackMoney'];
                    $realty[$i]['cId'][$index]['cSpCaseFeedBackMoney'] = $cid_arr[$j]['cSpCaseFeedBackMoney'];
                    $realty[$i]['cId'][$index]['bcode']                = $cid_arr[$j]['bcode'];
                    $realty[$i]['cId'][$index]['ck']                   = '地政士';
                    $realty[$i]['cId'][$index]['cEndDate']             = $cid_arr[$j]['cEndDate'];
                    $realty[$i]['cId'][$index]['cSignDate']            = $cid_arr[$j]['cSignDate'];
                    $realty[$i]['cId'][$index]['cFeedbackTarget']      = $cid_arr[$j]['sCode2'];
                    $realty[$i]['cId'][$index]['cBank']                = $cid_arr[$j]['cBank'];
                    $realty[$i]['cId'][$index]['cScrivener']           = $cid_arr[$j]['cScrivener'];
                    $realty[$i]['cId'][$index]['sId']                  = $cid_arr[$j]['sId'];
                    $realty[$i]['cId'][$index]['sOffice']              = $cid_arr[$j]['sOffice'];
                    $realty[$i]['cId'][$index]['cCertifiedId']         = $cid_arr[$j]['cCertifiedId'];
                    $realty[$i]['cId'][$index]['bApplication']         = $cid_arr[$j]['bApplication'];
                    $realty[$i]['cId'][$index]['sFeedDateCat']         = $cid_arr[$j]['sFeedDateCat'];
                    $realty[$i]['cId'][$index]['bCategory2']           = '特殊回饋(地政士)';
                    $realty[$i]['cId'][$index]['exportTime']           = $cid_arr[$j]['bExport_time'];


                    $index++;
                }
            }
        }

        if ($cid_arr[$j]['cFeedbackTarget1'] == '1') { //第二家回饋對象為仲介
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum1'] && $cid_arr[$j]['cBranchNum1'] > 0) { //第二家仲介
                $realty[$i]['cId'][$index]['buyer']              = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']              = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']         = $cid_arr[$j]['cBranchNum1'];
                $realty[$i]['cId'][$index]['cTotalMoney']        = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney']    = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']      = $cid_arr[$j]['cCaseFeedback1'];
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney1'];
                $realty[$i]['cId'][$index]['cEndDate']           = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cSignDate']          = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget']    = '';
                $realty[$i]['cId'][$index]['cBank']              = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cScrivener']         = '';
                $realty[$i]['cId'][$index]['cCertifiedId']       = $cid_arr[$j]['cCertifiedId'];
                $realty[$i]['cId'][$index]['bApplication']       = $cid_arr[$j]['bApplication'];

                $index++;
            }
        } else if ($cid_arr[$j]['cFeedbackTarget1'] == '2') { //回饋對象為地政士(二)
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum1'] && $cid_arr[$j]['cBranchNum1'] > 0) { //第二家仲介(代表)
                $realty[$i]['cId'][$index]['buyer']              = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']              = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']         = '地政士';
                $realty[$i]['cId'][$index]['cTotalMoney']        = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney']    = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']      = $cid_arr[$j]['cCaseFeedback1'];
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney1'];
                $realty[$i]['cId'][$index]['ck']                 = '地政士';
                $realty[$i]['cId'][$index]['cEndDate']           = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cSignDate']          = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget']    = $cid_arr[$j]['sCode2'];
                $realty[$i]['cId'][$index]['cBank']              = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cScrivener']         = $cid_arr[$j]['cScrivener'];
                $realty[$i]['cId'][$index]['cCertifiedId']       = $cid_arr[$j]['cCertifiedId'];
                $realty[$i]['cId'][$index]['bApplication']       = $cid_arr[$j]['bApplication'];
                $realty[$i]['cId'][$index]['sFeedDateCat']       = $cid_arr[$j]['sFeedDateCat'];
                $realty[$i]['cId'][$index]['sOffice']            = $cid_arr[$j]['sOffice'];
                $realty[$i]['cId'][$index]['sId']                = $cid_arr[$j]['sId'];
                $realty[$i]['cId'][$index]['exportTime']         = $cid_arr[$j]['bExport_time'];


                $index++;
            }
        }

        if ($cid_arr[$j]['cFeedbackTarget2'] == '1') { //第三家回饋對象為仲介
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum2'] && $cid_arr[$j]['cBranchNum2'] > 0) { //第三家仲介
                $realty[$i]['cId'][$index]['buyer']              = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']              = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']         = $cid_arr[$j]['cBranchNum2'];
                $realty[$i]['cId'][$index]['cTotalMoney']        = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney']    = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']      = $cid_arr[$j]['cCaseFeedback2'];
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney2'];
                $realty[$i]['cId'][$index]['cEndDate']           = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cSignDate']          = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget']    = '';
                $realty[$i]['cId'][$index]['cBank']              = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cScrivener']         = '';
                $realty[$i]['cId'][$index]['cCertifiedId']       = $cid_arr[$j]['cCertifiedId'];
                $realty[$i]['cId'][$index]['bApplication']       = $cid_arr[$j]['bApplication'];

                $index++;
            }
        } else if ($cid_arr[$j]['cFeedbackTarget2'] == '2') { //回饋對象為地政士(三)
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum2'] && $cid_arr[$j]['cBranchNum2'] > 0) { //第三家仲介(代表)
                $realty[$i]['cId'][$index]['buyer']              = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']              = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']         = '地政士';
                $realty[$i]['cId'][$index]['cTotalMoney']        = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney']    = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']      = $cid_arr[$j]['cCaseFeedback2'];
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney2'];
                $realty[$i]['cId'][$index]['ck']                 = '地政士';
                $realty[$i]['cId'][$index]['cEndDate']           = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cSignDate']          = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget']    = $cid_arr[$j]['sCode2'];
                $realty[$i]['cId'][$index]['cBank']              = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cScrivener']         = $cid_arr[$j]['cScrivener'];
                $realty[$i]['cId'][$index]['cCertifiedId']       = $cid_arr[$j]['cCertifiedId'];
                $realty[$i]['cId'][$index]['bApplication']       = $cid_arr[$j]['bApplication'];
                $realty[$i]['cId'][$index]['sFeedDateCat']       = $cid_arr[$j]['sFeedDateCat'];
                $realty[$i]['cId'][$index]['sOffice']            = $cid_arr[$j]['sOffice'];
                $realty[$i]['cId'][$index]['sId']                = $cid_arr[$j]['sId'];
                $realty[$i]['cId'][$index]['exportTime']         = $cid_arr[$j]['bExport_time'];


                $index++;
            }
        }

    }
    ##
}
##

$index       = 0;
$ct          = 0;
$total_money = 0;
$_arr_index  = 0;

for ($i = 0; $i < count($realty); $i++) {
    if (count($realty[$i]['cId']) && $realty[$i]['bId']) {
        for ($j = 0; $j < count($realty[$i]['cId']); $j++) {
            if ($invert_result == '1') { //顯示剔除資料
                if ($realty[$i]['cId'][$j]['cCaseFeedback'] == '1') { //不要回饋
                    $list[$index] = $realty[$i]['cId'][$j];

                    $list[$index]['bId']          = $realty[$i]['bId'];
                    $list[$index]['bBrand']       = $realty[$i]['bBrand'];
                    $list[$index]['bcode']        = $realty[$i]['cId'][$j]['bcode'];
                    $list[$index]['bStore']       = $realty[$i]['bStore'];
                    $list[$index]['bCategory']    = category_convert($realty[$i]['bCategory'], $realty[$i]['bBrand']);
                    $list[$index]['bFeedback']    = '不回饋';
                    $list[$index]['bFBTarget']    = $list[$index]['cFeedbackTarget'];
                    $list[$index]['bStoreClass']  = $realty[$i]['bStoreClass'];
                    $list[$index]['bClassBranch'] = $realty[$i]['bClassBranch'];
                    if (substr($list[$index]['bFBTarget'], 0, 2) != 'SC') {
                        $list[$index]['bFeedDateCat'] = $realty[$i]['bFeedDateCat'];
                    }

                    ##數量
                    $count[$list[$index]['cCertifiedId']] = $count[$list[$index]['cCertifiedId']] + 1;
                    ##

                    $list[$index]['cCaseFeedBackMoney'] = 0;
                    $ct++;
                    $total_money += $list[$index]['cCaseFeedBackMoney'] + 1 - 1;
                    $index++;
                }
            } else if ($invert_result == '2') { //顯示所有資料
                $list[$index] = $realty[$i]['cId'][$j];

                //顯示 "正常/剔除" title
                if ($realty[$i]['cId'][$j]['cCaseFeedback'] == '1') {
                    $fb                                 = '不回饋';
                    $list[$index]['cCaseFeedBackMoney'] = 0;
                } else {
                    $fb = '回饋';
                    //案件總回饋
                    $CaseFeedTotal[$list[$index]['cCertifiedId']] += $realty[$i]['cId'][$j]['cCaseFeedBackMoney'];
                }
                ##

                $list[$index]['bId']          = $realty[$i]['bId'];
                $list[$index]['bBrand']       = $realty[$i]['bBrand'];
                $list[$index]['bStore']       = $realty[$i]['bStore'];
                $list[$index]['bcode']        = $realty[$i]['cId'][$j]['bcode'];
                $list[$index]['bCategory']    = category_convert($realty[$i]['bCategory'], $realty[$i]['bBrand']);
                $list[$index]['bFeedback']    = $fb;
                $list[$index]['bFBTarget']    = $list[$index]['cFeedbackTarget'];
                $list[$index]['bStoreClass']  = $realty[$i]['bStoreClass'];
                $list[$index]['bClassBranch'] = $realty[$i]['bClassBranch'];
                if (substr($list[$index]['bFBTarget'], 0, 2) != 'SC') {
                    $list[$index]['bFeedDateCat'] = $realty[$i]['bFeedDateCat'];
                }

                ##數量
                $count[$list[$index]['cCertifiedId']] = $count[$list[$index]['cCertifiedId']] + 1;
                ##

                $ct++;
                $total_money += $list[$index]['cCaseFeedBackMoney'] + 1 - 1;

                $index++;
            } else { //顯示正常資料
                if ($realty[$i]['cId'][$j]['cCaseFeedback'] == '0') { //要回饋
                    $list[$index] = $realty[$i]['cId'][$j];

                    $list[$index]['bId']          = $realty[$i]['bId'];
                    $list[$index]['bBrand']       = $realty[$i]['bBrand'];
                    $list[$index]['bStore']       = $realty[$i]['bStore'];
                    $list[$index]['bcode']        = $realty[$i]['cId'][$j]['bcode'];
                    $list[$index]['bCategory']    = category_convert($realty[$i]['bCategory'], $realty[$i]['bBrand']);
                    $list[$index]['bFeedback']    = '回饋';
                    $list[$index]['bFBTarget']    = $list[$index]['cFeedbackTarget'];
                    $list[$index]['bStoreClass']  = $realty[$i]['bStoreClass'];
                    $list[$index]['bClassBranch'] = $realty[$i]['bClassBranch'];

                    if (substr($list[$index]['bFBTarget'], 0, 2) != 'SC') {
                        $list[$index]['bFeedDateCat'] = $realty[$i]['bFeedDateCat'];
                    }

                    ##數量
                    $count[$list[$index]['cCertifiedId']] = $count[$list[$index]['cCertifiedId']] + 1;

                    //案件總回饋
                    $CaseFeedTotal[$list[$index]['cCertifiedId']] += $realty[$i]['cId'][$j]['cCaseFeedBackMoney'];
                    ##

                    $ct++;
                    $total_money += $list[$index]['cCaseFeedBackMoney'] + 1 - 1;

                    $index++;
                }
            }
        }
    }
}

//echo "總共：".$ct." 筆, 共計：".$total_money."元" ;
$xx = count($list);

if ($invert_result != '1') {
    //將其他回饋對象，加進原本的資料陣列
    for ($i = 0; $i < count($otherFeed); $i++) {
        if($bCategory == 0 || $otherFeedbackAllItem[$otherFeed[$i]['cCertifiedId']] == $bCategory) {
            $list[($xx + $i)] = $otherFeed[$i];
            ##數量
            $count[$otherFeed[$i]['cCertifiedId']] = $count[$otherFeed[$i]['cCertifiedId']] + 1;
            //案件總回饋
            $CaseFeedTotal[$otherFeed[$i]['cCertifiedId']] += $otherFeed[$i]['cCaseFeedBackMoney'];
            ##
            $total_money = $total_money + $otherFeed[$i]['cCaseFeedBackMoney'];
        }
    }
}

$max = count($list);

// 以案件結案日期排序
for ($i = 0; $i < $max; $i++) {
    for ($j = 0; $j < $max - 1; $j++) {
        if ($list[$j]['cEndDate'] > $list[$j + 1]['cEndDate']) {
            $tmp          = $list[$j];
            $list[$j]     = $list[$j + 1];
            $list[$j + 1] = $tmp;
            unset($tmp);
        }
    }
}
##

if ($scrivener > 0 && $scrivener != 0) {
    $total_money = 0;
    $tmp_scr     = explode(',', $scrivener);
    $tmp_br      = explode(',', $branch);

    for ($i = 0; $i < count($list); $i++) {
        if ($list[$i]['bFBTarget'] != '') {
            $sId = substr($list[$i]['bFBTarget'], 2);

            if (in_array($sId, $tmp_scr)) {
                $list[$i]['bBrand'] = 'SC';
                $list[$i]['bId']    = $sId;
                $list[$i]['bStore'] = $list[$i]['sOffice'];
                $tmp[]              = $list[$i];
                $total_money += $list[$i]['cCaseFeedBackMoney'];
            }
        }

        if ($branch) { //如果還有查詢仲介店的話
            if (in_array($list[$i]['cBranchNum'], $tmp_br)) {
                $tmp[] = $list[$i];
                $total_money += $list[$i]['cCaseFeedBackMoney'];
            }
        }
    }

    $list = null;unset($list);

    $list = $tmp;

}

####

$total          = count($list);
$feedback_money = $total_money;
$max            = count($list);

if ($exports == 'ok') {
    $logs->writelog('casefeedbackExcel');

    if ($branch || $scrivener || $brand) {
        require_once __DIR__ . '/casefeedback_excelmonth.php';
    } else {
        require_once __DIR__ . '/casefeedback_excel.php';
    }

    die;
}
##

// 計算總頁數
if (($max % $record_limit) == 0) {
    $total_page = $max / $record_limit;
} else {
    $total_page = floor($max / $record_limit) + 1;
}
##

// 設定目前頁數顯示範圍
if ($current_page) {
    if ($current_page >= ($max / $record_limit)) {
        if ($max % $record_limit == 0) {
            $current_page = floor($max / $record_limit);
        } else {
            $current_page = floor($max / $record_limit) + 1;
        }
    }
    $i_end   = $current_page * $record_limit;
    $i_begin = $i_end - $record_limit;
    if ($i_end > $max) {
        $i_end = $max;
    }
    if ($i_end > $max) {$i_end = $max;}
} else {
    $i_end = $record_limit;
    if ($i_end > $max) {$i_end = $max;}
    $i_begin      = 0;
    $current_page = 1;
}
##

if ($max > 0) {
    $tb1 .= '
		<table cellspacing="0" cellpadding="0" style="margin-left:-50px;width:600px;">
			<tr style="background-color:#E4BeB1;text-align:center;">
				<td>查詢日期</td>
				<td>回饋對象數量</td>
				<td>回饋總計金額</td>
				<td>功能</td>
			</tr>
			<tr style="text-align:center;background-color:#F8ECE9;">
				<td>' . ($sales_year - 1911) . '年度&nbsp;' . $sales_season1 . '</td>
				<td>' . number_format($total) . '&nbsp;</td>
				<td>' . number_format($feedback_money) . '&nbsp;</td>
				<td id="showhide">
					<a href="#" onclick="detail()">查看明細</a>&nbsp;
					<a href="#" onclick="excel()">匯出結算明細通知書</a>
				</td>
			</tr>
			<tr style="text-align:center;background-color:#FFFFFF;">
				<td colspan="4" style="height:80px;">
					<input type="button" class="bt4" value="回上一頁" onclick=go_back()>
				</td>
			</tr>
		</table>
	';

    for ($i = $i_begin; $i < $i_end; $i++) {
        if ($i % 2 == 0) {$color_index = "#FFFFFF";} else { $color_index = "#F8ECE9";}

        $_date = trim(substr($list[$i]['cEndDate'], 0, 10));
        if (preg_match("/0000-00-00/", $_date)) {
            $_date = '-';
        } else {
            $_tmp  = explode('-', $_date);
            $_date = ($_tmp[0] - 1911) . '-' . $_tmp[1] . '-' . $_tmp[2];
            unset($_tmp);
        }

        if ($list[$i]['cSpCaseFeedBackMoney'] != '') {
            $tb2 .= '
					<tr style="background-color:' . $color_index . ';">
						<td style="font-size:10pt;">' . $_date . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['cBank'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['cCertifiedId'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . 'SC' . str_pad($list[$i]['sId'], 4, '0', STR_PAD_LEFT) . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['sOffice'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['bStoreClass'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['buyer'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['owner'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . number_format($list[$i]['cTotalMoney']) . '&nbsp;</td>
						<td style="font-size:10pt;">' . number_format($list[$i]['cSpCaseFeedBackMoney']) . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['bCategory'] . '&nbsp;</td>
					</tr>
			';

        } else {
            if ($list[$i]['bBrand'] == 'SC') {
                $dd = str_pad($list[$i]['bId'], 4, '0', STR_PAD_LEFT);
            } elseif ($list[$i]['bBrand'] == 'BM') {
                $dd = str_pad($list[$i]['bId'], 5, '0', STR_PAD_LEFT);
            } else {
                $dd = str_pad($list[$i]['bId'], 5, '0', STR_PAD_LEFT);
            }

            $tb2 .= '
					<tr style="background-color:' . $color_index . ';">
						<td style="font-size:10pt;">' . $_date . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['cBank'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['cCertifiedId'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['bBrand'] . $dd . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['bStore'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['bStoreClass'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['buyer'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['owner'] . '&nbsp;</td>
						<td style="font-size:10pt;">' . number_format($list[$i]['cTotalMoney']) . '&nbsp;</td>
						<td style="font-size:10pt;">' . number_format($list[$i]['cCaseFeedBackMoney']) . '&nbsp;</td>
						<td style="font-size:10pt;">' . $list[$i]['bCategory'] . '&nbsp;</td>
					</tr>
			';
        }
    }
} else {
    $tb1 .= '
	<table cellspacing="0" cellpadding="0" id="detail_table" style="margin-left:-50px;width:800px;display:<{$display}>;">
		<tr style="background-color:#E4BeB1;text-align:center;height:40px;">
			<td>日期</td>
			<td>銀行別</td>
			<td>保證號碼</td>
			<td>店編號</td>
			<td>店名</td>
			<td>身份別</td>
			<td>買方</td>
			<td>賣方</td>
			<td>買賣總價金</td>
			<td>回饋金額</td>
			<td>仲介類型</td>
		</tr>
		<tr style="text-align:center;background-color:#FFFFFF">
			<td colspan="11" style="height:20px;text-align:left;border:1px solid #ccc;"><span style="font-size:9pt;color:red;">目前尚無任何資料！</span></td>
		</tr>
		<tr>
			<td colspan="11" style="height:80px;">
				<input type="button" class="bt4" value="回上一頁" onclick=go_back()>
			</td>
		</tr>
	</table>
	';
}

if ($record_limit == 10) {$records_limit .= '<option value="10" selected="selected">10</option>' . "\n";} else { $records_limit .= '<option value="10">10</option>' . "\n";}
if ($record_limit == 50) {$records_limit .= '<option value="50" selected="selected">50</option>' . "\n";} else { $records_limit .= '<option value="50">50</option>' . "\n";}
if ($record_limit == 100) {$records_limit .= '<option value="100" selected="selected">100</option>' . "\n";} else { $records_limit .= '<option value="100">100</option>' . "\n";}
if ($record_limit == 150) {$records_limit .= '<option value="150" selected="selected">150</option>' . "\n";} else { $records_limit .= '<option value="150">150</option>' . "\n";}
if ($record_limit == 200) {$records_limit .= '<option value="200" selected="selected">200</option>' . "\n";} else { $records_limit .= '<option value="200">200</option>' . "\n";}

$functions = "";

if ($max == 0) {
    $i_begin = 0;
    $i_end   = 0;
} else {
    $i_begin += 1;
}

# 頁面資料
$smarty->assign('i_begin', $i_begin);
$smarty->assign('i_end', $i_end);
$smarty->assign('current_page', $current_page);
$smarty->assign('total_page', $total_page);
$smarty->assign('record_limit', $records_limit);
$smarty->assign('max', $max);
if ($next_page) {
    $smarty->assign('display', '');
} else {
    $smarty->assign('display', 'none');
}

# 搜尋資訊
$smarty->assign('bank', $bank);
$smarty->assign('bStoreClass', $bStoreClass);
$smarty->assign('branch', $branch);
$smarty->assign('sales_year', $sales_year);
$smarty->assign('sales_season', $sales_season);
$smarty->assign('certifiedid', $certifiedid);
$smarty->assign('bCategory', $bCategory);
$smarty->assign('invert_result', $invert_result);
$smarty->assign('scrivener', $scrivener);
$smarty->assign('bck', $bck);
$smarty->assign('brand', $brand);

# 搜尋結果
$smarty->assign('tb1', $tb1);
$smarty->assign('tb2', $tb2);

# 其他
$smarty->assign('show_hide', $show_hide);

$smarty->display('casefeedback_result.inc.tpl', '', 'report');
