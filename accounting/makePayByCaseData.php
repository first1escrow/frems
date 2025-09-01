<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/intolog.php' ;
require_once dirname(__DIR__) . '/openadodb.php' ;
require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';
require_once dirname(__DIR__) . '/session_check.php';

use First1\V1\PayByCase\PayByCase;

$payByCase = new PayByCase;

$confirm = trim($_POST['confirm']) ;

if($confirm == 'ok') {
    $cid = trim($_REQUEST['cid']) ;
    $res = $payByCase->manualAddSalesConfirmRecord($cid);
    if($res) {
        echo '
		<script>
		    alert("新增成功!!") ;
		</script>
		' ;
    }

}

$smarty->display('makePayByCaseData.inc.tpl', '', 'accounting');