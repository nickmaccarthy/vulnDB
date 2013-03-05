<?php


class CSV {


    protected $_handle = NULL;

    // Extract headers by default
    public $extract_headers = TRUE;    

    // How many rows we quene up before we flush to the DB
    public $insert_threshold = 2000;

    public static function factory()
    {
        
          return new CSV;
           
    }   
    
    public function insert($file, array $callback, $scan_info)
    {

        $class = $callback[0];
        $method = $callback[1];

        $class = Model::factory($class);

        $this->_handle = fopen($file, 'r');

        $headers = fgetcsv($this->_handle, $file);

        $scan_data = array();

        $file = new SplFileObject($file);

        $file->setFlags(SplFileObject::SKIP_EMPTY);
        $file->setFlags(SplFileObject::READ_AHEAD);
        $file->setFlags(SplFileObject::READ_CSV);

        $file->setCsvControl(",", '"',  "\"");

        $c = 0;
        foreach ( $file as $row )
        {
            $c++;

            if ( count($row) === count($headers))
            {
                $scan_data[] = array_combine($headers, $row);
                $row = array();
            }

            if ( $c % $this->insert_threshold==0)
            {
                Logger::msg('info', array('message' => 'flushing '.$this->insert_threshold.' rows', "class" => $callback[0], "method" => $callback[1], 'rows_inserted' => $c));
                Logger::msg('info', array('memory_usage' => $this->file_size(memory_get_usage())));

                $flush = $class->$method($scan_data, $scan_info);
                $scan_data = array();
            }
        } 
        $flush = $class->$method($scan_data, $scan_info);
        $scan_data = array();

        Logger::msg('info', array('memory_usage' => $this->file_size(memory_get_usage())));
        
        return $c;
    }
    
    public function parse($file)
    {

        $this->_handle = fopen($file, 'r');
            
        if ( $this->extract_headers === TRUE )
        {

            $headers = fgetcsv($this->_handle, $file);

            while ( $line = fgetcsv($this->_handle, $file ))
            {
                $rows[] = array_combine($headers, $line);
            }

            return $rows;
        }
        else
        {
            while ( $line = fgetcsv($this->_handle, $file ))
            {
                $rows[] = $line;
            }

            return $rows;
        }
        
    } 

    public function extract_headers()
    {

        $this->extract_headers = TRUE;

        return $this;
    }

    public function set_insert_threshold($threshold)
    {

        $this->insert_threshold = $threshold;

        return $this;
    }


    public function file_size($size)
    {
            $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");

            return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) .$filesizename[$i] : '0 Bytes';

    }
}
