<?php
$act_identity = 'S';

$store_save = $_POST['id'];

require __DIR__ . '/campaign_save.php';

$act_identity = $store_save = null;
unset($act_identity, $store_save);
