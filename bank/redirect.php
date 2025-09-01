<?php
include_once '../session_check.php' ;

$fn = $_REQUEST["fn"];
//if ($fn == 'quit_pay') {
//	header("Location: http://first.twhg.com.tw/bank/list_ok.php");
//}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>REDIRECT</title>
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<?php if ($fn == 'quit_pay') { ?>
<script>
parent.$.fn.colorbox.close(); 
</script>
<?php } ?>
</head>

<body>
</body>
</html>
