<?php


abstract class Database {

    // Query Types
    const SELECT = 1;
    const INSERT = 2;
    const UPDATE = 3;
    const DELETE = 4;

    public static $default = 'vulndb';

    public static $instance = '';

    public static $instances = array();

    // Last query executed
    public $last_query;

    // Char used for quote identifiers, aka, quoted results
    public $_identifier = "'";

    protected $_instance;

    protected $_connection;



    /**
     * Get a singleton Database instance. If configuration is not specified,
     * it will be loaded from the database configuration file using the same
     * group as the name.
     *
     *     // Load the default database
     *     $db = Database::instance();
     *
     *     // Create a custom configured instance
     *     $db = Database::instance('custom', $config);
     *
     * @param   string   $name    instance name
     * @param   array    $config  configuration parameters
     * @return  Database
     */
    public static function instance( $name = NULL, array $config = NULL)
    {

        Database::$instance = $name;

        if ( $name === NULL )
        {
            $name = Database::$default;
        }

        if ( ! isset(Database::$instances[$name]))
        {

            if ( $config === NULL )
            {
                // Load the config for this DB
                $config = Config::load('database');
            }


            // Set the driver
            $driver = 'Database_'.$config[$name]['type'];

            $driver = new $driver($name, $config);

            // Store the database instance
            Database::$instances[$name] = $driver;
        }

        return Database::$instances[$name];

    }

    
    public function __construct($name, array $config)
    {
        // Set the instance name
        $this->_instance = $name;

        // Store the config locally
        $this->_config = $config;

    }

    public function __destruct()
    {
        $this->disconnect();
    }
    public function disconnect()
    {
        unset(Database::$instances[$this->_instance]);

        return TRUE;
    }


    abstract public function query($type, $sql, $as_object = FALSE, array $params = NULL);


    public function quote($value)
    {

        if ( $value === NULL)
        {
            return 'NULL';
        }
        elseif ( $value === TRUE)
        {   
            return "'1'";
        }
        elseif ( $value === FALSE)
        {
            return "'0'";
        }

        if ( is_array($value))
        {
            return '('.implode(', ', array_map(array($this, __FUNCTION__), $value)).')';
        }
        elseif ( is_int($value))
        {
            return (int) $value;
        }
        elseif ( is_float($value))
        {
            // Covert it to no-locale aware to prevent possible commas
            return sprintf('%F', $value);
        }

        return $this->escape($value);

    }


    public function quote_column($column)
    {

        return '`'.$column.'`';

    }

    public function quote_table($table)
    {

        return '`'.$table.'`';

    }


    abstract public function escape($value);

}
