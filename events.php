<?php 
include './config.php';
date_default_timezone_set('America/Los_Angeles');

//First see if we need to go back
if(isset($_REQUEST['Digits'])&&($_REQUEST['Digits']=="*")){
    $greeting = "<Redirect method=\"GET\">clients.php</Redirect>";
}

$leadid = $_REQUEST['LEADID'];
//$xmlurl = './eventdata.txt';
$xmlurl = 'https://crm.zoho.com/crm/private/xml/Events/getRelatedRecords?newFormat=1&authtoken=' . $zoho_auth . '&scope=crmapi&id=' . $leadid . '&parentModule=Leads';
$xml = simplexml_load_string(file_get_contents($xmlurl), null, LIBXML_NOCDATA);
#$xml = simplexml_load_string(file_get_contents('eventdata.txt'), null, LIBXML_NOCDATA);
#$xml = simplexml_load_string(file_get_contents('errordata.txt'), null, LIBXML_NOCDATA);
$data = ($xml->xpath('/response/result/Events/row'));
$arrVariables = array("ACTIVITYID", "Subject", "Start DateTime", "End DateTime");
$retArr = NULL;

    foreach($data as $row)                      // Iterate over all the results
    {
        foreach($arrVariables as $arrVar)       // Iterate through the variables we're looking for
        {
            $rowData = ($row->xpath('FL[@val="'.$arrVar.'"]'));
            @$arrReturn[$arrVar] = (string)$rowData[0][0];
        }
        $retArr[] = $arrReturn;
    }

    //CONFIRM THERE ARE CALENDAR EVENTS
    //echo count($retArr);
    if(count($retArr)==0){
    //if(true){ //SHORT CIRCUITING THIS UNTIL WE'RE READY
        $greeting = "<Gather timeout=\"10\" numDigits=\"1\">
                                <Say>You have no upcoming appointments scheduled. Press 1 to schedule an appointment or press star to return to the menu.</Say>
                        </Gather>";
        //$greeting = "<Say>Confirming appointments is currently under development for current clients. Please check back soon for updates.</Say><Redirect method=\"GET\">clients.php</Redirect>";
    } else {
        //LOOP THROUGH THE ARRAY OF EVENTS AND LOOK FOR DAYS IN THE FUTURE
        $today = new DateTime('NOW');
        $i = 0;
        foreach ($retArr as $row) {
            $event = new DateTime($row['Start DateTime']);
            //echo date('c');
            $interval = $today->diff($event);
            //echo $interval->format('%R %d, ');
            
            if($interval->format('%R')=='+'){
                $events[$i]['ACTIVITYID'] = $row['ACTIVITYID'];
                $events[$i]['Subject'] = $row['Subject'];
                $events[$i]['Start DateTime'] = $row['Start DateTime'];
                $events[$i]['DaysUntil'] = $interval->format('%a');
                $i++;
            }
            //echo $interval->format('%R%a days');
            

        }
        //print_r($events);
        //echo count($events);

        //if there are no events in the future then say so and exit the if/then
        if(count($events)==0){
            $greeting = "<Gather timeout=\"10\" numDigits=\"1\">
                                <Say>You have no upcoming appointments scheduled. Press 1 to schedule an appointment or press star to return to the menu.</Say>
                        </Gather>";
        } else {


            
            //ASSEMBLE THE GREETING
            //check for pagination and an action
            $page = (isset($_REQUEST['page']) ? $_REQUEST['page'] : 0);
            $select = (isset($_REQUEST['Digits']) ? $_REQUEST['Digits'] : 0);

            if(($select == 4) && ($page>0)){
                $page--;
            } elseif (($select == 6) && ((count($events) - $page) > 1)) {
                $page++;
            }

            if($page==0){
                $preamble = "You have " . count($events) . " upcoming " . (count($events)==1 ? "appointment. " : "appointments. ") . "Your next scheduled session is ";
                $back = "";
            } else {
                $preamble = "You have a session ";
                $back = " Press 4 to go to the previous appointment.";
            }

            //echo (count($events) - $page);
            if((count($events) - $page) > 1){
                $forward = " Press 6 to skip to the next appointment.";
            } else {
                $forward = "";
            }

            //$back = ($page == 0 ? "" : " Press 4 to go to the previous appointment.");
            //$forward = (($page+1) < count($events) ? " Press 6 to skip to the next appointment." : "");
            $event = new DateTime($events[$page]['Start DateTime']);
            $greeting = "<Gather timeout=\"10\" numDigits=\"1\">
                                <Say>" . $preamble . $events[$page]['DaysUntil'] . ($events[$page]['DaysUntil'] == 1 ? " day" : " days") . " away on " . date_format($event, 'l, F jS \a\t g:i a') . ". Press 1 to repeat this appointment, 2 to make changes." . $back . $forward . " Or press star to return to the menu.</Say>
                        </Gather>";
        }
    }
    //echo $greeting;

    //GREET THE CALLER
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <?php echo $greeting; ?>
</Response>