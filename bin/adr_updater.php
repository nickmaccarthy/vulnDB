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
*       This script will download and insert an asset data report for a given report template.  The report template ID needs to be defined
*       in config/vulndb.php for the account and report template ID you wish to pull in.  Asset data report is the API call being used here, but it
*       is essentialy a report template.  I find this extremely useful for reports we need to track and trend since Qualys doesnt do that very well. 
*
*       Usage:
*
*       Define your report template ID's in config/vulndb.php by Account for ones you wish to pull in.  Note, You can have multiple report templates per account.  
*       Your actual report templates ( and corresponding report template ID ) are defined in the Qualys GUI.  Create your report template or choose and 
*       exiting one and get the report tempalte ID from there.
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


    Logger::msg("info", array("message" => "fetching report list", 'account' => $account_name, 'api_call' => 'report_template_list', 'api_version' => 1));

    $report_template_xml = $api1->report_template_list($url1, $username, $password);

    // Check that we got valid XML
    if ( ! $vulndb->is_xml($report_template_xml) )
    {
        Logger::msg("error", array('message' => 'what I got back from the API call was not XML'));
        Logger::msg("info", array('report_template_xml' => $report_template_xml));
        exit();
    }

    // Delete the current report templates in the table for this account
    $delete_templates = DB::query(Database::DELETE, "DELETE FROM ".REPORT_TEMPLATE_TABLE." WHERE ACCOUNT = :account")
                    ->bind(":account", $account_name)
                    ->execute();

    Logger::msg("info", array( "message" => "deleted report templates", "table" => REPORT_TEMPLATE_TABLE, "account" => $account_name, "rows_deleted" => $delete_templates));

    // Put the report template list in the DB
    $insert = $insert_model->report_templates($report_template_xml, $account_name);

    Logger::msg("info", array( "message" => "report templates inserted for account", "table" => REPORT_TEMPLATE_TABLE, "account" => $account_name, "rows_inserted" => $insert));

    // Now lets download our reports defined in config/vulndb.php

    // Get the reports defined in config/vulndb.php
    $report_ids = $vdb_config['adr_reports'][$account_name];

    // Go get 'em
    $adr_xml = ""; 
    foreach ( $report_ids as $report_id )
    {
        Logger::msg("info", array("message" => "downloading asset data report", "report_id" => $report_id, "account" => $account_name, "api_call" => "asset_data_report", "api_version" => 1));
         
        $adr_xml = $api1->asset_data_report($url1, $username, $password, array("template_id" => $report_id));

        // Check that we got valid XML
        if ( ! $vulndb->is_xml($adr_xml) )
        {
            Logger::msg("error", array('message' => 'what I got back from the API call was not XML'));
            Logger::msg("info", array("asset_data_report_xml" => $adr_xml));
            exit();
        }

        Logger::msg("info", array("message" => "asset data report download sucessful", "report_id" => $report_id, "account" => $account_name, "api_call" => "asset_data_report", "api_version" => 1));
        Logger::msg("info", array("message" => "inserting asset data report into vulnDB", "report_id" => $report_id, "account" => $account_name, "api_call" => "asset_data_report", "api_version" => 1));

        $insert = $insert_model->asset_data_report($adr_xml, array( "account_name" => $account_name, "report_template_id" => $report_id));

        Logger::msg("info", array("message" => "asset data report inserted", "report_id" => $report_id, "account" => $account_name, "rows_inserted" => $insert));

    }


}

Logger::msg("info", array("message" => "asset data report updater is complete"));


