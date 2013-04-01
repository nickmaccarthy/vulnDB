<?php

class Model_vulndb_main extends Model {

    public function getaccounts()
    {
        

        $account_query = "SELECT DISTINCT account, username, password, api_url FROM ".LOGINS_TABLE;

        $results = DB::query(Database::SELECT, $account_query)
                    ->execute('vulndb_users');

        return $results;

    }

    public function getaccountinfo($account)
    {


        $query = 'select * from '. LOGINS_TABLE . ' where account = :account';

        $results = DB::query(Database::SELECT, $query)
                    ->bind(':account', $account)
                    ->execute('vulndb_users');

        foreach ( $results as $result )
        {
            
            $return = array(
                        'username' => $result['username'],
                        'password' => CryptAES::decrypt($result['password']),
                        'url1'  => 'https://' . $result['api_url'] . "/msp/",
                        'url2'  => 'https://' . $result['api_url'] . "/api/2.0/fo/",
                    );

        }

        return $return;

    }

    public function getscans($account)
    {

        
        $query = "select distinct SCAN_ID from ".MAIN_SCAN_RUN_TABLE." where account = :account";

        $results = DB::query(Database::SELECT, $query)
                    ->bind(':account', $account)
                    ->execute();

        return $results;
    }


    public function prunescanlist($scanlist, $account)
    {

        if ( is_array($scanlist))
        {
            foreach ( $scanlist as $scan )
            {
                if ( $scan['SCAN_ID'] )
                {

                    $scan_ids[] = "'" . $scan['SCAN_ID'] . "'";
                }
                
            }

            $scan_ids = implode(",", $scan_ids);

            $account_scans = $this->getscans($account);

            foreach ( $account_scans as $account_scan )
            {
                unset($scanlist[$account_scan['SCAN_ID']]);
            }

            return $scanlist;

        }
        else
        {
            return $scanlist;

        }
    }

    public function is_xml($xml)
    {

	    if( preg_match("/^<\?xml version=\".*?\" encoding=\".*?\" \?>/i", $xml))
        {
            return TRUE;
        }

        return FALSE;

    }

    /**
     *  makelongip
     *  ip2long() does not return an unsigned int on 32 bit systems
     *  this method converts ip's to long and is 32 bit safe
     *
     *  @param  string  $ip IP Address we wish to make long
     *  @return int $longip IP address in long format
     *
     **/
    public static function makelongip($ip)
    {

        if(PHP_INT_SIZE === 8) // 64 Bit Systems
        {  
                
            $longip = ip2long($ip);

        }
        else
        {

            $longip = sprintf("%u", ip2long($ip));

        }

        return $longip;

    }

    public function file_size($size)
    {
            $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");

            return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) .$filesizename[$i] : '0 Bytes';

    }
}
