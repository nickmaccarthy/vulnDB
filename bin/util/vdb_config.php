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
*       This script is used to edit account details for vulnDB. It can be used to change passwords,
*       api urls etc.  It can also be used to view the account details and the decrypted password for the account.
*
*       Usage:
*
*       Simply run the script on CLI and follow the interface
*
*
*
*
**/

if ( ! is_file( $init_file = realpath(dirname(__FILE__))."/../../init.php"))
{
    echo "Could not find init.php, this file is requied for vulnDB to operate\n";
    exit(1);
}

require $init_file;

$vulndb = Model::factory('vulndb_main');

$accounts = $vulndb->getaccounts(); 

$exit = false;

echo "


               __         ______    _______  
 .--.--..--.--.|  |.-----.|   _  \  |   _   \ 
 |  |  ||  |  ||  ||     ||.  |   \ |.  1   / 
  \___/ |_____||__||__|__||.  |    \|.  _   \ 
                          |:  1    /|:  1    \
                         |::.. . / |::.. .  /
                          `------'  `-------' 

    Configuration tool to edit account settings and details


";

while ( ! $exit )
{
    echo "Please choose and account to edit:\n";
    echo "\n";

    $c = 1;
    foreach ( $accounts as $account )
    {
        
        echo "\t$c.) {$account['account']}\n";

        $c++;
    }
    echo "\t8.) Exit\n";

    echo "\n";
    echo "Please choose number:\n";

    $account = trim(fgets(STDIN));

    $account = (int) $account;

    if ( ! is_int($account))
    {
           echo "Please choose a number above\n";
           die();
    }

    if ( $account == 8 ) exit("Adios!\n");

    @$account = $account - 1;


    // Set our account
    if ( ! @$account = $accounts[$account] ) 
    {
        
        echo "Sorry can't find that account\n";
        break;
    }

    
    echo "\n\n\n\n";

    $back = false;
    while ( ! $back )
    {

        echo "What would you like to do?\n";
        echo "\n";

        echo "\t1.) View Account Details\n";
        echo "\t2.) Edit Account Name\n";
        echo "\t3.) Edit Account Username\n";
        echo "\t4.) Update/change Account Password\n";
        echo "\t5.) View Account Password\n";
        echo "\t6.) Update/change API URL\n";
        echo "\t7.) Go Back / Cancel\n";

        echo "\n";
        echo "Please choose:\n";

        $edit_choice = trim(fgets(STDIN));


        switch($edit_choice){

            case 1:
                foreach ( $account as $key => $val)
                {
                    echo "$key -- $val\n";
                }
                break;

            case 2:

                echo "Please enter the new name for this account:\n";
                $new_name = trim(fgets(STDIN));
                echo "Are you sure you want to update the name for {$account['account']}?\n";
                echo "1.) Yes\n";
                echo "2.) Cancel\n";
                $choice = trim(fgets(STDIN));
                if ( $choice == 1 )
                {
                    $update = $vulndb->update_account_name($account['account'], $new_name);
                    if($update)
                        echo "Name successfully updated to $new_name for {$account['account']}\n";
                }
                else
                {
                   break; 
                }

                break;

            case 3: 
                echo "Please enter a new username:\n";
                $new_username =  trim(fgets(STDIN));
                echo "\n\n";

                echo "Are you sure you want to update the username name for {$account['account']}?\n";
                echo "1.) Yes\n";
                echo "2.) Cancel\n";
                $choice = trim(fgets(STDIN));
                if ( $choice == 1 )
                {
                    $update = $vulndb->update_account_username($account['account'], $new_username);
                    if($update)
                        echo "Username successfully updated to $new_username for {$account['account']}\n";
                }
                else
                {
                   break; 
                }

                break;

            case 4:
                echo "Please enter new password:\n";
                $new_password = trim(fgets(STDIN));
                $new_enc_password = CryptAES::encrypt($new_password);
                echo "\n\n";
                echo "Are you sure you want to update the password for {$account['account']}?\n";
                echo "1.) Yes\n";
                echo "2.) Cancel\n";
                echo "Choice:\n";
                $choice = trim(fgets(STDIN));
                if ( $choice == 1 )
                {
                    $update_pw = $vulndb->update_account_password($account['account'], $new_enc_password);
                    if($update_pw)
                        echo "Password successfully updated for {$account['account']}\n";
                }
                else
                {
                   break; 
                }

                break;

            case 5:
                echo CryptAES::decrypt($account['password']) . "\n";;
                break;

            case 6:
                echo "Please enter new URL for API without https:// - ex qualysapi.qualys.com\n";
                $new_url = trim(fgets(STDIN));
                echo "\n\n";
                echo "Are you sure you want to update the API URL for {$account['account']}?\n";
                echo "1.) Yes\n";
                echo "2.) Cancel\n";
                echo "Choice:\n";
                $choice = trim(fgets(STDIN));
                if ( $choice == 1 )
                {
                    $update = $vulndb->update_account_url($account['account'], $new_url);
                    if($update)
                        echo "URL successfully updated for {$account['account']}\n";
                }
                else
                {
                   break; 
                }

                break;
                
            case 7:
                $back = true;
                break;


        }

        echo "\n\n";
    }


}


