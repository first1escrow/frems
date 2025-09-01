<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/class/scrivener.class.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCaseScrivener.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

require_once dirname(__DIR__) . '/lib.php';
require_once dirname(__DIR__) . '/writelog.php';

use First1\V1\PayByCase\PayByCaseScrivener;

$scrivener = new Scrivener();
$tlog      = new TraceLog();
$tlog->insertWrite($_SESSION['member_id'], json_encode($_POST), '新增特定地政士資料明細');

/* 日期轉換 */
$_POST["sAppointDate"] = date_convert($_POST["sAppointDate"]);
$_POST["sOpenDate"]    = date_convert($_POST["sOpenDate"]);
$_POST["sSaveDate"]    = date_convert($_POST["sSaveDate"]);

if (preg_match("/000-00-00/", $_POST["sBirthday"])) {
    $_POST["sBirthday"] = '';
} else {
    $_POST["sBirthday"] = date_convert($_POST["sBirthday"]);
}

if ($_POST['sRecall'] == '') {
    $_POST['sRecall'] = 33.33;
}

$is_edit              = trim($_POST['is_edit']); //確認是否有手動增加業務
$_POST['member_name'] = $_SESSION['member_name'];
$newid                = $scrivener->AddScrivener($_POST);

//帶入預設業務
$salesArr = array();
$sql      = "SELECT * FROM tZipArea WHERE zZip = '" . $_POST['zip2'] . "'";
$rs       = $conn->Execute($sql);
$salesArr = explode(',', $rs->fields['zScrivenerSales']);

for ($i = 0; $i < count($salesArr); $i++) {
    $sql = "INSERT INTO tSalesAreaScrivener (sZip,sScrivener,sSales) VALUES ('" . $_POST['zip2'] . "','" . $newid . "','" . $salesArr[$i] . "')";
    $conn->Execute($sql);

    $sql = "INSERT INTO tScrivenerSales (sSales,sScrivener,sStage) VALUES ('" . $salesArr[$i] . "','" . $newid . "','1')";
    $conn->Execute($sql);

    $sql = "INSERT INTO tSalesAreaLog (sType,sZip,sBranch,sSales) VALUES ('1','" . $_POST['zip2'] . "','" . $newid . "','" . $salesArr[$i] . "')";
    $conn->Execute($sql);

    $sql = "INSERT INTO tSalesRegionalAttribution (sType,sDate,sZip,sStoreId,sSales,sCreatTime) VALUES('1','" . date('Y-') . "-01-01','" . $_POST['zip2'] . "','" . $newid . "','" . $salesArr[$i] . "','" . date('Y-m-d H:i:s') . "')";
    $conn->Execute($sql);
}

//20221226 新增績效業務儲存功能
$sql = "INSERT INTO tScrivenerSalesForPerformance (sId, sSales, sScrivener, sCreatedAt) VALUES (UUID(), '" . $rs->fields['zPerformanceScrivenerSales'] . "', '" . $newid . "', NOW())";
$conn->Execute($sql);

$sql = "INSERT INTO tSalesRegionalAttributionForPerformance (sType, sDate, sZip, sStoreId, sSales, sCreatTime) VALUES ('1', '" . date('Y-m-d') . "', '" . $_POST['zip2'] . "', '" . $newid . "', '" . $rs->fields['zPerformanceScrivenerSales'] . "', NOW())";
$conn->Execute($sql);
##

$sContractStatus = @implode(',', $_POST['sContractStatus']);

$tmp                 = explode('-', $_POST['sContractStatusTime']);
$bContractStatusTime = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
$tmp                 = null;unset($tmp);

if ($sContractStatus == 1) {
    $sales = $_POST['signSales'];
    if (empty($sales)) {
        $sql   = "SELECT sSales FROM tScrivenerSalesForPerformance WHERE sScrivener = '" . $newid . "'";
        $rs    = $conn->Execute($sql);
        $sales = $rs->fields['sSales'];
    }

    $sql = "SELECT sId,sSales FROM tSalesSign WHERE sType = '1' AND sStore='" . $newid . "' AND sSignDate ='" . $bContractStatusTime . "'";
    $rs  = $conn->Execute($sql);

    if ($rs->fields['sId'] == '') {
        //增加至 tSalesSign
        $sql = "INSERT INTO
					tSalesSign
					(
						sType,
						sStore,
						sName,
						sOffice,
						sBrand,
						sArea,
						sSignDate,
						sSales
					) VALUES
					(
						'1',
						'" . $newid . "',
						'" . $_POST['sName'] . "',
						'" . $_POST['sOffice'] . "',
						'" . $_POST['bBrand'] . "',
						'" . $_POST['zip2'] . "',
						'" . $bContractStatusTime . "',
						'" . $sales . "'
					)";
        $conn->Execute($sql);
    }
}
##

//分辨會計跟其他人儲存
if (in_array($_SESSION['member_pDep'], [9, 10])) {
    $sql = "UPDATE tBranch SET  bEditor_Accounting='" . $_SESSION['member_name'] . "',bModify_time_Accounting ='" . date('Y-m-d H:i:s', time()) . "' WHERE bId='" . $newid . "'";
    $conn->Execute($sql);
}
##

//回饋金資料
for ($i = 0; $i < count($_POST['newTtitle']); $i++) {
    if ($_POST['newTtitle'][$i] != '') {
        $sql = "INSERT INTO tFeedBackData(
					fType,
					fStoreId,
					fFeedBack,
					fRtitle,
					fTitle,
					fIdentity,
					fIdentityNumber,
					fZipC,
					fAddrC,
					fZipR,
					fAddrR,
					fMobileNum,
					fEmail,
					fAccountNum,
					fAccountNumB,
					fAccount,
					fAccountName,
					fNote,
                    fIncomeCategory
				)VALUES(
					'1',
					'" . $newid . "',
					'" . $_POST['newFeedBack'][$i] . "',
					'" . $_POST['newRtitle'][$i] . "',
					'" . $_POST['newTtitle'][$i] . "',
					'" . $_POST['newIdentity'][$i] . "',
					'" . $_POST['newIdentityNumber'][$i] . "',
					'" . $_POST['newzipC'][$i] . "',
					'" . $_POST['newaddrC'][$i] . "',
					'" . $_POST['newzipR'][$i] . "',
					'" . $_POST['newaddrR'][$i] . "',
					'" . $_POST['newMobileNum'][$i] . "',
					'" . $_POST['newEmail'][$i] . "',
					'" . $_POST['newAccountNum'][$i] . "',
					'" . $_POST['newAccountNumB'][$i] . "',
					'" . $_POST['newAccount'][$i] . "',
					'" . $_POST['newAccountName'][$i] . "',
					'" . $_POST['newNote'][$i] . "',
					'" . $_POST['newIncomeCategory'][$i] . "'
				)";
        $conn->Execute($sql);
    }
}
##

//20230314 增加鴻兔大展自動新增機制
// $_campaign_time = date('Y-m-d');
// if (($_campaign_time >= "2023-03-01") && ($_campaign_time <= "2023-09-30")) { //活動期間：2023-03-01 ~ 2023-09-30
//     require_once dirname(__DIR__) . '/activities/2/scrivener_add.php';
// }
// $_campaign_time = null;unset($_campaign_time);
##

$conn->close();

//20230421 回饋金隨案支付
$pay_by_case_scrivener = new PayByCaseScrivener(new first1DB);
$pay_by_case_scrivener->modifyAffectCaseBankAccountByScrivener($newid);
##

echo "儲存完成";