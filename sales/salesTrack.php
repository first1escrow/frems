<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" href="../css/colorbox.css" />
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />
<script src="../js/jquery-1.7.2.min.js"></script>

<script src="../js/jquery.colorbox.js"></script>
<{include file='meta.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {
<?php
if (($_SESSION['member_sales'] != 1) && ($_SESSION['member_pDep'] == 7)) {
	$_SESSION['member_sales'] = 1 ;
?>
	$("#calender").submit() ;
<?php
}
?>
	$("#tracking").submit() ;
}) ;
</script>
</head>
<body>
<form id="calender" method="post" action="../calendar/calendar.php">

</form>
<form id="tracking" method="post" action="salesTracking.php" target="_blank">

</form>

</body>
</html>