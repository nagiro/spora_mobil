<?php

abstract class Bootstrap {
    public static function prepare() {
        //Parent dir locking
        chdir('.');

        self::setDebugging();
        
        self::cleanRequest();

        self::loadLibraries('../' . LIB_DIR);

        Sessions::startSession();

        if(Sessions::isLogged()) {
            Sessions::setPage('menu');
        }
    }

    private static function setDebugging() {
        ini_set('display_errors', 'On');
        error_reporting(E_ALL);
    }

    public static function cleanRequest() {
        unset($_POST);
        unset($_GET);

        self::cleanVar($_REQUEST);
    }

    private static function cleanVar($var) {
        foreach($var as $key => $value) {
            if(is_array($value)) {
                self::cleanVar($var[$key]);
            } else {
                $var[$key] = htmlentities($value, ENT_QUOTES, 'UTF-8');
            }
        }
    }

    public static function loadLibraries($base_dir) {
        if(is_dir($base_dir)) {
            $libs = scandir($base_dir);

            if(is_array($libs)) {
                foreach($libs as $lib) {
                    $lib = basename($lib);
                    
                    if($lib{0} == '.') {
                        continue;
                    }

                    if(is_dir($base_dir . '/' . $lib)) {
                        self::loadLibraries($base_dir . '/' . $lib . '/');
                    } elseif(is_readable($base_dir . '/' . $lib)) {
                        require $base_dir . '/' . $lib;
                    }
                }
            }
        }
    }

    public static function initBuffer() {
        ob_start();
    }

    public static function releaseBuffer() {
        ob_end_flush();
    }
}

?>