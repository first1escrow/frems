<?php
require_once dirname(__DIR__).'/configs/config.class.php';
require_once dirname(__DIR__).'/class/SmartyMain.class.php';
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';
require_once __DIR__.'/line_notify.php';

//類型轉換
Function converTargetType($type)
{
    if ($type == 'USER') {
        return '<i class="fas fa-user" title="USER"></i>';
    }

    if ($type == 'GROUP') {
        return '<i class="fas fa-users" title="GROUP"></i>';
    }

    return '';
}
##

//取得員工名稱
Function converStaff($pId)
{
    global $conn;

    $sql = 'SELECT `pName` FROM `tPeopleInfo` WHERE `pId` = :id;';
    $rs = $conn->one($sql, ['id' => $pId]);

    return empty($rs['pName']) ? '' : $rs['pName'];
}
##

//建構操作功能
Function addingOperationJS($code)
{
return '<button style="padding: 4px;" onclick="revoke(\''.$code.'\')"><i class="fas fa-trash-alt"></i>撤銷</button>';
}
##

//提示狀態指定
switch ($_GET['e']) {
    case 1 :
        $alert = '註冊綁定失敗';
    break;
    
    case 2 :
        $alert = 'Line 授權失敗';
    break;
    
    case 3 :
        $alert = '請重新註冊操作';
    break;

    default :
        $alert = '';
    break;
}

if (!empty($alert)) {
    $alert = 'alert("'.$alert.'");';
}
##

//取得清單
$sql_ext = in_array($_SESSION['member_id'], [1, 6]) ? '' : ' `lStaffId` = '.$_SESSION['member_id'].' AND ';

$conn = new first1DB;
$sql = 'SELECT `lCode`, `lStaffId`, `lNotifyTargetType`, `lNotifyTarget`, `lDescription`, `lCreatedAt` FROM `tLineNotify` WHERE '.$sql_ext.' `lStatus` = "Y";';
$rs = $conn->all($sql);

$data = [];
foreach ($rs as $v) {
    $v['lNotifyTargetType'] = empty($v['lNotifyTargetType']) ? '' : converTargetType($v['lNotifyTargetType']);
    $v['lStaffId']          = empty($v['lStaffId'])          ? '' : converStaff($v['lStaffId']);
    
    $v['operation'] = addingOperationJS($v['lCode']);

    $data[] = $v;
}
##

$show = (empty($data) || in_array($_SESSION['member_id'], [1, 6])) ? 1 : 2;

$smarty->assign('alert', $alert);
$smarty->assign('show', $show);
$smarty->assign('data', $data);
$smarty->assign('line_notify', $line_notify);

$smarty->display('notify.inc.tpl', '', 'notify');
?>
