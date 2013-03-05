<?php


class Logger {

    public static $date_format = "Y-m-d h:i:s T";

    public static function msg($type, $msgs)
    {

        foreach ( $msgs as $key => $val )
        {

            $formatted_msg[] = $key."=".'"'.$val.'"';
        }

        $msg = date(Logger::$date_format) . ' type="'.$type.'" '. implode(" ", $formatted_msg) . "\n";

        echo $msg;

        $filename = "vulndb_updater_".date("Y-m-d").".log";

        $fp = fopen(LOGPATH . $filename, 'a+');

        fwrite($fp, $msg);

        fclose($fp);

        return true;
    }

}
