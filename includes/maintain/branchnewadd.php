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
if (ereg('0000-00-00', $_POST["bCashierOrderDate"])) {
    $_POST["bCashierOrderDate"] = '';
} else {
    $_POST["bCashierOrderDate"] = date_convert($_POST["bCashierOrderDate"]);
}

if (ereg('0000-00-00', $_POST["bCashierOrderSave"])) {
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

$sql = "UPDATE tBranch SET bOldStoreID = '" . $_POST["id"] . "' WHERE bId = '" . $newid . "'";
$conn->Execute($sql);

$sql  = "SELECT * FROM tSalesSign WHERE sType = 2 AND sStore ='" . $_POST["id"] . "'";
$rs   = $conn->Execute($sql);
$sign = $rs->fields;

if ($rs->fields['sStore'] > 0) {
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
					sSales,
					sOldStoreID
				) VALUES(
					'2',
					'" . $newid . "',
					'" . $branch[0]['bStore'] . "',
					'" . $branch[0]['bName'] . "',
					'" . $branch[0]['bBrand'] . "',
					'" . $branch[0]['bZip'] . "',
					'" . $sign['sSignDate'] . "',
					'" . $sign['sSales'] . "',
					'" . $_POST['id'] . "'
				) ";
    $conn->Execute($sql);
}

//帶入預設業務
$salesArr = array();
$sql      = "SELECT * FROM tZipArea WHERE zZip = '" . $branch[0]['bZip'] . "'";
$rs       = $conn->Execute($sql);

if (in_array($branch[0]['bBrand'], [1, 49])) {
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
}

//20221226 新增績效業務儲存功能
if (!empty($rs->fields['zPerformanceSales'])) {
    $sql = "INSERT INTO tBranchSalesForPerformance (bId, bSales, bBranch, bCreatedAt) VALUES (UUID(), '" . $rs->fields['zPerformanceSales'] . "', '" . $newid . "', NOW())";
    // file_put_contents(dirname(dirname(__DIR__)) . '/log/sql_new_add_tBranchSalesForPerformance.log', date("Y-m-d H:i:s ") . $sql . "\n", FILE_APPEND);
    $conn->Execute($sql);

    $sql = "INSERT INTO tSalesRegionalAttributionForPerformance (sType, sDate, sZip, sStoreId, sSales, sCreatTime) VALUES ('2', '" . date('Y-m-d') . "', '" . $branch[0]['bZip'] . "', '" . $newid . "', '" . $rs->fields['zPerformanceSales'] . "', NOW())";
    // file_put_contents(dirname(dirname(__DIR__)) . '/log/sql_new_add_tBranchSalesForPerformance.log', date("Y-m-d H:i:s ") . $sql . "\n", FILE_APPEND);
    $conn->Execute($sql);
}
##

$salesArr = $rs = null;
unset($salesArr, $rs);

//簡訊對象
$sql = "SELECT * FROM tBranchSms WHERE bBranch ='" . $_POST['id'] . "' AND bCheck_id =0 AND bDel = 0";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $bSms[] = $rs->fields;
    $rs->MoveNext();
}

for ($i = 0; $i < count($bSms); $i++) {
    $sql = "INSERT INTO tBranchSms
			(
				bBranch,
				bNID,
				bName,
				bMobile,
				bDefault
			) VALUES (
				'" . $newid . "',
				'" . $bSms[$i]['bNID'] . "',
				'" . $bSms[$i]['bName'] . "',
				'" . $bSms[$i]['bMobile'] . "',
				'" . $bSms[$i]['bDefault'] . "'
			)";
    $conn->Execute($sql);
}
$bSms = null;unset($bSms);

//回饋金出款簡訊對象資料
$sql = "SELECT * FROM tBranchFeedback WHERE bBranch ='" . $_POST['id'] . "'";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $bSms[] = $rs->fields;
    $rs->MoveNext();
}

for ($i = 0; $i < count($bSms); $i++) {
    $sql = "INSERT INTO tBranchFeedback
			(
				bBranch,
				bNID,
				bName,
				bMobile
			) VALUES (
				'" . $newid . "',
				'" . $bSms[$i]['bNID'] . "',
				'" . $bSms[$i]['bName'] . "',
				'" . $bSms[$i]['bMobile'] . "'
			)";
    $conn->Execute($sql);
}
$bSms = null;unset($bSms);

#回饋金通知簡訊對象
$sql = "SELECT * FROM tFeedBackStoreSms WHERE fDelete = 0 AND fStoreId ='" . $_POST['id'] . "'";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $bSms[] = $rs->fields;
    $rs->MoveNext();
}

for ($i = 0; $i < count($bSms); $i++) {
    $sql = "INSERT INTO tFeedBackStoreSms
			(
				fType,
				fTitle,
				fStoreId,
				fName,
				fMobile,
				fOriginalId
			) VALUES (
				'" . $bSms[$i]['fType'] . "',
				'" . $bSms[$i]['fTitle'] . "',
				'" . $newid . "',
				'" . $bSms[$i]['fName'] . "',
				'" . $bSms[$i]['fMobile'] . "',
				'" . $bSms[$i]['fStoreId'] . "'
			)";
    $conn->Execute($sql);
}
$bSms = null;unset($bSms);

#備註說明
$sql = "SELECT * FROM tBranchNote WHERE bDel = 0 AND bStatus = 0 AND  bStore ='" . $_POST['id'] . "'";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $bBranchNote[] = $rs->fields;
    $rs->MoveNext();
}

for ($i = 0; $i < count($bBranchNote); $i++) {
    $sql = "INSERT INTO tBranchNote
			(
				bStore,
				bNote,
			    bCreator,
			    bCreatTime
			) VALUES (
				'" . $newid . "',
				'" . $bBranchNote[$i]['bNote'] . "',
				'" . $bBranchNote[$i]['bCreator'] . "',
                NOW()
			)";
    $conn->Execute($sql);
}
$bBranchNote = null;unset($bBranchNote);

echo "儲存完成";
