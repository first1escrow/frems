<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

//新增對象時亂數產出不重複色碼
function salesCalenderColor(&$conn)
{
    do {
        $color = randomColor();
        $sql   = 'SELECT pCalenderClass FROM tPeopleInfo WHERE pDep = 7 AND pCalenderClass = "' . $color . '";';
        $rs    = $conn->Execute($sql);
    } while (!$rs->EOF);

    return $color;
}

function randomColor()
{
    $seed  = 'ABCDEF0123456789';
    $color = '#';

    for ($i = 0; $i < 6; $i++) {
        $color .= $seed[rand(0, strlen($seed) - 1)];
    }

    return $color;
}

$_POST    = escapeStr($_POST);
$_GET     = escapeStr($_GET);
$category = ($_POST['cat']) ? $_POST['cat'] : $_GET['cat'];
$id       = ($_POST['id']) ? $_POST['id'] : $_GET['id'];

//預載log物件
$logs = new Intolog();

//基本資料
$sql = "SELECT * FROM tPeopleInfo WHERE pId = '" . $id . "'";
$rs  = $conn->Execute($sql);

$data = $auth = [];
if (!$rs->EOF) {
    $data = $rs->fields;

    if ($data['pFaxNum']) {
        $tmp                 = explode('-', $data['pFaxNum']);
        $data['pFaxNumArea'] = $tmp[0];
        $data['pFaxNum']     = $tmp[1];

        $tmp = null;unset($tmp);
    }

    $data['pOnBoard'] = ($data['pOnBoard'] == '0000-00-00') ? '000-00-00' : (substr($data['pOnBoard'], 0, 4) - 1911) . substr($data['pOnBoard'], 4);

    $sql  = "SELECT * FROM tPowerList WHERE pId = '" . $data['pDep'] . "'";
    $rs   = $conn->Execute($sql);
    $auth = json_decode($rs->fields['pFunction'], true);

    if ($data['pAuthority'] != '') {
        //個人權限
        $Personalauthority = json_decode($data['pAuthority'], true);

        //如果有個人權限以個人權限的為主
        foreach ($auth as $key => $value) {
            if ($Personalauthority[$key] != $value && $Personalauthority[$key] != '') {
                $auth[$key] = $Personalauthority[$key];
            }
        }

        //20220713 修正指定部門以外權限無法指定問題
        foreach ($Personalauthority as $k => $v) {
            if (!array_key_exists($k, $auth)) {
                $auth[$k] = $Personalauthority[$k];
            }
        }
    }
}

//權限
$list_main = $list_branch = [];

$sql = "SELECT * FROM tPeopleInfoAuthority WHERE pDelete = 0 ORDER BY pLevel,pId ASC";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $rs->fields['pAuthority2'] = ($rs->fields['pAuthority2']) ? unserialize($rs->fields['pAuthority2']) : array();

    if ($rs->fields['pLevel'] == 0) {
        $list_main[] = $rs->fields;
    } else {
        $list_branch[$rs->fields['pGroup']][] = $rs->fields;
    }

    $rs->MoveNext();
}

//單位部門選單
$depMenu = '<option value=""></option>';
$sql     = 'SELECT * FROM tDepartment ORDER BY dId ASC';
$rs      = $conn->Execute($sql);
while (!$rs->EOF) {
    $_no = $rs->fields['dId'];
    $depMenu .= '<option value="' . $_no . '"';

    if ($data['pDep'] == $_no) {
        $depMenu .= ' selected="selected"';
    }

    $depMenu .= '>' . $rs->fields['dTitle'];

    if (($_no != '1') && ($_no != '2') && ($_no != '3')) {
        $depMenu .= '(' . $rs->fields['dDep'] . ')';
    }

    $depMenu .= "</option>\n";

    $rs->MoveNext();
}
$_no = null;unset($_no);

//權限
$sql = "SELECT zCity,zTrainee FROM tZipArea GROUP BY zCity ORDER BY nid ASC";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $menuCity[] = ['city' => $rs->fields['zCity'], 'trainee' => $rs->fields['zTrainee']] ;

    if ($data['pTest'] == $rs->fields['zTrainee']) {
        $cityChecked[$rs->fields['zCity']] = 'checked="checked"';
    }

    $rs->MoveNext();
}

if ($category == 'add') {
    $salesAreaBlock         = 'display:none;';
    $data['pCalenderClass'] = salesCalenderColor($conn);
} else {
    $salesAreaBlock = ($data['pDep'] == 7) ? '' : 'display:none;';
}

$use = ($data['pTest'] != 0) ? 1 : 0;

$smarty->assign('cityChecked', $cityChecked);
$smarty->assign('menuCity', $menuCity);
$smarty->assign('use', $use);
$smarty->assign('id', $id);
$smarty->assign('auth', $auth);
$smarty->assign('list_branch', $list_branch);
$smarty->assign('list_main', $list_main);
$smarty->assign('menuAct', array(2 => '停用', 1 => '啟用'));
$smarty->assign('menuUse', array(0 => '關閉', 1 => '開啟'));
$smarty->assign('menuGender', array('M' => '男性', 'F' => '女性'));
$smarty->assign("data", $data);
$smarty->assign("depMenu", $depMenu);
$smarty->assign("cat", $category);
$smarty->display('memberEdit.inc.tpl', '', 'member');