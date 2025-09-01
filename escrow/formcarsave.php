<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';

$cId            = trim($_POST['cId'][0]);
$certifiedId    = trim($_POST['cCertifiedId']);
$new_total      = trim($_POST['new_cTotal']);
$new_ground     = trim($_POST['new_cGround']);
$new_floor      = trim($_POST['new_cFloor']);
$new_no         = trim($_POST['new_cNo']);
$new_category   = trim($_POST['new_cCategory']);
$new_owner      = trim($_POST['new_cOwner']);
$new_owner_type = trim($_POST['new_cOwnerType']);
$new_other      = trim($_POST['new_cOther']);

if ($cId) {
    if ($new_ground) {
        $sql = "INSERT INTO `tContractParking`
                (
                    `cId`,
                    `cCertifiedId`,
                    `cTotal`,
                    `cGround`,
                    `cFloor`,
                    `cNo`,
                    `cCategory`,
                    `cOwner`,
                    `cOwnerType`,
                    `cOther`
                ) VALUES (
                    null,
                    '" . $certifiedId . "',
                    '" . $new_total . "',
                    '" . $new_ground . "',
                    '" . $new_floor . "',
                    '" . $new_no . "',
                    '" . $new_category . "',
                    '" . $new_owner . "',
                    '" . $new_owner_type . "',
                    '" . $new_other . "'
                );";
        $conn->Execute($sql);
    }

    for ($i = 0; $i < count($_POST['cId']); $i++) {
        $sql = "UPDATE `tContractParking` SET
                    `cTotal` =  '" . $_POST['cTotal'][$i] . "',
                    `cGround` =  '" . $_POST['cGround' . $_POST['cId'][$i]] . "',
                    `cFloor` =  '" . $_POST['cFloor'][$i] . "',
                    `cNo` =  '" . $_POST['cNo'][$i] . "',
                    `cCategory` =  '" . $_POST['cCategory' . $_POST['cId'][$i]] . "',
                    `cBelong` = '" . $_POST['old' . $_POST['cId'][$i]] . "',
                    `cOwner` =  '" . $_POST['cOwner' . $_POST['cId'][$i]] . "',
                    `cOwnerType` =  '" . $_POST['cOwnerType' . $_POST['cId'][$i]][$i] . "',
                    `cOther` =  '" . trim($_POST['cOther'][$i]) . "'
                WHERE `cId` = '" . $_POST['cId'][$i] . "' ; ";
        $conn->Execute($sql);

        $owner = null;unset($owner);
    }
} else {
    $sql = "INSERT INTO `tContractParking`
            (
                `cId`,
                `cCertifiedId`,
                `cTotal`,
                `cGround`,
                `cFloor`,
                `cNo`,
                `cCategory`,
                `cOwner`,
                `cOwnerType`,
                `cOther`
             ) VALUES (
                null,
                '" . $certifiedId . "',
                '" . $new_total . "',
                '" . $new_ground . "',
                '" . $new_floor . "',
                '" . $new_no . "',
                '" . $new_category . "',
                '" . $new_owner . "',
                '" . $new_owner_type . "',
                '" . $new_other . "'
              );";
    $conn->Execute($sql);
}

header("Location: formbuyowneredit.php?id=" . $_POST["cCertifiedId"]);
