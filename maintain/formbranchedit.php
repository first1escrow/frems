<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/class/sms.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/class/getBank.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/first1Sales.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';
require_once dirname(__DIR__) . '/tracelog.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '查詢特定仲介店明細');

//預載log物件
$logs = new Intolog();
##

if ($_SESSION['member_id'] == 39) {
    $_SESSION['pBusinessEdit'] = 1;
    $_SESSION['pBusinessView'] = 1;
}

$brand      = new Brand();
$sms        = new SMS();
$data       = $brand->GetBranch($_POST["id"]);
$data       = $data[0];
$from_sales = $_POST['from_sales'];

if ($data['bContractStatus'] == 1 or $_SESSION['member_pDep'] == 7) {
    $address_disabled = 'distable';
}
##
$sms_target = '';
if ($_SESSION['member_pDep'] == 7) {
    $sms_target = 'distable';
}

//如果本票欄位為空就不要顯示
if ($data['bCashierOrderHas'] != '' || $data['bCashierOrderNumber'] != '' || $data['bCashierOrderMoney'] != '0' || $data['bInvoice1'] != '' || $data['bInvoice2'] != ''
    || ($data['bCashierOrderDate'] != '0000-00-00' && $data['bCashierOrderDate'] != '3822-00-00' && $data['bCashierOrderDate'] != '5733-00-00' && $data['bCashierOrderDate'] != '7644-00-00' && $data['bCashierOrderDate'] != '9555-00-00')
    || ($data['bCashierOrderSave'] != '0000-00-00' && $data['bCashierOrderSave'] != '3822-00-00' && $data['bCashierOrderSave'] != '5733-00-00' && $data['bCashierOrderSave'] != '7644-00-00' && $data['bCashierOrderSave'] != '9555-00-00')
    || $data['bReTicket'] != '' || $data['bCashierOrderRemark'] != '') {
    $ticketShow = 'OK';
}
##

$data['bMessage']         = explode(",", $data['bMessage']);
$data['bEmailReceive']    = explode(",", $data['bEmailReceive']);
$data['bCashierOrderHas'] = explode(",", $data['bCashierOrderHas']);
$data['bSystem']          = explode(",", $data['bSystem']);

$menu_categoryidentify     = $brand->GetCategoryIdentify();
$menu_categoryrealestate   = $brand->GetCategoryRealestate();
$menu_categorybranchstatus = $brand->GetCategoryBranchStatus();
$menu_categoryidentify     = $brand->GetCategoryIdentify();
$menu_categoryrecall       = $brand->GetCategoryRecall();
$list_ppl                  = $brand->GetPeopleList();
$menu_ppl                  = $brand->ConvertOption($list_ppl, 'pId', 'pName');
$list_categorybank_twhg    = $brand->GetCategoryBank(array(8, 77, 68));
$menu_categorybank_twhg    = $brand->ConvertOption($list_categorybank_twhg, 'cId', 'cBankName');
$menu_accunused            = array('1' => '是');

##群組
$group = $brand->GetGroupList();

$menu_group[0] = '請選擇';
for ($i = 0; $i < count($group); $i++) {
    $menu_group[$group[$i]['bId']] = $group[$i]['bName'];
}
##

//取得總行(1)選單
$menu_bank = $brand->GetBankMenuList();
##

//取得分行(1)選單
$menu_branch = getBankBranch($conn, $data['bAccountNum1'], $data['bAccountNum2']);
##

//取得分行(2)選單
$menu_branch21 = getBankBranch($conn, $data['bAccountNum11'], $data['bAccountNum21']);
##

//取得分行(3)選單
$menu_branch22 = getBankBranch($conn, $data['bAccountNum12'], $data['bAccountNum22']);
##

//取得分行(4)選單
$menu_branch23 = getBankBranch($conn, $data['bAccountNum13'], $data['bAccountNum23']);
##

//取得回饋金分行選單
$menu_branch6 = getBankBranch($conn, $data['bAccountNum5'], $data['bAccountNum6']);
##

//修正地址縣市區域重複
$data['bAddress'] = filterCityAreaName($conn, $data['bZip'], $data['bAddress']);
$data['bAddr3']   = filterCityAreaName($conn, $data['bZip3'], $data['bAddr3']);
$data['bAddr2']   = filterCityAreaName($conn, $data['bZip2'], $data['bAddr2']);
##

$list_brand            = $brand->GetBrandList(array(8, 77));
$menu_brand            = $brand->ConvertOption($list_brand, 'bId', 'bName');
$menu_emailreceive     = array('1' => '有');
$menu_message          = array('1' => '有');
$menu_cashierorderhas  = array('1' => '有');
$menu_bServiceOrderHas = array('1' => '有');

//取得簡訊發送對象資料
$sql = '
	SELECT
		a.bId as sn,
		a.bNID as id,
		a.bName as bName,
		a.bMobile as bMobile,
		a.bDefault as bDefault,
		b.tTitle as tTitle
	FROM
		tBranchSms AS a
	JOIN
		tTitle_SMS AS b ON a.bNID=b.id
	WHERE
		a.bBranch="' . trim($_POST['id']) . '"
		AND a.bCheck_id = 0
		AND a.bDel = 0
	ORDER BY
		a.bNID,b.tTitle
	ASC
;';
$rs = $conn->Execute($sql);

$data_sms = array();
$i        = 0;
while (!$rs->EOF) {
    $data_sms[$i]               = $rs->fields;
    $data_sms[$i]['defaultSms'] = '';
    if ($rs->fields['bDefault'] == '1') {
        $data_sms[$i]['defaultSms'] = ' checked="checked"';
    }

    $i++;
    $rs->MoveNext();
}
##

//回饋通知簡訊
$sql = "SELECT
			fbs.*,
			b.tTitle AS fTitle
		FROM
			tFeedBackStoreSms AS fbs
		LEFT JOIN
			tTitle_SMS AS b ON fbs.fTitle=b.id
		WHERE
			fbs.fType = '2'
			AND fbs.fStoreId = '" . $_POST['id'] . "'
			AND fbs.fDelete = 0
		ORDER BY
			fbs.fTitle,fbs.fModifyTime ASC
		";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $data_feedsmsNotify[] = $rs->fields;
    $rs->MoveNext();
}

//回饋出款簡訊
$sql = '
	SELECT
		a.bId as sn,
		a.bNID as id,
		a.bName as bName,
		a.bMobile as bMobile,
		b.tTitle as tTitle
	FROM
		tBranchFeedback AS a
	LEFT JOIN
		tTitle_SMS AS b ON a.bNID=b.id
	WHERE
		a.bBranch="' . trim($_POST['id']) . '"

	ORDER BY
		a.bNID,a.bId
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

$data['bCashierOrderDate'] = $brand->ConvertDateToRoc($data['bCashierOrderDate'], Brand::DATE_FORMAT_NUM_DATE);
$data['bCashierOrderSave'] = $brand->ConvertDateToRoc($data['bCashierOrderSave'], Brand::DATE_FORMAT_NUM_DATE);

//設定回饋年度範圍
$FBYear = array();
for ($i = 2012; $i <= date("Y"); $i++) {
    $arr        = array();
    $FBYear[$i] = ($i - 1911) . '&nbsp;';

    $tmp = $arr = null;
    unset($tmp, $arr);

    $rs->MoveNext();
}
##

//建立簡訊對象身分
$sql = 'SELECT * FROM `tTitle_SMS` WHERE `tKind`=0 GROUP BY `tTitle` ORDER BY `tTitle` ASC;';
$rs  = $conn->Execute($sql);
while ($tmp = $rs->fields) {
    $sms_tNID .= '<option value="' . $tmp['id'] . '">' . $tmp['tTitle'] . "</option>\n";
    $tmp = null;unset($tmp);

    $rs->MoveNext();
}
##

//負責業務
$bSales = '';
$sql    = '
	SELECT
		a.bId,
		a.bStage,
		(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName
	FROM
		tBranchSales AS a
	WHERE
		bBranch="' . trim($_POST['id']) . '"
	ORDER BY
		bId
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

    $tmp[$tIndex] .= $rs->fields['bSalesName'];
    $tIndex++;

    $rs->MoveNext();
}
$bSales = implode(',', $tmp);
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

$today = DateChange(date('Y-m-d'));
function DateChange($date)
{
    $date = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", "", $date));
    $tmp  = explode('-', $date);

    if (preg_match("/0000/", $tmp[0])) {$tmp[0] = '000';} else { $tmp[0] -= 1911;}

    $date = $tmp[0] . '-' . $tmp[1] . '-' . $tmp[2];
    unset($tmp);
    return $date;
}
##

##
$sql = "SELECT sStore,sSignDate,sSales,(SELECT pName FROM tPeopleInfo WHERE pId=sSales) as SalesName FROM tSalesSign WHERE sType = 2 AND sStore ='" . $_POST['id'] . "' AND sSales != 0 ORDER BY sSales DESC";
$rs  = $conn->Execute($sql);

$signSales = array();
// 可能會有一間店有兩個業務簽約
while (!$rs->EOF) {
    if ($rs->fields['sStore']) {
        $signSales[$rs->fields['sSales']] = $rs->fields['SalesName'];
        $signData                         = $rs->fields;

        $signData['sSignDate'] = DateChange($rs->fields['sSignDate']);
        if ($signData['sSales'] != 0) {
            $signData['bContractStatus'] = '1';
        }
    }

    $rs->MoveNext();
}

if ($data['bStatusDateStart'] == '0000-00-00') {
    $data['bStatusDateStart'] = '000-00-00';
} else {
    $data['bStatusDateStart'] = DateChange($data['bStatusDateStart']);
}

if ($data['bStatusDateEnd'] == '0000-00-00') {
    $data['bStatusDateEnd'] = '000-00-00';
} else {
    $data['bStatusDateEnd'] = DateChange($data['bStatusDateEnd']);
}

if ($data['bSalesDate'] == '0000-00-00') {
    $data['bSalesDate'] = '000-00-00';
} else {
    $data['bSalesDate'] = DateChange($data['bSalesDate']);
}

//大小章圖檔是否存在
$imgStamp = '未指定圖檔 ...';
$sql      = 'SELECT * FROM tBranchStamp WHERE 1 AND bBranchId = "' . $_POST["id"] . '" ORDER BY bId DESC LIMIT 1;';
$rs       = $conn->Execute($sql);
if (!$rs->EOF) {
    $imgStamp = '<div onclick="newImg()" style="cursor:pointer;"><img src="showStamp.php?bId=' . $_POST["id"] . '" style="width:236px;height:150px;"></div>';
}
##

//回饋對象資料
$data_feedData = array();
$data_feedData = FeedBackData($_POST['id'], 2);
##

//舊店ID
if ($data['bOldStoreID'] > 0) {
    $tmp                   = $brand->GetBranch($data['bOldStoreID']);
    $data['bOldStoreCode'] = $tmp[0]['bCode2'];
}
##

$sql = "SELECT pId,pName FROM tPeopleInfo WHERE (pDep = 4 OR pDep = 7) AND pJob = 1";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $menu_sales[$rs->fields['pId']] = $rs->fields['pName'];
    $rs->MoveNext();
}
##

$sql = "SELECT * FROM tRgMoney WHERE rAccount = '" . $data['bCode2'] . "' AND rDate >= '" . date('Y-m') . "-01' AND rDate <= '" . date('Y-m') . "-31'";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $dataRg = $rs->fields;
    $rs->MoveNext();
}

##銀行
$sql       = "SELECT * FROM tBranchBank WHERE bBranch ='" . $_POST['id'] . "'";
$bankcount = 5;
$rs        = $conn->Execute($sql);

while (!$rs->EOF) {
    //bankbranch
    $rs->fields['bankbranch'] = getBankBranch($conn, $rs->fields['bBankMain'], $rs->fields['bBankBranch']);
    $rs->fields['no']         = $bankcount;
    if ($rs->fields['bUnUsed'] == 1) {
        $rs->fields['disabled'] = 'disabled=disabled';
        $rs->fields['checked']  = 'checked=checked';
    }

    $dataBank[] = $rs->fields;
    $bankcount++;

    $rs->MoveNext();
}
$data['bBackDocument'] = explode(",", $data['bBackDocument']);
##

$sql = "SELECT
			(SELECT bStore FROM tBranch WHERE bId = b.bBranch) AS branch,
			bRecall,
			(SELECT CONCAT(b.bCode,LPAD(bId,5,'0')) as bCode FROM tBranch WHERE bId = b.bBranch) AS code,
			bSignDate
		FROM tBrand AS b WHERE b.bId ='" . $data['bBrand'] . "' AND bRecall != '' AND bRecall != 0 ";
$rs = $conn->Execute($sql);

$AnotherFeedBack = $rs->fields;
$total           = $rs->RecordCount();

if ($total == 0) {
    $sql = "SELECT
			(SELECT bStore FROM tBranch WHERE bId = b.bBranch) AS branch,
			bRecall,
			(SELECT CONCAT(((Select bCode From `tBrand` c Where c.bId = bBrand )),LPAD(bId,5,'0')) as bCode FROM tBranch WHERE bId = b.bBranch) AS code,
			bSignDate
		FROM tBranchGroup AS b WHERE b.bId ='" . $data['bGroup'] . "' AND bRecall != ''";
    $rs              = $conn->Execute($sql);
    $AnotherFeedBack = $rs->fields;
}

##
//備註
$sql       = "SELECT * FROM tBranchNote WHERE bStore = '" . $_POST['id'] . "' AND bDel = 0";
$rs        = $conn->Execute($sql);
$data_note = array();
$i         = 1;
while (!$rs->EOF) {
    $rs->fields['no']          = $i;
    $rs->fields['bStatusName'] = ($rs->fields['bStatus'] == 0) ? '使用中' : '停用';
    $rs->fields['bNote']       = nl2br($rs->fields['bNote']);
    array_push($data_note, $rs->fields);
    $i++;
    $rs->MoveNext();
}
##

//埋log紀錄
$logs->writelog('formBranch', '查詢仲介店(' . $data['bStore'] . ' ' . $data['bCode2'] . ')');
##

/**
 * 資料庫連線方式改為 pdo
 *
 */
//取得建經活動禮品
function getActivityGifts($aId, $act)
{
    $conn = new first1DB;

    $sql = 'SELECT aId, aName FROM tActivityGifts WHERE aActivityId = :id;';
    $rs  = $conn->all($sql, ['id' => $aId]);

    foreach ($rs as $k => $v) {
        $checked           = (!empty($act['aGift']) && ($act['aGift'] == $v['aId'])) ? ' checked="checked" ' : '';
        $rs[$k]['checked'] = $checked;

        $checked = null;unset($checked);
    }

    return $rs;
}
##

//取得活動方案規則
function getActivityRules($aId, $act)
{
    $conn = new first1DB;

    $sql = 'SELECT aId, aTitle, aItem FROM tActivityRules WHERE aActivityId = :id;';
    $rs  = $conn->all($sql, ['id' => $aId]);

    foreach ($rs as $k => $v) {
        $checked           = (!empty($act['aRule']) && ($act['aRule'] == $v['aId'])) ? ' checked="checked" ' : '';
        $rs[$k]['checked'] = $checked;

        $checked = null;unset($checked);
    }

    return $rs;
}
##

$pdo = new first1DB;

//年度活動
$activities_active = [];

$sql = 'SELECT * FROM tActivityRecords WHERE aIdentity = :identity AND aStoreId = :store;';
$rs  = $pdo->all($sql, ['identity' => 'R', 'store' => $_POST['id']]);
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
        AND a.aTarget IN ("A", "R")
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
            // require dirname(__DIR__) . '/includes/activities/2/branch.php';
            $act_identity = 'R';
            require dirname(__DIR__) . '/includes/activities/2/campaign.php';
            $act_identity = null;unset($act_identity);
        }
        ##
    }
}
$rs = null;unset($rs);

$smarty->assign('activities', $activities);
##

//通路等級相關
$smarty->assign('channel_menu', ['A' => 'A', 'B' => 'B']);
##

//地政士績效業務
$sql = 'SELECT (SELECT pName FROM tPeopleInfo WHERE pId = a.bSales) as sales FROM tBranchSalesForPerformance AS a WHERE bBranch = :bId;';
$rs  = $pdo->one($sql, ['bId' => $_POST['id']]);
$smarty->assign('performanceSales', $rs['sales']);
$rs = null;unset($rs);
##

//個案傭金回饋
$menu_bIndividual = ['' => ''];

$sql = 'SELECT
            a.bId,
            a.bName,
            a.bStore,
            (SELECT bName FROM tBrand WHERE bId = bBrand) as brand,
            (SELECT bCode FROM tBrand WHERE bId = bBrand) as brandCode
        FROM
            tBranch AS a
        WHERE
            a.bBrand = 3
        ORDER BY
            a.bStore;'; //品牌為個案回饋(BM = 3)的店家
$rs = $pdo->all($sql);
if (!empty($rs)) {
    foreach ($rs as $k => $v) {
        $menu_bIndividual[$v['bId']] = empty($v['bStore']) ? '未命名' : $v['bStore'];
        $menu_bIndividual[$v['bId']] .= '(' . $v['brandCode'] . str_pad($v['bId'], 5, '0', STR_PAD_LEFT) . ')';
    }
}

$bIndividuals = [];
if (!empty($data['bIndividual'])) {
    $sql = 'SELECT bId, bStore FROM tBranch WHERE bId IN (' . $data['bIndividual'] . ')';
    $rs  = $pdo->all($sql);
    if (!empty($rs)) {
        foreach ($rs as $k => $v) {
            $bIndividuals[$v['bId']] = $v['bStore'];
        }
    }
}

// if (!empty($bIndividuals)) {
//     foreach ($bIndividuals as $k => $v) {
//         if ($menu_bIndividual[$k]) {
//             unset($menu_bIndividual[$k]);
//         }
//     }
// }

$rs  = null;unset($rs);
$pod = null;unset($pdo);
/**
 * ########################
 */
$accdis_disabled = 0;
//服務費先行撥付同意書 經辦部門不能修改
if ($data['bServiceOrderHas'] == 1 and ($_SESSION['member_pDep'] == 5 and $_SESSION['member_id'] != 1)) {
    $accdis_disabled = 1;
}

// echo '<pre>';
// print_r($data);exit;
$smarty->assign('address_disabled', $address_disabled);
$smarty->assign('sms_target', $sms_target);
$smarty->assign('menu_smsStyle', array('0' => '預設', '1' => '簽約日+買方姓名+賣方姓名+門牌+(店家簡訊固定文字)+服務費內容'));
$smarty->assign('menu_feedbackType', array('0' => '禁止觀看', '1' => '同品牌', '2' => '分店編號'));
$smarty->assign('menu_mark', array('0' => '不標記', '1' => '標記'));
$smarty->assign('menu_gift2020', array(1 => '7-11禮券', 2 => '全聯禮券'));
$smarty->assign('menu_act', array(0 => '未參加', 1 => '辦法一', 2 => '辦法二', 3 => '辦法三', 4 => '辦法四'));
$smarty->assign('menu_act2021', array(0 => '未參加', 1 => '辦法一'));
$smarty->assign('menu_gift2021', array(1 => '7-11禮券', 2 => '全聯禮券', 3 => '現金'));

$smarty->assign('data_note', $data_note);
$smarty->assign('AnotherFeedBack', $AnotherFeedBack);
$smarty->assign('menu_BackDocument', array(1 => '身分證', 2 => '存摺', 3 => '登記事項卡'));
$smarty->assign('bankcount', $bankcount);
$smarty->assign('ticketShow', $ticketShow);
$smarty->assign('menu_rg', array('1' => '是', '0' => '否'));
$smarty->assign('dataRg', $dataRg);
$smarty->assign('data_feedData', $data_feedData);
$smarty->assign('data_feedData_count', (!empty($data_feedData)) ? count($data_feedData) : 0);
$smarty->assign('dataBank', $dataBank);
$smarty->assign('locker', $locker);
$smarty->assign('signSales', $signSales);
$smarty->assign('signData', $signData);
$smarty->assign('today', $today);
$smarty->assign('menu_cstatus', array('1' => '是'));
$smarty->assign('_disabled', $_disabled);
$smarty->assign('stage', $stage);
$smarty->assign('from_sales', $from_sales); //判斷是否為業務責任區審核來的

$smarty->assign('menu_accunused', $menu_accunused);
$smarty->assign('is_edit', 1);
$smarty->assign('menu_group', $menu_group);
$smarty->assign('menu_categoryidentify', $menu_categoryidentify);
$smarty->assign('menu_categoryrealestate', $menu_categoryrealestate);
$smarty->assign('menu_categorybranchstatus', $menu_categorybranchstatus);
$smarty->assign('menu_categoryidentify', $menu_categoryidentify);
$smarty->assign('menu_categoryrecall', $menu_categoryrecall);
$smarty->assign('menu_categorybank_twhg', $menu_categorybank_twhg);
$smarty->assign('menu_emailreceive', $menu_emailreceive);
$smarty->assign('menu_message', $menu_message);
$smarty->assign('menu_cashierorderhas', $menu_cashierorderhas);
$smarty->assign('menu_bServiceOrderHas', $menu_bServiceOrderHas);
$smarty->assign('menu_brand', $menu_brand);
$smarty->assign('menu_ppl', $menu_ppl);
$smarty->assign('menu_note', array('' => '請選擇', 'INV' => 'INV', 'REC' => 'REC'));
$smarty->assign('menu_bank', $menu_bank); //總行(1)
$smarty->assign('menu_branch', $menu_branch); //分行(1)
$smarty->assign('menu_branch21', $menu_branch21); //分行(2)
$smarty->assign('menu_branch22', $menu_branch22); //分行(3)
$smarty->assign('menu_branch23', $menu_branch23); //分行(4)
$smarty->assign('accdis_disabled', $accdis_disabled); //解匯帳戶不給經辦人員修改

$smarty->assign('menu_bIndividual', $menu_bIndividual); //個案傭金回饋
$smarty->assign('bIndividuals', $bIndividuals); //顯示個案傭金對象

$smarty->assign('menu_branch6', $menu_branch6); //回饋金分行
$smarty->assign('menu_sales', $menu_sales);

$smarty->assign('bSales', $bSales);
$smarty->assign('FBYear', $FBYear);
$smarty->assign('FBYearSelect', Date("Y"));
$smarty->assign('address', $address);
//
$smarty->assign('data', $data);
$smarty->assign('data_sms', $data_sms);
$smarty->assign('data_feedsms', $data_feedsms);
$smarty->assign('data_feedsmsNotify', $data_feedsmsNotify);
$smarty->assign('sms_tNID', $sms_tNID);
$smarty->assign('listCity', listCity($conn, $data['bZip'])); //聯絡地址-縣市
$smarty->assign('listArea', listArea($conn, $data['bZip'])); //聯絡地址-區域

$smarty->assign('imgStamp', $imgStamp); //大小章圖檔
$smarty->assign('menu_feedDateCat', array(0 => '季', 1 => '月'));
$smarty->assign('FeedCity', listCity($conn)); //回饋金-縣市
$smarty->assign('smsEdit', '1');

$smarty->display('formbranch.inc.tpl', '', 'maintain');
