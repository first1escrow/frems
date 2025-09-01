<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/class/contract.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/class/member.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/tracelog.php';

//
function GetLandPriceTwo($certifiedId, $item)
{
    global $conn;

    $advance = new Advance();

    $sql = "SELECT cLandItem, cMoveDate, cLandPrice, cPower1, cPower2 FROM tContractLandPrice WHERE cCertifiedId = '" . $certifiedId . "' AND cLandItem = '" . $item . "' AND cDel = 0;";
    $rs  = $conn->Execute($sql);

    $data = [];
    while (! $rs->EOF) {
        $rs->fields['cMoveDate'] = preg_match("/0000\-00\-00/", $rs->fields['cMoveDate']) ? '' : $advance->ConvertDateToRoc($rs->fields['cMoveDate'], base::DATE_FORMAT_NUM_MONTH);
        array_push($data, $rs->fields);

        $rs->MoveNext();
    }

    return $data;
}
##

$advance = new Advance();

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '查看合約書土地詳細資料');

$id = ($_POST['id']) ? $_POST['id'] : $_GET['id'];

$contract = new Contract();

$list_categorybuild = $contract->GetCategoryBuild();
$menu_categorybuild = $contract->ConvertOption($list_categorybuild, 'cId', 'cName');
$list_categoryland  = $contract->GetCategoryLand();
$menu_categoryland  = $contract->ConvertOption($list_categoryland, 'cId', 'cName');
$menu_categoryarea  = $contract->GetCategoryAreaMenuList();

$sql = "SELECT cSignCategory FROM tContractCase WHERE cCertifiedId = '" . $id . "'";
$rs  = $conn->Execute($sql);

$cSignCategory = $rs->fields['cSignCategory'];

$data_land = $contract->GetLandList($id); //取得土地資料 (cItem > 0)

$land_price_max = 2;
$max            = count($data_land);
for ($i = 0; $i < $max; $i++) {
    if ($data_land[$i]['cItem'] == '0') {
        continue;
    }

    $data_land[$i]['land_city']  = listCity($conn, $data_land[$i]['cZip']);
    $data_land[$i]['land_area']  = listArea($conn, $data_land[$i]['cZip']);
    $data_land[$i]['land_price'] = [];

    $landprice = GetLandPriceTwo($id, $data_land[$i]['cItem']); //取得土地前次移轉現值或原規定地價

    if (! empty($landprice)) {
        $data = [
            'land_item'  => [],
            'move_date'  => [],
            'land_price' => [],
            'power1'     => [],
            'power2'     => [],
        ];

        foreach ($landprice as $v) {
            $data['land_item'][]  = $v['cLandItem'];
            $data['move_date'][]  = $v['cMoveDate'];
            $data['land_price'][] = $v['cLandPrice'];
            $data['power1'][]     = $v['cPower1'];
            $data['power2'][]     = $v['cPower2'];
        }

        $data_land[$i]['land_price'] = $data;

        $data = null;unset($data);
    }

    if (! isset($data_land[$i]['land_price']['land_item']) || ! is_array($data_land[$i]['land_price']['land_item'])) {
        $data_land[$i]['land_price']['land_item'] = [];
    }
    if (count($data_land[$i]['land_price']['land_item']) < $land_price_max) {
        for ($j = count($data_land[$i]['land_price']['land_item']); $j < $land_price_max; $j++) {
            $data_land[$i]['land_price']['land_item'][]  = '';
            $data_land[$i]['land_price']['move_date'][]  = '';
            $data_land[$i]['land_price']['land_price'][] = '';
            $data_land[$i]['land_price']['power1'][]     = '';
            $data_land[$i]['land_price']['power2'][]     = '';
        }
    }
}

$new_record_default = $contract->GetLandFirst($id, 0);
if (empty($new_record_default)) {
    $new_record_default = [
        'land_city' => listCity($conn),
        'land_area' => listArea($conn),
        'cZip'      => '',
        'cLand1'    => '',
        'cLand2'    => '',
    ];
} else {
    $new_record_default['land_city'] = listCity($conn, $new_record_default['cZip']);
    $new_record_default['land_area'] = listArea($conn, $new_record_default['cZip']);
}

$new_record_default['land_price'] = [
    'land_item'  => ['', ''],
    'move_date'  => ['', ''],
    'land_price' => ['', ''],
    'power1'     => ['', ''],
    'power2'     => ['', ''],
];

$smarty->assign('land_country', listCity($conn)); //土地縣市
$smarty->assign('data_land', $data_land);
$landPrice = []; // 初始化 $landPrice 變數
$smarty->assign('landPrice', $landPrice);
$smarty->assign('cSignCategory', $cSignCategory); //判斷合約書位置
$smarty->assign('certifyid', $id);
$smarty->assign('menu_categoryland', $menu_categoryland);
$smarty->assign('menu_categoryarea', $menu_categoryarea);
$smarty->assign('menu_categorybuild', $menu_categorybuild);
$smarty->assign('new_record_default', $new_record_default);

$smarty->display('formland2edit.inc.tpl', '', 'escrow');
