<?php


class vulnDB_Exception extends Exception {


    public function __construct($message = "", array $variables = NULL, $code = 0, Exception $previous = NULL)
    {

        // Set the message
        $message = $this->replace_params($message, $variables);

        // Pass the message and integer code to the parent
        parent::__construct($message, (int) $code, $previous);
        
        $this->code = $code;

    }

    
    public function __toString()
    {
        return VulnDB_Exception::text($this);
    }

    /**
     * Get a single line of text representing the exception:
     *
     * Error [ Code ]: Message ~ File [ Line ]
     *
     * @param   Exception  $e
     * @return  string
     */
    public static function text(Exception $e)
    {
        return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
            get_class($e), $e->getCode(), strip_tags($e->getMessage()), $e->getFile(), $e->getLine());
    } 


    function replace_params($string, array $values = NULL)
    {
        return empty($values) ? $string : strtr($string, $values);
    }
}
