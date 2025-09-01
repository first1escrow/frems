<?php
include_once '../session_check.php' ;

require('push.php');
$beaconpush = new BeaconPush();

//send_to_channel($channel, $event, array $data = array())
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Send msg</title>
</head>

<body>
<?php
// Add user to the channel "theBestChannel"
//$beaconpush ->add_channel('theBestChannel');

// Send an event (+data) to all users in the channel "theBestChannel"
$beaconpush ->send_to_channel('mychannel', 'newMessage', array('message' => '訊息通知：出帳資料有退件！'));
?>
</body>
</html>
