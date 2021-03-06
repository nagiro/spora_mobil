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
    protected $cadastre;

    public function  __construct() {
        $this->carrer = 1;               
        $this->barri = 4;   
        $this->numero = 1;     
        $this->cadastre = 2;
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

        $barriAnterior = '';
        $carrerAnterior = '';        
        $viaAnterior = '';
        $idBarri = 0;
        $idCarrer = 0;
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
            $cadastre = cleanString($excel->val($i, $this->cadastre));

            if(empty($via) || empty($carrer_ok)) {
                echo 'Error: columna de carrer buida a [' . $i .']: Via:' . sprintf("%05s", $via) . ' Carrer:' . $carrer_ok . "<br />";
                continue;
            }
                        
            $barri = cleanString($excel->val($i, $this->barri));

            $canviVia   = ( $via == $viaAnterior );
            $canviCarrer= ( $carrer == $carrerAnterior );
            $canviBarri = ( $barri == $barriAnterior );

            if( $canviVia == 0 || $canviCarrer == 0 || $canviBarri == 0 ) {
            	//Busquem el carrer i si no existeix, el creem. I si el barri no existeix, també el creem.
                $idCarrer = Poblacions::obteIDCarrerPerNomComplet($via, $carrer_ok, $this->idMunicipi);                
                $idBarri = Poblacions::obteIDBarriPerNom($barri, $this->idMunicipi);
                $existeixBarrisCarrer = Poblacions::existeixBarrisCarrer( $idBarri , $idCarrer);
                
                if($idBarri < 1) {
                	echo 'Afegint: barri ' . $barri . ' carrer: ' . $carrer_ok  . "<br />";
                	$idBarri = Poblacions::afegeixBarri($barri, $this->idMunicipi);
                }
                                                
                //Si no trobem el carrer, l'afegim.
                if($idCarrer < 1) {
                    echo 'Afegint: via ' . $via . ' carrer: ' . $carrer_ok . "<br />";
                    $idCarrer = Poblacions::afegeixCarrer($via, $carrer_ok, $this->idMunicipi);
                    Poblacions::afegeixBarriCarrer($idBarri, $idCarrer);
                	//Si el carrer existeix, però no la relació amb el barri, afegim la relació amb el barri
                } elseif( !$existeixBarrisCarrer ){
                	echo "Afegint barri a carrer ".$carrer_ok.' - '.$barri. "<br />";
                	Poblacions::afegeixBarriCarrer($idBarri, $idCarrer);
                }                                                

                $viaAnterior = $via;
                $carrerAnterior = $carrer;
                $barriAnterior = $barri;
            }

            //Comprovem que ja no existeixi el carrer o el barri a direccions. Si existeix, no fem res.  
            if($idBarri > 0 && $idCarrer > 0 && !Poblacions::verificaDireccio( $idBarri , $idCarrer , $numero , $text , $cadastre )){
            	echo "Afegida l'adreça ".$carrer_ok.' - '.$barri. "<br />";
            	$numeros[$i] = array($idCarrer, $idBarri, $numero, $text, $cadastre);
            }
        }

        unset($excel);
        Poblacions::importaDireccions($numeros);
        ini_set('memory_limit', $previous_memory_limit);
    }
}

?>