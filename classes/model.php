<?php

abstract class Model {

    /**
     * Create a new model instance
     *
     *
     * Usage:  $model = Model::factory($name);
     *
     * @param   string  $name   model name
     * @return  Model
     **/
    public static function factory($name)
    {
        $class = 'Model_'.$name;

        return new $class;

    }
}
