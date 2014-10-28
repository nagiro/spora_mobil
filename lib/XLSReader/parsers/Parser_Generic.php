<?php

class Parser_Generic extends GenericParser {
	protected $via;
	protected $carrer;    
    protected $numero;              
    protected $barri;
    protected $text;    
    protected $municipi;
    protected $cadastre;

    public function  __construct() {
        $this->via = 1;
    	$this->carrer = 2;
    	$this->numero = 3;
    	$this->text = 4;
    	$this->barri = 5;
    	$this->municipi = 6;
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

        $total_carrers = 0;
        $total_barris = 0;
        $total_adreces = 0;
        $barriAnterior = '';
        $carrerAnterior = '';        
        $viaAnterior = '';
        $idBarri = 0;
        $idCarrer = 0;
        $numeros = array();

		//Primer validem l'arxiu perquè estigui tot ok. Per cada fila de l'excel.
        for($i = 2; $i <= $maxRow; $i++) {
            
            //Agafem les dades de la fila
        	$via = cleanString($excel->val($i, $this->via));
        	$carrer = cleanString($excel->val($i, $this->carrer));
        	$numero = intval($excel->val($i, $this->numero));
        	$text = cleanString($excel->val($i, $this->text));
        	$barri = cleanString($excel->val($i, $this->barri));
            $idMunicipi = intval($excel->val($i, $this->municipi));
            $cadastre = cleanString($excel->val($i, $this->cadastre));
        	        	
            //Si el carrer o la via està buit... mostrem un error.
            if(empty($via) || empty($carrer)) {
                echo 'Error: columna de carrer buida a [' . $i .']: Via:' . sprintf("%05s", $via) . ' Carrer:' . $carrer_ok . "<br />";
                continue;
            }
                                    
            $canviVia   = ( $via != $viaAnterior );
            $canviCarrer= ( $carrer != $carrerAnterior );
            $canviBarri = ( $barri != $barriAnterior );

            //Si hi ha un canvi en la via, el carrer o el barri, registrem la nova adreça 
            if( $canviVia || $canviCarrer || $canviBarri ) {
            	
            	//Busquem carrer i barri. Si no existeix el creem. 
                $idCarrer = Poblacions::obteIDCarrerPerNomComplet( $via , $carrer , $idMunicipi );                
                $idBarri = Poblacions::obteIDBarriPerNom( $barri , $idMunicipi );
                $existeixBarrisCarrer = Poblacions::existeixBarrisCarrer( $idBarri , $idCarrer);
                
                //Si el barri no existeix, l'afegim
                if($idBarri < 1) {
                	//echo 'Afegint: barri ' . $barri . ' carrer: ' . $carrer  . "<br />";
                	$idBarri = Poblacions::afegeixBarri( $barri , $idMunicipi );
                	$total_barris++;
                }
                                                
                //Si no trobem el carrer, l'afegim.
                if($idCarrer < 1) {
                    //echo 'Afegint: via ' . $via . ' carrer: ' . $carrer . "<br />";
                    $idCarrer = Poblacions::afegeixCarrer( $via , $carrer , $idMunicipi );
                    Poblacions::afegeixBarriCarrer( $idBarri , $idCarrer );
                    $total_carrers++;
                	
                	//Si el carrer existeix, però no la relació amb el barri, afegim la relació amb el barri
                } elseif( !$existeixBarrisCarrer ){
                	//echo "Afegint barri a carrer ".$carrer.' - '.$barri. "<br />";
                	Poblacions::afegeixBarriCarrer( $idBarri , $idCarrer );
                }                                                

                $viaAnterior = $via;
                $carrerAnterior = $carrer;
                $barriAnterior = $barri;
            }

            //Comprovem que ja no existeixi el carrer o el barri a direccions. Si existeix, no fem res.  
            if( $idBarri > 0 && $idCarrer > 0 && !Poblacions::verificaDireccio( $idBarri , $idCarrer , $numero , $text , $cadastre )){
            	//echo "Afegida l'adreça ".$carrer.' - '.$barri. "<br />";
            	$numeros[$i] = array($idCarrer, $idBarri, $numero, $text, $cadastre);
            	$total_adreces++;
            }
        }

        unset($excel);
        Poblacions::importaDireccions($numeros);
        
        echo "Importació finalitzada: Carrers ($total_carrers), Barris ($total_barris), Adreces ($total_adreces)";
        
        ini_set('memory_limit', $previous_memory_limit);
    }
}

?>