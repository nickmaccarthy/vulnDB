<?php require 'init.php';
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

Logger::msg("info", array('message' => "KB updater starting"));

$vdb_config = Config::load('vulndb');

// Name of the account we will use to pull back in the KB
// Define it config/vulndb
$kb_account = $vdb_config['kb_account'];

$vulndb = Model::factory('vulndb_main');
$insert = Model::factory('vulndb_insert');

$account_info = $vulndb->getaccountinfo($kb_account);

$username = $account_info['username'];
$password = $account_info['password'];

$url1 = $account_info['url1'];

$api1 = new QualysAPI_v1;

Logger::msg('info', array('message' => "Qualys KB download beginning", 'api_call' => 'get_qualys_kb', 'api_version' => '1'));

$KB_XML = $api1->get_qualys_kb($url1, $username, $password);

Logger::msg('info', array('message' => "Qualys KB download complete", 'api_call' => 'get_qualys_kb', 'api_version' => '1'));

// check the XML
if ( ! $vulndb->is_xml($KB_XML) )
{
    Logger::msg("error", array('message' => 'what I got back from the API call was not XML'));
    Logger::msg("info", array('kb_xml' => $KB_XML));
    exit();
}

// If we got good XML back, go ahead an truncate the current KB
$truncate = DB::query(Database::DELETE, "TRUNCATE " . MAIN_QUALYS_KB_TABLE)
                ->execute();

Logger::msg("info", array('message' => "Qualsy KB table, ". MAIN_QUALYS_KB_TABLE . " truncated"));

Logger::msg("info", array('message' => "KB insert beginning"));

$go = $insert->kb($KB_XML);

Logger::msg("info", array('message' => "KB insert complete", "rows_inserted" => $go));

unset($api1);
