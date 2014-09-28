<?php

class Parser_Badalona extends GenericParser {
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

        $barriAnterior = '';
        $carrerAnterior = '';
        $viaAnterior = '';
        $numeros = array();

        for($i = 2; $i <= $maxRow; $i++) {
            $barri = cleanString( $excel->val($i, $this->barri) );

            if(empty($barri)) {
                error_log('Error: columna de barri buida a [' . $i . ',' . $this->barri . ']');
                continue;
            }

            $parts = explode(',', $excel->val($i, $this->text));

            if(count($parts) < 2) {
                error_log('Error: columna de direcció incorrecta a [' . $i . ',' . $this->barri . ']');
                continue;
            }

            $carrer = cleanString($parts[0]);
            $via = word_shift_left($carrer);
            $numero = cleanString($parts[1]);

            $canviBarri = strcmp($barri, $barriAnterior);
            $canviVia   = strcmp($via, $viaAnterior);
            $canviCarrer= strcmp($carrer, $carrerAnterior);

            if($canviVia || $canviCarrer) {
                $idCarrer = Poblacions::obteIDCarrerPerNomComplet($via, $carrer);
                if($idCarrer < 1) {
                    $idCarrer = Poblacions::afegeixCarrer($via, $carrer, $this->idMunicipi);
                }

                $viaAnterior = $via;
                $carrerAnterior = $carrer;
            }

            if($canviBarri) {
                $idBarri = Poblacions::obteIDBarriPerNom($barri);
                if($idBarri < 1) {
                    $idBarri = Poblacions::afegeixBarri($barri, $this->idMunicipi);
                }

                $barriAnterior = $barri;
            }

            if(!Poblacions::existeixBarriCarrer($idBarri, $idCarrer)) {
                Poblacions::afegeixBarriCarrer($idBarri, $idCarrer);
            }

            $numeros[$i] = array($idCarrer, $idBarri, $numero);
        }

        unset($excel);
        Poblacions::importaDireccions($numeros);
        ini_set('memory_limit', $previous_memory_limit);
    }
}

?>