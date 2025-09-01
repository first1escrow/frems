<?php
header("Content-type: text/html; charset=utf-8");
include_once 'configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/advance.class.php';


$adv = new Advance();
//$adv->DoSql($sql);
$handle = fopen("db.txt", "r");
while ($userinfo = fscanf($handle, '%s')) {
    $userinfo = explode(',', $userinfo[0]);
    list($name, $tel, $fax) = $userinfo;
    $sql = "Update tScrivener SET sTelMain = '$tel', sFaxMain = '$fax' Where sName = '$name'; ";
    echo "$sql<br/>";
}
fclose($handle);

?>
