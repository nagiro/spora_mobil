<?php

function action_doLogin() {
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];

    if(Sessions::login($username, $password)) {    
        Sessions::redirect('menu');
    } else {        
        Sessions::redirect('login');
    }
}

function action_logout() {
    Sessions::logout();
    Sessions::redirect('login');
}

function action_searchNum() {
    if(isParam('carrer') && isParam('barri') && isParam('text')) {
        $numero = intval($_REQUEST['text']);
        $carrer = intval($_REQUEST['carrer']);
        $barri = intval($_REQUEST['barri']);
        $maxEntries = Poblacions::obteNombreLlarsCarrer($carrer, $barri);
        $pageSize = ENTRIES_PER_PAGE;

        if(is_numeric($numero)) {
            $start = Poblacions::cercaPrimeraPosicioNumero($carrer, $barri, $numero);
        } else {
            $start = 0;
        }

        $result = array(
            'c' => $carrer,
            'start' => $start
        );
        echo json_encode($result);
    }
    exit;
}

function action_saveIncidence() {
    if(!Sessions::isLogged()) {
        exit;
    }

    $uid = $_SESSION['userID'];

    if(empty($uid)) {
        Sessions::redirect('login');
        exit;
    }

    $tipus = 'Particular';
    $text = $_REQUEST['text'];

    if(empty($text)) {
        exit;
    }

    Incidencies::afegeixIncidencia($tipus, $uid, $text);
    exit;
}

function action_saveDirection() {
    $carrer = $_REQUEST['carrer'];
    $barri = $_REQUEST['barri'];
    $numero = $_REQUEST['numero'];
    $planta = (intval($_REQUEST['planta'])) ? $_REQUEST['planta'] : strtoupper($_REQUEST['planta']);
    $porta = $_REQUEST['porta'];

    if(empty($carrer) || empty($numero)) {
        exit;
    }

    Poblacions::afegeixDireccio($carrer, $barri, $numero, $planta, $porta);
    exit;
}

function action_saveActuacio() {
    $direccio = $_REQUEST['direccio'];
    $actuacio = $_REQUEST['actuacio'];
    $set = trim($_REQUEST['set']);

    $userID = $_SESSION['userID'];

    if(empty($userID) || empty($direccio) || empty($actuacio)) {
        exit;
    }

    //Comprova si cal desar la direcció o no
    if($set == 'true') {
        echo Poblacions::desaResolucioDireccio($direccio, $actuacio);
    } else {
        echo Poblacions::eliminaResolucioDireccio($direccio, $actuacio);
    }
    exit;
}

?>