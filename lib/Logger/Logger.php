<?php

abstract class Logger {
    const LOG_GENERAL   = 'log';
    const LOG_DB        = 'db';

    public static function append($channel, $message, $trace = '') {
        $channel = basename($channel);
        $message = trim($message);
        
        if(!self::isValidChannel($channel) || empty($message)) {
            return false;
        }
        //var_dump($message);
        $path = LOG_DIR . '/' . $channel . '.txt';

        if(file_exists($path)) {
            $fp = fopen($path, 'a');
        } else {
            $fp = fopen($path, 'w');
        }

        if(!is_resource($fp)) {
            return false;
        }

        $message = date('[d/m/Y H:i]') . $message;

        fwrite($fp, $message . PHP_EOL);        

        if(!empty($trace)) {
            fwrite($fp, "\t " . $trace . PHP_EOL);
        }

        fclose($fp);
    }

    private static function isValidChannel($channel) {
        return ($channel == self::LOG_GENERAL || $channel == self::LOG_DB);
    }
}

?>