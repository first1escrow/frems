<?php
include_once dirname(dirname(__DIR__)) . '/class/getBank.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

function GetBranch($id)
{
    $conn = new first1DB;

    $sql = " SELECT *,
                CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ), LPAD(bId,5,'0'))  bCode2
                FROM
                `tBranch` b
                WHERE b.bId = '" . $id . "'
                Order by b.bid ; ";
    return $conn->all($sql);
}

//經由郵遞區號取得縣市
function filterCityAreaName($_conn, $zips = '', $addr = '')
{
    if (empty($zips) || empty($addr)) {
        return $addr;
    }

    $sql = 'SELECT zCity,zArea FROM tZipArea WHERE zZip="' . $zips . '";';
    $rs  = $_conn->one($sql);

    if ($rs && isset($rs['zCity']) && isset($rs['zArea'])) {
        $city = $rs['zCity'];
        $area = $rs['zArea'];

        $addr = preg_replace("/$city/", "", $addr);
        $addr = preg_replace("/$area/", "", $addr);
    }

    return $addr;
}
##

//經由郵遞區號取得縣市
function getCityName($_conn, $zips = '')
{
    if (empty($zips)) {
        return '';
    }

    $sql = 'SELECT zCity FROM tZipArea WHERE zZip="' . $zips . '";';
    $rs  = $_conn->one($sql);

    return $rs && isset($rs['zCity']) ? $rs['zCity'] : '';
}
##

//經由郵遞區號取得鄉鎮市區
function getAreaName($_conn, $zips = '', $addr = '')
{
    if (empty($zips)) {
        return '';
    }

    $sql = 'SELECT zArea FROM tZipArea WHERE zZip="' . $zips . '";';
    $rs  = $_conn->one($sql);

    return $rs && isset($rs['zArea']) ? $rs['zArea'] : '';
}
##

function GetCategoryRecall()
{
    return [1 => '------', 2 => '整批', 3 => '結案'];
}

function GetCategoryIdentify()
{
    return [1 => '------', 2 => '身份證編號', 3 => '統一編號', 4 => '居留證號碼'];
}

function GetBankMenuList($list = [])
{
    $conn = new first1DB;
    $sql  = " SELECT
                bId, replace(concat(bBank3, bBank4), '　', '') bBank, replace(bBank4_name, '　', '') bBank4_name
                FROM
                `tBank`
                WHERE 1
                AND bBank4 = ''
                AND bOK = 0
                ORDER BY
                bBank3,bBank4
                ASC;";
    if (! empty($list)) {
        $sql .= " AND bBank3 IN (" . implode(',', $list) . ") ";
    }

    $result = $conn->all($sql);
    return ConvertBankOption($result, 'bBank', 'bBank4_name', true);
}

function ConvertBankOption($data, $value, $option, $hasNone = false)
{
    $aOptions = [];
    if ($hasNone) {
        $aOptions[0] = '--------';
    }
    foreach ($data as $k => $v) {
        $aOptions[$v[$value]] = $v[$option] . '(' . $v[$value] . ')';
    }

    return $aOptions;
}

function FeedBackData($id, $type)
{
    global $conn;

    $i    = 1;
    $data = []; // 初始化 $data 數組

    $sql = "SELECT * FROM tFeedBackData WHERE fType ='" . $type . "' AND fStoreId ='" . $id . "' AND fStatus = 0";
    // echo $sql."<br>";
    $rel = $conn->all($sql);

    foreach ($rel as $rs) {
        # code...
        $data[$i]                = $rs;
        $data[$i]['no']          = $i;
        $data[$i]['stop']        = ($rs['fStop'] == 1) ? 'checked=checked' : '';
        $data[$i]['disabled']    = ($rs['fStop'] == 1) ? 'disabled=disabled' : '';
        $data[$i]['countryC']    = listCity($conn, $rs['fZipC']);
        $data[$i]['areaC']       = listArea($conn, $rs['fZipC']);
        $data[$i]['countryR']    = listCity($conn, $rs['fZipR']);
        $data[$i]['areaR']       = listArea($conn, $rs['fZipR']);
        $data[$i]['bank_branch'] = getBankBranch($conn, $rs['fAccountNum'], $rs['fAccountNumB']);
        $i++;
    }

    return $data;
}

//取得縣市列表 SELECT
function listCity($_conn, $str = '')
{
    $val = '<option value="0"';
    if ($str == '') {
        $val .= ' selected="selected"';
    } else {
        $sql = 'SELECT * FROM tZipArea WHERE zZip="' . $str . '";';
        $rs  = $_conn->one($sql);
        $str = $rs['zCity'];
    }
    $val .= ">縣市</option>\n";

    $sql = 'SELECT * FROM tZipArea GROUP BY zCity ORDER BY zZip ASC;';
    $rel = $_conn->all($sql);
    foreach ($rel as $rs) {
        $val .= '<option value="' . $rs['zCity'] . '"';
        if ($str == $rs['zCity']) {
            $val .= ' selected="selected"';
        }
        $val .= '>' . $rs['zCity'] . "</option>\n";
    }

    return $val;
}
##

//取得縣市所屬鄉鎮市區列表
function listArea($_conn, $str = '')
{
    $city = ''; // 初始化 $city 變數

    $val = '<option value="0"';
    if ($str == '') {
        $val .= ' selected="selected"';
    } else {
        $sql = 'SELECT zCity FROM tZipArea WHERE zZip="' . $str . '";';
        $rs  = $_conn->one($sql);
        if ($rs && isset($rs['zCity'])) {
            $city = $rs['zCity'];
        }
    }
    $val .= ">鄉鎮市區</option>\n";

    if (! empty($city)) {
        $sql = 'SELECT * FROM tZipArea WHERE zCity="' . $city . '" ORDER BY zZip ASC;';
        $rel = $_conn->all($sql);
        foreach ($rel as $rs) {
            $val .= '<option value="' . $rs['zZip'] . '"';
            if ($str == $rs['zZip']) {
                $val .= ' selected="selected"';
            }
            $val .= '>' . $rs['zArea'] . "</option>\n";
        }
    }

    return $val;
}
##

function branchNote($branch_id)
{
    global $conn;

    //備註
    $sql = "SELECT * FROM tBranchNote WHERE bStore = '" . $branch_id . "' AND bDel = 0 AND bStatus = 0";
    $rel = $conn->all($sql);
    $txt = '';
    foreach ($rel as $rs) {
        $txt .= ($rs['bCreatTime'] != '0000-00-00 00:00:00') ? $rs['bCreatTime'] . "\r\n" : '';
        $txt .= $rs['bNote'] . "\r\n\r\n";
        // $rs->MoveNext();
    }

    return $txt;
}

$conn = new first1DB;

$result1 = GetBranch($_POST["id"]);
$result  = [];

$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS sales FROM tBranchSales WHERE bBranch = '" . $_POST["id"] . "'";
$rs  = $conn->one($sql);

// 確保必要的鍵存在並設置默認值
$result1[0]['sales']        = isset($rs['sales']) ? $rs['sales'] : '';
$result1[0]['bZip']         = isset($result1[0]['bZip']) ? $result1[0]['bZip'] : '';
$result1[0]['bAddress']     = isset($result1[0]['bAddress']) ? $result1[0]['bAddress'] : '';
$result1[0]['bAccountNum5'] = isset($result1[0]['bAccountNum5']) ? $result1[0]['bAccountNum5'] : '';
$result1[0]['bAccountNum6'] = isset($result1[0]['bAccountNum6']) ? $result1[0]['bAccountNum6'] : '';

$result1[0]['bAddress']       = filterCityAreaName($conn, $result1[0]['bZip'], $result1[0]['bAddress']);
$result1[0]['bCity']          = getCityName($conn, $result1[0]['bZip']);
$result1[0]['bArea']          = getAreaName($conn, $result1[0]['bZip']);
$result1[0]['FeedBackBranch'] = getBankBranchName($conn, $result1[0]['bAccountNum5'], $result1[0]['bAccountNum6']);
$result1[0]['note']           = branchNote($_POST["id"]);

//回饋金資料
$data_feedData         = FeedBackData($_POST["id"], 2);
$menu_categoryrecall   = GetCategoryRecall();
$menu_categoryidentify = GetCategoryIdentify();
$menu_bank             = GetBankMenuList();

$tbl = ''; // 初始化 $tbl 變數

if (is_array($data_feedData)) { //<input type="hidden" name="data_feedData'.$_POST["id"].'" value="1">
    foreach ($data_feedData as $k => $v) {
        $tbl .= '<table border="0" width="100%" ><tr><th bgcolor ="#E4BEB1" width="13%">回饋金對象資料</th><td colspan="5">
            	<table width="98%">
                <tr>
                    <td width="10%" align="center" class="tb-title2 th_title_sml">回饋<br>方式</td>
                   	<td>

                        <select disabled="disabled">';
        foreach ($menu_categoryrecall as $key => $value) {
            if ($key == $v['fFeedBack']) {
                $tbl .= '<option value=' . $key . ' selected>' . $value . '</option>';
            } else {
                $tbl .= '<option value=' . $key . '>' . $value . '</option>';
            }
        }
        $tbl .= '</select>
                </td>
                <td width="10%" align="center" class="tb-title2 th_title_sml">姓名/<br>抬頭</td>
                <td>
                    <input type="text" maxlength="15" class="input-text-big th_title_sml" value="' . $v['fTitle'] . '" disabled="disabled" style="width:200px"/>
                </td>
                <td width="10%" align="center" class="th_title_sml tb-title2">店長行<br>動電話</td>
                <td><input type="text" maxlength="10" class="input-text-big" value="' . $v['fMobileNum'] . '" disabled="disabled"/></td>
            </tr>
            <tr>
                <td align="center" class="th_title_sml tb-title2">身份別</td>
                <td><select disabled="disabled">';
        foreach ($menu_categoryidentify as $key => $value) {
            if ($key == $v['fIdentity']) {
                $tbl .= '<option value=' . $key . ' selected>' . $value . '</option>';
            } else {
                $tbl .= '<option value=' . $key . '>' . $value . '</option>';
            }
        }
        $tbl .= '</select></td>
           	<td align="center" class="th_title_sml tb-title2">證件<br>號碼</td>
           	<td>
               	<input type="text" maxlength="15" class="input-text-big" value="' . $v['fIdentityNumber'] . '" disabled="disabled"/>

           	</td>
           	<td></td>
           	<td></td>
       	</tr>
        <tr>
            <td align="center" class="th_title_sml tb-title2">聯絡<br>地址</td>
            <td colspan="5">

                <input type="text" maxlength="6" value="' . $v['fZipC'] . '" class="input-text-sml text-center" readonly="readonly" value="" disabled="disabled"/>
                <span id="FeedBaseC">
                    <select class="input-text-big" disabled="disabled" name="FeedBack_basecity">
                       ' . $v['countryC'] . '
                    </select>
                </span>
                <span id="FeedBaseA">
                <select class="input-text-big" disabled="disabled" name="FeedBack_basearea">
                    ' . $v['areaC'] . '
                </select>
                </span>
                <input style="width:500px;" value="' . $v['fAddrC'] . '" name="FeedBack_baseaddr" disabled="disabled"/>
            </td>
            </tr>
            <tr>
                <td align="center" class="th_title_sml tb-title2">戶藉<br>地址</td>
                <td colspan="5">
                <input type="text" maxlength="6" class="input-text-sml text-center" name="FeedBack_regzip" readonly="readonly" value="' . $v['fZipR'] . '" disabled="disabled"/>
                <span id="FeedRegistC">
                    <select class="input-text-big" disabled="disabled" name="FeedBack_regcity">
                        ' . $v['countryR'] . '
                    </select>
                </span>
                <span id="FeedRegistA">
                <select class="input-text-big" disabled="disabled" name="FeedBack_regarea">
                    ' . $v['areaR'] . '
                </select>
                </span>
                <input style="width:500px;"  value="' . $v['fAddrR'] . '"  disabled="disabled"/>
                </td>
                </tr>
                <tr>
                    <td align="center" class="th_title_sml tb-title2">電子<br>郵件</td>
                    <td colspan="3">
                        <input type="text" maxlength="255" class="input-text-per" value="' . $v['fEmail'] . '" disabled="disabled"/>
                   </td>
                </tr>
                <tr>
                    <td align="center" class="th_title_sml tb-title2">總行</td>
                    <td>
                    <select disabled="disabled">
					';
        foreach ($menu_bank as $key => $value) {
            if ($key == $v['fAccountNum']) {
                $tbl .= '<option value=' . $key . ' selected>' . $value . '</option>';
            } else {
                $tbl .= '<option value=' . $key . '>' . $value . '</option>';
            }
        }

        $tbl .= '</td>
             <td align="center"  class="th_title_sml tb-title2">分行</td>
             <td>
                 <span id="Feed_branch">
                     <select class="input-text-per" disabled="disabled" name="FeedBack_bankbranch">
                         ' . $v['bank_branch'] . '
                     </select>
                 </span>
             </td>
                <td align="center" class="th_title_sml tb-title2">指定<br>帳號</td>
                <td>
                    <input type="text" maxlength="14" class="input-text-per" name="FeedBack_acc" value="' . $v['fAccount'] . '" disabled="disabled"/>
                </td>
            </tr>
            <tr>
                            <td align="center" class="th_title_sml tb-title2">戶名</td>
                            <td>
                                <input type="text" maxlength="20" class="input-text-per" name="FeedBack_accname" value="' . $v['fAccountName'] . '" disabled="disabled"/>
                            </td>
                            <th>發票種類︰</th>
                            <td><input type="text"  value="' . $v['fNote'] . '" disabled="disabled"></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr><td colspan="6"><hr></td></tr>
                    </table>
                </td>
            </tr>';
    }
}

//連絡地址
$result1[0]['feedBackData'] = $tbl;

//戶籍地址
$result[0] = $result1;

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($result), '新增或切換仲介店家 ' . $_POST["cId"]);

echo json_encode($result);
