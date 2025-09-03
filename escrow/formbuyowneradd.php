<?php
// 暫時關閉所有錯誤和警告提示
error_reporting(0);
ini_set('display_errors', 0);

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/class/contract.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/class/member.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/class/getBank.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';

$advance   = new Advance();
$contract  = new Contract();
$scrivener = new Scrivener();
$member    = new Member();

$list_material          = $contract->GetMaterialsList();
$menu_material          = $contract->ConvertOption($list_material, 'bTypeId', 'bTypeName');
$list_objkind           = $contract->GetObjKind();
$menu_objkind           = $contract->ConvertOption($list_objkind, 'oTypeId', 'oTypeName');
$list_ObjUse            = $contract->GetObjUse();
$menu_objUse            = $contract->ConvertOption($list_ObjUse, 'uId', 'uName');
$list_statuscontract    = $contract->GetStatusContract();
$menu_statuscontract    = $contract->ConvertOption($list_statuscontract, 'sId', 'sName');
$list_statusexpenditure = $contract->GetStatusExpenditure();
$menu_statusexpenditure = $contract->ConvertOption($list_statusexpenditure, 'sId', 'sName');
$list_statusincome      = $contract->GetStatusIncome();
$menu_statusincome      = $contract->ConvertOption($list_statusincome, 'sId', 'sName');
$menu_StatusIncome      = $contract->ConvertOption($list_statusincome, 'sId', 'sName');
$list_brand             = $contract->GetCategoryBrand();
$menu_brand             = $contract->ConvertOption($list_brand, 'bId', 'bName');
$list_categroyexception = $contract->GetCategoryException();
$menu_categroyexception = $contract->ConvertOption($list_categroyexception, 'sId', 'sName');
$menu_reportupload      = ['0' => '預設上傳', '1' => '關閉不上傳(品牌、群義品牌)'];
$list_categorybank_twhg = $contract->GetContractBank();
$menu_categorybank_twhg = $contract->ConvertOption($list_categorybank_twhg, 'cBankCode', 'bankName');
$list_scrivener         = $scrivener->GetListScrivener();

$menu_scrivener    = [];
$menu_scrivener[0] = '--------';

foreach ($list_scrivener as $k => $v) {
    $menu_scrivener[$v['sId']] = 'SC' . str_pad($v['sId'], 4, 0, STR_PAD_LEFT) . $v['sName'];
}

$menu_budlevel           = $scrivener->GetBudLevel();
$menu_categorysex        = $contract->GetCategorySex();
$menu_categorycar        = $contract->GetCategoryCar();
$list_categoryland       = $contract->GetCategoryLand();
$menu_categoryland       = $scrivener->ConvertOption($list_categoryland, 'cId', 'cName');
$menu_categoryarea       = $contract->GetCategoryAreaMenuList();
$menu_categoryrealestate = $contract->GetCategoryRealestate();
$menu_categorycontract   = $contract->GetCategoryContract();
$menu_branch             = []; // 初始化 menu_branch 變數

//Default
$data_case['cCaseStatus']        = '1';
$data_case['cUndertakerId']      = $_SESSION['member_id'];
$data_case['cBank']              = '8';
$data_case['cApplyDate']         = date("Y-m-d");
$data_case['cRelatedCase']       = '';            // 初始化關聯案件字段
$data_case['cSignDate']          = date("Y-m-d"); // 初始化簽約日期
$data_case['cEscrowBankAccount'] = '';            // 初始化託管銀行帳戶
$data_case['cName']              = '';            // 初始化案件名稱
$data_case['count']              = 0;             // 初始化計數
$data_case['cIdentifyId']        = '';            // 初始化身份識別碼

$data_realstate['cName']         = '台灣房屋仲介股份有限公司';
$data_realstate['cBrand']        = '1';
$data_realstate['cSerialNumber'] = '28473562';
$data_realstate['cProperty']     = ''; // 添加 cProperty 鍵

// 初始化可能會在模板中使用的陣列
$data_owner = [
    'cName'         => '', // 初始化賣方名稱
    'count'         => 0,  // 初始化賣方計數
    'cAddr_country' => '', // 初始化賣方地址國家/縣市
    'cAddr'         => '', // 初始化賣方詳細地址
];

$data_buyer = [
    'cName'         => '', // 初始化買方名稱
    'count'         => 0,  // 初始化買方計數
    'cAddr_country' => '', // 初始化買方地址國家/縣市
    'cAddr'         => '', // 初始化買方詳細地址
];

$data_scrivener = [
    'cName'         => '', // 初始化地政士名稱
    'count'         => 0,  // 初始化地政士計數
    'cAddr_country' => '', // 初始化地政士地址國家/縣市
    'cAddr'         => '', // 初始化地政士詳細地址
];

$data_realestate1 = [
    'cName'         => '', // 初始化第二組仲介名稱
    'count'         => 0,  // 初始化第二組仲介計數
    'cAddr_country' => '', // 初始化第二組仲介地址國家/縣市
    'cAddr'         => '', // 初始化第二組仲介詳細地址
];

$data_realestate2 = [
    'cName'         => '', // 初始化第三組仲介名稱
    'count'         => 0,  // 初始化第三組仲介計數
    'cAddr_country' => '', // 初始化第三組仲介地址國家/縣市
    'cAddr'         => '', // 初始化第三組仲介詳細地址
];

// 初始化 $data_realestate 陣列，避免 null 陣列偏移錯誤
$data_realestate = [
    'cName'         => '',
    'count'         => 0,
    'cAddr_country' => '',
    'cAddr'         => '',
    'cProperty'     => '', // 添加 cProperty 鍵
];

// 確保 $data_case 也有 cProperty 鍵
$data_case['cProperty'] = '';

// 初始化可能的null變數，避免在模板中嘗試訪問陣列偏移時出錯
$data_owner       = []; // 賣方資料
$data_buyer       = []; // 買方資料
$data_scrivener   = []; // 地政士資料
$data_realestate  = []; // 仲介資料
$data_realestate1 = []; // 第二組仲介資料
$data_realestate2 = []; // 第三組仲介資料

//設定仲介服務對象
$STargetOption = [1 => '買賣方', 2 => '賣方', 3 => '買方'];
##

$case_undertaker = $member->GetMemberInfo($_SESSION['member_id'], 1);
$TaxReceipt      = [1 => '賣方', 2 => '買方', 3 => '無'];
$_disabled       = ' disabled="disabled"';

##點交前(租客是否願意搬遷)
$property_finish = ['1' => '租客願意搬遷', '2' => '租客不願意搬遷'];
$property_other  = ['1' => '買方自行排除', '2' => '由賣方負責'];

##契稅之歸屬
$ascription_option  = ['1' => '地政規費', '2' => '設定規費', '3' => '印花稅', '4' => '地政士業務執行費', '5' => '公證或監證費', '6' => '簽約費', '7' => '火險及地震險費', '8' => '塗銷費', '9' => '貸款相關費用', 10 => '實價登錄費', 11 => '履保費', 12 => '土地增值稅', 13 => '契稅'];
$ascription_option2 = ['1' => '一般稅率', '2' => '自用住宅優惠稅率'];
##
$object_option = ['1' => '一樓/法定空地', '2' => '騎樓', '3' => '陽台', '4' => '露台', '5' => '平台', '6' => '防火巷', '7' => '地下室', '8' => '夾層', '9' => '其他'];
##

$data_invoice['cInvoiceOwner']      = 0;
$data_invoice['cInvoiceBuyer']      = 0;
$data_invoice['cInvoiceRealestate'] = 0;
$data_invoice['cInvoiceScrivener']  = 0;
$data_invoice['cInvoiceOther']      = 0;

$data_income['cCertifiedMoneyPower1'] = 6;
$data_income['cCertifiedMoneyPower2'] = 10000;
$data_income['cCertifiedId']          = ''; // 初始化 cCertifiedId 變數
##

$data_property[0]['cItem'] = '0';
##

// 取案件進度
// 初始化變數以修復警告
$list                    = isset($list) ? $list : [];
$list['cCaseProcessing'] = isset($list['cCaseProcessing']) ? $list['cCaseProcessing'] : 0;
$list['status']          = isset($list['status']) ? $list['status'] : '';

$processing = '';
for ($j = 1; $j < 7; $j++) {
    $_index = ($list['status'] == '3') ? '' : ' onclick="processing(' . $j . ')"';

    $processing .= '<td id="ps' . $j . '"' . $_index; // 修正使用 $_index 而非 $index
    if (($j <= $list['cCaseProcessing']) || ($list['status'] == '3')) {
        $processing .= ' class="step_class"';
    }

    $processing .= '>　</td>' . "\n";
}
##

// 初始化變數以修復警告
$tbl        = '';
$colorIndex = '#f5f5f5'; // 設定一個預設的顏色

if ($tbl == '') {
    $tbl = '
	<tr style="background-color:' . $colorIndex . ';">
		<td colspan="7">尚無出入款紀錄!!</td>
	</tr>
	';
}

//承辦人選單
$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep = 5 ORDER BY pId ASC";
$rs  = $conn->Execute($sql);

$menu_Undertaker[0] = '請選擇';
while (! $rs->EOF) {
    $menu_Undertaker[$rs->fields['pName']] = $rs->fields['pName'];
    $rs->MoveNext();
}
##

$checkOwnerNote = 1; //賣方備註顯示
##

$menu_landCategoryLand  = [1 => '買賣標的如為農地，不得做為興建農舍、建蔽率、通行權或套繪管制使用之土地，且賣方應檢附農業用地作農業使用證明書。', 2 => '買方知悉農地存有或可能有上述情形，仍同意履行契約。(未勾選視為買方不同意)'];
$menu_landCategoryBuild = [1 => '買賣標的如為建地，不得作為法定空地、建蔽率、容積率或通行權使用之土地，且須可申請為建造之建築用地，並應提供賣方或第三人土地使用權同意書(若無則免除)使買方得申請建造興建房屋，縱賭地完成移轉登記予買方，賣方仍應擔保前述責任', 2 => '買方知悉建地存有或可能有上述情形，仍同意履行契約。(未勾選視為買方不同意) '];
$menu_LandFee           = [1 => '買方負擔', 2 => '賣方負擔'];
##

//解約條款預設2
$data_case['cCancellingClause'] = 2;
##

$smarty->assign('landPrice', [0 => [], 1 => []]);
$smarty->assign('menu_LandFee', $menu_LandFee);
$smarty->assign('menu_landCategoryLand', $menu_landCategoryLand);
$smarty->assign('menu_landCategoryBuild', $menu_landCategoryBuild);
$smarty->assign('checkOwnerNote', $checkOwnerNote);
$smarty->assign('menu_Undertaker', $menu_Undertaker);
$smarty->assign('tbl', $tbl);
$smarty->assign('processing', $processing);
$smarty->assign("data_property", $data_property);
$smarty->assign("data_property_count", count($data_property));
$smarty->assign('object_option', $object_option);
$smarty->assign('ascription_option', $ascription_option);
$smarty->assign('ascription_option2', $ascription_option2);
$smarty->assign('property_finish', $property_finish);
$smarty->assign('property_other', $property_other);
$smarty->assign('second_branch', 'none');
$smarty->assign('third_branch', 'none');
$smarty->assign('fourth_branch', 'none');
$smarty->assign('int_total', '尚未產生利息');
$smarty->assign('add_disabled', 'disabled="disabled"');
$smarty->assign('fbDisabled', ' disabled="disabled"');
$smarty->assign('fbcheckedR', ' checked="checked"');
$smarty->assign('fbcheckedS', '');
$smarty->assign('is_edit', 0);
$smarty->assign('_disabled', $_disabled);
$smarty->assign('limit_show', 0);
$smarty->assign('_tabs', '0');
$smarty->assign('data_case', $data_case);
$smarty->assign('data_realstate', $data_realstate);
$smarty->assign('data_income', $data_income);
$smarty->assign('data_owner', $data_owner);             // 添加賣方資料
$smarty->assign('data_buyer', $data_buyer);             // 添加買方資料
$smarty->assign('data_scrivener', $data_scrivener);     // 添加地政士資料
$smarty->assign('data_realestate', $data_realestate);   // 添加仲介資料
$smarty->assign('data_realestate1', $data_realestate1); // 添加第二組仲介資料
$smarty->assign('data_realestate2', $data_realestate2); // 添加第三組仲介資料
$smarty->assign('menu_material', $menu_material);
$smarty->assign('menu_objkind', $menu_objkind);
$smarty->assign('menu_objuse', $menu_objUse);
$smarty->assign('menu_statuscontract', $menu_statuscontract);
$smarty->assign('menu_statusincome', $menu_statusincome);
$smarty->assign('menu_statusexpenditure', $menu_statusexpenditure);
$smarty->assign('menu_categroyexception', $menu_categroyexception);
$smarty->assign('menu_reportupload', $menu_reportupload);
$smarty->assign('menu_brand', $menu_brand);
$smarty->assign('menu_branch', $menu_branch);
$smarty->assign('menu_categorybank_twhg', $menu_categorybank_twhg);
$smarty->assign('menu_scrivener', $menu_scrivener);
$smarty->assign('menu_budlevel', $menu_budlevel);
$smarty->assign('menu_categorysex', $menu_categorysex);
$smarty->assign('menu_categorycar', $menu_categorycar);
$smarty->assign('menu_categoryland', $menu_categoryland);
$smarty->assign('menu_categoryarea', $menu_categoryarea);
$smarty->assign('menu_categoryrealestate', $menu_categoryrealestate);
$smarty->assign('menu_categorycontract', $menu_categorycontract);
$smarty->assign('case_undertaker', $case_undertaker);
$smarty->assign('TaxReceipt', $TaxReceipt);
$smarty->assign('data_invoice', $data_invoice);
$smarty->assign('uniqid', uniqid());
$smarty->assign('STargetOption', $STargetOption);
$smarty->assign('cServiceTarget', '1');
$smarty->assign('cServiceTarget1', '1');
$smarty->assign('cServiceTarget2', '1');
$smarty->assign('cServiceTarget3', '1');
$smarty->assign('land_country', listCity($conn));        //土地縣市
$smarty->assign('land_area', listArea($conn));           //土地區域
$smarty->assign('property_country', listCity($conn));    //建物縣市
$smarty->assign('property_area', listArea($conn));       //建物區域
$smarty->assign('scrivener_country', listCity($conn));   //地政士縣市
$smarty->assign('scrivener_area', listArea($conn));      //地政士區域
$smarty->assign('owner_registcountry', listCity($conn)); //賣方戶籍縣市
$smarty->assign('owner_registarea', listArea($conn));    //賣方戶籍區域
$smarty->assign('owner_basecountry', listCity($conn));   //賣方通訊縣市
$smarty->assign('owner_basearea', listArea($conn));      //賣方通訊區域
$smarty->assign('buyer_registcountry', listCity($conn)); //買方戶籍縣市
$smarty->assign('buyer_registarea', listArea($conn));    //買方戶籍區域
$smarty->assign('buyer_basecountry', listCity($conn));   //買方通訊縣市
$smarty->assign('buyer_basearea', listArea($conn));      //買方通訊區域
$smarty->assign('realestate_country', listCity($conn));  //第一組仲介縣市
$smarty->assign('realestate_area', listArea($conn));     //第一組仲介區域
$smarty->assign('realestate_country1', listCity($conn)); //第二組仲介縣市
$smarty->assign('realestate_area1', listArea($conn));    //第二組仲介區域
$smarty->assign('realestate_country2', listCity($conn)); //第三組仲介縣市
$smarty->assign('realestate_area2', listArea($conn));    //第三組仲介區域
$smarty->assign('otherFeedStore', getStore(1));
$smarty->assign('menu_ftype', [1 => '地政士', 2 => '仲介']);
$smarty->assign('sSpRecall', 'none'); //特殊回饋金顯示
$smarty->assign('inputSelect', [0 => '否', 1 => '是']);
$smarty->assign('inputSelect2', [0 => '待確認', 1 => '是', 2 => '否']);
$smarty->assign('legal_record_edit', 'disabled');
$smarty->assign('funcAffixBranch', '');

$smarty->display('formbuyowner.inc.tpl', '', 'escrow');
