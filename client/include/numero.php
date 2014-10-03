<?php
    if(isNumericParam('c')) {
        $id = intval($_REQUEST['c']);
    } else {
        Sessions::redirect('menu');
    }

    if(isNumericParam('m')) {
        $a = intval($_REQUEST['m']);
        Sessions::setVar('GrupBarri', $a);
    } elseif(Sessions::isVar('GrupBarri')) {
        $a = Sessions::getVar('GrupBarri');
    } else {
        $a = null;
    }

    if(empty($a)) {
        Sessions::redirect('menu');
    }

    if(isNumericParam('start')) {
        $offset = intval($_REQUEST['start']);
    } else {
        $offset = 0;
    }

    $maxEntries = Poblacions::obteNombreLlarsCarrer($id, $a);
    $count = ENTRIES_PER_PAGE;

    if($offset>0 && $offset<$count) $prevID = 0;
    else $prevID = $offset - $count;
    $nextID = $offset + $count;

    if($prevID < 0) {
        $prevID = 0;
        $prevLink = '#';
    } else {
        $prevLink = '?page=numero&amp;c=' . $id . '&amp;start='. $prevID;
    }

    if($nextID >= $maxEntries) {
        $nextID = $maxEntries;
        $nextLink = '#';
    } else {
        $nextLink = '?page=numero&amp;c=' . $id . '&amp;start=' . $nextID;
    }

    $userID = $_SESSION['userID'];
    $userprofile = $_SESSION['profile'];
    
?>
<div data-role="page">
    <div data-role="header" data-nobackbtn="true">
        <h1><?php echo Poblacions::obteNomCarrer($id); ?>: <span id="pageCount"><?php echo ($offset + 1) . ' / ' . $maxEntries; ?><span></h1>

        <a href="#?page=carrer" data-icon="arrow-l" data-theme="b" data-iconpos="notext">Torna</a>
        <a href="#?page=incidencia" id="novaIncidencia" data-icon="alert" data-theme="b" data-rel="dialog" data-transition="pop" data-iconpos="notext">Incid&egrave;ncia</a>
        <div data-role="navbar">
            <ul>
                <li><a title="Cerca" href="?page=searchnum&amp;c=<?php echo $id; ?>" id="cercarNumero" data-rel="dialog" data-transition="pop" data-role="button">?</a></li>
                <li><a title="Afegeix" href="?page=addnumero&amp;c=<?php echo $id; ?>&amp;start=<?php echo $offset; ?>" id="afegirNumero" data-rel="dialog" data-transition="pop" data-role="button">+</a></li>
                <li><a title="Anterior" href="<?php echo $prevLink; ?>" data-role="button" data-transition="slide" data-back="true">&lt;</a></li>
                <li><a title="Seg&uuml;ent" href="<?php echo $nextLink; ?>" data-role="button" data-transition="slide" data-back="false">&gt;</a></li>
            </ul>
        </div>
    </div><!-- /header -->

    <div data-role="content" style="padding:0 15px;">
        <?php
            $num = Poblacions::mostraActuacionsCarrer($id, $a, $count, $offset);
            $opc = Poblacions::mostraOpcions(Poblacions::obteMunicipiCarrer($id));            

            if(is_array($num)) {
                foreach($num as $n) {
                    $actuacions = $n['actuacions'];
                    $educadors = $n['educadors'];

                    if(!empty($actuacions) && !empty($educadors)) {
                        $actuacions = explode(',', $actuacions);
                        $educadors = explode(',', $educadors);

                        $opcions = array();
                        foreach($actuacions as $i => $a){
                            if(isset($educadors[$i])) {
                                $opcions[$a] = $educadors[$i];
                            }
                        }
                    }

                    echo '<div data-role="fieldcontain">
                            <fieldset data-role="controlgroup" data-role="fieldcontain" data-type="horizontal">
                                <legend>' . $n['text'] . '</legend>';

                                foreach($opc as $o) {
                                    $oID = $o['id'];

                                    $idAttr = $n['id'] . '_' . $oID;
                                    $marcat = (isset($opcions[$oID]) && isset($n['actuacions']))? 'checked="checked"' : '';
                                    $bloquejat = (!empty($marcat) && ($opcions[$oID] != $userID) && $userprofile != 'Administrador')? 'disabled="disabled"' : '';
                                    if($bloquejat=='' && $o['perfil'] != $userprofile && $userprofile != 'Administrador'){
                                        $bloquejat = 'disabled="disabled"';
                                    }

                                    echo '
                                        <input class="opcioActuacio" type="checkbox" id="' . $idAttr . '" name="' . $idAttr . '" title="' . $n['id'] . '" ' . $bloquejat . ' ' . $marcat . '/>
                                        <label for="' . $idAttr . '"> ' . $o['abreviacio'] . ' </label>
                                    ';
                                }
                    echo '</fieldset>
                    </div>';
                }
            }

            $offset+= $count;
        ?>
    </div><!-- /content -->
</div><!-- /Activitat -->