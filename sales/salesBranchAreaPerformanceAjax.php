<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/first1Sales.php';
require_once dirname(__DIR__) . '/includes/sales/getSalesAreaForPerformance.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST = escapeStr($_POST);

$area   = $_POST['area'];
$branch = $_POST['branch'];
$sales  = $_POST['sales'];
$str    = '1 = 1';
##

//區域郵遞區號
for ($i = 0; $i < count($area); $i++) {
    if (is_numeric($area[$i])) {
        $zip[] = $area[$i];
    } else {
        $tmp[] = '"' . $area[$i] . '"';
    }
}

//查詢縣市所有的郵遞區號
if (is_array($tmp)) {
    $sql = "SELECT * FROM tZipArea WHERE zCity IN (" . @implode(',', $tmp) . ")";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $zip[] = '"' . $rs->fields['zZip'] . '"';
        $rs->MoveNext();
    }
}
unset($tmp);

if (is_array($zip)) {
    if ($str) {$str .= ' AND ';}
    $str .= " bZip IN (" . @implode(',', $zip) . ")";
}

//店家
if ($branch) {
    if ($str) {$str .= ' AND ';}
    $str .= " bId = '" . $branch . "'";
}
##

$sql = "SELECT
			bId,
			(SELECT bName FROM tBrand AS c WHERE c.bId = bBrand) AS brand,
			bStore,
			CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) as bCode,
			bStatus
		FROM
			tBranch WHERE " . $str;
$rs = $conn->Execute($sql);

$list = array();
while (!$rs->EOF) {
    $check = true;
    //有查詢業務時判斷是否為該業務的店家
    if ($sales) {
        if (!checkSales($rs->fields['bId'], $sales)) {
            $check = false;
        }
    }

    if ($check) {
        if ($rs->fields['bStatus'] == 2) {
            $rs->fields['bStore'] .= '(停用)';
        } elseif ($rs->fields['bStatus'] == 3) {
            $rs->fields['bStore'] .= '(暫停)';
        }
        $list[] = $rs->fields;
    }

    $rs->MoveNext();
}

unset($check, $str);

##
$html .= '<table cellpadding="0" cellspacing="0" class="tb" width="100%">';
$html .= '<tr><th width="60%"><input type="checkbox" name="storeAllR" onclick="checkAllStore(\'R\')">店家</th><th width="40%">業務</th></tr>';
for ($i = 0; $i < count($list); $i++) {
    $html .= '<tr>';
    $html .= "<td><input type=\"checkbox\" name=\"storeR[]\" class=\"ckStoreR\" value=\"" . $list[$i]['bId'] . "\">" . $list[$i]['bCode'] . $list[$i]['brand'] . $list[$i]['bStore'] . "</td>";
    $html .= "<td id=\"R" . $list[$i]['bId'] . "\">" . getSalesHtml($list[$i]['bId']) . "</td>";
    $html .= '</tr>';
}
$html .= '</table>';

echo $html;
