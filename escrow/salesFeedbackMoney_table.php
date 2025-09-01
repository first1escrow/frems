<?php
include_once dirname(__DIR__) . '/configs/config.class.php';
include_once dirname(__DIR__) . '/class/SmartyMain.class.php';
include_once dirname(__DIR__) . '/openadodb.php';
include_once dirname(__DIR__) . '/session_check.php';
include_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';

$_POST = escapeStr($_POST);
$id    = $_POST['cId'];

$sql = "SELECT
			ci.cCertifiedMoney,
			s.sOffice,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS branch,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS branch1,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS branch2,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum3) AS branch3
		FROM
			tContractIncome AS ci
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId =ci.cCertifiedId
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId = ci.cCertifiedId
		LEFT JOIN
			tScrivener AS s ON s.sId = cs. cScrivener
		WHERE ci.cCertifiedId = '" . $id . "'";

$rs               = $conn->Execute($sql);
$money            = $rs->fields['cCertifiedMoney'];
$scrivener_office = $rs->fields['sOffice'];
$branch_type1     = $rs->fields['branch'];
$branch_type2     = $rs->fields['branch1'];
$branch_type3     = $rs->fields['branch2'];
$branch_type4     = $rs->fields['branch3'];
//業務申請回饋金

$sql = "SELECT
			fId,
			fStatus,
			fNote,
			(SELECT pName FROM tPeopleInfo WHERE pId=fCreator) AS fCreator,
			fApplyTime,
			(SELECT pName FROM tPeopleInfo WHERE pId = fAuditor) AS fAuditor,
			fAuditorTime
		FROM
			tFeedBackMoneyReview WHERE fCertifiedId = '" . $id . "' AND fFail = 0 ORDER BY fId DESC";

$rs          = $conn->Execute($sql);
$i           = 0;
$SalesReview = []; // 初始化 SalesReview 陣列
$delNote     = []; // 初始化 delNote 陣列

while (! $rs->EOF) {
    $j                          = 0;
    $SalesReview[$i]            = $rs->fields;
    $SalesReview[$i]['Status']  = $SalesReview[$i]['fStatus'];
    $SalesReview[$i]['fStatus'] = ($SalesReview[$i]['fStatus'] == 0) ? '申請中' : '已核可'; //0:申請1:核可

    $sql = "SELECT * FROM tFeedBackMoneyReviewList WHERE fCertifiedId = '" . $id . "' AND fRId = '" . $SalesReview[$i]['fId'] . "' ORDER BY fCategory ASC";

    $rs2 = $conn->Execute($sql);
    while (! $rs2->EOF) {
        if ($rs2->fields['fCategory'] == 1) {
            # code...$branch_type1
            $SalesReview[$i]['BranchName']         = $branch_type1;
            $SalesReview[$i]['fCaseFeedback']      = $rs2->fields['fCaseFeedback'];
            $SalesReview[$i]['fFeedbackTarget']    = $rs2->fields['fFeedbackTarget'];
            $SalesReview[$i]['fCaseFeedBackMoney'] = $rs2->fields['fCaseFeedBackMoney'];
        } elseif ($rs2->fields['fCategory'] == 2) {
            $SalesReview[$i]['BranchName2']         = $branch_type2;
            $SalesReview[$i]['fCaseFeedback2']      = $rs2->fields['fCaseFeedback'];
            $SalesReview[$i]['fFeedbackTarget2']    = $rs2->fields['fFeedbackTarget'];
            $SalesReview[$i]['fCaseFeedBackMoney2'] = $rs2->fields['fCaseFeedBackMoney'];
        } elseif ($rs2->fields['fCategory'] == 3) {
            $SalesReview[$i]['BranchName3']         = $branch_type3;
            $SalesReview[$i]['fCaseFeedback3']      = $rs2->fields['fCaseFeedback'];
            $SalesReview[$i]['fFeedbackTarget3']    = $rs2->fields['fFeedbackTarget'];
            $SalesReview[$i]['fCaseFeedBackMoney3'] = $rs2->fields['fCaseFeedBackMoney'];
        } elseif ($rs2->fields['fCategory'] == 6) {
            $SalesReview[$i]['BranchName6']         = $branch_type4;
            $SalesReview[$i]['fCaseFeedback6']      = $rs2->fields['fCaseFeedback'];
            $SalesReview[$i]['fFeedbackTarget6']    = $rs2->fields['fFeedbackTarget'];
            $SalesReview[$i]['fCaseFeedBackMoney6'] = $rs2->fields['fCaseFeedBackMoney'];
        } elseif ($rs2->fields['fCategory'] == 4) {
            # code...
            $SalesReview[$i]['ScrivenerSPFeedMoney'] = $rs2->fields['fCaseFeedBackMoney'];
        } elseif ($rs2->fields['fCategory'] == 5) {
            //  // 2:branch fFeedbackTarget
            if ($rs2->fields['fDelete'] == 0) {
                $target                                            = ($rs2->fields['fFeedbackTarget'] == 1) ? '2' : '1';
                $SalesReview[$i]['data'][$j]                       = getStoreData($target, $rs2->fields['fFeedbackStoreId']);
                $SalesReview[$i]['data'][$j]['fCaseFeedBackMoney'] = $rs2->fields['fCaseFeedBackMoney'];
                $SalesReview[$i]['data'][$j]['fCaseFeedBackNote']  = $rs2->fields['fCaseFeedBackNote'];
                $SalesReview[$i]['data'][$j]['fFeedbackTarget']    = ($rs2->fields['fFeedbackTarget'] == 1) ? '2' : '1';

                $j++;
            } else {
                if ($rs2->fields['fCaseFeedBackMark'] != '') {
                    $target                        = ($rs2->fields['fFeedbackTarget'] == 1) ? 2 : 1;
                    $delData                       = getFeedBackStore($target, $rs2->fields['fFeedbackStoreId']);
                    $delData['fCaseFeedBackMoney'] = $rs2->fields['fCaseFeedBackMoney'];
                    $delData['fType']              = ($rs2->fields['fFeedbackTarget'] == 1) ? '仲介' : '地政士';
                    $delData['fNote']              = $rs2->fields['fCaseFeedBackNote'];

                    array_push($delNote, $delData);
                    unset($delData);
                }

            }

        }
        // $SalesReview[$i]['data'][$j] = $rs2->fields;

        $rs2->MoveNext();
    }

    $i++;
    $rs->MoveNext();
}
##

// print_r($SalesReview);
// die;
##
$smarty->assign('delNote', $delNote);
$smarty->assign('cMoney', $money);
$smarty->assign('scrivener_office', $scrivener_office);
$smarty->assign('id', $id);
$smarty->assign('SalesReview', $SalesReview);
$smarty->display('salesFeedbackMoney_table.inc.tpl', '', 'escrow');
