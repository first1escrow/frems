<?php  
session_start ();  
require_once 'google-api-php-client/src/Google/autoload.php'; 

$includePath = ini_get('include_path').':'.dirname(__FILE__) ;
ini_set('include_path', $includePath); //動態設定php.ini

//$client_id = '565623508584-47h7jmq5fr9djh3tti51i4cr016datc5.apps.googleusercontent.com' ; //Client ID  
$client_id = '116908525821-p9j2pmvnftb1qe7i67u1voe1f3ncmgmo.apps.googleusercontent.com' ; //Client ID  
//$Email_address = '565623508584-47h7jmq5fr9djh3tti51i4cr016datc5@developer.gserviceaccount.com'; //Email Address  
$Email_address = '116908525821-p9j2pmvnftb1qe7i67u1voe1f3ncmgmo@developer.gserviceaccount.com'; //Email Address  
//$key_file_location = 'Calendar API-b08e400ff464.p12';  
$key_file_location = 'calendar-c84e5d0905f4.p12';  
//$calendar_id = '58eiv4suspcblu729g4he7vmes@group.calendar.google.com';
$calendar_id = 'cmc569@gmail.com';
//$calendar_id = '565623508584-47h7jmq5fr9djh3tti51i4cr016datc5@developer.gserviceaccount.com';
//$calendar_id = 'first1escrow@gmail.com';  
$params = array(
//CAN'T USE TIME MIN WITHOUT SINGLEEVENTS TURNED ON,
//IT SAYS TO TREAT RECURRING EVENTS AS SINGLE EVENTS
    'singleEvents' => true,
    'orderBy' => 'startTime',
    'timeMin' => date(DateTime::ATOM),//ONLY PULL EVENTS STARTING TODAY
'maxResults' => 7 //ONLY USE THIS IF YOU WANT TO LIMIT THE NUMBER
                  //OF EVENTS DISPLAYED
 
);

$client = new Google_Client ();  
$client->setApplicationName ( "Client_Library_Examples" );  
$key = file_get_contents ( $key_file_location );  
$scope = array('https://www.googleapis.com/auth/calendar');  
$cred = new Google_Auth_AssertionCredentials ( $Email_address, $scope, $key );  
  
$client->setAssertionCredentials ( $cred );  
if ($client->getAuth ()->isAccessTokenExpired ()) {  
    $client->getAuth ()->refreshTokenWithAssertion ( $cred );  
}  
//print_r($client) ; exit ;
//print_r($client->getAccessToken()); exit ;
$service = new Google_Service_Calendar($client);  
//print_r($service); exit ;

//$acl = $service->acl->listAcl($calendar_id);
/*
$acl = $service->acl->listAcl('primary');

foreach ($acl->getItems() as $rule) {
  echo $rule->getId() . ': ' . $rule->getRole();
}exit ;
*/
//$calendarList = $service->calendarList->listCalendarList();
//print_r($calendarList) ; exit ;


// 得到所有这个service account 被share的日历  
//$calList = $service->calendarList->listCalendarList();  
//print_r($calList) ;
/*
//新增行程
//Set the Event data
        $event = new Google_Service_Calendar_Event();
        $event->setSummary('新測試1');
        $event->setDescription('新測試描述1');
        $dat ='2015-03-25T15:00:00.000+08:00';
        $dat1 ='2015-03-25T15:30:00.000+08:00';
        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime($dat);
        $event->setStart($start);

        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime($dat1);
        $event->setEnd($end);

    // On printing $event you can get data that I am not understanding error

        //$createdEvent = $service->events->insert($calendar_id, $event);
        $createdEvent = $service->events->insert('primary', $event);
        var_dump($createdEvent);

################
*/

//$events = $service->events->listEvents('primary', $params);
$events = $service->events->listEvents('primary');
//$events = $service->events->listEvents($calendar_id, $params);

//檢視所有行程
foreach ($events->getItems() as $event) {
	print_r($event) ;
	//Convert date to month and day
	$eventDateStr = $event->start->dateTime;
	
	if(empty($eventDateStr)) {
		// it's an all day event
		$eventDateStr = $event->start->date;
	}
	
	$temp_timezone = $event->start->timeZone;

	if (!empty($temp_timezone)) {
		$timezone = new DateTimeZone($temp_timezone); //GET THE TIME ZONE
	}
	else {
		//Set your default timezone in case your events don't have one
		$timezone = new DateTimeZone("America/New_York");
	}
	
	$eventdate = new DateTime($eventDateStr,$timezone);
	
	$newmonth = $eventdate->format("M");//CONVERT REGULAR EVENT DATE TO LEGIBLE MONTH
	$newday = $eventdate->format("j");//CONVERT REGULAR EVENT DATE TO LEGIBLE DAY
	/*
	?>
	<div class="event-container">
		<div class="eventDate">
			<span class="month"><?=$newmonth?></span><br />
			<span class="day"><?=$newday?></span><span class="dayTrail"></span>
		</div>
		<div class="eventBody">
			<a href="<?=$event->htmlLink?>"><?=$event->summary;?></a>
		</div>
		</div>
<?php
*/
}
##

/*
$acl = $service->acl->listAcl('primary');

foreach ($acl->getItems() as $rule) {
	echo $rule->getId() . ': ' . $rule->getRole();
}
*/
?>  