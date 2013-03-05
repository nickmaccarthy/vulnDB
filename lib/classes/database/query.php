<?php


class Database_Query {


    // What Query type we are
    protected $_type;

    // SQL Statement
    protected $_sql;

    // Quoted query params
    protected $_parameters = array();

    // If we will return an object of results, TRUE == return as object, FALSE == associative array
    protected $_as_object = FALSE;

    // Parameters for __construct when using object results
    protected $_object_params = array();

    public function __construct($type, $sql)
    {

        $this->_type = $type;
        $this->_sql = $sql;

    }


    public function bind($param, $var)
    {

        $this->_parameters[$param] = $var;

        return $this;

    }

    public function compile($db = NULL)
    {

        if ( ! is_object($db))
        {
            // Get the DB instance
            $db = Database::instance($db);
        }

        $sql = $this->_sql;

        if ( ! empty($this->_parameters))
        {

            $values = array_map(array($db, 'quote'), $this->_parameters);

            $sql = strtr($sql, $values);

        }

        return $sql;

    }

    public function execute($db = NULL, $as_object = NULL, $object_params = NULL)
    {

        if ( ! is_object($db))
        {
            // Get the DB instance
            $db = Database::instance($db);
        }

        /*
        if ( $as_object === NULL )
        {
            $as_object = $this->_as_object;

        }
        */

        if ( $object_params === NULL )
        {
            $object_params = $this->_object_params;
        }

        // Compile the query
        $sql = $this->compile($db);

        $result = $db->query($this->_type, $sql, $as_object, $object_params);

        // Were done here
        return $result;
    }
}
