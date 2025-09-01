<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once __DIR__ . '/HRMenuLock.php';

$conn = new first1DB;

$sql = 'SELECT
            a.pId,
            a.pName,
            a.pDep,
            (SELECT dDep FROM tDepartment WHERE dId = a.pDep) AS pDepName,
            CASE WHEN a.pGender = "M" THEN "男" ELSE "女" END AS pGender,
            a.pAccount,
            a.pOnBoard,
            a.pExt,
            b.pRegisterZip,
            (SELECT zCity FROM tZipArea WHERE zZip = b.pRegisterZip) AS registerCity,
            (SELECT zArea FROM tZipArea WHERE zZip = b.pRegisterZip) AS registerArea,
            b.pRegisterAddress,
            b.pMailingZip,
            (SELECT zCity FROM tZipArea WHERE zZip = b.pMailingZip) AS mailingCity,
            (SELECT zArea FROM tZipArea WHERE zZip = b.pMailingZip) AS mailingArea,
            b.pMailingAddress
        FROM
            tPeopleInfo AS a
        LEFT JOIN
            tPeopleInfoDetail AS b ON a.pId = b.pStaffId
        WHERE
            a.pId="' . $_SESSION['member_id'] . '";';
$rs = $conn->one($sql);

$staff = [
    'id'              => $rs['pId'],
    'name'            => $rs['pName'],
    'gender'          => $rs['pGender'],
    'account'         => $rs['pAccount'],
    'dep'             => $rs['pDepName'],
    'ext'             => $rs['pExt'],
    'onBoard'         => (empty($rs['pOnBoard']) || $rs['pOnBoard'] == '0000-00-00') ? '' : $rs['pOnBoard'],
    'registerAddress' => $rs['registerCity'] . $rs['registerArea'] . $rs['pRegisterAddress'],
    'mailingAddress'  => $rs['mailingCity'] . $rs['mailingArea'] . $rs['pMailingAddress'],
];

$sql = 'SELECT zCity FROM tZipArea GROUP BY zCity;';
$rs  = $conn->all($sql);

$city_options = '<option value=""></option>';
foreach ($rs as $row) {
    $city_options .= '<option value="' . $row['zCity'] . '">' . $row['zCity'] . '</option>';
}

$smarty->assign("staff", $staff);
$smarty->assign("city_options", $city_options);

$smarty->display('staffDetail.inc.tpl', '', 'staff');
