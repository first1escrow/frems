<?php
require_once dirname(dirname(dirname(__DIR__))) . '/openadodb.php';

$dir = __DIR__ . '/data';

//R
$current = [];

$files = glob($dir . '/R202*.csv');
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

// print_r($current);
##

//RT
$files = glob($dir . '/RT202*.csv');

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

// print_r($current);
##

//
$all = [];

$fh = file(dirname($dir) . '/R_all.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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
$dir = __DIR__ . '/data';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$fh = $dir . '/R' . date("Ymd") . '_append.csv';
file_put_contents($fh, "\xEF\xBB\xBF");
file_put_contents($fh, '保證號碼,仲介店類型,是否有服務費,身分' . "\r\n", FILE_APPEND);

if (!empty($append)) {
    foreach ($append as $v) {
        $txt = $v[0] . '_,' . $v[1] . ',' . $v[2] . ',' . $v[3];
        file_put_contents($fh, $txt . "\r\n", FILE_APPEND);
    }
}

exit('Done!!(' . date("Y-m-d G:i:s") . ')' . "\n");
