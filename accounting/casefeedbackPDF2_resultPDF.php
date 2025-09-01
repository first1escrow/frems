<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';
// require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/IDCheck.php';

ini_set('memory_limit', '256M');

//仲介類型轉碼
function category_convert($str = '0', $code = '')
{
    switch ($str) {
        case '1':
            $str = '加盟';
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

//預載log物件
$logs = new Intolog();
##

$_POST = escapeStr($_POST);

$bank         = $_POST['bank']; //查詢銀行系統
$bStoreClass  = $_POST['bStoreClass']; //查詢店身份 (總店:1、單店:2)
$sales_year   = $_POST['sales_year']; //查詢回饋年度
$sales_season = $_POST['sales_season']; //查詢回饋季
$certifiedid  = $_POST['certifiedid']; //查詢保證號碼
$bCategory    = $_POST['bCategory']; //查詢仲介商類型 (加盟:1、直營:2)
$branch       = $_POST['branch'];
$scrivener    = $_POST['scrivener'];
$storeSearch  = $_POST['bck'];
$filetype     = $_POST['filetype'];
$brand        = $_POST['bd'];

// $sales_year   = '2022';
// $sales_season = 'S4'; //查詢回饋季
// $bCategory    = '1,2,3';
// $bCategory   = '';
// $branch      = '4901';
// $scrivener   = '';
// $storeSearch = '';

##類別##
$CatArr = explode(',', $bCategory);

for ($i = 0; $i < count($CatArr); $i++) {
    if ($CatArr[$i] == 1) {
        $CatArr[] = "加盟";
    } elseif ($CatArr[$i] == 2) {
        $CatArr[] = "直營";
    } elseif ($CatArr[$i] == 3) {
        $CatArr[] = "非仲介成交";
        $CatArr[] = "特殊回饋(地政士)(回饋)";
        $CatArr[] = "地政士";
    }

    if ($CatArr[$i] == 1 || $CatArr[$i] == 2) {
        $CatArr[] = "特殊回饋(其他)(回饋)";
        $CatArr[] = "個案回饋(回饋)";
    }
}
##

// 找出愈搜尋的店身份(總店:1、單店:2)
if ($bStoreClass == "1") { //搜尋總店
    // 店名
    $_cond = '';

    //找出所有總店
    $bsql = 'SELECT
                bId,
                (SELECT bCode FROM tBrand WHERE bId=a.bBrand) as bBrand,
                bStore,
                bCategory,
                bStoreClass,
                bClassBranch,
                bFeedbackAllCase,
                bFeedbackMark2,
                bFeedDateCat
            FROM
                tBranch AS a
            WHERE
                bStoreClass="1"
                AND bStatus="1"
                ' . $_cond . '
            ORDER BY
                bId
            ASC;';
    $rs = $conn->Execute($bsql);

    $i = 0;
    while (!$rs->EOF) {
        $realty[$i] = $rs->fields;
        $realty_arr = explode(';', $realty[$i]['bClassBranch']);

        for ($j = 0; $j < count($realty_arr); $j++) {
            $realty_arr[$j] = preg_replace("/^[a-zA-Z]+/", "", $realty_arr[$j]);
            $realty_arr[$j] = $realty_arr[$j] + 1 - 1;
        }

        foreach ($realty_arr as $k => $v) {
            $bsql = 'SELECT
                        bId,
                        (SELECT bCode FROM tBrand WHERE bId=a.bBrand) as bBrand,
                        bStore,
                        bCategory,
                        bStoreClass,
                        bClassBranch,
                        bFeedDateCat,
                        bFeedbackAllCase,
                        bFeedbackMark2,
                        bFeedDateCat
                    FROM
                        tBranch AS a
                    WHERE
                        bId="' . $v . '"
                        AND bStatus="1"
                    ORDER BY
                        bId
                    ASC;';
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
    $_cond = '';

    $bsql = 'SELECT
                bId,
                (SELECT bCode FROM tBrand WHERE bId=a.bBrand) as bBrand,
                bStore,
                bCategory,
                bStoreClass,
                bClassBranch,
                bFeedDateCat,
                bFeedbackAllCase,
                bFeedbackMark2,
                bBrand AS brandId,
                bFeedDateCat
            FROM
                tBranch AS a
            WHERE
                a.bId <> 0
                ' . $_cond . '
            ORDER BY
                bId
            ASC;';
    $rs = $conn->Execute($bsql);

    while (!$rs->EOF) {
        $realty[] = $rs->fields;
        $rs->MoveNext();
    }
}
##

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
if ($sales_year && $sales_season) {
    switch ($sales_season) {
        case 'S1':
            $date_range    = ' cas.cFeedbackDate>="' . $sales_year . '-01-01" AND cas.cFeedbackDate<="' . $sales_year . '-03-31"';
            $sales_season1 = ($sales_year - 1911) . '年第01季';
            $formDateStart = $sales_year . '-01-01';
            $formDateEnd   = $sales_year . '-03-31';
            break;
        case 'S2':
            $date_range    = ' cas.cFeedbackDate>="' . $sales_year . '-04-01" AND cas.cFeedbackDate<="' . $sales_year . '-06-30"';
            $sales_season1 = ($sales_year - 1911) . '年第02季';
            $formDateStart = $sales_year . '-04-01';
            $formDateEnd   = $sales_year . '-06-30';
            break;
        case 'S3':
            $date_range    = ' cas.cFeedbackDate>="' . $sales_year . '-07-01" AND cas.cFeedbackDate<="' . $sales_year . '-09-30"';
            $sales_season1 = ($sales_year - 1911) . '年第03季';
            $formDateStart = $sales_year . '-07-01';
            $formDateEnd   = $sales_year . '-09-30';
            break;
        case 'S4':
            $date_range    = ' cas.cFeedbackDate>="' . $sales_year . '-10-01" AND cas.cFeedbackDate<="' . $sales_year . '-12-31"';
            $sales_season1 = ($sales_year - 1911) . '年第04季';
            $formDateStart = $sales_year . '-10-01';
            $formDateEnd   = $sales_year . '-12-31';
            break;
        default:
            $date_range    = ' cas.cFeedbackDate>="' . $sales_year . '-' . $sales_season . '-01" AND cas.cFeedbackDate<="' . $sales_year . '-' . $sales_season . '-31"';
            $sales_season1 = ($sales_year - 1911) . '年' . str_pad($sales_season, 2, '0', STR_PAD_LEFT) . '月';

            $formDateStart = $sales_year . '-' . $sales_season . '-01';
            $formDateEnd   = $sales_year . '-' . $sales_season . '-' . date('t', strtotime($formDateStart));

            break;
    }
    $_cond .= ' AND ' . $date_range;
}

if ($sales_year_end && $sales_season_end) {
    if ($qstr) {
        $qstr .= " AND ";
    }

    switch ($sales_season_end) {
        case 'S1':
            $date_start = ($sales_year_end - 1911) . "年第01季";
            $qstr .= ' sSeason = "' . $date_start . '"';
            break;
        case 'S2':
            $date_start = ($sales_year_end - 1911) . "年第02季";
            $qstr .= ' sSeason = "' . $date_start . '"';

            break;
        case 'S3':
            $date_start = ($sales_year_end - 1911) . "年第03季";
            $qstr .= ' sSeason = "' . $date_start . '"';

            break;
        case 'S4':
            $date_start = ($sales_year_end - 1911) . "年第04季";
            $qstr .= ' sSeason = "' . $date_start . '"';

            break;
        default:
            $date_start = $sales_year_end . "-" . $sales_season . "-01";
            $qstr .= ' sEndTime >= "' . $date_start . '"';
            if ($qstr) {
                $qstr .= ' AND ';
            }
            $date_end = $sales_year_end . "-" . $sales_season . "-" . date('t', $sales_year_end . "-" . $sales_season_end);
            $qstr .= 'sEndTime2 >= "' . $date_start . '" AND sEndTime2 <= "' . $date_end . '"';
            break;
    }
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

$cid_arr = array();
$_sql    = 'SELECT
                cas.cCertifiedId
            FROM
                tContractCase AS cas 
            WHERE 1
                ' . $_cond . '
            ORDER BY
                cas.cEndDate
            ASC;';
$rs = $conn->Execute($_sql);
// exit('sql = ' . $_sql);
while (!$rs->EOF) {
    $cid_arr[] = $rs->fields;
    $rs->MoveNext();
}
##

//取得所有已出履保費且不重複的履保帳號
$_cid_arr = array_values(array_unique(array_column($cid_arr, 'cCertifiedId')));
##

$_cid_arr = null;unset($_cid_arr);
##

// $cid_arr = [
// ['cCertifiedId' => '101217439'],
// ['cCertifiedId' => '110229630'],
// ];

$_cid_arr = array_column($cid_arr, 'cCertifiedId');

$otherFeed = array();
$cid_max   = count($cid_arr);

//--依據保證號碼找出買賣方、店編號1、店編號2、買賣總價金、是否回饋、回饋金1、回饋金2、結案日期、銀行別--
$Dsql = 'SELECT
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
                cas.cSpCaseFeedBackMoney as cSpCaseFeedBackMoney,
                cas.cCaseFeedback as cCaseFeedback,
                cas.cCaseFeedback1 as cCaseFeedback1,
                cas.cCaseFeedback2 as cCaseFeedback2,
                cas.cCaseFeedBackMoney as cCaseFeedBackMoney,
                cas.cCaseFeedBackMoney1 as cCaseFeedBackMoney1,
                cas.cCaseFeedBackMoney2 as cCaseFeedBackMoney2,
                cas.cEndDate as cEndDate,
                cas.cFeedbackDate as cFeedbackDate,
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
                (SELECT CONCAT("SC", LPAD(a.cScrivener,4,"0"))  FROM tContractScrivener AS a WHERE a.cCertifiedId=cas.cCertifiedId) AS sCode2
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
                rea.cCertifyId IN ("' . implode('","', $_cid_arr) . '");';
// rea.cCertifyId="' . $cid_arr[$i]['cCertifiedId'] . '";';
$rs = $conn->Execute($Dsql);

$cid_arr = null;unset($cid_arr);
while (!$rs->EOF) {
    $v         = $rs->fields;
    $cid_arr[] = $v;

    //撈取其他回饋對象
    $_branch = ($branch == 4901) ? '' : $branch; //實易總管理(EB04901)要能看到EB5811的特殊回饋金額，所以將 _branch = ''，以便取得總部回饋金額
    $tmp     = getOtherFeed_case($v['cCertifiedId'], $v, $_branch, $scrivener, $brand);

    if (is_array($tmp)) {
        $otherFeed = array_merge($otherFeed, $tmp);
    }

    $_branch = $v = $tmp = null;
    unset($_branch, $v, $tmp);

    $rs->MoveNext();
}

//所有店家
for ($i = 0; $i < count($realty); $i++) {
    //辨識店家身分
    $realty[$i]['bStoreClass'] = ($realty[$i]['bStoreClass'] == '1') ? '總店' : '單店';
    ##

    //檢核是否有屬於該店之保證號碼
    $index = 0;
    for ($j = 0; $j < $cid_max; $j++) {
        if ($cid_arr[$j]['cFeedbackTarget'] == '1') { //第一家回饋對象為仲介
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum']) { //第一家仲介
                $realty[$i]['cId'][$index]['buyer']           = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']           = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']      = $cid_arr[$j]['cBranchNum'];
                $realty[$i]['cId'][$index]['cTotalMoney']     = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']   = $cid_arr[$j]['cCaseFeedback'];
                $realty[$i]['cId'][$index]['bCategory']       = category_convert($realty[$i]['bCategory']);
                //
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney'];
                $realty[$i]['cId'][$index]['cEndDate']           = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cFeedbackDate']      = $cid_arr[$j]['cFeedbackDate'];
                $realty[$i]['cId'][$index]['cSignDate']          = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget']    = '';
                $realty[$i]['cId'][$index]['cBank']              = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cCertifiedId']       = $cid_arr[$j]['cCertifiedId'];
                $index++;

                if ($cid_arr[$j]['cSpCaseFeedBackMoney'] != 0 && empty($branch) && (($cid_arr[$j]['cBrand'] != 2 && $cid_arr[$j]['cBrand'] != 49 && $cid_arr[$j]['cBrand'] != 1) || ($cid_arr[$j]['cBrand1'] != 2 && $cid_arr[$j]['cBrand1'] != 49 && $cid_arr[$j]['cBrand1'] != 1) || ($cid_arr[$j]['cBrand2'] != 2 && $cid_arr[$j]['cBrand2'] != 49 && $cid_arr[$j]['cBrand2'] != 1))) {
                    $realty[$i]['cId'][$index]['buyer']                = $cid_arr[$j]['buyer'];
                    $realty[$i]['cId'][$index]['owner']                = $cid_arr[$j]['owner'];
                    $realty[$i]['cId'][$index]['cBranchNum']           = $cid_arr[$j]['cBranchNum'];
                    $realty[$i]['cId'][$index]['cTotalMoney']          = $cid_arr[$j]['cTotalMoney'];
                    $realty[$i]['cId'][$index]['cCertifiedMoney']      = $cid_arr[$j]['cCertifiedMoney'];
                    $realty[$i]['cId'][$index]['cCaseFeedback']        = 0;
                    $realty[$i]['cId'][$index]['cCaseFeedBackMoney']   = $cid_arr[$j]['cSpCaseFeedBackMoney'];
                    $realty[$i]['cId'][$index]['cSpCaseFeedBackMoney'] = $cid_arr[$j]['cSpCaseFeedBackMoney'];
                    $realty[$i]['cId'][$index]['bcode']                = $cid_arr[$j]['bcode'];
                    $realty[$i]['cId'][$index]['bCategory']            = '特殊回饋(地政士)(回饋)';

                    $realty[$i]['cId'][$index]['cEndDate']        = $cid_arr[$j]['cEndDate'];
                    $realty[$i]['cId'][$index]['cFeedbackDate']   = $cid_arr[$j]['cFeedbackDate'];
                    $realty[$i]['cId'][$index]['cSignDate']       = $cid_arr[$j]['cSignDate'];
                    $realty[$i]['cId'][$index]['cFeedbackTarget'] = $cid_arr[$j]['sCode2'];
                    $realty[$i]['cId'][$index]['ck']              = '地政士';
                    $realty[$i]['cId'][$index]['cBank']           = $cid_arr[$j]['cBank'];
                    $realty[$i]['cId'][$index]['cScrivener']      = $cid_arr[$j]['cScrivener'];
                    $realty[$i]['cId'][$index]['sId']             = $cid_arr[$j]['sId'];
                    $realty[$i]['cId'][$index]['sOffice']         = $cid_arr[$j]['sOffice'];
                    $realty[$i]['cId'][$index]['cCertifiedId']    = $cid_arr[$j]['cCertifiedId'];
                    $index++;
                }
            }
        } else if ($cid_arr[$j]['cFeedbackTarget'] == '2') { //回饋對象為地政士(一)
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum']) { //第一家仲介(代表)
                $realty[$i]['cId'][$index]['buyer']              = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']              = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']         = '地政士';
                $realty[$i]['cId'][$index]['cTotalMoney']        = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney']    = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']      = $cid_arr[$j]['cCaseFeedback'];
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney'];
                $realty[$i]['cId'][$index]['bCategory']          = '地政士';

                $realty[$i]['cId'][$index]['cEndDate']        = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cFeedbackDate']   = $cid_arr[$j]['cFeedbackDate'];
                $realty[$i]['cId'][$index]['cSignDate']       = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget'] = $cid_arr[$j]['sCode2'];
                $realty[$i]['cId'][$index]['ck']              = '地政士';
                $realty[$i]['cId'][$index]['cBank']           = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cScrivener']      = $cid_arr[$j]['cScrivener'];
                $realty[$i]['cId'][$index]['cCertifiedId']    = $cid_arr[$j]['cCertifiedId'];
                $realty[$i]['cId'][$index]['sFeedDateCat']    = $cid_arr[$j]['sFeedDateCat'];
                $realty[$i]['cId'][$index]['sOffice']         = $cid_arr[$j]['sOffice'];
                $realty[$i]['cId'][$index]['sId']             = $cid_arr[$j]['sId'];

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
                    $realty[$i]['cId'][$index]['bCategory']            = '特殊回饋(地政士)(回饋)';

                    $realty[$i]['cId'][$index]['cEndDate']        = $cid_arr[$j]['cEndDate'];
                    $realty[$i]['cId'][$index]['cFeedbackDate']   = $cid_arr[$j]['cFeedbackDate'];
                    $realty[$i]['cId'][$index]['cSignDate']       = $cid_arr[$j]['cSignDate'];
                    $realty[$i]['cId'][$index]['cFeedbackTarget'] = $cid_arr[$j]['sCode2'];
                    $realty[$i]['cId'][$index]['cBank']           = $cid_arr[$j]['cBank'];
                    $realty[$i]['cId'][$index]['cScrivener']      = $cid_arr[$j]['cScrivener'];
                    $realty[$i]['cId'][$index]['sId']             = $cid_arr[$j]['sId'];
                    $realty[$i]['cId'][$index]['sOffice']         = $cid_arr[$j]['sOffice'];
                    $realty[$i]['cId'][$index]['cCertifiedId']    = $cid_arr[$j]['cCertifiedId'];
                    $realty[$i]['cId'][$index]['sFeedDateCat']    = $cid_arr[$j]['sFeedDateCat'];
                    $index++;
                }
            }
        }

        if ($cid_arr[$j]['cFeedbackTarget1'] == '1') { //第二家回饋對象為仲介
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum1']) { //第二家仲介
                $realty[$i]['cId'][$index]['buyer']              = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']              = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']         = $cid_arr[$j]['cBranchNum1'];
                $realty[$i]['cId'][$index]['cTotalMoney']        = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney']    = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']      = $cid_arr[$j]['cCaseFeedback1'];
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney1'];
                $realty[$i]['cId'][$index]['cEndDate']           = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cFeedbackDate']      = $cid_arr[$j]['cFeedbackDate'];
                $realty[$i]['cId'][$index]['cSignDate']          = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget']    = '';
                $realty[$i]['cId'][$index]['cBank']              = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cScrivener']         = '';
                $realty[$i]['cId'][$index]['cCertifiedId']       = $cid_arr[$j]['cCertifiedId'];
                $realty[$i]['cId'][$index]['bCategory']          = category_convert($realty[$i]['bCategory']);

                $index++;
            }
        } else if ($cid_arr[$j]['cFeedbackTarget1'] == '2') { //回饋對象為地政士(二)
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum1']) { //第二家仲介(代表)
                $realty[$i]['cId'][$index]['buyer']              = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']              = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']         = '地政士';
                $realty[$i]['cId'][$index]['cTotalMoney']        = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney']    = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']      = $cid_arr[$j]['cCaseFeedback1'];
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney1'];
                $realty[$i]['cId'][$index]['ck']                 = '地政士';
                $realty[$i]['cId'][$index]['bCategory']          = '地政士';

                $realty[$i]['cId'][$index]['cEndDate']        = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cFeedbackDate']   = $cid_arr[$j]['cFeedbackDate'];
                $realty[$i]['cId'][$index]['cSignDate']       = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget'] = $cid_arr[$j]['sCode2'];
                $realty[$i]['cId'][$index]['cBank']           = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cScrivener']      = $cid_arr[$j]['cScrivener'];
                $realty[$i]['cId'][$index]['cCertifiedId']    = $cid_arr[$j]['cCertifiedId'];
                $realty[$i]['cId'][$index]['sFeedDateCat']    = $cid_arr[$j]['sFeedDateCat'];
                $realty[$i]['cId'][$index]['sOffice']         = $cid_arr[$j]['sOffice'];
                $realty[$i]['cId'][$index]['sId']             = $cid_arr[$j]['sId'];
                $index++;
            }
        }

        if ($cid_arr[$j]['cFeedbackTarget2'] == '1') { //第三家回饋對象為仲介
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum2']) { //第三家仲介
                $realty[$i]['cId'][$index]['buyer']              = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']              = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']         = $cid_arr[$j]['cBranchNum2'];
                $realty[$i]['cId'][$index]['cTotalMoney']        = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney']    = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']      = $cid_arr[$j]['cCaseFeedback2'];
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney2'];
                $realty[$i]['cId'][$index]['cEndDate']           = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cFeedbackDate']      = $cid_arr[$j]['cFeedbackDate'];
                $realty[$i]['cId'][$index]['cSignDate']          = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget']    = '';
                $realty[$i]['cId'][$index]['cBank']              = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cScrivener']         = '';
                $realty[$i]['cId'][$index]['cCertifiedId']       = $cid_arr[$j]['cCertifiedId'];
                $realty[$i]['cId'][$index]['bCategory']          = category_convert($realty[$i]['bCategory']);

                $index++;
            }
        } else if ($cid_arr[$j]['cFeedbackTarget2'] == '2') { //回饋對象為地政士(三)
            if ($realty[$i]['bId'] == $cid_arr[$j]['cBranchNum2']) { //第三家仲介(代表)
                $realty[$i]['cId'][$index]['buyer']              = $cid_arr[$j]['buyer'];
                $realty[$i]['cId'][$index]['owner']              = $cid_arr[$j]['owner'];
                $realty[$i]['cId'][$index]['cBranchNum']         = '地政士';
                $realty[$i]['cId'][$index]['cTotalMoney']        = $cid_arr[$j]['cTotalMoney'];
                $realty[$i]['cId'][$index]['cCertifiedMoney']    = $cid_arr[$j]['cCertifiedMoney'];
                $realty[$i]['cId'][$index]['cCaseFeedback']      = $cid_arr[$j]['cCaseFeedback2'];
                $realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney2'];
                $realty[$i]['cId'][$index]['ck']                 = '地政士';
                $realty[$i]['cId'][$index]['bCategory']          = '地政士';

                $realty[$i]['cId'][$index]['cEndDate']        = $cid_arr[$j]['cEndDate'];
                $realty[$i]['cId'][$index]['cFeedbackDate']   = $cid_arr[$j]['cFeedbackDate'];
                $realty[$i]['cId'][$index]['cSignDate']       = $cid_arr[$j]['cSignDate'];
                $realty[$i]['cId'][$index]['cFeedbackTarget'] = $cid_arr[$j]['sCode2'];
                $realty[$i]['cId'][$index]['cBank']           = $cid_arr[$j]['cBank'];
                $realty[$i]['cId'][$index]['cScrivener']      = $cid_arr[$j]['cScrivener'];
                $realty[$i]['cId'][$index]['cCertifiedId']    = $cid_arr[$j]['cCertifiedId'];
                $realty[$i]['cId'][$index]['sFeedDateCat']    = $cid_arr[$j]['sFeedDateCat'];
                $realty[$i]['cId'][$index]['sOffice']         = $cid_arr[$j]['sOffice'];
                $realty[$i]['cId'][$index]['sId']             = $cid_arr[$j]['sId'];
                $index++;
            }
        }
    }
    ##
}
$cid_arr = null;unset($cid_arr);
##

$index      = 0;
$ct         = 0;
$_arr_index = 0;

$list = array();
for ($i = 0; $i < count($realty); $i++) {
    if (!empty($realty[$i]['cId']) && $realty[$i]['bId']) {
        for ($j = 0; $j < count($realty[$i]['cId']); $j++) {
            if ($realty[$i]['cId'][$j]['cCaseFeedback'] == '0') { //要回饋
                $list[$index] = $realty[$i]['cId'][$j];

                $list[$index]['bId']    = $realty[$i]['bId'];
                $list[$index]['bBrand'] = $realty[$i]['bBrand'];
                $list[$index]['bcode']  = $realty[$i]['cId'][$j]['bcode'];

                $list[$index]['bFeedback'] = '回饋';
                $list[$index]['bFBTarget'] = $list[$index]['cFeedbackTarget'];

                if ($list[$index]['bFBTarget'] == '') {
                    $list[$index]['bFBTarget'] = $realty[$i]['bBrand'] . str_pad($realty[$i]['bId'], 5, 0, STR_PAD_LEFT);
                    $list[$index]['bStore']    = $realty[$i]['bStore'];
                } else {
                    $list[$index]['bStore'] = $list[$index]['sOffice'];
                }

                $list[$index]['bStoreClass']      = $realty[$i]['bStoreClass'];
                $list[$index]['bClassBranch']     = $realty[$i]['bClassBranch'];
                $list[$index]['bFeedDateCat']     = $realty[$i]['bFeedDateCat'];
                $list[$index]['bFeedbackMark2']   = $realty[$i]['bFeedbackMark2'];
                $list[$index]['bFeedbackAllCase'] = $realty[$i]['bFeedbackAllCase'];

                ##數量
                $count[$list[$index]['cCertifiedId']] = $count[$list[$index]['cCertifiedId']] + 1;
                ##

                $ct++;
                $index++;
            }
        }
    }
}

$xx = count($list);

//將其他回饋對象，加進原本的資料陣列
for ($i = 0; $i < count($otherFeed); $i++) {
    if (preg_match("/直營/", $otherFeed[$i]['bStore'])) {
        $otherFeed[$i]['bCategory'] = '直營';
    } elseif (preg_match("/加盟/", $otherFeed[$i]['bStore'])) {
        $otherFeed[$i]['bCategory'] = '加盟';
    }

    $list[($xx + $i)] = $otherFeed[$i];

    ##數量
    $count[$otherFeed[$i]['cCertifiedId']] = $count[$otherFeed[$i]['cCertifiedId']] + 1;
    ##
}
$otherFeed = null;unset($otherFeed);

$max = count($list);

//總店回饋
$managerStore = array();
$sql          = "SELECT
                    bId,
                    bClassBranch,
                    bStoreClass,
                    bFeedbackAllCase,
                    bFeedbackMark2,
                    bBrand,
                    bStore,
                    CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) as Code,
                    (SELECT bName FROM tBrand WHERE bId = bBrand) AS brandName
                FROM
                    tBranch
                WHERE
                    bFeedbackAllCase > 0"; //可看分店的回饋案件 0:禁止觀看、1:同品牌(bBrand)、2:分店編號(bClassBranch)
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    if ($rs->fields['bFeedbackAllCase'] == 1) { //品牌
        $managerStore[$rs->fields['bId']]['code']            = $rs->fields['Code'];
        $managerStore[$rs->fields['bId']]['name']            = $rs->fields['brandName'] . $rs->fields['bStore'];
        $managerStore[$rs->fields['bId']]['feedbackallcase'] = $rs->fields['bFeedbackAllCase'];
        $managerStore[$rs->fields['bId']]['bFeedbackMark2']  = $rs->fields['bFeedbackMark2'];

        $sql = "SELECT bId FROM tBranch WHERE bBrand = '" . $rs->fields['bBrand'] . "' AND bId != '" . $rs->fields['bId'] . "'";
        $rs2 = $conn->Execute($sql);

        while (!$rs2->EOF) {
            $managerStore[$rs->fields['bId']]['branch'][] = $rs2->fields['bId'];
            $rs2->MoveNext();
        }
    } elseif ($rs->fields['bFeedbackAllCase'] == 2) { //分店
        $managerStore[$rs->fields['bId']]['code']            = $rs->fields['Code'];
        $managerStore[$rs->fields['bId']]['name']            = $rs->fields['brandName'] . $rs->fields['bStore'];
        $managerStore[$rs->fields['bId']]['feedbackallcase'] = $rs->fields['bFeedbackAllCase'];
        $managerStore[$rs->fields['bId']]['bFeedbackMark2']  = $rs->fields['bFeedbackMark2'];

        $exp_b = explode(';', $rs->fields['bClassBranch']);
        for ($j = 0; $j < count($exp_b); $j++) {
            $managerStore[$rs->fields['bId']]['branch'][] = (int) preg_replace("/^[a-zA-Z]+/", "", $exp_b[$j]);
        }
        $exp_b = null;unset($exp_b);
    }

    $rs->MoveNext();
}

//地政是查詢
if ($scrivener) {
    $scrArr = array();
    $scrArr = explode(',', $scrivener);
}

//仲介查詢
if ($branch) {
    $branchArr = $branchArr2 = array();
    $branchArr = explode(',', $branch);

    //如果查詢總店，要把分店也加入查詢內
    foreach ($branchArr as $k => $v) {
        if (array_key_exists($v, $managerStore)) {
            $branchArr2 = array_merge($branchArr2, $managerStore[$v]['branch']);
        }
    }
}

if ($brand) {
    $sql       = "SELECT bCode FROM tBrand WHERE bId = '" . $brand . "'";
    $rs        = $conn->Execute($sql);
    $brandCode = $rs->fields['bCode'];
}

for ($i = 0; $i < $max; $i++) {
    $code   = (substr($list[$i]['bFBTarget'], 0, 2) == 'SC') ? 's' : 'b';
    $codeId = substr($list[$i]['bFBTarget'], 2);
    $code2  = $code . $codeId;
    $check  = false;

    //查詢地政士
    if ($scrivener) {
        if (in_array((int) $codeId, $scrArr) && $code == 's') {
            $check = true;
        }
    }

    //查詢仲介
    if ($branch) {
        if (in_array((int) $codeId, $branchArr) && $code != 's') {
            $check = true;
        }

        //總店分店
        if (in_array((int) $codeId, $branchArr2) && $code != 's') {
            $check = true;
        }
    }

    if ($brand) {
        if ($brandCode == substr($list[$i]['bFBTarget'], 0, 2)) {
            $check = true;
        }
    }

    if (($bCategory && $storeSearch == '') && in_array($list[$i]['bCategory'], $CatArr)) {
        if ($bCategory == 2) {
            //直營只顯示ˇ TH00110 TH00111 TH00112 TH00545 TH00646
            if ($list[$i]['bFBTarget'] == 'TH00110' || $list[$i]['bFBTarget'] == 'TH00111' || $list[$i]['bFBTarget'] == 'TH00112' || $list[$i]['bFBTarget'] == 'TH00545' || $list[$i]['bFBTarget'] == 'TH00646') {
                $check = true;
            } else {
                $check = false;
            }
        } elseif ($list[$i]['bCategory'] == '直營') {
            if ($list[$i]['bFBTarget'] == 'TH00110' || $list[$i]['bFBTarget'] == 'TH00111' || $list[$i]['bFBTarget'] == 'TH00112' || $list[$i]['bFBTarget'] == 'TH00545' || $list[$i]['bFBTarget'] == 'TH00646') {
                $check = true;
            } else {
                $check = false;
            }
        } else {
            $check = true;
        }
    }

    if ($list[$i]['cCaseFeedBackMoney'] == 0) { //回饋金為0濾掉
        $check = false;
    }

    if ($check) {
        if ($branch) {
            if (in_array((int) $codeId, $branchArr) && $code != 's') {
                $data[$code2]['data'][] = $list[$i];
                $data[$code2]['feedbackMoney'] += (int) $list[$i]['cCaseFeedBackMoney']; //回饋金
                $data[$code2]['cCertifiedMoney'] += (int) $list[$i]['cCertifiedMoney']; //保證費
                $data[$code2]['total'] += (int) $list[$i]['cTotalMoney']; //總價金
                $data[$code2]['storeId']        = $list[$i]['bFBTarget']; //店編號號
                $data[$code2]['storeName']      = $list[$i]['bStore']; //店名稱
                $data[$code2]['bFeedbackMark2'] = $list[$i]['bFeedbackMark2'];
            }
        } else {
            $data[$code2]['data'][] = $list[$i];
            $data[$code2]['feedbackMoney'] += (int) $list[$i]['cCaseFeedBackMoney']; //回饋金
            $data[$code2]['cCertifiedMoney'] += (int) $list[$i]['cCertifiedMoney']; //保證費
            $data[$code2]['total'] += (int) $list[$i]['cTotalMoney']; //總價金
            $data[$code2]['storeId']        = $list[$i]['bFBTarget']; //店編號號
            $data[$code2]['storeName']      = $list[$i]['bStore']; //店名稱
            $data[$code2]['bFeedbackMark2'] = $list[$i]['bFeedbackMark2'];
        }

        //總店看分店品牌
        if (is_array($managerStore)) {
            foreach ($managerStore as $k => $v) {
                $manager_code = 'b' . str_pad($k, 5, '0', STR_PAD_LEFT);
                $checkCode    = (int) substr($list[$i]['bFBTarget'], 2);

                if (in_array($checkCode, $v['branch']) && substr($list[$i]['bFBTarget'], 0, 2) != 'SC') {
                    $data[$manager_code]['data'][] = $list[$i];
                    $data[$manager_code]['feedbackMoney'] += (int) $list[$i]['cCaseFeedBackMoney']; //回饋金
                    $data[$manager_code]['cCertifiedMoney'] += (int) $list[$i]['cCertifiedMoney']; //保證費
                    $data[$manager_code]['total'] += (int) $list[$i]['cTotalMoney']; //總價金
                    $data[$manager_code]['storeId']         = $v['code']; //店編號號
                    $data[$manager_code]['storeName']       = $v['name']; //店名稱
                    $data[$manager_code]['feedbackallcase'] = $v['feedbackallcase'];
                    $data[$manager_code]['bFeedbackMark2']  = $v['bFeedbackMark2'];
                }

                $manager_code = null;unset($manager_code);
            }
        }
    }
}
####

if ($oldflag == 1) {
    if (count($data) > 0) {
        ksort($data);
        $cat = 1;

        if ($filetype == 'excel') {
            include_once dirname(__FILE__) . '/pdf/excel.php';
        } else {
            set_time_limit(0);

            $endTime = $formDateStart . "~" . $formDateEnd;
            include_once dirname(__FILE__) . '/pdf/pdfPrint_2020_pdfMain.php';
            include_once dirname(__FILE__) . '/pdf/excel.php';
        }
    } else {
        $cat = 0;
    }
} else {
    if (is_array($data)) {
        ksort($data);

        $sql = "UPDATE
                    tStoreFeedBackMoneyFrom
                SET
                    sDelete = 1
                WHERE
                    sEndTime = '" . $formDateStart . "'
                  AND sEndTime2 = '" . $formDateEnd . "' AND sLock != 1 AND sStatus <= 1";

        // $sql = "UPDATE
        //             tStoreFeedBackMoneyFrom
        //         SET
        //             sDelete = 1
        //         WHERE
        //             sEndTime = '" . $formDateStart . "'
        //           AND sEndTime2 = '" . $formDateEnd . "' AND sLock != 1 AND sStatus <= 1 AND sDelete = 1 ";

        $conn->Execute($sql);

        foreach ($data as $k => $v) {
            $feedbackData    = array();
            $type            = (substr($v['storeId'], 0, 2) == 'SC') ? 1 : 2; //1:地政士
            $storeId         = (int) substr($v['storeId'], 2);
            $storeCode       = substr($v['storeId'], 0, 2);
            $v['storeName']  = str_replace('(待停用)', '', $v['storeName']);
            $method          = 0; //1公司2事務所3個人
            $category        = 1;
            $feedbackallcase = empty($v['feedbackallcase']) ? '' : $v['feedbackallcase']; //是否可看分店

            $sql = "UPDATE
						tStoreFeedBackMoneyFrom
					SET
						sDelete = 1
					WHERE
						sEndTime = '" . $formDateStart . "'
						AND sEndTime2 = '" . $formDateEnd . "' AND sLock != 1 AND sStatus <= 1 AND sStoreCode = '" . $storeCode . "'  AND sStoreId = '" . $storeId . "'"; // AND sStatus = '".$status."'
            $conn->Execute($sql);

            //查詢是否鎖住
            $sql = "SELECT sLock,sStatus,sDeleteName,sDelete FROM tStoreFeedBackMoneyFrom WHERE sStoreId = '" . $storeId . "'
				AND sType = '" . $type . "' AND sEndTime = '" . $formDateStart . "' AND sEndTime2 = '" . $formDateEnd . "' ORDER BY sEditTime DESC LIMIT 1";
            $rs = $conn->Execute($sql);

            if ($rs->fields['sLock'] == 1 && $rs->fields['sDelete'] == 0) { //判斷是否鎖住 1:鎖住
                continue;
            }

            if ($rs->fields['sDeleteName'] != 0 && $rs->fields['sLock'] == 1) { //有手動刪除過，不用再產生 (但解鎖的可以重新產生)
                continue;
            }

            if ($rs->fields['sStatus'] > 1 && $rs->fields['sLock'] == 1) { //對方確認過但解鎖也可以重新產生
                continue;
            }

            //回饋資料
            $sql = "SELECT
						fIdentityNumber,
						fIdentity,
						fNote,
						(SELECT bBank4_name FROM tBank WHERE bBank3 = fAccountNum AND bBank4 ='') AS bankMain,
						(SELECT bBank4_name FROM tBank WHERE bBank3 = fAccountNum AND bBank4 =fAccountNumB) AS bankBranch,
						fAccountNum,
						fAccountNumB,
						fAccount,
						fAccountName
					FROM
						tFeedBackData
					WHERE
						fType = '" . $type . "' AND fStoreId = '" . $storeId . "' AND fStop = 0 AND fStatus = 0 ORDER BY fId ASC";
            $rs = $conn->Execute($sql);

            $i           = 0;
            $checkMethod = 0;
            while (!$rs->EOF) {
                $feedbackData[$i] = $rs->fields;

                if (($feedbackData[$i]['fIdentity'] == 2 || $feedbackData[$i]['fIdentity'] == 4) && $checkMethod == 0) { //PID($feedbackData[$i]['fIdentityNumber'])
                    $method = 3;
                } elseif ($feedbackData[$i]['fNote'] == 'REC' && $checkMethod == 0) {
                    $method = 2;
                } else {
                    $checkMethod = 1;
                    $method      = 1;
                }

                $i++;
                $rs->MoveNext();
            }

            //業務
            if ($type == 1) {
                $sql = "SELECT
							sSales AS sales,
							(SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS salesName,
							(SELECT pMobile FROM tPeopleInfo WHERE pId = sSales) AS mobile
						FROM
							tScrivenerSales
						WHERE
							sScrivener = '" . (int) substr($v['storeId'], 2) . "'";
            } else {
                $sql = "SELECT
							bSales AS sales,
							(SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS salesName,
							(SELECT pMobile FROM tPeopleInfo WHERE pId = bSales) AS mobile,
							(SELECT bCategory FROM tBranch WHERE bId = bBranch) AS category
						FROM
							tBranchSales
						WHERE
							bBranch = '" . (int) substr($v['storeId'], 2) . "'";
            }
            $rs = $conn->Execute($sql);

            while (!$rs->EOF) {
                //仲介商類型(1:加盟、2:直營、3:非仲介)
                if ($type == 1) {
                    $category = 3;
                } elseif ($type == 2) {
                    $category = $rs->fields['category'];
                }

                $rs->MoveNext();
            }
            ##

            //寫入資料表
            $sql = "INSERT INTO
						tStoreFeedBackMoneyFrom
					SET
						sStoreId = '" . $storeId . "',
						sStoreCode = '" . $storeCode . "',
						sStoreName = '" . $v['storeName'] . "',
						sMethod = '" . $method . "',
						sCategory = '" . $category . "',
						sType = '" . $type . "',
						sSeason = '" . $sales_season1 . "',
						sEndTime = '" . $formDateStart . "',
						sEndTime2 = '" . $formDateEnd . "',
						sCreatTime = '" . date('Y-m-d H:i:s') . "',
						sEditor = '" . $_SESSION['member_id'] . "',
						sFeedbackAllCase = '" . $feedbackallcase . "',
						sFeedbackMark = '" . $v['bFeedbackMark2'] . "'";
            $conn->Execute($sql);
            $last_id = $conn->Insert_ID();

            $sFeedBackMoneyTotal = 0;
            //寫入回饋案件資料表
            foreach ($v['data'] as $m => $n) {
                $feeddatecat = ($n['bFeedDateCat'] == 1) ? '(月結)' : '(季結)';

                $sql = "INSERT INTO
                            tStoreFeedBackMoneyFrom_Case
                        SET
                            sFromId = '" . $last_id . "',
                            sStoreId = '" . $n['bFBTarget'] . "',
                            sStoreName = '" . $n['bStore'] . "',
                            sEndDate = '" . substr($n['cFeedbackDate'], 0, 10) . "',
                            sCertifiedId = '" . $n['cCertifiedId'] . "',
                            sBuyer = '" . $n['buyer'] . "',
                            sOwner = '" . $n['owner'] . "',
                            sTotalMoney = '" . $n['cTotalMoney'] . "',
                            sCertifiedMoney = '" . $n['cCertifiedMoney'] . "',
                            sFeedBackMoney = '" . $n['cCaseFeedBackMoney'] . "',
                            sFeedDateCat = '" . $feeddatecat . "'";
                $conn->Execute($sql);

                $sFeedBackMoneyTotal += $n['cCaseFeedBackMoney'];
            }

            $sql = "UPDATE tStoreFeedBackMoneyFrom SET sFeedBackMoneyTotal = '" . $sFeedBackMoneyTotal . "' WHERE sId = '" . $last_id . "'";
            $conn->Execute($sql);

            //寫入店家回饋資料表
            if (is_array($feedbackData)) {
                foreach ($feedbackData as $m => $n) {
                    $sql = "INSERT INTO
								tStoreFeedBackMoneyFrom_Account
							SET
								sFromId = '" . $last_id . "',
								sBankMain = '" . $n['fAccountNum'] . "',
								sBankBranch = '" . $n['fAccountNumB'] . "',
								sBankAccountNo = '" . $n['fAccount'] . "',
								sBankAccountName = '" . $n['fAccountName'] . "'";
                    $conn->Execute($sql);
                }
            }

            $data_feedData = null;unset($data_feedData);
        }

        $unlock = null;unset($unlock);
    }
}