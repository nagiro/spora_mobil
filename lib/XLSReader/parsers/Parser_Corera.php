<?php

class Parser_Corera extends GenericParser {
    protected $carrer;
    protected $via;
    protected $numero;
    protected $bloc;
    protected $escala;
    protected $pis;
    protected $porta;

    public function  __construct() {
        $this->via = 1;
        $this->carrer = 2;
        $this->bloc = 3;
        $this->escala = 4;
        $this->pis = 5;
        $this->porta = 6;
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

        $idBarri = 68; //Hardcoding - veure script BD versió 1.7
        $carrerAnterior = '';
        $viaAnterior = '';
        $numeros = array();

        for($i = 2; $i <= $maxRow; $i++) {
            $via = cleanString($excel->val($i, $this->via));
            $carreraux = cleanString($excel->val($i, $this->carrer));

	    $numero = shift_right($carreraux, ' ');

	    $carrer = $carreraux;

            if(empty($via) || empty($carrer)) {
                echo 'Error: columna de carrer buida a [' . $i .']: Via:' . sprintf("%05s", $via) . ' Carrer:' . $carrer . PHP_EOL;
                continue;
            }

            $bloc = $excel->val($i, $this->bloc);
            if(!empty($bloc)) {
                $numero.= ' ' . $bloc;
            }

            $escala = $excel->val($i, $this->escala);
            if(!empty($escala)) {
                $numero.= ' ' . $escala;
            }

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
                $idCarrer = Poblacions::obteIDCarrerPerNomComplet($via, $carrer, $this->idMunicipi);
                
                if($idCarrer < 1) {
                    echo 'Afegint: via ' . $via . ' carrer: ' . $carrer . PHP_EOL;
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