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
            $greeting = "<Redirect method=\"GET\">events.php</Redirect>";
            break;
        case '2':
            $greeting = "<Redirect method=\"GET\">visitor.php</Redirect>";
            break;
        case '3':
            $greeting = "<Redirect method=\"GET\">information.php</Redirect>";
            break;
    } 

    // now greet the caller
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <?php echo $greeting ?>
</Response>