<?php

class Database_Truncate extends Database_Query {


    public function __construct($table = NULL)
    {
       
        if ( $table )
        {
            // Set our table name
            $this->_table = $table;

        }
    }

    public function compile( $db = NULL )
    {
       
        if ( ! is_object($db))
        {
            // Get connected to the DB
            $db = Database::instance($db);

        }

        // Start the insert query
        $query = 'TRUNCATE TABLE ' .$db->quote_table($this->_table);
       
        $this->_sql = $query;

        return parent::compile($db);
    } 
}
