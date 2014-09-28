<?php

class Parser_Olot extends GenericParser {
    protected $carrer;
    protected $via;
    protected $numero;
    protected $bloc;
    protected $escala;
    protected $pis;
    protected $porta;
    protected $barri;

    public function  __construct() {
        $this->carrer = 1;
        $this->via = 2;
        $this->numero = 3;
        $this->bloc = 4;
        $this->escala = 5;
        $this->pis = 6;
        $this->porta = 7;
        $this->barri = 8;
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

//        $idBarri = 49; //Hardcoding - veure script BD versió 1.7
        $carrerAnterior = '';
        $viaAnterior = '';
        $numeros = array();

        for($i = 2; $i <= $maxRow; $i++) {
            $carrer = cleanString($excel->val($i, $this->carrer));
            $via = cleanString($excel->val($i, $this->via));

            if(empty($via) || empty($carrer)) {
                echo 'Error: columna de carrer buida a [' . $i .']: Via:' . sprintf("%05s", $via) . ' Carrer:' . $carrer . PHP_EOL;
                continue;
            }

            $numero = $excel->val($i, $this->numero);

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
            
            $barri = cleanString($excel->val($i, $this->barri));

            $canviVia   = strcmp($via, $viaAnterior);
            $canviCarrer= strcmp($carrer, $carrerAnterior);

            if($canviVia || $canviCarrer) {
                $idCarrer = Poblacions::obteIDCarrerPerNomComplet($via, $carrer, $this->idMunicipi);
                
                $idBarri = Poblacions::obteIDBarriPerNom($barri, $this->idMunicipi);
                
                if($idBarri < 1) {
                	echo 'Afegint: barri ' . $barri . ' carrer: ' . $carrer . PHP_EOL;
                	$idBarri = Poblacions::afegeixBarri($barri, $this->idMunicipi);
                }
                
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