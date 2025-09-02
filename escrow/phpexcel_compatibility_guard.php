<?php
/**
 * PHPExcel 兼容性防護文件
 *
 * 這個文件確保系統不會意外載入舊版的 PHPExcel，
 * 並提供必要的類別定義來維持向後兼容性
 */

// 防止重複載入
if (defined('PHPEXCEL_COMPATIBILITY_GUARD')) {
    return;
}
define('PHPEXCEL_COMPATIBILITY_GUARD', true);

// 檢查是否已經載入了舊版 PHPExcel
if (class_exists('PHPExcel', false) ||
    class_exists('PHPExcel_IOFactory', false) ||
    class_exists('PHPExcel_Writer_Excel2007', false) ||
    defined('PHPEXCEL_ROOT')) {

    // 記錄警告但不終止執行，以防其他部分依賴舊版
    error_log('Warning: Old PHPExcel library detected. Consider migrating to PhpSpreadsheet.');

    // 對於新的實現，我們應該拋出異常
    if (defined('FORCE_PHPSPREADSHEET_ONLY') && FORCE_PHPSPREADSHEET_ONLY) {
        throw new Exception('Old PHPExcel library is not compatible with PHP 8+. Please use PhpSpreadsheet instead.');
    }
}

// 確保 PhpSpreadsheet 可用
if (! class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
    // 如果 PhpSpreadsheet 不可用，嘗試載入
    if (file_exists(dirname(__DIR__, 1) . '/vendor/autoload.php')) {
        require_once dirname(__DIR__, 1) . '/vendor/autoload.php';
    } else {
        throw new Exception('PhpSpreadsheet is required but not found. Please install via composer.');
    }
}

// 移除可能導致衝突的 include paths
$oldIncludePath = ini_get('include_path');
$pathsToRemove  = [
    dirname(__DIR__) . '/bank/Classes',
    dirname(__DIR__) . '/bank/Classes/',
    dirname(__DIR__, 2) . '/bank/Classes',
    dirname(__DIR__, 2) . '/bank/Classes/',
    './bank/Classes',
    './bank/Classes/',
    '../bank/Classes',
    '../bank/Classes/',
];

$newIncludePath = str_replace($pathsToRemove, '', $oldIncludePath);
if ($newIncludePath !== $oldIncludePath) {
    ini_set('include_path', $newIncludePath);
}

return true;
