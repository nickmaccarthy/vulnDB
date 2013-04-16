<?php


/**
 *
 *  Script to encrypt or decrypt text using our defined key in config/crypt.php 
 *
 *
 *  Usage: ( from the main vulnDB directory )
 *    php bin/util/kb_to_csv_converter.php /path/to/output/file/to.csv
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

if ( ! isset($argv[1]) || ! $argv[2] )
    Usage();

$output_file = $argv[1];




if ( $argv[2]  == "encrypt"){
    $encrypted_text = CryptAES::encrypt($argv[1]) . "\n" ;
    echo "
Encrypted Text: $encrypted_text
    ";
} else if ($argv[2] == "decrypt"){
    $decrypted_text = CryptAES::decrypt($argv[1]) . "\n";
    echo "
Decrypted Text: $decrypted_text
";
}

function Usage(){
    
echo " 
Please Supply text to be encrypted.


Usage:  php {$_SERVER['SCRIPT_NAME']} <text_to_encrypt> <method>('encrypt' or 'decrypt')

";


}
