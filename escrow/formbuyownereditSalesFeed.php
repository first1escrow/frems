<?php
include_once dirname(__DIR__) . '/configs/config.class.php';
include_once dirname(__DIR__) . '/class/SmartyMain.class.php';
include_once dirname(__DIR__) . '/class/advance.class.php';
include_once dirname(__DIR__) . '/class/contract.class.php';
include_once dirname(__DIR__) . '/class/scrivener.class.php';
include_once dirname(__DIR__) . '/class/member.class.php';
include_once dirname(__DIR__) . '/class/brand.class.php';
include_once dirname(__DIR__) . '/class/getAddress.php';
include_once dirname(__DIR__) . '/class/getBank.php';
include_once dirname(__DIR__) . '/class/intolog.php';
include_once dirname(__DIR__) . '/includes/escrow/contractbank.php';
include_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';
include_once dirname(__DIR__) . '/web_addr.php';
include_once dirname(__DIR__) . '/openadodb.php';
include_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';

$_POST = escapeStr($_POST);

// Safely read inputs from POST or GET (avoid undefined index warnings)
$id = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : ''); //保號

$rid = isset($_POST['rId']) ? $_POST['rId'] : (isset($_GET['rId']) ? $_GET['rId'] : '');

$cat = isset($_POST['cat']) ? $_POST['cat'] : (isset($_GET['cat']) ? $_GET['cat'] : '');

$certifyDate = isset($_POST['certifyDate']) ? $_POST['certifyDate'] : (isset($_GET['certifyDate']) ? $_GET['certifyDate'] : ''); //履保費出款日

// if incoming request asked to add but no rId supplied, fall back to search
if ($cat == 'add' && $rid == '') {
    $cat = 'search';
}
##

$sql = "SELECT * FROM  tFeedBackMoneyReview WHERE fCertifiedId = '" . $id . "' AND fId = '" . $rid . "'";
$rs  = $conn->Execute($sql);

// Ensure $review is always an array, even if no data is found
$review = $rs->fields;
if (! is_array($review)) {
    $review = [
        'fId'   => '',
        'fNote' => '',
    ]; // Provide default structure expected by template
}
##

//save
if ($cat == 'add') {
    $sql    = "SELECT I.`cTotalMoney`, I.`cCertifiedMoney`, S.`cScrivener` FROM `tContractIncome` AS I  LEFT JOIN `tContractScrivener` AS S ON I.cCertifiedId = S.cCertifiedId  WHERE I.cCertifiedId = '" . $id . "'";
    $res    = $conn->Execute($sql);
    $income = $res->fields;

    $sql = "INSERT INTO tFeedBackMoneyReview (
				fCertifiedId,
				fNote,
			    fTotalMoney,
			    fCertifiedMoney,
				fCreator,
				fApplyTime
			)VALUES(
				'" . $id . "',
				'" . (isset($_POST['note']) ? $_POST['note'] : '') . "',
				'" . $income['cTotalMoney'] . "',
				'" . $income['cCertifiedMoney'] . "',
				'" . $_SESSION['member_id'] . "',
				'" . date('Y-m-d H:i:s') . "'
			)";
    $conn->Execute($sql);
    $rid = $conn->Insert_ID();

    #主要地政士回饋帳戶
    isset($_POST['fFeedbackDataId']) ? $feedbackDataId = $_POST['fFeedbackDataId'] : $feedbackDataId = 0;
    if ($feedbackDataId != 0) {
        $sql = 'SELECT * FROM tFeedBackData WHERE fType = 1 AND fStoreId = ' . $income['cScrivener'] . ' AND fId =' . $feedbackDataId;
        $rs  = $conn->Execute($sql);

        if ($rs->RecordCount() == 0) {
            $feedbackDataId = 0;
        }
    }
    //第一間店
    $sql = "INSERT INTO tFeedBackMoneyReviewList (
				fCertifiedId,
				fRId,
				fCategory,
				fCaseFeedback,
				fFeedbackTarget,
				fFeedbackStoreId,";
    if (isset($_POST['cFeedbackTarget_1']) && $_POST['cFeedbackTarget_1'] == 2 and $feedbackDataId != 0) {
        $sql .= "fFeedbackDataId,";
    }
    $sql .= "fCaseFeedBackMoney
		)VALUES(
				'" . $id . "',
				'" . $rid . "',
				'1',
				'" . (isset($_POST['cCaseFeedback_1']) ? $_POST['cCaseFeedback_1'] : '0') . "',
				'" . (isset($_POST['cFeedbackTarget_1']) ? $_POST['cFeedbackTarget_1'] : '1') . "',
				'" . (isset($_POST['cFeedbackStoreId_1']) ? $_POST['cFeedbackStoreId_1'] : '') . "',";
    if (isset($_POST['cFeedbackTarget_1']) && $_POST['cFeedbackTarget_1'] == 2 and $feedbackDataId != 0) {
        $sql .= "" . $feedbackDataId . ",";
    }
    $sql .= "'" . (isset($_POST['cCaseFeedBackMoney_1']) ? $_POST['cCaseFeedBackMoney_1'] : '') . "'
		)";
    $conn->Execute($sql);

    $storeId1 = isset($_POST['cFeedbackStoreId_1']) ? $_POST['cFeedbackStoreId_1'] : null;
    if ($storeId1 !== null && isset($_POST['individualMoney'][$storeId1]) && is_array($_POST['individualMoney'][$storeId1]) && count($_POST['individualMoney'][$storeId1]) > 0) {
        foreach ($_POST['individualMoney'][$storeId1] as $key => $value) {
            //第一間店個案回饋
            $branchId        = isset($_POST['individualBranchId'][$storeId1][$key]) ? $_POST['individualBranchId'][$storeId1][$key] : '';
            $individualId    = isset($_POST['individualId'][$storeId1][$key]) ? $_POST['individualId'][$storeId1][$key] : '';
            $individualMoney = isset($_POST['individualMoney'][$storeId1][$key]) ? $_POST['individualMoney'][$storeId1][$key] : '';
            $sql             = "INSERT INTO tFeedBackMoneyReviewList (
						fCertifiedId,
						fRId,
						fCategory,
						fCaseFeedback,
						fFeedbackTarget,
						fFeedbackStoreId,
						fIndividualId,
						fCaseFeedBackMoney
				)VALUES(
						'" . $id . "',
						'" . $rid . "',
						'7',
						'1',
						'3',
						'" . $branchId . "',
						'" . $individualId . "',
						'" . $individualMoney . "'
				);";
            $conn->Execute($sql);
        }
    }

    //第二間店
    $sql = "INSERT INTO tFeedBackMoneyReviewList (
			fCertifiedId,
			fRId,
			fCategory,
			fCaseFeedback,
			fFeedbackTarget,
			fFeedbackStoreId,";
    if ($_POST['cFeedbackTarget_2'] == 2 and $feedbackDataId != 0) {
        $sql .= "fFeedbackDataId,";
    }
    $sql .= "fCaseFeedBackMoney
		)VALUES(
			'" . $id . "',
			'" . $rid . "',
			'2',
			'" . $_POST['cCaseFeedback_2'] . "',
			'" . $_POST['cFeedbackTarget_2'] . "',
			'" . $_POST['cFeedbackStoreId_2'] . "',";
    if ($_POST['cFeedbackTarget_2'] == 2 and $feedbackDataId != 0) {
        $sql .= "" . $feedbackDataId . ",";
    }
    $sql .= "'" . (isset($_POST['cCaseFeedBackMoney_2']) ? $_POST['cCaseFeedBackMoney_2'] : '') . "'
		)";
    $conn->Execute($sql);

    $storeId2 = isset($_POST['cFeedbackStoreId_2']) ? $_POST['cFeedbackStoreId_2'] : null;
    if ($storeId2 !== null && isset($_POST['individualMoney'][$storeId2]) && is_array($_POST['individualMoney'][$storeId2]) && count($_POST['individualMoney'][$storeId2]) > 0) {
        foreach ($_POST['individualMoney'][$storeId2] as $key => $value) {
            //第二間店個案回饋
            $branchId        = isset($_POST['individualBranchId'][$storeId2][$key]) ? $_POST['individualBranchId'][$storeId2][$key] : '';
            $individualId    = isset($_POST['individualId'][$storeId2][$key]) ? $_POST['individualId'][$storeId2][$key] : '';
            $individualMoney = isset($_POST['individualMoney'][$storeId2][$key]) ? $_POST['individualMoney'][$storeId2][$key] : '';
            $sql             = "INSERT INTO tFeedBackMoneyReviewList (
					fCertifiedId,
					fRId,
					fCategory,
					fCaseFeedback,
					fFeedbackTarget,
					fFeedbackStoreId,
					fIndividualId,
					fCaseFeedBackMoney
			)VALUES(
					'" . $id . "',
					'" . $rid . "',
					'7',
					'1',
					'3',
					'" . $branchId . "',
					'" . $individualId . "',
					'" . $individualMoney . "'
				)";

            $conn->Execute($sql);
        }
    }

    //第三間店
    $sql = "INSERT INTO tFeedBackMoneyReviewList (
			fCertifiedId,
			fRId,
			fCategory,
			fCaseFeedback,
			fFeedbackTarget,
			fFeedbackStoreId,";
    if ($_POST['cFeedbackTarget_3'] == 2 and $feedbackDataId != 0) {
        $sql .= "fFeedbackDataId,";
    }
    $sql .= "fCaseFeedBackMoney
		)VALUES(
			'" . $id . "',
			'" . $rid . "',
			'3',
			'" . $_POST['cCaseFeedback_3'] . "',
			'" . $_POST['cFeedbackTarget_3'] . "',
			'" . $_POST['cFeedbackStoreId_3'] . "',";
    if ($_POST['cFeedbackTarget_3'] == 2 and $feedbackDataId != 0) {
        $sql .= "" . $feedbackDataId . ",";
    }
    $sql .= "'" . (isset($_POST['cCaseFeedBackMoney_3']) ? $_POST['cCaseFeedBackMoney_3'] : '') . "'
		)";
    $conn->Execute($sql);

    $storeId3 = isset($_POST['cFeedbackStoreId_3']) ? $_POST['cFeedbackStoreId_3'] : null;
    if ($storeId3 !== null && isset($_POST['individualMoney'][$storeId3]) && is_array($_POST['individualMoney'][$storeId3]) && count($_POST['individualMoney'][$storeId3]) > 0) {
        foreach ($_POST['individualMoney'][$storeId3] as $key => $value) {
            //第三間店個案回饋
            $branchId        = isset($_POST['individualBranchId'][$storeId3][$key]) ? $_POST['individualBranchId'][$storeId3][$key] : '';
            $individualId    = isset($_POST['individualId'][$storeId3][$key]) ? $_POST['individualId'][$storeId3][$key] : '';
            $individualMoney = isset($_POST['individualMoney'][$storeId3][$key]) ? $_POST['individualMoney'][$storeId3][$key] : '';
            $sql             = "INSERT INTO tFeedBackMoneyReviewList (
					fCertifiedId,
					fRId,
					fCategory,
					fCaseFeedback,
					fFeedbackTarget,
					fFeedbackStoreId,
					fIndividualId,
					fCaseFeedBackMoney
			)VALUES(
					'" . $id . "',
					'" . $rid . "',
					'7',
					'1',
					'3',
					'" . $branchId . "',
					'" . $individualId . "',
					'" . $individualMoney . "'
			);";

            $conn->Execute($sql);
        }

    }
    //第四間店
    $sql = "INSERT INTO tFeedBackMoneyReviewList (
			fCertifiedId,
			fRId,
			fCategory,
			fCaseFeedback,
			fFeedbackTarget,
			fFeedbackStoreId,";
    if ($_POST['cFeedbackTarget_6'] == 2 and $feedbackDataId != 0) {
        $sql .= "fFeedbackDataId,";
    }
    $sql .= "fCaseFeedBackMoney
		)VALUES(
			'" . $id . "',
			'" . $rid . "',
			'6',
			'" . $_POST['cCaseFeedback_6'] . "',
			'" . $_POST['cFeedbackTarget_6'] . "',
			'" . $_POST['cFeedbackStoreId_6'] . "',";
    if ($_POST['cFeedbackTarget_6'] == 2 and $feedbackDataId != 0) {
        $sql .= "" . $feedbackDataId . ",";
    }
    $sql .= "'" . (isset($_POST['cCaseFeedBackMoney_6']) ? $_POST['cCaseFeedBackMoney_6'] : '') . "'
		)";
    $conn->Execute($sql);

    $storeId6 = isset($_POST['cFeedbackStoreId_6']) ? $_POST['cFeedbackStoreId_6'] : null;
    if ($storeId6 !== null && isset($_POST['individualMoney'][$storeId6]) && is_array($_POST['individualMoney'][$storeId6]) && count($_POST['individualMoney'][$storeId6]) > 0) {
        foreach ($_POST['individualMoney'][$storeId6] as $key => $value) {
            //第四間店個案回饋
            $branchId        = isset($_POST['individualBranchId'][$storeId6][$key]) ? $_POST['individualBranchId'][$storeId6][$key] : '';
            $individualId    = isset($_POST['individualId'][$storeId6][$key]) ? $_POST['individualId'][$storeId6][$key] : '';
            $individualMoney = isset($_POST['individualMoney'][$storeId6][$key]) ? $_POST['individualMoney'][$storeId6][$key] : '';
            $sql             = "INSERT INTO tFeedBackMoneyReviewList (
					fCertifiedId,
					fRId,
					fCategory,
					fCaseFeedback,
					fFeedbackTarget,
					fFeedbackStoreId,
					fIndividualId,
					fCaseFeedBackMoney
			)VALUES(
					'" . $id . "',
					'" . $rid . "',
					'7',
					'1',
					'3',
					'" . $branchId . "',
					'" . $individualId . "',
					'" . $individualMoney . "'
			);";
            $conn->Execute($sql);
        }

    }
    //地政士特殊回饋
    if ($_POST['scrivenerId'] != 0 && $_POST['scrivenerId'] != '') {
        $sql = "INSERT INTO tFeedBackMoneyReviewList (
				fCertifiedId,
				fRId,
				fCategory,
				fCaseFeedback,
				fFeedbackTarget,
				fFeedbackStoreId,";
        if ($feedbackDataId != 0) {
            $sql .= "fFeedbackDataId,";
        }
        $sql .= "fCaseFeedBackMoney
			)VALUES(
				'" . $id . "',
				'" . $rid . "',
				'4',
				'1',
				'2',
				'" . $_POST['scrivenerId'] . "',";
        if ($feedbackDataId != 0) {
            $sql .= "" . $feedbackDataId . ",";
        }
        $sql .= "'" . $_POST['cSpCaseFeedBackMoney'] . "'
			)";
        $conn->Execute($sql);
    }

    //其他回饋
    isset($_POST['fOtherFeedbackDataId']) ? $otherFeedbackDataId = $_POST['fOtherFeedbackDataId'] : $otherFeedbackDataId = 0;
    $newOtherIndex                                               = isset($_POST['newOtherIndex']) ? (int) $_POST['newOtherIndex'] : -1;
    for ($i = 0; $i <= $newOtherIndex; $i++) {
        if (isset($_POST['newotherFeedMoney' . $i]) && $_POST['newotherFeedMoney' . $i] > 0) {
            $_POST['newotherFeedType' . $i] = (isset($_POST['newotherFeedType' . $i]) && $_POST['newotherFeedType' . $i] == 2) ? '1' : '2'; //原資料是相反的，所以要調整

            //回饋代書 & 指定回饋帳戶 確認帳戶是不是屬於該代書
            if ($_POST['newotherFeedType' . $i] == 2 and $otherFeedbackDataId != 0) {
                $sql = 'SELECT * FROM tFeedBackData WHERE fType = 1 AND fStoreId = ' . $_POST['newotherFeedstoreId' . $i] . ' AND fId =' . $otherFeedbackDataId;
                $rs  = $conn->Execute($sql);
                if ($rs->RecordCount() == 0) {
                    $otherFeedbackDataId = 0;
                }
            }
            $sql = "INSERT INTO tFeedBackMoneyReviewList (
				fCertifiedId,
				fRId,
				fCategory,
				fCaseFeedback,
				fFeedbackTarget,
				fFeedbackStoreId,";

            if ($_POST['newotherFeedType' . $i] == 2 and $otherFeedbackDataId != 0) {
                $sql .= "fFeedbackDataId,";
            }
            $sql .= "fCaseFeedBackMoney,
                fCaseFeedBackNote,
                fCaseFeedBackMark
			)VALUES(
				'" . $id . "',
				'" . $rid . "',
				'5',
				'0',
				'" . $_POST['newotherFeedType' . $i] . "',
				'" . $_POST['newotherFeedstoreId' . $i] . "',";

            if ($_POST['newotherFeedType' . $i] == 2 and $otherFeedbackDataId != 0) {
                $sql .= "'" . $otherFeedbackDataId . "',";
            }
            $sql .= "'" . $_POST['newotherFeedMoney' . $i] . "',
                '" . $_POST['newotherFeedMoneyNote' . $i] . "',
                '" . $_POST['oId' . $i] . "'
			)";
            $conn->Execute($sql);
        }
    }

    //原本有資料刪除的寫入ID
    $sql = "UPDATE tFeedBackMoneyReviewList SET fRId = '" . $rid . "' WHERE fCertifiedId = '" . $id . "' AND fRId = 0";
    $conn->Execute($sql);

    //20231110 更新送審資料
    $paybycase = new First1\V1\PayByCase\PayByCase;

    $paybycase->salesConfirmList($id);
    $paybycase = null;unset($paybycase);

    $msg = '申請成功';
    $cat = 'save';
} elseif ($cat == 'save') {
    $sql = "UPDATE
				tFeedBackMoneyReviewList
			SET
				fCaseFeedback = '" . (isset($_POST['cCaseFeedback_1']) ? $_POST['cCaseFeedback_1'] : '0') . "',
				fFeedbackTarget = '" . (isset($_POST['cFeedbackTarget_1']) ? $_POST['cFeedbackTarget_1'] : '1') . "',
				fFeedbackStoreId = '" . (isset($_POST['cFeedbackStoreId_1']) ? $_POST['cFeedbackStoreId_1'] : '') . "',
				fCaseFeedBackMoney  = '" . (isset($_POST['cCaseFeedBackMoney_1']) ? $_POST['cCaseFeedBackMoney_1'] : '0') . "'
			WHERE
				fCategory = 1 AND fCertifiedId ='" . $id . "' AND fRId = '" . $rid . "'";
    $conn->Execute($sql);

    $sql = "UPDATE
				tFeedBackMoneyReviewList
			SET
				fCaseFeedback = '" . (isset($_POST['cCaseFeedback_2']) ? $_POST['cCaseFeedback_2'] : '0') . "',
				fFeedbackTarget = '" . (isset($_POST['cFeedbackTarget_2']) ? $_POST['cFeedbackTarget_2'] : '1') . "',
				fFeedbackStoreId = '" . (isset($_POST['cFeedbackStoreId_2']) ? $_POST['cFeedbackStoreId_2'] : '') . "',
				fCaseFeedBackMoney  = '" . (isset($_POST['cCaseFeedBackMoney_2']) ? $_POST['cCaseFeedBackMoney_2'] : '0') . "'
			WHERE
				fCategory = 2 AND fCertifiedId ='" . $id . "' AND fRId = '" . $rid . "'";
    $conn->Execute($sql);

    $sql = "UPDATE
				tFeedBackMoneyReviewList
			SET
				fCaseFeedback = '" . (isset($_POST['cCaseFeedback_3']) ? $_POST['cCaseFeedback_3'] : '0') . "',
				fFeedbackTarget = '" . (isset($_POST['cFeedbackTarget_3']) ? $_POST['cFeedbackTarget_3'] : '1') . "',
				fFeedbackStoreId = '" . (isset($_POST['cFeedbackStoreId_3']) ? $_POST['cFeedbackStoreId_3'] : '') . "',
				fCaseFeedBackMoney  = '" . (isset($_POST['cCaseFeedBackMoney_3']) ? $_POST['cCaseFeedBackMoney_3'] : '0') . "'
			WHERE
				fCategory = 3 AND fCertifiedId ='" . $id . "' AND fRId = '" . $rid . "'";
    $conn->Execute($sql);

    $sql = "UPDATE
				tFeedBackMoneyReviewList
			SET
				fCaseFeedback = '" . (isset($_POST['cCaseFeedback_6']) ? $_POST['cCaseFeedback_6'] : '0') . "',
				fFeedbackTarget = '" . (isset($_POST['cFeedbackTarget_6']) ? $_POST['cFeedbackTarget_6'] : '1') . "',
				fFeedbackStoreId = '" . (isset($_POST['cFeedbackStoreId_6']) ? $_POST['cFeedbackStoreId_6'] : '') . "',
				fCaseFeedBackMoney  = '" . (isset($_POST['cCaseFeedBackMoney_6']) ? $_POST['cCaseFeedBackMoney_6'] : '0') . "'
			WHERE
				fCategory = 6 AND fCertifiedId ='" . $id . "' AND fRId = '" . $rid . "'";
    $conn->Execute($sql);

    $sql = "UPDATE
				tFeedBackMoneyReviewList
			SET
				fCaseFeedBackMoney = '" . (isset($_POST['cSpCaseFeedBackMoney']) ? $_POST['cSpCaseFeedBackMoney'] : '0') . "'
			WHERE
				fCategory = 4 AND fCertifiedId ='" . $id . "' AND fRId = '" . $rid . "'";
    $conn->Execute($sql);

    //軟刪其他回饋對象
    $sql = "UPDATE
				tFeedBackMoneyReviewList
			SET
				fDelete = '1'
			WHERE
				fCategory = 5 AND fCertifiedId ='" . $id . "' AND fRId = '" . $rid . "'";
    $conn->Execute($sql);

    for ($i = 0; $i <= $_POST['newOtherIndex']; $i++) {
        if ($_POST['newotherFeedMoney' . $i] > 0) {
            $_POST['newotherFeedType' . $i] = ($_POST['newotherFeedType' . $i] == 2) ? '1' : '2'; //原資料是相反的，所以要調整

            $sql = "INSERT INTO tFeedBackMoneyReviewList (
						fCertifiedId,
						fRId,
						fCategory,
						fCaseFeedback,
						fFeedbackTarget,
						fFeedbackStoreId,
						fCaseFeedBackMoney,
						fCaseFeedBackNote,
						fCaseFeedBackMark
					)VALUES(
						'" . $id . "',
						'" . $rid . "',
						'5',
						'0',
						'" . $_POST['newotherFeedType' . $i] . "',
						'" . $_POST['newotherFeedstoreId' . $i] . "',
						'" . $_POST['newotherFeedMoney' . $i] . "',
						'" . $_POST['newotherFeedMoneyNote' . $i] . "',
						'" . $_POST['oId' . $i] . "'
					)";
            $conn->Execute($sql);
        }
    }

    $sql = "UPDATE tFeedBackMoneyReview SET fNote = '" . (isset($_POST['note']) ? $_POST['note'] : '') . "' WHERE fId = '" . $rid . "'";
    $conn->Execute($sql);

    //20231110 更新送審資料
    $paybycase = new First1\V1\PayByCase\PayByCase;

    $paybycase->salesConfirmList($id);
    $paybycase = null;unset($paybycase);
    $msg       = '修改成功';
}
##

$contract  = new Contract();
$scrivener = new Scrivener();

$data_case        = $contract->GetContract($id);
$data_realstate   = $contract->GetRealstate($id);
$data_income      = $contract->GetIncome($id);
$data_scrivener   = $contract->GetScrivener($id);
$scrivenerDetail  = $scrivener->GetScrivenerInfo($data_scrivener["cScrivener"]);
$scrivenerAccount = $scrivener->GetScrivenerFeedbackBank($data_scrivener["cScrivener"]); //回饋帳戶

##

//合契
$store_cooperation = [];

//branch 1
$storeCount = 1;
$sql        = 'SELECT
		(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand'] . '") as brandName,
		b.bName,
		b.bStore,
		b.bCategory,
		b.bCooperationHas
	FROM
		tBranch AS b
	WHERE
		bId=' . $data_realstate['cBranchNum'] . '
	ORDER BY
		bId
	ASC';

$rs = $conn->Execute($sql);

$sql = 'SELECT
    		f.fIndividualId,
			(SELECT bStore FROM `tBranch` WHERE bId = f.fIndividualId ) AS individualName,
			f.fMoney
		FROM `tFeedBackMoney` AS f
		WHERE f.fCertifiedId =' . $id . ' AND f.fType = 3 AND fStoreId = ' . $data_realstate['cBranchNum'] . ' AND fDelete = 0
		';
$tFeedBackMoney = $conn->GetAll($sql);

$store[$storeCount]['bId']            = $data_realstate['cBranchNum'];
$store[$storeCount]['brand']          = $rs->fields['brandName'];
$store[$storeCount]['branch']         = $rs->fields['bStore'];
$store[$storeCount]['cooperationHas'] = $rs->fields['bCooperationHas'];
$store[$storeCount]['individual']     = []; // Initialize to ensure key exists
foreach ($tFeedBackMoney as $fb) {
    $store[$storeCount]['individual'][] = [
        'individualName'  => $fb['individualName'],
        'individualMoney' => $fb['fMoney'],
        'individualId'    => $fb['fIndividualId'],
    ];
}

$feedBackScrivener = 0;
if ($cat == 'search') {
    $store[$storeCount]['feedbackmoney']  = $data_case['cCaseFeedBackMoney'];
    $store[$storeCount]['caseFeedback0']  = ($data_case['cCaseFeedback'] == 0) ? 'checked=checked' : '';
    $store[$storeCount]['caseFeedback1']  = ($data_case['cCaseFeedback'] == 1) ? 'checked=checked' : '';
    $store[$storeCount]['caseFeedTarget'] = $data_case['cFeedbackTarget'];
    if ($store[$storeCount]['caseFeedTarget'] == 2) { //回饋對象 地政士 回饋
        $feedBackScrivener = 1;
    }
} else {
    $store[$storeCount]['caseFeedback0']  = 'checked=checked';
    $store[$storeCount]['caseFeedTarget'] = ($data_realstate['cBranchNum'] == 505) ? 2 : 1; //非仲介成交回饋給地政士
}

$store_cooperation[] = $store[$storeCount]['cooperationHas'];

$storeCount++;

//branch 2
if ($data_realstate['cBranchNum1'] > 0) {
    $sql = 'SELECT
			(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand1'] . '") as brandName,
			b.bName,
			b.bStore,
			b.bCategory,
			b.bCooperationHas
		FROM
			tBranch AS b
		WHERE
			bId=' . $data_realstate['cBranchNum1'] . '
		ORDER BY
			bId
		ASC';
    $rs = $conn->Execute($sql);

    $sql = 'SELECT
    		f.fIndividualId,
			(SELECT bStore FROM `tBranch` WHERE bId = f.fIndividualId ) AS individualName,
			f.fMoney
		FROM `tFeedBackMoney` AS f
		WHERE f.fCertifiedId =' . $id . ' AND f.fType = 3 AND fStoreId = ' . $data_realstate['cBranchNum1'] . ' AND fDelete = 0
		';
    $tFeedBackMoney = $conn->GetAll($sql);

    $store[$storeCount]['bId']            = $data_realstate['cBranchNum1'];
    $store[$storeCount]['brand']          = $rs->fields['brandName'];
    $store[$storeCount]['branch']         = $rs->fields['bStore'];
    $store[$storeCount]['cooperationHas'] = $rs->fields['bCooperationHas'];
    $store[$storeCount]['individual']     = []; // Initialize to ensure key exists
    foreach ($tFeedBackMoney as $fb) {
        $store[$storeCount]['individual'][] = [
            'individualName'  => $fb['individualName'],
            'individualMoney' => $fb['fMoney'],
            'individualId'    => $fb['fIndividualId'],
        ];
    }

    if ($cat == 'search') {
        $store[$storeCount]['feedbackmoney']  = $data_case['cCaseFeedBackMoney1'];
        $store[$storeCount]['caseFeedback0']  = ($data_case['cCaseFeedback1'] == 0) ? 'checked=checked' : '';
        $store[$storeCount]['caseFeedback1']  = ($data_case['cCaseFeedback1'] == 1) ? 'checked=checked' : '';
        $store[$storeCount]['caseFeedTarget'] = $data_case['cFeedbackTarget1'];
        if ($store[$storeCount]['caseFeedTarget'] == 2) { //回饋對象 地政士 回饋
            $feedBackScrivener = 1;
        }
    } else {
        $store[$storeCount]['caseFeedback0']  = 'checked=checked';
        $store[$storeCount]['caseFeedTarget'] = ($data_realstate['cBranchNum1'] == 505) ? 2 : 1; //非仲介成交回饋給地政士
    }

    $store_cooperation[] = $store[$storeCount]['cooperationHas'];

    $storeCount++;
}

//branch 3
if ($data_realstate['cBranchNum2'] > 0) {
    $sql = 'SELECT
			(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand2'] . '") as brandName,
			b.bName,
			b.bStore,
			b.bCategory,
			b.bCooperationHas
		FROM
			tBranch AS b
		WHERE
			bId=' . $data_realstate['cBranchNum2'] . '
		ORDER BY
			bId
		ASC';

    $rs = $conn->Execute($sql);

    $sql = 'SELECT
    		f.fIndividualId,
			(SELECT bStore FROM `tBranch` WHERE bId = f.fIndividualId ) AS individualName,
			f.fMoney
		FROM `tFeedBackMoney` AS f
		WHERE f.fCertifiedId =' . $id . ' AND f.fType = 3 AND fStoreId = ' . $data_realstate['cBranchNum2'] . ' AND fDelete = 0
		';
    $tFeedBackMoney = $conn->GetAll($sql);

    $store[$storeCount]['bId'] = $data_realstate['cBranchNum2'];

    $store[$storeCount]['brand']          = $rs->fields['brandName'];
    $store[$storeCount]['branch']         = $rs->fields['bStore'];
    $store[$storeCount]['cooperationHas'] = $rs->fields['bCooperationHas'];
    $store[$storeCount]['individual']     = []; // Initialize to ensure key exists
    foreach ($tFeedBackMoney as $fb) {
        $store[$storeCount]['individual'][] = [
            'individualName'  => $fb['individualName'],
            'individualMoney' => $fb['fMoney'],
            'individualId'    => $fb['fIndividualId'],
        ];
    }

    if ($cat == 'search') {
        $store[$storeCount]['feedbackmoney']  = $data_case['cCaseFeedBackMoney2'];
        $store[$storeCount]['caseFeedback0']  = ($data_case['cCaseFeedback2'] == 0) ? 'checked=checked' : '';
        $store[$storeCount]['caseFeedback1']  = ($data_case['cCaseFeedback2'] == 1) ? 'checked=checked' : '';
        $store[$storeCount]['caseFeedTarget'] = $data_case['cFeedbackTarget2'];
        if ($store[$storeCount]['caseFeedTarget'] == 2) { //回饋對象 地政士 回饋
            $feedBackScrivener = 1;
        }
    } else {
        $store[$storeCount]['caseFeedback0']  = 'checked=checked';
        $store[$storeCount]['caseFeedTarget'] = ($data_realstate['cBranchNum2'] == 505) ? 2 : 1; //非仲介成交回饋給地政士
    }

    $store_cooperation[] = $store[$storeCount]['cooperationHas'];

    $storeCount++;
}

//branch 4
if ($data_realstate['cBranchNum3'] > 0) {
    $sql = 'SELECT
			(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand3'] . '") as brandName,
			b.bName,
			b.bStore,
			b.bCategory,
			b.bCooperationHas
		FROM
			tBranch AS b
		WHERE
			bId=' . $data_realstate['cBranchNum3'] . '
		ORDER BY
			bId
		ASC';
    $rs = $conn->Execute($sql);

    $sql = 'SELECT
    		f.fIndividualId,
			(SELECT bStore FROM `tBranch` WHERE bId = f.fIndividualId ) AS individualName,
			f.fMoney
		FROM `tFeedBackMoney` AS f
		WHERE f.fCertifiedId =' . $id . ' AND f.fType = 3 AND fStoreId = ' . $data_realstate['cBranchNum3'] . ' AND fDelete = 0
		';
    $tFeedBackMoney = $conn->Execute($sql);

    $store[6]['bId'] = $data_realstate['cBranchNum3'];

    $store[6]['brand']          = $rs->fields['brandName'];
    $store[6]['branch']         = $rs->fields['bStore'];
    $store[6]['cooperationHas'] = $rs->fields['bCooperationHas'];
    $store[6]['individual']     = []; // Initialize to ensure key exists
    foreach ($tFeedBackMoney as $fb) {
        $store[6]['individual'][] = [
            'individualName'  => $fb['individualName'],
            'individualMoney' => $fb['fMoney'],
            'individualId'    => $fb['fIndividualId'],
        ];
    }

    if ($cat == 'search') {
        $store[6]['feedbackmoney']  = $data_case['cCaseFeedBackMoney3'];
        $store[6]['caseFeedback0']  = ($data_case['cCaseFeedback3'] == 0) ? 'checked=checked' : '';
        $store[6]['caseFeedback1']  = ($data_case['cCaseFeedback3'] == 1) ? 'checked=checked' : '';
        $store[6]['caseFeedTarget'] = ($data_case['cFeedbackTarget3'] == 2) ? 2 : 1;
        if ($store[$storeCount]['caseFeedTarget'] == 2) { //回饋對象 地政士 回饋
            $feedBackScrivener = 1;
        }
    } else {
        $store[6]['caseFeedback0']  = 'checked=checked';
        $store[6]['caseFeedTarget'] = ($data_realstate['cBranchNum3'] == 505) ? 2 : 1; //非仲介成交回饋給地政士
    }

    $store_cooperation[] = $store[6]['cooperationHas'];

    $storeCount++;
}

//
$spRecall = (
    (isset($data_case['cScrivenerSpRecall']) ? (int) $data_case['cScrivenerSpRecall'] : 0)
     + (isset($data_case['cBranchScrRecall']) ? (int) $data_case['cBranchScrRecall'] : 0)
     + (isset($data_case['cBranchScrRecall1']) ? (int) $data_case['cBranchScrRecall1'] : 0)
     + (isset($data_case['cBranchScrRecall2']) ? (int) $data_case['cBranchScrRecall2'] : 0)
     + (isset($data_case['cBranchScrRecall3']) ? (int) $data_case['cBranchScrRecall3'] : 0)
);
$chekcsp               = ($spRecall > 0) ? 1 : 0;
$feedBackScrivener     = (int) $feedBackScrivener + $chekcsp;
$otherScrivenerAccount = [];
if ($cat == 'search') {
    //scrivener
    $scrivenerDetail['cSpCaseFeedBackMoney'] = $data_case['cSpCaseFeedBackMoney'];

    //其他回饋對象
    $otherFeed2 = getFeedBackMoney($id);

    for ($i = 0; $i < count($otherFeed2); $i++) {
        $otherFeed2[$i]['id'] = $otherFeed2[$i]['fId'];
        if ($otherFeed2[$i]['fType'] == 1) {
            $otherScrivenerAccount = $scrivener->GetScrivenerFeedbackBank($otherFeed2[$i]['fStoreId']); //回饋帳戶
        }
        unset($otherFeed2[$i]['fId']);
    }

    $cat = 'add';
} else if ($rid) {
    $sql = "SELECT * FROM tFeedBackMoneyReviewList WHERE fCertifiedId = '" . $id . "' AND fRId = '" . $rid . "'  ORDER BY fId ASC"; //AND fDelete = 0
    $rs  = $conn->Execute($sql);

    $i       = 0;
    $delNote = [];
    while (! $rs->EOF) {
        if ($rs->fields['fDelete'] == 0) {
            if ($rs->fields['fCategory'] <= 3 || $rs->fields['fCategory'] == 6) { //仲介回饋
                if (is_array($store[$rs->fields['fCategory']])) {
                    $store[$rs->fields['fCategory']]['feedbackmoney']  = $rs->fields['fCaseFeedBackMoney'];
                    $store[$rs->fields['fCategory']]['caseFeedback0']  = ($rs->fields['fCaseFeedback'] == 0) ? 'checked=checked' : '';
                    $store[$rs->fields['fCategory']]['caseFeedback1']  = ($rs->fields['fCaseFeedback'] == 1) ? 'checked=checked' : '';
                    $store[$rs->fields['fCategory']]['caseFeedTarget'] = $rs->fields['fFeedbackTarget'];
                }

            } elseif ($rs->fields['fCategory'] == 4) { //地政士回饋
                $scrivenerDetail['cSpCaseFeedBackMoney'] = $rs->fields['fCaseFeedBackMoney'];
            } elseif ($rs->fields['fCategory'] == 5) { //其他回饋
                $otherFeed2[$i]['fNote']    = $rs->fields['fCaseFeedBackNote'];
                $otherFeed2[$i]['fMoney']   = $rs->fields['fCaseFeedBackMoney'];
                $otherFeed2[$i]['fStoreId'] = $rs->fields['fFeedbackStoreId'];
                $otherFeed2[$i]['fType']    = ($rs->fields['fFeedbackTarget'] == 1) ? 2 : 1; //fFeedbackTarget 回饋對象1:仲介、2:代書跟其他回饋金類別相反
                $otherFeed2[$i]['store']    = getStore($otherFeed2[$i]['fType']);            //
                $otherFeed2[$i]['fId']      = $rs->fields['fId'];
                $otherFeed2[$i]['id']       = $rs->fields['fCaseFeedBackMark']; //從原本的帶過來

                $i++;
            } elseif ($rs->fields['fCategory'] == 7) { //個案回饋
                $no = '';
                if ($rs->fields['fFeedbackStoreId'] == $store[1]['bId']) {
                    $no = 1;
                }

                if ($rs->fields['fFeedbackStoreId'] == $store[2]['bId']) {
                    $no = 2;
                }

                if ($rs->fields['fFeedbackStoreId'] == $store[3]['bId']) {
                    $no = 3;
                }

                if ($rs->fields['fFeedbackStoreId'] == $store[6]['bId']) {
                    $no = 6;
                }

                if ($no) {
                    $store[$no]['individualName']  = $rs->fields['fCaseFeedBackMoney'];
                    $store[$no]['individualMoney'] = $rs->fields['fCaseFeedBackMoney'];
                    $store[$no]['individualId']    = $rs->fields['fIndividualId'];
                }
            }
        } else {
            if ($rs->fields['fCaseFeedBackMark'] != '') {
                $target                        = ($rs->fields['fFeedbackTarget'] == 1) ? 2 : 1;
                $delData                       = getFeedBackStore($target, $rs->fields['fFeedbackStoreId']);
                $delData['fCaseFeedBackMoney'] = $rs->fields['fCaseFeedBackMoney'];
                $delData['fType']              = ($rs->fields['fFeedbackTarget'] == 1) ? '仲介' : '地政士';
                $delData['fNote']              = $rs->fields['fCaseFeedBackNote'];

                array_push($delNote, $delData);
                unset($delData);
            }

        }

        $rs->MoveNext();
    }

    $cat = 'save';
}
##

if ($_SESSION['member_pDep'] == 7) {
    $sql = "SELECT
			s.sName AS Name,
			s.sOffice AS Name2,
			s.sId AS ID,
			CONCAT('SC',LPAD(s.sId,4,'0')) as Code
		FROM
			tScrivener AS s
		LEFT JOIN
			tScrivenerSales AS ss ON ss.sScrivener = s.sId
		WHERE
			ss.sSales = '" . $_SESSION['member_id'] . "'
		";
} else {
    $sql = "SELECT sName AS Name,sOffice AS Name2,sId AS ID,CONCAT('SC',LPAD(sId,4,'0')) as Code FROM tScrivener ORDER BY sName ASC";
}

$rs = $conn->Execute($sql);

$option[0] = '請選擇';
// ensure variables are initialized to avoid undefined variable warnings
$menuotherFeedStoreA = [];
$menuotherFeedStore  = '';
while (! $rs->EOF) {
    $menuotherFeedStoreA[$rs->fields['ID']] = $rs->fields['Code'] . $rs->fields['Name'] . "(" . $rs->fields['Name2'] . ")";

    $rs->MoveNext();
}

foreach ($menuotherFeedStoreA as $k => $v) {
    $menuotherFeedStore .= '<option value="' . $k . '">' . $v . '</option>';
}

unset($menuotherFeedStoreA);
##

//20220804 當所有仲介店都沒有合契時，不給編輯不回饋
// echo '<pre>';
// print_r($store_cooperation);
$_tf = false;
foreach ($store_cooperation as $v) {
    if (! empty($v)) {
        $_tf = true;
        break;
    }
}

$store_cooperation_disable = empty($_tf) ? ' disabled="disabled" ' : '';

$_tf = $store_cooperation = $v = null;
unset($_tf, $store_cooperation, $v);
// exit('store_cooperation_disable = '.$store_cooperation_disable);
##

$menuTarget               = [1 => '仲介', 2 => '地政士'];
$menuOTarget              = [1 => '地政士', 2 => '仲介'];
$data_case['scrivenerId'] = $data_scrivener["cScrivener"];

$otherFeed2      = is_array($otherFeed2) ? $otherFeed2 : [];
$otherFeed2Count = count($otherFeed2) + 1;

//回饋金代書關閉欄位
$scrivenerDisabled = '';
if ($data_case['cFeedBackScrivenerClose'] == 1) {
    $scrivenerDisabled = ' disabled';
}
##

#只有一個回饋帳戶不用選
if (is_array($scrivenerAccount) && count($scrivenerAccount) <= 1) {
    $scrivenerAccount = [];
}
if (is_array($otherScrivenerAccount) && count($otherScrivenerAccount) <= 1) {
    $otherScrivenerAccount = [];
}

// Initialize variables that might not be set in all code paths
if (! isset($delNote)) {
    $delNote = [];
}
if (! isset($msg)) {
    $msg = '';
}
if (! isset($otherFeed)) {
    $otherFeed = [];
}

$smarty->assign('id', $id);
$smarty->assign('delNote', $delNote);
$smarty->assign('msg', $msg);
$smarty->assign('data_realstate', $data_realstate);
$smarty->assign('chekcsp', $chekcsp);
$smarty->assign('cat', $cat);
$smarty->assign('data_case', $data_case);
$smarty->assign('otherFeed', $otherFeed);
$smarty->assign('otherFeed2', $otherFeed2);
$smarty->assign('otherFeed2Count', $otherFeed2Count);
$smarty->assign('data_income', $data_income);
$smarty->assign('scrivenerDetail', $scrivenerDetail);
$smarty->assign('store', $store);
$smarty->assign('review', $review);
$smarty->assign('menuTarget', $menuTarget);
$smarty->assign('menuOTarget', $menuOTarget);
$smarty->assign('otherFeedStore', $menuotherFeedStore);
$smarty->assign('store_cooperation_disable', $store_cooperation_disable);
$smarty->assign('certifyDate', $certifyDate);
$smarty->assign('scrivenerDisabled', $scrivenerDisabled);
$smarty->assign('scrivenerAccount', $scrivenerAccount);
$smarty->assign('otherScrivenerAccount', $otherScrivenerAccount);
$smarty->assign('feedBackScrivener', $feedBackScrivener);
$smarty->display('formbuyownerSalesFeed.inc.tpl', '', 'escrow');
