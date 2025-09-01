<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/includes/staff/leaveConfig.php';

if ($_POST) {
    require_once dirname(__DIR__) . '/includes/staff/sendOvertimeApply.php';

    echo '
                <script>
                    alert("申請已送出");
                    window.location.href = "myLeave.php";
                    parent.refresh("overtime");
                </script>
    ';
    exit;
}

$smarty->display('overtimeApply.inc.tpl', '', 'staff');