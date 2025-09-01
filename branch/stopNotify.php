<?php
require_once dirname(__DIR__).'/first1DB.php';

Function getStopBranch() {
    $conn = new first1DB;

    $sql = "SELECT	
                bId, bName, bStore, bStatusDateEnd
            FROM
                tBranch AS b
            WHERE
                b.bStatus = 3 AND bStatusDateEnd <= NOW()";

    $rs = $conn->all($sql);

    $txt = '';
    foreach ($rs as $v) {
        $txt.= "仲介店(".$v['bId'].")：".$v['bName']."_".$v['bStore']."暫停過期\r\n";
    }
    echo $txt;
}

$notifyTime = date('Y-m-d')." 09:00";

if (date('Y-m-d H:i') == $notifyTime) {
    getStopBranch();
}