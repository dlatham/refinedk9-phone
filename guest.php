<?php   
    
    // SEE IF THEY HAVE SELECTED SOMETHING
    $select = '#';
    if (isset($_REQUEST['Digits'])){
        $select = $_REQUEST['Digits'];
    }

    //ASSEMBLE THE GREETING

    switch ($select) {
        case '#':
            $greeting = "<Gather timeout=\"10\" numDigits=\"1\" finishOnKey=\"\">
                         <Play>./audio/guest.mp3</Play>
                         <Pause length=\"5\"/>
                         </Gather>";
            break;
        case '1':
            $greeting = "<Redirect method=\"GET\">services.php</Redirect>";
            break;
        case '0':
            //leave message here
            $greeting = "<Say>Please leave your message after the tone...</Say><Record recordingStatusCallback=\"recording-complete.php?From=" . $_REQUEST['From'] . "\" recordingStatusCallbackMethod=\"GET\" maxLength=\"120\" /><Say>I did not receive your recording.</Say>";
            break;
        case '*':
            $greeting = "<Redirect method=\"GET\">hello.php</Redirect>";
            break;
    } 

    // now greet the caller
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <?php echo $greeting ?>
</Response>