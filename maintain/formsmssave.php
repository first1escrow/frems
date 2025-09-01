<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/session_check.php';

$scrivener = new Scrivener();
if ($scrivener->CheckSmsScrivener($_POST['scid'], 11)) {
    echo 1;
    $scrivener->SaveSmsScrivener($_POST);
} else {
    echo "2";
    $scrivener->DelSmsScrivener($_POST['scid']);
    $scrivener->AddSmsScrivener($_POST, $_POST['scid']);
}
$scrivener->SaveSmsDefault($_POST['sDefault'], $_POST['scid']);

header("Location:formscriveneredit.php?id=" . $_POST['scid']);
