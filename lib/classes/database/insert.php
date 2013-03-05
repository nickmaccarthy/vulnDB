<?php


class Database_Insert extends Database_Query {

    // INSERT INTO
    protected $_table;

    // ( ... )
    protected $_columns = array();

    // VALUES ( ... )
    protected $_values = array();

    
    public function __construct($table = NULL, array $columns = NULL)
    {

        if ( $table )
        {
            // Set our table name
            $this->_table = $table;
        }

        if ( $columns ) 
        {
            // Set the column names
            $this->_columns = $columns;
        }


    }

    /**
     * Adds or overwrites values. Multiple value sets can be added.
     *
     * @param   array   $values  values list
     * @param   ...
     * @return  $this
     */
    public function values(array $values)
    {
        if ( ! is_array($this->_values))
        {
            throw new vulnDB_Exception('INSERT INTO ... SELECT statements cannot be combined with INSERT INTO ... VALUES');
        }

        // Get all of the passed values
        $values = func_get_args();

        $this->_values = array_merge($this->_values, $values);

        return $this;

    }


    public function compile( $db = NULL )
    {


        if ( ! is_object($db))
        {
            // Get connected to the DB
            $db = Database::instance($db);

        }

        // Start the insert query
        $query = 'INSERT INTO ' .$db->quote_table($this->_table);

        // Add the column names
        $query .= ' ('.implode(', ', array_map(array($db, 'quote_column'), $this->_columns)).') ';

        if ( is_array($this->_values))
        {

            // Callback for quoting values
            $quote = array($db, 'quote');

            $groups = array();
            foreach ($this->_values as $group)
            {
                foreach ($group as $offset => $value)
                {
                    if ((is_string($value) AND array_key_exists($value, $this->_parameters)) === FALSE)
                    {
                        // Quote the value, it is not a parameter
                        $group[$offset] = $db->quote($value);
                    }
                }

                $groups[] = '('.implode(', ', $group).')';
            }

            // Add the values
            $query .= 'VALUES '.implode(', ', $groups);

        }
        
        $this->_sql = $query;

        return parent::compile($db);
    }

    /**
     * 
     *  Resets all query values
     *  
     *  @return $this
     **/
    public function reset_values()
    {
        $this->_values = array();

        return $this;
    }

    /**
     *
     *  Resets all values
     *
     *  @return $this
     **/
    public function reset()
    {
        $this->_table = NULL;

        $this->_columns =
        $this->_values  = array();

        $this->_parameters = array();

        $this->_sql = NULL;

        return $this;
    } 
}
