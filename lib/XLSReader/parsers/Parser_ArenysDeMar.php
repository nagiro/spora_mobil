<?php

class Parser_ArenysDeMar extends GenericParser {
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
        $maxSheets = $excel->sheetscount();

        $carrerAnterior = '';
        $viaAnterior = '';
        $numeros = array();

        for($sheet = 2; $sheet < $maxSheets; $sheet++) {
            $idBarri = Poblacions::obteIDBarriPerNom('Sector' . ($sheet+1));
            if($idBarri < 1) {
                $idBarri = Poblacions::afegeixBarri('Sector' . ($sheet+1), $this->idMunicipi);
            }

            for($i = 1; $i <= $maxRow; $i++) {
                $textDireccio = $excel->val($i, $this->text, $sheet);

                $via = cleanString(shift_right($textDireccio, ' '));
                $carrer = cleanString(shift_left($textDireccio, '  '));
                $numero = cleanString($textDireccio);

                if(empty($via) || empty($carrer) || empty($numero)) {
                    continue;
                }

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

                if(!Poblacions::existeixBarriCarrer($idBarri, $idCarrer)) {
                    Poblacions::afegeixBarriCarrer($idBarri, $idCarrer);
                }

                $numeros[] = array($idCarrer, $idBarri, $numero);
            }
        }

        Poblacions::importaDireccions($numeros);
        ini_set('memory_limit', $previous_memory_limit);
    }
}

?>