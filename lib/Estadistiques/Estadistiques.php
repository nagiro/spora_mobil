<?php

abstract class Estadistiques {

	public static function poblacio_excel($municipi = null, $barri = null, $carrer = null, $educador = null, $actuacio = null, $inici = null, $fi = null) {
        $db = Database::getInstance();

        $originalLimit = ini_get('memory_limit');
        ini_set('memory_limit', '128M');

        $query = '
        SELECT
            `al`.`nom` AS `actuacio`,
        	`a`.`id` AS `idActuacio`,	
            `a`.`informat`,
            CONCAT(`c`.`via`, `c`.`nom`) AS `carrer`,
            `b`.`nom` AS barri,
            `c`.`municipi` AS idMunicipi,
            `m`.`nom` AS municipi,
            `d`.`text`,
        	`d`.`cadastre`,
            `u`.`username` AS `educador`
        FROM `direccions` `d`
            LEFT JOIN `carrers` `c` ON `c`.`id` = `d`.`carrer`
        	LEFT JOIN `barris` `b` ON `d`.`barri` = `b`.`id`                        
            LEFT JOIN `municipis` `m` ON `m`.`id` = `c`.`municipi`
            LEFT JOIN `formularipoblacio` `fp` ON `d`.`id` = `fp`.`direccio`
            LEFT JOIN `usuaris` `u` ON `u`.`id` = `fp`.`educador`
            LEFT JOIN `actuacions` `a` ON `a`.`id` = `fp`.`actuacio`
	    	LEFT JOIN `actuacions_labels` `al` ON `al`.`actuacio` = `fp`.`actuacio` AND `al`.`idioma` = \'ca\'               
        ';
        
        $filters = array();

        if(!empty($carrer)) {
            $filters[] = '`c`.`id` = :carrer';
            $params[':carrer'] = $carrer;
        }
        else if(!empty($barri)) {
            $filters[] = '`ba`.`grup` = :grup';
            $params[':grup'] = $barri;
        }
        else if(!empty($municipi)) {
            $filters[] = '`c`.`municipi` = :municipi';
            $params[':municipi'] = $municipi;
        }

        if(!empty($educador)) {
            $filters[] = '`u`.`id` = :educador';
            $params[':educador'] = $educador;
        }

        if(!empty($actuacio)) {
            if(is_numeric($actuacio)) {
                $filters[] = '`a`.`id` = :actuacio';
                $params[':actuacio'] = $actuacio;
            } else {
                $filters[] = '`a`.`informat` = "1"';
            }
        }

        if(validDateRange($inici, $fi)) {
            $filters[] = 'AND `fp`.`data` BETWEEN :inici AND :fi';
            $params[':inici'] = date2Timestamp($inici, true);
            $params[':fi'] = date2Timestamp($fi, false);
        }

        if(count($filters) > 0) {
            $query.= 'WHERE 1=1 ' . join(' AND ', $filters);
        }

        $query.= ' AND `ocult` = 0 ORDER BY `educador` DESC,`carrer` ASC, `text` ASC';
        $resultats = $db->query($query, $params);        

        if(is_array($resultats)) {            

        	$rows = array();
        	$direccio_ant = "";
        	$educador_ant = "";
        	$i = 1;

        	$path = FILES_DIR.'/estadistiques.csv';
        	$fp = fopen($path,"w+");
        	
        	$previous_memory_limit = ini_get('memory_limit');
        	ini_set('memory_limit', '128M');
        	        	        	        
        	fputcsv($fp, array('V','PaP','PI','N/V','Carrer','Barri','Municipi','Adreça','Catastre','Educador'),";",'"');
        	
            foreach($resultats as $r) {
                
            	if($direccio_ant <> $r['text'] || $educador_ant <> $r['educador']):            		
            		if(isset($rows[$i])) fputcsv($fp, $rows[$i],";",'"');            		
            		$rows[++$i] = array(
            								'V'=>0,
            								'PaP'=>0,
            								'PI'=>0,
            								'N/V'=>0,
            								'Carrer'=> $r['carrer'],
            								'Barri' => $r['barri'],
            								'Municipi' => $r['municipi'],
            								'Adreca' => $r['text'],
            								'Cadastre' => $r['cadastre'],            							
            								'Educador' => $r['educador']);            		
            		
            	endif;            	
            		
            	$direccio_ant = $r['text'];
            	$educador_ant = $r['educador'];            	       	            
            	switch($r['idActuacio']){
            		case  '9': $rows[$i]['V'] = 1; break;
            		case '10': $rows[$i]['PaP'] = 1; break;
            		case '11': $rows[$i]['PI'] = 1; break;
            		case '12': $rows[$i]['N/V'] = 1; break;
            	}            	
            	
        	}
        	
        	echo json_encode($path);        	
        	fclose($fp);
        }
          
        
        ini_set('memory_limit', $originalLimit);        
        
    }
	
	public static function poblacio($municipi = null, $barri = null, $carrer = null, $educador = null, $actuacio = null, $inici = null, $fi = null) {
        $db = Database::getInstance();

        $originalLimit = ini_get('memory_limit');
        ini_set('memory_limit', '128M');

        $query = '
        SELECT
            `al`.`nom` AS `actuacio`,
            `a`.`informat`,
            CONCAT(`c`.`via`, `c`.`nom`) AS `carrer`,
            `b`.`nom` AS barri,
            `c`.`municipi` AS idMunicipi,
            `m`.`nom` AS municipi,
            `d`.`text`,
            `u`.`username` AS `educador`
        FROM `direccions` `d`
            LEFT JOIN `carrers` `c` ON `c`.`id` = `d`.`carrer`
            LEFT JOIN `barrisagrupats` `ba` ON `ba`.`barri` = `d`.`barri`
            LEFT JOIN `barris` `b` ON `ba`.`barri` = `b`.`id`
            LEFT JOIN `municipis` `m` ON `m`.`id` = `c`.`municipi`
            LEFT JOIN `formularipoblacio` `fp` ON `d`.`id` = `fp`.`direccio`
            LEFT JOIN `usuaris` `u` ON `u`.`id` = `fp`.`educador`
            LEFT JOIN `actuacions` `a` ON `a`.`id` = `fp`.`actuacio`
	    LEFT JOIN `actuacions_labels` `al` ON `al`.`actuacio` = `fp`.`actuacio` AND `al`.`idioma` = :idioma
        ';
	$params = array(':idioma' => Sessions::getVar('language'));
        $filters = array();

        if(!empty($carrer)) {
            $filters[] = '`c`.`id` = :carrer';
            $params[':carrer'] = $carrer;
        }
        else if(!empty($barri)) {
            $filters[] = '`ba`.`grup` = :grup';
            $params[':grup'] = $barri;
        }
        else if(!empty($municipi)) {
            $filters[] = '`c`.`municipi` = :municipi';
            $params[':municipi'] = $municipi;
        }

        if(!empty($educador)) {
            $filters[] = '`u`.`id` = :educador';
            $params[':educador'] = $educador;
        }

        if(!empty($actuacio)) {
            if(is_numeric($actuacio)) {
                $filters[] = '`a`.`id` = :actuacio';
                $params[':actuacio'] = $actuacio;
            } else {
                $filters[] = '`a`.`informat` = "1"';
            }
        }

        if(validDateRange($inici, $fi)) {
            $filters[] = '`fp`.`data` BETWEEN :inici AND :fi';
            $params[':inici'] = date2Timestamp($inici, true);
            $params[':fi'] = date2Timestamp($fi, false);
        }

        if(count($filters) > 0) {
            $query.= 'WHERE ' . join(' AND ', $filters);
        }

        $query.= ' AND `ocult` = 0 ORDER BY `carrer`';

        $resultats = $db->query($query, $params);

        if(is_array($resultats)) {
            $stats = array(
                0,
                0
            );
            if($municipi==""){
                $queryT = '
                    SELECT COUNT(DISTINCT `d`.`carrer`,`d`.`text`) AS `total`
                    FROM `direccions` `d`
                ';
            }else{
                $queryT = '
                    SELECT COUNT(DISTINCT `d`.`carrer`,`d`.`text`) AS `total`
                    FROM `direccions` `d`
                    LEFT JOIN `carrers` `c` ON `c`.`id` = `d`.`carrer`
                    WHERE `c`.`municipi` = '.$municipi.'
                ';
            }
            $resultatsT = $db->query($queryT);

            $actuaciocerca = $actuacio;
            $barricerca = $barri;
	    $municipicerca = $municipi;

            foreach($resultats as $r) {
                $municipi = $r['municipi'];
                $barri = "Tots";
                $carrer = $r['carrer'];
                $actuacio = $r['actuacio'];
                $educador = $r['educador'];

                if(!isset($stats[$municipi])) {
                    $stats[$municipi] = array(
                        0,  //Informats
                        0   //Total
                    );
                }

                if(!isset($stats[$municipi][$barri])) {
                    $stats[$municipi][$barri] = array(
                        0,  //Informats
                        0   //Total
                    );
                    if($barricerca!=''){
                        $queryB = '
                            SELECT COUNT(DISTINCT `d`.`carrer`,`d`.`text`) AS `total`
                            FROM `direccions` `d`
                            LEFT JOIN `barrisagrupats` `b` ON `b`.`barri` = `d`.`barri`
                            WHERE `b`.`grup` = :barri
                        ';
                        $paramsB[':barri'] = $barricerca;
                        $resultatsB = $db->query($queryB, $paramsB);
                    }else{
                        $resultatsB = $resultatsT;
                    }
                }

                if(!isset($stats[$municipi][$barri][$carrer])) {

                    $stats[$municipi][$barri][$carrer] = array(
                        0,  //Informats
                        0   //Total
                    );

                    if($actuaciocerca=='Informats') {
                        $queryDuplicats = 'SELECT SUM(total) AS quantitat FROM (
                            SELECT COUNT(f.direccio),1 AS total FROM formularipoblacio f
                            LEFT JOIN direccions d ON d.id=f.direccio
                            LEFT JOIN carrers c ON c.id=d.carrer
                            WHERE f.actuacio IN (1,3,4,5)
                            AND f.ocult = 0
                            AND f.data BETWEEN :inici AND :fi
                            AND CONCAT(c.via,c.nom) = :carrer
                            GROUP BY f.direccio
                        HAVING ( COUNT(f.direccio) > 1 )) AS e';
                        $paramsDuplicats = array(
                            ':carrer' => $carrer
                            ,':inici' => date2Timestamp($inici, true)
                            ,':fi' => date2Timestamp($fi, false)
                        );

                        $resultatsDuplicats = $db->query($queryDuplicats,$paramsDuplicats);
                        $numDuplicats = $resultatsDuplicats[0]['quantitat'];
                        $stats[$municipi][0] -= $numDuplicats;
                        $stats[$municipi][$barri][0] -= $numDuplicats;
                        $stats[$municipi][$barri][$carrer][0] -= $numDuplicats;
                    }
                    $queryC = '
                        SELECT COUNT(DISTINCT `d`.`carrer`,`d`.`text`) AS `total`
                        FROM `direccions` `d`
                        LEFT JOIN `carrers` `c` ON `c`.`id` = `d`.`carrer`
                        WHERE CONCAT(`c`.`via`, `c`.`nom`) = :carrer
			AND `c`.`municipi` = :municipi
                    ';
                    $paramsC = array(
			':carrer' => $carrer
			,':municipi' => $municipicerca
		    );
                    $resultatsC = $db->query($queryC, $paramsC);
                }

                if(!isset($stats[$municipi][$barri][$carrer][$educador])) {
                    $stats[$municipi][$barri][$carrer][$educador] = array(
                        0,  //Informats
                        0   //Total
                    );
                }

                $llarInformada = intval($r['informat']);

                if($actuaciocerca=='Informats') {
                    $stats[0]+= $llarInformada;
                    $stats[$municipi][0]+= $llarInformada;
                    $stats[$municipi][$barri][0]+= $llarInformada;
                    $stats[$municipi][$barri][$carrer][0]+= $llarInformada;
		    $stats[$municipi][$barri][$carrer][$educador][0]+= $llarInformada;
                }else{
                    $stats[0]++;
                    $stats[$municipi][0]++;
                    $stats[$municipi][$barri][0]++;
                    $stats[$municipi][$barri][$carrer][0]++;
		    $stats[$municipi][$barri][$carrer][$educador][0]++;
                }
                $stats[1] = $resultatsT[0]['total'];
                $stats[$municipi][1] = $resultatsT[0]['total'];
                $stats[$municipi][$barri][1] = $resultatsB[0]['total'];
                $stats[$municipi][$barri][$carrer][1] = $resultatsC[0]['total'];
                $stats[$municipi][$barri][$carrer][$educador][1]++;
            }
        }

        echo json_encode($stats);
        ini_set('memory_limit', $originalLimit);
        return $stats;

        
    }
}

?>