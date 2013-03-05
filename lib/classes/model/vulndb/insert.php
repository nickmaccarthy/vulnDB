<?php


class Model_vulndb_insert extends Model {

    protected $our_map = array();
    
    public function __construct()
    {
    
        $this->_vulndb = Model::factory('vulndb_main');
        
    }

    public function scan(array $scan_data, array $scan_info)
    {



        $info = array(
                        'SCAN_ID'       => $scan_info['SCAN_ID'],
                        'SCAN_DATE'     => $scan_info['SCAN_DATE'],
                        'DATE_ENTERED'  => $scan_info['DATE_ENTERED'], 
                        'ACCOUNT'       => $scan_info['ACCOUNT'],

                    );

        $this->our_map = array();

        foreach ( $scan_data as $data )
        {
            if ($data['DNS Name'] === "DNS Name") continue;  // skip the headers incase they are in the data

            $data_map = array(
                        'IP'            => $this->_vulndb->makelongip($data['IP']),
                        'DNS'           => $data['DNS Name'],
                        'NETBIOS'       => $data['Netbios Name'],
                        'QID'           => $data['QID'],
                        'RESULT'        => $data['Result'],
                        'PROTOCOL'      => $data['Protocol'],
                        'PORT'          => $data['Port'],
                        'SSL_ENABLED'   => $data['SSL'],
                        'FQDN'          => $data['FQDN'],
                        
                );

            $this->our_map[] = array_merge($scan_info, $data_map);
        }


        // If we can't get the headers, then the scan results are probably invalid... abort
        if ( ! $fields = array_keys($this->our_map[0]) )
            return FALSE;

        $insert = DB::insert(MAIN_SCAN_RESULTS_TABLE, $fields);

        foreach ( $this->our_map as $map )
        {

            $insert->values($map);

        }

        $insert->execute();

        return $insert;

    }

    public static function scan_details($details, $account_info)
    {

        $scan_details = array(
                        'SCAN_ID'                       => $details['SCAN_ID'],
                        'TYPE'                          => $details['TYPE'],
                        'TITLE'                         => $details['SCAN_TITLE'],
                        'SCAN_DATE'                     => $details['SCAN_DATE'],
                        'SCAN_STATUS'                   => $details['SCAN_STATUS'],
                        'SUB_STATE'                     => $details['SUB_STATE'],
                        'TARGETS'                       => $details['SCAN_TARGETS'],
                        'ASSET_GROUPS'                  => $details['ASSET_GROUPS'],
                        'OPTION_PROFILE'                => $details['OPTION_PROFILE'],
                        'OPTION_PROFILE_DEFAULT_FLAG'  => $details['OPTION_PROFILE_DEFAULT_FLAG'],
                    );

        // Map 'em
        $our_map = array_merge($scan_details, $account_info);

        // Pull our the fields we need to insert into
        $fields = array_keys($our_map);

        // Go go gadget query!
        $insert = DB::insert(MAIN_SCAN_RUN_TABLE, $fields)
                    ->values($our_map)
                    ->execute();


        return $insert;
        
    }

    public function ags($ag, $account)
    {

        $ags_parsed = parse::ags($ag, $account);

        $fields = array_keys($ags_parsed[0]);

        $insert = DB::insert(MAIN_AG_TABLE, $fields);

        foreach ( $ags_parsed as $ag)
        {
            $insert->values($ag);

        }

        $insert->execute();

        return $insert;

    }

    public function kb($kb)
    {

        $kb_arr = parse::qualys_kb($kb);

        $fields = array_keys($kb_arr[0]);

        $insert = DB::insert(MAIN_QUALYS_KB_TABLE, $fields);


        $c = 0;
        foreach ( $kb_arr as $kb )
        {

            $c++;
            $insert->values($kb);

            if ( $c % 500 === 0 )
            {
                $insert->execute();
                $insert->reset_values();
                echo "Inserted 500 rows -- total rows inserted: $c\n";
            }
        }

        $insert->execute();

        return $c;
    }

}
