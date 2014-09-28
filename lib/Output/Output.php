<?php

abstract class Output {
    private static $consoleInfo = array();

    const EVENT_SUCCESSFUL_IMPORT   = 'Entry imported';
    const EVENT_FAILED_IMPORT       = 'Failed entry import';
    const EVENT_DELETE              = 'Deleted entry';

    public static function debug($level, $args) {
        if(DEBUG_MODE === true) {
            if($level == self::EVENT_DELETE) {
                $msg = '[%s] Table `%s`, where %s = "%s"';
                $msg = sprintf($msg, $level, $args['name'], $args['key'], $args['value']);
            } elseif($level == self::EVENT_SUCCESSFUL_IMPORT) {
                $msg = '[%s] Entry successfully imported: [%04s,%04s]';
                $msg = sprintf($msg, $level, $args['row'], $args['col']);
            } elseif($level == self::EVENT_FAILED_IMPORT) {
                $msg = '[%s] Failed entry importing at  : [%04s,%04s]';
                $msg = sprintf($msg, $level, $args['row'], $args['col']);
            }

            self::$consoleInfo[] = $msg;
        }
    }

    public static function flushConsole() {
        if(DEBUG_MODE === true && count(self::$consoleInfo) > 0) {
            echo '<script type="text/javascript" language="javascript">
            if(window.console && console.log) {';

            foreach(self::$consoleInfo as $msg) {
                echo 'console.log("' . htmlspecialchars($msg, ENT_QUOTES) . '"); ' . PHP_EOL;
            }

            echo '}
            </script>';
        }
    }
}

?>