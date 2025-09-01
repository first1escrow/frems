<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/first1Sales.php';
require_once dirname(__DIR__) . '/includes/sales/getSalesAreaForPerformance.php';
require_once dirname(__DIR__) . '/session_check.php';

$area   = $_POST['area'];
$branch = $_POST['branch'];
$sales  = $_POST['sales'];
$str    = "1=1";
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
$tmp = null;unset($tmp);

if (is_array($zip)) {
    if ($str) {
        $str .= ' AND ';
    }

    $str .= " sCpZip1 IN (" . @implode(',', $zip) . ")";
}

//店家
if ($branch) {
    if ($str) {
        $str .= ' AND ';
    }

    $str .= " sId = '" . $branch . "'";
}
##

$sql = "SELECT
			CONCAT('SC',LPAD(sId,4,'0')) as Code,
			sId,
			sName,
			sOffice,
			sStatus
		FROM
			tScrivener WHERE " . $str;
$rs = $conn->Execute($sql);

$list = array();
while (!$rs->EOF) {
    $check = true;

    //有查詢業務時判斷是否為該業務的店家
    if ($sales) {
        if (!checkSales($rs->fields['sId'], $sales, 's')) {
            $check = false;
        }
    }

    if ($check) {
        if ($rs->fields['sStatus'] == 2) {
            $rs->fields['sOffice'] .= '(停用)';
        } elseif ($rs->fields['sStatus'] == 3) {
            $rs->fields['sOffice'] .= '(重複建檔)';
        } elseif ($rs->fields['sStatus'] == 4) {
            $rs->fields['sOffice'] .= '(未簽約)';
        }
        $list[] = $rs->fields;
    }

    $rs->MoveNext();
}

unset($check, $str);

$html .= '<table cellpadding="0" cellspacing="0" class="tb" width="100%">';
$html .= '<tr><th width="60%"><input type="checkbox" name="storeAll" onclick="checkAllStore()">店家</th><th width="40%">業務</th></tr>';
for ($i = 0; $i < count($list); $i++) {
    $html .= '<tr>';
    $html .= "<td><input type=\"checkbox\" name=\"store[]\" class=\"ckStore\" value=\"" . $list[$i]['sId'] . "\">" . $list[$i]['Code'] . $list[$i]['sName'] . "(" . $list[$i]['sOffice'] . ")</td>";
    $html .= "<td id=\"" . $list[$i]['sId'] . "\">" . getSalesHtml($list[$i]['sId'], 's') . "</td>";
    $html .= '</tr>';
}
$html .= '</table>';

echo $html;
