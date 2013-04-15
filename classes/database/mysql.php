<?php

class Database_MySQL extends Database {

    // Database in use by each connection
    protected static $_current_databases = array();

    protected $_connection_id;


    public function connect()
    {

        if ( $this->_connection )
            return;

        $connection = $this->_config[$this->_instance]['connection'];

        $this->db_driver = $connection['db_driver'];
        $this->database = $connection['database'];
        $this->host = $connection['host'];
        $this->username = $connection['username'];
        $this->password = $connection['password'];
        $this->persistent = $connection['persistent'];

        try {
            if ( $this->persistent )
            {
                $this->_connection = mysql_pconnect($this->host, $this->username, $this->password);
            }
            else
            {
                $this->_connection = mysql_connect($this->host, $this->username, $this->password);
            }
        } catch ( Exception $e ) {

            $this->_connection = NULL;

            throw new vulnDB_Exception("Unable to connect to database: :error", array(':error' => $e->getMessage()), $e->getCode());

        }

        $this->_select_db($this->database);


    }


    public function _select_db($database)
    {

        if ( ! mysql_select_db($database, $this->_connection))
        {
            
            throw new vulnDB_Exception("Unable to select database: :error", 
                                            array(":error" => mysql_error($this->_connection)),
                                            mysql_errno($this->_connection));
        }

        Database_MySQL::$_current_databases[$this->_connection_id] = $database;
       
    }


    public function disconnect()
    {
        try
        {
            // Database is assumed disconnected
            $status = TRUE;

            if (is_resource($this->_connection))
            {
                if ($status = mysql_close($this->_connection))
                {
                    // Clear the connection
                    $this->_connection = NULL;

                    // Clear the instance
                    parent::disconnect();
                }
            }
        }
        catch (Exception $e)
        {
            // Database is probably not disconnected
            $status = ! is_resource($this->_connection);
        }

        return $status;
    }

    public function query($type, $sql, $as_object = FALSE, array $params = NULL)
    {
        // Make sure the database is connected
        $this->_connection or $this->connect();
 

        // Execute the query
        if (($result = mysql_query($sql, $this->_connection)) === FALSE)
        {

            throw new vulnDB_Exception('Unable to make query: :error [ :query ]',
                                           array(':error' => mysql_error($this->_connection), ':query' => $sql),
                                            mysql_errno($this->_connection));
        }

        if ( $type === Database::SELECT )
        {
            return $result;
        }
        elseif ( $type === Database::INSERT )
        {

            return array(
                    mysql_insert_id($this->_connection),
                    mysql_affected_rows($this->_connection),
                );
        }
        else
        {
            return mysql_affected_rows($this->_connection);
        }
    }

    public function escape($value)
    {

       // Make sure the database is connected
        $this->_connection or $this->connect();

        if (($value = mysql_real_escape_string( (string) $value, $this->_connection)) === FALSE)
        {
            throw new vulnDB_Exception('Error escapting DB variable: :error [ :value ]',
                array(':error' => mysql_error($this->_connection), ':value' => $value),
                mysql_errno($this->_connection));
        }

        // SQL standard is to use single-quotes for all values
        return "'$value'"; 

    }
}
