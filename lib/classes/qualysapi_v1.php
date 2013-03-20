<?php
/*
    Basic usage examples:
    API V1:
        // Initialize the class
        $api1 = new QualysAPI_v1();
        
        // make an API call
        $download_raw_scan = $api1->getscanreport($url, $username, $password, $scan_ref_id, $download_to_filename);

*/

// QualysAPI_v1
class QualysAPI_v1{

        protected $_request_method = 'POST';
    
        /**
        * This method will download the Qualys Knowlege Base and return the raw XML back
        *
        * @param string $base_url  The base URL for the api call - i.e. https://qualysapi.qualys.<tld>/msp
        * @param string $username  The username for the account
        * @param string $password  The password for the account
        * @returns string $output  This will be the raw XML from the API call, in this case the Qualys KB
        */
        public function get_qualys_kb($base_url, $username, $password)
        {
            $url = $base_url . "knowledgebase_download.php?show_cvss_submetrics=1&show_pci_flag=1";

            $output = $this->get_url($url, $username, $password);

            return $output;
        }

        /**
        * This method will download the Asset Group List for the client
        *
        * @param string $base_url - The base URL for the api call - i.e. https://qualysapi.qualys.<tld>/msp
        * @param string $username - The username for the account
        * @param string $password - The password for the account
        * @returns string $output - This will be the raw XML from the API call, in this case the Asset Group list for the client
        */
        public function get_asset_groups($base_url, $username, $password)
        {

            $url = $base_url . "asset_group_list.php";

            $output = $this->get_url($url, $username, $password);

            return $output;
        } 

        public function scan_report($base_url, $username, $password, $ref)
        {

            $url = $base_url . "scan_report.php";

            $post_vars = ( array( 'ref' => $ref ));

            $output = $this->post_url($url, $username, $password, $post_vars);

            return $output;

        }
        public function asset_group($base_url, $username, $password, $arr)
        {

            $url = $base_url . "asset_group.php";
            foreach ($arr as $key => $val)
            {
                if ($val) $post_vars[$key] = $val;
            }

            $output = $this->post_url($url, $username, $password, $post_vars);
            

            return $output;

        }

        public function asset_data_report($base_url, $username, $password, $opts)
        {

            $url = $base_url . "asset_data_report.php";

            foreach ($opts as $key => $val)
            {
                if ($val) $post_vars[$key] = $val;
            }

            $output = $this->post_url($url, $username, $password, $post_vars);

            return $output;
        } 

        /**
        *
        *   Used to add, list, and remove scheduled scans and maps to the qualys account.
        *   For more infor, see "Scheduled Scans and Maps" in the Qualys API v1 documentation 
        *   Note: This method will eventually be deperecated and replaced with the 'scheduled_scans' method, so if you are building someting new, start using that one instead...
        *
        *   @param      string      $base_url   Base URL - ex. https://qualysapi.qualys.com/msp
        *   @param      string      $username   Username for the qualys account
        *   @param      string      $password   Password for the qualys account
        *   @param      array       $opts       Associative array of options to pass along.  Example "array( 'active' => 'yes', 'type' => 'scan' )"
        *   @return     string      $output     XML output from our API call
        **/
        public function scheduled_scans($base_url, $username, $password, $opts)
        {
            $url = $base_url . "scheduled_scans.php";

            $output = $this->post_url($url, $username, $password, $opts);

            return $output;
        }

        public function scan_running_list($base_url, $username, $password)
        {
            $url = $base_url . "scan_running_list.php";

            $output = $this->post_url($url, $username, $password);

            return $output;
        }

        public function report_template_list($base_url, $username, $password)
        {
            $url = $base_url . "report_template_list.php";

            $output = $this->post_url($url, $username, $password);

            return $output;
        }

        public function scan_target_history($base_url, $username, $password, $opts)
        {
            $url = $base_url . "scan_target_history.php";

            $output = $this->post_url($url,$username, $password, $opts);

            return $output;

        }

        /**
         *  Set Vulnerabilities to Ignore on Hosts
         *
         *  @param string $url - base_url for qualys to connect to, i.e. qualysapi.qualys.com
         *  @param string $username username for qualys account
         *  @param strnig $password password for qualys account
         *  @opts array $opts associative array of options from qualys api documentation to pass to api call, currently on page 174 of the api v1 documentation
         *
         *
         **/
        public function ignore_vuln($base_url, $username, $password, $opts)
        {
        
            $url = $base_url . "ignore_vuln.php";

            $output = $this->post_url($url, $username, $password, $opts);

            return $output;

        }


        /**
         * Set the reqeust method to GET
         *
         * Default for this class is POST
         *
         **/
        public function get_url($url, $username, $password, $opts = NULL)
        {

                $this->_request_method = 'GET';

                return $this->post_url($url, $username, $password, $opts);

        }


        public function post_url($url, $username, $password, $post_array = NULL)
        {

                if(!is_null($post_array))
                {
                    $post_string = http_build_query($post_array);
                }

                $ch = curl_init($url);


                curl_setopt($ch, CURLOPT_HEADER, FALSE);

                // Timeouts
                curl_setopt($ch, CURLOPT_TIMEOUT, 500);
                curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, 120);
                curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, 10);


                if ( $this->_request_method === "POST")
                {

                    curl_setopt($ch, CURLOPT_POST, 1);
                }

                if($post_array){

                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
                }

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

                $curl_result = curl_exec($ch);

                // Log our curl stats for this run
                Logger::msg("info", array_merge(array("message" => "curl_stats", "qualys_api_version" => "1"), curl_getinfo($ch)) );

                curl_close($ch);

                return $curl_result;


                /*
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



                return $result;
                */

          }

        public function byte_convert($bytes){
                $symbol = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

                $exp = 0;
                $converted_value = 0;
                if( $bytes > 0 )
                {
                  $exp = floor( log($bytes)/log(1024) );
                  $converted_value = ( $bytes/pow(1024,floor($exp)) );
                }

                return sprintf( '%.2f '.$symbol[$exp], $converted_value );
              }

}// end class QualysAPI_v1 
