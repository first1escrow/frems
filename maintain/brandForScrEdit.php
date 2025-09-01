<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/includes/writelog.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '編修品牌回饋代書');

$_POST = escapeStr($_POST);
// echo '123456789'.$_POST['type'];
$type  = $_POST['type'];
$sId   = $_POST['sId'];
$id    = $_POST['id'];
$brand = $_POST['brand'];

switch ($type) {
    case 'add':
        $sql = "INSERT INTO
				tScrivenerFeedSp (
					sScrivener,
					sBrand,
					sRecall,
					sReacllBrand,
					sCreatName,
					sCreatTime
					) VALUES('" . $sId . "','" . $brand . "','" . $_POST['recall'] . "','" . $_POST['recallb'] . "','" . $_SESSION['member_name'] . "','" . date('Y-m-d H:i:s') . "')";
        $conn->Execute($sql);
        break;
    case 'mod':
        $sql = "UPDATE tScrivenerFeedSp SET sBrand = '" . $brand . "',sRecall ='" . $_POST['recall'] . "',sReacllBrand='" . $_POST['recallb'] . "',sModifyName='" . $_SESSION['member_name'] . "',sModifyTime='" . date('Y-m-d H:i:s') . "' WHERE sId ='" . $id . "'";

        $conn->Execute($sql);
        break;
    case 'del':
        $sql = "UPDATE tScrivenerFeedSp SET sDel = '1' WHERE sId ='" . $id . "'";
        $conn->Execute($sql);
        break;
    default:
        # code...
        break;
}
//跟改相對案件回饋金()
$changeCertifiedId = getFeedMoney('bs', $brand, $sId);
// getFeedMoney('bs',$brand,$sId);

// die;

##

//顯示所有
$brand      = new Brand();
$list_brand = $brand->GetBrandList(array(8, 77));

$menu_brand    = $brand->ConvertOption($list_brand, 'bId', 'bName');
$menu_brand[0] = '請選擇';
ksort($menu_brand);
// print_r($menu_brand);
$sql = "SELECT * FROM tScrivenerFeedSp WHERE sScrivener ='" . $sId . "' AND sDel =0";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $list[] = $rs->fields;

    $rs->MoveNext();
}
// $html = '<table>';
$html = '';
for ($i = 0; $i < count($list); $i++) {
    # code...
    $html .= "<table><tr><td>品牌：<select name='Brand" . $list[$i]['sId'] . "'>";
    $option = '';
    foreach ($menu_brand as $k => $v) {
        if ($k == $list[$i]['sBrand']) {
            $option .= "<option value='" . $k . "' selected>" . $v . "</option>";

        } else {
            $option .= "<option value='" . $k . "'>" . $v . "</option>";

        }

    }

    $html .= $option;
    $html .= "</select></td>";
    $html .= "<td>品牌回饋比率：<input type='text' name='ReacllBrand" . $list[$i]['sId'] . "'  value='" . $list[$i]['sReacllBrand'] . "' style='width:50px'></td>";
    $html .= "<td>地政士回饋比率：<input type='text' name='Recall" . $list[$i]['sId'] . "'  value='" . $list[$i]['sRecall'] . "' style='width:50px'>";
    $html .= '<input type="button" onclick="Edit(\'mod\',' . $list[$i]['sId'] . ')" value="修改">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $html .= '<input type="button" onclick="Edit(\'del\',' . $list[$i]['sId'] . ')" value="刪除"></td>';
    $html .= "</tr>";
    // <input type="text" name="newRecall" id="" value="" style="width:50px">
}
$html .= '</table>';

echo $html;
###########被更改的保證號碼###########
$html = '<hr><div id="certifiedId"><div>有更動回饋金的保證號碼</div>';
if (is_array($changeCertifiedId)) {
    foreach ($changeCertifiedId as $k => $v) {
        $tmp[] = $v;
    }
}

$html .= @implode('_', $tmp) . '</div>';
unset($tmp);
echo $html;
