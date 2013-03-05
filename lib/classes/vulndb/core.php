<?php


class vulndb_core {


    protected static $_paths = array(LIBPATH, CONFIGPATH);

    public static function auto_load($class, $directory = 'classes')
    {

        $file      = '';
        $file .= str_replace('_', DIRECTORY_SEPARATOR, strtolower($class));

        if ($path = vulndb_core::find_file($directory, $file))
        {
            // Load the class file
            require $path;

            // Class has been found
            return TRUE;
        }

        // Class is not in the filesystem
        return FALSE;
    }

    public static function find_file($dir, $file, $ext = NULL, $array = FALSE)
    {

        if ( $ext === NULL )
        { 
            $ext = EXT;
        }

        // Create a partial path of the filename
        $path = LIBPATH.$dir.DIRECTORY_SEPARATOR.$file.$ext;

        if ( ! is_file($path))
        {

            $file = str_replace(DIRECTORY_SEPARATOR, "_", $file);

            $path = LIBPATH.$dir.DIRECTORY_SEPARATOR.$file.$ext;

            if ( is_file($path))
            {
                $found = $path;
                return $found;
            }
            
        }
        else 
        {
            $found = $path;
            return $found;
        }
        if ($array OR $dir === 'config')
        {
            // Include paths must be searched in reverse
            $paths = array_reverse(vulndb_core::$_paths);

            // Array of files that have been found
            $found = array();

            foreach ($paths as $dir)
            {
                if (is_file($dir.$path))
                {
                    // This path has a file, add it to the list
                    $found[] = $dir.$path;
                }
            }
        }
        else
        {
            // The file has not been found yet
            $found = FALSE;


            foreach (vulndb_core::$_paths as $dir)
            {
                if (is_file($dir.$path))
                {
                    // A path has been found
                    $found = $dir.$path;

                    // Stop searching
                    break;
                }
            }
        }

        return $found;
    }

}
