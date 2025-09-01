<?php
include_once '../configs/config.class.php';
include_once '../class/SmartyMain.class.php';
include_once '../openadodb.php';
include_once '../session_check.php';
include_once '../tracelog.php';
require_once dirname(__DIR__) . '/first1DB.php';

$arr          = $arr2          = [];
$cCertifiedId = trim(addslashes($_POST['certified_id']));
$bid          = trim(addslashes($_POST['bBranch']));
$index        = (int) trim(addslashes($_POST['index'])); // 轉換為整數

$tlog = new TraceLog();
$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '案件仲介簡訊修改');

// 檢查 isSelect 是否存在且為陣列
$isSelectArray = isset($_POST['isSelect']) && is_array($_POST['isSelect']) ? $_POST['isSelect'] : [];

if (count($isSelectArray) > 0) {
    foreach ($isSelectArray as $k => $v) {
        $arr[] = trim(addslashes($v));

    }

    $isSelect = implode(',', $arr);

    // 檢查 add 是否存在且為陣列
    $addArray = isset($_POST['add']) && is_array($_POST['add']) ? $_POST['add'] : [];

    if (count($addArray) > 0) {
        $tmp3 = []; // 初始化陣列

        for ($i = 0; $i < count($addArray); $i++) {

            $number = $addArray[$i] - 1;
            // 檢查 smsphone 陣列是否存在且有對應的索引
            if (isset($_POST['smsphone']) && is_array($_POST['smsphone']) && isset($_POST['smsphone'][$number])) {
                $tmp3[] = trim(addslashes($_POST['smsphone'][$number]));
            }

        }

        $phone    = implode(',', $tmp3);
        $isSelect = $isSelect . ',' . $phone;
    }

    unset($arr);

    //更新合約書代書簡訊發送對象清單
    if ($cCertifiedId) {
        if ($isSelect) {
            // 處理欄位名稱：index 為 0 時使用 cBranchNum，其他情況使用 cBranchNum + index
            $branchNumField = ($index == 0) ? 'cBranchNum' : 'cBranchNum' . $index;
            $smsTargetField = ($index == 0) ? 'cSmsTarget' : 'cSmsTarget' . $index;

            $sql = 'UPDATE tContractRealestate SET ' . $smsTargetField . '="' . $isSelect . '" WHERE cCertifyId="' . $cCertifiedId . '" AND ' . $branchNumField . '="' . $bid . '";';

            $_conn = new first1DB();
            $_conn->exeSql($sql);
            $_conn = null;unset($_conn);
        }
    }
    ##
} else {

    // 檢查 add 是否存在且為陣列
    $addArray = isset($_POST['add']) && is_array($_POST['add']) ? $_POST['add'] : [];

    if (count($addArray) > 0) {
        $tmp3 = []; // 初始化陣列

        for ($i = 0; $i < count($addArray); $i++) {

            $number = $addArray[$i] - 1;
            // 檢查 smsphone 陣列是否存在且有對應的索引
            if (isset($_POST['smsphone']) && is_array($_POST['smsphone']) && isset($_POST['smsphone'][$number])) {
                $tmp3[] = trim(addslashes($_POST['smsphone'][$number]));
            }

        }

        if (isset($tmp3) && is_array($tmp3)) {
            $phone    = implode(',', $tmp3);
            $isSelect = $phone;
        }
    }

    if (isset($isSelect)) {
        // 處理欄位名稱：index 為 0 時使用 cBranchNum，其他情況使用 cBranchNum + index
        $branchNumField = ($index == 0) ? 'cBranchNum' : 'cBranchNum' . $index;
        $smsTargetField = ($index == 0) ? 'cSmsTarget' : 'cSmsTarget' . $index;

        $sql = 'UPDATE tContractRealestate SET ' . $smsTargetField . '="' . $isSelect . '" WHERE cCertifyId="' . $cCertifiedId . '" AND ' . $branchNumField . '="' . $bid . '";';
        // echo $sql ;
        $conn->Execute($sql);
    }
}

##
//額外新增簡訊對象

// 檢查 add 是否存在且為陣列
$addArray = isset($_POST['add']) && is_array($_POST['add']) ? $_POST['add'] : [];

if (count($addArray) > 0) {

    for ($i = 0; $i < count($addArray); $i++) {
        $number = $addArray[$i] - 1;

        // 檢查各個陣列是否存在且有對應的索引
        if (isset($_POST['title']) && is_array($_POST['title']) && isset($_POST['title'][$number])) {
            $tmp1[] = trim(addslashes($_POST['title'][$number]));
        }
        if (isset($_POST['smsname']) && is_array($_POST['smsname']) && isset($_POST['smsname'][$number])) {
            $tmp2[] = trim(addslashes($_POST['smsname'][$number]));
        }

        // 確保所有必要的變數都存在
        if (isset($tmp1[$i]) && isset($tmp2[$i]) && isset($tmp3[$i])) {
            $sql = 'INSERT INTO tBranchSms (bBranch,bNID,bName,bMobile,bCheck_id ) VALUES (' . $bid . ',' . $tmp1[$i] . ',"' . $tmp2[$i] . '","' . $tmp3[$i] . '","' . $cCertifiedId . '")';

            $_conn = new first1DB();
            $_conn->exeSql($sql);
            $_conn = null;unset($_conn);
        }
    }

    if (isset($tmp1)) {
        unset($tmp1);
    }

    if (isset($tmp2)) {
        unset($tmp2);
    }

    if (isset($tmp3)) {
        unset($tmp3);
    }

}

##

header('location: formcasesmsrealty.php?bid=' . $bid . '&cid=' . $cCertifiedId . '&ok=1&in=' . ($index + 1));
