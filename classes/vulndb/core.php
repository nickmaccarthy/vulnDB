<?php


class vulndb_core {


    protected static $_paths = array(DOCROOT, LIBPATH, CONFIGPATH);

    /**
     *  @var array  Currently active modules
     */
    protected static $_modules = array();

    public static function auto_load($class, $directory = 'classes')
    {

        $file  = '';
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

    /**
     *
     *  Set the currently enabled modlues
     *
     *  @param  array   $modules    list of module paths
     *  @return array    enabled modules
     **/
    public static function modules( array $modules = NULL )
    {

        if ( $modules === NULL )
        {
            return vulndb_core::$_modules;

        }

        $paths = vulndb_core::$_paths;

        // Add our modules into our vulndb_core::$_path array
        foreach ( $modules as $name => $path )
        {
            if ( is_dir($path))
            {
                $paths[] = $modules[$name] = realpath($path).DIRECTORY_SEPARATOR;
            }
            else
            {
                throw new vulnDB_exception('Attempted to load a missing or invalid module \':module\' at \':path\'', array(
                                ':module' => $name,
                                ':path' => $path,
                            ));
            }
        }


        // Set the new include paths
        vulndb_core::$_paths = $paths;

        // Set the current module list
        vulndb_core::$_modules = $modules;


        foreach ( vulndb_core::$_modules as $path )
        {

            // modules/foobar/init.php
            $init = $path.'init'.EXT;

            if ( is_file($init))
            {
                require_once $init;

            }
        }

        return vulndb_core::$_modules;
    }

    public static function find_file($dir, $file, $ext = NULL, $array = FALSE)
    {

        if ( $ext === NULL )
        { 
            $ext = EXT;
        }

        // Create a partial path of the filename
        $path = $dir.DIRECTORY_SEPARATOR.$file.$ext;

        if ($array)
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
