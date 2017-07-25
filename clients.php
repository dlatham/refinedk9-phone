<?php   
    
    include './config.php';
    // SEE IF THEY HAVE SELECTED SOMETHING - if '*' is selected then just default to no entry to repeat this menu
    $select = '';
    if ((isset($_REQUEST['Digits'])) && ($_REQUEST['Digits']!='*')) {
        $select = $_REQUEST['Digits'];
    }

    //ASSEMBLE THE GREETING

    switch ($select) {
        case '':
            $greeting = "<Gather timeout=\"10\" numDigits=\"1\" finishOnKey=\"\">
                         <Play>./audio/clients.mp3</Play>
                         <Pause length=\"5\"/>
                         </Gather>";
            break;
        case '1':
            //They wanto review their existing events so we need to check and see if they are in the CRM and then redirect with the appropriate LEADID (contacts not supported yet)
            $greeting = "<Redirect method=\"POST\">crm_id.php?forwardurl=events.php&returnurl=clients.php</Redirect>";
            break;
        case '2':
            //Direct to Ken
            $greeting = "<Say>Please wait a moment, connecting you to " . $trainer1_name . "</Say><Dial>" . $trainer1_phone . "</Dial>";
            break;
        case '3':
            //Direct to Jose
            $greeting = "<Say>Please wait a moment, connecting you to " . $trainer2_name . "</Say><Dial>" . $trainer2_phone . "</Dial>";
            break;
        case '4':
            //Direct to Theo
            $greeting = "<Say>Please wait a moment, connecting you to " . $trainer3_name . "</Say><Dial>" . $trainer3_phone . "</Dial>";
            break;
        case '0':
            //Recording goes here
            $greeting = "<Say>Please leave your message after the tone...</Say><Record recordingStatusCallback=\"recording-complete.php?from=" . $_REQUEST['From'] . "\" recordingStatusCallbackMethod=\"GET\" maxLength=\"120\" /><Say>I did not receive your recording.</Say>";
            break;
        case '#':
            $greeting = "<Redirect method=\"GET\">hello.php</Redirect>";
    } 

    // now greet the caller
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <?php echo $greeting ?>
</Response>