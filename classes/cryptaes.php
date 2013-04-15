<?php

define('ENC_KEY', CryptAES::getkey());

if ( ENC_KEY === '' )
{
    die("There is no Encryption Key Set.\nPlease define an encryption key in config/crypt.php using your own string.\nExiting\n");
}

class CryptAES{

        public static function encrypt($text)
        {

                // AES 256
                return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, ENC_KEY, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
        }

        public static function decrypt($text)
        {

                $key = CryptAES::getkey();

                return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, ENC_KEY, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
        }


        public static function getkey()
        {
            
            $config = Config::load('crypt');

            return $config['key'];

        }
}


