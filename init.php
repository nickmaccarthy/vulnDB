<?php 

// Defines our "lib" path where will hold any libraries we need
$lib = 'lib';

// Defines our "config" path where we will hold our configs
$config = 'config';

// Defines our "log" path where will store log messages
$log = "log";

// Defines our "reports" path where will store scan report and the such
$reports = "reports";


/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 **/
define('EXT', '.php');

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// Set the lib to the docroot
if ( ! is_dir($lib) AND is_dir(DOCROOT.$lib))
    $lib = DOCROOT.$lib;

// Set the config dir to the docroot
if ( ! is_dir($config) AND is_dir(DOCROOT.$config))
    $config = DOCROOT.$config;

// Set the config dir to the docroot
if ( ! is_dir($log) AND is_dir(DOCROOT.$log))
    $log = DOCROOT.$log;

// Set the report dir to the docroot
if ( ! is_dir($reports) AND is_dir(DOCROOT.$reports))
    $reports = DOCROOT.$reports;


/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @link http://www.php.net/manual/errorfunc.configuration#ini.error-reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 *
 * When using a legacy application with PHP >= 5.3, it is recommended to disable
 * deprecated notices. Disable with: E_ALL & ~E_DEPRECATED
 */
error_reporting(E_ALL | E_STRICT);


define('LIBPATH', realpath($lib).DIRECTORY_SEPARATOR);
define('CONFIGPATH', realpath($config).DIRECTORY_SEPARATOR);
define('LOGPATH', realpath($log).DIRECTORY_SEPARATOR);
define('REPORTPATH', realpath($reports).DIRECTORY_SEPARATOR);



// Load the main vulndb_core class
require(LIBPATH . "classes/vulndb/core" . EXT);

// Set our auto loader
spl_autoload_register(array('vulndb_core', 'auto_load'));

/**
 *
 *
 *  DB Tables mappings/constants
 *
 *  You can change the mapping names in config/db_tables.php, but do not change the CONSTANTS
 *
 *
 */ 
$db_tables = Config::load('db_tables');

// VulnDB Core tables
define("MAIN_SCAN_RUN_TABLE", $db_tables['vulndb']['scan_run']); // AKA the 'SCAN_LIST' table, details about every scan that has ran
define("MAIN_SCAN_RESULTS_TABLE", $db_tables['vulndb']['scan_results']); // All Data from a scan
define("MAIN_AG_TABLE", $db_tables['vulndb']['asset_groups']); // Asset Groups for Qualys Account
define("MAIN_QUALYS_KB_TABLE", $db_tables['vulndb']['qualys_kb']); // Qualys KB table

// Asset data report/report template tables
define("ADR_HOSTS_TABLE", $db_tables['vulndb']['adr_hosts']);
define("ADR_VULNS_TABLE", $db_tables['vulndb']['adr_vulns']);
define("ADR_AG_TABLE", $db_tables['vulndb']['adr_asset_groups']);
define("REPORT_TEMPLATE_TABLE", $db_tables['vulndb']['report_templates']);

// Logins
define("LOGINS_TABLE", $db_tables['vulndb_users']['logins']);

// End DB Table mappings/constants
