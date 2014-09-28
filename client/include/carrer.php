<?php

    if(isNumericParam('m')) {
        $id = intval($_REQUEST['m']);
        Sessions::setVar('GrupBarri', $id);
    } elseif(Sessions::isVar('GrupBarri')) {
        $id = Sessions::getVar('GrupBarri');
    } else {
        $id = null;
    }

    $lletra = isParam('l')? $_REQUEST['l'] : null;

    if(!$id) {
        Sessions::redirect('menu');
    }
?>
<div data-role="page">
    <div data-role="header" data-nobackbtn="true">
        <?php
            if($lletra!=''){
                echo '<a href="#?page=carrer" data-icon="arrow-l" data-theme="b" data-iconpos="notext">Torna</a>';
            }else{
                echo '<a href="#?page=poblacio" data-icon="arrow-l" data-theme="b" data-iconpos="notext">Torna</a>';
            }
        ?>
        <h1>Calles</h1>
        <a href="?page=incidencia" id="novaIncidencia" data-icon="alert" data-theme="b" data-rel="dialog" data-transition="pop" data-iconpos="notext" class="ui-btn-right">Incid&egrave;ncia</a>
    </div><!-- /header -->

    <div data-role="content">
        <ul data-role="listview" data-filter="true" role="listbox">
        <?php
            $carrer = Poblacions::llistaCarrersPerBarris($id, $lletra);
            
            if(is_array($carrer)) {
                $letter = '';

                foreach($carrer as $c) {
                    if($lletra==''){
                        $inicial = $c['nom'];
                        $inicial = $inicial{0};

                        if($inicial != $letter) {
                            $letter = $inicial;
                            echo '<li><a href="?page=carrer&amp;l=' . $letter . '">' . $letter . '</a></li>';
                        }
                    }else{
                        echo '<li><a href="?page=numero&amp;c=' . $c['id'] . '&amp;start=0">' . $c['via'] . ' ' . $c['nom'] . '</a></li>';
                    }
                }
            }
        ?>
        </ul>
    </div><!-- /content -->
</div><!-- /Activitat -->