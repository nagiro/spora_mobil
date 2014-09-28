<?php

class Parser_Banyoles extends GenericParser {
    public function parse() {
        $tmpFile = Sessions::getVar('UploadedXLS');
        $path = FILES_DIR . '/' . basename($tmpFile);

        if(empty($tmpFile) || !file_exists($path)) {
            throw new Exception('No es pot accedir al fitxer carregat');
        }

        $previous_memory_limit = ini_get('memory_limit');
        ini_set('memory_limit', '128M');

        $excel = new Spreadsheet_Excel_Reader($path, false);
        $maxRow = $excel->rowcount();

        $idBarri = 50; //Hardcoding - veure script BD versió 1.7
        $carrerAnterior = '';
        $viaAnterior = '';
        $numeros = array();

        for($i = 2; $i <= $maxRow; $i++) {
            $numero = cleanString2($excel->val($i, $this->text));

            if(empty($numero)) {
                echo '['. sprintf("%-04d", $i) .'] Error: columna de text buida' . PHP_EOL;
                continue;
            }

            if(strpos($numero, '/') === false) {
                $via = shift_left($numero, ' ');
            } else {
                $via = shift_left($numero, '/');
            }

            $numero = trim($numero);

            if(strpos($numero, ',') === false) {
                $carrer = shift_left($numero, ' ');
            } else {
                $carrer = shift_left($numero, ',');
            }

            if(empty($carrer)) {
                echo '['. sprintf("%-04d", $i) .'] Error: columna de carrer buida' . PHP_EOL;
                continue;
            }

            $numero = trim($numero);
            
            $canviVia   = strcmp($via, $viaAnterior);
            $canviCarrer= strcmp($carrer, $carrerAnterior);

            if($canviVia || $canviCarrer) {
                $idCarrer = Poblacions::obteIDCarrerPerNomComplet($via, $carrer);
                
                if($idCarrer < 1) {
                    echo '['. sprintf("%-04d", $i) .'] Afegint: via ' . sprintf("%-5s", $via) . ' carrer: ' . $carrer . PHP_EOL;
                    $idCarrer = Poblacions::afegeixCarrer($via, $carrer, $this->idMunicipi);
                    Poblacions::afegeixBarriCarrer($idBarri, $idCarrer);
                }

                $viaAnterior = $via;
                $carrerAnterior = $carrer;
            }

            $numeros[$i] = array($idCarrer, $idBarri, $numero);
        }

        unset($excel);
        Poblacions::importaDireccions($numeros);
        ini_set('memory_limit', $previous_memory_limit);
    }
}

?>