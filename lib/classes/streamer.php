<?php

class Streamer {


    protected $buffer;

    function stream_open($path, $mode, $options, $opened_path)
    {
        return true;
    }


    public function set_output_file($path, $filename, $mode)
    {

        $this->file_path = $path;
        $this->filename = $filename;
        $this->mode = $mode;

        $this->fp = fopen($this->file_path . $this->filename, $mode);


    }

    public function put_contents($fp)
    {

        fwrite($fp);

    }


    public function stream_write($data)
    {


            // Extract the lines
            $lines = explode("\n", $data);

            $lines[0] = $this->buffer . $lines[0];

            $nb_lines = count($lines);
            $this->buffer = $lines[$nb_lines - 1];
            unset($lines[$nb_lines - 1]);

            print_r($lines);
    }

}

