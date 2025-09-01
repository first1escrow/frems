<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$script = '' ;

if (($_SESSION['member_sales'] != 1) && ($_SESSION['member_pDep'] == 7)) {
	$script = 'window.open("../calendar/calendar.php","_blank") ;' ;
	$_SESSION['member_sales'] = 1 ;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" href="../css/colorbox.css" />
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />
<script src="../js/jquery-1.7.2.min.js"></script>

<script src="../js/jquery.colorbox.js"></script>
<{include file='meta.inc.tpl'}>
<script type="text/javascript" src="../js/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(document).ready(function() {
<?php
if (($_SESSION['member_sales'] != 1) && ($_SESSION['member_pDep'] == 7)) {
	$_SESSION['member_sales'] = 1 ;
	echo '$("#calender").submit();'."\n" ;
}
?>
	$("#tracking").submit() ;
}) ;
</script>
</head>
<body>
<form id="calender" method="post" action="../calendar/calendar.php" target="_blank">

</form>
<form id="tracking" method="post" action="salesTracking">

</form>

</body>
</html>