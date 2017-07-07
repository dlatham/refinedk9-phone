<?php   
    
    include './config.php';
    // SEE IF THEY HAVE SELECTED SOMETHING
    $select = '';
    if (isset($_REQUEST['Digits'])){
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
            $greeting = "<Redirect method=\"GET\">events.php</Redirect>";
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