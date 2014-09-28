<?php

class DBException extends PDOException {
    public function __construct($message, $code = 0) {
        Logger::append(Logger::LOG_DB, $message, $this->getTraceAsString());
    }
}

class InvalidParamsException extends Exception {
    public function __construct($message, $code = 0) {
        $message = 'Invalid params at ' . $this->getFile() . ', line ' . $this->getLine();
        
        Logger::append(Logger::LOG_GENERAL, $message, $this->getTraceAsString());
    }
}

class IOFileException extends Exception {
    public function __construct($message, $code = 0) {
        $message = 'File IO error: ' . $message;

        Logger::append(Logger::LOG_GENERAL, $message);
    }
}

?>