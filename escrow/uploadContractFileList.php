<?php
header("Content-Type:text/html; charset=utf-8");

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';

$tlog = new TraceLog();
$tlog->insertWrite($_SESSION['member_id'], json_encode($_GET), '檔案上傳列表');

$_GET = escapeStr($_GET);

$id = $_GET['id'];
if (empty($id)) {
    echo '<div style="clear:both"></div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="table-title" width="70%">檔案名稱</td>
				<td class="table-title" width="20%">時間</td>
				<td class="table-title" width="10%">&nbsp;</td>
			</tr>
		</table>';
    exit;
}

$sort = $_GET['s']; //排序
$cat  = $_GET['cat'];
//filemtime
$saveUrl  = "../contractFile/" . $id;
$fileList = [];
if (is_dir($saveUrl)) {

    if ($dh = opendir($saveUrl)) {
        $i = 0;
        while (($filename = readdir($dh)) !== false) {
            if (! preg_match("/$filename/", "^\.+$")) {
                $EXTENSION                  = pathinfo($filename, PATHINFO_EXTENSION);
                $fileList[$i]['name']       = $filename;
                $fileList[$i]['url']        = $saveUrl . "/" . $filename . '?t=' . time();
                $fileList[$i]['modifyTime'] = (date('Y', filemtime($saveUrl . "/" . $filename)) - 1911) . date('-m-d H:i', filemtime($saveUrl . "/" . $filename));
                $fileList[$i]['sort']       = ($sort == 0) ? str_replace("." . $EXTENSION, '', $filename) : $fileList[$i]['modifyTime'];

                $i++;
                // unset($EXTENSION) ;
            }
        }

        ##
    }
    closedir($dh);
}
$fileList = Bubble_Sort($fileList);
//氣泡排序
function Bubble_Sort($_arrT)
{
    for ($i = 0; $i < count($_arrT); $i++) {
        for ($j = 0; $j < count($_arrT) - 1; $j++) {
            //檔名由小到大排序
            if ($_arrT[$j]['sort'] > $_arrT[$j + 1]['sort']) {
                $_tmp          = $_arrT[$j];
                $_arrT[$j]     = $_arrT[$j + 1];
                $_arrT[$j + 1] = $_tmp;
                unset($_tmp);
            }
        }
    }
    return $_arrT;
}

require_once '../class/SmartyMain.class.php'; // 確保載入 Smarty 類別

if (! isset($smarty)) {
    $smarty = new SmartyMain(); // 初始化 $smarty
}

#
$smarty->assign('cat', $cat);
$smarty->assign('fileList', $fileList);
$smarty->display('uploadContractFileList.inc.tpl', '', 'escrow');
$conn->close();
