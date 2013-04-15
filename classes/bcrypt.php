<?php

class Bcrypt {


    public static function hash($input, $work_factor = 12)
    {


        if ( version_compare(PHP_VERSION, '5.3') < 0 ) throw new Exception( "Bcrypt requires PHP 5.3 or above" );

        if ( CRYPT_BLOWFISH != 1 )
        {
            throw new Exception (" Bcrypt is not supported in your installation.  It is required:  See http://php.net/crypt " );
        }

        if (! function_exists('openssl_random_pseudo_bytes')) 
        {
            throw new Exception('Bcrypt requires openssl PHP extension');
        }

        if ($work_factor < 4 || $work_factor > 31) $work_factor = 8;
        $salt = 
            '$2a$' . str_pad($work_factor, 2, 0, STR_PAD_LEFT) . '$' . 
            substr(
                strtr(base64_encode(openssl_random_pseudo_bytes(16)), '+', '.'), 0, 22
            );

        return crypt($input, $salt);
        
    }

    public static function check($input, $stored_hash)
    {

        if ( version_compare(PHP_VERSION, '5.3') < 0 ) throw new Exception( "Bcrypt requires PHP 5.3 or above" );

        return crypt($input, $stored_hash) == $stored_hash;
    }
}
