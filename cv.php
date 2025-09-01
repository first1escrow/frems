<?php
include_once 'configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/scrivener.class.php';

$sc = new Scrivener();
$list = $sc->GetListScrivener();
foreach ($list as $k => $v) {
    $sql = " INSERT INTO `tScrivenerSms` (`sId`, `sScrivener`, `sNID`, `sName`, `sMobile`, `sDefault`) VALUES  (NULL, '".$v['sId']."', '1', '".$v['sName']."', '".$v['sMobileNum']."', '1' ); ";
    $sc->DoSql($sql);
}

/*
 * Update `tContractScrivener` Set cSmsTarget = '1' Where cSmsTarget = ''
 */
?>
