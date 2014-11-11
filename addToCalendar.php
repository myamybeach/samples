<?php
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
Zend_Loader::loadClass('Zend_Http_Client');

$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
$user = '   ** google userid **  ';
$pass = '   ** google password **  ';
$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
$gdataCal = new Zend_Gdata_Calendar($client);

$event= $gdataCal->newEventEntry();

// Set the date using RFC 3339 format.
$timestamp = str_replace('-', '/', $startDate);

if (($timestamp = strtotime($timestamp.' '.$startTime.$sAP)) === false) {
    echo '<script language="JavaScript">alert("'.$startDate.' is not a valid date");';
    echo 'history.go(-1);</script>';
} else {
    if ($timestamp < time() ) {
       echo '<script language="JavaScript">alert("'.$startDate.' is in the past");';
       echo 'history.go(-1);</script>';
    } else {
       $startDate = date('Y-m-d', $timestamp);
    }
}
echo '<br>startDate: '.$startDate;

$startTime=date('H:i',$timestamp);

echo '<br>startTime: '.$startTime;

$timestamp = strtotime($startDate.' '.$endTime.$eAP);
$endTime=date('H:i',$timestamp);
echo '<br>endTime: '.$endTime;
$endDate = $startDate;

$sponsor=str_replace("'","\'",$sponsor);
    $query = "INSERT INTO trainingCalendar SET day=1,course_id=$courseID,sponsor='$sponsor', date='$startDate',starttime='$startTime',endtime='$endTime',location_id=$locationID,maxStudents=$maxStudents,cost=$cost";
    $result = mysql_query($query) or die("Query failed: $query");
    $key = mysql_insert_id();

    $register = 'Click here to register: <a href="http://www.6pinternational.com/register.php?course='.$key.'">www.6pinternational.com/register.php?course='.$key.'</a>';
    $description = $register.'<br><br>'.$description;

    $query = "SELECT * FROM trainingCenters where id=$locationID";
    $result = mysql_query($query) or die("Query failed");
    $num_rows = mysql_num_rows($result);

   if (!$result) {
        echo "Could not successfully run query ($sql) from DB: " . mysql_error();
        exit;
    }
    
    if ($num_rows == 0) {
        echo "Item not found in database: $query";
        exit;
    }
    $row = mysql_fetch_assoc($result);
    $location = $row["name"];
    $street1 = $row["street1"];
    $street2 = $row["street2"];
    $city = $row["city"];
    $state = $row["state"];
    $zip = $row["zip"];

    if ($street1>'') {
       $location = $location.', '.$street1;
    }
    if ($street2>'') {
       $location = $location.', '.$street2;
    }
    if ($city>'') {
       $location = $location.', '.$city;
    }
    if ($state>'') {
       $location = $location.' '.$state;
    }
    if ($zip>'') {
       $location = $location.', '.$zip;
    }

if ($sponsor>'') {
   $name=$name.'<br>Sponsored by: '.$sponsor;
}

$event->title = $gdataCal->newTitle($name);
$event->where = array($gdataCal->newWhere($location));
$event->content = $gdataCal->newContent($description);

$when = $gdataCal->newWhen();
$when->startTime = "{$startDate}T{$startTime}:00.000";
$when->endTime = "{$endDate}T{$endTime}:00.000";
$event->when = array($when);
$newEvent = $gdataCal->insertEvent($event,'http://www.google.com/calendar/feeds/5sgg01bk5r5qc1dlm4eohf15o0@group.calendar.google.com/private/full');

echo '<br>event added';

?>
