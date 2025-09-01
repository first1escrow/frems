<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/staffReview.class.php';
require_once dirname(__DIR__) . '/staffHRBeginDate.php';

use First1\V1\Staff\StaffReview;

$member_id = $_SESSION['member_id'];

$conn = new first1DB;

//取得待審核資訊
$review = StaffReview::getInstance();
$cases  = $review->getOvertimeReviewHistory($member_id);

$output = [
    'data' => $cases,
];

exit(json_encode($output));