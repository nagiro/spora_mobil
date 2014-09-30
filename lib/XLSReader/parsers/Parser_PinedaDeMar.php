<?php

class Parser_PinedaDeMar extends GenericParser {
    protected $carrer;
    protected $via;
    protected $numero;
    protected $bloc;
    protected $escala;
    protected $pis;
    protected $porta;
    protected $barri;
    protected $text;

    public function  __construct() {
        $this->carrer = 1;
        $this->text = 2;
        $this->barri = 4;   
        $this->numero = 1;     
    }
    
    public function parse() {
        $tmpFile = Sessions::getVar('UploadedXLS');
        $path = FILES_DIR . '/' . basename($tmpFile);
        //$path = "D:\www\spora_mobil\habitatges.xls";

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
            //Agafem el nom del carrer
        	$carrer = $excel->val($i, $this->carrer);
            
            //Tallem el text i deixem el número i altres a l'altra banda...             
        	preg_match("/[A-Za-z ]+/", $carrer, $carrer_ok);
        	$carrer_ok = cleanString($carrer_ok[0]);        	
        	$numero = intval(substr($carrer, strlen($carrer_ok),5));
        	$text = cleanString(substr($carrer, strlen($carrer_ok)));       	            		           
            $via = "Cr"; //Posem la via per codi

            if(empty($via) || empty($carrer_ok)) {
                echo 'Error: columna de carrer buida a [' . $i .']: Via:' . sprintf("%05s", $via) . ' Carrer:' . $carrer_ok . PHP_EOL;
                continue;
            }
                        
            $barri = cleanString($excel->val($i, $this->barri));

            $canviVia   = strcmp($via, $viaAnterior);
            $canviCarrer= strcmp($carrer, $carrerAnterior);

            if($canviVia || $canviCarrer) {
            	//Busquem el carrer i si no existeix, el creem. I si el barri no existeix, també el creem.
                $idCarrer = Poblacions::obteIDCarrerPerNomComplet($via, $carrer_ok, $this->idMunicipi);                
                $idBarri = Poblacions::obteIDBarriPerNom($barri, $this->idMunicipi);
                
                if($idBarri < 1) {
                	echo 'Afegint: barri ' . $barri . ' carrer: ' . $carrer_ok . '<br />';
                	$idBarri = Poblacions::afegeixBarri($barri, $this->idMunicipi);
                }
                
                if($idCarrer < 1) {
                    echo 'Afegint: via ' . $via . ' carrer: ' . $carrer_ok . '<br />';
                    $idCarrer = Poblacions::afegeixCarrer($via, $carrer_ok, $this->idMunicipi);
                    Poblacions::afegeixBarriCarrer($idBarri, $idCarrer);
                }

                $viaAnterior = $via;
                $carrerAnterior = $carrer;
            }

            $numeros[$i] = array($idCarrer, $idBarri, $numero, $text);
        }

        unset($excel);
        Poblacions::importaDireccions($numeros);
        ini_set('memory_limit', $previous_memory_limit);
    }
}

?>