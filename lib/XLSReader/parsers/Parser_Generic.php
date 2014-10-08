<?php

class Parser_Generic extends GenericParser {
    protected $carrer;
    protected $via;
    protected $numero;
    protected $text;
    protected $barri;
    protected $cadastre;
    protected $municipi;

    public function  __construct() {
        $this->municipi = 1;
        $this->barri = 2;
        $this->via = 3;
        $this->carrer = 4;
        $this->numero = 5;
        $this->text = 6;
        $this->cadastre = 7;
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

        $carrerAnterior = '';        
        $viaAnterior = '';
        $numeros = array();

		//Per cada fila a l'excel
        for($i = 2; $i <= $maxRow; $i++) {
        	
        	$carrer = cleanString($excel->val($i, $this->carrer));
        	$via = cleanString($excel->val($i, $this->carrer));
        	$numero = cleanString($excel->val($i, $this->carrer));
        	$text = cleanString($excel->val($i, $this->carrer));
        	$barri = cleanString($excel->val($i, $this->carrer));
        	$cadastre = cleanString($excel->val($i, $this->carrer));
        	$municipi = cleanString($excel->val($i, $this->carrer));
        	 
        	//Comprovem que el municipi existeixi
        	$tmp = Poblacions::obteMunicipi($municipi);
        	if(!is_numeric($municipi) || empty($municipi) || empty($tmp)) {
        		echo 'Error: columna de municipi buida o incorrecte a [' . $i .']: ' . PHP_EOL;
        		continue;
        	}
        	 
            if(empty($via) || empty($carrer)) {
                echo 'Error: columna de carrer buida a [' . $i .']: Via:' . sprintf("%05s", $via) . ' Carrer:' . $carrer . PHP_EOL;
                continue;
            }                                    

            $canviVia   = strcmp($via, $viaAnterior);
            $canviCarrer= strcmp($carrer, $carrerAnterior);

            if($canviVia || $canviCarrer) {
            	//Busquem el carrer i si no existeix, el creem. I si el barri no existeix, també el creem.
                $idCarrer = Poblacions::obteIDCarrerPerNomComplet($via, $carrer, $municipi);
                $idBarri = Poblacions::obteIDBarriPerNom($barri, $municipi);
                
                if($idBarri < 1) {
                	echo 'Afegint: barri ' . $barri . ' carrer: ' . $carrer  . PHP_EOL;
                	$idBarri = Poblacions::afegeixBarri($barri, $municipi);
                }
                
                if($idCarrer < 1) {
                    echo 'Afegint: via ' . $via . ' carrer: ' . $carrer  . PHP_EOL;
                    $idCarrer = Poblacions::afegeixCarrer($via, $carrer, $municipi);
                    Poblacions::afegeixBarriCarrer($idBarri, $idCarrer);
                }

                $viaAnterior = $via;
                $carrerAnterior = $carrer;
            }

            $numeros[$i] = array($idCarrer, $idBarri, $numero, $text, $cadastre);
        }

        unset($excel);
        Poblacions::importaDireccions($numeros);
        ini_set('memory_limit', $previous_memory_limit);
    }
}

?>