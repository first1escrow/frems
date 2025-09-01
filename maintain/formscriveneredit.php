<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/class/getBank.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/first1Sales.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';

if ($_SESSION['member_id'] == 39) {
    $_SESSION['pBusinessEdit'] = 1;
    $_SESSION['pBusinessView'] = 1;
}

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '檢視特定地政士資料明細');

//預載log物件
$logs = new Intolog();
##

if (empty($_POST["id"])) {
    $_POST["id"] = $_GET['id'];
}

##
$sms_target = '';
if ($_SESSION['member_pDep'] == 7) {
    $sms_target = 'distable';
}

$scrivener = new Scrivener();

$data = $scrivener->GetScrivenerInfo($_POST["id"]);

//如果本票欄位為空就不要顯示
if (($data['sAppointDate'] != '0000-00-00' && $data['sAppointDate'] != '3822-00-00' && $data['sAppointDate'] != '5733-00-00' && $data['sAppointDate'] != '7644-00-00' && $data['sAppointDate'] != '9555-00-00')
    || ($data['sSaveDate'] != '0000-00-00' && $data['sSaveDate'] != '3822-00-00' && $data['sSaveDate'] != '5733-00-00' && $data['sSaveDate'] != '7644-00-00' && $data['sSaveDate'] != '9555-00-00')
    || ($data['sOpenDate'] != '0000-00-00' && $data['sOpenDate'] != '3822-00-00' && $data['sOpenDate'] != '5733-00-00' && $data['sOpenDate'] != '7644-00-00' && $data['sOpenDate'] != '9555-00-00')
    || $data['sTicketNumber'] != '' || $data['sTicketMoney'] != '0') { //
    $ticketShow = 'OK';
}
##

$from_sales = $_POST['from_sales'];

$list_ppl   = $scrivener->GetPeopleList();
$menu_ppl   = $scrivener->ConvertOption($list_ppl, 'pId', 'pName');
$list_brand = $scrivener->GetBrandList();

$sql = "SELECT bId,bName FROM tBrand WHERE bContract = 1";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $menu_brand[$rs->fields['bId']] = $rs->fields['bName'];
    $rs->MoveNext();
}

$menu_invoice          = $scrivener->GetCategoryInvoice();
$menu_status           = $scrivener->GetCategoryScrivenerStatus();
$data['sBrand']        = explode(",", $data['sBrand']);
$data['sBank']         = explode(",", $data['sBank']);
$data['sBackDocument'] = explode(",", $data['sBackDocument']);
$menu_accunused        = array('1' => '是');

//修正地址縣市區域重複
$data['sAddress']   = filterCityAreaName($conn, $data['sZip1'], $data['sAddress']);
$data['sCpAddress'] = filterCityAreaName($conn, $data['sCpZip1'], $data['sCpAddress']);

//設定代書系統欄位預設值
if (empty($data['sScrivenerSystem'])) {
    $data['sScrivenerSystem'] = '';
}
if (empty($data['sScrivenerSystemOther'])) {
    $data['sScrivenerSystemOther'] = '';
}
##

//取得總行(1)選單
$menu_bank = $scrivener->GetBankMenuList();
##

//取得分行(1)選單
$menu_branch = getBankBranch($conn, $data['sAccountNum1'], $data['sAccountNum2']);
##

//取得分行(2)選單
$menu_branch21 = getBankBranch($conn, $data['sAccountNum11'], $data['sAccountNum21']);
$menu_branch22 = getBankBranch($conn, $data['sAccountNum12'], $data['sAccountNum22']);
##

$menu_categoryrecall   = $scrivener->GetCategoryRecall();
$menu_categoryidentify = $scrivener->GetCategoryIdentify();
$menu_feedbank         = getBankBranch($conn, $data['sAccountNum5'], $data['sAccountNum6']);
##

//負責業務
$sSales = '';
$sql    = '
	SELECT
		a.sId,
		a.sStage,
		(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName
	FROM
		tScrivenerSales AS a
	WHERE
		sScrivener="' . trim($_POST['id']) . '"
	ORDER BY
		sId
	ASC;
';
$rs = $conn->Execute($sql);

$tmp    = array();
$_stage = array();
$tIndex = 0;
$stage  = '';
while (!$rs->EOF) {
    if ($_SESSION['pBusinessEdit'] == 1) {
        $color   = 'yellow';
        $display = '';
    } else {
        $color   = 'orange';
        $display = 'none';
    }
    ##

    $tmp[$tIndex] .= $rs->fields['sSalesName'];

    $tIndex++;
    $rs->MoveNext();
}
$sSales = implode(',', $tmp);
$tmp    = null;unset($tmp);

if (!$stage) {
    $stage = implode(',', $_stage);
}
$_stage = null;unset($_stage);
##

//是否可調整回饋金權限
$_disabled = ' disabled=disabled';

if ($_SESSION['member_pFeedBackModify'] == '1' && $_SESSION['member_pDep'] != 9 && $_SESSION['member_pDep'] != 10) {
    $_disabled = '';
}

if ($_SESSION['member_id'] == 48) {
    $_disabled = '';
}
##

//埋log紀錄
$logs->writelog('formScrivener', '查詢地政士(' . $data['sName'] . ' SC' . str_pad($data['sId'], 4, '0', STR_PAD_LEFT) . ')');
##

$data['sAppointDate'] = $scrivener->ConvertDateToRoc($data['sAppointDate'], base::DATE_FORMAT_NUM_DATE);
$data['sOpenDate']    = $scrivener->ConvertDateToRoc($data['sOpenDate'], base::DATE_FORMAT_NUM_DATE);
$data['sSaveDate']    = $scrivener->ConvertDateToRoc($data['sSaveDate'], base::DATE_FORMAT_NUM_DATE);
$data['sLicenseExpired'] = (empty($data['sLicenseExpired']) || $data['sLicenseExpired'] == '0000-00-00') ? '' : DateChange($data['sLicenseExpired']);
##

//設定回饋年度範圍
for ($i = 2012; $i <= date("Y"); $i++) {
    $arr        = array();
    $tmp        = $rs->fields['cEndDate'];
    $arr        = explode('-', $tmp);
    $FBYear[$i] = ($i - 1911) . '&nbsp;';

    $tmp = $arr = null;
    unset($tmp, $arr);

    $rs->MoveNext();
}

$today = DateChange(date('Y-m-d'));

function DateChange($date)
{
    $date = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", "", $date));
    $tmp  = explode('-', $date);

    if (preg_match("/0000/", $tmp[0])) {
        $tmp[0] = '000';
    } else {
        $tmp[0] -= 1911;
    }

    $date = $tmp[0] . '-' . $tmp[1] . '-' . $tmp[2];
    $tmp  = null;unset($tmp);
    return $date;
}
##

$sql = "SELECT sStore,sSignDate,sSales,(SELECT pName FROM tPeopleInfo WHERE pId=sSales) AS SalesName FROM tSalesSign WHERE sType = 1 AND sStore ='" . $_POST["id"] . "' ORDER BY sSales DESC";
$rs  = $conn->Execute($sql);

$signSales = array();
// 可能會有一間店有兩個業務簽約
while (!$rs->EOF) {
    if ($rs->fields['sStore']) {
        $signSales[$rs->fields['sSales']] = $rs->fields['SalesName'];
        $signData                         = $rs->fields;

        $signData['sSignDate'] = DateChange($rs->fields['sSignDate']);
        if ($signData['sSales'] != '0') {
            $signData['sContractStatus'] = '1';
        }
    }

    $rs->MoveNext();
}

//日期格式轉換
$data['sStatusDateStart'] = ($data['sStatusDateStart'] == '0000-00-00') ? '000-00-00' : DateChange($data['sStatusDateStart']);
$data['sStatusDateEnd']   = ($data['sStatusDateEnd'] == '0000-00-00') ? '000-00-00' : DateChange($data['sStatusDateEnd']);
$data['sSalesDate']       = ($data['sSalesDate'] == '0000-00-00') ? '000-00-00' : DateChange($data['sSalesDate']);
$data['sBirthday']        = ($data['sBirthday'] == '0000-00-00') ? '000-00-00' : DateChange($data['sBirthday']);
##

//回饋簡訊
$sql = '
	SELECT
		a.sId as sn,
		a.sNID as id,
		a.sName as sName,
		a.sMobile as sMobile,
		b.tTitle as tTitle
	FROM
		tScrivenerFeedSms AS a
	JOIN
		tTitle_SMS AS b ON a.sNID=b.id
	WHERE
		a.sScrivener="' . trim($_POST['id']) . '"

	ORDER BY
		a.sNID,b.tTitle
	ASC
;';
$rs = $conn->Execute($sql);

$data_feedsms = array();
$i            = 0;
while (!$rs->EOF) {
    $data_feedsms[$i] = $rs->fields;
    $i++;

    $rs->MoveNext();
}
##

//回饋對象資料
$data_feedData = FeedBackData($_POST['id'], 1);
##

//回饋通知簡訊
$data_feedsmsNotify = array();

$sql = "SELECT
			fName,
			fMobile,
			(SELECT tTitle FROM tTitle_SMS WHERE id=fTitle) AS fTitle
		FROM
			tFeedBackStoreSms WHERE fType = 1 AND fStoreId = '" . $_POST['id'] . "' AND fDelete = 0";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $data_feedsmsNotify[] = $rs->fields;
    $rs->MoveNext();
}
##

//品牌回饋地政士
$sql = "SELECT *,(SELECT bName FROM tBrand WHERE bId = sBrand) AS BrandName FROM tScrivenerFeedSp WHERE sScrivener ='" . $_POST['id'] . "' AND sDel =0";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $FeedSp[] = $rs->fields;
    $rs->MoveNext();
}
##

$sql = "SELECT pId,pName FROM tPeopleInfo WHERE (pDep = 4 OR pDep = 7) AND pJob = 1";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $menu_sales[$rs->fields['pId']] = $rs->fields['pName'];
    $rs->MoveNext();
}
##

$sql = "SELECT * FROM tRgMoney WHERE rAccount = 'SC" . str_pad($_POST['id'], 4, 0, STR_PAD_LEFT) . "' AND rDate >= '" . date('Y-m') . "-01' AND rDate <= '" . date('Y-m') . "-31'";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $dataRg = $rs->fields;
    $rs->MoveNext();
}
##

##銀行
$sql = "SELECT * FROM tScrivenerBank WHERE sScrivener ='" . $_POST['id'] . "'";
$rs  = $conn->Execute($sql);

$bankcount = 4;
while (!$rs->EOF) {
    //bankbranch
    $rs->fields['bankbranch'] = getBankBranch($conn, $rs->fields['sBankMain'], $rs->fields['sBankBranch']);
    $rs->fields['no']         = $bankcount;
    if ($rs->fields['sUnUsed'] == 1) {
        $rs->fields['disabled'] = 'disabled=disabled';
        $rs->fields['checked']  = 'checked=checked';

    }

    $dataBank[] = $rs->fields;
    $bankcount++;

    $rs->MoveNext();
}
##

//合約銀行
$menu_contractbank = array();
$sql               = "SELECT cBankFullName,cBranchFullName,cId FROM tContractBank WHERE cShow = 1 AND cId != 4"; //永豐用城中
$rs                = $conn->Execute($sql);

while (!$rs->EOF) {
    $menu_contractbank[$rs->fields['cId']] = $rs->fields['cBankFullName'] . $rs->fields['cBranchFullName'];
    $rs->MoveNext();
}
##

//取得地政士選單 20221012
$menu_scriveners = $scrivener->GetListScrivener();
$menu_scriveners = $scrivener->ConvertOption($menu_scriveners, 'sId', 'sOffice', true);
##

/**
 * 資料庫連線方式改為 pdo
 *
 */
//

function getActivityGifts($aId, $act)
{
    $conn = new first1DB;
    $sql  = 'SELECT aId, aName FROM tActivityGifts WHERE aActivityId = :id;';
    $rs   = $conn->all($sql, ['id' => $aId]);

    foreach ($rs as $k => $v) {
        $checked           = (!empty($act['aGift']) && ($act['aGift'] == $v['aId'])) ? ' checked="checked" ' : '';
        $rs[$k]['checked'] = $checked;
        unset($checked);
    }

    return $rs;
}

function getActivityRules($aId, $act)
{
    $conn = new first1DB;
    $sql  = 'SELECT aId, aTitle, aItem FROM tActivityRules WHERE aActivityId = :id;';
    $rs   = $conn->all($sql, ['id' => $aId]);

    foreach ($rs as $k => $v) {
        $checked           = (!empty($act['aRule']) && ($act['aRule'] == $v['aId'])) ? ' checked="checked" ' : '';
        $rs[$k]['checked'] = $checked;
        unset($checked);
    }

    return $rs;
}

$pdo = new first1DB;

$activities_active = [];

$sql = 'SELECT * FROM tActivityRecords WHERE aIdentity = :identity AND aStoreId = :store;';
$rs  = $pdo->all($sql, ['identity' => 'S', 'store' => $_POST['id']]);
if (!empty($rs)) {
    foreach ($rs as $k => $v) {
        $activities_active[$v['aActivityId']] = $v;
    }
}

$activities = [];

$sql = '
    SELECT
        a.aId,
        a.aTitle,
        a.aYear,
        a.aStartDate,
        a.aEndDate,
        a.aPriority,
        a.aRemark,
        b.aContent as ext
    FROM
        tActivities AS a
    LEFT JOIN
        tActivityExt AS b ON a.aId = b.aActivityId
    WHERE
        a.aStatus = "Y"
        AND a.aTarget IN ("A", "S")
    ORDER BY
        a.aYear, a.aId
    ASC
;';
$rs = $pdo->all($sql);

if (!empty($rs)) {
    foreach ($rs as $k => $v) {
        $activities[$v['aId']]             = $v;
        $activities[$v['aId']]['Rules']    = getActivityRules($v['aId'], $activities_active[$v['aId']]);
        $activities[$v['aId']]['Gifts']    = getActivityGifts($v['aId'], $activities_active[$v['aId']]);
        $activities[$v['aId']]['priority'] = $activities_active[$v['aId']]['aPriority'];
        $activities[$v['aId']]['ext']      = json_decode($v['ext'], true);

        //鴻兔大展活動 20230220
        if ($v['aId'] == '2') {
            // require dirname(__DIR__) . '/includes/activities/2/scrivener.php';
            $act_identity = 'S';
            require dirname(__DIR__) . '/includes/activities/2/campaign.php';
            $act_identity = null;unset($act_identity);
        }
        ##

        //蛇采飛揚活動 20250717
        if ($v['aId'] == '3') {
            $act_identity = 'S';
            require dirname(__DIR__) . '/includes/activities/3/campaign.php';
            $act_identity = null;unset($act_identity);
        }
        ##
    }
}
$rs = null;unset($rs);
// print_r($activities);exit;

$smarty->assign('activities', $activities);
##

//地政士績效業務
$sql = 'SELECT (SELECT pName FROM tPeopleInfo WHERE pId = a.sSales) as sales FROM tScrivenerSalesForPerformance AS a WHERE sScrivener = :sId;';
$rs  = $pdo->one($sql, ['sId' => $_POST['id']]);
$smarty->assign('performanceSales', $rs['sales']);
$rs = null;unset($rs);
##

$pdo = null;unset($pdo);

$menu_feedDateCat = array(0 => '季', 1 => '月', 2 => '隨案');

$data['sFeedDateCatSwitchDate'] = empty($data['sFeedDateCatSwitchDate']) ? '' : substr($data['sFeedDateCatSwitchDate'], 0, 7);

/**
 * ########################
 */

$smarty->assign('sms_target', $sms_target);
$smarty->assign('menu_mark', array('0' => '不標記', '1' => '標記'));
$smarty->assign('menu_contractbank', $menu_contractbank);
$smarty->assign('dataBank', $dataBank);
$smarty->assign('bankcount', $bankcount);
$smarty->assign('ticketShow', $ticketShow);
$smarty->assign('dataRg', $dataRg);
$smarty->assign('FeedSp', $FeedSp);
$smarty->assign('data_feedData_count', count($data_feedData));
$smarty->assign('menu_note', array('' => '請選擇', 'INV' => 'INV', 'REC' => 'REC'));
$smarty->assign('menu_incomecategory', array('' => '請選擇', '9A-13' => '9A-13', '9A-76' => '9A-76'));
$smarty->assign('data_feedData', $data_feedData);
$smarty->assign('data_feedsms', $data_feedsms);
$smarty->assign('data_feedsmsNotify', $data_feedsmsNotify);
$smarty->assign('signSales', $signSales);
$smarty->assign('signData', $signData);
$smarty->assign('locker', $locker);
$smarty->assign('today', $today);
$smarty->assign('menu_cstatus', array('1' => '是'));
$smarty->assign('menu_choice', array('1' => '是', '0' => '否'));
$smarty->assign('_disabled', $_disabled);
$smarty->assign('FBYear', $FBYear);
$smarty->assign('FBYearSelect', Date("Y"));
$smarty->assign('stage', $stage);
$smarty->assign('from_sales', $from_sales); //判斷是否為業務責任區審核來的
$smarty->assign('sOptions', array(1 => '加盟', 2 => '直營'));
$smarty->assign('menu_sbackDoc', array(1 => '身分證', 2 => '存摺', 3 => '變更帳戶'));
$smarty->assign('is_edit', 1);
$smarty->assign('menu_bank', $menu_bank);
$smarty->assign('menu_branch', $menu_branch); //分行(1)
$smarty->assign('menu_branch21', $menu_branch21); //分行(2)
$smarty->assign('menu_branch22', $menu_branch22);
$smarty->assign('menu_status', $menu_status);
$smarty->assign('menu_invoice', $menu_invoice);
$smarty->assign('menu_brand', $menu_brand);
$smarty->assign('menu_ppl', $menu_ppl);
$smarty->assign('menu_sales', $menu_sales);
$smarty->assign('sSales', $sSales);
$smarty->assign('listCity', listCity($conn, $data['sZip1'])); //聯絡地址-縣市
$smarty->assign('listArea', listArea($conn, $data['sZip1'])); //聯絡地址-區域
$smarty->assign('listCity2', listCity($conn, $data['sCpZip1'])); //公司地址-縣市
$smarty->assign('listArea2', listArea($conn, $data['sCpZip1'])); //公司地址-區域
$smarty->assign('data', $data);
$smarty->assign('menu_categoryidentify', $menu_categoryidentify);
$smarty->assign('menu_categoryrecall', $menu_categoryrecall);
$smarty->assign('menu_accunused', $menu_accunused);
$smarty->assign('FeedCity', listCity($conn)); //回饋金-縣市
$smarty->assign('menu_feedDateCat', array_merge(['' => ''], $menu_feedDateCat));
$smarty->assign('feed_date_cat_name', $menu_feedDateCat[$data['sFeedDateCat']] . '結');
$smarty->assign('feed_date_cat_switch_name', ($data['sFeedDateCatSwitchDate'] == '') ? '' : $menu_feedDateCat[$data['sFeedDateCatSwitchDate']]);
$smarty->assign('menu_scriveners', $menu_scriveners); //地政士選單
$smarty->assign('min_date', date("Y-m"));

$smarty->display('formscrivener.inc.tpl', '', 'maintain');
