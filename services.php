<?php   
    
    // SEE IF THEY HAVE SELECTED SOMETHING
    $select = '*';
    if (isset($_REQUEST['Digits'])){
        $select = $_REQUEST['Digits'];
    }

    //ASSEMBLE THE GREETING

    switch ($select) {
        case '*':
            $greeting = "<Gather timeout=\"10\" numDigits=\"1\" finishOnKey=\"\">
                         <Play>./audio/services.mp3</Play>
                         <Pause length=\"5\"/>
                         </Gather>";
            break;
        case '#':
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