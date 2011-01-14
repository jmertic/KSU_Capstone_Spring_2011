<?php

// specify the REST web service to interact with
$url = 'http://ruttanvm.cs.kent.edu:4080/service/v2/rest.php';

// Open a curl session for making the call
$curl = curl_init($url);

// Tell curl to use HTTP POST
curl_setopt($curl, CURLOPT_POST, true);

// Tell curl not to return headers, but do return the response
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Set the POST arguments to pass to the Sugar server
$parameters = array(
    'user_auth' => array(
        'user_name' => 'class',
        'password' => md5('class123'),
        ),
    );
$json = json_encode($parameters);
$postArgs = 'method=login&input_type=JSON&response_type=JSON&rest_data=' . $json;
curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);

// Make the REST call, returning the result
$response = curl_exec($curl);
if (!$response) {
    die("Connection Failure.\n");
}

// Convert the result from JSON format to a PHP array
$result = json_decode($response);
if ( !is_object($result) ) {
    die("Error handling result.\n");
}
if ( !isset($result->id) ) {
    die("Error: {$result->name} - {$result->description}\n.");
}

// Get the session id
$sessionId = $result->id;
echo "Login Successful! Session ID is {$sessionId}\n";

// Now, let's add a new Accounts record
$parameters = array(
    'session' => $sessionId,
    'module' => 'Accounts',
    'name_value_list' => array(
        array('name' => 'name', 'value' => 'New Account'),
        array('name' => 'description', 'value' => 'This is an account created from a REST web services call'),
        ),
    );
$json = json_encode($parameters);
$postArgs = 'method=set_entry&input_type=JSON&response_type=JSON&rest_data=' . $json;
curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);

// Make the REST call, returning the result
$response = curl_exec($curl);
if (!$response) {
    die("Connection Failure.\n");
}

// Convert the result from JSON format to a PHP array
$result = json_decode($response);
if ( !is_object($result) ) {
    die("Error handling result.\n");
}
if ( !isset($result->id) ) {
    die("Error: {$result->name} - {$result->description}\n.");
}

// Get the newly created Account record id
$accountId = $result->id;
echo "Record created successfully! Account ID is {$accountId}\n";

// Now, let's add a new Contacts record
$parameters = array(
    'session' => $sessionId,
    'module' => 'Contacts',
    'name_value_list' => array(
        array('name' => 'first_name', 'value' => 'John'),
        array('name' => 'last_name', 'value' => 'Mertic'),
        ),
    );
$json = json_encode($parameters);
$postArgs = 'method=set_entry&input_type=JSON&response_type=JSON&rest_data=' . $json;
curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);

// Make the REST call, returning the result
$response = curl_exec($curl);
if (!$response) {
    die("Connection Failure.\n");
}

// Convert the result from JSON format to a PHP array
$result = json_decode($response);
if ( !is_object($result) ) {
    die("Error handling result.\n");
}
if ( !isset($result->id) ) {
    die("Error: {$result->name} - {$result->description}\n.");
}

// Get the newly created Contact record id
$contactId = $result->id;
echo "Record created successfully! Contact ID is {$contactId}\n";

// Now let's relate the records together
$parameters = array(
    'session' => $sessionId,
    'module_name' => 'Accounts',
    'module_id' => $accountId,
    'link_field_name' => 'contacts',
    'related_ids' => array($contactId),
    );
$json = json_encode($parameters);
$postArgs = 'method=set_relationship&input_type=JSON&response_type=JSON&rest_data=' . $json;
curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);

// Make the REST call, returning the result
$response = curl_exec($curl);
if (!$response) {
    die("Connection Failure.\n");
}

// Convert the result from JSON format to a PHP array
$result = json_decode($response);
if ( !is_object($result) ) {
    die("Error handling result.\n");
}

if ( $result->created == 1 ) {
    echo "Records related successfully!\n";
}
elseif ( $result->failed == 1 ) {
    echo "Error: Records not related successfully\n";
}

// Now read back the related records for this record to verify it
$parameters = array(
    'session' => $sessionId,
    'module_name' => 'Accounts',
    'module_id' => $accountId,
    'link_field_name' => 'contacts',
    );
$json = json_encode($parameters);
$postArgs = 'method=get_relationships&input_type=JSON&response_type=JSON&rest_data=' . $json;
curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);

// Make the REST call, returning the result
$response = curl_exec($curl);
if (!$response) {
    die("Connection Failure.\n");
}

// Convert the result from JSON format to a PHP array
$result = json_decode($response);
if ( !is_object($result) ) {
    die("Error handling result.\n");
}
if ( !isset($result->entry_list) ) {
    die("Error: {$result->name} - {$result->description}\n.");
}

$found = false;
foreach ( $result->entry_list as $record ) {
    if ( $record->id == $contactId ) {
        $found = true;
        break;
    }
}

if ( $found ) {
    echo "Found Contact ID {$contactId} related to Account ID {$accountId}!\n";
}
else {
    die("Error: Contact ID {$contactId} not related to Account ID {$accountId}!\n");
}
