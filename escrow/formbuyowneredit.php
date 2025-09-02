<?php
// 生產環境錯誤報告設定
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/class/contract.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/class/member.class.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/class/getBank.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';
require_once dirname(__DIR__) . '/includes/escrow/contractbank.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCaseScrivener.class.php';

use First1\V1\PayByCase\PayByCaseScrivener;

//預載log物件
$logs = new Intolog();
##

$id = empty($_POST["id"])
? $_GET["id"]
: $_POST["id"];

if (empty($id)) {
    exit('Invalid access!!');
}

//20230325 指定顯示的頁籤
$_tabs = isset($_POST['_tabs']) && preg_match("/^\d+$/", $_POST['_tabs']) ? intval($_POST['_tabs']) : 0;
##

$advance            = new Advance();
$contract           = new Contract();
$brand              = new Brand();
$scrivener          = new Scrivener();
$member             = new Member();
$brand              = new brand();
$payByCaseScrivener = new payByCaseScrivener(new first1DB);

$data_case      = $contract->GetContract($id);
$data_realstate = $contract->GetRealstate($id);

$data_scrivener = $contract->GetScrivener($id);
//取得回饋金隨案支付銀行帳戶
$feedbackAccount = $payByCaseScrivener->getPayByCaseBankAccountSalesConfirm($id);

$data_income      = $contract->GetIncome($id);
$data_expenditure = $contract->GetExpenditure($id);
$data_invoice     = $contract->GetInvoice($id);
$data_buyer       = $contract->GetBuyer($id);

$data_buyer['count'] = count($contract->GetOthers($id, 1)) + 1;
if (preg_match("/等.*人/", $data_buyer['cName'])) {
    $data_buyer['count'] = 1; //他們會自己KEY等???人，所以就不顯示
}

$data_buyer1 = $contract->GetOthers($id, 6); //買代理人
$data_owner  = $contract->GetOwner($id);

//賣方備註用，收集賣方名子
$ownerArr[] = $data_owner['cName'];
$ownerOther = $contract->GetOthers($id, 2);
for ($i = 0; $i < count($ownerOther); $i++) {
    $ownerArr[] = $ownerOther[$i]['cName'];
}
$data_owner['count'] = count($ownerOther) + 1; //
unset($ownerOther);

//
if (preg_match("/等.*人/", $data_owner['cName'])) {
    $data_owner['count'] = 1; //他們會自己KEY等???人，所以就不顯示
}
$data_buyer5 = $contract->GetOthers($id, 5); //買方登記名義人

$data_land              = $contract->GetLandFirst($id, 0);
$data_owner1            = $contract->GetOthers($id, 7); //賣代理人
$data_furniture         = $contract->GetFurniture($id);
$data_ascription        = $contract->GetAscription($id);
$data_rent              = $contract->GetRent($id);
$data_LandCategory      = $contract->GetContractLandCategory($id);
$menu_landCategoryLand  = [1 => '買賣標的如為農地，不得做為興建農舍、建蔽率、通行權或套繪管制使用之土地，且賣方應檢附農業用地作農業使用證明書。', 2 => '買方知悉農地存有或可能有上述情形，仍同意履行契約。(未勾選視為買方不同意)'];
$menu_landCategoryBuild = [1 => '買賣標的如為建地，不得作為法定空地、建蔽率、容積率或通行權使用之土地，且須可申請為建造之建築用地，並應提供賣方或第三人土地使用權同意書(若無則免除)使買方得申請建造興建房屋，縱土地完成移轉登記予買方，賣方仍應擔保前述責任', 2 => '買方知悉建地存有或可能有上述情形，仍同意履行契約。(未勾選視為買方不同意) '];

$menu_LandFee = [1 => '買方負擔', 2 => '賣方負擔'];

//國籍代碼
$list_countrycode = $contract->GetCountryCode();
$menu_countrycode = [];

$menu_countrycode = $contract->ConvertOption($list_countrycode, 'cCode', 'cCountry');
array_unshift($menu_countrycode, '請選擇');

//
$list_categoryland       = $contract->GetCategoryLand();
$menu_categoryland       = $scrivener->ConvertOption($list_categoryland, 'cId', 'cName');
$menu_categoryrealestate = $contract->GetCategoryRealestate();
$menu_categorycontract   = $contract->GetCategoryContract();

//停車位判斷
$tmp = $contract->GetParking($id);

if (! empty($tmp) && isset($tmp[0]['cId']) && $tmp[0]['cId']) {
    $parking = 1;
} else {
    $parking = 0;
}

//建物
$sql = "SELECT * FROM tContractProperty WHERE cCertifiedId='" . $id . "'";
$rs  = $conn->Execute($sql);

$i = 0;
while (! $rs->EOF) {

    $data_property[$i] = $rs->fields;

    // 檢查並處理 land_movedate
    $land_movedate = isset($data_property[$i]['land_movedate']) ? $data_property[$i]['land_movedate'] : '';
    if (preg_match("/0000\-00\-00/", $land_movedate)) {$land_movedate = '';}
    $data_property[$i]['land_movedate'] = $advance->ConvertDateToRoc($land_movedate, base::DATE_FORMAT_NUM_DATE);

    // 檢查並處理 cRentDate
    $cRentDate = isset($data_property[$i]['cRentDate']) ? $data_property[$i]['cRentDate'] : '';
    if (preg_match("/0000\-00\-00/", $cRentDate)) {$cRentDate = '';}
    $data_property[$i]['cRentDate'] = $advance->ConvertDateToRoc($cRentDate, base::DATE_FORMAT_NUM_DATE);

    if (preg_match("/0000\-00\-00/", $data_property[$i]['cClosingDay'])) {$data_property[$i]['cClosingDay'] = '';} else { $data_property[$i]['cClosingDay'] = $advance->ConvertDateToRoc($data_property[$i]['cClosingDay'], base::DATE_FORMAT_NUM_DATE);}

    $data_property[$i]['cBuildDate'] = $advance->ConvertDateToRoc($data_property[$i]['cBuildDate'], base::DATE_FORMAT_NUM_DATE);

    //修正地址縣市區域重複
    $data_property[$i]['cAddr'] = filterCityAreaName($conn, $data_property[$i]['cZip'], $data_property[$i]['cAddr']);

    //建物類別
    $data_property[$i]['cObjUse']          = explode(',', $data_property[$i]['cObjUse']);
    $data_property[$i]['property_country'] = listCity($conn, $data_property[$i]['cZip']); //建物縣市
    $data_property[$i]['property_area']    = listArea($conn, $data_property[$i]['cZip']); //建物區域

    $sql = "SELECT zCity,zArea FROM tZipArea WHERE zZip='" . $data_property[$i]['cZip'] . "'";
    $rs2 = $conn->Execute($sql);

    $data_property[$i]['cAddr_country'] = $rs2->fields['zCity'] . $rs2->fields['zArea'];

    if ($data_property[$i]['cActualArea'] == 0) {
        if ($data_property[$i]['cPower2'] > 0) {
            $tmp_Area = round($data_property[$i]['cMeasureTotal'] * ($data_property[$i]['cPower1'] / $data_property[$i]['cPower2']), 2);

            $sql = "UPDATE tContractProperty SET cActualArea = '" . $tmp_Area . "' WHERE cCertifiedId='" . $id . "' AND cItem = '" . $data_property[$i]['cItem'] . "'";
            $conn->Execute($sql);

            $data_property[$i]['cActualArea'] = $tmp_Area;
        }
    }

    //20230523 新增建物座落地號
    $sql = 'SELECT cBuildingSession, cBuildingSessionExt, cBuildingLandNo FROM tContractPropertyBuildingLandNo WHERE cCertifiedId = "' . $id . '" AND cItem = "' . $data_property[$i]['cItem'] . '";';
    $_rs = $conn->Execute($sql);

    while (! $_rs->EOF) {
        $data_property[$i]['buildingLand'][] = $_rs->fields;
        $_rs->MoveNext();
    }
    $_rs = null;unset($_rs);
    ##

    $i++;
    $rs->MoveNext();
}
##

//取得法務催告項目
$sql = 'SELECT lId, lItem FROM tLegalItem ORDER BY lId ASC;';
$rs  = $conn->Execute($sql);

$menu_legal_items = [0 => '  '];
while (! $rs->EOF) {
    $menu_legal_items[$rs->fields['lId']] = $rs->fields['lItem'];
    $rs->MoveNext();
}
##

//取得法務催告紀錄
$sql = 'SELECT a.lId, a.lCertifiecId, a.lItem, (SELECT lItem FROM tLegalItem WHERE lId = a.lItem) as item, a.lDate, a.lRemark FROM tLegalNotify AS a WHERE a.lCertifiecId = "' . $id . '";';
$rs  = $conn->Execute($sql);

$legal_record = [];
if (! $rs->EOF) {
    $legal_record = $rs->fields;
}

$legal_record_edit = ($_SESSION['pLegalCaseNotify'] == '1') ? '' : 'disabled';
##

//契稅之歸屬
$data_ascription['cBuyer'] = explode(",", $data_ascription['cBuyer']);
$data_ascription['cOwner'] = explode(",", $data_ascription['cOwner']);

//第一組仲介店
$branch_opt = $brand->GetBranchList($data_realstate['cBrand'], $data_realstate['bCategory'], $data_case['cCaseStatus']);

$max            = count($branch_opt);
$branch_options = '';
for ($i = 0; $i < $max; $i++) {
    $branch_options .= '<option value="' . $branch_opt[$i]['bId'] . '"';
    if ($branch_opt[$i]['bId'] == $data_realstate['cBranchNum']) {$branch_options .= ' selected="selected"';}
    $branch_options .= '>' . $branch_opt[$i]['bStore'] . "</option>\n";
}
unset($max, $branch_opt);

//本票同意書換成服務費先行撥付同意書
if ($data_realstate['bServiceOrderHas1']) {$promissory1 = '有';} else { $promissory1 = '無';}
##

//第二組仲介店
$branch_opt1 = $brand->GetBranchList($data_realstate['cBrand1'], $data_realstate['bCategory1'], $data_case['cCaseStatus']);

$max             = count($branch_opt1);
$branch_options1 = '';
for ($i = 0; $i < $max; $i++) {
    $branch_options1 .= '<option value="' . $branch_opt1[$i]['bId'] . '"';
    if ($branch_opt1[$i]['bId'] == $data_realstate['cBranchNum1']) {$branch_options1 .= ' selected="selected"';}
    $branch_options1 .= '>' . $branch_opt1[$i]['bStore'] . "</option>\n";
}
unset($max, $branch_opt1);

if ($data_realstate['cBranchNum1'] == '0') {
    $second_branch = 'none';
} else {
    $second_branch = '';
}
unset($max, $branch_opt);

if ($data_realstate['bServiceOrderHas2']) {$promissory2 = '有';} else { $promissory2 = '無';}
##

//第三組仲介店
$branch_opt2 = $brand->GetBranchList($data_realstate['cBrand2'], $data_realstate['bCategory2'], $data_case['cCaseStatus']);

$max             = count($branch_opt2);
$branch_options2 = '';
for ($i = 0; $i < $max; $i++) {
    $branch_options2 .= '<option value="' . $branch_opt2[$i]['bId'] . '"';
    if (($branch_opt2[$i]['bId'] == $data_realstate['cBranchNum2']) && ($data_realstate['cBranchNum2'] != '0')) {$branch_options2 .= ' selected="selected"';}
    $branch_options2 .= '>' . $branch_opt2[$i]['bStore'] . "</option>\n";
}
unset($max, $branch_opt2);

if (($data_realstate['cBranchNum1'] == '0') || ($data_realstate['cBranchNum2'] == '0')) {
    $third_branch = 'none';
} else {
    $third_branch = '';
}
unset($max, $branch_opt2);

if ($data_realstate['bServiceOrderHas3']) {$promissory3 = '有';} else { $promissory3 = '無';}
##

//第四組仲介店
$branch_opt3 = $brand->GetBranchList($data_realstate['cBrand3'], $data_realstate['bCategory3'], $data_case['cCaseStatus']);

$max             = count($branch_opt3);
$branch_options3 = '';
for ($i = 0; $i < $max; $i++) {
    $branch_options3 .= '<option value="' . $branch_opt3[$i]['bId'] . '"';
    if (($branch_opt3[$i]['bId'] == $data_realstate['cBranchNum3']) && ($data_realstate['cBranchNum3'] != '0')) {$branch_options3 .= ' selected="selected"';}
    $branch_options3 .= '>' . $branch_opt3[$i]['bStore'] . "</option>\n";
}
unset($max, $branch_opt3);

if ($data_realstate['cBranchNum3'] == '0') {
    $fourth_branch = 'none';
} else {
    $fourth_branch = '';
}
unset($max, $branch_opt3);

if ($data_realstate['bServiceOrderHas3']) {$promissory3 = '有';} else { $promissory3 = '無';}
##

$int_money = 0;
//取得其他買賣方利息金額(增加查詢姓名及發票金額)
$sql = 'SELECT cInterestMoney,cInterestMoneyCheck,cInvoiceMoney,cInvoiceMoneyCheck,cName,cIdentity,cInvoiceDonate,cId,cInvoicePrint, cIdentifyId FROM tContractOthers WHERE cCertifiedId="' . $id . '" ORDER BY cId ASC;';

// 初始化陣列變數
$buyer_other = [];
$owner_other = [];

$rs = $conn->Execute($sql);
while (! $rs->EOF) {
    if ($rs->fields['cIdentity'] == 1) {
        $buyer_other[] = $rs->fields;
    } else if ($rs->fields['cIdentity'] == 2) {
        $owner_other[] = $rs->fields;
    }

    $int_money += (int) $rs->fields['cInterestMoney'];
    $rs->MoveNext();
}
unset($rs);

//取得利息
$sql       = 'SELECT * FROM tChecklist WHERE cCertifiedId="' . $id . '";';
$rs        = $conn->Execute($sql);
$int_total = '尚未產生利息';

if ($rs->RecordCount() > 0) {
    $int_total = (int) $rs->fields['cInterest'];
    $int_total += (int) $rs->fields['bInterest'];

    $int_total = '<span id="int_total">NT$' . $int_total . '元</span><input type="hidden" name="int_total" value="' . $int_total . '">';

    //取得買方利息金額
    $sql = 'SELECT cInterestMoney FROM tContractBuyer WHERE cCertifiedId="' . $id . '";';
    $rs  = $conn->Execute($sql);
    $int_money += (int) $rs->fields['cInterestMoney'];
    unset($rs);
    ##

    //取得賣方利息金額
    $sql = 'SELECT cInterestMoney FROM tContractOwner WHERE cCertifiedId="' . $id . '";';
    $rs  = $conn->Execute($sql);
    $int_money += $rs->fields['cInterestMoney'] + 1 - 1;
    unset($rs);
    ##

    //取得仲介利息金額
    $sql = 'SELECT cInterestMoney,cInterestMoney1,cInterestMoney2, cInterestMoney3 FROM tContractRealestate WHERE cCertifyId="' . $id . '";';
    $rs  = $conn->Execute($sql);
    $int_money += $rs->fields['cInterestMoney'] + 1 - 1;
    $int_money += $rs->fields['cInterestMoney1'] + 1 - 1;
    $int_money += $rs->fields['cInterestMoney2'] + 1 - 1;
    $int_money += $rs->fields['cInterestMoney3'] + 1 - 1;
    unset($rs);
    ##

    //取得代書利息金額
    $sql = 'SELECT cInterestMoney,cInvoiceDonate FROM tContractScrivener WHERE cCertifiedId="' . $id . '";';
    $rs  = $conn->Execute($sql);
    $int_money += $rs->fields['cInterestMoney'] + 1 - 1;
    unset($rs);
    ##

    //利息指定對象
    $sql = "SELECT * FROM  tContractInterestExt  WHERE cCertifiedId ='" . $id . "'";
    $rs  = $conn->Execute($sql);

    $i = 0;
    while (! $rs->EOF) {
        $data_int_another[] = $rs->fields;
        $int_money += $rs->fields['cInterestMoney'] + 1 - 1;

        $rs->MoveNext();
    }

    $int_total .= '<span id="int_money">(已分配：' . $int_money . '元)</span><input type="hidden" name="int_money" value="' . $int_money . '">';
}
##

//找出各代書對應之承辦人
$undertaker = '';
$sql        = '
	SELECT
		(SELECT pName FROM tPeopleInfo WHERE pId=b.sUndertaker1) as undertaker,
		b.sName AS ScrivenerName
	FROM
		tContractScrivener AS a
	JOIN
		tScrivener as b ON b.sId=a.cScrivener
	WHERE
		a.cCertifiedId="' . $id . '"
';
$rs            = $conn->Execute($sql);
$undertaker    = $rs->fields['undertaker'];
$ScrivenerName = $rs->fields['ScrivenerName'];

$feedbackScrivenerCheck = 1; //檢查是否是業務專用的地政士;

if (preg_match("/業務專用/", $ScrivenerName)) {
    $feedbackScrivenerCheck = 0;
}
##

//取得設定回饋金對象
$fbcheckedR['1']  = ' checked="checked"';
$fbcheckedS['2']  = '';
$fbcheckedR['11'] = ' checked="checked"';
$fbcheckedS['12'] = '';
$fbcheckedR['21'] = ' checked="checked"';
$fbcheckedS['22'] = '';
$fbcheckedR['31'] = ' checked="checked"';
$fbcheckedS['32'] = '';

if ($data_case['cFeedbackTarget'] == '2') {
    $fbcheckedR['1'] = '';
    $fbcheckedS['2'] = ' checked="checked"';
}

if ($data_case['cFeedbackTarget1'] == '2') {
    $fbcheckedR['11'] = '';
    $fbcheckedS['12'] = ' checked="checked"';
}

if ($data_case['cFeedbackTarget2'] == '2') {
    $fbcheckedR['21'] = '';
    $fbcheckedS['22'] = ' checked="checked"';
}

if ($data_case['cFeedbackTarget3'] == '3') {
    $fbcheckedR['31'] = '';
    $fbcheckedS['32'] = ' checked="checked"';
}

if ($data_rent['cRentDate'] != '0000-00-00') {
    $data_rent['cRentDate'] = $advance->ConvertDateToRoc($data_rent['cRentDate'], base::DATE_FORMAT_NUM_DATE);
}

//埋log紀錄
$logs->writelog('escrowQuery', '查詢案件(保證號碼:' . $id . ')');
##
$sql         = "SELECT sName FROM tStatusCase WHERE sId = '" . $data_case['cCaseStatus'] . "'";
$rs          = $conn->Execute($sql);
$cCaseStatus = $rs->fields['sName'];
##

$list_material           = $contract->GetMaterialsList();
$menu_material           = $contract->ConvertOption($list_material, 'bTypeId', 'bTypeName');
$list_objkind            = $contract->GetObjKind();
$menu_objkind            = $contract->ConvertOption($list_objkind, 'oTypeId', 'oTypeName');
$list_ObjUse             = $contract->GetObjUse();
$menu_objUse             = $contract->ConvertOption($list_ObjUse, 'uId', 'uName');
$list_statuscontract     = $contract->GetStatusContract($cCaseStatus);
$menu_statuscontract     = $contract->ConvertOption($list_statuscontract, 'sId', 'sName');
$list_statusexpenditure  = $contract->GetStatusExpenditure();
$menu_statusexpenditure  = $contract->ConvertOption($list_statusexpenditure, 'sId', 'sName');
$list_statusincome       = $contract->GetStatusIncome();
$menu_StatusIncome       = $contract->ConvertOption($list_statusincome, 'sId', 'sName');
$list_categroyrealestate = $contract->GetCategroyRealestate();
$menu_categroyrealestate = $contract->ConvertOption($list_categroyrealestate, 'cId', 'cName');
$list_categroyexception  = $contract->GetCategoryException();
$menu_categroyexception  = $contract->ConvertOption($list_categroyexception, 'sId', 'sName');
$menu_reportupload       = ['0' => '預設上傳', '1' => '關閉不上傳(品牌、群義品牌)'];
$list_categorybank_twhg  = $contract->GetContractBank();
$menu_categorybank_twhg  = $contract->ConvertOption($list_categorybank_twhg, 'cBankCode', 'bankName');
$list_scrivener          = $scrivener->GetListScrivener();
$menu_scrivener          = [];
$menu_scrivener[0]       = '--------';

foreach ($list_scrivener as $k => $v) {
    $menu_scrivener[$v['sId']] = 'SC' . str_pad($v['sId'], 4, 0, STR_PAD_LEFT) . $v['sName'];
}

$menu_budlevel          = $scrivener->GetBudLevel();
$menu_categorysex       = $contract->GetCategorySex();
$menu_categorycar       = $contract->GetCategoryCar();
$menu_categorycertifyid = $contract->GetCategoryCertifyID();
$list_brand             = $contract->GetCategoryBrand();
$menu_brand             = $contract->ConvertOption($list_brand, 'bId', 'bName', 1);
$menu_categoryarea      = $contract->GetCategoryAreaMenuList();

//取得總行(1)選單
$menu_bank = $contract->GetBankMenuList();
##

//取得分行(1)選單
$owner_menu_branch = getBankBranch($conn, $data_owner['cBankKey2'], $data_owner['cBankBranch2']);
##

//取得分行(2)選單
$buyer_menu_branch = getBankBranch($conn, $data_buyer['cBankKey2'], $data_buyer['cBankBranch2']);
##

$case_undertaker = $member->GetMemberInfo($data_case['cUndertakerId'], 1);
$case_lasteditor = $member->GetMemberInfo($data_case['cLastEditor'], 1);

$data_case['cLastTime'] = $advance->ConvertDateToRoc($data_case['cLastTime'], base::DATE_FORMAT_NUM_TIME);

$data_case['cEndDate']     = $advance->ConvertDateToRoc($data_case['cEndDate'], base::DATE_FORMAT_NUM_DATE);
$data_case['cFinishDate']  = $advance->ConvertDateToRoc($data_case['cFinishDate'], base::DATE_FORMAT_NUM_DATE);
$data_case['cFinishDate2'] = $advance->ConvertDateToRoc($data_case['cFinishDate2'], base::DATE_FORMAT_NUM_DATE);
$data_case['cApplyDate']   = $advance->ConvertDateToRoc($data_case['cApplyDate'], base::DATE_FORMAT_NUM_DATE);
$data_case['cAffixDate']   = $advance->ConvertDateToRoc($data_case['cAffixDate'], base::DATE_FORMAT_NUM_DATE);
$data_case['cSignDate']    = $advance->ConvertDateToRoc($data_case['cSignDate'], base::DATE_FORMAT_NUM_DATE);
$data_case['cFirstDate']   = $advance->ConvertDateToRoc($data_case['cFirstDate'], base::DATE_FORMAT_NUM_DATE);

$data_buyer['cBirthdayDay'] = $advance->ConvertDateToRoc($data_buyer['cBirthdayDay'], base::DATE_FORMAT_NUM_DATE);

if (preg_match("/0000-00-00/", $data_buyer['cPaymentDate'])) {$data_buyer['cPaymentDate'] = '';} else { $data_buyer['cPaymentDate'] = $advance->ConvertDateToRoc($data_buyer['cPaymentDate'], base::DATE_FORMAT_NUM_DATE);}

$data_owner['cBirthdayDay'] = $advance->ConvertDateToRoc($data_owner['cBirthdayDay'], base::DATE_FORMAT_NUM_DATE);

if (preg_match("/0000-00-00/", $data_owner['cPaymentDate'])) {$data_owner['cPaymentDate'] = '';} else { $data_owner['cPaymentDate'] = $advance->ConvertDateToRoc($data_owner['cPaymentDate'], base::DATE_FORMAT_NUM_DATE);}

if (($data_case['cCaseStatus'] == '0') || ($data_case['cCaseStatus'] == '1') || ($data_case['cCaseStatus'] == '2') || ($data_case['cCaseStatus'] == '6') || ($_SESSION['member_bankcheck'] == '1')) {
    $limit_show = 0; // 可變更    0：無    1：已申請    2：進行中    6：異常 pBankCheck權限為1(20140624)
} else {
    $limit_show = 1; // 不可變更
}

//取得設定是否回饋
$feedback['1']  = ' checked="checked"'; //回饋(0)
$feedback['2']  = '';                   //不回饋(1)
$feedback['11'] = ' checked="checked"'; //回饋(0)
$feedback['12'] = '';                   //不回饋(1)
$feedback['21'] = ' checked="checked"'; //回饋(0)
$feedback['22'] = '';                   //不回饋(1)
$feedback['31'] = ' checked="checked"'; //回饋(0)
$feedback['32'] = '';                   //不回饋(1)

if ($data_case['cCaseFeedback'] == '1') { //-->不回饋
    $feedback['1'] = '';                      //回饋(0)
    $feedback['2'] = ' checked="checked"';    //不回饋(1)
}

if ($data_case['cCaseFeedback1'] == '1') { //-->不回饋
    $feedback['11'] = '';                      //回饋(0)
    $feedback['12'] = ' checked="checked"';    //不回饋(1)
}

if ($data_case['cCaseFeedback2'] == '1') { //-->不回饋
    $feedback['21'] = '';                      //回饋(0)
    $feedback['22'] = ' checked="checked"';    //不回饋(1)
}

if ($data_case['cCaseFeedback3'] == '1') { //-->不回饋
    $feedback['31'] = '';                      //回饋(0)
    $feedback['32'] = ' checked="checked"';    //不回饋(1)
}
##

//是否可調整回饋金權限
$_disabled  = ' disabled="disabled"';
$fbDisabled = ' disabled="disabled"';

if ($_SESSION['member_pFeedBackModify'] == '1' && $_SESSION['member_pCaseFeedBackModify'] == '2') {
    $_disabled  = '';
    $fbDisabled = '';
}
##

//回饋金代書關閉欄位
$scrivenerDisabled = '';
if ($data_case['cFeedBackScrivenerClose'] == 1) {
    $scrivenerDisabled = ' disabled';
}

//是否可調整保證金權限
if ($data_case['cCaseFeedBackModifier'] == '') {
    $certifiedchg = '';
} else if ($_SESSION['member_pFeedBackModify'] == '1' && $_SESSION['member_pCaseFeedBackModify'] == '2') {
    $certifiedchg = '';
} else {
    $certifiedchg = ' disabled="disabled"';
}
##

//取得地政士資料
$scr = [];
$sql = 'SELECT * FROM tScrivener WHERE sId="' . $data_scrivener['cScrivener'] . '";';
$rs  = $conn->Execute($sql);
$scr = $rs->fields;

$data_scrivener['sName'] = $rs->fields['sName'];
$scr_sSpRecall           = $rs->fields['sSpRecall'];
$scr_sCategory           = $rs->fields['sCategory'];
$scr_sFeedbackMoney      = $rs->fields['sFeedbackMoney'];
##

//取得第一組仲介資料
if ($data_realstate['cBranchNum'] > 0) {
    $rel1 = [];
    $sql  = 'SELECT * FROM tBranch WHERE bId="' . $data_realstate['cBranchNum'] . '";';
    $rs   = $conn->Execute($sql);
    $rel1 = $rs->fields;

    //回饋對象資料 20170606 改用欄位(bCooperationHas)去看
    $data_feedData1       = FeedBackData($data_realstate['cBranchNum'], 2);
    $data_feedDataCount1  = $rel1['bCooperationHas'];
    $data_bFeedbackMoney1 = $rel1['bFeedbackMoney']; //未收足也要回饋

    $rel1['note'] = branchNote($data_realstate['cBranchNum']);
}
##

//取得第二組仲介資料
$rel2 = []; // 初始化為空陣列，設置預設值
if ($data_realstate['cBranchNum1'] > 0) {
    $sql = 'SELECT * FROM tBranch WHERE bId="' . $data_realstate['cBranchNum1'] . '";';
    $rs  = $conn->Execute($sql);
    if ($rs && ! $rs->EOF) {
        $rel2 = $rs->fields;

        //回饋對象資料 20170606 改用欄位(bCooperationHas)去看
        $data_feedData2       = FeedBackData($data_realstate['cBranchNum1'], 2);
        $data_feedDataCount2  = $rel2['bCooperationHas'];
        $data_bFeedbackMoney2 = $rel2['bFeedbackMoney']; //未收足也要回饋

        $rel2['note'] = branchNote($data_realstate['cBranchNum1']);
    }
}

// 確保 $rel2 有必要的預設值
if (empty($rel2)) {
    $rel2 = [
        'bZip'         => '',
        'bZip2'        => '',
        'bZip3'        => '',
        'bStatus'      => '',
        'bAccountNum5' => '',
        'bAccountNum6' => '',
    ];
}
##

//取得第三組仲介資料
$rel3 = []; // 初始化為空陣列
if ($data_realstate['cBranchNum2'] > 0) {
    $sql = 'SELECT * FROM tBranch WHERE bId="' . $data_realstate['cBranchNum2'] . '";';
    $rs  = $conn->Execute($sql);
    if ($rs && ! $rs->EOF) {
        $rel3 = $rs->fields;

        //回饋對象資料
        $data_feedData2       = FeedBackData($data_realstate['cBranchNum2'], 2);
        $data_feedDataCount3  = $rel3['bCooperationHas']; //
        $data_bFeedbackMoney3 = $rel3['bFeedbackMoney'];  //未收足也要回饋

        $rel3['note'] = branchNote($data_realstate['cBranchNum2']);
    }
}

// 確保 $rel3 有必要的預設值
if (empty($rel3)) {
    $rel3 = [
        'bZip'         => '',
        'bZip2'        => '',
        'bZip3'        => '',
        'bStatus'      => '',
        'bAccountNum5' => '',
        'bAccountNum6' => '',
    ];
}
##

//取得第四組仲介資料
$rel4 = []; // 初始化為空陣列
if ($data_realstate['cBranchNum3'] > 0) {
    $sql = 'SELECT * FROM tBranch WHERE bId="' . $data_realstate['cBranchNum3'] . '";';
    $rs  = $conn->Execute($sql);
    if ($rs && ! $rs->EOF) {
        $rel4 = $rs->fields;

        //回饋對象資料
        $data_feedData3       = FeedBackData($data_realstate['cBranchNum3'], 2);
        $data_feedDataCount4  = $rel4['bCooperationHas']; //
        $data_bFeedbackMoney4 = $rel4['bFeedbackMoney'];  //未收足也要回饋

        $rel4['note'] = branchNote($data_realstate['cBranchNum3']);
    }
}

// 確保 $rel4 有必要的預設值
if (empty($rel4)) {
    $rel4 = [
        'bZip'         => '',
        'bZip2'        => '',
        'bZip3'        => '',
        'bStatus'      => '',
        'bAccountNum5' => '',
        'bAccountNum6' => '',
    ];
}
##

//設定仲介服務對象
$STargetOption   = [1 => '買賣方', 2 => '賣方', 3 => '買方'];
$cServiceTarget  = $data_realstate['cServiceTarget'];
$cServiceTarget1 = $data_realstate['cServiceTarget1'];
$cServiceTarget2 = $data_realstate['cServiceTarget2'];
$cServiceTarget3 = $data_realstate['cServiceTarget3'];
##

//修正地址縣市區域重複
$data_owner['cRegistAddr'] = filterCityAreaName($conn, $data_owner['cRegistZip'], $data_owner['cRegistAddr']);
##

$TaxReceipt = [1 => '賣方', 2 => '買方', 3 => '無'];
$caseSales  = []; //案件相關業務

//地政士業務
$sql = 'SELECT
		a.sId,
		a.sSales,
		(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
		b.sOffice,
		b.sSpRecall,
		b.sSpRecall2,
		b.sBrand
	FROM
		tScrivenerSales AS a,
		tScrivener AS b
	WHERE
		a.sScrivener=' . $data_scrivener['cScrivener'] . ' AND
		b.sId=a.sScrivener
	ORDER BY
		sId
	ASC';

$rs                           = $conn->Execute($sql);
$scrivener_office             = $rs->fields['sOffice'];
$scrivener_sSpRecall          = $rs->fields['sSpRecall'];
$data_scrivener['sSpRecall2'] = $rs->fields['sSpRecall2'];

if ($data_scrivener['sSpRecall2'] != '' && $data_case['cScrivenerSpRecall2'] == '') { //如果沒有存到特殊回饋，就在寫入一次
    $data_case['cScrivenerSpRecall2'] = $data_scrivener['sSpRecall2'];
}

$scrivener_brand         = $rs->fields['sBrand'];
$menu_scrivener_sales[0] = '請選擇';
$tmp                     = [];
while (! $rs->EOF) {
    $tmp[]                                   = $rs->fields['sSalesName'];
    $caseSales[$rs->fields['sSales']]        = $rs->fields['sSales'];     //地政士
    $scrivener_option[$rs->fields['sSales']] = $rs->fields['sSalesName']; //業務下拉
    $rs->MoveNext();
}

$scrivener_sales = implode(',', $tmp);

unset($tmp);
$branch_count = 0;

//仲介店(1)業務
if ($data_realstate['cBranchNum'] > 0) {
    $sql = 'SELECT
					(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand'] . '") as brandName,
					b.bName,
					b.bStore,
					b.bCategory
				FROM

					tBranch AS b
				WHERE
					bId=' . $data_realstate['cBranchNum'] . '
				ORDER BY
					bId
				ASC';
    $rs           = $conn->Execute($sql);
    $brand_type1  = $rs->fields['brandName'];
    $branch_type1 = $rs->fields['bStore'];
    $branch_cat1  = $menu_categoryrealestate[$rs->fields['bCategory']];

    $sql = 'SELECT
					a.bId,
					a.bSales,
					(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
					(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand'] . '") as brandName,
					b.bName,
					b.bStore,
					b.bCategory
				FROM
					tBranchSales AS a,
					tBranch AS b
				WHERE
					bBranch=' . $data_realstate['cBranchNum'] . ' AND
					b.bId=a.bBranch
				ORDER BY
					bId
				ASC';
    $rs = $conn->Execute($sql);

    $tmp = [];
    while (! $rs->EOF) {
        $tmp[] = $rs->fields['bSalesName'];
        if ($data_realstate['cBranchNum'] != 505) {
            $caseSales[$rs->fields['bSales']] = $rs->fields['bSales'];
        }
        $rs->MoveNext();
    }

    $branchnum_data_sales = implode(',', $tmp);
    if ($data_realstate['cBranchNum'] == 505) { //判斷回饋對象為地政士還是業務
        $branchnum_sales = $scrivener_sales;
    } else {
        $branchnum_sales = implode(',', $tmp);
    }

    unset($tmp);
    $branch_count++;
}

//仲介店(2)業務
if ($data_realstate['cBranchNum1'] > 0) {
    $sql = 'SELECT
					(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand1'] . '") as brandName,
					b.bName,
					b.bStore,
					b.bCategory
				FROM
					tBranch AS b
				WHERE
					bId=' . $data_realstate['cBranchNum1'] . '
				ORDER BY
					bId
				ASC';
    $rs           = $conn->Execute($sql);
    $brand_type2  = $rs->fields['brandName'];
    $branch_type2 = $rs->fields['bStore'];
    $branch_cat2  = $menu_categoryrealestate[$rs->fields['bCategory']];

    $sql = 'SELECT
					a.bId,
					a.bSales,
					(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
					(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand1'] . '") as brandName,
					b.bName,
					b.bStore,
					b.bCategory
				FROM
					tBranchSales AS a,
					tBranch AS b
				WHERE
					bBranch=' . $data_realstate['cBranchNum1'] . ' AND
					b.bId=a.bBranch
				ORDER BY
					bId
				ASC';
    $rs = $conn->Execute($sql);

    $tmp = [];
    while (! $rs->EOF) {
        $tmp[]                            = $rs->fields['bSalesName'];
        $caseSales[$rs->fields['bSales']] = $rs->fields['bSales'];
        $rs->MoveNext();
    }

    $branchnum_data_sales1 = implode(',', $tmp);
    if ($data_realstate['cBranchNum1'] == 505) { //判斷回饋對象為地政士還是業務
        $branchnum_sales1 = $scrivener_sales;
    } else {
        $branchnum_sales1 = implode(',', $tmp);
    }

    unset($tmp);
    $branch_count++;
}

//仲介店(3)業務
if ($data_realstate['cBranchNum2'] > 0) {
    $sql = 'SELECT
					(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand2'] . '") as brandName,
					b.bName,
					b.bStore,
					b.bCategory
				FROM

					tBranch AS b
				WHERE
					bId=' . $data_realstate['cBranchNum2'] . '
				ORDER BY
					bId
				ASC';
    $rs           = $conn->Execute($sql);
    $brand_type3  = $rs->fields['brandName'];
    $branch_type3 = $rs->fields['bStore'];
    $branch_cat3  = $menu_categoryrealestate[$rs->fields['bCategory']];

    $sql = 'SELECT
				a.bId,
				a.bSales,
				(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
				(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand2'] . '") as brandName,
				b.bName,
				b.bStore,
				b.bCategory
			FROM
				tBranchSales AS a,
				tBranch AS b
			WHERE
				bBranch=' . $data_realstate['cBranchNum2'] . ' AND
				b.bId=a.bBranch
			ORDER BY
				bId
			ASC';

    $rs = $conn->Execute($sql);

    $tmp = [];
    while (! $rs->EOF) {
        $tmp[]                            = $rs->fields['bSalesName'];
        $caseSales[$rs->fields['bSales']] = $rs->fields['bSales'];
        $rs->MoveNext();
    }

    $branchnum_data_sales2 = implode(',', $tmp);
    if ($data_realstate['cBranchNum2'] == 505) { //判斷回饋對象為地政士還是業務
        $branchnum_sales2 = $scrivener_sales;
    } else {
        $branchnum_sales2 = implode(',', $tmp);
    }

    unset($tmp);
    $branch_count++;
}

//仲介店(4)業務
if ($data_realstate['cBranchNum3'] > 0) {
    $sql = 'SELECT
					(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand3'] . '") as brandName,
					b.bName,
					b.bStore,
					b.bCategory
				FROM

					tBranch AS b
				WHERE
					bId=' . $data_realstate['cBranchNum3'] . '
				ORDER BY
					bId
				ASC';
    $rs           = $conn->Execute($sql);
    $brand_type4  = $rs->fields['brandName'];
    $branch_type4 = $rs->fields['bStore'];
    $branch_cat4  = $menu_categoryrealestate[$rs->fields['bCategory']];

    $sql = 'SELECT
				a.bId,
				a.bSales,
				(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
				(SELECT bName FROM tBrand WHERE bId="' . $data_realstate['cBrand3'] . '") as brandName,
				b.bName,
				b.bStore,
				b.bCategory
			FROM
				tBranchSales AS a,
				tBranch AS b
			WHERE
				bBranch=' . $data_realstate['cBranchNum3'] . ' AND
				b.bId=a.bBranch
			ORDER BY
				bId
			ASC';
    $rs = $conn->Execute($sql);

    $tmp = [];
    while (! $rs->EOF) {
        $tmp[]                            = $rs->fields['bSalesName'];
        $caseSales[$rs->fields['bSales']] = $rs->fields['bSales'];
        $rs->MoveNext();
    }

    $branchnum_data_sales3 = implode(',', $tmp);
    if ($data_realstate['cBranchNum3'] == 505) { //判斷回饋對象為地政士還是業務
        $branchnum_sales3 = $scrivener_sales;
    } else {
        $branchnum_sales3 = implode(',', $tmp);
    }

    unset($tmp);
    $branch_count++;
}
##

//業務下拉
$sql             = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(4,7,8) AND pJob=1";
$rs              = $conn->Execute($sql);
$tmp             = [];
$sales_option[0] = '';
while (! $rs->EOF) {
    $sales_option[$rs->fields['pId']] = $rs->fields['pName'];
    $rs->MoveNext();
}
##

//負責業務(仲介店一)
$bSales = '';
$sql    = '
	SELECT
		cId,
		cCertifiedId,
		cSalesId,
		cBranch,
		(SELECT pName FROM tPeopleInfo WHERE pId=cSalesId ) as bSalesName,
		(SELECT pJob FROM tPeopleInfo WHERE pId=cSalesId ) as pJob,
		(SELECT pTransfer FROM tPeopleInfo WHERE pId=cSalesId ) as transfer
	FROM
		 tContractSales
	WHERE
		cBranch="' . $data_realstate['cBranchNum'] . '"
	AND
		cCertifiedId =' . $id . '
';
$rs = $conn->Execute($sql);

$tmp = [];
while (! $rs->EOF) {
    if ($_SESSION['member_pDep'] == 7 || $data_case['cFeedBackClose'] == 1) {
        $tmp[] = '<span style="padding:2px;background-color:yellow;">' . $rs->fields['bSalesName'] . '</span>';
    } else {
        $tmp[] = '<span style="padding:2px;background-color:yellow;"><span onclick="del(1,' . $rs->fields['cSalesId'] . ',' . $rs->fields['cBranch'] . ')" style="cursor:pointer;">X</span>' . $rs->fields['bSalesName'] . '</span>';
    }

    $rs->MoveNext();
}

$Sales1 = implode(',', $tmp);
unset($tmp);

//負責業務(仲介店二)
$bSales = '';
$sql    = '
	SELECT
		cId,
		cCertifiedId,
		cSalesId,
		cBranch,
		(SELECT pName FROM tPeopleInfo WHERE pId=cSalesId ) as bSalesName,
		(SELECT pJob FROM tPeopleInfo WHERE pId=cSalesId ) as pJob,
		(SELECT pTransfer FROM tPeopleInfo WHERE pId=cSalesId ) as transfer
	FROM
		 tContractSales
	WHERE
		cBranch="' . $data_realstate['cBranchNum1'] . '"
	AND
		cCertifiedId =' . $id . '
';
$rs  = $conn->Execute($sql);
$tmp = [];
while (! $rs->EOF) {
    if ($rs->fields['pJob'] == 2) {
        $tmp2                     = transfer($conn, $rs->fields['transfer']);
        $rs->fields['cSalesId']   = $tmp2[0];
        $rs->fields['bSalesName'] = $tmp2[1];
        unset($tmp2);
    }

    if ($_SESSION['member_pDep'] == 7 || $data_case['cFeedBackClose'] == 1) {
        $tmp[] = '<span style="padding:2px;background-color:yellow;">' . $rs->fields['bSalesName'] . '</span>';
    } else {
        $tmp[] = '<span style="padding:2px;background-color:yellow;"><span onclick="del(2,' . $rs->fields['cSalesId'] . ',' . $rs->fields['cBranch'] . ')" style="cursor:pointer;">X</span>' . $rs->fields['bSalesName'] . '</span>';
    }

    $rs->MoveNext();
}

$Sales2 = implode(',', $tmp);
unset($tmp);

//負責業務(仲介店三)
$bSales = '';
$sql    = '
	SELECT
		cId,
		cCertifiedId,
		cSalesId,
		cBranch,
		(SELECT pName FROM tPeopleInfo WHERE pId=cSalesId ) as bSalesName,
		(SELECT pJob FROM tPeopleInfo WHERE pId=cSalesId ) as pJob,
		(SELECT pTransfer FROM tPeopleInfo WHERE pId=cSalesId ) as transfer
	FROM
		 tContractSales
	WHERE
		cBranch="' . $data_realstate['cBranchNum2'] . '"
	AND
		cCertifiedId =' . $id . '
';
$rs  = $conn->Execute($sql);
$tmp = [];
while (! $rs->EOF) {
    if ($rs->fields['pJob'] == 2) {
        $tmp2                     = transfer($conn, $rs->fields['transfer']);
        $rs->fields['cSalesId']   = $tmp2[0];
        $rs->fields['bSalesName'] = $tmp2[1];
        unset($tmp2);
    }

    if ($_SESSION['member_pDep'] == 7 || $data_case['cFeedBackClose'] == 1) {
        $tmp[] = '<span style="padding:2px;background-color:yellow;">' . $rs->fields['bSalesName'] . '</span>';
    } else {
        $tmp[] = '<span style="padding:2px;background-color:yellow;"><span onclick="del(3,' . $rs->fields['cSalesId'] . ',' . $rs->fields['cBranch'] . ')" style="cursor:pointer;">X</span>' . $rs->fields['bSalesName'] . '</span>';
    }
    $rs->MoveNext();
}

$Sales3 = implode(',', $tmp);
unset($tmp);
###

//處理買、賣方電話
$tmp          = explode(',', $data_owner['cMobileNum']); //賣
$owner_mobile = $tmp[0];

$tmp        = explode(',', $data_buyer['cMobileNum']); //買
$buy_mobile = $tmp[0];

unset($tmp);
##

##特殊回饋金
$sSpRecall = '';
$check     = 0;

if ($data_realstate['cBrand'] != 1) {
    if ($data_realstate['cBrand'] != 49) {
        if ($data_realstate['cBrand'] != 2) {
            $check = 1;
        }
    }
} else if ($data_realstate['cBrand1'] != 1 && $data_realstate['cBrand1'] != '0') {
    if ($data_realstate['cBrand1'] != 49) {
        if ($data_realstate['cBrand1'] != 2) {
            $check = 1;
        }
    }
} else if ($data_realstate['cBrand2'] != 1 && $data_realstate['cBrand2'] != '0') {
    if ($data_realstate['cBrand2'] != 49) {
        if ($data_realstate['cBrand2'] != 2) {
            $check = 1;
        }
    }
}

//如果有仲介代書回饋比率，就顯示
if ((floatval($data_case['cScrivenerSpRecall']) + floatval($data_case['cBranchScrRecall']) + floatval($data_case['cBranchScrRecall1']) + floatval($data_case['cBranchScrRecall2']) + floatval($data_case['cBranchScrRecall3'])) > 0 or floatval($data_case['cSpCaseFeedBackMoney']) > 0) {
    $sSpRecall = '';
} else {
    $sSpRecall = 'none';
}
##

$showAffixBranch['cBrand']  = ($data_realstate['cBrand'] == 69) ? "" : "none";
$showAffixBranch['cBrand1'] = ($data_realstate['cBrand1'] == 69) ? "" : "none";
$showAffixBranch['cBrand2'] = ($data_realstate['cBrand2'] == 69) ? "" : "none";

##宜蘭宏鎰集團回饋流程(案件2仲介以上且都是宏鎰集團)
$funcAffixBranch = '';
$branchGroup18   = [0];
$sql_group18     = 'SELECT bId FROM tBranch WHERE bGroup = 18';
$rs_group18      = $conn->Execute($sql_group18);
while (! $rs_group18->EOF) {
    $branchGroup18[] = (int) $rs_group18->fields['bId'];
    $rs_group18->MoveNext();
}

$showAffixBranch['group18Brand']  = "none";
$showAffixBranch['group18Brand1'] = "none";
$showAffixBranch['group18Brand2'] = "none";
$showAffixBranch['group18Brand3'] = "none";
if (
    $data_realstate['cBranchNum'] > 0 && $data_realstate['cBranchNum1'] > 0 &&
    in_array($data_realstate['cBranchNum'], $branchGroup18) &&
    in_array($data_realstate['cBranchNum1'], $branchGroup18) &&
    in_array($data_realstate['cBranchNum2'], $branchGroup18) &&
    in_array($data_realstate['cBranchNum3'], $branchGroup18)
) {
    $funcAffixBranch                  = "group18";
    $showAffixBranch['group18Brand']  = "";
    $showAffixBranch['group18Brand1'] = "";
    $showAffixBranch['group18Brand2'] = "";
    $showAffixBranch['group18Brand3'] = "";
}

##點交前(租客是否願意搬遷)
$property_finish = ['1' => '租客願意搬遷', '2' => '租客不願意搬遷'];
$property_other  = ['1' => '買方自行排除', '2' => '由賣方負責'];
##

##契稅之歸屬
$ascription_option  = ['1' => '地政規費', '2' => '設定規費', '3' => '印花稅', '4' => '地政士業務執行費', '5' => '公證或監證費', '6' => '簽約費', '7' => '火險及地震險費', '8' => '塗銷費', '9' => '貸款相關費用', 10 => '實價登錄費', 11 => '履保費', 12 => '土地增值稅', 13 => '契稅'];
$ascription_option2 = ['1' => '一般稅率', '2' => '自用住宅優惠稅率'];
##

##
$object_option = ['1' => '一樓/法定空地', '2' => '騎樓', '3' => '陽台', '4' => '露台', '5' => '平台', '6' => '防火巷', '7' => '地下室', '8' => '夾層', '9' => '其他'];
##

##明細表(如有異動請連tran_table.php一併修改)
$color = '#FFFFFF';

// 取案件進度 2018-01-24 解約或中止進度圖要到結案 之前的不要
$data_case['cCaseProcessing'] += 1 - 1;
$processing = '';

for ($j = 1; $j < 7; $j++) {
    if ($data_case['cCaseStatus'] == '3' || ($data_case['cLastTime'] >= '107-01-24 00:00:00' && ($data_case['cCaseStatus'] == 4 || $data_case['cCaseStatus'] == 9))) {$_index = "";} else { $index = ' onclick="processing(' . $j . ')"';}

    $processing .= '<td id="ps' . $j . '"' . $index;
    if (($j <= $data_case['cCaseProcessing']) || ($data_case['cCaseStatus'] == '3') || ($data_case['cLastTime'] >= '107-01-24 00:00:00' && ($data_case['cCaseStatus'] == 4 || $data_case['cCaseStatus'] == 9))) {
        $processing .= ' class="step_class" ';
    }
    $processing .= ' >　</td>' . "\n";
}
##

//表格顏色區分
$cindex = 0; // 初始化顏色索引變數
if ((($cindex + 1) % 2) == 1) {
    $colorIndex  = 'background-color:#FFFFFF;';
    $colorIndex1 = '';
} else {
    $colorIndex  = '';
    $colorIndex1 = 'background-color:#FFFFFF;';
}
##

##票據部分##
$cheque = [];
$income = [];
//取得銷帳檔入帳紀錄資料
$sql = '
	SELECT
		*,
		(SELECT sName FROM tCategoryIncome WHERE sId = exp.eStatusRemark) eStatusRemarkName,
		(SELECT eId FROM tExpenseDetail WHERE eExpenseId=exp.id AND eCertifiedId="' . $id . '" LIMIT 1) as eId,
		(SELECT sName FROM tCategoryIncome WHERE sId=exp.eStatusRemark) object
	FROM
		tExpense AS exp
	JOIN
		tCategoryIncome AS inc ON exp.eStatusRemark = inc.sId
	WHERE
		eDepAccount = "00' . $data_case['cEscrowBankAccount'] . '" AND ePayTitle <> "網路整批"
	ORDER BY
		eTradeDate
	ASC ;';

$rs         = $conn->Execute($sql);
$income_max = count($income);
$j          = 0;
while (! $rs->EOF) {
    $income[$j]          = $rs->fields;
    $income[$j]['match'] = 'x';
    $j++;
    $rs->MoveNext();
}

//取得保證號碼之所有交換票據資料
$_cheque = []; // 先初始化陣列

if (preg_match("/^60001/", $data_case['cEscrowBankAccount']) || preg_match("/^55006/", $data_case['cEscrowBankAccount'])) { //若為一銀的案件，則加入票據資料
    $sql = 'SELECT * FROM tExpense_cheque WHERE eDepAccount = "00' . $data_case['cEscrowBankAccount'] . '" AND eTradeStatus IN(0,10,11,20,40,49) ORDER BY eTradeDate ASC; ';
    $rs  = $conn->Execute($sql);

    $x = 0;
    while (! $rs->EOF) {
        $_cheque[$x]          = $rs->fields;
        $_cheque[$x]['match'] = 'x';
        $x++;
        $rs->MoveNext();
    }

    if (is_array($_cheque)) {
                                                   //檢核票據是否已兌現
        for ($x = 0; $x < count($_cheque); $x++) { //票據交易(8)
            for ($j = 0; $j < count($income); $j++) {
                if (($_cheque[$x]['eDepAccount'] == $income[$j]['eDepAccount']) //保證號碼相同
                    && ($income[$j]['eTradeCode'] == '1793')                        //交易代碼為1793
                    && ($_cheque[$x]['eDebit'] == $income[$j]['eDebit'])            //支出金額相符
                    && ($_cheque[$x]['eLender'] == $income[$j]['eLender'])          //收入金額相符
                    && ($_cheque[$x]['eTradeDate'] < $income[$j]['eTradeDate'])     //票據日期須小於銷帳日期
                    && ($income[$j]['match'] == 'x')) {                             //銷帳紀錄須未被配對

                    $income[$j]['match']  = '1'; //在銷帳紀錄中找到支票紀錄
                    $_cheque[$x]['match'] = '1'; //在支票紀錄中找到銷帳紀錄
                    $income[$j]['remark'] = ' 本款項由' . tDate_check($_cheque[$x]['eTradeDate'], 'md', 'b', '/', 0, 0) . '支票兌現';
                    break;
                }
            }
        }
    }

    if (is_array($_cheque)) {
        for ($x = 0; $x < count($_cheque); $x++) { //正常交易(0)
            for ($j = 0; $j < count($income); $j++) {
                if (($_cheque[$x]['eDepAccount'] == $income[$j]['eDepAccount']) //保證號碼相同
                    && ($income[$j]['eTradeCode'] == '1950')                        //交易代碼為1950
                    && ($_cheque[$x]['eDebit'] == $income[$j]['eDebit'])            //支出金額相符
                    && ($_cheque[$x]['eLender'] == $income[$j]['eLender'])          //收入金額相符
                    && ($income[$j]['eTradeStatus'] == '0')                         //交易狀態為票據交易
                    && ($_cheque[$x]['eTradeDate'] < $income[$j]['eTradeDate'])     //票據日期須小於銷帳日期
                    && ($income[$j]['match'] == 'x')) {                             //銷帳紀錄須未被配對(重要)

                    $income[$j]['match']  = '1'; //在銷帳紀錄中找到支票紀錄
                    $_cheque[$x]['match'] = '1'; //在支票紀錄中找到銷帳紀錄
                    $income[$j]['remark'] = ' 本款項由' . tDate_check($_cheque[$x]['eTradeDate'], 'md', 'b', '/', 0, 0) . '支票兌現';
                    break;
                }
            }
        }
    }

} else if (preg_match("/^9998[56]0/", $data_case['cEscrowBankAccount'])) { //若為永豐的案件，則加入票據資料
                                                                               //取得次交票紀錄(Time to Pay tickets)
    $Time2Pay = [];

    $sql = '
		SELECT
			DISTINCT eCheckNo
		FROM
			tExpense_cheque
		WHERE
			eDepAccount = "00' . $data_case['cEscrowBankAccount'] . '"
			AND eTradeStatus = "0"
			AND eCheckDate = "0000000"
		ORDER BY
			eDepAccount
		ASC;
	';

    $rs = $conn->Execute($sql);
    while (! $rs->EOF) {
        $tmp[] = $rs->fields;
        $rs->MoveNext();
    }

    $y = 0;
    if ($tmp) {
        for ($x = 0; $x < count($tmp); $x++) {
            //依據支票號碼，取得最後一日次交票的保證號碼支票紀錄
            $sql = '
					SELECT
						*
					FROM
						tExpense_cheque
					WHERE
						eDepAccount = "00' . $data_case['cEscrowBankAccount'] . '"
						AND eTradeStatus = "0"
						AND eCheckDate = "0000000"
						AND eCheckNo = "' . $tmp[$x]['eCheckNo'] . '"
					ORDER BY
						eTradeDate
					DESC
					LIMIT 1
				';
            $rs = $conn->Execute($sql);

            $tmp2 = $rs->fields;

            if ($tmp2['eDepAccount'] != '') {
                $Time2Pay[$y]               = $tmp2;
                $Time2Pay[$y]['match']      = 'x';
                $Time2Pay[$y++]['Time2Pay'] = '1'; //保留、顯示
            }
            ##
        }

        unset($tmp);
    }

    //取得託收票紀錄(Bills for Collection)
    $B4C = [];

    $sql = '
		SELECT
			*
		FROM
			tExpense_cheque
		WHERE
			eDepAccount = "00' . $data_case['cEscrowBankAccount'] . '"
			AND eTradeStatus = "0"
			AND eCheckDate <> "0000000"
		ORDER BY
			eDepAccount,eCheckDate
		ASC
		;
	';
    $rs = $conn->Execute($sql);

    $x = 0;
    while (! $rs->EOF) {
        $B4C[$x]             = $rs->fields;
        $B4C[$x]['match']    = 'x';
        $B4C[$x]['Time2Pay'] = '1'; //保留、顯示
        $x++;
        $rs->MoveNext();
    }
    ##

    //比對當相同紀錄出現時，剔除託收票紀錄
    if ($Time2Pay) {
        for ($x = 0; $x < count($Time2Pay); $x++) {
            for ($y = 0; $y < count($B4C); $y++) {
                if ($Time2Pay[$x]['eDepAccount'] == $B4C[$y]['eDepAccount']
                    && $Time2Pay[$x]['eCheckNo'] == $B4C[$y]['eCheckNo']
                    && $Time2Pay[$x]['eDebit'] == $B4C[$y]['eDebit']
                    && $Time2Pay[$x]['eLender'] == $B4C[$y]['eLender']) {
                    $B4C[$y]['Time2Pay'] = '2'; //剔除、不顯示
                }
            }
        }
        $B4C = array_merge($B4C, $Time2Pay);
        unset($Time2Pay);
    }

    if ($B4C) { //080123475
        $y = 0;
        for ($x = 0; $x < count($B4C); $x++) {
            if ($B4C[$x]['Time2Pay'] == '1') { //僅取出保留的票據資料
                $_cheque[$y++] = $B4C[$x];
            }
        }
        unset($B4C);
    }

    //檢核票據是否已兌現
    if ($_cheque && $income) {
        for ($x = 0; $x < count($_cheque); $x++) { //票據交易
            for ($j = 0; $j < count($income); $j++) {
                if (($_cheque[$x]['eDepAccount'] == $income[$j]['eDepAccount']) //保證號碼相同
                    && ($_cheque[$x]['eDebit'] == $income[$j]['eDebit'])            //支出金額相符
                    && ($_cheque[$x]['eLender'] == $income[$j]['eLender'])          //收入金額相符
                    && ($income[$j]['eSummary'] == '票據轉入')                  //交易摘要為票據轉入
                    && ($_cheque[$x]['eCheckNo'] == $income[$j]['eCheckNo'])        //支票號碼相同
                    && ($income[$j]['match'] == 'x')) {                             //銷帳紀錄須未被配對

                    $income[$j]['match']  = '1'; //在銷帳紀錄中找到支票紀錄
                    $_cheque[$x]['match'] = '1'; //在支票紀錄中找到銷帳紀錄
                    $income[$j]['remark'] = ' 本款項由' . tDate_check($_cheque[$x]['eTradeDate'], 'md', 'b', '/', 0, 0) . '支票兌現';
                    break;
                }
            }
        }
    }
                                                                           ##
} else if (preg_match("/^96988/", $data_case['cEscrowBankAccount'])) { //若為台新的案件，則加入票據資料
    $sql = 'SELECT * FROM tExpense_cheque WHERE eDepAccount = "00' . $data_case['cEscrowBankAccount'] . '" AND eTradeStatus = "0" ORDER BY eTradeDate ASC; ';
    $rs  = $conn->Execute($sql);

    $x = 0;
    while (! $rs->EOF) {
        $_cheque[$x]          = $rs->fields;
        $_cheque[$x]['match'] = 'x';
        $x++;

        $rs->MoveNext();
    }

    //檢核票據是否已兌現
    if ($_cheque && $income) {
        for ($x = 0; $x < count($_cheque); $x++) { //正常交易(0)
            for ($j = 0; $j < count($income); $j++) {
                if (($_cheque[$x]['eDepAccount'] == $income[$j]['eDepAccount']) //保證號碼相同
                    && ($income[$j]['eTradeCode'] == 'PDC')                         //交易代碼為 PDC 票據交易
                    && ($_cheque[$x]['eDebit'] == $income[$j]['eDebit'])            //支出金額相符
                    && ($_cheque[$x]['eLender'] == $income[$j]['eLender'])          //收入金額相符
                    && ($income[$j]['eTradeStatus'] == '0')                         //交易狀態為票據交易
                    && ($_cheque[$x]['eTradeDate'] < $income[$j]['eTradeDate'])     //票據日期須小於銷帳日期
                    && ($income[$j]['match'] == 'x')) {                             //銷帳紀錄須未被配對(重要)

                    $income[$j]['match']  = '1'; //在銷帳紀錄中找到支票紀錄
                    $_cheque[$x]['match'] = '1'; //在支票紀錄中找到銷帳紀錄
                    $income[$j]['remark'] = ' 本款項由' . tDate_check($_cheque[$x]['eTradeDate'], 'md', 'b', '/', 0, 0) . '支票兌現';
                    break;
                }
            }
        }
    }

}

//取出未兌現支票據資料  (再這處李天數)
$j = 0;

if ($_cheque) {
    for ($x = 0; $x < count($_cheque); $x++) {                                                                             //將未標記之票據紀錄取出
        if ($_cheque[$x]['eTipDate'] != '' || ($_cheque[$x]['eCheckDate'] != '0000000' && $_cheque[$x]['eCheckDate'] != '')) { //如果是託收票  以到期日加一日為兌現日
            $_expire_date = tDate_check($_cheque[$x]['eCheckDate'], 'ymd', 'b', '-', 1, 1);                                        //

        } else {
            $_expire_date = tDate_check($_cheque[$x]['eTradeDate'], 'ymd', 'b', '-', 3, 1); //票據(預計)兌現時間
        }

        if ($_expire_date <= date("Y-m-d")) { //若今日超過兌現時間，則不顯示
            $_cheque[$x]['match'] = '1';
        }

        if ($_cheque[$x]['match'] == 'x') {
            $cheque[$j]           = $_cheque[$x];
            $cheque[$j]['cheque'] = '1';

            $j++;
        }
    }

    unset($_cheque);
}
##

//合併顯示
$income_arr = array_merge($income, $cheque);
unset($income, $cheque);
##

//排序
for ($j = 0; $j < count($income_arr); $j++) {
    $arr[$j]['date'] = (substr($income_arr[$j]['eTradeDate'], 0, 3) + 1911) . '-' . substr($income_arr[$j]['eTradeDate'], 3, 2) . '-' . substr($income_arr[$j]['eTradeDate'], 5);

    $arr[$j]['income']   = (int) substr($income_arr[$j]['eLender'], 0, -2) + 1 - 1;
    $arr[$j]['outgoing'] = (int) substr($income_arr[$j]['eDebit'], 0, -2) + 1 - 1;

    if (isset($income_arr[$j]['cheque']) && $income_arr[$j]['cheque'] == 1) {
        $arr[$j]['detail'] = '支票' . $income_arr[$j]['ePayTitle'];
    } else if ($income_arr[$j]['eStatusRemark'] == '0') {
        $arr[$j]['detail'] = $income_arr[$j]['ePayTitle'];
    } else {
        $arr[$j]['detail'] = $income_arr[$j]['object'];
    }

    //20210407 改為官網摘要附註顯示
    $arr[$j]['remark']       = (! empty($income_arr[$j]['eRemarkContentSp'])) ? $income_arr[$j]['eRemarkContentSp'] : $income_arr[$j]['eRemarkContent'];
    $arr[$j]['obj']          = '1';                   // 1 表示為收入
    $arr[$j]['expId']        = $income_arr[$j]['id']; //入帳ID
    $arr[$j]['eId']          = isset($income_arr[$j]['eId']) ? $income_arr[$j]['eId'] : '';
    $arr[$j]['eTradeStatus'] = $income_arr[$j]['eTradeStatus'];
    $arr[$j]['show']         = isset($income_arr[$j]['eShow']) ? $income_arr[$j]['eShow'] : '';
    $arr[$j]['cheque']       = isset($income_arr[$j]['cheque']) ? $income_arr[$j]['cheque'] : 0;

    if (isset($income_arr[$j]['cheque']) && $income_arr[$j]['cheque'] == 1) {
        if (($income_arr[$j]['eCheckDate'] != '') && ($income_arr[$j]['eCheckDate'] != '0000000')) {
            $_tDate            = tDate_check($income_arr[$j]['eCheckDate'], 'md', 'b', '/', 1, 0);
            $arr[$j]['remark'] = '<font color="red">※未兌現、預計' . $_tDate . '兌現。(NT$' . number_format(($arr[$j]['income'] + 1 - 1)) . ' 不可動用)</font>';
        } else {
            $arr[$j]['remark'] = '<font color="red">※未兌現、預計二日後兌現。(NT$' . number_format(($arr[$j]['income'] + 1 - 1)) . ' 不可動用)</font>';
        }
        $minus_money += $arr[$j]['income'] + 1 - 1; //票據金額加總
    }
}
##

// 支出部分
$sql_tra = '
SELECT
	tBankLoansDate as tExport_time,
    tExport_time as tExport_time2,
	tObjKind,
	tKind,
	tMoney,
	tTxt,
	tId,
	tShow,
	tObjKind2Item,
	tBank_kind,
	tObjKind2,
	tAccountName,
	tPayOk
FROM
	tBankTrans
WHERE
	tVR_Code="' . $data_case['cEscrowBankAccount'] . '" AND tObjKind2 != "02"
ORDER BY
	tExport_time
ASC ;
';

$rs          = $conn->Execute($sql_tra);
$exportCount = $rs->RecordCount();

$checkOwnerNote     = 1; //確認是否符合顯示賣方備註 0:要顯示填寫
$checkCaseEnd       = 0; //檢查是否有出結案款項 1:有出
$checkOwnerNoteTime = 0; //檢查是否已經過修改的時間 true:超過
while (! $rs->EOF) {
    $arr[$j]['date']     = ($rs->fields['tPayOk'] == '1') ? substr($rs->fields['tExport_time'], 0, 10) : ''; //20230803 調整當銀行未付款時(tPayOk=2)，即使日期有押也不能顯示日期
    $arr[$j]['detail']   = $rs->fields['tObjKind'];
    $arr[$j]['income']   = '';
    $arr[$j]['outgoing'] = $rs->fields['tMoney'];
    $arr[$j]['remark']   = $rs->fields['tTxt'];
    $arr[$j]['obj']      = '2';                // 2 表示為支出
    $arr[$j]['tran_id']  = $rs->fields['tId']; //出款ID
    $arr[$j]['show']     = $rs->fields['tShow'];

    if ($rs->fields['tKind'] == '保證費') {
        if ($data_case['cEscrowBankAccount'] == '99985003081297' || $data_case['cEscrowBankAccount'] == '96988090025288') {
            $cCertifyDate = '&nbsp;';
        } else { //2015-09-08 惠婷要求遮掉此案件的履保費出款日
            $cCertifyDate = substr($rs->fields['tExport_time'], 0, 10);
        }

        if ($data_case['cEscrowBankAccount'] == '96988110143954' && substr($rs->fields['tExport_time2'], 0, 10) == '2023-09-18') {
            $arr[$j]['date'] = substr($rs->fields['tExport_time2'], 0, 10); //20231003 因為此案(110143954)點交結案出款退回，所以刪除資料庫tBankLoansDate，以tExport_time日期取代，以免標表撈出
        }
    } else {
        $tmp = explode('-', $data_case['cBankList']);
        if ($data_case['cBankList'] != '') {
            $cCertifyDate = ($tmp[0] - 1911) . '-' . str_pad($tmp[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($tmp[2], 2, '0', STR_PAD_LEFT);
        }

        unset($tmp);
    }

    if ($rs->fields['tBank_kind'] == '台新' && $rs->fields['tObjKind2'] == '01') {
        $arr[$j]['taishinSp'] = ($rs->fields['tObjKind2Item'] != '') ? '已返還代墊款' : '未返還代墊款';

        if ($rs->fields['tObjKind2Item'] == '') {
            $taishinSPMoney += $rs->fields['tMoney'];
        }
    }

    //賣方備註(沒賣方不用填寫;有非賣方帳戶要填寫;//結案有出賣方帳戶但其中有賣方未收錢(EX:賣1、賣2、賣3;出款只出給了賣1、賣2))
    if (($rs->fields['tObjKind'] == '點交(結案)' || $rs->fields['tObjKind'] == '解除契約' || $rs->fields['tObjKind'] == '建經發函終止') && $rs->fields['tKind'] == '賣方') {
        $checkCaseEnd             = 1;                           //有出結案
        $ownerExportAccountName[] = $rs->fields['tAccountName']; //賣方出款帳戶
        if (! in_array($rs->fields['tAccountName'], $ownerArr)) { //非賣方帳戶要顯示填寫
            $checkOwnerNote = 0;
        }

        if ($rs->fields['tPayOk'] == 1 && (date('Y-m-d H:i:s') > $rs->fields['tExport_time'] . " 17:50:00")) { //
            $checkOwnerNoteTime = 1;
        }

        //202202之後不鎖時間
        if ($rs->fields['tPayOk'] == 1 && (strtotime("2022-02-01") < strtotime($arr[$j]['date']))) {
            $checkOwnerNoteTime = 0;
        }
    }

    $j++;

    $rs->MoveNext();
}

//未出款但先勾代墊利息
if ($exportCount == 0) {
    $tmp = explode('-', $data_case['cBankList']);
    if ($data_case['cBankList'] != '') {
        $cCertifyDate = ($tmp[0] - 1911) . '-' . str_pad($tmp[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($tmp[2], 2, '0', STR_PAD_LEFT);
    }

    unset($tmp);
}

if ($checkCaseEnd == 1) {
    if (is_array($ownerExportAccountName)) {
        foreach ($ownerArr as $k => $v) {
            if (! in_array($v, $ownerExportAccountName)) { //結案有出賣方帳戶但其中有賣方未收錢(EX:賣1、賣2、賣3;出款只出給了賣1、賣2)
                $checkOwnerNote = 0;
            }
        }
    }
}

//還未出結案(台新先不防呆)
if ($checkCaseEnd == 0 || $data_case['cBank'] == 68) {
    $checkOwnerNote = 1;
}

$max = ($arr) ? count($arr) : 0;

for ($i = 0; $i < $max; $i++) {
    for ($j = 0; $j < $max - 1; $j++) {
        if ($arr[$j]['date'] > $arr[$j + 1]['date']) {
            $tmp         = $arr[$j];
            $arr[$j]     = $arr[$j + 1];
            $arr[$j + 1] = $tmp;
            unset($tmp);
        } else if ($arr[$j]['date'] == $arr[$j + 1]['date']) {
            if (($arr[$j]['obj'] == '2') && ($arr[$j + 1]['obj'] == '1')) {
                $tmp         = $arr[$j];
                $arr[$j]     = $arr[$j + 1];
                $arr[$j + 1] = $tmp;
                unset($tmp);
            }
        }
    }
}

// 初始化變數
$total         = 0;
$incomeTotal   = 0;
$outgoingTotal = 0;
$tbl           = '';

// 建立帳務明細表格
for ($i = 0; $i < $max; $i++) {
    $color = ($i % 2 == 0) ? $colorIndex : $colorIndex1;

    $total += (int) $arr[$i]['income'] + 1 - 1;
    $total -= (int) $arr[$i]['outgoing'] + 1 - 1;

    $income   = (int) $arr[$i]['income'] + 1 - 1;
    $outgoing = $arr[$i]['outgoing'] + 1 - 1;
    $expId    = $arr[$i]['expId'];
    $incomeTotal += (int) $arr[$i]['income'] + 1 - 1;
    $outgoingTotal += (int) $arr[$i]['outgoing'] + 1 - 1;
    $tbl .= '
	<tr style="' . $color . ';">
		<td>' . $arr[$i]['date'] . '&nbsp;</td>
	';

    if ($arr[$i]['obj'] == '1') {
        $aa = '';
        $bb = '';
        if ($arr[$i]['eId']) {
            $aa = 'class="incomeDetail" ';
            $bb = '<span style="width:100%;color:red;font-weight:bold;">&nbsp;*</span>';
        }

        $correct = '';

        if ($arr[$i]['eTradeStatus'] == '9') {
            $correct = '<span style="font-size:9pt;color:red;">(被沖正)</span>';
        }

        $tbl .= '<td>
					<span style="float:left;">
					' . $arr[$i]['detail'] . $correct . '<span style="font-size:9pt;color:red;">' . (isset($arr[$i]['taishinSp']) ? $arr[$i]['taishinSp'] : '') . '</span>&nbsp;
					</span>';
        if ($arr[$i]['cheque'] != 1) {
            $tbl .= '<span style="font-size:9pt;float:right;">
						<a href="../inquire/expenseDetail.php?cid=' . $id . '&eid=' . $expId . '" class="iframe">(編輯)</a>
					</span>
				';
        }

        $tbl .= '</td>';
        $tbl .= '<td ' . $aa . 'id="' . $expId . '" style="text-align:right;">' . $bb . number_format($income) . '&nbsp;</td>';
    } else {
        if ($arr[$i]['detail'] == '賣方先動撥') {
            $tbl .= '<td>' . $arr[$i]['detail'] . '<span style="font-size:9pt;color:red;">' . $arr[$i]['taishinSp'] . '<a href="bankTransConfirmCall.php?action=contract&cid=' . $id . '&bid=' . $arr[$i]['tran_id'] . '" class="iframe" style="font-size:9pt;">(照會)</a></span>&nbsp;</td>';
        } else {
            $tbl .= '<td>' . $arr[$i]['detail'] . '<span style="font-size:9pt;color:red;">' . $arr[$i]['taishinSp'] . '</span>&nbsp;</td>';
        }
        $tbl .= '<td style="text-align:right;">' . number_format($income) . '&nbsp;</td>';
    }

    $tbl .= '
		<td style="text-align:right;">' . number_format($outgoing) . '&nbsp;</td>
		<td style="text-align:right;">' . number_format($total) . '&nbsp;</td>
		<td>' . ltrim($arr[$i]['remark'], '+') . '&nbsp;</td>';
    if ($arr[$i]['show'] == 0) {
        $ck = 'checked=checked';

    } else {
        $ck = '';

    }

    $tbl .= '</tr>
	';
}

if ($tbl == '') {
    $tbl = '
	<tr style="background-color:' . $colorIndex . ';">
		<td colspan="6">尚無出入款紀錄!!</td>
	</tr>
	';
}

##
//合約書版本 bApplication
$sql = "SELECT bBrand,bCategory,bApplication,bFrom,bNo72 FROM tBankCode WHERE bAccount  LIKE'" . $data_case['cEscrowBankAccount'] . "'";

$rs = $conn->Execute($sql);

$data_bankcode = $rs->fields;

##

//發票指定對象
$sql = "SELECT * FROM tContractInvoiceExt  WHERE cCertifiedId ='" . $id . "'";
$rs  = $conn->Execute($sql);

$i = 0;
while (! $rs->EOF) {
    if ($rs->fields['cInvoiceDonate'] == 1) {

        $rs->fields['cInvoiceDonate'] = '[捐贈]';
    } else {
        $rs->fields['cInvoiceDonate'] = '';
    }

    $data_invoice_another[] = $rs->fields;

    $rs->MoveNext();
}
##

//取得額外銀行資料
$buyer_bank = getBankData($conn, $id, 1);
$owner_bank = getBankData($conn, $id, 2);
##

//其他回饋對象
$otherFeed = getFeedBackMoney($id);
##
//個案回饋
$individual = getIndividualFeedBack($id);

//承辦人選單
$sql                = "SELECT pId,pName FROM tPeopleInfo WHERE pDep = 5 ORDER BY pId ASC";
$rs                 = $conn->Execute($sql);
$menu_Undertaker[0] = '請選擇';
while (! $rs->EOF) {
    $menu_Undertaker[$rs->fields['pName']] = $rs->fields['pName'];
    $rs->MoveNext();
}
##

// "合約書客服紀錄"
$sql = "SELECT * FROM tContractService WHERE cCertifiedId ='" . $id . "' AND cDel = 0 ";
$rs  = $conn->Execute($sql);

$i = 0;
while (! $rs->EOF) {
    $data_service[$i]       = $rs->fields;
    $data_service[$i]['no'] = ($i + 1);
    $i++;
    $rs->MoveNext();
}
##

//品牌回饋代書
$sql = "SELECT *,(SELECT bName FROM tBrand WHERE bId = sBrand) AS BrandName FROM tScrivenerFeedSp WHERE sScrivener ='" . $data_scrivener['cScrivener'] . "'  AND sDel =0";
$rs  = $conn->Execute($sql);

$tmp = []; // 初始化為空陣列
$i   = 0;
while ($rs && ! $rs->EOF) {
    $tmp[] = $rs->fields['BrandName'] . ":" . $rs->fields['sReacllBrand'] . "%(仲介)、" . $rs->fields['sRecall'] . "%(地政士)";

    $i++;
    $rs->MoveNext();
}
$ScrivenerFeedSpTxt = @implode(';', $tmp);

unset($tmp);

function transfer($conn, $pid)
{
    $sql = "SELECT pId,pName FROM tPeopleInfo WHERE pId = '" . $pid . "'";
    $rs  = $conn->Execute($sql);

    $tmp = []; // 初始化陣列
    if ($rs && ! $rs->EOF) {
        $tmp[0] = $rs->fields['pId'];
        $tmp[1] = $rs->fields['pName'];
    } else {
        $tmp[0] = '';
        $tmp[1] = '';
    }
    return $tmp;
}
##

//檢查第一間店是否有大小章圖檔
$imgStampEdit = '';
$imgStamp     = '';
$sql          = 'SELECT `bId` FROM `tBranchStamp` WHERE `bBranchId` = "' . $data_realstate['cBranchNum'] . '";';
$rs           = $conn->Execute($sql);

if ($rs->RecordCount() > 0) {
    $imgStampEdit = '1';
    if (! $rs->EOF) {
        $imgStamp = '<div onclick="newImg(' . $data_realstate['cBranchNum'] . ', \'' . $id . '\')" style="cursor:pointer;"><img src="showcIdStamp.php?bId=' . $data_realstate['cBranchNum'] . '&cId=' . $id . '" style="width:236px;height:135px;"></div>';
    }
}
##

//檢查第二間店是否有大小章圖檔
$imgStampEdit1 = '';
$imgStamp1     = '';
$sql           = 'SELECT `bId` FROM `tBranchStamp` WHERE `bBranchId` = "' . $data_realstate['cBranchNum1'] . '";';
$rs            = $conn->Execute($sql);

if ($rs->RecordCount() > 0) {
    $imgStampEdit1 = '1';
    if (! $rs->EOF) {
        $imgStamp1 = '<div onclick="newImg(' . $data_realstate['cBranchNum1'] . ', \'' . $id . '\')" style="cursor:pointer;"><img src="showcIdStamp.php?bId=' . $data_realstate['cBranchNum1'] . '&cId=' . $id . '" style="width:236px;height:135px;"></div>';
    }
}
##

//檢查第三間店是否有大小章圖檔
$imgStampEdit2 = '';
$imgStamp2     = '';
$sql           = 'SELECT `bId` FROM `tBranchStamp` WHERE `bBranchId` = "' . $data_realstate['cBranchNum2'] . '";';
$rs            = $conn->Execute($sql);

if ($rs->RecordCount() > 0) {
    $imgStampEdit2 = '1';
    if (! $rs->EOF) {
        $imgStamp2 = '<div onclick="newImg(' . $data_realstate['cBranchNum2'] . ', \'' . $id . '\')" style="cursor:pointer;"><img src="showcIdStamp.php?bId=' . $data_realstate['cBranchNum2'] . '&cId=' . $id . '" style="width:236px;height:135px;"></div>';
    }
}
##

// 回饋金修改者
$tmp                  = transfer($conn, $data_case['cCaseFeedBackModifier']);
$CaseFeedBackModifier = $tmp[1];
unset($tmp);

$cCaseFeedBackModifyTime = $data_case['cCaseFeedBackModifyTime'];
##

//
$sql = "SELECT *,(SELECT pName FROM tPeopleInfo WHERE pId = cCreator) AS cCreator FROM tContractNote WHERE cCertifiedId = '" . $id . "' AND cDel = 0 ORDER BY cModify_Time ASC";
$rs  = $conn->Execute($sql);

while (! $rs->EOF) {
    $rs->fields['cNote']                      = nl2br($rs->fields['cNote']);
    $contractNote[$rs->fields['cCategory']][] = $rs->fields;

    $rs->MoveNext();
}

$sql = "SELECT cNote FROM tContractNote WHERE cCertifiedId = '" . $id . "'  AND cCategory = 5 ORDER BY cCreatTime DESC";
$rs  = $conn->Execute($sql);

$income_reason = $rs->fields['cNote'];
##

//地政士特殊回饋(不同比例，且沒有對應比例)
if ($data_case['cSpCaseFeedBackMoneyMark'] == 'x' && $data_case['cSpCaseFeedBackMoney'] == 0) {
    $data_case['cSpCaseFeedBackMoney'] = '';
}
##

//業務申請回饋金
$sql = "SELECT
			fId,
			fStatus,
			fNote,
			fTotalMoney,
			fCertifiedMoney,
			(SELECT pName FROM tPeopleInfo WHERE pId=fCreator) AS fCreator,
			fApplyTime,
			(SELECT pName FROM tPeopleInfo WHERE pId = fAuditor) AS fAuditor,
			fAuditorTime
		FROM
			tFeedBackMoneyReview WHERE fCertifiedId = '" . $id . "' AND fFail = 0 ORDER BY fId DESC";
$rs      = $conn->Execute($sql);
$i       = 0;
$delNote = []; // 將變數移到外層作用域

while (! $rs->EOF) {
    $j                          = 0;
    $SalesReview[$i]            = $rs->fields;
    $SalesReview[$i]['Status']  = $SalesReview[$i]['fStatus'];
    $SalesReview[$i]['fStatus'] = ($SalesReview[$i]['fStatus'] == 0) ? '申請中' : '已核可'; //0:申請1:核可

    $sql = "SELECT
                *,
                (SELECT bStore FROM `tBranch` WHERE `tBranch`.bId = `tFeedBackMoneyReviewList`.fIndividualId) AS fIndividualName,
                (SELECT CONCAT(`fAccountName`,'  ', `fAccountNum`, `fAccountNumB`, '-',`fAccount`) FROM tFeedBackData WHERE `tFeedBackData`.fId = `tFeedBackMoneyReviewList`.fFeedbackDataId) AS bankAccount
            FROM tFeedBackMoneyReviewList WHERE fCertifiedId = '" . $id . "' AND fRId = '" . $SalesReview[$i]['fId'] . "'  ORDER BY fCategory ASC"; //AND fDelete = 0
    $rs2 = $conn->Execute($sql);

    while (! $rs2->EOF) {
        if ($rs2->fields['fCategory'] == 1) {
            $SalesReview[$i]['BranchName']         = $branch_type1;
            $SalesReview[$i]['fCaseFeedback']      = $rs2->fields['fCaseFeedback'];
            $SalesReview[$i]['fFeedbackTarget']    = $rs2->fields['fFeedbackTarget'];
            $SalesReview[$i]['fCaseFeedBackMoney'] = $rs2->fields['fCaseFeedBackMoney'];
            if (! empty($rs2->fields['bankAccount'])) {
                $SalesReview[$i]['scrivenerAccount'] = $rs2->fields['bankAccount'];
            }
        } elseif ($rs2->fields['fCategory'] == 2) {
            $SalesReview[$i]['BranchName2']         = $branch_type2;
            $SalesReview[$i]['fCaseFeedback2']      = $rs2->fields['fCaseFeedback'];
            $SalesReview[$i]['fFeedbackTarget2']    = $rs2->fields['fFeedbackTarget'];
            $SalesReview[$i]['fCaseFeedBackMoney2'] = $rs2->fields['fCaseFeedBackMoney'];
            if (! empty($rs2->fields['bankAccount'])) {
                $SalesReview[$i]['scrivenerAccount'] = $rs2->fields['bankAccount'];
            }
        } elseif ($rs2->fields['fCategory'] == 3) {
            $SalesReview[$i]['BranchName3']         = $branch_type3;
            $SalesReview[$i]['fCaseFeedback3']      = $rs2->fields['fCaseFeedback'];
            $SalesReview[$i]['fFeedbackTarget3']    = $rs2->fields['fFeedbackTarget'];
            $SalesReview[$i]['fCaseFeedBackMoney3'] = $rs2->fields['fCaseFeedBackMoney'];
            if (! empty($rs2->fields['bankAccount'])) {
                $SalesReview[$i]['scrivenerAccount'] = $rs2->fields['bankAccount'];
            }
        } elseif ($rs2->fields['fCategory'] == 6) {
            $SalesReview[$i]['BranchName6']         = $branch_type4;
            $SalesReview[$i]['fCaseFeedback6']      = $rs2->fields['fCaseFeedback'];
            $SalesReview[$i]['fFeedbackTarget6']    = $rs2->fields['fFeedbackTarget'];
            $SalesReview[$i]['fCaseFeedBackMoney6'] = $rs2->fields['fCaseFeedBackMoney'];
            if (! empty($rs2->fields['bankAccount'])) {
                $SalesReview[$i]['scrivenerAccount'] = $rs2->fields['bankAccount'];
            }
        } elseif ($rs2->fields['fCategory'] == 4) {
            $SalesReview[$i]['ScrivenerSPFeedMoney'] = $rs2->fields['fCaseFeedBackMoney'];
            if (! empty($rs2->fields['bankAccount'])) {
                $SalesReview[$i]['scrivenerAccount'] = $rs2->fields['bankAccount'];
            }
        } elseif ($rs2->fields['fCategory'] == 5) {
            if ($rs2->fields['fDelete'] == 0) {
                $target                                            = ($rs2->fields['fFeedbackTarget'] == 1) ? 2 : 1;
                $SalesReview[$i]['data'][$j]                       = getStoreData($target, $rs2->fields['fFeedbackStoreId']);
                $SalesReview[$i]['data'][$j]['fCaseFeedBackMoney'] = $rs2->fields['fCaseFeedBackMoney'];
                $SalesReview[$i]['data'][$j]['fCaseFeedBackNote']  = $rs2->fields['fCaseFeedBackNote'];
                $SalesReview[$i]['data'][$j]['fFeedbackTarget']    = ($rs2->fields['fFeedbackTarget'] == 1) ? 2 : 1;
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
            if (! empty($rs2->fields['bankAccount'])) {
                $SalesReview[$i]['otherAccount'] = $rs2->fields['bankAccount'];
            }

            $j++;
        } elseif ($rs2->fields['fCategory'] == 7) { //個案回饋

            $no = '';
            if ($rs2->fields['fFeedbackStoreId'] == $data_realstate['cBranchNum']) {
                $no = 1;
            }

            if ($rs2->fields['fFeedbackStoreId'] == $data_realstate['cBranchNum1']) {
                $no = 2;
            }

            if ($rs2->fields['fFeedbackStoreId'] == $data_realstate['cBranchNum2']) {
                $no = 3;
            }

            if ($rs2->fields['fFeedbackStoreId'] == $data_realstate['cBranchNum3']) {
                $no = 6;
            }

            if ($no) {
                if ($no == 1) {
                    $no = '';
                }

                $SalesReview[$i]['individualName' . $no][]  = $rs2->fields['fIndividualName'];
                $SalesReview[$i]['individualMoney' . $no][] = $rs2->fields['fCaseFeedBackMoney'];
                $SalesReview[$i]['individualId' . $no][]    = $rs2->fields['fIndividualId'];

            }
        }

        $rs2->MoveNext();
    }

    $i++;
    $rs->MoveNext();
}
##

//如果已經結案次月後
//就不行在改回進行中 做內容修改了
$disabled_caseStatus = '';
if ($data_case['cFinishDate3'] != '0000-00-00 00:00:00') { //已更改時間去鎖
    $month_check = date("Ym", mktime(0, 0, 0, (substr($data_case['cFinishDate3'], 5, 2) + 1), substr($data_case['cFinishDate3'], 8, 2), substr($data_case['cFinishDate3'], 0, 4)));
    //多判斷結案時間是否有超過
    if ($month_check <= date('Ym')) {
        $disabled_caseStatus = 'disabled=disabled';
    } else {
        $month_check = date("Ym", mktime(0, 0, 0, (substr($data_case['cEndDate'], 4, 2) + 1), substr($data_case['cEndDate'], 8, 2), (substr($data_case['cEndDate'], 0, 4) + 1911)));
    }
} else if (($data_case['cFinishDate3'] == '0000-00-00 00:00:00' && $data_case['cEndDate'] != '0000-00-00 00:00:00')) {
    $month_check = date("Ym", mktime(0, 0, 0, (substr($data_case['cEndDate'], 4, 2) + 1), substr($data_case['cEndDate'], 8, 2), (substr($data_case['cEndDate'], 0, 4) + 1911)));
    if ($month_check <= date('Ym')) {
        $disabled_caseStatus = 'disabled=disabled';
    }
}
##

//保證費少於萬分之六
$cer_title         = round(($data_income['cTotalMoney'] - $data_income['cFirstMoney']) * 0.0006);
$checkCertifiedFee = (($data_income['cCertifiedMoney'] + 10) < $cer_title) ? 0 : 1; //如果少收FALSE 正常TRUE
##

$undate = (substr($data_case['cEndDate'], 0, 4) + 1911) . "-" . substr($data_case['cEndDate'], 4);
if (isset($data_income['cInspetor2']) && $data_income['cInspetor2'] > 0 && isset($data_case['cCaseStatus']) && $data_case['cCaseStatus'] == 2) {
    $unf = 1;
} else if (strtotime($undate) <= strtotime("2018-12-13 00:00:00") && isset($data_case['cCaseStatus']) && $data_case['cCaseStatus'] == 3) {
    $unf = 1;
} else if (strtotime($undate) > strtotime("2018-12-13 00:00:00") && isset($data_case['cCaseStatus']) && $data_case['cCaseStatus'] == 3) {
    if (isset($data_income['cInspetor2']) && $data_income['cInspetor2'] > 0) {
        $unf = 1;
    }
}

$sql                           = "SELECT pName FROM tPeopleInfo WHERE pId = '" . $data_income['cInspetor2'] . "'";
$rs                            = $conn->Execute($sql);
$data_income['cInspetorName2'] = $rs->fields['pName'];

$sql                          = "SELECT pName FROM tPeopleInfo WHERE pId = '" . $data_income['cInspetor'] . "'";
$rs                           = $conn->Execute($sql);
$data_income['cInspetorName'] = $rs->fields['pName'];

//買賣經紀人電話
$sql = "SELECT * FROM tContractPhone WHERE cCertifiedId = '" . $id . "' AND (cIdentity =3 OR cIdentity = 4)";
$rs  = $conn->Execute($sql);

// 初始化陣列
$buyerSalesPhone = [];
$ownerSalesPhone = [];

while (! $rs->EOF) {
    if ($rs->fields['cIdentity'] == 3) {
        $buyerSalesPhone[] = $rs->fields;
    } elseif ($rs->fields['cIdentity'] == 4) {
        $ownerSalesPhone[] = $rs->fields;
    }

    $rs->MoveNext();
}
##

//賣方備註選單
$sql = "SELECT * FROM tCategorySellerNote  ORDER BY cOrder ASC ";
$rs  = $conn->Execute($sql);
while (! $rs->EOF) {
    $menuSellerNote[$rs->fields['cId']] = $rs->fields['cName'];
    $rs->MoveNext();
}

//賣方備註
$sql = "SELECT tAnother,tAnotherNote,eSend, relation1, relation3, relation4 FROM tBankTransSellerNote WHERE tCertifiedId = '" . $id . "'";
$rs  = $conn->Execute($sql);

$dataSellerNote['another']     = explode(',', $rs->fields['tAnother']);
$dataSellerNote['anotherNote'] = $rs->fields['tAnotherNote'];
$dataSellerNote['send']        = $rs->fields['eSend'];
$dataSellerNote['relation1']   = $rs->fields['relation1'];
$dataSellerNote['relation3']   = $rs->fields['relation3'];
$dataSellerNote['relation4']   = $rs->fields['relation4'];
##

//判斷賣方全部不帶入點交單和出款 是否要勾選
$BankCount   = 0;
$BankCountCk = 0;

//賣方
if ($data_owner['cBankAccNumber'] != '') {
    $BankCount++;
}
if ($data_owner['cChecklistBank'] == 1) {
    $BankCountCk++;
}

if ($owner_bank) {
    for ($i = 0; $i < count($owner_bank); $i++) {
        if ($owner_bank[$i]['cBankAccountNo'] != '') {
            $BankCount++;
        }

        if ($owner_bank[$i]['cChecklistBank'] == 1) {
            $BankCountCk++;
        }
    }
}

$data_owner['ownerChecklist'] = (($BankCount == $BankCountCk) && $BankCount > 0) ? '1' : '';

$BankCount   = 0;
$BankCountCk = 0;

//買方
if ($data_buyer['cBankAccNumber'] != '') {
    $BankCount++;
}
if ($data_buyer['cChecklistBank'] == 1) {
    $BankCountCk++;
}

if ($buyer_bank) {
    for ($i = 0; $i < count($buyer_bank); $i++) {
        if ($buyer_bank[$i]['cBankAccountNo'] != '') {
            $BankCount++;
        }

        if ($buyer_bank[$i]['cChecklistBank'] == 1) {
            $BankCountCk++;
        }
    }
}

$data_buyer['buyerChecklist'] = (($BankCount == $BankCountCk) && $BankCount > 0) ? '1' : '';

unset($BankCount, $BankCountCk);
##

//檢查票據是否兌現(日期檢查)
function tDate_check($_date, $_dateForm = 'ymd', $_dateType = 'r', $_delimiter = '', $_minus = 0, $_sat = 0)
{
    $_aDate[0] = (substr($_date, 0, 3) + 1911);
    $_aDate[1] = substr($_date, 3, 2);
    $_aDate[2] = substr($_date, 5);

    //是否遇六日要延後(六延兩天、日延一天)
    if ($_sat == '1') {
        $_ss = 0;
        $_ss = date("w", mktime(0, 0, 0, $_aDate[1], ($_aDate[2] + $_minus), $_aDate[0]));
        if ($_ss == '0') { //如果是星期日的話，則延後一天
            $weekend = 1;
        } else if ($_ss == '6') { //如果是星期六的話，則延後兩天
            $weekend = 2;
        }
    }
    ##

    $_minus = $_minus + $weekend;                                                             //傳進來的日期必須加上遇到加日延後的日期
    $_t     = date("Y-m-d", mktime(0, 0, 0, $_aDate[1], ($_aDate[2] + $_minus), $_aDate[0])); //設定日期為 t+1 天
    unset($_aDate);

    $_aDate = explode('-', $_t);

    if ($_dateType == 'r') { //若要回覆日期格式為"民國"
        $_aDate[0] = $_aDate[0] - 1911;
    } else { //若要回覆日期格式為"西元"

    }

    //決定回覆日期格式
    switch ($_dateForm) {
        case 'y': //年
            return $_aDate[0];
            break;
        case 'm': //月
            return $_aDate[1];
            break;
        case 'd': //日
            return $_aDate[2];
            break;
        case 'ym': //年月
            return $_aDate[0] . $_delimiter . $_aDate[1];
            break;
        case 'md': //月日
            return $_aDate[1] . $_delimiter . $_aDate[2];
            break;
        case 'ymd': //年月日
            return $_aDate[0] . $_delimiter . $_aDate[1] . $_delimiter . $_aDate[2];
            break;
        default:
            break;
    }
    ##
}
##

//保證費
$tmptotal       = $data_income['cTotalMoney'];
$certifiedMoney = $data_income['cCertifiedMoney'];

$part                                 = round($certifiedMoney / $tmptotal, 5);
$tmp                                  = explode('.', $part);
$data_income['cCertifiedMoneyPower1'] = (int) $tmp[1];
$data_income['cCertifiedMoneyPower2'] = str_pad(1, (strlen($tmp[1]) + 1), 0, STR_PAD_RIGHT);
unset($tmp);
##

$data_property_count   = ($data_property) ? count($data_property) : 0;
$bc                    = ($branch_count) ? ($branch_count + 2) : 2;
$buyer_other_count     = ($buyer_other) ? (count($buyer_other) + 2) : 2;
$owner_other_count     = ($owner_other) ? (count($owner_other) + 2) : 2;
$ownerSalesPhone_count = ($ownerSalesPhone) ? (count($ownerSalesPhone) + 1) : 1;
$buyerSalesPhone_count = ($buyerSalesPhone) ? (count($buyerSalesPhone) + 1) : 1;
$caseSalesCount        = (is_array($caseSales)) ? count($caseSales) : 0;
##

$sql   = "SELECT lStatus FROM tLegalCase WHERE lCertifiedId = '" . $id . "'";
$rs    = $conn->Execute($sql);
$legal = $rs->fields;
##

$landPrice = [];
$sql       = "SELECT * FROM tContractLandPrice WHERE cCertifiedId = '" . $id . "' AND cLandItem = 0 AND cItem < 2"; //只取兩筆 這兩筆不會被刪除
$rs        = $conn->Execute($sql);

while (! $rs->EOF) {
    if (preg_match("/0000\-00\-00/", $rs->fields['cMoveDate'])) {$rs->fields['cMoveDate'] = '';} else { $rs->fields['cMoveDate'] = $advance->ConvertDateToRoc($rs->fields['cMoveDate'], base::DATE_FORMAT_NUM_MONTH);}

    array_push($landPrice, $rs->fields);

    $rs->MoveNext();
}

if (empty($landPrice)) {
    $landPrice = [0 => [], 1 => []];
}
##

function branchNote($branch_id)
{
    global $conn;

    //備註
    $sql = "SELECT * FROM tBranchNote WHERE bStore = '" . $branch_id . "' AND bDel = 0 AND bStatus = 0";
    $rs  = $conn->Execute($sql);
    $txt = '';
    while (! $rs->EOF) {
        $txt .= $rs->fields['bNote'] . "\r\n\r\n";
        $rs->MoveNext();
    }

    return $txt;
}
$checkOwnerAddr = in_array($data_case['cCaseStatus'], [3, 4, 9, 10]) ? 1 : 0;

//20240215 加入顧代書選單
$sql         = 'SELECT cKuDownload FROM tContractScrivener WHERE cCertifiedId = "' . $id . '";';
$rs          = $conn->Execute($sql);
$ku_download = $rs->EOF ? 'N' : $rs->fields['cKuDownload'];

//
$smarty->assign('delNote', $delNote);
$smarty->assign('landPrice', $landPrice);
$smarty->assign('legal', $legal);
$smarty->assign('feedbackScrivenerCheck', $feedbackScrivenerCheck);
$smarty->assign('caseSalesCount', $caseSalesCount);
$smarty->assign('buyerSalesPhone_count', $buyerSalesPhone_count);
$smarty->assign('ownerSalesPhone_count', $ownerSalesPhone_count);
$smarty->assign('menu_LandFee', $menu_LandFee);
$smarty->assign('data_LandCategory', $data_LandCategory);
$smarty->assign('menu_landCategoryLand', $menu_landCategoryLand);
$smarty->assign('menu_landCategoryBuild', $menu_landCategoryBuild);
$smarty->assign('menu_checklist', [1 => '全部不帶入點交單和出款']);
$smarty->assign('checkOwnerNoteTime', $checkOwnerNoteTime);
$smarty->assign('dataSellerNote', $dataSellerNote);
$smarty->assign('menuSellerNote', $menuSellerNote);
$smarty->assign('checkOwnerNote', $checkOwnerNote);
$smarty->assign('buyerSalesPhone', $buyerSalesPhone);
$smarty->assign('ownerSalesPhone', $ownerSalesPhone);
$smarty->assign('incomeTotal', number_format($incomeTotal));
$smarty->assign('outgoingTotal', number_format($outgoingTotal));
$smarty->assign('taishinSPMoney', $taishinSPMoney);
$smarty->assign('unf', $unf);
$smarty->assign('income_reason', $income_reason);
$smarty->assign('checkCertifiedFee', $checkCertifiedFee);
$smarty->assign('disabled_caseStatus', $disabled_caseStatus);
$smarty->assign('scr_sFeedbackMoney', $scr_sFeedbackMoney);
$smarty->assign('SalesReview', $SalesReview);
$smarty->assign('contractNote', $contractNote);
$smarty->assign('CaseFeedBackModifier', $CaseFeedBackModifier);
$smarty->assign('cCaseFeedBackModifyTime', $cCaseFeedBackModifyTime);
$smarty->assign('ScrivenerFeedSpTxt', $ScrivenerFeedSpTxt);
$smarty->assign('data_service', $data_service);
$smarty->assign('menu_Undertaker', $menu_Undertaker);
$smarty->assign('otherFeed', $otherFeed);
if ($otherFeed) {
    $smarty->assign('otherFeedCount', count($otherFeed));
}

$smarty->assign('otherFeedStore', getStore(1));
$smarty->assign('individual', $individual);
$smarty->assign('scr_sCategory', $scr_sCategory);
$smarty->assign('buyer_bank', $buyer_bank);
$smarty->assign('owner_bank', $owner_bank);
if ($buyer_bank) {
    $smarty->assign('buyer_bank_count', count($buyer_bank));
}

if ($owner_bank) {
    $smarty->assign('owner_bank_count', count($owner_bank));
}

$smarty->assign('data_invoice_another', $data_invoice_another);
$smarty->assign('data_int_another', $data_int_another);
$smarty->assign('brand_type1', $brand_type1); //仲介店(1)類別
$smarty->assign('brand_type2', $brand_type2); //仲介店(2)類別
$smarty->assign('brand_type3', $brand_type3); //仲介店(2)類別
$smarty->assign('brand_type4', $brand_type4); //仲介店(2)類別
$smarty->assign('ScrivenerName', $ScrivenerName);
$smarty->assign('cCaseStatus', $cCaseStatus);
$smarty->assign('data_buyer1', $data_buyer1);
$smarty->assign('data_buyer5', $data_buyer5);
$smarty->assign('data_owner1', $data_owner1);
$smarty->assign('CertifyDate', $cCertifyDate); //履保費出款日
$smarty->assign('data_bankcode', $data_bankcode);
$smarty->assign('tbl', $tbl);
$smarty->assign('total', number_format($total));
$smarty->assign('minus_money', $minus_money);
$smarty->assign('processing', $processing);
$smarty->assign('parking', $parking);
$smarty->assign('check_object', $check_object);
$smarty->assign('object_option', $object_option);
$smarty->assign('ascription_option', $ascription_option);
$smarty->assign('ascription_option2', $ascription_option2);
$smarty->assign('data_ascription', $data_ascription);
$smarty->assign('data_rent', $data_rent);

$smarty->assign('furniture', $data_furniture);
$smarty->assign('property_finish', $property_finish);
$smarty->assign('property_other', $property_other);
$smarty->assign('owner_mobile', $owner_mobile);
$smarty->assign('buy_mobile', $buy_mobile);

$smarty->assign('branch_options', $branch_options);
$smarty->assign('branch_options1', $branch_options1);
$smarty->assign('branch_options2', $branch_options2);
$smarty->assign('branch_options3', $branch_options3);

$smarty->assign('second_branch', $second_branch);
$smarty->assign('third_branch', $third_branch);
$smarty->assign('fourth_branch', $fourth_branch);
$smarty->assign('STargetOption', $STargetOption);
$smarty->assign('cServiceTarget', $cServiceTarget);
$smarty->assign('cServiceTarget1', $cServiceTarget1);
$smarty->assign('cServiceTarget2', $cServiceTarget2);
$smarty->assign('cServiceTarget3', $cServiceTarget3);

$smarty->assign('certifiedchg', $certifiedchg);
$smarty->assign('promissory1', $promissory1);
$smarty->assign('promissory2', $promissory2);
$smarty->assign('promissory3', $promissory3);
$smarty->assign('promissory4', $promissory4);
$smarty->assign('web_addr', $web_addr);
$smarty->assign('int_total', $int_total);
$smarty->assign('undertaker', $undertaker);
$smarty->assign('fbDisabled', $fbDisabled);
$smarty->assign('fbcheckedR', $fbcheckedR);
$smarty->assign('fbcheckedS', $fbcheckedS);

$smarty->assign('inputSelect2', [0 => '待確認', 1 => '是', 2 => '否']);
$smarty->assign('inputSelect', [0 => '否', 1 => '是']);
$smarty->assign('owner_resident_seledted', $data_owner['cResidentLimit']);
$smarty->assign('buyer_resident_seledted', $data_buyer['cResidentLimit']);

$smarty->assign('feedback', $feedback);
$smarty->assign('_disabled', $_disabled);
$smarty->assign('scrivenerDisabled', $scrivenerDisabled);

$smarty->assign('limit_show', $limit_show);
$smarty->assign('_tabs', $_tabs);
$smarty->assign('is_edit', 1);
$smarty->assign('menu_ftype', [1 => '地政士', 2 => '仲介']);
$smarty->assign('menu_material', $menu_material);
$smarty->assign('menu_objkind', $menu_objkind);
$smarty->assign('menu_objuse', $menu_objUse);
$smarty->assign('menu_statuscontract', $menu_statuscontract);
$smarty->assign('menu_statusincome', $menu_statusincome);
$smarty->assign('menu_statusexpenditure', $menu_statusexpenditure);
$smarty->assign('menu_categroyexception', $menu_categroyexception);
$smarty->assign('menu_reportupload', $menu_reportupload);
$smarty->assign('menu_categroyrealestate', $menu_categroyrealestate);
$smarty->assign('menu_categorybank_twhg', $menu_categorybank_twhg);
$smarty->assign('menu_categoryrealestate', $menu_categoryrealestate);
$smarty->assign('menu_categorycontract', $menu_categorycontract);
$smarty->assign('menu_scrivener', $menu_scrivener);
$smarty->assign('menu_budlevel', $menu_budlevel);
$smarty->assign('menu_categorysex', $menu_categorysex);
$smarty->assign('menu_categorycar', $menu_categorycar);
$smarty->assign('menu_categorycertifyid', $menu_categorycertifyid);
$smarty->assign('menu_categoryland', $menu_categoryland);
$smarty->assign('menu_categoryarea', $menu_categoryarea);
$smarty->assign('menu_brand', $menu_brand);
$smarty->assign('menu_bank', $menu_bank);
$smarty->assign('owner_menu_branch', $owner_menu_branch);
$smarty->assign('buyer_menu_branch', $buyer_menu_branch);
$smarty->assign('data_case', $data_case);
$smarty->assign('data_realstate', $data_realstate);
$smarty->assign('data_scrivener', $data_scrivener);
$smarty->assign('data_buyer_other', $buyer_other);
$smarty->assign('buyer_other_count', $buyer_other_count);
$smarty->assign('data_owner_other', $owner_other);
$smarty->assign('data_owner_total', is_array($owner_other) ? count($owner_other) : 0);
$smarty->assign('owner_other_count', $owner_other_count);
$smarty->assign('branch_count', $bc);
$smarty->assign('data_property', $data_property);
$smarty->assign('data_property_count', $data_property_count);
$smarty->assign('data_income', $data_income);
$smarty->assign('data_expenditure', $data_expenditure);
$smarty->assign('data_buyer', $data_buyer);
$smarty->assign('data_owner', $data_owner);
$smarty->assign('data_invoice', $data_invoice);
$smarty->assign('TaxReceipt', $TaxReceipt);
$smarty->assign('data_land', $data_land);
$smarty->assign('case_undertaker', $case_undertaker);
$smarty->assign('case_lasteditor', $case_lasteditor);
$smarty->assign('WEB_STAGE', $GLOBALS['WEB_STAGE']);
$smarty->assign('checkOwnerAddr', $checkOwnerAddr);

$smarty->assign('land_country', listCity($conn, $data_land['cZip']));               //土地縣市
$smarty->assign('land_area', listArea($conn, $data_land['cZip']));                  //土地區域
$smarty->assign('property_country', listCity($conn, $data_property['cZip']));       //建物縣市
$smarty->assign('property_area', listArea($conn, $data_property['cZip']));          //建物區域
$smarty->assign('scrivener_country', listCity($conn, $scr['sZip1']));               //地政士縣市
$smarty->assign('scrivener_area', listArea($conn, $scr['sZip1']));                  //地政士區域
$smarty->assign('scrivener_zip', $scr['sZip1']);                                    //地政士郵遞區號
$smarty->assign('owner_registcountry', listCity($conn, $data_owner['cRegistZip'])); //賣方戶籍縣市
$smarty->assign('owner_registarea', listArea($conn, $data_owner['cRegistZip']));    //賣方戶籍區域
$smarty->assign('owner_basecountry', listCity($conn, $data_owner['cBaseZip']));     //賣方通訊縣市
$smarty->assign('owner_basearea', listArea($conn, $data_owner['cBaseZip']));        //賣方通訊區域
$smarty->assign('buyer_registcountry', listCity($conn, $data_buyer['cRegistZip'])); //買方戶籍縣市
$smarty->assign('buyer_registarea', listArea($conn, $data_buyer['cRegistZip']));    //買方戶籍區域
$smarty->assign('buyer_basecountry', listCity($conn, $data_buyer['cBaseZip']));     //買方通訊縣市
$smarty->assign('buyer_basearea', listArea($conn, $data_buyer['cBaseZip']));        //買方通訊區域
$smarty->assign('realestate_country', listCity($conn, $rel1['bZip']));              //第一組仲介縣市
$smarty->assign('realestate_area', listArea($conn, $rel1['bZip']));                 //第一組仲介區域
$smarty->assign('realestate_country1', listCity($conn, $rel2['bZip']));             //第二組仲介縣市
$smarty->assign('realestate_area1', listArea($conn, $rel2['bZip']));                //第二組仲介區域
$smarty->assign('realestate_country2', listCity($conn, $rel3['bZip']));             //第三組仲介縣市
$smarty->assign('realestate_area2', listArea($conn, $rel3['bZip']));                //第三組仲介區域
$smarty->assign('realestate_country3', listCity($conn, $rel4['bZip']));             //第四組仲介縣市
$smarty->assign('realestate_area3', listArea($conn, $rel4['bZip']));                //第四組仲介區域

$smarty->assign('realestate_status', $rel1['bStatus']);
$smarty->assign('realestate_status1', $rel2['bStatus']);
$smarty->assign('realestate_status2', $rel3['bStatus']);
$smarty->assign('realestate_status3', $rel4['bStatus']);
$smarty->assign('scrivener_sales', $scrivener_sales);             //地政業務
$smarty->assign('branchnum_sales', $branchnum_sales);             //仲介店(1)業務 (下拉的)
$smarty->assign('branchnum_sales1', $branchnum_sales1);           //仲介店(2)業務(下拉的)
$smarty->assign('branchnum_sales2', $branchnum_sales2);           //仲介店(3)業務(下拉的)
$smarty->assign('branchnum_sales3', $branchnum_sales3);           //仲介店(4)業務(下拉的)
$smarty->assign('branchnum_data_sales', $branchnum_data_sales);   //仲介店(1)業務資訊
$smarty->assign('branchnum_data_sales1', $branchnum_data_sales1); //仲介店(2)業務資訊
$smarty->assign('branchnum_data_sales2', $branchnum_data_sales2); //仲介店(3)業務資訊
$smarty->assign('branchnum_data_sales3', $branchnum_data_sales3); //仲介店(4)業務資訊
$smarty->assign('scrivener_office', $scrivener_office);
$smarty->assign('scrivener_sSpRecall', $scrivener_sSpRecall); //特殊回饋 $scrivener_brand
$smarty->assign('scrivener_brand', $scrivener_brand);
$smarty->assign('sSpRecall', $sSpRecall);               //特殊回饋金顯示
$smarty->assign('branch_type1', $branch_type1);         //仲介店(1)
$smarty->assign('branch_type2', $branch_type2);         //仲介店(2)
$smarty->assign('branch_type3', $branch_type3);         //仲介店(3)
$smarty->assign('branch_type4', $branch_type4);         //仲介店(4)
$smarty->assign('branch_cat1', $branch_cat1);           //
$smarty->assign('branch_cat2', $branch_cat2);           //
$smarty->assign('branch_cat3', $branch_cat3);           //
$smarty->assign('branch_cat4', $branch_cat4);           //
$smarty->assign('sales_option', $sales_option);         //業務option
$smarty->assign('menu_countrycode', $menu_countrycode); //國籍代碼
$smarty->assign('feedbackAccount', $feedbackAccount);   //回饋金帳戶

$smarty->assign('sales1', $Sales1); //被選的業務仲介一
$smarty->assign('sales2', $Sales2); //被選的業務仲介二
$smarty->assign('sales3', $Sales3); //被選的業務仲介三
$smarty->assign('sales4', $Sales4); //被選的業務仲介三
$smarty->assign('rel1', $rel1);     //仲介店資料1
$smarty->assign('rel2', $rel2);     //仲介店資料2
$smarty->assign('rel3', $rel3);     //仲介店資料3
$smarty->assign('rel4', $rel4);     //仲介店資料4

$smarty->assign('scrivener_feedDateCat', $scr['sFeedDateCat']); //地政士回饋方式 季/月/隨案

$smarty->assign('data_feedData1', $data_feedData1); //回饋金資料1
// 確保變數有預設值
$data_feedData2 = $data_feedData2 ?? [];
$data_feedData3 = $data_feedData3 ?? [];
$data_feedData4 = $data_feedData4 ?? [];

$data_feedDataCount2 = $data_feedDataCount2 ?? 0;
$data_feedDataCount3 = $data_feedDataCount3 ?? 0;
$data_feedDataCount4 = $data_feedDataCount4 ?? 0;

$data_bFeedbackMoney2 = $data_bFeedbackMoney2 ?? 0;
$data_bFeedbackMoney3 = $data_bFeedbackMoney3 ?? 0;
$data_bFeedbackMoney4 = $data_bFeedbackMoney4 ?? 0;

$smarty->assign('data_feedData2', $data_feedData2); //回饋金資料2
$smarty->assign('data_feedData3', $data_feedData3); //回饋金資料3
$smarty->assign('data_feedData4', $data_feedData4); //回饋金資料3

$smarty->assign('data_feedDataCount1', $data_feedDataCount1); //回饋金資料1
$smarty->assign('data_feedDataCount2', $data_feedDataCount2); //回饋金資料2
$smarty->assign('data_feedDataCount3', $data_feedDataCount3); //回饋金資料3
$smarty->assign('data_feedDataCount4', $data_feedDataCount4); //回饋金資料4

$smarty->assign('data_bFeedbackMoney1', $data_bFeedbackMoney1); //未收足回饋
$smarty->assign('data_bFeedbackMoney2', $data_bFeedbackMoney2); //未收足回饋
$smarty->assign('data_bFeedbackMoney3', $data_bFeedbackMoney3); //未收足回饋
$smarty->assign('data_bFeedbackMoney4', $data_bFeedbackMoney4); //未收足回饋

//回饋帶入
//第一間店
$smarty->assign('FeedBaseCity', listCity($conn, $rel1['bZip3']));
$smarty->assign('FeedBaseArea', listArea($conn, $rel1['bZip3']));
$smarty->assign('FeedRegistCity', listCity($conn, $rel1['bZip2']));
$smarty->assign('FeedRegistArea', listArea($conn, $rel1['bZip2']));
$smarty->assign('menu_categoryrecall', $brand->GetCategoryRecall());
$smarty->assign('menu_categoryidentify', $brand->GetCategoryIdentify());
$smarty->assign('menu_bank', $brand->GetBankMenuList());

$smarty->assign('imgStampEdit', $imgStampEdit);
$smarty->assign('imgStamp', $imgStamp);

$smarty->assign('menu_branch', getBankBranch($conn, $rel1['bAccountNum5'], $rel1['bAccountNum6']));

//第二間店
$smarty->assign('FeedBaseCity1', listCity($conn, $rel2['bZip3']));
$smarty->assign('FeedBaseArea1', listArea($conn, $rel2['bZip3']));
$smarty->assign('FeedRegistCity1', listCity($conn, $rel2['bZip2']));
$smarty->assign('FeedRegistArea1', listArea($conn, $rel2['bZip2']));
$smarty->assign('menu_categoryrecall1', $brand->GetCategoryRecall());
$smarty->assign('menu_categoryidentify1', $brand->GetCategoryIdentify());
$smarty->assign('menu_bank1', $brand->GetBankMenuList());

$smarty->assign('imgStampEdit1', $imgStampEdit1);
$smarty->assign('imgStamp1', $imgStamp1);

$smarty->assign('menu_branch1', getBankBranch($conn, $rel2['bAccountNum5'], $rel2['bAccountNum6']));

//第三間店
$smarty->assign('FeedBaseCity2', listCity($conn, $rel3['bZip3']));
$smarty->assign('FeedBaseArea2', listArea($conn, $rel3['bZip3']));
$smarty->assign('FeedRegistCity2', listCity($conn, $rel3['bZip2']));
$smarty->assign('FeedRegistArea2', listArea($conn, $rel3['bZip2']));
$smarty->assign('menu_categoryrecall2', $brand->GetCategoryRecall());
$smarty->assign('menu_categoryidentify2', $brand->GetCategoryIdentify());
$smarty->assign('menu_bank2', $brand->GetBankMenuList());

//第4間店
$smarty->assign('FeedBaseCity3', listCity($conn, $rel4['bZip3']));
$smarty->assign('FeedBaseArea3', listArea($conn, $rel4['bZip3']));
$smarty->assign('FeedRegistCity3', listCity($conn, $rel4['bZip2']));
$smarty->assign('FeedRegistArea3', listArea($conn, $rel4['bZip2']));
$smarty->assign('menu_categoryrecall3', $brand->GetCategoryRecall());
$smarty->assign('menu_categoryidentify3', $brand->GetCategoryIdentify());
$smarty->assign('menu_bank3', $brand->GetBankMenuList());

$smarty->assign('imgStampEdit2', $imgStampEdit2);
$smarty->assign('imgStamp2', $imgStamp2);

$smarty->assign('menu_branch2', getBankBranch($conn, $rel3['bAccountNum5'], $rel3['bAccountNum6']));
$smarty->assign('showAffixBranch', $showAffixBranch);

$smarty->assign('menu_legal_items', $menu_legal_items);
$smarty->assign('selected_legal_items', empty($legal_record['lItem']) ? '' : $legal_record['lItem']);
$smarty->assign('legal_record', $legal_record);
$smarty->assign('legal_record_edit', $legal_record_edit);

$smarty->assign('ku_download_menu', ['Y' => ' 是', 'N' => ' 否']);
$smarty->assign('ku_download', $ku_download);

//是否要判斷 服務費收款店checkbox 選項
$smarty->assign('funcAffixBranch', $funcAffixBranch);

$smarty->display('formbuyowner.inc.tpl', '', 'escrow');
