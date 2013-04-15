<?php


class DB {


    public static function query($type, $sql)
    {
        return new Database_Query($type, $sql);
    }

    public static function insert($table = NULL, array $columns = NULL)
    {
        return new Database_Insert($table, $columns);
    }

}
