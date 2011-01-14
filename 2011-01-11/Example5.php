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

// Now, let's see what fields are in the Accounts module
$parameters = array(
    'session' => $sessionId,
    'module_name' => 'Accounts',
    );
$json = json_encode($parameters);
$postArgs = 'method=get_module_fields&input_type=JSON&response_type=JSON&rest_data=' . $json;
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
if ( !isset($result->module_name) ) {
    die("Error: {$result->name} - {$result->description}\n.");
}

// Iterate over the fields and display them
echo "Module: {$result->module_name}\nFields:\n";
foreach ($result->module_fields as $name => $properties) {
    echo "\t{$name}\n";
}
echo "Relationships:\n";
foreach ($result->link_fields as $name => $properties) {
    echo "\t{$name}";
    if ( !empty($properties->module) ) {
        echo " ( {$properties->module} )";
    }
    echo "\n";
}
