<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/traits/CheckIn.trait.php';
require_once dirname(__DIR__) . '/staffHRBeginDate.php';

class CheckInData
{
    use CheckIn;

    public function __construct()
    {
        $this->conn = new first1DB;
    }
}

$year      = empty($_POST['year']) ? date('Y') : $_POST['year'];
$month     = empty($_POST['month']) ? date('m') : $_POST['month'];
$member_id = $_SESSION['member_id'];

$to   = date('Y-m-d 23:59:59');
$from = date('Y-m-d 00:00:00', strtotime('-40 day', strtotime($to)));

$checkInData = new CheckInData;

$checkInData->BEGINDATE = BEGINDATE;
$output                 = $checkInData->getCheckInOutList($from, $to, $member_id);

exit(json_encode($output['periodData']));