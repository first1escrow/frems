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

for ($i = 0; $i < count($_POST['ReacllBranch']); $i++) {
    if ($_POST['del'] == '' || $_POST['del'] != $i) { //有刪除代碼就不要在算進來
        if ($_POST['ReacllBranch'][$i] != '' && $_POST['RecallScrivener'][$i] != '') {
            $data[$_POST['ReacllBranch'][$i]] = $_POST['RecallScrivener'][$i];
        }

    }

}

$json = json_encode($data);

$sql = "UPDATE tScrivener SET sSpRecall2 = '" . $json . "',sSpRecall = 0 WHERE sId ='" . $_POST['sId'] . "'";

$conn->Execute($sql);

unset($json);unset($data);
##

$cc = getFeedMoney('s', $_POST['sId']);

$html .= '<div>異動的保證號碼：' . @implode(',', $cc) . '</div>';

$sql = "SELECT sSpRecall2 FROM tScrivener WHERE sId ='" . $_POST['sId'] . "'";

$rs = $conn->Execute($sql);

$data = json_decode($rs->fields['sSpRecall2'], true);

if (is_array($data)) {
    $count = 1;
    $html .= '<table>';
    foreach ($data as $key => $value) {
        $html .= '<tr>';
        $html .= '<td>仲介回饋比率：<input type="text" name="ReacllBranch[]" id="" value="' . $key . '" style="width:50px"></td>';
        $html .= '<td>地政士回饋比率：<input type="text" name="RecallScrivener[]" id="" value="' . $value . '" style="width:50px"></td>';
        $html .= '<td><input type="button" onclick="Edit()" value="修改">&nbsp;&nbsp;&nbsp;&nbsp;
	                  <input type="button" onclick="Del(' . $count . ')" value="刪除"></td>';
        $html .= '</tr>';
        $count++;
    }
    $html .= '</table>';
}

echo $html;
