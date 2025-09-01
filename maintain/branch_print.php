<?php
header("Content-Type:text/html; charset=utf-8");

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

$brand = new Brand();
$sms   = new SMS();
$data  = $brand->GetBranch($_POST["id"]);
$data  = $data[0];

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

//取得總行(2)選單
//$menu_bank11 = getBankMain($conn,$data['bAccountNum11']) ;
##

//取得分行(2)選單
$menu_branch21 = getBankBranch($conn, $data['bAccountNum11'], $data['bAccountNum21']);
##

//取得分行(3)選單
$menu_branch22 = getBankBranch($conn, $data['bAccountNum12'], $data['bAccountNum22']);
##

//取得回饋金總行選單
//$menu_bank5 = getBankMain($conn,$data['bAccountNum5']) ;
##

//取得回饋金分行選單
$menu_branch6 = getBankBranch($conn, $data['bAccountNum5'], $data['bAccountNum6']);
##

//修正地址縣市區域重複
$data['bAddress'] = filterCityAreaName($conn, $data['bZip'], $data['bAddress']);
$data['bAddr3']   = filterCityAreaName($conn, $data['bZip3'], $data['bAddr3']);
$data['bAddr2']   = filterCityAreaName($conn, $data['bZip2'], $data['bAddr2']);
##

$list_brand           = $brand->GetBrandList(array(8, 77));
$menu_brand           = $brand->ConvertOption($list_brand, 'bId', 'bName');
$menu_emailreceive    = array('1' => '有');
$menu_message         = array('1' => '有');
$menu_cashierorderhas = array('1' => '有');

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
		a.bBranch="' . trim(addslashes($_POST['id'])) . '"
		AND a.bCheck_id = 0
		AND a.bDel= 0
	ORDER BY
		a.bNID,b.tTitle
	ASC
;';

$rs       = $conn->Execute($sql);
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
/* 暫存
<tr>
<th>職稱︰</th>
<td>
<input type="hidden" name="tID" value="<{$item.sn}>" />
<input type="hidden" name="tNID" value="<{$item.id}>" />
<input type="text" class="input-text-mid" value="<{$item.tTitle}>" disabled='disabled' />
<input type="checkbox" name="defaultSms" value="<{$item.bMobile}>"<{$item.defaultSms}>>
</td>
<th>姓名︰</th>
<td>
<input type="text" name="tName" maxlength="14" class="input-text-per" value="<{$item.bName}>" />
</td>
<th>行動電話︰</th>
<td>
<input type="text" name="tMobile" maxlength="10" class="input-text-per" value="<{$item.bMobile}>" />
</td>
</tr>
 */
##

$sql = '
	SELECT
		a.bId as sn,
		a.bNID as id,
		a.bName as bName,
		a.bMobile as bMobile,
		b.tTitle as tTitle
	FROM
		tBranchFeedback AS a
	JOIN
		tTitle_SMS AS b ON a.bNID=b.id
	WHERE
		a.bBranch="' . trim(addslashes($_POST['id'])) . '"

	ORDER BY
		a.bNID,b.tTitle
	ASC
;';

// echo $sql."\r\n";
$rs           = $conn->Execute($sql);
$data_feedsms = array();
$i            = 0;
while (!$rs->EOF) {
    $data_feedsms[$i] = $rs->fields;

    $i++;
    $rs->MoveNext();
}
// echo "<pre>";
// print_r($data_feedsms);
// echo "</pre>";
##
$data['bCashierOrderDate'] = $brand->ConvertDateToRoc($data['bCashierOrderDate'], Brand::DATE_FORMAT_NUM_DATE);
$data['bCashierOrderSave'] = $brand->ConvertDateToRoc($data['bCashierOrderSave'], Brand::DATE_FORMAT_NUM_DATE);

//設定回饋年度範圍
for ($i = 2012; $i <= date("Y"); $i++) {
    $arr        = array();
    $tmp        = $rs->fields['cEndDate'];
    $arr        = explode('-', $tmp);
    $FBYear[$i] = ($i - 1911) . '&nbsp;';
    unset($tmp);unset($arr);
    $rs->MoveNext();
}
##

//建立簡訊對象身分
$sql = 'SELECT * FROM `tTitle_SMS` WHERE `tKind`=0 GROUP BY `tTitle` ORDER BY `tTitle` ASC;';
$rs  = $conn->Execute($sql);
while ($tmp = $rs->fields) {
    $sms_tNID .= '<option value="' . $tmp['id'] . '">' . $tmp['tTitle'] . "</option>\n";
    unset($tmp);
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
		bBranch="' . trim(addslashes($_POST['id'])) . '"
	ORDER BY
		bId
	ASC;
';
$rs     = $conn->Execute($sql);
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

    $tmp[$tIndex] = '<span style="padding:2px;background-color:' . $color . ';">';

    //判斷是否關店(1使用2停用) 如果是的話就不要有刪除的
    if (($data['bStatus'] == 1) && ($rs->fields['bStage'] != '2')) {
        $tmp[$tIndex] .= '<span onclick="del(' . $rs->fields['bId'] . ')" style="cursor:pointer;display:' . $display . '">X</span>';
    }

    ##

    $tmp[$tIndex] .= $rs->fields['bSalesName'];

    if ($rs->fields['bStage'] == '2') {
        $tmp[$tIndex] .= '(已審核)';
        $_stage[] = '<span style="padding:2px;background-color:' . $color . ';"><span onclick="salesConfirm(\'' . $rs->fields['bId'] . '\',\'n\')" style="cursor:pointer;display:' . $display . '">X</span>' . $rs->fields['bSalesName'] . '</span>';
    } else {
        $stage = '<input type="button" style="padding:5px;margin-right:10px;" value="確認" onclick="salesConfirm(\'' . $rs->fields['bId'] . '\',\'y\')">';
    }

    $tmp[$tIndex] .= '</span>';

    $tIndex++;
    $rs->MoveNext();
}
$bSales = implode(',', $tmp);
unset($tmp);

if (!$stage) {
    $stage = implode(',', $_stage);
}

unset($_stage);
##

//是否可調整回饋金權限
$_disabled = ' disabled="disabled"';

if ($_SESSION['member_pFeedBackModify'] == '1') {
    $_disabled = '';

}

##

$smarty->assign('_disabled', $_disabled);

$smarty->assign('stage', $stage);

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
$smarty->assign('menu_brand', $menu_brand);
$smarty->assign('menu_ppl', $menu_ppl);
$smarty->assign('menu_bank', $menu_bank); //總行(1)
$smarty->assign('menu_branch', $menu_branch); //分行(1)
//$smarty->assign('menu_bank11', $menu_bank11) ;        //總行(2)
$smarty->assign('menu_branch21', $menu_branch21); //分行(2)
$smarty->assign('menu_branch22', $menu_branch22); //分行(3)

//$smarty->assign('menu_bank5', $menu_bank5) ;            //回饋金總行
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
$smarty->assign('sms_tNID', $sms_tNID);
$smarty->assign('listCity', listCity($conn, $data['bZip'])); //聯絡地址-縣市
$smarty->assign('listArea', listArea($conn, $data['bZip'])); //聯絡地址-區域
$smarty->assign('listCity3', listCity($conn, $data['bZip3'])); //回饋金聯絡地址-縣市
$smarty->assign('listArea3', listArea($conn, $data['bZip3'])); //回饋金聯絡地址-區域
$smarty->assign('listCity2', listCity($conn, $data['bZip2'])); //回饋金戶籍地址-縣市
$smarty->assign('listArea2', listArea($conn, $data['bZip2'])); //回饋金戶籍地址-區域
$smarty->assign('smsEdit', '1');
$smarty->display('formbranch_print.inc.tpl', '', 'maintain');
