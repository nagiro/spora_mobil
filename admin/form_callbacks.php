<?php

function action_doLogin() {
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];

    if(Sessions::login($username, $password)) {
        Sessions::redirect('direccions');
    }
}

function action_logout() {
    Sessions::logout();
    Sessions::redirect('login');
}

function action_changePage() {
    if(isParam('page')) {
        Sessions::drawPageBody($_REQUEST['page']);
    }
}

function action_saveArea() {
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);
    } else {
        $id = 0;
    }

    $nom = $_REQUEST['nom'];

    if(empty($nom)) {
        throw new Exception('El nom no pot estar en blanc');
    }

    $municipi = intval($_REQUEST['municipi']);

    if($municipi < 1) {
        throw new Exception('Has de triar un municipi de la llista');
    }

    $barri = new DBTable('barris', $id);
    $barri->nom = $nom;
    $barri->municipi = $municipi;
    $barri->store();

    Sessions::ajaxRedirect('barris');
}

function action_esborraMunicipi() {
    $municipi = intval($_REQUEST['municipi']);

    if($municipi < 1) {
        throw new Exception('El municipi no Ã©s vÃ lid');
    }

    if(!Sessions::isMaintainer()) {
        throw new Exception('No tens permÃ­s per executar aquesta acciÃ³');
    }

    Poblacions::eliminaCarrersMunicipi($municipi);
    Sessions::ajaxRedirect('manteniment');
}

function action_saveCarrer() {
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);
    } else {
        $id = 0;
    }

    $via = $_REQUEST['via'];

    if(empty($via)) {
        throw new Exception('La via no pot estar en blanc');
    }

    $nom = $_REQUEST['nom'];

    if(empty($nom)) {
        throw new Exception('El nom no pot estar en blanc');
    }

    $municipi = intval($_REQUEST['municipi']);

    if($municipi < 1) {
        throw new Exception('Has de triar un municipi de la llista');
    }

    $carrer = new DBTable('Carrers', $id);
    $carrer->via = $via;
    $carrer->nom = $nom;
    $carrer->municipi = $municipi;
    $carrer->store();

    Sessions::ajaxRedirect('direccions');
}

function action_saveDireccio() {
    
}

function action_saveDirection() {
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);
    } else {
        $id = 0;
    }

    $carrer = intval($_REQUEST['carrer']);

    if($carrer < 1) {
        throw new Exception('Has de triar un carrer de la llista');
    }

    $text = $_REQUEST['llar'];

    if(empty($text)) {
        throw new Exception('Has d\'escriure el nÃºmero de la llar');
    }

    $direccio = new DBTable('direccions', $id);
    $direccio->carrer = $carrer;
    $direccio->text = $text;
    $direccio->store();

    Sessions::ajaxRedirect('direccions');
}

function action_saveUser() {
    $id = $_REQUEST['id'];
    $username = $_REQUEST['username'];
    $password1 = $_REQUEST['password1'];
    $password2 = $_REQUEST['password2'];
    $profile = $_REQUEST['profile'];
    $name = $_REQUEST['nom'];
    $municipis = $_REQUEST['municipis'];
    $language = $_REQUEST['language'];

    if(strcmp($password1, $password2) !== 0) {
        throw new Exception('Les contrasenyes no coincideixen');
    }

    if(!empty($id)) {
        Users::modifyUser($id, $password1, $name, $profile, $language);
        Users::eliminaMunicipisUsuari($id);
    } else {
        Users::register($username, $password1, $name, $profile, $language);
    }
    
    Users::desaMunicipisUsuari($id, $municipis);

    Sessions::ajaxRedirect('usuaris');
}

function action_deleteUser() {
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);

        $user = new DBTable('usuaris', $id);
        $user->delete();
    }

    Sessions::redirect('usuaris');
}

function action_deleteIncidence() {
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);

        $incidencia = new DBTable('incidencies', $id);
        $incidencia->delete();
    }

    Sessions::redirect('incidencies_particulars');
}

function action_deleteDirection() {
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);

        $carrers = new DBTable('carrers', $id);
        $carrers->delete();
    }

    Sessions::redirect('direccions');
}

function action_upload() {
    XLSReader::placeUploadedFile();
    exit;
}

function action_getXLSCols() {
    XLSReader::printTmpFileColumns();
    exit;
}

function action_getXLSSheets() {
    XLSReader::printTmpFileSheetsNumber();
    exit;
}

function action_parseXLSText() {
    $municipi = $_REQUEST['municipi'];

    if(empty($municipi)) {
        exit;
    }
    
    $parser = XLSReader::getParser($municipi);
    $parser->setMunicipi($municipi);    
    $parser->parse();
    exit;
}

function action_get() {
    if(isset($_REQUEST['table']) && !empty($_REQUEST['table'])) {
        $table = strtolower(trim($_REQUEST['table']));

        $tableObject = new DBTable($table);

        $fields = $tableObject->getAllFields();

        if(count($fields) > 0) {
            if(isParam('sort')) {
                $sort = $_REQUEST['sort'];

                if($tableObject->isSetField($sort)) {
                    $tableObject->setSorting($sort);
                }
            }

            foreach($fields as $name => $value) {
                if(isParam($name) && $tableObject->isSetField($name)) {
                    $tableObject->setFieldValue($name, $_REQUEST[$name]);
                }
            }

            $list = $tableObject->readAll();

            echo json_encode($list);
        }
    }

    exit;
}

function action_stats_particular() {
    $municipi = intval($_REQUEST['municipi']);
    
    if(count($_SESSION['municipi']) && !in_array($municipi, $_SESSION['municipi'])) {
        throw new Exception('No se\'t permet consultar estadÃ­stiques d\'aquest municipi');
    }
    
    $barri = intval($_REQUEST['barri']);
    $carrer = intval($_REQUEST['carrer']);
    $educador = intval($_REQUEST['educador']);
    $tipusAccio = intval($_REQUEST['tipusAccio']);
    $inici = $_REQUEST['inici'];
    $fi = $_REQUEST['fi'];

    Estadistiques::poblacio($municipi, $barri, $carrer, $educador, $tipusAccio, $inici, $fi);
    exit;
}

function action_deleteArea() {
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);
    
        $barri = new DBTable('barris', $id);
        $barri->delete();
    }

    Sessions::redirect('barris');
}

function action_saveAreaGrouping() {
    $barris = $_REQUEST['barris'];

    if(!is_array($barris) || count($barris) < 1) {
        exit;
    }

    if(isNumericParam('id')) {
        $grup = intval($_REQUEST['id']);

        Poblacions::editaAgrupacioBarris($grup, $barris);
    } else {
        Poblacions::afegeixAgrupacioBarris($barris);
    }

    Sessions::redirect('agrupacionsbarris');
    exit;
}

function action_deleteAreaGrouping() {
    if(!isNumericParam('id')) {
        Sessions::redirect('agrupacionsbarris');
        exit;
    }

    $grup = intval($_REQUEST['id']);

    Poblacions::eliminaAgrupacioBarris($grup);
    Sessions::redirect('agrupacionsbarris');
    exit;
}

function action_obteGrupsBarris() {
    if(isNumericParam('municipi')) {
        $municipi = intval($_REQUEST['municipi']);
        
        if(count($_SESSION['municipi']) && !in_array($municipi, $_SESSION['municipi'])) {
            throw new Exception('No se\'t permet obtenir els barris d\'aquest municipi');
        }
    } else {
        $municipi = NULL;
    }

    $grups = Poblacions::llistaAgrupacionsBarris($municipi);

    echo json_encode($grups);
    exit;
}

function action_obteCarrersBarrisAgrupats() {
    if(isNumericParam('barri')) {
        $agrupacioBarris = intval($_REQUEST['barri']);

        $carrers = Poblacions::llistaCarrersPerBarris($agrupacioBarris);
    } elseif(isNumericParam('municipi')) {
        $municipi = intval($_REQUEST['municipi']);
        
        if(count($_SESSION['municipi']) && !in_array($municipi, $_SESSION['municipi'])) {
            throw new Exception('No se\'t permeten obtenir els carrers d\'aquest municipi');
        }

        $c = new DBTable('carrers');
        $c->municipi = $municipi;
        $c->setSorting('nom');
        $carrers = $c->readAll();
    } else {
        exit;
    }

    echo json_encode($carrers);
    exit;
}

function action_backupDireccions() {
    Actualitzacions::creaBackupDireccions();
    //Sessions::redirect('manteniment');
    exit;
}

function action_restauraDireccions() {
    Actualitzacions::restauraBackupDireccions();
    Sessions::redirect('manteniment');
    exit;
}

function action_backupActuacions() {
    Actualitzacions::creaBackupActuacions();
    Sessions::redirect('manteniment');
    exit;
}

function action_restauraActuacions() {
    Actualitzacions::restauraBackupActuacions();
    Sessions::redirect('manteniment');
    exit;
}

function action_backupServer() {
    if(!isArrayParam('dbs')) {
        exit;
    }

    $dbs = $_REQUEST['dbs'];

    Actualitzacions::creaBackupServidor($dbs);
    exit;
}

function action_saveProductor() {
    $id = intval($_REQUEST['id']);
    $nom = $_REQUEST['nom'];
    $municipi = intval($_REQUEST['municipi']);
    $tipusEstabliment = $_REQUEST['establiment'];
    $telefon = $_REQUEST['telefon'];
    $personaContacte = $_REQUEST['contacte'];

    $horariInici = sprintf('%02d:%02d:00', $_REQUEST['horaInici'], $_REQUEST['minutInici']);
    $horariFi = sprintf('%02d:%02d:00', $_REQUEST['horaFi'], $_REQUEST['minutFi']);

    if(strcmp($horariInici, $horariFi) > 0) {
        throw new Exception('L\'horari no Ã©s vÃ lid: la hora final de l\'horari Ã©s anterior a la d\'inici');
    }

    $diesDescans = array();
    $valorsDies = array(
        'dilluns', 'dimarts', 'dimecres', 'dijous',
        'divendres', 'dissabte', 'diumenge'
    );
    foreach($valorsDies as $v) {
        if(isset($_REQUEST[$v]) && $_REQUEST[$v] == 'true') {
            $diesDescans[] = $v;
        }
    }

    if(count($diesDescans) > 0) {
        $diesDescans = join(',', $diesDescans);
    } else {
        $diesDescans = '';
    }

    $suggerimentsComentaris = $_REQUEST['suggerimentsComentaris'];

    $materialsTriats = $_REQUEST['materialsTriats'];

    if(empty($materialsTriats)) {
        throw new Exception('Has de triar almenys un material de la taula');
    }

    $sistemesTriats = $_REQUEST['sistemesRecollidaTriats'];

    if(empty($id)) {
        $id = Productors::creaProductor(
            $nom,
            $municipi,
            $tipusEstabliment,
            $telefon,
            $personaContacte,
            $horariInici,
            $horariFi,
            $diesDescans,
            $suggerimentsComentaris
        );

        if(empty($id)) {
            throw new Exception('No s\'ha pogut desar el productor');
        }

        $id = intval($id);
    } else {
        Productors::modificaProductor(
            $id,
            $nom,
            $municipi,
            $tipusEstabliment,
            $telefon,
            $personaContacte,
            $horariInici,
            $horariFi,
            $diesDescans,
            $suggerimentsComentaris
        );

        Productors::eliminaNecessitatContenidorsProductor($id);
        Productors::eliminaSistemesRecollidaProductor($id);
    }

    $materialsTriats = explode(',', $materialsTriats);
    $materialsTriats = array_chunk($materialsTriats, 3);

    Productors::desaNecessitatContenidorsProductor($id, $materialsTriats);

    $sistemesTriats = explode(',', $sistemesTriats);
    $sistemesTriats = array_chunk($sistemesTriats, 2);

    Productors::desaSistemesRecollidaProductor($id, $sistemesTriats);

    Sessions::ajaxRedirect('altaproductors');
}

function action_saveEntregaMaterial() {
    $id = intval($_REQUEST['id']);
    $productor = intval($_REQUEST['productor']);
    $materialsTriats = explode(',', $_REQUEST['materialsTriats']);
    $materialsTriats = array_chunk($materialsTriats, 3);
    $entregaMaterialGrafic = $_REQUEST['entregaMaterialGrafic'];
    $suggerimentsComentaris = $_REQUEST['suggerimentsComentaris'];

    if(empty($id)) {
        Productors::creaFormulariEntregaMaterial(
            $productor,
            $materialsTriats,
            $entregaMaterialGrafic,
            $suggerimentsComentaris
        );
    } else {
        Productors::modificaFormulariEntregaMaterial(
            $id,
            $productor,
            $materialsTriats,
            $entregaMaterialGrafic,
            $suggerimentsComentaris
        );
    }

    Sessions::ajaxRedirect('entreguesmaterial');
}

function action_saveSeguiment() {
    $id = intval($_REQUEST['id']);
    $productor = intval($_REQUEST['productor']);
    $participa = ($_REQUEST['participa'] == 'true')? 'SÃ­' : 'No';
    $grauSatisfaccio = intval($_REQUEST['grauSatisfaccio']);
    $queixes = $_REQUEST['queixes'];
    $dubtes = $_REQUEST['dubtes'];
    $comentaris = $_REQUEST['comentaris'];
    $suggeriments = $_REQUEST['suggeriments'];

    if(empty($id)) {
        Productors::creaFormulariSeguiment(
            $productor,
            $participa,
            $grauSatisfaccio,
            $queixes,
            $dubtes,
            $comentaris,
            $suggeriments
        );
    } else {
        Productors::modificaFormulariSeguiment(
            $id,
            $productor,
            $participa,
            $grauSatisfaccio,
            $queixes,
            $dubtes,
            $comentaris,
            $suggeriments
        );
    }

    Sessions::ajaxRedirect('seguimentsproductor');
}

function action_savePoblacio() {
	$id = intval($_REQUEST['id']);
	$nom = $_REQUEST['nom'];
	$actiu = intval($_REQUEST['actiu']);
	
	if(empty($id)) {
		Poblacions::creaPoblacions($nom, $actiu);		
	} else {
		Poblacions::updatePoblacions($id, $nom, $actiu);
	}

	Sessions::ajaxRedirect('poblacions');
}

function action_deleteProducer() {
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);

        $productor = new DBTable('productors', $id);
        $productor->delete();
    }

    Sessions::redirect('altaproductors');
}

function action_deleteEntregaMaterial() {
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);

        $formularientregamaterial = new DBTable('formularientregamaterial', $id);
        $formularientregamaterial->delete();
    }

    Sessions::redirect('entreguesmaterial');
}

function action_deleteSeguimentProductor() {
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);

        $seguiment = new DBTable('formulariseguiment', $id);
        $seguiment->delete();
    }

    Sessions::redirect('seguimentsproductor');
}

function action_llistaCarrers() {
    if(isNumericParam('municipi')) {
        if(count($_SESSION['municipi']) && !in_array($_REQUEST['municipi'], $_SESSION['municipi'])) {
            throw new Exception('No se\'t permet llistar els carrers d\'aquest municipi');
        }
        
        $municipi = intval($_REQUEST['municipi']);
    } else {
        $municipi = 0;
    }

    if(isNumericParam('barri')) {
        $barri = intval($_REQUEST['barri']);
    } else {
        $barri = 0;
    }

    echo json_encode(Poblacions::llistaCarrers($municipi, $barri));
    exit;
}

function action_deletePoblacions() {
	if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
		$id = intval($_REQUEST['id']);

		$poblacio = new DBTable('municipis', $id);
		$poblacio->delete();
	}

	Sessions::redirect('poblacions');
}

function action_exportXLS(){
    $municipi = intval($_REQUEST['municipi']);
    
//    if(count($_SESSION['municipi']) && !in_array($municipi, $_SESSION['municipi'])) {
//        throw new Exception('No se\'t permet consultar estadÃ­stiques d\'aquest municipi');
//    }
    
    $barri = intval($_REQUEST['barri']);
    $carrer = intval($_REQUEST['carrer']);
    $educador = intval($_REQUEST['educador']);
    $tipusAccio = intval($_REQUEST['tipusAccio']);
    $inici = $_REQUEST['inici'];
    $fi = $_REQUEST['fi'];

    Estadistiques::poblacio_excel($municipi, $barri, $carrer, $educador, $tipusAccio, $inici, $fi);    
    exit;
		
}


?>