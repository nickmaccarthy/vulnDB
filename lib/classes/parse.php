<?php


class Parse {


    /** 
    * This method will parse the XML return for the Scan List and return an associative array of the results.
    *
    * @param        string      $xml                        The XML return from the $api2->PollScan API call
    * @return       array       $return_array               An associative array of the ScanList results
    */
    public static function scanlist($xml){ 
        

            $xml = new SimpleXMLElement($xml);

            $AGs = '';

            foreach($xml->RESPONSE->SCAN_LIST->SCAN as $eS)
            {

                if( $eS->ASSET_GROUP_TITLE_LIST )
                {
                    $AGs = array();
                    foreach( $eS->ASSET_GROUP_TITLE_LIST->ASSET_GROUP_TITLE as $eAG )
                    {
                            $AGs[] .= $eAG;
                    }

                    $AGs = implode(",", $AGs);
                }

                $return_array[(string)$eS->REF] = array(
                            "SCAN_ID" => (string)$eS->REF,
                            "TYPE" => (string)$eS->TYPE,
                            "SCAN_TITLE" => (string)$eS->TITLE,
                            "SCAN_DATE" => (string)$eS->LAUNCH_DATETIME,
                            "SCAN_STATUS" => (string)$eS->STATUS->STATE,
                            "SUB_STATE" => (string) $eS->STATUS->SUB_STATE,
                            "SCAN_TARGETS" => (string)$eS->TARGET,
                            "ASSET_GROUPS" => (string)$AGs,
                            "OPTION_PROFILE" => (string)$eS->OPTION_PROFILE->TITLE,
                            "OPTION_PROFILE_DEFAULT_FLAG" => (string) $eS->OPTION_PROFILE->DEFAULT_FLAG

                        );

            }

            return  is_array($return_array) ? $return_array : FALSE;
    } 
    
       
    public static function qualys_kb($xml_data){


        $xml = new SimpleXMLElement($xml_data);

        $vuln_count = count($xml);

        // Define our vars
        $QID = ''; 
        $TYPE = ''; 
        $SEVERITY = ''; 
        $TITLE = ''; 
        $CATEGORY = ''; 
        $LAST_UPDATE = ''; 
        $BUGTRAQ_ID = ''; 
        $PATCHABLE = ''; 
        $CVE_ID = ''; 
        $DIAGNOSIS = ''; 
        $CONSEQUENCE = ''; 
        $SOLUTION = ''; 
        $COMPLIANCE_TYPE = ''; 
        $COMPLIANCE_SECTION = ''; 
        $COMPLIANCE_DESCRIPTION = ''; 
        $CVSS_BASE = ''; 
        $CVSS_TEMPORAL = ''; 
        $CVSS_ACCESS_VECTOR = ''; 
        $CVSS_ACCESS_COMPLEXITY = ''; 
        $CVSS_AUTENTICATION = ''; 
        $CVSS_CONFIDENTIALITY_IMPACT = ''; 
        $CVSS_INTEGRITY_IMPACT = ''; 
        $CVSS_AVAILABILITY_IMPACT = ''; 
        $CVSS_EXPLOITABILITY = ''; 
        $CVSS_REMEDIATION_LEVEL = ''; 
        $CVSS_REPORT_CONFIDENCE = ''; 
        $PCI_FLAG = ''; 
        $DATE_ENTERED = ''; 


        foreach($xml->VULN as $ev)
        {

            $QID = $ev->QID;
            $TYPE = $ev->VULN_TYPE;
            $SEVERITY_LEVEL = $ev->SEVERITY_LEVEL;
            $TITLE = $ev->TITLE;
            $CATEGORY = $ev->CATEGORY;
            $LAST_UPDATE = $ev->LAST_UPDATE;
        
            $btArray = array();	// reset the bugtraq_id array every loop
            if($ev->BUGTRAQ_ID_LIST){ // this will only run IF there is a bugtraq id on this vuln, most do not have it
                foreach($ev->BUGTRAQ_ID_LIST->BUGTRAQ_ID as $bt)
                {

                        $btArray[] .= $bt->ID;

                }
                if(count($btArray) > 1)
                {
                    $BUGTRAQ_ID = implode(",", $btArray);	

                }else
                {
                    $BUGTRAQ_ID = $btArray[0];
                }
            }

            $PATCHABLE = $ev->PATCHABLE;

            $cveArray = array();
            if($ev->CVE_ID_LIST){
                foreach($ev->CVE_ID_LIST->CVE_ID as $eCVE){
                    $cveArray[] .= $eCVE->ID;
                }
                if(count($cveArray) > 1){
                    $CVE_ID = implode(",", $cveArray);
                }else{
                    $CVE_ID = $cveArray[0];
                }
            }

            $DIAGNOSIS = $ev->DIAGNOSIS;
            $CONSEQUENCE = $ev->CONSEQUENCE;
            $SOLUTION = $ev->SOLUTION;

            if($ev->COMPLIANCE){
                $COMP = $ev->COMPLIANCE->COMPLIANCE_INFO;
                $COMPLIANCE_TYPE = $COMP->COMPLIANCE_TYPE;
                $COMPLIANCE_SECTION = $COMP->COMPLIANCE_SECTION;
                $COMPLIANCE_DESCRIPTION = $COMP->COMPLIANCE_DESCRIPTION;
            }
            $CVSS_BASE = $ev->CVSS_BASE;
            $CVSS_TEMPORAL = $ev->CVSS_TEMPORAL;
            $CVSS_ACCESS_VECTOR = $ev->CVSS_ACCESS_VECTOR;
            $CVSS_ACCESS_COMPLEXITY = $ev->CVSS_ACCESS_COMPLEXITY;
            $CVSS_AUTENTICATION = $ev->CVSS_AUTENTICATION;
            $CVSS_CONFIDENTIALITY_IMPACT = $ev->CVSS_CONFIDENTIALITY_IMPACT;
            $CVSS_INTEGRITY_IMPACT = $ev->CVSS_INTEGRITY_IMPACT;
            $CVSS_AVAILABILITY_IMPACT = $ev->CVSS_EXPLOITABILITY;
            $CVSS_EXPLOITABILITY = $ev->CVSS_EXPLOITABILITY;
            $CVSS_REMEDIATION_LEVEL = $ev->CVSS_REMEDIATION_LEVEL;
            $CVSS_REPORT_CONFIDENCE = $ev->CVSS_REPORT_CONFIDENCE;
            $PCI_FLAG = $ev->PCI_FLAG;

            $kb[] = array(
                'QID' => (int) $QID, 
                'TYPE' => (string) $TYPE,
                'SEVERITY' => (int) $SEVERITY_LEVEL,
                'TITLE' => (string) $TITLE, 
                'CATEGORY' => (string) $CATEGORY,
                'LAST_UPDATE' => (string) $LAST_UPDATE,
                'BUGTRAQ_ID' => (string) $BUGTRAQ_ID,
                'PATCHABLE' => (string) $PATCHABLE,
                'CVE_ID' => (string) $CVE_ID,
                'DIAGNOSIS' => (string) $DIAGNOSIS,
                'CONSEQUENCE' => (string) $CONSEQUENCE,
                'SOLUTION' => (string) $SOLUTION, 
                'COMPLIANCE_TYPE' => (string) $COMPLIANCE_TYPE,
                'COMPLIANCE_SECTION' => (string) $COMPLIANCE_SECTION, 
                'COMPLIANCE_DESCRIPTION' => (string) $COMPLIANCE_DESCRIPTION, 
                'CVSS_BASE' => (string) $CVSS_BASE,
                'CVSS_TEMPORAL' => (string) $CVSS_TEMPORAL, 
                'CVSS_ACCESS_VECTOR' => (string) $CVSS_ACCESS_VECTOR, 
                'CVSS_ACCESS_COMPLEXITY' => (string) $CVSS_ACCESS_COMPLEXITY, 
                'CVSS_AUTENTICATION' => (string) $CVSS_AUTENTICATION,
                'CVSS_CONFIDENTIALITY_IMPACT' => (string) $CVSS_CONFIDENTIALITY_IMPACT,
                'CVSS_INTEGRITY_IMPACT' => (string) $CVSS_INTEGRITY_IMPACT, 
                'CVSS_AVAILABILITY_IMPACT' => (string) $CVSS_AVAILABILITY_IMPACT, 
                'CVSS_EXPLOITABILITY' => (string) $CVSS_EXPLOITABILITY, 
                'CVSS_REMEDIATION_LEVEL' => (string) $CVSS_REMEDIATION_LEVEL, 
                'CVSS_REPORT_CONFIDENCE' => (string) $CVSS_REPORT_CONFIDENCE, 
                'PCI_FLAG' => (string) $PCI_FLAG, 
                'DATE_ENTERED' => date('c'),
            );
        }

        return $kb;
    }

    public static function ags($xml_data, $account)
    {

        $vulndb = Model::factory('vulndb_main');

        $now = date('c');
        $xml = new SimpleXMLElement($xml_data);

        // Set our fields
        $ACCOUNT = '';
        $ASSET_ID = '';
        $TITLE = '';
        $IP_START = '';
        $IP_END = '';
        $APPLIANCE_NAME = '';
        $SCANNER_SN = '';
        $COMMENTS = '';
        $BIZ_IMPACT_RANK = '';
        $BIZ_IMPACT_TITLE = '';
        $LOCATION = '';
        $CVSS_ENVIRO_CDP = '';
        $CVSS_ENVIRO_TD = '';
        $CVSS_ENVIRO_CR = '';
        $CVSS_ENVIRO_IR = '';
        $CVSS_ENVIRO_AR = '';
        $LAST_UPDATE = '';
        $USER_LOGIN = '';
        $FIRST_NAME = '';
        $LAST_NAME = '';
        $ROLE = '';
        $DATE_ENTERED = '';

        foreach($xml->ASSET_GROUP as $AG)
        {

            $ID = $AG->ID;
            $TITLE = $AG->TITLE;

            $BIZ_IMP_RANK = $AG->BUSINESS_IMPACT->RANK;
            $BIZ_IMP_TITLE = $AG->BUSINESS_IMPACT->IMPACT_TITLE;

            $LOCATION = $AG->LOCATION;

            $CVSS_ENVIRO_CDP = $AG->CVSS_ENVIRO_CDP;
            $CVSS_ENVIRO_TD = $AG->CVSS_ENVIRO_TD;
            $CVSS_ENVIRO_CR = $AG->CVSS_ENVIRO_CR;
            $CVSS_ENVIRO_IR = $AG->CVSS_ENVIRO_IR;
            $CVSS_ENVIRO_AR = $AG->CVSS_ENVIRO_AR;

            $LAST_UPDATE = $AG->LAST_UPDATE;

            if(count($AG->ASSIGNED_USERS)){
                foreach($AG->ASSIGNED_USERS->ASSIGNED_USER as $eU){
                    $LOGIN = $eU->LOGIN;
                    $FIRSTNAME = $eU->FIRSTNAME;
                    $LASTNAME = $eU->LASTNAME;
                    $ROLE = $eU->ROLE;
                }

            }

            if(count($AG->SCANNER_APPLIANCES)){
                $APPLIANCE_NAME = $AG->SCANNER_APPLIANCES->SCANNER_APPLIANCE->SCANNER_APPLIANCE_NAME;
                $APPLIANCE_SN = $AG->SCANNER_APPLIANCES->SCANNER_APPLIANCE->SCANNER_APPLIANCE_SN;
            }

            $COMMENTS = $AG->COMMENTS;

            if ( $AG->SCANIPS )
            {

                foreach ( $AG->SCANIPS->IP as $eip )
                {

                    if ( strpos($eip, "-"))
                    {
                        $ip_parts = explode('-', $eip);

                        $ip_start = $ip_parts[0];
                        $ip_end = $ip_parts[1];

                        $IP_START = $vulndb->makelongip($ip_start);
                        $IP_END  = $vulndb->makelongip($ip_end);

                    }
                    else
                    {
                        $IP_START = $vulndb->makelongip($eip);
                        $IP_END = $vulndb->makelongip($eip);
                    }
                    
                }

            }

            $ag[] = array(

                'ACCOUNT' => $account,
                'ASSET_ID' => $ID,
                'TITLE' => $TITLE,
                'IP_START' => $IP_START,
                'IP_END' => $IP_END,
                'APPLIANCE_NAME' => $APPLIANCE_NAME,
                'SCANNER_SN' => $SCANNER_SN,
                'COMMENTS' => $COMMENTS,
                'BIZ_IMPACT_RANK' => $BIZ_IMP_RANK,
                'BIZ_IMPACT_TITLE' => $BIZ_IMP_TITLE,
                'LOCATION' => $LOCATION,
                'CVSS_ENVIRO_CDP' => $CVSS_ENVIRO_CDP,
                'CVSS_ENVIRO_TD' => $CVSS_ENVIRO_TD,
                'CVSS_ENVIRO_CR' => $CVSS_ENVIRO_CR,
                'CVSS_ENVIRO_IR' => $CVSS_ENVIRO_IR,
                'CVSS_ENVIRO_AR' => $CVSS_ENVIRO_AR,
                'LAST_UPDATE' => $LAST_UPDATE,
                'USER_LOGIN' => $USER_LOGIN,
                'FIRST_NAME' => $FIRST_NAME,
                'LAST_NAME' => $LAST_NAME,
                'ROLE' => $ROLE,
                'DATE_ENTERED' => $now,
            );
            
        }

        return $ag;
    }

    public static function report_template($xml, $account)
    {

        try {
            $xml = new SimpleXMLElement($xml);
        } catch ( Exception $e ){
            throw new vulnDB_Exception("Error loading XML: :error", array(":error", $e->getMessage()));
        }

        foreach($xml->REPORT_TEMPLATE as $rt)
        {
            $reports[] = array(
                            "ID" => (int) $rt->ID,
                            "TYPE" => (string) $rt->TYPE,
                            "TEMPLATE_TYPE" => (string) $rt->TEMPLATE_TYPE,
                            "TITLE" => (string) $rt->TITLE,
                            "USER_LOGIN" => (string) $rt->USER->LOGIN,
                            "USER_FIRSTNAME" => (string) $rt->USER->FIRSTNAME,
                            "USER_LASTNAME" => (string) $rt->USER->LASTNAME,
                            "LAST_UPDATE" => (string) $rt->LAST_UPDATE,
                            "GLOBAL" => (int) $rt->GLOBAL,
                            "ACCOUNT" => (string) $account,
                        );

        }

        return $reports;

    }

    public static function asset_data_report($adr_xml, $opts)
    {
        $vulndb = Model::factory('vulndb_main');

        $account = $opts['account_name'];
        $template_id = $opts['report_template_id'];
        $now = date('c');

        if ( ! $adr_xml)
            return false;

        libxml_use_internal_errors(true);

        $xml = new SimpleXMLElement($adr_xml);

        if ( $xml === false )
        {
            echo "Failed loading XML:\n";
            foreach ( libxml_get_errors() as $error)
            {
                echo "\t".$error->message;
            }
        }

        $host_info = array();
        $vulns = array();
        $ag_info = array();


        foreach ($xml->HOST_LIST->HOST as $eH)
        {

            // Get our Hosts
            $host_info[] = array( 
                                "IP" => (string) $vulndb->makelongip($eH->IP),
                                "TRACKING_METHOD" => (string)$eH->TRACKING_METHOD,
                                "DNS" => (string) $eH->DNS,
                                "NETBIOS" => (string) $eH->NETBIOS,
                                "OS" => (string)$eH->OPERATING_SYSTEM,
                                "REPORT_TEMPLATE_ID" => $template_id, 
                                "ACCOUNT" => $account,
                                "DATE_ENTERED" => $now,
                                );

            foreach ($eH->ASSET_GROUPS AS $ag)
            {
                // Get our Asset Groups
                $ag_info[] = array(
                                "IP" => (string) $vulndb->makelongip($eH->IP),
                                "ASSET_GROUP_TITLE" => (string) $ag->ASSET_GROUP_TITLE,
                                "REPORT_TEMPLATE_ID" => $template_id,
                                "ACCOUNT" => $account,
                                "DATE_ENTERED" => $now,
                            );
            }

            foreach ($eH->VULN_INFO_LIST->VULN_INFO as $eV)
            {

                    // Get our vulns
                    $vulns[] = array(
                                "IP" => (string) $vulndb->makelongip($eH->IP),
                                "QID" => (string) $eV->QID,
                                "PORT" => (string) $eV->PORT,
                                "PROTOCOL" => (string) $eV->PROTOCOL,
                                "TYPE" => (string) $eV->TYPE,
                                "SSL_ENABLED" => (string) $eV->SSL,
                                "RESULT" => (string) $eV->RESULT,
                                "FIRST_FOUND" => (string) $eV->FIRST_FOUND,
                                "LAST_FOUND" => (string) $eV->LAST_FOUND,
                                "TIMES_FOUND" => (string) $eV->TIMES_FOUND,
                                "VULN_STATUS" => (string) $eV->VULN_STATUS,
                                "CVSS_FINAL" => (string) $eV->CVSS_FINAL,
                                "TICKET_NUMBER" => (string) $eV->TICKET_NUMBER,
                                "TICKET_STATE" => (string) $eV->TICKET_STATE,
                                "REPORT_TEMPLATE_ID" => $template_id,
                                "ACCOUNT" => $account,
                                "DATE_ENTERED" => $now,
                                );
            }
                        
        }


        return array( "hosts" => $host_info, "vulns" => $vulns, "ags" => $ag_info);

    }

    public static function scan_schedules($scan_schedules, $account)
    {

        $now = date('c');

        // Load the XML into SimpleXML
        try {
            $xml = new SimpleXMLElement($scan_schedules);
        } catch (Exception $e) {
            echo "Error loading XML: " . $e->getMessage() . "\n";
        }

        foreach ( $xml->SCAN as $scan)
        {
            // Build an associative array from our XML for the query later...
            $scans[] = array (

                            "ACTIVE"            =>  (string) $scan['active'],
                            "REF"               =>  (string) $scan['ref'],
                            "TITLE"             =>  (string) $scan->TITLE,
                            "TARGETS"           =>  (string) $scan->TARGETS,
                            "START_DATE_UTC"    =>  (string)  $scan->SCHEDULE->START_DATE_UTC,
                            "START_HOUR"        =>  (int) $scan->SCHEDULE->START_HOUR,
                            "START_MINUTE"      =>  (int) $scan->SCHEDULE->START_MINUTE,
                            "END_AFTER_HOURS"   =>  (int) $scan->SCHEDULE->END_AFTER_HOURS,
                            "TIME_ZONE_CODE"    =>  (string) $scan->SCHEDULE->TIME_ZONE->TIME_ZONE_CODE,
                            "TIME_ZONE_DETAILS" =>  (string) $scan->SCHEDULE->TIME_ZONE->TIME_ZONE_DETAILS,
                            "DST_SELECTED"      =>  (int) $scan->SCHEDULE->DST_SELECTED,
                            "RECURRENCE"        =>  (int) $scan->SCHEDULE->RECURRENCE,
                            "DEFAULT_SCANNER"   =>  (string) $scan->DEFAULT_SCANNER,
                            "NEXTLAUNCH_UTC"    =>  (string) $scan->NEXTLAUNCH_UTC,
                            "ISCANNER_NAME"     =>  (string) $scan->ISCANNER_NAME,
                            "OPTION"            =>  (string) $scan->OPTION,
                            "TYPE"              =>  (string) $scan->TYPE,
                            "OPTION_PROFILE"    =>  (string) $scan->OPTION_PROFILE->OPTION_PROFILE_TITLE,
                            "LAST_UPDATED"      =>  (string) $now,
                            "ACCOUNT"           =>  (string) $account
                        );

        }


        return $scans;
    }


    public static function running_scans( $scanlist_v1, $scanlist_v2, $account, $now)
    {


        $xml = new SimpleXMLElement($scanlist_v2);
        $xml_v1 = new SimpleXMLElement($scanlist_v1);


        if ( ! $xml->RESPONSE->SCAN_LIST->SCAN)
        {
            $no_scan_msg = "NO_SCANS_RUNNING";

             $scan_details[0] = array( "TYPE" => $no_scan_msg, "TITLE" => $no_scan_msg, "STATUS" => $no_scan_msg, "ACCOUNT" => $account, "DATE_ENTERED" => $now );
             
             return $scan_details;
        }

        foreach ( $xml->RESPONSE->SCAN_LIST->SCAN as $scan)
        {

            $ags = array();
            $asset_groups = "";
            if ( $scan->ASSET_GROUP_TITLE_LIST)
            {
                foreach ($scan->ASSET_GROUP_TITLE_LIST->ASSET_GROUP_TITLE as $ag)
                {
                    $ags[] = $ag;
                }

                $asset_groups = implode(',', $ags);
            }

            $ref = (string) trim($scan->REF);

            $scan_details[$ref] = array(
                                    'SCAN_ID'           =>  (string) trim($scan->REF),
                                    'TYPE'              =>  (string) trim($scan->TYPE),
                                    'TITLE'             =>  (string) trim($scan->TITLE),
                                    'USER_LOGIN'        =>  (string) trim($scan->USER_LOGIN),
                                    'LAUNCH_DATETIME'   =>  (string) trim($scan->LAUNCH_DATETIME),
                                    'STATUS'            =>  (string) trim($scan->STATUS->STATE),
                                    'TARGET'            =>  (string) trim($scan->TARGET),
                                    'ASSET_GROUPS'      =>  (string) trim($asset_groups),
                                    'OPTION_PROFILE'    =>  (string) trim($scan->OPTION_PROFILE->TITLE),
                                    'DEFAULT_FLAG'      =>  (string) trim($scan->OPTION_PROFILE->DEFAULT_FLAG),
                                    'DATE_ENTERED'      =>  (string) $now,
                                );

        }

        // Strips out the targets from the api v1 call so we can map them to the apiv2 results later
        foreach ( $xml_v1 as $eS)
        {
            $ref = (string) $eS['value'];

            $targets = (string) $eS->KEY[1];

            $v1_arr[$ref] = array('targets' => $targets);
        }

        // map the results from the api1 scan list to the api2 scan list
        foreach ( $scan_details as $scan_ref => $info )
        {
            foreach ( $v1_arr as $scan_id => $details)
            {
                if ( $scan_ref === $scan_id) $scan_details[$scan_id]['targets'] = $details['targets'];
            }
        }

        return $scan_details;
    }
}
