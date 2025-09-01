<?php
$act_identity = 'R';

$store_save = $_POST['bId'];

require __DIR__ . '/campaign_save.php';

$act_identity = $store_save = null;
unset($act_identity, $store_save);
