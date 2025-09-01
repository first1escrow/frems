<?php
$company = json_decode(file_get_contents(dirname(dirname(dirname(__FILE__))) . '/includes/company.json'), true);

function expMoney($nu)
{
    global $conn;

    $sql = "SELECT SUM(tMoney) AS totalMoney FROM tBankTrans WHERE tExport_nu ='" . $nu . "'";
    $rs  = $conn->Execute($sql);

    return $rs->fields['totalMoney'];

}
function getCategoryBookId($txt)
{
    global $conn;

    $sql = "SELECT cId FROM tCategoryBook WHERE cName = '" . $txt . "'";
    $rs  = $conn->Execute($sql);

    return $rs->fields['cId'];

}

function NumtoStr($num)
{
    $numc  = "零,壹,貳,參,肆,伍,陸,柒,捌,玖";
    $unic  = ",拾,佰,仟";
    $unic1 = " ,萬,億,兆,京";

    $numc_arr  = explode(",", $numc);
    $unic_arr  = explode(",", $unic);
    $unic1_arr = explode(",", $unic1);

    $i   = str_replace(',', '', $num); #取代逗號
    $c0  = 0;
    $str = array();
    do {
        $aa = 0;
        $c1 = 0;
        $s  = "";
        #取最右邊四位數跑迴圈,不足四位就全取
        $lan = (strlen($i) >= 4) ? 4 : strlen($i);
        $j   = substr($i, -$lan);
        while ($j > 0) {
            $k = $j % 10; #取餘數
            if ($k > 0) {
                $aa = 1;
                $s  = $numc_arr[$k] . $unic_arr[$c1] . $s;
            } elseif ($k == 0) {
                if ($aa == 1) {
                    $s = "0" . $s;
                }
            }
            $j = intval($j / 10); #只取整數(商)
            $c1 += 1;
        }

        #轉成中文後丟入陣列,全部為零不加單位
        $str[$c0] = ($s == '') ? '' : $s . $unic1_arr[$c0];

        #計算剩餘字串長度
        $count_len = strlen($i) - 4;
        $i         = ($count_len > 0) ? substr($i, 0, $count_len) : '';

        $c0 += 1;
    } while ($i != '');

    #組合陣列
    $string = '';
    foreach ($str as $k => $v) {
        $tmp = empty($str[($k + 1)]) ? '' : $str[($k + 1)];
        $string .= array_pop($str);
        if (!empty($tmp) && preg_match("/萬$/iu", $tmp)) {
            if (preg_match("/^\D{1}佰/iu", $v)) {
                $string .= '零';
            } else if (preg_match("/^\D{1}拾/iu", $v)) {
                $string .= '零';
            }
        }
        $tmp = null;unset($tmp);
    }

    #取代重複0->零
    $string = preg_replace('/0+/', '零', $string);

    return $string;
}

function NumtoStr_old($num)
{
    $numc  = "零,壹,貳,參,肆,伍,陸,柒,捌,玖";
    $unic  = ",拾,佰,仟";
    $unic1 = " ,萬,億,兆,京";

    $numc_arr  = explode(",", $numc);
    $unic_arr  = explode(",", $unic);
    $unic1_arr = explode(",", $unic1);

    $i   = str_replace(',', '', $num); #取代逗號
    $c0  = 0;
    $str = array();
    do {
        $aa = 0;
        $c1 = 0;
        $s  = "";
        #取最右邊四位數跑迴圈,不足四位就全取
        $lan = (strlen($i) >= 4) ? 4 : strlen($i);
        $j   = substr($i, -$lan);
        while ($j > 0) {
            $k = $j % 10; #取餘數
            if ($k > 0) {
                $aa = 1;
                $s  = $numc_arr[$k] . $unic_arr[$c1] . $s;
            } elseif ($k == 0) {
                if ($aa == 1) {
                    $s = "0" . $s;
                }
            }
            $j = intval($j / 10); #只取整數(商)
            $c1 += 1;
        }
        #轉成中文後丟入陣列,全部為零不加單位
        $str[$c0] = ($s == '') ? '' : $s . $unic1_arr[$c0];

        #計算剩餘字串長度
        $count_len = strlen($i) - 4;
        $i         = ($count_len > 0) ? substr($i, 0, $count_len) : '';

        $c0 += 1;
    } while ($i != '');

    #組合陣列
    $string = '';
    foreach ($str as $v) {
        $tmp = empty($str[($k + 1)]) ? '' : $str[($k + 1)];
        $string .= array_pop($str);
        if (!empty($tmp) && preg_match("/萬$/iu", $tmp)) {
            if (preg_match("/^\D{1}佰/iu", $v)) {
                $string .= '零';
            } else if (preg_match("/^\D{1}拾/iu", $v)) {
                $string .= '零';
            }
        }
        $tmp = null;unset($tmp);
    }

    #取代重複0->零
    $string = preg_replace('/0+/', '零', $string);

    return $string;
}

function BookStatus($val)
{
    switch ($val) {
        case '0':
            $val = '待確認';
            break;
        case '1':
            $val = '待審核';
            break;
        case '2':
            $val = '已審核';
            break;
        default:
            $val = '未知';
            break;
    }

    return $val;
}

function dateformate($val)
{

    $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", "", $val));
    $tmp = explode('-', $val);

    if (preg_match("/0000/", $tmp[0])) {
        $tmp[0] = '000';
    } else {
        $tmp[0] -= 1911;
    }

    $val = $tmp[0] . '-' . $tmp[1] . '-' . $tmp[2];
    $tmp = null;unset($tmp);

    return $val;
}

function checkBook($id, $code2)
{ //出款用
    global $conn;

    $check = 0;
    $catId = getCategoryBookId($code2);

    if ($id != 0) {
        $sql   = "SELECT bId,bCategory FROM tBankTrankBook WHERE bBankTranId ='" . $id . "' AND bDel = 0";
        $rs    = $conn->Execute($sql);
        $count = $rs->RecordCount();

        if ($count == 0) {
            $check = 2;
        } else if ($rs->fields['bCategory'] != $catId) { //更改了交易類別，所以作廢
            $sql = "UPDATE tBankTrankBook SET bDel = 1 WHERE bId ='" . $rs->fields['bId'] . "'";
            $conn->Execute($sql);

            $sql = "UPDATE tBankTrankBookDetail SET bDel = 1 WHERE bTrankBookId = '" . $rs->fields['bId'] . "'";
            $conn->Execute($sql);

            $check = 1;
        } else if ($count > 0) { //有資料
            $check = 0;
        } else {
            $check = 2;
        }
    }

    return $check;
}

function getBank($val, $val2)
{
    global $conn;

    $sql = "SELECT bBank4_name AS BanchName,(SELECT bBank4_name FROM tBank WHERE bBank3 ='" . $val . "' AND bBank4='') AS BankName FROM tBank WHERE bBank3 ='" . $val . "' AND bBank4='" . $val2 . "'";
    $rs  = $conn->Execute($sql);

    return $rs->fields;
}