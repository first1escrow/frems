<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/class/brand.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

require_once dirname(__DIR__) . '/lib.php';
require_once dirname(__DIR__) . '/writelog.php';

$tlog = new TraceLog();
$tlog->insertWrite($_SESSION['member_id'], json_encode($_POST), '新增特定仲介店明細');

$brand = new Brand();

/* 日期轉換 */
if ($_POST["bCashierOrderDate"] == '0000-00-00' || $_POST["bCashierOrderDate"] == '') {
    $_POST["bCashierOrderDate"] = '';
} else {
    $_POST["bCashierOrderDate"] = date_convert($_POST["bCashierOrderDate"]);
}

if ($_POST["bCashierOrderDate"] == '0000-00-00' || $_POST["bCashierOrderDate"] == '') {
    $_POST["bCashierOrderSave"] = '';
} else {
    $_POST["bCashierOrderSave"] = date_convert($_POST["bCashierOrderSave"]);
}

if ($_POST['bRecall'] == '') {
    $_POST['bRecall'] = 33.33;
}

$newid  = $brand->AddBranch($_POST);
$branch = $brand->GetBranch($newid);
$brand  = $brand->GetBrand($branch[0]['bBrand']);
##

//編修仲介總表
$sql = "SELECT * FROM tCategoryRealty WHERE cBrandId='" . $branch[0]['bBrand'] . "' AND cBranch ='" . $branch[0]['bStore'] . "'";
$rs  = $conn->Execute($sql);

if ($rs->fields['cId']) {
    $sql2 = "UPDATE tCategoryRealty SET bId='" . $newid . "' WHERE cId = '" . $rs->fields['cId'] . "'";
    $conn->Execute($sql2);
    write_log("add:" . $sql . "\r\n", 'checkbranch');
} else {
    $sql = "INSERT INTO tCategoryRealty (cBrandId,cBrand,cBranch,cCompany,bId) VALUES('" . $branch[0]['bBrand'] . "','" . $brand['bName'] . "','" . $branch[0]['bStore'] . "','" . $branch[0]['bName'] . "','" . $newid . "')";
    write_log("add:" . $sql . "\r\n", 'checkbranch');
    $conn->Execute($sql);
}

//帶入預設業務
$salesArr = array();
$sql      = "SELECT * FROM tZipArea WHERE zZip = '" . $branch[0]['bZip'] . "'";
$rs       = $conn->Execute($sql);

if ($branch[0]['bBrand'] == 1 || $branch[0]['bBrand'] == 49) {
    $salesArr = explode(',', $rs->fields['zSalesTwhg']);
} else {
    $salesArr = explode(',', $rs->fields['zSales']);
}

for ($i = 0; $i < count($salesArr); $i++) {
    $sql = "INSERT INTO tSalesArea (sZip,sBranch,sSales) VALUES ('" . $branch[0]['bZip'] . "','" . $newid . "','" . $salesArr[$i] . "')";
    $conn->Execute($sql);

    $sql = "INSERT INTO tBranchSales (bSales,bBranch,bStage) VALUES ('" . $salesArr[$i] . "','" . $newid . "','1')";
    $conn->Execute($sql);

    $sql = "INSERT INTO tSalesAreaLog (sType,sZip,sBranch,sSales) VALUES ('2','" . $branch[0]['bZip'] . "','" . $newid . "','" . $salesArr[$i] . "')";
    $conn->Execute($sql);

    $sql = "INSERT INTO tSalesRegionalAttribution (sType,sDate,sZip,sStoreId,sSales,sCreatTime) VALUES('2','" . date('Y-') . "-01-01','" . $branch[0]['bZip'] . "','" . $newid . "','" . $salesArr[$i] . "','" . date('Y-m-d H:i:s') . "')";
    $conn->Execute($sql);
}

//20221226 新增績效業務儲存功能
if (!empty($rs->fields['zPerformanceSales'])) {
    $sql = "INSERT INTO tBranchSalesForPerformance (bId, bSales, bBranch, bCreatedAt) VALUES (UUID(), '" . $rs->fields['zPerformanceSales'] . "', '" . $newid . "', NOW())";
    $conn->Execute($sql);

    $sql = "INSERT INTO tSalesRegionalAttributionForPerformance (sType, sDate, sZip, sStoreId, sSales, sCreatTime) VALUES ('2', '" . date('Y-m-d') . "', '" . $branch[0]['bZip'] . "', '" . $newid . "', '" . $rs->fields['zPerformanceSales'] . "', NOW())";
    $conn->Execute($sql);
}
##

$salesArr = $rs = null;
unset($salesArr, $rs);

//簽約業務
$bContractStatus = @implode(',', $_POST['bContractStatus']);

if ($_POST['bContractStatusTime'] != '' && $_POST['bContractStatusTime'] != '000-00-00') {
    $tmp = explode('-', $_POST['bContractStatusTime']);

    $tmp[0]              = $tmp[0] + 1911;
    $bContractStatusTime = $tmp[0] . "-" . $tmp[1] . "-" . $tmp[2];
    $tmp                 = null;unset($tmp);
} else {
    $bContractStatusTime = "0000-00-00";
}

if ($bContractStatus == 1) {
    $sql = "UPDATE tBranch SET bContractStatusTime = '" . $bContractStatusTime . "' WHERE bId ='" . $newid . "'";
    $conn->Execute($sql);

    $sql = "SELECT sId,sSales FROM tSalesSign WHERE sType = '2' AND sStore='" . $newid . "' AND sSignDate ='" . $bContractStatusTime . "'";
    $rs  = $conn->Execute($sql);

    $sales = $_POST['signSales'];

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
						'2',
						'" . $newid . "',
						'" . $_POST['bStore'] . "',
						'" . $_POST['bName'] . "',
						'" . $_POST['bBrand'] . "',
						'" . $_POST['zip'] . "',
						'" . $bContractStatusTime . "',
						'" . $sales . "'
					)";
        write_log("add:" . $sql . "\r\n", 'salesin');
        $conn->Execute($sql);
    }

} else if ($bContractStatus != 1) {
    $sql = "UPDATE tBranch SET bContractStatusTime = '0000-00-00' WHERE bId ='" . $newid . "'";
    $conn->Execute($sql);
}

//分辨會計跟其他人儲存
if (in_array($_SESSION['member_pDep'], [9, 10])) {
    $sql = "UPDATE tBranch SET  bEditor_Accounting='" . $_SESSION['member_name'] . "',bModify_time_Accounting ='" . date('Y-m-d H:i:s', time()) . "' WHERE bId='" . $newid . "'";
    $conn->Execute($sql);
}
##

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
					fNote
				)VALUES(
					'2',
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
					'" . $_POST['newNote'][$i] . "'
				)";
        $conn->Execute($sql);

        if (!empty($_POST['newAccountName'][$i])) {
            $sql = "UPDATE tBranch SET bCooperationHas = 1 WHERE bId ='" . $newid . "'";
            $conn->Execute($sql);
        }
    }
}
##

//編修55紀錄(台屋加盟店寫到台屋資料庫!!!)
//20230314 拿掉此功能

//20230314 增加鴻兔大展自動新增機制
// $_campaign_time = date('Y-m-d');
// if (($_campaign_time >= "2023-03-01") && ($_campaign_time <= "2023-09-30")) { //活動期間：2023-03-01 ~ 2023-09-30
//     require_once dirname(__DIR__) . '/activities/2/branch_add.php';
// }
// $_campaign_time = null;unset($_campaign_time);
##

echo "儲存完成";