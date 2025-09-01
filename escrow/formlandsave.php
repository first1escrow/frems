<?php
include_once '../configs/config.class.php';
include_once '../class/contract.class.php';
include_once '../session_check.php';
include_once '../openadodb.php';
include_once '../tracelog.php';

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '編修合約書建物詳細資料');

$contract = new Contract();

$cItem = $_POST['new_item'];
$bitem = $_POST['bitem'];

$j = $cItem;
for ($i = 0; $i < count($_POST['new_cCategory']); $i++) {
    # code...
    if ($_POST['new_cCategory'][$i] != 0 && $_POST['new_cCategory'][$i] != '') {
        $new_cMeasureMain = $_POST['new_cMeasureTotal'][$i] * $_POST['new_cPower1'][$i] / $_POST['new_cPower2'][$i];

        $sql = "INSERT INTO tContractPropertyObject (cCertifiedId,cItem,cCategory,cLevelUse,cMeasureTotal,cPower1,cPower2,cMeasureMain,cBuildItem)
				VALUES
				(
					'" . $_POST["cCertifiedId"] . "',
					'" . $j . "',
					'" . $_POST['new_cCategory'][$i] . "',
					'" . $_POST['new_cLevelUse'][$i] . "',
					'" . $_POST['new_cMeasureTotal'][$i] . "',
					'" . $_POST['new_cPower1'][$i] . "',
					'" . $_POST['new_cPower2'][$i] . "',
					'" . $new_cMeasureMain . "',
					'" . $bitem . "'
				)";
        // echo $sql."<br>";
        // die();
        $conn->Execute($sql);
        unset($new_cMeasureMain);
        $j++;
    }
}

$contract->SavePropertyObject($_POST);

$property = $contract->GetProperty($_POST["cCertifiedId"], $bitem);

// print_r($property);
if ($property['cPower2'] > 0) {
    $actualArea = round(($property['cMeasureTotal'] * ($property['cPower1'] / $property['cPower2'])), 2);
    // echo $property['cMeasureTotal']."_".$property['cPower1']."_".$property['cPower2']."<bR>";

    $sql = "UPDATE tContractProperty SET cActualArea ='" . $actualArea . "'  WHERE cCertifiedId ='" . $_POST['cCertifiedId'] . "' AND cItem = '" . $bitem . "'";
    // echo $sql;
    $conn->Execute($sql);
}

header("Location: formbuyowneredit.php?id=" . $_POST["cCertifiedId"]);
