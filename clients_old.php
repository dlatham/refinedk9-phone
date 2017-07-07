<?php 
include './config.php';

//FIRST SEE IF A SELECTION WAS MADE
$select = "0";
if (isset($_REQUEST['Digits'])){
    $select = $_REQUEST['Digits'];
}

//NOW AVOID THE CRM CALL IF WE DON'T NEED IT
if($select == 4){
    $greeting = "<Redirect method=\"GET\">hello.php</Redirect>";
} else {


    $from = ltrim($_REQUEST['From']," +1");
    $xmlurl = 'https://crm.zoho.com/crm/private/xml/Leads/searchRecords?newFormat=1&authtoken=' . $zoho_auth .'&scope=crmapi&criteria=(Phone:'.$from.')';
    $xml = simplexml_load_string(file_get_contents($xmlurl), null, LIBXML_NOCDATA);
    $data = ($xml->xpath('/response/result/Leads/row'));
    $arrVariables = array("LEADID", "First Name", "Email", "Dog Name");
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

    //Wev've got all the names so now lets see how many we have
    
    if(count($retArr)==1){
        if($select=="0"){
    	   $greeting = "<Gather timeout=\"10\" numDigits=\"1\">
                     <Say>Welcome back " . $retArr[0]['First Name'] . ". Thanks for calling refined canine. We're excited to be working with you and " . $retArr[0]['Dog Name'] . ". Press 1 to confirm or schedule a training session. Press 2 to be connected to your trainer. Press 3 to provide us feedback on our services or leave us a message. Press 4 to return to the main menu.</Say>
                     </Gather>";
        } elseif ($select=="1"){
            $greeting = "<Redirect method=\"GET\">events.php?LEADID=" . $retArr[0]['LEADID'] . "</Redirect>";      
        } else {
            $greeting = "<Say>Something went wrong</Say>";
        }
    } else {
    	$greeting = "<Gather timeout=\"10\" numDigits=\"10\" finishOnKey=\"*\">
                     <Say>We can’t recognize your phone number as one of our existing clients. If you used a different phone number when signing up, please enter that number with area code first, now. Otherwise press star to return to the main menu.</Say>
                     </Gather>";
    }

}    

    // now greet the caller
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <?php echo $greeting ?>
</Response>