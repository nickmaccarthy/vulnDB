<?php


class CSV {


    protected $_handle = NULL;

    // Extract headers by default
    public $extract_headers = TRUE;    

    // How many rows we quene up before we flush to the DB
    public $insert_threshold = 2000;
    
    protected $_output_file = '';

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

    /**
     *
     *  Writes an array to a CSV file
     *  If you have an associative array, then this method will get the 'headers' from the first array element
     *  Otherwise you can define your own headers in $headers
     *
     *  Usage:
     *      CSV::factory()->set_out_file('/somwhere/out.csv')->write($data_array);
     *
     *  @param  array   $data   data to be written to the CSV
     *  @param  array   $headers    headers to be written to CSV
     *  @return bool
     *
     **/ 
    public function write(array $data, array $headers = NULL)
    {

        $fp = fopen($this->_output_file, 'w+');

        // Use the headers if defined, otherwise, get them from the first array element 
        if ( count($headers) )
        {
            $headers = $headers;
        }
        else
        {
            $headers = array_keys($data[0]);

        }

        // Write the headers
        fputcsv($fp, $headers);


        // Now write the rest of the data
        foreach ( $data as $row )
        {
            
            fputcsv($fp, array_values($row));

        }

        fclose($fp);

        return true;
    }

    public function extract_headers()
    {

        $this->extract_headers = TRUE;

        return $this;
    }

    public function set_output_file($file)
    {
        $this->_output_file = $file;
        
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
