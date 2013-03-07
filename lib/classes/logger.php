<?php


class Logger {

    public static $date_format = "Y-m-d h:i:s T";

    public static function msg($type, $msgs)
    {

        foreach ( $msgs as $key => $val )
        {

            $formatted_msg[] = $key."=".'"'.$val.'"';
        }

        $parent_script = $_SERVER['SCRIPT_NAME'];
        $parent_class = get_parent_class();

        $msg = "[ ". date(Logger::$date_format) . " ] - log_type=\"$type\" script=\"$parent_script\" ". implode(" ", $formatted_msg) . "\n";

        echo $msg;

        $filename = "vulndb_updater_".date("Y-m-d").".log";

        $fp = fopen(LOGPATH . $filename, 'a+');

        fwrite($fp, $msg);

        fclose($fp);

        return true;
    }

}
