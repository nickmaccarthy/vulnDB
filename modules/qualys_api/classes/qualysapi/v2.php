<?php
/*
    API V2:
        // Initialize the class
        $api2 = new QualysAPI_v2($url, $username, $password, $client_name);  // This will get us logged in and the session set upon __construct();

        // make an API call
        $poll_scans = $api2->PollScans();

        // here you can either unset the class to log us out
        unset($api2)

        // or upon completion, or exit of the script, the class will be destroyed and the __destruct method will run to log us out

*/


// QualysAPI_ v2
/**
* @package QualysAPI_v2
* @version 1.0
* @author Nick MacCarthy
*
*/
class QualysAPI_v2{


    /* __contstruct */
    /**
    * This method will take care of logging us into the qualys api v2.0, getting a session ID,  and keeping the session ID handy for other API calls used during the instance of the class.
    * 
    * @param string $base_url - The base url needd for the api - ex "https://qualysapi.qualys.<tld>/api/2.0/fo/"
    * @param string $username - The username for the account
    * @param string $password - The password for the account
    * @return void 
    */ 
    public function __construct($base_url, $username, $password)
    {

            $this->base_url = $base_url;
            $this->username = $username;

            $this->cookie_file = "/tmp/cookie_file" . rand();
            
            $this->headers = array("Content-type: application/x-www-form-urlencoded", "X-Requested-With: vulnDB");
                            
            // On construct(), login automagiclly
            $url = $base_url . "session/";
            $postdata = array( 'echo_request' => 1, 'action' => 'login', 'username' => $username, 'password' => $password);

            $output = $this->post_url($url, $postdata, $this->headers);
            
    } 

    /**  
    * This method takes care of downloading the scan results in whatever format specified.
    *
    * @param string $scan_ref Scan Reference ID From Qualys
    * @param string $mode reporting mode - can either be 'extended' or 'brief'
    * @param string $output_format output format for scan api call -- valid is 'json' or 'csv'
    * @return string 
    */
    public function downloadscan($scan_ref, $mode, $output_format, $options){

            $url = $this->base_url . "scan/";

            $postdata = array ( 
                                    'action' => 'fetch',
                                    'scan_ref' => $scan_ref,
                                    'mode' => $mode,
                                    'output_format' => $output_format,
                            );

            if ( $options['return_stream'] && ! in_array('qapi', stream_get_wrappers())) stream_wrapper_register('qapi', 'Streamer') or die("Failed to register stream protocol");

            $output = $this->post_url($url, $postdata, $this->headers, $options);

            return $output;

    }/* }}}*/

    public function reports($opts)
    {
            $url = $this->base_url . "report/";

            $output = $this->post_url($url, $opts, $this->headers);

            return $output;
    }
    
    /**
    * This method takes care of 'polling' the scans for an account within in a given time period.  
    * The $postdata array contains the relevent arugments for the api call to 'list' the scans, 'show the asset groups' and 'show the options profile' 
    * @param string $sincedate -- The date from which to show the scans from 
    * @return string
    *
    */
    public function pollscans($sincedate, $postdata = NULL)
    {

            $url = $this->base_url . "scan/";

            // Our default options, additional options can be added by passing an array through the $postdata variable
            $postdata['action'] = 'list';
            $postdata['show_ags'] = '1';
            $postdate['show_op'] = '1';
            $postdata['launched_after_datetime'] = $sincedate;


            $output = $this->post_url($url, $postdata, $this->headers);

            return $output;

    }

    /**
    * This method will make the appropriate CURL call to make the API call to Qualys
    * @param string url - The URL for the API call
    * @param array $post_array - An array for the post data we need to send the API call
    * @param array $headers - An array of the headers we need to make for the API call
    * @param string $filename - If set, this will output data to a file
    * @returns string $result - This will be the result from the HTTP request
    */
    public function post_url($url, $post_array, $header_array, $options = NULL)
    {

            $post_string = http_build_query($post_array);

            $ch = curl_init($url);

            // Set our tmp cookie files
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);

            // Timeouts
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
            curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, 100);
            curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, 60);


            // Don't return the header
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            if($post_array){
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            if ( isset($options['return_stream']))
            {

                if ( isset( $options['return_file']))
                {

                    if ( ! isset($options['output_filename']))
                    {
                        throw new Exception("Output_filename must be set, and must be a full path");
                    }

                    $fp = fopen($options['output_filename'], 'w+');
                }
                else
                {
                    $fp = fopen('qapi://memory', 'r+');
                }

                curl_setopt($ch, CURLOPT_FILE, $fp);

                $curl_result = curl_exec($ch);

                fclose($fp);
            }
            else
            {
                $curl_result = curl_exec($ch);
            }

            
            $raw_headers = substr($curl_result, 0, strpos($curl_result, "\r\n\r\n"));
            $body =  substr($curl_result, strpos($curl_result, "\r\n\r\n")) ;

            $result = $body; 

            $raw_header_array = explode("\r\n", $raw_headers);
            $http_code = array_shift($raw_header_array);

            foreach($raw_header_array as $header_line){
                $key = strtoupper(trim(substr($header_line, 0, strpos($header_line, ":"))));
                $val = trim(substr($header_line, strpos($header_line, ":")+1));

                $headers[$key] = $val;
            }

            // Log our curl stats for this run
            Logger::msg("info", array_merge( array("message" => "curl_stats"), curl_getinfo($ch)) );

            // Close the curl connection
            curl_close($ch);

            return $result;

       }

        public function set_streamreturn()
        {
        
                $this->return_stream = true;

                return self;

        }

        public function set_output_filename($filename)
        {

                $this->output_filename = $filename;

                return self;
        }


        /* __destruct() */ 
        /**
        * This method will log us out of the api 2.0 cleanly upson class destruction
        */
        public function __destruct()
        {

                // On destruct(), logout automagically
                $url = $this->base_url . "session/";
                $postdata = array( 'action' => 'logout' );
                
                $output = $this->post_url($url, $postdata, $this->headers);

                unset($this->cookie_file);
                
        } 

}   



class QAPI_Stream {


    protected $buffer;


    function stream_open($path, $mode, $options, $opened_path)
    {
            return true;
    }


    public function stream_write($data)
    {

        $lines = explode("\n", $data);


        $lines[0] = $this->buffer . $lines[0];


        $nb_lines = count($lines);
        $this->buffer = $lines[$nb_lines - 1];
        unset($lines[$nb_lines - 1]);

        print_r($lines);
        echo "\n";


    }


}
