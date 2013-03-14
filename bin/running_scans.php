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
*   
*       Description:
*
*       This script is used to find running scans on each subscription as defined in your 'vulndb_users' table
*
*       It uses both API 1 and API 2 due to a bug/limitation in API 2 currently where the 'TARGETS' field is truncated prematurely.  
*       The TARGETS list is not truncated in the V1 call, but the V2 call has more info about the current running scan.  So I make both calls
*       And combine the TARGETS from V1 into the V2 results in the pasre::running_scans() method, and then place the results in the DB, noting if there 
*       are no scans running via the "NO_SCANS_RUNNING" text.
*
*
*       Usage:
*        
*       This script is useful when run as a CRON job so the running scans can be populated into the 'running_scans' table. 
*       This is useful for an organization that needs to know what scans are running without logging into Qualys.  The data
*       from this table is used in the frontend for vulnDB so users can quickly see if a particular host or hostname is being 
*       scanned without logging into Qualys. I run this script at half hour intervals via CRON, so when users on the front
*       end check, they hit my DB table instead of the Qualys API.  This of course gives them a half hour delay, but you only get 
*       300 API calls per day from Qualys and this particular script requires two API call each time it runs.
*
*
*
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

