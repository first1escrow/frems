<?php
include_once '../configs/config.class.php';
include_once dirname(__DIR__).'/class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
require_once dirname(__DIR__).'/includes/lib/contractBank.php';

/**
 * 2024-09-20
 * 檢查回饋金審核過的回饋是否遭到覆蓋
 */

$sql = "SELECT a.fId,a.fCertifiedId,c.fRId,
            b.cCaseFeedback,b.cCaseFeedback1,b.cCaseFeedback2,b.cCaseFeedback3,
            b.cFeedbackTarget,b.cFeedbackTarget1,b.cFeedbackTarget2,b.cFeedbackTarget3,
            b.cCaseFeedBackMoney,b.cCaseFeedBackMoney1,b.cCaseFeedBackMoney2,b.cCaseFeedBackMoney3,b.cSpCaseFeedBackMoney,
            c.fCategory,c.fCaseFeedback,c.fFeedbackTarget,c.fCaseFeedBackMoney,c.fCaseFeedBackMark,c.fFeedbackStoreId, 
            d.cBranchNum,d.cBranchNum1,d.cBranchNum2,d.cBranchNum3 
            FROM tFeedBackMoneyReview AS a 
            LEFT JOIN tContractCase AS b ON a.fCertifiedId=b.cCertifiedId 
            LEFT JOIN tFeedBackMoneyReviewList AS c ON a.fId=c.fRId 
            LEFT JOIN tContractRealestate AS d ON d.cCertifyId=a.fCertifiedId
            where a.fStatus = 1 AND a.fFail = 0 AND (b.cCaseStatus = 2 OR (b.cCaseStatus > 2 AND cFinishDate3 >= '".date("Y-m-d 00:00:00")."' AND cFinishDate3 <= '".date("Y-m-d 23:59:59")."')) AND c.fDelete = 0
            ORDER BY c.fId DESC LIMIT 2000";
$rs = $conn->Execute($sql) ;

$checkList = [];
$errorReviews = [];
$errorMemo = [];
$tFeedBackMoney = [];
$fCategoryFlag4 = [];
$cSpCaseFeedBackMoneyCheck = [];

if($rs){
    while (!$rs->EOF) {
        if(isset($checkList[$rs->fields['fCertifiedId']]) && $checkList[$rs->fields['fCertifiedId']] != $rs->fields['fId']){
            $rs->MoveNext(); continue;
        }

        if (!empty($rs->fields['cSpCaseFeedBackMoney']) && $rs->fields['cSpCaseFeedBackMoney'] > 0) {
            if(!isset($fCategoryFlag4[$rs->fields['fCertifiedId']])) {
                $fCategoryFlag4[$rs->fields['fCertifiedId']] = $rs->fields['fCategory'];
                $cSpCaseFeedBackMoneyCheck[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
            }
        }

        $tmpCategory = ["1" => "","2" => "1","3" => "2","6" => "3"];
        if($rs->fields['fCategory'] == "1" || $rs->fields['fCategory'] == "2" || $rs->fields['fCategory'] == "3" || $rs->fields['fCategory'] == "6"){
            $tmp = $tmpCategory[$rs->fields['fCategory']];

            if($rs->fields['cBranchNum'.$tmp] > 0 && $rs->fields['fCaseFeedback'] != $rs->fields['cCaseFeedback'.$tmp]){
                $errorReviews[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                $errorMemo[$rs->fields['fCertifiedId']][] = "是否回饋不一致";
            }
            if($rs->fields['cBranchNum'.$tmp] > 0 && $rs->fields['fFeedbackTarget'] > 0 && $rs->fields['fFeedbackTarget'] != $rs->fields['cFeedbackTarget'.$tmp]){
                //20250526 tFeedBackMoneyReviewList的fFeedbackTarget 預設值雖然是代書但值仍可能寫入是0
                $errorReviews[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                $errorMemo[$rs->fields['fCertifiedId']][] = "回饋對象不一致";
            }
            if($rs->fields['cBranchNum'.$tmp] > 0 && $rs->fields['fCaseFeedBackMoney'] != $rs->fields['cCaseFeedBackMoney'.$tmp]){
                $errorReviews[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                $errorMemo[$rs->fields['fCertifiedId']][] = "回饋金額不一致";
            }
        } else if($rs->fields['fCategory'] == "4"){
            unset($cSpCaseFeedBackMoneyCheck[$rs->fields['fCertifiedId']]);

            if($rs->fields['fCaseFeedBackMoney'] != $rs->fields['cSpCaseFeedBackMoney']){
                $errorReviews[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                $errorMemo[$rs->fields['fCertifiedId']][] = "特殊回饋金額不一致";
            }
        } else if($rs->fields['fCategory'] == "5"){
            if(!empty($rs->fields['fCaseFeedBackMark'])){
                if(!isset($tFeedBackMoney[$rs->fields['fCaseFeedBackMark']])){
                    $sql_tFeedBackMoney = "SELECT fId,fCertifiedId,fStoreId,fMoney FROM tFeedBackMoney WHERE fCertifiedId = '".$rs->fields['fCertifiedId']."' AND fDelete = 0";
                    $rs_tFeedBackMoney = $conn->Execute($sql_tFeedBackMoney);
                    if($rs_tFeedBackMoney){
                        while(!$rs_tFeedBackMoney->EOF){
                            $tFeedBackMoney[$rs_tFeedBackMoney->fields['fCertifiedId']][$rs_tFeedBackMoney->fields['fStoreId']] = $rs_tFeedBackMoney->fields['fMoney'];
                            $rs_tFeedBackMoney->MoveNext();
                        }
                        $rs_tFeedBackMoney->Close();
                    }
                }

                if($tFeedBackMoney[$rs->fields['fCertifiedId']][$rs->fields['fFeedbackStoreId']] != $rs->fields['fCaseFeedBackMoney']){
                    $errorMemo[$rs->fields['fCertifiedId']][] = "其他回饋資料不一致";
                    $errorReviews[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                }
            }
        }

        $checkList[$rs->fields['fCertifiedId']] = $rs->fields['fId'];

        $rs->MoveNext();
    }

    if(count($cSpCaseFeedBackMoneyCheck) > 0){
        $cSpCaseFeedBackMoneyCheck = array_unique($cSpCaseFeedBackMoneyCheck);
        foreach ($cSpCaseFeedBackMoneyCheck as $k=>$v){
            $errorReviews[$v] = $v;
            $errorMemo[$v][] = "特殊回饋資料不一致";
        }
    }

    $rs->Close();
}

$x = 0;
$tbl = '';

if(count($errorReviews) > 0) {
    foreach ($errorReviews as $v) {
        $colorIndex = ($x % 2 == 0) ? '' : '#F8ECE9';

        $tbl .= "<tr style='background-color:" . $colorIndex . ";'>";
        $tbl .= "<td><a href='#' onclick='contract(\"" . $v . "\")'>" . $v . "</a></td>";
        $tbl .= "<td>" . implode('<br>', $errorMemo[$v]) . "</td>";
        $tbl .= "</tr>";

        $x++;
    }
} else {
    $tbl = '<tr><td colspan="2">無資料顯示</td></tr>';
}

##

$smarty->assign('tbl',$tbl) ;
$smarty->display('feedbackReviewError.tpl', '', 'report') ;
?>