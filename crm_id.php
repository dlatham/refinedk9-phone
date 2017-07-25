<?php 
//Phone lookup in order to find the lead or contact id that should be passed back to another function
include './config.php';

//Parameter check
if((isset($_REQUEST['From'])) && (isset($_REQUEST['forwardurl'])) && (isset($_REQUEST['returnurl']))) {
	if(isset($_REQUEST['Digits']) && ($_REQUEST['Digits'] != '*')) { //See if they entered their phone number otherwise use the From parameter
		$from = $_REQUEST['Digits'];
	} else {
		$from = ltrim($_REQUEST['From']," +1");
	}
	$forwardurl = $_REQUEST['forwardurl'];
	$returnurl = $_REQUEST['returnurl'];
} else {
	//Error handling
	exit("There has been a parameter error.");
}

if(isset($_REQUEST['Digits']) && ($_REQUEST['Digits'] == '*')) {
	$greeting = "<Redirect method=\"GET\">" . $returnurl . "</Redirect>";

} else {

	//Check to see if it is a lead first
	$xmlurl = "https://crm.zoho.com/crm/private/xml/Leads/searchRecords?newFormat=1&authtoken=" . $zoho_auth . "&scope=crmapi&criteria=(Phone:" . $from . ")";
	$xml = simplexml_load_string(file_get_contents($xmlurl), null, LIBXML_NOCDATA);
	$data = ($xml->xpath('/response/result/Leads/row'));
	$arrVariables = array("LEADID");
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

	//If not then check to see if it is a contact
	if (count($retArr) == 0) {
		$xmlurl = "https://crm.zoho.com/crm/private/xml/Contacts/searchRecords?newFormat=1&authtoken=" . $zoho_auth . "&scope=crmapi&criteria=(Phone:" . $from . ")";
		$xml = simplexml_load_string(file_get_contents($xmlurl), null, LIBXML_NOCDATA);
    	$data = ($xml->xpath('/response/result/Contacts/row'));
    	$arrVariables = array("CONTACTID");
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
	}

	//If the lead or contact is found then handle the redirect otherwise say it wasn't found and return to previous
	if(count($retArr) == 0) {
		$greeting = "<Gather timeout=\"10\" numDigits=\"10\" finishOnKey=\"*\"><Say>This phone number wasn't found in our records. Enter the phone number associated with your account or press star to return to the menu.</Say></Gather>";
	} else {
		$greeting = "<Redirect method=\"GET\">" . $forwardurl . "?id=" . (isset($retArr[0]['LEADID']) ? $retArr[0]['LEADID'] : $retArr[0]['CONTACTID']) . "&type=" . (isset($retArr[0]['LEADID']) ? "lead" : "contact") . "</Redirect>";
	}
}

// now greet the caller
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <?php echo $greeting ?>
</Response>