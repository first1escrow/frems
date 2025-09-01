<?php
/**
 * 顧代書系統串接程式
 */
require_once __DIR__ . '/ku.class.php';

use First1\V1\KU\Ku;

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

$max_row = 0;

/**
 * 義務人
 */
$owners  = $ku->getOwners($cId);
$max_row = (count($owners) > $max_row) ? count($owners) : $max_row;

/**
 * 權利人
 */
$buyers  = $ku->getBuyers($cId);
$max_row = (count($buyers) > $max_row) ? count($buyers) : $max_row;

/**
 * 土地
 */
$lands   = $ku->getLands($cId);
$max_row = (count($lands) > $max_row) ? count($lands) : $max_row;

$lands_before = [];
if (!empty($lands)) {
    foreach ($lands as $v) {
        if (!empty($v['extra'])) {
            foreach ($v['extra'] as $va) {
                $lands_before[] = $va;
            }
        }
    }
}
$max_row = (count($lands_before) > $max_row) ? count($lands_before) : $max_row;

/**
 * 建物
 */
$buildings = $ku->getBuildings($cId);
$max_row   = (count($buildings) > $max_row) ? count($buildings) : $max_row;

// print_r($lands);
// print_r($lands_before);
// exit;

$fh = $dir . '/kumain_' . time() . '.csv';

$csv = "\xEF\xBB\xBF";
file_put_contents($fh, $csv);

$csv = 'bidno.no編號,bidno.noname,bidno.no身分,bidno.noidno,bidno.no地持分,bidno.no建持分,bidno.no戶籍,bidno.no通訊處,bidno.nobirth,bidno.notel,bidno.notelh,bidno.notel2,bidno.c公私有,bidno.c人或法人,';
$csv .= 'sidno.no編號,sidno.noname,sidno.no身分,sidno.noidno,sidno.no地持分,sidno.no建持分,sidno.no戶籍,sidno.no通訊處,sidno.nobirth,sidno.notel,sidno.notelh,sidno.notel2,sidno.c公私有,sidno.c人或法人,';
$csv .= 'kutp1.n次序,kutp1.c變前後,kutp1.c所有人,kutp1.p縣市,kutp1.p鄉鎮區,kutp1.p地段,kutp1.p小段,kutp1.p地號,kutp1.p面積,kutp1.p權利範圍2,kutp1.p前移轉日,kutp1.p前移轉價,kutp1.p現值,kutp1.p實價,kutp1.p本筆金額,kutp1.p指數,kutp1.p按現實,';
$csv .= 'kutpa.n次序,kutpa.c變前後,kutpa.c棟戶別,kutpa.c所有人,kutpa.a移轉範圍,kutpa.a建號,kutpa.a房屋門牌,kutpa.a縣市,kutpa.a鄉鎮區,kutpa.a地段,kutpa.a小段,kutpa.a地號1,kutpa.a地號2,kutpa.a地號3,kutpa.a地號4,kutpa.a總層數,kutpa.a層1,kutpa.a層2,kutpa.a層3,kutpa.a層4,kutpa.a層5,kutpa.a層6,kutpa.a層7,kutpa.a層8,kutpa.a主用途,kutpa.a完成日,kutpa.a構造1,kutpa.a面積1,kutpa.a面積2,kutpa.a面積3,kutpa.a面積4,kutpa.a面積5,kutpa.a面積6,kutpa.a面積7,kutpa.a面積8,kutpa.a共計,kutpa.a附用途1,kutpa.a附用途2,kutpa.a附用途3,kutpa.a附用途4,kutpa.a附用途5,kutpa.a附面積1,kutpa.a附面積2,kutpa.a附面積3,kutpa.a附面積4,kutpa.a附面積5,kutpa.a共建1,kutpa.a共建2,kutpa.a共建3,kutpa.a共建4,kutpa.a共建5,kutpa.a共建面1,kutpa.a共建面2,kutpa.a共建面3,kutpa.a共建面4,kutpa.a共建面5,kutpa.a共建分1,kutpa.a共建分2,kutpa.a共建分3,kutpa.a共建分4,kutpa.a共建分5,kutpa.a車位1,kutpa.a車位2,kutpa.a車位3,kutpa.a車位分1,kutpa.a車位分2,kutpa.a車位分3,kutpa.a稅籍號,kutpa.a移轉金額,kutpa.a報稅金額,';
$csv .= '前次.p地號,前次.p權利範圍2,前次.p前移轉日,前次.p前移轉價,前次.n次,前次.noidno,前次.noname';

file_put_contents($fh, $csv . "\n", FILE_APPEND);

$data = [];
for ($i = 0; $i < $max_row; $i++) {
    $data[$i] = array_fill(0, 121, null);

    //買方
    if (!empty($buyers[$i])) {
        $v = $buyers[$i];

        $birthday = ($v['birthday'] == '0000-00-00') ? '' : $v['birthday'];
        if (!empty($birthday)) {
            $tmp      = explode('-', $birthday);
            $birthday = str_pad(($tmp[0] - 1911), 4, '0', STR_PAD_LEFT) . '/' . $tmp[1] . '/' . $tmp[2];
            $tmp      = null;unset($tmp);
        }

        $data[$i][0]  = ($i + 1); //bidno.no編號
        $data[$i][1]  = $v['name']; //bidno.noname
        $data[$i][2]  = '買受人'; //bidno.no身分
        $data[$i][3]  = $v['identifyId']; //bidno.noidno
        $data[$i][4]  = ''; //bidno.no地持分 ?
        $data[$i][5]  = ''; //bidno.no建持分 ?
        $data[$i][6]  = $v['registCity'] . $v['registDistrict'] . $v['registAddr']; //bidno.no戶籍
        $data[$i][7]  = $v['baseCity'] . $v['baseDistrict'] . $v['baseAddr']; //bidno.no通訊處
        $data[$i][8]  = $birthday; //bidno.nobirth
        $data[$i][9]  = $v['tel1']; //bidno.notel
        $data[$i][10] = $v['tel2']; //bidno.notelh
        $data[$i][11] = $v['mobile']; //bidno.notel2
        $data[$i][12] = '私有'; //bidno.c公私有
        $data[$i][13] = preg_match("/^[0-9]{8}$/", $v['identifyId']) ? '法人' : '自然人'; //bidno.c人或法人
    }

    //賣方
    if (!empty($owners[$i])) {
        $v = $owners[$i];

        $birthday = ($v['birthday'] == '0000-00-00') ? '' : $v['birthday'];
        if (!empty($birthday)) {
            $tmp      = explode('-', $birthday);
            $birthday = str_pad(($tmp[0] - 1911), 4, '0', STR_PAD_LEFT) . '/' . $tmp[1] . '/' . $tmp[2];
            $tmp      = null;unset($tmp);
        }

        $data[$i][14] = ($i + 1); //sidno.no編號
        $data[$i][15] = $v['name']; //sidno.noname
        $data[$i][16] = '出賣人'; //sidno.no身分
        $data[$i][17] = $v['identifyId']; //sidno.noidno
        $data[$i][18] = ''; //sidno.no地持分
        $data[$i][19] = ''; //sidno.no建持分
        $data[$i][20] = $v['registCity'] . $v['registDistrict'] . $v['registAddr']; //sidno.no戶籍
        $data[$i][21] = $v['baseCity'] . $v['baseDistrict'] . $v['baseAddr']; //sidno.no通訊處
        $data[$i][22] = $birthday; //sidno.nobirth
        $data[$i][23] = $v['tel1']; //sidno.notel
        $data[$i][24] = $v['tel2']; //sidno.notelh
        $data[$i][25] = $v['mobile']; //sidno.notel2
        $data[$i][26] = '私有'; //sidno.c公私有
        $data[$i][27] = preg_match("/^[0-9]{8}$/", $v['identifyId']) ? '法人' : '自然人'; //sidno.c人或法人
    }

    //土地
    if (!empty($lands[$i])) {
        $v = $lands[$i];

        $data[$i][28] = ($i + 1); //kutp1.n次序
        $data[$i][29] = ''; //kutp1.c變前後 ?
        $data[$i][30] = ''; //kutp1.c所有人
        $data[$i][31] = $v['city']; //kutp1.p縣市
        $data[$i][32] = $v['district']; //kutp1.p鄉鎮區
        $data[$i][33] = $v['cLand1']; //kutp1.p地段
        $data[$i][34] = $v['cLand2']; //kutp1.p小段
        $data[$i][35] = $v['cLand3']; //kutp1.p地號
        $data[$i][36] = $v['cMeasure']; //kutp1.p面積
        $data[$i][37] = ''; //kutp1.p權利範圍2 ?
        $data[$i][38] = ''; //kutp1.p前移轉日 ?
        $data[$i][39] = $v['cMoney']; //kutp1.p前移轉價
        $data[$i][40] = ''; //kutp1.p現值 ?
        $data[$i][41] = ''; //kutp1.p實價 ?
        $data[$i][42] = $v['totalMoney'];
        $data[$i][43] = ''; //kutp1.p指數 cpi ?
        $data[$i][44] = 1; //kutp1.p按現實 ?
    }

    //建物
    if (!empty($buildings[$i])) {
        $v = $buildings[$i];

        $power   = (empty($v['cPower1']) || empty($v['cPower2'])) ? '' : $v['cPower1'] . '/' . $v['cPower2'];
        $address = $v['city'] . $v['district'] . $v['cAddr'];

        $data[$i][45] = ($i + 1); //kutpa.n次序
        $data[$i][46] = ''; //kutpa.c變前後
        $data[$i][47] = ''; //kutpa.c棟戶別
        $data[$i][48] = ''; //kutpa.c所有人
        $data[$i][49] = $power; //kutpa.a移轉範圍
        $data[$i][50] = $v['cBuildNo']; //kutpa.a建號
        $data[$i][51] = $address; //kutpa.a房屋門牌
        $data[$i][52] = $v['city']; //kutpa.a縣市
        $data[$i][53] = $v['district']; //kutpa.a鄉鎮區

        $power = $address = null;
        unset($power, $address);

        $data[$i][54] = $v['extra'][0]['cBuildingSession']; //kutpa.a地段
        $data[$i][55] = $v['extra'][0]['cBuildingSessionExt']; //kutpa.a小段
        $data[$i][56] = $v['extra'][0]['cBuildingLandNo']; //kutpa.a地號1
        $data[$i][57] = $v['extra'][1]['cBuildingLandNo']; //kutpa.a地號2
        $data[$i][58] = $v['extra'][2]['cBuildingLandNo']; //kutpa.a地號3
        $data[$i][59] = $v['extra'][3]['cBuildingLandNo']; //kutpa.a地號4

        $data[$i][60] = $v['cLevelHighter']; //kutpa.a總層數

        $data[$i][61] = $v['level'][0]['cLevelUse']; //kutpa.a層1
        $data[$i][62] = $v['level'][1]['cLevelUse']; //kutpa.a層2
        $data[$i][63] = $v['level'][2]['cLevelUse']; //kutpa.a層3
        $data[$i][64] = $v['level'][3]['cLevelUse']; //kutpa.a層4
        $data[$i][65] = $v['level'][4]['cLevelUse']; //kutpa.a層5
        $data[$i][66] = $v['level'][5]['cLevelUse']; //kutpa.a層6
        $data[$i][67] = $v['level'][6]['cLevelUse']; //kutpa.a層7
        $data[$i][68] = $v['level'][7]['cLevelUse']; //kutpa.a層8

        $obj_use_map = $ku->getObjectUse();
        $obj_use     = '';
        if (empty($v['cObjUse'])) {
            if (!empty($v['cIsOther']) && !empty($v['cOther'])) {
                $obj_use = $v['cOther'];
            }
        } else {
            $obj_use = explode(',', $v['cObjUse']);
            $obj_use = array_map(function ($item) use ($obj_use_map) {
                return $obj_use_map[$item];
            }, $obj_use);
            $obj_use = implode(' ', $obj_use);
        }

        $data[$i][69] = $obj_use; //kutpa.a主用途

        $obj_use = $obj_use_map = null;
        unset($obj_use, $obj_use_map);

        if (preg_match("/^\d{4}\-\d{2}\-\d{2}/", $v['cBuildDate'])) {
            $v['cBuildDate'] = str_replace('-', '/', substr($v['cBuildDate'], 0, 10));
            $tmp             = explode('/', $v['cBuildDate']);
            $tmp[0] -= 1911;
            $v['cBuildDate'] = implode('/', $tmp);

            $tmp = null;unset($tmp);
        } else {
            $v['cBuildDate'] = '';
        }
        $data[$i][70] = $v['cBuildDate']; //kutpa.a完成日

        $data[$i][71] = $v['material']; //kutpa.a構造1

        $total = 0;

        $data[$i][72] = $v['level'][0]['cMeasureMain']; //kutpa.a面積1
        $total += empty($v['level'][0]['cMeasureMain']) ? 0 : $v['level'][0]['cMeasureMain'];

        $data[$i][73] = $v['level'][1]['cMeasureMain']; //kutpa.a面積2
        $total += empty($v['level'][1]['cMeasureMain']) ? 0 : $v['level'][0]['cMeasureMain'];

        $data[$i][74] = $v['level'][2]['cMeasureMain']; //kutpa.a面積3
        $total += empty($v['level'][2]['cMeasureMain']) ? 0 : $v['level'][0]['cMeasureMain'];

        $data[$i][75] = $v['level'][3]['cMeasureMain']; //kutpa.a面積4
        $total += empty($v['level'][3]['cMeasureMain']) ? 0 : $v['level'][0]['cMeasureMain'];

        $data[$i][76] = $v['level'][4]['cMeasureMain']; //kutpa.a面積5
        $total += empty($v['level'][4]['cMeasureMain']) ? 0 : $v['level'][0]['cMeasureMain'];

        $data[$i][77] = $v['level'][5]['cMeasureMain']; //kutpa.a面積6
        $total += empty($v['level'][5]['cMeasureMain']) ? 0 : $v['level'][0]['cMeasureMain'];

        $data[$i][78] = $v['level'][6]['cMeasureMain']; //kutpa.a面積7
        $total += empty($v['level'][6]['cMeasureMain']) ? 0 : $v['level'][0]['cMeasureMain'];

        $data[$i][79] = $v['level'][7]['cMeasureMain']; //kutpa.a面積8
        $total += empty($v['level'][7]['cMeasureMain']) ? 0 : $v['level'][0]['cMeasureMain'];

        $data[$i][80] = $total; //kutpa.a共計

        $total = null;unset($total);

        $data[$i][81]  = $v['sub'][0]['cLevelUse']; //kutpa.a附用途1
        $data[$i][82]  = $v['sub'][1]['cLevelUse']; //kutpa.a附用途2
        $data[$i][83]  = $v['sub'][2]['cLevelUse']; //kutpa.a附用途3
        $data[$i][84]  = $v['sub'][3]['cLevelUse']; //kutpa.a附用途4
        $data[$i][85]  = $v['sub'][4]['cLevelUse']; //kutpa.a附用途5
        $data[$i][86]  = $v['sub'][0]['cMeasureMain']; //kutpa.a附面積1
        $data[$i][87]  = $v['sub'][1]['cMeasureMain']; //kutpa.a附面積2
        $data[$i][88]  = $v['sub'][2]['cMeasureMain']; //kutpa.a附面積3
        $data[$i][89]  = $v['sub'][3]['cMeasureMain']; //kutpa.a附面積4
        $data[$i][90]  = $v['sub'][4]['cMeasureMain']; //kutpa.a附面積5
        $data[$i][91]  = $v['share'][0]['cLevelUse']; //kutpa.a共建1
        $data[$i][92]  = $v['share'][1]['cLevelUse']; //kutpa.a共建2
        $data[$i][93]  = $v['share'][2]['cLevelUse']; //kutpa.a共建3
        $data[$i][94]  = $v['share'][3]['cLevelUse']; //kutpa.a共建4
        $data[$i][95]  = $v['share'][4]['cLevelUse']; //kutpa.a共建5
        $data[$i][96]  = $v['share'][0]['cMeasureMain']; //kutpa.a共建面1
        $data[$i][97]  = $v['share'][1]['cMeasureMain']; //kutpa.a共建面2
        $data[$i][98]  = $v['share'][2]['cMeasureMain']; //kutpa.a共建面3
        $data[$i][99]  = $v['share'][3]['cMeasureMain']; //kutpa.a共建面4
        $data[$i][100] = $v['share'][4]['cMeasureMain']; //kutpa.a共建面5

        $power1        = $v['share'][0]['cPower1'];
        $power2        = $v['share'][0]['cPower2'];
        $data[$i][101] = (empty($power1) || empty($power2)) ? '' : $power1 . '/' . $power2; //kutpa.a共建分1

        $power1        = $v['share'][1]['cPower1'];
        $power2        = $v['share'][1]['cPower2'];
        $data[$i][102] = (empty($power1) || empty($power2)) ? '' : $power1 . '/' . $power2; //kutpa.a共建分2

        $power1        = $v['share'][0]['cPower1'];
        $power2        = $v['share'][0]['cPower2'];
        $data[$i][103] = (empty($power1) || empty($power2)) ? '' : $power1 . '/' . $power2; //kutpa.a共建分3

        $power1        = $v['share'][0]['cPower1'];
        $power2        = $v['share'][0]['cPower2'];
        $data[$i][104] = (empty($power1) || empty($power2)) ? '' : $power1 . '/' . $power2; //kutpa.a共建分4

        $power1        = $v['share'][0]['cPower1'];
        $power2        = $v['share'][0]['cPower2'];
        $data[$i][105] = (empty($power1) || empty($power2)) ? '' : $power1 . '/' . $power2; //kutpa.a共建分5

        $power1 = $power2 = null;
        unset($power1, $power2);

        $data[$i][106] = $v['parking'][0]['cNo']; //kutpa.a車位1
        $data[$i][107] = $v['parking'][1]['cNo']; //kutpa.a車位2
        $data[$i][108] = $v['parking'][2]['cNo']; //kutpa.a車位3
        $data[$i][109] = ''; //kutpa.a車位分1
        $data[$i][110] = ''; //kutpa.a車位分2
        $data[$i][111] = ''; //kutpa.a車位分3
        $data[$i][112] = ''; //kutpa.a稅籍號
        $data[$i][113] = ''; //kutpa.a移轉金額
        $data[$i][114] = ''; //kutpa.a報稅金額
    }

    //前次
    if (!empty($lands_before[$i])) {
        $v = $lands_before[$i];

        $data[$i][115] = $v['cLand3']; //前次.p地號
        $data[$i][116] = $v['cPower1'] . '/' . $v['cPower2']; //前次.p權利範圍2
        $data[$i][117] = $v['cMoveDate']; //前次.p前移轉日
        $data[$i][118] = $v['cLandPrice']; //前次.p前移轉價
        $data[$i][119] = ($v['cItem'] + 1); //前次.n次
        $data[$i][120] = $v['cIdentifyId']; //前次.noidno
        $data[$i][121] = $v['cName']; //前次.noname
    }
    print_r($data);

    $csv = implode(',', $data[$i]);
    file_put_contents($fh, $csv . "\n", FILE_APPEND);

}
