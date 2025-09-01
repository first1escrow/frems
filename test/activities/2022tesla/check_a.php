<?php
require_once dirname(dirname(dirname(__DIR__))) . '/openadodb.php';

$dir = __DIR__ . '/data';

//A
$current = [];

$files = glob($dir . '/A202*.csv');

foreach ($files as $file) {
    // echo 'file = ' . $file . "\n";

    $fh = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($fh as $line) {
        $csv = explode(',', $line);

        $csv[0] = str_replace('_', '', $csv[0]);
        if (preg_match("/^\d+$/", $csv[0])) {
            $current[] = $csv;
        }

        $csv = null;unset($csv);
    }

    $fh = null;unset($fh);
}

$files = $file = null;
unset($files, $file);

// print_r($current);exit;
##

//AT
$files = glob($dir . '/AT202*.csv');

foreach ($files as $file) {
    // echo 'file = ' . $file . "\n";

    $fh = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($fh as $line) {
        $csv = explode(',', $line);

        $csv[0] = str_replace('_', '', $csv[0]);
        if (preg_match("/^\d+$/", $csv[0])) {
            $current[] = $csv;
        }

        $csv = null;unset($csv);
    }

    $fh = null;unset($fh);
}

$files = $file = null;
unset($files, $file);

// print_r($current);exit;
##

//
$all = [];

$fh = file(dirname($dir) . '/A_all.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($fh as $line) {
    $csv = explode(',', $line);

    $csv[0] = str_replace('_', '', $csv[0]);
    if (preg_match("/^\d+$/", $csv[0])) {
        $all[] = $csv;
    }

    $csv = null;unset($csv);

}
$fh = null;unset($fh);

// print_r($all);
##

//
$append = [];

$certifiedId = array_column($current, 0);
foreach ($all as $v) {
    if (!in_array($v[0], $certifiedId)) {
        $append[] = $v;
    }
}

// $append = array_unique(array_column($append, 0));
print_r($append);
##

//
$fh = fopen('A' . date("Ymd") . '_append.csv', 'w');

fwrite($fh, "\xEF\xBB\xBF");
fwrite($fh, '保證號碼,簽約日期,建檔日期,案件狀態,買方身份證字號,賣方身份證字號,物件地址,類型,買方姓名,賣方姓名,買方電話,賣方電話,買方仲介店,賣方仲介店' . "\r\n");

for ($i = 0; $i < count($append); $i++) {
    if (preg_match("/直營/", $append[$i][7])) {
        fwrite($fh, $append[$i][0] . '_,' . $append[$i][1] . ',' . $append[$i][2] . ',' . $append[$i][3] . ',' . $append[$i][4] . ',' . $append[$i][5] . ',' . $append[$i][6] . ',' . $append[$i][7] . ',' . $append[$i][8] . ',' . $append[$i][9] . ',' . $append[$i][10] . ',' . $append[$i][11] . ',' . $append[$i][12] . ',' . $append[$i][13] . "\r\n");
    }
}
fclose($fh);

echo 'Done!!(' . date("Y-m-d G:i:s") . ')';
##
