<?php
/**
*
*
*               __         ______    _______  
* .--.--..--.--.|  |.-----.|   _  \  |   _   \ 
* |  |  ||  |  ||  ||     ||.  |   \ |.  1   / 
*  \___/ |_____||__||__|__||.  |    \|.  _   \ 
*                          |:  1    /|:  1    \
*                         |::.. . / |::.. .  /
*                          `------'  `-------' 
*
*   Written by Nick MacCarthy
*       nickmaccarthy@gmail.com
*       http://www.nickmaccarthy.com
*   
*       Released under the MIT license
*
*       2013
*
**/
if ( ! is_file( $init_file = realpath(dirname(__FILE__))."/../init.php"))
{
    echo "Could not find init.php, this file is requied for vulnDB to operate\n";
    exit(1);
}

require $init_file;


$insert_model = Model::factory('vulndb_insert');
$vulndb = Model::factory('vulndb_main');
$vdb_config = Config::load('vulndb');

$accounts = $vulndb->getaccounts();

foreach ( $accounts as $account )
{

    $now = date('c');

    $account_name = $account['account'];

    $username = $account['username'];
    $password = CryptAES::decrypt($account['password']);

    $url1 = 'https://' . $account['api_url'] . '/msp/';
    $url2 = 'https://' . $account['api_url'] . '/api/2.0/fo/';

    $api1 = new QualysAPI_v1;
    $api2 = new QualysAPI_v2($url2, $username, $password); 

    
    // Get a list of upcomming scans
    $upcoming = $api1->scheduled_scans($url1, $username, $password, array( "active" => "yes", "type" => "scan" ));

    $insert = $insert_model->scheduled_scans($upcoming, $account_name);


}
