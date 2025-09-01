<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/web_addr.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

//取得資料夾內檔案
function getDirFiles($doc_dir)
{
    $files = [];

    foreach (glob($doc_dir . "*") as $filename) {
        $filename = str_replace($doc_dir, '', $filename);
        $files[]  = [$filename, $doc_dir . $filename];
    }

    return $files;
}

//排序合併
function sortAndMerge(&$document, $data, $type, $prefix, $color)
{
    $data = Bubble_Sort($data);

    foreach ($data as $v) {
        $document[] = [
            'link'  => $v[1],
            'name'  => $v[0],
            'type'  => $type,
            'del'   => $prefix . '_' . $v[0],
            'color' => $color,
        ];
    }
}

//氣泡排序
function Bubble_Sort($_arrT)
{
    for ($i = 0; $i < count($_arrT); $i++) {
        for ($j = 0; $j < count($_arrT) - 1; $j++) {
            //檔名由小到大排序
            if ($_arrT[$j][0] > $_arrT[$j + 1][0]) {
                $_tmp          = $_arrT[$j];
                $_arrT[$j]     = $_arrT[$j + 1];
                $_arrT[$j + 1] = $_tmp;

                $_tmp = null;unset($_tmp);
            }
        }
    }

    return $_arrT;
}

# 取得身分
$ide = trim($_POST['ide']);

//設定檔案存放目錄位置
$uploaddir = $GLOBALS['FILE_PATH_UPLOAD'] . '/';

//上傳
if ($ide) {
    //設定檔案名稱
    $uploadfile = $uploaddir . $ide . '/' . $_FILES['upload_file']['name'];
    $localfile  = $_FILES['upload_file']['tmp_name'];
    $filename   = $_FILES['upload_file']['name'];

    $error = move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadfile) ? '2' : '3';
}

if (is_array($_POST['id'])) {
    foreach ($_POST['id'] as $k => $v) {
        $v = trim($v);

        $tmp = [];
        preg_match("/^(\w+)\_(.*)$/iuU", $v, $tmp);

        //設定檔案名稱
        $uploadfile = $uploaddir . $tmp[1] . '/' . $tmp[2];
        $error      = unlink($uploadfile) ? '4' : '5';

        $tmp = null;unset($tmp);
    }
}

# 讀取上傳區全部檔案
$document = [];

// 仲介部分
$doc_dir = $uploaddir . 'branch/';
if (is_dir($doc_dir)) {
    sortAndMerge($document, getDirFiles($doc_dir), '仲介', 'branch', '#FFDEA1');
}

// 地政士共用部分
$doc_dir = $uploaddir . 'scrivener/';
if (is_dir($doc_dir)) {
    sortAndMerge($document, getDirFiles($doc_dir), '地政士', 'scrivener', '#FFDEDE');
}

// 地政士加盟部分
$doc_dir = $uploaddir . 'scrivener1/';
if (is_dir($doc_dir)) {
    sortAndMerge($document, getDirFiles($doc_dir), '地政士加盟', 'scrivener1', '#FFDEDE');
}

// 地政士直營部分
$doc_dir = $uploaddir . 'scrivener2/';
if (is_dir($doc_dir)) {
    sortAndMerge($document, getDirFiles($doc_dir), '地政士直營', 'scrivener2', '#FFDEDE');
}

// 共用部分
$doc_dir = $uploaddir . 'common/';
if (is_dir($doc_dir)) {
    sortAndMerge($document, getDirFiles($doc_dir), '共用', 'common', '#FFDAC8');
}
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>檔案上傳</title>
    <link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css"
        rel="Stylesheet" />
    <script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript" src="/js/calender_limit.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        <?php if ($error == '1') {?>
        $('#msg').html('前台連線失敗!!');
        <?php } else if ($error == '2') {?>
        $('#msg').html('前台上傳成功!!');
        <?php } else if ($error == '3') {?>
        $('#msg').html('檔案上傳失敗!!');
        <?php } else if ($error == '4') {?>
        $('#msg').html('檔案刪除成功!!');
        <?php } else if ($error == '5') {?>
        $('#msg').html('檔案刪除失敗!!');
        <?php }?>

        <?php if ($error != '') {?>
        /* 設定 UI dialog 屬性 */
        $('#msg').dialog({
            modal: true,
            buttons: {
                OK: function() {
                    $(this).dialog("close");
                }
            }
        });

        <?php }?>
        //godel
        $("[name='godel']").on('click', function() {
            if (confirm("確定要刪除檔案嗎?")) {
                $('[name="delform"]').submit();
            }
        });
    });
    </script>
    <style>
    input {
        /* background-color: #FFFFFD ;*/
    }

    table {
        border: 1px solid #999;
    }

    td,
    th {
        border: 1px solid #FFF;
    }
    </style>
</head>

<body>

    <div id="msg" title="上傳結果"></div>
    <form name="myform" method="POST" enctype="multipart/form-data">
        <div style="padding-top:30px;">
            <div style="margin:0px auto;width:500px;text-align:left;padding:10px;border:1px solid #ccc;">
                <div style="padding:10px;">
                    <div>請選擇文件分類：</div>
                    <div style="padding: 10px;">
                        <input type="radio" name="ide" checked="checked" value="scrivener">地政士專用&nbsp;&nbsp;
                        <input type="radio" name="ide" value="scrivener1">地政士(加盟)專用&nbsp;&nbsp;
                        <input type="radio" name="ide" value="scrivener2">地政士(直營)專用&nbsp;&nbsp;

                        <input type="radio" name="ide" value="branch">仲介專用&nbsp;&nbsp;
                        <input type="radio" name="ide" value="common">其他(共用)
                    </div>
                    <div style="padding:10px;">
                        <input type="hidden" name="max_file_size" value="10240000">
                        <input style="width:300px;" type="file" name="upload_file">
                        <button id="uploadFile">開始上傳</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <br />
    <br />

    <div style="margin:0px auto;width:500px;padding:10px;border:1px solid #ccc;">
        <font size="6px">檔案刪除列表</font>
        <center>
            <form method="POST" name="delform">
                <table cellpadding="0" cellspacing="0" width="500px">
                    <tr>
                        <td colspan="2" align="center">
                            <font size="5px">共用檔案</font>
                        </td>
                    </tr>
                    <tr>
                        <th width="10%" bgcolor="#FF8000">&nbsp;</th>
                        <th bgcolor="#FF8000">檔案名稱</th>
                    </tr>
                    <?php $c = 0;
if (is_array($document)) {
    foreach ($document as $k => $v) {
        if ($v['type'] == '共用') {
            $c = 1;
            ?>
                    <tr>
                        <td align="center" bgcolor="<?=$v['color']?>">
                            <input type="checkbox" name="id[]" id="" value="<?=$v['del']?>" />
                        </td>
                        <td bgcolor="<?=$v['color']?>"><?=$v['name']?></td>
                    </tr>
                    <?php }

    }
}?>

                    <?php
if ($c == 0) {?>
                    <tr>
                        <td colspan="2" align="center" bgcolor="#FFDAC8">無檔案</td>
                    </tr>
                    <?php	}
?>
                </table>
                <br />

                <table cellpadding="0" cellspacing="0" width="500px">
                    <tr>
                        <td colspan="2" align="center">
                            <font size="5px">仲介檔案</font>
                        </td>
                    </tr>
                    <tr>
                        <th width="10%" bgcolor="#F59F00">&nbsp;</th>
                        <th bgcolor="#F59F00">檔案名稱</th>
                    </tr>
                    <?php
if (is_array($document)) {
    foreach ($document as $k => $v) {
        if ($v['type'] == '仲介') {?>
                    <tr>
                        <td align="center" bgcolor="<?=$v['color']?>">
                            <input type="checkbox" name="id[]" id="" value="<?=$v['del']?>" />
                        </td>
                        <td bgcolor="<?=$v['color']?>"><?=$v['name']?></td>
                    </tr>
                    <?php }
    }
}?>

                </table>
                <br />
                <table cellpadding="0" cellspacing="0" width="500px">
                    <tr>
                        <td colspan="2" align="center">
                            <font size="5px">地政士(共用)檔案</font>
                        </td>
                    </tr>
                    <tr>
                        <th width="10%" bgcolor="#FF8C8C">&nbsp;</th>
                        <th bgcolor="#FF8C8C">檔案名稱</th>
                    </tr>
                    <?php
if (is_array($document)) {
    foreach ($document as $k => $v) {
        if ($v['type'] == '地政士') {?>
                    <tr>
                        <td align="center" bgcolor="<?=$v['color']?>">
                            <input type="checkbox" name="id[]" id="" value="<?=$v['del']?>" />
                        </td>
                        <td bgcolor="<?=$v['color']?>"><?=$v['name']?></td>
                    </tr>
                    <?php }
    }
}?>

                </table>
                <br />
                <table cellpadding="0" cellspacing="0" width="500px">
                    <tr>
                        <td colspan="2" align="center">
                            <font size="5px">地政士(加盟)檔案</font>
                        </td>
                    </tr>
                    <tr>
                        <th width="10%" bgcolor="#FF8C8C">&nbsp;</th>
                        <th bgcolor="#FF8C8C">檔案名稱</th>
                    </tr>
                    <?php
if (is_array($document)) {
    foreach ($document as $k => $v) {
        if ($v['type'] == '地政士加盟') {?>
                    <tr>
                        <td align="center" bgcolor="<?=$v['color']?>">
                            <input type="checkbox" name="id[]" id="" value="<?=$v['del']?>" />
                        </td>
                        <td bgcolor="<?=$v['color']?>"><?=$v['name']?></td>
                    </tr>
                    <?php }
    }
}?>

                </table>
                <br />
                <table cellpadding="0" cellspacing="0" width="500px">
                    <tr>
                        <td colspan="2" align="center">
                            <font size="5px">地政士(直營)檔案</font>
                        </td>
                    </tr>
                    <tr>
                        <th width="10%" bgcolor="#FF8C8C">&nbsp;</th>
                        <th bgcolor="#FF8C8C">檔案名稱</th>
                    </tr>
                    <?php
if (is_array($document)) {
    foreach ($document as $k => $v) {
        if ($v['type'] == '地政士直營') {?>
                    <tr>
                        <td align="center" bgcolor="<?=$v['color']?>">
                            <input type="checkbox" name="id[]" id="" value="<?=$v['del']?>" />
                        </td>
                        <td bgcolor="<?=$v['color']?>"><?=$v['name']?></td>
                    </tr>
                    <?php }
    }
}?>

                </table>
                <br />
                <input type="button" value="刪除" name="godel" style="width:100px;" />
            </form>
        </center>
    </div>
</body>

</html>