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

Logger::msg('info', array('message' => 'running scans updater complete'));

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

    Logger::msg('info', array('message' => 'polling scans', 'account' => $account_name));

    $scanlist_v2 = $api2->pollscans(date('Y-m-d', strtotime('-15 day')), array('state' => 'Running'));
    $scanlist_v1 = $api1->scan_running_list($url1, $username, $password); 

    $insert = $insert_model->running_scans($scanlist_v1, $scanlist_v2, $account_name);
    
    Logger::msg('info', array('message' => "running scans updated", 'account' => $account_name));
     
}

Logger::msg('info', array('message' => 'running scans updater complete'));

