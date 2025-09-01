<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/class/contract.class.php';

$contract      = new Contract();
$brand         = '';
$status        = '';
$category      = '';
$contract_bank = '';

//取得房仲品牌列表
$query = 'SELECT bId,bName FROM tBrand ORDER BY bId ASC;';
$rs    = $conn->Execute($query);

while (!$rs->EOF) {
    $brand .= "<option value='" . $rs->fields['bId'] . "'>" . $rs->fields['bName'] . "</option>\n";
    $rs->MoveNext();
}

//取得案件狀態列表
$query = 'SELECT sId,sName FROM tStatusCase WHERE sId<>"0" ORDER BY sId ASC;';
$rs    = $conn->Execute($query);

while (!$rs->EOF) {
    $status .= "<option value='" . $rs->fields['sId'] . "'>" . $rs->fields['sName'] . "</option>\n";
    $rs->MoveNext();
}

//仲介群組
$menu_group = '';

$sql = "SELECT bId,bName FROM tBranchGroup ";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $menu_group .= '<option value="' . $rs->fields['bId'] . '">' . $rs->fields['bName'] . '</option>';
    $rs->moveNext();
}

$category = "<option value='11'>加盟(其他品牌)</option>\n";
$category .= "<option value='12'>加盟(台灣房屋)</option>\n";
$category .= "<option value='13'>加盟(優美地產)</option>\n";
$category .= "<option value='14'>加盟(永春不動產)</option>\n";
$category .= "<option value='1'>加盟</option>\n";
$category .= "<option value='2'>直營</option>\n";
$category .= "<option value='3'>非仲介成交</option>\n";
$category .= "<option value='4'>其他(未指定)</option>\n";
$category .= "<option value='5'>台屋集團</option>\n";
$category .= "<option value='6'>他牌+非仲</option>\n";

//簽約銀行
$list_categorybank = $contract->GetContractBank();
foreach ($list_categorybank as $val) {
    $contract_bank .= "<option value='" . $val['cBankCode'] . "'>" . $val['cBankFullName'] . "(" . $val['cBranchFullName'] . ")</option>\n";
}

//承辦人
$sql = 'SELECT
            b.pName as undertaker,
            b.pId as cUndertakerId
        FROM
            tContractCase AS a
        JOIN
            tPeopleInfo AS b ON b.pId=a.cUndertakerId
        WHERE
            b.pJob="1"
            AND b.pId<>"6"
            AND pDep IN("5","6")
        GROUP BY
            b.pId;';
$rs = $conn->Execute($sql);

$undertaker = '';
while (!$rs->EOF) {
    $undertaker .= "<option value='" . $rs->fields['cUndertakerId'] . "'>" . $rs->fields['undertaker'] . "</option>\n";
    $rs->MoveNext();
}

if ($_SESSION['member_test'] != 0) {
    $sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '" . $_SESSION['member_test'] . "'";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $test_tmp[] = "'" . $rs->fields['zZip'] . "'";
        $rs->MoveNext();
    }

    $z_str = " AND zZip IN(" . implode(',', $test_tmp) . ")";

    $test_tmp = null;unset($test_tmp);
} else if ($_SESSION['member_pDep'] == 7) {
    $z_str = 'AND FIND_IN_SET(' . $_SESSION['member_id'] . ',zSales)';
}

//縣市
$sql = 'SELECT zCity FROM tZipArea WHERE 1=1 ' . $z_str . '  GROUP BY zCity ORDER BY zZip,zCity ASC;';
$rs  = $conn->Execute($sql);

$citys = '<option selected="selected" value="">全部</option>' . "\n";
while (!$rs->EOF) {
    $citys .= '<option value="' . $rs->fields['zCity'] . '">' . $rs->fields['zCity'] . "</option>\n";
    $rs->MoveNext();
}

//業務區域
$zip = array();

$sql = 'SELECT zZip FROM tZipArea WHERE 1=1 ' . $z_str . '  ORDER BY zZip,zCity ASC;';
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $zip[] = "'" . $rs->fields['zZip'] . "'";
    $rs->MoveNext();
}

$tmp_z = implode(',', $zip);
$s_str = "1=1";
if ($_SESSION['member_job'] == 7) {
    $b_str = ' AND b.bZip IN (' . $tmp_z . ')';
    $s_str = ' sZip1 IN(' . $tmp_z . ')';
}
$tmp_z = null;unset($tmp_z);

//仲介商
$sql = 'SELECT
            bId,
            bName,
            bStore,
            CONCAT((SELECT bCode FROM tBrand AS a WHERE a.bId=b.bBrand ),LPAD(bId,5,"0")) as bCode,
            (SELECT bName FROM tBrand AS c WHERE c.bId=b.bBrand) bBrand,
            bStatus
        FROM
            tBranch b
        WHERE
            b.bId NOT IN (0,980)
            ' . $b_str . ';';
$rs = $conn->Execute($sql);

$branch_search = '';
while (!$rs->EOF) {
    if (preg_match("/自有品牌/", $rs->fields['bBrand'])) {
        $rs->fields['bBrand'] = '自有品牌';
    }

    if ($rs->fields['bStatus'] == 2) {
        $rs->fields['bStatus'] = "[關店]";
    } else if ($rs->fields['bStatus'] == 3) {
        $rs->fields['bStatus'] = "[暫停]";
    } else {
        $rs->fields['bStatus'] = '';
    }

    $branch_search .= "<option value='" . $rs->fields['bId'] . "'>" . $rs->fields['bCode'] . $rs->fields['bBrand'] . $rs->fields['bStore'] . $rs->fields['bStatus'] . "</option>\n";

    $rs->MoveNext();
}

//地政士
$sql = 'SELECT
            sId,
            sName,
            CONCAT("SC",LPAD(sId,4,"0")) as Code
        FROM
            tScrivener
        WHERE
        ' . $s_str . '
        GROUP BY
            sId
        ASC;';
$rs = $conn->Execute($sql);

$scrivener_search = '';
while (!$rs->EOF) {
    $scrivener_search .= "<option value='" . $rs->fields['sId'] . "'>" . $rs->fields['Code'] . $rs->fields['sName'] . "</option>\n";
    $rs->MoveNext();
}

$sql = "SELECT pName,pId FROM tPeopleInfo WHERE pDep IN(7,4) AND pJob = 1";
$rs  = $conn->Execute($sql);

$menuSalse = '';
while (!$rs->EOF) {
    $menuSalse .= "<option value='" . $rs->fields['pId'] . "'>" . $rs->fields['pName'] . "</option>\n";
    $rs->MoveNext();
}

$sql = "SELECT bId,bName FROM tBrand WHERE bContract = 1";
$rs  = $conn->Execute($sql);

$menu_brand = '';
while (!$rs->EOF) {
    if ($rs->fields['bId'] != 2) {
        $menu_brand[$rs->fields['bId']] = $rs->fields['bName'];
    }

    $rs->MoveNext();
}

//報表類型選項
$menu_report    = array();
$menu_report[0] = '預設';
if (in_array($_SESSION['member_id'], [1, 6, 12])) { //20220713
    $menu_report[1] = '品牌報表';
    $menu_report[3] = '群義品牌報表';
}
$menu_report[2] = '統計表';

$smarty->assign('menu_group', $menu_group);
$smarty->assign('menu_report', $menu_report);
$smarty->assign('menuSalse', $menuSalse);
$smarty->assign('brand', $brand);
$smarty->assign('status', $status);
$smarty->assign('category', $category);
$smarty->assign('contract_bank', $contract_bank);
$smarty->assign('branch_search', $branch_search);
$smarty->assign('scrivener_search', $scrivener_search);
$smarty->assign('undertaker', $undertaker);
$smarty->assign('menu_brand', $menu_brand);
$smarty->assign('citys', $citys);
$smarty->display('applycase.inc.tpl', '', 'report');
