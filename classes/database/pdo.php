<?php


class Database_PDO extends Database {

    // Last insert ID used for Insert Statements, default false
    private $return_lastinsertid = false;


    private $prepare_insert = false;


    /**
     *  implement our parent class Database, and get connecteced to the database
     *
     *  @param  string  $name   db instance name
     *  @param  array   $config configuartion parameters
     *  @return void
     **/
    public function __construct($name, array $config)
    {
        
        parent::__construct($name, $config);

        $this->connect();

    }

    public function __destruct()
    {
        $this->_connection = NULL;
    }

    public function connect()
    {

        if ( isset( $this->_connection ) )
            return;

        $connection = $this->_config[$this->_instance]['connection'];

        $this->db_driver = $connection['db_driver'];
        $this->database = $connection['database'];
        $this->host = $connection['host'];
        $this->username = $connection['username'];
        $this->password = $connection['password'];
        $this->persistent = $connection['persistent'];

        // build our DSN -- todo:  add port compabilitiy
        $this->dsn = $this->db_driver.":"."host=".$this->host.";dbname=".$this->database;

        // Force PDO to use exceptions for all erros
        $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

        if ( ! empty($persistent))
        {
            // Make the connection persistent
            $options[PDO::PERSISTENT] = TRUE;
        }

        try {

            $this->_connection = new PDO($this->dsn, $this->username, $this->password, $options);

        } catch ( PDOException $e ) {

            throw new vulnDB_Exception("Unable to connect to database for database :database:\n\n Reason: :reason ", array(":database" => $this->database, ":reason" => $e->getMessage()) );
        }

        $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    public function query($type, $sql,array $params = NULL)
    {

        // Make sure we are connected to the DB
        $this->_connection or $this->connect();

        // Run the query
        try {
            $result = $this->_connection->query($sql);
        } catch ( Exception $e ) {
            throw new vulnDB_Exception("Error running DB Query\n\nReason: :reason\n\n SQL: :sql", array(':sql' => $sql, ':reason' => $e->getMessage()));
        }

        $this->_last_query = $sql;

        if ( $type === Database::SELECT)
        {

            // Return back an assoc array of the results
            $result->setFetchMode(PDO::FETCH_ASSOC);
        
            $result = $result->fetchAll();
                
            return $result;

        }
        elseif( $type === Database::INSERT)
        {
            return array(
                    $this->_connection->lastInsertId(),
                    $result->rowCount(),
                );
        }
        else
        {
            return $result->rowCount();

        }


    }

    public function begintransaction($mode = NULL)
    {

        // Ensure we are connected to the DB
        $this->connection or $this->_connect();

        return $this->_connection->beginTransaction();

    }

    public function commit()
    {
        // Ensure we are connected to the DB
        $this->connection or $this->_connect();

        return $this->_connection->commit();

    }

    public function rollback()
    {

        // Ensure we are connected to the DB
        $this->connection or $this->_connect();

        return $this->_connection->rollBack();

    }


    public function insert($query)
    {

        $this->_query = $query;

        $this->return_lastinsertid = true;

        return $this;

    }

    public function prepare($query, $type = NULL)
    {

        $this->_query = $query;

        if ( $type === "insert")
        {    
            $this->prepare_insert = true;
        }

        return $this;
    }

    public function delete($query)
    {

        $this->_query = $query;

        return $this;

    }

    public function escape($value)
    {

        // Make sure we are connected to the DB
        $this->_connection or $this->connect();

        return $this->_connection->quote($value);

    }

    public function bind($param,  $var)
    {

        $this->_parameters[$param] = $var;

        return $this;
    }


    public function execute()
    {

        try {

            $stmt = $this->_connection->prepare($this->_query);

            if ( is_array($this->_parameters))
            {
                if ( $this->prepare_insert === TRUE )
                {
                    try {
                        $stmt->execute($this->_parameters);
                        return array("rowcount" => $stmt->rowCount());
                    } catch ( PDOException $e ) {
                        throw new vulnDB_Exception (" Unable to select with bound variables: " . $e->getMessage());

                    }

                }
                else 
                {
                    try {

                        $stmt->execute($this->_parameters);

                        $stmt->setFetchMode(PDO::FETCH_ASSOC);

                        $results = $stmt->fetchAll();

                        return $results;
                        
                         
                    } catch ( PDOException $e ) {
                        throw new vulnDB_Exception ( "Unable to insert with bound variables : " . $e->getMessage());
                    }
                }
            } 
            else 
            {
                try {
                    $stmt->execute();
                    return array("rowcount" => $stmt->rowCount());
                } catch ( PDOException $e ) {
                    throw new vulnDB_Exception ( "Unable to insert : " . $e->getMessage());
                }
            }

        } catch ( PDOException $e ) {
            throw new vulnDB_Exception( "Unable to bind value PDO insert on {$this->database}: " . $e->getMessage() );
        }


    }
}
