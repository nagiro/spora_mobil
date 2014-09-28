<?php

class Parser_Camos extends GenericParser {
    protected $carrer;
    protected $via;
    protected $numero;
    protected $bloc;
    protected $escala;
    protected $pis;
    protected $porta;

    public function  __construct() {
        $this->text = 1;
        $this->numero = 2;
        $this->pis = 3;
        $this->porta = 4;
    }

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

        $idBarri = 53; //Hardcoding - veure script BD versió 1.7
        $carrerAnterior = '';
        $viaAnterior = '';
        $numeros = array();

        for($i = 2; $i <= $maxRow; $i++) {
            $carrer = cleanString2($excel->val($i, $this->text));

            if(empty($carrer)) {
                echo '['. sprintf("%-04d", $i) .'] Error: columna de text buida' . PHP_EOL;
                continue;
            }

            $via = word_shift_left($carrer);

            if(empty($carrer)) {
                echo '['. sprintf("%-04d", $i) .'] Error: columna de carrer buida' . PHP_EOL;
                continue;
            }

            $numero = $excel->val($i, $this->numero);

            $pis = $excel->val($i, $this->pis);
            if(!empty($pis)) {
                $numero.= ' ' . $pis;
            }

            $porta = $excel->val($i, $this->porta);
            if(!empty($porta)) {
                $numero.= ' ' . $porta;
            }

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