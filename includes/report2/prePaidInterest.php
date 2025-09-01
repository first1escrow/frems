<?php
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(dirname(__DIR__)) . '/class/bankExport/adjustAccount.class.php';
require_once dirname(dirname(__DIR__)) . '/class/util.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

use First1\V1\Bank\AdjustAccount;

//取得參數
$from_date = $_POST['fromDate'];
$to_date   = $_POST['toDate'];
##

$conn              = new first1DB;
$pre_paid_interest = new AdjustAccount;

/**
 * 一銀城東內容(55006110050011)
 */

$account   = '55006110050011';
$chengdong = [];

//取得開始日期帳戶餘額
$chengdong_balance = $pre_paid_interest->accountBalance($account, $from_date);
##

//取得紀錄
$chengdong = $pre_paid_interest->getData($account, $from_date, $to_date);
##
// echo '<pre>';
// print_r($chengdong);exit;
/**
 * 一銀桃園內容(60001110019411)
 */

$account = '60001110019411';
$taoyuan = [];

//取得開始日期帳戶餘額
$taoyuan_balance = $pre_paid_interest->accountBalance($account, $from_date);
##

//取得紀錄
$taoyuan = $pre_paid_interest->getData($account, $from_date, $to_date);
##
// echo '<pre>';
// print_r($taoyuan);exit;

require_once __DIR__ . '/prePaidInterestExcel.php';
