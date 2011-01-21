<?php

// specify the REST web service to interact with
$url = 'http://ruttanvm.cs.kent.edu:4080/service/v2/rest.php';
$user_name = 'class';
$user_password = 'class123';

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
        'user_name' => $user_name,
        'password' => md5($user_password),
        ),
    );
$json = json_encode($parameters);
$postArgs = 'method=login&input_type=JSON&response_type=JSON&rest_data=' . $json;
curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);

// Make the REST call, returning the result
$response = curl_exec($curl);

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

// Now, let's add a new Contacts record
$parameters = array(
    'session' => $sessionId,
    'module' => 'Contacts',
    'name_value_list' => array(
        array('name' => 'first_name', 'value' => 'John'),
        array('name' => 'last_name', 'value' => 'Mertic'),
        array('name' => 'email1', 'value' => 'jmertic@sugarcrm.com'),
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

// Get the newly created record id
$contactID = $result->id;
echo "Record created successfully! Contact ID is {$contactID}\n";

// Retieve the contact record we just created
$parameters = array(
    'session' => $sessionId, 
    'module_name' => 'Contacts', 
    'query' => "contacts.id = '$contactID'", 
    'order_by' => 'last_name', 
    'offset' => '',
    'select_fields' => array('first_name','last_name','email1'),
    'link_name_to_fields_array' => array(array('name' =>  'email_addresses', 'value' => array('id', 'email_address', 'opt_out', 'primary_address'))),
    );

$json = json_encode($parameters);
$postArgs = 'method=get_entry_list&input_type=JSON&response_type=JSON&rest_data=' . $json;
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
if ( !isset($result->result_count) ) {
    die("Error: {$result->name} - {$result->description}\n.");
}

var_dump($result);
