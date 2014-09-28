<?php

abstract class XLSReader {
    const PARSE_FROM_TEXT       = 0;
    const PARSE_FROM_COLS       = 1;

    public static function isValidMode($mode) {
        return ($mode == self::PARSE_FROM_COLS) || ($mode == self::PARSE_FROM_TEXT);
    }

    public static function placeUploadedFile() {
        $content = file_get_contents('php://input');

        $tmpFile = md5(uniqid(time()));

        while(file_exists(FILES_DIR . '/' . $tmpFile)) {
            $tmpFile = md5(uniqid(time()));
        }

        $path = FILES_DIR . '/' . $tmpFile;
        $result = file_put_contents($path, $content);

        if($result) {
            Sessions::setVar('UploadedXLS', $tmpFile);
        } else {
            Sessions::setVar('UploadedXLS', '');
        }

        return $result;
    }

    public static function printTmpFileColumns() {
        $tmpFile = Sessions::getVar('UploadedXLS');

        if(empty($tmpFile)) {
            return;
        }

        $path = FILES_DIR . '/' . basename($tmpFile);

        if(!file_exists($path)) {
            return;
        }
        
        $previous_memory_limit = ini_get('memory_limit');
        ini_set('memory_limit', '128M');

        $excel = new Spreadsheet_Excel_Reader($path, false);
        echo json_encode( $excel->headingColumns() );

        ini_set('memory_limit', $previous_memory_limit);
    }

    public static function printTmpFileSheetsNumber() {

        $tmpFile = Sessions::getVar('UploadedXLS');

        if(empty($tmpFile)) {
            return;
        }

        $path = FILES_DIR . '/' . basename($tmpFile);

        if(!file_exists($path)) {
            return;
        }

        $previous_memory_limit = ini_get('memory_limit');
        ini_set('memory_limit', '128M');

        $excel = new Spreadsheet_Excel_Reader($path, false);
        echo json_encode( $excel->sheetscount() );

        ini_set('memory_limit', $previous_memory_limit);
    }

    public static function getParser($municipi) {
        $municipi = Poblacions::obteNomMunicipi($municipi);
        $municipi = str_replace(" ", "", cleanString($municipi));
        
        $classname = 'Parser_' . cleanString($municipi);

        return new $classname;
    }
}

?>