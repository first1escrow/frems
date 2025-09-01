<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/class/staffReview.class.php';

use First1\V1\Staff\StaffReview;

//取得待審核資訊
$review = StaffReview::getInstance();
$cases  = $review->isReview($_SESSION['member_id']);
echo empty($cases) ? 'N' : 'Y';
exit;
