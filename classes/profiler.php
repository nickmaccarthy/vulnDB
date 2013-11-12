<?php
/**
 * 
 *
 *  Profiler class for monitoring memory usage for a script
 *
 *
 *  Usage:
 *
 *  add this line to your script
 *
 *  $profiler = Profiler::factory()
 *
 *
 *  At any point you can get your current memory usage:
 *
 *  echo "Current Memory Usage: {$profiler->get_mem_usage()}";
 *
 *  # Current Memory Usage: 2.6Mb
 *
 *
 *  Todo:  Add output to logger class and add profiling hooks throughout the rest of this framework
 *
 **/

class Profiler {


    public static function factory()
    {

        return new Profiler;
    }

    public function __construct()
    {

        $this->init_memory = memory_get_usage(); 

    }


    public function get_memory_usage($human_readable = TRUE)
    {

                
        return $human_readable ? $this->convert(memory_get_usage()) : memory_get_usage();

    }


    public function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');

        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];

    }

}
