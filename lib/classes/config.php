<?php


class Config {

        public static function load($config)
        {

                $config = require CONFIGPATH . strtolower($config) . EXT;

                return $config;
        }


}
