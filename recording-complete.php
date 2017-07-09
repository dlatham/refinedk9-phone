<?php
//This code is called by recordingStatusCallback to deliver the recording URL via email once a new message is received
include './config.php';
//date_default_timezone_set($timezone);
date_default_timezone_set('America/Los_Angeles');

//First, assemble and error check all the necessary URL parameters
$messageurl = $_REQUEST['RecordingUrl'];


// See if this caller is in the CRM based on a phone number search
$from = $_REQUEST['from']; //Confirm that this is the variable being passed
$from = ltrim($from," +1");

//First check the leads
$xmlurl = "https://crm.zoho.com/crm/private/xml/Leads/searchRecords?newFormat=1&authtoken=" . $zoho_auth . "&scope=crmapi&criteria=(Phone:" . $from . ")";
$xml = simplexml_load_string(file_get_contents($xmlurl), null, LIBXML_NOCDATA);
$data = ($xml->xpath('/response/result/Leads/row'));
$arrVariables = array("LEADID", "First Name", "Last Name", "City", "Dog Name", "Dog Breed");
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


//If no leads result, check the accounts
if(count($retArr)==0){
	$xmlurl = "https://crm.zoho.com/crm/private/xml/Contacts/searchRecords?newFormat=1&authtoken=" . $zoho_auth . "&scope=crmapi&criteria=(Phone:" . $from . ")";
	$xml = simplexml_load_string(file_get_contents($xmlurl), null, LIBXML_NOCDATA);
	$data = ($xml->xpath('/response/result/Contacts/row'));
	$arrVariables = array("CONTACTID", "First Name", "Last Name", "City", "Dog Name", "Dog Breed");
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

//If no accounts result, send a unknown caller email
if(count($retArr)==0){
	$subject = "New Refined K-9 voicemail from: " . $from;
	$message = "<html><body>Received: " . date('Y-m-d') . "<br />Caller: ". $from . " (Number not found in the CRM)<br /><br />Audio: <a href='" . $messageurl . "'>" . $messageurl . "</a><br /><br />Additional Tools<br /><a href='https://crm.zoho.com/new/'>Add this phone caller to the CRM</a><br /><a href='https://twilio.com/block'>Block this caller from making phone calls</a></body></html>";
} else {
	$subject = "New Refined K-9 voicemail from: " . $retArr[0]['Last Name'] . ", " . $retArr[0]['First Name'];
	$message = "<html><body>Received: " . date('Y-m-d') . "<br />Caller: " . $retArr[0]['Last Name'] . ", " . $retArr[0]['First Name'] . "<br />Phone: " . $from . "<br />Dog Name: " . $retArr[0]['Dog Name'] . "<br />Dog Breed: " . $retArr[0]['Dog Breed'] . "<br />City: " . $retArr[0]['City'] . "<br /><br />Audio: <a href='" . $messageurl . "'>" . $messageurl . "</a><br /><br />Additional Tools<br /><a href='https://crm.zoho.com/crm/EntityInfo.do?module=" . ($retArr[0]['LEADID']!=NULL ? "Leads&id=" . $retArr[0]['LEADID'] : "Contacts&id=" . $retArr[0]['CONTACTID'] ) . "'>View the contact in the CRM</a></body></html>";

}
sendConfirmation($emailVoicemailsTo, $subject, $message);


function sendConfirmation($email,$subject,$message) {
	$headers = 'From: Refined K-9 Phone System <no-reply@refinedk9.com>' . "\r\n" .
    'Reply-To: no-reply@refinedk9.com\r\n';
    $headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    mail($email,$subject,$message,$headers);
}

?>