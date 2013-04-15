<?php


class Config {

        public static function load($config, $directory = 'config')
        {
  
                if ( $config = vulndb_core::find_file($directory, $config))
                {

                    $config = require $config;

                    return $config;

                }

                return FALSE;
                
        }

}
