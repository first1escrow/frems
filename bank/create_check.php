<?php
require_once dirname(__DIR__) . '/openadodb.php';

$sid  = trim($_POST['sid']);
$bank = trim($_POST['bank']);

$sql = "SELECT * FROM tScrivener WHERE sId='" . $sid . "'";
$rs  = $conn->Execute($sql);

$options = '<select name="bBrand" id="bBrand">';

// 合約書版本-飛鷹(銀行別 限一銀)
// 群義(銀行別 限台新 建物)
// 實易不動產(銀行別 限一銀)

$tmp   = explode(',', $rs->fields['sBrand']);
$check = 0;
for ($i = 0; $i < count($tmp); $i++) {
    if ($tmp[$i] != 49) { //優美排出 應為沒有優美的合約書
        if ($bank) {
            if (!in_array($bank, [1, 7]) && $tmp[$i] == 75) {
                continue;
            }

            if (!in_array($bank, [1, 7]) && $tmp[$i] == 80) {
                continue;
            }

            if ($bank != 5 && $tmp[$i] == 72) {
                continue;
            }
        }

        $sql = "SELECT * FROM tBrand WHERE bId='" . $tmp[$i] . "'";
        $rs  = $conn->Execute($sql);

        $options .= "<option value='" . $rs->fields['bId'] . "'>" . $rs->fields['bName'] . "</option>";
    }
}

$options .= '</select>';
echo $options;
die;
