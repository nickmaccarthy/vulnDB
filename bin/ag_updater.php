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
*           This script will update the asset groups for an account or accounts
*
*       Usage:
*           Just run it from CLI
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

Logger::msg('info', array('message' => 'Starting the asset data report updater'));

$accounts = $vulndb->getaccounts();

foreach( $accounts as $account )
{

    $now = date('c');

    $account_name = $account['account'];

    $username = $account['username'];
    $password = CryptAES::decrypt($account['password']);

    $url1 = 'https://' . $account['api_url'] . '/msp/';
    $url2 = 'https://' . $account['api_url'] . '/api/2.0/fo/';

    $api1 = new QualysAPI_v1;
    $api2 = new QualysAPI_v2($url2, $username, $password); 

    // Delete AGs for account so we always have a fresh copy
    // If you want to keep a trail for the AG's, just comment this out
    $deleted_ags = DB::query(Database::DELETE, "DELETE FROM " . MAIN_AG_TABLE . " WHERE ACCOUNT=:account")
        ->bind(':account', $account_name)
        ->execute();

    // Pull in the asset groups for the account
    Logger::msg('info', array('message' => 'downloading asset groups', 'api_call' => 'get_asset_groups', 'api_version' => 1, 'account_name' => $account_name));
    $ags = $api1->get_asset_groups($url1, $username, $password);

    // Put AG's into vulnDB
    $insert = $insert_model->ags($ags, $account_name);

    Logger::msg('info', array('message' => 'asset groups successfully entered into vulndb', 'account_name' => $account_name));
}
