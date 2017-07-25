# Refined K-9 Phone IVR

A simple PHP application that provides a Twilio endpoint to power the Refined K-9 phone tree.

## Requirements:
 - Twilio voice number
 - Zoho CRM api access
 - PHP 5.3 minimum

## Setup:
 - Add the appropriate audio files to the ./audio folder
 - Rename the config-example.php to config.php and add the auth tokens.
 - Ensure the api subdomian has been added to the SSL cert
 - Ensure the the correct virtualhost is setup for the api endpoint in your apache configuration (Use https obviously)

## Changelog:
 - Added the ability to record voicemails and email multiple recipients the resulting audio file
 - v0.1: First production release which has limited the use of events as a future feature

## Feature Backlog
 - Get the leadid on the client.php call (no need to differentiate the menu however)
 - Add voicemails to the CRM entry
 - Extract timezone to the config file
 - Version for public launch / seasonal updates to recordings
 - Add encryption
 - Test transcription
 - Add SMS alert capability