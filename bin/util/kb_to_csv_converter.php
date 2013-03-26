<?php

/**
 *
 *  Script to export the Qualys Knowledge Base into a CSV file
 *
 *
 *  Usage: ( from the main vulnDB directory )
 *    php bin/util/kb_to_csv_converter.php /path/to/output/file/to.csv
 *
 *
 *
 **/


if ( ! isset($argv[1]) )
    Usage();

$output_file = $argv[1];


if ( ! is_file( $init_file = realpath(dirname(__FILE__))."/../../init.php"))
{
    echo "Could not find init.php, this file is requied for vulnDB to operate\n";
    exit(1);
}

require $init_file;

$kb_results = DB::query(Database::SELECT, "select * from qualys_kb")
                ->execute();


$write = CSV::factory()
            ->set_output_file($output_file)
            ->write($kb_results);

echo "KB successfully exported to $output_file\n";


function Usage()
{
    echo "\n\n";
    echo "Please specify the output file\n";
    echo "Usage:  php /bin/util/kb_to_csv_converter.php /path/to/output/file/to.csv\n";
    echo "\n\n";
    die();
}
