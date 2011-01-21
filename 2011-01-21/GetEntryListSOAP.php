<?php

// specify the SOAP web service to interact with
$url = 'http://ruttanvm.cs.kent.edu:4080/service/v2/soap.php?wsdl';
$user_name = 'class';
$user_password = 'class123';

$soapClient = new SoapClient($url,array('trace'=>1));

// Login
try { 
    $info = $soapClient->login(
        array(
            'user_name' => $user_name,
            'password'  => md5($user_password), 
            )
        );
} 
catch (SoapFault $fault) { 
    die("Sorry, the service returned the following ERROR: ".$fault->faultcode."-".$fault->faultstring."."); 
}

// Get the session id
$sessionId = $info->id;
echo "Login Successful! Session ID is {$sessionId}\n";

// Now, let's add a new Contacts record
try { 
    $info = $soapClient->set_entry(
        $sessionId,
        'Contacts',
        array(
            array(
                'name' => 'first_name',
                'value' => 'John',
                ),
            array(
                'name' => 'last_name',
                'value' => 'Mertic',
                ),
            array(
                'name' => 'email1',
                'value' => 'jmertic@sugarcrm.com',
                ),
            )
        );
}
catch (SoapFault $fault) { 
    die("Sorry, the service returned the following ERROR: ".$fault->faultcode."-".$fault->faultstring."."); 
}

// Get the newly created record id
$contactID = $info->id;
echo "Record created successfully! Contact ID is {$contactID}\n";

// Retieve the contact record we just created
try { 
    $info = $soapClient->get_entry_list(
        $sessionId, 
        'Contacts',
        "contacts.id = '{$contactID}'",
        'last_name',
        '',
        array('last_name', 'first_name', 'email1'),
        array(array('name' =>  'email_addresses', 'value' => array('id', 'email_address', 'opt_out', 'primary_address')))
        );
}
catch (SoapFault $fault) { 
    die("Sorry, the service returned the following ERROR: ".$fault->faultcode."-".$fault->faultstring."."); 
}

var_dump($info);
