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
*       This script is what does the main manual scan data updating for vulnDB.  It can pull in scan data for multiple accounts for a specified timeframe.
*       If run daily, it will continually pull in new scans that have ran.  
*
*
*       Usage:
*   
*       Define timeframe in config/vulndb.php. Note that you only get 300 API calls per day, and each scan download takes 1 API call.  So if you have alot
*       of scans in a timeframe, you may run out of API calls for that period.  The time period can be changed at any time.  So if you started off with a 
*       shorter timeframe, and changed it to a longer timeframe the next day, vulnDB will still pull in those older scans.  You can keep doing this until you
*       have all the scans in for your particular timeframe.
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

$vdb_config = Config::load('vulndb');

$timeframe = date('Y-m-d', strtotime($vdb_config['scan_timeframe']));

$insert_model = Model::factory('vulndb_insert');
$vulndb = Model::factory('vulndb_main');

Logger::msg('info', array('message' => 'Starting the vulnDB updater'));

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


    Logger::msg('info', array('account' => $account_name, 'message' => "starting with account $account_name"));
    Logger::msg('info', array('account' => $account_name, 'message' => "polling scan list", 'timeframe' => $timeframe, 'api_call' => 'pollscans', 'api_version' => 2));

    $scanlist_xml = $api2->pollscans($timeframe);

    $scanlist = parse::scanlist($scanlist_xml);

    $scans_in_vulndb = $vulndb->getscans($account_name);

    // Filter out the scans we already have vulnDB and move onto scans we need to get
    $scans_to_get = $vulndb->prunescanlist($scanlist, $account_name);

    // Lets get those scans
    if ( count($scans_to_get))
    {
        foreach ( $scans_to_get as $scan_to_get )
        {
            
            $scanstatus = $scan_to_get['SCAN_STATUS'];
            $scanid = (string) $scan_to_get['SCAN_ID'];
            $scantitle = (string) $scan_to_get['SCAN_TITLE'];
            $scandate = (string) $scan_to_get['SCAN_DATE'];

            $scantypestoget = $vdb_config['scan_types_to_get'];

            if ( in_array($scanstatus, $scantypestoget))
            {

                Logger::msg('info', array('account' => $account_name, 'message' => "downloading scan", 'scan_id' => $scanid, 'scan_title' => $scantitle, 'scan_date' => $scandate));

                $filename = "$account_name-".str_replace("/", "_", $scanid) . ".csv";

                $report_path = REPORTPATH.DIRECTORY_SEPARATOR.$account_name;

                if ( ! is_dir($report_path))
                {
                    Logger::msg("info", array('message' => 'creating directory', 'directory' => $report_path));

                    mkdir($report_path, 0700);
                }

                $output_path = $report_path . DIRECTORY_SEPARATOR . $filename;

                // Download the scan and store it in our $output_path defined above
                Logger::msg('info', array('message' => 'downloading scan', 'scan_id' => $scanid, 'scan_title' => $scantitle, 'scan_date' => $scandate, 'scan_status' => $scanstatus, 'api_call' => 'downloadscan', 'api_version' => 2));
                $download_scan = $api2->downloadscan($scanid, 'extended', 'csv', array('return_stream' => true, 'return_file' => true, 'output_filename' => $output_path));

                if ( $download_scan )
                {    
                    Logger::msg('info', array('account' => $account_name, 'message' => "scan successfully downloaded", 'scan_id' => $scanid, 'scan_title' => $scantitle, 'scan_date' => $scandate));
                }
                else
                {
                    Logger::msg('info', array('account' => $account_name, 'message' => "scan was unable to be downloaded", 'scan_id' => $scanid, 'scan_title' => $scantitle, 'scan_date' => $scandate));
                }

                // Lets parse the scan and put it into the database
                $insert = CSV::factory()
                            ->insert($output_path, array('vulndb_insert', 'scan'), array("SCAN_ID" => $scanid, "SCAN_DATE" => $scandate, "DATE_ENTERED" => $now, "ACCOUNT" => $account_name));

               
                if ( $insert )
                {
                    Logger::msg('info', array('account' => $account_name, 'message' => 'scan successfully inserted in vulnDB', 'scan_id' => $scanid, 'scan_title' => $scantitle, 'scan_status' => $scanstatus, 'rows_inserted' => $insert));

                    // Write the scan details into vulnDB
                    $insert_details = $insert_model->scan_details($scan_to_get, array('ACCOUNT' => $account_name, "DATE_ENTERED" => $now));

                } else {
                    Logger::msg('info', array('account' => $account_name, 'message' => 'unable to insert scan into vulnDB', 'scan_id' => $scanid, 'scan_title' => $scantitle, 'scan_status', $scanstatus));
                }

            }
            elseif ( $scanstatus = 'RUNNING')
            {
                // We can't pull in running scans, nor do we want to put that entry into the db
                continue;
            }
            else
            {
                Logger::msg('info', array('account' => $account_name, 'message' => 'scan status did not match $scantypestoget and will not be pulled in', 'scan_id' => $scanid, 'scan_title' => $scantitle, 'scan_status' => $scanstatus));
                $insert_details = $insert_model->scan_details($scan_to_get, array("ACCOUNT" => $account_name, "DATE_ENTERED" => $now));

            }


        }

    }
    else
    {
        Logger::msg('info', array('account' => $account_name, 'message' => "no scans found for this account.  moving onto the next one"));
    }

    // Delete AGs for account so we always have a fresh copy
    // If you want to keep a trail for the AG's, just comment this out
    $deleted_ags = DB::query(Database::DELETE, "DELETE FROM " . MAIN_AG_TABLE . " WHERE ACCOUNT=:account")
        ->bind(':account', $account_name)
        ->execute();

    // Pull in the asset groups for the account
    Logger::msg('info', array('message' => 'downloading asset groups', 'api_call' => 'get_asset_groups', 'api_version' => 1));
    $ags = $api1->get_asset_groups($url1, $username, $password);

    // Put AG's into vulnDB
    $insert = $insert_model->ags($ags, $account_name);

    Logger::msg('info', array('message' => 'asset groups successfully entered into vulndb', 'account' => $account_name));


    // release the api classes
    unset($api1);
    unset($api2);

} // end foreaach($accounts...)


Logger::msg('info', array('message' => 'Ending vulnDB updater'));


