<?php

//Adodb 連線
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once __DIR__ . '/snoopy/Snoopy.class.php';
##

$_REQUEST = escapeStr($_REQUEST);

$acc = $_REQUEST['acc'];
$ide = $_REQUEST['ide'];
$day = $_REQUEST['day'];

if (empty($day)) {
    $day  = date("Y-m-d");
    $day1 = getPrev($acc, $day);
    $str  = ' AND am.aCreateTime >= "' . $day1 . ' 00:00:00" AND am.aCreateTime <="' . $day . ' 23:59:59"';
} else {
    $day1 = getPrev($acc, $day);
    if ($day1 == false) { //已經沒有資料了
        exit;
    }

    $str = ' AND am.aCreateTime >= "' . $day1 . ' 00:00:00" AND am.aCreateTime < "' . $day . ' 00:00:00"';
}

$sql = 'SELECT
			am.*,
			aa.aName AS ScrName,
			(SELECT (SELECT pName FROM tPeopleInfo AS p WHERE p.pId= s.sUndertaker1) FROM tScrivener AS s WHERE s.sId = SUBSTR(aa.aParentId,3)) AS pName,
			(SELECT (SELECT pSlackToken FROM tPeopleInfoAccount AS p WHERE p.pInfoId= s.sUndertaker1) FROM tScrivener AS s WHERE s.sId = SUBSTR(aa.aParentId,3)) AS sToken
		FROM
			tAppMessages AS am,
			tAppAccount AS aa
		WHERE
			aa.aId = am.aAccount
			AND am.aAccount="' . $acc . '"
			' . $str . '
		ORDER BY
			am.aCreateTime DESC;';
$rs = $conn->Execute($sql);

$arr = array();
while (!$rs->EOF) {
    $arr[] = $rs->fields;
    $rs->MoveNext();
}

$arr = array_reverse($arr);

$data        = '';
$tmpDay      = '';
$newMsg      = 1;
$unReadCheck = 0;

foreach ($arr as $k => $v) {
    $sql    = '';
    $unRead = '';
    $newMsg = 1;
    if ($v['aRead'] == 'N' && $v['aFlow'] == 1 && $unReadCheck == 0) {
        $unReadCheck = 1;
        $data .= '<div style="background-color:#CCC"><span style="font-size:10px;display:inline">以下未讀</span></div>';
    }

    $dd               = $v['aCreateTime'];
    $v['aCreateTime'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", "", $v['aCreateTime'])); //

    if ($tmpDay != $v['aCreateTime']) {
        $tmpDay = $v['aCreateTime'];
        $data .= '<div style="background-color: #F8ECE9;text-align:center" class="now" id="' . $tmpDay . '">' . $tmpDay . '</div>';
    }

    if ($ide == 2) {
        if ($v['aFlow'] == '1') {
            if ($v['aRead'] == 'N') {
                if ($v['aCheck'] == 0) {
                    setRead($v['sToken'], $v['aImId'], $v['aSlackCreatTime']);
                }
                $newMsg = 2;
            }
        } else if ($v['aFlow'] == '2') {
            if ($v['aRead'] == 'Y') {
                $unRead = '<span style="font-size:6pt;color:#008888;">已讀</span>';
            }
        }
    }

    if (!empty($sql)) {
        $conn->Execute($sql);
    }

    if ($ide == 2) {
        $float     = ($v['aFlow'] == '1') ? 'float:left;' : 'float:right;text-align:right;';
        $v['Name'] = ($v['aFlow'] == '1') ? $v['ScrName'] : $v['pName'];
    } else if ($ide == 2) {
        $float     = ($v['aFlow'] == '2') ? 'float:right;text-align:right;' : 'float:left;';
        $v['Name'] = ($v['aFlow'] == '2') ? $v['pName'] : $v['ScrName'];
    }

    $time = '';
    $msg  = empty($v['aContent']) ? ($v['aAppFile'] != '') ? '<a href="getSlackFile.php?id=' . $v['aAppFile'] . '&sId=' . $v['aSlackId'] . '" target="_blank">' . basename($v['aAppFileName']) . '</a>' : basename($v['aAppFileName']) . '<span style="font-size:9pt;color:#000088;">(檔案連結失效)</span>' : $v['aContent'];
    $time = '<span style="font-size:6pt;">' . substr($dd, 11, 5) . '</span>'; //substr($v['aCreateTime'],5,11)

    if (!empty($unRead)) {
        $unRead = '<div style="height:3px;"></div>' . $unRead;
    }

    $data .= '
	<div style="font-family:Microsoft JhengHei;' . $float . '">
		<span style="font-size:9pt;color:#008888;">' . $v['Name'] . '&nbsp;說&nbsp;(' . $time . ')</span><br>' . $msg . $unRead . '
	</div>
	<div style="clear:both;margin-bottom:10px;"></div>
	';
}
$data .= '</div>';

echo $newMsg . $data;
##

function setRead($token, $imId, $aSlackCreatTime)
{
    global $conn;
    $url  = 'https://slack.com/api/im.mark'; //rtm.start
    $args = array('token' => $token, 'channel' => $imId, 'pretty' => 1, 'ts' => $aSlackCreatTime);

    $snoopy = new Snoopy;
    $snoopy->submit($url, $args);

    $html = $snoopy->results;
    $list = json_decode($html, true);

    $path = __DIR__ . '/files';
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
    $fw = fopen($path . '/aCheck.log', 'a+');
    if ($list['ok'] == 1) {
        $sql = "UPDATE tAppMessages SET aCheck = '1' WHERE aImId ='" . $imId . "' AND aSlackCreatTime ='" . $aSlackCreatTime . "'";
        fwrite($fw, date('Y-m-d H:i:s') . '_' . $token . '成功' . $aSlackCreatTime . "\r\n");
        $conn->Execute($sql);
    } else {
        fwrite($fw, date('Y-m-d H:i:s') . '_' . $token . '失敗' . $aSlackCreatTime . "\r\n");
    }

    fclose($fw);
}

function getPrev($acc, $day)
{
    global $conn;

    $sql = 'SELECT DATE_FORMAT(aCreateTime,"%Y-%m-%d") AS creatDay FROM tAppMessages WHERE aAccount="' . $acc . '"  AND aCreateTime < "' . $day . ' 00:00:00" GROUP BY DATE_FORMAT(aCreateTime,"%Y-%m-%d") ORDER BY aCreateTime ASC;';
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $data[] = $rs->fields['creatDay'];
        $rs->MoveNext();
    }

    $pre = count($data) - 6; //計算有資料天數
    if (count($data) == 0) {
        return false;
    }

    if ($pre < 0) {
        $pre = 0;
    }

    return $data[$pre];
}

function getScrivener($acc)
{
    global $conn;

    $sql = 'SELECT * FROM tScrivener WHERE sId = "' . $acc . '";';
    $rs  = $conn->Execute($sql);

    return (!$rs->EOF) ? $rs->fields['sName'] . ' 代書' : false;
}
##

//
function getStaff($acc)
{
    global $conn;

    $sql = 'SELECT (SELECT pName FROM tPeopleInfo WHERE pId=a.sUndertaker1) as staff FROM tScrivener AS a WHERE sId = "' . $acc . '";';
    $rs  = $conn->Execute($sql);
    return (!$rs->EOF) ? $rs->fields['staff'] . ' 經辦' : false;
}
