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
*       Script to update the Qualys Knowledge base
*
*       Usage:
*   
*       Define a 'kb_account' in config/vulndb.php for which you want to pull the KB for.  This account should have access to this API call
*       and if it does not, the functionality can be added by your regional TAM or Qualys Support.
*
*
*
**/
if ( ! is_file( $init_file = realpath(dirname(__FILE__))."/../init.php"))
{
    echo "Could not find init.php, this file is requied for vulnDB to operate\n";
    exit(1);
}

ini_set('memory_limit', '512M');

require $init_file;

Logger::msg("info", array('message' => "KB updater starting"));

$vdb_config = Config::load('vulndb');

// Name of the account we will use to pull back in the KB
// Define it config/vulndb
$kb_account = $vdb_config['kb_account'];

$vulndb = Model::factory('vulndb_main');
$insert = Model::factory('vulndb_insert');

$account_info = $vulndb->getaccountinfo($kb_account);

$username = $account_info['username'];
$password = CryptAES::decrypt($account_info['password']);

$url1 = $account_info['url1'];

$api1 = new QualysAPI_v1;

Logger::msg('info', array('message' => "Qualys KB download beginning", 'api_call' => 'get_qualys_kb', 'api_version' => '1', 'kb_account' => $kb_account, 'account_username' => $username));

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

Logger::msg("info", array('message' => "Qualys KB table, ". MAIN_QUALYS_KB_TABLE . " truncated"));

Logger::msg("info", array('message' => "KB insert beginning"));

$go = $insert->kb($KB_XML);

Logger::msg("info", array('message' => "KB insert complete", "rows_inserted" => $go));

unset($api1);
