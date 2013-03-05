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
*       2012
*
**/

$cwd = getcwd();

$ddl_dir = $cwd . DIRECTORY_SEPARATOR . "ddl" . DIRECTORY_SEPARATOR;

$our_config = "../config.json";

echo <<<MSG
                                                                                                           
                                           lllllll                  DDDDDDDDDDDDD      BBBBBBBBBBBBBBBBB   
                                           l:::::l                  D::::::::::::DDD   B::::::::::::::::B  
                                           l:::::l                  D:::::::::::::::DD B::::::BBBBBB:::::B 
                                           l:::::l                  DDD:::::DDDDD:::::DBB:::::B     B:::::B
vvvvvvv           vvvvvvvuuuuuu    uuuuuu   l::::lnnnn  nnnnnnnn      D:::::D    D:::::D B::::B     B:::::B
 v:::::v         v:::::v u::::u    u::::u   l::::ln:::nn::::::::nn    D:::::D     D:::::DB::::B     B:::::B
  v:::::v       v:::::v  u::::u    u::::u   l::::ln::::::::::::::nn   D:::::D     D:::::DB::::BBBBBB:::::B 
   v:::::v     v:::::v   u::::u    u::::u   l::::lnn:::::::::::::::n  D:::::D     D:::::DB:::::::::::::BB  
    v:::::v   v:::::v    u::::u    u::::u   l::::l  n:::::nnnn:::::n  D:::::D     D:::::DB::::BBBBBB:::::B 
     v:::::v v:::::v     u::::u    u::::u   l::::l  n::::n    n::::n  D:::::D     D:::::DB::::B     B:::::B
      v:::::v:::::v      u::::u    u::::u   l::::l  n::::n    n::::n  D:::::D     D:::::DB::::B     B:::::B
       v:::::::::v       u:::::uuuu:::::u   l::::l  n::::n    n::::n  D:::::D    D:::::D B::::B     B:::::B
        v:::::::v        u:::::::::::::::uul::::::l n::::n    n::::nDDD:::::DDDDD:::::DBB:::::BBBBBB::::::B
         v:::::v          u:::::::::::::::ul::::::l n::::n    n::::nD:::::::::::::::DD B:::::::::::::::::B 
          v:::v            uu::::::::uu:::ul::::::l n::::n    n::::nD::::::::::::DDD   B::::::::::::::::B  
           vvv               uuuuuuuu  uuuullllllll nnnnnn    nnnnnnDDDDDDDDDDDDD      BBBBBBBBBBBBBBBBB   

    Written by: Nick MacCarthy          nickmaccarthy@gmail.com     http://www.nickmaccarthy.com    2012    MIT License

                                    Welcome to the vulnDB setup script.

            I will need credentials for your root db user and I will take care of the rest.

            I will create a new database of your choosing (default 'vulnDB'), and a new user 'vulndb'.  

            I will then create the tables necessary for vulnDB to operate.

            After which I will create a config file called 'config.json' in the root directory (currently $cwd) of this app.  

            I will NOT keep or store your root credentials for any longer than this script runs.

            To get started, please enter in the information below:


MSG;

echo "\n\n";

die("Dont use this yet -- work in progress\n");

if ( ! is_file($our_config))
{
    $conf['salt']['default'] = generateSalt(25);

    fwrite(STDOUT, "1.) Please tell me the host where the database will reside (default '127.0.0.1'): \n");
    $dbhost = trim(fgets(STDIN));
    if ( ! empty($dbhost)) $conf['database']['dbhost'] = $dbhost;
    else $conf['database']['dbhost'] = "127.0.0.1";

    fwrite(STDOUT, "2.) Please tell me the username of an administrative DB user (default 'root'): \n");
    $db_root = trim(fgets(STDIN)) ? STDIN : "root";

    fwrite(STDOUT, "3.) Please tell me the password of an administrative DB user: \n");
    $db_root_pass = trim(fgets(STDIN));

    fwrite(STDOUT, "4.) Please tell me the name of the database you wish to create for vulnDB (default 'vulnDB') : \n");
    $dbname = trim(fgets(STDIN));
    if ( ! empty($dbname)) $conf['database']['dbname'] = $dbname;
    else $conf['database']['dbname'] = "vulnDB";

    fwrite(STDOUT, "5.) If you wish to have an alias in front of the table names, please tell me that now : \n");
    $t_alias = trim(fgets(STDIN));
    if ( ! empty($t_alias)) $conf['database']['table_alias'] = $t_alias . "_";
    else $conf['database']['table_alias'] = "";


    $conf['database']['dbuser'] = "vulndb";
    $conf['database']['dbpass'] = generateSalt(25);

    $dbh = mysql_connect($conf['database']['dbhost'], $db_root, $db_root_pass);
    echo "Creating database: {$conf['database']['dbname']}.\n";

    $vdb_create = mysql_query("CREATE DATABASE IF NOT EXISTS " . $conf['database']['dbname'] . ";", $dbh) or die(mysql_error());
    echo "Created database {$conf['database']['dbname']}\n";
    $vdb_user_create = mysql_query("GRANT ALL PRIVILEGES ON " . $conf['database']['dbname'] . ".* TO " . $conf['database']['dbuser'] . "@" . $conf['database']['dbhost'] . " IDENTIFIED BY '" . $conf['database']['dbpass'] . "';", $dbh) or die( mysql_error());
    echo "Created user {$conf['database']['dbuser']}\n";

    // Select our new DB
    mysql_select_db($conf['database']['dbname']);

    echo "Creating Tables\n";

    $d = dir($ddl_dir);
    while ( FALSE !== ($entry = $d->read()))
    {
        if ($entry == ".." | $entry == ".") continue;

        $table_short_name = str_replace(".sql", "", $entry);

        $our_table = $conf['database']['table_alias'].$table_short_name;
        $query = file_get_contents($ddl_dir.$entry);

        $query = preg_replace('/^CREATE TABLE `(\w+)`/', "CREATE TABLE IF NOT EXISTS `$our_table`", $query);

        $create = mysql_query($query) or die( "Error creating $our_table: " . mysql_error());

        echo "Table $our_table has been successfully created.\n";

        $conf['tables'][$table_short_name] = $our_table;
    }

    $default_install = str_replace("setup", "", $cwd);
    
    fwrite(STDOUT, "6.) What is the directory where this app is installed? ( default $default_install ) : \n");
    $app_root = trim(fgets(STDIN));
    if ( ! empty($app_root)) $conf['default']['app_root'] = $app_root;
    else $conf['default']['app_root'] = $default_install;

    // Defines our QualysAPI PHP Class
    $conf['default']['qualys_api_class'] = $conf['default']['app_root'] . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "qualys_api" . DIRECTORY_SEPARATOR . "qualys_api.php";
    // Defines custom functions
    $conf['default']['custom_functions'] = $conf['default']['app_root'] . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "custom_functions.php";

    fwrite(STDOUT, "How many Qualys accounts do you have?\n");
    $account_num = trim(fgets(STDIN));

    for ( $i = 1; $i <= $account_num; $i++)
    {
        fwrite(STDOUT, "Info for Account #$i\n");
        fwrite(STDOUT, "What name do you want to specify for this account in vulnDB? This is how vuln data will be seperated in vulnDB.\n");
        $account_name = trim(fgets(STDIN));

        fwrite(STDOUT, "What is the Qualys username you will using for the API calls? : \n");
        $conf['qualys']["$account_name"]['api_username'] = trim(fgets(STDIN)); 

        fwrite(STDOUT, "What is the password for the above Qualys API user? : \n");
        $conf['qualys']["$account_name"]['api_password'] = trim(fgets(STDIN));

        fwrite(STDOUT, "What should I use for the base URL for the API calls?  If you dont know, go with the default (default qualysapi.qualys.com ): \n");
        $qapi_baseurl = trim(fgets(STDIN));
        if ( ! empty($qapi_baseurl)) $conf['qualys']["$account_name"]['base_url'] = $qapi_baseurl;
        else $conf['qualys']["$account_name"]['base_url'] = "qualysapi.qualys.com";

        echo "\n\n";

    }

    echo "Thank you!  I will now write the config file.  Please check the README for next steps.\n";
    // Writes our our INI file
    write_json_config($conf, $our_config);

    echo "Will now create 'kickoff.sh', a shell script that can be used to automate vulnDB via a CRON job.\n";


$ksh = '#!/bin/bash

##
# You can use this shell script to kick off Updater scripts.
#
# Define full paths to the scripts below.
#
##
  
ROOT_DIR='.$conf['default']['app_root'].'
REPORT_DIR=$ROOT_DIR/reports
LOG_DIR=$ROOT_DIR/logs

echo "Updating Qualys KB"
php $ROOT_DIR/qualysKB_updater.php >> $LOG_DIR/qualysKB_updater-`date +"%Y-%m-%d%H:%M%SZ"`.log

echo "Running JSON vulnDB Updater"
php $ROOT_DIR/vulnDB_updater.php  >> $LOG_DIR/vulndb_updater-`date +"%Y-%m-%d%H:%M%SZ"`.log

';

    $ksh_filename = $conf['default']['app_root'].'/kickoff.sh';

    if ( ! $handle = fopen($ksh_filename, "w+")) echo "Uh ohh, cant open kickoff.sh"; 

    if ( ! fwrite($handle, $ksh)) echo "Uh off, can't write contents of ksh to kickoff.sh";

    fclose($handle);

    chmod($ksh_filename, 0754);

    echo "Kickoff.sh has been written!\n";
        
    echo "Setup is complete, please referer to README.md for the next steps!\n";
}
else 
{
    echo "Our Config: $our_config already exists, please remove this file to run the setup script again\n";
}

function generateSalt($length = 20) 
{
        $characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*?";
        $i = 0;
        $salt = "";
        while ( $i < $length)
        {
            $salt .= $characterList{mt_rand(0, (strlen($characterList) - 1))};
            $i++;
        }
        return $salt;
}

function write_json_config($conf_arr, $path)
{

    if ( ! $handle = fopen($path, "w+")) return false;

    $json = json_encode($conf_arr);

    $pretty_json = indent($json);

    if ( ! fwrite($handle, $pretty_json)) return false;

    fclose($handle);

    return true;
}

/**
 * Indents a flat JSON string to make it more human-readable.
 *
 * @param string $json The original JSON string to process.
 *
 * @return string Indented version of the original JSON string.
 */
function indent($json) {

    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;
        
        // If this character is the end of an element, 
        // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }
        
        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element, 
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }
            
            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }
        
        $prevChar = $char;
    }

    return $result;
}


