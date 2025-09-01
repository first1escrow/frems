<?php
require_once __DIR__ . '/ku.class.php';

use First1\V1\KU\Ku;

$ku = new Ku;

$dir = __DIR__ . '/csv';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

// $cId = '120236595'; //多筆地號
// $cId = '100132562'; //多筆地號
// $cId = '120035720'; //多筆地號

// $cId = '120088986'; //單筆地號
// $cId = '100859221'; //單筆地號

// $cId = '120043411'; //地號多前次

// $cId = '120150572';
// $cId = '110207551';
// $cId = '111031179';

// $cId = '110071981';
// $cId = '110139629';

// $cId = '120640611'; //有買方登記人
// $cId = '120242580'; //有買方登記人
// $cId = '111736717'; //有買方登記人
// $cId = '090430041'; //有買方登記人

$cId = '120021464';
/**
 * 義務人
 */
$owners = $ku->getOwners($cId);
// print_r($owners);exit;

$fh = $dir . '/sidno.csv';

$csv = "\xEF\xBB\xBF";
file_put_contents($fh, $csv);

$csv = 'no編號,noname,no身分,noidno,no地持分,no建持分,no戶籍,no通訊處,nobirth,notel,notelh,notel2,c公私有,c人或法人';
file_put_contents($fh, $csv . "\n", FILE_APPEND);

$index = 1;
foreach ($owners as $k => $v) {
    $identity = '出賣人';
    $type     = '私有';
    $class    = preg_match("/^[0-9]{8}$/", $v['identifyId']) ? '法人' : '自然人';
    $birthday = ($v['birthday'] == '0000-00-00') ? '' : $v['birthday'];
    if (!empty($birthday)) {
        $tmp      = explode('-', $birthday);
        $birthday = str_pad(($tmp[0] - 1911), 4, '0', STR_PAD_LEFT) . '/' . $tmp[1] . '/' . $tmp[2];
        $tmp      = null;unset($tmp);
    }

    //
    if (empty($v['item'])) {
        $csv = $index . ',' . $v['name'] . ',' . $identity . ',' . $v['identifyId'] . ',,';
        $csv .= ',' . $v['registCity'] . $v['registDistrict'] . $v['registAddr'];
        $csv .= ',' . $v['baseCity'] . $v['baseDistrict'] . $v['baseAddr'];
        $csv .= ',' . $birthday . ',' . $v['tel1'] . ',' . $v['tel2'] . ',' . $v['mobile'] . ',' . $type . ',' . $class;

        file_put_contents($fh, $csv . "\n", FILE_APPEND);

        $index++;
        continue;
    }

    foreach ($v['item'] as $va) {
        $max = (count($va['land']) > count($va['building'])) ? count($va['land']) : count($va['building']);

        for ($i = 0; $i < $max; $i++) {
            $land = empty($va['land'][$i]) ? '' : $va['land'][$i];
            if (!empty($land)) {
                $land = (empty($land['cTranferPower1']) || empty($land['cTranferPower2'])) ? '' : $land['cTranferPower1'] . '/' . $land['cTranferPower2'];
            }

            $building = empty($va['building'][$i]) ? '' : $va['building'][$i];
            if (!empty($building)) {
                $building = (empty($building['cTranferPower1']) || empty($building['cTranferPower2'])) ? '' : $building['cTranferPower1'] . '/' . $building['cTranferPower2'];
            }

            $csv = $index . ',' . $v['name'] . ',' . $identity . ',' . $v['identifyId'] . ',' . $land . ',' . $building;
            $csv .= ',' . $v['registCity'] . $v['registDistrict'] . $v['registAddr'];
            $csv .= ',' . $v['baseCity'] . $v['baseDistrict'] . $v['baseAddr'];
            $csv .= ',' . $birthday . ',' . $v['tel1'] . ',' . $v['tel2'] . ',' . $v['mobile'] . ',' . $type . ',' . $class;

            file_put_contents($fh, $csv . "\n", FILE_APPEND);

            $land = $building = null;
            unset($land, $building);

            $index++;
        }

        continue;
    }

    $csv = null;
}
// exit;

/**
 * 權利人
 */
$buyers = $ku->getBuyers($cId);
// print_r($buyers);exit;

$fh = $dir . '/bidno.csv';

$csv = "\xEF\xBB\xBF";
file_put_contents($fh, $csv);

$csv = 'no編號,noname,no身分,noidno,no地持分,no建持分,no戶籍,no通訊處,nobirth,notel,notelh,notel2,c公私有,c人或法人';
file_put_contents($fh, $csv . "\n", FILE_APPEND);

$index = 1;
foreach ($buyers as $k => $v) {
    $identity = '買受人';
    $type     = '私有';
    $class    = preg_match("/^[0-9]{8}$/", $v['identifyId']) ? '法人' : '自然人';
    $birthday = ($v['birthday'] == '0000-00-00') ? '' : $v['birthday'];
    if (!empty($birthday)) {
        $tmp      = explode('-', $birthday);
        $birthday = str_pad(($tmp[0] - 1911), 4, '0', STR_PAD_LEFT) . '/' . $tmp[1] . '/' . $tmp[2];
        $tmp      = null;unset($tmp);
    }

    //
    if (empty($v['item'])) {
        $csv = $index . ',' . $v['name'] . ',' . $identity . ',' . $v['identifyId'] . ',,';
        $csv .= ',' . $v['registCity'] . $v['registDistrict'] . $v['registAddr'];
        $csv .= ',' . $v['baseCity'] . $v['baseDistrict'] . $v['baseAddr'];
        $csv .= ',' . $birthday . ',' . $v['tel1'] . ',' . $v['tel2'] . ',' . $v['mobile'] . ',' . $type . ',' . $class;

        file_put_contents($fh, $csv . "\n", FILE_APPEND);

        $index++;
        continue;
    }

    foreach ($v['item'] as $va) {
        $max = (count($va['land']) > count($va['building'])) ? count($va['land']) : count($va['building']);

        for ($i = 0; $i < $max; $i++) {
            $land = empty($va['land'][$i]) ? '' : $va['land'][$i];
            if (!empty($land)) {
                $land = (empty($land['cTranferPower1']) || empty($land['cTranferPower2'])) ? '' : $land['cTranferPower1'] . '/' . $land['cTranferPower2'];
            }

            $building = empty($va['building'][$i]) ? '' : $va['building'][$i];
            if (!empty($building)) {
                $building = (empty($building['cTranferPower1']) || empty($building['cTranferPower2'])) ? '' : $building['cTranferPower1'] . '/' . $building['cTranferPower2'];
            }

            $csv = $index . ',' . $v['name'] . ',' . $identity . ',' . $v['identifyId'] . ',' . $land . ',' . $building;
            $csv .= ',' . $v['registCity'] . $v['registDistrict'] . $v['registAddr'];
            $csv .= ',' . $v['baseCity'] . $v['baseDistrict'] . $v['baseAddr'];
            $csv .= ',' . $birthday . ',' . $v['tel1'] . ',' . $v['tel2'] . ',' . $v['mobile'] . ',' . $type . ',' . $class;

            file_put_contents($fh, $csv . "\n", FILE_APPEND);

            $land = $building = null;
            unset($land, $building);

            $index++;
        }

        continue;
    }

    $csv = null;
}
// exit;

/**
 * 土地
 */
$lands = $ku->getLands($cId);
// print_r($lands);exit;

$fh = $dir . '/kutp1.csv';

$csv = "\xEF\xBB\xBF";
file_put_contents($fh, $csv);

$csv = 'n次序,c變前後,c所有人,p縣市,p鄉鎮區,p地段,p小段,p地號,p面積';
$csv .= ',p權利範圍2,p前移轉日,p前移轉價';
$csv .= ',p現值,p實價,p本筆金額,p指數,p按現實';
file_put_contents($fh, $csv . "\n", FILE_APPEND);

$one = array_column($lands, 'one');
$one = in_array(false, $one) ? '多筆' : '單筆';

$multi_item = [];
foreach ($lands as $k => $v) {
    //
    $_land_price = $v['cMoney']; //p現值
    $real_price  = ''; //p實價
    $charge      = 1; //p按現實(按實價申報)

    if (!empty($v['extra'])) {
        $extra_land_price       = array_column($v['extra'], 'cLandPrice');
        $extra_land_price_count = array_filter($extra_land_price, function ($item) use ($_land_price) {
            if ($item > $_land_price) { //前次大於現值
                return $item;
            }
        });

        if (count($extra_land_price) == count($extra_land_price_count)) { //所有前次大於現值的數量等於所有前次的數量，代表所有前次金額都大於現值
            $real_price = max($extra_land_price); //最大的前次金額
            $charge     = 2; //按現值申報
        }
    }
    ##

    //轉移資訊
    if ($one == '多筆') { //多筆轉移資料
        $transfer_data = ',,';
        $multi_item[]  = $v;
    } else { //單筆轉移資料
        $power    = (empty($v['extra'][0]['cPower1']) || empty($v['extra'][0]['cPower2'])) ? '' : $v['extra'][0]['cPower1'] . '/' . $v['extra'][0]['cPower2'];
        $movedate = empty($v['extra'][0]['cMoveDate']) ? [] : explode('-', $v['extra'][0]['cMoveDate']);
        $movedate = empty($movedate) ? '' : str_pad(($movedate[0] - 1911), 4, '0', STR_PAD_LEFT) . '/' . str_pad($movedate[1], 2, '0', STR_PAD_LEFT) . '/' . str_pad($movedate[2], 2, '0', STR_PAD_LEFT);

        $transfer_data = $power . ',' . $movedate . ',';
        $transfer_data .= empty($v['extra'][0]['cLandPrice']) ? '' : $v['extra'][0]['cLandPrice'];

        $power = $movedate = null;
        unset($power, $movedate);
    }
    ##

    $before_after = ''; //變前後
    $v['name']    = ''; //c所有人

    $cpi = ''; //p指數

    if (!empty($v['extra'])) {
        $extra_move_date = max(array_column($v['extra'], 'cMoveDate'));
        $cpi             = $ku->getCPI($extra_move_date);
    }

    $csv = ($k + 1) . ',' . $before_after . ',' . $v['name'] . ',' . $v['city'] . ',' . $v['district'] . ',' . $v['cLand1'] . ',' . $v['cLand2'] . ',' . $v['cLand3'] . ',' . $v['cMeasure'];
    $csv .= ',' . $transfer_data;
    $csv .= ',' . $_land_price . ',' . $real_price . ',' . $v['totalMoney'] . ',' . $cpi . ',' . $charge;

    file_put_contents($fh, $csv . "\n", FILE_APPEND);

    $csv = $transfer_data = $before_after = $real_price = $charge = $_land_price = null;
    unset($csv, $transfer_data, $before_after, $real_price, $charge, $_land_price);
}

if (!empty($multi_item)) {
    $fh = $dir . '/前次.csv';

    $csv = "\xEF\xBB\xBF";
    file_put_contents($fh, $csv);

    $csv = 'p地段,p權利範圍2,p前移轉日,p前移轉價,n次,noidno,noname';
    file_put_contents($fh, $csv . "\n", FILE_APPEND);

    foreach ($multi_item as $v) {
        $_land_no = $v['cLand3'];

        $_order    = 0;
        $_order_id = '';
        foreach ($v['extra'] as $va) {
            $power    = (empty($va['cPower1']) || empty($va['cPower2'])) ? '' : $va['cPower1'] . '/' . $va['cPower2'];
            $movedate = empty($va['cMoveDate']) ? [] : explode('-', $va['cMoveDate']);
            // $movedate = empty($movedate) ? '' : str_pad(($movedate[0] - 1911), 4, '0', STR_PAD_LEFT) . '/' . str_pad($movedate[1], 2, '0', STR_PAD_LEFT) . '/' . str_pad($movedate[2], 2, '0', STR_PAD_LEFT);
            $movedate = empty($movedate) ? '' : str_pad(($movedate[0] - 1911), 4, '0', STR_PAD_LEFT) . '/' . str_pad($movedate[1], 2, '0', STR_PAD_LEFT) . '/01';

            $_order++;

            if ($_order_id != $va['cIdentifyId']) {
                $_order_id = $va['cIdentifyId'];
                $_order    = 1;
            }

            $owner = convertName($owners, $va['cIdentifyId']);

            $csv = $_land_no . ',' . $power . ',' . $movedate . ',' . $va['cLandPrice'] . ',' . $_order . ',' . $va['cIdentifyId'] . ',' . $owner;
            file_put_contents($fh, $csv . "\n", FILE_APPEND);

            $power = $csv = $owner = null;
            unset($power, $csv, $owner);
        }
        $_order = null;unset($_order);
    }
}
// exit;

/**
 * 建物
 */
$buildings = $ku->getBuildings($cId);
// print_r($buildings);

$fh = $dir . '/kutpa.csv';

$csv = "\xEF\xBB\xBF";
file_put_contents($fh, $csv);

$csv = 'n次序,c變前後,c棟戶別,c所有人,a移轉範圍,a建號,a房屋門牌,';
$csv .= 'a縣市,a鄉鎮區,a地段,a小段,a地號1,a地號2,a地號3,a地號4,';
$csv .= 'a總層數,a層1,a層2,a層3,a層4,a層5,a層6,a層7,a層8,a主用途,a完成日,';
$csv .= 'a構造1,a面積1,a面積2,a面積3,a面積4,a面積5,a面積6,a面積7,a面積8,a共計,';
$csv .= 'a附用途1,a附用途2,a附用途3,a附用途4,a附用途5,a附面積1,a附面積2,a附面積3,a附面積4,a附面積5,';
$csv .= 'a共建1,a共建2,a共建3,a共建4,a共建5,a共建面1,a共建面2,a共建面3,a共建面4,a共建面5,';
$csv .= 'a共建分1,a共建分2,a共建分3,a共建分4,a共建分5,';
$csv .= 'a車位1,a車位2,a車位3,a車位分1,a車位分2,a車位分3,';
$csv .= 'a稅籍號,a移轉金額,a報稅金額';
file_put_contents($fh, $csv . "\n", FILE_APPEND);

foreach ($buildings as $k => $v) {
    $before_after = '';
    $power        = (empty($v['cPower1']) || empty($v['cPower2'])) ? '' : $v['cPower1'] . '/' . $v['cPower2'];
    $address      = $v['city'] . $v['district'] . $v['cAddr'];

    $csv = ($k + 1) . ',' . $before_after . ',,,' . $power . ',' . $v['cBuildNo'] . ',' . $address . ',';
    $csv .= $v['city'] . ',' . $v['district'] . ',';

    if (empty($v['extra'])) {
        $csv .= ',,,,,,';
    } else {
        $csv .= $v['extra'][0]['cBuildingSession'] . ',' . $v['extra'][0]['cBuildingSessionExt'] . ',';

        for ($i = 0; $i < 4; $i++) {
            $csv .= empty($v['extra'][$i]['cBuildingLandNo']) ? '' : $v['extra'][$i]['cBuildingLandNo'];
            $csv .= ',';
        }
    }
    $before_after = $power = $address = null;
    unset($before_after, $power, $address);

    //總層數
    $csv .= $v['cLevelHighter'] . ',';

    //樓層
    $level      = $v['level'];
    $level_data = [];

    for ($i = 0; $i < 8; $i++) {
        $_level = array_shift($level);
        if (!empty($_level)) {
            $level_data[] = $_level;
            $csv .= empty($_level['cLevelUse']) ? '' : $_level['cLevelUse'];
        }
        $csv .= ',';

        $_level = null;unset($_level);
    }

    //主用途
    $obj_use_map = $ku->getObjectUse();

    if (empty($v['cObjUse'])) {
        if (!empty($v['cIsOther']) && !empty($v['cOther'])) {
            $csv .= $v['cOther'];
        }
    } else {
        $obj_use = explode(',', $v['cObjUse']);
        $obj_use = array_map(function ($item) use ($obj_use_map) {
            return $obj_use_map[$item];
        }, $obj_use);
        $csv .= implode(' ', $obj_use);
    }
    $csv .= ',';

    $obj_use_map = $obj_use = null;
    unset($obj_use_map, $obj_use);

    //完成日
    if (preg_match("/^\d{4}\-\d{2}\-\d{2}/", $v['cBuildDate'])) {
        $csv .= str_replace('-', '/', substr($v['cBuildDate'], 0, 10));
    }
    $csv .= ',';

    //構造1
    $csv .= $v['material'] . ',';

    //面積
    $cMeasureMain = 0;
    for ($i = 0; $i < 8; $i++) {
        if (!empty($level_data[$i])) {
            $csv .= empty($level_data[$i]['cMeasureMain']) ? '' : $level_data[$i]['cMeasureMain'];
        }
        $csv .= ',';

        $cMeasureMain += empty($level_data[$i]['cMeasureMain']) ? 0 : $level_data[$i]['cMeasureMain'];
    }
    $csv .= $cMeasureMain . ',';

    $cMeasureMain = null;unset($cMeasureMain);

    //附用途
    $sub      = $v['sub'];
    $sub_data = [];

    for ($i = 0; $i < 5; $i++) {
        $_sub = array_shift($sub);
        if (!empty($_sub)) {
            $sub_data[] = $_sub;
            $csv .= empty($_sub['cLevelUse']) ? '' : $_sub['cLevelUse'];
        }
        $csv .= ',';

        $_sub = null;unset($_sub);
    }

    //附面積
    for ($i = 0; $i < 5; $i++) {
        if (!empty($sub_data[$i])) {
            $csv .= empty($sub_data[$i]['cMeasureMain']) ? '' : $sub_data[$i]['cMeasureMain'];
        }
        $csv .= ',';
    }
    $sub = $sub_data = null;
    unset($sub, $sub_data);

    //共建
    $share      = $v['share'];
    $share_data = [];

    for ($i = 0; $i < 5; $i++) {
        $_share = array_shift($share);
        if (!empty($_share)) {
            $share_data[] = $_share;
            $csv .= empty($_share['cLevelUse']) ? '' : $_share['cLevelUse'];
        }
        $csv .= ',';

        $_share = null;unset($_share);
    }

    //共建面
    for ($i = 0; $i < 5; $i++) {
        if (!empty($share_data[$i])) {
            $csv .= empty($share_data[$i]['cMeasureMain']) ? '' : $share_data[$i]['cMeasureMain'];
        }
        $csv .= ',';
    }

    //共建分
    for ($i = 0; $i < 5; $i++) {
        if (!empty($share_data[$i])) {
            $_power = (empty($share_data[$i]['cPower1']) || empty($share_data[$i]['cPower2'])) ? '' : $share_data[$i]['cPower1'] . '/' . $share_data[$i]['cPower2'];
            $csv .= $_power;

            $_power = null;unset($_power);
        }
        $csv .= ',';

    }
    $share = $share_data = null;
    unset($share, $share_data);

    //車位
    $parking = $v['parking'];
    for ($i = 0; $i < 3; $i++) {
        $_parking = array_shift($parking);
        $csv .= empty($_parking['cNo']) ? '' : $_parking['cNo'];
        $csv .= ',';

        $_parking = null;unset($_parking);
    }
    $parking = null;unset($parking);

    //車位分
    $csv .= ',,,';

    //稅籍號
    $csv .= ',';

    //移轉金額
    $csv .= ',';

    //報稅金額
    $csv .= ',';

    file_put_contents($fh, $csv . "\n", FILE_APPEND);

    $csv = $transfer_data = $before_after = null;
    unset($csv, $transfer_data, $before_after);
}

function convertName($owners, $cIdentifyId)
{
    if (!empty($owners)) {
        foreach ($owners as $owner) {
            if ($owner['identifyId'] == $cIdentifyId) {
                return $owner['name'];
            }
        }
    }

    return false;
}
