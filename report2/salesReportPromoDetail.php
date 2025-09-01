<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/class/datalist.class.php';

$conn = new first1DB;

$alert = '';

$sId = $_GET['sId'];

if (!empty($_POST)) {
    //媒體檔歸檔
    if (is_file($_FILES['sMedia']['tmp_name'])) {
        $media = $_FILES['sMedia'];

        $publicPath = dirname(__DIR__) . '/public';
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0777, true);
            chmod($publicPath, 0777);
        }

        $storePath = 'classMedia';
        if (!is_dir($publicPath . '/' . $storePath)) {
            mkdir($publicPath . '/' . $storePath, 0777, true);
            chmod($publicPath . '/' . $storePath, 0777);
        }

        $fh = 'media_' . time() . '.' . pathinfo($media['name'], PATHINFO_EXTENSION);
        move_uploaded_file($media['tmp_name'], $publicPath . '/' . $storePath . '/' . $fh);

        $media = null;unset($media);
    }
    ##

    //
    $media = is_file($publicPath . '/' . $storePath . '/' . $fh) ? 'sMedia = "' . $storePath . '/' . $fh . '",' : null;

    $sId = $_POST['sId'];
    if (!empty($sId) && (strlen($sId) == 36)) { //更新
        $msg = '更新';

        $match = [];
        preg_match("/\(([A-Z]{2})(\d{4,5})\)/iu", $_POST['sStore'], $match);

        if (empty($match[1]) || empty($match[2])) {
            $msg = '店家資料錯誤!!';
        }

        $identity = ($match[1] == 'SC') ? 'S' : 'R';
        $store    = (int) $match[2];

        $sql = 'UPDATE
                    tSalesReportPromo
                SET
                    ' . $media . '
                    sIdentity = "' . $identity . '",
                    sStore = "' . $store . '",
                    sMemo = "' . addslashes($_POST['sMemo']) . '",
                    sDate = "' . $_POST['sDate'] . '",
                    sMaintainer = "' . $_SESSION['member_name'] . '"
                WHERE
                    sId = "' . $sId . '";';
        $msg .= $conn->exeSql($sql) ? '成功' : '失敗';
    } else { //新增
        $match = [];
        preg_match("/\(([A-Z]{2})(\d{4,5})\)/iu", $_POST['sStore'], $match);

        if (empty($match[1]) || empty($match[2])) {
            $msg = '店家資料錯誤!!';
        } else {
            $identity = ($match[1] == 'SC') ? 'S' : 'R';
            $store    = (int) $match[2];

            $msg = '新增';

            $sql = 'INSERT INTO
                        tSalesReportPromo
                    SET
                        sId = UUID(),
                        sSales = "' . $_POST['sSales'] . '",
                        sDate = "' . $_POST['sDate'] . '",
                        sIdentity = "' . $identity . '",
                        sStore = "' . $store . '",
                        ' . $media . '
                        sMemo = "' . $_POST['sMemo'] . '",
                        sMaintainer = "' . $_SESSION['member_name'] . '",
                        sCreatedAt = NOW();';
            if ($conn->exeSql($sql)) {
                $sql = 'SELECT sId FROM tSalesReportPromo WHERE sSales = :sales AND sDate = :date AND sIdentity = :identity AND sStore = :store;';
                $rs  = $conn->one($sql, [
                    'sales'    => $_POST['sSales'],
                    'date'     => $_POST['sDate'],
                    'identity' => $identity,
                    'store'    => $store,
                ]);

                $sId = $rs['sId'];
                $rs  = null;unset($rs);

                $msg .= '成功';
            } else {
                $msg .= '失敗';
            }
        }

        $match = $identity = $store = null;
        unset($match, $identity, $store);
    }

    $alert = '<script>alert("' . $msg . '")</script>' . "\n";
    $msg   = null;unset($msg);
    ##
}

//取得所有業務
$sql = 'SELECT pId, pName FROM tPeopleInfo WHERE pDep = 7 AND pJob = 1 ORDER BY pId ASC;';
$rs  = $conn->all($sql);

$menu_sales = [];
foreach ($rs as $v) {
    $menu_sales[$v['pId']] = $v['pName'];
}
##

//取得所有地政士與仲介aotocomplete清單
$stores = [];

$_datalist = new Datalist;
$stores    = $_datalist->all();

$_datalist = null;unset($_datalist);
##

//
$data = [];
if (!empty($sId)) {
    $sql  = 'SELECT sSales, sDate, sIdentity, sStore, sMedia, sConfirmed, sMemo FROM tSalesReportPromo WHERE sId = "' . $sId . '";';
    $data = $conn->one($sql);

    if (!empty($data)) {
        if ($data['sIdentity'] == 'S') {
            $sql = 'SELECT sId, sOffice FROM tScrivener WHERE sId = :sId;';
            $rs  = $conn->one($sql, ['sId' => $data['sStore']]);

            $data['sStore'] = $rs['sOffice'] . '(SC' . str_pad($rs['sId'], 4, '0', STR_PAD_LEFT) . ')';
        } else {
            $sql = 'SELECT a.bId, a.bStore, b.bCode AS code, b.bName AS brnad FROM tBranch AS a JOIN tBrand AS b ON a.bBrand = b.bId WHERE a.bId = :sId;';
            $rs  = $conn->one($sql, ['sId' => $data['sStore']]);

            $data['sStore'] = $rs['brand'] . $rs['bStore'] . '(' . $rs['code'] . str_pad($rs['bId'], 5, '0', STR_PAD_LEFT) . ')';
        }
    }
}

if (empty($data)) {
    $data = [
        'sDate'      => date("Y-m-d"),
        'sStore'     => '',
        'sMemo'      => '',
        'sConfirmed' => 'N',
    ];
}
##

$sql = $rs = $conn = null;
unset($sql, $rs, $conn);

$smarty->assign('menu_sales', $menu_sales);
$smarty->assign('menu_identity', ['S' => '地政士', 'R' => '仲介']);
$smarty->assign('sId', $sId);
$smarty->assign('data', $data);
$smarty->assign('stores', $stores);
$smarty->assign('alert', $alert);

$smarty->display('salesReportPromoDetail.inc.tpl', '', 'report2');
